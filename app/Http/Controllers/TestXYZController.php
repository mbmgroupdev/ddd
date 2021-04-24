<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceOuttime;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\Benefits;
use App\Models\Hr\Bills;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;


class TestXYZController extends Controller
{
    public function rfidUpdate()
    {
    	return $this->shiftAssigned();
        return "";
    	$data = array();
    	$getBasic = DB::table('hr_as_basic_info')
    	->select('as_id', 'as_rfid_code', 'as_oracle_code', 'as_unit_id')
    	->whereIn('as_unit_id', [3,8])
        // 	->where('as_rfid_code', 'LIKE', '#%')
    	->whereRaw('LENGTH(as_rfid_code) < 10')
    	->get();
        // 	->pluck('as_oracle_code');
    	return ($getBasic);
    	foreach ($getBasic as $emp) {
    	    $rfid = ltrim($emp->as_rfid_code,'#');
        // 		$rfid = str_pad($emp->as_rfid_code, 10, "0", STR_PAD_LEFT); 
	   //     if($rfid == '0000000000'){
	   //     	$rfid = null;
	   //     }
	        $check = DB::table('hr_as_basic_info')->where('as_rfid_code', $rfid)->first();
	        if($check == null){
	            $data[$emp->as_id] = DB::table('hr_as_basic_info')
    	        ->where('as_id', $emp->as_id)
    	        ->update([
    	        	'as_rfid_code' => $rfid
    	        ]);
	        }
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
    public function monthlyCheck(){
        
        
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-03-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('holiday_roaster')
        //           ->where('as_id', $e->associate_id)
        //           ->whereDate('date','<',$e->as_doj)
        //           ->get()->toArray();
            
        // }
        // dd($query);
        $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-03-01')->get();
            $data = [];
        foreach ($user as $key => $e) {
            $query = DB::table('hr_absent')
                                      ->where('date', 'like', '2021-03%')
                                      ->where('associate_id', $e->associate_id)
                                      ->where('date','<',$e->as_doj)
                                      ->pluck('id','date');
            if(count($query) > 0){
                $data[$e->associate_id] = $query;
            }
        }
        dd($data);
        // $leave_array = [];
        //         $absent_array = [];
        //         for($i=1; $i<=31; $i++) {
        //         $date = date('Y-m-d', strtotime('2021-03-'.$i));
        //         $leave = DB::table('hr_attendance_mbm AS a')
        //                 ->where('a.in_time', 'like', $date.'%')
        //                 // ->where('a.as_id', 8958)
        //                 ->leftJoin('hr_as_basic_info AS b', function($q){
        //                     $q->on('b.as_id', 'a.as_id');
        //                 })
        //                 ->pluck('b.associate_id');
        //         $leave_array[] = $leave;
        //         $absent_array[] = DB::table('hr_absent')
        //                 ->whereDate('date', $date)
        //                 ->whereIn('associate_id', $leave)
        //                 ->get()->toArray();
        //         }
        //         dump($leave_array,$absent_array);
        //         dd('end');

        //         $leave_array = [];
        //         $absent_array = [];
        //         for($i=1; $i<=31; $i++) {
        //         $date = date('Y-m-d', strtotime('2021-03-'.$i));
        //         $leave = DB::table('hr_attendance_ceil AS a')
        //                 ->whereIn('a.in_date',  ['2021-03-05','2021-03-12','2021-03-19','2021-03-26'])
        //                 ->whereIn('b.as_unit_id', [2])
        //                 ->where('b.shift_roaster_status', 1)
        //                 ->leftJoin('hr_as_basic_info AS b', function($q){
        //                     $q->on('a.in_date', 'a.as_id');
        //                 })
        //                 ->pluck('b.as_id', 'b.associate_id');
        //         $leave_array[] = $leave;
                
                
        //         }
        //         dump($leave_array);
        //         dd('end');

                // $leave_array = [];
                // $absent_array = [];
                // for($i=1; $i<=31; $i++) {
                // $date = date('Y-m-d', strtotime('2020-11-'.$i));
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
            // for($i=20; $i<=31; $i++) {
            // $date = date('Y-m-d', strtotime('2021-03-'.$i));
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
            // // return "done";
            // dump($leave_array,$absent_array);
            // dd('end');

            // $leave_array = [];
            // $absent_array = [];
            // for($i=1; $i<=31; $i++) {
            // $date = date('Y-m-d', strtotime('2021-03-'.$i));
            // $leave = DB::table('hr_leave AS l')
            //         ->where('l.leave_from', '<=', $date)
            //         ->where('l.leave_to',   '>=', $date)
            //         ->where('l.leave_status', '=', 1)
            //         ->leftJoin('hr_as_basic_info AS b', function($q){
            //             $q->on('b.associate_id', 'l.leave_ass_id');
            //         })
            //         ->pluck('b.associate_id','b.as_id');
            // $leave_array[] = $leave;
            // $absent_array[] = DB::table('hr_absent')
            //         ->whereDate('date', $date)
            //         ->whereIn('associate_id', $leave)
            //         ->get()->toArray();
            // }
            // dump($leave_array,$absent_array);
            // dd('end');

        
        $leave_array = [];
        $absent_array = [];
        for($i=1; $i<=31; $i++) {
            $date = date('Y-m-d', strtotime('2021-03-'.$i));
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
        return $absent_array;
        // return ($absent_array);
        // exit;
    }
    public function otHourCheck()
    {
        $section = section_by_id();
        $department = department_by_id();
        /*$getData = DB::table('hr_attendance_mbm AS m')
            ->select('ba.associate_id', 'ba.as_oracle_code', 'ba.as_department_id', 'ba.as_section_id', 'ba.as_name','m.*', 'b.hr_shift_end_time', 'b.hr_shift_break_time')
            ->whereIn('m.in_date', ['2021-03-12', '2021-03-19'])
            // ->whereIn('m.as_id', $getBasic)
            ->leftJoin('hr_shift AS b', function($q){
                $q->on('b.hr_shift_code', 'm.hr_shift_code');
            })
            ->leftJoin('hr_as_basic_info AS ba', function($q){
                $q->on('ba.as_id', 'm.as_id');
            })
            ->whereNotNull('m.out_time')
            ->whereNotNull('m.in_time')
            ->where('m.ot_hour', 0)
            ->where('ba.as_ot', 1)
            ->get();
        $d = [];
        foreach ($getData as $att) {
            $d[] = array(
                'Oracle Id' => $att->as_oracle_code,
                'Associate Id' => $att->associate_id,
                'Name' => $att->as_name,
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Section' => $section[$att->as_section_id]['hr_section_name']??'',
                'Date' =>  date('m/d/Y', strtotime($att->in_date)),
                'In Time' => date('H:i:s', strtotime($att->in_time)),
                'Out Time' => date('H:i:s', strtotime($att->out_time)),
            );
        }
        return (new FastExcel(collect($d)))->download('Ot missing(12 19).xlsx');
        dd($getData);*/
    	$getBasic = DB::table('hr_as_basic_info')
    	->where('as_ot', 1)
    	->whereIn('as_unit_id', [1])
    	->where('as_status', 1)
    	->pluck('as_id');
    	$getat = [];
    	for($i=1; $i<=31; $i++) {
	    	$getData = DB::table('hr_attendance_mbm AS m')
	    	->select('ba.associate_id', 'ba.as_oracle_code', 'ba.as_department_id', 'ba.as_section_id', 'ba.as_name','m.*', 'b.hr_shift_end_time', 'b.hr_shift_break_time')
	    	->where('m.in_date', '2021-03-'.$i)
	    	->whereIn('m.as_id', $getBasic)
	    	->leftJoin('hr_shift AS b', function($q){
	            $q->on('b.hr_shift_code', 'm.hr_shift_code');
	        })
            ->leftJoin('hr_as_basic_info AS ba', function($q){
                $q->on('ba.as_id', 'm.as_id');
            })
	        ->whereNotNull('m.out_time')
	        ->whereNotNull('m.in_time')
            // ->where('m.ot_hour', 0)
	        ->get();

	        
	        foreach ($getData as $data) {
	        	$punchOut = $data->out_time;
	        	$shiftOuttime = date('Y-m-d', strtotime($punchOut)).' '.$data->hr_shift_end_time;
	        	$otDiff = ((strtotime($punchOut) - (strtotime($shiftOuttime) + (($data->hr_shift_break_time + 10) * 60))))/3600;
	        	if($otDiff > 0 && $data->ot_hour <= 0){
	        		$getat[$data->as_id.' '.$data->in_date] = $data;
	        	}
	        }
	    }

        foreach ($getat as $att) {
            $d[] = array(
                'Oracle Id' => $att->as_oracle_code,
                'Associate Id' => $att->associate_id,
                'Name' => $att->as_name,
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Section' => $section[$att->as_section_id]['hr_section_name']??'',
                'Date' =>  date('m/d/Y', strtotime($att->in_date)),
                'In Time' => date('H:i:s', strtotime($att->in_time)),
                'Out Time' => date('H:i:s', strtotime($att->out_time)),
            );
        }
        return (new FastExcel(collect($d)))->download('Ot missing.xlsx');
        return ($getat);
        
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
    	return ($ge);
    }
    public function tiffinBillEntry()
    {
        $data = [];
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_id', 'm.in_date')
        ->join('hr_attendance_mbm AS m', 'b.as_id', 'm.as_id')
        ->where('m.in_date', '2021-03-12')
        ->whereIn('b.as_unit_id', [1,4,5])
        ->whereIn('b.as_id', $data)
        ->get();
        // dd($getEmployee);
        $getBill = DB::table('hr_bill')
            ->where('bill_date', '2021-03-12')
            ->get()
            ->keyBy('as_id');
        DB::beginTransaction();
        try {
            foreach ($getEmployee as $key => $value) {
                Bills::updateOrCreate([
                    'as_id' => $value->as_id,
                    'bill_date' => '2021-03-12'
                ],
                [
                    'bill_type' => ((isset($getBill[$value->as_id]) && $getBill[$value->as_id]->amount == 70)?2:1),
                    'amount' => 70,
                    'pay_status' => 0
                ]);
            }

            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

    }
    
    public function tiffinBillCheck()
    {
        $date = '2021-02-';
        $data = [];
        for ($i=1; $i <= 31; $i++) { 
            $getBill = DB::table('hr_bill')
            ->where('bill_date', date('Y-m-d', strtotime($date.$i)))
            ->get()
            ->toArray();
            // $getatt = DB::table('hr_attendance_mbm')
            // ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"))
            // ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            // ->get()
            // ->keyBy('asdate')
            // ->toArray();
            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"), 'in_date', 'in_time', 'out_time')
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            ->where('hr_shift_code', 'HH3')
            ->get()
            ->keyBy('asdate')
            ->toArray();
            
            foreach ($getBill as $value) {
                if(isset($getatt[$value->bill_date.$value->as_id])){
                    // $data[] = $value;
                    $data[] = DB::table('hr_bill')->where('id', $value->id)->delete();
                }
            }
        }
        return ($data);
    }
    public function billRemove()
    {
        $getBill = DB::table("hr_bill AS t")
        ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.as_id', 't.as_id');
        })
        // ->whereIn('b.as_location', [12,13])
        // ->whereIn('b.as_subsection_id', [185,108])
        // ->whereIn('b.as_designation_id', [408,397,218,229,204,211,356,230,470,407,221,293,375,449,196,454,402,463])
        ->whereIn('b.as_department_id', [53,56])
        ->get();
        return $getBill;

    }
    public function employeeCheck()
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'ben.hr_bn_associate_name', 'b.as_status', 'b.as_name')
        ->leftJoin('hr_employee_bengali AS ben', 'b.associate_id', 'ben.hr_bn_associate_id')
        ->where('b.as_unit_id', 2)
        ->whereNull('ben.hr_bn_associate_name')
        ->whereIn('b.as_status', [1,6,2,5])
        ->get();
        dd($getEmployee);
    }
    
