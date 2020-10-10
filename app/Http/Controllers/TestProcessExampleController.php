<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestProcessExampleController extends Controller
{
    public function recheckatt()
    {

        $getData = DB::table('hr_shift_roaster')
        ->where('day_1', 'Day Early')
        ->get();
        $getDate = ['2020-09-01', '2020-09-02', '2020-09-03', '2020-09-04', '2020-09-05','2020-09-06', '2020-09-07', '2020-09-08', '2020-09-09', '2020-09-10', '2020-09-11','2020-09-12', '2020-09-13', '2020-09-14', '2020-09-15'];
        foreach ($getData as $data) {
            $getEmployee = Employee::where('associate_id', $getData->shift_roaster_associate_id)->first();
            $tableName = get_att_table($getEmployee->as_unit_id);
            $unitId = $getEmployee->as_unit_id;
            foreach ($getDate as $adate) {
                $today = date('Y-m-d', strtotime($adate));
                $month = date('m', strtotime($today));            
                $year = date('Y', strtotime($today)); 
                $getEmpAtt = DB::table($tableName)
                ->where('as_id', $getEmployee->as_id)
                ->where('in_date', $today)
                ->first();
                if($getEmpAtt != null){
                    $day_of_date = date('j', strtotime($getEmpAtt->in_time));
                    $day_num = "day_".$day_of_date;
                    $shift= DB::table("hr_shift_roaster")
                    ->where('shift_roaster_month', $month)
                    ->where('shift_roaster_year', $year)
                    ->where("shift_roaster_user_id", $getEmployee->as_id)
                    ->select([
                        $day_num,
                        'hr_shift.hr_shift_id',
                        'hr_shift.hr_shift_start_time',
                        'hr_shift.hr_shift_end_time',
                        'hr_shift.hr_shift_break_time',
                        'hr_shift.hr_shift_night_flag'
                    ])
                    ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                        $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                        $q->where('hr_shift.hr_shift_unit_id', $unitId);
                    })
                    ->orderBy('hr_shift.hr_shift_id', 'desc')
                    ->first();
                    
                    if(!empty($shift) && $shift->$day_num != null){
                        $cShifStartTime = strtotime(date("H:i", strtotime($shift->hr_shift_start_time)));
                        $cShifStart = $shift->hr_shift_start_time;
                        $cShifEnd = $shift->hr_shift_end_time;
                        $cBreak = $shift->hr_shift_break_time;
                        $nightFlag = $shift->hr_shift_night_flag;
                    }
                    else{
                        $cShifStartTime = strtotime(date("H:i", strtotime($getEmployee->shift['hr_shift_start_time'])));
                        $cShifStart = $getEmployee->shift['hr_shift_start_time'];
                        $cShifEnd = $getEmployee->shift['hr_shift_end_time'];
                        $cBreak = $getEmployee->shift['hr_shift_break_time'];
                        $nightFlag = $getEmployee->shift['hr_shift_night_flag'];
                    }

                    //late count
                    $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($getEmployee->as_unit_id, $getEmployee->shift['hr_shift_name']);
                    if($getLateCount != null){
                        if($today >= $getLateCount->date_from && $today <= $getLateCount->date_to){
                            $lateTime = $getLateCount->value;
                        }else{
                            $lateTime = $getLateCount->default_value;
                        }
                    }else{
                        $lateTime = 3;
                    }
                    $inTime = ($cShifStartTime+($lateTime * 60));

                    if($dayStatus == 'OT'){
                        $late = 0;
                    }else if($cIn > $inTime  || $getEmpAtt->remarks == 'DSI'){
                        $late = 1;
                    }else{
                        $late = 0;
                    }

                    // CALCULATE OVER TIME
                    if(!empty($cOut))
                    {
                        if($getEmployee->as_ot == 1 && $getEmpAtt->remarks != 'DSI'){
                            $otHour = EmployeeHelper::daliyOTCalculation($getEmpAtt->in_time, $getEmpAtt->out_time, $cShifStart, $cShifEnd, $cBreak, $nightFlag, $getEmployee->associate_id, $getEmployee->shift_roaster_status, $getEmployee->as_unit_id);
                        }else{
                            $otHour = 0;
                        }

                        // update attendance table ot_hour   
                        DB::table($tableName)
                        ->where('id', $this->tId)
                        ->update([
                            'ot_hour' => $otHour,
                            'late_status' => $late
                        ]);
                        
                        
                    }
                }
                

                $yearMonth = $year.'-'.$month; 
                if($month == date('m')){
                    $totalDay = date('d');
                }else{
                    $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                }
                $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $getEmployee->as_id, $totalDay))
                        ->onQueue('salarygenerate')
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);


            }            
            
        }




        return 'done';
    }

    public function shiftChange($value='')
    {
        $dayNo = 'day_1';
        $getShift = DB::table('hr_shift_roaster as s')
        // ->where('day_1', 'Day Early')
        ->where('b.as_unit_id', 1)
        ->leftJoin('hr_as_basic_info as b', 'b.associate_id', 's.shift_roaster_associate_id')
        ->whereNotNull('s.'.$dayNo)
        ->select('s.'.$dayNo, 's.shift_roaster_id')
        ->pluck('s.'.$dayNo, 's.shift_roaster_id');
        dd($getShift);
        // ->get();
        $notMacth = array();
        foreach ($getShift as $key => $shift) {
            // dd($shift);
            if($shift == 'Day Early' || $shift == 'MBM New Employee ADD Default'){
                $change = 'Day 1 (7-15)';
            }elseif($shift == 'WASHIN GeneraL SHIFT' || $shift == 'General' || $shift == 'DAY WASHING' || $shift == 'Day'){
                $change = 'Day 2 (8-16)';
            }elseif($shift == 'SECURITY NIGHT Shift -10' || $shift == 'CUTTING NIGHT'){
                $change = 'Night 3 (22 - 6)';
            }elseif($shift == 'SECURITY DAY Shift -2'){
                $change = 'Afternoon 1 (14-22)';
            }elseif($shift == 'NIGHT (WASHING)' || $shift == 'Night' || $shift == 'FRIDAY NIGHT (WAS)'){
                $change = 'Night 1 (20 - 4)';
            }elseif($shift == 'MBM Morning Shift 01 (COOK)3' || $shift == 'MBM Morning Shift 01 (COOK)'){
                $change = 'Morning 1 (3-11)';
            }elseif($shift == 'MBM Morning Shift (COOK)6' || $shift == 'MBM Morning Shift (COOK)' || $shift == 'SECURITY Morning Shift -6'){
                $change = 'Morning 2 (6-14)';
            }elseif($shift == 'FRIDAY WASHING'){
                $change = 'Noon fri(12-18.30)';
            }elseif($shift == 'FRIDAY GENERAL'){
                $change = 'Day 2 Fri (8-16)';
            }elseif($shift == 'Friday'){
                $change = 'Day 1 Fri(7-15)';
            }elseif($shift == 'Day Head Office'){
                $change = 'OfficeTime (9-18)';
            }elseif($shift == 'CUTTING NIGHT TWO'){
                $change = 'Night 2 (20.30 - 4.30)';
            }elseif($shift == 'CUTTING GENEARL'){
                $change = 'Afternoon 2 (16-24)';
            }else{
                $notMacth[] = $shift;
            }


            DB::table('hr_shift_roaster')
            ->where('shift_roaster_id', $key)
            ->update([$dayNo => $change]);

        }
        print_r($notMacth);exit;
    }
}
