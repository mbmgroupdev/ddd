<?php

namespace App\Jobs;

use App\Models\Hr\Benefits;
use App\Models\Hr\AttendanceBonus;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class ProcessMonthlySalary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
    */
    public $tries = 5;
    public $asId;
    public $today;
    public $late;
    public $overtimes;
    public $present;
    
    public function __construct($asId, $today, $late, $overtimes, $present)
    {
        $this->asId = $asId;
        $this->today = $today;
        $this->late = $late;
        $this->overtimes = $overtimes;
        $this->present = $present;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $asId        = $this->asId;
        $today       = $this->today;
        $late        = $this->late;
        $overtimes   = $this->overtimes;
        $present     = $this->present;
        $getEmployee = Employee::where('as_id', $asId)->first();
        if($getEmployee != null){
            //  get benefit employee associate id wise
            $getBenefit = Benefits::
            where('ben_as_id', $getEmployee->associate_id)
            ->first();
            if($getBenefit != null){
                $year = Carbon::parse($today)->format('Y');
                $month = Carbon::parse($today)->format('m');
                // get exists check this month data employee wise
                $getSalary = HrMonthlySalary::
                where('as_id', $getEmployee->associate_id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

                // get holiday employee wise
                $getHoliday = DB::table("hr_yearly_holiday_planner")
                        ->where('hr_yhp_unit', $getEmployee->as_unit_id)
                        ->whereYear('hr_yhp_dates_of_holidays', $year)
                        ->whereMonth('hr_yhp_dates_of_holidays', $month)
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
                ->whereYear('leave_from', '>=', $year)
                ->whereYear('leave_to', '<=', $year)
                ->whereMonth('leave_from', '>=', $month)
                ->whereMonth('leave_to', '<=', $month)
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
                $getAddDeduct = SalaryAddDeduct::
                where('associate_id', $getEmployee->associate_id)
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
                $overtime_salary = 0;
                
                // get unit wise att bonus calculation 
                $bonusamount = AttendanceBonus::
                            where('unit',$getEmployee->as_unit_id)
                            ->where('status',1)
                            ->first();

                $attBonus = 0;
                if($getSalary != null){
                    $totalLate = $getSalary->late_count;
                }else{
                    $totalLate = $late;
                }
                //get unit wise bonus rules
                $getBonusRule = DB::table('hr_attendance_bonus_dynamic')
                ->where('unit_id', $getEmployee->as_unit_id)
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
                if ($totalLate <= $lateAllow && $leaveCount <= $leaveAllow && $getAbsent <= $absentAllow && $getEmployee->as_emp_type_id == 3) {
                    $lastMonth = Carbon::parse($today);
                    $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('n');
                    $getLastMonthSalary = HrMonthlySalary::
                        where('as_id', $getEmployee->associate_id)
                        ->where('month', $lastMonth)
                        ->where('year', $year)
                        ->first();
                    if (($getLastMonthSalary != null) && ($getLastMonthSalary->attendance_bonus > 0)) {
                        if(isset($bonusamount->from_2nd)) {
                            $attBonus = $bonusamount->from_2nd;
                        }
                    } else {
                        if(isset($bonusamount->first_month)) {
                            $attBonus = $bonusamount->first_month;
                        }
                    }
                }

                // get salary payable calculation
                $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + ($deductCost) + $stamp);

                if($getSalary == null){

                    $salary = [
                        'as_id' => $getEmployee->associate_id,
                        'month' => $month,
                        'year'  => $year,
                        'gross' => $getBenefit->ben_current_salary,
                        'basic' => $getBenefit->ben_basic,
                        'house' => $getBenefit->ben_house_rent,
                        'medical' => $getBenefit->ben_medical,
                        'transport' => $getBenefit->ben_transport,
                        'food' => $getBenefit->ben_food,
                        'late_count' => $late,
                        'present' => $present,
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
                        'gross' => $getBenefit->ben_current_salary,
                        'basic' => $getBenefit->ben_basic,
                        'house' => $getBenefit->ben_house_rent,
                        'medical' => $getBenefit->ben_medical,
                        'transport' => $getBenefit->ben_transport,
                        'food' => $getBenefit->ben_food,
                        'late_count' => ($getSalary->late_count + $late),
                        'present' => ($getSalary->present + $present),
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
                }
            }
        }
    }
}
