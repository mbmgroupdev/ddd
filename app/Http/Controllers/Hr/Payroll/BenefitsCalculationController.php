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
use Carbon\Carbon;
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
            if(!empty($benefits)){

               $date2 = strtotime(date('Y-m-d', strtotime($benefits->status_date) ) );

               $details->already_given = 'yes';   
               $details->benefits = $benefits;   
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
            if(!empty($benefits)){
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

            /*$input = array(
                'associate' => $request->emp_id,
                'month_year' => date('Y-m-d')
            );*/
            $request->associate = $request->emp_id;
            $request->month_year = date('Y-m');

            $details->jobcard = $this->getEmpJobcard($request);

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

        return view('hr.common.job_card_layout', compact('request','attendance','info'));
    }

    /*get employee attendance table*/
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

    //Get earned leave
    public function earnedLeave($leaves, $as_id, $associate_id, $unit_id)
    {
        $table = $this->getTableName($unit_id);
        $leavesForEarned = collect($leaves)->sortBy('year');

            
        $earnedLeaves = [];
        if(count($leavesForEarned)>0){
            $remainEarned = 0;
            foreach($leavesForEarned AS $yearlyLeave){
                
                $attendance = DB::table($table)
                                ->where('a.as_id',$as_id)
                                ->whereYear('a.in_time', $yearlyLeave->year)
                                ->count();

                $earnedTotal = intval($attendance/18)+$remainEarned;
                

                $enjoyed = DB::table("hr_leave")
                            ->select(
                                DB::raw("
                                    SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS enjoyed
                                ")
                            )
                            ->where("leave_ass_id", $associate_id)
                            ->where("leave_status", "1")
                            ->where(DB::raw("YEAR(leave_from)"), '=', $yearlyLeave->year)
                            ->value("enjoyed");

                $remainEarned = $earnedTotal-$enjoyed;

                $earnedLeaves[$yearlyLeave->year]['remain'] = $remainEarned;
                $earnedLeaves[$yearlyLeave->year]['enjoyed'] = $enjoyed;
                $earnedLeaves[$yearlyLeave->year]['earned'] = $earnedTotal;

            }   
        }else{
            $yearAtt = DB::table($table)
                            ->select(DB::raw('count(as_id) as att'))
                            ->where('a.as_id',$as_id)
                            ->groupBy(DB::raw('Year(in_time)'))
                            ->first();
            //dd($yearAtt);
            $earnedTotal = 0;
            if($yearAtt!= null){
                foreach ($yearAtt as $key => $att) {
                    $earnedTotal += intval($att/18);    
                }
                
            }
            $earnedLeaves[date('Y')]['remain'] = $earnedTotal;
            $earnedLeaves[date('Y')]['enjoyed'] = 0;
            $earnedLeaves[date('Y')]['earned'] = $earnedTotal;
        }

        //Total the results
        $total_earned = 0;
        $total_enjoy  = 0;
        $total_remain = 0;
        foreach ($earnedLeaves as $el) {
        	$total_earned += $el['earned'];
        	$total_enjoy  += $el['enjoyed'];
        	$total_remain += $el['remain'];
        }

        $total_earnedLeaves['total_earned'] = $total_earned; 
        $total_earnedLeaves['total_enjoy']  = $total_enjoy;
        $total_earnedLeaves['total_remain'] = $total_remain;

        return $total_earnedLeaves;  
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

                $salary = $this->processPartialSalary($employee, $request->status_date, $status);

                $salary_page = view('hr.common.partial_salary_sheet', compact('salary','employee' ))->render();

                

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

        $per_day_basic = round($employee->ben_basic/30,2);
        $per_day_gross = round($employee->ben_current_salary/30,2);

        // calculate earn leave payment
        $earn_leave_payment = $earned_leave*$per_day_gross;

        // calculate service benefit
        $service_years = $years;
        $service_months = $months;
        if( 5 <= $service_years && $service_years < 10){
            if($service_months >= 11){
                $service_years++;
            }

            $service_benefit =  (14*$service_years)*$per_day_basic;
            $service_days =  (14*$service_years);
        }else if($service_years >= 10){
            if($service_months >= 11){
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
        }else if($request->benefit_on == 'on_dismiss') {
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
                if($months > 6 && $months < 11) {
                    $years += 0.5;
                }else if($months == 11){
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
        $first_day = Carbon::create($salary_date)->firstOfMonth()->format('Y-m-d');

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

        
        $yearMonth = $year.'-'.$month;
        $empdoj = $employee->as_doj;
        $empdojMonth = date('Y-m', strtotime($employee->as_doj));
        $empdojDay = date('d', strtotime($employee->as_doj));


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

        // get absent employee wise
        $getAbsent = DB::table('hr_absent')
            ->where('associate_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $salary_date)
            ->count();

        // get leave employee wise

        $leaveCount = DB::table('hr_leave')
        ->select(
            DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
        )
        ->where('leave_ass_id', $employee->associate_id)
        ->where('leave_from', '>=', $first_day)
        ->where('leave_to', '<=', $salary_date)
        ->first()->total??0;



        // get salary add deduct id form salary add deduct table
        $getAddDeduct = SalaryAddDeduct::
        where('associate_id', $employee->associate_id)
        ->where('month',  $month)
        ->where('year',  $year)
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
        $perDayBasic = round(($employee->ben_basic / 30),2);
        $perDayGross = round(($employee->ben_current_salary / 30),2);
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
            'emp_status' => $status,
            'stamp' => 0,
            'pay_status' => 1,
            'bank_payable' => 0,
            'tds' => 0
        ];
        
        

        $stamp = 0;
        
        // get salary payable calculation
        $salaryPayable = ceil(((($perDayGross*$salary_date) - ($getAbsentDeduct + ($deductCost)))));
        $totalPayable = ceil((($salaryPayable + ($overtime_rate*$overtimes))));
        
        $salary['total_payable'] = $totalPayable;
        $salary['cash_payable'] = $totalPayable;
        $salary['salary_payable'] = $salaryPayable;


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
        $salary['deduct'] = $deductCost;
        $salary['per_day_basic'] = $perDayBasic;
        $salary['per_day_gross'] = $perDayGross;
        $salary['salary_date'] = $salary_date;
        

        return $salary;
    }
    public function givenBenefitList(){
        $unitList = DB::table('hr_unit')->pluck('hr_unit_short_name')->toArray();
        return view('hr.payroll.given_benefits_list', compact('unitList'));
    }

    public function getGivenBenefitData(Request $request){
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
                        return 'Resign Benefits';
                    }
                    else if($data->benefit_on == '4'){
                        return 'Dismiss Benefits';
                    }
                    else if($data->benefit_on == '3'){
                        return 'Termination Benefits';
                    }
                    else if($data->benefit_on == '7'){
                        return 'Death Benefits';
                    }else if($data->benefit_on == '8'){
                        return 'Retirement Benefits';
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
                ->rawColumns(['benefit_on','total_amount'])
                ->toJson();
    }

    
}
