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
        $partial = 0;
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

                $totalDay = $this->totalDay;
                $today = $yearMonth.'-01';
                $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
                if($empdojMonth == $yearMonth){
                    $totalDay = $this->totalDay - ((int) $empdojDay-1);
                    $firstDateMonth = $getEmployee->as_doj;
                }

                if($getBenefit != null){
                    
                    if($getEmployee->as_status_date != null){
                        $sDate = $getEmployee->as_status_date;
                        $sYearMonth = Carbon::parse($sDate)->format('Y-m');
                        $sDay = Carbon::parse($sDate)->format('d');


                        if($yearMonth == $sYearMonth){
                            $firstDateMonth = $getEmployee->as_status_date;
                            $totalDay = $this->totalDay - ((int) $sDay-1);

                            if($sDay > 1){
                                $partial = 1;
                            }
                        }
                    }

                    
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
                    
                    $lateCount = 0;
                    $halfCount = 0;
                    $presentOt = 0;
                    if(!isset($getPresentOT->present)){
                        $getPresentOT->present = 0;
                        $lateCount = $getPresentOT->late??0;
                        $halfCount = $getPresentOT->halfday??0;
                        $presentOt = $getPresentOT->ot??0;
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

                    
                    

                    // check OT roaster employee
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
                        $checkAtt = DB::table($this->tableName)
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
                        
                        if($empdojMonth == $yearMonth){
                            $query = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                                ->where('hr_yhp_dates_of_holidays','>=', $empdoj)
                                ->where('hr_yhp_open_status', 0);
                            if(count($rosterOtData) > 0){
                                $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                            }
                            $shiftHolidayCount = $query->count();
                        }else{
                            $query = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                                ->where('hr_yhp_open_status', 0);
                            if(count($rosterOtData) > 0){
                                $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                            }
                            $shiftHolidayCount = $query->count();
                        }
                        $shiftHolidayCount = $shiftHolidayCount + ($totalOt - $otDayCount);

                        if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                            $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
                        }else{
                            $getHoliday = $shiftHolidayCount;
                        }
                    }
                    $getHoliday = $getHoliday < 0 ? 0:$getHoliday;


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

                    if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $monthDayCount > $this->totalDay || $partial == 1){
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

                    if($getSalary == null){
                        $salary = [
                            'as_id' => $getEmployee->associate_id,
                            'ot_status' => $getEmployee->as_ot,
                            'unit_id' => $getEmployee->as_unit_id,
                            'designation_id' => $getEmployee->as_designation_id,
                            'sub_section_id' => $getEmployee->as_subsection_id,
                            'location_id' => $getEmployee->as_location,
                            'pay_type' => $getBenefit->bank_name,
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
                        HrMonthlySalary::insert($salary);
                    }else{
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
                            'present' => $getPresentOT->present,
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
                        HrMonthlySalary::where('id', $getSalary->id)->update($salary);
                    }
                }
            }
            return 'success';

        } catch (\Exception $e) {
            DB::table('error')->insert(['msg' => $this->asId.' '.$e->getMessage()]);
        }
    }
}
