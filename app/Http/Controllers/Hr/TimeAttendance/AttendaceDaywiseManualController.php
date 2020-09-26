<?php
namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Helpers\Attendance2;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceInOutTime;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\BuyerManualAttandenceProcess;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Line;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF, Validator, Auth, ACL, DB, DataTables;

class AttendaceDaywiseManualController extends Controller
{
 # Daywise Manual Attendance Form
  public function dayManual(Request $request)
  {

    try {

        $lineid=$request->line_id;
        $florid=$request->floor_id;
        $section=$request->section;
        $subSection=$request->subSection;

        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                   ->pluck('hr_unit_name', 'hr_unit_id');

        $info = DB::table('hr_absent as abs')
                   ->leftJoin('hr_as_basic_info as b', 'b.associate_id', 'abs.associate_id')
                   ->leftJoin('hr_department AS d', 'b.as_department_id', 'd.hr_department_id')
                   ->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id')
                   ->when(!empty($request->as_id), function ($query) use($request){
                          return $query->where('abs.associate_id',$request->as_id);
                          })
                    ->when(!empty($request->area), function ($query) use($request){
                           return $query->where('b.as_area_id',$request->area);
                           })
                   ->when(!empty($request->department), function ($query) use($request){
                          return $query->where('b.as_department_id',$request->department);
                          })
                    ->where(function($condition) use ($florid,$lineid,$section,$subSection){

                      if ($florid!=null)
                      {
                        $condition->where('b.as_floor_id', $florid);
                      }
                      if ($lineid!=null)
                      {
                        $condition->where('b.as_line_id', $lineid);
                      }
                      if ($section!=null)
                      {
                        $condition->where('b.as_section_id', $section);
                      }
                      if ($subSection!=null)
                      {
                        $condition->where('b.as_subsection_id', $subSection);
                      }
                    })
                    ->where('b.as_status',1) // checking status
                    ->select([
                      DB::raw('@sl:=@sl+1 AS serial_no'),
                      'b.as_id',
                      'b.associate_id',
                      'b.as_name',
                      'b.as_ot',
                      'b.as_shift_id',
                      'b.as_department_id',
                      'd.hr_department_name',
                      'dsg.hr_designation_name'
                    ])

                    ->where('abs.hr_unit',$request->unit_id)
                    ->where('abs.date',$request->report_date)
                    ->orderBy('dsg.hr_designation_name','ASC')
                    ->get();

        $present=0; $absent=0;

        foreach($info AS $employees){
            $unit = $request->unit_id;
            $associate =$employees->associate_id;
            if($employees->as_ot == 1){
              if(!isset($employees->as_shift_id)){
                 $shiftinfo = DB::table('hr_shift')->where('hr_shift_unit_id',$unit)->first();
              }else{
                 $shiftinfo = DB::table('hr_shift')->where('hr_shift_id',$employees->as_shift_id)->first();
              }

                 //dd($shiftinfo);exit;
               $employees->hr_shift_start_time = $shiftinfo->hr_shift_start_time;
               $employees->hr_shift_end_time = $shiftinfo->hr_shift_end_time;
               $employees->hr_shift_break_time = $shiftinfo->hr_shift_break_time;
            }


            $attends   = false;
            $leaves    = false;
            $absents   = false;
            $lates     = false;
            $holidays  = -1;
            $holiday_comment= "";
            $overtimes = 0;
            $output_out= null;
            $output_in = null;
            $leave_type=null;
            $startDate = date("Y-m-d", strtotime($request->report_date));
            $endDate   = date("Y-m-d", strtotime($request->report_date));
            $totalDays = ((strtotime($request->report_date) - strtotime($request->report_date)) / (60 * 60 * 24))+1;
            $atid=null;

            $tableName="";

          if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
              $tableName="hr_attendance_mbm AS a";
              $colName= "a.id as attid";
          }

          else if($unit ==2){
              $tableName="hr_attendance_ceil AS a";
              $colName= "a.id as attid";
          }

          else if($unit ==3){
            $colName= "a.id as attid";
              $tableName="hr_attendance_aql AS a";
          }

          else if($unit ==6){
              $tableName="hr_attendance_ho AS a";
              $colName= "a.id as attid";
          }

          else if($unit ==8){
              $tableName="hr_attendance_cew AS a";
              $colName= "a.id as attid";
          }
          else{
              $tableName="hr_attendance_mbm AS a";
              $colName= "a.att_id as attid";
          }
      #-----------------------------------------------------
        $today = $startDate;

        // check leave
        $leaveCheck = DB::table("hr_leave")->where('leave_ass_id', $associate)
            ->where(function ($q) use($today) {
                    $q->where('leave_from', '<=', $today);
                    $q->where('leave_to', '>=', $today);
                });



        if($leaveCheck->exists())
        {
          $leaveCheck= $leaveCheck->first();

            $leaves=true;

            $leave_type= $leaveCheck->leave_type;
        }
        else
        {
            // if today is a holiday
            $holidayCheck = DB::table("hr_yearly_holiday_planner")
                ->where('hr_yhp_dates_of_holidays', $today)
                ->where('hr_yhp_unit', $unit)
                ->whereNotIn('hr_yhp_open_status', [1]);


            if($holidayCheck->exists())
            {
              $holidayCheckData = $holidayCheck->first();
                // check if open status = 0, then holiday
                if ($holidayCheckData->hr_yhp_open_status == "0")
                {
                    $holidays= 0;
                    $holiday_comment= $holidayCheckData->hr_yhp_comments;

                }
                else if($holidayCheckData->hr_yhp_open_status == "2")
                {
                    $holidays=2;

                // check attendance
                $attendCheck = DB::table($tableName)
                    ->select(
                        "a.*",
                        $colName,
                        "s.hr_shift_start_time",
                        "s.hr_shift_end_time",
                        "s.hr_shift_break_time"
                    )
                    ->join("hr_as_basic_info AS b", function($join) {
                        $join->on("b.as_id", "=", "a.as_id");
                    })
                    ->leftJoin("hr_shift AS s", function($join) {
                        $join->on("s.hr_shift_code", "=", "a.hr_shift_code");
                    })
                    ->where('b.associate_id', $associate)
                    ->whereDate('a.in_time', '=', $today);

                $attendCheckData = $attendCheck->first();

                    // check out time and exists then
                    // calculate OT = outtime - (sf_start+breack)
                    if($attendCheck->exists())
                    {
                        /*
                        * attendance intime & outtime
                        * if not empty outtime
                        * -- then outtime = outtime
                        * else if (outitme empty and intime > sf_start + 4 hours)
                        * -- then outime=intime and calculate ot
                        */
                        $output_in= $attendCheckData->in_time;
                        $output_out= $attendCheckData->out_time;
                        $cIn = strtotime(date("H:i", strtotime($attendCheckData->in_time)));

                        $cOut = strtotime(date("H:i", strtotime($attendCheckData->out_time)));
                        $cShifStart = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_start_time)));
                        $cBreak = $attendCheckData->hr_shift_break_time*60;
                        $atid =$attendCheckData->attid;

                        if (!empty($attendCheckData->out_time))
                        {
                            $total_minutes = ($cOut - ($cShifStart+$cBreak))/60;
                            $minutes= ($total_minutes%60);
                            $ot_minute = $total_minutes-$minutes;
                            //round minutes
                            if($minutes>=13 && $minutes<43) $minutes= 30;
                            else if($minutes>=43) $minutes= 60;
                            else $minutes= 0;
                            if($ot_minute>=0)
                            $overtimes+=($ot_minute+$minutes);
                        }
                        else if (empty($attendCheckData->out_time) && ($cIn>($cShifStart+14399)) )
                        {
                            $total_minutes= ($cIn - ($cShifStart+$cBreak))/60;
                            $minutes= ($total_minutes%60);
                            $ot_minute = $total_minutes-$minutes;
                            //round minutes
                            if($minutes>=13 && $minutes<43) $minutes= 30;
                            else if($minutes>=43) $minutes= 60;
                            else $minutes= 0;
                            if($ot_minute>=0)
                            $overtimes+=($ot_minute+$minutes);
                        }
                    }
                }
            }
            else
            {

              //Check  holiday(general) only for showing in report
              $generalHolidayCheck = DB::table("hr_yearly_holiday_planner")
                  ->where('hr_yhp_dates_of_holidays', $today)
                  ->where('hr_yhp_unit', $unit)
                  ->where('hr_yhp_open_status', 1)
                  ->exists();
               if($generalHolidayCheck) {
                $holidays = 1;
               }


              // check attendance
              $attendCheck = DB::table($tableName)
                  ->select(
                      "a.*",
                      $colName,
                      "s.hr_shift_start_time",
                      "s.hr_shift_end_time",
                      "s.hr_shift_break_time",
                      "b.as_ot"
                  )
                  ->join("hr_as_basic_info AS b", function($join) {
                      $join->on("b.as_id", "=", "a.as_id");
                  })
                  ->leftJoin("hr_shift AS s", function($join) {
                      $join->on("s.hr_shift_code", "=", "a.hr_shift_code");
                  })
                  ->where('b.associate_id', $associate)
                  ->whereDate('a.in_time', '=', $today);

              $attendCheckData = $attendCheck->first();

                // calculate general time & att with overtime
                if($attendCheck->exists())
                {
                    $attends=true;
                    // -------------------------------------------------
                    $shift_start_time = $attendCheckData->hr_shift_start_time;
                    $shift_end_time   = $attendCheckData->hr_shift_end_time;
                    $shift_break_time = $attendCheckData->hr_shift_break_time;
                    $punch_in         = $attendCheckData->in_time;
                    $punch_out        = $attendCheckData->out_time;
                    $output_in= $attendCheckData->in_time;
                    $output_out= $attendCheckData->out_time;
                    $atid = $attendCheckData->attid;
                    // -------------------------------------------------
                    $cIn = strtotime(date("H:i", strtotime($attendCheckData->in_time)));
                    $cOut = strtotime(date("H:i", strtotime($attendCheckData->out_time)));
                    $cShifStart = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_start_time)));
                    $cShifEnd = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_end_time)));
                    $cBreak = $attendCheckData->hr_shift_break_time*60;
                    //------------------------------------
                    // Calculate INTIME & OUTTIME



                    $ot_manual = DB::table("hr_ot")
                        ->where("hr_ot_as_id",  $associate)
                        ->whereDate('hr_ot_date', '=', $today)
                        ->value("hr_ot_hour");

                    if (!empty($ot_manual))
                    {
                        $overtimes += ($ot_manual*60);
                    }
                    else
                    {

                        if (empty($punch_out))
                        {
                            if ($cIn > ($cShifStart+$cBreak))
                            {
                                $cOut = $cIn;
                            }
                            else
                            {
                                $cOut = null;
                            }
                        }
                        else
                        {
                            if ($cOut < $cShifEnd+$cBreak)
                            {
                                $cOut = null;
                            }
                        }

                        // CALCULATE OVER TIME
                        if(!empty($cOut))
                        {
                            $total_minutes = ($cOut - ($cShifEnd+$cBreak))/60;
                            $minutes = ($total_minutes%60);
                            $ot_minute = $total_minutes-$minutes;
                            //round minutes
                            if($minutes>=13 && $minutes<43) $minutes= 30;
                            else if($minutes>=43) $minutes= 60;
                            else $minutes= 0;

                            if($ot_minute>=0)
                            $overtimes+=($ot_minute+$minutes);
                        }
                    }


                    // check shift time an in time
                    $late_time = ((strtotime(date('H:i:s',strtotime($punch_in))) - strtotime('TODAY')) - (strtotime(date('H:i:s', strtotime($shift_start_time))) - strtotime('TODAY')));

                    if($late_time > 180) // 3*60=180 seconds
                    {
                        $lates=true;
                    }
                    // -----------------------------
                }
                else
                {
                    $absents== true;
                }
            }

      }
      $h = floor($overtimes/60) ? ((floor($overtimes/60)<10)?("0".floor($overtimes/60)):floor($overtimes/60)) : '00';
      $m = $overtimes%60 ? (($overtimes%60<10)?("0".$overtimes%60):($overtimes%60)) : '00';


          $employees->in_time= (!empty($output_in))?date("H:i:s", strtotime($output_in)):null;
          $employees->out_time= (!empty($output_out))?date("H:i:s", strtotime($output_out)):null;
          $employees->oth= (($overtimes>0)?"$h:$m":"");
          $employees->otm= $overtimes;
          $employees->atid= $atid;

          if($holidays == 0)
            $employees->att= "Weekend";

          else if($holidays == 1)
            $employees->att= "Weekend(General)";

          else if($holidays == 2)
            $employees->att= "Weekend(OT)";
          else{
            if($attends)
              $employees->att= "P";
            else
              $employees->att= "A";
          }
        }

        $departments= $info->unique('as_department_id');

        $unit_name= DB::table('hr_unit')
        ->where('hr_unit_id', $request->unit_id)
        ->pluck('hr_unit_name')
        ->first();

        if(!empty($lineid)){
          $line_name= DB::table('hr_line')
          ->where('hr_line_id', $request->line_id)
          ->pluck('hr_line_name')
          ->first();
        }
        else  $line_name="";

        if(!empty($florid)){
          $floor_name= DB::table('hr_floor')
          ->where('hr_floor_id', $florid)
          ->pluck('hr_floor_name')
          ->first();
        }
        else  $floor_name="";

        $report_date= $request->report_date;

        $floorList= DB::table('hr_floor')
                    ->where('hr_floor_unit_id', $request->unit_id)
                    ->pluck('hr_floor_name', 'hr_floor_id');
        $lineList= DB::table('hr_line')
                  ->where('hr_line_unit_id', $request->unit_id)
                  ->pluck('hr_line_name', 'hr_line_id');

        $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        $deptList= DB::table('hr_department')
                            ->where('hr_department_area_id', $request->area)
                            ->pluck('hr_department_name', 'hr_department_id');

        $sectionList= DB::table('hr_section')
                            ->where('hr_section_department_id', $request->department)
                            ->pluck('hr_section_name', 'hr_section_id');

        $subSectionList= DB::table('hr_subsection')
                            ->where('hr_subsec_section_id', $request->section)
                            ->pluck('hr_subsec_name', 'hr_subsec_id');
         //dd($info);exit;
        return view('hr/timeattendance/attendance_daywise_manual', compact('unitList', 'info','departments', 'unit_name','floorList','floor_name', 'line_name', 'report_date','absent','present', 'lineList','areaList','deptList','sectionList','subSectionList'));

    }
  catch (\Exception $e) {
            $bug1 = $e->getMessage();
            return redirect()->back()->with('error', $bug1);
    }
  }
 # Daywise Manual Attendance Form Test
  public function dayManualTest(Request $request){
    try {
       //dd($request->all());

        $lineid=$request->line_id;
        $florid=$request->floor_id;
        $section=$request->section;
        $subSection=$request->subSection;

        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                   ->pluck('hr_unit_name', 'hr_unit_id');



        $departments= $info->unique('as_department_id');

        $unit_name= DB::table('hr_unit')
        ->where('hr_unit_id', $request->unit_id)
        ->pluck('hr_unit_name')
        ->first();

        if(!empty($lineid)){
          $line_name= DB::table('hr_line')
          ->where('hr_line_id', $request->line_id)
          ->pluck('hr_line_name')
          ->first();
        }
        else  $line_name="";

        if(!empty($florid)){
          $floor_name= DB::table('hr_floor')
          ->where('hr_floor_id', $florid)
          ->pluck('hr_floor_name')
          ->first();
        }
        else  $floor_name="";

        $report_date= $request->report_date;

        $floorList= DB::table('hr_floor')
                    ->where('hr_floor_unit_id', $request->unit_id)
                    ->pluck('hr_floor_name', 'hr_floor_id');
        $lineList= DB::table('hr_line')
                  ->where('hr_line_unit_id', $request->unit_id)
                  ->pluck('hr_line_name', 'hr_line_id');

        $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        $deptList= DB::table('hr_department')
                            ->where('hr_department_area_id', $request->area)
                            ->pluck('hr_department_name', 'hr_department_id');

        $sectionList= DB::table('hr_section')
                            ->where('hr_section_department_id', $request->department)
                            ->pluck('hr_section_name', 'hr_section_id');

        $subSectionList= DB::table('hr_subsection')
                            ->where('hr_subsec_section_id', $request->section)
                            ->pluck('hr_subsec_name', 'hr_subsec_id');




        return view('hr/timeattendance/attendance_daywise_manual_test', compact('unitList', 'departments', 'unit_name','floorList','floor_name', 'line_name', 'report_date','absent','present', 'lineList','areaList','deptList','sectionList','subSectionList'));

    }
  catch (\Exception $e) {
            $bug1 = $e->getMessage();
            return redirect()->back()->with('error', $bug1);
    }
  }

