<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Benefits;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\Hr\YearlyHolyDay;
use Attendance2;
use Attendance;
use Carbon\Carbon;
use DB, auth;
use Illuminate\Http\Request;

class SalarySheetCustomController extends Controller
{
    public function convertMonthNameToNumber($month)
    {
        return Carbon::parse("1 $month")->month;
    }

    public function index()
    {
        /*$value = 100;
        // encrypt process
        $encrypt = encryptStringValue($value);
        // decrypt process
        $decrypt = decryptStringValue($encrypt);
        return $decrypt;*/
        try {
            //$data['getEmployees']  = Employee::getSelectIdNameEmployee();
            $data['unitList']      = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->pluck('hr_unit_name', 'hr_unit_id');
            $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
            $data['floorList']     = Floor::getFloorList();
            $data['deptList']      = Department::getDeptList();
            $data['sectionList']   = Section::getSectionList();
            $data['subSectionList'] = Subsection::getSubSectionList();
            $data['salaryMin']      = Benefits::getSalaryRangeMin();
            $data['salaryMax']      = Benefits::getSalaryRangeMax();
            $data['getYear']       = HrMonthlySalary::select('year')->distinct('year')->orderBy('year', 'desc')->pluck('year');
            return view('hr.operation.salary.index', $data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    // for extra ot
    public function salary_sheet_extra_ot()
    {
        try {
            $data['getEmployees']  = Employee::getSelectIdNameEmployee();
            $data['unitList']      = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->pluck('hr_unit_name', 'hr_unit_id');
            $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
            $data['floorList']     = Floor::getFloorList();
            $data['deptList']      = Department::getDeptList();
            $data['sectionList']   = Section::getSectionList();
            $data['subSectionList'] = Subsection::getSubSectionList();
            $data['salaryMin']      = Benefits::getSalaryRangeMin();
            $data['salaryMax']      = Benefits::getSalaryRangeMax();
            //return $data;
            return view('hr.reports.salary_sheet_extra_ot', $data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function individualSearch(Request $request)
    {
        $input = $request->all();
        try {
            // form explode
            $formExplode = explode('-', $input['form_date']);
            $input['formMonth'] = $this->convertMonthNameToNumber($formExplode[0]);
            $input['formYear']  = $formExplode[1];
            // to explode
            $toExplode          = explode('-', $input['to_date']);
            $input['toMonth']   = $this->convertMonthNameToNumber($toExplode[0]);
            $input['toYear']    = $toExplode[1];
            $query              = Employee::getSingleEmployeeWiseSalarySheet($input);
            $getSalaryList      = $query->get();
            $locationDataSet    = $getSalaryList->toArray();
            $locationList       = array_column($locationDataSet, 'as_location');
            $uniqueLocation     = array_unique($locationList);
            $locationDataSet    = array_chunk($locationDataSet, 5, true);
            $getEmployee        = Employee::getEmployeeAssociateIdWise($input['as_id']);
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['pay_date']       = '';
            $pageHead['unit_name']      = $getEmployee->unit['hr_unit_name_bn'];
            $pageHead['for_date']       = $input['form_date'].' - '.$input['to_date'];
            //$pageHead['total_work_day'] = $input['disbursed_date'];
            $pageHead['floor_name']     = $getEmployee->floor['hr_floor_name_bn'];

            $pageHead = (object) $pageHead;
            return view('hr.common.employee_salary_sheet', compact('uniqueLocation', 'getSalaryList', 'pageHead', 'locationDataSet'));
        } catch (\Exception $e) {
           return 'error';
        }
    }

    public function groupWiseSalarySheet($associate_id, $input)
    {
        try {
            $getSalary = HrMonthlySalary::getSalaryListFilterWise($associate_id, $input['month'], $input['year'], $input['min_sal'], $input['max_sal']);
            $holiday_ot_day = [];
            if($getSalary != null){
                $total_ot_minutes = 0;
                if($input['ot_range'] > 0){
                    $year   = $input['year'];
                    $month  = $input['month'];
                    $date   = ($year."-".$month."-"."01");
                    $startDay   = date('Y-m-d', strtotime($date));
                    $endDay     = date('Y-m-t', strtotime($date));
                    $totalDays  = (date('d', strtotime($endDay))-date('d', strtotime($startDay)));
                    $x  =   1;
                    $get_salary_ot_minute = 0;
                    $ot_hours = 0;
                    for($i=0; $i<=$totalDays; $i++) {
                        $date       = ($year."-".$month."-".$x++);
                        // check holiday ot (if holiday+ot status found) than skip ot hour calculation
                        $holiday_ot = YearlyHolyDay::where(['hr_yhp_dates_of_holidays' => $date, 'hr_yhp_unit' => $input['unit'], 'hr_yhp_open_status' => 2])->first();
                        if(empty($holiday_ot)) {
                            $startDay   = date('Y-m-d', strtotime($date));
                            $att        = Attendance2::track($associate_id, $input['unit'], $startDay, $startDay);
                            $ot_minutes    = $att->overtime_minutes;
                            if($ot_minutes > ($input['ot_range'] * 60)) {
                                $ot_minutes = ($input['ot_range'] * 60);
                            }
                            $total_ot_minutes += $ot_minutes;
                        }
                    }
                    // convert minute from hours
                    if($getSalary->ot_hour) {
                        $get_salary_ot_minute = ($this->hoursToseconds($getSalary->ot_hour)/60);
                    }
                   // change value if geter than salary ot_hour
                    if($get_salary_ot_minute > $total_ot_minutes){
                        $ot_hours = number_format((float)($total_ot_minutes/60), 2, '.', ''); // minute to float hours
                        $ot_hours = sprintf('%02d:%02d', (int) $ot_hours, fmod($ot_hours, 1) * 60); // convert float hours to hour:minute
                        $getSalary->ot_hour = $ot_hours;
                    }
                }
            }
            return $getSalary;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hoursToseconds($inHour) 
    {
        try {
            list($hours,$minutes,$seconds) = array_pad(explode(':',$inHour),3,'00');
            sscanf($inHour, "%d:%d:%d", $hours, $minutes, $seconds);
            return isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
        } catch(\Exception $e) {
            return $inHour;
        }
    }

    // for extra ot
    public function groupWiseSalarySheetExtraOt($associate_id, $input)
    {
        try {
            $getSalary = HrMonthlySalary::getSalaryListFilterWise($associate_id, $input['month'], $input['year'], $input['min_sal'], $input['max_sal']);
            $holiday_ot_day = [];
            if($getSalary != null){
                $total_ot_minutes = 0;
                if($input['ot_range'] > 0){
                    $year   = $input['year'];
                    $month  = $input['month'];
                    $date   = ($year."-".$month."-"."01");
                    $startDay   = date('Y-m-d', strtotime($date));
                    $endDay     = date('Y-m-t', strtotime($date));
                    $totalDays  = (date('d', strtotime($endDay))-date('d', strtotime($startDay)));
                    $x  =   1;
                    $get_salary_ot_minute = 0;
                    $ot_hours = 0;
                    $getSalary->holiday_ot_minutes = 0;
                    $ot_overtime_minutes = [];
                    for($i=0; $i<=$totalDays; $i++) {
                        $date       = ($year."-".sprintf("%02d", $month)."-".sprintf("%02d", $x++)); // prepend 0 to 1 to 9
                        // check holiday ot (if holiday+ot status found) than skip ot hour calculation
                        $holiday_ot = YearlyHolyDay::where(['hr_yhp_dates_of_holidays' => $date, 'hr_yhp_unit' => $input['unit'], 'hr_yhp_open_status' => 2])->first();
                        $startDay   = date('Y-m-d', strtotime($date));
                        $att        = Attendance2::track($associate_id, $input['unit'], $startDay, $startDay);
                        $ot_minutes = $att->overtime_minutes;
                        // if hoiliday not found
                        if(empty($holiday_ot)) {
                            if($ot_minutes > 0) {
                                $ot_overtime_minutes[] = $ot_minutes-($input['ot_range'] * 60);
                            }
                        } else {
                            // get holiday ot minutes
                            $ot_minutes = $this->getEmployeeHolidayOt($getSalary,$startDay,$input['unit']);
                            if($ot_minutes) {
                                $getSalary->holiday_ot_minutes += $ot_minutes;
                            }
                            $ot_overtime_minutes[] = $ot_minutes;
                        }
                    }
                    if(!empty($ot_overtime_minutes)) {
                        // remove nagative values
                        $ot_overtime_minutes = array_filter($ot_overtime_minutes, function ($v) {
                          return $v > 0;
                        });
                        // calculate total ot_overtime minutes
                        $total_ot_minutes = array_sum($ot_overtime_minutes);
                        // convert minute from hours
                        if($getSalary->ot_hour) {
                            $get_salary_ot_minute = ($this->hoursToseconds($getSalary->ot_hour)/60);
                        }
                        if($get_salary_ot_minute > $total_ot_minutes) {
                            $ot_hours = number_format((float)($total_ot_minutes/60), 2, '.', ''); // minute to float hours
                            $ot_hours = sprintf('%02d:%02d', (int) $ot_hours, fmod($ot_hours, 1) * 60); // convert float hours to hour:minute
                            $getSalary->ot_hour = $ot_hours;
                        }
                    } else {
                        $getSalary->ot_hour = 0;
                    }
                }
            }
            return $getSalary;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getEmployeeHolidayOt($getSalary,$startDay,$unit)
    {
        $data_ex = (object)[];
        $extra_ot_minute = 0;
        if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
            $tableName="hr_attendance_mbm";
        } else if($unit ==2){
            $tableName="hr_attendance_ceil";
        } else if($unit ==3){
            $tableName="hr_attendance_aql";
        } else if($unit ==6){
            $tableName="hr_attendance_ho";
        } else if($unit ==8){
            $tableName="hr_attendance_cew";
        } else{
            $tableName="hr_attendance_mbm";
        }
        // get holiday ot
        $hr_basic = Employee::where('associate_id',$getSalary->as_id)->first();
        $data_ex = DB::table($tableName)->where('as_id',$hr_basic->as_id)->whereDate('in_time', '=', $startDay)->first();
        if(isset($data_ex->id)) {
            $to = Carbon::createFromFormat('Y-m-d H:s:i', $data_ex->in_time);
            $from = Carbon::createFromFormat('Y-m-d H:s:i', $data_ex->out_time);
            $extra_ot_minute = $to->diffInMinutes($from);
        }
        return $extra_ot_minute;
    }

    public function ajaxGetEmployee(Request $request)
    {
        $data = $request->all();
        $getUnit = Unit::getUnitNameBangla($data['unit']);
        $month = date("F", mktime(0, 0, 0, $data['month'], 10));
        // return $data;
        try {
            $info = [];
            if(isset($data['area'])){
                $info['area'] = Area::where('hr_area_id',$data['area'])->first()->hr_area_name_bn??'';
            }
            if(isset($data['floor'])){
                $info['floor'] = Floor::where('hr_floor_id',$data['floor'])->first()->hr_floor_name_bn??'';
            }
            if(isset($data['department'])){
                $info['department'] = Department::where('hr_department_id',$data['department'])->first()->hr_department_name_bn??'';
            }
            if(isset($data['section'])){
                $info['section'] = Section::where('hr_section_id',$data['section'])->first()->hr_section_name_bn??'';
            }
            if(isset($data['sub_section'])){
                $info['sub_section'] = Subsection::where('hr_subsec_id',$data['sub_section'])->first()->hr_subsec_name_bn??'';
            }


            $query = Employee::getEmployeeWiseSalarySheet($data);
            $getSalaryList = $query->get();
            $locationDataSet = $getSalaryList->toArray();
            // $locationDataSet = array_slice($locationDataSet, 0, 15);
            $locationList = array_column($locationDataSet, 'as_location');
            $uniqueLocation = array_unique($locationList);
            $locationDataSet = array_chunk($locationDataSet, 5, true);
            // $title = $getUnit->hr_unit_name_bn;
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['pay_date']       = $data['disbursed_date'];
            $pageHead['unit_name']      = $getUnit->hr_unit_name_bn;
            $pageHead['for_date']       = Custom::engToBnConvert($month.' - '.$data['year']);
            $pageHead['floor_name']     = $data['floor'];
            $pageHead['month']     = $data['month'];
            $pageHead['year']     = $data['year'];
            $pageHead = (object)$pageHead;
            // dd($locationDataSet, $uniqueLocation);
            return view('hr.common.employee_salary_sheet', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            // return $bug;
            return 'error';

        }
    }
    public function employeeWise(Request $request)
    {
        $input = $request->all();
        try {
            $yearMonth = explode('-', $input['month']);
            $month = $this->convertMonthNameToNumber($yearMonth[0]);
            $data = [
                'month' => $month,
                'year'  => $yearMonth[1]
            ];

            $query = Employee::whereIn('associate_id', $input['as_id'])
            ->with(array('salary'=>function($query) use ($data)
            {
                $query->where('month', $data['month']);
                $query->where('year', $data['year']);
            }));
            $getSalaryList = $query->get();
            $locationDataSet = $getSalaryList->toArray();

            $locationList = array_column($locationDataSet, 'as_location');
            $uniqueLocation = array_unique($locationList);
            $locationDataSet = array_chunk($locationDataSet, 5, true);
            // $title = $getUnit->hr_unit_name_bn;
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['pay_date']       = '';
            $pageHead['unit_name']      = '';
            $pageHead['for_date']       = Custom::engToBnConvert($month.' - '.$yearMonth[1]);
            $pageHead['floor_name']     = '';
            $pageHead['month']     = $data['month'];
            $pageHead['year']     = $data['year'];
            $pageHead = (object)$pageHead;
            // dd($locationDataSet, $uniqueLocation);
            return view('hr.common.employee_salary_sheet', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return 'error';
        }
    }
    public function ajaxGetMultiSearchResultChunk(Request $request)
    {
        try {
            $input          = $request->input('input');
            $getEmployees   = $request->input('employeechunk');
            $input['unit']  = intval($input['unit']);

            $getUnit = Unit::getUnitNameBangla($input['unit']);
            if($getUnit != null){
                $unitName = $getUnit->hr_unit_name_bn;
            } else {
                $unitName = '';
            }
            $getFloor = Floor::getFloorNameBangla($input['floor']);

            if($getFloor != null){
                $floorName = $getFloor->hr_floor_name_bn;
            } else {
                $floorName = '';
            }
            $pageHead['current_date']   = date('d-m-Y');
            $pageHead['current_time']   = date('H:i');
            $pageHead['pay_date']       = $input['disbursed_date'];
            $pageHead['unit_name']      = $unitName;
            $pageHead['for_date']       = $input['month'].' - '.$input['year'];
            //$pageHead['total_work_day'] = $input['disbursed_date'];
            $pageHead['floor_name']     = $floorName;
            $result['pageHead']         = $pageHead;
            //
            $getSalaryList    = array();
            $result['group1'] = array();
            $result['group2'] = array();
            $result['group3'] = array();
            foreach ($getEmployees as $k=>$employee) {
                if((intval($input['unit']) == $employee['as_unit_id']) && (intval($input['unit']) == $employee['as_location'])){

                    $group1 = $this->groupWiseSalarySheet($employee['associate_id'], $input);
                    if($group1 != null){
                        $result['group1'][] = $group1;
                    }

                } elseif ((intval($input['unit']) == $employee['as_unit_id']) && (intval($input['unit']) != $employee['as_location'])){

                    $group2 = $this->groupWiseSalarySheet($employee['associate_id'], $input);
                    if($group2 != null){
                        $result['group2'][] = $group2;
                    }

                } elseif ((intval($input['unit']) != $employee['as_unit_id']) && (intval($input['unit']) == $employee['as_location'])){
                    $group3 = $this->groupWiseSalarySheet($employee['associate_id'], $input);
                    if($group3 != null){
                        $result['group3'][] = $group3;
                    }
                } else {
                    return 'error';
                }
            }
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    // for extra ot
    public function ajaxGetMultiSearchResultExtraOtChunk(Request $request)
    {
        try{
            $input          = $request->input('input');
            $getEmployees   = $request->input('employeechunk');
            $input['unit']  = intval($input['unit']);

            $getUnit = Unit::getUnitNameBangla($input['unit']);
            if($getUnit != null){
                $unitName = $getUnit->hr_unit_name_bn;
            } else {
                $unitName = '';
            }
            $getFloor = Floor::getFloorNameBangla($input['floor']);

            if($getFloor != null){
                $floorName = $getFloor->hr_floor_name_bn;
            } else {
                $floorName = '';
            }
            $pageHead['current_date']   = date('d-m-Y');
            $pageHead['current_time']   = date('H:i');
            $pageHead['pay_date']       = $input['disbursed_date'];
            $pageHead['unit_name']      = $unitName;
            $pageHead['for_date']       = $input['month'].' - '.$input['year'];
            //$pageHead['total_work_day'] = $input['disbursed_date'];
            $pageHead['floor_name']     = $floorName;
            $result['pageHead']         = $pageHead;
            //
            $getSalaryList    = array();
            $result['group1'] = array();
            $result['group2'] = array();
            $result['group3'] = array();
            foreach ($getEmployees as $k=>$employee) {
                if((intval($input['unit']) == $employee['as_unit_id']) && (intval($input['unit']) == $employee['as_location'])){

                    $group1 = $this->groupWiseSalarySheetExtraOt($employee['associate_id'], $input);
                    if($group1 != null){
                        $result['group1'][] = $group1;
                    }

                } elseif ((intval($input['unit']) == $employee['as_unit_id']) && (intval($input['unit']) != $employee['as_location'])){

                    $group2 = $this->groupWiseSalarySheetExtraOt($employee['associate_id'], $input);
                    if($group2 != null){
                        $result['group2'][] = $group2;
                    }

                } elseif ((intval($input['unit']) != $employee['as_unit_id']) && (intval($input['unit']) == $employee['as_location'])){
                    $group3 = $this->groupWiseSalarySheetExtraOt($employee['associate_id'], $input);
                    if($group3 != null){
                        $result['group3'][] = $group3;
                    }
                } else {
                    return 'error';
                }
            }
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function ajaxGetMultiSearchResultList(Request $request)
    {
        try{
            $result = json_decode($request->viewdata);
            $data['pageHead'] = $result->pageHead;
            $data['group1'] = $result->group1;
            $data['group2'] = $result->group2;
            $data['group3'] = $result->group3;
            // return $data;
            $result = view('hr.common.group_wise_salary_sheet_list', $data)->render();
            return json_encode($result);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function multiSearch(Request $request)
    {
        $input = $request->all();
        $input['unit'] = intval($input['unit']);

        try {
            //getEmployee Unit, Floor, Area, Deparment, Section, SubScetion with
            $getEmployees = Employee::getEmployeeFilterWise($input);
            $getUnit = Unit::getUnitNameBangla($input['unit']);

            if($getUnit != null){
                $unitName = $getUnit->hr_unit_name_bn;
            } else {
                $unitName = '';
            }
            $getFloor = Floor::getFloorNameBangla($input['floor']);

            if($getFloor != null){
                $floorName = $getFloor->hr_floor_name_bn;
            } else {
                $floorName = '';
            }

            $pageHead['current_date']   = date('d-m-Y');
            $pageHead['current_time']   = date('H:i');
            $pageHead['pay_date']       = $input['disbursed_date'];
            $pageHead['unit_name']      = $unitName;
            $pageHead['for_date']       = $input['month'].' - '.$input['year'];
            //$pageHead['total_work_day'] = $input['disbursed_date'];
            $pageHead['floor_name']     = $floorName;
            $result['pageHead']         = $pageHead;
            //
            $getSalaryList    = array();
            $result['group1'] = array();
            $result['group2'] = array();
            $result['group3'] = array();
            foreach ($getEmployees as $employee) {
                if((intval($input['unit']) == $employee->as_unit_id) && (intval($input['unit']) == $employee->as_location)){

                    $group1 = $this->groupWiseSalarySheet($employee->associate_id, $input);
                    if($group1 != null){
                        $result['group1'][] = $group1;
                    }

                } elseif ((intval($input['unit']) == $employee->as_unit_id) && (intval($input['unit']) != $employee->as_location)){

                    $group2 = $this->groupWiseSalarySheet($employee->associate_id, $input);
                    if($group2 != null){
                        $result['group2'][] = $group2;
                    }

                } elseif ((intval($input['unit']) != $employee->as_unit_id) && (intval($input['unit']) == $employee->as_location)){
                    $group3 = $this->groupWiseSalarySheet($employee->associate_id, $input);
                    if($group3 != null){
                        $result['group3'][] = $group3;
                    }
                } else {
                    return "error";
                }
            }
            //return $result['group3'];
            return view('hr.common.group_wise_salary_sheet_list', $result);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function empDisbursed(Request $request){
        $input = $request->all();
        $result = array();
        $result['status'] = 'error';
        try {
            $salary = HrMonthlySalary::getEmployeeSalary($input);
            if($salary != null){
                $getSalary = HrMonthlySalary::where('id',$salary->id)
                ->update([
                    'disburse_date' => date('Y-m-d'),
                    'updated_by' => auth::user()->id
                ]);
                $result['status'] = 'success';
                $result['value'] = 'হ্যাঁ '.Custom::engToBnConvert(date('Y-m-d'));
            }else{
                $result['value'] = 'কর্মচারী পাওয়া যায় নি';
            }
            return $result;
        } catch (\Exception $e) {
            $result['value'] = $e->getMessage();
            return $result;
        }
    }
}
