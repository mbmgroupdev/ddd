<?php

namespace App\Http\Controllers\Hr\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Hr\Reports\JobCardController as JobCard;
use App\Models\Employee;
use App\Models\Hr\HrAllGivenBenefits;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\AttendanceBonus;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\YearlyHolyDay;
use App\Models\Hr\SalaryAdjustMaster;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use DB, Response, Auth, Exception, DataTables, Validator;

class BenefitsCalculationController extends Controller
{
    public function index(){
    	return view('hr.payroll.benefits');
    }
    
    public function associtaeSearch(Request $request)
    {

 
        $cantacces = [];
     
        $userIdNotAccessible = DB::table('roles')
               ->whereIn('name',$cantacces)
               ->leftJoin('model_has_roles','roles.id','model_has_roles.role_id')
               ->pluck('model_has_roles.model_id');


        $data = [];
        if($request->has('keyword'))
        {
            $search = $request->keyword;
            $data = Employee::select("associate_id", DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS associate_name'))
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                })
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereNotIn('as_id', auth()->user()->management_permissions())
                ->take(20)
                ->get();
        }

        return response()->json($data);
    }

    public function getEmployeeDetails(Request $request)
    {
    	try{
            
            $benefits = DB::table('hr_all_given_benefits')->where('associate_id', $request->emp_id)->first();


	    	$details = get_employee_by_id($request->emp_id);
            $employee = $details;
	        $date1 = strtotime($details->as_doj);
            $details->as_pic = emp_profile_picture($details);
            $details->date_join = $details->as_doj->format('Y-m-d');
            
            if($benefits || in_array($employee->as_status,[2,3,4,5,7,8])){

                if(date('t', strtotime($employee->as_status_date)) != 1){

                    $date2 = strtotime(date('Y-m-d', strtotime($employee->as_status_date)));

                    $details->already_given = 'yes';   
                    $details->benefits = $benefits;  
                    $month = date('m', strtotime($employee->as_status_date));
                    $year = date('Y', strtotime($employee->as_status_date));
                    $salary = HrMonthlySalary::
                        where('as_id', $employee->associate_id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->first();

                    $dateCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                    if($salary){
                        $salary = $salary->toArray();

                        $getAddDeduct = SalaryAddDeduct::
                            where('associate_id', $employee->associate_id)
                            ->where('month',  $month)
                            ->where('year',  $year)
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

                        $salary['salary_date'] = $salary['present'] + $salary['leave'] + $salary['absent'] + $salary['holiday'];
                        
                        $salary['per_day_basic'] = round(($salary['basic']/$dateCount),2);
                        $salary['per_day_gross'] = round(($salary['gross']/$dateCount),2);

                        
                        $salary['adjust'] = $salary['leave_adjust'] - $deductCost + $deductSalaryAdd + $salary['production_bonus'];

                      
                        $details->salary_page = view('hr.common.partial_salary_sheet', compact('salary','employee' ))->render();

                    }

                }

                
            }
            else{
	           $date2 = strtotime(date('Y-m-d'));

               $details->already_given = 'no';  
            }

	        $diff = abs($date2 - $date1);            
	        // To get the year divide the resultant date into 
	        $years = floor($diff / (365*60*60*24));  
	        // To get the month, subtract it with years and 
	        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  
	        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            if($benefits){
                $details->benefit = view('hr.common.end_of_job_final_pay', compact('employee','benefits','years','months'))->render();
            }
	        $details->service_years  = $years; 
	        $details->service_months = $months;
	        $details->service_days   = $days;

	        $earned = DB::table('hr_earned_leave')
                        ->select(DB::raw('sum(earned - enjoyed) as l'),'earned','enjoyed')
                        ->where('associate_id', $request->emp_id)
                        ->where('associate_id', $request->emp_id)
                        ->first();
            
            $details->remain = $earned->l??0;
            $details->earned = $earned->earned??0;
	        $details->enjoyed = $earned->lenjoyed??0;
            if(empty($benefits)){
                $input = [];
                $input['associate'] = $request->emp_id;
                $input['month_year'] = date('Y-m');
                $input = (object) $input;
                $att = $this->getEmpJobcard($input);
                $lastdate = $att['lastdate'];
                $details->effective_date = Carbon::parse($lastdate)->addDay()->toDateString();
                $jobcard = '<div class="alert alert-danger " role="alert" style="background-color: #fff5f4 !important;"><p class="iq-alert-text">Last working day of this employee  was <b> '.$att['lastdate'] .'</b>. Effective date will be <b>'.$details->effective_date.'</b> <br><span style="font-size:10px;">*Including holiday & leave</span></p></div>';
                $jobcard .= $att['jobcard'];
                $details->jobcard = $jobcard;
            }

	    	return Response::json($details);

    	}catch(\Exception $e){
    		return $e->getMessage();
    	}
    	

    }

    public function getEmpJobcard($request)
    {
        $jobcard = new JobCard();
        $result = $jobcard->empAttendanceByMonth($request);


        $attendance = $result['attendance'];
        $info = $result['info'];
        $joinExist = $result['joinExist'];
        $leftExist = $result['leftExist'];

        $filtered = Arr::where($attendance, function ($value, $key) {
            if($value['day_status'] == 'P'){
                return $value;
            }
        });




        
        

        if(empty($filtered)){
            $last_key = 0;
            $last_date = Carbon::parse($request->month_year)->subMonth()->lastOfMonth()->toDateString();
        }else{
            $last_key = array_key_last($filtered);
            $last_date = $attendance[$last_key]['date'];

            if(isset($attendance[$last_key+1])){
                if(($attendance[$last_key+1]['day_status']) == 'W'){
                    $last_date = Carbon::parse($last_date)->addDay()->toDateString();
                }
            }
        }
        

        $card = view('hr.common.job_card_layout_custom', compact('request','attendance','info','joinExist','leftExist'))->render();

        return array(
            'jobcard' => $card,
            'lastdate' => $last_date
        );
    }

   


    public function saveBenefits(Request $request)
    {
        try{
        	$validator= Validator::make($request->all(),[
                'associate_id'            => 'required',
                'benefit_on'              => 'required',
                'status_date'             => 'required|date'
            ]);
            if ($validator->fails())
            {
                return 'Please fillup all required fields!';

            }else{

                $employee = get_employee_by_id($request->associate_id);
                $diff = abs(strtotime($request->status_date) - strtotime($employee->as_doj));            
                // To get the year divide the resultant date into 
                $years = floor($diff / (365*60*60*24));  
                // To get the month, subtract it with years and 
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  

                $earned = DB::table('hr_earned_leave')
                        ->select(DB::raw('sum(earned - enjoyed) as l'))
                        ->where('associate_id', $request->associate_id)
                        ->where('associate_id', $request->associate_id)
                        ->first();

                $earned_leave = $earned->l??0;


                $data = $this->storeBenefit($employee, $years, $months, $earned_leave, $request);
                $benefits = $data['benefit'];
                $status = $data['status'];

                $benefit_page = view('hr.common.end_of_job_final_pay', compact('employee','benefits','years','months'))->render();
                $salary_date = Carbon::parse($request->status_date)->subDay();
                $month_last = $salary_date->copy()->endOfMonth()->toDateString();

                $lock['month'] = date('m', strtotime($salary_date));
                $lock['year'] = date('Y', strtotime($salary_date));
                $lock['unit_id'] = $employee->as_unit_id;
                $lockActivity = monthly_activity_close($lock);


                if($salary_date->toDateString() != $month_last && $lockActivity == 0){

                    $salary = $this->processPartialSalary($employee, $salary_date->toDateString(), $status);

                    $salary_page = view('hr.common.partial_salary_sheet', compact('salary','employee' ))->render();
                }else{

                    // remove current month salary
                    DB::table('hr_monthly_salary')
                        ->where('month',date('m'))
                        ->where('year', date('Y'))
                        ->where('as_id', $employee->associate_id)
                        ->delete();
                    $salary_page = '<div class="text-center alert alert-primary"><p class="iq-alert-text">Salary of this employee already recorded in active salary sheet!</p></div>';
                }

                

                return ['benefit' => $benefit_page, 'salary' => $salary_page, 'status' => 1];
            }


            
        }catch(\Exception $e){
            return $e;
        }
    }


    public function storeBenefit($employee, $years, $months, $earned_leave, $request)
    {

        $service_benefit = 0;
        $earn_leave_payment = 0;
        $subsistance_allowance = 0;
        $notice_pay = 0;
        $total_payment = 0;
        $service_days = 0;
        $death_days = 0;
        $termination_benefits = 0;
        $notice_pay_month = 0;
        $status_date = $request->status_date;

        $per_day_basic = round($employee->ben_basic/30,2);
        $per_day_gross = round($employee->ben_current_salary/30,2);

        // calculate earn leave payment
        $earn_leave_payment = $earned_leave*$per_day_gross;

        // calculate service benefit
        $service_years = $years;
        $service_months = $months;
        if( 5 <= $service_years && $service_years < 10){
            if($service_months >= 8){
                $service_years++;
            }

            $service_benefit =  (14*$service_years)*$per_day_basic;
            $service_days =  (14*$service_years);
        }else if($service_years >= 10){
            if($service_months >= 8){
                $service_years++;
            }
            $service_benefit =  (30*$service_years)*$per_day_basic;
            $service_days =  (30*$service_years);
        }

        if($request->benefit_on == 'on_resign'){
            if($request->notice_pay == 1){
                $notice_pay_month = 2;
                $notice_pay = 2*$employee->ben_basic;
            }
            $total_payment = $earn_leave_payment + $service_benefit - $notice_pay;
            $status = 2;
        }else if($request->benefit_on == 'on_left'){
            $notice_pay_month = 2;
            $notice_pay = 2*$employee->ben_basic;
            $total_payment = $earn_leave_payment + $service_benefit - $notice_pay;
            $status = 5;
            $status_date = date('Y-m-d');
        }
        else if($request->benefit_on == 'on_dismiss') {
            $subsistance_allowance = ($request->suspension_days*$per_day_basic)+1850;
            $total_payment = $earn_leave_payment + $subsistance_allowance;
            $status = 4;
        }
        else if($request->benefit_on == 'on_terminate') {
            if($request->notice_pay == 1){
                $notice_pay = 4*$employee->ben_basic;
                $notice_pay_month = 4;
            }
            $total_payment = $earn_leave_payment + $service_benefit + $notice_pay;
            $status = 3;
        }
        else if($request->benefit_on == 'on_death') {
            // death_benefit
            $death_benefit = 0;
            if($years >= 2){
                if($months > 4 && $months < 8) {
                    $years += 0.5;
                }else if($months > 8){
                    $years += 1;
                }
                if($request->death_reason == 'natural_death'){
                    $death_benefit = (30*$years)*$per_day_basic;
                    $death_days = (30*$years);

                }else if($request->death_reason  == 'duty_accidental_death'){
                    $death_benefit = (45*$years)*$per_day_basic;
                    $death_days = (45*$years);
                }
            }
            $status = 7;

            $total_payment = $earn_leave_payment + $service_benefit + $death_benefit;
        }else if($request->benefit_on == 'on_retirement') {
            $total_payment = $earn_leave_payment + $service_benefit;
            $status = 8;
        }

      
           
        $data = new HrAllGivenBenefits();
        $data->associate_id             = $request->associate_id;     
        $data->benefit_on               = $status;   
        $data->suspension_days          = $request->suspension_days??0;       
        $data->earn_leave_amount        = $earn_leave_payment;
        $data->service_days             = $service_days;
        $data->service_benefits         = round($service_benefit??0,2); 
        $data->subsistance_allowance    = round($subsistence_allowance??0,2); 
        $data->notice_pay_month         = $notice_pay_month; 
        $data->notice_pay               = round($notice_pay??0,2);
        $data->termination_benefits     = $termination_benefits??0;  
        $data->death_days               = round($death_days??0,2);  
        $data->death_reason             = $request->death_reason; 
        $data->death_benefits           = round($death_benefit??0,2);
        $data->status_date              = $request->status_date;
        $data->earned_leave             = $earned_leave??0;
        $data->created_by               = auth()->user()->id;
        $data->save();

        if($data){
            DB::table('hr_as_basic_info')
            ->where('associate_id', $request->associate_id)
            ->update([
                 'as_status' => $status,  
                 'as_status_date' => $request->status_date 
            ]);
        }

        return ['benefit' => $data, 'status' => $status];
    }

    public function processPartialSalary($employee, $salary_date, $status)
    {
        $month = date('m', strtotime($salary_date));
        $year = date('Y', strtotime($salary_date));
        $total_day = date('d', strtotime($salary_date));

        $yearMonth = $year.'-'.$month;
        $empdoj = $employee->as_doj;
        $empdojMonth = date('Y-m', strtotime($employee->as_doj));
        $empdojDay = date('d', strtotime($employee->as_doj));

        $first_day = Carbon::create($salary_date)->firstOfMonth()->format('Y-m-d');
        if($empdojMonth ==  $yearMonth){
            $first_day = $employee->as_doj;
            $total_day = $total_day - $empdojDay + 1;
        }




        $table = get_att_table($employee->as_unit_id);
        $att = DB::table($table)
                ->select(
                    DB::raw('COUNT(*) as present'),
                    DB::raw('SUM(ot_hour) as ot_hour'),
                    DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late')
                )
                ->where('as_id',$employee->as_id)
                ->where('in_date','>=',$first_day)
                ->where('in_date','<=', $salary_date)
                ->first();

        $late = $att->late??0;
        $overtimes = $att->ot_hour??0; 
        $present = $att->present??0;

        $getSalary = DB::table('hr_monthly_salary')
                    ->where([
                        'as_id' => $employee->associate_id,
                        'month' => $month,
                        'year' => $year
                    ])
                    ->first();

        // check OT roaster employee
        $rosterOTCount = HolidayRoaster::where('year', $year)
        ->where('month', $month)
        ->where('as_id', $employee->associate_id)
        ->where('date','>=', $first_day)
        ->where('date','<=', $salary_date)
        ->where('remarks', 'OT')
        ->get();
        $rosterOtData = $rosterOTCount->pluck('date');

        $otDayCount = 0;
        $totalOt = count($rosterOTCount);
        // return $rosterOTCount;
        foreach ($rosterOTCount as $ot) {
            $checkAtt = DB::table($this->tableName)
            ->where('as_id', $employee->as_id)
            ->where('in_date', $ot->date)
            ->first();
            if($checkAtt != null){
                $otDayCount += 1;
            }
        }

        if($employee->shift_roaster_status == 1){
            // check holiday roaster employee
            $getHoliday = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->where('remarks', 'Holiday')
            ->count();
            $getHoliday = $getHoliday + ($totalOt - $otDayCount);
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->where('remarks', 'Holiday')
            ->count();
            // check General roaster employee
            $RosterGeneralCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->where('remarks', 'General')
            ->count();
            
             // check holiday shift employee
            
            if($empdojMonth == $yearMonth){
                $query = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $salary_date)
                    ->where('hr_yhp_dates_of_holidays','>=', $empdoj)
                    ->where('hr_yhp_open_status', 0);
                if(count($rosterOtData) > 0){
                    $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
                $shiftHolidayCount = $query->count();
            }else{
                $query = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $salary_date)
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

        if($employee->shift_roaster_status == 1){
            // check holiday roaster employee
            $getHoliday = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->where('remarks', 'Holiday')
            ->count();
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->where('remarks', 'Holiday')
            ->count();
            // check General roaster employee
            $RosterGeneralCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->where('remarks', 'General')
            ->count();
             // check holiday shift employee
            
            if($empdojMonth == $yearMonth){
                $shiftHolidayCount = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $salary_date)
                    ->where('hr_yhp_dates_of_holidays','>=', $empdoj)
                    ->where('hr_yhp_open_status', 0)
                    ->count();
            }else{
                $shiftHolidayCount = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $salary_date)
                    ->where('hr_yhp_open_status', 0)
                    ->count();
            }
            
            if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
            }else{
                $getHoliday = $shiftHolidayCount;
            }
        }

        $getHoliday = $getHoliday < 0 ? 0:$getHoliday;

        
        // get leave employee wise

        $leaveCount = DB::table('hr_leave')
        ->select(
            DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
        )
        ->where('leave_ass_id', $employee->associate_id)
        ->where('leave_from', '>=', $first_day)
        ->where('leave_to', '<=', $salary_date)
        ->first()->total??0;

        // get absent employee wise
        $getAbsent = $total_day - ($present + $getHoliday + $leaveCount);
        if($getAbsent < 0){
            $getAbsent = 0;
        }

        // get salary add deduct id form salary add deduct table
        $getAddDeduct = SalaryAddDeduct::
        where('associate_id', $employee->associate_id)
        ->where('month',  $month)
        ->where('year',  $year)
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

        $dateCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        //get add absent deduct calculation
        $perDayBasic = round(($employee->ben_basic /  $dateCount),2);
        $perDayGross = round(($employee->ben_current_salary /  $dateCount),2);
        $getAbsentDeduct = $getAbsent * $perDayBasic;

        //stamp = 10 by default all employee;
        

        if($employee->as_ot == 1){
            $overtime_rate = number_format((($employee->ben_basic/208)*2), 2, ".", "");
        } else {
            $overtime_rate = 0;
        }
        $overtime_salary = 0;
        

        $attBonus = 0;
        $totalLate = $late;
        $salary_date = $present + $getHoliday + $leaveCount;
        
        $salary = [
            'as_id' => $employee->associate_id,
            'month' => $month,
            'year'  => $year,
            'gross' => $employee->ben_current_salary??0,
            'basic' => $employee->ben_basic??0,
            'house' => $employee->ben_house_rent??0,
            'medical' => $employee->ben_medical??0,
            'transport' => $employee->ben_transport??0,
            'food' => $employee->ben_food??0,
            'late_count' => $late,
            'present' => $present,
            'holiday' => $getHoliday,
            'absent' => $getAbsent,
            'leave' => $leaveCount,
            'absent_deduct' => $getAbsentDeduct,
            'salary_add_deduct_id' => $deductId,
            'ot_rate' => $overtime_rate,
            'ot_hour' => $overtimes,
            'attendance_bonus' => $attBonus,
            'production_bonus' => $productionBonus,
            'emp_status' => $status,
            'stamp' => 0,
            'pay_status' => 1,
            'bank_payable' => 0,
            'tds' => 0
        ];
        
        

        $stamp = 0;

        $salaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($employee->associate_id, $month, $year);
        $leaveAdjust = 0.00;
        if($salaryAdjust != null){
            if(isset($salaryAdjust->salary_adjust)){
                foreach ($salaryAdjust->salary_adjust as $leaveAd) {
                    $leaveAdjust += $leaveAd->amount;
                }
            }
        }

        $leaveAdjust = ceil((float)$leaveAdjust);
        
        // get salary payable calculation
        $salaryPayable = ceil(((($perDayGross*$total_day) - ($getAbsentDeduct + ($deductCost)))));
        $ot = ($overtime_rate*$overtimes);

        $totalPayable = ceil((float)($salaryPayable + $ot + $deductSalaryAdd  + $productionBonus + $leaveAdjust));
        
        $salary['total_payable'] = $totalPayable;
        $salary['cash_payable'] = $totalPayable;
        $salary['salary_payable'] = $salaryPayable;
        $salary['leave_adjust'] = $leaveAdjust;


        $getSalary = HrMonthlySalary::
                    where('as_id', $employee->associate_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

        if($getSalary == null){
            DB::table('hr_monthly_salary')->insert($salary);
        }else{
            DB::table('hr_monthly_salary')->where('id', $getSalary->id)->update($salary);  
        }
        $salary['adjust'] = $leaveAdjust - $deductCost + $deductSalaryAdd + $productionBonus;
        $salary['per_day_basic'] = $perDayBasic;
        $salary['per_day_gross'] = $perDayGross;
        $salary['salary_date'] = $total_day;
        

        return $salary;
    }

    public function givenBenefitList()
    {
        $unitList = DB::table('hr_unit')->pluck('hr_unit_short_name')->toArray();
        return view('hr.payroll.given_benefits_list', compact('unitList'));
    }

    public function getGivenBenefitData(Request $request)
    {
        $data = DB::table('hr_all_given_benefits as b')
                        ->select([
                            'b.*',
                            'c.as_name',
                            'd.hr_unit_short_name as unit_name'
                        ])
                        ->leftJoin('hr_as_basic_info as c', 'c.associate_id', 'b.associate_id')
                        ->leftJoin('hr_unit as d', 'd.hr_unit_id', 'c.as_unit_id')
                        ->whereIn('c.as_unit_id', auth()->user()->unit_permissions())
                        ->whereIn('c.as_location', auth()->user()->location_permissions())
                        ->orderBy('b.id', 'DESC')
                        ->get();

        // dd($data);exit;
        return DataTables::of($data)->addIndexColumn()
                ->editColumn('benefit_on', function($data){
                    if($data->benefit_on == '2'){
                        return 'Resign';
                    }else if($data->benefit_on == '5'){
                        return 'Left';
                    }
                    else if($data->benefit_on == '4'){
                        return 'Dismiss';
                    }
                    else if($data->benefit_on == '3'){
                        return 'Termination';
                    }
                    else if($data->benefit_on == '7'){
                        return 'Death';
                    }else if($data->benefit_on == '8'){
                        return 'Retirement';
                    }
                })
                ->addColumn('total_amount', function($data){
                    return $data->earn_leave_amount+
                            $data->service_benefits+
                            $data->subsistance_allowance+
                            $data->notice_pay+
                            $data->termination_benefits+
                            $data->death_benefits;
                })
                ->addColumn('action', function($data){
                    return "<a href=".url('hr/payroll/benefits?associate='.$data->associate_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"View\" style=\"margin-top:1px;\">
                        <i class=\" fa fa-eye bigger-120\"></i>";
                })
                ->rawColumns(['benefit_on','total_amount','action'])
                ->toJson();
    }

    
}
