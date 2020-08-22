<?php

namespace App\Jobs;

use App\Models\Hr\Absent;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Hr\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Leave;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class ProcessUnitWiseSalary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    // public $tries = 3;
    public $timeout=500;
    public $tableName;
    public $month;
    public $year;
    public $asId;
    public $totalDay;
    public function __construct($tableName, $month, $year, $asId, $totalDay)
    {
        $this->tableName = $tableName;
        $this->month = $month;
        $this->year = $year;
        $this->asId = $asId;
        $this->totalDay = $totalDay;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $getEmployee = Employee::where('as_id', $this->asId)->first();
        $year = $this->year;
        $month = $this->month;
        $yearMonth = $year.'-'.$month;
        try {
            //&& date('Y-m', $getEmployee->as_doj) =< date('')
            if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $yearMonth){

                //  get benefit employee associate id wise
                $getBenefit = Benefits::
                where('ben_as_id', $getEmployee->associate_id)
                ->first();
                if($getBenefit != null){
                    
                    // get exists check this month data employee wise
                    $getSalary = HrMonthlySalary::
                    where('as_id', $getEmployee->associate_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                    // get absent employee wise
                    $getPresentOT = DB::table($this->tableName)
                        ->select([
                            \DB::raw('count(as_id) as present'),
                            \DB::raw('SUM(ot_hour) as ot')

                        ])
                        ->where('as_id', $this->asId)
                        ->where('in_time', 'LIKE', $yearMonth.'%')
                        ->first();

                    if(!isset($getPresentOT->present)){
                        $getPresentOT->present = 0;
                    }

                    if(!isset($getPresentOT->ot)){
                        $getPresentOT->ot = 0;
                    }
                    
                    $lateCount = DB::table($this->tableName)
                        ->where('as_id', $this->asId)
                        ->where('in_time', 'LIKE', $yearMonth.'%')
                        ->where('late_status', 1)
                        ->count();

                    $halfCount = DB::table($this->tableName)
                        ->where('as_id', $this->asId)
                        ->where('in_time', 'LIKE', $yearMonth.'%')
                        ->where('remarks', 'HD')
                        ->count();

                    $empdoj = $getEmployee->as_doj;
                    $empdojMonth = date('Y-m', strtotime($getEmployee->as_doj));
                    $empdojDay = date('d', strtotime($getEmployee->as_doj));

                    if($getEmployee->shift_roaster_status == 1){
                        // check holiday roaster employee
                        $holidayCount = HolidayRoaster::where('year', $year)
                        ->where('month', $month)
                        ->where('as_id', $getEmployee->associate_id)
                        ->where('remarks', 'Holiday')
                        ->count();
                    }else{
                        // check holiday roaster employee
                        $RosterHolidayCount = HolidayRoaster::where('year', $year)
                        ->where('month', $month)
                        ->where('as_id', $getEmployee->associate_id)
                        ->where('remarks', 'Holiday')
                        ->count();
                        // check General roaster employee
                        $RosterGeneralCount = HolidayRoaster::where('year', $year)
                        ->where('month', $month)
                        ->where('as_id', $getEmployee->associate_id)
                        ->where('remarks', 'General')
                        ->count();
                         // check holiday shift employee
                        
                        if($empdojMonth == $yearMonth){
                            $shiftHolidayCount = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->whereYear('hr_yhp_dates_of_holidays', $year)
                                ->whereMonth('hr_yhp_dates_of_holidays', $month)
                                ->where('hr_yhp_dates_of_holidays','>=', $empdoj)
                                ->where('hr_yhp_open_status', 0)
                                ->count();
                        }else{
                            $shiftHolidayCount = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->whereYear('hr_yhp_dates_of_holidays', $year)
                                ->whereMonth('hr_yhp_dates_of_holidays', $month)
                                ->where('hr_yhp_open_status', 0)
                                ->count();
                        }
                        
                        if($RosterHolidayCount > 0){
                            $holidayCount = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
                        }else{
                            $holidayCount = $shiftHolidayCount;
                        }
                    }



                    // get absent employee wise
                    $getAbsent = Absent::
                    where('associate_id', $getEmployee->associate_id)
                    ->whereMonth('date', '=', $month)
                    ->whereYear('date', '=', $year)
                    ->count();

                    // get leave employee wise
                    $getLeave = Leave::
                    where('leave_ass_id', $getEmployee->associate_id)
                    ->where('leave_from', 'LIKE', $yearMonth.'%')
                    ->where('leave_to', 'LIKE', $yearMonth.'%')
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

                    if($empdojMonth == $yearMonth){
                        $totalDay = $this->totalDay - ((int) $empdojDay-1);
                    }else{
                        $totalDay = $this->totalDay;
                    }
                    $getHoliday = $totalDay - ($getPresentOT->present + $getAbsent + $leaveCount);
                    if($getHoliday > $holidayCount){
                        $getHoliday = $holidayCount;
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
                    $getHalfDeduct = $halfCount * ($perDayBasic / 2);

                    $stamp = 10;

                    if($getEmployee->as_ot == 1){
                        $overtime_rate = number_format((($getBenefit->ben_basic/208)*2), 2, ".", "");
                    } else {
                        $overtime_rate = 0;
                    }
                    
                    // get unit wise att bonus calculation 
                    $attBonus = 0;
                    
                    /*
                     *get unit wise bonus rules 
                     *if employee joined this month, employee will get bonus 
                      only he/she joined at 1
                    */ 
                      if($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1){
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
                        $today = $yearMonth.'-01';
                        if ($lateCount <= $lateAllow && $leaveCount <= $leaveAllow && $getAbsent <= $absentAllow && $getEmployee->as_emp_type_id == 3) {
                            $lastMonth = Carbon::parse($today);
                            $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('n');
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
                    
                    $monthDayCount  = Carbon::parse($yearMonth)->daysInMonth;

                    if($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1){
                        $perDayGross   = $getBenefit->ben_current_salary/$monthDayCount;
                        $totalGrossPay = ($perDayGross * $totalDay);
                        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }else{

                        $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }

                    $ot = round((float)($overtime_rate) * ($getPresentOT->ot));
                    
                    $totalPayable = $salaryPayable + $ot + $deductSalaryAdd + $attBonus + $leaveAdjust;

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
                            'late_count' => $lateCount,
                            'present' => $getPresentOT->present,
                            'holiday' => $getHoliday,
                            'absent' => $getAbsent,
                            'leave' => $leaveCount,
                            'absent_deduct' => $getAbsentDeduct,
                            'half_day_deduct' => $getHalfDeduct,
                            'salary_add_deduct_id' => $deductId,
                            'salary_payable' => $salaryPayable,
                            'ot_rate' => $overtime_rate,
                            'ot_hour' => $getPresentOT->ot,
                            'attendance_bonus' => $attBonus,
                            'leave_adjust' => $leaveAdjust,
                            'total_payable' => $totalPayable,
                        ];
                        HrMonthlySalary::insert($salary);
                    }else{
                        $salary = [
                            'gross' => $getBenefit->ben_current_salary,
                            'basic' => $getBenefit->ben_basic,
                            'house' => $getBenefit->ben_house_rent,
                            'medical' => $getBenefit->ben_medical,
                            'transport' => $getBenefit->ben_transport,
                            'food' => $getBenefit->ben_food,
                            'late_count' => $lateCount,
                            'present' => $getPresentOT->present,
                            'holiday' => $getHoliday,
                            'absent' => $getAbsent,
                            'leave' => $leaveCount,
                            'absent_deduct' => $getAbsentDeduct,
                            'half_day_deduct' => $getHalfDeduct,
                            'salary_payable' => $salaryPayable,
                            'ot_rate' => $overtime_rate,
                            'ot_hour' => $getPresentOT->ot,
                            'attendance_bonus' => $attBonus,
                            'leave_adjust' => $leaveAdjust,
                            'total_payable' => $totalPayable,
                        ];
                        HrMonthlySalary::where('id', $getSalary->id)->update($salary);
                    }
                }
            }

        } catch (\Exception $e) {
            /*$bug = $e->errorInfo[1];
            // $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                // duplicate
            }*/
        }
    }
}