    public function incrementHistory1()
    {
        $getData = [];
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('as_oracle_code', 'associate_id', 'as_status', 'as_doj', 'as_name')
        ->whereIn('b.as_unit_id', [3])
        ->whereIn('b.as_location', [9])
        ->get();

        // $getIncrement = DB::table('hr_increment')
        // ->get()
        // ->keyBy('associate_id')
        // ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getData as $key1 => $value) {
                if($info->as_oracle_code == $value['PID']){
                    $getIncrement = DB::table('hr_increment')->where('associate_id', $info->associate_id)->where('effective_date', date('Y-m-d', strtotime($value['L_INCR_DT'])))->first();
                    // ++$count;
                    if($getIncrement != null){
                            // $macth[$info->associate_id] = $value;
                            ++$count;
                        
                        $macth[] = DB::table('hr_increment')
                        ->where('id', $getIncrement->id)
                        ->update([
                            'associate_id' => $info->associate_id,
                            'current_salary' => ($value['CURRENT_SALARY'] - $value['L_INCR_AMT']),
                            'increment_type' => 2,
                            'increment_amount' => $value['L_INCR_AMT'],
                            'amount_type' => 1,
                            'applied_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'eligible_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'effective_date' => date('Y-m-d', strtotime($value['L_INCR_DT'])),
                            'status' => 1,
                        ]);

                    }
                }
            }
        }

        // return $count;
        return count($macth);
    }
    public function benefitUpdate1()
    {
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'b.as_status', 'b.as_doj', 'b.as_name', 'b.as_unit_id', 'a.ben_current_salary')
        // ->whereIn('b.as_unit_id', [8])
        ->leftJoin('hr_benefits AS a', function($q){
            $q->on('a.ben_as_id', 'b.associate_id');
        })
        ->where('as_status', '!=', 0)
        ->get();
        // return $getBasic;
        $getIncrement = DB::table('hr_increment')
        ->get()
        ->keyBy('associate_id')
        ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getIncrement as $key => $value) {
                if($info->associate_id == $value->associate_id && (($value->current_salary+$value->increment_amount) > $info->ben_current_salary)){

                    $value->ben_current_salary = $info->ben_current_salary;
                    $value->as_unit_id = $info->as_unit_id;
                    $macth[] = $value;

                }
            }
        }

        $tomacth = [];
        return $macth;
        foreach ($macth as $key1 => $val) {
            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.associate_id', $val->associate_id)
                            ->first();
            if($ben != null){
                $up['ben_current_salary'] = ($val->current_salary + $val->increment_amount);
                $up['ben_basic'] = ceil(($up['ben_current_salary']-1850)/1.5);
                $up['ben_house_rent'] = $up['ben_current_salary'] -1850 - $up['ben_basic'];

                if($ben->ben_bank_amount > 0){
                    $up['ben_bank_amount'] = $up['ben_current_salary'];
                    $up['ben_cash_amount'] = 0;
                }else{
                    $up['ben_cash_amount'] = $up['ben_current_salary'];
                    $up['ben_bank_amount'] = 0;
                }
                $tomacth[] = $up;
                //$exist[$key1] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);
            }
        }
        return ($exist);
    }
    
    public function incrementMarge1()
    {
        $getIncrement = DB::table('hr_increment')
        ->select('associate_id', 'increment_type', 'applied_date', 'eligible_date', DB::raw('COUNT(*) AS count'))
        ->groupBy(['associate_id', 'increment_type', 'applied_date', 'eligible_date'])
        ->having('count', '>', 1)
        ->get();
        $increment = [];
        foreach ($getIncrement as $key => $value) {
            $increment[] = DB::table('hr_increment')
            ->select('associate_id', 'applied_date', DB::raw('sum(increment_amount) as amount'), DB::raw('MAX(id) AS maxid'), DB::raw('MIN(id) AS minid'))
            ->where('associate_id', $value->associate_id)
            ->where('applied_date', $value->applied_date)
            ->groupBy('associate_id')
            ->first();
        }

        foreach ($increment as $key1 => $va) {
            DB::table("hr_increment")
            ->where('associate_id', $va->associate_id)
            ->where('id', $va->maxid)
            ->update([
                'increment_amount' => $va->amount
            ]);

            DB::table('hr_increment')
            ->where('id', $va->minid)
            ->delete();
        }
        return 'success';
    }


    public function incrementHistory()
    {
        $getData = [];
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('as_oracle_code', 'associate_id', 'as_status', 'as_doj', 'as_name')
        ->whereIn('b.as_unit_id', [3])
        ->whereIn('b.as_location', [9])
        ->get();

        // $getIncrement = DB::table('hr_increment')
        // ->get()
        // ->keyBy('associate_id')
        // ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getData as $key1 => $value) {
                if($info->as_oracle_code == $value['PID']){
                    $getIncrement = DB::table('hr_increment')->where('associate_id', $info->associate_id)->where('effective_date', date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])))->where('increment_amount', $value['LAST_INCRIMENT_AMOUNT'])->first();

                    if($getIncrement == null){
                            // $macth[$info->associate_id] = $value;
                            ++$count;
                        
                        $macth[] = DB::table('hr_increment')
                        ->insertGetId([
                            'associate_id' => $info->associate_id,
                            'current_salary' => ($value['CURRENT_SALARY'] - $value['LAST_INCRIMENT_AMOUNT']),
                            'increment_type' => 2,
                            'increment_amount' => $value['LAST_INCRIMENT_AMOUNT'],
                            'amount_type' => 1,
                            'applied_date' => date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])),
                            'eligible_date' => date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])),
                            'effective_date' => date('Y-m-d', strtotime($value['LAST_INCRIMENT_DATE'])),
                            'status' => 1,
                        ]);

                    }
                }
            }
        }

        // return $count;
        return count($macth);
    }

    public function benefitUpdate()
    {
        $getBasic = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'b.as_status', 'b.as_doj', 'b.as_name', 'b.as_unit_id', 'a.ben_current_salary')
        // ->whereIn('b.as_unit_id', [8])
        ->leftJoin('hr_benefits AS a', function($q){
            $q->on('a.ben_as_id', 'b.associate_id');
        })
        ->where('as_status', '!=', 0)
        ->get();
        // return $getBasic;
        $getIncrement = DB::table('hr_increment')
        ->get()
        ->keyBy('associate_id')
        ->toArray();

        $count = 0;
        $macth = [];
        foreach ($getBasic as $key => $info) {
            foreach ($getIncrement as $key => $value) {
                if($info->associate_id == $value->associate_id && (($value->current_salary+$value->increment_amount) > $info->ben_current_salary) && in_array($info->as_unit_id, [3,8])){

                    $value->ben_current_salary = $info->ben_current_salary;
                    $value->as_unit_id = $info->as_unit_id;
                    $macth[] = $value;

                }
            }
        }

        $tomacth = [];
        return $macth;
        foreach ($macth as $key1 => $val) {
            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.associate_id', $val->associate_id)
                            ->first();
            if($ben != null){
                $up['ben_current_salary'] = ($val->current_salary + $val->increment_amount);
                $up['ben_basic'] = ceil(($up['ben_current_salary']-1850)/1.5);
                $up['ben_house_rent'] = $up['ben_current_salary'] -1850 - $up['ben_basic'];

                if($ben->ben_bank_amount > 0){
                    $up['ben_bank_amount'] = $up['ben_current_salary'];
                    $up['ben_cash_amount'] = 0;
                }else{
                    $up['ben_cash_amount'] = $up['ben_current_salary'];
                    $up['ben_bank_amount'] = 0;
                }
                $tomacth[] = $up;
                $exist[$key1] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);
            }
        }
        return ($exist);
    }

    public function incrementMarge()
    {
        $getIncrement = DB::table('hr_increment')
        ->select('associate_id', 'increment_type', 'applied_date', 'eligible_date', DB::raw('COUNT(*) AS count'))
        ->groupBy(['associate_id', 'increment_type', 'applied_date', 'eligible_date'])
        ->having('count', '>', 1)
        ->get();
        $increment = [];
        foreach ($getIncrement as $key => $value) {
            $increment[] = DB::table('hr_increment')
            ->select('associate_id', 'applied_date', DB::raw('sum(increment_amount) as amount'), DB::raw('MAX(id) AS maxid'), DB::raw('MIN(id) AS minid'))
            ->where('associate_id', $value->associate_id)
            ->where('applied_date', $value->applied_date)
            ->groupBy('associate_id')
            ->first();
        }

        foreach ($increment as $key1 => $va) {
            DB::table("hr_increment")
            ->where('associate_id', $va->associate_id)
            ->where('id', $va->maxid)
            ->update([
                'increment_amount' => $va->amount
            ]);

            DB::table('hr_increment')
            ->where('id', $va->minid)
            ->delete();
        }
        return 'success';
    }

    public function getAttCheck()
    {
       
        $getData = [];

        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_id', $getData)
        ->select('as_name', 'as_oracle_code', 'as_id')
        ->get()
        ->keyBy('as_id');

        $getAtt = DB::table('hr_attendance_ceil AS b')
        ->select('b.*', DB::raw('(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'))
        ->leftJoin('hr_shift AS s', 'b.hr_shift_code', 's.hr_shift_code')
        ->whereIn('b.as_id', $getData)
        ->where('b.in_date', '>=', '2021-03-20')
        ->where('b.in_date', '<=', '2021-03-25')
        ->orderBy('b.in_date', 'asc')
        ->get();

        $d = [];
        foreach ($getAtt as $att) {
            $d[] = array(
                'Oracle Id' => $getEmployee[$att->as_id]->as_oracle_code,
                'Name' => $getEmployee[$att->as_id]->as_name,
                'Date' =>  date('m/d/Y', strtotime($att->in_date)),
                'In Time' => date('H:i:s', strtotime($att->in_time)),
                'Out Time' => date('H:i:s', strtotime($att->out_time)),
                'Working Hour' => round($att->hourDuration / 60, 2)
            );
        }
        return (new FastExcel(collect($d)))->download('Attendance History.xlsx');
        return $getAtt;
    }

    public function accountInfoUpdate()
    {
        $data = [];
        
        $exist = []; $not = [];
        foreach ($data as $key => $val) {

            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.as_oracle_code', $key)
                            ->first();
            
            // $up['rock']                
            $exist[$key] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);

            $tableName = get_att_table($ben->as_unit_id);

            if($ben->as_status == 1){

                $queue = (new ProcessUnitWiseSalary($tableName, '03', '2021', $ben->as_id, '31'))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }else{
                $not[]=$ben->associate_id;
            }

        }
        return $not;

        // $data = [];
        
        // $exist = []; $not = [];
        // foreach ($data as $key => $val) {

        //     $ben = DB::table('hr_benefits as b')
        //                     ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
        //                     ->where('a.as_oracle_code', $key)
        //                     ->first();
        //     $up['ben_current_salary'] = $val['New Gross'];
        //     $up['ben_basic'] = ceil(($val['New Gross']-1850)/1.5);
        //     $up['ben_house_rent'] = $val['New Gross'] -1850 - $up['ben_basic'];

        //     if($ben->ben_bank_amount > 0){
        //         $up['ben_bank_amount'] = $val['New Gross'];
        //         $up['ben_cash_amount'] = 0;
        //     }else{
        //         $up['ben_cash_amount'] = $val['New Gross'];
        //         $up['ben_bank_amount'] = 0;
        //     }

        //     $exist[$key] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);

        //     $tableName = get_att_table($ben->as_unit_id);

        //     if($ben->as_status == 1){

        //         $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $ben->as_id, date('d')))
        //                     ->onQueue('salarygenerate')
        //                     ->delay(Carbon::now()->addSeconds(2));
        //                     dispatch($queue);
        //     }else{
        //         $not[]=$ben->associate_id;
        //     }

        // }
        // return $not;
    }

    public function holidayAttCheck()
    {
        // $getEmployee = DB::table('hr_as_basic_info')
        // ->whereIn('as_unit_id', [1])
        // ->where('as_status', 1)
        // ->where('shift_roaster_status', 0)
        // ->select('associate_id', 'as_id', 'as_unit_id')
        // ->get();

        // $data = [];
        // $roasterData = YearlyHolyDay::
        // whereIn('hr_yhp_unit', [1])
        // ->where('hr_yhp_dates_of_holidays','>=', '2021-03-01')
        // ->where('hr_yhp_dates_of_holidays','<=', '2021-03-31')
        // ->where('hr_yhp_open_status', 0)
        // ->get();
        //     foreach ($getEmployee as $key => $va) {

        //         if(count($roasterData) > 0){
        //             foreach ($roasterData as $key => $value) {
        //                 // return $va->as_id;
        //                 $dat = DB::table('hr_attendance_mbm')
        //                     ->where('in_date', $value->hr_yhp_dates_of_holidays)
        //                     ->where('as_id', $va->as_id)
        //                     ->first();
                            
        //                 if($dat != null){
        //                     $data[$va->associate_id] = $dat;
        //                 }
        //             }
                    
        //         }
                
        //     }
        // // }
        //     $fj = [];
        //     foreach ($data as $key => $value) {
        //         $fd = HolidayRoaster::select('date','remarks')
        //         ->where('as_id', $key)
        //         ->where('date','>=', $value->in_date)
        //         ->first();
        //         if($fd == null){
        //             $fj[] = $value;
        //         }
        //     }
        // return $fj;

        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [8])
        ->where('as_status', 1)
        ->where('shift_roaster_status', 1)
        ->select('associate_id', 'as_id', 'as_unit_id')
        ->get();
        $employeeKey = collect($getEmployee)->pluck('associate_id');
        $HolidayRoaster = HolidayRoaster::
        where('year', 2021)
        ->where('month', '03')
        ->whereIn('as_id', $employeeKey)
        ->get()
        ->groupBy('as_id', true);
        // return $employeeKey;
        $data = [];
        // return $HolidayRoaster;
        foreach ($getEmployee as $key => $va) {
            foreach ($HolidayRoaster[$va->associate_id] as $key => $value) {
                // return $va->as_id;
                $dat = DB::table('hr_attendance_cew')
                    ->where('in_date', $value->date)
                    ->where('as_id', $va->as_id)
                    ->first();
                    
                if($dat != null){
                    $data[$va->associate_id] = $dat;
                }
            }
        }
        // }
        return $data;
            $fj = [];
            foreach ($data as $key => $value) {
                $fd = HolidayRoaster::select('date','remarks')
                ->where('as_id', $key)
                ->where('date','>=', $value->in_date)
                ->first();
                if($fd == null){
                    $fj[] = $value;
                }
            }
        return $fj;
        $roasterData = HolidayRoaster::select('date','remarks')
                ->where('year', $year)
                ->where('month', $month)
                ->where('as_id', $getEmployee->associate_id)
                ->where('date','>=', $firstDateMonth)
                ->where('date','<=', $lastDateMonth)
                ->get();

        $rosterOtData = collect($roasterData)
            ->where('remarks', 'OT')
            ->pluck('date');

        $otDayCount = 0;
        $totalOt = count($rosterOtData);
        // return $rosterOTCount;
        $otDayCount = DB::table($this->tableName)
            ->where('as_id', $getEmployee->as_id)
            ->whereIn('in_date', $rosterOtData)
            ->count();


        if($getEmployee->shift_roaster_status == 1){
            // check holiday roaster employee
            $getHoliday = collect($roasterData)
                ->where('remarks', 'Holiday')
                ->count();
            $getHoliday = $getHoliday + ($totalOt - $otDayCount);
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = collect($roasterData)
                ->where('remarks', 'Holiday')
                ->count();
            // check General roaster employee
            $RosterGeneralCount = collect($roasterData)
                ->where('remarks', 'General')
                ->count();
            
            // check holiday shift employee
            $query = YearlyHolyDay::
                where('hr_yhp_unit', $getEmployee->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                ->where('hr_yhp_open_status', 0);
                if($empdojMonth == $yearMonth){
                    $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                }

                if(count($rosterOtData) > 0){
                    $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
            $shiftHolidayCount = $query->count();
            // OT check 
            $queryOt = YearlyHolyDay::
                where('hr_yhp_unit', $getEmployee->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                ->where('hr_yhp_open_status', 2);
                if($empdojMonth == $yearMonth){
                    $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                }
                
                if(count($rosterOtData) > 0){
                    $queryOt->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
            $getShiftOt = $queryOt->get();
            $shiftOtCount = $getShiftOt->count();
            $shiftOtDayCout = 0;

            foreach ($getShiftOt as $shiftOt) {
                $checkAtt = DB::table($this->tableName)
                ->where('as_id', $getEmployee->as_id)
                ->where('in_date', $shiftOt->hr_yhp_dates_of_holidays)
                ->first();
                if($checkAtt != null){
                    $shiftOtDayCout += 1;
                }
            }
            
            $shiftHolidayCount = $shiftHolidayCount + ($totalOt - $otDayCount) + ($shiftOtCount - $shiftOtDayCout);

            if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
            }else{
                $getHoliday = $shiftHolidayCount;
            }
        }

        $getHoliday = $getHoliday < 0 ? 0:$getHoliday;
    }
    public function extraCheck($value='')
    {
        $section = section_by_id();
        $department = department_by_id();
        // $check = DB::table('hr_attendance_mbm AS a')
        // ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->whereIn('a.in_date', ['2021-03-29', '2021-03-30'])
        // // ->where('a.as_id', 8958)
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 'a.as_id');
        // })
        // ->groupBy('a.as_id')
        // ->get();

        // $check = DB::table('hr_attendance_mbm AS a')
        // ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->where('a.hr_shift_code', 'N')
        // ->whereIn('a.in_date', ['2021-03-28'])
        // // ->where('b.as_department_id', 67)
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 'a.as_id');
        // })
        // ->groupBy('a.as_id')
        // ->pluck('a.as_id');
        // $acheck = DB::table('hr_attendance_mbm AS a')
        // ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->whereIn('a.in_date', ['2021-03-29'])
        // ->whereIn('a.as_id',$check)
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.as_id', 'a.as_id');
        // })
        // ->groupBy('a.as_id')
        // ->get();
        $check = DB::table('holiday_roaster AS a')
        ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // ->select('as_id', DB::raw('COUNT(*) AS count'))
        ->whereIn('a.date', ['2021-03-29', '2021-03-30'])
        ->where('a.comment', 'Shab-e-Barat')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.associate_id', 'a.as_id');
        })
        ->groupBy('as_id')
        ->get();

        $d = [];
        foreach ($check as $att) {
            if($att->count == 2){
                $d[] = array(
                    'Associate Id' => $att->associate_id,
                    'Name' => $att->as_name,
                    'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$att->as_section_id]['hr_section_name']??''
                );
            }
        }

        return (new FastExcel(collect($d)))->download('two days att.xlsx');
        return ($check);
    }

    public function salaryCheck()
    {
        $getEmployee = Employee::where('as_id', 1574)->first();
        $year = 2021;
        $month = '03';
        $yearMonth = $year.'-'.$month;
        $monthDayCount  = Carbon::parse($yearMonth)->daysInMonth;
        $partial = 0;
        $totalDay = $monthDayCount;
        $tableName = get_att_table($getEmployee->as_unit_id);
        $ttotalDay = 31;
        try {
            if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $yearMonth){
                // check lock month
                $checkL['month'] = $month;
                $checkL['year'] = $year;
                $checkL['unit_id'] = $getEmployee->as_unit_id;
                $checkLock = monthly_activity_close($checkL);
                if($checkLock == 1){
                    return 'error';
                }
                //  get benefit employee associate id wise
                $getBenefit = Benefits::
                where('ben_as_id', $getEmployee->associate_id)
                ->first();

                $empdoj = $getEmployee->as_doj;
                $empdojMonth = date('Y-m', strtotime($getEmployee->as_doj));
                $empdojDay = date('d', strtotime($getEmployee->as_doj));

                $totalDay = $ttotalDay;
                $today = $yearMonth.'-01';
                $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
                if($empdojMonth == $yearMonth){
                    $totalDay = $ttotalDay - ((int) $empdojDay-1);
                    $firstDateMonth = $getEmployee->as_doj;
                }

                if($getBenefit != null){
                    
                    if($getEmployee->as_status_date != null){
                        $sDate = $getEmployee->as_status_date;
                        $sYearMonth = Carbon::parse($sDate)->format('Y-m');
                        $sDay = Carbon::parse($sDate)->format('d');


                        if($yearMonth == $sYearMonth){
                            $firstDateMonth = $getEmployee->as_status_date;
                            $totalDay = $ttotalDay - ((int) $sDay-1);

                            if($sDay > 1){
                                $partial = 1;
                            }
                        }
                    }

                    
                    if($monthDayCount > $ttotalDay){
                        $lastDateMonth = $yearMonth.'-'.$ttotalDay;
                    }else{
                        $lastDateMonth = Carbon::parse($today)->endOfMonth()->toDateString();
                    }
                    // get exists check this month data employee wise
                    $getSalary = HrMonthlySalary::
                    where('as_id', $getEmployee->associate_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();
                    return $month.'-'.$year;
                    // get absent employee wise
                    $getPresentOT = DB::table($tableName)
                        ->select([
                            DB::raw('count(as_id) as present'),
                            DB::raw('SUM(ot_hour) as ot'),
                            DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                            DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')

                        ])
                        ->where('as_id', $getEmployee->as_id)
                        ->where('in_date','>=',$firstDateMonth)
                        ->where('in_date','<=', $lastDateMonth)
                        ->first();
                    
                    $lateCount = 0;
                    $halfCount = 0;
                    $presentOt = 0;
                    $present = 0;
                    $overtime_rate = 0;
                    if($getPresentOT){
                        $present = $getPresentOT->present??0;
                        $lateCount = $getPresentOT->late??0;
                        $halfCount = $getPresentOT->halfday??0;
                    }

                    // for ot holder

                    if($getEmployee->as_ot == 1){
                        $presentOt = $getPresentOT->ot??0;

                        // check if friday has extra ot
                        if($getEmployee->shift_roaster_status == 1 ){
                            $friday_ot = DB::table('hr_att_special')
                                            ->where('as_id', $getEmployee->as_id)
                                            ->where('in_date','>=', $firstDateMonth)
                                            ->where('in_date','<=', $lastDateMonth)
                                            ->get()
                                            ->sum('ot_hour');

                            $presentOt = $presentOt + $friday_ot;
                        }

                        $diffExplode = explode('.', $presentOt);
                        $minutes = (isset($diffExplode[1]) ? $diffExplode[1] : 0);
                        $minutes = floatval('0.'.$minutes);
                        if($minutes > 0 && $minutes != 1){
                            $min = (int)round($minutes*60);
                            $minOT = min_to_ot();
                            $minutes = $minOT[$min]??0;
                        }

                        $presentOt = $diffExplode[0]+$minutes;
                        $overtime_rate = number_format((($getBenefit->ben_basic/208)*2), 2, ".", "");
                    }


                    
                    

                    // check OT roaster employee
                    $roasterData = HolidayRoaster::select('date','remarks')
                            ->where('year', $year)
                            ->where('month', $month)
                            ->where('as_id', $getEmployee->associate_id)
                            ->where('date','>=', $firstDateMonth)
                            ->where('date','<=', $lastDateMonth)
                            ->get();

                    $rosterOtData = collect($roasterData)
                        ->where('remarks', 'OT')
                        ->pluck('date');

                    $otDayCount = 0;
                    $totalOt = count($rosterOtData);
                    // return $rosterOTCount;
                    $otDayCount = DB::table($tableName)
                        ->where('as_id', $getEmployee->as_id)
                        ->whereIn('in_date', $rosterOtData)
                        ->count();

                    
                    if($getEmployee->shift_roaster_status == 1){
                        // check holiday roaster employee
                        $getHoliday = collect($roasterData)
                            ->where('remarks', 'Holiday')
                            ->count();
                        $getHoliday = $getHoliday + ($totalOt - $otDayCount);
                    }else{
                        // check holiday roaster employee
                        $RosterHolidayCount = collect($roasterData)
                            ->where('remarks', 'Holiday')
                            ->count();
                        // check General roaster employee
                        $RosterGeneralCount = collect($roasterData)
                            ->where('remarks', 'General')
                            ->count();
                        
                        // check holiday shift employee
                        $query = YearlyHolyDay::
                            where('hr_yhp_unit', $getEmployee->as_unit_id)
                            ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                            ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                            ->where('hr_yhp_open_status', 0);
                            if($empdojMonth == $yearMonth){
                                $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                            }

                            if(count($rosterOtData) > 0){
                                $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                            }
                        $shiftHolidayCount = $query->count();
                        // OT check 
                        $queryOt = YearlyHolyDay::
                            where('hr_yhp_unit', $getEmployee->as_unit_id)
                            ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                            ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                            ->where('hr_yhp_open_status', 2);
                            if($empdojMonth == $yearMonth){
                                $query->where('hr_yhp_dates_of_holidays','>=', $empdoj);
                            }
                            
                            if(count($rosterOtData) > 0){
                                $queryOt->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                            }
                        $getShiftOt = $queryOt->get();
                        $shiftOtCount = $getShiftOt->count();
                        $shiftOtDayCout = 0;

                        foreach ($getShiftOt as $shiftOt) {
                            $checkAtt = DB::table($tableName)
                            ->where('as_id', $getEmployee->as_id)
                            ->where('in_date', $shiftOt->hr_yhp_dates_of_holidays)
                            ->first();
                            if($checkAtt != null){
                                $shiftOtDayCout += 1;
                            }
                        }
                        
                        $shiftHolidayCount = $shiftHolidayCount + ($totalOt - $otDayCount) + ($shiftOtCount - $shiftOtDayCout);

                        if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                            $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
                        }else{
                            $getHoliday = $shiftHolidayCount;
                        }
                    }

                    $getHoliday = $getHoliday < 0 ? 0:$getHoliday;


                    $leaveCount = DB::table('hr_leave')
                    ->select(
                        DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
                    )
                    ->where('leave_ass_id', $getEmployee->associate_id)
                    ->where('leave_status', 1)
                    ->where('leave_from', '>=', $firstDateMonth)
                    ->where('leave_to', '<=', $lastDateMonth)
                    ->first()->total??0;

                    

                    $getAbsent = $totalDay - ($present + $getHoliday + $leaveCount);
                    if($getAbsent < 0){
                        $getAbsent = 0;
                    }
                    // get salary add deduct id form salary add deduct table
                    $getAddDeduct = SalaryAddDeduct::
                    where('associate_id', $getEmployee->associate_id)
                    ->where('month', '=', $month)
                    ->where('year', '=', $year)
                    ->first();
                    if($getAddDeduct != null){
                        $deductCost = ($getAddDeduct->advp_deduct + $getAddDeduct->cg_deduct + $getAddDeduct->food_deduct + $getAddDeduct->others_deduct);
                        $deductSalaryAdd = $getAddDeduct->salary_add;
                        $productionBonus = $getAddDeduct->bonus_add;
                        $deductId = $getAddDeduct->id;
                    }else{
                        $deductCost = 0;
                        $deductSalaryAdd = 0;
                        $deductId = null;
                        $productionBonus = 0;
                    }
                    
                    //get add absent deduct calculation
                    $perDayBasic = $getBenefit->ben_basic / 30;
                    $getAbsentDeduct = (int)($getAbsent * $perDayBasic);
                    $getHalfDeduct = (int)($halfCount * ($perDayBasic / 2));

                    $stamp = 10;
                    $payStatus = 1; // cash pay
                    if($getBenefit->ben_bank_amount != 0 && $getBenefit->ben_cash_amount != 0){
                        $payStatus = 3; // partial pay
                    }elseif($getBenefit->ben_bank_amount != 0){
                        $payStatus = 2; // bank pay
                    }

                    if($getBenefit->ben_cash_amount == 0 && $getEmployee->as_emp_type_id == 3){
                        $stamp = 0;
                    }

                    
                    // get unit wise att bonus calculation 
                    $attBonus = 0;
                    
                    /*
                     *get unit wise bonus rules 
                     *if employee joined this month, employee will get bonus 
                      only he/she joined at 1
                    */ 
                      if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $partial == 1 ){
                        $attBonus = 0;
                      }else{
                        
                        $getBonusRule = AttendanceBonusConfig::
                        where('unit_id', $getEmployee->as_unit_id)
                        ->first();
                        if($getBonusRule != null){
                            $lateAllow = $getBonusRule->late_count;
                            $leaveAllow = $getBonusRule->leave_count;
                            $absentAllow = $getBonusRule->absent_count;
                        }else{
                            $lateAllow = 3;
                            $leaveAllow = 1;
                            $absentAllow = 1;
                        }
                        
                        if ($lateCount <= $lateAllow && $leaveCount <= $leaveAllow && $getAbsent <= $absentAllow && $getEmployee->as_emp_type_id == 3) {
                            $lastMonth = Carbon::parse($today);
                            $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('m');
                            if($lastMonth == '12'){
                                $year = $year - 1;
                            }
                            $getLastMonthSalary = HrMonthlySalary::
                                where('as_id', $getEmployee->associate_id)
                                ->where('month', $lastMonth)
                                ->where('year', $year)
                                ->first();
                            if (($getLastMonthSalary != null) && ($getLastMonthSalary->attendance_bonus > 0)) {
                                if(isset($getBonusRule->second_month)) {
                                    $attBonus = $getBonusRule->second_month;
                                }
                            } else {
                                if(isset($getBonusRule->first_month)) {
                                    $attBonus = $getBonusRule->first_month;
                                }
                            }
                        }
                    }

                    // leave adjust calculate
                    $salaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($getEmployee->associate_id, $month, $year);
                    $leaveAdjust = 0.00;
                    if($salaryAdjust != null){
                        if(isset($salaryAdjust->salary_adjust)){
                            foreach ($salaryAdjust->salary_adjust as $leaveAd) {
                                $leaveAdjust += $leaveAd->amount;
                            }
                        }
                    }

                    $leaveAdjust = ceil((float)$leaveAdjust);

                    if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $monthDayCount > $ttotalDay || $partial == 1){
                        $perDayGross   = $getBenefit->ben_current_salary/$monthDayCount;
                        $totalGrossPay = ($perDayGross * $totalDay);
                        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }else{

                        $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }

                    $ot = ((float)($overtime_rate) * ($presentOt));
                    
                    $totalPayable = ceil((float)($salaryPayable + $ot + $deductSalaryAdd + $attBonus + $productionBonus + $leaveAdjust));

                    // cash & bank part
                    $tds = $getBenefit->ben_tds_amount??0;
                    if($payStatus == 1){
                        $tds = 0;
                        $cashPayable = $totalPayable;
                        $bankPayable = 0; 
                    }elseif($payStatus == 2){
                        $cashPayable = 0;
                        $bankPayable = $totalPayable;
                    }else{
                        if($getBenefit->ben_bank_amount <= $totalPayable){
                            $cashPayable = $totalPayable - $getBenefit->ben_bank_amount;
                            $bankPayable = $getBenefit->ben_bank_amount;
                        }else{
                            $cashPayable = 0;
                            $bankPayable = $totalPayable;
                        }
                    }

                    if($bankPayable > 0 && $tds > 0 && $bankPayable > $tds){
                        $bankPayable = $bankPayable - $tds;
                    }else{
                        $tds = 0;
                    }

                    $salary = [
                        'ot_status' => $getEmployee->as_ot,
                        'unit_id' => $getEmployee->as_unit_id,
                        'designation_id' => $getEmployee->as_designation_id,
                        'sub_section_id' => $getEmployee->as_subsection_id,
                        'location_id' => $getEmployee->as_location,
                        'pay_type' => $getBenefit->bank_name,
                        'gross' => $getBenefit->ben_current_salary,
                        'basic' => $getBenefit->ben_basic,
                        'house' => $getBenefit->ben_house_rent,
                        'medical' => $getBenefit->ben_medical,
                        'transport' => $getBenefit->ben_transport,
                        'food' => $getBenefit->ben_food,
                        'late_count' => $lateCount,
                        'present' => $present,
                        'holiday' => $getHoliday,
                        'absent' => $getAbsent,
                        'leave' => $leaveCount,
                        'absent_deduct' => $getAbsentDeduct,
                        'half_day_deduct' => $getHalfDeduct,
                        'salary_add_deduct_id' => $deductId,
                        'salary_payable' => $salaryPayable,
                        'ot_rate' => $overtime_rate,
                        'ot_hour' => $presentOt,
                        'attendance_bonus' => $attBonus,
                        'production_bonus' => $productionBonus,
                        'leave_adjust' => $leaveAdjust,
                        'stamp' => $stamp,
                        'pay_status' => $payStatus,
                        'emp_status' => $getEmployee->as_status,
                        'total_payable' => $totalPayable,
                        'cash_payable' => $cashPayable,
                        'bank_payable' => $bankPayable,
                        'tds' => $tds
                    ];

                    if($getSalary == null){
                        $salary['as_id'] = $getEmployee->associate_id;
                        HrMonthlySalary::insert($salary);
                    }else{
                        
                        HrMonthlySalary::where('id', $getSalary->id)->update($salary);
                    }
                }
            }
            // return 'success';
            return $salary;
        } catch (\Exception $e) {
            return $e->getMessage();
            // DB::table('error')->insert(['msg' => $this->asId.' '.$e->getMessage()]);
            /*$bug = $e->errorInfo[1];
            // $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                // duplicate
            }*/
        }
    }

    public function attSpecialCheck($value='')
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->where('b.as_status', 1)
        ->where('b.as_unit_id', 8)
        ->where('b.as_ot', 0)
        ->pluck('b.as_id');

        return $getEmployee;
    }

    public function addRocket(){
        
            
        
        $data = [];
        
        $emp = DB::table('hr_as_basic_info as b')
                ->select('b.as_id','b.associate_id','ben.ben_current_salary','b.as_oracle_code')
                ->leftJoin('hr_benefits as ben','b.associate_id','ben.ben_as_id')
                ->where('b.as_unit_id', 8)
                ->where('b.as_location', 11)
                ->where('b.as_status',1)
                ->whereIn('b.as_oracle_code', array_keys($data))
                ->get()->keyBy('as_oracle_code');
        $dp = [];
        foreach($data as $key => $d){
            if(isset($emp[$key])){
                
            DB::table('hr_benefits')
                ->where('ben_as_id', $emp[$key]->associate_id)
                ->update([
                    'ben_bank_amount' => $emp[$key]->ben_current_salary,
                    'ben_cash_amount' => 0,
                    'bank_name' => 'rocket',
                    'bank_no' => $d
                  ]);
                  
           $queue = (new ProcessUnitWiseSalary('hr_attendance_cew', '03', 2021, $emp[$key]->as_id, 31))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }else{
                $dp[$key] = $d;
            }
        }
        return $dp;
        
    }

    public function bangleMissingCheck()
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.associate_id')
        ->join('hr_employee_bengali AS a', 'a.hr_bn_associate_id', 'b.associate_id')
        ->whereNull('a.hr_bn_associate_name')
        ->where('as_unit_id', 3)
        // ->where('as_status', 5)
        ->get();

        return ($getEmployee);
    }
    public function employeeInfo()
    {
        $getData = ['16D8699P',
            '16E8458P',
            '17E8862P',
            '16K9116P',
            '18A9243P',
            '18D8860P',
            '18E8508P',
            '18D8839P',
            '19M8920P',
            '19L8959P',
            '19L9011P',
            '18M8701P',
            '18M8761P',
            '20K8617P',
            '20K8766P',
            '20K8729P',
            '20M8616P',
            '20M8628P',
            '20M9066P',
            '20M9067P',
            '20J8556P',
            '20L8823P',
            '20L8835P',
            '20L8850P',
            '20L9006P',
            '20M9076P',
            '21A9002P',
            '21A9103P',
            '21A9112P',
            '21A9114P',
            '21A9131P',
            '21A9145P',
            '21A9164P',
            '21A9171P',
            '20L8546P',
            '20L8967P',
            '20L8994P',
            '20K8493P',
            '20K8741P',
            '20L9047P',
            '21A8625P',
            '21A8996P',
            '21A9198P',
            '21A9206P',
            '21A9207P',
            '21A9215P',
            '21A9220P',
            '21A9226P',
            '21A9015P',
            '20L8814P',
            '20L8877P',
            '20L8930P',
            '20L8932P',
            '20M9073P',
            '21A9205P',
            '20K8676P',
            '20K8716P',
            '20M8858P',
            '20M9071P',
            '21A9151P',
            '20K8751P',
            '20K8779P',
            '18D8854P',
            '20L8922P',
            '20L8964P',
            '20L8997P',
            '19M9029P',
            '20L9048P',
            '20M9086P',
            '20K8737P',
            '20L9041P',
            '20L9013P',
            '19L8830P',
            '18B9372P',
            '17D8530P'];
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->whereIn('b.as_oracle_code', $getData)
        ->leftjoin('hr_benefits AS ben', 'b.associate_id', 'ben.ben_as_id')
        ->get();    

        $designation = designation_by_id();
        $department = department_by_id();
        $location = location_by_id();

        $d = [];
        $i = 0;
        foreach ($getEmployee as $emp) {

            $d[] = array(
                'Sl' => ++$i,
                'Oracle ID' => $emp->as_oracle_code,
                'Associate ID' => $emp->associate_id,
                'Name' => $emp->as_name,
                'OT Status' => ($emp->as_ot == 1?'OT':'Non OT'),
                'Designation' => $designation[$emp->as_designation_id]['hr_designation_name']??'',
                'Department' => $department[$emp->as_department_id]['hr_designation_name']??'',
                'DOJ' => date('m/d/Y', strtotime($emp->as_doj)),
                'Gross' => $emp->ben_current_salary,
                'Basic' => $emp->ben_basic,
                'House Rent' => $emp->ben_house_rent,
                'Other Part' => ($emp->ben_medical + $emp->ben_transport + $emp->ben_food),
                'Present' => 0,
                'Absent' => 0,
                'OT Hour' => 0,
                'OT Amount' => 0,
                'Attendance Bonus' => 0,
                'Payable Salary' => 0,
                'Bank Amount' => 0,
                'Tax Amount' => 0,
                'Cash Amount' => 0,
                'Stamp Amount' => 0,
                'Net Pay' => 0,
                'Payment Method' => '',
                'Account No.' => '',
                'Location' => $location[$emp->as_location]['hr_location_short_name']
            );
        }
        return (new FastExcel(collect($d)))->download('Employee History(CEW).xlsx');
    }

    public function bonusUploadExcel()
    {
        $getData = array(
            '90A100367N' => 138490,
            '97K027020E' => 50633,
            '18J020019C' => 24205,
            '99L027005E' => 33490,
            '98A027034E' => 47419,
            '96G027033E' => 29276,
            '18L000003A' => 49205,
            '16J020004C' => 48490,
            '97K027024E' => 25990,
            '93A027031E' => 38490,
            '89E100353N' => 61348,
            '18C000021A' => 10633,
            '97G020005C' => 35276,
            '90F000233A' => 19205,
            '91F101871N' => 36490,
            '12M027043E' => 27776,
            '17K065013K' => 7776,
            '88F100364N' => 57776,
            '97M027028E' => 27133,
            '92D100357N' => 56348,
            '98H700019P' => 21133,
            '13A027027E' => 20990,
            '07K090008M' => 25490,
            '90L100356N' => 50276,
            '98B027008E' => 25276,
            '98B000015A' => 14562,
            '11D025001D' => 28490,
            '12C025004D' => 28490,
            '00C100365N' => 18419,
            '92E075035L' => 18276,
            '12L700036P' => 18205,
            '03D500191O' => 12919,
            '97F020003C' => 22776,
            '89K101862N' => 22776,
            '94C500151O' => 27633,
            '97L100037N' => 14740,
            '92D100361N' => 42062,
            '99D000235A' => 12062,
            '96E700032P' => 13919,
            '18C100009N' => 14919,
            '12L000078A' => 16348,
            '97E000136A' => 11348,
            '04G090006M' => 25276,
            '01K100011N' => 19205,
            '89L101861N' => 13098,
            '09G100052N' => 13990,
            '00J100033N' => 13990,
            '05E000010A' => 10990,
            '93G500159O' => 20633,
            '92H101947N' => 18633,
            '11L000018A' => 10633,
            '97K090007M' => 30490,
            '94G500185O' => 16348,
            '99E100003N' => 30276,
            '91G100047N' => 14776,
            '18G075002L' => 27776,
            '97K000142A' => 9562,
            '98D500245O' => 14419,
            '15L100039N' => 11348,
            '18J090005M' => 24205,
            '00M027030E' => 19205,
            '05G500162O' => 14205,
            '03F000144A' => 9205,
            '98L500189O' => 14133,
            '08C101858N' => 13990,
            '16K000052A' => 6901,
            '99E101863N' => 13883,
            '03G100006N' => 18848,
            '11B027029E' => 18848,
            '92G100049N' => 15848,
            '10L000031A' => 13848,
            '98J500166O' => 15848,
            '01H100360N' => 13776,
            '00M027026E' => 18633,
            '11C100063N' => 13562,
            '90L500160O' => 68490,
            '12D101942N' => 13490,
            '06D075026L' => 15812,
            '13G700013P' => 10276,
            '08E500163O' => 13205,
            '93F101865N' => 10205,
            '00D101922N' => 23133,
            '99D100051N' => 13133,
            '16E020001C' => 8133,
            '09G000402A' => 12776,
            '10D075011L' => 15633,
            '11F000139A' => 7562,
            '05H500170O' => 15490,
            '06H700027P' => 10419,
            '98G065010K' => 17276,
            '17G000037A' => 5187,
            '98J000081A' => 22062,
            '89L700029P' => 17062,
            '11G027018E' => 11705,
            '12L101936N' => 9709,
            '97G065014K' => 16562,
            '15D000033A' => 9562,
            '18F000451A' => 6348,
            '02G101316N' => 16348,
            '13L700028P' => 11348,
            '18G100370N' => 36348,
            '89H101307N' => 26348,
            '10K075006L' => 21205,
            '99G100333N' => 25990,
            '05G500173O' => 15991,
            '98G101924N' => 10990,
            '96E100030N' => 13705,
            '12J020012C' => 25633,
            '10L000030A' => 10633,
            '11L000049A' => 10633,
            '02G075032L' => 15348,
            '96E500154O' => 25348,
            '08E090002M' => 20276,
            '18D100008N' => 9919,
            '06E500188O' => 14919,
            '17J000023A' => 9919,
            '18J090003M' => 20633,
            '07L065015K' => 14633,
            '12E101933N' => 9615,
            '94H065002K' => 19562,
            '12G700031P' => 9562,
            '17J000012A' => 9562,
            '16K000013A' => 9562,
            '14M027009E' => 9562,
            '11J700030P' => 9348,
            '98C100372N' => 34240,
            '18L500195O' => 24205,
            '08F700020P' => 14205,
            '18D100022N' => 9205,
            '89E100032N' => 17205,
            '00J075030L' => 14205,
            '15H075008L' => 18848,
            '04G700021P' => 13490,
            '89L700007P' => 33133,
            '10F020020C' => 12990,
            '16C101948N' => 6776,
            '08G106854N' => 103133
        );
    
        $getId = array_keys($getData);
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->join('hr_benefits AS ben', 'b.associate_id', 'ben.ben_as_id')
        ->whereIn('b.associate_id', $getId)
        ->get();
        $insert = [];
        foreach ($getEmployee as $emp) {
            $bonus_amount = $getData[$emp->associate_id]??0;
            $from = '2021-05-14';
            $month = Carbon::parse($emp->as_doj)->diffInMonths($from);
            $bonus_month = $month > 12?12:$month;
            $stamp = 10;
            $netPayable = $bonus_amount - $stamp;
            $insert[$emp->associate_id] = [
                'unit_id' => $emp->as_unit_id,
                'location_id' => $emp->as_location,
                'bonus_rule_id' => 1,
                'associate_id' => $emp->associate_id,
                'bonus_amount' => $bonus_amount,
                'type' => 'normal',
                'gross_salary' => $emp->ben_current_salary,
                'basic' => $emp->ben_basic,
                'medical' => $emp->ben_medical,
                'transport' => $emp->ben_transport,
                'food' => $emp->ben_food,
                'duration' => $bonus_month,
                'stamp' => $stamp,
                'pay_status' => 1,
                'emp_status' => 1,
                'net_payable' => $netPayable,
                'cash_payable' => $netPayable,
                'bank_payable' => 0,
                'override' => 1,
                'bank_name' => null,
                'subsection_id' => $emp->as_subsection_id,
                'designation_id' => $emp->as_designation_id,
                'ot_status' => $emp->as_ot
            ];
        }
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_bonus_sheet')->insertOrIgnore(collect($n)->toArray());
            }
        }

        return 'success';
    }

    public function checkFunction()
    {
        $shiftOuttime = date('Y-m-d H:i', strtotime('2021-04-15 16:00:00'));
        $outtimePunch = date('Y-m-d H:i', strtotime('2021-04-15 23:15:00'));
        $shiftBreak = 60;
        // check add
        // $shiftOutTime = Carbon::createFromFormat('Y-m-d H:i:s', $outtimePunch);
        $shiftAddBreak = Carbon::parse($shiftOuttime)->addMinutes($shiftBreak);
        $shiftAddSixH = Carbon::parse($shiftAddBreak)->addHours(6);
        $shiftAddSevenH = Carbon::parse($shiftAddBreak)->addHours(7);
        $extraBreakMin = 0;
        if((strtotime($outtimePunch) > strtotime(date('Y-m-d H:i', strtotime($shiftAddSixH))))){
            $extraBreakMin = $shiftBreak;
            if(strtotime($outtimePunch) < strtotime(date('Y-m-d H:i', strtotime($shiftAddSevenH)))){
                $extraBreakMin = (strtotime($outtimePunch) - strtotime(date('Y-m-d H:i', strtotime($shiftAddSixH))))/60;
            }
        }
        return $extraBreakMin;
        return strtotime(date('Y-m-d H:i', strtotime($shiftAddSevenH)));
        return date('Y-m-d H:i', strtotime($shiftAddSixH));
        $today = '2021-04-15';
        $extraMin = 0;
        if(strtotime($outtimePunch) > strtotime(date('Y-m-d H:i', strtotime($today.' 18:00:00')))){
            $extraMin = 60;
            if(strtotime($outtimePunch) < strtotime(date('Y-m-d H:i', strtotime($today.' 19:00:00')))){
                $extraMin = (strtotime($outtimePunch) - strtotime(date('Y-m-d H:i', strtotime($today.' 18:00:00'))))/60;
            }
        }
        return $extraMin;
    }
    public function jobcardupdate()
    {
        /*$id = DB::table('hr_monthly_salary as s')
                ->leftJoin('hr_as_basic_info as b','s.as_id','b.associate_id')
                ->where('s.month', '03')
                ->where('s.year', 2021)
                ->where('b.as_ot', 1)
                //->where('s.ot_hour','>', 0)
                ->pluck('b.as_id');*/
                
        // $id = DB::table('hr_as_basic_info')
        //     ->whereIn('as_unit_id', [2])->where('as_status', 1)->pluck('as_unit_id','as_id');
        // foreach($id as $k => $i)   {   
        //     $tb = 'hr_attendance_ceil';
        //     // $tb = get_att_table($i);
        //     $data = DB::table($tb)
        //         ->where('in_date','2021-04-14')
        //         // ->where('in_date','<=','2021-04-13')
        //         ->where('remarks', '!=', 'DSI')
        //         ->whereNotNull('in_time')
        //         ->whereNotNull('out_time')
        //         ->where('as_id', $k)
        //         ->get();
        
        //     foreach ($data as $key => $v) 
        //     {
        //         if($v->in_time && $v->out_time){
        //             $queue = (new ProcessAttendanceOuttime($tb, $k, $i))
        //                     ->delay(Carbon::now()->addSeconds(2));
        //                     dispatch($queue);
        //         }

                

                
        //     }
        // }
            $tb = 'hr_attendance_mbm';
            // $tb = get_att_table($i);
            $data = DB::table($tb)
                ->where('in_date','2021-04-15')
                // ->where('in_date','<=','2021-04-13')
                ->where('remarks', '!=', 'DSI')
                ->whereNotNull('in_time')
                ->whereNotNull('out_time')
                ->get();
        
            foreach ($data as $key => $v) 
            {
                if($v->in_time && $v->out_time){
                    $queue = (new ProcessAttendanceOuttime($tb, $v->id, 1))
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
                } 
            }
        return count($data);

    }

    public function shiftAssigned()
    {
        $year = 2021;
        $month = '04';
        $getEmployee = DB::table('hr_as_basic_info')
        ->where('as_unit_id', 2)
        ->where('as_location', 7)
        ->where('as_status', 1)
        ->get();
        $empIds = collect($getEmployee)->pluck('associate_id');

        $roster = DB::table('hr_shift_roaster')
        ->whereIn('shift_roaster_associate_id', $empIds)
        ->where('shift_roaster_year', $year)
        ->where('shift_roaster_month', $month)
        ->get()
        ->keyBy('shift_roaster_associate_id');
        // return ($roster);
        $insert = [];
        $update = [];
        foreach ($getEmployee as $key => $emp) {
            $shift = 'Ramadan Day Early First';
            
            if(isset($roster[$emp->associate_id])){
                $update[$emp->associate_id] = DB::table('hr_shift_roaster')
                ->where('shift_roaster_id', $roster[$emp->associate_id]->shift_roaster_id)
                // ->first();
                ->update(['day_24' => $shift, 'day_25' => $shift, 'day_26' => $shift, 'day_27' => $shift, 'day_28' => $shift, 'day_29' => $shift, 'day_30' => $shift]);
            }else{
                $insert[$emp->associate_id] = [
                    'shift_roaster_associate_id' => $emp->associate_id,
                    'shift_roaster_user_id' => $emp->as_id,
                    'shift_roaster_year' => $year,
                    'shift_roaster_month' => $month,
                    'day_24' => $shift,
                    'day_25' => $shift,
                    'day_26' => $shift,
                    'day_27' => $shift,
                    'day_28' => $shift,
                    'day_29' => $shift,
                    'day_30' => $shift
                ];
                
            }
        }
        // return $update;
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_shift_roaster')->insertOrIgnore(collect($n)->toArray());
            }
        }

        return 'success';
        
    } 
}
