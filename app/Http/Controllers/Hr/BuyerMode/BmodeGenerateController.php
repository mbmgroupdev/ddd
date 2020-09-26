<?php

namespace App\Http\Controllers\Hr\BuyerMode;

use Illuminate\Http\Request;
use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
use App\MOdels\Hr\Benefits;
use App\Models\Hr\AttendanceBonus;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Jobs\BuyerAttandenceProcess;
use App\Jobs\BuyerSalaryProcess;
use App\Jobs\BuyerAttandenceInsertProcess;
use App\Models\Hr\SalaryAddDeduct;
use Carbon\Carbon;

use DB, PDF;

class BmodeGenerateController extends Controller
{

 public function generateBuyerMode(){

   $unit = DB::table('hr_unit')->pluck('hr_unit_name','hr_unit_id');
   return view("hr/buyermode/generate_buyer_mode",compact(
     "unit"
   ));
 }
 public function getEmployees(Request $request){


$employees = DB::table('hr_as_basic_info')->where('as_unit_id',$request->unit)->where('as_status',1)->get();
//dd($employees);exit;
  return $employees;
 }

 public function generateBuyerModeData1(Request $request)
 {
   $employees = json_decode($request->employees);
   // foreach ($employees as $employee) {
   //   //dd($employee);exit;
   //   // $result['associate'][] = $employee->associate_id;
   //   //
   //   // $result['unit'][] = $employee->as_unit_id;
   //   // $getLeave1 = DB::table('hr_leave')
   //   // ->select([
   //   //   \DB::raw('sum(DATEDIFF(leave_to, leave_from)+1) as count')
   //   // ])
   //   // ->where('leave_ass_id', '10G103178N')
   //   // ->whereYear('leave_from', '>=', 2019)
   //   // ->whereYear('leave_to', '<=', 2019)
   //   // ->whereMonth('leave_from', '>=', 04)
   //   // ->whereMonth('leave_to', '<=', 04)
   //   // ->where('leave_status', 1)
   //   // ->first();
   //   //
   //   // $getLeave[] = $getLeave1->count==null?0:$getLeave1->count;
   //
   // }
   return date('m', strtotime($request->month));
 }
  //generate buyer mode data
  public function generateBuyerModeData(Request $request){

    // for ($i=1; $i <=30 ; $i++) {
    //   $queue = (new BuyerAttandenceProcess($request->year.'-'.date('m', strtotime($request->month)).'-'.$i,$request->employees))
    //   ->delay(Carbon::now()->addSeconds(2));
    //   dispatch($queue);
    // }
    $employees = json_decode($request->employees);
    $getHoliday = DB::table("hr_yearly_holiday_planner")
            ->where('hr_yhp_unit', $employees[0]->as_unit_id)
            ->whereYear('hr_yhp_dates_of_holidays', $request->year)
            ->whereMonth('hr_yhp_dates_of_holidays', date('m', strtotime($request->month)))
            ->where('hr_yhp_open_status', 0)
            ->count();

    // dd($request->all());exit;

    // $request = new Benefits;

    //$request->date = $this->date;
    // $request->unit = $this->unitId;


    //dd($employees);exit;
     $data = [];
    foreach ($employees as $employee) {
      //dd($employee);exit;
      $request->associate = $employee->associate_id;

      $request->unit = $employee->as_unit_id;
      ///////////////////////////////////////////////
      $templates = DB::table('hr_buyer_template')->get();

      foreach ($templates as $template) {
        $request->buyer_template_id = $template->id;
        //get attendance for buyer mode templatewise
        $data[$request->associate][$template->id] = $this->employeeDataAll($request);
        //dd($results);exit;
        // $queue = (new BuyerAttandenceInsertProcess($results,$template->id,$request->associate,$request->unit))
        // ->delay(Carbon::now()->addSeconds(2));
        // dispatch($queue);
        // foreach ($results as $result) {
        //   $exists = DB::table('hr_buyer_att')->where('as_id',$request->associate)->first();
        //   if(empty($exists)){
        //     $hr_buyer_att_id = DB::table('hr_buyer_att')->insertGetId([
        //       'as_id' => $request->associate,
        //       'unit_id' => $request->unit
        //     ]);
        //
        //     DB::table('hr_buyer_att_template')->insert([
        //       'hr_buyer_att_id' => $hr_buyer_att_id,
        //       'hr_buyer_template_id' => $request->buyer_template_id,
        //       'in_time' => $result['date'].' '.$result['in_time'],
        //       'out_time' => $result['date'].' '.$result['out_time'],
        //       'late_status' => $result['present_status'] == "P(late)"?1:0,
        //       'present_status' => $result['present_status'],
        //       'ot_hour'=> isset($result['overtime_time'])?$result['overtime_time']:''
        //     ]);
        //   }else{
        //     DB::table('hr_buyer_att_template')->insert([
        //       'hr_buyer_att_id' => $exists->id,
        //       'hr_buyer_template_id' => $request->buyer_template_id,
        //       'in_time' => $result['date'].' '.$result['in_time'],
        //       'out_time' => $result['date'].' '.$result['out_time'],
        //       'late_status' => $result['present_status'] == "P(Late)"?1:0,
        //       'present_status' => $result['present_status'],
        //       'ot_hour'=> isset($result['overtime_time'])?$result['overtime_time']:''
        //     ]);
        //   }
        //   //calculate salary and save in db
        //   $this->generate_salary_day_wise($result,$request->associate,$request->buyer_template_id,$getHoliday);
        //
        // }

      }
      // dd($results);exit;
    }
     //dd($data[$request->associate]);exit;
     $queue = (new BuyerAttandenceInsertProcess($data,$request->unit))
     ->delay(Carbon::now()->addSeconds(2));
     dispatch($queue);

     $queue = (new BuyerSalaryProcess($data))
     ->delay(Carbon::now()->addSeconds(2));
     dispatch($queue);

  }


