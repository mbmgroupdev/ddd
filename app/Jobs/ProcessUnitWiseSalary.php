<?php

namespace App\Jobs;

use App\Models\Hr\Absent;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Employee;
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
        $monthDayCount  = Carbon::parse($yearMonth)->daysInMonth;
        try {
            if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $yearMonth){

                //  get benefit employee associate id wise
                $getBenefit = Benefits::
                where('ben_as_id', $getEmployee->associate_id)
                ->first();
                if($getBenefit != null){
                    $today = $yearMonth.'-01';
                    $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
                    if($monthDayCount > $this->totalDay){
                        $lastDateMonth = $yearMonth.'-'.$this->totalDay;
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
                    $getPresentOT = DB::table($this->tableName)
                        ->select([
                            DB::raw('count(as_id) as present'),
                            DB::raw('SUM(ot_hour) as ot'),
                            DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                            DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')

                        ])
                        ->where('as_id', $this->asId)
                        ->where('in_date','>=',$firstDateMonth)
                        ->where('in_date','<=', $lastDateMonth)
                        ->first();

                    if(!isset($getPresentOT->present)){
                        $getPresentOT->present = 0;
                    }

                    if(!isset($getPresentOT->ot)){
                        $getPresentOT->ot = 0;
                    }
                    
                    $lateCount = $getPresentOT->late??0;
                    $halfCount = $getPresentOT->halfday??0;

                    $empdoj = $getEmployee->as_doj;
                    $empdojMonth = date('Y-m', strtotime($getEmployee->as_doj));
                    $empdojDay = date('d', strtotime($getEmployee->as_doj));

                    if($getEmployee->shift_roaster_status == 1){
                        // check holiday roaster employee
                        $getHoliday = HolidayRoaster::where('year', $year)
                        ->where('month', $month)
                        ->where('as_id', $getEmployee->associate_id)
                        ->where('date','>=', $firstDateMonth)
                        ->where('date','<=', $lastDateMonth)
                        ->where('remarks', 'Holiday')
                        ->count();
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
                        
                        if($empdojMonth == $yearMonth){
                            $shiftHolidayCount = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                                ->where('hr_yhp_dates_of_holidays','>=', $empdoj)
                                ->where('hr_yhp_open_status', 0)
                                ->count();
                        }else{
                            $shiftHolidayCount = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                                ->where('hr_yhp_open_status', 0)
                                ->count();
                        }
                        
                        if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                            $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
                        }else{
                            $getHoliday = $shiftHolidayCount;
                        }
                    }

                    // get absent employee wise
                    // $getAbsent = DB::table('hr_absent')
                    //     ->where('associate_id', $getEmployee->associate_id)
                    //     ->where('date','>=', $firstDateMonth)
                    //     ->where('date','<=', $lastDateMonth)
                    //     ->count();

                    // get leave employee wise

                    $leaveCount = DB::table('hr_leave')
                    ->select(
                        DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
                    )
                    ->where('leave_ass_id', $getEmployee->associate_id)
                    ->where('leave_status', 1)
                    ->where('leave_from', '>=', $firstDateMonth)
                    ->where('leave_to', '<=', $lastDateMonth)
                    ->first()->total??0;

                    if($empdojMonth == $yearMonth){
                        $totalDay = $this->totalDay - ((int) $empdojDay-1);
                    }else{
                        $totalDay = $this->totalDay;
                    }
                    $getAbsent = $totalDay - ($getPresentOT->present + $getHoliday + $leaveCount);
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
                    $getAbsentDeduct = $getAbsent * $perDayBasic;
                    $getHalfDeduct = $halfCount * ($perDayBasic / 2);

                    $stamp = 10;
                    $payStatus = 1; // cash pay
                    if($getBenefit->ben_bank_amount != 0 && $getBenefit->ben_cash_amount != 0){
                        $payStatus = 3; // partial pay
                    }elseif($getBenefit->ben_bank_amount != 0){
                        $payStatus = 2; // bank pay
                    }

                    if($getBenefit->ben_cash_amount == 0){
                        $stamp = 0;
                    }

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
                    
                    if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $monthDayCount > $this->totalDay){
                        $perDayGross   = $getBenefit->ben_current_salary/$monthDayCount;
                        $totalGrossPay = ($perDayGross * $totalDay);
                        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }else{

                        $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }

                    $ot = round((float)($overtime_rate) * ($getPresentOT->ot));
                    
                    $totalPayable = round((float)($salaryPayable + $ot + $deductSalaryAdd + $attBonus + $productionBonus + $leaveAdjust));

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
