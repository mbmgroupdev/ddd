<?php

namespace App\Jobs;

use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class AttendanceOTCalculation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tableName;
    public $tId;
    public $unitId;
    public function __construct($tableName, $tId, $unitId)
    {
        $this->tableName = $tableName;
        $this->tId = $tId;
        $this->unitId = $unitId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $getEmpAtt = DB::table($this->tableName)->where('id', $this->tId)->first();
        $getEmployee = DB::table('hr_as_basic_info')
            ->where('as_id', $getEmpAtt->as_id)
            ->first();
        //get unit wise and shift code wise shift working time.
        $getShift = DB::table('hr_shift')->select('hr_shift_id', 'hr_shift_start_time', 'hr_shift_end_time', 'hr_shift_break_time')->where('hr_shift_unit_id', $getEmployee->as_unit_id)->where('hr_shift_code', $getEmpAtt->hr_shift_code)->first();

        // ------------------------------------------------- 
        //punch_in punch out

        // check today holiday but working day count
        $today = Carbon::parse($getEmpAtt->in_time)->format('Y-m-d');
        $holidayCheck = DB::table("hr_yearly_holiday_planner")
                    ->where('hr_yhp_dates_of_holidays', $today)
                    ->where('hr_yhp_unit', $getEmployee->as_unit_id)
                    ->where('hr_yhp_open_status', 2)
                    ->first();


        $cIn = strtotime(date("H:i", strtotime($getEmpAtt->in_time)));
        $cOut = strtotime(date("H:i", strtotime($getEmpAtt->out_time)));
        // -----
        $cShifStart = strtotime(date("H:i", strtotime($getShift->hr_shift_start_time)));
        $cShifEnd = strtotime(date("H:i", strtotime($getShift->hr_shift_end_time)));
        $cBreak = $getShift->hr_shift_break_time*60;

        if ($cOut < ($cShifEnd+$cBreak))
        {
            $cOut = null;
        }
        $overtimes = 0;
        // CALCULATE OVER TIME
        if(!empty($cOut))
        {  // 0 = holiday, 1 = ot  
            if($holidayCheck == null){
                $total_minutes = ($cOut - ($cShifEnd+$cBreak))/60;
            }else{
                $total_minutes = ($cOut - ($cShifStart+$cBreak))/60; 
            }
            $minutes = ($total_minutes%60);
            $ot_minute = $total_minutes-$minutes;
            //round minutes
            if($minutes >= 13 && $minutes < 43) $minutes = 30;
            else if($minutes >= 43) $minutes = 60;
            else $minutes = 0; 
            if($ot_minute >= 0)
            $overtimes += ($ot_minute+$minutes);

            $h = floor($overtimes/60) ? ((floor($overtimes/60)<10)?("0".floor($overtimes/60)):floor($overtimes/60)) : '00';
            $m = $overtimes%60 ? (($overtimes%60<10)?("0".$overtimes%60):($overtimes%60)) : '00';
            // update attendance table ot_hour   
            DB::table($this->tableName)
            ->where('id', $this->tId)
            ->update([
                'ot_hour' => "$h:$m"
            ]);


            //hr monthly salary generate 
            
            if($getEmployee != null){
               //  get benefit employee associate id wise
                $getBenefit = DB::table('hr_benefits')
                ->where('ben_as_id', $getEmployee->associate_id)
                ->first();
                $year = Carbon::parse($getEmpAtt->in_time)->format('Y');
                $month = Carbon::parse($getEmpAtt->in_time)->format('m');
                // get exists check this month data employee wise
                $getSalary = DB::table('hr_monthly_salary')
                ->where('as_id', $getEmpAtt->as_id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

                // get holiday employee wise
                $getHoliday = DB::table("hr_yearly_holiday_planner")
                        ->where('hr_yhp_unit', $getEmployee->as_unit_id)
                        ->where('hr_yhp_open_status', 0)
                        ->count();

                // get absent employee wise
                $getAbsent = DB::table('hr_absent')
                ->where('associate_id', $getEmployee->associate_id)
                ->whereMonth('date', '=', $month)
                ->whereYear('date', '=', $year)
                ->count();

                // get leave employee wise

                $getLeave = DB::table('hr_leave')
                ->where('leave_ass_id', $getEmployee->associate_id)
                ->whereMonth('leave_from', '=', $month)
                ->whereYear('leave_from', '=', $year)
                ->where('leave_status', 1)
                ->get();
                $leaveCount = 0;
                if(count($getLeave) > 0){
                    foreach ($getLeave as $leave) {
                        $day = 1;
                        $date = Carbon::parse($leave->leave_from);
                        $now = Carbon::parse($leave->leave_to);
                        $diff = $date->diffInDays($now);
                        $leaveCount += $diff + $day;
                    }
                }
                // get salary add deduct id form salary add deduct table
                $getAddDeduct = DB::table('hr_salary_add_deduct')
                ->where('associate_id', $getEmployee->associate_id)
                ->where('month', '=', $month)
                ->where('year', '=', $year)
                ->first();
                if($getAddDeduct != null){
                    $deductCost = ($getAddDeduct->advp_deduct + $getAddDeduct->cg_deduct + $getAddDeduct->food_deduct + $getAddDeduct->others_deduct);
                    $deductSalaryAdd = $getAddDeduct->salary_add;
                    $deductId = $getAddDeduct->id;
                }else{
                    $deductCost = 0;
                    $deductSalaryAdd = 0;
                    $deductId = null;
                }

                //get add absent deduct calculation
                $perDayBasic = $getBenefit->ben_basic / 30;
                $getAbsentDeduct = $getAbsent * $perDayBasic;

                //stamp = 10 by default all employee;
                $stamp = 10;

                if($getEmployee->as_ot == 1){
                    $overtime_rate = number_format((($getBenefit->ben_basic/208)*2), 2, ".", "");
                } else {
                    $overtime_rate = 0;
                }
                $overtime_salary = number_format($overtime_rate*($overtimes/60), 2, ".", "");
                //late count
                $inTime = ($cShifStart+3);
                if($cIn > $inTime){
                    $late = 1;
                }else{
                    $late = 0;
                }
                // get unit wise att bonus calculation 
                $bonusamount = DB::table('hr_attendance_bonus')
                            ->where('unit',$getEmployee->as_unit_id)
                            ->where('status',1)
                            ->first();
                $date1 = strtotime(date("Y-m", strtotime($getEmployee->as_doj)));
                $date2 = strtotime(date("Y-m", strtotime($today)));

                $attBonus = 0;
                if ($late <= 3 && $leaveCount <= 1 && $getEmployee->as_emp_type_id == 3 && ($date1 <= $date2)) {
                    if ($date1 == $date2) {
                        if(isset($bonusamount->first_month)) {
                            $attBonus = $bonusamount->first_month;
                        }
                    } else {
                        if(isset($bonusamount->first_month)) {
                            $attBonus = $bonusamount->from_2nd;
                        }
                    }
                }

                // get salary payable calculation
                $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + ($deductCost) + $stamp) + $deductSalaryAdd + $overtime_salary + $attBonus;

                
                // $overtimesHours = "$h.$m";

                if($getSalary == null){
                    $salary = [
                        'as_id' => $getEmployee->associate_id,
                        // 'unit_id' => $getEmployee->as_unit_id,
                        'month' => $month,
                        'year'  => $year,
                        'gross' => $getBenefit->ben_current_salary,
                        'basic' => $getBenefit->ben_basic,
                        'house' => $getBenefit->ben_house_rent,
                        'medical' => $getBenefit->ben_medical,
                        'transport' => $getBenefit->ben_transport,
                        'food' => $getBenefit->ben_food,
                        'late_count' => $late,
                        'present' => 1,
                        'holiday' => $getHoliday,
                        'absent' => $getAbsent,
                        'leave' => $leaveCount,
                        'absent_deduct' => $getAbsentDeduct,
                        'salary_add_deduct_id' => $deductId,
                        'salary_payable' => $salaryPayable,
                        'ot_rate' => $overtime_rate,
                        'ot_hour' => $overtimes,
                        'attendance_bonus' => $attBonus,
                    ];
                    DB::table('hr_monthly_salary')->insert($salary);
                }else{
                    $salary = [
                        // 'unit_id' => $getEmployee->as_unit_id,
                        'gross' => $getBenefit->ben_current_salary,
                        'basic' => $getBenefit->ben_basic,
                        'house' => $getBenefit->ben_house_rent,
                        'medical' => $getBenefit->ben_medical,
                        'transport' => $getBenefit->ben_transport,
                        'food' => $getBenefit->ben_food,
                        'late_count' => ($getSalary->late_count + $late),
                        'present' => ($getSalary->present + 1),
                        'holiday' => $getHoliday,
                        'absent' => $getAbsent,
                        'leave' => $leaveCount,
                        'absent_deduct' => $getAbsentDeduct,
                        'salary_payable' => $salaryPayable,
                        'ot_rate' => $overtime_rate,
                        'ot_hour' => ($getSalary->ot_hour + $overtimes),
                        'attendance_bonus' => $attBonus,
                    ];
                    DB::table('hr_monthly_salary')->where('id', $getSalary->id)->update($salary);
                    // $getSalary->update($salary);
                } 
            }
        }
    }
}
