<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Hr\IDGenerator as IDGenerator;
use App\Jobs\ProcessUnitWiseSalary;
use App\Jobs\ProcessAttendanceOuttime;
use App\Helpers\EmployeeHelper;
use App\Models\Employee;
use App\Models\Hr\AdvanceInfo;
use App\Models\Hr\MedicalInfo;
use App\Models\Hr\Absent;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\YearlyHolyDay;
use App\Models\Hr\Leave;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\HrMonthlySalary;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use PDF, Validator, Auth, ACL, DB, DataTables;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Jobs\ProcessAttendanceInOutTime;


class TestController extends Controller
{
    public function jobcardupdate()
    {
        $data = DB::table('hr_attendance_ceil')
            ->where('in_date','2020-11-15')
            ->get();

        foreach ($data as $key => $v) 
        {


            $queue = (new ProcessAttendanceInOutTime('hr_attendance_ceil', $v->id, 2))
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
            
        }
        return 'success 30';

    } 


    public function test()
    {

        
        return $this->makeAbsent();
        return $this->testMail();
        
        return '';
        return $this->getLeftEmployee();
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $disctrict = district_by_id();
        $upzilla = upzila_by_id();







        $data = DB::table('hr_as_basic_info AS b')
                ->select('r.as_id','r.in_date','b.associate_id','b.as_oracle_code','b.as_name','b.as_section_id','b.as_designation_id','b.as_department_id','b.as_unit_id','bn.ben_current_salary','b.as_doj')
                ->leftJoin('hr_attendance_mbm AS r','b.as_id','r.as_id')
                ->leftJoin('hr_benefits as bn','bn.ben_as_id','b.associate_id')
                ->whereIn('r.in_date',['2020-10-30','2020-10-02'])
                ->where('b.as_emp_type_id', 3)
                ->where('b.as_doj','>=','2020-08-09')
                ->get();
           
        $employees = collect($data)->groupBy('as_id');
        $sal=[];
        foreach ($employees as $key => $e) {
            $sal[] = array(
                'Associate ID' =>  $e[0]->associate_id,
                'Oracle ID' =>  $e[0]->as_oracle_code,
                'Name' =>  $e[0]->as_name,
                'DOJ' =>  $e[0]->as_doj,
                'Designation' =>  $designation[$e[0]->as_designation_id]['hr_designation_name'],
                'Section' =>  $section[$e[0]->as_section_id]['hr_section_name'],
                'Department' =>  $department[$e[0]->as_department_id]['hr_department_name'],
                'Unit' =>  $unit[$e[0]->as_unit_id]['hr_unit_short_name'],
                'Gross' =>  $e[0]->ben_current_salary,
                'Day' => count($e),
                'Per Day' =>  round($e[0]->ben_current_salary/31,2),
                'Total' => ceil(count($e)*round($e[0]->ben_current_salary/31,2))
            );
        }
        return (new FastExcel(collect($sal)))->download('Substitute Holiday Payment.xlsx');
        dd($employees);
        $array = ['products' => ['desk' => ['price' => 100], 'hi' => 'Test']];
        Arr::forget($array, 'products.desk');
        dd($array);
       


        $data = DB::table('hr_as_basic_info as b')
                    ->leftJoin('hr_benefits as ben', 'ben.ben_as_id', 'b.associate_id')
                    ->whereIn('b.as_unit_id', auth()->user()
                    ->unit_permissions())->whereIn('b.as_status', [2,3,4,5,6])
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('b.as_status_date', '>=', '2020-10-01')->where('b.as_status_date', '<=', '2020-10-31')->get();

        $emps = []; 
        foreach ($data as $key => $emp) {


            $emps[$emp->associate_id] = $this->processPartialSalary($emp,$emp->as_status_date, $emp->as_status); 
        }


        dd($emps);

        $getEmployee = Employee::where('associate_id', '19D700071P')->first();
        $year = 2020;
        $month = 10;
        $yearMonth = $year.'-'.$month;
        $monthDayCount  = Carbon::parse($yearMonth)->daysInMonth;
        $partial = 0;
        $firstDateMonth = '2020-10-01';
        $lastDateMonth = '2020-10-07';
        $empdojMonth = '2019-01-01';

        if($getEmployee->as_status_date != null){
                        $sDate = $getEmployee->as_status_date;
                        $sYearMonth = Carbon::parse($sDate)->format('Y-m');
                        $sDay = Carbon::parse($sDate)->format('d');


                        if($yearMonth == $sYearMonth){
                            $firstDateMonth = $getEmployee->as_status_date;
                            $totalDay = 31 - ((int) $sDay-1);

                            if($sDay > 1){
                                $partial = 1;
                            }
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

                    //dd($shiftHolidayCount);
            }
            
            if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
            }else{
                $getHoliday = $shiftHolidayCount;
            }
        }



