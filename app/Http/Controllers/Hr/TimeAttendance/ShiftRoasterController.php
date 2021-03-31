<?php

namespace App\Http\Controllers\Hr\TimeAttendance;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\EmpType;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Line;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;
use Validator, DB, DataTables, ACL, Collection;

class ShiftRoasterController extends Controller
{
    protected $planner;

    protected $roaster;

    protected $holiday;

    protected $general;

    public function shiftAssign()
    {
        //ACL::check(["permission" => "hr_time_shift_assign"]);
        #-----------------------------------------------------------#
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');

        $shiftList = Shift::where('hr_shift_status', 1)->pluck("hr_shift_name", "hr_shift_id");

        // $sectionList = Section::where('hr_section_status',1)->pluck('hr_section_name','hr_section_id');
        // $subsectionList = Subsection::where('hr_subsec_status',1)->pluck('hr_subsec_name','hr_subsec_id');
        $areaList = Area::where('hr_area_status',1)->pluck('hr_area_name','hr_area_id');
        // dd($sectionList, $subsectionList);
        return view('hr/operation/shift_roster_assign', compact('shiftList', 'employeeTypes', 'unitList','areaList'));
    }

    public function getAssociateByTypeUnitShift(Request $request)
    {
        $employees = Employee::where(function($query) use ($request){
            if ($request->emp_type != null)
            {
                $query->where('as_emp_type_id', $request->emp_type);
            }
            if ($request->unit != null)
            {
                $query->where('as_unit_id', $request->unit);
            }
            if ($request->otnonot != null)
            {
                $query->where('as_ot', $request->otnonot);
            }
            if ($request->shift != null)
            {
                // $query->where('as_shift_id', $request->shift);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->section != null)
            {
                $query->where('as_section_id', $request->section);
            }
            if ($request->subsection != null)
            {
                $query->where('as_subsection_id', $request->subsection);
            }
            if ($request->status != null)
            {
                $query->where('shift_roaster_status', $request->status);
            }
            $query->where("as_status", 1);
        })
        ->whereIn('as_unit_id', auth()->user()->unit_permissions())
        // ->limit(5)
        ->get();

        // show user id
        $shiftCode = null;
        if ($request->shift != null)
        {
            $shiftCode = Shift::where('hr_shift_id',$request->shift)->first();
        }

        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
        $data['result'] = "";

        $data['shiftRosterCount'] = [];
        $data['shiftDefaultCount'] = [];
        // $data['shiftRosterCount2'] = [];
        $data['total'] = 0;

        $filter_year  = date('Y');
        $filter_month = date('n');
        $filter_day   = date('j');
        if($request->searchDate != null) {
            $filter_year = date('Y', strtotime($request->searchDate));
            $filter_month = date('n', strtotime($request->searchDate));
            $filter_day = date('j', strtotime($request->searchDate));
        }

        foreach($employees as $employee)
        {
            if($employee->as_shift_id != null){
                $todayShift = DB::table('hr_shift_roaster')
                ->where('shift_roaster_associate_id', $employee->associate_id)
                ->where('shift_roaster_year', $filter_year)
                ->where('shift_roaster_month', $filter_month)
                ->pluck('day_'.$filter_day)
                ->first();
                $shift_code = null;

                if(!$todayShift){
                    if($shiftCode) {
                        if($shiftCode->hr_shift_code == $employee->shift['hr_shift_code']) {
                            $shift_code = $shiftCode->hr_shift_name.' - Default';
                            $data['shiftDefaultCount'][$shiftCode->hr_shift_name][$shiftCode->hr_shift_code][] = $employee->shift['hr_shift_name'].' - Default';
                        }
                    } else {
                        $empShift = Shift::where('hr_shift_code',$employee->shift['hr_shift_code'])->first();
                        if($empShift) {
                            $shift_code = $employee->shift['hr_shift_name'].' - Default';
                            $data['shiftDefaultCount'][$empShift->hr_shift_name][$empShift->hr_shift_code][] = $employee->shift['hr_shift_name'].' - Default';
                        }
                    }
                }else{
                    // $data['shiftRosterCount2'][] = $todayShift.' - Changed';
                    if($shiftCode) {
                        if($todayShift == $shiftCode->hr_shift_name) {
                            $shift_code = $todayShift.' - Changed';
                        }
                    } else {
                        $empShift = Shift::where('hr_shift_name',$todayShift)->first();
                        if($empShift) {
                            $shift_code = $todayShift.' - Changed';
                            $data['shiftRosterCount'][$empShift->hr_shift_name][$empShift->hr_shift_code][] = $todayShift.' - Changed';
                        }
                    }
                }

                if($shift_code != null) {
                    $data['total'] += 1;
                    $image = ($employee->as_pic == null?'/assets/images/avatars/profile-pic.jpg': $employee->as_pic);
                    $data['result'].= "<tr class='add'>
                        <td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".$image."' class='small-image' onError='this.onerror=null;this.src=\"/assets/images/avatars/avatar2.png\"'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td>
                        <td>$employee->as_name </td><td>$shift_code </td></tr>";
                } else {
                    // $data['no_shift_code'] = $employee->associate_id;
                }
            }

        }
        return $data;
    }

    public function getAssociateByTypeUnitShiftRosterAjax(Request $request)
    {
      $input = $request->all();
      // return $input;
      $otCondition = '';
      if(!empty($request->doj) && $request->condition == 'Equal')
      {
        $otCondition = '=';

      }elseif (!empty($request->doj) && $request->condition == 'Less Than') {
        $otCondition = '<';
      }elseif (!empty($request->doj) && $request->condition == 'Greater Than') {
        $otCondition = '>';
      }

        $expdate = explode(',',$request->dates);
        $query1 = Employee::
                    whereIn('as_unit_id', auth()->user()->unit_permissions());

        $query1->where(function($query) use ($request,$otCondition){
            if ($request->emp_type != null)
            {
                $query->where('as_emp_type_id', $request->emp_type);
            }
            if ($request->unit != null)
            {
                $query->where('as_unit_id', $request->unit);
            }
            if ($request->otnonot != null)
            {
                $query->where('as_ot', $request->otnonot);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if($request->doj != null)
            {
                $query->where('as_doj',$otCondition,$request->doj);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->section != null)
            {
                $query->where('as_section_id', $request->section);
            }
            if ($request->subsection != null)
            {
                $query->where('as_subsection_id', $request->subsection);
            }
            if ($request->status != null)
            {
                $query->where('shift_roaster_status', $request->status);
            }
            $query->where("as_status", 1);
        });
        if ($request->type != null)
        {
            $query1->leftJoin('holiday_roaster', function ($join) use ($expdate,$request) {
                            $join->on('holiday_roaster.as_id', '=', 'associate_id')
                                 ->where('holiday_roaster.remarks', $request->type);
                                 //->whereBetween('holiday_roaster.date', $expdate);
                        });

            $query1->whereIn('holiday_roaster.date', $expdate);
            // foreach ($expdate as $key => $value) {
            //   $query1->where('holiday_roaster.date', $value);
            // }
            $query1->groupBy('holiday_roaster.as_id');
        }

        $employees = $query1->get();
        
        // show user id
        $shiftCode = null;
        if ($request->shift != null)
        {
            $shiftCode = Shift::where('hr_shift_id',$request->shift)->first();
        }

        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
        $data['result'] = "";

        $data['shiftRosterCount'] = [];
        $data['shiftDefaultCount'] = [];
        // $data['shiftRosterCount2'] = [];
        $data['total'] = 0;

        foreach($employees as $employee)
        {
            if($employee->shift != null){
                $todayShift = DB::table('hr_shift_roaster')
                ->where('shift_roaster_associate_id', $employee->associate_id)
                ->where('shift_roaster_year', date('Y'))
                ->where('shift_roaster_month', date('n'))
                ->pluck('day_'.date('j'))
                ->first();
                $shift_code = null;

                if(!$todayShift){
                    if($shiftCode) {
                        if($shiftCode->hr_shift_code == $employee->shift['hr_shift_code']) {
                            $shift_code = $shiftCode->hr_shift_name.' - Default';
                            $data['shiftDefaultCount'][$shiftCode->hr_shift_name][$shiftCode->hr_shift_code][] = $employee->shift['hr_shift_name'].' - Default';
                        }
                    } else {
                        $empShift = Shift::where('hr_shift_code',$employee->shift['hr_shift_code'])->first();
                        if($empShift) {
                            $shift_code = $employee->shift['hr_shift_name'].' - Default';
                            $data['shiftDefaultCount'][$empShift->hr_shift_name][$empShift->hr_shift_code][] = $employee->shift['hr_shift_name'].' - Default';
                        }
                    }
                }else{
                    // $data['shiftRosterCount2'][] = $todayShift.' - Changed';
                    if($shiftCode) {
                        if($todayShift == $shiftCode->hr_shift_name) {
                            $shift_code = $todayShift.' - Changed';
                        }
                    } else {
                        $empShift = Shift::where('hr_shift_name',$todayShift)->first();
                        if($empShift) {
                            $shift_code = $todayShift.' - Changed';
                            $data['shiftRosterCount'][$empShift->hr_shift_name][$empShift->hr_shift_code][] = $todayShift.' - Changed';
                        }
                    }
                }
                if($shift_code != null) {
                    $data['total'] += 1;
                    $image = ($employee->as_pic == null?'/assets/images/avatars/profile-pic.jpg': $employee->as_pic);
                    $data['result'].= "<tr class='add'>
                        <td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".$image."' class='small-image' onError='this.onerror=null;this.src=\"/assets/images/avatars/avatar2.png\"'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td><td><span class=\"lbl\"> $employee->as_oracle_code</span></td>
                        <td>$employee->as_name </td><td>$shift_code </td></tr>";
                }
            }
        }
        return $data;
    }

    public function saveAssignedShift(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'shift'     => 'required',
            'year'      => 'required',
            'month'     => 'required',
            'start_day' => 'required',
            'end_day'   => 'required'
        ]);

        $input = $request->all();
        // dd($input);
        if($validator->fails())
        {
            return back()
            ->withInput()
            ->with($validator)
            ->with('error',"Error! Please Select all required fields!");
        }
        if (empty($request->assigned) || !is_array($request->assigned))
        {
            return back()
                ->withInput()
                ->with('error',"Error! Please select at least one associate.");
        }
        $input['ass_ids'] = array_chunk($input['assigned'], 10);
        unset($input['assigned']);
        // return $input;
        try {
            return view('hr/timeattendance/shift_assign_process', $input);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function assignShiftProcessing(Request $request)
    {
        $input = $request->all();
        try {
            foreach ($request->getdata as $key => $ass_id) {
                for($j=$input['start_day']; $j<=$input['end_day']; $j++)
                {
                    $data = Shift::where('hr_shift_id', '=', $input['shift'])->value('hr_shift_name');
                    $day= "day_".$j;
                    $exist = ShiftRoaster::where('shift_roaster_associate_id', $ass_id)
                        ->where('shift_roaster_year', $input['year'])
                        ->where('shift_roaster_month', $input['month'])
                        ->first();
                    $getBasic = Employee::getEmployeeAssociateIdWise($ass_id);
                    if($exist != null){
                        $getId = $exist->id;
                        ShiftRoaster::where('shift_roaster_associate_id', $ass_id)
                        ->where('shift_roaster_year',$input['year'])
                        ->where('shift_roaster_month', $input['month'])
                        ->update([$day => $data]);
                    }else{
                        $getId = ShiftRoaster::insertGetId([
                            'shift_roaster_associate_id' => $ass_id,
                            'shift_roaster_user_id' => $getBasic->as_id,
                            'shift_roaster_year' => $input['year'],
                            'shift_roaster_month' => $input['month'],
                            'day_'.$j => $data
                        ]);
                    }
                    $this->logFileWrite("Shift Roster Day Wise Updated", $getId);
                }
            }
            return 'success';
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }

    public function getRoaster(Request $request)
    {
        //ACL::check(["permission" => "hr_time_shift_roaster"]);

        #-----------------------------------------------------------#
        $s_nm="";
        $unitList = Unit::where('hr_unit_status',1)
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name','hr_unit_id');
            $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        // if(!is_null($unitList)){
        //     foreach ($unitList as $key => $unit) {
        //         if($key == $request->unit){
        //             $s_nm = $unit;
        //             break;
        //         }
        //     }
        //     $join_attendance_table = "hr_attendance_".strtolower($s_nm);
        // }
        $roasters=[];
        $userLineInfo = array();
        $userShifInfo = array();
        $userNameInfo = array();
        $userInfoJoin = array();
        $allUserShifInfo = array();
        $year = date('Y');
        $month = date('m');
        if($request->has('unit')){
            $unit  = $request->post("unit");
            $floor = $request->post("floor_id");
            $line  = $request->post("line_id");
            $area  = $request->post("area");
            $department  = $request->post("department");
            $section     = $request->post("section");
            $subsection  = $request->post("subsection");
            $month = date("n", strtotime($request->post("month")));
            if(strlen($month) == 1){
                $month = '0'.$month;
            }
            $year  =$request->year;

            $userInfo = DB::table("hr_as_basic_info AS b")
            ->select([
                'b.as_id',
                'b.associate_id',
                'b.as_name',
                'b.as_line_id',
                'b.as_shift_id'
            ])
            ->where('b.as_unit_id', $unit)
            ->where('b.as_status', 1)
            ->when(!empty($floor), function($q) use($floor){
                $q->where('b.as_floor_id', $floor);
            })
            ->when(!empty($line), function($q) use($line){
                $q->where('b.as_line_id', $line);
            })
            ->when(!empty($area), function($q) use($area){
                $q->where('b.as_area_id', $area);
            })
            ->when(!empty($department), function($q) use($department){
                $q->where('b.as_department_id', $department);
            })
            ->when(!empty($section), function($q) use($section){
                $q->where('b.as_section_id', $section);
            })
            ->when(!empty($subsection), function($q) use($subsection){
                $q->where('b.as_subsection_id', $subsection);
            })
            ->leftJoin('hr_line AS l','l.hr_line_id','b.as_line_id')
            ->orderBy('b.as_id', 'ASC');

            $userLineInfo = $userInfo->pluck('l.hr_line_name','b.associate_id')->toArray();
            $userShifInfo = $userInfo->pluck('b.as_shift_id','b.associate_id')->toArray();
            $allUserShifInfo = $userInfo->pluck('b.as_shift_id','b.associate_id')->toArray();
            $userNameInfo = $userInfo->pluck('s.as_name','b.associate_id')->toArray();
            $userInfoJoin = $userInfo->pluck('b.associate_id','b.as_id')->toArray();
            $roasters_raw = DB::table('hr_shift_roaster as b')
                ->where(['b.shift_roaster_month' => (int)$month, 'b.shift_roaster_year' => (int)$year])
                ->whereIn('b.shift_roaster_associate_id', $userInfoJoin)
                ->groupBy('b.shift_roaster_associate_id');
                // ->limit(100)
                // ->pluck('associate_id');
            $roasters_out = $roasters_raw->pluck('shift_roaster_associate_id');
            $roasters     = $roasters_raw->get();

            $firstDay = date($year.'-'.$month.'-01');
            $lastDay = date($year.'-'.$month.'-t');
            foreach($roasters as $sKey => $singleRoaster) {
                $s_associate_id = $singleRoaster->shift_roaster_associate_id;
                $singleRoaster->$s_associate_id = [];
                // $singleRoaster->$s_associate_id['shift'] = $shiftName;
                // check holiday
                $holidayRoaster = HolidayRoaster::where('as_id',$s_associate_id)->whereBetween('date',[$firstDay,$lastDay])->pluck('remarks','date');
                $singleRoaster->$s_associate_id['holiday_roaster'] = [];
                if(!$holidayRoaster->isEmpty()){
                    $singleRoaster->$s_associate_id['holiday_roaster'] = $holidayRoaster;
                }
                $holidayEmployee = Employee::where('associate_id',$s_associate_id)->first();
                // if shift assign then check yearly hoiliday
                $singleRoaster->$s_associate_id['holiday_planner'] = [];
                if((int)$holidayEmployee->shift_roaster_status == 0) {
                    $holidayCheck = DB::table("hr_yearly_holiday_planner")
                            ->whereBetween('hr_yhp_dates_of_holidays', [$firstDay,$lastDay])
                            ->where('hr_yhp_status', 1)
                            ->where('hr_yhp_unit', $unit)
                            ->pluck('hr_yhp_comments','hr_yhp_dates_of_holidays');
                    if($holidayCheck){
                        $singleRoaster->$s_associate_id['holiday_planner'] = $holidayCheck;
                    }
                }
                unset($allUserShifInfo[$s_associate_id]);
            }
            // $allUserShifInfo = array_slice($allUserShifInfo, 0, 100);
            // dd($roasters);

            foreach ($allUserShifInfo as $allShiftUser => $shiftName) {
                $allUserShifInfo[$allShiftUser] = [];
                $allUserShifInfo[$allShiftUser]['shift'] = $shiftName;
                // check holiday
                $holidayRoaster = HolidayRoaster::where('as_id',$allShiftUser)->whereBetween('date',[$firstDay,$lastDay])->pluck('remarks','date');
                $allUserShifInfo[$allShiftUser]['holiday_roaster'] = [];
                if(!$holidayRoaster->isEmpty()){
                    $allUserShifInfo[$allShiftUser]['holiday_roaster'] = $holidayRoaster;
                }
                $holidayEmployee = Employee::where('associate_id',$allShiftUser)->first();
                // if shift assign then check yearly hoiliday
                $allUserShifInfo[$allShiftUser]['holiday_planner'] = [];
                if((int)$holidayEmployee->shift_roaster_status == 0) {
                    $holidayCheck = DB::table("hr_yearly_holiday_planner")
                            ->whereBetween('hr_yhp_dates_of_holidays', [$firstDay,$lastDay])
                            ->where('hr_yhp_status', 1)
                            ->where('hr_yhp_unit', $unit)
                            ->pluck('hr_yhp_comments','hr_yhp_dates_of_holidays');
                    if($holidayCheck){
                        $allUserShifInfo[$allShiftUser]['holiday_planner'] = $holidayCheck;
                    }
                }
            }
        }
        // dd($allUserShifInfo);
        $floorList = [];
        $lineList = [];
        $areaList = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        $deptList = [];
        $sectionList = [];
        $subSectionList = [];

        return view('hr/timeattendance/shift_roaster', [
                "unitList"  => $unitList,
                "floorList" => $floorList,
                "lineList"  => $lineList,
                "areaList"  => $areaList,
                "deptList"  => $deptList,
                "roasters"  => $roasters,
                'year'      => $year,
                'month'     => $month,
                "allUserShifInfo"   => $allUserShifInfo,
                "sectionList"       => $sectionList,
                "subSectionList"    => $subSectionList,
                "userLineInfo"      => $userLineInfo,
                "userShifInfo"      => $userShifInfo,
                "userNameInfo"      => $userNameInfo,
                "employeeTypes"     => $employeeTypes
        ]);
    }

    public function datatableReturnColumn($day, $no, $data, $year, $month, $holidayCheckStatus, $holidayCheckComment, $holidayRoasterAll)
    {
        //$data->get_shift_name();
        // $lastDay  = date($year.'-'.$month.'-t');
        $lastDay  = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $returnData = '';
        if((int)$no <= (int)$lastDay) {
            $date = $year.'-'.$month.'-'.$no;
            $associate_id = $data->associate_id;

            // holiday roster check
            if(isset($holidayRoasterAll[$associate_id])){
                if(array_search($date, array_column($holidayRoasterAll[$associate_id], 'date')) !== false){
                    $rosterKey = array_search($date, array_column($holidayRoasterAll[$associate_id], 'date'));
                    $returnData = "Day Off"; // holiday found
                    if(isset($holidayRoasterAll[$associate_id][$rosterKey])) {
                        if($holidayRoasterAll[$associate_id][$rosterKey]['remarks'] == 'General' || $holidayRoasterAll[$associate_id][$rosterKey]['remarks'] == 'OT') {
                            if($data->$day != null) {
                                $returnData = $holidayRoasterAll[$associate_id][$rosterKey]['remarks'].'-'.$data->$day;
                            } else {
                                $returnData = $holidayRoasterAll[$associate_id][$rosterKey]['remarks'].'-'.$data->as_shift_id;
                            }
                        }
                        if($holidayRoasterAll[$associate_id][$rosterKey]['comment'] != null) {
                            $returnData .= ' - '.$holidayRoasterAll[$associate_id][$rosterKey]['comment'];
                        }
                    }
                } else {
                    if($data->shift_roaster_status == 0) {
                        // holiday planner check
                        if(isset($holidayCheckComment[$date])) {
                            $returnData = $holidayCheckComment[$date];
                            // 1 for General and 2 for OT
                            if($holidayCheckStatus[$date] == 1 || $holidayCheckStatus[$date] == 2) {
                                if($data->$day != null) {
                                    $returnData = $holidayCheckComment[$date].'-'.$data->$day;
                                } else {
                                    $returnData = $holidayCheckComment[$date].'-'.$data->as_shift_id;
                                }
                            }
                        } else {
                            if($data->$day != null) {
                                $returnData = $data->$day;
                            } else {
                                $returnData = $data->as_shift_id;
                            }
                        }
                    }

                    if($data->shift_roaster_status == 1) {
                        if($data->$day != null) {
                            $returnData = $data->$day;
                        } else {
                            $returnData = $data->as_shift_id;
                        }
                    }
                }
            } else {
                if($data->shift_roaster_status == 0) {
                    // holiday planner check
                    if(isset($holidayCheckComment[$date])) {
                        $returnData = $holidayCheckComment[$date];
                        // 1 for General and 2 for OT
                        if($holidayCheckStatus[$date] == 1 || $holidayCheckStatus[$date] == 2) {
                            if($data->$day != null) {
                                $returnData = $holidayCheckComment[$date].'-'.$data->$day;
                            } else {
                                $returnData = $holidayCheckComment[$date].'-'.$data->as_shift_id;
                            }
                        }
                    } else {
                        if($data->$day != null) {
                            $returnData = $data->$day;
                        } else {
                            $returnData = $data->as_shift_id;
                        }
                    }
                }

                if($data->shift_roaster_status == 1) {
                    if($data->$day != null) {
                        $returnData = $data->$day;
                    } else {
                        $returnData = $data->as_shift_id;
                    }
                }
            }

        }
        return $returnData;
    }

    public function getRoasterDatatableData(Request $request)
    {
        $unit  = $request->unit??'';
        $floor = $request->floor_id??'';
        $line  = $request->line_id??'';
        $area  = $request->area??'';
        $otnonot     = $request->otnonot??'';
        $department  = $request->department??'';
        $section     = $request->section??'';
        $subsection  = $request->subsection??'';
        $type        = $request->emptype;
        // year month section
        $month_d = $request->month;
        $yearMonth = explode('-', $request->month);
        $month = $yearMonth[1];
        $year = $yearMonth[0];
        // last day and first day of the selected month
        $firstDay = date($year.'-'.$month.'-01');
        $lastDay  = date($year.'-'.$month.'-t');
        //   
        if(isset($request->reporttype) && $request->reporttype == 0){
            
            $shiftRosterData = DB::table('hr_shift_roaster')
                                ->where('shift_roaster_month', (int)$month)
                                ->where('shift_roaster_year', (int)$year);
            $shiftRosterDataSql = $shiftRosterData->toSql();

            $data = DB::table('hr_as_basic_info as b')
                ->select([
                    'b.as_id',
                    'b.associate_id',
                    'b.as_oracle_code',
                    'b.as_name',
                    'b.as_shift_id',
                    'b.shift_roaster_status',
                    'b.as_designation_id',
                    'b.as_line_id',
                    'b.as_floor_id',
                    'b.as_shift_id',
                    'b.as_doj',
                    'b.as_status_date',
                    's.*'
                ])
                ->where('b.as_unit_id', $unit)
                ->where('b.as_status', 1)
                ->when(!empty($floor), function($q) use($floor){
                    $q->where('b.as_floor_id', $floor);
                })
                ->when($otnonot !=null, function($q) use($otnonot){
                    $q->where('b.as_ot', $otnonot);
                })
                ->when(!empty($line), function($q) use($line){
                    $q->where('b.as_line_id', $line);
                })
                ->when(!empty($area), function($q) use($area){
                    $q->where('b.as_area_id', $area);
                })
                ->when(!empty($department), function($q) use($department){
                    $q->where('b.as_department_id', $department);
                })
                ->when(!empty($type), function($q) use($type){
                    $q->where('b.as_emp_type_id', $type);
                })
                ->when(!empty($section), function($q) use($section){
                    $q->where('b.as_section_id', $section);
                })
                ->when(!empty($subsection), function($q) use($subsection){
                    $q->where('b.as_subsection_id', $subsection);
                })
                ->leftjoin(DB::raw('(' . $shiftRosterDataSql. ') AS s'), function($join) use ($shiftRosterData) {
                    $join->on('b.associate_id','s.shift_roaster_associate_id')->addBinding($shiftRosterData->getBindings());
                    
                })
                /*->leftjoin(DB::raw('hr_shift_roaster AS s'), function($join) use ($month, $year){
                    $join->on('b.associate_id','s.shift_roaster_associate_id');
                    $join->where('s.shift_roaster_month', (int)$month);
                    $join->where('s.shift_roaster_year', (int)$year);
                    
                })*/
               /* ->leftjoin('hr_shift_roaster AS s','b.associate_id','s.shift_roaster_associate_id')
                ->where('s.shift_roaster_month', (int)$month)
                ->where('s.shift_roaster_year', (int)$year)*/
                ->orderBy('b.as_id', 'ASC')->get();

            //$userPluck = $data->pluck('associate_id');
            //$data = $data;

        }else{
            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();

            $data = DB::table('hr_shift_roaster as s')
                ->select('emp.as_id',
                    'emp.associate_id',
                    'emp.as_oracle_code',
                    'emp.as_name',
                    'emp.as_shift_id',
                    'emp.shift_roaster_status',
                    'emp.as_designation_id',
                    'emp.as_line_id',
                    'emp.as_floor_id',
                    'emp.as_shift_id',
                    'emp.as_doj',
                    'emp.as_status_date',
                    's.*')
                ->where('s.shift_roaster_month', (int)$month)
                ->where('s.shift_roaster_year', (int)$year)
                ->where('emp.as_unit_id', $unit)
                ->where('emp.as_status', 1)
                ->when(!empty($floor), function($q) use($floor){
                    $q->where('emp.as_floor_id', $floor);
                })
                ->when($otnonot!=null, function($q) use($otnonot){
                    $q->where('emp.as_ot', $otnonot);
                })
                ->when(!empty($line), function($q) use($line){
                    $q->where('emp.as_line_id', $line);
                })
                ->when(!empty($area), function($q) use($area){
                    $q->where('emp.as_area_id', $area);
                })
                ->when(!empty($department), function($q) use($department){
                    $q->where('emp.as_department_id', $department);
                })
                ->when(!empty($type), function($q) use($type){
                    $q->where('emp.as_emp_type_id', $type);
                })
                ->when(!empty($section), function($q) use($section){
                    $q->where('emp.as_section_id', $section);
                })
                ->when(!empty($subsection), function($q) use($subsection){
                    $q->where('emp.as_subsection_id', $subsection);
                })
                ->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('emp.associate_id','s.shift_roaster_associate_id')->addBinding($employeeData->getBindings());
                });
            $data = $data->orderBy('emp.as_id', 'ASC')->get();
        }

        $userPluck = $data->pluck('associate_id');

        $holidayCheck = DB::table("hr_yearly_holiday_planner")
                ->whereBetween('hr_yhp_dates_of_holidays', [$firstDay,$lastDay])
                ->where('hr_yhp_status', 1)
                ->where('hr_yhp_unit', $unit)
                ->get();

        // assign to planner for checking later
        $this->planner = $holidayCheck;

        $this->holiday = collect($this->planner)
                    ->where('hr_yhp_open_status', 0)
                    ->pluck('hr_yhp_comments','hr_yhp_dates_of_holidays');

        // holiday roaster
        $holidayRoasterAll = DB::table('holiday_roaster')
            ->where('status', 1)
            ->whereIn('as_id',$userPluck)
            ->where('remarks','Holiday')
            ->whereBetween('date',[$firstDay,$lastDay])
            ->get()
            ->groupBy('as_id')
            ->map(function($q){
                return collect($q)->keyBy('date');
            });

        $this->roaster = $holidayRoasterAll;

        $data = collect($data)->map(function($q) use($month_d){
            return $this->getShiftEmployee($q,$month_d);
        });

        

        $designation = designation_by_id();
        $line = line_by_id();
        $floor = floor_by_id();

        return DataTables::of($data)->addIndexColumn()
            ->addColumn("designation", function($data) use ($designation){
                return $designation[$data->as_designation_id]['hr_designation_name']??'';
            })
            ->addColumn("line", function($data) use ($line){
                return $line[$data->as_line_id]['hr_line_name']??'';
            })
            ->addColumn("floor", function($data) use ($floor){
                return $floor[$data->as_floor_id]['hr_floor_name']??'';
            })
            ->rawColumns([
                'designation',
                'line',
                'floor'
            ])
            ->make(true);
    }

    protected function getShiftEmployee($emp, $month)
    {
        // holiday planner pluck
        $emp->name    = $emp->as_name; 
        $emp->associate = $emp->associate_id;
        $emp->day_1   = $this->getDailyShift('1',$emp,$month);
        $emp->day_2   = $this->getDailyShift('2',$emp,$month);
        $emp->day_3   = $this->getDailyShift('3',$emp,$month);
        $emp->day_4   = $this->getDailyShift('4',$emp,$month);
        $emp->day_5   = $this->getDailyShift('5',$emp,$month);
        $emp->day_6   = $this->getDailyShift('6',$emp,$month);
        $emp->day_7   = $this->getDailyShift('7',$emp,$month);
        $emp->day_8   = $this->getDailyShift('8',$emp,$month);
        $emp->day_9   = $this->getDailyShift('9',$emp,$month);
        $emp->day_10  = $this->getDailyShift('10',$emp,$month);
        $emp->day_11  = $this->getDailyShift('11',$emp,$month);
        $emp->day_12  = $this->getDailyShift('11',$emp,$month);
        $emp->day_13  = $this->getDailyShift('13',$emp,$month);
        $emp->day_14  = $this->getDailyShift('14',$emp,$month);
        $emp->day_15  = $this->getDailyShift('15',$emp,$month);
        $emp->day_16  = $this->getDailyShift('16',$emp,$month);
        $emp->day_17  = $this->getDailyShift('17',$emp,$month);
        $emp->day_18  = $this->getDailyShift('18',$emp,$month);
        $emp->day_19  = $this->getDailyShift('19',$emp,$month);
        $emp->day_20  = $this->getDailyShift('20',$emp,$month);
        $emp->day_21  = $this->getDailyShift('21',$emp,$month);
        $emp->day_22  = $this->getDailyShift('22',$emp,$month);
        $emp->day_23  = $this->getDailyShift('23',$emp,$month);
        $emp->day_24  = $this->getDailyShift('24',$emp,$month);
        $emp->day_25  = $this->getDailyShift('25',$emp,$month);
        $emp->day_26  = $this->getDailyShift('26',$emp,$month);
        $emp->day_27  = $this->getDailyShift('27',$emp,$month);
        $emp->day_28  = $this->getDailyShift('28',$emp,$month);
        $emp->day_29  = $this->getDailyShift('29',$emp,$month);
        $emp->day_30  = $this->getDailyShift('30',$emp,$month);
        $emp->day_31  = $this->getDailyShift('31',$emp,$month); 

        return $emp;
    }

    protected function getDailyShift($column, $emp, $month)
    {
        $date    = date('Y-m-d', strtotime($month.'-'.$column));
        $clm     = 'day_'.$column;
        // before joining
        if($date < $emp->as_doj) return null;
        // return default shift
        if($emp->$clm  == null) return $emp->as_shift_id;
        
        // global planner holiday
        if($emp->shift_roaster_status == 0 && isset($this->planner[$date]))
            return $this->planner[$date];
        // roaster holiday
        $roaster = $this->roaster[$emp->associate_id]??[];
        if(isset($roaster[$date]))
            return 'Day Off';

        return $emp->$clm ;

    }

    

    public function getRoasterData(Request $request)
    {

        $unit  = $request->post("unit");
        $floor = $request->post("floor");
        $month = date("n", strtotime($request->post("month")));
        $year  = date("Y", strtotime($request->post("year")));

        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table("hr_as_basic_info AS b")
            ->select([
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                's.*',
                'b.associate_id',
                'b.as_name',
                'l.hr_line_name'
            ])
            ->where('b.as_unit_id', $unit)
            ->where('b.as_status', 1)
            ->where(function($query) use($floor) {
                if (!empty($floor))
                {
                    $query->where('b.as_floor_id', $floor);
                }
            })
            ->join("hr_shift_roaster AS s", function($join) use ($year, $month) {
                $join->on( "s.shift_roaster_associate_id", "=", "b.associate_id")
                    ->where("shift_roaster_year", $year)
                    ->where("shift_roaster_month", $month);
            })
            ->leftJoin('hr_line AS l', 'b.as_line_id', 'l.hr_line_id')
            ->get();

        return DataTables::of($data)
            ->addColumn("associate", function($data){
                return "<div class='text-center'>$data->shift_roaster_associate_id  ($data->as_name)</div>";
            })
            ->addColumn("line", function($data){
                return "<div class='text-center'>$data->hr_line_name</div>";
            })
            ->addColumn("year", function($data){
                return date("Y", strtotime($data->shift_roaster_year));
            })
            ->addColumn("month", function($data){
                return date('F', mktime(0, 0, 0, $data->shift_roaster_month));
                return strftime('%B', mktime(0, 0, 0, $data->shift_roaster_month));
            })
            ->rawColumns(['associate', 'line', 'month', 'year'])
            ->toJson();
    }

    public function getFloorByUnit(Request $request)
    {
        $list = "<option value=\"\">Select Floor Name </option>";
        if (!empty($request->unit))
        {
            $floorList  = Floor::where('hr_floor_unit_id', $request->unit)
                    ->where('hr_floor_status', '1')
                    ->pluck('hr_floor_name', 'hr_floor_id');
            foreach ($floorList as $key => $floor)
            {
                $list .= "<option value=\"$key\"";
                if($request->floor_id == $key){
                    $list .= "selected=\"selected\"";
                }
                $list .= ">$floor</option>";
            }
        }
        return $list;
    }

    /*
    *-------------------------------------------------------
    * CRON JOBS
    *-------------------------------------------------------
    */

    public function shiftJobs()
    {
        $day   = "day_".(int)date("j");
        $month = (int)date("n");
        $year  = (int)date("Y");
        $assigns = ShiftRoaster::select("shift_roaster_id", "hr_shift.hr_shift_id", "shift_roaster_associate_id", "$day")
            ->leftJoin("hr_shift", "hr_shift.hr_shift_code", "=", "$day")
            ->where("shift_roaster_month", $month)
            ->where("shift_roaster_year", $year)
            ->where("status", "0")
            ->get();

        foreach ($assigns as $assign)
        {
            // Update Employee Shift ID
            if($request->hr_shift_id != null && $request->hr_shift_id != ''){
                Employee::where("associate_id", $assign->shift_roaster_associate_id)
                    ->update(["as_shift_id" => $assign->hr_shift_id]);

                $this->logFileWrite("Employee Shift id updated by", $assign->shift_roaster_associate_id);

                // Update ShiftRoaster
                ShiftRoaster::where("shift_roaster_id", $assign->shift_roaster_id)
                    ->update(["status" => "1"]);
                $this->logFileWrite("Shift Roster Status updated", $assign->shift_roaster_id );
            }
        }
    }

    # Return  Shift List by Unit
    public function unitShift(Request $request)
    {
        $list = "<option value=\"\">Select Shift </option>";
        if (!empty($request->unit_id))
        {
                $shifts = DB::select("SELECT
                    s1.hr_shift_id,
                    s1.hr_shift_name,
                    s1.hr_shift_code,
                    s1.hr_shift_start_time,
                    s1.hr_shift_end_time,
                    s1.hr_shift_break_time
                    FROM hr_shift s1
                    LEFT JOIN hr_shift s2
                    ON (s1.hr_shift_unit_id = s2.hr_shift_unit_id AND s1.hr_shift_name = s2.hr_shift_name AND s1.hr_shift_id < s2.hr_shift_id)
                    LEFT JOIN hr_unit AS u
                    ON u.hr_unit_id = s1.hr_shift_unit_id
                    WHERE s2.hr_shift_id IS NULL AND s1.hr_shift_unit_id= $request->unit_id
                  ");
            foreach ($shifts as  $value)
            {
                $list .= "<option value=\"$value->hr_shift_name\">$value->hr_shift_name</option>";
            }
        }
        return $list;
    }

    # Return  Shift List by Unit for table
    public function shiftTable(Request $request)
    {
        $list = "";
        $input = $request->all();
        if (!empty($request->unit_id))
        {   
            $year  = date('Y', strtotime($input['searchDate']));
            $month = date('n', strtotime($input['searchDate']));
            // $year  = 2020;
            // $month = 2;
            $filter_day   = date('j', strtotime($input['searchDate']));
            $column = 'day_'.$filter_day;

            $shifts = Shift::select(DB::raw('t.*'))
                ->from(DB::raw('(SELECT * FROM hr_shift ORDER BY hr_shift_id DESC) t'))
                ->groupBy('t.hr_shift_name')
                ->where('t.hr_shift_unit_id', $request->unit_id)
                ->get()
                ->keyBy('hr_shift_name');


            $shiftEmp = DB::table('hr_as_basic_info AS b')
                     ->select('s.hr_shift_start_time','s.hr_shift_end_time','s.hr_shift_break_time','b.as_unit_id','b.as_shift_id','b.associate_id','s.hr_shift_name')
                     ->leftJoin('hr_shift AS s', function($q) {
                         $q->on('s.hr_shift_name', 'b.as_shift_id')
                           ->on('s.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE s.hr_shift_name = b.as_shift_id AND s.hr_shift_unit_id = b.as_unit_id )"));
                     })
                     ->whereNotNull('b.as_shift_id')
                     ->where('b.as_unit_id', $request->unit_id)
                     ->where('b.as_status', 1)
                     ->get();

            $roaster = DB::table('hr_shift_roaster')
                     ->whereNotNull($column)
                     ->where('shift_roaster_year', $year)
                     ->where('shift_roaster_month', $month)
                     ->pluck( $column.' AS roaster', 'shift_roaster_associate_id');

            //dd($shiftEmp, $roaster);
            $shiftwise = [];
            foreach ($shiftEmp  as $key => $sf) {
                if (isset($roaster[$sf->associate_id])) {
                    $shiftwise[$roaster[$sf->associate_id]]['changed'][] = $sf->associate_id;
                }else{
                    $shiftwise[$sf->as_shift_id]['default'][] = $sf->associate_id;
                }
            }
            
            $list = '';

            // return ($getUnit);
            foreach ($shiftwise as $key => $sft) {
                if(isset($shifts[$key])){

                    $value = $shifts[$key];
                    /*$cBreak = $hours = intdiv($value->hr_shift_break_time, 60).':'. ($value->hr_shift_break_time % 60);
                    $cBreak = strtotime(date("H:i", strtotime($cBreak)));
                    $cShifEnd = strtotime(date("H:i", strtotime($value->hr_shift_end_time)));*/
                    // $cBreak = ($value->hr_shift_break_time % 60);
                    //$minute = $cShifEnd + $cBreak;
                    $shiftEndTime = \Carbon\Carbon::parse($input['searchDate'].' '.$value->hr_shift_end_time)->addMinutes($value->hr_shift_break_time)->format('H:i:s'); //gmdate("H:i:s",$minute);
                    
                    $defaultEmployee = 0;
                    if(isset($sft['default'])){
                        $defaultEmployee = count($sft['default']);
                    }
                    $changedEmployee = 0;
                    if(isset($sft['changed'])){
                        $changedEmployee = count($sft['changed']);
                    }
                    
                    $list   .= "<tr><td> $value->hr_shift_name </td>
                          <td> $value->hr_shift_start_time </td>
                          <td> $value->hr_shift_break_time </td>
                          <td> $shiftEndTime </td>
                          <td>
                            $defaultEmployee
                           </td>
                          <td>
                            $changedEmployee
                           </td>
                         </tr>
                          ";
                }
            
            }
            return $list;
        }
        return "unit Empty";
    }


    # REturn section by department
    public function departmentSection(Request $request)
    {
        // dd($request->all());
        $list = "<option value=\"\">Select Department </option>";
        if (!empty($request->department_id))
        {
            $sectionList = Section::select('hr_section_name','hr_section_id','hr_section_department_id')
            ->where('hr_section_status',1)
            ->where('hr_section_department_id',$request->department_id)
            ->get();


            foreach ($sectionList as  $value)
            {
                $list .= "<option value=\"$value->hr_section_id\">$value->hr_section_name</option>";
            }
        }

        // dd($list);
        return $list;
    }

    # REturn department by area
    public function areaDepartment(Request $request)
    {
        // dd($request->all());
        $list = "<option value=\"\">Select Department </option>";
        if (!empty($request->area_id))
        {
            $departmentList = Department::select('hr_department_name','hr_department_id','hr_department_area_id')
            ->where('hr_department_status',1)
            ->where('hr_department_area_id',$request->area_id)
            ->get();


            foreach ($departmentList as  $value)
            {
                $list .= "<option value=\"$value->hr_department_id\">$value->hr_department_name</option>";
            }
        }

        // dd($list);
        return $list;
    }

    # REturn Subsection by section
    public function sectionSubsection(Request $request)
    {
        // dd($request->all());
        $list = "<option value=\"\">Select Sub Section </option>";
        if (!empty($request->section_id))
        {
            $subsectionList = Subsection::select('hr_subsec_name','hr_subsec_id','hr_subsec_section_id')
            ->where('hr_subsec_status',1)
            ->where('hr_subsec_section_id',$request->section_id)
            ->get();


            foreach ($subsectionList as  $value)
            {
                $list .= "<option value=\"$value->hr_subsec_id\">$value->hr_subsec_name</option>";
            }
        }

        // dd($list);
        return $list;
    }

}
