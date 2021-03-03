<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class TestXYZController extends Controller
{
    public function rfidUpdate()
    {
    	return $this->tiffinBillCheck();
    	$data = array();
    	$getBasic = DB::table('hr_as_basic_info')
    	->select('as_id', 'as_rfid_code')
    	->whereIn('as_unit_id', [3,8])
    	->whereRaw('LENGTH(as_rfid_code) < 10')
    	->get();
    	foreach ($getBasic as $emp) {
    		// $length = strlen($emp->as_rfid_code);
    		// $rfid = $emp->as_rfid_code;
	     //    if($length < 11){
	     //        $digit = 10 - $length;
	     //        $added = 0;
	     //        for ($i=0; $i < $digit; $i++) { 
	     //            $rfid = $added.$emp->as_rfid_code;
	     //        }
	     //    }
    		$rfid = str_pad($emp->as_rfid_code, 10, "0", STR_PAD_LEFT); 
	        if($rfid == '0000000000'){
	        	$rfid = null;
	        }
	        $data[] = DB::table('hr_as_basic_info')
	        ->where('as_id', $emp->as_id)
	        ->update([
	        	'as_rfid_code' => $rfid
	        ]);
    	}
    	return $data;
    }

    public function shiftUpdate()
    {
    	$data[] = DB::table('hr_as_basic_info')
    	->where('as_unit_id', 8)
    	->whereIn('as_oracle_code', [])
    	->update([
    		'as_shift_id' => 'Day'
    	]);

    	return $data;
    }

    public function checkMonthly()
    {
    	$user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-02-01')->get();
    	$leave_array = [];
        $absent_array = [];
        for($i=1; $i<=28; $i++) {
	        $date = date('Y-m-d', strtotime('2021-02-'.$i));
	        $leave = DB::table('hr_attendance_ceil AS a')
	                ->where('a.in_time', 'like', $date.'%')
	                ->leftJoin('hr_as_basic_info AS b', function($q){
	                    $q->on('b.as_id', 'a.as_id');
	                })
	                ->pluck('b.associate_id');
	        $leave_array[] = $leave;
	        $getholiday = DB::table('holiday_roaster AS a')
	        		->select('a.id','b.as_id', 'a.date', 'a.month', 'a.year')
	        		->leftJoin('hr_as_basic_info AS b', function($q){
	                    $q->on('b.associate_id', 'a.as_id');
	                })
		            ->whereIn('a.as_id', $leave)
		            ->whereDate('a.date', $date)
	                ->get();
	        if(count($getholiday) > 0){
	        	$absent_array[] = $getholiday->toArray();
	        	foreach ($getholiday as $value) {
	        		DB::table('holiday_roaster')->where('id', $value->id)->delete();
	        		$queue = (new ProcessUnitWiseSalary('hr_attendance_ceil', '02', 2021, $value->as_id, 28))
                        ->onQueue('salarygenerate')
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
	        	}
	        }

        }

        return $absent_array;
        dd('end');
     //    $data = [];
     //    foreach ($user as $key => $e) {
     //        $query[] = DB::table('holiday_roaster')
     //                                  ->where('as_id', $e->associate_id)
     //                                  ->whereDate('date','<',$e->as_doj)
     //                                  ->get()->toArray();
            
     //    }
     //    dd($query);
        // foreach ($user as $key => $e) {
        //     $query = DB::table('hr_absent')
        //       ->where('date', 'like', '2021-02%')
        //       ->where('associate_id', $e->associate_id)
        //       ->whereDate('date','<',$e->as_doj)
        //       ->pluck('id','date');
        //     if(count($query) > 0){
        //         $data[$e->associate_id] = $query;
        //     }
        // }
        // dd($data); yes
        // $leave_array = [];
        // $absent_array = [];
        // for($i=1; $i<=31; $i++) {
        // $date = date('Y-m-d', strtotime('2021-02-'.$i));
        // $leave = DB::table('hr_attendance_mbm AS a')
        //         ->where('a.in_time', 'like', $date.'%')
        //         ->leftJoin('hr_as_basic_info AS b', function($q){
        //             $q->on('b.as_id', 'a.as_id');
        //         })
        //         ->pluck('b.associate_id');
        // $leave_array[] = $leave;
        // $absent_array[] = DB::table('hr_absent')
        //         ->whereDate('date', $date)
        //         ->whereIn('associate_id', $leave)
        //         ->get()->toArray();
        // }
        // dump($leave_array,$absent_array);
        // dd('end');

        // $leave_array = [];
        // $absent_array = [];
        // for($i=1; $i<=31; $i++) {
        // $date = date('Y-m-d', strtotime('2021-02-'.$i));
        // $leave = DB::table('hr_absent AS a')
        //         ->where('a.date', '=', $date)
        //         ->whereIn('b.as_unit_id', [1, 4, 5])
        //         ->leftJoin('hr_as_basic_info AS b', function($q){
        //             $q->on('b.associate_id', 'a.associate_id');
        //         })
        //         ->pluck('b.as_id', 'b.associate_id');
        // $leave_array[] = $leave;
        // $absent_array[] = DB::table('hr_attendance_mbm')
        //         ->whereDate('in_time', $date)
        //         ->whereIn('as_id', $leave)
        //         ->get()->toArray();
        // }
        // dump($leave_array,$absent_array);
        // dd('end');
        // $leave_array = [];
        // $absent_array = [];
        // for($i=1; $i<=31; $i++) {
        // $date = date('Y-m-d', strtotime('2021-02-'.$i));
        // $leave = DB::table('hr_leave AS l')
        //         ->where('l.leave_from', '<=', $date)
        //         ->where('l.leave_to',   '>=', $date)
        //         ->where('l.leave_status', '=', 1)
        //         ->whereIn('b.as_unit_id', [1, 4, 5])
        //         ->leftJoin('hr_as_basic_info AS b', function($q){
        //             $q->on('b.associate_id', 'l.leave_ass_id');
        //         })
        //         ->pluck('b.as_id', 'b.associate_id');
        // $leave_array[] = $leave;
        // $absent_array[] = DB::table('hr_attendance_mbm')
        //         ->whereDate('in_time', $date)
        //         ->whereIn('as_id', $leave)
        //         ->get()->toArray();
        // }
        // dump($leave_array,$absent_array);
        // dd('end');

        /*$leave_array = [];
        $absent_array = [];
        for($i=1; $i<=31; $i++) {
            $date = date('Y-m-d', strtotime('2021-02-'.$i));
            $leave = DB::table('hr_leave AS l')
                    ->where('l.leave_from', '<=', $date)
                    ->where('l.leave_to',   '>=', $date)
                    ->where('l.leave_status', '=', 1)
                    ->leftJoin('hr_as_basic_info AS b', function($q){
                        $q->on('b.associate_id', 'l.leave_ass_id');
                    })
                    ->pluck('b.associate_id','b.as_id');
            $leave_array[] = $leave;
            $absent_array[] = DB::table('hr_absent')
                    ->whereDate('date', $date)
                    ->whereIn('associate_id', $leave)
                    ->get()->toArray();
        }
        // return $absent_array;
        dump($leave_array,$absent_array);
        dd('end'); yes*/ 

        // $leave_array = [];
        // $absent_array = [];
        // for($i=1; $i<=30; $i++) {
        // $date = date('Y-m-d', strtotime('2021-02-'.$i));
        // $leave = DB::table('hr_absent AS a')
        //         ->where('a.date', '=', $date)
        //         ->whereIn('b.as_unit_id', [2])
        //         ->leftJoin('hr_as_basic_info AS b', function($q){
        //             $q->on('b.associate_id', 'a.associate_id');
        //         })
        //         ->pluck('b.as_id', 'b.associate_id');
        // $leave_array[] = $leave;
        // $absent_array[] = DB::table('hr_attendance_ceil')
        //         ->whereDate('in_time', $date)
        //         ->whereIn('as_id', $leave)
        //         ->get()->toArray();
        // }
        // dump($leave_array,$absent_array);
        // dd('end');
        

        
        $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-02-01')->get();
        $data = [];
        foreach ($user as $key => $e) {
            $query[] = DB::table('holiday_roaster')
                                      ->where('as_id', $e->associate_id)
                                      ->whereDate('date','<',$e->as_doj)
                                      ->get()->toArray();
            
        }
        dd($query);
        
    }

    public function otHourCheck()
    {
    	$getBasic = DB::table('hr_as_basic_info')
    	->where('as_ot', 1)
    	->whereIn('as_unit_id', [1,4,5])
    	->where('as_status', 1)
    	->pluck('as_id');
    	$getat = [];
    	for($i=1; $i<=28; $i++) {
	    	$getData = DB::table('hr_attendance_mbm AS m')
	    	->select('m.*', 'b.hr_shift_end_time', 'b.hr_shift_break_time')
	    	->where('m.in_date', '2021-02-'.$i)
	    	->whereIn('m.as_id', $getBasic)
	    	->leftJoin('hr_shift AS b', function($q){
	            $q->on('b.hr_shift_code', 'm.hr_shift_code');
	        })
	        ->whereNotNull('m.out_time')
	        ->whereNotNull('m.in_time')
	        ->get();
	        // dd($getData);
	        
	        foreach ($getData as $data) {
	        	$punchOut = $data->out_time;
	        	$shiftOuttime = date('Y-m-d', strtotime($punchOut)).' '.$data->hr_shift_end_time;
	        	$otDiff = ((strtotime($punchOut) - (strtotime($shiftOuttime) + (($data->hr_shift_break_time + 10) * 60))))/3600;
	        	if($otDiff > 0 && $data->ot_hour <= 0){
	        		$getat[$data->as_id] = $data;
	        	}
	        }
	    }
        dd($getat);
        
    }

    public function earlyarPunchCheck()
    {
        $getBasic = DB::table('hr_as_basic_info')
        ->where('as_ot', 1)
        ->whereIn('as_unit_id', [2])
        ->where('as_status', 1)
        ->pluck('as_id');
        $getat = [];
        for($i=1; $i<=28; $i++) {
            $getData = DB::table('hr_attendance_ceil AS m')
            ->select('m.*', 'b.hr_shift_start_time', 'b.hr_shift_break_time')
            ->where('m.in_date', '2021-02-'.$i)
            ->whereIn('m.as_id', $getBasic)
            ->leftJoin('hr_shift AS b', function($q){
                $q->on('b.hr_shift_code', 'm.hr_shift_code');
            })
            // ->whereNotNull('m.out_time')
            // ->whereNotNull('m.in_time')
            ->get();
            // dd($getData);
            
            foreach ($getData as $data) {
                $punchIn = $data->in_time;
                $shiftIntime = date('Y-m-d', strtotime($punchIn)).' '.$data->hr_shift_start_time;
                $earlyTime = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($shiftIntime)));
                
                if(strtotime($punchIn) < strtotime($earlyTime)){
                    $getat[$data->as_id.' - '.$data->in_date] = $data;
                }
            }
        }
        return ($getat);
        
    }

    public function monthlyLeftCheck()
    {
    	$data = DB::table('hr_monthly_salary')
    	->where('month', '01')
    	->where('year', '2021')
    	->where('emp_status', 2)
    	->get();

    	$current = DB::table('hr_monthly_salary')
    	->select('as_id')
    	->where('month', '02')
    	->where('year', '2021')
    	->where('emp_status', 1)
    	->get()
    	->keyBy('as_id')
    	->toArray();

    	$ge = array();
    	foreach ($data as $value) {
    		if(isset($current[$value->as_id])){
    			$ge[] = $value->as_id;
    		}
    	}
    	dd($ge);
    }

    public function tiffinBillCheck()
    {
        $date = '2021-01-';
        $data = [];
        for ($i=1; $i <= 31; $i++) { 
            $getBill = DB::table('hr_bill')
            ->where('bill_date', date('Y-m-d', strtotime($date.$i)))
            ->get()
            ->toArray();
            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"))
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            ->get()
            ->keyBy('asdate')
            ->toArray();
            
            foreach ($getBill as $value) {
                if(!isset($getatt[$value->bill_date.$value->as_id])){
                    $data[] = DB::table('hr_bill')->where('id', $value->id)->delete();
                }
            }
        }
        return $data;
    }
}
