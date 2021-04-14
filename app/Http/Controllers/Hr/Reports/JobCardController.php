<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;
use PDF,DB;

class JobCardController extends Controller
{ 

    public function jobCard(Request $request)
    {

        if(auth()->user()->hasRole('Buyer Mode')){
            return redirect('hrm/operation/job_card');
        }

        $result['unitList']  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');

        $result['areaList']  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        if ($request->get('pdf') == true) {
            $result = $this->empAttendanceByMonth($request);
            $attendance = $result['attendance'];
            $info = $result['info'];

            $pdf = PDF::loadView('hr/reports/job_card_pdf', $result);
            return $pdf->download('Job_Card_Report_'.date('d_F_Y').'.pdf');
        } elseif($request->associate != null && $request->month_year != null){
            $result = $this->empAttendanceByMonth($request);
            if($result != null){
                return view("hr/reports/job_card", $result);
            }else{
                toastr()->error('This Month Job Card Not Found!');
                return back();
            }

        }else{
          return view("hr/reports/job_card", $result);
        }

        return view("hr/reports/job_card", $result);
    }

    public function jobCardPartial(Request $request)
    {
        $result = $this->empAttendanceByMonth($request);
        if($result != null){
            $attendance = $result['attendance'];
            $info = $result['info'];
            $joinExist = $result['joinExist'];
            $leftExist = $result['leftExist'];
            return view("hr/reports/job_card_partial", $result)->render();
        }else{
           return "<h3 class='text-center'>This Month Job Card Not Found!</h3>";
        }
    }

