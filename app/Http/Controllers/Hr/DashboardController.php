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

        $permitted_associate = auth()->user()->permitted_associate()->toArray();
        $permitted_asid      = auth()->user()->permitted_asid()->toArray();
        $att_chart = $this->att_data($permitted_asid );
        $ot_chart = $this->ot_data($permitted_associate);
        $salary_chart = $this->salary_data($permitted_associate);
        $today_att_chart = $this->today_att($permitted_associate);

        return view('hr.dashboard.index', compact('ot_chart','salary_chart','att_chart','today_att_chart'));
    }

    public function att_data($per_id)
    {
        $att_mbm =  Cache::remember('att_mbm', 10000, function () {
            return cache_att_mbm();
        });

        $att_mfw =  Cache::remember('att_mfw', 10000, function () {
            return cache_att_mfw();
        });

        $att_mbm2 =  Cache::remember('att_mbm2', 10000, function () {
            return cache_att_mbm2();
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

            if(isset($att_mbm[$thisday])){
                $mbm = collect($att_mbm[$thisday])->keyBy('as_id')->keys()->toArray();
                $res = array_intersect($mbm, $per_id);  
                $att_data['mbm'][$thisday] = count($res);
            }else{
                $att_data['mbm'][$thisday] = 0;
            }

            if(isset($att_ceil[$thisday])){
                $ceil = collect($att_ceil[$thisday])->keyBy('as_id')->keys()->toArray();
                $res = array_intersect($ceil, $per_id);  
                $att_data['ceil'][$thisday] = count($res);
            }else{
                $att_data['ceil'][$thisday] = 0;
            }

            if(isset($att_aql[$thisday])){
                $aql = collect($att_aql[$thisday])->keyBy('as_id')->keys()->toArray();
                $res = array_intersect($aql, $per_id);  
                $att_data['aql'][$thisday] = count($res);
            }else{
                $att_data['aql'][$thisday] = 0;
            }

            if(isset($att_mfw[$thisday])){
                $mfw = collect($att_mfw[$thisday])->keyBy('as_id')->keys()->toArray();
                $res = array_intersect($mfw, $per_id);  
                $att_data['mfw'][$thisday] = count($res);
            }else{
                $att_data['mfw'][$thisday] = 0;
            }

            if(isset($att_mbm2[$thisday])){
                $mbm2 = collect($att_mbm2[$thisday])->keyBy('as_id')->keys()->toArray();
                $res = array_intersect($mbm2, $per_id);  
                $att_data['mbm2'][$thisday] = count($res);
            }else{
                $att_data['mbm2'][$thisday] = 0;
            }

            $now = $now->subDay();
        }
        $att_data['mbm'] = array_reverse($att_data['mbm']);
        $att_data['ceil'] = array_reverse($att_data['ceil']);
        $att_data['aql'] = array_reverse($att_data['aql']);
        $att_data['mbm2'] = array_reverse($att_data['mbm2']);
        $att_data['mfw'] = array_reverse($att_data['mfw']);
        
        return $att_data;
        
    }

    public function ot_data($per_id)
    {

        $data = cache_monthly_ot();

        $ot_data = [];

        $now = Carbon::now();
        // retrive last 5 month salary from cache
        for ($i=0; $i < 5 ; $i++) {
            $key = $now->format('Y-m');
            if(isset($data[$key])){
                $emp = $data[$key];
                $ot = collect($emp)->keyBy('as_id')->toArray();
                $ot = array_intersect_key($ot,array_flip($per_id));
                $collection = collect($ot);
                $format = $now->format('M');
                $ot_data[$format] = round($collection->sum('ot_hour'));
            }
        
            $now = $now->subMonth();
        }

        return array_reverse($ot_data); 

        
    }

    public function salary_data($per_id)
    {
        $data = cache_monthly_salary();
        $salary_data = [];

        $now = Carbon::now();
        // retrive last 5 month salary from cache
        for ($i=0; $i < 5 ; $i++) {
            $key = $now->format('Y-m');
            if(isset($data[$key])){
                $emp = $data[$key];
                $salary = collect($emp)->keyBy('as_id')->toArray();
                $salary = array_intersect_key($salary,array_flip($per_id));
                $collection = collect($salary);
                $salary_data['salary'][$key] = round($collection->sum('salary_payable')/100000);
                $salary_data['ot'][$key] = round($collection->sum('ot')/100000);
                $salary_data['category'][$key] = date('M',strtotime($key));
            }
        
            $now = $now->subMonth();
        }

        $salary_data['salary'] = array_reverse($salary_data['salary']);
        $salary_data['ot'] = array_reverse($salary_data['ot']);
        $salary_data['category'] = array_reverse($salary_data['category']);

        return $salary_data;
    }


    public function today_att($per_id)
    {

        $today_att = Cache::remember('today_att', 10000, function  (){
            return cache_today_att();
        });
                
        if(isset($today_att['date']) && $today_att['date'] != date('Y-m-d')){
            
            Cache::put('today_att', cache_today_att(), 10000);
            $today_att = cache('today_att');
        }

        $p = array_intersect($today_att['data']['present'], $per_id);
        $a = array_intersect($today_att['data']['absent'], $per_id);
        $l = array_intersect($today_att['data']['leave'], $per_id);
        $h = array_intersect($today_att['data']['holiday'], $per_id);


        return array(
          'present' => count($p),
          'absent'  => count($a),
          'leave'   => count($l),
          'holiday' => count($h),
          'date'    => date('Y-m-d')
        );

    }


}
