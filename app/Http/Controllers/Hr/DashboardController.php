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

        $att_aql =  Cache::remember('att_aql', 300, function () {
            return AttAQL::selectRaw('count(*) as present,in_date')
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
    		$att_data['aql'][$thisday] = $att_aql[$thisday]??0;
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
    	$data =  Cache::remember('monthly_salary', 300, function () {
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

    	return array_reverse($salary_data); 
    }


    public function today_att()
    {
        $unit = auth()->user()->employee['as_unit_id'];
        $today_att = Cache::remember('today_att'.$unit, 300, function  () use ($unit) {
            return $this->attData($unit);
        });

        return $today_att;
    }


    private function attData($unit)
    {
        $today = date("2019-12-31");
        $table = get_att_table($unit);

        $present = 0;
        $late = 0;
        $leave   = 0;
        $totalUser    = 0;
        $absent  = 0;

    
        $present  = DB::table($table.' AS a')
                        ->select(
                                DB::raw("DISTINCT(a.as_id)"),
                                "a.hr_shift_code"
                              )
                        ->whereDate('a.in_time', $today)
                        ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                        ->where('b.as_unit_id', $unit)
                        ->get()
                        ->count();

        $late  = DB::table($table.' AS a')
                        ->select('late_status')
                        ->whereDate('a.in_time', $today)
                        ->where('a.late_status', 1)
                        ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                        ->where('b.as_unit_id', $unit)
                        ->get()
                        ->count();
      
        /*----------------Leave------------------*/
        $leave = DB::table('hr_leave AS l')
                 ->where('l.leave_from', '<=', $today)
                 ->where('l.leave_to',   '>=', $today)
                 ->where('l.leave_status', '=', 1)
                 ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'l.leave_ass_id')
                 ->where('b.as_unit_id', $unit)
                 ->count();

        $query1 = DB::table('hr_as_basic_info AS b')
                  ->where('as_status', 1);
        $query1->where('hdr.date','LIKE',$today);
        $query1->where('hdr.remarks', 'Holiday');
        $query1->Join('holiday_roaster AS hdr', 'hdr.as_id', 'b.associate_id');

        $holiday = $query1->get()->count();

        $employee = Employee::where("as_status", 1)->where('as_unit_id', $unit)->count();

        $absent = DB::table('hr_absent')
                   ->whereBetween('date',array($today,$today))
                   ->where('hr_unit', $unit)
                   ->get()
                   ->count();

        $unit_info= Unit::where('hr_unit_id', $unit)->first();

        $today_att = [
          'employee'=> $employee,
          'present' => $present,
          'late'    => $late,
          'absent'  => $absent,
          'leave'   => $leave,
          'holiday' => $holiday,
          'unit'    => $unit_info->hr_unit_name??'',
          'unit_id' => $unit
        ];

        return $today_att;
    }




}
