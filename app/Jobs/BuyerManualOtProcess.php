<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\MOdels\Hr\Benefits;
use App\Models\Hr\AttendanceBonus;
use App\Models\Hr\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Jobs\BuyerAttandenceProcess;
use App\Models\Hr\SalaryAddDeduct;
use Carbon\Carbon;

use DB;

class BuyerManualOtProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     public $date;
     public $as_id;
     public $ot_hour;
    public function __construct($date,$as_id,$ot_hour)
    {
        $this->date = $date;
        $this->as_id = $as_id;
        $this->ot_hour = $ot_hour;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $templates = DB::table('hr_buyer_template')->get();

        foreach ($templates as $template) {
          $attTemplate = DB::table('hr_buyer_att')
                            ->select(
                              'hr_buyer_att_template.*'
                              )
                            ->where('hr_buyer_att.as_id',$this->as_id)
                            ->whereDate('hr_buyer_att_template.in_time',$this->date)
                            ->where('hr_buyer_att_template.hr_buyer_template_id',$template->id)
                            ->leftJoin('hr_buyer_att_template','hr_buyer_att.id','hr_buyer_att_template.hr_buyer_att_id')
                            ->first();
                            //dd($attTemplate);exit;
             if($attTemplate->ot_hour > $this->ot_hour){

                 if($attTemplate->out_time != '00:00:00'){
                   $expout = explode(' ',$attTemplate->out_time);

                   $expData = explode(':',$this->ot_hour);
                    if($expData[1] == '00'){
                        $outTimeInSeconds = $this->hoursToseconds($expout[1]);
                        $otHourInSeconds = $this->hoursToseconds($this->ot_hour);

                        $out_time = gmdate('H:i:s',($outTimeInSeconds - $otHourInSeconds));

                    }else{
                       if($expData[0] == '00'){
                         $templateotHourInSeconds = $this->hoursToseconds($attTemplate->ot_hour);
                         $outTimeInSeconds = $this->hoursToseconds($expout[1]);

                         $otHourInSeconds = $this->hoursToseconds($this->ot_hour);
                          // dd($expout);exit;
                         $out_time = gmdate('H:i:s',($outTimeInSeconds - $templateotHourInSeconds)+(60*rand(15,30)));

                       }else{
                         $templateotHourInSeconds = $this->hoursToseconds($attTemplate->ot_hour);
                         //$expout = explode(' ',$attTemplate->out_time);

                         $outTimeInSeconds = $this->hoursToseconds($expout[1]);

                         $otHourInSeconds = ((int)$expData[0]*60)*60;

                         $out_time = gmdate('H:i:s',($outTimeInSeconds - $templateotHourInSeconds)+$otHourInSeconds+(60*rand(15,30)));
                       }

                    }

                 }
               DB::table('hr_buyer_att_template')->where('id', $attTemplate->id)->update([
                 'out_time' => $this->date.' '.$out_time,
                 'ot_hour' => $this->ot_hour
               ]);


               $expDate = explode('-',$this->date);
               $expOt = explode(':',$this->ot_hour);
               $hTom = $expOt[0]*60;
               if($expOt[1] != '00'){
                 $forSalaryCalculation = $expOt[0].'5';
               }else{
                 $forSalaryCalculation = $expOt[0];
               }
               $otMinutes = $hTom + $expOt[1];
                $buyerSalaryTemId = DB::table('hr_monthly_salary')
                                      ->where('as_id',$this->as_id)
                                      ->where('month',$expDate[1])
                                      ->where('year',$expDate[0])
                                      ->where('hr_buyer_salary_template.hr_buyer_template_id',$template->id)
                                      ->leftJoin('hr_buyer_salary_template','hr_monthly_salary.id','hr_buyer_salary_template.hr_monthly_salary_id')
                                      ->first();
                                      //dd($buyerSalaryTemId);exit;
                $changedOt = $buyerSalaryTemId->ot_hour - $otMinutes;
                // $changedSalary = $buyerSalaryTemId->salary_payable -((float)$forSalaryCalculation * $buyerSalaryTemId->ot_rate);

                DB::table('hr_buyer_salary_template')
                  ->where('hr_monthly_salary_id', $buyerSalaryTemId->hr_monthly_salary_id)
                  ->where('hr_buyer_template_id',$template->id)
                  ->update([
                    'ot_hour' => $changedOt,
                    // 'salary_payable' => $changedSalary
                  ]);

             }

        }

    }
    public function hoursToseconds($inHour) {
        if($inHour) {
            list($hours,$minutes,$seconds) = array_pad(explode(':',$inHour),3,'00');
            sscanf($inHour, "%d:%d:%d", $hours, $minutes, $seconds);
            return isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
        }
    }
}
