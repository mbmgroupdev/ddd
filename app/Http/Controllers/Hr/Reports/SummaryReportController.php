<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Location;
use App\Models\Hr\Attendace;
use App\Models\Employee;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use DB, PDF;
use Illuminate\Http\Request;
use App\Exports\Hr\SummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class SummaryReportController extends Controller
{
	public function index(Request $request)
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

        $reportType = [
            'ot'=>'OT', 
            'working_hour' => 'Working Hour'
        ];
        if(array_intersect(auth()->user()->unit_permissions(), [1,4,5])){
           $reportType['ot_levis'] = 'OT (Levis format)';
        }
        if(auth()->user()->can('End of Job Benefits')){
           $reportType['left_resign'] = 'Left & Resign';
        }

        if(auth()->user()->can('Manage Employee')){
           $reportType['recruitment'] = 'Recruitment';
        }

        return view('hr/reports/summary/index', compact('unitList','areaList','locationList','reportType'));
	}

    public function attendanceReport(Request $request)
    {
        $input = $request->all();
        try {
            
            $unit = unit_by_id();
            $location = location_by_id();
            $line = line_by_id();
            $floor = floor_by_id();
            $department = department_by_id();
            $designation = designation_by_id();
            $section = section_by_id();
            $subSection = subSection_by_id();
            $area = area_by_id();

            $input['area']       = isset($request['area'])?$request['area']:'';
            $input['location']       = isset($request['location'])?$request['location']:'';
            $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';

            $format = $request['report_group']??'';

         
            $getEmployee = array();
            $data = array();
            $format = $request['report_group'];
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

            $tableName = get_att_table($request['unit']).' AS a';

            if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'ot_levis'){
                
                $attData = DB::table($tableName)
                            ->where('a.in_date','>=', $input['from_date'])
                            ->where('a.in_date','<=', $input['to_date']);
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

            }else if($input['report_type'] == 'absent'){
                $attData = DB::table('hr_absent AS a')
                ->where('a.date', $request['date']);
            }else if($input['report_type'] == 'leave'){
                $attData = DB::table('hr_leave AS l')
                ->whereRaw('? between leave_from and leave_to', [$request['date']])
                ->where('l.leave_status',1);
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
                    return $query->whereNull('emp.'.$input['report_group']);
                }else{
                    return $query->where('emp.'.$input['report_group'], $input['selected']);
                }
            });

            // if non basic table then join basic

            if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'ot_levis'){
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

            if($input['report_type'] == 'ot'){
                
                $attData->where('a.ot_hour', '>', 0);
                $attData->leftJoin('hr_benefits AS bn', 'bn.ben_as_id', 'emp.associate_id');
                if($input['report_format'] == 1 && $input['report_group'] != null){

                    $attData->select($groupBy, DB::raw('count( distinct emp.as_id) as total'), DB::raw('sum(ot_hour) as groupOt'), DB::raw('sum(a.ot_hour*(bn.ben_basic/104)) as ot_amount'))->groupBy($groupBy);
                    $alldata = $attData->get();
                    $totalOtHour =  array_sum(array_column($alldata->toArray(),'groupOt'));
                    $totalOtAmount =  array_sum(array_column($alldata->toArray(),'ot_amount'));
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_unit_id', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', DB::raw('sum(a.ot_hour) as ot_hour'), DB::raw('sum(a.ot_hour*(bn.ben_basic/104)) as ot_amount'),DB::raw('count(  a.in_date) as days'))->orderBy('a.ot_hour','desc')->groupBy('emp.as_id');
                    $alldata = $attData->get();
                    $totalOtHour = array_sum(array_column($alldata->toArray(),'ot_hour'));
                    $totalOtAmount =  array_sum(array_column($alldata->toArray(),'ot_amount'));
                    
                }
                
                $totalValue = numberToTimeClockFormat($totalOtHour);

            }else if($input['report_type'] == 'ot_levis'){
                $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_unit_id', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', 
                    DB::raw('sum(a.ot_hour) as ot_hour'), 
                    DB::raw('count(  a.in_date) as days'),
                    DB::raw('
                        (CASE 
                            WHEN sum(a.ot_hour) > 100 THEN "100" 
                            WHEN sum(a.ot_hour) >= 91 THEN "91-100" 
                            WHEN sum(a.ot_hour) >= 81 THEN "81-90" 
                            WHEN sum(a.ot_hour) >= 71 THEN "71-80" 
                            WHEN sum(a.ot_hour) >= 53 THEN "53-60" 
                            WHEN sum(a.ot_hour) >= 0 THEN "0-52" 
                        END) AS r'
                    )
                )->orderBy('a.ot_hour','desc')
                ->where('emp.as_ot',1)
                ->groupBy('emp.as_id');

                $data = $attData->get();

                if(isset($request['filter']) && $request['filter'] != null){
                    $data = $data->filter(function ($item) use ($request) {
                        return $item->r == $request['filter'];
                    });
                }

                $totalEmployees = count($data);


                
                    
                $uniqueGroups = $data->groupBy($input['report_group'], true);


                return view('hr.reports.summary.ot_levis_52', compact('uniqueGroups', 'format', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','totalEmployees'));
                


            }else if($input['report_type'] == 'working_hour'){
                $attData->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function($join) use ($shiftData) {
                    $join->on('a.hr_shift_code', '=', 's.hr_shift_code')->addBinding($shiftData->getBindings());
                });
                // $attData->whereNotNull('a.out_time');
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    
                    
                    $attData->select($groupBy, DB::raw('count( distinct emp.as_id) as total'), DB::raw('sum((TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time)) as groupHourDuration'))->groupBy($groupBy);
                    
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(),'groupHourDuration'));
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_unit_id','emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', 's.hr_shift_break_time', DB::raw('sum(a.ot_hour) as ot_hour'),DB::raw('count(  a.in_date) as days'))->groupBy('a.as_id');
                    $attData->addSelect(DB::raw('sum(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'));
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(),'hourDuration'));
                    
                }

                $hours = $totalWorkingMinute == 0?0:floor($totalWorkingMinute / 60);
                $minutes = $totalWorkingMinute == 0?0:($totalWorkingMinute % 60);
                $totalValue = sprintf('%02d Hours, %02d Minutes', $hours, $minutes);
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
                    
                }
            }else if($input['report_type'] == 'recruitment'){
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    
                    $attData->select(
                        'emp.'.$input['report_group'], 
                        DB::raw('count(*) as total')
                    )
                    ->groupBy('emp.'.$input['report_group']);
                    
                }
            }


            if($input['report_group'] == 'as_section_id' || $input['report_group'] == 'as_subsection_id'){
                $attData->orderBy('emp.as_department_id', 'asc');
            }else if($input['report_group'] == 'as_line_id'){
                $attData->orderBy('emp.as_unit_id', 'asc');
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
                $avgMin = $totalWorkingMinute == 0?0:$totalWorkingMinute / $totalEmployees;
                $aHours = $avgMin == 0?0:floor($avgMin / 60);
                $aMinutes = $avgMin == 0?0:($avgMin % 60);
                $totalAvgHour = sprintf('%02d Hours, %02d Minutes', $aHours, $aMinutes);
            }

            if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
                $uniqueGroups = collect($getEmployee)->groupBy($request['report_group'],true);
                
            }

           
            if($input['report_type'] == 'ot'){
                return view('hr.reports.summary.ot_summary', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees','totalValue', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','totalOtAmount'));
            }else if($input['report_type'] == 'working_hour'){
                return view('hr.reports.summary.working_hour_summary', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'totalValue', 'totalAvgHour', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area'));
            }else if($input['report_type'] == 'left_resign'){
                return view('hr.reports.summary.left_resign', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area'));
            }else if($input['report_type'] == 'recruitment'){
                return view('hr.reports.summary.recruitment', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }


    



    public function excel(Request $request)
    {
        $input = $request->all();
        $filename = 'Summary Report ';

        if($input['report_type'] == 'ot' || $input['report_type'] == 'ot_levis'){
            $filename = 'Over Time Report ';
        }else if($input['report_type'] == 'working_hour'){
            $filename = 'Working Hour Report ';
        }else if($input['report_type'] == 'left_resign'){
            $filename = 'Left & Resign Report ';
        }else if($input['report_type'] == 'recruitment'){
            $filename = 'Recruitment Report ';
        }
        $filename .= $input['from_date'].' to '.$input['to_date'];
        $filename .= '.xlsx';
        return Excel::download(new SummaryExport($input), $filename);
    }
}