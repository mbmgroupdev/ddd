<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Hr\IDGenerator as IDGenerator;
use App\Http\Controllers\Hr\Recruitment\RecruitController as Recruit;
use App\Jobs\ProcessUnitWiseSalary;
use App\Jobs\ProcessAttendanceOuttime;
use App\Helpers\EmployeeHelper;
use App\Models\Employee;
use App\Models\Hr\Benefits;
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
    public $timeout = 500;
    public $buyer;
    public $month;
    public $year;
    public $asId;
    public $attTable;
    public $salaryTable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->buyer = DB::table('hr_buyer_template')->where('id',7)->first();
        $this->month = '01';
        $this->year  = 2021;
        $this->asId  = 4601;
        $this->attTable  = 'hr_buyer_att_'.$this->buyer->table_alias;
        $this->salaryTable  = 'hr_buyer_salary_'.$this->buyer->table_alias;
    }*/

    public function jobcardupdate()
    {
        $data = DB::table('hr_attendance_ceil')
            ->where('in_date','2021-01-03')
            ->get();

        foreach ($data as $key => $v) 
        {

            if($v->in_time && $v->out_time){

                $queue = (new ProcessAttendanceInOutTime('hr_attendance_ceil', $v->id, 2))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            }
            
        }
        return 'success 30';

    } 

    public function updateSalary()
    {
        $in = DB::table('hr_as_basic_info as b')
                ->leftJoin('hr_benefits as bn','bn.ben_as_id', 'b.associate_id')
                ->where('as_unit_id', 2)
                ->where('as_location', 7)
                ->get();

        foreach ($in as $k => $val) {
            # code...
            DB::table('hr_monthly_salary')
                ->where('as_id', $val->associate_id)
                ->update([
                    'unit_id' => $val->as_unit_id,
                    'designation_id' => $val->as_designation_id,
                    'sub_section_id' => $val->as_subsection_id,
                    'location_id' => $val->as_location,
                    'pay_type' => $val->bank_name
                ]);
        }
        return 'done';
    }


    public function test()
    {

        
        return $this->updateDept();
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
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_status', [2,3,4,5,6])
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
            $checkAtt = DB::table($table)
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
                    ->where(function($q) use ($date){
                        $q->where(function($qa) use ($date){
                            $qa->where('b.as_status',1);
                            $qa->where('b.as_doj' , '<=', $date);
                        });
                        $q->orWhere(function($qa) use ($date){
                            $qa->whereIn('b.as_status',[2,3,4,5,6,7,8]);
                            $qa->where('b.as_status_date' , '>', $date);
                        });

                    })->get();

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
                    $excel[$a->associate_id]['AsStatus'] = $a->as_status;
                    $excel[$a->associate_id]['AsStatusDate'] = $a->as_status_date;
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
                    'Out Time' => '',
                    'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date

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
                    'Out Time' => '',
                    'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date
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
                    'Out Time' => '',
                    'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date
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
                        'Status' => $a->as_status.' '.$a->as_status_date,
                        'Late' => '',
                        'OT Hour' => '',
                        'Date' => $date,
                        'In Time' =>  '',
                        'Out Time' => '',
                        'AsStatus' => $a->as_status,
                        'AsStatusDate' => $a->as_status_date

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

        
        $leave_array = [];
            $absent_array = [];
            for($i=1; $i<=31; $i++) {
            $date = date('Y-m-d', strtotime('2021-01-'.$i));
            $leave = DB::table('hr_leave AS l')
                    ->where('l.leave_from', '<=', $date)
                    ->where('l.leave_to',   '>=', $date)
                    ->where('l.leave_status', '=', 1)
                    ->leftJoin('hr_as_basic_info AS b', function($q){
                        $q->on('b.associate_id', 'l.leave_ass_id');
                    })
                    ->pluck('b.as_id', 'b.associate_id');
            $leave_array[] = $leave;
            $absent_array[] = DB::table('holiday_roaster')
                    ->whereDate('date', $date)
                    ->whereIn('as_id', $leave)
                    ->get()->toArray();
            }
            // return "done";
            dump($leave_array,$absent_array);
            dd('end');
        
    }
    public function monthlycheck($value='')
    {
        $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-01-01')->get();
        $data = [];
        foreach ($user as $key => $e) {
            $query[] = DB::table('holiday_roaster')
                                      ->where('as_id', $e->associate_id)
                                      ->whereDate('date','<',$e->as_doj)
                                      ->get()->toArray();
            
        }
        dd($query);
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2021-01-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('hr_absent')
        //                               ->where('date', 'like', '2021-01%')
        //                               ->where('associate_id', $e->associate_id)
        //                               ->whereDate('date','<',$e->as_doj)
        //                               ->pluck('id','date');
        //     if(count($query) > 0){
        //         $data[$e->associate_id] = $query;
        //     }
        // }
        // dd($data);
        // $leave_array = [];
        //         $absent_array = [];
        //         for($i=1; $i<=31; $i++) {
        //         $date = date('Y-m-d', strtotime('2021-01-'.$i));
        //         $leave = DB::table('hr_attendance_mbm AS a')
        //                 ->where('a.in_time', 'like', $date.'%')
        //                 // ->where('a.as_id', 8958)
        //                 ->leftJoin('hr_as_basic_info AS b', function($q){
        //                     $q->on('b.as_id', 'a.as_id');
        //                 })
        //                 ->pluck('b.associate_id');
        //         $leave_array[] = $leave;
        //         $absent_array[] = DB::table('hr_absent')
        //                 ->whereDate('date', $date)
        //                 ->whereIn('associate_id', $leave)
        //                 ->get()->toArray();
        //         }
        //         dump($leave_array,$absent_array);
        //         dd('end');

                // $leave_array = [];
                // $absent_array = [];
                // for($i=1; $i<=31; $i++) {
                // $date = date('Y-m-d', strtotime('2021-01-'.$i));
                // $leave = DB::table('hr_absent AS a')
                //         ->where('a.date', '=', $date)
                //         ->whereIn('b.as_unit_id', [1, 4, 5])
                //         ->leftJoin('hr_as_basic_info AS b', function($q){
                //             $q->on('b.associate_id', 'a.associate_id');
                //         })
                //         ->pluck('b.as_id', 'b.associate_id');
                // $leave_array[] = $leave;
                // $absent_array[] = DB::table('hr_attendance_mbm')
                //         ->whereDate('in_time', $date)
                //         ->whereIn('as_id', $leave)
                //         ->get()->toArray();
                // }
                // dump($leave_array,$absent_array);
                // dd('end');
            // $leave_array = [];
            // $absent_array = [];
            // for($i=1; $i<=31; $i++) {
            // $date = date('Y-m-d', strtotime('2020-12-'.$i));
            // $leave = DB::table('hr_leave AS l')
            //         ->where('l.leave_from', '<=', $date)
            //         ->where('l.leave_to',   '>=', $date)
            //         ->where('l.leave_status', '=', 1)
            //         ->whereIn('b.as_unit_id', [1, 4, 5])
            //         ->leftJoin('hr_as_basic_info AS b', function($q){
            //             $q->on('b.associate_id', 'l.leave_ass_id');
            //         })
            //         ->pluck('b.as_id', 'b.associate_id');
            // $leave_array[] = $leave;
            // $absent_array[] = DB::table('hr_attendance_mbm')
            //         ->whereDate('in_time', $date)
            //         ->whereIn('as_id', $leave)
            //         ->get()->toArray();
            // }
            // // return "done";
            // dump($leave_array,$absent_array);
            // dd('end');

            $leave_array = [];
            $absent_array = [];
            for($i=1; $i<=31; $i++) {
                $date = date('Y-m-d', strtotime('2020-12-'.$i));
                $leave = DB::table('hr_leave AS l')
                        ->where('l.leave_from', '<=', $date)
                        ->where('l.leave_to',   '>=', $date)
                        ->where('l.leave_status', '=', 1)
                        ->leftJoin('hr_as_basic_info AS b', function($q){
                            $q->on('b.associate_id', 'l.leave_ass_id');
                        })
                        ->pluck('b.associate_id','b.as_id');
                $leave_array[] = $leave;
                $absent_array[] = DB::table('hr_absent')
                        ->whereDate('date', $date)
                        ->whereIn('associate_id', $leave)
                        ->delete();
            }
            return $absent_array;
            dump($leave_array,$absent_array);
            dd('end');

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
        

        
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2020-12-01')->get();
        // $data = [];
        // foreach ($user as $key => $e) {
        //     $query[] = DB::table('holiday_roaster')
        //                               ->where('as_id', $e->associate_id)
        //                               ->whereDate('date','<',$e->as_doj)
        //                               ->get()->toArray();
            
        // }
        // dd($query);
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2020-12-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('hr_absent')
        //                               ->where('date', 'like', '2020-12%')
        //                               ->where('associate_id', $e->associate_id)
        //                               ->whereDate('date','<',$e->as_doj)
        //                               ->pluck('id','date');
        //     if(count($query) > 0){
        //         $data[$e->associate_id] = $query;
        //     }
        // }
        // dd($data);
        // $leave_array = [];
        //         $absent_array = [];
        //         for($i=1; $i<=31; $i++) {
        //         $date = date('Y-m-d', strtotime('2020-12-'.$i));
        //         $leave = DB::table('hr_attendance_mbm AS a')
        //                 ->where('a.in_time', 'like', $date.'%')
        //                 // ->where('a.as_id', 8958)
        //                 ->leftJoin('hr_as_basic_info AS b', function($q){
        //                     $q->on('b.as_id', 'a.as_id');
        //                 })
        //                 ->pluck('b.associate_id');
        //         $leave_array[] = $leave;
        //         $absent_array[] = DB::table('hr_absent')
        //                 ->whereDate('date', $date)
        //                 ->whereIn('associate_id', $leave)
        //                 ->get()->toArray();
        //         }
        //         dump($leave_array,$absent_array);
        //         dd('end');

                // $leave_array = [];
                // $absent_array = [];
                // for($i=1; $i<=31; $i++) {
                // $date = date('Y-m-d', strtotime('2020-12-'.$i));
                // $leave = DB::table('hr_absent AS a')
                //         ->where('a.date', '=', $date)
                //         ->whereIn('b.as_unit_id', [1, 4, 5])
                //         ->leftJoin('hr_as_basic_info AS b', function($q){
                //             $q->on('b.associate_id', 'a.associate_id');
                //         })
                //         ->pluck('b.as_id', 'b.associate_id');
                // $leave_array[] = $leave;
                // $absent_array[] = DB::table('hr_attendance_mbm')
                //         ->whereDate('in_time', $date)
                //         ->whereIn('as_id', $leave)
                //         ->get()->toArray();
                // }
                // dump($leave_array,$absent_array);
                // dd('end');
            // $leave_array = [];
            // $absent_array = [];
            // for($i=1; $i<=31; $i++) {
            // $date = date('Y-m-d', strtotime('2020-12-'.$i));
            // $leave = DB::table('hr_leave AS l')
            //         ->where('l.leave_from', '<=', $date)
            //         ->where('l.leave_to',   '>=', $date)
            //         ->where('l.leave_status', '=', 1)
            //         ->whereIn('b.as_unit_id', [1, 4, 5])
            //         ->leftJoin('hr_as_basic_info AS b', function($q){
            //             $q->on('b.associate_id', 'l.leave_ass_id');
            //         })
            //         ->pluck('b.as_id', 'b.associate_id');
            // $leave_array[] = $leave;
            // $absent_array[] = DB::table('hr_attendance_mbm')
            //         ->whereDate('in_time', $date)
            //         ->whereIn('as_id', $leave)
            //         ->get()->toArray();
            // }
            // // return "done";
            // dump($leave_array,$absent_array);
            // dd('end');

            $leave_array = [];
            $absent_array = [];
            for($i=1; $i<=31; $i++) {
                $date = date('Y-m-d', strtotime('2020-12-'.$i));
                $leave = DB::table('hr_leave AS l')
                        ->where('l.leave_from', '<=', $date)
                        ->where('l.leave_to',   '>=', $date)
                        ->where('l.leave_status', '=', 1)
                        ->leftJoin('hr_as_basic_info AS b', function($q){
                            $q->on('b.associate_id', 'l.leave_ass_id');
                        })
                        ->pluck('b.associate_id','b.as_id');
                $leave_array[] = $leave;
                $absent_array[] = DB::table('hr_absent')
                        ->whereDate('date', $date)
                        ->whereIn('associate_id', $leave)
                        ->delete();
            }
            return $absent_array;
            dump($leave_array,$absent_array);
            dd('end');

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

    public function incrementexcel()
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
    
    public function increment()
    {
        $data = DB::table('hr_increment as ic')
                ->select('ic.*','a.as_id','a.as_unit_id','a.as_status','b.*')
                ->leftJoin('hr_as_basic_info as a','a.associate_id','ic.associate_id')
                ->leftJoin('hr_benefits as b','b.ben_as_id','ic.associate_id')
                ->where('ic.effective_date','<=',date('Y-m-d'))
                ->where('ic.status', 0)
                ->get();
                
        

        foreach ($data as $key => $d) {
            $gross = $d->current_salary + $d->increment_amount;
            $up['ben_current_salary'] = $gross;
            $up['ben_basic'] = ceil(($gross-1850)/1.5);
            $up['ben_house_rent'] = $gross -1850 - $up['ben_basic'];

            if($d->ben_bank_amount > 0 && $d->ben_cash_amount > 0){
                $up['ben_cash_amount'] = $gross - $d->ben_bank_amount;
            }else if ($d->ben_bank_amount > 0 && $d->ben_cash_amount == 0){
                $up['ben_bank_amount'] = $gross;
                $up['ben_cash_amount'] = 0;
            }else{
                $up['ben_bank_amount'] = 0;
                $up['ben_cash_amount'] = $gross;
            }

            DB::table('hr_benefits')->where('ben_id', $d->ben_id)->update($up);
            DB::table('hr_increment')->where('id', $d->id)->where('associate_id', $d->associate_id)->update(['status' => 1]);

            $tableName = get_att_table($d->as_unit_id);

            if($d->as_status == 1){

                $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $d->as_id, date('d')))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
            }

        }
        return count($data);
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

            $holiday = DB::table('holiday_roaster');
        }
    }

    public function getAttFile($date)
    {
        $outdate = Carbon::parse($date)->subDays(1)->toDateString();
        $outtime = DB::table('hr_attendance_mbm as a')
                    ->select('a.out_time','b.as_rfid_code')
                    ->leftJoin('hr_as_basic_info as b', 'a.as_id', 'b.as_id')
                    ->where('a.out_time', 'like', $outdate.'%')
                    ->get();
        
        foreach ($outtime as $key => $val) {
            
        }
    }

    public function setSalaryDate()
    {
        $data = DB::table('hr_monthly_salary')->whereIn('emp_status',[2,3,4,5,6,7])->get();

        foreach ($data as $key => $v) {
            $date = date('Y-m-d', strtotime($v->year.'-'.$v->month.'-'.($v->present+$v->absent+$v->holiday+$v->leave)));
            DB::table('hr_all_given_benefits')->where('associate_id', $v->as_id)->update([
                'salary_date' => $date
            ]);
        }

    }

    public function testBuyer()
    {

        $as_id = 4624;
        $getEmployee = Employee::where('as_id', $as_id)->first();
        $yearMonth = $this->year.'-'.$this->month;
        $start_date = date($yearMonth.'-01');
        $monthDayCount = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
        $partial = 0;

            //$getEmployee = Employee::where('as_id', $as_id)->first();

                if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $yearMonth){

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
                            $end_date = date('Y-m-t');
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
                        $leave = $att->leave ?? 0;
                        $holiday = $att->holiday ?? 0;
                        $absent = abs($maxDay - ($present + $leave + $holiday));

                        $ot_hour = 0;
                        $ot_num_min = min_to_ot();

                        if($att->ot > 0){
                            $otfm = explode(".", $att->ot);

                            if(isset($otfm[1])){
                                $ot_min = sprintf("%02d", round((('0.'.$otfm[1]) * 60)));
                                $ot_hour = $otfm[0] + $ot_num_min[$ot_min];
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
                            $adv_deduct = $getAddDeduct->advp_deduct;
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

                        dd($salary);



                        if($getSalary){
                            DB::table($this->salaryTable)->where('id', $getSalary->id)->update($salary);
                        }else{
                            $salary['as_id'] = $getEmployee->as_id;
                            $salary['month'] = $this->month;
                            $salary['year']  = $this->year;
                            DB::table($this->salaryTable)->insert($salary);
                        }
                    }else{
                        DB::table('error')->insert([
                            'msg' => $as_id.' salary not found'
                        ]);
                    }

                }else{
                    DB::table('error')->insert([
                        'msg' => $as_id.' out of range'
                    ]);
                }
    }

    public function insertRoaster()
    {

        $json = '';

        $ex = collect(json_decode($json))->toArray();




        $array = [

            ];

        dd(array_diff($array, array_keys($ex)));
        /*$dta = DB::table('hr_as_basic_info')
                 ->whereIn('as_oracle_code', $array)
                 ->where('as_unit_id', 2)
                 ->pluck('associate_id');
        $insert = [];
        foreach($dta as $k => $v){
            $insert[] = [
                'year' => 2021,
                'month' => '01',
                'as_id' => $v,
                'date' => '2021-01-29',
                'remarks' => 'General',
                'status' => 1
            ];
            $insert[] = [
                'year' => 2021,
                'month' => '01',
                'as_id' => $v,
                'date' => '2021-01-30',
                'remarks' => 'Holiday',
                'status' => 1
            ];
        }

        DB::table('holiday_roaster')->insert($insert);*/

        return 'success';
    }

    public function substitute()
    {
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
    }

    public function newMigrate()
    {
        $section = section_by_id();
        $designation = designation_by_id();

        $emps =  [];

        $insert = [];
        foreach ($emps as $key => $v) {
            $insert[$key] = $v;
            $insert[$key]['worker_area_id'] = null;
            $insert[$key]['as_oracle_code'] = $key;
            $insert[$key]['worker_department_id'] = null;
            $insert[$key]['worker_emp_type_id'] = null;
            if($v['worker_section_id'] != null){
                $k = $v['worker_section_id'];
                if(isset($section[$k])){

                    $insert[$key]['worker_area_id'] = $section[$k]['hr_section_area_id'];
                    $insert[$key]['worker_department_id'] = $section[$k]['hr_section_department_id'];
                }

            }
            if($v['worker_designation_id'] != null){
                $kd = $v['worker_designation_id'];
                if(isset($designation[$k])){
                    $insert[$key]['worker_emp_type_id'] = $designation[$kd]['hr_designation_emp_type'];
                }
            }

            $insert[$key]['worker_color_band_join'] = 1;
            $insert[$key]['worker_doctor_acceptance'] = 1;

        }

        DB::table('hr_worker_recruitment')->insert($insert);

        dd($insert);
    }

    public function migrateAll(){
        $data = DB::table('hr_worker_recruitment')
            ->where('worker_unit_id', 8)
            ->whereNotNull('worker_department_id')
            ->take(10)
            ->get();
            $d = [];
        foreach ($data as $key => $worker) {
            DB::beginTransaction();
            try {
                if ( ($worker->worker_unit_id != null || $worker->worker_unit_id != ''))
                {
                    $location= DB::table('hr_location')->where('hr_location_unit_id', $worker->worker_unit_id)->orderBy('hr_location_id', 'asc')->first(['hr_location_id']); 
                    $shift_exist= DB::table('hr_shift')
                            ->where('hr_shift_unit_id', $worker->worker_unit_id)
                            ->where('hr_shift_default', 1)
                            ->pluck('hr_shift_name')
                            ->first();
                    
                    $IDGenerator = (new  \App\Http\Controllers\Hr\IDGenerator)->generator2(array(
                        'department' => $worker->worker_department_id,
                        'date' => $worker->worker_doj
                    ));

                    

                    if (!empty($IDGenerator['error']))
                    {
                        /*toastr()->error($IDGenerator['error']);
                        return back()->with("error", $IDGenerator['error']);*/
                    }
                    else if(strlen($IDGenerator['id']) != 10)
                    {
                        /*toastr()->error("Unable to start the migration: Alphanumeric Associate's ID required with exactly 10 characters ");
                        return back()->with("error", "Unable to start the migration: Alphanumeric Associate's ID required with exactly 10 characters ");*/
                    }
                    else if($shift_exist == null)
                    {
                        /*toastr()->error("Unable to start the migration: Default Shift Doesn't Exist ");
                        return back()->with("error", "Unable to start the migration: Default Shift Doesn't Exist ");*/
                    }
                    else
                    {
                        //Default Shift Code
                        $default_shift= DB::table('hr_shift')
                        ->where('hr_shift_unit_id', $worker->worker_unit_id)
                        ->where('hr_shift_default', 1)
                        ->pluck('hr_shift_name')
                        ->first();
                        /*---INSERT INTO BASIC INFO TABLE---*/
                        $check = Employee::insert(array(
                            'as_emp_type_id'  => $worker->worker_emp_type_id,
                            'as_unit_id'      => $worker->worker_unit_id,
                            'as_shift_id'     => $default_shift,
                            'as_area_id'      => $worker->worker_area_id,
                            'as_department_id' => $worker->worker_department_id,
                            'as_section_id'  => $worker->worker_section_id,
                            'as_subsection_id'  => $worker->worker_subsection_id,
                            'as_designation_id' => $worker->worker_designation_id,
                            'as_doj'         => (!empty($worker->worker_doj)?date('Y-m-d',strtotime($worker->worker_doj)):null),
                            'temp_id'        => $IDGenerator['temp'],
                            'associate_id'   => $IDGenerator['id'],
                            'as_name'        => $worker->worker_name,
                            'as_gender'      => $worker->worker_gender,
                            'as_dob'         => (!empty($worker->worker_dob)?date('Y-m-d',strtotime($worker->worker_dob)):null),
                            'as_contact'     => $worker->worker_contact,
                            'as_ot'          => $worker->worker_ot,
                            'as_oracle_code' => $worker->as_oracle_code,
                            'as_oracle_sl'   => ($worker->as_oracle_code != ''?substr($worker->as_oracle_code,3, -1):''),
                            'as_rfid_code'   => $worker->as_rfid,
                            'as_pic'         => null,
                            'created_at'     => date("Y-m-d H:i:s"),
                            'created_by'     => Auth::user()->id,
                            'as_status'      => 1 ,
                            'as_location'    => $location->hr_location_id??''
                        ));

                        DB::table('hr_med_info')->insert(array(
                            'med_as_id'       => $IDGenerator['id'],
                            'med_height'      => $worker->worker_height,
                            'med_weight'      => $worker->worker_weight,
                            'med_tooth_str'   => $worker->worker_tooth_structure,
                            'med_blood_group' => $worker->worker_blood_group,
                            'med_ident_mark'  => (!empty($worker->worker_identification_mark)?$worker->worker_identification_mark:"N/A"),
                            'med_doct_comment'   => $worker->worker_doctor_comments,
                            'med_doct_conf_age'  => $worker->worker_doctor_age_confirm,
                            'med_doct_signature' => $worker->worker_doctor_signature
                        ));

                        DB::table('hr_as_adv_info')->insert(array(
                            'emp_adv_info_as_id' => $IDGenerator['id'],
                            'emp_adv_info_nid'   => $worker->worker_nid
                        ));


                        $t = DB::table('hr_worker_recruitment')->where('worker_id', $worker->worker_id)
                            ->delete();

                        // make default absent
                        DB::table('hr_absent')->insert([
                            'associate_id' => $IDGenerator['id'],
                            'date' => date('Y-m-d'),
                            'hr_unit' => $worker->worker_unit_id
                        ]);
                        $d[] = $IDGenerator['id'];
                        

                        //Cache::forget('employee_count');
                        DB::commit();
                        /*toastr()->success('Migration successful!');
                        $this->logFileWrite("Employee migration updated", $request->worker_id);
                        return back()->with("success", "Migration successful!");*/
                    }
                }
                else
                {
                    /*toastr()->error("Unable to start the migration: Please check the user's medical status or user already migrated!");
                    return back()->with("error", "Unable to start the migration: Please check the user's medical status or user already migrated!");*/
                }
                
            } catch (\Exception $e) {
                $d[] = $e->getMessage();
                DB::rollback();
            }
        }

        return $d;
    }

    public function set_emp_type_id()
    {
        $designation = designation_by_id();
        $data = DB::table('hr_worker_recruitment')
                    ->whereIn('worker_unit_id',[3,8])
                    ->whereIn('location_id',[9,11])
                    ->get();

        foreach ($data  as $key => $v) {
            if($v->worker_designation_id != null){
                $kd = $v->worker_designation_id;
                if(isset($designation[$kd])){
                    DB::table('hr_worker_recruitment')
                        ->where('worker_id', $v->worker_id)
                        ->update([
                            'worker_emp_type_id' => $designation[$kd]['hr_designation_emp_type']
                        ]);
                }
            }
            # code...
        }
        
        return 'done';
    }

    public function updateRFID()
    {
        $data = [];

        foreach ($data as $key => $v) {
            DB::table('hr_as_basic_info')
                ->where('as_unit_id', 3)
                ->where('as_location', 9)
                ->where('as_oracle_code', $key)
                ->update([
                    'as_rfid_code' => $v['as_rfid']
                ]);
        }

        return 'done';
    }

    public function advBn()
    {
        $data = array(
                 '20K1469E' => array('SALARY' => '9500', 'J_SAL' => '9500', 'MARRIED' => 'M', 'FNAME' => 'MD.Demo', 'HNAME' => '', 'PAD1' => 'Demo', 'PAD2' => '', 'PPOST' => 'HOLDIBARI-5470', 'PTHANA' => 'PIRGANJ', 'PDIST' => '32', 'CAD1' => 'SALNA', 'CAD2' => '', 'CPOST' => 'SALNA BAZAR', 'CTHANA' => '154', 'CDIST' => '3', 'B_NAME' => 'মোঃ লেবু মন্ডল', 'MOTHER' => 'মোছাঃ রোকেয়া বেগম', 'BFATHER' => 'মোঃ আব্দুল মান্নান মন্ডল', 'BGRAM' => 'বড় বদনাপাড়া', 'BPOST' => 'হলদীবাড়ী', 'MNAME' => 'MST.ROKEYA BEGUM', 'HOUSE_NO' => 'সালনা', 'ROAD_NO' => '', 'PO' => 'সালনা বাজার', 'CHILDREN' => '', 'CLASS' => 'EIGHT', 'RELEG' => 'I')
            );


        $as = DB::table('hr_as_basic_info')
            ->where('as_unit_id', 3)
            ->pluck('associate_id','as_oracle_code');

        $bn = DB::table('hr_employee_bengali')
                ->whereIn('hr_bn_associate_id', $as)
                ->pluck('hr_bn_associate_id')->toArray();
        $insert = [];
        foreach ($data as $key => $v) {
            if(isset($as[$key])){
                DB::table('hr_as_adv_info')
                    ->where('emp_adv_info_as_id', $as[$key])
                    ->update([
                        'emp_adv_info_nationality' => 'BANGLADESHI', 
                        'emp_adv_info_fathers_name' => $v['FNAME'], 
                        'emp_adv_info_mothers_name' => $v['MNAME'], 
                        'emp_adv_info_spouse' => $v['HNAME'], 
                        'emp_adv_info_children' => $v['CHILDREN'], 
                        //'emp_adv_info_religion' => $v['FNAME'], 
                        'emp_adv_info_per_vill' => $v['PAD1'],
                        'emp_adv_info_per_po' => $v['PPOST'],
                        'emp_adv_info_per_dist' => $v['PDIST'],
                        'emp_adv_info_pres_house_no' => $v['CAD1'],
                        'emp_adv_info_pres_road' => $v['CAD2'],
                        'emp_adv_info_pres_po' => $v['CPOST'],
                        'emp_adv_info_pres_dist' => $v['CDIST'],
                        'emp_adv_info_pres_upz' => $v['CTHANA'],
                    ]);

                if(in_array($as[$key], $bn)){
                    DB::table('hr_employee_bengali')
                        ->where('hr_bn_associate_id', $as[$key])
                        ->update([
                        'hr_bn_associate_name' => $v['B_NAME'], 
                        'hr_bn_father_name' => $v['BFATHER'], 
                        'hr_bn_mother_name' => $v['MOTHER'], 
                        'hr_bn_permanent_village' => $v['BGRAM'],
                        'hr_bn_permanent_po' => $v['BPOST'],
                        'hr_bn_present_road' => $v['ROAD_NO'],
                        'hr_bn_present_house' => $v['HOUSE_NO'],
                        'hr_bn_present_po' => $v['PO']
                    ]);

                }else{
                    $insert[$key] = [
                        'hr_bn_associate_id' => $as[$key],
                        'hr_bn_associate_name' => $v['B_NAME'], 
                        'hr_bn_father_name' => $v['BFATHER'], 
                        'hr_bn_mother_name' => $v['MOTHER'], 
                        'hr_bn_permanent_village' => $v['BGRAM'],
                        'hr_bn_permanent_po' => $v['BPOST'],
                        'hr_bn_present_road' => $v['ROAD_NO'],
                        'hr_bn_present_house' => $v['HOUSE_NO'],
                        'hr_bn_present_po' => $v['PO']
                    ];
                }
            }
        }
        DB::table('hr_employee_bengali')->insert($insert);

        return 'success';
    }

   /* public function processLeftSalary()
    {
        $data = [];
        $datas = DB::table('hr_as_basic_info as b')
            ->whereIn('b.associate_id', array_keys($data))
            ->leftJoin('hr_benefits as ben', 'b.associate_id','ben.ben_as_id')
            ->where('b.as_unit_id', 2)
            ->where('b.as_status', '!=', 1)
            ->get();
        foreach ($datas as $key => $v) {
            \App\Helpers\EmployeeHelper::processPartialSalary($v, $data[$v->associate_id]['Date'],2);
        }
        return 'done';
    }*/

    public function processBuyerLeftSalary()
    {
        
        $datas = DB::table('hr_monthly_salary as s')
            ->select('s.*','b.as_id as ass')
            ->leftJoin('hr_as_basic_info as b', 'b.associate_id','s.as_id')
            ->where('b.as_unit_id', 2)
            ->where('s.month','01')
            ->where('s.emp_status',5)
            ->whereNull('s.disburse_date')
            ->where('s.location_id',7)
            ->get();

        $as_id = collect($datas)->pluck('ass');


        $buyer_sal = DB::table('hr_buyer_salary_ceil4 as s')
                    ->select('s.*')
                    ->leftJoin('hr_as_basic_info as b','b.as_id','s.as_id')
                    ->whereIn('s.as_id', $as_id)
                    ->where('b.as_unit_id', 2)
                    ->where('s.month', '01')
                    ->get()
                    ->keyBy('as_id');

        foreach ($datas as $key => $v) {
            if(isset($buyer_sal[$v->ass])){
                    $sl = $buyer_sal[$v->ass];
                    $deductCost = ($sl->adv_deduct + $sl->cg_deduct + $sl->food_deduct + $sl->others_deduct);
                    $ot = ($v->ot_rate*$sl->ot_hour);
                    $lvadjust = $sl->leave_adjust;
                    $deductSalaryAdd = $sl->salary_add;
            }else{
                    $adv_deduct = 0;
                        $cg_deduct = 0;
                        $food_deduct = 0;
                        $others_deduct = 0;
                        $salary_add = 0;
                        $bonus_add = 0;
                        $deductCost = 0;
                        $productionBonus = 0;

                        $getAddDeduct = DB::table('hr_salary_add_deduct')
                            ->where('associate_id', $v->as_id)
                            ->where('month', '=', '01')
                            ->where('year', '=', 2021)
                            ->first();

                        if($getAddDeduct != null){
                            $advp_deduct = $getAddDeduct->advp_deduct;
                            $cg_deduct = $getAddDeduct->cg_deduct;
                            $food_deduct = $getAddDeduct->food_deduct;
                            $others_deduct = $getAddDeduct->others_deduct;
                            $salary_add = $getAddDeduct->salary_add;
                            $deductSalaryAdd = $salary_add;

                            $deductCost = ($advp_deduct + $cg_deduct + $food_deduct + $others_deduct);
                            $productionBonus = $getAddDeduct->bonus_add;
                        }

                    $ot = DB::table('hr_buyer_att_ceil4')
                            ->where('as_id', $v->ass)
                            ->where('in_date','<=','2021-01-30')
                            ->sum('ot_hour');

                    $ot_num_min = min_to_ot();

                        if($ot > 0){
                            $otfm = explode(".", $ot);

                            if(isset($otfm[1])){
                                $ot_min = round((('0.'.$otfm[1]) * 60));
                                $ot_hour = $otfm[0] + ($ot_min == 1? 1:($ot_num_min[$ot_min]));
                            }else{
                                $ot_hour = $ot;
                            }
                        }

                    $ot = $ot*$v->ot_rate;
                    $lvadjust = 0;


            }
            $at = [
                'present' => $v->present,
                'absent' => $v->absent,
                'holiday' => $v->holiday,
                'late_count' => $v->late_count,
                'leave' => $v->leave,
                'ot_rate' => $v->ot_rate,
                'stamp' => 10,
                'pay_type' => null,
                'emp_status' => 5,
                'disburse_date' => null
            ];

            $attBonus = 0;
            $salary_date = $v->present + $v->holiday + $v->leave + $v->absent;
            $stamp = 10;
            

            $perDayBasic = round(($v->basic / 30),2);
            $perDayGross = round(($v->gross /  31),2);
            $getAbsentDeduct = $v->absent * $perDayBasic;

            
            
            // get salary payable calculation
            $salaryPayable = round((($perDayGross*$salary_date) - ($getAbsentDeduct + $deductCost + $stamp)), 2);
            

            $totalPayable = ceil((float)($salaryPayable + $ot   + $v->production_bonus + $lvadjust));
            
            $at['total_payable'] = $totalPayable;
            $at['cash_payable'] = $totalPayable;
            $at['bank_payable'] = 0;
            $at['salary_payable'] = $salaryPayable;
            $at['leave_adjust'] = $lvadjust;
            $at['absent_deduct'] = $getAbsentDeduct;

            if(isset($buyer_sal[$v->ass])){

                DB::table('hr_buyer_salary_ceil4')
                    ->where('as_id', $v->ass)
                    ->where('id', $sl->id)
                    ->update($at);
            }else{
                $at['month'] = '01';
                $at['as_id'] = $v->ass;
                $at['year'] = 2021;

                $at['gross'] = $v->gross;
                $at['basic'] = $v->basic;
                $at['house'] = $v->house;
                $at['medical'] = $v->medical;
                $at['transport'] = $v->transport;
                $at['food'] = $v->food;
                $at['late_count'] = $v->late_count;
                $at['absent_deduct'] = $getAbsentDeduct;
                $at['adv_deduct'] = $adv_deduct;
                $at['cg_deduct'] = $cg_deduct;
                $at['food_deduct'] = $food_deduct;
                $at['others_deduct'] = $others_deduct;
                $at['salary_add'] = $salary_add;
                $at['bonus_add'] = $bonus_add;
                $at['leave_adjust'] = $v->leave_adjust;
                $at['ot_hour'] = $ot_hour;
                $at['attendance_bonus'] = 0;
                $at['production_bonus'] = $productionBonus;
                $at['stamp'] = 10;
                $at['salary_payable'] = $salaryPayable;
                $at['total_payable'] = $totalPayable;
                $at['cash_payable'] = $totalPayable;
                $at['bank_payable'] = 0;
                $at['tds'] = 0;
                $at['pay_status'] = $v->pay_status;
                $at['pay_type'] = $v->pay_type;
                $at['emp_status'] = $v->emp_status;
                $at['ot_status'] = $v->ot_status;
                $at['designation_id'] = $v->designation_id;
                $at['subsection_id'] = $v->sub_section_id;
                $at['location_id'] = $v->location_id;
                $at['unit_id'] = $v->unit_id;
                DB::table('hr_buyer_salary_ceil4')->insert($at);
            }


            
            
        }


        DB::table('hr_buyer_salary_ceil4')
            ->whereNotIn('as_id', $as_id)
            ->where('emp_status', 5)
            ->where('month', '01')
            ->update([
                'disburse_date'=> '2021-01-30'
            ]);


        return 'hi';
    }


    public function addSalary()
    {
        $emp = DB::table('hr_as_basic_info')
                ->where('as_unit_id', 8)
                ->pluck('associate_id', 'as_oracle_code');

        $ben = DB::table('hr_benefits')
                ->whereIn('ben_as_id', $emp)
                ->get()
                ->keyBy('ben_as_id');
        $data = [];

        $up = []; $ins = [];

        foreach ($data as $key => $val) {

            if(isset($emp[$key])){
                $ass = $emp[$key];
                // check salary
                $up['ben_joining_salary'] = $val['J_SAL'];
                $up['ben_current_salary'] = $val['SALARY'];
                $up['ben_basic'] = ceil(($val['SALARY']-1850)/1.5);
                $up['ben_house_rent'] = $val['SALARY'] -1850 - $up['ben_basic'];

                // bank 
                if($val['dbbl'] != '#N/A'){
                    $up['ben_bank_amount'] = $val['bank'];
                    $up['ben_cash_amount'] = $val['SALARY'] - $val['bank'] ;
                    $up['ben_tds_amount'] = $val['tds'];
                    $up['bank_name'] = 'dbbl';
                    $up['bank_no'] = $val['dbbl'];

                }else if($val['rocket'] != '#N/A'){
                // rocket
                    $up['ben_bank_amount'] = $val['SALARY'];
                    $up['ben_cash_amount'] = 0 ;
                    $up['bank_name'] = 'rocket';
                    $up['bank_no'] = $val['rocket'];
                }else{
                // cash
                    $up['ben_bank_amount'] = 0;
                    $up['ben_cash_amount'] = $val['SALARY'] ;

                }


                if(isset($ben[$ass])){
                    DB::table('hr_benefits')->where('ben_as_id', $ass)->update($up);
                }else{
                    $ins[$ass]['ben_as_id'] = $ass;
                    $ins[$ass] = $up;
                    $ins[$ass]['ben_medical'] = 600;
                    $ins[$ass]['ben_transport'] = 350;
                    $ins[$ass]['ben_food'] = 900;
                    $ins[$ass]['ben_status'] = 1;
                    $ins[$ass]['ben_as_id'] = $ass;
                }
            }
        }
        
        DB::table('hr_benefits')->insert($ins);
    }


    public function gross2pay()
    {
        $data = DB::table('hr_attendance_mbm as a')
                 ->select('b.associate_id', DB::raw('ben.ben_current_salary/28 as gs'))
                 ->leftJoin('hr_as_basic_info as b', 'b.as_id', 'a.as_id')
                 ->leftJoin('hr_benefits as ben', 'ben.ben_as_id', 'b.associate_id')
                 ->whereIn('b.as_unit_id',[1,4,5])
                 ->where('a.in_date', '2021-02-01')
                 ->get();
        
        foreach($data as $d){
            DB::table('hr_salary_add_deduct')
            ->insert([
              'associate_id' => $d->associate_id,
              'month' => '02',
              'year' => '2021',
              'salary_add' => ceil(2 * $d->gs),
              'salary_add_reason' => '২১শে ফেব্রুয়ারী'
            ]);
            
        }
        
    }
    public function addRocket(){
        $data = [];
        
        $emp = DB::table('hr_as_basic_info as b')
                ->select('b.as_id','b.associate_id','ben.ben_current_salary')
                ->leftJoin('hr_benefits as ben','b.associate_id','ben.ben_as_id')
                ->whereIn('associate_id', array_keys($data))
                ->get()->keyBy('associate_id');
        
        foreach($data as $key => $d){
            DB::table('hr_benefits')
                ->where('ben_as_id', $key)
                ->update([
                    'ben_bank_amount' => $emp[$key]->ben_current_salary,
                    'ben_cash_amount' => 0,
                    'bank_name' => 'rocket',
                    'bank_no' => $d['ACCOUNT NUMBER']
                  ]);
                  
           $queue = (new ProcessUnitWiseSalary('hr_attendance_mbm', '02', 2021, $emp[$key]->as_id, 28))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
        }
        return $emp;
    }


    


    public function giveRoaster()
    {
        $data = [];

        $emp = DB::table('hr_as_basic_info')
                ->whereIn('as_oracle_code', array_keys($data))
                ->where('as_unit_id', 8)
                ->pluck('as_oracle_code','as_id');

        foreach ($emp as $key => $v) {
            $ins[] = [
                'as_id' => $key,
                'day' => ucfirst(strtolower($data[$v]['ROSTER']))
            ];
        }
        DB::table('hr_roaster_holiday')
            ->insert($ins);

        return $emp;

    }

    public function makeHoliday()
    {
        $first_day = date('Y-m-01');
        $day_count = date('t');
        $date_by_day = [];
        for ($i = 1; $i <= $day_count; $i++) {
            $date = date('Y/m').'/'.$i;
            $date = date('Y-m-d', strtotime($date));
            $day = date('D', strtotime($date));
            $date_by_day[$day][] = $date;
        }

           
        $holiday = DB::table('hr_roaster_holiday as r')
            ->select('r.*','b.associate_id')
            ->where('b.shift_roaster_status', 1)
            ->whereIn('b.as_unit_id', [3,8])
            ->leftJoin('hr_as_basic_info as b','b.as_id','r.as_id')
            ->get();



        $holidays = collect($holiday)
                        ->groupBy('day');
        

        $exists = DB::table('holiday_roaster')
            ->select(
                DB::raw("CONCAT(date,as_id) AS pp"),
                'remarks'
            )
            ->where('month', date('m'))
            ->where('year', date('Y'))
            ->get()
            ->keyBy('pp');

        foreach ($holidays as $key => $emp) {
            $ins = [];
            foreach ($emp as $k1 => $h) {
                if(isset($date_by_day[$key])){    
                    foreach ($date_by_day[$key] as $k => $v) {
                        if(!isset($exists[$v.$h->associate_id])){

                            $ins[$v.$h->associate_id] = array(
                                'year'  => date('Y'),
                                'month' => date('m'),
                                'as_id' => $h->associate_id,
                                'date'  =>  $v,
                                'remarks'   => 'Day Off',
                                'status' => 1
                            );
                        }
                    }
                }
            }
            DB::table('holiday_roaster')->insertOrIgnore($ins);
        }

        return 'done';
    }


    public function findManual()
    {
        $designation = designation_by_id();
        $att = DB::table('hr_attendance_mbm as a')
            ->select('a.id','a.as_id','b.associate_id','b.as_name','b.as_doj','b.as_designation_id','b.as_unit_id','a.in_date','a.in_time','a.out_time','a.ot_hour')
            ->leftJoin('hr_as_basic_info as b','a.as_id','b.as_id')
            ->where('a.in_date','>=','2021-02-01')
            ->where('a.in_date','<=','2021-02-28')
            ->where('a.remarks','BM')
            ->get();

         // find event history related to this fields
        $event_ids = collect($att)->pluck('id');
        $ev =  DB::table('event_history')
            ->select('previous_event->id as id','previous_event','modified_event')
            ->whereIn('previous_event->id',$event_ids)
            ->orderBy('id','desc')
            ->get()
            ->keyBy('id');


        $actual = DB::table('hr_attendance_history')
                    ->select('as_id','att_date','raw_data')
                    ->whereIn('unit_id', [1,4,5])
                    ->where('att_date','>=','2021-02-01')
                    ->where('att_date','<=','2021-02-28')
                    ->get();
        $actual = collect($actual)->groupBy('as_id');
        $bm = [];
        foreach ($att as $key => $v) {
            $v->as_designation_id = isset($designation[$v->as_designation_id])?$designation[$v->as_designation_id]['hr_designation_name']:'';
            if($v->as_unit_id == 1)
                $v->as_unit_id = 'MBM';
            else if($v->as_unit_id == 4)
                $v->as_unit_id = 'MFW';
            else if($v->as_unit_id == 5)
                $v->as_unit_id = 'MBM-2';

            $v->prev_in = null;
            $v->prev_out = null;
            $v->prev_ot_hour = null;
            if(isset($actual[$v->as_id])){
                $raw = collect($actual[$v->as_id]);
                // check exist
                $fndIn = $raw->where('raw_data', $v->in_time)->first();
                $fndOut = $raw->where('raw_data', $v->out_time)->first();

                if($fndIn && $fndOut){

                }else{
                    
                    if($fndIn){
                        //find out punch from event history
                        if(isset($ev[$v->id])){
                            $v->prev_out = json_decode($ev[$v->id]->previous_event)->out_time;
                            $v->prev_ot_hour = json_decode($ev[$v->id]->previous_event)->ot_hour;
                        }
                    }     
                    if($fndOut){
                        //find in punch from event history
                        if(isset($ev[$v->id])){
                            $v->prev_ot_hour = json_decode($ev[$v->id]->previous_event)->ot_hour;
                            $v->prev_in = json_decode($ev[$v->id]->previous_event)->in_time;
                        }
                    }

                    $bm[] = $v; 
                }
            }else{
                $bm[] = $v;
            }
        }

        
        //return $event_ids;

       

        $chk = collect($bm)->groupBy('as_id');
        $chk = $chk->filter(function($i){
            return count($i) >= 5;
        });

        $arr = [];

        foreach ($chk as $key => $v) {
            foreach ($v as $k => $t) {
                $arr[] = $t; 
            }
        }

        /*$chk = collect($chk)->map( function($i){
            $np = collect($i)->first();
            $ot_hour = collect($i)->sum('ot_hour');
            $prev_hour = collect($i)->sum('prev_ot_hour');
            return array(
                'Associate ID' => $np->associate_id,
                'Name' => $np->as_name,
                'Designation' => $np->as_designation_id,
                'Unit' => $np->as_unit_id,
                'DoJ' => $np->as_doj,
                'OT' => $ot_hour,
                'Prev OT' => $prev_hour,
                'Changed' => count($i)
            );
        });*/

        return (new FastExcel(collect($arr)))->download('mbm_all_final.xlsx');

    }

    public function processLeftSalary()
    {
        $data = array(
            '19D700071P' => array('Date' => '2020-10-07'),
            '14K000025A' => array('Date' => '2020-10-04'),
            '19F100083N' => array('Date' => '2020-10-09'),
            '17A100091N' => array('Date' => '2020-10-05'),
            '19G100102N' => array('Date' => '2020-10-17'),
            '19C100154N' => array('Date' => '2020-10-30'),
            '15L100156N' => array('Date' => '2020-10-30'),
            '17L100201N' => array('Date' => '2020-10-25'),
            '18A100252N' => array('Date' => '2020-10-25'),
            '19D100270N' => array('Date' => '2020-10-16'),
            '18D100280N' => array('Date' => '2020-10-27'),
            '18K100298N' => array('Date' => '2020-10-27'),
            '18D500031O' => array('Date' => '2020-10-11'),
            '17A500041O' => array('Date' => '2020-10-11'),
            '02F500187O' => array('Date' => '2020-10-27'),
            '15M100373N' => array('Date' => '2020-10-09'),
            '13A100430N' => array('Date' => '2020-10-07'),
            '16L100438N' => array('Date' => '2020-10-05'),
            '12F100447N' => array('Date' => '2020-10-06'),
            '13J100460N' => array('Date' => '2020-10-13'),
            '10B100486N' => array('Date' => '2020-10-17'),
            '13D100504N' => array('Date' => '2020-10-30'),
            '18D100598N' => array('Date' => '2020-10-06'),
            '06C100610N' => array('Date' => '2020-10-06'),
            '18K100615N' => array('Date' => '2020-10-11'),
            '18D100642N' => array('Date' => '2020-10-19'),
            '17L100748N' => array('Date' => '2020-10-06'),
            '17J100753N' => array('Date' => '2020-10-06'),
            '12L100771N' => array('Date' => '2020-10-09'),
            '19G100774N' => array('Date' => '2020-10-26'),
            '17M100846N' => array('Date' => '2020-10-12'),
            '18C100857N' => array('Date' => '2020-10-17'),
            '15L100982N' => array('Date' => '2020-10-06'),
            '18B101021N' => array('Date' => '2020-10-10'),
            '18C101023N' => array('Date' => '2020-10-20'),
            '15L101050N' => array('Date' => '2020-10-13'),
            '19A101070N' => array('Date' => '2020-10-10'),
            '19B101075N' => array('Date' => '2020-10-24'),
            '19B101080N' => array('Date' => '2020-10-23'),
            '19C101108N' => array('Date' => '2020-10-30'),
            '19F101115N' => array('Date' => '2020-10-30'),
            '13F101243N' => array('Date' => '2020-10-20'),
            '13D101258N' => array('Date' => '2020-10-23'),
            '19F101261N' => array('Date' => '2020-10-12'),
            '19F101275N' => array('Date' => '2020-10-27'),
            '94L101326N' => array('Date' => '2020-10-14'),
            '17J101359N' => array('Date' => '2020-10-14'),
            '19J000075A' => array('Date' => '2020-10-12'),
            '11D101557N' => array('Date' => '2020-10-05'),
            '18C101605N' => array('Date' => '2020-10-14'),
            '19E101640N' => array('Date' => '2020-10-09'),
            '13C101726N' => array('Date' => '2020-10-30'),
            '18A101756N' => array('Date' => '2020-10-12'),
            '18G102009N' => array('Date' => '2020-10-02'),
            '19K000404A' => array('Date' => '2020-10-28'),
            '19K106051N' => array('Date' => '2020-10-06'),
            '19K106120N' => array('Date' => '2020-10-06'),
            '19L106177N' => array('Date' => '2020-10-19'),
            '19L106253N' => array('Date' => '2020-10-21'),
            '19M106471N' => array('Date' => '2020-10-09'),
            '19M106551N' => array('Date' => '2020-10-20'),
            '19L106562N' => array('Date' => '2020-10-01'),
            '19M106584N' => array('Date' => '2020-10-14'),
            '20C000452A' => array('Date' => '2020-10-06'),
            '20K106765N' => array('Date' => '2020-10-07'),
            '20K106782N' => array('Date' => '2020-10-06'),
            '20K106786N' => array('Date' => '2020-10-27'),
            '20K106790N' => array('Date' => '2020-10-27'),
            '20K700397P' => array('Date' => '2020-10-09'),
            '20K500889O' => array('Date' => '2020-10-18'),
            '20K106809N' => array('Date' => '2020-10-13'),
            '20K500892O' => array('Date' => '2020-10-18'),
            '20K500896O' => array('Date' => '2020-10-06'),
            '20K700399P' => array('Date' => '2020-10-06'),
            '20K106822N' => array('Date' => '2020-10-16'),
            '20K106825N' => array('Date' => '2020-10-18'),
            '20K106852N' => array('Date' => '2020-10-27'),
            '20L107680N' => array('Date' => '2020-10-06'),
            '20L107681N' => array('Date' => '2020-10-06')
        );
        $ids = DB::table('hr_monthly_salary')
            ->where('month','10')
            ->whereIn('unit_id', [1,4,5])
            ->whereIn('emp_status',[2,5])
            ->whereIn('as_id', array_keys($data))
            ->pluck('as_id');


        $datas = DB::table('hr_as_basic_info as b')
            ->whereIn('b.associate_id',$ids)
            ->leftJoin('hr_benefits as ben', 'b.associate_id','ben.ben_as_id')
            ->get();

        $ben = DB::table('hr_all_given_benefits')
                ->whereIn('associate_id',$ids)
                ->groupBy('associate_id')
                ->get()
                ->keyBy('associate_id');
        $h = [];
        foreach ($datas as $key => $v) {
            $dt = $data[$v->associate_id]['Date'];
            \App\Helpers\EmployeeHelper::processPartialSalary($v, $dt ,$v->as_status);

            if(isset($ben[$v->associate_id])){
                $ldt = $ben[$v->associate_id]->status_date??$dt;

                if($dt >=  $ldt || $v->as_status == 2){

                    $ldt = Carbon::parse($dt)->addDay()->toDateString();
                    

                }
                DB::table('hr_as_basic_info')
                    ->where('as_id', $v->as_id)
                    ->update(['as_status_date' => $ldt]);

               $h[$v->associate_id] = $dt;
               DB::table('hr_all_given_benefits')
                    ->where('associate_id', $v->associate_id)
                    ->update(
                        [
                            'status_date' => $ldt,
                            'salary_date' => $dt 
                        ]
                    );

            }
        }
        return $h;
    }


    public function setIncrementMonth()
    {
        $data = DB::table('hr_as_basic_info')
            ->select('associate_id','as_doj','as_emp_type_id')
            ->get();

        $insert = [];

        foreach ($data as $key => $v) {
            if($v->as_doj){
                if($v->as_emp_type_id == 3 && $v->as_doj < '2018-12-01'){
                    $insert[$v->associate_id] = array(
                        'associate_id' => $v->associate_id,
                        'month' => 'Dec',
                        'remarks' => 'G'
                    );
                }else{
                    $insert[$v->associate_id] = array(
                        'associate_id' => $v->associate_id,
                        'month' => date('M', strtotime($v->as_doj)),
                        'remarks' => null
                    );
                }
            }
        }

        $d = array_chunk($insert, 300);

        foreach ($d as $key => $in) {
            DB::table('hr_increment_month')
                    ->insertOrIgnore($in);
        }

        dd($d);
    }


    public function updateDept()
    {
        $data = array(
            '20K075136L' => array('subsection' => '281', 'floor' => '104'),
            '16C000094A' => array('subsection' => '108', 'floor' => '104'),
            '16E075022L' => array('subsection' => '281', 'floor' => '104'),
            '20L700608P' => array('subsection' => '148', 'floor' => '104'),
            '17H101434N' => array('subsection' => '148', 'floor' => '68'),
            '18A075014L' => array('subsection' => '280', 'floor' => '104'),
            '17L101421N' => array('subsection' => '148', 'floor' => '71'),
            '18J101424N' => array('subsection' => '202', 'floor' => '104'),
            '15G999901Q' => array('subsection' => '202', 'floor' => '104'),
            '15L999900Q' => array('subsection' => '202', 'floor' => '104'),
            '20J700610P' => array('subsection' => '202', 'floor' => '104'),
            '16E999903Q' => array('subsection' => '202', 'floor' => '104'),
            '15K999902Q' => array('subsection' => '202', 'floor' => '104'),
            '21B110437N' => array('subsection' => '202', 'floor' => '104'),
            '20K700611P' => array('subsection' => '202', 'floor' => '104'),
            '17A101456N' => array('subsection' => '202', 'floor' => '104'),
            '19D101430N' => array('subsection' => '202', 'floor' => '104'),
            '16H101466N' => array('subsection' => '202', 'floor' => '104'),
            '20H700609P' => array('subsection' => '202', 'floor' => '104'),
            '20M109487N' => array('subsection' => '202', 'floor' => '104'),
            '20K000529A' => array('subsection' => '105', 'floor' => '104'),
            '16C101443N' => array('subsection' => '202', 'floor' => '104'),
            '16C101447N' => array('subsection' => '202', 'floor' => '104'),
            '18B101379N' => array('subsection' => '148', 'floor' => '69'),
            '16M101437N' => array('subsection' => '148', 'floor' => '68'),
            '15M000132A' => array('subsection' => '105', 'floor' => '104'),
            '15L000126A' => array('subsection' => '105', 'floor' => '104'),
            '20K000528A' => array('subsection' => '105', 'floor' => '104'),
            '16D000124A' => array('subsection' => '105', 'floor' => '104'),
            '18A000106A' => array('subsection' => '105', 'floor' => '104'),
            '19M000536A' => array('subsection' => '105', 'floor' => '104'),
            '17A000120A' => array('subsection' => '105', 'floor' => '104'),
            '16E000130A' => array('subsection' => '105', 'floor' => '104'),
            '19A000105A' => array('subsection' => '105', 'floor' => '104'),
            '17G000119A' => array('subsection' => '105', 'floor' => '104'),
            '21B000518A' => array('subsection' => '105', 'floor' => '104'),
            '16D000123A' => array('subsection' => '105', 'floor' => '104'),
            '16C000133A' => array('subsection' => '105', 'floor' => '104'),
            '18G000117A' => array('subsection' => '105', 'floor' => '104'),
            '16D000128A' => array('subsection' => '105', 'floor' => '104'),
            '17H000109A' => array('subsection' => '105', 'floor' => '104'),
            '21B000516A' => array('subsection' => '105', 'floor' => '104'),
            '16G000118A' => array('subsection' => '105', 'floor' => '104'),
            '20K000526A' => array('subsection' => '105', 'floor' => '104'),
            '19L000540A' => array('subsection' => '105', 'floor' => '104'),
            '18A000116A' => array('subsection' => '105', 'floor' => '104'),
            '21A000520A' => array('subsection' => '105', 'floor' => '104'),
            '16K000122A' => array('subsection' => '105', 'floor' => '104'),
            '16G101433N' => array('subsection' => '148', 'floor' => '69'),
            '15L101420N' => array('subsection' => '148', 'floor' => '69'),
            '16H101431N' => array('subsection' => '148', 'floor' => '69'),
            '17C101419N' => array('subsection' => '148', 'floor' => '68'),
            '15K101410N' => array('subsection' => '148', 'floor' => '71'),
            '15K101439N' => array('subsection' => '148', 'floor' => '68'),
            '17B101414N' => array('subsection' => '148', 'floor' => '69'),
            '17K101422N' => array('subsection' => '148', 'floor' => '68'),
            '19D101382N' => array('subsection' => '148', 'floor' => '69'),
            '21B700481P' => array('subsection' => '342', 'floor' => '71'),
            '18E101478N' => array('subsection' => '142', 'floor' => '70'),
            '20K700497P' => array('subsection' => '342', 'floor' => '71'),
            '17D101508N' => array('subsection' => '142', 'floor' => '70'),
            '21B109463N' => array('subsection' => '142', 'floor' => '70'),
            '17L101538N' => array('subsection' => '142', 'floor' => '70'),
            '21B109472N' => array('subsection' => '142', 'floor' => '70'),
            '20M109533N' => array('subsection' => '142', 'floor' => '70'),
            '19K700523P' => array('subsection' => '342', 'floor' => '71'),
            '20L700524P' => array('subsection' => '342', 'floor' => '71'),
            '21A700459P' => array('subsection' => '230', 'floor' => '68'),
            '17M000110A' => array('subsection' => '289', 'floor' => '104'),
            '21A700472P' => array('subsection' => '342', 'floor' => '71'),
            '16D101399N' => array('subsection' => '148', 'floor' => '69'),
            '18J700342P' => array('subsection' => '343', 'floor' => '71'),
            '21B700452P' => array('subsection' => '342', 'floor' => '71'),
            '20L700527P' => array('subsection' => '342', 'floor' => '71'),
            '19A101417N' => array('subsection' => '148', 'floor' => '69'),
            '17M101444N' => array('subsection' => '148', 'floor' => '70'),
            '20L700542P' => array('subsection' => '342', 'floor' => '71'),
            '20L000523A' => array('subsection' => '289', 'floor' => '104'),
            '21B700626P' => array('subsection' => '230', 'floor' => '68'),
            '20K700486P' => array('subsection' => '230', 'floor' => '68'),
            '19B101485N' => array('subsection' => '142', 'floor' => '70'),
            '18E101520N' => array('subsection' => '142', 'floor' => '70'),
            '18D101541N' => array('subsection' => '142', 'floor' => '70'),
            '21B109465N' => array('subsection' => '142', 'floor' => '70'),
            '20K700509P' => array('subsection' => '342', 'floor' => '71'),
            '21B700439P' => array('subsection' => '230', 'floor' => '68'),
            '18A700122P' => array('subsection' => '155', 'floor' => '68'),
            '20M700590P' => array('subsection' => '230', 'floor' => '68'),
            '21B109468N' => array('subsection' => '142', 'floor' => '70'),
            '20M700578P' => array('subsection' => '230', 'floor' => '68'),
            '20K109505N' => array('subsection' => '142', 'floor' => '70'),
            '21B700518P' => array('subsection' => '230', 'floor' => '68'),
            '19B700265P' => array('subsection' => '342', 'floor' => '71'),
            '21A109537N' => array('subsection' => '142', 'floor' => '70'),
            '21B700456P' => array('subsection' => '342', 'floor' => '71'),
            '19M700602P' => array('subsection' => '230', 'floor' => '68'),
            '21A700469P' => array('subsection' => '342', 'floor' => '71'),
            '19A700192P' => array('subsection' => '155', 'floor' => '68'),
            '19A101468N' => array('subsection' => '142', 'floor' => '70'),
            '21B700490P' => array('subsection' => '342', 'floor' => '71'),
            '20L109520N' => array('subsection' => '142', 'floor' => '70'),
            '21B700491P' => array('subsection' => '230', 'floor' => '68'),
            '18A101498N' => array('subsection' => '142', 'floor' => '70'),
            '21B700424P' => array('subsection' => '230', 'floor' => '68'),
            '21A109496N' => array('subsection' => '142', 'floor' => '70'),
            '16F101527N' => array('subsection' => '142', 'floor' => '70'),
            '21A109528N' => array('subsection' => '142', 'floor' => '70'),
            '18J101375N' => array('subsection' => '147', 'floor' => '69'),
            '20K700496P' => array('subsection' => '342', 'floor' => '71'),
            '18D101505N' => array('subsection' => '142', 'floor' => '70'),
            '20K700508P' => array('subsection' => '230', 'floor' => '71'),
            '18E101537N' => array('subsection' => '142', 'floor' => '70'),
            '18C700224P' => array('subsection' => '343', 'floor' => '71'),
            '20M700584P' => array('subsection' => '342', 'floor' => '71'),
            '17L700104P' => array('subsection' => '155', 'floor' => '68'),
            '21B109476N' => array('subsection' => '142', 'floor' => '70'),
            '20L700522P' => array('subsection' => '342', 'floor' => '71'),
            '21A700594P' => array('subsection' => '342', 'floor' => '71'),
            '18J700172P' => array('subsection' => '155', 'floor' => '68'),
            '18M700318P' => array('subsection' => '343', 'floor' => '71'),
            '18K000107A' => array('subsection' => '289', 'floor' => '104'),
            '20K700480P' => array('subsection' => '230', 'floor' => '68'),
            '18D101477N' => array('subsection' => '154', 'floor' => '70'),
            '19M109517N' => array('subsection' => '142', 'floor' => '70'),
            '20L700526P' => array('subsection' => '342', 'floor' => '71'),
            '19D700327P' => array('subsection' => '342', 'floor' => '71'),
            '21B700625P' => array('subsection' => '230', 'floor' => '68'),
            '15M101463N' => array('subsection' => '148', 'floor' => '68'),
            '21A700475P' => array('subsection' => '342', 'floor' => '71'),
            '20K700485P' => array('subsection' => '230', 'floor' => '71'),
            '17M101484N' => array('subsection' => '142', 'floor' => '70'),
            '20L700552P' => array('subsection' => '230', 'floor' => '68'),
            '21A700501P' => array('subsection' => '230', 'floor' => '68'),
            '16D101516N' => array('subsection' => '142', 'floor' => '70'),
            '19G700198P' => array('subsection' => '343', 'floor' => '71'),
            '21B700429P' => array('subsection' => '342', 'floor' => '71'),
            '21B109501N' => array('subsection' => '142', 'floor' => '70'),
            '18J700229P' => array('subsection' => '342', 'floor' => '71'),
            '21B109473N' => array('subsection' => '142', 'floor' => '70'),
            '19K700515P' => array('subsection' => '230', 'floor' => '68'),
            '20M700589P' => array('subsection' => '342', 'floor' => '71'),
            '21B700451P' => array('subsection' => '342', 'floor' => '71'),
            '19G000525A' => array('subsection' => '289', 'floor' => '104'),
            '21B700442P' => array('subsection' => '342', 'floor' => '71'),
            '20L109511N' => array('subsection' => '142', 'floor' => '70'),
            '18B700261P' => array('subsection' => '342', 'floor' => '71'),
            '20M000539A' => array('subsection' => '105', 'floor' => '104'),
            '21B700455P' => array('subsection' => '342', 'floor' => '71'),
            '19F700300P' => array('subsection' => '343', 'floor' => '71'),
            '18M700190P' => array('subsection' => '155', 'floor' => '68'),
            '18J700336P' => array('subsection' => '343', 'floor' => '71'),
            '21B110436N' => array('subsection' => '311', 'floor' => '69'),
            '20K700476P' => array('subsection' => '342', 'floor' => '71'),
            '19G101467N' => array('subsection' => '154', 'floor' => '70'),
            '17C000104A' => array('subsection' => '109', 'floor' => '104'),
            '20K700489P' => array('subsection' => '230', 'floor' => '68'),
            '17E101495N' => array('subsection' => '154', 'floor' => '70'),
            '21B700423P' => array('subsection' => '230', 'floor' => '68'),
            '19L109527N' => array('subsection' => '142', 'floor' => '70'),
            '21B700432P' => array('subsection' => '230', 'floor' => '68'),
            '20K700495P' => array('subsection' => '342', 'floor' => '71'),
            '19A101504N' => array('subsection' => '142', 'floor' => '70'),
            '20L109530N' => array('subsection' => '142', 'floor' => '70'),
            '20M700583P' => array('subsection' => '342', 'floor' => '71'),
            '17L700102P' => array('subsection' => '155', 'floor' => '68'),
            '20K109509N' => array('subsection' => '142', 'floor' => '70'),
            '21B109475N' => array('subsection' => '142', 'floor' => '70'),
            '20L700521P' => array('subsection' => '342', 'floor' => '71'),
            '21A700593P' => array('subsection' => '342', 'floor' => '71'),
            '21A700457P' => array('subsection' => '230', 'floor' => '68'),
            '20L700536P' => array('subsection' => '230', 'floor' => '68'),
            '18M700341P' => array('subsection' => '343', 'floor' => '71'),
            '17L101475N' => array('subsection' => '142', 'floor' => '70'),
            '19E700179P' => array('subsection' => '155', 'floor' => '68'),
            '20L700540P' => array('subsection' => '342', 'floor' => '71'),
            '17A000125A' => array('subsection' => '289', 'floor' => '104'),
            '21B700624P' => array('subsection' => '230', 'floor' => '68'),
            '21A700474P' => array('subsection' => '342', 'floor' => '71'),
            '19M700484P' => array('subsection' => '230', 'floor' => '68'),
            '19D101483N' => array('subsection' => '142', 'floor' => '70'),
            '18J101385N' => array('subsection' => '147', 'floor' => '69'),
            '21B700500P' => array('subsection' => '342', 'floor' => '71'),
            '21B700428P' => array('subsection' => '342', 'floor' => '71'),
            '21B700438P' => array('subsection' => '342', 'floor' => '71'),
            '17L700114P' => array('subsection' => '155', 'floor' => '68'),
            '20M700588P' => array('subsection' => '342', 'floor' => '71'),
            '21B109477N' => array('subsection' => '142', 'floor' => '70'),
            '21B700450P' => array('subsection' => '342', 'floor' => '71'),
            '18G700283P' => array('subsection' => '343', 'floor' => '71'),
            '21A700596P' => array('subsection' => '342', 'floor' => '71'),
            '21B109474N' => array('subsection' => '142', 'floor' => '70'),
            '21B109478N' => array('subsection' => '142', 'floor' => '70'),
            '18D700298P' => array('subsection' => '342', 'floor' => '71'),
            '19M700531P' => array('subsection' => '230', 'floor' => '68'),
            '21A700467P' => array('subsection' => '342', 'floor' => '71'),
            '18A700187P' => array('subsection' => '281', 'floor' => '104'),
            '20L700544P' => array('subsection' => '230', 'floor' => '68'),
            '19D700334P' => array('subsection' => '343', 'floor' => '71'),
            '20K109492N' => array('subsection' => '142', 'floor' => '70'),
            '17D101418N' => array('subsection' => '148', 'floor' => '71'),
            '20K700488P' => array('subsection' => '230', 'floor' => '68'),
            '19L700555P' => array('subsection' => '230', 'floor' => '68'),
            '17D101494N' => array('subsection' => '142', 'floor' => '70'),
            '18D101544N' => array('subsection' => '142', 'floor' => '70'),
            '20L700565P' => array('subsection' => '230', 'floor' => '68'),
            '21B700430P' => array('subsection' => '230', 'floor' => '68'),
            '21B700431P' => array('subsection' => '230', 'floor' => '68'),
            '20K700510P' => array('subsection' => '230', 'floor' => '68'),
            '18D101502N' => array('subsection' => '154', 'floor' => '70'),
            '20K700506P' => array('subsection' => '342', 'floor' => '71'),
            '18B101532N' => array('subsection' => '142', 'floor' => '70'),
            '19B700217P' => array('subsection' => '343', 'floor' => '71'),
            '21B109471N' => array('subsection' => '142', 'floor' => '70'),
            '20M700581P' => array('subsection' => '230', 'floor' => '68'),
            '20K109508N' => array('subsection' => '142', 'floor' => '70'),
            '21B700446P' => array('subsection' => '342', 'floor' => '71'),
            '20L700520P' => array('subsection' => '342', 'floor' => '71'),
            '21A700592P' => array('subsection' => '342', 'floor' => '71'),
            '21A109481N' => array('subsection' => '142', 'floor' => '70'),
            '21A700605P' => array('subsection' => '230', 'floor' => '68'),
            '19D700197P' => array('subsection' => '155', 'floor' => '68'),
            '20K700479P' => array('subsection' => '230', 'floor' => '68'),
            '18E101473N' => array('subsection' => '142', 'floor' => '70'),
            '20M700558P' => array('subsection' => '342', 'floor' => '71'),
            '21B700623P' => array('subsection' => '230', 'floor' => '68'),
            '21A700473P' => array('subsection' => '230', 'floor' => '68'),
            '16D101407N' => array('subsection' => '148', 'floor' => '69'),
            '19B700343P' => array('subsection' => '343', 'floor' => '71'),
            '20K700483P' => array('subsection' => '342', 'floor' => '71'),
            '17B101482N' => array('subsection' => '142', 'floor' => '70'),
            '18A101512N' => array('subsection' => '142', 'floor' => '70'),
            '21B109499N' => array('subsection' => '142', 'floor' => '70'),
            '19G700227P' => array('subsection' => '343', 'floor' => '71'),
            '21B700437P' => array('subsection' => '342', 'floor' => '71'),
            '18G700113P' => array('subsection' => '155', 'floor' => '68'),
            '20M700587P' => array('subsection' => '230', 'floor' => '68'),
            '21B700449P' => array('subsection' => '342', 'floor' => '71'),
            '21A109539N' => array('subsection' => '142', 'floor' => '70'),
            '20L700539P' => array('subsection' => '342', 'floor' => '71'),
            '17L000121A' => array('subsection' => '289', 'floor' => '104'),
            '21A700516P' => array('subsection' => '342', 'floor' => '71'),
            '21B700454P' => array('subsection' => '230', 'floor' => '68'),
            '18A700297P' => array('subsection' => '343', 'floor' => '71'),
            '19M700600P' => array('subsection' => '342', 'floor' => '71'),
            '21A700466P' => array('subsection' => '342', 'floor' => '71'),
            '17L700185P' => array('subsection' => '155', 'floor' => '68'),
            '20K109491N' => array('subsection' => '142', 'floor' => '70'),
            '19L700554P' => array('subsection' => '230', 'floor' => '68'),
            '17B101493N' => array('subsection' => '142', 'floor' => '70'),
            '19E700199P' => array('subsection' => '343', 'floor' => '71'),
            '21B109467N' => array('subsection' => '142', 'floor' => '70'),
            '20K109503N' => array('subsection' => '142', 'floor' => '70'),
            '19M700505P' => array('subsection' => '230', 'floor' => '68'),
            '19L109529N' => array('subsection' => '142', 'floor' => '70'),
            '21B109470N' => array('subsection' => '142', 'floor' => '70'),
            '20M700580P' => array('subsection' => '342', 'floor' => '71'),
            '18M700099P' => array('subsection' => '155', 'floor' => '68'),
            '19M700511P' => array('subsection' => '230', 'floor' => '68'),
            '21B700445P' => array('subsection' => '342', 'floor' => '71'),
            '19K700519P' => array('subsection' => '342', 'floor' => '71'),
            '19G700267P' => array('subsection' => '343', 'floor' => '71'),
            '21A109480N' => array('subsection' => '142', 'floor' => '70'),
            '19M700603P' => array('subsection' => '230', 'floor' => '68'),
            '19M700470P' => array('subsection' => '342', 'floor' => '71'),
            '17E700195P' => array('subsection' => '155', 'floor' => '68'),
            '18A700339P' => array('subsection' => '342', 'floor' => '71'),
            '20K700478P' => array('subsection' => '230', 'floor' => '68'),
            '18J101471N' => array('subsection' => '154', 'floor' => '70'),
            '21B109516N' => array('subsection' => '142', 'floor' => '70'),
            '21A700556P' => array('subsection' => '230', 'floor' => '68'),
            '19E101501N' => array('subsection' => '142', 'floor' => '70'),
            '19E101479N' => array('subsection' => '142', 'floor' => '70'),
            '21B109518N' => array('subsection' => '142', 'floor' => '70'),
            '21A700498P' => array('subsection' => '342', 'floor' => '71'),
            '17L101510N' => array('subsection' => '142', 'floor' => '70'),
            '21B109464N' => array('subsection' => '311', 'floor' => '69'),
            '21B109498N' => array('subsection' => '142', 'floor' => '70'),
            '19G101540N' => array('subsection' => '154', 'floor' => '70'),
            '21B700436P' => array('subsection' => '342', 'floor' => '71'),
            '20M700585P' => array('subsection' => '230', 'floor' => '68'),
            '21B700448P' => array('subsection' => '342', 'floor' => '71'),
            '19B700277P' => array('subsection' => '343', 'floor' => '71'),
            '21A700595P' => array('subsection' => '342', 'floor' => '71'),
            '19C700176P' => array('subsection' => '155', 'floor' => '68'),
            '18G700324P' => array('subsection' => '342', 'floor' => '71'),
            '19D000113A' => array('subsection' => '289', 'floor' => '104'),
            '21B700622P' => array('subsection' => '230', 'floor' => '68'),
            '21A109485N' => array('subsection' => '142', 'floor' => '70'),
            '21A700599P' => array('subsection' => '342', 'floor' => '71'),
            '20L700528P' => array('subsection' => '342', 'floor' => '71'),
            '21A109482N' => array('subsection' => '142', 'floor' => '70'),
            '20L700543P' => array('subsection' => '230', 'floor' => '68'),
            '19E700330P' => array('subsection' => '343', 'floor' => '71'),
            '21A000524A' => array('subsection' => '289', 'floor' => '104'),
            '21B700627P' => array('subsection' => '230', 'floor' => '68'),
            '17C000102A' => array('subsection' => '109', 'floor' => '104'),
            '17M101489N' => array('subsection' => '142', 'floor' => '70'),
            '20L109519N' => array('subsection' => '142', 'floor' => '70'),
            '20A109513N' => array('subsection' => '148', 'floor' => '69'),
            '19B101491N' => array('subsection' => '154', 'floor' => '70'),
            '20L109526N' => array('subsection' => '142', 'floor' => '70'),
            '20K700503P' => array('subsection' => '342', 'floor' => '71'),
            '16L101521N' => array('subsection' => '142', 'floor' => '70'),
            '17L101542N' => array('subsection' => '142', 'floor' => '70'),
            '21B109466N' => array('subsection' => '142', 'floor' => '70'),
            '20K109502N' => array('subsection' => '142', 'floor' => '70'),
            '21B700440P' => array('subsection' => '230', 'floor' => '68'),
            '18B700125P' => array('subsection' => '155', 'floor' => '68'),
            '21A109510N' => array('subsection' => '142', 'floor' => '70'),
            '19D700209P' => array('subsection' => '342', 'floor' => '71'),
            '21B700566P' => array('subsection' => '230', 'floor' => '68'),
            '21B109469N' => array('subsection' => '142', 'floor' => '70'),
            '20M700579P' => array('subsection' => '230', 'floor' => '68'),
            '18D700098P' => array('subsection' => '155', 'floor' => '68'),
            '19L109506N' => array('subsection' => '311', 'floor' => '69'),
            '21B700444P' => array('subsection' => '342', 'floor' => '71'),
            '19D700266P' => array('subsection' => '343', 'floor' => '71'),
            '21A700591P' => array('subsection' => '342', 'floor' => '71'),
            '20K700614P' => array('subsection' => '148', 'floor' => '68'),
            '21A109479N' => array('subsection' => '142', 'floor' => '70'),
            '21A109540N' => array('subsection' => '142', 'floor' => '70'),
            '20L109512N' => array('subsection' => '142', 'floor' => '70'),
            '19F700194P' => array('subsection' => '155', 'floor' => '68'),
            '20L700547P' => array('subsection' => '230', 'floor' => '68'),
            '19C700338P' => array('subsection' => '343', 'floor' => '71'),
            '19M109494N' => array('subsection' => '142', 'floor' => '70'),
            '17E101470N' => array('subsection' => '142', 'floor' => '70'),
            '20L109522N' => array('subsection' => '142', 'floor' => '70'),
            '21A700607P' => array('subsection' => '148', 'floor' => '68'),
            '20K700492P' => array('subsection' => '230', 'floor' => '68'),
            '19E101500N' => array('subsection' => '142', 'floor' => '70'),
            '21B700425P' => array('subsection' => '230', 'floor' => '68'),
            '21B109497N' => array('subsection' => '142', 'floor' => '70'),
            '18E101530N' => array('subsection' => '142', 'floor' => '70'),
            '20K075137L' => array('subsection' => '281', 'floor' => '104'),
            '18B101412N' => array('subsection' => '148', 'floor' => '68'),
            '16H075020L' => array('subsection' => '281', 'floor' => '104'),
            '21B700606P' => array('subsection' => '148', 'floor' => '68'),
            '21A700619P' => array('subsection' => '148', 'floor' => '68'),
            '20M065186K' => array('subsection' => '177', 'floor' => '104'),
            '09G000097A' => array('subsection' => '108', 'floor' => '104'),
            '17H075018L' => array('subsection' => '281', 'floor' => '104'),
            '20M700618P' => array('subsection' => '148', 'floor' => '68'),
            '20K075135L' => array('subsection' => '281', 'floor' => '104'),
            '16K075012L' => array('subsection' => '281', 'floor' => '104'),
            '20M109493N' => array('subsection' => '148', 'floor' => '69'),
            '20M075138L' => array('subsection' => '281', 'floor' => '104'),
            '16C065041K' => array('subsection' => '177', 'floor' => '104'),
            '18D101390N' => array('subsection' => '148', 'floor' => '69'),
            '16E065035K' => array('subsection' => '177', 'floor' => '104'),
            '17M065040K' => array('subsection' => '177', 'floor' => '104'),
            '18G101413N' => array('subsection' => '148', 'floor' => '69'),
            '16D101397N' => array('subsection' => '148', 'floor' => '68'),
            '16D075015L' => array('subsection' => '287', 'floor' => '104'),
            '17D065043K' => array('subsection' => '177', 'floor' => '69'),
            '21B075134L' => array('subsection' => '281', 'floor' => '104'),
            '18J075013L' => array('subsection' => '287', 'floor' => '104'),
            '15J075024L' => array('subsection' => '287', 'floor' => '104'),
            '16E065039K' => array('subsection' => '177', 'floor' => '104'),
            '16C101445N' => array('subsection' => '148', 'floor' => '104'),
            '15H075023L' => array('subsection' => '287', 'floor' => '104'),
            '16K065037K' => array('subsection' => '177', 'floor' => '104'),
            '17D101427N' => array('subsection' => '148', 'floor' => '69'),
            '15J075019L' => array('subsection' => '287', 'floor' => '104'),
            '17G101425N' => array('subsection' => '148', 'floor' => '69'),
            '16F075016L' => array('subsection' => '287', 'floor' => '104'),
            '21A700561P' => array('subsection' => '155', 'floor' => '68'),
            '18D101376N' => array('subsection' => '147', 'floor' => '69'),
            '16F700225P' => array('subsection' => '343', 'floor' => '71'),
            '18A700107P' => array('subsection' => '155', 'floor' => '68'),
            '16C700247P' => array('subsection' => '343', 'floor' => '71'),
            '21B700447P' => array('subsection' => '155', 'floor' => '68'),
            '16D700144P' => array('subsection' => '155', 'floor' => '68'),
            '17D700273P' => array('subsection' => '343', 'floor' => '71'),
            '17B700174P' => array('subsection' => '155', 'floor' => '68'),
            '21B700621P' => array('subsection' => '155', 'floor' => '68'),
            '19M700598P' => array('subsection' => '155', 'floor' => '68'),
            '16K700151P' => array('subsection' => '155', 'floor' => '68'),
            '17C700181P' => array('subsection' => '155', 'floor' => '68'),
            '21A109489N' => array('subsection' => '147', 'floor' => '69'),
            '17D101387N' => array('subsection' => '147', 'floor' => '69'),
            '21B700502P' => array('subsection' => '343', 'floor' => '71'),
            '17J700230P' => array('subsection' => '343', 'floor' => '71'),
            '16G700255P' => array('subsection' => '343', 'floor' => '71'),
            '16L700235P' => array('subsection' => '343', 'floor' => '71'),
            '17D700097P' => array('subsection' => '155', 'floor' => '68'),
            '21B700443P' => array('subsection' => '343', 'floor' => '71'),
            '16D700137P' => array('subsection' => '155', 'floor' => '68'),
            '21B700533P' => array('subsection' => '155', 'floor' => '68'),
            '17J700337P' => array('subsection' => '343', 'floor' => '71'),
            '16D101398N' => array('subsection' => '147', 'floor' => '69'),
            '17L700207P' => array('subsection' => '343', 'floor' => '71'),
            '21B700426P' => array('subsection' => '155', 'floor' => '68'),
            '21B700435P' => array('subsection' => '343', 'floor' => '71'),
            '17D700242P' => array('subsection' => '343', 'floor' => '71'),
            '15M700245P' => array('subsection' => '343', 'floor' => '71'),
            '16L700143P' => array('subsection' => '155', 'floor' => '68'),
            '18B700272P' => array('subsection' => '343', 'floor' => '71'),
            '21A700458P' => array('subsection' => '155', 'floor' => '68'),
            '21B700620P' => array('subsection' => '155', 'floor' => '68'),
            '19M700471P' => array('subsection' => '155', 'floor' => '68'),
            '16D700150P' => array('subsection' => '155', 'floor' => '68'),
            '21A700465P' => array('subsection' => '155', 'floor' => '68'),
            '17G700180P' => array('subsection' => '155', 'floor' => '68'),
            '18L101386N' => array('subsection' => '147', 'floor' => '69'),
            '19L700573P' => array('subsection' => '343', 'floor' => '71'),
            '18A700117P' => array('subsection' => '155', 'floor' => '68'),
            '15M700149P' => array('subsection' => '148', 'floor' => '68'),
            '19M700597P' => array('subsection' => '343', 'floor' => '71'),
            '16E700133P' => array('subsection' => '155', 'floor' => '68'),
            '16C700156P' => array('subsection' => '155', 'floor' => '68'),
            '21B700468P' => array('subsection' => '155', 'floor' => '68'),
            '16K101396N' => array('subsection' => '148', 'floor' => '69'),
            '21A700504P' => array('subsection' => '343', 'floor' => '71'),
            '16L101526N' => array('subsection' => '219', 'floor' => '70'),
            '17J700200P' => array('subsection' => '343', 'floor' => '71'),
            '16L700234P' => array('subsection' => '343', 'floor' => '71'),
            '19M700559P' => array('subsection' => '343', 'floor' => '71'),
            '21A700507P' => array('subsection' => '343', 'floor' => '71'),
            '16E101534N' => array('subsection' => '219', 'floor' => '70'),
            '17L700221P' => array('subsection' => '343', 'floor' => '71'),
            '21B700434P' => array('subsection' => '343', 'floor' => '71'),
            '16E700240P' => array('subsection' => '343', 'floor' => '71'),
            '16D700142P' => array('subsection' => '155', 'floor' => '68'),
            '17L700271P' => array('subsection' => '343', 'floor' => '71'),
            '17M700163P' => array('subsection' => '155', 'floor' => '68'),
            '17L700315P' => array('subsection' => '343', 'floor' => '71'),
            '19M109483N' => array('subsection' => '219', 'floor' => '70'),
            '19K109495N' => array('subsection' => '147', 'floor' => '69'),
            '21A109523N' => array('subsection' => '147', 'floor' => '69'),
            '16E101513N' => array('subsection' => '219', 'floor' => '70'),
            '16G700228P' => array('subsection' => '343', 'floor' => '71'),
            '19M700572P' => array('subsection' => '343', 'floor' => '71'),
            '16A101409N' => array('subsection' => '147', 'floor' => '69'),
            '16G700253P' => array('subsection' => '343', 'floor' => '71'),
            '16D700148P' => array('subsection' => '148', 'floor' => '68'),
            '21A700463P' => array('subsection' => '155', 'floor' => '68'),
            '16E700131P' => array('subsection' => '155', 'floor' => '68'),
            '19K700517P' => array('subsection' => '343', 'floor' => '71'),
            '18B700260P' => array('subsection' => '343', 'floor' => '71'),
            '16C700155P' => array('subsection' => '155', 'floor' => '68'),
            '21B110435N' => array('subsection' => '147', 'floor' => '69'),
            '21B109462N' => array('subsection' => '147', 'floor' => '69'),
            '16D101395N' => array('subsection' => '147', 'floor' => '69'),
            '16K101524N' => array('subsection' => '219', 'floor' => '70'),
            '16G700233P' => array('subsection' => '148', 'floor' => '71'),
            '21A700494P' => array('subsection' => '155', 'floor' => '68'),
            '16D700238P' => array('subsection' => '343', 'floor' => '71'),
            '17G700100P' => array('subsection' => '155', 'floor' => '68'),
            '16D700141P' => array('subsection' => '155', 'floor' => '68'),
            '18B700269P' => array('subsection' => '343', 'floor' => '71'),
            '18G700162P' => array('subsection' => '155', 'floor' => '68'),
            '17G700312P' => array('subsection' => '343', 'floor' => '71'),
            '17L700340P' => array('subsection' => '343', 'floor' => '71'),
            '19L700549P' => array('subsection' => '343', 'floor' => '71'),
            '21A700550P' => array('subsection' => '343', 'floor' => '71'),
            '17D101381N' => array('subsection' => '147', 'floor' => '69'),
            '21A700499P' => array('subsection' => '343', 'floor' => '71'),
            '21B700427P' => array('subsection' => '155', 'floor' => '68'),
            '21A700571P' => array('subsection' => '155', 'floor' => '68'),
            '21B700514P' => array('subsection' => '155', 'floor' => '68'),
            '15L700147P' => array('subsection' => '155', 'floor' => '68'),
            '18B700280P' => array('subsection' => '343', 'floor' => '71'),
            '21A700461P' => array('subsection' => '343', 'floor' => '71'),
            '17B700178P' => array('subsection' => '155', 'floor' => '68'),
            '18B700325P' => array('subsection' => '343', 'floor' => '71'),
            '16L700129P' => array('subsection' => '155', 'floor' => '68'),
            '16G700258P' => array('subsection' => '343', 'floor' => '71'),
            '16F700154P' => array('subsection' => '155', 'floor' => '68'),
            '17G700333P' => array('subsection' => '343', 'floor' => '71'),
            '21B110434N' => array('subsection' => '147', 'floor' => '69'),
            '16M101392N' => array('subsection' => '147', 'floor' => '69'),
            '16K101522N' => array('subsection' => '219', 'floor' => '70'),
            '16L700232P' => array('subsection' => '343', 'floor' => '71'),
            '19M700576P' => array('subsection' => '155', 'floor' => '68'),
            '21B700441P' => array('subsection' => '343', 'floor' => '71'),
            '15M101403N' => array('subsection' => '147', 'floor' => '69'),
            '16E101531N' => array('subsection' => '148', 'floor' => '70'),
            '15M101404N' => array('subsection' => '147', 'floor' => '69'),
            '16D700237P' => array('subsection' => '343', 'floor' => '71'),
            '16D700139P' => array('subsection' => '155', 'floor' => '68'),
            '18A700309P' => array('subsection' => '343', 'floor' => '71'),
            '19M700604P' => array('subsection' => '155', 'floor' => '68'),
            '21B700630P' => array('subsection' => '343', 'floor' => '71'),
            '18G101377N' => array('subsection' => '147', 'floor' => '69'),
            '18A700226P' => array('subsection' => '343', 'floor' => '71'),
            '19L700570P' => array('subsection' => '343', 'floor' => '71'),
            '16D101408N' => array('subsection' => '147', 'floor' => '69'),
            '18E700111P' => array('subsection' => '155', 'floor' => '68'),
            '16F700248P' => array('subsection' => '148', 'floor' => '71'),
            '16C700145P' => array('subsection' => '155', 'floor' => '68'),
            '21B109515N' => array('subsection' => '147', 'floor' => '69'),
            '21B700453P' => array('subsection' => '343', 'floor' => '71'),
            '16G700293P' => array('subsection' => '343', 'floor' => '71'),
            '16L700152P' => array('subsection' => '155', 'floor' => '68'),
            '17L700182P' => array('subsection' => '155', 'floor' => '68'),
            '21B700422P' => array('subsection' => '155', 'floor' => '68'),
            '18J101391N' => array('subsection' => '147', 'floor' => '69'),
            '21A700563P' => array('subsection' => '155', 'floor' => '68'),
            '16L700231P' => array('subsection' => '343', 'floor' => '71'),
            '16L700257P' => array('subsection' => '343', 'floor' => '71'),
            '16L700236P' => array('subsection' => '343', 'floor' => '71'),
            '16L700138P' => array('subsection' => '155', 'floor' => '68'),
            '16F700159P' => array('subsection' => '155', 'floor' => '68'),
            '19K700548P' => array('subsection' => '343', 'floor' => '71'),
            '16L101401N' => array('subsection' => '147', 'floor' => '69'),
            '17B065065K' => array('subsection' => '344', 'floor' => '104'),
            '18A065049K' => array('subsection' => '344', 'floor' => '104'),
            '21B000515A' => array('subsection' => '344', 'floor' => '104'),
            '17D065071K' => array('subsection' => '344', 'floor' => '104'),
            '18E065058K' => array('subsection' => '344', 'floor' => '104'),
            '16K065073K' => array('subsection' => '344', 'floor' => '104'),
            '15M065080K' => array('subsection' => '344', 'floor' => '104'),
            '18L065064K' => array('subsection' => '344', 'floor' => '104'),
            '18E065045K' => array('subsection' => '344', 'floor' => '104'),
            '18A065069K' => array('subsection' => '344', 'floor' => '104'),
            '18A065057K' => array('subsection' => '344', 'floor' => '104'),
            '18G065063K' => array('subsection' => '344', 'floor' => '104'),
            '21B000519A' => array('subsection' => '344', 'floor' => '104'),
            '18G065076K' => array('subsection' => '344', 'floor' => '68'),
            '20L000534A' => array('subsection' => '344', 'floor' => '104'),
            '17E065067K' => array('subsection' => '344', 'floor' => '104'),
            '20K000530A' => array('subsection' => '344', 'floor' => '104'),
            '19M000541A' => array('subsection' => '344', 'floor' => '104'),
            '18D065055K' => array('subsection' => '344', 'floor' => '104'),
            '19M000527A' => array('subsection' => '344', 'floor' => '104'),
            '21A000522A' => array('subsection' => '344', 'floor' => '104'),
            '18M065079K' => array('subsection' => '344', 'floor' => '104'),
            '17H065062K' => array('subsection' => '344', 'floor' => '104'),
            '20L000538A' => array('subsection' => '344', 'floor' => '104'),
            '20L000533A' => array('subsection' => '344', 'floor' => '104'),
            '18E065051K' => array('subsection' => '344', 'floor' => '104'),
            '16L065075K' => array('subsection' => '344', 'floor' => '104'),
            '17M065061K' => array('subsection' => '344', 'floor' => '104'),
            '21B000517A' => array('subsection' => '344', 'floor' => '104'),
            '16C065046K' => array('subsection' => '344', 'floor' => '104'),
            '16C065081K' => array('subsection' => '344', 'floor' => '104'),
            '20L000537A' => array('subsection' => '344', 'floor' => '104'),
            '15L065078K' => array('subsection' => '344', 'floor' => '104'),
            '20L000532A' => array('subsection' => '344', 'floor' => '104'),
            '17M065050K' => array('subsection' => '344', 'floor' => '104'),
            '18E065072K' => array('subsection' => '344', 'floor' => '104'),
            '21A000521A' => array('subsection' => '344', 'floor' => '104'),
            '18E065060K' => array('subsection' => '344', 'floor' => '104'),
            '16C101455N' => array('subsection' => '202', 'floor' => '104'),
            '15K101454N' => array('subsection' => '148', 'floor' => '70'),
            '20M501185O' => array('subsection' => '154', 'floor' => '70'),
            '19A500201O' => array('subsection' => '154', 'floor' => '70'),
            '18J500217O' => array('subsection' => '154', 'floor' => '70'),
            '16A500232O' => array('subsection' => '154', 'floor' => '70'),
            '19M501182O' => array('subsection' => '154', 'floor' => '70'),
            '16G500237O' => array('subsection' => '154', 'floor' => '70'),
            '20L501191O' => array('subsection' => '154', 'floor' => '70'),
            '17L500205O' => array('subsection' => '154', 'floor' => '70'),
            '18J500223O' => array('subsection' => '154', 'floor' => '70'),
            '16B500211O' => array('subsection' => '154', 'floor' => '70'),
            '21B700628P' => array('subsection' => '154', 'floor' => '70'),
            '18A500200O' => array('subsection' => '154', 'floor' => '70'),
            '17B500216O' => array('subsection' => '154', 'floor' => '70'),
            '16D500231O' => array('subsection' => '154', 'floor' => '70'),
            '16F500236O' => array('subsection' => '154', 'floor' => '70'),
            '17L500204O' => array('subsection' => '154', 'floor' => '70'),
            '16K500222O' => array('subsection' => '154', 'floor' => '70'),
            '19F500210O' => array('subsection' => '154', 'floor' => '70'),
            '21A501193O' => array('subsection' => '154', 'floor' => '70'),
            '17G500199O' => array('subsection' => '154', 'floor' => '70'),
            '18A500215O' => array('subsection' => '154', 'floor' => '70'),
            '16C500230O' => array('subsection' => '154', 'floor' => '70'),
            '16C500235O' => array('subsection' => '154', 'floor' => '70'),
            '20L501180O' => array('subsection' => '154', 'floor' => '70'),
            '18J500221O' => array('subsection' => '154', 'floor' => '70'),
            '20L501188O' => array('subsection' => '154', 'floor' => '70'),
            '17K500241O' => array('subsection' => '154', 'floor' => '70'),
            '18A500209O' => array('subsection' => '154', 'floor' => '70'),
            '16G500196O' => array('subsection' => '154', 'floor' => '70'),
            '18J500214O' => array('subsection' => '154', 'floor' => '70'),
            '16B500229O' => array('subsection' => '154', 'floor' => '70'),
            '15M500234O' => array('subsection' => '154', 'floor' => '70'),
            '18A500202O' => array('subsection' => '154', 'floor' => '70'),
            '16K500220O' => array('subsection' => '154', 'floor' => '70'),
            '20M501190O' => array('subsection' => '154', 'floor' => '70'),
            '16E500240O' => array('subsection' => '154', 'floor' => '70'),
            '18D500207O' => array('subsection' => '154', 'floor' => '70'),
            '16D500226O' => array('subsection' => '154', 'floor' => '70'),
            '18G500213O' => array('subsection' => '154', 'floor' => '70'),
            '16L500228O' => array('subsection' => '154', 'floor' => '70'),
            '18J500219O' => array('subsection' => '154', 'floor' => '70'),
            '20K501186O' => array('subsection' => '154', 'floor' => '70'),
            '20M501192O' => array('subsection' => '154', 'floor' => '70'),
            '16D500233O' => array('subsection' => '154', 'floor' => '70'),
            '21A501183O' => array('subsection' => '154', 'floor' => '70'),
            '16A500239O' => array('subsection' => '154', 'floor' => '70'),
            '21A501184O' => array('subsection' => '154', 'floor' => '70'),
            '18B500206O' => array('subsection' => '154', 'floor' => '70'),
            '16D500224O' => array('subsection' => '154', 'floor' => '70'),
            '18B500212O' => array('subsection' => '154', 'floor' => '70'),
            '20K501187O' => array('subsection' => '154', 'floor' => '70'),
            '16D500227O' => array('subsection' => '154', 'floor' => '70'),
            '21B700629P' => array('subsection' => '154', 'floor' => '70'),
            '16A500238O' => array('subsection' => '148', 'floor' => '70'),
            '16K101440N' => array('subsection' => '148', 'floor' => '71'),
            '16D000103A' => array('subsection' => '109', 'floor' => '104'),
            '16A101464N' => array('subsection' => '148', 'floor' => '71'),
            '21A000535A' => array('subsection' => '108', 'floor' => '104'),
            '11F000091A' => array('subsection' => '108', 'floor' => '104'),
            '19K000531A' => array('subsection' => '108', 'floor' => '104'),
            '11L000096A' => array('subsection' => '108', 'floor' => '104'),
            '17A000090A' => array('subsection' => '108', 'floor' => '104'),
            '12D000095A' => array('subsection' => '108', 'floor' => '104'),
            '15F000101A' => array('subsection' => '108', 'floor' => '104'),
            '14B000093A' => array('subsection' => '108', 'floor' => '104'),
            '15H000100A' => array('subsection' => '108', 'floor' => '104'),
            '10M000089A' => array('subsection' => '108', 'floor' => '104'),
            '14B000092A' => array('subsection' => '108', 'floor' => '104'),
            '17G000099A' => array('subsection' => '108', 'floor' => '104'),
            '11H000088A' => array('subsection' => '108', 'floor' => '104'),
            '17A000098A' => array('subsection' => '108', 'floor' => '104'),
            '20K700612P' => array('subsection' => '148', 'floor' => '68'),
            '16C101459N' => array('subsection' => '148', 'floor' => '71'),
            '16A101452N' => array('subsection' => '148', 'floor' => '71'),
            '15K101436N' => array('subsection' => '148', 'floor' => '68'),
            '16B101458N' => array('subsection' => '148', 'floor' => '70'),
            '16A101451N' => array('subsection' => '148', 'floor' => '70'),
            '21A109532N' => array('subsection' => '148', 'floor' => '69'),
            '16D101394N' => array('subsection' => '148', 'floor' => '69'),
            '16G101450N' => array('subsection' => '148', 'floor' => '68'),
            '20L700616P' => array('subsection' => '148', 'floor' => '68'),
            '16G101393N' => array('subsection' => '148', 'floor' => '69'),
            '15L101462N' => array('subsection' => '148', 'floor' => '70'),
            '20M109488N' => array('subsection' => '148', 'floor' => '69'),
            '16E101449N' => array('subsection' => '148', 'floor' => '71'),
            '15K101465N' => array('subsection' => '148', 'floor' => '68'),
            '21B109514N' => array('subsection' => '148', 'floor' => '68'),
            '20L700615P' => array('subsection' => '148', 'floor' => '69'),
            '16D101406N' => array('subsection' => '148', 'floor' => '69'),
            '20K700613P' => array('subsection' => '148', 'floor' => '68'),
            '15L101438N' => array('subsection' => '148', 'floor' => '68'),
            '16A101460N' => array('subsection' => '148', 'floor' => '71'),
            '15K101453N' => array('subsection' => '148', 'floor' => '70'),
            '16C700290P' => array('subsection' => '343', 'floor' => '71'),
            '15L700157P' => array('subsection' => '155', 'floor' => '68'),
            '16A700254P' => array('subsection' => '148', 'floor' => '71'),
            '16C700250P' => array('subsection' => '148', 'floor' => '71'),
            '16E700160P' => array('subsection' => '148', 'floor' => '68'),
            '17A065038K' => array('subsection' => '177', 'floor' => '104'),
            '20L700617P' => array('subsection' => '148', 'floor' => '68'),
            '17A065036K' => array('subsection' => '177', 'floor' => '104'),
            '17B065042K' => array('subsection' => '177', 'floor' => '104'),
            '17D065044K' => array('subsection' => '177', 'floor' => '104'),
            '20L700569P' => array('subsection' => '155', 'floor' => '68'),
            '20K700513P' => array('subsection' => '155', 'floor' => '68'),
            '19L109538N' => array('subsection' => '147', 'floor' => '69'),
            '20L700538P' => array('subsection' => '343', 'floor' => '71'),
            '19G700321P' => array('subsection' => '343', 'floor' => '71'),
            '19F700328P' => array('subsection' => '343', 'floor' => '71'),
            '20L700553P' => array('subsection' => '343', 'floor' => '71'),
            '20M109524N' => array('subsection' => '147', 'floor' => '69'),
            '19E700304P' => array('subsection' => '343', 'floor' => '71'),
            '20L700546P' => array('subsection' => '155', 'floor' => '68'),
            '20K700477P' => array('subsection' => '155', 'floor' => '68'),
            '20L700560P' => array('subsection' => '343', 'floor' => '71'),
            '20L700568P' => array('subsection' => '155', 'floor' => '68'),
            '20K700512P' => array('subsection' => '155', 'floor' => '68'),
            '20L700537P' => array('subsection' => '155', 'floor' => '68'),
            '20L700541P' => array('subsection' => '343', 'floor' => '71'),
            '20L700420P' => array('subsection' => '155', 'floor' => '68'),
            '20L700562P' => array('subsection' => '343', 'floor' => '71'),
            '17M700284P' => array('subsection' => '343', 'floor' => '71'),
            '20K109504N' => array('subsection' => '147', 'floor' => '69'),
            '20L700532P' => array('subsection' => '155', 'floor' => '68'),
            '20L700545P' => array('subsection' => '155', 'floor' => '68'),
            '20M109531N' => array('subsection' => '147', 'floor' => '69'),
            '17M700326P' => array('subsection' => '343', 'floor' => '71'),
            '20L700551P' => array('subsection' => '343', 'floor' => '71'),
            '19L109500N' => array('subsection' => '147', 'floor' => '69'),
            '20M109536N' => array('subsection' => '147', 'floor' => '69'),
            '20L700577P' => array('subsection' => '155', 'floor' => '68'),
            '20L700567P' => array('subsection' => '343', 'floor' => '71'),
            '20L700535P' => array('subsection' => '155', 'floor' => '68'),
            '20M109535N' => array('subsection' => '147', 'floor' => '69'),
            '20L700530P' => array('subsection' => '155', 'floor' => '68'),
            '20M700487P' => array('subsection' => '343', 'floor' => '71'),
            '20L700564P' => array('subsection' => '155', 'floor' => '68'),
            '19D700214P' => array('subsection' => '343', 'floor' => '71'),
            '20L700534P' => array('subsection' => '155', 'floor' => '68'),
            '20K700493P' => array('subsection' => '343', 'floor' => '71'),
            '19K700482P' => array('subsection' => '155', 'floor' => '68'),
            '19L700575P' => array('subsection' => '155', 'floor' => '68'),
            '20M109534N' => array('subsection' => '147', 'floor' => '69')
        );

        $sub = subSection_by_id();

        foreach ($data as $key => $v) {
            if(isset($sub[$v['subsection']])){
            $ss = $sub[$v['subsection']];
            DB::table('hr_as_basic_info')
                ->where('as_unit_id', 8)
                ->where('as_location', 11)
                ->where('associate_id', $key)
                ->update([
                    'as_subsection_id' => $v['subsection'],
                    'as_section_id' => $ss['hr_subsec_section_id'],
                    'as_department_id' => $ss['hr_subsec_department_id'],
                    'as_area_id' => $ss['hr_subsec_area_id'],
                    'as_floor_id' => $v['floor']
                ]);
            }
        }

        return 'done';
    }


}
