<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\ShiftRoaster;
use DB;
use Illuminate\Http\Request;

class UnitReportsController extends Controller
{
    public function shiftIndex()
    {
    	$unitList = unit_list();
    	return view('hr.reports.unit-shift.index', compact('unitList'));
    }

    public function shiftReport(Request $request)
    {
    	
    	$unitShift = [];
    	$input = $request->all();
    	// dd($input);
    	try {
            
	        $year  = date('Y', strtotime($input['date']));
            $month = date('n', strtotime($input['date']));
            // $year  = 2020;
            // $month = 2;
            $filter_day   = date('j', strtotime($input['date']));
            $column = 'day_'.$filter_day;
            // employee
            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();
            if($input['unit'] == 'all'){
            	$unitList = unit_list();
            }else{
            	$unit = unit_by_id();
            	$unitList[$input['unit']] = $unit[$input['unit']]['hr_unit_name'];
            }

            foreach ($unitList as $unitId => $unit) {
            	$list = "";
            	$queryData = ShiftRoaster::select('hr_shift_roaster.'.$column, DB::raw('count(*) as total'));
	            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
	                $join->on('emp.as_id','hr_shift_roaster.shift_roaster_user_id')->addBinding($employeeData->getBindings());
	            });
	            $queryData->where('emp.as_unit_id', $unitId)
	            ->whereNotNull('hr_shift_roaster.'.$column)
	            ->where('hr_shift_roaster.shift_roaster_year', $year)
	            ->where('hr_shift_roaster.shift_roaster_month', $month);
	            $shiftRoster = $queryData->groupBy('hr_shift_roaster.'.$column)->get()->keyBy($column);

	            // return $shiftRoster;
	            $assignedEmployee = 0;
	            $defaultEmployee = 0;
	            $totalEmployee = 0;
	            // employee unit id
	            $table = 'hr_as_basic_info';
	            $getUnit = DB::table('hr_as_basic_info')
	            ->select('hr_shift.hr_shift_start_time','hr_shift.hr_shift_end_time','hr_shift.hr_shift_break_time','hr_as_basic_info.as_unit_id','hr_as_basic_info.as_shift_id', DB::raw('count(*) as defaultTotal'))
	            ->where('hr_as_basic_info.as_unit_id', $unitId)
	            ->whereNotNull('hr_as_basic_info.as_shift_id')
	            ->where('hr_as_basic_info.as_status', 1)
	            ->leftJoin('hr_shift', function($q) {
	                 $q->on('hr_shift.hr_shift_name', 'hr_as_basic_info.as_shift_id')
	                   ->on('hr_shift.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE hr_shift.hr_shift_name = hr_as_basic_info.as_shift_id AND hr_shift.hr_shift_unit_id = hr_as_basic_info.as_unit_id )"));
	             })
	            ->groupBy('hr_as_basic_info.as_shift_id')
	            ->get();
	            // return ($getUnit);
	            foreach ($getUnit as $key => $value) {
	                $cBreak = $hours = intdiv($value->hr_shift_break_time, 60).':'. ($value->hr_shift_break_time % 60);
	                $cBreak = strtotime(date("H:i", strtotime($cBreak)));
	                $cShifEnd = strtotime(date("H:i", strtotime($value->hr_shift_end_time)));
	                // $cBreak = ($value->hr_shift_break_time % 60);
	                $minute = $cShifEnd + $cBreak;
	                $shiftEndTime = gmdate("H:i:s",$minute);
	                $defaultEmployee = $value->defaultTotal - (isset($shiftRoster[$value->as_shift_id])?$shiftRoster[$value->as_shift_id]->total:0);
	                $totalEmployee += $value->defaultTotal;
	                $changeEmployee = (isset($shiftRoster[$value->as_shift_id])?$shiftRoster[$value->as_shift_id]->total:0);
	                $list   .= "<tr><td> $value->as_shift_id </td>
	                      <td> $value->hr_shift_start_time </td>
	                      <td> $value->hr_shift_break_time </td>
	                      <td> $shiftEndTime </td>
	                      <td>
	                        $defaultEmployee
	                       </td>
	                      <td>
	                        $changeEmployee
	                       </td>
	                     </tr>
	                      ";
	            }
	            $list.= "<tr><td colspan='4'> Total</td>
	                      <td colspan='2'><p class='text-center'>$totalEmployee</p></td>
	                     </tr>
	                      ";
	            $unitShift[$unit] = $list;
            }
        	// dd($unitShift);
            return view('hr.reports.unit-shift.report', compact('unitShift'));
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		return $bug;	
    	}
    }
}
