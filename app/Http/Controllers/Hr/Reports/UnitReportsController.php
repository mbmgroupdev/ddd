<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\Shift;
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

            $roaster = DB::table('hr_shift_roaster')
	                     ->whereNotNull($column)
	                     ->where('shift_roaster_year', $year)
	                     ->where('shift_roaster_month', $month)
	                     ->pluck( $column.' AS roaster', 'shift_roaster_associate_id');

            foreach ($unitList as $unitId => $unit) {

            	$shifts = Shift::select(DB::raw('t.*'))
	                ->from(DB::raw('(SELECT * FROM hr_shift ORDER BY hr_shift_id DESC) t'))
	                ->groupBy('t.hr_shift_name')
	                ->where('t.hr_shift_unit_id', $unitId)
	                ->get()
	                ->keyBy('hr_shift_name');

	            $shift = DB::table('hr_as_basic_info AS b')
	                     ->select('s.hr_shift_start_time','s.hr_shift_end_time','s.hr_shift_break_time','b.as_unit_id','b.as_shift_id','b.associate_id','s.hr_shift_name')
	                     ->leftJoin('hr_shift AS s', function($q) {
	                         $q->on('s.hr_shift_name', 'b.as_shift_id')
	                           ->on('s.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE s.hr_shift_name = b.as_shift_id AND s.hr_shift_unit_id = b.as_unit_id )"));
	                     })
	                     ->whereNotNull('b.as_shift_id')
	                     ->where('b.as_unit_id', $unitId)
	                     ->where('b.as_status', 1)
	                     ->get();

	            

	            $shiftwise = [];
	            foreach ($shift  as $key => $sf) {
	                if (isset($roaster[$sf->associate_id])) {
	                    $shiftwise[$roaster[$sf->associate_id]]['changed'][] = $sf->associate_id;
	                }else{
	                    $shiftwise[$sf->hr_shift_name]['default'][] = $sf->associate_id;
	                }
	            }
	            
	            $list = '';

	            // return ($getUnit);
	            foreach ($shiftwise as $key => $sft) {
	                if(isset($shifts[$key])){

	                    $value = $shifts[$key];
	                    $cBreak = $hours = intdiv($value->hr_shift_break_time, 60).':'. ($value->hr_shift_break_time % 60);
	                    $cBreak = strtotime(date("H:i", strtotime($cBreak)));
	                    $cShifEnd = strtotime(date("H:i", strtotime($value->hr_shift_end_time)));
	                    // $cBreak = ($value->hr_shift_break_time % 60);
	                    $minute = $cShifEnd + $cBreak;
	                    $shiftEndTime = gmdate("H:i:s",$minute);
	                    
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
