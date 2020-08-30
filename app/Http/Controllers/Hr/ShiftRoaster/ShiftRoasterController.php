<?php

namespace App\Http\Controllers\Hr\ShiftRoaster;

use App\Helpers\Custom;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\EmpType;
use App\Models\Employee;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator,DB,ACL,DataTables;


class ShiftRoasterController extends Controller
{
    public function index()
    {
      $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
      $unitList  = Unit::where('hr_unit_status', '1')
          ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
          ->pluck('hr_unit_short_name', 'hr_unit_id');

      $shiftList = Shift::where('hr_shift_status', 1)->pluck("hr_shift_name", "hr_shift_id");

      // $sectionList = Section::where('hr_section_status',1)->pluck('hr_section_name','hr_section_id');
      // $subsectionList = Subsection::where('hr_subsec_status',1)->pluck('hr_subsec_name','hr_subsec_id');
      $areaList = Area::where('hr_area_status',1)->pluck('hr_area_name','hr_area_id');
      // dd($sectionList, $subsectionList);
      return view('hr/operation/holiday_roster/index', compact('shiftList', 'employeeTypes', 'unitList','areaList'));

    }

    public function employeeWiseRosterSave($associate_id, $selectedDates, $type, $comment)
    {
      DB::beginTransaction();
      try {
        foreach ($selectedDates as $selectedDate) {
          $exist = DB::table('holiday_roaster')->where('date',$selectedDate)->where('as_id',$associate_id)->first();
          $year = date('Y',strtotime($selectedDate));
          $month = date('m',strtotime($selectedDate));
          if($exist){
            DB::table('holiday_roaster')->where('date',$selectedDate)->where('as_id',$associate_id)->update([
              'remarks'=>$type,
              'comment'=>$comment
            ]);
          }else{
            DB::table('holiday_roaster')->insert([
             'year'=>$year,
             'month'=>$month,
             'date'=>$selectedDate,
             'as_id'=>$associate_id,
             'remarks'=>$type,
             'comment'=>$comment,
             'status'=>1
            ]);
          }
          $today = date('Y-m-d');
          $yearMonth = $year.'-'.$month;
          if($today > $selectedDate){
            $modifyFlag = 0;
            // if type holiday then employee absent delete
            if($type == 'Holiday'){
              $getStatus = EmployeeHelper::employeeAttendanceAbsentDelete($associate_id, $selectedDate);
              if($getStatus == 'success'){
                $modifyFlag = 1;
              }
            }
            // if type OT then employee attendance OT count change
            if($type == 'OT' || $type == 'General'){
              // check exists attendance
              $getStatus = EmployeeHelper::employeeAttendanceOTUpdate($associate_id, $selectedDate);
              if($getStatus == 'success'){
                $modifyFlag = 1;
              }
            }

            if($modifyFlag == 1){
              $getEmployee = Employee::getEmployeeAssIdWiseSelectedField($associate_id, ['as_id', 'as_unit_id']);
              $tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
              if($month == date('m')){
                $totalDay = date('d');
              }else{
                  $totalDay = Carbon::parse($yearMonth)->daysInMonth;
              }
              $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $getEmployee->as_id, $totalDay))
                      ->onQueue('salarygenerate')
                      ->delay(Carbon::now()->addSeconds(2));
                      dispatch($queue); 
            }
          }
        }

