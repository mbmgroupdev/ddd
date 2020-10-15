<?php

namespace App\Http\Controllers\Hr\Training;

use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Training;
use App\Models\Hr\TrainingNames;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB, Validator, DataTables, ACL;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
	// Show Add Training Form
    public function showForm()
    {
        $today = '2020-10';
        $as_id = 9071;
        $getTotalDay = date('d');
        $tableName = 'hr_attendance_mbm';
        $getEmployee = Employee::where('as_id', $as_id)->first();
        $year = date('Y', strtotime($today));
        $month = date('m', strtotime($today));
        $yearMonth = $year.'-'.$month;
        $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
        $monthDayCount  = Carbon::parse($today)->daysInMonth;
        if($monthDayCount > $getTotalDay){
            $lastDateMonth = $yearMonth.'-'.$getTotalDay;
        }else{
            $lastDateMonth = Carbon::parse($today)->endOfMonth()->toDateString();
        }

    
        
        // return $firstDateMonth.' - '.$lastDateMonth.' - '.$getTotalDay;

        
        try {
            //&& date('Y-m', $getEmployee->as_doj) =< date('')
            if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $yearMonth){

                //  get benefit employee associate id wise
                $getBenefit = Benefits::
                where('ben_as_id', $getEmployee->associate_id)
                ->first();
                if($getBenefit != null){
                    $today = $yearMonth.'-01';
                    $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
                    if($monthDayCount > $getTotalDay){
                        $lastDateMonth = $yearMonth.'-'.$getTotalDay;
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
                    $getPresentOT = DB::table($tableName)
                        ->select([
                            DB::raw('count(as_id) as present'),
                            DB::raw('SUM(ot_hour) as ot'),
                            DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                            DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')

                        ])
                        ->where('as_id', $as_id)
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
                        ->where('remarks', 'Holiday')
                        ->count();
                    }else{
                        // check holiday roaster employee
                        $RosterHolidayCount = HolidayRoaster::where('year', $year)
                        ->where('month', $month)
                        ->where('as_id', $getEmployee->associate_id)
                        ->where('remarks', 'Holiday')
                        ->count();
                        // check General roaster employee
                        $RosterGeneralCount = HolidayRoaster::where('year', $year)
                        ->where('month', $month)
                        ->where('as_id', $getEmployee->associate_id)
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
                        $totalDay = $getTotalDay - ((int) $empdojDay-1);
                    }else{
                        $totalDay = $getTotalDay;
                    }
                    $getAbsent = $totalDay - ($getPresentOT->present + $getHoliday + $leaveCount);
                    

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
                    
                    

                    if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $monthDayCount > $getTotalDay){
                        $perDayGross   = $getBenefit->ben_current_salary/$monthDayCount;
                        $totalGrossPay = ($perDayGross * $totalDay);
                        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }else{

                        $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }

                    $ot = round((float)($overtime_rate) * ($getPresentOT->ot));
                    
                    $totalPayable = round((float)($salaryPayable + $ot + $deductSalaryAdd + $attBonus + $productionBonus + $leaveAdjust));

                    // cash & bank part
                    if($payStatus == 1){
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
                            'bank_payable' => $bankPayable
                        ];
                        //HrMonthlySalary::insert($salary);
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
                            'bank_payable' => $bankPayable
                        ];
                        //HrMonthlySalary::where('id', $getSalary->id)->update($salary);
                    }
                }
            }
            return $salary;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        dd($getPresentOT);

    	$trainingNames = TrainingNames::where('hr_tr_status', '1')
    					->pluck('hr_tr_name', 'hr_tr_name_id');

    	return view('hr/training/add_training', compact('trainingNames'));
    }

    # Store Training
    public function saveTraining(Request $request)
    {
        //ACL::check(["permission" => "hr_training_add"]);
        #-----------------------------------------------------------#

    	$validator = Validator::make($request->all(), [
            'tr_as_tr_id'     => 'required|max:11',
            'tr_trainer_name' => 'required|max:128',
            'tr_description'  => 'required|max:1024',
            'tr_start_date'   => 'required|date',
            'tr_end_date'     => 'date|nullable',
            'tr_start_time'   => 'required|max:5',
            'tr_end_time'     => 'required|max:5'
        ]);


        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            //-----------Store Data---------------------
        	$store = new Training;
			$store->tr_as_tr_id  = $request->tr_as_tr_id;
			$store->tr_trainer_name = $request->tr_trainer_name;
			$store->tr_description = $request->tr_description;
			$store->tr_start_date = (!empty($request->tr_start_date)?date('Y-m-d',strtotime($request->tr_start_date)):null);
			$store->tr_end_date = (!empty($request->tr_end_date)?date('Y-m-d',strtotime($request->tr_end_date)):null);
			$store->tr_start_time = (!empty($request->tr_start_time)?date('H:i',strtotime($request->tr_start_time)):null);
			$store->tr_end_time = (!empty($request->tr_end_time)?date('H:i',strtotime($request->tr_end_time)):null);

			if ($store->save())
			{
                $this->logFileWrite("Training Entry Saved", $store->tr_id);
        		return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
			}
			else
			{
        		return back()
        			->withInput()->with('error', 'Please try again.');
			}
        }
    }


    # training list
    public function trainingList()
    {
        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        return view('hr/training/training_list');
    }

    # training data
    public function getData()
    {
        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('hr_training AS tr')
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                'tr.*',
                'tn.hr_tr_name AS training_name'
            )
            ->leftJoin('hr_training_names AS tn','tn.hr_tr_name_id', '=', 'tr.tr_as_tr_id')
            ->orderBy('tr.tr_start_date','desc')
            ->orderBy('tr.tr_id','desc')
            ->get();

        return DataTables::of($data)
            ->addColumn('schedule_date', function ($data) {

                if($data->tr_start_date != null)
                {
                    $start_date=date('d-M-Y',strtotime($data->tr_start_date));

                    if (!empty($data->tr_end_date))
                    {
                        $end_date=date('d-M-Y',strtotime($data->tr_end_date));
                    }
                    else
                    {
                        $end_date = "Continue";
                    }

                    return "<strong>Start : </strong><span>$start_date</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$end_date</span>";
                }
                else
                {
                    return "<strong>Start : </strong><span>$data->tr_start_date</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$data->tr_end_date</span>";
                }
            })
            ->addColumn('schedule_time', function ($data) {
                return "<strong>Start : </strong><span>$data->tr_start_time</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$data->tr_end_time</span>";
            })
            ->addColumn('action', function ($data) {
                if ($data->tr_status == 1)
                    return "<div class=\"btn-group\">
                            <button type=\"button\" disabled class='btn btn-xs btn-success' style='width:55px;'>Active</button>
                            <a href=".url('hr/training/training_status/'.$data->tr_id."/inactive")." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Inactive Now!\" style='width:29px;'>
                            <i class=\"ace-icon fa fa-times bigger-120\"></i>
                        </div>";
                else
                    return "<div class=\"btn-group\">
                            <button type=\"button\" disabled class='btn btn-xs btn-danger'>Inactive</button>
                            <a href=".url('hr/training/training_status/'.$data->tr_id."/active")." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Active Now!\">
                            <i class=\"ace-icon fa fa-check bigger-120\"></i>
                        </div>";
            })
            ->rawColumns(['serial_no', 'schedule_date', 'schedule_time', 'action'])
            ->toJson();
    }


    # training Status
    public function trainingStatus(Request $request)
    {

        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        if ($request->status == 'active')
        {
            Training::where('tr_id', $request->id)
            ->update(['tr_status'=>'1']);

            $this->logFileWrite("Training Activated", $request->id);
            return back()->with('success', 'Training is Activated!');
        }
        else
        {
            Training::where('tr_id', $request->id)
            ->update(['tr_status'=>'0']);

            $this->logFileWrite("Training Inactivated", $request->id);
            return back()->with('success', 'Training is Inactivated!');

        }

    }
}