  //generate salary daywise
    public function generate_salary_day_wise($result,$associate_id,$templateId,$getHoliday){
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
              // $getHoliday = DB::table("hr_yearly_holiday_planner")
              //         ->where('hr_yhp_unit', $getEmployee->as_unit_id)
              //         ->whereYear('hr_yhp_dates_of_holidays', $year)
              //         ->whereMonth('hr_yhp_dates_of_holidays', $month)
              //         ->where('hr_yhp_open_status', 0)
              //         ->count();

              // get absent employee wise
              $getAbsent = DB::table('hr_absent')
              ->where('associate_id', $getEmployee->associate_id)
              ->whereMonth('date', '=', $month)
              ->whereYear('date', '=', $year)
              ->count();

              // get leave employee wise
              ### anik vai query
              // $getLeave = DB::table('hr_leave')
              // ->select([
              //   'hr_leave.*',
              //   \DB::raw('DATEDIFF(leave_from, leave_to) as count')
              // ])
              // ->where('leave_ass_id', $getEmployee->associate_id)
              // ->whereYear('leave_from', '>=', $year)
              // ->whereYear('leave_to', '<=', $year)
              // ->whereMonth('leave_from', '>=', $month)
              // ->whereMonth('leave_to', '<=', $month)
              // ->where('leave_status', 1)
              // ->get();

              // $leaveCount = 0;
              // if(count($getLeave) > 0){
              //     foreach ($getLeave as $leave) {
              //         $day = 1;
              //         $date = Carbon::parse($leave->leave_from);
              //         $now = Carbon::parse($leave->leave_to);
              //         $diff = $date->diffInDays($now);
              //         $leaveCount += $diff + $day;
              //     }
              // }
              ### rubel query
              $getLeave = DB::table('hr_leave')
              ->select([
                \DB::raw('sum(DATEDIFF(leave_to, leave_from)+1) as count')
              ])
              ->where('leave_ass_id', $getEmployee->associate_id)
              ->whereYear('leave_from', '>=', $year)
              ->whereYear('leave_to', '<=', $year)
              ->whereMonth('leave_from', '>=', $month)
              ->whereMonth('leave_to', '<=', $month)
              ->where('leave_status', 1)
              ->first();

              $leaveCount = $getLeave->count==null?0:$getLeave->count;
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

  public function track($associate = null, $unit = null, $startDate = null, $endDate = null,$templateId = null)
  {

   $attends   = false;
   $leaves    = false;
   $absents   = false;
   $lates     = false;
   $holidays  = -1;
   $holiday_comment= "";
   $overtimes = 0;
   $output_out= null;
   $output_in = null;
   $leave_type=null;
   $startDate = date("Y-m-d", strtotime($startDate));
   $endDate   = date("Y-m-d", strtotime($endDate));
   $totalDays = ((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24))+1;
   #---------------------------------------------------------------

       $today = $startDate;

       // check leave
       $leaveCheck = DB::table("hr_leave")->where('leave_ass_id', $associate)
           ->where(function ($q) use($today) {
                   $q->where('leave_from', '<=', $today);
                   $q->where('leave_to', '>=', $today);
               });



       if($leaveCheck->exists())
       {
         $leaveCheck= $leaveCheck->first();

           $leaves=true;

           $leave_type= $leaveCheck->leave_type;
       }
       else
       {
           // if today is a holiday
            // dd($today);exit;
            $holidayCheckbuyer = DB::table("hr_yearly_holiday_planner")
               ->where('hr_yhp_dates_of_holidays', $today)
               ->where('hr_yhp_unit', $unit)
                ->first();
                // dd($holidayCheckbuyer);exit;

            // if($holidayCheckbuyer->hr_buyer_mode_open_status != null){
            //    $holidayCheck
            // }else{
            //
            // }
           /////////////////////////



           $holidayCheck = DB::table("hr_yearly_holiday_planner")
               ->where('hr_yhp_dates_of_holidays', $today)
               ->where('hr_yhp_unit', $unit)
               ->whereNotIn('hr_yhp_open_status', [1]);

         if(!empty($holidayCheckbuyer->hr_buyer_mode_open_status)){
           $buyerModeTemStatus = explode(',',$holidayCheckbuyer->hr_buyer_mode_open_status);
           //dd($templateId);exit;
           foreach ($buyerModeTemStatus as $b) {
                $r = explode('-',$b);

                //dd($r);exit;
                $status = 0;
                if($r[0] == $templateId){
                  $status = $r[1];
                }else{
                  $status = $holidayCheckbuyer->hr_yhp_open_status;
                }

                // dd($status);exit;
           }
           //dd($status);exit;
           if((int)$status == 0 || (int)$status == 2)
           {
             $holidayCheckData = $holidayCheck->first();
               // check if open status = 0, then holiday
               //$holidayCheckData->hr_yhp_open_status = (int)$status;
               if ((int)$status == "0" )
               {

                   $holidays= 0;
                   $holiday_comment= $holidayCheckData->hr_yhp_comments;

               }
               else if((int)$status == "2")
               {
                   $holidays=2;
                   $tableName="";

                if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
                   $tableName="hr_attendance_mbm AS a";
                }

                else if($unit ==2){
                   $tableName="hr_attendance_ceil AS a";
                }

                else if($unit ==3){
                   $tableName="hr_attendance_aql AS a";
                }

                else if($unit ==6){
                   $tableName="hr_attendance_ho AS a";
                }

                else if($unit ==8){
                   $tableName="hr_attendance_cew AS a";
                }
                else{
                   $tableName="hr_attendance_mbm AS a";
                }
               // check attendance
               $attendCheck = DB::table($tableName)
                   ->select(
                       "a.*",
                       "s.hr_shift_start_time",
                       "s.hr_shift_end_time",
                       "s.hr_shift_break_time"
                   )
                   ->join("hr_as_basic_info AS b", function($join) {
                       $join->on("b.as_id", "=", "a.as_id");
                   })
                   ->leftJoin("hr_shift AS s", function($join) {
                       $join->on("s.hr_shift_code", "=", "a.hr_shift_code");
                   })
                   ->where('b.associate_id', $associate)
                   ->whereDate('a.in_time', '=', $today);

               $attendCheckData = $attendCheck->first();
                     //dd($attendCheckData);exit;
                   // check out time and exists then
                   // calculate OT = outtime - (sf_start+breack)
                   if($attendCheck->exists())
                   {
                       /*
                       * attendance intime & outtime
                       * if not empty outtime
                       * -- then outtime = outtime
                       * else if (outitme empty and intime > sf_start + 4 hours)
                       * -- then outime=intime and calculate ot
                       */
                       $shift_start_time = $attendCheckData->hr_shift_start_time;
                       $shift_end_time   = $attendCheckData->hr_shift_end_time;
                       $shift_break_time = $attendCheckData->hr_shift_break_time;
                       $output_in= $attendCheckData->in_time;
                       $output_out= $attendCheckData->out_time;
                       $overtimes = $attendCheckData->ot_hour;
                       $cIn = strtotime(date("H:i", strtotime($attendCheckData->in_time)));

                       $cOut = strtotime(date("H:i", strtotime($attendCheckData->out_time)));
                       $cShifStart = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_start_time)));
                       $cBreak = $attendCheckData->hr_shift_break_time*60;

                       // if (!empty($attendCheckData->out_time))
                       // {
                       //     $total_minutes = ($cOut - ($cShifStart+$cBreak))/60;
                       //     $minutes= ($total_minutes%60);
                       //     $ot_minute = $total_minutes-$minutes;
                       //     //round minutes
                       //     if($minutes>=13 && $minutes<43) $minutes= 30;
                       //     else if($minutes>=43) $minutes= 60;
                       //     else $minutes= 0;
                       //     if($ot_minute>=0)
                       //     $overtimes+=($ot_minute+$minutes);
                       // }
                       // else if (empty($attendCheckData->out_time) && ($cIn>($cShifStart+14399)) )
                       // {
                       //     $total_minutes= ($cIn - ($cShifStart+$cBreak))/60;
                       //     $minutes= ($total_minutes%60);
                       //     $ot_minute = $total_minutes-$minutes;
                       //     //round minutes
                       //     if($minutes>=13 && $minutes<43) $minutes= 30;
                       //     else if($minutes>=43) $minutes= 60;
                       //     else $minutes= 0;
                       //     if($ot_minute>=0)
                       //     $overtimes+=($ot_minute+$minutes);
                       // }
                   }
               }
           }
           else
           {

             //Check  holiday(general) only for showing in report
             // $generalHolidayCheck = DB::table("hr_yearly_holiday_planner")
             //     ->where('hr_yhp_dates_of_holidays', $today)
             //     ->where('hr_yhp_unit', $unit)
             //     ->where('hr_yhp_open_status', 1)
             //     ->exists();
              // if($generalHolidayCheck) {
               $holidays = 1;
              // }
          ////////////////////////
             $tableName="";

         if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
             $tableName="hr_attendance_mbm AS a";
         }

