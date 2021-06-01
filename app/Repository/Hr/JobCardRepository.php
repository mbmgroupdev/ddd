<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\JobCardInterface;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Leave;
use App\Models\Hr\Unit;
use App\Repository\Hr\AttendanceProcessRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class JobCardRepository
{
    protected $attProcess;
    
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
        $this->attProcess = new AttendanceProcessRepository;
    }

    public function jobCardByMonth($value='')
    {
        $employee = Employee::where('associate_id', $value['associate'])->first(['as_id', 'as_doj', 'as_unit_id', 'associate_id', 'as_status_date', 'as_ot', 'shift_roaster_status', 'as_emp_type_id', 'as_designation_id', 'as_subsection_id', 'as_location', 'as_status', 'as_oracle_code', 'as_name', 'as_section_id', 'as_gender', 'as_line_id', 'as_floor_id']);
        
        $row = [];
        $row['yearMonth'] = $value['month_year'];
        $row['month'] = date('m', strtotime($value['month_year']));
        $row['year'] = date('Y', strtotime($value['month_year']));
        $row['totalDay'] = Carbon::parse($row['yearMonth'])->daysInMonth;
        $row['tableName'] = get_att_table($employee['as_unit_id']);
        $row = array_merge($row, $employee->toArray());

        $basicInfo = $this->attProcess->getEmployeeMonthStartNEndInfo($row);
       
        $row = array_merge($row, $basicInfo);
        return $this->makeJobCard($row);
    }

    protected function makeJobCard($value='')
    {
        $leaveDate = $this->attProcess->getEmployeeLeaveData($value);

        ksort($leaveDate);

        $rosterPlanner = $this->attProcess->getEmployeeRosterPlannerDateWithKey($value, [0,1,2]);
        $otDate = $rosterPlanner['ot'];
        $holidayDate = $rosterPlanner['holiday'] + $rosterPlanner['ot'];
        $holidayDate = array_diff_key($holidayDate, $leaveDate); 
        ksort($holidayDate);

        $otPresentDate = $rosterPlanner['presentOt'];
        ksort($otPresentDate);

        $offDay = ($holidayDate + $leaveDate);
        // outside work
        $outsideDate = $this->attProcess->getEmployeeOutsideInfo($value);
        // present info
        $getPresent = $this->attProcess->getEmployeePresentInfo($value);
        $presentInDate = collect($getPresent)->keyBy('in_date');
        $presentDiffDate = array_diff_key($presentInDate->toArray(), $offDay);
        $presentDate = array_diff_key($presentDiffDate, $outsideDate);
        $otHour = 0;
        if($value['as_ot']==1){
            $otHour = collect($presentDate)->sum('ot_hour');
            
        }
        // special attendance for check if Friday has extra OT
        $specialAttDate = [];
        if($value['as_ot']==1 && $value['shift_roaster_status'] == 1 ){
            $specialInfo = $this->attProcess->getEmployeeSpecialInfo($value);
            $specialAttDate = collect($specialInfo)->keyBy('in_date');
            $fridayOt = collect($specialInfo)->sum('ot_hour');
            $otHour = $otHour + $fridayOt;
        }

        $value['otHour'] = $otHour;
        $totalAbsent = $value['totalDay'] - (count($leaveDate) + count($holidayDate) + count($presentDiffDate));
        $value['totalAbsent'] = $totalAbsent < 0?0:$totalAbsent;
        $value['totalPresent'] = count($presentDiffDate);
        if($value['yearMonth'] < date('Y-m')){
            $value = $this->employeePreviousInfo($value);
        }

        $data['info'] = $value;
        $data['leaveDate'] = $leaveDate;
        $data['otDate'] = $otDate;
        $data['holidayDate'] = $holidayDate;
        $data['otPresentDate'] = $otPresentDate;
        $data['offDay'] = $offDay;
        $data['presentDate'] = $presentDate;
        $data['outsideDate'] = $outsideDate;
        $data['specialAttDate'] = $specialAttDate;
        $data['unit'] = unit_by_id();
        $data['section'] = section_by_id();
        $data['designation'] = designation_by_id();
        $data['line'] = line_by_id();
        $data['floor'] = floor_by_id();
        return $data;
    }
    public function employeePreviousInfo($value='')
    {
        $salary = HrMonthlySalary::
        where('as_id', $value['associate_id'])
        ->where('month', $value['month'])
        ->where('year', $value['year'])
        ->first();
        if($salary != null){
            $subSection = subSection_by_id();
            $value['as_designation_id'] = $salary->designation_id??$value['as_designation_id'];
            $value['as_section_id'] = $subSection[$salary->sub_section_id]['hr_subsec_section_id']??$value['as_section_id'];
            $value['as_unit_id'] = $salary->unit_id??$value['as_unit_id'];
            $value['as_ot'] = $salary->ot_status??$value['as_ot'];
            $value['otHour'] = $salary->ot_status == 1?$salary->ot_hour:$value['otHour'];
            $value['totalPresent'] = $salary->present??$value['totalPresent'];
            $value['totalAbsent'] = $salary->absent??$value['totalAbsent'];
        }
        return $value;
    }
}