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
    	$unit  = Unit::where(['hr_unit_status' => 1])->get()->keyBy('hr_unit_status');

    	$operator = DB::table('hr_as_basic_info')
    				->select(DB::raw('COUNT(*) as emp'), 'as_unit_id')
    				->where('as_status', 1)
    				->where('as_subsection_id', 138)
    				->orWhere('as_subsection_id', 54)
    				->groupBy('as_unit_id')
    				->pluck('emp','as_unit_id');

    	$present = array();

    	$employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();

    	$present = DB::table('hr_attendance_mbm AS a')
					->whereDate('a.in_time', $date)
					->select(
						DB::raw('count(*) AS count'),
						'b.as_unit_id'
					)
					/*$queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS b'), function($join) use ($employeeData) {
		                $join->on('emp.associate_id','hr_absent.associate_id')->addBinding($employeeData->getBindings());
		            });*/
					->leftJoin('hr_as_basic_info AS b', 'a.as_id', 'b.as_id')
					->where('b.as_status',1) 
					->groupBy('b.as_unit_id')
					->pluck('count', 'b.as_unit_id');

		//dd($present);

    	return view('hr.daily_mmr_report', compact('report'));
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
    				->whereDate('a.in_time', $date)
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