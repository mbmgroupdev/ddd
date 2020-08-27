<?php

namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Helpers\Attendance2;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\BuyerManualAttandenceProcess;
use App\Jobs\ProcessAttendanceInOutTime;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessAttendanceOuttime;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\AttendanceMBM;
use App\Models\Employee;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use DataTables, DB, Auth, ACL, stdClass;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function dailyAttendance()
    {
        //ACL::check(["permission" => "hr_time_daily_att_list"]);
        #-----------------------------------------------------------#
        return view('hr/timeattendance/daily_attendance_list');
    }

    public function getAttendanceData()
    {
        //ACL::check(["permission" => "hr_time_daily_att_list"]);
        #-----------------------------------------------------------#

        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table("hr_attendance AS a")
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                "b.associate_id",
                "b.as_name",
                "b.as_emp_type_id",
                "f.hr_floor_name",
                "a.in_time",
                "a.out_time",
                "a.hr_shift_code"
            )
            ->join("hr_as_basic_info AS b", function($join){
                $join->on( "b.as_id", "=", "a.as_id");
                $join->where( "b.as_status", "=", "1");
            })
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->leftJoin("hr_floor AS f", "f.hr_floor_id", "=", "b.as_floor_id")
            ->get();

        return DataTables::of($data)
        ->addColumn('ot', function ($data) {

            $out= date('H:i:s', strtotime($data->out_time));
            $shift_out= DB::table('hr_shift')
                        ->select('hr_shift_id', 'hr_shift_start_time', 'hr_shift_end_time')
                        ->where('hr_shift_code', "=", $data->hr_shift_code)
                        ->first();
            if($shift_out == null){
                $shift_out_time=0;
            }
            else{
                $shift_out_time=$shift_out->hr_shift_end_time;
            }
            if(($data->as_emp_type_id == 3) && ($out > $shift_out_time) && $shift_out_time !=0){
                    $hour= date('h', strtotime($out) - strtotime($shift_out_time));
                if(date('h',strtotime($out)) == date('h',strtotime($shift_out_time)))
                    $hour=0;
                    $minute= date('i', strtotime($out) - strtotime($shift_out_time));

                if($hour ==0 && $minute==0)
                    $ot="";
                else
                $ot= $hour." h ". $minute. " m";
            }
            else {
                $ot="" ;
            }
            return $ot;
            })
            ->rawColumns(['serial_no', 'ot'])
            ->toJson();
    }

    public function attendanceReport()
    {

        #-----------------------------------------------------------#
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');
        $floorList= [];
        $lineList= [];
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        $deptList= [];
        $sectionList= [];
        $subSectionList= [];

        return view('hr/operation/attendance/index', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList'));
    }

   public function makeAbsent(Request $request)
   {
       $previous = $request->all();

       $employee = DB::table('hr_as_basic_info')->select('as_id', 'as_unit_id')->where('associate_id',$request->associate_id)->first();
        if($employee == null){
            return 'error, Employee not found!';
        }
        $current['unit'] = $employee->as_unit_id;
        $current['associate_id'] = $request->associate_id;
         $current['date'] = $request->date;

        DB::beginTransaction();
        try {
            $data = EmployeeHelper::employeeDateWiseMakeAbsent($employee->as_id, $request->date);
            if($data['status'] != 'success'){
                return "error, ".$data['message'];
            }
            DB::table('event_history')
            ->insert([
                'user_id' => $previous['associate_id'],
                'type' => 3,
                'previous_event' =>  json_encode($previous),
                'modified_event' => json_encode($current),
                'created_by' => Auth::user()->associate_id
            ]);
            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return 'error, '.$bug;
        }
    }
    public function makeHalfday(Request $request)
    {
        $previous=$request->all();
        $employee = DB::table('hr_as_basic_info')->where('associate_id',$request->associate_id)->first();
        $tableName = $this->getTableName($employee->as_unit_id);
        $table = explode(' ',$tableName);
        $current['unit']=$employee->as_unit_id;
        $current['associate_id']=$request->associate_id;
        $current['date']=$request->date;
        $current['remarks']= 'HD';
        DB::beginTransaction();
        try {

            $attendanceData = DB::table($table[0])
              ->where('as_id',$employee->as_id)
              ->whereDate('in_time',date('Y-m-d',strtotime($request->date)))
              ->first();
               DB::table($table[0])
                ->where('id',$attendanceData->id)
                ->update([
                  'remarks'=>'HD'
                ]);

            DB::table('event_history')
                ->insert([
                    'user_id' => $previous['associate_id'],
                    'type' => 4,
                    'previous_event' =>  json_encode($previous),
                    'modified_event' => json_encode($current),
                    'created_by' => Auth::user()->associate_id
                ]);

              // sent to queue for salary calculation
            $year = Carbon::parse($request->date)->format('Y');
            $month = Carbon::parse($request->date)->format('m');
            $yearMonth = $year.'-'.$month;
            if($month == date('m')){
                $totalDay = date('d');
            }else{
                $totalDay = Carbon::parse($yearMonth)->daysInMonth;
            }
            $queue = (new ProcessUnitWiseSalary($table[0], $month, $year, $employee->as_id, $totalDay))
                    ->onQueue('salarygenerate')
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
            DB::commit();
            return 'success';
        } catch (\Exception $e) {
           DB::rollback();
           $bug = $e->getMessage();
          return "error, ".$bug;
        }

    }
    public function getTableName($unit)
    {
        $tableName = "";
        //CEIL
        if($unit == 2){
           $tableName= "hr_attendance_ceil AS a";
        }
        //AQl
        else if($unit == 3){
            $tableName= "hr_attendance_aql AS a";
        }
        // MBM
        else if($unit == 1 || $unit == 4 || $unit == 5 || $unit == 9){
            $tableName= "hr_attendance_mbm AS a";
        }
        //HO
        else if($unit == 6){
            $tableName= "hr_attendance_ho AS a";
        }
        // CEW
        else if($unit == 8){
            $tableName= "hr_attendance_cew AS a";
        }
        else{
            $tableName= "hr_attendance_mbm AS a";
        }
        return $tableName;
    }

    public function getEmpAttGetData($request)
    {
        $associate_id = isset($request['associate_id'])?$request['associate_id']:'';
        $report_from  = isset($request['report_from'])?$request['report_from']:date('Y-m-d');
        $report_to    = isset($request['report_to'])?$request['report_to']:date('Y-m-d');
        $unit         = isset($request['unit'])?$request['unit']:'';
        $otnonot      = isset($request['otnonot'])?$request['otnonot']:'';
        $areaid       = isset($request['area'])?$request['area']:'';
        $departmentid = isset($request['department'])?$request['department']:'';
        $lineid       = isset($request['line_id'])?$request['line_id']:'';
        $florid       = isset($request['floor_id'])?$request['floor_id']:'';
        $section      = isset($request['section'])?$request['section']:'';
        $subSection   = isset($request['subSection'])?$request['subSection']:'';

        $otCondition = '';
        if(!empty($request['ot_hour']) && $request['condition'] == 'Equal')
        {
          $otCondition = '=';

        }elseif (!empty($request['ot_hour']) && $request['condition'] == 'Less Than') {
          $otCondition = '<';
        }elseif (!empty($request['ot_hour']) && $request['condition'] == 'Greater Than') {
          $otCondition = '>';
        }

        $tableName = $this->getTableName($unit);
        $attData = DB::table($tableName);

        $attData->whereBetween('a.in_time', [date('Y-m-d',strtotime($report_from))." "."00:00:00", date('Y-m-d',strtotime($report_to))." "."23:59:59"]);

        $leaveData = DB::table('hr_leave');

        $leaveData->whereDate('leave_from','<=', $report_from);
        $leaveData->whereDate('leave_to','>=', $report_to);
        $leaveData->where('leave_status','1');
        $leaveData->groupBy('leave_ass_id');

        $attData_sql    = $attData->toSql();  // compiles to SQL
        $leaveData_sql  = $leaveData->toSql();  // compiles to SQL

        // unit to sql
        $unitData = DB::table('hr_unit');
        $unitData_sql    = $unitData->toSql();
        // hr_designation to sql
        $designationData = DB::table('hr_designation');
        $designationData_sql    = $designationData->toSql();
        // shift to sql
        $shiftData = DB::table('hr_shift');
        $shiftData_sql    = $shiftData->toSql();

        $query1 = DB::table('hr_as_basic_info AS b')
        ->select(
            "b.associate_id",
            "b.as_unit_id",
            "b.as_name",
            "b.as_pic",
            "b.as_oracle_code",
            "b.as_gender",
            "b.as_shift_id",
            "b.as_emp_type_id",
            "a.in_time",
            "a.out_time",
            "a.ot_hour",
            "a.hr_shift_code",
            "a.remarks",
            "a.late_status",
            "c.*",
            "s.hr_shift_id",
            "s.hr_shift_break_time",
            "s.hr_shift_start_time",
            "s.hr_shift_end_time",
            "s.hr_shift_name",
            "s.hr_shift_night_flag",
            "u.hr_unit_name",
            'dsg.hr_designation_name',
            "b.as_ot"
        )
        ->where('b.as_status', 1);
        if (!empty($unit)) {
            $query1->where('b.as_unit_id',$unit);
        }
        if ($request['otnonot']!=null) {
            $query1->where('b.as_ot',$otnonot);
        }
        if (!empty($associate_id)) {
            $query1->where('b.associate_id', $associate_id);
        }
        if(!empty($areaid)) {
            $query1->where('b.as_area_id',$areaid);
        }
        if(!empty($departmentid)) {
            $query1->where('b.as_department_id',$departmentid);
        }
        if(!empty($floorid)) {
            $query1->where('b.as_floor_id',$floorid);
        }
        if (!empty($lineid)) {
            $query1->where('b.as_line_id', $lineid);
        }
        if (!empty($section)) {
            $query1->where('b.as_section_id', $section);
        }
        if (!empty($subSection)) {
            $query1->where('b.as_subsection_id', $subSection);
        }

        if(!empty($otCondition)){
          $query1->where('a.ot_hour',$otCondition,'0'.$request['ot_hour'].'.00');
        }

        $query1->leftjoin(DB::raw('(' . $attData_sql. ') AS a'), function($join) use ($attData) {
            $join->on('a.as_id', '=', 'b.as_id')->addBinding($attData->getBindings());
        });
        $query1->leftjoin(DB::raw('(' . $leaveData_sql. ') AS c'), function($join1) use ($leaveData) {
            $join1->on('c.leave_ass_id', '=', 'b.associate_id')->addBinding($leaveData->getBindings());
        });
        $query1->leftjoin(DB::raw('(' . $designationData_sql. ') AS dsg'), function($joinDesignation) use ($designationData) {
            $joinDesignation->on('dsg.hr_designation_id', 'b.as_designation_id')->addBinding($designationData->getBindings());
        });
        $query1->leftjoin(DB::raw('(' . $unitData_sql. ') AS u'), function($joinUnit) use ($unitData) {
            $joinUnit->on("u.hr_unit_id", "=", "b.as_unit_id")->addBinding($unitData->getBindings());
        });
        $query1->leftjoin(DB::raw('(' . $shiftData_sql. ') AS s'), function($joinShift) use ($shiftData) {
            $joinShift->on("a.hr_shift_code", "=", "s.hr_shift_code")->addBinding($shiftData->getBindings());
        });
        // $query1->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id');
        // $query1->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id");
        // $query1->leftJoin("hr_shift AS s", "a.hr_shift_code", "=", "s.hr_shift_code");
        // $query1->groupBy('b.associate_id');
        //dd($query1);exit;
        $employee_list = $query1->get();
        dd($employee_list);

        $data = [];
        foreach($employee_list as $k=>$employee) {
            if ($employee->in_time == null) {
                if(!empty($employee->leave_type)){

                    $employee->status = $employee->leave_type.' Leave';
                    $employee->hr_shift_name = $employee->as_shift_id;
                    $data[] = $employee; //'Leave';
                }

            }else {
                if($employee->remarks == 'DSI'){
                    $time = explode(' ',$employee->in_time);
                    if($employee->late_status == 1){
                        $employee->status = 'Present (Late)';
                        $employee->in_time = null;
                        $data[] = $employee; //'Present (Late)';
                    } else {
                        $employee->status = 'Present';
                        $employee->in_time = null;
                        $data[] = $employee; //'Present';
                    }
               }elseif ($employee->remarks == null) {
                    $time = explode(' ',$employee->in_time);
                    if($employee->late_status == 1){
                        $employee->status = 'Present (Late)';
                        $data[] = $employee; //'Present (Late)';
                    } else {
                        $employee->status = 'Present';
                        $data[] = $employee; //'Present';
                    }
                }elseif ($employee->remarks == 'HD') {
                    $employee->status = 'Present (Halfday)';
                    $data[] = $employee; //'Present';
                }
                else{

                    $time = explode(' ',$employee->in_time);
                    if($employee->late_status == 1){
                        $employee->status = 'Present (Late)';
                        $data[] = $employee; //'Present (Late)';

                    }else{
                        $employee->status = 'Present';
                        $data[] = $employee; //'Present';

                    }
                }

            }
        }
        return $data;
    }

    public function getHolidayData($request){

        $associate_id = isset($request['associate_id'])?$request['associate_id']:'';
        $report_from  = isset($request['report_from'])?$request['report_from']:date('Y-m-d');
        $report_to    = isset($request['report_to'])?$request['report_to']:date('Y-m-d');
        $unit         = isset($request['unit'])?$request['unit']:'';
        $otnonot      = isset($request['otnonot'])?$request['otnonot']:'';
        $areaid       = isset($request['area'])?$request['area']:'';
        $departmentid = isset($request['department'])?$request['department']:'';
        $lineid       = isset($request['line_id'])?$request['line_id']:'';
        $florid       = isset($request['floor_id'])?$request['floor_id']:'';
        $section      = isset($request['section'])?$request['section']:'';
        $subSection   = isset($request['subSection'])?$request['subSection']:'';

        $query1 = DB::table('hr_as_basic_info AS b')
         ->select(
           "b.associate_id",
           "b.as_unit_id",
           "b.as_name",
           "b.as_pic",
           "b.as_gender",
           "b.as_contact as cell",
           "b.as_section_id",
           "b.as_shift_id",
           "b.as_oracle_code",
           "sec.hr_section_name as section",
           "b.as_emp_type_id",
           "hdr.*",
           "s.hr_shift_break_time",
           "s.hr_shift_start_time",
           "s.hr_shift_end_time",
           "s.hr_shift_name",
           "u.hr_unit_name",
           'dsg.hr_designation_name',
           "b.as_ot"
           )
           ->where('as_status', 1);
           if (!empty($unit)) {
             $query1->where('b.as_unit_id',$unit);
           }
           if (!empty($associate_id)) {
             $query1->where('b.associate_id', $associate_id);
           }
           if(!empty($areaid)) {
             $query1->where('b.as_area_id',$areaid);
           }
           if(!empty($departmentid)) {
             $query1->where('b.as_department_id',$departmentid);
           }
           if(!empty($floorid)) {
             $query1->where('b.as_floor_id',$floorid);
           }
           if (!empty($lineid)) {
             $query1->where('b.as_line_id', $lineid);
           }
           if (!empty($section)) {
             $query1->where('b.as_section_id', $section);
           }
           if (!empty($subSection)) {
             $query1->where('b.as_subsection_id', $subSection);
           }

           if (!empty($report_from) && !empty($report_to)) {
             $query1->whereBetween('hdr.date',array($request['report_from'],$request['report_to']));

           }
           $query1->where('hdr.remarks', 'Holiday');


           $query1->Join('holiday_roaster AS hdr', 'hdr.as_id', 'b.associate_id');
           $query1->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id');
           $query1->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id");
           $query1->leftJoin("hr_shift AS s", "b.as_shift_id", "=", "s.hr_shift_id");
           $query1->leftJoin("hr_section AS sec", "sec.hr_section_id", "b.as_section_id");

           $employee_list = $query1->get();

           $data =[];
           $asids = array_unique(array_column($employee_list->toArray(),'associate_id'),SORT_REGULAR);
           foreach ($asids as $k=>$asid) {
             $dates = '';
             $count = 1;
             $rs = new Shift;
             $ck =0;
             foreach ($employee_list as $d) {
               $ck++;
                 if($asid == $d->associate_id){

                   $dat = date('d', strtotime($d->date));

                   $dates .= $dat.',';

                   $rs->dates = $dates;
                   $rs->absent_count =$count;
                   $rs->associate_id        = $d->associate_id;
                   $rs->as_unit_id          = $d->hr_unit_name;
                   $rs->as_name             = $d->as_name;
                   $rs->as_oracle_code      = $d->as_oracle_code;
                   $rs->as_emp_type_id = $d->as_emp_type_id;
                   $rs->in_time     = null;
                   $rs->out_time     = null;
                   $rs->ot_hour     = 0;

                   $rs->cell                = $d->cell ;
                   $rs->section             = $d->section;
                   $rs->as_pic              = $d->as_pic;
                   $rs->as_gender           = $d->as_gender;
                   $rs->hr_designation_name = $d->hr_designation_name;
                   $rs->hr_unit_name        = $d->hr_unit_name;
                   $rs->dates         = $report_from;

                   $rs->hr_shift_name      = $d->as_shift_id;
                   $rs->hr_unit_name      = $d->hr_unit_name;
                   $rs->as_ot              = $d->as_ot;
                   $rs->date              = $dates;
                   $rs->status              = 'Holiday';
                   $data[$k] = $rs;
                   $count++;
                 }
               }
           }

        return $data;
    }


    public function getAbsentData($request){

      $areaid       = isset($request['area'])?$request['area']:'';
      $otnonot      = isset($request['otnonot'])?$request['otnonot']:'';
      $departmentid = isset($request['department'])?$request['department']:'';
      $lineid       = isset($request['line_id'])?$request['line_id']:'';
      $florid       = isset($request['floor_id'])?$request['floor_id']:'';
      $section      = isset($request['section'])?$request['section']:'';
      $subSection   = isset($request['subSection'])?$request['subSection']:'';

         $absentData = DB::table('hr_absent')
                         ->where('hr_unit',$request['unit'])
                         ->whereBetween('date',array($request['report_from'],$request['report_to']))
                         ->when(!empty($areaid), function ($query) use($areaid){
                           return $query->where('hr_as_basic_info.as_area_id',$areaid);
                         })
                         ->when(!empty($departmentid), function ($query) use($departmentid){
                           return $query->where('hr_as_basic_info.as_department_id',$departmentid);
                         })
                         ->when(!empty($lineid), function ($query) use($lineid){
                           return $query->where('hr_as_basic_info.as_line_id', $lineid);
                         })
                         ->when(!empty($florid), function ($query) use($florid){
                           return $query->where('hr_as_basic_info.as_floor_id',$florid);
                         })
                         ->when($request['otnonot']!=null, function ($query) use($otnonot){
                           return $query->where('hr_as_basic_info.as_ot',$otnonot);
                         })
                         ->when(!empty($section), function ($query) use($section){
                           return $query->where('hr_as_basic_info.as_section_id', $section);
                         })
                         ->when(!empty($subSection), function ($query) use($subSection){
                           return $query->where('hr_as_basic_info.as_subsection_id', $subSection);
                         })
                         ->leftJoin('hr_as_basic_info','hr_absent.associate_id','hr_as_basic_info.associate_id')
                         ->leftJoin('hr_designation', 'hr_designation.hr_designation_id', 'hr_as_basic_info.as_designation_id')
                         ->leftJoin("hr_unit", "hr_unit.hr_unit_id", "hr_as_basic_info.as_unit_id")
                         ->leftJoin('hr_shift', function($q) {
                             $q->on('hr_shift.hr_shift_name', 'hr_as_basic_info.as_shift_id')
                               ->on('hr_shift.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE hr_shift.hr_shift_name = hr_as_basic_info.as_shift_id AND hr_shift.hr_shift_unit_id = hr_as_basic_info.as_unit_id )"));
                         })
                         ->where('hr_as_basic_info.as_status', 1)
                         ->get();

                $data = [];
                  $i = 0;
                foreach ($absentData as $absent) {
                     //dd($absent);exit;
                  $d = new Employee; // creating a blank object
                  $d->associate_id = $absent->associate_id;
                  $d->as_unit_id   = $absent->hr_unit;
                  $d->as_name     = $absent->as_name;
                  $d->as_pic      = $absent->as_pic;
                  $d->as_oracle_code      = $absent->as_oracle_code;
                  $d->as_gender    = $absent->as_gender;
                  $d->as_emp_type_id = $absent->as_emp_type_id;
                  $d->in_time     = null;
                  $d->out_time     = null;
                  $d->ot_hour     = 0;

                  $d->hr_shift_code = $absent->hr_shift_code;
                  $d->hr_shift_break_time = $absent->hr_shift_break_time;
                  $d->hr_shift_start_time = $absent->hr_shift_start_time;
                  $d->hr_shift_end_time = $absent->hr_shift_end_time;
                  $d->hr_shift_name      = $absent->as_shift_id;
                  $d->hr_unit_name      = $absent->hr_unit_name;
                  $d->hr_designation_name = $absent->hr_designation_name;
                  $d->as_ot              = $absent->as_ot;
                  $d->date              = $absent->date;
                  $d->status              = 'Absent';

                  $data[$i] = $d; //assigning object into array
                 $i++;
                }
                return $data;

    }
    public function unitWiseAttendance($request)
    {
        $getEmployee = Employee::where('as_unit_id', $request['unit']);
        $employeeToSql = $getEmployee->toSql();

        $attData = AttendanceMBM::select('hr_attendance_mbm.*', 'b.*')
        ->whereBetween('in_time', [date('Y-m-d',strtotime($request['report_from']))." "."00:00:00", date('Y-m-d',strtotime($request['report_to']))." "."23:59:59"]);

        $attData->leftjoin(DB::raw('(' . $employeeToSql. ') AS b'), function($join) use ($getEmployee) {
            $join->on('hr_attendance_mbm.as_id', '=', 'b.as_id')->addBinding($getEmployee->getBindings());
        });
        $attData->where('b.as_status', 1);
        if($request['area'] != null){
            $attData->where('b.as_area_id', $request['area']);
        }
        if($request['department'] != null){
            $attData->where('b.as_department_id', $request['department']);
        }
        if($request['section'] != null){
            $attData->where('b.as_section_id', $request['section']);
        }
        if($request['subSection'] != null){
            $attData->where('b.as_subsection_id', $request['subSection']);
        }
        if($request['floor_id'] != null){
            $attData->where('b.as_floor_id', $request['floor_id']);
        }
        if($request['line_id'] != null){
            $attData->where('b.as_line_id', $request['line_id']);
        }
        if($request['otnonot'] != null){
            $attData->where('b.as_ot', $request['otnonot']);
        }
        if($request['otnonot'] != null){
            $attData->where('b.as_ot', $request['otnonot']);
        }
        if($request['ot_hour'] != null){
            if($request['condition'] == 'Less Than'){
                $sign = '<';
            }else if($request['condition'] == 'Greater Than'){
                $sign = '>';
            }else{
                $sign = '=';
            }
            $attData->where('hr_attendance_mbm.ot_hour', $sign, $request['ot_hour']);
        }
        $attData->addSelect(DB::raw("'all' as reportType"));
        return $attData->get();
    }
    public function attendanceReportData(Request $request){
        // ACL::check(["permission" => "hr_time_daily_att_list"]);
        #-----------------------------------------------------------#
        $data = [];
        $type = $request->type;
        // return $request->all();
        if($type == 'Absent'){
          $data = $this->getAbsentData($request->all());
        }elseif ($type == 'All') {
            // $results = $this->getEmpAttGetData($request->all());
            // foreach ($results as $d) {
            //     if($d->status == 'Present' || $d->status == 'Present (Late)')
            //         $data[] = $d;
            // }
            $data = $this->unitWiseAttendance($request->all());

        }elseif ($type == 'Present(Intime Empty)') {
          $results = $this->getEmpAttGetData($request->all());
          foreach ($results as $d) {
            if($d->status == 'Present' && $d->in_time == '' )
              $data[] = $d;
          }
        }elseif ($type == 'Present(Outtime Empty)') {
          $results = $this->getEmpAttGetData($request->all());
          foreach ($results as $d) {
            if($d->status == 'Present' && $d->out_time == '' )
              $data[] = $d;
          }
        }elseif ($type == 'Present (Late(Outtime Empty))') {
          $results = $this->getEmpAttGetData($request->all());
          foreach ($results as $d) {
            if($d->status == 'Present (Late)' && $d->out_time == '' )
              $data[] = $d;
          }
        }elseif ($type == 'Present (Halfday)') {
          $results = $this->getEmpAttGetData($request->all());
          foreach ($results as $d) {
            if($d->status == 'Present (Halfday)' && $d->out_time == '' )
              $data[] = $d;
          }
        }elseif ($type == 'Present') {
          $results = $this->getEmpAttGetData($request->all());
          foreach ($results as $d) {
            if($d->status == 'Present' && $d->out_time != '' && $d->in_time != '')
              $data[] = $d;
          }
        }elseif ($type == 'Leave') {
          $results = $this->getEmpAttGetData($request->all());
          foreach ($results as $d) {
            if(($d->status == 'Casual Leave' || $d->status == 'Maternity Leave' || $d->status == 'Sick Leave') && $d->out_time == '' && $d->in_time == '')
              $data[] = $d;
          }
        }elseif ($type == 'Present (Late)') {
          $results = $this->getEmpAttGetData($request->all());
          foreach ($results as $d) {
            if($d->status == 'Present (Late)' && $d->out_time != '' && $d->in_time != '')
              $data[] = $d;
          }
        }elseif ($type == 'Holiday') {
          $data = $this->getHolidayData($request->all());

        }
        $date = isset($request->report_from)?$request->report_from:date('Y-m-d');

        return DataTables::of($data)->addIndexColumn()
            ->addColumn('edit_jobcard', function($data) {
              //dd($data);exit;
                $date = '';
                if ($data->in_time != null) {
                    $date = date('Y-F-d', strtotime($data->in_time));
                } elseif($data->in_time == null && $data->out_time == null && $data->status == 'Absent' ){
                    $date = date('Y-F-d', strtotime($data->date));
                } elseif($data->in_time == null && $data->out_time == null && $data->status != 'Absent' && $data->status != 'Holiday' ){
                    $date = date('Y-F-d', strtotime($data->leave_from));
                } elseif($data->in_time == null && $data->out_time == null && $data->status == 'Holiday' ){

                    $date = date('Y-F-d', strtotime($data->dates));
                }

                else {
                    $date = date('Y-F-d', strtotime($data->out_time));
                }

                list($year,$month,$day) = explode('-',$date);
                $url = 'hr/reports/job_card?associate='.$data->associate_id.'&month='.$month.'&year='.$year;
                return '<a href="'.url($url).'" target="blank">Job Card</a>';
            })
            ->addColumn('associate_id', function($data) {
                return $data->associate_id;
            })
            ->addColumn('att_date', function ($data) use ($date) {
                if ($data->in_time != null) {
                    return date('Y-m-d', strtotime($data->in_time));
                } elseif($data->in_time == null && $data->out_time == null && $data->status == 'Absent' ){
                    return date('Y-m-d', strtotime($data->date));
                }
                elseif($data->in_time == null && $data->out_time == null && $data->status != 'Absent' && $data->status != 'Holiday' ){
                    return '<strong>'.date('Y-m-d', strtotime($data->leave_from)).'</strong>'.' To '.'<strong>'.date('Y-m-d', strtotime($data->leave_to)).'</strong>';
                }
                elseif($data->in_time == null && $data->out_time == null && $data->status == 'Holiday' ){
                    return $data->date;
                }
                else{
                    return date('Y-m-d', strtotime($data->out_time));
                }
            })

            ->addColumn('oracle_id', function ($data) {
                if(!empty($data->as_oracle_code)){
                    return $data->as_oracle_code;
                }
            })
            ->addColumn('hr_designation_name', function ($data) {
                if($data->as_unit_id == 1 && isset($data->reportType) && $data->reportType == 'all'){
                    if(!empty($data->as_designation_id)){
                        return $data->employee->designation['hr_designation_name'];
                    }
                }else{
                    if(!empty($data->hr_designation_name)){
                        return $data->hr_designation_name;
                    }
                }
            })
            ->addColumn('hr_shift_name', function ($data) {
                 //dd($data->hr_shift_name);exit;
                if(!empty($data->hr_shift_name)){
                    return $data->hr_shift_name;
                }else{
                  return $data->as_shift_id;
                }
            })
            ->addColumn('att_status', function ($data) use ($date) {
                if($data->as_unit_id == 1 && isset($data->reportType) && $data->reportType == 'all'){
                    if($data->remarks == 'DSI'){
                        if($data->late_status == 1){
                            $data->status = 'Present (Late)';
                        } else {
                            $data->status = 'Present';
                        }
                   }elseif ($data->remarks == null) {
                        if($data->late_status == 1){
                            $data->status = 'Present (Late)';
                        } else {
                            $data->status = 'Present';
                        }
                    }elseif ($data->remarks == 'HD') {
                        $data->status = 'Present (Halfday)';
                    }
                    else{
                        if($data->late_status == 1){
                            $data->status = 'Present (Late)';
                        }else{
                            $data->status = 'Present';
                        }
                    }

                }

                if(($data->status == 'Casual Leave' || $data->status == 'Maternity Leave' || $data->status == 'Sick Leave')){
                   return '<span class="label label-md label-warning">'.$data->leave_type.' Leave</span>';
                }
                if($data->status == 'Absent') {
                   return '<span class="label label-md label-danger">Absent</span>';
                }
                if($data->status == 'Holiday') {
                   return '<span class="label label-md label-danger">Holiday</span>';
                }
                if($data->status == 'Present (Late)' || $data->status == 'Present (Halfday)'){
                   if($data->in_time == null){
                     return '<span class="label label-md label-warning">Late</span><span class="label label-sm label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-absent" >Make Absent</button>';
                   }elseif ($data->out_time == null && $data->remarks != 'HD') {
                     return '<span class="label label-md label-warning">Late</span><span class="label label-sm label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-halfday" >Make Halfday</button>';
                   }elseif ($data->out_time == null && $data->remarks == 'HD') {
                     $time = explode(' ',$data->in_time);
                     if(strtotime($time[1]) >(strtotime($data->hr_shift_start_time)+180)){
                       return '<span class="label label-md label-warning">Late</span><span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';

                     } else {
                       return '<span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';

                     }
                   }
                   else{
                     return '<span class="label label-md label-warning">Late</span><span class="label label-sm label-success">Present</span>';
                   }
                }
                if($data->status == 'Present' || $data->status == 'Present (Halfday)') {
                   if($data->in_time == null){
                     return '<span class="label label-md  label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-absent" >Make Absent</button>';
                   }elseif ($data->out_time == null && $data->remarks != 'HD') {
                     return '<span class="label label-md  label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-halfday" >Make Halfday</button>';
                   }elseif ($data->out_time == null && $data->remarks == 'HD') {
                     $time = explode(' ',$data->in_time);
                     if(strtotime($time[1]) >(strtotime($data->hr_shift_start_time)+180)){
                       return '<span class="label label-md label-warning">Late</span><span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';
                     } else {
                       return '<span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';
                     }
                   }
                   else{
                     return '<span class="label label-md label-success">Present</span>';
                   }
                }
            })
            ->addColumn('in_punch', function ($data) {

                if ($data->in_time !=null)
                {
                    $inTime = date('H:i:s', strtotime($data->in_time));
                    if($inTime == '00:00:00' || $data->remarks == 'DSI'){
                        return null;
                    }
                    return $inTime;
                }
            })
            ->addColumn('out_punch', function ($data) {
                if ($data->out_time != null)
                {
                    $outTime = date('H:i:s', strtotime($data->out_time));
                    if($outTime == '00:00:00'){
                        return null;
                    }
                    return $outTime;
                }
            })

            ->addColumn('ot', function ($data) {
              if ($data->as_ot == 1){
                /**/
                $expdata = explode('.',$data->ot_hour);
                return !empty($expdata[1])?$expdata[0].':'.($expdata[1]=='50'?'30':'00'):$data->ot_hour;
              }else{
                return 'Non OT';
              }
            })
            ->rawColumns(['edit_jobcard','associate_id','ot', 'in_punch', 'out_punch', 'att_date','att_status','oracle_id'])
            ->make(true);
            // ->toJson();
    }

    // public function attendanceReportData(Request $request){
    //     // ACL::check(["permission" => "hr_time_daily_att_list"]);
    //     #-----------------------------------------------------------#

    //     $data = [];
    //     $type = $request->type;
    //     if($type == 'Absent'){
    //       $data = $this->getAbsentData($request->all());
    //     }elseif ($type == 'All') {

    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if($d->status == 'Present' || $d->status == 'Present (Late)')
    //           $data[] = $d;
    //       }

    //     }elseif ($type == 'Present(Intime Empty)') {
    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if($d->status == 'Present' && $d->in_time == '' )
    //           $data[] = $d;
    //       }
    //     }elseif ($type == 'Present(Outtime Empty)') {
    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if($d->status == 'Present' && $d->out_time == '' )
    //           $data[] = $d;
    //       }
    //     }elseif ($type == 'Present (Late(Outtime Empty))') {
    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if($d->status == 'Present (Late)' && $d->out_time == '' )
    //           $data[] = $d;
    //       }
    //     }elseif ($type == 'Present (Halfday)') {
    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if($d->status == 'Present (Halfday)' && $d->out_time == '' )
    //           $data[] = $d;
    //       }
    //     }elseif ($type == 'Present') {
    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if($d->status == 'Present' && $d->out_time != '' && $d->in_time != '')
    //           $data[] = $d;
    //       }
    //     }elseif ($type == 'Leave') {
    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if(($d->status == 'Casual Leave' || $d->status == 'Maternity Leave' || $d->status == 'Sick Leave') && $d->out_time == '' && $d->in_time == '')
    //           $data[] = $d;
    //       }
    //     }elseif ($type == 'Present (Late)') {
    //       $results = $this->getEmpAttGetData($request->all());
    //       foreach ($results as $d) {
    //         if($d->status == 'Present (Late)' && $d->out_time != '' && $d->in_time != '')
    //           $data[] = $d;
    //       }
    //     }elseif ($type == 'Holiday') {
    //       $data = $this->getHolidayData($request->all());

    //     }
    //     $date = isset($request->report_from)?$request->report_from:date('Y-m-d');

    //     return DataTables::of($data)->addIndexColumn()
    //         ->addColumn('edit_jobcard', function($data) {
    //             $date = '';
    //             if ($data->in_time != null) {
    //                 $date = date('Y-F-d', strtotime($data->in_time));
    //             } elseif($data->in_time == null && $data->out_time == null && $data->status == 'Absent' ){
    //                 $date = date('Y-F-d', strtotime($data->date));
    //             } elseif($data->in_time == null && $data->out_time == null && $data->status != 'Absent' ){
    //                 $date = date('Y-F-d', strtotime($data->leave_from));
    //             } else {
    //                 $date = date('Y-F-d', strtotime($data->out_time));
    //             }
    //             list($year,$month,$day) = explode('-',$date);
    //             $url = 'hr/reports/job_card?associate='.$data->associate_id.'&month='.$month.'&year='.$year;
    //             return '<a href="'.url($url).'" target="blank">Job Card</a>';
    //         })
    //         ->addColumn('associate_id', function($data) {
    //             return $data->associate_id;
    //         })
    //         ->addColumn('att_date', function ($data) use ($date) {
    //             if ($data->in_time != null) {
    //                 return date('Y-m-d', strtotime($data->in_time));
    //             } elseif($data->in_time == null && $data->out_time == null && $data->status == 'Absent' ){
    //                 return date('Y-m-d', strtotime($data->date));
    //             }
    //             elseif($data->in_time == null && $data->out_time == null && $data->status != 'Absent' && $data->status != 'Holiday' ){
    //                 return '<strong>'.date('Y-m-d', strtotime($data->leave_from)).'</strong>'.' To '.'<strong>'.date('Y-m-d', strtotime($data->leave_to)).'</strong>';
    //             }
    //             elseif($data->in_time == null && $data->out_time == null && $data->status == 'Holiday' ){
    //                 return $data->date;
    //             }
    //             else{
    //                 return date('Y-m-d', strtotime($data->out_time));
    //             }
    //         })

    //         ->addColumn('oracle_id', function ($data) {
    //             if(!empty($data->as_oracle_code)){
    //                 return $data->as_oracle_code;
    //             }
    //         })
    //         ->addColumn('att_status', function ($data) use ($date) {
    //             if(($data->status == 'Casual Leave' || $data->status == 'Maternity Leave' || $data->status == 'Sick Leave')){
    //                return '<span class="label label-md label-warning">'.$data->leave_type.' Leave</span>';
    //             }
    //             if($data->status == 'Absent') {
    //                return '<span class="label label-md label-danger">Absent</span>';
    //             }
    //             if($data->status == 'Holiday') {
    //                return '<span class="label label-md label-danger">Holiday</span>';
    //             }
    //             if($data->status == 'Present (Late)' || $data->status == 'Present (Halfday)'){
    //                if($data->in_time == null){
    //                  return '<span class="label label-md label-warning">Late</span><span class="label label-sm label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-absent" >Make Absent</button>';
    //                }elseif ($data->out_time == null && $data->remarks != 'HD') {
    //                  return '<span class="label label-md label-warning">Late</span><span class="label label-sm label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-halfday" >Make Halfday</button>';
    //                }elseif ($data->out_time == null && $data->remarks == 'HD') {
    //                  $time = explode(' ',$data->in_time);
    //                  if(strtotime($time[1]) >(strtotime($data->hr_shift_start_time)+180)){
    //                    return '<span class="label label-md label-warning">Late</span><span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';

    //                  } else {
    //                    return '<span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';

    //                  }
    //                }
    //                else{
    //                  return '<span class="label label-md label-warning">Late</span><span class="label label-sm label-success">Present</span>';
    //                }
    //             }
    //             if($data->status == 'Present' || $data->status == 'Present (Halfday)') {
    //                if($data->in_time == null){
    //                  return '<span class="label label-md  label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-absent" >Make Absent</button>';
    //                }elseif ($data->out_time == null && $data->remarks != 'HD') {
    //                  return '<span class="label label-md  label-success">Present</span> <button class="btn btn-xs btn-round btn-danger make-halfday" >Make Halfday</button>';
    //                }elseif ($data->out_time == null && $data->remarks == 'HD') {
    //                  $time = explode(' ',$data->in_time);
    //                  if(strtotime($time[1]) >(strtotime($data->hr_shift_start_time)+180)){
    //                    return '<span class="label label-md label-warning">Late</span><span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';
    //                  } else {
    //                    return '<span class="label label-md  label-success">Present</span> <span class="label label-md  label-danger">Halfday</span>';
    //                  }
    //                }
    //                else{
    //                  return '<span class="label label-md label-success">Present</span>';
    //                }
    //             }
    //         })
    //         ->addColumn('in_punch', function ($data) {
    //             if ($data->in_time !=null)
    //             {
    //                 return date('H:i:s', strtotime($data->in_time));
    //             }
    //         })
    //         ->addColumn('out_punch', function ($data) {
    //             if ($data->out_time != null)
    //             {
    //                 return date('H:i:s', strtotime($data->out_time));
    //             }
    //         })

    //         ->addColumn('ot', function ($data) {
    //           if ($data->as_ot == 1){
    //             /**/
    //             $expdata = explode('.',$data->ot_hour);
    //             return !empty($expdata[1])?$expdata[0].':'.($expdata[1]=='50'?'30':'00'):$data->ot_hour;
    //           }else{
    //             return 'Non OT';
    //           }
    //         })
    //         ->rawColumns(['edit_jobcard','associate_id','ot', 'in_punch', 'out_punch', 'att_date','att_status','oracle_id'])
    //         ->make(true);
    //         // ->toJson();
    // }

    public function saveFromReport(Request $request)
    {
       $current=$request->all();
       $table = $this->getTableName($request->unit);
       $tableNmae = explode(' ',$table);
       $userInfo = DB::table('hr_as_basic_info')->where('associate_id',$request->associate_id)->first();
       
        $intime = date('Y-m-d', strtotime($request->date))." ".date('H:i:s', strtotime($request->in_punch_new));
        if(strtotime($request->in_punch_new) > strtotime($request->out_punch_new))
        {
          $ndate = date('Y-m-d',strtotime("+1 day", strtotime($request->date)));
          $outime = date('Y-m-d', strtotime($ndate))." ".date('H:i:s', strtotime($request->out_punch_new));
        }else{
          $outime = date('Y-m-d', strtotime($request->date))." ".date('H:i:s', strtotime($request->out_punch_new));
        }

        if($request->type == 'in'){
            if($request->out_punch_new == '00:00:00' && (Auth::user()->hasRole('super user'))){
                $employee = DB::table('hr_as_basic_info')->where('associate_id',$request->associate_id)->first();
                $attd = DB::table($tableNmae[0])
                    ->where('as_id',$userInfo->as_id)
                    ->whereDate('out_time',date('Y-m-d', strtotime($request->date)))
                    ->first();
                if($attd->in_time == null){
                    $data = EmployeeHelper::employeeDateWiseMakeAbsent($employee->as_id, $request->date);
                    if($data['status'] != 'success'){
                        return "error, ".$data['message'];
                    }
                    DB::table('event_history')
                      ->insert([
                        'user_id' => $current['associate_id'],
                        'type' => 3,
                        'previous_event' =>  json_encode($attd),
                        'modified_event' => json_encode($current),
                        'created_by' => Auth::user()->associate_id
                    ]);

                }
            }else{

                $attd = DB::table($tableNmae[0])
                ->where('as_id',$userInfo->as_id)
                ->whereDate('out_time',date('Y-m-d', strtotime($request->date)))
                ->first();

                DB::table($tableNmae[0])
                ->where('id',$attd->id)
                ->update([
                    'in_date' => date('Y-m-d', strtotime($intime)),
                    'in_time' => date('Y-m-d H:i:s', strtotime($intime)),
                    'out_time'=> date('Y-m-d H:i:s', strtotime($outime)),
                    'ot_hour' => $request->ot_new,
                    'remarks'=>'BM'
                   ]);
                DB::table('event_history')
                ->insert([
                    'user_id' => $current['associate_id'],
                    'type' => 1,
                    'previous_event' =>  json_encode($attd),
                    'modified_event' => json_encode($current),
                    'created_by' => Auth::user()->associate_id
                ]);
                // sent to queue for salary calculation
                $year = Carbon::parse($request->date)->format('Y');
                $month = Carbon::parse($request->date)->format('m');
                $yearMonth = $year.'-'.$month;
                if($month == date('m')){
                   $totalDay = date('d');
                }else{
                   $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                }

                $queue = (new ProcessAttendanceOuttime($tableNmae[0], $attd->id, $userInfo->as_unit_id))
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
            }
        }else{

            if($request->in_punch_new == '00:00:00' && (Auth::user()->hasRole('super user'))){
                $employee = DB::table('hr_as_basic_info')->where('associate_id',$request->associate_id)->first();

                $attd = DB::table($tableNmae[0])
                     ->where('as_id',$userInfo->as_id)
                     ->whereDate('in_time',date('Y-m-d', strtotime($request->date)))
                     ->first();
                if($attd->out_time == null){
                    $data = EmployeeHelper::employeeDateWiseMakeAbsent($employee->as_id, $request->date);
                    if($data['status'] != 'success'){
                        return "error, ".$data['message'];
                    }
                    DB::table('event_history')
                    ->insert([
                        'user_id' => $request->associate_id,
                        'type' => 3,
                        'previous_event' =>  json_encode($attd),
                        'modified_event' => json_encode($current),
                        'created_by' => Auth::user()->associate_id
                    ]);

                }

            }else{

                $attd = DB::table($tableNmae[0])
                ->where('as_id',$userInfo->as_id)
                ->whereDate('in_time',date('Y-m-d', strtotime($request->date)))
                ->first();

                DB::table($tableNmae[0])
                ->where('id',$attd->id)
                ->update([
                    'in_date' => date('Y-m-d', strtotime($intime)),
                    'in_time' => date('Y-m-d H:i:s', strtotime($intime)),
                    'out_time'=> date('Y-m-d H:i:s', strtotime($outime)),
                    'ot_hour' => $request->ot_new,
                    'remarks'=>'BM'
                   ]);
                DB::table('event_history')
                ->insert([
                    'user_id' => $current['associate_id'],
                    'type' => 1,
                    'previous_event' =>  json_encode($attd),
                    'modified_event' => json_encode($current),
                    'created_by' => Auth::user()->associate_id
                ]);
                 // sent to queue for salary calculation
                $year = Carbon::parse($request->date)->format('Y');
                $month = Carbon::parse($request->date)->format('m');
                $yearMonth = $year.'-'.$month;
                if($month == date('m')){
                    $totalDay = date('d');
                }else{
                    $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                }
                $queue = (new ProcessAttendanceOuttime($tableNmae[0], $attd->id, $userInfo->as_unit_id))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            }
        }
        if($attd){
          return 'success';
        }else{
          return 'error';
        }
    }

    public function saveFromReportAbsent(Request $request)
    {
        $current=$request->all();

        $table = $this->getTableName($request->unit);
        $tableNmae = explode(' ',$table);
        $userInfo = DB::table('hr_as_basic_info')
                    ->where('associate_id',$request->associate_id)
                    ->leftJoin('hr_shift', function($q) {
                        $q->on('hr_shift.hr_shift_name', 'hr_as_basic_info.as_shift_id')
                           ->on('hr_shift.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE hr_shift.hr_shift_name = hr_as_basic_info.as_shift_id AND hr_shift.hr_shift_unit_id = hr_as_basic_info.as_unit_id )"));
                        })
                    ->first();
        $intime = null;
        $outime = null;
        if($request->in_punch_new != '00:00:00' && $request->in_punch_new != null){
          $intime = date('Y-m-d', strtotime($request->date))." ".date('H:i:s', strtotime($request->in_punch_new));
        }
        if($request->out_punch_new != '00:00:00' && $request->out_punch_new != null){
          if(strtotime($request->in_punch_new) > strtotime($request->out_punch_new))
          {
            $ndate = date('Y-m-d',strtotime("+1 day", strtotime($request->date)));
            $outime = date('Y-m-d', strtotime($ndate))." ".date('H:i:s', strtotime($request->out_punch_new));
          }else{
            $outime = date('Y-m-d', strtotime($request->date))." ".date('H:i:s', strtotime($request->out_punch_new));
          }

        }

        $attd = DB::table($tableNmae[0])
                ->where('as_id',$userInfo->as_id)
                ->whereDate('in_time',date('Y-m-d', strtotime($request->date)))
                ->first();
        $previous['associate_id']=$request->associate_id;
        $previous['date']=$request->date;
        if($attd != null){

            DB::table($tableNmae[0])
                ->where('id',$attd->id)
                ->update([
                    'as_id' => $userInfo->as_id,
                    'in_date' => date('Y-m-d', strtotime($intime)),
                    'in_time' => $intime,
                    'out_time'=> $outime,
                    'hr_shift_code' => $userInfo->hr_shift_code,
                    'ot_hour' => $request->ot_new,
                    'remarks'=>'BM',
                    'updated_by' => auth()->user()->associate_id,
                    'updated_at' => NOW()
                    ]);
            DB::table('hr_absent')
                ->where('associate_id',$request->associate_id)
                ->where('date',date('Y-m-d',strtotime($request->date)))
                ->delete();

            DB::table('event_history')
            ->insert([
                'user_id' => $current['associate_id'],
                'type' => 2,
                'previous_event' =>  json_encode($previous),
                'modified_event' => json_encode($current),
                'created_by' => Auth::user()->associate_id
            ]);
            $lastPunchId =$attd->id;

        }else{
            $lastPunchId = DB::table($tableNmae[0])
            ->insertGetId([
                'as_id' => $userInfo->as_id,
                'in_date' => date('Y-m-d', strtotime($intime)),
                'in_time' => $intime,
                'out_time'=> $outime,
                'hr_shift_code' => $userInfo->hr_shift_code,
                'ot_hour' => $request->ot_new,
                'remarks'=>'BM',
                'updated_by' => auth()->user()->associate_id,
                'updated_at' => NOW()
            ]);
            DB::table('hr_absent')
                ->where('associate_id',$request->associate_id)
                ->where('date',date('Y-m-d',strtotime($request->date)))
                ->delete();
            DB::table('event_history')
            ->insert([
                'user_id' => $current['associate_id'],
                'type' => 2,
                'previous_event' =>  json_encode($previous),
                'modified_event' => json_encode($current),
                'created_by' => Auth::user()->associate_id
            ]);
        }

        if($lastPunchId){
          if($outime == null){
            // ProcessAttendanceIntime queue run
            $queue = (new ProcessAttendanceIntime($table, $lastPunchId, $request->unit))
                ->delay(Carbon::now()->addSeconds(2));
                dispatch($queue);
          }else{
            // ProcessAttendanceInOutTime queue run
            $queue = (new ProcessAttendanceInOutTime($table, $lastPunchId, $request->unit))
              ->delay(Carbon::now()->addSeconds(2));
              dispatch($queue);

              // $queue = (new BuyerManualAttandenceProcess($intime, $userInfo->as_id, $request->unit))
              //   ->delay(Carbon::now()->addSeconds(2));
              //   dispatch($queue);
          }
          return 'success';
        }else{
          return 'error';
        }

    }

    public function attendanceSummary(Request $request)
    {
        // Working hour
        $associate_id = $request->associate_id;
        $report_from  = $request->report_from;
        $report_to    = $request->report_to;
        $unit         = $request->unit;

        $data = DB::table("hr_attendance AS a")
            ->select(
                "b.associate_id",
                "a.in_time",
                "a.out_time",
                "a.hr_shift_code",
                "s.hr_shift_break_time",
                "s.hr_shift_start_time",
                "s.hr_shift_end_time"
            )
            ->where(function($query) use ($associate_id, $report_from, $report_to) {
                if (!empty($associate_id))
                {
                    $query->where('b.associate_id', '=', $associate_id);
                }

                if (!empty($report_from) && !empty($report_to))
                {
                    $query->whereBetween('a.in_time', [$report_from." 00:00:00", $report_to." 23:59:59"]);
                }
            })
            ->join("hr_as_basic_info AS b", function($join){
                $join->on( "b.as_id", "=", "a.as_id");
                $join->where( "b.as_status", "=", "1");
            })
            ->where('b.as_unit_id', $unit)
            ->leftJoin("hr_floor AS f", "f.hr_floor_id", "=", "b.as_floor_id")
            ->leftJoin("hr_shift AS s", "a.hr_shift_code", "=", "s.hr_shift_code")
            ->get();
            $total_ot=0;

        foreach($data AS $associate)
        {
            $startDay= date('Y-m-d', strtotime($associate->in_time));
            $ot= Attendance2::trackOTSum($associate->associate_id, $unit, $startDay, $startDay);
            $total_ot+=$ot;
        }

        $result= floor($total_ot/60)."h:".((($total_ot%60)>0)? (($total_ot%60))."m":"00m");
        return $result;
    }


}
