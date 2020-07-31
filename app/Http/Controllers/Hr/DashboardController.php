<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Models\Hr\Unit;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\AttMBM;
use App\Models\Hr\AttCEIL;
use Carbon\Carbon;
use Cache, DB;

class DashboardController extends Controller
{
    public function index()
    {
    	$att_chart = $this->att_data();
    	$ot_chart = $this->ot_data();
    	$salary_chart = $this->salary_data();
    	return view('hr.dashboard.index', compact('ot_chart','salary_chart','att_chart'));
    }

    public function att_data()
    {
    	$att_mbm =  Cache::remember('att_mbm', 300, function () {
		    return AttMBM::whereHas('employee', function ($query) {
			    $query->where('as_unit_id','1');
			})->selectRaw('count(*) as present,in_date')
		    /*->whereMonth('in_date',date('m'))
		    ->whereYear('in_date',date('Y'))*/
		    ->whereMonth('in_date','12')
		    ->whereYear('in_date','2019')
    		->groupBy('in_date')
   			->pluck('present','in_date');
		});

		$att_ceil =  Cache::remember('att_ceil', 300, function () {
		    return AttCEIL::selectRaw('count(*) as present,in_date')
		    /*->whereMonth('in_date',date('m'))
		    ->whereYear('in_date',date('Y'))*/
		    ->whereMonth('in_date','12')
		    ->whereYear('in_date','2019')
    		->groupBy('in_date')
   			->pluck('present','in_date');
		});
		$att_data = array();
    	//$now = Carbon::now();
    	$now = Carbon::parse('2019-12-31');
    	// retrive last 5 month salary from cache
    	for ($i= date('d'); $i > 0; $i--) {
	    	$thisday = $now->format('Y-m-d');
    		$att_data['mbm'][$thisday] = $att_mbm[$thisday]??0;
    		$att_data['ceil'][$thisday] = $att_ceil[$thisday]??0;
    		$now = $now->subDay();
    	}
    	
    	return $att_data;
    	
    }

    public function ot_data()
    {

    	$data =  Cache::remember('monthly_ot', 300, function () {
		    return HrMonthlySalary::selectRaw('sum(ot_hour) as ot, CONCAT(year,"-",month) as ym')
    		->groupBy('month','year')
    		->orderBy('id','DESC')
   			->pluck('ot','ym');
		});

    	$ot_data = [];

    	$now = Carbon::now();
    	// retrive last 5 month ot from cache
    	for ($i=0; $i < 5 ; $i++) {
	    	$thismonth = $now->format('Y-m');
	    	$format = $now->format('M');
    		$ot_data[$format] = $data[$thismonth]??0;
    		$now = $now->subMonth();
    	}

    	return array_reverse($ot_data); 

    	
    }

    public function salary_data()
    {
    	$data =  Cache::remember('monthly_salary', 20, function () {
		    return HrMonthlySalary::selectRaw(
		    	'round(sum(salary_payable)/1000,0) as salary, round(sum(ot_hour*ot_rate)/1000,0) as ot, CONCAT(year,"-",month) as ym')
    		->groupBy('month','year')
    		->orderBy('id','DESC')
   			->get()
   			->keyBy('ym')
   			->toArray();
		});

		$salary_data = [];

    	$now = Carbon::now();
    	// retrive last 5 month salary from cache
    	for ($i=0; $i < 5 ; $i++) {
	    	$thismonth = $now->format('Y-m');
    		$salary_data['salary'][$thismonth] = $data[$thismonth]['salary']??0;
    		$salary_data['ot'][$thismonth] = $data[$thismonth]['ot']??0;
    		$salary_data['category'][$thismonth] = $now->format('M');
    		$now = $now->subMonth();
    	}
    	//dd($salary_data);
    	return array_reverse($salary_data); 
    }



}