        dd($getHoliday, $RosterHolidayCount);
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $data = DB::table('hr_monthly_salary as s')
        ->leftJoin('hr_as_basic_info as b','s.as_id','b.associate_id')
        ->whereIn('b.as_location', auth()->user()->location_permissions())
        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
        ->where(['b.as_status' => 1,'s.month'=> 10, 's.year' => 2020])->get();
        
        dd($data);
        $excel = [];

        foreach ($data as $key => $a) {
            $excel[$a->associate_id] = array(
                'Name' => $a->as_name,
                'Associate ID' => $a->associate_id,
                'Oracle ID' => $a->as_oracle_code,
                'Name' => $a->as_name,
                'RF ID' => $a->as_rfid_code??0,
                'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                'Total Salary' => $a->total_payable,
                'Cash Amount' => $a->cash_payable,
                'Bank Amount' => $a->bank_payable,
                'Gross' => $a->gross,
                'Basic' => $a->basic,
                'House Rent' => $a->house??0,
                'OT' => numberToTimeClockFormat($a->ot_hour),
                'OT Amount' => ceil($a->ot_hour*$a->ot_rate),
                'Present' => $a->present,
                'Leave' => $a->leave,
                'Absent' => $a->absent,
                'Holiday' => $a->holiday,
                'Leave' => $a->leave,
                'Total Day' => $a->leave+$a->holiday+$a->present,
                'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT'
            );
        }

