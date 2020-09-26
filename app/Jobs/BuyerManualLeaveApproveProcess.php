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

class BuyerManualLeaveApproveProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     public $leave_type;
     public $startDate;
     public $endDate;
     public $as_id;
    public function __construct($leave_type,$startDate,$endDate,$as_id)
    {
        $this->leave_type = $leave_type;
        $this->startDate  = $startDate;
        $this->endDate    = $endDate;
        $this->as_id      = $as_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

      $templates = DB::table('hr_buyer_template')->get();

      $getBenefit = Benefits::
      where('ben_as_id', $this->as_id)
      ->first();

        foreach ($templates as $template) {
          $attData = DB::table('hr_buyer_att')
                            // ->select(
                            //   'hr_buyer_att_template.*'
                            //   )
                            ->where('hr_buyer_att.as_id',$this->as_id)
                            ->whereDate('hr_buyer_att_template.in_time','>=',$this->startDate)
                            ->whereDate('hr_buyer_att_template.in_time','<=',$this->endDate)
                            // ->whereBetween('hr_buyer_att_template.in_time', array($this->startDate, $this->endDate))
                            ->where('hr_buyer_att_template.hr_buyer_template_id',$template->id)
                            ->leftJoin('hr_buyer_att_template','hr_buyer_att.id','hr_buyer_att_template.hr_buyer_att_id')
                            ->get();
              $totalDays = count($attData);
              //dd($attData);exit;
          foreach ($attData as $att) {
               if($att->present_status == 'A'){
                 DB::table('hr_buyer_att_template')->where('id',$att->id)->update([
                    'present_status' => $this->leave_type.' Leave'
                 ]);
               }


          }
          $expDate = explode('-',$this->startDate);
          $buyerSalaryTemId = DB::table('hr_monthly_salary')
                                ->where('as_id',$this->as_id)
                                ->where('month',$expDate[1])
                                ->where('year',$expDate[0])
                                ->where('hr_buyer_salary_template.hr_buyer_template_id',$template->id)
                                ->leftJoin('hr_buyer_salary_template','hr_monthly_salary.id','hr_buyer_salary_template.hr_monthly_salary_id')
                                ->first();
              $leave =  $buyerSalaryTemId->leave + $totalDays;
              $absent = $buyerSalaryTemId->absent - $totalDays;

              $perDayBasic = $getBenefit->ben_basic / 30;
              $getAbsentDeduct = $totalDays * $perDayBasic;

              $absent_deduct = $buyerSalaryTemId->absent_deduct - $getAbsentDeduct;
              $salarypayable = $buyerSalaryTemId->absent_deduct +$getAbsentDeduct;

              DB::table('hr_buyer_salary_template')
                ->where('hr_monthly_salary_id', $buyerSalaryTemId->hr_monthly_salary_id)
                ->where('hr_buyer_template_id',$template->id)
                ->update([
                  'leave'=>$leave,
                  'absent'=>$absent,
                  'absent_deduct' => $absent_deduct,
                  'salary_payable' => $salarypayable
                ]);
        }
    }
}
