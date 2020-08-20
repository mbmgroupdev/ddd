<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Models\Hr\Unit;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\AttMBM;
use App\Models\Hr\AttCEIL;
use App\Models\Hr\AttAQL;
use Carbon\Carbon;
use Cache, DB;

class DashboardController extends Controller
{
    public function index()
    {
    	$att_chart = $this->att_data();
    	$ot_chart = $this->ot_data();
    	$salary_chart = $this->salary_data();
        $today_att_chart = $this->today_att();

        //dd($today_att_chart);
    	return view('hr.dashboard.index', compact('ot_chart','salary_chart','att_chart','today_att_chart'));
    }

    public function att_data()
    {
        $att_mbm =  Cache::remember('att_mbm', 10000, function () {
            return cache_att_mbm();
        });

        $att_aql =  Cache::remember('att_aql', 10000, function () {
            return cache_att_aql();
        });

        $att_ceil =  Cache::remember('att_ceil', 1000, function () {
            return cache_att_ceil();
        });
        $att_data = array();
        $now = Carbon::now();
        //$now = Carbon::parse('2019-12-31');
        // retrive last 5 month salary from cache
        for ($i= date('d'); $i > 0; $i--) {
            $thisday = $now->format('Y-m-d');
            $att_data['mbm'][$thisday] = $att_mbm[$thisday]??0;
            $att_data['ceil'][$thisday] = $att_ceil[$thisday]??0;
            $att_data['aql'][$thisday] = $att_aql[$thisday]??0;
            $now = $now->subDay();
        }
        
        return $att_data;
        
    }

    public function ot_data()
    {

        $data =  Cache::remember('monthly_ot', 10000, function () {
            return cache_monthly_ot();
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
        $data =  Cache::remember('monthly_salary', 10000, function () {
            return cache_monthly_salary();
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

        return $salary_data; 
    }


    public function today_att()
    {
        $unit = 1;//auth()->user()->employee['as_unit_id'];
        $today_att = Cache::remember('today_att'.$unit, 10000, function  () use ($unit) {
            return unit_wise_today_att($unit);
        });
        
        
        if(isset($today_att['date']) && $today_att['date'] != date('Y-m-d')){
            $today_att = Cache::put('today_att'.$unit, unit_wise_today_att($unit), 10000);
        }

        return $today_att;
    }





}
