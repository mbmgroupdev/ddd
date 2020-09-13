<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Unit;
use App\Models\Hr\Leave;
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
    	$unitid = isset($request['unit'])?$request['unit']:'';
    	$areaid = isset($request['area'])?$request['area']:'';
        $departmentid = isset($request['department'])?$request['department']:'';
        $lineid   = isset($request['line_id'])?$request['line_id']:'';
        $florid   = isset($request['floor_id'])?$request['floor_id']:'';
        $section    = isset($request['section'])?$request['section']:'';
        $subSection = isset($request['subSection'])?$request['subSection']:'';

        $getEmployee = array();
        $format = $request['report_group'];
        $uniqueGroups = ['all'];
        $totalValue = 0;

        $employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();


        $queryData = Leave::select('emp.as_id')
        ->where('leave_from', $request['from_date'])
        ->where('leave_type', 'Maternity')
        ->when(!empty($unitid), function ($query) use($unitid){
           return $query->where('emp.as_unit_id',$unitid);
        })
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
        ->when(!empty($section), function ($query) use($section){
           return $query->where('emp.as_section_id', $section);
        })
        ->when(!empty($subSection), function ($query) use($subSection){
           return $query->where('emp.as_subsection_id', $subSection);
        });
        $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
            $join->on('emp.associate_id','hr_leave.leave_ass_id')->addBinding($employeeData->getBindings());
        });
        
        $leaveData = $queryData->get();


        dd($leaveData);
    }
}