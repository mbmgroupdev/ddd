<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Unit;
use App\Models\Hr\Leave;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    public function index()
    {
        
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        return view('hr/reports/monthly_activity/index', compact('unitList','areaList'));
    }

    public function maternity(Request $request)
    {
        $input = $request->all();

    	$input['unit'] = isset($request['unit'])?$request['unit']:'';
    	$input['area'] = isset($request['area'])?$request['area']:'';
        $input['department'] = isset($request['department'])?$request['department']:'';
        $input['line'] = isset($request['line_id'])?$request['line_id']:'';
        $input['floor'] = isset($request['floor_id'])?$request['floor_id']:'';
        $input['section'] = isset($request['section'])?$request['section']:'';
        $input['subSectin'] = isset($request['subSection'])?$request['subSection']:'';
        $input['start_date'] = $request['from_date'].'-01';

        $dt = Carbon::createFromFormat('Y-m',$request['to_date']);
        $input['end_date'] =   $dt->endOfMonth()->format('Y-m-d');

        $input['duration'] = Carbon::createFromFormat('Y-m',$request['from_date'])->format('F, Y');
        if($request['from_date'] != $request['to_date']){
            $input['duration'] .= ' - '.$dt->format('F, Y');
        }

        $getEmployee = array();
        $format = $request['report_group'];
        $uniqueGroups = ['all'];
        $totalPay = 0;

        $employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();

        $benefitData = DB::table('hr_benefits');
        $benefitData_sql = $benefitData->toSql();

        $queryData = Leave::where('leave_type', 'Maternity')
        ->where('leave_from','>=', $input['start_date'])
        ->where('leave_from','<=', $input['end_date'])

        ->when(!empty($input['unit']), function ($query) use($input){
           return $query->where('emp.as_unit_id',$input['unit']);
        })
        ->when(!empty($input['area']), function ($query) use($input){
           return $query->where('emp.as_area_id',$input['area']);
        })
        ->when(!empty($input['department']), function ($query) use($input){
           return $query->where('emp.as_department_id',$input['department']);
        })
        ->when(!empty($input['line']), function ($query) use($input){
           return $query->where('emp.as_line_id', $input['line']);
        })
        ->when(!empty($input['floor']), function ($query) use($input){
           return $query->where('emp.as_floor_id',$input['floor']);
        })
        ->when(!empty($input['section']), function ($query) use($input){
           return $query->where('emp.as_section_id', $input['section']);
        })
        ->when(!empty($input['subSection']), function ($query) use($input){
           return $query->where('emp.as_subsection_id', $input['subSection']);
        });
        $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
            $join->on('emp.associate_id','hr_leave.leave_ass_id')->addBinding($employeeData->getBindings());
        });
        $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
            $join->on('ben.ben_as_id','hr_leave.leave_ass_id')->addBinding($benefitData->getBindings());
        });
        

        if($input['report_format'] == 1 && $input['report_group'] != null){
            
            $queryData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'), DB::raw('sum(ben.ben_current_salary * 4) as maternity_pay'))->groupBy('emp.'.$input['report_group']);
        }else{
            $queryData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'hr_leave.*','ben.ben_basic','ben.ben_current_salary' , DB::raw('ben.ben_current_salary * 4 as maternity_pay'));
            
        }
        $getEmployee = $queryData->get();

        $totalPay =  array_sum(array_column($getEmployee->toArray(),'maternity_pay'));
        if($input['report_format'] == 1 && $input['report_group'] != null){
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
                
            }
        }

        return view('hr.reports.monthly_activity.maternity', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees','totalPay'));
    }
}