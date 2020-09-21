<?php

namespace App\Http\Controllers\Hr\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceInOutTime;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\BuyerManualAttandenceProcess;
use App\Models\Hr\Shift;
use App\Models\Employee;
use App\Models\Hr\Unit;
use App\Models\Hr\Benefits;
use App\Helpers\Attendance2;
use Carbon\Carbon;
use DataTables, DB, Auth, ACL;

class AbsentPresentListController extends Controller
{
  public function absentPresentIndex()
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

    $data['salaryMin']      = Benefits::getSalaryRangeMin();
    $data['salaryMax']      = Benefits::getSalaryRangeMax();


    return view('hr/operation/absent_or_attendance_list', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'data'));
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
    $areaid       = isset($request['area'])?$request['area']:'';
    $departmentid = isset($request['department'])?$request['department']:'';
    $lineid       = isset($request['line_id'])?$request['line_id']:'';
    $florid       = isset($request['floor_id'])?$request['floor_id']:'';
    $section      = isset($request['section'])?$request['section']:'';
    $subSection   = isset($request['subSection'])?$request['subSection']:'';
    $min_salary   = (double)(isset($request['min_salary'])?$request['min_salary']:'');
    $max_salary   = (double)(isset($request['max_salary'])?$request['max_salary']:'');

    // dd($min_salary, $max_salary);exit;

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

    $leaveData->where('leave_from','<=', $report_from);
    $leaveData->where('leave_to','>=', $report_to);
    $leaveData->where('leave_status','1');
    $leaveData->groupBy('leave_ass_id');

    $attData_sql    = $attData->toSql();  // compiles to SQL
    $leaveData_sql  = $leaveData->toSql();  // compiles to SQL
    $query1 = DB::table('hr_as_basic_info AS b')
    ->select(
      "b.associate_id",
      "b.as_unit_id",
      "b.as_name",
      "b.as_pic",
      "b.as_gender",
      "b.as_contact as cell",
      "b.as_section_id",
      "sec.hr_section_name as section",
      "b.as_emp_type_id",
      "a.in_time",
      "a.out_time",
      "a.ot_hour",
      "a.late_status",
      "a.hr_shift_code",
      "a.remarks",
      // "s.hr_shift_break_time",
      // "s.hr_shift_start_time",
      // "s.hr_shift_end_time",
      // "s.hr_shift_name",
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

      if(!empty($otCondition)){
        $query1->where('a.ot_hour',$otCondition,'0'.$request['ot_hour'].':00');
      }
      if(!empty($min_salary) && !empty($max_salary)){
        $query1->where('ben.ben_current_salary', '>=', $min_salary);
        $query1->where('ben.ben_current_salary', '<=', $max_salary);
      }

      $query1->leftjoin(DB::raw('(' . $attData_sql. ') AS a'), function($join) use ($attData) {
        $join->on('a.as_id', '=', 'b.as_id')->addBinding($attData->getBindings());
      });
      $query1->leftjoin(DB::raw('(' . $leaveData_sql. ') AS c'), function($join1) use ($leaveData) {
        $join1->on('c.leave_ass_id', '=', 'b.associate_id')->addBinding($leaveData->getBindings()); ;
      });
      $query1->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id');
      $query1->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id");
      // $query1->leftJoin("hr_shift AS s", "a.hr_shift_code", "=", "s.hr_shift_code");
      $query1->leftJoin("hr_section AS sec", "sec.hr_section_id", "b.as_section_id");
      $query1->leftJoin("hr_benefits AS ben", "ben.ben_as_id", "b.associate_id");

      $employee_list = $query1->get();
      $data = [];
      foreach($employee_list as $k=>$employee) {
        if ($employee->in_time == null) {
          if(!empty($employee->leave_type)){
            $employee->status = 'Leave';
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
            } else {
              $employee->status = 'Present';
              $data[] = $employee; //'Present';
            }
          }

        }
      }
      return $data;
    }


    public function getAbsentData($request){

      $areaid       = isset($request['area'])?$request['area']:'';
      $departmentid = isset($request['department'])?$request['department']:'';
      $lineid       = isset($request['line_id'])?$request['line_id']:'';
      $florid       = isset($request['floor_id'])?$request['floor_id']:'';
      $section      = isset($request['section'])?$request['section']:'';
      $subSection   = isset($request['subSection'])?$request['subSection']:'';
      $min_salary   = (double)(isset($request['min_salary'])?$request['min_salary']:'');
      $max_salary   = (double)(isset($request['max_salary'])?$request['max_salary']:'');
      // dd($min_salary, $max_salary);exit;
      $getEmployee = DB::table('hr_as_basic_info')->where('as_unit_id', $request['unit']);
      $employeeToSql = $getEmployee->toSql();
      $getDesignation = designation_by_id();
      $getSection = section_by_id();
      $absentData = DB::table('hr_absent')
      ->where('hr_unit',$request['unit'])
      ->whereBetween('date',array($request['report_from'],$request['report_to']))
      ->when(!empty($areaid), function ($query) use($areaid){
        return $query->where('b.as_area_id',$areaid);
      })
      ->when(!empty($departmentid), function ($query) use($departmentid){
        return $query->where('b.as_department_id',$departmentid);
      })
      ->when(!empty($lineid), function ($query) use($lineid){
        return $query->where('b.as_line_id', $lineid);
      })
      ->when(!empty($florid), function ($query) use($florid){
        return $query->where('b.as_floor_id',$florid);
      })
      ->when(!empty($section), function ($query) use($section){
        return $query->where('b.as_section_id', $section);
      })
      ->when(!empty($subSection), function ($query) use($subSection){
        return $query->where('b.as_subsection_id', $subSection);
      })
      ->when(!empty($min_salary), function ($query) use($min_salary){
        return $query->where('hr_benefits.ben_current_salary','>=', $min_salary);
      })
      ->when(!empty($max_salary), function ($query) use($max_salary){
        return $query->where('hr_benefits.ben_current_salary','<=', $max_salary);
      })
      ->leftjoin(DB::raw('(' . $employeeToSql. ') AS b'), function($join) use ($getEmployee) {
        $join->on('hr_absent.associate_id', '=', 'b.associate_id')->addBinding($getEmployee->getBindings());
      })
      //->leftJoin('b','hr_absent.associate_id','b.associate_id')
      //->leftJoin('hr_designation', 'hr_designation.hr_designation_id', 'b.as_designation_id')
      //->leftJoin("hr_unit", "hr_unit.hr_unit_id", "b.as_unit_id")
      //->leftJoin("hr_section", "hr_section.hr_section_id", "b.as_section_id")
      ->leftJoin("hr_benefits", "hr_benefits.ben_as_id", "b.associate_id")
      ->orderBy('date','DESC')
      ->get()->groupBy('associate_id');


      // dd('Absent Data', $absentData);exit;

      $data = [];
      $i = 0;
      foreach ($absentData as $absent) {
        $dates = '';
        $firstDate = '';
        $d = new Shift; // creating a blank object
        $d->absent_count = sizeof($absent);
        $ck = 0;
        foreach ($absent as $key => $abs) {
          $ck++;
          $dt=explode('-', $abs->date);
          $firstDate = $abs->date;
          
          $dates .= $dt[2].'/'.$dt[1];
          if($ck < sizeof($absent)){ $dates .= ', '; }

          $d->associate_id 		= $abs->associate_id;
          $d->as_unit_id   		= $abs->as_unit_id;
          $d->as_name     		= $abs->as_name;
          $d->cell     			  = $abs->as_contact;
          $d->section     		= $getSection[$abs->as_section_id]['hr_section_name']??'';
          $d->as_pic      		= $abs->as_pic;
          $d->as_gender    		= $abs->as_gender;
          $d->hr_designation_name = $getDesignation[$abs->as_designation_id]['hr_designation_name']??'';
          //$d->hr_unit_name      	= $abs->hr_unit_short_name;
        }
        $d->first_date = $firstDate;
        $d->dates         = $dates;
        $data[$i] = $d; //assigning object into array

        $i++;

      }

      return $data;


    }


    public function attendanceReportData(Request $request){

      $input = $request->all();
      #-----------------------------------------------------------#
      $data = [];
      $type = $request->type;
      if($type == 'Absent'){
        $data = $this->getAbsentData($request->all());
      }elseif($type == 'Intime-Outtime Empty'){
          $results = $this->getEmpAttGetData($request->all());
          // dd($results[0]);
          $asids = array_unique(array_column($results,'associate_id'),SORT_REGULAR);
          foreach ($asids as $k=>$asid) {
            $dates_in_miss  = '';
            $dates_out_miss = '';
            $count_in_miss = 0;
            $count_out_miss = 0;
            $rs = new Shift;
            foreach ($results as $d) {
                if( $d->status == 'Present' && $d->out_time == ''){
                    if($asid == $d->associate_id){
                      if($d->out_time == ''){
                        $dat = date('d', strtotime($d->in_time));
                        $dates_out_miss .= $dat.',';
                        ++$count_out_miss;
                      }
                      $rs->absent_count = 'In-Time-Empty: '.$count_in_miss.'<br/>'.'Out-Time-Empty: '.$count_out_miss.'';
                      $rs->associate_id     = $d->associate_id;
                      $rs->as_unit_id       = $d->hr_unit_name;
                      $rs->as_name        = $d->as_name;
                      $rs->cell           = $d->cell ;
                      $rs->section        = $d->section;
                      $rs->as_pic         = $d->as_pic;
                      $rs->as_gender        = $d->as_gender;
                      $rs->hr_designation_name = $d->hr_designation_name;
                      $rs->hr_unit_name       = $d->hr_unit_name;
                      $rs->dates         = 'In-Time-Empty: '.$dates_in_miss.'<br/>'.'Out-Time-Empty: '.$dates_out_miss.'';

                      $data[$k] = $rs;
                    }
                }
                if( $d->status == 'Present' && $d->in_time == ''){
                  if($asid == $d->associate_id){
                      if($d->in_time == ''){
                        $dat = date('d', strtotime($d->out_time));
                        $dates_in_miss .= $dat.',';
                        ++$count_in_miss;
                      }
                      $rs->absent_count = 'In-Time-Empty: '.$count_in_miss.'<br/>'.'Out-Time-Empty: '.$count_out_miss.'';
                      $rs->associate_id     = $d->associate_id;
                      $rs->as_unit_id       = $d->hr_unit_name;
                      $rs->as_name        = $d->as_name;
                      $rs->cell           = $d->cell ;
                      $rs->section        = $d->section;
                      $rs->as_pic         = $d->as_pic;
                      $rs->as_gender        = $d->as_gender;
                      $rs->hr_designation_name = $d->hr_designation_name;
                      $rs->hr_unit_name       = $d->hr_unit_name;
                      $rs->dates         = 'In-Time-Empty: '.$dates_in_miss.'<br/>'.'Out-Time-Empty: '.$dates_out_miss.'';

                      $data[$k] = $rs;
                  }
                }
              }
            }

      }elseif ($type == 'Present(Intime Empty)') {
        $results = $this->getEmpAttGetData($request->all());
        $asids = array_unique(array_column($results,'associate_id'),SORT_REGULAR);
        foreach ($asids as $k=>$asid) {
          $dates = '';
          $count = 1;
          $rs = new Shift;
          foreach ($results as $d) {

            if($d->status == 'Present' && $d->in_time == '' ){
              if($asid == $d->associate_id){

                $dat = date('d', strtotime($d->out_time));
                $dates .= $dat.',';

                $rs->dates = $dates;
                $rs->absent_count =$count;
                $rs->associate_id 		= $d->associate_id;
                $rs->as_unit_id   		= $d->hr_unit_name;
                $rs->as_name     		= $d->as_name;
                $rs->cell     			= $d->cell ;
                $rs->section     		= $d->section;
                $rs->as_pic      		= $d->as_pic;
                $rs->as_gender    		= $d->as_gender;
                $rs->hr_designation_name = $d->hr_designation_name;
                $rs->hr_unit_name      	= $d->hr_unit_name;
                $rs->dates         = $dates;

                $data[$k] = $rs;
                $count++;
              }
            }
          }
        }

      }elseif ($type == 'Present(Outtime Empty)') {
        $results = $this->getEmpAttGetData($request->all());

        $asids = array_unique(array_column($results,'associate_id'),SORT_REGULAR);
        foreach ($asids as $k=>$asid) {
          $dates = '';
          $count = 1;
          $rs = new Shift;
          foreach ($results as $d) {

            if($d->status == 'Present' && $d->out_time == '' ){
              if($asid == $d->associate_id){

                $dat = date('d', strtotime($d->in_time));
                $dates .= $dat.',';

                $rs->dates = $dates;
                $rs->absent_count =$count;
                $rs->associate_id 		= $d->associate_id;
                $rs->as_unit_id   		= $d->hr_unit_name;
                $rs->as_name     		= $d->as_name;
                $rs->cell     			= $d->cell ;
                $rs->section     		= $d->section;
                $rs->as_pic      		= $d->as_pic;
                $rs->as_gender    		= $d->as_gender;
                $rs->hr_designation_name = $d->hr_designation_name;
                $rs->hr_unit_name      	= $d->hr_unit_name;
                $rs->dates         = $dates;

                $data[$k] = $rs;
                $count++;
              }
            }
          }
        }
      }elseif ($type == 'Present (Late(Outtime Empty))') {
        $results = $this->getEmpAttGetData($request->all());

        $asids = array_unique(array_column($results,'associate_id'),SORT_REGULAR);
        foreach ($asids as $k=>$asid) {
          $dates = '';
          $count = 1;
          $rs = new Shift;
          foreach ($results as $d) {

            if($d->status == 'Present (Late)' && $d->out_time == '' ){
              if($asid == $d->associate_id){

                $dat = date('d', strtotime($d->in_time));
                $dates .= $dat.',';

                $rs->dates = $dates;
                $rs->absent_count =$count;
                $rs->associate_id 		= $d->associate_id;
                $rs->as_unit_id   		= $d->hr_unit_name;
                $rs->as_name     		= $d->as_name;
                $rs->cell     			= $d->cell ;
                $rs->section     		= $d->section;
                $rs->as_pic      		= $d->as_pic;
                $rs->as_gender    		= $d->as_gender;
                $rs->hr_designation_name = $d->hr_designation_name;
                $rs->hr_unit_name      	= $d->hr_unit_name;
                $rs->dates         = $dates;

                $data[$k] = $rs;
                $count++;
              }
            }
          }
        }
      }elseif ($type == 'Present (Halfday)') {
        $results = $this->getEmpAttGetData($request->all());

        $asids = array_unique(array_column($results,'associate_id'),SORT_REGULAR);
        foreach ($asids as $k=>$asid) {
          $dates = '';
          $count = 1;
          $rs = new Shift;
          foreach ($results as $d) {

            if($d->status == 'Present (Halfday)' && $d->out_time == '' ){
              if($asid == $d->associate_id){

                $dat = date('d', strtotime($d->in_time));
                $dates .= $dat.',';

                $rs->dates = $dates;
                $rs->absent_count =$count;
                $rs->associate_id 		= $d->associate_id;
                $rs->as_unit_id   		= $d->hr_unit_name;
                $rs->as_name     		= $d->as_name;
                $rs->cell     			= $d->cell ;
                $rs->section     		= $d->section;
                $rs->as_pic      		= $d->as_pic;
                $rs->as_gender    		= $d->as_gender;
                $rs->hr_designation_name = $d->hr_designation_name;
                $rs->hr_unit_name      	= $d->hr_unit_name;
                $rs->dates         = $dates;

                $data[$k] = $rs;
                $count++;
              }
            }
          }
        }
      }elseif ($type == 'Present (Late)') {
        $results = $this->getEmpAttGetData($request->all());

        $asids = array_unique(array_column($results,'associate_id'),SORT_REGULAR);
        foreach ($asids as $k=>$asid) {
          $dates = '';
          $count = 1;
          $rs = new Shift;
          foreach ($results as $d) {

            if($d->status == 'Present (Late)' && $d->out_time == '' ){
              if($asid == $d->associate_id){

                $dat = date('d', strtotime($d->in_time));
                $dates .= $dat.',';

                $rs->dates = $dates;
                $rs->absent_count =$count;
                $rs->associate_id 		= $d->associate_id;
                $rs->as_unit_id   		= $d->hr_unit_name;
                $rs->as_name     		= $d->as_name;
                $rs->cell     			= $d->cell ;
                $rs->section     		= $d->section;
                $rs->as_pic      		= $d->as_pic;
                $rs->as_gender    		= $d->as_gender;
                $rs->hr_designation_name = $d->hr_designation_name;
                $rs->hr_unit_name      	= $d->hr_unit_name;
                $rs->dates         = $dates;

                $data[$k] = $rs;
                $count++;
              }
            }
          }
        }
      }
      elseif ($type == 'Present') {
        $results = $this->getEmpAttGetData($request->all());

        $asids = array_unique(array_column($results,'associate_id'),SORT_REGULAR);
        foreach ($asids as $k=>$asid) {
          $dates = '';
          $count = 1;
          $rs = new Shift;
          foreach ($results as $d) {

            if($d->status == 'Present' && $d->out_time != '' && $d->in_time != '' ){
              if($asid == $d->associate_id){

                $dat = date('d', strtotime($d->in_time));
                $dates .= $dat.',';

                $rs->dates = $dates;
                $rs->absent_count =$count;
                $rs->associate_id 		= $d->associate_id;
                $rs->as_unit_id   		= $d->hr_unit_name;
                $rs->as_name     		= $d->as_name;
                $rs->cell     			= $d->cell ;
                $rs->section     		= $d->section;
                $rs->as_pic      		= $d->as_pic;
                $rs->as_gender    		= $d->as_gender;
                $rs->hr_designation_name = $d->hr_designation_name;
                $rs->hr_unit_name      	= $d->hr_unit_name;
                $rs->dates         = $dates;

                $data[$k] = $rs;
                $count++;
              }
            }
          }
        }
      }
      else{
        $results = $this->getEmpAttGetData($request->all());
        foreach ($results as $d) {
          if($d->status == $type )
          $data[] = $d;
        }

      }

      $date = isset($request->report_from)?$request->report_from:date('Y-m-d');
      $actionMonth = isset($request->report_to)?date('Y-m', strtotime($request->report_to)):date('Y-m');
      
      return DataTables::of($data)->addIndexColumn()
      ->addColumn('pic', function ($data) {
        return '<img src="'.emp_profile_picture($data).'" class="min-img-file">';
      })
      ->editColumn('dates', function($data){
        return $data->dates;
      })
      ->editColumn('absent_count', function($data){
        return $data->absent_count;
      })
      ->addColumn('action', function($data) use ($actionMonth, $type){
        if($type == 'Absent'){
          $url = url("hr/operation/warning-notice?associate=$data->associate_id&month_year=$actionMonth&start_date=$data->first_date&days=$data->absent_count");
          return '<a href="'.$url.'" class="btn btn-sm btn-outline-success" target="blank" data-toggle="tooltip" data-placement="top" title="Take action for '.$data->as_name.'" data-original-title="Take action for '.$data->as_name.'"><i class="las la-random"></i></a>';
        }else{  
          return '';
        }
        
      })
      ->rawColumns(['pic', 'dates', 'absent_count', 'action'])
      ->make(true);
    }
}
