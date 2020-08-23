<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Unit;
use DB;
use Illuminate\Http\Request;

class MonthlyActivityReportController extends Controller
{
    public function salary()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        $salaryMin = Benefits::getSalaryRangeMin();
        $salaryMax = Benefits::getSalaryRangeMax();
        return view('hr/reports/monthly_activity/salary.index', compact('unitList','areaList', 'salaryMin', 'salaryMax'));
    }

    public function salaryReport(Request $request)
    {
        $input = $request->all();
        // return $input;
        try {
            $yearMonth = explode('-', $input['month']);
            $month = date('m', strtotime($yearMonth[0]));
            $year = $yearMonth[1];
            $areaid       = isset($request['area'])?$request['area']:'';
            $otnonot      = isset($request['otnonot'])?$request['otnonot']:'';
            $departmentid = isset($request['department'])?$request['department']:'';
            $lineid       = isset($request['line_id'])?$request['line_id']:'';
            $florid       = isset($request['floor_id'])?$request['floor_id']:'';
            $section      = isset($request['section'])?$request['section']:'';
            $subSection   = isset($request['subSection'])?$request['subSection']:'';

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();
            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];

            $queryData = DB::table('hr_monthly_salary AS s')
            ->where('emp.as_unit_id',$input['unit'])
            ->where('emp.as_status',$input['employee_status'])
            ->where('s.year', $year)
            ->where('s.month', $month)
            ->whereBetween('s.gross', [$input['min_sal'], $input['max_sal']])
            ->when(!empty($areaid), function ($query) use($areaid){
               return $query->where('emp.as_area_id',$areaid);
            })
            ->when(!empty($departmentid), function ($query) use($departmentid){
               return $query->where('emp.as_department_id',$departmentid);
            })
            ->when(!empty($lineid), function ($query) use($lineid){
               return $query->where('emp.as_line_id', $lineid);
            })
            ->when(!empty($florid), function ($query) use($florid){
               return $query->where('emp.as_floor_id',$florid);
            })
            ->when($request['otnonot']!=null, function ($query) use($otnonot){
               return $query->where('emp.as_ot',$otnonot);
            })
            ->when(!empty($section), function ($query) use($section){
               return $query->where('emp.as_section_id', $section);
            })
            ->when(!empty($subSection), function ($query) use($subSection){
               return $query->where('emp.as_subsection_id', $subSection);
            });
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
            });
            if($input['report_format'] == 1 && $input['report_group'] != null){
                $queryData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'), DB::raw('sum(total_payable) as groupSalary'))->groupBy('emp.'.$input['report_group']);

            }else{
                $queryData->select('emp.as_id', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_section_id', 's.total_payable');
                $totalSalary = round($queryData->sum("s.total_payable"));
            }
            $getEmployee = $queryData->get();
            if($input['report_format'] == 1 && $input['report_group'] != null){
                $totalSalary = round(array_sum(array_column($getEmployee->toArray(),'groupSalary')));
                $totalEmployees = array_sum(array_column($getEmployee->toArray(),'total'));
            }else{
                $totalEmployees = count($getEmployee);
            }
            if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
                $getEmployeeArray = $getEmployee->toArray();
                $formatBy = array_column($getEmployeeArray, $request['report_group']);
                $uniqueGroups = array_unique($formatBy);
                if (!array_filter($uniqueGroups)) {
                    $uniqueGroups = ['all'];
                    $format = '';
                }
            }
            return view('hr.reports.monthly_activity.salary.report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalSalary', 'totalEmployees'));
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }

    public function salaryReportModal(Request $request)
    {
        $data['type'] = 'error';
        $input = $request->all();
        try {
            if($input['as_id'] == null){
                $data['message'] = 'Employee Id Not Found!';
                return $data;
            }
            if(isset($input['year']) && $input['year'] != null){
                $year = $input['year'];
            }else{
                $year = date('Y');
            }
            // get yearly report
            $getData = HrMonthlySalary::getYearlySalaryMonthWise($input['as_id'], $year);
            $activity = '';
            if(count($getData) == 0){
                $activity.= '<tr>';
                $activity.= '<td colspan="2" class="text-center"> No Data Found! </td>';
                $activity.= '</tr>';
            }else{
                foreach ($getData as $el) {
                    $activity.= '<tr>';
                    $activity.='<td>'.date("F", mktime(0, 0, 0, $el->month, 1)).'</td>';
                    $activity.='<td>'.$el->total_payable.'</td>';
                    $activity.= '</tr>';
                }
            }
            $data['value'] = $activity;
            $data['type'] = 'success';
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
    }
}
