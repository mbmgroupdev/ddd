<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceOuttime;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Hr\Bills;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Leave;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class TestXYZController extends Controller
{
    public function rfidUpdate()
    {
        return $this->workFromHomeBill();
        return "";
        $data = array();
        $getBasic = DB::table('hr_as_basic_info')
        ->select('as_id', 'as_rfid_code', 'as_oracle_code', 'as_unit_id')
        ->whereIn('as_unit_id', [3,8])
    //  ->where('as_rfid_code', 'LIKE', '#%')
        ->whereRaw('LENGTH(as_rfid_code) < 10')
        ->get();
    //  ->pluck('as_oracle_code');
        return ($getBasic);
        foreach ($getBasic as $emp) {
            $rfid = ltrim($emp->as_rfid_code,'#');
    //      $rfid = str_pad($emp->as_rfid_code, 10, "0", STR_PAD_LEFT); 
       //     if($rfid == '0000000000'){
       //       $rfid = null;
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
        // return ($query);
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-03-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('hr_absent')
        //                               ->where('date', 'like', '2021-03%')
        //                               ->where('associate_id', $e->associate_id)
        //                               ->where('date','<',$e->as_doj)
        //                               ->get();
        //     if(count($query) > 0){
        //         $data[$e->associate_id] = $query;
        //     }
        // }
        // return ($data);
        // $leave_array = [];
        //         $absent_array = [];
        //         for($i=1; $i<=31; $i++) {
        //         $date = date('Y-m-d', strtotime('2021-03-'.$i));
        //         $leave = DB::table('hr_attendance_cew AS a')
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
        //                 ->get();
        //         }
        //         return $absent_array;
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
            // return "done";
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
            //         ->get();
            // }
            // return $absent_array;
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
        $d = [];
        // $getData = DB::table('hr_attendance_mbm AS m')
        //     ->select('ba.associate_id', 'ba.as_oracle_code', 'ba.as_department_id', 'ba.as_section_id', 'ba.as_name','m.*', 'b.hr_shift_end_time', 'b.hr_shift_break_time')
        //     ->whereIn('m.in_date', ['2021-03-12', '2021-03-19'])
        //     // ->whereIn('m.as_id', $getBasic)
        //     ->leftJoin('hr_shift AS b', function($q){
        //         $q->on('b.hr_shift_code', 'm.hr_shift_code');
        //     })
        //     ->leftJoin('hr_as_basic_info AS ba', function($q){
        //         $q->on('ba.as_id', 'm.as_id');
        //     })
        //     ->whereNotNull('m.out_time')
        //     ->whereNotNull('m.in_time')
        //     ->where('m.ot_hour', 0)
        //     ->where('ba.as_ot', 1)
        //     ->get();
        // $d = [];
        // foreach ($getData as $att) {
        //     $d[] = array(
        //         'Oracle Id' => $att->as_oracle_code,
        //         'Associate Id' => $att->associate_id,
        //         'Name' => $att->as_name,
        //         'Department' => $department[$att->as_department_id]['hr_department_name']??'',
        //         'Section' => $section[$att->as_section_id]['hr_section_name']??'',
        //         'Date' =>  date('m/d/Y', strtotime($att->in_date)),
        //         'In Time' => date('H:i:s', strtotime($att->in_time)),
        //         'Out Time' => date('H:i:s', strtotime($att->out_time)),
        //     );
        // }
        // return (new FastExcel(collect($d)))->download('Ot missing(12 19).xlsx');
        // dd($getData);
        $getBasic = DB::table('hr_as_basic_info')
        ->where('as_ot', 1)
        ->whereIn('as_unit_id', [8])
        ->where('as_status', 1)
        ->pluck('as_id');
        $getat = [];
        for($i=1; $i<=31; $i++) {
            $getData = DB::table('hr_attendance_cew AS m')
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
            ->where('m.remarks', '!=', 'DSI')
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
    public function indiviBillEntry(){
        $date = '2021-04-';
        for($i=14; $i<=24; $i++){
            DB::table('hr_bill')
            ->insert([
                'as_id' => 2990,
                'bill_date' => $date.$i,
                'bill_type' => 4,
                'amount' => 30,
                'pay_status' => 0
            ]);
        }
        return 'success';
    }
    public function billEntry()
    {
        $data = [];
        $date = '2021-04-24';
    
        $getTable = DB::table('hr_attendance_mbm AS m')
        ->select('m.as_id', 'm.in_date', 'm.out_time', 'm.hr_shift_code', 's.hr_shift_night_flag')
        ->join('hr_shift AS s', 'm.hr_shift_code', 's.hr_shift_code')
        ->where('m.in_date', $date)
        //->where('s.hr_shift_night_flag',1)
        ->get();

        DB::beginTransaction();
        $insert = [];
        try {
            foreach ($getTable as $key => $value) {
                $insert[] = [
                    'as_id' => $value->as_id,
                    'bill_date' => $date,
                    'bill_type' => 4,
                    'amount' => 30,
                    'pay_status' => 0
                ];
            }

            if(count($insert) > 0){
                $chunk = collect($insert)->chunk(200);
                foreach ($chunk as $key => $n) {        
                    DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
                }
            }
            
            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

    }
    public function annaBillCheck()
    {
        $date = '2021-04-';
        $data = [];
        $getBill = DB::table('hr_bill')
            ->select(DB::raw("CONCAT(bill_date,as_id) AS asdate"), 'bill_date', 'bill_type')
            ->whereBetween('bill_date', ['2021-04-01', '2021-04-30'])
            ->where('bill_type', 2)
            ->get()
            ->keyBy('asdate')
            ->toArray();
        for ($i=1; $i < 31; $i++) { 
            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"), 'in_date', 'in_time', 'out_time', 'as_id')
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            ->where('in_time', '>', $date.$i.' 18:00:00')
            ->get()
            ->keyBy('asdate')
            ->toArray();
            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id])){

                    $data[] = $att;
                    $insert[] = [
                        'as_id' => $att->as_id,
                        'bill_date' => $att->in_date,
                        'bill_type' => 2,
                        'amount' => 70,
                        'pay_status' => 0
                    ];
                // }elseif(isset($getBill[$att->in_date.$att->as_id]) && $getBill[$att->in_date.$att->as_id]->bill_type == 1){
                //     $data[] = $att;
                //     DB::table('hr_bill')
                //     ->where('bill_date', $att->in_date)
                //     ->where('as_id', $att->as_id)
                //     ->update([
                //         'pay_status' => 2,
                //         'amount' => 70
                //     ]);
                }
            }

        }
        return $data;
        if(count($insert) > 0){
            $chunk = collect($insert)->chunk(200);
            foreach ($chunk as $key => $n) {        
                DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
            }
        }
        return 'success';
        // dd($data);

        for ($i=14; $i <= 24; $i++) { 
            $getBill = DB::table('hr_bill')
            ->select(DB::raw("CONCAT(bill_date,as_id) AS asdate"), 'bill_date', 'bill_type')
            ->where('bill_date', date('Y-m-d', strtotime($date.$i)))
            ->where('bill_type', 4)
            ->get()
            ->keyBy('asdate')
            ->toArray();

            $getatt = DB::table('hr_attendance_mbm')
            ->select(DB::raw("CONCAT(in_date,as_id) AS asdate"), 'in_date', 'in_time', 'out_time', 'as_id')
            ->where('in_date', date('Y-m-d', strtotime($date.$i)))
            ->get()
            ->keyBy('asdate')
            ->toArray();
            
            foreach ($getatt as $att) {
                if(!isset($getBill[$att->in_date.$att->as_id])){
                    $data[] = $att;
                }
            }
        }
        return ($data);
    }
    public function tiffinBillCheck()
    {
        $date = '2021-04-';
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
    public function workFromHomeBill()
    {
        $data = [];
        $outsideCheck= DB::table('hr_outside')
            ->select('hr_as_basic_info.as_id', 'hr_outside.start_date', 'hr_outside.end_date', 'hr_as_basic_info.as_unit_id')
            ->where('start_date','>','2021-04-13')
            ->where('requested_location','WFHOME')
            ->join('hr_as_basic_info', 'hr_outside.as_id', 'hr_as_basic_info.associate_id')
            ->get();
        foreach($outsideCheck as $outside){
            $tableName = get_att_table($outside->as_unit_id);
            $data[] = DB::table($tableName)
            ->where('as_id', $outside->as_id)
            ->whereNotNull('in_unit')
            ->whereBetween('in_date', [$outside->start_date, $outside->end_date])
            ->get();
        }
        return ($data);

        $data = [];
        $outsideCheck= DB::table('hr_outside')
            ->select('hr_as_basic_info.as_id', 'hr_outside.start_date', 'hr_outside.end_date')
            ->where('start_date','>','2021-04-13')
            //->where('status',1)
            ->where('requested_location','WFHOME')
            ->join('hr_as_basic_info', 'hr_outside.as_id', 'hr_as_basic_info.associate_id')
            ->get();
        foreach($outsideCheck as $outside){
            $data[] = DB::table('hr_bill')
            ->where('as_id', $outside->as_id)
            ->whereBetween('bill_date', [$outside->start_date, $outside->end_date])
            ->delete();
        }
        return ($data);
    }
    public function hoLocationbillEntry()
    {
        $data = [];
        $date = '2021-04-14';
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_id', 'b.as_unit_id', 'b.as_name')
        ->whereIn('b.as_unit_id', [3])
        ->where('b.as_location', 12)
        ->where('b.as_status', 1)
        ->pluck('as_id');

        $getTable = DB::table('hr_attendance_aql AS m')
        ->select('m.as_id', 'm.in_date', 'm.out_time', 'm.hr_shift_code', 's.hr_shift_night_flag')
        ->join('hr_shift AS s', 'm.hr_shift_code', 's.hr_shift_code')
        ->where('m.in_date','>', '2021-04-13')
        ->whereIn('m.as_id', $getEmployee)
        ->where('s.hr_shift_night_flag',0)
        ->get();

        DB::beginTransaction();
        $insert = [];
        try {
            foreach ($getTable as $key => $value) {
                $insert[] = [
                    'as_id' => $value->as_id,
                    'bill_date' => $value->in_date,
                    'bill_type' => 4,
                    'amount' => 30,
                    'pay_status' => 0
                ];
            }
            
            if(count($insert) > 0){
                $chunk = collect($insert)->chunk(200);
                foreach ($chunk as $key => $n) {        
                    DB::table('hr_bill')->insertOrIgnore(collect($n)->toArray());
                }
            }
            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }

    }
    public function billRemove()
    {
        $data = DB::table("hr_bill AS t")
        ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.as_id', 't.as_id');
        })
        ->where('t.bill_date', '2021-04-13')
        ->whereNotIn('b.as_department_id', [67])
        ->where('t.bill_type', 2)
        ->get();

        return count($data);

        $getBill = DB::table("hr_bill AS t")
        ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.as_id', 't.as_id');
        })
        //->whereIn('b.as_location', [12,13])
        // ->whereIn('b.as_subsection_id', [185,108])
        // ->whereIn('b.as_designation_id', [408,397,218,229,204,211,356,230,470,407,221,293,375,449,196,454,402,463])
        //->whereIn('b.as_department_id', [53,56])
        ->get();
        return $getBill;

    }
    public function billUpdate()
    {
        $getBill = DB::table("hr_bill AS t")
        ->select('t.*', 'b.as_designation_id', 'b.as_location', 'b.as_subsection_id', 'b.as_department_id')
        ->leftJoin('hr_as_basic_info AS b', function($q){
            $q->on('b.as_id', 't.as_id');
        })
        ->where('t.bill_type', 4)
        ->where('b.as_unit_id', 2)
        ->where('b.as_location', 7)
        ->update(['pay_status' => 1, 'pay_date'=>date('Y-m-d')]);
        return $getBill;

    }
    public function summaryReport()
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
        ->whereIn('b.as_location', auth()->user()->location_permissions())
        ->where('b.as_status', 1)
        ->get()
        ->keyBy('as_id');

        $empId = collect($getEmployee)->pluck('as_id');

        $getBill = DB::table('hr_bill')
        ->select(DB::raw('sum(amount) AS empTotal'), 'as_id')
        ->whereIn('as_id', $empId)
        ->whereBetween('bill_date', ['2021-04-15', '2021-04-22'])
        ->where('bill_type', 4)
        ->groupBy('as_id')
        ->get();
       
        $getB = collect($getBill)->map(function($q) use ($getEmployee){
            $q->as_ot      = $getEmployee[$q->as_id]->as_ot;
            return $q;
        });

        $data = collect($getB);
        $sum  = (object)[];
        $sum->totalAmount      = $data->sum('empTotal');
        $sum->totalEmp      = $data->count();
        $sum->otEmpAmount      = $data->where('as_ot', 1)->sum('empTotal');
        $sum->nonOtEmpAmount      = $data->where('as_ot', 0)->sum('empTotal');
        $sum->otEmp      = $data->where('as_ot', 1)->count();
        $sum->nonOtEmp      = $data->where('as_ot', 0)->count();
        print_r($sum);exit;
    }
    public function employeeCheck()
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->select('b.as_oracle_code', 'b.associate_id', 'ben.hr_bn_associate_name', 'b.as_status', 'b.as_name')
        ->leftJoin('hr_employee_bengali AS ben', 'b.associate_id', 'ben.hr_bn_associate_id')
        ->where('b.as_unit_id', 2)
        ->whereNull('ben.hr_bn_associate_name')
        ->whereIn('b.as_status', [1,6])
        ->get();
        $d = [];
        foreach ($getEmployee as $emp) {
            $d[] = array(
                'Associate Id' => $emp->associate_id,
                'Oracle Id' => $emp->as_oracle_code,
                'Name' => $emp->as_name,
                'Status' =>  $emp->as_status == 1?'Active':'Maternity'
                
            );
        }
        return (new FastExcel(collect($d)))->download('Employee Bangla info missing.xlsx');
        dd($getEmployee);
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
    public function checkHoliday(){
        // $getBasic = DB::table('hr_as_basic_info AS b')
        // ->select('b.associate_id', 'b.shift_roaster_status')
        // ->where('b.shift_roaster_status', 0)
        // //->whereIn('b.as_status', [1,6])
        // ->whereIn('b.as_unit_id', [1,4,5])
        // ->pluck('associate_id');
        // $hoaster = DB::table('holiday_roaster')
        // ->where('date', '2021-04-02')
        // ->whereIn('as_id', $getBasic)
        // ->where('remarks', 'General')
        // ->delete();
        // return ($hoaster);
        $getEmployee = DB::table('hr_as_basic_info')->where('associate_id', '11M000041A')->first();
        $firstDateMonth = '2021-04-01';
        $lastDateMonth = '2021-04-30';
        $month = '04';
        $year = 2021;
        $yearMonth = $year.'-'.$month;
        $empdoj = $getEmployee->as_doj;
        $empdojMonth = date('Y-m', strtotime($getEmployee->as_doj));
        $empdojDay = date('d', strtotime($getEmployee->as_doj));
        $tableName = get_att_table($getEmployee->as_unit_id);
        $rosterOTCount = HolidayRoaster::where('year', $year)
        ->where('month', $month)
        ->where('as_id', $getEmployee->associate_id)
        ->where('date','>=', $firstDateMonth)
        ->where('date','<=', $lastDateMonth)
        ->where('remarks', 'OT')
        ->get();
        $rosterOtData = $rosterOTCount->pluck('date');

        $otDayCount = 0;
        $totalOt = count($rosterOTCount);
        // return $rosterOTCount;
        foreach ($rosterOTCount as $otc) {
            $checkAtt = DB::table($tableName)
            ->where('as_id', $getEmployee->as_id)
            ->where('in_date', $otc->date)
            ->first();
            if($checkAtt != null){
                $otDayCount += 1;
            }
        }

        if($getEmployee->shift_roaster_status == 1){
            // check holiday roaster employee
            $getHoliday = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $getEmployee->associate_id)
            ->where('date','>=', $firstDateMonth)
            ->where('date','<=', $lastDateMonth)
            ->where('remarks', 'Holiday')
            ->count();
            $getHoliday = $getHoliday + ($totalOt - $otDayCount);
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $getEmployee->associate_id)
            ->where('date','>=', $firstDateMonth)
            ->where('date','<=', $lastDateMonth)
            ->where('remarks', 'Holiday')
            ->count();
            // check General roaster employee
            $RosterGeneralCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $getEmployee->associate_id)
            ->where('date','>=', $firstDateMonth)
            ->where('date','<=', $lastDateMonth)
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
        return $getHoliday;
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
                'Working Hour' => round($att->hourDuration/60, 2)
            );
        }
        return (new FastExcel(collect($d)))->download('Attendance History.xlsx');
        return $getAtt;
    }
    
    public function holidayAttCheck()
    {

        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [8])
        ->where('as_status', 1)
        // ->where('as_location', 9)
        ->where('shift_roaster_status', 1)
        ->select('associate_id', 'as_id', 'as_unit_id')
        ->get();
        $employeeKey = collect($getEmployee)->pluck('associate_id');
        $HolidayRoaster = HolidayRoaster::
        where('year', 2021)
        ->where('month', '04')
        ->whereIn('as_id', $employeeKey)
        ->where('remarks', 'Holiday')
        ->get()
        ->groupBy('as_id', true);
        // return $employeeKey;
        $data = [];
        // return $HolidayRoaster;
        foreach ($getEmployee as $key => $va) {
            //if(isset($HolidayRoaster[$va->associate_id])){
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
            //}
        }
        // }
        return $data;
        
        $getEmployee = DB::table('hr_as_basic_info')
        ->whereIn('as_unit_id', [3])
        ->where('as_status', 1)
        ->where('shift_roaster_status', 0)
        ->select('associate_id', 'as_id', 'as_unit_id')
        ->get();

        $data = [];
        $roasterData = YearlyHolyDay::
        whereIn('hr_yhp_unit', [3])
        ->where('hr_yhp_dates_of_holidays','>=', '2021-03-01')
        ->where('hr_yhp_dates_of_holidays','<=', '2021-03-31')
        ->where('hr_yhp_open_status', 0)
        ->get();
            foreach ($getEmployee as $key => $va) {

                if(count($roasterData) > 0){
                    foreach ($roasterData as $key => $value) {
                        // return $va->as_id;
                        $dat = DB::table('hr_attendance_aql')
                            ->where('in_date', $value->hr_yhp_dates_of_holidays)
                            ->where('as_id', $va->as_id)
                            ->first();
                            
                        if($dat != null){
                            $data[$va->associate_id] = $dat;
                        }
                    }
                    
                }
                
            }
        // }
        //return $data;
            $fj = [];
            foreach ($data as $key => $value) {
                $fd = HolidayRoaster::select('date','remarks')
                ->where('as_id', $key)
                ->where('date', $value->in_date)
                ->where('remarks', '!=', 'General')
                ->first();
                if($fd == null){
                    $fj[$key] = $value;
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

        $getEmployee = DB::table('hr_attendance_mbm AS m')
        ->join('hr_as_basic_info AS b', 'm.as_id', 'b.as_id')
        ->whereIn('m.in_date', ['2021-04-09', '2021-04-23'])
        ->where('b.as_ot', 1)
        ->where('m.ot_hour', 0)
        ->get();
        return $getEmployee;
        $d = [];
        foreach ($getEmployee as $att) {
            $d[] = array(
                'Associate Id' => $att->associate_id,
                'Name' => $att->as_name,
                'DOJ' => $att->as_doj,
                'Department' => $department[$att->as_department_id]['hr_department_name']??'',
                'Section' => $section[$att->as_section_id]['hr_section_name']??''
            );
            
        }
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
        // $check = DB::table('holiday_roaster AS a')
        // ->select('a.as_id', 'b.associate_id', 'b.as_name', 'b.as_section_id', 'b.as_department_id', DB::raw('COUNT(*) AS count'))
        // // ->select('as_id', DB::raw('COUNT(*) AS count'))
        // ->whereIn('a.date', ['2021-03-29', '2021-03-30'])
        // ->where('a.comment', 'Shab-e-Barat')
        // ->leftJoin('hr_as_basic_info AS b', function($q){
        //     $q->on('b.associate_id', 'a.as_id');
        // })
        // ->groupBy('as_id')
        // ->get();

        // $d = [];
        // foreach ($check as $att) {
        //     if($att->count == 2){
        //         $d[] = array(
        //             'Associate Id' => $att->associate_id,
        //             'Name' => $att->as_name,
        //             'Department' => $department[$att->as_department_id]['hr_department_name']??'',
        //             'Section' => $section[$att->as_section_id]['hr_section_name']??''
        //         );
        //     }
        // }

        return (new FastExcel(collect($d)))->download('general 02.xlsx');
        return ($check);
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
            //$key = isset($emp[$key])?$key:'A'.$key;
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
        return (new FastExcel(collect($dp)))->download('Rocket.xlsx');
    }
    
    public function attSpecialCheck($value='')
    {
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->where('b.as_status', 1)
        ->where('b.as_unit_id', 8)
        ->where('b.as_ot', 0)
        ->pluck('b.as_id');
        
        $getAtt = DB::table('hr_att_special')
        ->whereIn('as_id', $getEmployee)
        ->get();
        foreach($getAtt as $att){
            DB::table('hr_att_special')
            ->where('id', $att->id)
            ->update([
                    'ot_hour' => 0
                ]);
            
        }
        
        foreach($getEmployee as $emp){
            $queue = (new ProcessUnitWiseSalary('hr_attendance_cew', '03', 2021, $emp, 31))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
        }

        return 'success';
    }
    
    public function employeeInfo()
    {
        $getData = [];
        $getEmployee = DB::table('hr_as_basic_info AS b')
        ->whereIn('b.as_oracle_code', $getData)
        ->leftjoin('hr_benefits AS ben', 'b.associate_id', 'ben.ben_as_id')
        ->orderBy('b.as_department_id', 'asc')
        ->get();    

        $designation = designation_by_id();
        $department = department_by_id();
        $location = location_by_id();
        $section = section_by_id();

        $d = [];
        $i = 0;
        foreach ($getEmployee as $emp) {
            $departmentName = $department[$emp->as_department_id]['hr_department_name']??'';
            $sectionName = $section[$emp->as_section_id]['hr_section_name'];
            $d[] = array(
                'Sl' => ++$i,
                'Oracle ID' => $emp->as_oracle_code,
                'Associate ID' => $emp->associate_id,
                'Name' => $emp->as_name,
                'OT Status' => ($emp->as_ot == 1?'OT':'Non OT'),
                'Designation' => $designation[$emp->as_designation_id]['hr_designation_name']??'',
                'Department' => $departmentName.' - '.$sectionName,
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
            '98K102533N' => 12796
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
    
    public function jobcardupdate()
    {
        
        $tb = 'hr_attendance_mbm';
        $data = DB::table($tb)
            ->where('in_date','2021-04-23')
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

    public function salaryCheck()
    {
        $asId = 1278;
        $tableName = 'hr_attendance_mbm';
        $getEmployee = Employee::where('as_id', $asId)->first();
        // return $getEmployee;
        $year = 2021;
        $month = '04';
        $yearMonth = $year.'-'.$month;
        $monthDayCount  = Carbon::parse($yearMonth)->daysInMonth;
        $partial = 0;
        $totalDay = 30;
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

                $totalDay = $totalDay;
                $today = $yearMonth.'-01';
                $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
                if($empdojMonth == $yearMonth){
                    $totalDay = $totalDay - ((int) $empdojDay-1);
                    $firstDateMonth = $getEmployee->as_doj;
                }

                if($getBenefit != null){
                    
                    if($getEmployee->as_status_date != null){
                        $sDate = $getEmployee->as_status_date;
                        $sYearMonth = Carbon::parse($sDate)->format('Y-m');
                        $sDay = Carbon::parse($sDate)->format('d');


                        if($yearMonth == $sYearMonth){
                            $firstDateMonth = $getEmployee->as_status_date;
                            $totalDay = $totalDay - ((int) $sDay-1);

                            if($sDay > 1){
                                $partial = 1;
                            }
                        }
                    }

                    
                    if($monthDayCount > $totalDay){
                        $lastDateMonth = $yearMonth.'-'.$totalDay;
                    }else{
                        $lastDateMonth = Carbon::parse($today)->endOfMonth()->toDateString();
                    }
                    // get exists check this month data employee wise
                    $getSalary = HrMonthlySalary::
                    where('as_id', $getEmployee->associate_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                    // get absent employee wise
                    $getPresentOT = DB::table($tableName)
                        ->select([
                            DB::raw('count(as_id) as present'),
                            DB::raw('SUM(ot_hour) as ot'),
                            DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                            DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')

                        ])
                        ->where('as_id', $asId)
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
                    $incrementAdjust = 0;
                    $salaryAdd = 0;
                    if($salaryAdjust != null){
                        $adj = DB::table('hr_salary_adjust_details')
                            ->where('salary_adjust_master_id', $salaryAdjust->id)
                            ->get();

                        $leaveAdjust = collect($adj)->where('type',1)->sum('amount');
                        $incrementAdjust = collect($adj)->where('type',3)->sum('amount');
                        $salaryAdd = collect($adj)->where('type',2)->sum('amount');
                        
                    }

                    $leaveAdjust = ceil((float) $leaveAdjust);
                    $incrementAdjust = ceil((float) $incrementAdjust);

                    if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $monthDayCount > $totalDay || $partial == 1){
                        $perDayGross   = $getBenefit->ben_current_salary/$monthDayCount;
                        $totalGrossPay = ($perDayGross * $totalDay);
                        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }else{

                        $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }

                    $ot = ((float)($overtime_rate) * ($presentOt));
                    
                    $totalPayable = ceil((float)($salaryPayable + $ot + $deductSalaryAdd + $attBonus + $productionBonus + $leaveAdjust + $salaryAdd + $incrementAdjust));

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
                        'tds' => $tds,
                        'roaster_status' => $getEmployee->shift_roaster_status
                    ];

                    
                }
            }
            return $salary;

        } catch (\Exception $e) {
            return $e->getMessage();
            
        }
    }
    
    
}
