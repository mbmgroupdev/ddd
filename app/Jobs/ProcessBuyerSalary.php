<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
Use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use App\Models\Employee;

use DB;


class ProcessBuyerSalary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 500;
    public $buyer;
    public $month;
    public $year;
    public $employees;
    public $attTable;
    public $salaryTable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($buyer, $month, $year, $employees)
    {
        $this->buyer = $buyer;
        $this->month = $month;
        $this->year  = $year;
        $this->employees  = $employees;
        $this->attTable  = 'hr_buyer_att_'.$buyer->table_alias;
        $this->salaryTable  = 'hr_buyer_salary_'.$buyer->table_alias;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $yearMonth = $this->year.'-'.$this->month;
        

        foreach ($this->employees as $key => $as_id) {
            try {

                $monthDayCount = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
                $partial = 0;
                $start_date = date($yearMonth.'-01');
                $getEmployee = Employee::where('as_id', $as_id)->first();

                if($getEmployee){

                    $empdoj = $getEmployee->as_doj;
                    $empdojMonth = date('Y-m', strtotime($getEmployee->as_doj));
                    
                    $empdojDay = date('d', strtotime($getEmployee->as_doj));

                    $monthlySalary = DB::table('hr_monthly_salary')
                                    ->where([
                                        'as_id' => $getEmployee->associate_id,
                                        'month' => $this->month,
                                        'year'  => $this->year
                                    ])->first();

                    if($monthlySalary){
                        
                        if($yearMonth == date('Y-m')){
                            $maxDay = date('d');
                            $end_date = date('Y-m-d');
                            $partial = 1;
                        }else{
                            $maxDay = $monthDayCount;
                            $end_date = date('Y-m-t', strtotime($start_date));
                            $partial = 0;
                        }

                        if($getEmployee->as_status_date){
                            $statusMonth = date('Y-m', strtotime($getEmployee->as_status_date));
                            if($statusMonth == $yearMonth){
                                
                                if(in_array($getEmployee->as_status, [2,3,4,7] )){
                                    $maxDay = date('d', strtotime($getEmployee->as_status_date));
                                    $end_date = $getEmployee->as_status_date;
                                    $partial = 1;
                                }else if($getEmployee->as_status == 5){
                                    $salary_date = DB::table('hr_all_given_benefits')
                                                    ->where('associate_id', $getEmployee->associate_id)
                                                    ->first()
                                                    ->salary_date;
                                    if(!$salary_date){
                                        $salary_date = $getEmployee->as_status_date;
                                    }
                                    $maxDay = date('d', strtotime($salary_date));
                                    $end_date = $salary_date;
                                    $partial = 1;
                                }else if($getEmployee->as_status == 1 && $getEmployee->as_status_date != null){
                                    $maxDay = $maxDay - ((date('d', strtotime($getEmployee->as_status_date))) + 1);

                                    $start_date = $getEmployee->as_status_date;
                                    $partial = 1;
                                }
                            }
                        }
                        
                        if($empdojMonth == $yearMonth){
                            $salary_date = $getEmployee->as_doj;
                            $maxDay = $maxDay - $empdojDay + 1;
                        }

                        if($start_date > $end_date){
                           $end_date = $start_date;
                        }

                        $att = DB::table($this->attTable)
                                ->select(
                                    DB::raw('SUM(ot_hour) as ot'),
                                    DB::raw('COUNT(*) as days'),
                                    DB::raw('COUNT(CASE WHEN att_status = "p" THEN 1 END) AS present'),
                                    DB::raw('COUNT(CASE WHEN att_status = "a" THEN 1 END) AS absent'),

                                    DB::raw('COUNT(CASE WHEN att_status = "l" THEN 1 END) AS leaves'),

                                    DB::raw('COUNT(CASE WHEN att_status = "h" THEN 1 END) AS holiday'),
                                    DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                                    DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')
                                )
                                ->where('in_date', '>=', $start_date)
                                ->where('in_date', '<=', $end_date)
                                ->where('as_id', $as_id)
                                ->first();

                        $present = $att->present ?? 0;
                        $leave = $att->leaves ?? 0;
                        $holiday = $att->holiday ?? 0;
                        $absent = $maxDay - ($present + $leave + $holiday);

                        $ot_hour = 0;
                        $ot_num_min = min_to_ot();

                        if($att->ot > 0){
                            $otfm = explode(".", $att->ot);

                            if(isset($otfm[1])){
                                $ot_min = round((('0.'.$otfm[1]) * 60));
                                $ot_hour = $otfm[0] + ($ot_min == 1? 1:($ot_num_min[$ot_min]));
                            }else{
                                $ot_hour = $att->ot;
                            }
                        }

                        $adv_deduct = 0;
                        $cg_deduct = 0;
                        $food_deduct = 0;
                        $others_deduct = 0;
                        $salary_add = 0;
                        $bonus_add = 0;
                        $deductCost = 0;
                        $productionBonus = 0;

                        $getAddDeduct = DB::table('hr_salary_add_deduct')
                            ->where('associate_id', $getEmployee->associate_id)
                            ->where('month', '=', $this->month)
                            ->where('year', '=', $this->year)
                            ->first();

                        if($getAddDeduct != null){
                            $advp_deduct = $getAddDeduct->advp_deduct;
                            $cg_deduct = $getAddDeduct->cg_deduct;
                            $food_deduct = $getAddDeduct->food_deduct;
                            $others_deduct = $getAddDeduct->others_deduct;
                            $salary_add = $getAddDeduct->salary_add;

                            $deductCost = ($advp_deduct + $cg_deduct + $food_deduct + $others_deduct);
                            $productionBonus = $getAddDeduct->bonus_add;
                        }


                        //get add absent deduct calculation
                        $perDayBasic = $monthlySalary->basic / 30;
                        $getAbsentDeduct = (int)($absent * $perDayBasic);
                        $getHalfDeduct = (int)($att->halfday * ($perDayBasic / 2));


                        /*
                         *get unit wise bonus rules 
                         *if employee joined this month, employee will get bonus 
                          only he/she joined at 1
                        */ 
                        $attBonus = 0;
                        if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $partial == 1 ){
                            $attBonus = 0;
                        }else{
                            
                            $getBonusRule = DB::table('hr_attendance_bonus_dynamic')
                                ->where('unit_id', $monthlySalary->unit_id)
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
                            
                            if ($att->late <= $lateAllow && $leave <= $leaveAllow && $absent <= $absentAllow && $getEmployee->as_emp_type_id == 3) {

                                $lastMonth = Carbon::parse($start_date)->subMonth();
                                $l_month = $lastMonth->copy()->format('n');
                                $l_year = $lastMonth->copy()->format('Y');

                                $getLastMonthSalary = DB::table($this->salaryTable)
                                                        ->where('as_id', $as_id)
                                                        ->where('month', $l_month)
                                                        ->where('year', $l_year)
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

                        if($monthlySalary->ot_status == 1){
                            $overtime_rate = number_format((($monthlySalary->basic/208)*2), 2, ".", "");
                        } else {
                            $overtime_rate = 0;
                        }

                        if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1)  || $partial == 1){
                            $perDayGross   = $monthlySalary->gross/$monthDayCount;
                            $totalGrossPay = ($perDayGross * $maxDay);
                            $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $monthlySalary->stamp);
                        }else{
                            $salaryPayable = $monthlySalary->gross - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $monthlySalary->stamp);
                        }

                        $ot = ((float)($overtime_rate) * ($ot_hour));
                        
                        $totalPayable = ceil((float)($salaryPayable + $ot + $salary_add + $bonus_add + $attBonus + $productionBonus + $monthlySalary->leave_adjust));

                        $tds = $monthlySalary->tds??0;
                        if($monthlySalary->pay_status == 1){
                            $tds = 0;
                            $cashPayable = $totalPayable;
                            $bankPayable = 0; 
                        }elseif($monthlySalary->pay_status == 2){
                            $cashPayable = 0;
                            $bankPayable = $totalPayable;
                        }else{
                            if($monthlySalary->bank_payable <= $totalPayable){
                                $cashPayable = $totalPayable - $monthlySalary->bank_payable;
                                $bankPayable = $monthlySalary->bank_payable;
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


                        $getSalary = DB::table($this->salaryTable)
                                        ->where([
                                            'as_id' => $as_id,
                                            'month' => $this->month,
                                            'year'  => $this->year
                                        ])->first();

                        $salary = [
                            'gross' => $monthlySalary->gross,
                            'basic' => $monthlySalary->basic,
                            'house' => $monthlySalary->house,
                            'medical' => $monthlySalary->medical,
                            'transport' => $monthlySalary->transport,
                            'food' => $monthlySalary->food,
                            'late_count' => $att->late,
                            'present' => $present,
                            'holiday' => $holiday,
                            'absent' => $absent,
                            'leave' => $leave,
                            'absent_deduct' => $getAbsentDeduct,
                            'half_day_deduct' => $getHalfDeduct,
                            'adv_deduct' => $adv_deduct,
                            'cg_deduct' => $cg_deduct,
                            'food_deduct' => $food_deduct,
                            'others_deduct' => $others_deduct,
                            'salary_add' => $salary_add,
                            'bonus_add' => $bonus_add,
                            'leave_adjust' => $monthlySalary->leave_adjust,
                            'ot_rate' => $overtime_rate,
                            'ot_hour' => $ot_hour,
                            'attendance_bonus' => $attBonus,
                            'production_bonus' => $productionBonus,
                            'stamp' => $monthlySalary->stamp,
                            'salary_payable' => $salaryPayable,
                            'total_payable' => $totalPayable,
                            'cash_payable' => $cashPayable,
                            'bank_payable' => $bankPayable,
                            'tds' => $tds,
                            'pay_status' => $monthlySalary->pay_status,
                            'pay_type' => $monthlySalary->pay_type,
                            'emp_status' => $monthlySalary->emp_status,
                            'ot_status' => $monthlySalary->ot_status,
                            'designation_id' => $monthlySalary->designation_id,
                            'subsection_id' => $monthlySalary->sub_section_id,
                            'location_id' => $monthlySalary->location_id,
                            'unit_id' => $monthlySalary->unit_id,
                            'created_by' => auth()->id()
                        ];



                        if($getSalary){
                            DB::table($this->salaryTable)->where('id', $getSalary->id)->update($salary);
                        }else{
                            $salary['as_id'] = $getEmployee->as_id;
                            $salary['month'] = $this->month;
                            $salary['year']  = $this->year;
                            DB::table($this->salaryTable)->insert($salary);
                        }
                        
                        DB::table('error')->insert(['msg' => $as_id.' '.$present]);
                    }

                }
            } catch (\Exception $e) {
                DB::table('error')->insert(['msg' => $as_id.' '.$e->getMessage()]);
            }
        }


        return 'success';

    }
}
