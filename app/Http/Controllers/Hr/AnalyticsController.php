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

class AnalyticsController extends Controller
{
    public function index()
    {
    	$data = DB::table('hr_monthly_salary')
    			->select('hr_monthly_salary.*', DB::raw('(ot_hour * ot_rate) as ot_pay'))
    			->get();

    	$thismonth = collect($data)
		    			->where('month',date('m'))
		    			->where('year',date('Y'))
		    			->all();

		$ot = collect($thismonth)
				->sum('ot_pay');

    	$benefits = DB::table('hr_benefits')
    				 ->get();

    	$new_recruit  = DB::table('hr_as_basic_info')
    					 ->where('as_doj','>=',date('Y-m-01'))
    					 ->where('as_doj','<=',date('Y-m-t'))
    					 ->pluck('associate_id');

    	$left  = DB::table('hr_as_basic_info')
    					 ->whereIn('as_status',[2,5])
    					 ->where('as_status_date','>=',date('Y-m-01'))
    					 ->where('as_status_date','<=',date('Y-m-t'))
    					 ->pluck('associate_id');

    	$new_recruited_salary = collect($thismonth)
	    							->whereIn('as_id',$new_recruit)
	    							->sum('gross');
	    $left_salary = collect($benefits)
	    				->whereIn('ben_as_id',$left )
	    				->sum('ben_current_salary');

    	dd($new_recruited_salary, count($new_recruit),count($left),$left_salary, $ot);
    }
}