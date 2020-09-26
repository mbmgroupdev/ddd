<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\MOdels\Hr\Benefits;
use App\Models\Hr\AttendanceBonus;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Jobs\BuyerAttandenceProcess;
use App\Models\Hr\SalaryAddDeduct;
use Carbon\Carbon;

use DB;

class BuyerSalaryProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     public $data;
     // public $associate_id;
     // public $templateId;
    public function __construct($data)
    {
        $this->data = $data;
       // $this->associate_id = $associate_id;
       // $this->templateId = $templateId;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

      $request = new Benefits;
      //
      // $request->date = $this->date;
      //$request->unit =$this->unitId;
      //
      // $employees = DB::table('hr_as_basic_info')->where('as_unit_id',$request->unit)->where('as_status',1)->get();

      // foreach ($employees as $employee) {
      //   //dd($employee);exit;
        // $request->associate = $this->associate_id;
      //
      //   $request->unit=$employee->as_unit_id;
      //   ///////////////////////////////////////////////
        //$templates = DB::table('hr_buyer_template')->get();

        // foreach ($templates as $template) {
          // $request->buyer_template_id = $this->templateId;
          //get attendance for buyer mode templatewise
          $results = $this->data;
          //dd($results);exit;
          //dd($result);exit;
          // $associate_id = $this->associate_id;
          // $templateId = $this->templateId;
          // $this->generate_salary_day_wise($results,$associate_id,$templateId);
          foreach ($results as $associate => $result) {
              foreach ($result as $templateId => $attDatas) {
                    //dd($attData);exit;
                    foreach ($attDatas as $key => $attData) {

                      //calculate salary and save in db
                      $this->generate_salary_day_wise($attData,$associate,$templateId);

                    }

              }

          //}
          // }
          }

         }


         //generate salary daywise
           public function generate_salary_day_wise($result,$associate_id,$templateId){
            //dd($result);exit;
               $mitutes=0;
             if(!empty($result['overtime_time'])){
               $ot_h_m = explode(':',$result['overtime_time']);
                 $mitutes = (int)$ot_h_m[0]*60;
                 $mitutes += (int)$ot_h_m[1];
             }

              if($result['present_status'] == "Weekend(OT)"){
                  if(!empty($result['in_time'])){
                    $present =1;
                  }else{
                    $present = 0;
                  }
              }

             $today       = $result['date'];
             $late        = $result['present_status'] == "P(Late)"?1:0;
             $overtimes   = $mitutes;
             $present     = $result['present_status'] == "P"?1:0;
             $getEmployee = Employee::where('associate_id', $associate_id)->first();
             if($getEmployee != null){
                 //  get benefit employee associate id wise
                 $getBenefit = Benefits::
                 where('ben_as_id', $getEmployee->associate_id)
                 ->first();
                 //  dd($getEmployee);exit;
                 if($getBenefit != null){
                     $year = Carbon::parse($today)->format('Y');
                     $month = Carbon::parse($today)->format('m');
                     // get exists check this month data employee wise
                     $getSalary = HrMonthlySalary::
                     where('as_id', $getEmployee->associate_id)
                     ->where('month', $month)
                     ->where('year', $year)
                     // ->leftJoin('hr_buyer_salary_template','id','hr_buyer_salary_template.hr_monthly_salary_id')
                     ->first();

                     //$getbuyerSalary = DB::table()

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
                     /*$date1 = strtotime(date("Y-m", strtotime($getEmployee->as_doj)));
                     $date2 = strtotime(date("Y-m", strtotime($today)));*/
                     $attBonus = 0;
                     if($getSalary != null){
                         $totalLate = $getSalary->late_count;
                     }else{
                         $totalLate = $late;
                     }
                     if ($totalLate <= 3 && $leaveCount <= 1 && $getEmployee->as_emp_type_id == 3) {
                         $lastMonth = Carbon::parse($today);
                         $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('n');
                         $getLastMonthSalary = HrMonthlySalary::
                               where('as_id', $getEmployee->associate_id)
                             ->where('month', $lastMonth)
                             ->where('year', $year)
                             //->leftJoin('hr_buyer_salary_template','id','hr_buyer_salary_template.hr_monthly_salary_id')
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
                     if($getSalary != null){
                       $get_buyer_salary = DB::table('hr_buyer_salary_template')->where('hr_monthly_salary_id', $getSalary->id)->where('hr_buyer_template_id',$templateId)->first();
                       if($get_buyer_salary == null){

                           $salary = [
                               // 'as_id' => $getEmployee->associate_id,
                               // 'month' => $month,
                               // 'year'  => $year,
                               // 'gross' => $getBenefit->ben_current_salary,
                               // 'basic' => $getBenefit->ben_basic,
                               // 'house' => $getBenefit->ben_house_rent,
                               // 'medical' => $getBenefit->ben_medical,
                               // 'transport' => $getBenefit->ben_transport,
                               // 'food' => $getBenefit->ben_food,
                               // 'late_count' => $late,
                               'hr_buyer_template_id' => $templateId,
                               'hr_monthly_salary_id'=> $getSalary->id,
                               'present' => $present,
                               'holiday' => $getHoliday,
                               'absent' => $getAbsent,
                               'leave' => $leaveCount,
                               'absent_deduct' => $getAbsentDeduct,
                               // 'salary_add_deduct_id' => $deductId,
                               'salary_payable' => $salaryPayable,
                               // 'ot_rate' => $overtime_rate,
                               'ot_hour' => $overtimes,
                               // 'attendance_bonus' => $attBonus,
                           ];
                           DB::table('hr_buyer_salary_template')->insert($salary);
                           // return $salary;
                           //dd($salary);exit;
                       }else{
                           $salary = [
                               // 'gross' => $getBenefit->ben_current_salary,
                               // 'basic' => $getBenefit->ben_basic,
                               // 'house' => $getBenefit->ben_house_rent,
                               // 'medical' => $getBenefit->ben_medical,
                               // 'transport' => $getBenefit->ben_transport,
                               // 'food' => $getBenefit->ben_food,
                               // 'late_count' => ($getSalary->late_count + $late),
                               // 'hr_monthly_salary_id'=> $getSalary->id,
                               'present' => ($get_buyer_salary->present + $present),
                               'holiday' => $getHoliday,
                               'absent' => $getAbsent,
                               'leave' => $leaveCount,
                               'absent_deduct' => $getAbsentDeduct,
                               'salary_payable' => $salaryPayable,
                               // 'ot_rate' => $overtime_rate,
                               'ot_hour' => ($get_buyer_salary->ot_hour + $overtimes),
                               // 'attendance_bonus' => $attBonus,
                           ];
                           //DB::table('hr_buyer_salary_template')->insert($salary);
                           DB::table('hr_buyer_salary_template')->where('hr_monthly_salary_id', $getSalary->id)->where('hr_buyer_template_id',$templateId)->update($salary);

                           // $getSalary->update($salary);
                           // return $salary;
                       }
                     }

                 }
             }
           }


         }