        return (new FastExcel(collect($excel)))->download('Monthly Salary.xlsx');
       
    }

    public function processSalaryLeft()
    {
        $datas = DB::table('hr_as_basic_info as b')
                ->leftJoin('hr_monthly_salary as s', 's.as_id','b.associate_id')
                ->leftJoin('hr_benefits as ben', 'ben.ben_as_id', 'b.associate_id')
                ->whereIn('b.associate_id', ["17E100162N","14M100304N","17K700090P","17E100177N","18G101898N","17A500248O","18G100329N","18K100860N","18C100792N","18G100629N","15L100425N","08B100534N","11A100451N","08A100501N","17D100524N","18A100737N","19K106074N","18C101175N"
                ])
                ->where('s.month',12)
                ->where('s.year',2020)
                ->get();

        foreach ($datas as $key => $data) {
            if(isset($data->total_payable)){


                $payable = $data->present + $data->holiday + $data->absent +$data->leave;
                $perDayBasic = $data->ben_basic / 30;
                $perDayGross   = $data->ben_current_salary/ 31;
                $absent_deduct = (int) ($data->absent * $perDayBasic);

                $salaryPayable = $perDayGross*$payable - ($absent_deduct + $data->stamp);
                if($data->as_ot == 1){
                    $overtime_rate = number_format((($data->ben_basic/208)*2), 2, ".", "");
                } else {
                    $overtime_rate = 0;
                }
                $ot_payable = $overtime_rate * $data->ot_hour;

                $total_payable = ceil($salaryPayable + $ot_payable +$data->attendance_bonus + $data->production_bonus);
                $sal = [
                    'gross' => $data->ben_current_salary,
                    'basic' => $data->ben_basic,
                    'house' => $data->ben_house_rent,
                    'ot_rate' => $overtime_rate,
                    'salary_payable' => $salaryPayable,
                    'total_payable' => $total_payable,
                    'cash_payable' => $total_payable,
                    'absent_deduct' => $absent_deduct
                ];

                DB::table('hr_monthly_salary')->where('id',$data->id)->update($sal);
            }
        }

        return 'hi';
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


    public function migrateemployee()
    {


    }
     public function exportReport(Request $request)
    {

        if(isset($request->date)){
            $date = $request->date;
            $data = DB::table('hr_as_basic_info AS b')
                     ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                     ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('b.as_status',1)
                    ->where('b.as_doj' , '<=', $date)
                    ->get();

            $data = collect($data)->keyBy('associate_id');
            $units = auth()->user()->unit_permissions();
        
            $filename = 'Employee record -'.$date.'.xlsx';
            
            $designation = designation_by_id();
            $department = department_by_id();
            $section = section_by_id();
            $subsection = subSection_by_id();
            $unit = unit_by_id();
            $excel = [];
            foreach ($units as $key => $u) {
                
                $table = get_att_table($u).' AS a';
                $att = DB::table($table)
                        ->leftJoin('hr_as_basic_info as b','b.as_id','a.as_id')
                        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                        ->whereIn('b.as_location', auth()->user()->location_permissions())
                        ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                        ->where('a.in_date', $date)
                        ->get();
                
                foreach ($att as $key => $a) {
                    $excel[$a->associate_id] = array(
                        'Associate ID' => $a->associate_id,
                        'Oracle ID' => $a->as_oracle_code,
                        'Name' => $a->as_name,
                        'RF ID' => $a->as_rfid_code??0,
                        'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                        'Current Salary' => $a->ben_current_salary,
                        'Basic Salary' => $a->ben_basic,
                        'House Rent' => $a->ben_house_rent??0,
                        'Cash Amount' => $a->ben_cash_amount??0,
                        'Bank/Rocket' => $a->ben_bank_amount??0,
                        'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                        'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                        'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                        'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                        'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                        'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                        'Status' => 'Present',
                        'Late' => $a->late_status,
                        'OT Hour' => numberToTimeClockFormat($a->ot_hour),
                        'Date' => $date
                    );
                    $excel[$a->associate_id]['In Time'] = '';
                    $excel[$a->associate_id]['Out Time'] = '';
                    if($a->in_time != null && $a->remarks != 'DSI'){
                        $excel[$a->associate_id]['In Time'] = date('H.i', strtotime($a->in_time));
                    }
                    if($a->out_time != null){
                        if(date('H:i', strtotime($a->out_time)) != '00:00'){
                            $excel[$a->associate_id]['Out Time'] = date('H.i', strtotime($a->out_time));
                        }
                    }
                }
                
                
            }
            
            $ab = DB::table('hr_absent as a')
                    ->leftJoin('hr_as_basic_info as b','b.associate_id','a.associate_id')
                    ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('a.date', $date)
                    ->whereIn('b.as_unit_id', $units)
                    ->get();

            $lv = DB::table('hr_leave as a')
                    ->leftJoin('hr_as_basic_info as b','b.associate_id','a.leave_ass_id')
                    ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('a.leave_from', "<=", $date)
                    ->where('a.leave_to', ">=", $date)
                    ->whereIn('b.as_unit_id', $units)
                    ->get();

            $do = DB::table('holiday_roaster as a')
                    ->leftJoin('hr_as_basic_info as b','b.associate_id','a.as_id')
                    ->leftJoin('hr_benefits as c','b.associate_id','c.ben_as_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('a.date', $date)
                    ->whereIn('b.as_unit_id', $units)
                    ->where('a.remarks', 'Holiday')
                    ->get();

            

            

                

            foreach ($ab as $key => $a) {
                $excel[$a->associate_id] = array(
                    'Associate ID' => $a->associate_id,
                    'Oracle ID' => $a->as_oracle_code,
                    'Name' => $a->as_name,
                    'RF ID' => $a->as_rfid_code??0,
                    'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                    'Current Salary' => $a->ben_current_salary,
                    'Basic Salary' => $a->ben_basic,
                    'House Rent' => $a->ben_house_rent??0,
                    'Cash Amount' => $a->ben_cash_amount??0,
                    'Bank/Rocket' => $a->ben_bank_amount??0,
                    'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                    'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                    'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                    'Status' => 'Absent',
                    'Late' => '',
                    'OT Hour' => '',
                    'Date' => $date,
                    'In Time' =>  '',
                    'Out Time' => ''

                );
            }

            foreach ($lv as $key => $a) {
                $excel[$a->associate_id] = array(
                    'Associate ID' => $a->associate_id,
                    'Oracle ID' => $a->as_oracle_code,
                    'Name' => $a->as_name,
                    'RF ID' => $a->as_rfid_code??0,
                    'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                    'Current Salary' => $a->ben_current_salary,
                    'Basic Salary' => $a->ben_basic,
                    'House Rent' => $a->ben_house_rent??0,
                    'Cash Amount' => $a->ben_cash_amount??0,
                    'Bank/Rocket' => $a->ben_bank_amount??0,
                    'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                    'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                    'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                    'Status' => 'Leave',
                    'Late' => '',
                    'OT Hour' => '',
                    'Date' => $date,
                    'In Time' =>  '',
                    'Out Time' => ''
                );
            }

            foreach ($do as $key => $a) {
                $excel[$a->associate_id] = array(
                    'Associate ID' => $a->associate_id,
                    'Oracle ID' => $a->as_oracle_code,
                    'Name' => $a->as_name,
                    'RF ID' => $a->as_rfid_code??0,
                    'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                    'Current Salary' => $a->ben_current_salary,
                    'Basic Salary' => $a->ben_basic,
                    'House Rent' => $a->ben_house_rent??0,
                    'Cash Amount' => $a->ben_cash_amount??0,
                    'Bank/Rocket' => $a->ben_bank_amount??0,
                    'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                    'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                    'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                    'Status' => 'Day Off',
                    'Late' => '',
                    'OT Hour' => '',
                    'Date' => $date,
                    'In Time' =>  '',
                    'Out Time' => ''
                );
            }

            foreach ($data as $key => $a) {
                if(!isset($excel[$a->associate_id])){

                    $excel[$a->associate_id] = array(
                        'Associate ID' => $a->associate_id,
                        'Oracle ID' => $a->as_oracle_code,
                        'Name' => $a->as_name,
                        'RF ID' => $a->as_rfid_code??0,
                        'DOJ' => date('d-M-Y', strtotime($a->as_doj)),
                        'Current Salary' => $a->ben_current_salary,
                        'Basic Salary' => $a->ben_basic,
                        'House Rent' => $a->ben_house_rent??0,
                        'Cash Amount' => $a->ben_cash_amount??0,
                        'Bank/Rocket' => $a->ben_bank_amount??0,
                        'Designation' => $designation[$a->as_designation_id]['hr_designation_name']??'',
                        'Department' => $department[$a->as_department_id]['hr_department_name']??'',
                        'Section' => $section[$a->as_section_id]['hr_section_name']??'',
                        'Sub Section' => $subsection[$a->as_subsection_id]['hr_subsec_name']??'',
                        'Unit' => $unit[$a->as_unit_id]['hr_unit_short_name']??'',
                        'OT/NONOT' => $a->as_ot == 1?'OT':'NonOT',
                        'Status' => '',
                        'Late' => '',
                        'OT Hour' => '',
                        'Date' => $date,
                        'In Time' =>  '',
                        'Out Time' => ''
                    );
                }
            }

            

            return (new FastExcel(collect($excel)))->download($filename);
        }

        return view('common.employee-record');
    }





    public function bulkManualStore($request)
    {
        // dd($request->all());
        $unit=$request['as_unit_id'];
        $info = Employee::where('as_id',$request['as_id'])->first();
        $tableName= get_att_table($unit);
        $date = $request['in_date'];
        $month = '09';
        $year = '2020';

        if(strlen($request['in_time']) > 2){
            $intime = date('H:i:s', strtotime($request['in_time']));
        }else{
            $intime = date('H:i:s', strtotime($request['in_time'].'.0'));
        }

        if(strlen($request['out_time']) > 2){
            $outtime = date('H:i:s', strtotime($request['out_time']));
        }else{
            $outtime = date('H:i:s', strtotime($request['out_time'].'.0'));
        }

        
        

        $day_of_date = date('j', strtotime($date));
        $day_num = "day_".$day_of_date;
        $shift= DB::table("hr_shift_roaster")
        ->where('shift_roaster_month', $month)
        ->where('shift_roaster_year', $year)
        ->where("shift_roaster_user_id", $info->as_id)
        ->select([
            $day_num,
            'hr_shift.hr_shift_id',
            'hr_shift.hr_shift_start_time',
            'hr_shift.hr_shift_end_time',
            'hr_shift.hr_shift_code',
            'hr_shift.hr_shift_break_time',
            'hr_shift.hr_shift_night_flag'
        ])
        ->leftJoin('hr_shift', function($q) use($day_num, $unit) {
            $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
            $q->where('hr_shift.hr_shift_unit_id', $unit);
        })
        ->orderBy('hr_shift.hr_shift_id', 'desc')
        ->first();
        
        if(!empty($shift) && $shift->$day_num != null){
            $shift_start= $shift->hr_shift_start_time;
            $shift_end= $shift->hr_shift_end_time;
            $break= $shift->hr_shift_break_time;
            $nightFlag= $shift->hr_shift_night_flag;
            $shiftCode= $shift->hr_shift_code;
            $new_shift_id = $shift->hr_shift_id;
        }else{
            $shift_start= $info->shift['hr_shift_start_time'];
            $shift_end= $info->shift['hr_shift_end_time'];
            $break= $info->shift['hr_shift_break_time'];
            $nightFlag= $info->shift['hr_shift_night_flag'];
            $shiftCode= $info->shift['hr_shift_code'];
            $new_shift_id= $info->shift['hr_shift_id'];
        }

        DB::beginTransaction();
        try {
                    $checkDay = EmployeeHelper::employeeDateWiseStatus($date, $info->associate_id, $info->as_unit_id, $info->shift_roaster_status);
                    if($checkDay == 'open' || $checkDay == 'OT'){
                        $insert = [];
                        $insert['remarks'] = 'BM';
                        $insert['as_id'] = $info->as_id;
                        $insert['hr_shift_code'] = $shiftCode;

                        
                        if (strpos($intime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$intime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $intime = null;
                            }
                        }

                        
                        if (strpos($outtime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$outtime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $outtime = null;
                            }
                        }

                        

                        if($intime == null && $outtime == null){
                            $absentData = [
                                'associate_id' => $info->associate_id,
                                'date' => $date,
                                'hr_unit' => $info->as_unit_id
                            ];
                            $getAbsent = Absent::where($absentData)->first();
                            if($getAbsent == null && $checkDay == 'open'){
                                Absent::insert($absentData);
                            }
                        }else{
                            if($intime == '00:00:00' || $intime == null){
                                $empIntime = $shift_start;
                                $insert['remarks'] = 'DSI';
                            }else{
                                $empIntime = $intime;
                            }
                            $attInsert = 0;
                            $insert['in_time'] = $date.' '.$empIntime;
                            if($outtime == '00:00:00' || $outtime == null){
                                $insert['out_time'] = null;
                            }else{
                                $insert['out_time'] = $date.' '.$outtime;
                            }
                            if($checkDay == 'OT'){
                                $insert['late_status'] = 0;
                            }else if($intime != null){
                                $insert['in_unit'] = $unit;
                                $insert['late_status'] = $this->getLateStatus($unit, $new_shift_id,$date,$intime,$shift_start);
                            }else{
                                $insert['late_status'] = 1;
                            }
                            if($outtime != null){
                                $insert['out_unit'] = $unit;
                                $insert['out_time'] = $date.' '.$outtime;
                                if($intime != null) {
                                    // out time is tomorrow
                                    if(strtotime($intime) > strtotime($outtime)) {
                                        $dateModify = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                        $insert['out_time'] = $dateModify.' '.$outtime;
                                    }
                                }
                            }

                            //check OT hour if out time exist
                            if($intime != null && $outtime != null && $info->as_ot != 0 && $insert['remarks'] != 'DSI'){
                                $overtimes = EmployeeHelper::daliyOTCalculation($insert['in_time'], $insert['out_time'], $shift_start, $shift_end, $break, $nightFlag, $info->associate_id, $info->shift_roaster_status, $unit);
                                $insert['ot_hour'] = $overtimes;
                            }else{
                                $insert['ot_hour'] = 0;
                            }
                            $insert['in_date'] = date('Y-m-d', strtotime($insert['in_time']));
                            DB::table($tableName)->insert($insert);
                            
                            //
                            $absentWhere = [
                                'associate_id' => $info->associate_id,
                                'date' => $date,
                                'hr_unit' => $info->as_unit_id
                            ];
                            Absent::where($absentWhere)->delete();
                            
                        }
                    }else{
                        $absentWhere = [
                            'associate_id' => $info->associate_id,
                            'date' => $date,
                            'hr_unit' => $info->as_unit_id
                        ];
                        Absent::where($absentWhere)->delete();
                    }

              

            //dd($year);exit;
            $yearMonth = $year.'-'.$month;
            if($month == date('m')){
                $totalDay = date('d');
            }else{
                $totalDay = Carbon::parse($yearMonth)->daysInMonth;
            }

            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
             
        }
    }



    public function getLateStatus($unit,$shift_id,$date,$intime,$shift_start)
    {
        $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($unit, $shift_id);
        if($getLateCount != null){
            if(date('Y-m-d', strtotime($date))>= $getLateCount->date_from && date('Y-m-d', strtotime($date)) <= $getLateCount->date_to){
                $lateTime = $getLateCount->value;
            }else{
                $lateTime = $getLateCount->default_value;
            }
        }else{
            $lateTime = 180;
        }
        $shiftinTime = (strtotime(date("H:i:s", strtotime($shift_start)))+$lateTime);
        if(strtotime(date('H:i:s', strtotime($intime))) > $shiftinTime){
            $late = 1;
        }else{
            $late = 0;
        }
        return $late;
    }

    

    public function noMacth()
    {
        $nomatch = [];
        foreach ($getData as $key => $value) {
            $flag=0;
            $counter=0;
            foreach ($getEmployee as $emp) {
                ++$counter;
                if($emp->as_oracle_code == $value['PID']){
                    $flag=0;
                    break;
                }else{
                    $flag++;
                    continue;
                }
            }

            if($flag>0 || $counter==0 ){
                $nomatch[] = $value['PID'];
            }
        }


        return ($nomatch);
    }


    public function check()
    {
        $salaryAddDeduct = DB::table('hr_salary_add_deduct')
                ->where('year',2020)
                ->where('month', 11)
                ->get()->keyBy('associate_id')->toArray();
            return $salaryAddDeduct;
       

                $leave_array = [];
                $absent_array = [];
                for($i=1; $i<=20; $i++) {
                $date = date('Y-m-d', strtotime('2020-11-'.$i));
                $leave = DB::table('hr_absent AS a')
                        ->where('a.date', '=', $date)
                        ->whereIn('b.as_unit_id', [1, 4, 5])
                        ->leftJoin('hr_as_basic_info AS b', function($q){
                            $q->on('b.associate_id', 'a.associate_id');
                        })
                        ->pluck('b.as_id', 'b.associate_id');
                $leave_array[] = $leave;
                $absent_array[] = DB::table('hr_attendance_mbm')
                        ->whereDate('in_time', $date)
                        ->whereIn('as_id', $leave)
                        ->get()->toArray();
                }
                return ($absent_array);
        
        $n = 5;
        $a = [4, 0, 1, -2, 3];
        $result = array();
        $p = array();
        for($i=0; $i<$n; $i++){
            $p[] =($a[$i-1]??0).' - '.($a[$i]??0).' - '.($a[$i+1]??0);
            $result[] = $a[$i-1]??0+$a[$i]??0+$a[$i+1]??0;
        }
        return $p;
        $asId = 10242;
        $unit = 1;
        $date = '2020-10-12';
        $shiftNight = 0;
        $designationId = 363;
        $test = EmployeeHelper::dailyBillCalculation($unit, $date, $asId, $shiftNight, $designationId);
        dd($test);
        

                $leave_array = [];
                $absent_array = [];
                for($i=1; $i<=20; $i++) {
                $date = date('Y-m-d', strtotime('2020-11-'.$i));
                $leave = DB::table('hr_absent AS a')
                        ->where('a.date', '=', $date)
                        ->whereIn('b.as_unit_id', [1, 4, 5])
                        ->leftJoin('hr_as_basic_info AS b', function($q){
                            $q->on('b.associate_id', 'a.associate_id');
                        })
                        ->pluck('b.as_id', 'b.associate_id');
                $leave_array[] = $leave;
                $absent_array[] = DB::table('hr_attendance_mbm')
                        ->whereDate('in_time', $date)
                        ->whereIn('as_id', $leave)
                        ->get()->toArray();
                }
                dump($leave_array,$absent_array);
                dd('end');
           
    }
    
     public function getLeftEmployee()
    {

        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $disctrict = district_by_id();
        $upzilla = upzila_by_id();

        $data = DB::table('hr_as_basic_info as b')
                    ->whereIn('b.as_unit_id', [1,4,5])
                    ->whereIn('b.as_status', [2,3,4,5,7,8])
                    ->where('b.as_status_date', '>=', '2020-11-01')->where('b.as_status_date', '<=', '2020-11-30')->get();


       
           
        
        foreach ($data as $key => $e) {
            $sal[] = array(
                'Associate ID' =>  $e->associate_id,
                'Oracle ID' =>  $e->as_oracle_code,
                'RF ID' =>  $e->as_rfid_code,
                'Name' =>  $e->as_name,
                'DOJ' =>  $e->as_doj,
                'Designation' =>  $designation[$e->as_designation_id]['hr_designation_name'],
                'Section' =>  $section[$e->as_section_id]['hr_section_name'],
                'Department' =>  $department[$e->as_department_id]['hr_department_name'],
                'Unit' =>  $unit[$e->as_unit_id]['hr_unit_short_name'],
                'OT/NONOT' => $e->as_ot == 1?'OT':'NonOT',
                'Date' => $e->as_status_date,
                'Status' => emp_status_name($e->as_status)
            );
        }

        return (new FastExcel(collect($sal)))->download('Monthly Summary.xlsx');

    }
    
    
    public function getMonthlySalary(Request $request)
    {
        $month = $request->month??date('m');
        $year = $request->year??date('Y');
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $disctrict = district_by_id();
        $upzilla = upzila_by_id();

        $data = DB::table('hr_monthly_salary AS s')
                    ->leftJoin('hr_as_basic_info AS b','b.associate_id','s.as_id' )
                    ->leftJoin('hr_benefits AS ben','ben.ben_as_id','b.associate_id' )
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('s.emp_status', 1)
                    ->where('s.month', $month )
                    ->where('s.year', $year)
                    ->get();
        
        foreach ($data as $key => $e) {
            $sal[] = array(
                'Name' =>  $e->as_name,
                'Associate ID' =>  $e->associate_id,
                'Oracle ID' =>  $e->as_oracle_code,
                'OT/NONOT' => $e->ot_status == 1?'OT':'NonOT',
                'Present' => $e->present,
                'Leave' => $e->leave,
                'Absent' => $e->absent,
                'Holiday' => $e->holiday,
                'Total Day' => $e->present + $e->leave + $e->holiday ,
                'Late Count' => $e->late_count,
                'OT Hour' => $e->ot_hour,
                'OT Rate' => $e->ot_rate,
                'OT Amount' => round($e->ot_rate*$e->ot_hour,2),
                'Att Bonus' => $e->attendance_bonus,
                'Leave Adjust' => $e->leave_adjust,
                'Absent Deduct' => $e->absent_deduct,
                'Stamp' => $e->stamp,
                'Total Salary' => $e->total_payable,
                'Bank Amount' => $e->bank_payable,
                'Cash Amount' => $e->cash_payable,
                'TDS' => $e->tds,
                'Bank Name' => $e->bank_name??'',
                'Account Number' => $e->bank_no??'',
                'Current Salary' => $e->gross,
                'Basic' => $e->basic,
                'House Rent' => $e->house,
                'RF ID' =>  $e->as_rfid_code,
                'DOJ' =>  $e->as_doj,
                'Designation' =>  $designation[$e->as_designation_id]['hr_designation_name'],
                'Section' =>  $section[$e->as_section_id]['hr_section_name'],
                'Department' =>  $department[$e->as_department_id]['hr_department_name'],
                'Unit' =>  $unit[$e->as_unit_id]['hr_unit_short_name'],
            );
        }

        return (new FastExcel(collect($sal)))->download('Monthly Salary.xlsx');

    }

    public function increment()
    {
        $data = [];
        
        $exist = []; $not = [];
        foreach ($data as $key => $val) {

            $ben = DB::table('hr_benefits as b')
                            ->leftJoin('hr_as_basic_info as a','a.associate_id','b.ben_as_id')
                            ->where('a.as_oracle_code', $key)
                            ->first();
            $up['ben_current_salary'] = $val['New Gross'];
            $up['ben_basic'] = ceil(($val['New Gross']-1850)/1.5);
            $up['ben_house_rent'] = $val['New Gross'] -1850 - $up['ben_basic'];

            if($ben->ben_bank_amount > 0){
                $up['ben_bank_amount'] = $val['New Gross'];
                $up['ben_cash_amount'] = 0;
            }else{
                $up['ben_cash_amount'] = $val['New Gross'];
                $up['ben_bank_amount'] = 0;
            }

            $exist[$key] = DB::table('hr_benefits')->where('ben_id', $ben->ben_id)->update($up);

            $tableName = get_att_table($ben->as_unit_id);

            if($ben->as_status == 1){

                $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $ben->as_id, date('d')))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }else{
                $not[]=$ben->associate_id;
            }

        }
        return $not;
    }
    
    public function testMail()
    {
        $data = [];

        Mail::to('rakib@mbmdhaka.com')->send(new TestMail($data));
    }

    public function makeAbsent()
    {
        $data = DB::table('hr_as_basic_info')
                ->where('shift_roaster_status',1)
                ->whereIn('as_unit_id',[1,4,5])
                ->pluck('associate_id','as_id');
        $dates = ['2020-12-11','2020-01-16','2020-01-25','2020-01-17','2020-01-18'];

        foreach ($data as $key => $val) {
            $att = DB::table('hr_attendance_mbm')
                   ->whereIn('in_date', $dates)
                   ->pluck('in_date');

            $holiday = DB::table('holiday_roaster')
        }

        // check att


        // check roaster

        //


        dd($data);
    }
    

}
