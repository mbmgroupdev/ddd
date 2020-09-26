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
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;

use Carbon\Carbon;
use DB, PDF;
use Attendance;


class SalaryDayWiseSaveProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     public $requestData;
     // public $salary_range;
     // public $ot_range;
    public function __construct($requestData)
    {
        $this->requestData = $requestData;
        // $this->salary_range = $salary_range;
        // $this->ot_range = $ot_range;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       $requestdt = $this->requestData;
       //dd($this->requestData);exit;
        $results = $this->getSalaryDataDayWise($this->requestData);
        // dd($results[1]);exit;
       foreach ($results as $result)
       {

            $exist = DB::table('partial_salary')
                        ->where('as_id',$result['as_id'])
                        ->where('month',$result['month'])
                        ->where('year',$result['year'])
                        ->where('date_range',$requestdt['day_from'].'/'.$requestdt['day_to'])
                        ->where('ot_range',$requestdt['ot_from'].'/'.$requestdt['ot_to'])
                        ->first();
          if(!$exist){
            DB::table('partial_salary')->insert([
                "as_id" => $result['as_id'],
                "month" => $result['month'],
                "year" => $result['year'],
                "gross" => $result['gross'],
                "basic" => $result['basic'],
                "house" => $result['house'],
                "medical" => $result['medical'],
                "transport" => $result['transport'],
                "food" => $result['food'],
                "late_count" => $result['late_count'],
                "present" => $result['present'],
                "holiday" => $result['holiday'],
                "absent" => $result['absent'],
                "leave" => $result['leave'],
                "ot_rate" => $result['ot_rate'],
                "ot_hour" => $result['ot_hour'],
                "absent_deduct" => $result['absent_deduct'],
                "half_day_deduct" => $result['half_day_deduct'],
                "salary_add_deduct_id" => $result['salary_add_deduct_id'],
                "salary_payable" => $result['salary_payable'],
                "attendance_bonus" =>  $result['attendance_bonus'],
                "date_range" => $requestdt['day_from'].'/'.$requestdt['day_to'],
                "ot_range" => $requestdt['ot_from'].'/'.$requestdt['ot_to']

            ]);

          }




       }

    }
    public function getTableName($unit)
    {
        $tableName = "";
        //CEIL
        if($unit == 2){
           $tableName= "hr_attendance_ceil AS a";
        }
        //AQl
        else if($unit == 3){
            $tableName= "hr_attendance_aql AS a";
        }
        // MBM
        else if($unit == 1 || $unit == 4 || $unit == 5 || $unit == 9){
            $tableName= "hr_attendance_mbm AS a";
        }
        //HO
        else if($unit == 6){
            $tableName= "hr_attendance_ho AS a";
        }
        // CEW
        else if($unit == 8){
            $tableName= "hr_attendance_cew AS a";
        }
        else{
            $tableName= "hr_attendance_mbm AS a";
        }
        return $tableName;
    }

    public function getSalaryDataDayWise($request){
      // if($request->unit){
      //   dd("request");
      // }else{
      //   dd("Hi");
      // }
      // exit;
    //  dd($request->all());exit;
         // $cdate = date('Y-m');
         //  if($request->day > 0){
         //    $start_date = $cdate.'-1';
         //    $end_date = $cdate.'-'.($request->day+1);
         //  }else{
         //    $start_date = $cdate.'-'.$request->day;
         //    $end_date = $cdate.'-'.$request->day;
         //  }
          $start_date = $request['day_from'];
          $end_date = $request['day_to'];
          $ot_start = $request['ot_from'];
          $ot_end = $request['ot_to'];

          // $start_date = '2019-04-01';
          // $end_date = '2019-04-15';
          // $ot_start = '2019-04-01';
          // $ot_end = '2019-04-10';
         //dd($end_date);exit;
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',',');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',',');
        $data       = [];
      $work_days  = $this->workDays(
            $start_date,
            $end_date,
            $request['unit']
        );

            $employee_list   = $this->employeeInfo($request['unit'],$start_date);
            // return $employee_list;

          // dd($employee_list);exit;
        foreach($employee_list as $key=>$employee) {
            /*
            *--------------------------------------------------------------
            * ATTENDANCE
            *--------------------------------------------------------------
            */
            $startDate          = date("Y-m-d", strtotime($start_date));
            $endDate            = date("Y-m-d", strtotime($end_date));
            //$track              = Attendance::track($employee->associate, $employee->as_id, $employee->unit, $startDate, $endDate,$ot_start,$ot_end);
           //dd($employee);exit;
           ///////////////////////////////////////////////////////////////////
           // $expdate = explode('-',$startDate);
           // $yearMonth = $expdate[0].'-'.$expdate[1]
           $tableName = $this->getTableName($employee->unit);
           $totalDays = ((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24))+1;
           $getPresentOT = DB::table($tableName)
               ->select([
                   \DB::raw('count(as_id) as present'),
                   \DB::raw('SUM(ot_hour) as ot')

               ])
               ->where('as_id', $employee->as_id)
               ->whereDate('in_time','>=',$startDate)
               ->whereDate('in_time','<=',$endDate)
               ->first();

               $getOT = DB::table($tableName)
                   ->select([
                       \DB::raw('count(as_id) as present'),
                       \DB::raw('SUM(ot_hour) as ot')

                   ])
                   ->where('as_id', $employee->as_id)
                   ->whereDate('in_time','>=',$ot_start)
                   ->whereDate('in_time','<=',$ot_end)
                   ->first();

           if(!isset($getPresentOT->present)){
               $getPresentOT->present = 0;
           }

           if(!isset($getOT->ot)){
               $getOT->ot = 0;
           }

           $lateCount = DB::table($tableName)
               ->where('as_id', $employee->as_id)
               ->whereDate('in_time','>=',$startDate)
               ->whereDate('in_time','<=',$endDate)
               ->where('late_status', 1)
               ->count();

           $halfCount = DB::table($tableName)
               ->where('as_id', $employee->as_id)
               ->whereDate('in_time','>=',$startDate)
               ->whereDate('in_time','<=',$endDate)
               ->where('remarks', 'HD')
               ->count();

           // get holiday employee wise
           $holidayCount = DB::table("hr_yearly_holiday_planner")
                   ->where('hr_yhp_unit', $employee->unit)
                   // ->whereYear('hr_yhp_dates_of_holidays', $year)
                   // ->whereMonth('hr_yhp_dates_of_holidays', $month)
                   ->where('hr_yhp_dates_of_holidays','>=',$startDate)
                   ->where('hr_yhp_dates_of_holidays','<=',$endDate)
                   ->where('hr_yhp_open_status', 0)
                   ->count();
           // get absent employee wise
           $getAbsent = DB::table('hr_absent')
           ->where('associate_id', $employee->associate)
           ->where('date','>=',$startDate)
           ->where('date','<=',$endDate)
           ->count();

           // get leave employee wise
           $getLeave = DB::table('hr_leave')
           ->where('leave_ass_id', $employee->associate)
           ->where('leave_from', '<=',$startDate)
           ->where('leave_to', '>=',$endDate)
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

           $getHoliday = $totalDays - ($getPresentOT->present + $getAbsent + $leaveCount);
           if($getHoliday > $holidayCount){
               $getHoliday = $holidayCount;
           }
           /////////////////////////////////////////////////////////////////////

            $salary_add_deduct  = Attendance::salaryAddDeduct($employee->associate, $startDate);
            #---------------------------------------------------------------
            //dd($track);exit;
            $totalDays = $totalDays;
            $attends   = $getPresentOT->present;
            $leaves    = $leaveCount;
            $absents   = (($getPresentOT->present == 0) && ($getAbsent == 0))?$totalDays-($leaveCount+$holidayCount):$getAbsent;
            $lates     = $lateCount;
            $holidays  = $holidayCount;

            // dd($absents);exit;
            if($employee->as_ot == 1){
                $overtimes      = $getOT->ot;
                $overtime_time  = $getOT->ot;
            } else {
                $overtimes      = 0;
                $overtime_time  = null;
            }
            /*
            *--------------------------------------------------------------
            * Attendance Bonus
            *--------------------------------------------------------------
            */
            $bonusamount = DB::table('hr_attendance_bonus as a')->Select('a.*')
                            ->where('a.unit',$request['unit'])
                            ->where('a.status',1)
                            ->first();
            $present_bonous = 0;
            $date1 = strtotime(date("Y-m", strtotime($employee->doj)));
            $date2 = strtotime(date("Y-m", strtotime($startDate)));
            $lateAllow = 0;
            $leaveAllow = 0;
            $absentAllow = 0;
            //get unit wise bonus rules
            $getBonusRule = DB::table('hr_attendance_bonus_dynamic')
            ->where('unit_id', $request['unit'])
            ->first();

            //dd($getBonusRule);exit;
            $getAbsent = DB::table('hr_absent')
            ->where('associate_id', $employee->associate)
            ->whereMonth('date', '=', $startDate)
            ->whereYear('date', '=', $endDate)
            ->count();
            if($getBonusRule != null){
                $lateAllow = $getBonusRule->late_count;
                $leaveAllow = $getBonusRule->leave_count;
                $absentAllow = $getBonusRule->absent_count;
            }else{
                $lateAllow = 3;
                $leaveAllow = 1;
                $absentAllow = 1;
            }
            // $lastMonth = Carbon::now();
            // $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('n');
            // dd($lastMonth);exit;
            $expd = explode('-',$startDate);
            //dd((int)$expd[2]);exit;
            if (((int)$lates <= (int)$lateAllow) && ((int)$leaves <= (int)$leaveAllow) && ((int)$absents <= (int)$absentAllow) && ($employee->type == 3 ) && ((int)$expd[2] >= 16 )) {
                // if ($date1 == $date2) {
                //     if(isset($bonusamount->first_month)) {
                //         $present_bonous = ($bonusamount->first_month/30)*$totalDays;
                //     }
                // } else {
                //     if(isset($bonusamount->first_month)) {
                //         $present_bonous = ($bonusamount->first_month/30)*$totalDays;
                //     }
                // }
                $lastMonth = Carbon::now();
                $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('n');
                //dd($lastMonth);exit;
                if($lastMonth == '12'){
                    $year = $year - 1;
                }
                $getLastMonthSalary = HrMonthlySalary::
                    where('as_id', $employee->associate)
                    ->where('month', $lastMonth)
                    ->where('year', $year)
                    ->first();
                if (($getLastMonthSalary != null) && ($getLastMonthSalary->attendance_bonus > 0)) {
                    if(isset($bonusamount->from_2nd)) {
                        $present_bonous = $bonusamount->from_2nd;
                    }
                } else {
                    if(isset($bonusamount->first_month)) {
                        $present_bonous = $bonusamount->first_month;
                    }
                }
            }
            /*
            *--------------------------------------------------------------
            * EXPENSE & PAYMENT
            *--------------------------------------------------------------
            */
            $basic              = $employee->basic?$employee->basic:"0.00";
            $salary_absent      = $basic?number_format(($basic/30)*$absents, 2, ".", ""):"0.00";
            $salary_half_day    = "0.00";
            $salary_advance     = ($salary_add_deduct["advp_deduct"]/30)*$totalDays;
            $salary_product     = ($salary_add_deduct["cg_deduct"]/30)*$totalDays;
            $salary_food        = ($salary_add_deduct["food_deduct"]/30)*$totalDays;
            $salary_others      = ($salary_add_deduct["others_deduct"]/30)*$totalDays;
            $salary_stamp       = "10.00";

            /* TOTAL & NET PAY*/
            $gross_salary   = number_format(($employee->salary?$employee->salary:0), 2, ".", "");
            $getHalfDeduct = $halfCount * (($gross_salary/30) / 2);
            $salary_net     = number_format((($gross_salary/30)*($totalDays)-($salary_absent+$getHalfDeduct+$salary_half_day+$salary_product+$salary_advance+$salary_others+$salary_food)), 2, ".", "");
            if($employee->as_ot == 1){
                $overtime_rate   = number_format((($basic/208)*2), 2, ".", "");
            } else {
                $overtime_rate   = 0;
            }
             $e = explode('.',$overtimes);
             $eh = $e[0];
             $em = (isset($e[1]) && !empty($e[1]))?'30':'00';
             $overtimes = $eh+$em;

             //dd($overtimes);exit;
            $overtime_salary        = number_format($overtime_rate*($overtimes/60), 2, ".", "");
            $salary_advance_adjust  = ($salary_add_deduct["salary_add"]/30)*$totalDays;
            $total_pay              = number_format((($salary_net)-($salary_stamp)), 2, ".", "");


              // $data['local'][$key]['no']           = ($employee_list->perPage() * ($employee_list->currentPage()-1)) + ($key + 1);
              $data[$key]['as_id']          = $employee->associate;
              $data[$key]['month']          = date("m", strtotime($start_date)); // without 0 EX: 03 -> 3
              $data[$key]['year']           = date("Y", strtotime($end_date));
              $data[$key]['designation']  = $employee->designation;
              $data[$key]['name']         = $employee->name;
              $data[$key]['designation_grade']  = $employee->grade;
              $data[$key]['doj']  = $employee->doj;
              $data[$key]['gross']          = $this->nullCheck($gross_salary);
              $data[$key]['basic']          = $this->nullCheck($employee->basic);
              $data[$key]['house']          = $this->nullCheck($employee->house);
              $data[$key]['medical']        = $this->nullCheck($employee->medical);
              $data[$key]['transport']      = $this->nullCheck($employee->transport);
              $data[$key]['food']           = $this->nullCheck($employee->food);
              $data[$key]['late_count']     = $this->nullCheck($lates);
              $data[$key]['present']        = $this->nullCheck($attends);
              $data[$key]['holiday']        = $this->nullCheck($holidays);
              $data[$key]['absent']         = $this->nullCheck($absents);
              $data[$key]['leave']          = $this->nullCheck($leaves);
              $data[$key]['ot_rate']        = $this->nullCheck($overtime_rate);
              $data[$key]['ot_hour']        = $this->nullCheck($overtime_time);
              $data[$key]['absent_deduct']  = $this->nullCheck($salary_absent);
              $data[$key]['half_day_deduct']        = $this->nullCheck($salary_half_day);
              $data[$key]['salary_add_deduct_id']   = $salary_add_deduct["add_deduct_id"];
              $data[$key]['salary_payable']         = $this->nullCheck($total_pay);
              $data[$key]['attendance_bonus']       = (string)number_format($this->nullCheck($present_bonous),2, '.', ',');
              $data[$key]['overtime_salary']  = (string)number_format($overtime_salary,2, '.', ',');
              $data[$key]['salary_advance_adjust'] = (string)number_format($salary_advance_adjust,2, '.', ',');
              $data[$key]['salary_product']   = (string)number_format($salary_product,2, '.', ',');
              $data[$key]['salary_others']    = (string)number_format($salary_others,2, '.', ',');




                // $exist  = HrMonthlySalary::where(['as_id' => $data['as_id'], 'month' => $data['month'], 'year' => $data['year']])->first();
                // if(isset($exist->id)) {
                //     // update data if exist
                //     $data['updated_by'] = $user_id;
                //     $data['updated_at'] = date('Y-m-d');
                //     HrMonthlySalary::where('id', $exist->id)->update($data);
                // } else {
                //     // insert data if not exist
                //     $data['created_by'] = $user_id;
                //     $data['created_at'] = date('Y-m-d');
                //     HrMonthlySalary::insert($data);
                // }

        }

        //dd($data);exit;
        return $data;

    }

    public function nullCheck($value) {
        if($value == NULL) {
            $value = 0;
        }
        return $value;
    }

    // get total working days
    public function workDays($startDate = null, $endDate = null, $unit = null)
    {
        $startDate = date("Y-m-d", strtotime($startDate));
        $endDate   = date("Y-m-d", strtotime($endDate));
        $totalDays = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        $work_days = 0;
        #----------------------------------------------
        # Check Holiday with unit & month_year
        $total_holidays = DB::table("hr_yearly_holiday_planner")
            ->where("hr_yhp_unit", $unit)
            ->whereBetween("hr_yhp_dates_of_holidays", [$startDate, $endDate])
            ->count();

        $work_days = (($totalDays+1)-$total_holidays);
        return $work_days;
    }

    public function employeeInfo($unit = null,$salaryMonth=null)
    {

        $salaryMonth = date("Y-m", strtotime($salaryMonth));

        $result = DB::table("hr_as_basic_info AS b")
            ->select(
                "bd.hr_bn_associate_name AS name",
                "b.as_doj AS doj",
                'dg.hr_designation_name_bn AS designation',
                'dg.hr_designation_grade AS grade',
                "b.as_id",
                "b.as_ot",
                "b.temp_id",
                "b.associate_id AS associate",
                "b.as_emp_type_id AS type",
                "b.as_name",
                "b.as_unit_id AS unit",
                "ben.ben_current_salary AS salary",
                "ben.ben_basic AS basic",
                "ben.ben_house_rent AS house",
                "ben.ben_medical AS medical",
                "ben.ben_transport AS transport",
                "ben.ben_food AS food"
            )
            ->leftJoin("hr_employee_bengali AS bd", "bd.hr_bn_associate_id", "=", "b.associate_id")
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_benefits AS ben', function($join){
                $join->on('ben.ben_as_id', '=', 'b.associate_id');
                $join->where('ben.ben_status', '=', 1);
            })
            ->where(function($c) use($unit){
                $c->where("b.as_unit_id", $unit);

            })
            ->where(DB::raw("DATE_FORMAT(b.as_doj, '%Y-%m')"), "<=", $salaryMonth)
            // ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            // ->whereNotIn('b.associate_id',$asIds)
            ->whereIn('b.as_status',[1,5]); // checking status
            // if($paginate != null) {
            //   return $result->paginate(25);
            // } else {
            //   return $result->get()->toArray();
            // }
            return $result->get()->toArray();
    }

}