# Daywise Manual Attendance Form Test data
  public function dayManualTestData(Request $request){
    $floors= Floor::select('hr_floor_name', 'hr_floor_id')
    ->where('hr_floor_unit_id', $request->unit)
    ->get();

    $data= '<option value="">Select Floor</option>';
    foreach ($floors as $floor) {
      $data.='<option value="'.$floor->hr_floor_id.'">'.$floor->hr_floor_name.'</option>';
    }
    return $data;
  }


# Get Floor based on Unit
  public function getFloorByUnit(Request $request){
    $floors= Floor::select('hr_floor_name', 'hr_floor_id')
    ->where('hr_floor_unit_id', $request->unit)
    ->get();

    $data= '<option value="">Select Floor</option>';
    foreach ($floors as $floor) {
      $data.='<option value="'.$floor->hr_floor_id.'">'.$floor->hr_floor_name.'</option>';
    }
    return $data;
  }

# Get line based on Unit
  public function getLineByUnit(Request $request){
    $lines= Line::select('hr_line_name', 'hr_line_id')
    ->where('hr_line_unit_id', $request->unit)
    ->get();

    $data= '<option value="">Select Line</option>';
    foreach ($lines as $line) {
      $data.='<option value="'.$line->hr_line_id.'">'.$line->hr_line_name.'</option>';
    }

    return $data;
  }

 # Daywise Manual Attendance Store

  public function dayManualStore(Request $request)
  {
    //dd($request->all());exit;
    $unit=$request->unit_att;
    // Set Attendance Table and Column Name

      if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
        $tableName="hr_attendance_mbm";
        $colName= "id";
      }

      else if($unit ==2){
        $tableName="hr_attendance_ceil";
        $colName= "id";
      }

      else if($unit ==3){

        $tableName="hr_attendance_aql";
        $colName= "id";
      }

      else if($unit ==6){
        $tableName="hr_attendance_ho";
        $colName= "id";
      }

      else if($unit ==8){
        $tableName="hr_attendance_cew";
        $colName= "id";
      }
      else{
        $tableName="hr_attendance_mbm";
        $colName= "att_id";
      }

    DB::beginTransaction();
    try {

      for($i=0; $i<sizeof($request->attendance_id); $i++)
      {

          if($request->att_status[$i]=="P"){ //dd($request->att_status[$i]);

            if(($request->attendance_id[$i]==0)){

              //get shift code from shift roaster/basic information table according to attandance date
                $shift_code= null;
                $shift_ass= $request->ass_id[$i];
                $shift_year= date('Y', strtotime($request->startday));
                $shift_month= date('n', strtotime($request->startday));
                $shift_day= "day_".date('j', strtotime($request->startday));

              //get shift code from shift roaster
                $shift_code= DB::table('hr_shift_roaster')
                ->where('shift_roaster_year', $shift_year)
                ->where('shift_roaster_month', $shift_month)
                ->pluck($shift_day)
                ->first();

              //if Shift code is null then get default shift code from basic information table

                  $shift_code= DB::table('hr_as_basic_info As b')
                  ->where('b.as_id',$request->ass_id[$i])
                  ->leftJoin('hr_shift', function($q) {
                      $q->on('hr_shift.hr_shift_name', 'b.as_shift_id')
                        ->on('hr_shift.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE hr_shift.hr_shift_name = b.as_shift_id AND hr_shift.hr_shift_unit_id = b.as_unit_id )"));
                  })

                  ->pluck('hr_shift.hr_shift_code')
                  ->first();


            // Set intime and outtime with date
              $intime[$i]=$request->startday." ".$request->intime[$i];

              if($request->outtime[$i]==""){$outime[$i]=null;}
              else{$outime[$i]=$request->startday." ".$request->outtime[$i];}
                       //   dd($intime[$i]);

              // Store attendance
               if($request->intime[$i]!="00:00:00"){
                $lastPunchId = DB::table($tableName)->insertGetId([
                  'as_id'   =>$request->ass_id[$i],
                  'hr_shift_code'=> $shift_code,
                  'in_date' => date('Y-m-d', strtotime($intime[$i])),
                  'in_time' =>$intime[$i],
                  'out_time'=>$outime[$i],
                  'remarks'=>'BM',
                  'updated_by' => auth()->user()->associate_id,
                  'updated_at' => NOW()
                ]);
                $associate_id_emp = DB::table('hr_as_basic_info')->where('as_id',$request->ass_id[$i])->first();

                if($outime[$i] == "00:00:00"){
                  // ProcessAttendanceIntime queue run
                  $queue = (new ProcessAttendanceIntime($tableName, $lastPunchId, $associate_id_emp->as_unit_id))
                      ->delay(Carbon::now()->addSeconds(2));
                      dispatch($queue);
                }else{
                  // ProcessAttendanceInOutTime queue run
                  $queue = (new ProcessAttendanceInOutTime($tableName, $lastPunchId, $associate_id_emp->as_unit_id))
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);

                    $queue = (new BuyerManualAttandenceProcess($intime[$i], $request->ass_id[$i], $associate_id_emp->as_unit_id))
                      ->delay(Carbon::now()->addSeconds(2));
                      dispatch($queue);
                }


                // dd($associate_id_emp);exit;
                /*DB::table('hr_absent')->where('date',$request->startday)->where('associate_id',$associate_id_emp->associate_id)->delete();*/
                //process of ot calculation
              }

             }

               /* else {
                    $intime[$i]=$request->startday[$i]." ".$request->intime[$i];
                     $outime[$i]=$request->startday[$i]." ".$request->outtime[$i];
                    //dd($intime);

                   DB::table($tableName)
                    ->where($colName, $request->attendance_id[$i])
                    ->update([
                      'in_time' =>$intime[$i],
                      'out_time'=>$outime[$i],
                      'remarks'=>'BM',
                      'updated_by' => auth()->user()->associate_id,
                      'updated_at' => NOW()
                     ]);

                   }*/

                 }

      }
     DB::commit();

     return back()
               ->with('success', " Inserted Successfully!!");
    }
    catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            //$bug = $e->errorInfo[1];;
            return redirect()->back()->with('error',$bug);
    }


  }

}