         else if($unit ==2){
             $tableName="hr_attendance_ceil AS a";
         }

         else if($unit ==3){
             $tableName="hr_attendance_aql AS a";
         }

         else if($unit ==6){
             $tableName="hr_attendance_ho AS a";
         }

         else if($unit ==8){
             $tableName="hr_attendance_cew AS a";
         }
         else{
             $tableName="hr_attendance_mbm AS a";
         }
             // check attendance
             $attendCheck = DB::table($tableName)
                 ->select(
                     "a.*",
                     "s.hr_shift_start_time",
                     "s.hr_shift_end_time",
                     "s.hr_shift_break_time",
                     "b.as_ot"
                 )
                 ->join("hr_as_basic_info AS b", function($join) {
                     $join->on("b.as_id", "=", "a.as_id");
                 })
                 ->leftJoin("hr_shift AS s", function($join) {
                     $join->on("s.hr_shift_code", "=", "a.hr_shift_code");
                 })
                 ->where('b.associate_id', $associate)
                 ->whereDate('a.in_time', '=', $today);

             $attendCheckData = $attendCheck->first();



               // calculate general time & att with overtime
               if($attendCheck->exists())
               {
                   $attends=true;
                   // -------------------------------------------------
                   $shift_start_time = $attendCheckData->hr_shift_start_time;
                   $shift_end_time   = $attendCheckData->hr_shift_end_time;
                   $shift_break_time = $attendCheckData->hr_shift_break_time;
                   $punch_in         = $attendCheckData->in_time;
                   $punch_out        = $attendCheckData->out_time;
                   $output_in= $attendCheckData->in_time;
                   $output_out= $attendCheckData->out_time;
                   // -------------------------------------------------
                   $cIn = strtotime(date("H:i", strtotime($attendCheckData->in_time)));
                   $cOut = strtotime(date("H:i", strtotime($attendCheckData->out_time)));
                   $cShifStart = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_start_time)));
                   $cShifEnd = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_end_time)));
                   $cBreak = $attendCheckData->hr_shift_break_time*60;
                   //------------------------------------
                   // Calculate INTIME & OUTTIME

                   $ot_manual = DB::table("hr_ot")
                       ->where("hr_ot_as_id",  $associate)
                       ->whereDate('hr_ot_date', '=', $today)
                       ->value("hr_ot_hour");

                   // if (!empty($ot_manual))
                   // {
                   //     $mot = explode('.',$ot_manual);
                   //     $moth = isset($mot[0])?$mot[0]:'00';
                   //     $motm = isset($mot[1])?'30':'00';
                   //     $overtimes = $moth.':'.$motm;
                   // }
                   // else
                   // {

                       if (empty($punch_out))
                       {
                           if ($cIn > ($cShifStart+$cBreak))
                           {
                               $cOut = $cIn;
                           }
                           else
                           {
                               $cOut = null;
                           }
                       }
                       else
                       {
                           if ($cOut < $cShifEnd+$cBreak)
                           {
                               $cOut = null;
                           }
                       }

                       // CALCULATE OVER TIME
                       if(!empty($cOut))
                       {
                           // $total_minutes = ($cOut - ($cShifEnd+$cBreak))/60;
                           // $minutes = ($total_minutes%60);
                           // $ot_minute = $total_minutes-$minutes;
                           // //round minutes
                           // if($minutes>=13 && $minutes<43) $minutes= 30;
                           // else if($minutes>=43) $minutes= 60;
                           // else $minutes= 0;
                           //
                           // if($ot_minute>=0)
                           $overtimes=$attendCheckData->ot_hour;
                       }else{
                         // $overtimes="0:00";
                       }
                   // }


                   // check shift time an in time
                   $late_time = ((strtotime(date('H:i:s',strtotime($punch_in))) - strtotime('TODAY')) - (strtotime(date('H:i:s', strtotime($shift_start_time))) - strtotime('TODAY')));

                   if($late_time > 180) // 3*60=180 seconds
                   {
                       $lates=true;
                   }
                   // -----------------------------
               }
               else
               {
                   $absents== true;
               }
           }
         }else{
           if($holidayCheck->exists())
           {
             $holidayCheckData = $holidayCheck->first();
               // check if open status = 0, then holiday
               if ($holidayCheckData->hr_yhp_open_status == "0" )
               {

                   $holidays= 0;
                   $holiday_comment= $holidayCheckData->hr_yhp_comments;

               }
               else if($holidayCheckData->hr_yhp_open_status == "2")
               {
                   $holidays=2;
                   $tableName="";

               if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
                   $tableName="hr_attendance_mbm AS a";
               }

               else if($unit ==2){
                   $tableName="hr_attendance_ceil AS a";
               }

               else if($unit ==3){
                   $tableName="hr_attendance_aql AS a";
               }

               else if($unit ==6){
                   $tableName="hr_attendance_ho AS a";
               }

               else if($unit ==8){
                   $tableName="hr_attendance_cew AS a";
               }
               else{
                   $tableName="hr_attendance_mbm AS a";
               }
               // check attendance
               $attendCheck = DB::table($tableName)
                   ->select(
                       "a.*",
                       "s.hr_shift_start_time",
                       "s.hr_shift_end_time",
                       "s.hr_shift_break_time"
                   )
                   ->join("hr_as_basic_info AS b", function($join) {
                       $join->on("b.as_id", "=", "a.as_id");
                   })
                   ->leftJoin("hr_shift AS s", function($join) {
                       $join->on("s.hr_shift_code", "=", "a.hr_shift_code");
                   })
                   ->where('b.associate_id', $associate)
                   ->whereDate('a.in_time', '=', $today);

               $attendCheckData = $attendCheck->first();

                   // check out time and exists then
                   // calculate OT = outtime - (sf_start+breack)
                   if($attendCheck->exists())
                   {
                       /*
                       * attendance intime & outtime
                       * if not empty outtime
                       * -- then outtime = outtime
                       * else if (outitme empty and intime > sf_start + 4 hours)
                       * -- then outime=intime and calculate ot
                       */
                       $shift_start_time = $attendCheckData->hr_shift_start_time;
                       $shift_end_time   = $attendCheckData->hr_shift_end_time;
                       $shift_break_time = $attendCheckData->hr_shift_break_time;
                       $output_in= $attendCheckData->in_time;
                       $output_out= $attendCheckData->out_time;
                       $cIn = strtotime(date("H:i", strtotime($attendCheckData->in_time)));

                       $cOut = strtotime(date("H:i", strtotime($attendCheckData->out_time)));
                       $cShifStart = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_start_time)));
                       $cBreak = $attendCheckData->hr_shift_break_time*60;
                       $overtimes = $attendCheckData->ot_hour;
                       // if (!empty($attendCheckData->out_time))
                       // {
                       //     $total_minutes = ($cOut - ($cShifStart+$cBreak))/60;
                       //     $minutes= ($total_minutes%60);
                       //     $ot_minute = $total_minutes-$minutes;
                       //     //round minutes
                       //     if($minutes>=13 && $minutes<43) $minutes= 30;
                       //     else if($minutes>=43) $minutes= 60;
                       //     else $minutes= 0;
                       //     if($ot_minute>=0)
                       //     $overtimes+=($ot_minute+$minutes);
                       // }
                       // else if (empty($attendCheckData->out_time) && ($cIn>($cShifStart+14399)) )
                       // {
                       //     $total_minutes= ($cIn - ($cShifStart+$cBreak))/60;
                       //     $minutes= ($total_minutes%60);
                       //     $ot_minute = $total_minutes-$minutes;
                       //     //round minutes
                       //     if($minutes>=13 && $minutes<43) $minutes= 30;
                       //     else if($minutes>=43) $minutes= 60;
                       //     else $minutes= 0;
                       //     if($ot_minute>=0)
                       //     $overtimes+=($ot_minute+$minutes);
                       // }


                   }
                  //  $h = floor((int)$overtimes/60) ? ((floor((int)$overtimes/60)<10)?("0".floor((int)$overtimes/60)):floor((int)$overtimes/60)) : '00';
                  // $m = (int)$overtimes%60 ? (((int)$overtimes%60<10)?("0".(int)$overtimes%60):((int)$overtimes%60)) : '00';
                  // $overtimes = $h.':'.$m;
                  // dd($overtimes);exit;
               }
           }
           else
           {

             //Check  holiday(general) only for showing in report
             $generalHolidayCheck = DB::table("hr_yearly_holiday_planner")
                 ->where('hr_yhp_dates_of_holidays', $today)
                 ->where('hr_yhp_unit', $unit)
                 ->where('hr_yhp_open_status', 1)
                 ->exists();
              if($generalHolidayCheck) {
               $holidays = 1;
              }
          ////////////////////////
             $tableName="";

         if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
             $tableName="hr_attendance_mbm AS a";
         }

         else if($unit ==2){
             $tableName="hr_attendance_ceil AS a";
         }

         else if($unit ==3){
             $tableName="hr_attendance_aql AS a";
         }

         else if($unit ==6){
             $tableName="hr_attendance_ho AS a";
         }

         else if($unit ==8){
             $tableName="hr_attendance_cew AS a";
         }
         else{
             $tableName="hr_attendance_mbm AS a";
         }
             // check attendance
             $attendCheck = DB::table($tableName)
                 ->select(
                     "a.*",
                     "s.hr_shift_start_time",
                     "s.hr_shift_end_time",
                     "s.hr_shift_break_time",
                     "b.as_ot"
                 )
                 ->join("hr_as_basic_info AS b", function($join) {
                     $join->on("b.as_id", "=", "a.as_id");
                 })
                 ->leftJoin("hr_shift AS s", function($join) {
                     $join->on("s.hr_shift_code", "=", "a.hr_shift_code");
                 })
                 ->where('b.associate_id', $associate)
                 ->whereDate('a.in_time', '=', $today);

             $attendCheckData = $attendCheck->first();



               // calculate general time & att with overtime
               if($attendCheck->exists())
               {
                   $attends=true;
                   // -------------------------------------------------
                   $shift_start_time = $attendCheckData->hr_shift_start_time;
                   $shift_end_time   = $attendCheckData->hr_shift_end_time;
                   $shift_break_time = $attendCheckData->hr_shift_break_time;
                   $punch_in         = $attendCheckData->in_time;
                   $punch_out        = $attendCheckData->out_time;
                   $output_in= $attendCheckData->in_time;
                   $output_out= $attendCheckData->out_time;
                   // -------------------------------------------------
                   $cIn = strtotime(date("H:i", strtotime($attendCheckData->in_time)));
                   $cOut = strtotime(date("H:i", strtotime($attendCheckData->out_time)));
                   $cShifStart = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_start_time)));
                   $cShifEnd = strtotime(date("H:i", strtotime($attendCheckData->hr_shift_end_time)));
                   $cBreak = $attendCheckData->hr_shift_break_time*60;
                   //------------------------------------
                   // Calculate INTIME & OUTTIME

                   $ot_manual = DB::table("hr_ot")
                       ->where("hr_ot_as_id",  $associate)
                       ->whereDate('hr_ot_date', '=', $today)
                       ->value("hr_ot_hour");

                   // if (!empty($ot_manual))
                   // {
                   //     $mot = explode('.',$ot_manual);
                   //     $moth = isset($mot[0])?$mot[0]:'00';
                   //     $motm = isset($mot[1])?'30':'00';
                   //     $overtimes = $moth.':'.$motm;
                   // }
                   // else
                   // {

                       if (empty($punch_out))
                       {
                           if ($cIn > ($cShifStart+$cBreak))
                           {
                               $cOut = $cIn;
                           }
                           else
                           {
                               $cOut = null;
                           }
                       }
                       else
                       {
                           if ($cOut < $cShifEnd+$cBreak)
                           {
                               $cOut = null;
                           }
                       }

                       // CALCULATE OVER TIME
                       if(!empty($cOut))
                       {
                           // $total_minutes = ($cOut - ($cShifEnd+$cBreak))/60;
                           // $minutes = ($total_minutes%60);
                           // $ot_minute = $total_minutes-$minutes;
                           // //round minutes
                           // if($minutes>=13 && $minutes<43) $minutes= 30;
                           // else if($minutes>=43) $minutes= 60;
                           // else $minutes= 0;
                           //
                           // if($ot_minute>=0)
                           $overtimes=$attendCheckData->ot_hour;
                       }else{
                         // $overtimes="0:00";
                       }
                   // }


                   // check shift time an in time
                   $late_time = ((strtotime(date('H:i:s',strtotime($punch_in))) - strtotime('TODAY')) - (strtotime(date('H:i:s', strtotime($shift_start_time))) - strtotime('TODAY')));

                   if($late_time > 180) // 3*60=180 seconds
                   {
                       $lates=true;
                   }
                   // -----------------------------
               }
               else
               {
                   $absents== true;
               }
           }
         }


     }
      if($holidays == "2"){
        $h = floor((int)$overtimes/60) ? ((floor((int)$overtimes/60)<10)?("0".floor((int)$overtimes/60)):floor((int)$overtimes/60)) : '00';
       $m = (int)$overtimes%60 ? (((int)$overtimes%60<10)?("0".(int)$overtimes%60):((int)$overtimes%60)) : '00';
      }else{
        $hm = explode(':',$overtimes);
        $h = isset($hm[0])?$hm[0]:0;
        $m = isset($hm[1])?$hm[1]:0;
      }
     // $h = floor($overtimes/60) ? ((floor($overtimes/60)<10)?("0".floor($overtimes/60)):floor($overtimes/60)) : '00';
     // $m = $overtimes%60 ? (($overtimes%60<10)?("0".$overtimes%60):($overtimes%60)) : '00';
      // dd($overtimes);exit;

       // $hm = explode(':',$overtimes);
       // $h = isset($hm[0])?$hm[0]:0;
       // $m = isset($hm[1])?$hm[1]:0;
   // 	if(date("j", strtotime($startDate))==7)
   // dd($leaves);

   return (object)array(
     'start_date' => $startDate,
     'end_date'   => $endDate,
     'total_days' => $totalDays,
     'associate' => $associate,
     'unit'      => $unit,
     'attends'   => $attends,
     'leaves'    => $leaves,
     'leave_type'=> $leave_type,
     'absents'   => $absents,
     'lates'     => $lates,
     'holidays'  => $holidays,
      'shift_start_time' =>isset($shift_start_time)?$shift_start_time:'',

      'shift_end_time'   =>   isset($shift_end_time)?$shift_end_time:'',
      'shift_break_time' =>isset($shift_break_time)?$shift_break_time:'',

     'holiday_comment'  => $holiday_comment,
     'in_time'  => (!empty($output_in))?date("H:i:s", strtotime($output_in)):null,
     'out_time'  => (!empty($output_out))?date("H:i:s", strtotime($output_out)):null,
     'overtime_minutes' => $overtimes,
     'overtime_time'    => (($overtimes>0)?"$h:$m:00":"")
   );

  }
  public function employeeDataAll($request){
       // if(isset($request->buyer_template_id)){
       //   if($request->buyer_template_id != null){
       //     $buyerTemplate = DB::table('hr_buyer_template_detail')->where('buyer_template_id',$request->buyer_template_id)->first();
       //   }else{
       //     $buyerTemplate = DB::table('hr_buyer_template_detail')->where('buyer_template_id',1)->first();
       //
       //   }
       // }else{
       //   if(auth()->user()->buyer_template_permission() != null){
       //     $buyerTemplate = DB::table('hr_buyer_template_detail')->where('buyer_template_id',auth()->user()->buyer_template_permission())->first();
       //   }else{
       //     $buyerTemplate = DB::table('hr_buyer_template_detail')->where('buyer_template_id',1)->first();
       //
       //   }
       // }
       $buyerTemplate = DB::table('hr_buyer_template_detail')->where('buyer_template_id',$request->buyer_template_id)->first();




        $total_attend   = 0;
        $total_overtime = 0;
        $associate = $request->associate;
        $tempdate= "01-".$request->month."-".$request->year;


        $month = date("m", strtotime($tempdate));
        $year  = $request->year;
        #------------------------------------------------------
        // ASSOCIATE INFORMATION
        $fetchUser = DB::table("hr_as_basic_info AS b")
            ->select(
              "b.associate_id AS associate",
              "b.as_name AS name",
              "b.as_doj AS doj",
              "b.as_ot",
              "u.hr_unit_id AS unit_id",
              "u.hr_unit_name AS unit",
              "s.hr_section_name AS section",
              "d.hr_designation_name AS designation"
            )
            ->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id")
            ->leftJoin("hr_section AS s", "s.hr_section_id", "=", "b.as_section_id")
            ->leftJoin("hr_designation AS d", "d.hr_designation_id", "=", "b.as_designation_id")
            ->where("b.associate_id", "=", $associate);

        if ($fetchUser->exists()) {
            $info = $fetchUser->first();
            $date       = ($year."-".$month."-"."01");
            //$date       = $request->date;
            $startDay   = date('Y-m-d', strtotime($date));
            $endDay     = date('Y-m-t', strtotime($date));
            $toDay      = date('Y-m-d');

            //dd($endDay);exit;
            //If end date is after current date then end day will be today
            if($endDay>$toDay) $endDay= $toDay;
            $totalDays  = (date('d', strtotime($endDay))-date('d', strtotime($startDay)));
            //total attends and total overtime
            //$totalDays  = 1;

            $total_ot_hours = 0;
            $total_attends    = 0;
            $x=1;
            $ttt = 0;
            $attendance=[];
            $k = 0;
            for($i=0; $i<=$totalDays; $i++) {
                $date       = ($year."-".$month."-".$x++);
                // $date       = $request->date;
                $startDay   = date('Y-m-d', strtotime($date));
                $templateId = $request->buyer_template_id;
                $data       = $this->track($info->associate, $info->unit_id, $startDay, $startDay,$templateId);
                // calculate ot range calculation

                //dd($data);exit;
                $total_hour = 0;
                if(!empty($data->in_time)){
                if(!empty($buyerTemplate->in_time_start_range)){
                       $intime_start = $buyerTemplate->in_time_start_range*60;

                       $shift_start_time_seceonds = $this->hoursToseconds($data->shift_start_time)-$intime_start;
                       $intime_in_seceonds = $this->hoursToseconds($data->in_time);
                       if($intime_in_seceonds < $shift_start_time_seceonds){
                          $data->in_time = gmdate('H:i:s',($shift_start_time_seceonds+(60*rand(0,(int)$buyerTemplate->in_time_start_range))));

                       }
                       elseif ($intime_in_seceonds > ($this->hoursToseconds($data->shift_start_time)+($buyerTemplate->in_time_end_range*60)))
                        {
                           if($intime_in_seceonds > ($this->hoursToseconds($data->shift_start_time)+(3*60))){
                             $data->in_time = gmdate('H:i:s',($this->hoursToseconds($data->shift_start_time)+(60*rand(4,(int)$buyerTemplate->in_time_end_range))));

                           }else{
                             $data->in_time = gmdate('H:i:s',($this->hoursToseconds($data->shift_start_time)+(60*rand(0,3))));

                           }
                       }
                      // dd(gmdate('H:i:s',$shift_start_time_seceonds));exit;
                      // dd()
                }
              }
              //$buyerTemplate->ot_hour = 2;
                if($data->overtime_time != '' && $data->holidays != 2){
                    $data->overtime_time = $data->overtime_time.':00';
                    list($o_time,$o_minutes,$o_seconds) = array_pad(explode(':',$data->overtime_time),3,'00');
                    if(!empty($buyerTemplate->ot_hour) || $buyerTemplate->ot_hour != 0) {
                        if($o_minutes != '00' || $o_seconds != '00') {
                            $data->overtime_time    = $o_time.':'.$o_minutes.':'.$o_seconds;
                        }
                        //dd($o_time);exit;
                        // $buyerTemplate->ot_hour =6;
                        if((int)$o_time >= (int)$buyerTemplate->ot_hour) {
                            // subtraction on out time
                            // convert hour to seconds
                            $ot_range_time_seconds  = ($buyerTemplate->ot_hour*60)*60;
                            $out_time_seconds       = $this->hoursToseconds($data->overtime_time)-$ot_range_time_seconds;
                            $final_out_time_seconds = $this->hoursToseconds($data->out_time)-$out_time_seconds;
                            // convert second to time

                            $outtime_start = $buyerTemplate->out_time_start_range*60;
                            $outtime_end = $buyerTemplate->out_time_end_range*60;
                            $shift_break_time =  $data->shift_break_time*60;
                            $outtime_start_time_seceonds = ($this->hoursToseconds($data->shift_end_time)+$ot_range_time_seconds+$shift_break_time)-$outtime_start;
                            $outtime_end_time_seceonds = ($this->hoursToseconds($data->shift_end_time)+$ot_range_time_seconds+$shift_break_time)+$outtime_end;

                            // dd(gmdate('H:i:s',$outtime_end_time_seceonds));exit;
                            // if($ot_range_time_seconds > $this->hoursToseconds($data->overtime_time) ){
                            //   $data->out_time = gmdate('H:i:s',$final_out_time_seconds);
                            //
                            // }else{
                              if($final_out_time_seconds < $outtime_start_time_seceonds){
                                $data->out_time = gmdate('H:i:s',($outtime_start_time_seceonds+(60*rand(0,(int)$buyerTemplate->out_time_start_range))));
                              }elseif ($final_out_time_seconds > $outtime_end_time_seceonds) {
                                $data->out_time = gmdate('H:i:s',($outtime_end_time_seceonds-(60*rand(0,(int)$buyerTemplate->out_time_end_range))));
                              }else{
                                $data->out_time = gmdate('H:i:s',$final_out_time_seconds);
                              }
                            // }

                            // update overtime

                            $ot_range_time          = '0'.$buyerTemplate->ot_hour.':00:00';
                            $data->overtime_time    = $ot_range_time;
                        }
                    }
                    // convert hour to seconds
                    list($total_hour,$second,$third) = array_pad(explode(':',$data->overtime_time),3,null);
                }

                // if(!empty($data->out_time)){
                //
                //
                // }
                //dd($data->overtime_time);exit;
                $t = strtotime($data->overtime_time);
                //dd($t);exit;
                $total_ot_hours += !empty($t)?$t:0;
                $attendance[$k]['date'] = $startDay;
                $attendance[$k]['in_time'] = !empty($data->in_time)?$data->in_time:null;
                $attendance[$k]['out_time'] = !empty($data->out_time)?$data->out_time:null;
                $attendance[$k]['overtime_time'] = (($info->as_ot==1)? $data->overtime_time:"");

                if($data->leaves== true){
                    $attendance[$k]['present_status']=$data->leave_type." Leave";
                } else if($data->holidays>=0) {
                    if($data->holidays==1) {
                      $attendance[$k]['present_status']="Weekend(General)";
                        $total_attends++;
                    }
                    else if($data->holidays==2){
                      $attendance[$k]['present_status']="Weekend(OT)";
                        $total_attends++;
                    }
                    else if($data->holidays==0)
                      $attendance[$k]['present_status']=$data->holiday_comment;
                } else {
                    if($data->attends){
                      if(strtotime($attendance[$k]['in_time']) >(strtotime($data->shift_start_time)+180)){
                          $attendance[$k]['present_status']="P(late)";
                          // $data[] = $employee; //'Present (Late)';
                      } else {
                        $attendance[$k]['present_status']="P";
                          // $data[] = $employee; //'Present';
                      }

                        $total_attends++;
                    }
                    else {
                      $attendance[$k]['present_status']="A";
                        //echo "A";
                    }
                }
                $startDay= date("Y-m-d", strtotime("$startDay +1 day"));

                $k++;
             }

             //dd($total_attends);exit;
              // $attendance['total_ot_hours'] = $total_ot_hours;
              //
              //dd($attendance);exit;
             return $attendance;
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
