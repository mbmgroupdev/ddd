<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Unit;
use App\Models\Hr\Line;
use App\Models\Hr\Area;
use App\Models\Hr\Station;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, DB, DataTables, stdClass, Cache;

class ReportController extends Controller
{
    public function line()
    {
    	$lines = Employee::where('as_line_id', '!=', null)
    	->get()
    	->groupBy('as_line_id');

    	dd($lines);

    	return view('hr.line_report', compact('lines'));
    }


    public function mmr(Request $request)
    {
        
    	
    	$date = $request->date??date('Y-m-d');
    	$units  = unit_by_id();
    	$operator = DB::table('hr_as_basic_info')
    				->select(DB::raw('COUNT(*) as emp'), 'as_unit_id')
    				->where('as_status', 1)
    				->where('as_subsection_id', 138)
    				->orWhere('as_subsection_id', 54)
    				->groupBy('as_unit_id')
    				->pluck('emp','as_unit_id');

    	$present = array();

    	$present = DB::table('hr_attendance_mbm AS a')
					->where('a.in_date', $date)
					->select(
						DB::raw('count(*) AS count'),
						'b.as_unit_id'
					)
					->leftJoin('hr_as_basic_info AS b', 'a.as_id', 'b.as_id')
					->where('b.as_status',1) 
					->groupBy('b.as_unit_id')
					->pluck('count', 'b.as_unit_id');

        $present[2] = DB::table('hr_attendance_ceil')
                        ->where('in_date', $date)
                        ->count();
        $present[3] = DB::table('hr_attendance_aql')
                        ->where('in_date', $date)
                        ->count();

        $present[8] = DB::table('hr_attendance_cew')
                        ->where('in_date', $date)
                        ->count();
        

        foreach ($operator as $key => $op) {
            $op = $op == 0?1:$op; 
            $p  = $present[$key]??0;
            $mmr = round(($p/$op),2); 
            $chart_data[] = array(
                'Unit' => $units[$key]['hr_unit_short_name'],
                'MMR'  => $mmr
            );
            $mmr_data[$key] = $mmr;
        }

    	return view('common.daily_mmr_report', compact('chart_data','units','present','operator','mmr_data'));
    }


    public function otEmpAttendance($unit = null, $date = null, $ot)
    {
		$tablename = get_att_table($unit).' AS a';
		
		$data = array();

		$data['total'] = DB::table('hr_as_basic_info')
				 ->select([
					DB::raw('count(*) AS count'),
					'as_subsection_id'
				])
				//->whereDate('as_doj','<=', $date)
				->where('as_unit_id', $unit)
				->where('as_status',1) 
				->where('as_ot', $ot)
				->groupBy('as_subsection_id')
				->get()
				->filter(function ($item)
		        {
		            return $item->count > 0;
		        })
				->pluck('count','as_subsection_id')->toArray();


    	$data['present'] = DB::table($tablename)
    				->where('a.in_date', $date)
    				->select([
    					DB::raw('count(*) AS count'),
    					'b.as_subsection_id'
    				])
    				->leftJoin('hr_as_basic_info AS b', 'a.as_id', 'b.as_id')
    				->where('b.as_unit_id', $unit)
    				->where('b.as_status',1) 
    				->where('b.as_ot', $ot)
    				->groupBy('b.as_subsection_id')
    				->get()
    				->pluck('count','as_subsection_id')->toArray();

        
		return $data;
    }
}