        DB::commit();
        return "success";
      } catch (\Exception $e) {
        DB::rollback();
        $bug = $e->getMessage();
        return "error";
      }

    }

    public function saveRoaster(Request $request)
    {
      // dd($request->all());
      $validator= Validator::make($request->all(),[
           'unit'     => 'required',
           'type'      => 'required'
      ]);
      if($validator->fails())
      {
          return back()
           ->withInput()
           ->with($validator)
           ->with('error',"Error! Please Select all required fields!");
      }

      /*if(empty($request->multi_select_dates) && empty($request->single_select_dates) )
      {
         return back()->withInput()->with('error',"Error! Please Select Dates!");
      }
      */

      if(!isset($request->assigned)){
        return back()->with('error',"Error! Please Select Employee!");
      }

      /*$selectedDates = !empty($request->multi_select_dates)?explode(',',$request->multi_select_dates):(!empty($request->single_select_dates)?explode(',',$request->single_select_dates):null);*/

      $input = $request->all();
      DB::beginTransaction();
      // return $input;
      try {
        $assignDates = !empty($input['assignDates']) ? explode(',', $input['assignDates']): '';
        $subDates = !empty($input['subDates']) ? explode(',', $input['subDates']): '';

        foreach ($request->assigned as $associate_id) {
          if($assignDates != ''){
            $result = $this->employeeWiseRosterSave($associate_id, $assignDates, $request->type, $request->comment);
          }

          if(($input['subtype'] != null) && ($subDates != '')){
            $result = $this->employeeWiseRosterSave($associate_id, $subDates, $request->subtype, $request->subcomment);
          }
        }
        DB::commit();
        return back()->with('success',"Shift Roaster Saved Successfully");
      } catch (Exception $e) {
        DB::rollback();
        $bug = $e->getMessage();
        return back()->with('error', $bug);
      }
    }

    public function viewRoaster()
    {

      $unitList  = Unit::where('hr_unit_status', '1')
      ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
      ->pluck('hr_unit_name', 'hr_unit_id');
      $floorList= [];
      $lineList= [];

      $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

      $deptList= [];

      $sectionList= [];

      $subSectionList= [];


      return view('hr/operation/holiday_roster/list', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList'));


    }

    public function roasterSaveChanges(Request $request)
    {
     //dd($request->all());exit;
     //dd($request->previous);exit;
        $previous = explode(',',$request->previous);
        $previousChanged = isset($request->previousDateChanged)?$request->previousDateChanged:[];
        $missing = array_diff($previous,$previousChanged);
          //dd($missing);exit;
          foreach ($missing as $value) {
            $exist = DB::table('holiday_roaster')->where('as_id',$request->as_id)->where('date',$request->year.'-'.$request->month.'-'.$value)->delete();
          }
       if(!empty($request->dates) && $request->selectType == 'single'){

         foreach ($request->dates as $date) {
           // code...
            //DB::table('holiday_roaster')->where('as_id',$request->as_id)
          //  $d = explode('-',$previous);
            //$missing = array_diff($previous,$request->previousDateChanged);


            // if(!in_array($d[2], $previous.toArray())){
            //   $exist = DB::table('holiday_roaster')->where('as_id',$request->as_id)->where('date',$date)->delete();
            // }
          //  $exist = DB::table('holiday_roaster')->where('as_id',$request->as_id)->where('date',$date)->delete();
            $exist = DB::table('holiday_roaster')->where('as_id',$request->as_id)->where('date',$date)->first();
            //dd($exist);exit;
            if(empty($exist)){

              DB::table('holiday_roaster')->insert([
                    'as_id' => $request->as_id,
                    'year' => $request->year,
                    'month' => $request->month,
                    'date'=>$date,
                    'remarks' => $request->type,
                    'comment' => $request->comment
                ]);

            }else{
              DB::table('holiday_roaster')->where('id',$exist->id)
                    ->update([
                      'remarks' => $request->type,
                      'comment' => $request->comment
                    ]);
            }
         }

       }elseif (!empty($request->dates) && $request->selectType == 'multi') {
         // code...
           DB::table('holiday_roaster')->where('as_id',$request->as_id)->delete();
         foreach ($request->dates as $date) {
           // code...

            $exist = DB::table('holiday_roaster')->where('as_id',$request->as_id)->where('date',$date)->first();
            //dd($exist);exit;
            if(empty($exist)){

              DB::table('holiday_roaster')->insert([
                    'as_id' => $request->as_id,
                    'year' => $request->year,
                    'month' => $request->month,
                    'date'=>$date,
                    'remarks' => $request->type
                ]);

            }else{
              DB::table('holiday_roaster')->where('id',$exist->id)
                    ->update([
                      'remarks' => $request->type
                    ]);
            }
         }
       }


    }

    public function getRoasterData(Request $request)
    {
      $input = $request->all();
      // dd($input);
      $associate_id = isset($request->associate_id)?$request->associate_id:'';
      $month        = isset($request->month)?$request->month:'';
      $day          = isset($request->day)?$request->day:'';
      $unit         = isset($request->unit)?$request->unit:'';
      $areaid       = isset($request->area)?$request->area:'';
      $departmentid = isset($request->department)?$request->department:'';
      $lineid       = isset($request->line_id)?$request->line_id:'';
      $florid       = isset($request->floor_id)?$request->floor_id:'';
      $section      = isset($request->section)?$request->section:'';
      $subSection   = isset($request->subSection)?$request->subSection:'';
      $sdate   = isset($request->date)?$request->date:'';
      
      // dd($sdate);exit;
      $datesday = [];
      $str = $month.'-';
      $year = date('Y', strtotime($month));
      $month = date('m', strtotime($month));
      if(!empty($day)){
         $d=cal_days_in_month(CAL_GREGORIAN,$month,$year);
        for($i2=1; $i2<$d; $i2++)
        {

          // echo '<br>',
            $ddd = $str.$i2;
          // echo '',
            $date = date('Y M D', $time = strtotime($ddd) );

          if(strpos($date, $day))
          {
            $datesday[] = date('Y-m-d', strtotime($ddd) );
          }
        }
      }
      // return $input;
      $query1 = DB::table('hr_as_basic_info AS b')
      ->select(
        "b.associate_id",
        "b.as_oracle_code",
        "b.as_unit_id",
        "b.as_name",
        "b.as_pic",
        "b.as_gender",
        "b.as_contact as cell",
        "b.as_section_id",
        "b.as_shift_id",
        "sec.hr_section_name as section",
        "b.as_emp_type_id",
        "hdr.*",
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
        if (!empty($month)) {
          $query1->where('hdr.month', $month);
        }
        if (!empty($year)) {
          $query1->where('hdr.year', $year);
        }
        if (!empty($day)) {
          $query1->whereIn('hdr.date', $datesday);
        }
        if (!empty($request->type) && $request->type != 'Substitute') {
          $query1->where('hdr.remarks', $request->type);
        }elseif ($request->type == 'Substitute') {
          $query1->where('hdr.comment', $request->type);
        }
        if (!empty($sdate)) {
          $query1->where('hdr.date', $sdate);
        }

        $query1->join('holiday_roaster AS hdr', 'hdr.as_id', 'b.associate_id');
        $query1->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id');
        $query1->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id");
        // $query1->leftJoin("hr_shift AS s", "b.as_shift_id", "=", "s.hr_shift_id");
        $query1->leftJoin("hr_section AS sec", "sec.hr_section_id", "b.as_section_id");

        $employee_list = $query1->get();
        // return $employee_list;
        $data =[];
        $asids = array_unique(array_column($employee_list->toArray(),'associate_id'),SORT_REGULAR);
        foreach ($asids as $k=>$asid) {
          $dates = '';
          $count = 1;
          $rs = new Shift;
          $ck =0;
          foreach ($employee_list as $d) {
            $ck++;
            //if($d->status == 'Present' && $d->in_time == '' ){
              if($asid == $d->associate_id){

                $dat = date('d', strtotime($d->date));

                $dates .= $dat.',';
                //if($ck < sizeof($employee_list)){ $dates .= ','; }

                $rs->dates = $dates;
                $rs->day_count =$count;
                $rs->as_oracle_code 		= $d->as_oracle_code;
                $rs->associate_id     = $d->associate_id;
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
          //}
        }

        return DataTables::of($data)->addIndexColumn()
        ->addColumn('pic', function ($data) {
          $image = emp_profile_picture($data);
          return "<img src='".$image."' class='small-image' style='height:40px;width:auto'>";
        })
        ->editColumn('dates', function($data){
          return $data->dates;
        })
        ->editColumn('day_count', function($data){
          return $data->day_count;
        })
        ->addColumn('actions', function($data){
          return '';
          // return '<a href="#" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#calendarModal" id="calendar-view">view</a>';
        })
        ->rawColumns(['pic', 'as_oracle_code', 'associate_id','hr_unit_name','as_name','cell', 'section', 'hr_designation_name', 'dates', 'day_count','actions'])
        ->make(true);
    }

}
