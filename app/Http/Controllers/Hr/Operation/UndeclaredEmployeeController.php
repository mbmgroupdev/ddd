<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;
use DB;

class UndeclaredEmployeeController extends Controller
{
    public function index()
    {
    	$unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('hr_unit_name', 'desc')
        ->pluck('hr_unit_name', 'hr_unit_id');
        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        return view('hr/operation/undeclared/index', compact('unitList','areaList', 'locationList'));
    }

    public function getData(Request $request)
    {
    	$input = $request->all();
    	// return $input;
    	try {
    		ini_set('zlib.output_compression', 1);

            $input['area']       = isset($request['area'])?$request['area']:'';
            $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';
            $input['location'] = isset($request['location'])?$request['location']:'';

            $getEmployee = array();
            $data = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];
            $totalValue = 0;
            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();
            
            $attData = DB::table('hr_attendance_undeclared as a')
            ->where('a.punch_date', $request['date'])
            ->where('a.type', $input['report_type'])
            ->where('a.flag', 0);
            // employee check
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $attData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $attData->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('emp.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('emp.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('emp.as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['selected'] == 'null'){
                    return $query->whereNull($input['report_group']);
                }else{
                    return $query->where($input['report_group'], $input['selected']);
                }
            });
            $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
            });
            
            $groupBy = 'emp.'.$input['report_group'];
            if($input['report_format'] == 1 && $input['report_group'] != null){
                    
                $attData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                
            }else{
                $attData->select('a.punch_date', 'a.punch_time', 'emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code','emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id');
                
            }
            if($input['report_group'] == 'as_section_id' || $input['report_group'] == 'as_subsection_id'){
                $attData->orderBy('emp.as_department_id', 'asc');
            }else{
                $attData->orderBy($groupBy, 'asc');
            }
            if($input['report_group'] == 'as_subsection_id'){
                $attData->orderBy('emp.as_section_id', 'asc');
            } 
            $getEmployee = $attData->get();

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
                    // $format = '';
                }
            }

            $unit = unit_by_id();
            $location = location_by_id();
            $line = line_by_id();
            $floor = floor_by_id();
            $department = department_by_id();
            $designation = designation_by_id();
            $section = section_by_id();
            $subSection = subSection_by_id();
            $area = area_by_id();
            $uniqueGroupEmp = [];

            if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
                if($request['report_group'] == 'ot_hour'){
                    $uniqueGroupEmp = collect($getEmployee)->groupBy(function ($item) {
                            return (string) $item->ot_hour;
                    },true);
                }else{
                    $uniqueGroupEmp = collect($getEmployee)->groupBy($request['report_group'],true);
                }

            }
            return view('hr/operation/undeclared/report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees','totalValue', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }
}