    public  function empAttendanceByMonth($request)
    {


        $total_attend   = 0;
        $total_overtime = 0;
        $associate = $request->associate;
        if(isset($request->month_year)){
            $request->month = date('m', strtotime($request->month_year));
            $request->year = date('Y', strtotime($request->month_year));
        }

        $tempdate= "01-".$request->month."-".$request->year;

        $month = date("m", strtotime($tempdate));
        $year  = $request->year;
            #------------------------------------------------------

            // ASSOCIATE INFORMATION
        $flag = 0;
        $fetchUser = DB::table("hr_as_basic_info AS b");
        $fetchUser->where("b.associate_id", $associate);

        //check user exists
        if($fetchUser->exists()) {
            $getUnit = unit_by_id();
            $getLine = line_by_id();
            $getFloor = floor_by_id();
            $getDesignation = designation_by_id();
            $getSection = section_by_id();
            $subSection = subSection_by_id();

            $info = $fetchUser->first();
            $info->pre_section = '';
            $info->pre_unit = '';
            $info->pre_designation = '';
            // check salary
            if(strtotime(date('Y-m')) > strtotime($request->month_year)){

                $sal = DB::table('hr_monthly_salary as s')
                    ->select('s.unit_id', 's.designation_id', 's.sub_section_id', 'subsec.hr_subsec_department_id', 'subsec.hr_subsec_section_id','s.ot_status')
                    ->leftJoin('hr_subsection AS subsec', 's.sub_section_id', 'subsec.hr_subsec_id')
                    ->where('s.as_id', $associate)
                    ->where('s.month', $request->month)
                    ->where('s.year', $request->month_year)
                    ->first();

                $flag = $sal != null? 1:0;

                

                if($flag == 1 && $sal->designation_id != $info->as_designation_id){
                    $info->pre_designation = $getDesignation[$sal->designation_id]['hr_designation_name']??'';
                }
                if($flag == 1 && $info->as_section_id != $sal->hr_subsec_section_id){
                    $info->pre_designation = $getSection[$sal->hr_subsec_section_id]['hr_section_name']??'';
                }
                if($flag == 1 && $info->as_unit_id != $sal->unit_id){
                    $info->pre_unit = $getUnit[$sal->unit_id]['hr_unit_name']??'';
                }
            }

            
            $info->unit = $getUnit[$info->as_unit_id]['hr_unit_name'];
            $info->section = $getSection[$info->as_section_id]['hr_section_name']??'';
            $info->designation = $getDesignation[$info->as_designation_id]['hr_designation_name']??'';
            
            $date       = ($year."-".$month."-"."01");
            $startDay   = date('Y-m-d', strtotime($date));
            $endDay     = date('Y-m-t', strtotime($date));
            $toDay      = date('Y-m-d');

            $endDay = $endDay > $toDay?$toDay:$endDay;

            $associate= $info->associate_id;

            
            $total_attends  = 0; $absent = 0; $x=1; $total_ot = 0;
            $attendance=[];
            // join exist this month
            $iEx = 0;
            $joinExist = false;
            if($info->as_doj != null) {
                list($yearE,$monthE,$dateE) = explode('-',$info->as_doj);
                if($year == $yearE && $month == $monthE) {
                    $iEx = $dateE-1;
                    $joinExist = true;
                    $x = $dateE;
                    $startDay = $info->as_doj;
                }
            }


            $leftExist = false;
            if($info->as_status_date != null) {
                list($yearL,$monthL,$dateL) = explode('-',$info->as_status_date);
                if($year == $yearL && $month == $monthL) {
                    // if rejoin
                    if($info->as_status == 1){
                        $x = $dateL;
                        $startDay = $info->as_status_date;
                    }

                    // left,terminate,resign, suspend, delete
                    if(in_array($info->as_status,[0,2,3,4,5])!=false) {
                        $iEx = $joinExist == false ? 1: ($iEx+1);        
                        $leftExist = true;
                        $endDay = $info->as_status_date;
                    }
                }
            }

            $totalDays  = (date('d', strtotime($endDay)) - date('d', strtotime($startDay))) + 1;


            $floor = $getFloor[$info->as_floor_id]['hr_floor_name']??'';
            $line = $getLine[$info->as_line_id]['hr_line_name']??'';

            // holiday roster
            $getHolidayRoster = HolidayRoaster::where('as_id',$associate)
                ->where('date','>=', $startDay)
                ->where('date','<=', $endDay)
                ->get()
                ->keyBy('date')->toArray();

            $tableName= get_att_table($info->as_unit_id);
            // get attendance
            $getAttendance = DB::table($tableName)
                ->where('as_id', $info->as_id)
                ->where('in_date','>=', $startDay)
                ->where('in_date','<=', $endDay)
                ->get()
                ->keyBy('in_date')->toArray();

            // yearly holiday roster planner
            $getHoliday = DB::table("hr_yearly_holiday_planner")
                ->where('hr_yhp_status', 1)
                ->where('hr_yhp_unit', $info->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $startDay)
                ->where('hr_yhp_dates_of_holidays','<=', $endDay)
                ->get()
                ->keyBy('hr_yhp_dates_of_holidays')->toArray();

            // check firday outtime
            $friday_att = [];
            if($info->shift_roaster_status == 1 ){

                $friday_att = DB::table('hr_att_special')
                                ->where('as_id', $info->as_id)
                                ->where('in_date','>=', $startDay)
                                ->where('in_date','<=', $endDay)
                                ->get()
                                ->keyBy('in_date');
            }
                  
            for($i=$iEx; $i<=$totalDays; $i++) {
                $date      = ($year."-".$month."-".$x++);
                $thisDay   = date('Y-m-d', strtotime($date));


                $lineFloorInfo = DB::table('hr_station')
                    ->where('associate_id',$associate)
                    ->whereDate('start_date','<=',$thisDay)
                    ->where(function ($q) use($thisDay) {
                        $q->whereDate('end_date', '>=', $thisDay);
                        $q->orWhereNull('end_date');
                    })
                    ->first();

                $attendance[$i] = array(
                    'in_time' => null,
                    'out_time' => null,
                    'overtime_time' => null,
                    'late_status' => null,
                    'present_status' =>"A",
                    'remarks' => null,
                    'date' => $thisDay,
                    'floor' => !empty($lineFloorInfo->changed_floor)?($getFloor[$lineFloorInfo->changed_floor]['hr_floor_name']??''):$floor,
                    'line' => !empty($lineFloorInfo->changed_line)?($getLine[$lineFloorInfo->changed_line]['hr_line_name']??''):$line,
                    'outside' => null,
                    'outside_msg' => null,
                    'attPlusOT' => null,
                    'day_status' => "A"
                );


                    //check leave first
                $leaveCheck = Leave::where('leave_ass_id', $associate)
                    ->where(function ($q) use($thisDay) {
                        $q->where('leave_from', '<=', $thisDay);
                        $q->where('leave_to', '>=', $thisDay);
                    })
                    ->where('leave_status',1)
                    ->first();

                if($leaveCheck){
                    $attendance[$i]['present_status'] = $leaveCheck->leave_type." Leave <br><b>".$leaveCheck->leave_comment.'</b>';
                    $attendance[$i]['day_status'] = "P";
                } else {
                    $attendCheck = $getAttendance[$thisDay]??'';
                    // check holiday
                    $holidayRoaster = $getHolidayRoster[$thisDay]??'';

                    if($holidayRoaster == ''){
                        // $holidayEmployee = Employee::where('associate_id',$associate)->first();
                        // if shift assign then check yearly hoiliday
                        if((int)$info->shift_roaster_status == 0) {

                            $holidayCheck = $getHoliday[$thisDay]??'';
                            if($holidayCheck != ''){
                                $attendance[$i]['day_status'] = isset($getAttendance[$thisDay])?'P':'';
                                if($holidayCheck->hr_yhp_open_status == 1) {
                                    $attendance[$i]['present_status'] = "Weekend(General)".(isset($getAttendance[$thisDay])?'':' - A');

                                } if($holidayCheck->hr_yhp_open_status == 2){
                                    $attendance[$i]['present_status'] = "Weekend(OT)";
                                    $attendance[$i]['attPlusOT'] = 'OT - '.$holidayCheck->hr_yhp_comments;
                                }else if($holidayCheck->hr_yhp_open_status == 0){
                                    $attendance[$i]['present_status'] = $holidayCheck->hr_yhp_comments;
                                    $attendance[$i]['day_status'] = "W";
                                }
                            }
                        }
                    } else {
                        if($holidayRoaster['remarks'] == 'Holiday') {
                            $attendance[$i]['present_status'] = ($info->shift_roaster_status == 1)?"Day Off":"Weekend";
                            if($holidayRoaster['comment'] != null) {
                                $attendance[$i]['present_status'] .= ' - '.$holidayRoaster['comment'];
                            }
                            $attendance[$i]['day_status'] = "W";
                        }

                        if($holidayRoaster['remarks'] == 'OT') {
                            $attendance[$i]['day_status'] = isset($getAttendance[$thisDay])?'P':'';
                            $attendance[$i]['present_status'] = "OT";
                            $attendance[$i]['attPlusOT'] = 'OT - '.$holidayRoaster['comment'];
                        }
                    }

                    if($attendCheck != ''){
                        $intime = (!empty($attendCheck->in_time))?date("H:i", strtotime($attendCheck->in_time)):null;
                        $outtime = (!empty($attendCheck->out_time))?date("H:i", strtotime($attendCheck->out_time)):null;
                        if($attendCheck->remarks == 'DSI'){
                            $attendance[$i]['in_time'] = null;
                        }else{
                            $attendance[$i]['in_time'] = $intime;
                        }   
                        $attendance[$i]['out_time'] = $outtime;
                        $attendance[$i]['late_status']= $attendCheck->late_status;
                        $attendance[$i]['remarks']= $attendCheck->remarks;
                        $attendance[$i]['day_status'] = "P";
                        $attendance[$i]['present_status']=$attendance[$i]['attPlusOT']? "P (".$attendance[$i]['attPlusOT'].")":'P';
                        if($flag == 1){
                            $attendance[$i]['overtime_time'] = (($sal->ot_status==1)? $attendCheck->ot_hour:"");
                            if($sal->ot_status==1){
                                $total_ot+= (float) $attendCheck->ot_hour;
                            }
                        }else{
                            $attendance[$i]['overtime_time'] = (($info->as_ot==1)? $attendCheck->ot_hour:"");
                            if($info->as_ot==1){
                                $total_ot+= (float) $attendCheck->ot_hour;
                            }
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
                    $loc = $outsideCheck->requested_location;
                    if($outsideCheck->requested_location == 'WFHOME'){
                        $attendance[$i]['outside'] = 'Work from Home';
                        $loc = 'Home';
                    }else if ($outsideCheck->requested_location == 'Outside'){
                        $attendance[$i]['outside'] = 'Outside';
                        $loc = $outsideCheck->requested_place;
                    }else{
                       $attendance[$i]['outside'] = $outsideCheck->requested_location;
                    }
                    if($outsideCheck->type==1){
                        $attendance[$i]['outside_msg'] = 'Full Day at '.$loc;
                    }else if($outsideCheck->type==2){
                        $attendance[$i]['outside_msg'] = 'First Half at '.$loc;
                    }else if($outsideCheck->type==3){
                        $attendance[$i]['outside_msg'] = 'Second Half at '.$loc;
                    }
                }

                if($attendance[$i]['present_status'] == 'A' || $attendance[$i]['present_status'] == 'Weekend(General) - A'){
                    $absent++;
                }

            }

            $info->present = $total_attends;
            $info->absent = $absent;
            if(count($friday_att) > 0){
                $total_ot += collect($friday_att)->sum('ot_hour');
            }

            $info->ot_hour = $total_ot;

            $result['attendance']   = $attendance;
            $result['info']         = $info;
            $result['joinExist']    = $joinExist;
            $result['leftExist']    = $leftExist;
            $result['friday']       = $friday_att;

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
