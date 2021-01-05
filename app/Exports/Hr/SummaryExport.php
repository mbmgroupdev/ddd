<?php

namespace App\Exports\Hr;

use App\Models\Hr\HrMonthlySalary;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SummaryExport implements FromView, WithHeadingRow
{
	use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function view(): View
    {
    	$input = $this->data;
    	$input['area']       = isset($input['area'])?$input['area']:'';
        $input['otnonot']    = isset($input['otnonot'])?$input['otnonot']:'';
        $input['department'] = isset($input['department'])?$input['department']:'';
        $input['line_id']    = isset($input['line_id'])?$input['line_id']:'';
        $input['floor_id']   = isset($input['floor_id'])?$input['floor_id']:'';
        $input['section']    = isset($input['section'])?$input['section']:'';
        $input['subSection'] = isset($input['subSection'])?$input['subSection']:'';
        $input['location'] = isset($input['location'])?$input['location']:'';

     
        $getEmployee = array();
        $data = array();
        $format = $input['report_group'];
        $uniqueGroups = ['all'];
        $totalValue = 0;

        // employee basic sql binding
        $employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();
        // shift
        if($input['report_type'] == 'working_hour'){
            $shiftData = DB::table('hr_shift');
            $shiftDataSql = $shiftData->toSql();
        }

        $tableName = get_att_table($input['unit']).' AS a';

        if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late'){
            
            $attData = DB::table($tableName)
                        ->where('a.in_date','>=', $input['from_date'])
                        ->where('a.in_date','<=', $input['to_date']);
        }else if($input['report_type'] == 'absent'){
            $attData = DB::table('hr_absent AS a')
            ->where('a.date', $input['date']);
        }else if($input['report_type'] == 'left_resign'){

            $attData = DB::table('hr_as_basic_info AS emp')
                        ->whereIn('emp.as_status', [2,5])
                        ->where('emp.as_status_date','>=', $input['from_date'])
                        ->where('emp.as_status_date','<=', $input['to_date']);

        }else if($input['report_type'] == 'recruitment'){

            $attData = DB::table('hr_as_basic_info AS emp')
                        ->where('emp.as_doj','>=', $input['from_date'])
                        ->where('emp.as_doj','<=', $input['to_date'])
                        ->where('emp.as_status',1);

        }

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
        ->when($input['otnonot']!=null, function ($query) use($input){
           return $query->where('emp.as_ot',$input['otnonot']);
        })
        ->when(!empty($input['section']), function ($query) use($input){
           return $query->where('emp.as_section_id', $input['section']);
        })
        ->when(!empty($input['subSection']), function ($query) use($input){
           return $query->where('emp.as_subsection_id', $input['subSection']);
        });

        if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour'){
            $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
            });
        }

        if($input['report_group'] == 'ot_hour'){
            $groupBy = 'a.'.$input['report_group'];
            $attData->orderBy('a.ot_hour','desc');
        }else{
            $groupBy = 'emp.'.$input['report_group'];
        }

        // calculation for ot
        if($input['report_type'] == 'ot'){
            
            $attData->where('a.ot_hour', '>', 0);
            $attData->leftJoin('hr_benefits AS bn', 'bn.ben_as_id', 'emp.associate_id');
            if($input['report_format'] == 1 && $input['report_group'] != null){

                $attData->select($groupBy, DB::raw('count( distinct emp.as_id) as total'), DB::raw('sum(ot_hour) as groupOt'), DB::raw('sum(a.ot_hour*(bn.ben_basic/104)) as ot_amount'))->groupBy($groupBy);
                $totalValue =  array_sum(array_column($attData->get()->toArray(),'groupOt'));
                $totalAmount = ceil(array_sum(array_column($attData->get()->toArray(),'ot_amount')));
            }else{
                $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', DB::raw('sum(a.ot_hour) as ot_hour'), DB::raw('sum(a.ot_hour*(bn.ben_basic/104)) as ot_amount'),DB::raw('count(  a.in_date) as days'))->orderBy('a.ot_hour','desc')->groupBy('emp.as_id');
                $totalValue = array_sum(array_column($attData->get()->toArray(),'ot_hour'));
                $totalAmount = ceil(array_sum(array_column($attData->get()->toArray(),'ot_amount')));
                
            }
        }else if($input['report_type'] == 'working_hour'){
            $attData->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function($join) use ($shiftData) {
                $join->on('a.hr_shift_code', '=', 's.hr_shift_code')->addBinding($shiftData->getBindings());
            });
            // $attData->whereNotNull('a.out_time');
            if($input['report_format'] == 1 && $input['report_group'] != null){
                
                
                $attData->select($groupBy, DB::raw('count( distinct emp.as_id) as total'), DB::raw('sum((TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time)) as groupHourDuration'))->groupBy($groupBy);
                
                $totalValue = array_sum(array_column($attData->get()->toArray(),'groupHourDuration'))/60;
            }else{
                $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', 's.hr_shift_break_time', DB::raw('sum(a.ot_hour) as ot_hour'),DB::raw('count(  a.in_date) as days'))->groupBy('a.as_id');
                $attData->addSelect(DB::raw('sum(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'));
                
                $totalValue = array_sum(array_column($attData->get()->toArray(),'hourDuration'))/60;
            }

        }else if($input['report_type'] == 'left_resign'){
            if($input['report_format'] == 1 && $input['report_group'] != null){
                
                $attData->select(
                    'emp.'.$input['report_group'], 
                    DB::raw('count(*) as total'),
                    DB::raw("COUNT(CASE WHEN as_status = '5' THEN as_status END) AS lefts,
                        COUNT(CASE WHEN as_status = '2' THEN as_status END) AS resigns
                    ")
                )
                ->groupBy('emp.'.$input['report_group']);
                
            }else{
                $attData->get();
            }
        }else if($input['report_type'] == 'recruitment'){
            if($input['report_format'] == 1 && $input['report_group'] != null){
                
                $attData->select(
                    'emp.'.$input['report_group'], 
                    DB::raw('count(*) as total')
                )
                ->groupBy('emp.'.$input['report_group']);
                
            }else{
                $attData->get();
            }
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


        if($input['report_type'] == 'working_hour'){
            $totalAvgHour = $totalValue/$totalEmployees;
        }

        if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
            $uniqueGroups = collect($getEmployee)->groupBy($request['report_group'],true);
            $format = $request['report_group'];
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


        // dd($uniqueGroups);
        if($input['report_type'] == 'ot'){
            return view('hr.reports.summary.export.ot', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees','totalValue','totalAmount', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area'));
        }else if($input['report_type'] == 'working_hour'){
            return view('hr.reports.summary.export.working_hour', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'totalValue', 'totalAvgHour', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area'));
        }else if($input['report_type'] == 'left_resign'){
            return view('hr.reports.summary.export.left_resign', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area'));
        }else if($input['report_type'] == 'recruitment'){
            return view('hr.reports.summary.export.recruitment', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area'));
        }
    }
    public function headingRow(): int
    {
        return 3;
    }
}
