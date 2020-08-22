<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use Illuminate\Http\Request;
use PDF,DB;

class JobCardController extends Controller
{ 

  public function jobCard(Request $request)
  {
    if ($request->get('pdf') == true) {
      $result = $this->empAttendanceByMonth($request);
      $attendance = $result['attendance'];
      $info = $result['info'];

      $pdf = PDF::loadView('hr/reports/job_card_pdf', $result);
      return $pdf->download('Job_Card_Report_'.date('d_F_Y').'.pdf');
    } elseif($request->all() != null){
      $result = $this->empAttendanceByMonth($request);
      $attendance = $result['attendance'];
      $info = $result['info'];
      $joinExist = $result['joinExist'];
      $leftExist = $result['leftExist'];
      return view("hr/reports/job_card",compact('attendance','info','joinExist','leftExist'));
    }else{
      return view("hr/reports/job_card");
    }
    return view("hr/reports/job_card");
  }

  public  function empAttendanceByMonth($request)
  {
    //if (!empty(request()->associate) && !empty(request()->month) && !empty(request()->year)) {
    $total_attend   = 0;
    $total_overtime = 0;
    $associate = $request->associate;
    $tempdate= "01-".$request->month."-".$request->year;


    $month = date("m", strtotime($tempdate));
    $year  = $request->year;
    #------------------------------------------------------
    // ASSOCIATE INFORMATION
    $fetchUser = DB::table("hr_as_basic_info AS b")
                  ->select(
                    "b.associate_id AS associate",
                    "b.as_name AS name",
                    "b.as_doj AS doj",
                    "b.as_ot",
                    "b.as_status_date",
                    "b.as_status",
                    "u.hr_unit_id AS unit_id",
                    "u.hr_unit_name AS unit",
                    "s.hr_section_name AS section",
                    "d.hr_designation_name AS designation",
                    "b.as_floor_id",
                    "b.as_line_id",
                    "hr_floor.hr_floor_name",
                    "hr_line.hr_line_name"
                  )
                  ->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id")
                  ->leftJoin("hr_section AS s", "s.hr_section_id", "=", "b.as_section_id")
                  ->leftJoin("hr_designation AS d", "d.hr_designation_id", "=", "b.as_designation_id")
                  ->leftJoin('hr_floor','b.as_floor_id','hr_floor.hr_floor_id')
                  ->leftJoin('hr_line','b.as_line_id','hr_line.hr_line_id')
                  ->where("b.associate_id", "=", $associate);
    //check user exists
    if($fetchUser->exists()) {
      $info = $fetchUser->first();
      $date       = ($year."-".$month."-"."01");
      $startDay   = date('Y-m-d', strtotime($date));
      $endDay     = date('Y-m-t', strtotime($date));
      $toDay      = date('Y-m-d');
      //If end date is after current date then end day will be today
      if($endDay>$toDay) {
        $endDay= $toDay;
      }
      $tableName= get_att_table($info->unit_id).' AS a';
      $associate= $info->associate;

      $totalDays  = (date('d', strtotime($endDay))-date('d', strtotime($startDay)));
      $total_attends  = 0; $absent = 0; $x=1; $total_ot = 0;
      $attendance=[];
      // join exist this month
      $iEx = 0;
      $joinExist = false;
      if($info->doj != null) {
        list($yearE,$monthE,$dateE) = explode('-',$info->doj);
        if($year == $yearE && $month == $monthE) {
          $iEx = $dateE-1;
          $joinExist = true;
          $x = $dateE;
        }
      }
      // left,terminate,resign, suspend, delete exist
      $leftExist = false;
      if($info->as_status_date != null) {
        if(in_array($info->as_status,[0,2,3,4,5])!==false) {
          list($yearL,$monthL,$dateL) = explode('-',$info->as_status_date);
          if($year == $yearL && $month == $monthL) {
            if($joinExist == false) {
              $iEx = 1;
            } else {
              $iEx = $iEx+1;
            }
            $leftExist = true;
            $totalDays = $dateL;
          }
        }
      }
      // dd($totalDays);
      for($i=$iEx; $i<=$totalDays; $i++) {
        $date       = ($year."-".$month."-".$x++);
        $thisDay   = date('Y-m-d', strtotime($date));


        $lineFloorInfo = DB::table('hr_station')
                         ->where('associate_id',$associate)
                         ->whereDate('start_date','<=',$thisDay)
                         ->whereDate('end_date','>=',$thisDay)
                         ->leftJoin('hr_floor','hr_station.changed_floor','hr_floor.hr_floor_id')
                         ->leftJoin('hr_line','hr_station.changed_line','hr_line.hr_line_id')
                         ->first();
        $floor = !empty($info->hr_floor_name)?$info->hr_floor_name:null;
        $line = !empty($info->hr_line_name)?$info->hr_line_name:null;


        //Default Values
        $attendance[$i]['in_time'] = null;
        $attendance[$i]['out_time'] = null;
        $attendance[$i]['overtime_time'] = null;
        $attendance[$i]['late_status']= null;
        $attendance[$i]['present_status']="A";
        $attendance[$i]['remarks']= null;
        $attendance[$i]['date'] = $thisDay;
        $attendance[$i]['floor'] = !empty($lineFloorInfo->hr_floor_name)?$lineFloorInfo->hr_floor_name:$floor;
        $attendance[$i]['line'] = !empty($lineFloorInfo->hr_line_name)?$lineFloorInfo->hr_line_name:$line;
        $attendance[$i]['outside'] = null;
        $attendance[$i]['outside_msg'] = null;
        $attendance[$i]['attPlusOT'] = null;


        //check leave first
        $leaveCheck = Leave::where('leave_ass_id', $associate)
                        ->where(function ($q) use($thisDay) {
                          $q->where('leave_from', '<=', $thisDay);
                          $q->where('leave_to', '>=', $thisDay);
                        })
                        ->first();
        if($leaveCheck){
          $attendance[$i]['present_status']=$leaveCheck->leave_type." Leave";
        } else {
          // check attendance
          $attendCheck = DB::table($tableName)
                          ->select(
                              "a.*",
                              "b.as_ot"
                          )
                          ->join("hr_as_basic_info AS b", function($join) {
                              $join->on("b.as_id", "=", "a.as_id");
                          })
                          ->where('b.associate_id', $associate)
                          ->whereDate('a.in_time', '=', $thisDay)
                          //->orWhereDate('a.out_time', '=', $thisDay)
                          ->first();

            // check holiday
            $holidayRoaster = HolidayRoaster::where(['date'=>$thisDay,'as_id'=>$associate])->first();
            if(!$holidayRoaster){
              $holidayEmployee = Employee::where('associate_id',$associate)->first();
              // if shift assign then check yearly hoiliday
              if((int)$holidayEmployee->shift_roaster_status == 0) {
                $holidayCheck = DB::table("hr_yearly_holiday_planner")
                            ->where('hr_yhp_dates_of_holidays', $thisDay)
                            ->where('hr_yhp_status', 1)
                            ->where('hr_yhp_unit', $info->unit_id)
                            ->first();
                if($holidayCheck){
                  if($holidayCheck->hr_yhp_open_status == 1) {
                    $attendance[$i]['present_status'] = "Weekend(General)";
                  }
                  else if($holidayCheck->hr_yhp_open_status == 2){
                    $attendance[$i]['present_status'] = "Weekend(OT)";
                    $attendance[$i]['attPlusOT'] = 'OT - '.$holidayCheck->hr_yhp_comments;
                  }
                  else if($holidayCheck->hr_yhp_open_status == 0){
                    $attendance[$i]['present_status'] = $holidayCheck->hr_yhp_comments;
                  }
                }
              }
            } else {
              if($holidayRoaster->remarks == 'Holiday') {
                $attendance[$i]['present_status'] = "Day Off";
                if($holidayRoaster->comment != null) {
                  $attendance[$i]['present_status'] .= ' - '.$holidayRoaster->comment;
                }
              }
              if($holidayRoaster->remarks == 'OT') {
                $attendance[$i]['present_status'] = "OT";
                $attendance[$i]['attPlusOT'] = 'OT - '.$holidayRoaster->comment;
              }
            }

            if($attendCheck){
              $intime = (!empty($attendCheck->in_time))?date("H:i", strtotime($attendCheck->in_time)):null;
              $outtime = (!empty($attendCheck->out_time))?date("H:i", strtotime($attendCheck->out_time)):null;
              if($attendCheck->remarks == 'DSI'){
                $attendance[$i]['in_time'] = null;
              }else{
                $attendance[$i]['in_time'] = $intime;
              }
              $attendance[$i]['out_time'] = $outtime;
              $attendance[$i]['overtime_time'] = (($info->as_ot==1)? $attendCheck->ot_hour:"");
              $attendance[$i]['late_status']= $attendCheck->late_status;
              $attendance[$i]['remarks']= $attendCheck->remarks;
              $attendance[$i]['present_status']=$attendance[$i]['attPlusOT']? "P (".$attendance[$i]['attPlusOT'].")":'P';
              if($info->as_ot==1){
                $total_ot+= (float) $attendCheck->ot_hour;
              }
              $total_attends++;
            }
        }

        $outsideCheck= DB::table('hr_outside')
                      ->where('start_date','<=',$thisDay)
                      ->where('end_date','>=',$thisDay)
                      ->where('status',1)
                      ->where('as_id',$associate)
                      ->first();
        if($outsideCheck){
          $attendance[$i]['outside'] = 'Outside';
          if($outsideCheck->type==1){
            $attendance[$i]['outside_msg'] = 'Full Day';
          }else if($outsideCheck->type==2){
            $attendance[$i]['outside_msg'] = 'First Half' ;
          }else if($outsideCheck->type==3){
            $attendance[$i]['outside_msg'] = 'Second Half' ;
          }
        }

        if($attendance[$i]['present_status'] == 'A'){
          $absent++;
        }

      }
      //end of loop
      $info->present = $total_attends;
      $info->absent = $absent;
      $info->ot_hour = $total_ot;
      $result ['attendance']= $attendance;
      $result ['info']= $info;
      $result ['joinExist']= $joinExist;
      $result ['leftExist']= $leftExist;
      //dd($result);exit;
      return $result;
    }
  }

  public function hoursToseconds($inHour)
  {
      if($inHour) {
          list($hours,$minutes,$seconds) = array_pad(explode(':',$inHour),3,'00');
          sscanf($inHour, "%d:%d:%d", $hours, $minutes, $seconds);
          return isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
      }
  }

}
