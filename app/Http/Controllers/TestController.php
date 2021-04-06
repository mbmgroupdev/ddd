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

        
        return $this->findFriday();
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
        $section = subSection_by_id();
        $designation = designation_by_id();

        $emps =  [];

        $insert = [];
        foreach ($emps as $key => $v) {
            $insert[$key]['as_oracle_code'] = $key;
            $insert[$key]['worker_name'] = $v['NAME'];
            $insert[$key]['worker_doj'] = date('Y-m-d', strtotime($v['doj']));
            $insert[$key]['worker_dob'] = date('Y-m-d', strtotime($v['dob']));
            $insert[$key]['worker_ot'] = $v['OT'] == 'Y'?1:0;
            $insert[$key]['worker_gender'] = $v['sex'] == 'M'?'Male':'Female';
            $insert[$key]['worker_unit_id'] = 3;
            $insert[$key]['location_id'] = 9;
            $insert[$key]['worker_area_id'] = null;
            $insert[$key]['worker_department_id'] = null;
            $insert[$key]['worker_section_id'] = null;
            $insert[$key]['worker_subsection_id'] = null;
            $insert[$key]['worker_emp_type_id'] = null;
            $insert[$key]['worker_designation_id'] = null;
            $c = 0;
            if($v['SECTION'] != null){
                $k = $v['SECTION'];
                if(isset($section[$k])){

                    $insert[$key]['worker_area_id'] = $section[$k]['hr_subsec_area_id'];
                    $insert[$key]['worker_department_id'] = $section[$k]['hr_subsec_department_id'];
                    $insert[$key]['worker_section_id'] = $section[$k]['hr_subsec_section_id'];
                    $insert[$key]['worker_subsection_id'] = $k;
                }

            }
            if($v['DESIGNATION'] != null){

                $kd = $v['DESIGNATION'];
                if(isset($designation[$kd])){
                    $insert[$key]['worker_emp_type_id'] = $designation[$kd]['hr_designation_emp_type'];
                    $insert[$key]['worker_designation_id'] = $kd;
                }
            }

            $insert[$key]['worker_color_band_join'] = 1;
            $insert[$key]['worker_doctor_acceptance'] = 1;

        }

        return DB::table('hr_worker_recruitment')->insert($insert);

        return (count($insert));
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
                ->where('as_unit_id', 3)
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
                $up['ben_joining_salary'] = $val['J_SAL']??0;
                $up['ben_current_salary'] = $val['salary'];
                $up['ben_basic'] = ceil(($val['salary']-1850)/1.5);
                $up['ben_house_rent'] = $val['salary'] -1850 - $up['ben_basic'];

                // bank 
              /*  if($val['dbbl'] != '#N/A'){
                    $up['ben_bank_amount'] = $val['bank'];
                    $up['ben_cash_amount'] = $val['salary'] - $val['bank'] ;
                    $up['ben_tds_amount'] = $val['tds'];
                    $up['bank_name'] = 'dbbl';
                    $up['bank_no'] = $val['dbbl'];

                }else if($val['rocket'] != '#N/A'){
                // rocket
                    $up['ben_bank_amount'] = $val['salary'];
                    $up['ben_cash_amount'] = 0 ;
                    $up['bank_name'] = 'rocket';
                    $up['bank_no'] = $val['rocket'];
                }else{*/
                // cash
                    /*$up['ben_bank_amount'] = 0;
                    $up['ben_cash_amount'] = $val['salary'] ;*/

               /* }*/


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
        $data = [];

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

    public function removefriday()
    {
        $dt = '2021-03-05';
        $data = DB::table('hr_shift_roaster')
            ->where('shift_roaster_month','03')
            ->where('day_5','Friday Day')
            ->pluck('shift_roaster_user_id')->toArray();

        $att = DB::table('hr_att_special')
                ->where('in_date', $dt)
                ->whereIn('as_id',$data)
                ->delete();
    }

    public function findFriday()
    {
        $dt = '2021-03-05';
        $data = DB::table('hr_shift_roaster as h')
            ->select('b.*')
            ->leftJoin('hr_as_basic_info as b','b.as_id','h.shift_roaster_user_id')
            ->where('h.shift_roaster_month','03')
            ->where('h.day_5','Friday Night')
            ->get()
            ->keyBy('as_id');

        $as = collect($data)->pluck('as_id')->toArray();


        $att = DB::table('hr_attendance_history')
                ->where('att_date', $dt)
                ->whereIn('as_id',$as)
                ->where('unit',8)
                ->get();

        $shift = DB::table('hr_shift')
                        ->where('hr_shift_name', 'Friday OT')
                        ->where('hr_shift_unit_id', 8)
                        ->where('ot_status', 1)
                        ->orderBy('hr_shift_id','DESC')
                        ->first();

        foreach ($att as $key => $value) {
            $this->extractSpecialOT($dt, $value->raw_data, $data[$value->as_id], $shift);
        }

    }



    public function extractSpecialOT($date, $time, $emp, $shift)
    {
        $start = $date." ".$shift->hr_shift_start_time;

        $in_time = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $in_time_begin = $in_time->copy()->subHours(2);
        $in_time_end   = $in_time->copy()->addHours(1);



        $end   = $date." ".$shift->hr_shift_end_time;
        $out_time = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        $out_time_begin = $in_time_end->copy()->addSecond();
        $out_time_end   = $out_time->copy()->addHours(4);

       

        if($time > $in_time_begin && $time < $out_time_end ){

            $last_punch = DB::table('hr_att_special')
                            ->where([
                                'in_date' => $date,
                                'as_id' => $emp->as_id
                            ])
                            ->first();
            $dt = [];

            if($last_punch){
                // check in
                if(($in_time_begin <= $time &&  $in_time_end >= $time) && ($time <= $last_punch->in_time  || $last_punch->in_time == null)){
                    $dt['in_time'] = $time;
                    $last_punch->in_time = $time;
                }else if(($out_time_begin <= $time &&  $out_time_end >= $time) && ($time >= $last_punch->out_time || $last_punch->out_time == null )){
                    $dt['out_time'] = $time;
                    $last_punch->out_time = $time;
                }

                // update ot
                if($last_punch->in_time != null && $last_punch->out_time != null){
                    $dt['ot_hour'] = $this->fullot($last_punch->in_time, $start, $last_punch->out_time,  $shift->hr_shift_break_time);

                }
                if($dt){

                    DB::table('hr_att_special')
                        ->where('id',$last_punch->id)
                        ->where('as_id',$emp->as_id)
                        ->update($dt);
                }

            }else{
                $dt = array(
                    'in_date' => $date,
                    'as_id' => $emp->as_id,
                    'hr_shift_code' => $shift->hr_shift_code
                );
                if($in_time_begin <= $time &&  $in_time_end >= $time){
                    $dt['in_time'] = $time;
                }else if($out_time_begin <= $time &&  $out_time_end >= $time){
                    $dt['out_time'] = $time;
                }else{
                    return 0;
                }
                DB::table('hr_att_special')
                    ->insert($dt);
            }
            return 1;
        }

    }

    public function fullot($start, $shift_start, $end, $break)
    {
        $start = $start < $shift_start? $shift_start:$start;
        $diff = (strtotime($end) - (strtotime($start) + ($break*60)))/3600;
        $diff = $diff < 0 ? 0:$diff;

        $part    = explode('.', $diff);
        $minutes = (isset($part[1]) ? $part[1] : 0);
        $minutes = floatval('0.'.$minutes);
        // return $minutes;
        if($minutes > 0.16667 && $minutes <= 0.75) $minutes = $minutes;
        else if($minutes >= 0.75) $minutes = 1;
        else $minutes = 0;
        
        if($minutes > 0 && $minutes != 1){
            $min = (int)round($minutes*60);
            $minOT = min_to_ot();
            $minutes = $minOT[$min]??0;
        }

        $overtimes = $part[0] + $minutes;
        $overtimes = number_format((float)$overtimes, 3, '.', '');

        return  $overtimes;
    }

    public function floorUpdate()
    {
        $data = array(
            '19E010020B' => array('fl' => '82'),
            '19G102836N' => array('fl' => '82'),
            '15H010038B' => array('fl' => '82'),
            '19E010041B' => array('fl' => '82'),
            '17B010042B' => array('fl' => '82'),
            '19F010048B' => array('fl' => '82'),
            '15H500339O' => array('fl' => '82'),
            '17J500444O' => array('fl' => '82'),
            '13L103161N' => array('fl' => '92'),
            '19B103720N' => array('fl' => '90'),
            '18G104205N' => array('fl' => '82'),
            '17J104503N' => array('fl' => '85'),
            '08A105480N' => array('fl' => '86'),
            '13G500783O' => array('fl' => '82'),
            '19J103355N' => array('fl' => '89'),
            '18J103643N' => array('fl' => '86'),
            '17G103984N' => array('fl' => '86'),
            '18A104164N' => array('fl' => '86'),
            '19G500582O' => array('fl' => '92'),
            '16A104654N' => array('fl' => '85'),
            '15M104686N' => array('fl' => '89'),
            '15K104981N' => array('fl' => '90'),
            '13L105018N' => array('fl' => '86'),
            '13H105189N' => array('fl' => '90'),
            '10M105286N' => array('fl' => '86'),
            '14L105296N' => array('fl' => '86'),
            '16J105314N' => array('fl' => '86'),
            '13B105321N' => array('fl' => '86'),
            '18J105322N' => array('fl' => '86'),
            '13D105335N' => array('fl' => '86'),
            '15A105365N' => array('fl' => '86'),
            '13L105378N' => array('fl' => '86'),
            '16A105382N' => array('fl' => '86'),
            '17A105407N' => array('fl' => '86'),
            '11L105438N' => array('fl' => '82'),
            '17J105446N' => array('fl' => '86'),
            '16K105465N' => array('fl' => '86'),
            '17M105525N' => array('fl' => '86'),
            '17G105611N' => array('fl' => '86'),
            '18F105626N' => array('fl' => '86'),
            '17L105786N' => array('fl' => '86'),
            '21A075125L' => array('fl' => '82'),
            '21C010071B' => array('fl' => '82'),
            '20M035008F' => array('fl' => '82'),
            '21A108992N' => array('fl' => '92'),
            '21A075123L' => array('fl' => '82'),
            '21A075130L' => array('fl' => '89'),
            '21B110422N' => array('fl' => '89'),
            '21B501279O' => array('fl' => '90'),
            '21B110426N' => array('fl' => '90'),
            '21B501280O' => array('fl' => '89'),
            '21B110430N' => array('fl' => '90'),
            '21B501281O' => array('fl' => '90'),
            '21C110856N' => array('fl' => '85'),
            '21C111109N' => array('fl' => '92'),
            '21C111111N' => array('fl' => '92'),
            '21C111114N' => array('fl' => '92'),
            '21C501376O' => array('fl' => '90'),
            '21C111140N' => array('fl' => '90'),
            '21C111153N' => array('fl' => '90'),
            '21C111156N' => array('fl' => '90'),
            '21C111162N' => array('fl' => '90'),
            '21C111174N' => array('fl' => '92'),
            '19M107379N' => array('fl' => '82'),
            '19M107386N' => array('fl' => '82'),
            '19M500957O' => array('fl' => '90'),
            '12C107628N' => array('fl' => '86'),
            '20L107827N' => array('fl' => '86'),
            '20L107895N' => array('fl' => '86'),
            '20L108258N' => array('fl' => '90'),
            '20M108682N' => array('fl' => '92'),
            '20M108705N' => array('fl' => '92'),
            '20M108706N' => array('fl' => '89'),
            '21A109206N' => array('fl' => '86'),
            '21A109266N' => array('fl' => '86'),
            '21C110697N' => array('fl' => '92'),
            '21C110705N' => array('fl' => '92'),
            '21C110747N' => array('fl' => '89'),
            '21C110755N' => array('fl' => '92'),
            '21C110801N' => array('fl' => '90'),
            '21C110835N' => array('fl' => '92'),
            '21C110852N' => array('fl' => '89'),
            '18F103143N' => array('fl' => '82'),
            '08K102664N' => array('fl' => '82'),
            '19L000444A' => array('fl' => '82'),
            '11D102607N' => array('fl' => '92'),
            '19K055025J' => array('fl' => '82'),
            '20K000463A' => array('fl' => '82'),
            '13J102612N' => array('fl' => '90'),
            '18J065145K' => array('fl' => '82'),
            '08E010046B' => array('fl' => '82'),
            '20H107350N' => array('fl' => '85'),
            '10A102580N' => array('fl' => '86'),
            '13L102593N' => array('fl' => '92'),
            '18H102651N' => array('fl' => '86'),
            '96H500483O' => array('fl' => '85'),
            '19J500485O' => array('fl' => '89'),
            '18C500497O' => array('fl' => '92'),
            '12J500514O' => array('fl' => '85'),
            '12M500542O' => array('fl' => '82'),
            '19J500554O' => array('fl' => '86'),
            '19A040004G' => array('fl' => '82'),
            '15B020024C' => array('fl' => '82'),
            '18M500578O' => array('fl' => '82'),
            '14H055026J' => array('fl' => '82'),
            '13F055033J' => array('fl' => '82'),
            '18F104366N' => array('fl' => '85'),
            '15L010037B' => array('fl' => '82'),
            '15H010044B' => array('fl' => '82'),
            '16K010045B' => array('fl' => '82'),
            '11J045017I' => array('fl' => '82'),
            '12G045019I' => array('fl' => '89'),
            '17L045020I' => array('fl' => '90'),
            '18M000367A' => array('fl' => '82'),
            '16E090022M' => array('fl' => '82'),
            '17K010054B' => array('fl' => '82'),
            '19A000443A' => array('fl' => '82'),
            '20K107281N' => array('fl' => '86'),
            '20C500941O' => array('fl' => '92'),
            '19K500967O' => array('fl' => '90'),
            '19L500970O' => array('fl' => '89'),
            '21C055058J' => array('fl' => '85'),
            '13E090013M' => array('fl' => '82'),
            '10L010030B' => array('fl' => '82'),
            '15H025007D' => array('fl' => '82'),
            '14L500562O' => array('fl' => '82'),
            '98F500613O' => array('fl' => '82'),
            '19H010043B' => array('fl' => '82'),
            '17A010049B' => array('fl' => '82'),
            '17K010052B' => array('fl' => '82'),
            '20G010064B' => array('fl' => '82'),
            '20F010066B' => array('fl' => '82'),
            '20L501059O' => array('fl' => '86'),
            '20M010068B' => array('fl' => '82'),
            '21A075129L' => array('fl' => '82'),
            '15L105828N' => array('fl' => '82'),
            '15K105831N' => array('fl' => '82'),
            '19L107564N' => array('fl' => '82'),
            '18G104467N' => array('fl' => '85'),
            '18E104490N' => array('fl' => '85'),
            '16D104508N' => array('fl' => '85'),
            '16G104528N' => array('fl' => '85'),
            '17M104535N' => array('fl' => '85'),
            '16C104536N' => array('fl' => '85'),
            '18A104537N' => array('fl' => '85'),
            '12E104594N' => array('fl' => '85'),
            '16J104611N' => array('fl' => '85'),
            '16C104640N' => array('fl' => '85'),
            '14H105836N' => array('fl' => '85'),
            '19M107642N' => array('fl' => '85'),
            '19M107645N' => array('fl' => '85'),
            '19M107664N' => array('fl' => '85'),
            '21A109310N' => array('fl' => '85'),
            '21A109321N' => array('fl' => '85'),
            '21C110539N' => array('fl' => '85'),
            '21C110916N' => array('fl' => '85'),
            '21C111125N' => array('fl' => '85'),
            '17H075104L' => array('fl' => '82'),
            '17H075105L' => array('fl' => '82'),
            '10G105305N' => array('fl' => '86'),
            '08M105316N' => array('fl' => '86'),
            '11K105354N' => array('fl' => '86'),
            '15K105373N' => array('fl' => '86'),
            '16A105384N' => array('fl' => '86'),
            '15C105386N' => array('fl' => '86'),
            '15B105413N' => array('fl' => '86'),
            '15A105414N' => array('fl' => '86'),
            '18J105596N' => array('fl' => '86'),
            '18J105600N' => array('fl' => '86'),
            '19M107434N' => array('fl' => '86'),
            '20M108426N' => array('fl' => '86'),
            '21A109138N' => array('fl' => '86'),
            '21A109177N' => array('fl' => '86'),
            '13K000329A' => array('fl' => '86'),
            '16K000333A' => array('fl' => '90'),
            '15L000348A' => array('fl' => '89'),
            '20L000486A' => array('fl' => '82'),
            '21C000559A' => array('fl' => '92'),
            '08K500564O' => array('fl' => '82'),
            '15C090015M' => array('fl' => '82'),
            '13L105334N' => array('fl' => '86'),
            '15D105357N' => array('fl' => '86'),
            '16B105359N' => array('fl' => '86'),
            '16M105405N' => array('fl' => '86'),
            '16K105427N' => array('fl' => '86'),
            '16G105430N' => array('fl' => '86'),
            '17J105450N' => array('fl' => '86'),
            '16K105456N' => array('fl' => '86'),
            '17G105457N' => array('fl' => '86'),
            '17G105459N' => array('fl' => '86'),
            '16M105467N' => array('fl' => '86'),
            '17C105470N' => array('fl' => '86'),
            '16D105472N' => array('fl' => '86'),
            '16E105479N' => array('fl' => '86'),
            '16D105481N' => array('fl' => '86'),
            '17L105489N' => array('fl' => '86'),
            '17K105504N' => array('fl' => '86'),
            '17C105505N' => array('fl' => '86'),
            '16J105512N' => array('fl' => '86'),
            '16J105513N' => array('fl' => '86'),
            '17M105530N' => array('fl' => '86'),
            '18A105538N' => array('fl' => '86'),
            '18A105542N' => array('fl' => '86'),
            '18C105546N' => array('fl' => '86'),
            '18J105599N' => array('fl' => '86'),
            '17K105620N' => array('fl' => '86'),
            '18J105648N' => array('fl' => '86'),
            '19F105662N' => array('fl' => '85'),
            '19C105695N' => array('fl' => '86'),
            '19F105754N' => array('fl' => '86'),
            '19F105757N' => array('fl' => '86'),
            '19H105797N' => array('fl' => '86'),
            '19F105821N' => array('fl' => '86'),
            '19M107387N' => array('fl' => '86'),
            '19M107400N' => array('fl' => '86'),
            '19M107405N' => array('fl' => '86'),
            '19M107406N' => array('fl' => '86'),
            '20L107828N' => array('fl' => '86'),
            '21A108979N' => array('fl' => '86'),
            '21A108996N' => array('fl' => '86'),
            '21A109086N' => array('fl' => '86'),
            '21B109544N' => array('fl' => '86'),
            '21B109546N' => array('fl' => '86'),
            '21B110288N' => array('fl' => '86'),
            '21B110373N' => array('fl' => '86'),
            '21B110732N' => array('fl' => '86'),
            '21C110738N' => array('fl' => '86'),
            '21C110807N' => array('fl' => '86'),
            '16A105361N' => array('fl' => '86'),
            '17K105444N' => array('fl' => '86'),
            '17G105495N' => array('fl' => '86'),
            '18C105539N' => array('fl' => '86'),
            '18J105594N' => array('fl' => '86'),
            '18G105608N' => array('fl' => '86'),
            '19F105682N' => array('fl' => '86'),
            '19H105708N' => array('fl' => '86'),
            '19J105780N' => array('fl' => '86'),
            '19H105808N' => array('fl' => '86'),
            '19K105916N' => array('fl' => '86'),
            '19M107425N' => array('fl' => '86'),
            '19L107569N' => array('fl' => '86'),
            '19L107577N' => array('fl' => '86'),
            '19K107632N' => array('fl' => '86'),
            '20L107839N' => array('fl' => '86'),
            '20L107987N' => array('fl' => '86'),
            '20L107998N' => array('fl' => '86'),
            '20L107999N' => array('fl' => '86'),
            '20L108116N' => array('fl' => '86'),
            '20L108117N' => array('fl' => '86'),
            '20E108633N' => array('fl' => '86'),
            '20M108652N' => array('fl' => '86'),
            '21A108917N' => array('fl' => '86'),
            '21A108977N' => array('fl' => '86'),
            '21A108991N' => array('fl' => '86'),
            '21A109167N' => array('fl' => '86'),
            '21A109208N' => array('fl' => '86'),
            '21B110263N' => array('fl' => '86'),
            '21C110540N' => array('fl' => '86'),
            '21C110541N' => array('fl' => '86'),
            '19F104335N' => array('fl' => '85'),
            '17K104525N' => array('fl' => '85'),
            '16C104526N' => array('fl' => '85'),
            '16C104574N' => array('fl' => '85'),
            '15B104615N' => array('fl' => '85'),
            '16C104620N' => array('fl' => '85'),
            '15A104624N' => array('fl' => '85'),
            '16C104628N' => array('fl' => '85'),
            '13L104829N' => array('fl' => '85'),
            '19J105240N' => array('fl' => '89'),
            '11J105249N' => array('fl' => '85'),
            '19M065171K' => array('fl' => '82'),
            '19K107636N' => array('fl' => '86'),
            '21A109144N' => array('fl' => '82'),
            '21A065184K' => array('fl' => '82'),
            '19J102563N' => array('fl' => '89'),
            '17L102571N' => array('fl' => '92'),
            '17K102719N' => array('fl' => '92'),
            '17B102726N' => array('fl' => '92'),
            '18J102741N' => array('fl' => '90'),
            '19B102877N' => array('fl' => '90'),
            '19J103163N' => array('fl' => '92'),
            '19J103171N' => array('fl' => '92'),
            '19K103182N' => array('fl' => '92'),
            '19H103195N' => array('fl' => '92'),
            '19H103196N' => array('fl' => '90'),
            '19F103209N' => array('fl' => '89'),
            '19H103212N' => array('fl' => '90'),
            '19F103214N' => array('fl' => '92'),
            '19K103216N' => array('fl' => '92'),
            '19K103221N' => array('fl' => '90'),
            '19J103240N' => array('fl' => '92'),
            '19J103261N' => array('fl' => '89'),
            '19J103265N' => array('fl' => '92'),
            '19J103279N' => array('fl' => '92'),
            '19F103305N' => array('fl' => '92'),
            '19H103307N' => array('fl' => '89'),
            '19F103313N' => array('fl' => '89'),
            '19J103357N' => array('fl' => '86'),
            '19J103359N' => array('fl' => '90'),
            '19J103363N' => array('fl' => '89'),
            '19J103366N' => array('fl' => '89'),
            '19H103372N' => array('fl' => '90'),
            '19H103374N' => array('fl' => '86'),
            '19H103375N' => array('fl' => '90'),
            '19H103378N' => array('fl' => '90'),
            '19H103383N' => array('fl' => '90'),
            '19G103400N' => array('fl' => '86'),
            '19G103405N' => array('fl' => '89'),
            '19G103406N' => array('fl' => '90'),
            '19G103411N' => array('fl' => '90'),
            '19G103413N' => array('fl' => '92'),
            '19J103421N' => array('fl' => '86'),
            '19J103439N' => array('fl' => '90'),
            '19H103457N' => array('fl' => '92'),
            '19K103465N' => array('fl' => '92'),
            '19J103513N' => array('fl' => '90'),
            '19G103532N' => array('fl' => '90'),
            '19G103535N' => array('fl' => '92'),
            '19G103536N' => array('fl' => '89'),
            '19G103539N' => array('fl' => '90'),
            '19F103544N' => array('fl' => '92'),
            '19E103551N' => array('fl' => '90'),
            '19D103574N' => array('fl' => '90'),
            '18K103595N' => array('fl' => '92'),
            '18K103596N' => array('fl' => '92'),
            '18F103597N' => array('fl' => '89'),
            '18E103598N' => array('fl' => '90'),
            '19F103612N' => array('fl' => '86'),
            '19F103613N' => array('fl' => '86'),
            '19D103627N' => array('fl' => '90'),
            '18J103642N' => array('fl' => '92'),
            '19D103656N' => array('fl' => '86'),
            '18M103676N' => array('fl' => '90'),
            '18M103680N' => array('fl' => '89'),
            '18J103691N' => array('fl' => '92'),
            '18J103695N' => array('fl' => '92'),
            '19B103716N' => array('fl' => '92'),
            '19B103725N' => array('fl' => '90'),
            '19A103738N' => array('fl' => '90'),
            '19A103740N' => array('fl' => '90'),
            '18L103749N' => array('fl' => '89'),
            '18E103758N' => array('fl' => '92'),
            '18M103765N' => array('fl' => '86'),
            '18M103768N' => array('fl' => '90'),
            '18M103769N' => array('fl' => '90'),
            '18L103778N' => array('fl' => '90'),
            '18L103780N' => array('fl' => '90'),
            '18L103781N' => array('fl' => '92'),
            '18K103787N' => array('fl' => '89'),
            '18K103807N' => array('fl' => '90'),
            '18G103816N' => array('fl' => '90'),
            '18J103821N' => array('fl' => '90'),
            '18J103829N' => array('fl' => '89'),
            '18F103841N' => array('fl' => '92'),
            '18L103863N' => array('fl' => '90'),
            '18K103870N' => array('fl' => '89'),
            '18K103889N' => array('fl' => '92'),
            '18J103891N' => array('fl' => '92'),
            '18J103892N' => array('fl' => '89'),
            '18J103895N' => array('fl' => '89'),
            '18J103904N' => array('fl' => '89'),
            '18G103915N' => array('fl' => '89'),
            '18G103923N' => array('fl' => '89'),
            '18F103925N' => array('fl' => '89'),
            '18J103931N' => array('fl' => '90'),
            '18J103932N' => array('fl' => '89'),
            '18G103965N' => array('fl' => '92'),
            '18G103972N' => array('fl' => '89'),
            '18G103973N' => array('fl' => '89'),
            '17L103996N' => array('fl' => '89'),
            '17L104002N' => array('fl' => '86'),
            '17L104003N' => array('fl' => '92'),
            '17M104010N' => array('fl' => '86'),
            '17M104018N' => array('fl' => '89'),
            '17M104039N' => array('fl' => '89'),
            '18D104042N' => array('fl' => '90'),
            '18A104057N' => array('fl' => '90'),
            '18A104060N' => array('fl' => '90'),
            '18C104069N' => array('fl' => '92'),
            '18C104071N' => array('fl' => '92'),
            '18B104075N' => array('fl' => '89'),
            '18B104077N' => array('fl' => '92'),
            '18A104082N' => array('fl' => '92'),
            '18C104088N' => array('fl' => '90'),
            '18A104095N' => array('fl' => '90'),
            '18C104102N' => array('fl' => '90'),
            '18C104105N' => array('fl' => '90'),
            '18C104109N' => array('fl' => '90'),
            '17M104121N' => array('fl' => '92'),
            '18K104125N' => array('fl' => '92'),
            '17M104129N' => array('fl' => '92'),
            '17L104143N' => array('fl' => '92'),
            '17L104146N' => array('fl' => '90'),
            '17L104147N' => array('fl' => '92'),
            '17L104154N' => array('fl' => '92'),
            '17L104155N' => array('fl' => '90'),
            '17M104170N' => array('fl' => '90'),
            '17L104174N' => array('fl' => '89'),
            '18B104179N' => array('fl' => '92'),
            '17L104180N' => array('fl' => '89'),
            '18B104182N' => array('fl' => '90'),
            '17L104185N' => array('fl' => '89'),
            '17L104192N' => array('fl' => '89'),
            '17L104194N' => array('fl' => '90'),
            '17L104201N' => array('fl' => '92'),
            '17G104202N' => array('fl' => '90'),
            '17C104220N' => array('fl' => '89'),
            '17G104230N' => array('fl' => '92'),
            '16K104235N' => array('fl' => '90'),
            '16J104236N' => array('fl' => '90'),
            '17K104250N' => array('fl' => '92'),
            '16E104271N' => array('fl' => '89'),
            '18B104281N' => array('fl' => '89'),
            '17K104283N' => array('fl' => '89'),
            '17H104288N' => array('fl' => '90'),
            '17G104292N' => array('fl' => '90'),
            '17J104309N' => array('fl' => '90'),
            '17B104313N' => array('fl' => '90'),
            '16J104328N' => array('fl' => '92'),
            '16E104329N' => array('fl' => '90'),
            '15L104343N' => array('fl' => '92'),
            '16J104344N' => array('fl' => '89'),
            '16B104356N' => array('fl' => '92'),
            '17K104359N' => array('fl' => '90'),
            '17K104360N' => array('fl' => '89'),
            '17K104362N' => array('fl' => '89'),
            '17K104371N' => array('fl' => '92'),
            '17G104373N' => array('fl' => '90'),
            '17G104379N' => array('fl' => '90'),
            '16L104380N' => array('fl' => '92'),
            '15H104510N' => array('fl' => '90'),
            '15E104515N' => array('fl' => '92'),
            '16C104542N' => array('fl' => '90'),
            '16D104577N' => array('fl' => '92'),
            '17D104582N' => array('fl' => '92'),
            '17G104598N' => array('fl' => '90'),
            '17L104603N' => array('fl' => '90'),
            '17L104607N' => array('fl' => '89'),
            '16E104617N' => array('fl' => '90'),
            '17D104625N' => array('fl' => '92'),
            '15L104642N' => array('fl' => '90'),
            '16B104659N' => array('fl' => '89'),
            '16B104661N' => array('fl' => '90'),
            '16A104662N' => array('fl' => '90'),
            '15D104665N' => array('fl' => '92'),
            '15B104666N' => array('fl' => '90'),
            '15K104668N' => array('fl' => '90'),
            '15M104672N' => array('fl' => '90'),
            '15K104680N' => array('fl' => '92'),
            '17B104682N' => array('fl' => '90'),
            '15M104687N' => array('fl' => '90'),
            '15L104694N' => array('fl' => '90'),
            '15B104705N' => array('fl' => '90'),
            '16C104714N' => array('fl' => '92'),
            '16C104723N' => array('fl' => '89'),
            '16B104726N' => array('fl' => '90'),
            '15A104738N' => array('fl' => '90'),
            '17A104745N' => array('fl' => '89'),
            '16K104749N' => array('fl' => '90'),
            '16K104753N' => array('fl' => '90'),
            '16J104757N' => array('fl' => '90'),
            '16K104773N' => array('fl' => '89'),
            '14M104790N' => array('fl' => '90'),
            '14C104799N' => array('fl' => '92'),
            '13D104806N' => array('fl' => '90'),
            '14M104808N' => array('fl' => '90'),
            '15H104832N' => array('fl' => '92'),
            '17A104839N' => array('fl' => '90'),
            '14B104844N' => array('fl' => '90'),
            '16A104847N' => array('fl' => '92'),
            '16A104850N' => array('fl' => '89'),
            '15K104851N' => array('fl' => '92'),
            '15K104855N' => array('fl' => '90'),
            '15K104859N' => array('fl' => '90'),
            '14K104876N' => array('fl' => '92'),
            '14C104882N' => array('fl' => '92'),
            '15A104891N' => array('fl' => '86'),
            '14C104898N' => array('fl' => '92'),
            '14L104917N' => array('fl' => '92'),
            '14J104924N' => array('fl' => '90'),
            '16B104933N' => array('fl' => '90'),
            '13C104935N' => array('fl' => '92'),
            '12E104942N' => array('fl' => '90'),
            '13A104946N' => array('fl' => '92'),
            '14H104961N' => array('fl' => '92'),
            '14H104964N' => array('fl' => '92'),
            '12E104978N' => array('fl' => '90'),
            '12E104979N' => array('fl' => '92'),
            '15K104992N' => array('fl' => '90'),
            '12E104997N' => array('fl' => '92'),
            '12F105001N' => array('fl' => '92'),
            '12L105007N' => array('fl' => '90'),
            '15K105008N' => array('fl' => '92'),
            '12L105010N' => array('fl' => '90'),
            '14J105015N' => array('fl' => '90'),
            '13J105017N' => array('fl' => '90'),
            '13L105026N' => array('fl' => '90'),
            '13L105029N' => array('fl' => '90'),
            '13L105030N' => array('fl' => '92'),
            '13L105033N' => array('fl' => '90'),
            '13J105044N' => array('fl' => '86'),
            '12J105045N' => array('fl' => '92'),
            '13J105046N' => array('fl' => '90'),
            '11L105065N' => array('fl' => '92'),
            '11L105069N' => array('fl' => '86'),
            '14K105082N' => array('fl' => '90'),
            '08K105108N' => array('fl' => '92'),
            '11D105133N' => array('fl' => '90'),
            '14C105144N' => array('fl' => '90'),
            '14C105148N' => array('fl' => '90'),
            '11C105153N' => array('fl' => '92'),
            '11B105154N' => array('fl' => '90'),
            '11C105159N' => array('fl' => '92'),
            '13H105164N' => array('fl' => '90'),
            '09M105195N' => array('fl' => '92'),
            '09L105197N' => array('fl' => '92'),
            '13E105216N' => array('fl' => '92'),
            '12L105219N' => array('fl' => '90'),
            '14H105223N' => array('fl' => '92'),
            '13A105225N' => array('fl' => '90'),
            '13H105226N' => array('fl' => '92'),
            '10B105229N' => array('fl' => '92'),
            '13H105235N' => array('fl' => '90'),
            '13H105236N' => array('fl' => '90'),
            '13K105826N' => array('fl' => '82'),
            '17G105920N' => array('fl' => '92'),
            '19K105934N' => array('fl' => '90'),
            '19K105945N' => array('fl' => '92'),
            '19K105948N' => array('fl' => '90'),
            '19K105949N' => array('fl' => '90'),
            '19K105951N' => array('fl' => '90'),
            '19K105956N' => array('fl' => '90'),
            '19K105958N' => array('fl' => '86'),
            '20K106941N' => array('fl' => '89'),
            '20K106948N' => array('fl' => '90'),
            '20K106961N' => array('fl' => '90'),
            '20K106966N' => array('fl' => '89'),
            '20K106968N' => array('fl' => '92'),
            '20K106972N' => array('fl' => '92'),
            '20K106978N' => array('fl' => '92'),
            '20K106989N' => array('fl' => '92'),
            '20K106994N' => array('fl' => '92'),
            '20K107004N' => array('fl' => '92'),
            '20K107005N' => array('fl' => '92'),
            '20K107010N' => array('fl' => '90'),
            '20K107012N' => array('fl' => '89'),
            '20K107014N' => array('fl' => '92'),
            '20K107020N' => array('fl' => '90'),
            '20K107026N' => array('fl' => '92'),
            '20K107030N' => array('fl' => '92'),
            '20K107033N' => array('fl' => '90'),
            '20K107043N' => array('fl' => '90'),
            '20K107049N' => array('fl' => '90'),
            '20K107057N' => array('fl' => '89'),
            '20K107060N' => array('fl' => '92'),
            '20K107070N' => array('fl' => '90'),
            '20K107071N' => array('fl' => '89'),
            '20K107073N' => array('fl' => '89'),
            '20K107082N' => array('fl' => '89'),
            '20K107083N' => array('fl' => '89'),
            '20K107084N' => array('fl' => '92'),
            '20K107088N' => array('fl' => '89'),
            '20K107092N' => array('fl' => '92'),
            '20K107107N' => array('fl' => '89'),
            '20K107115N' => array('fl' => '89'),
            '20K107119N' => array('fl' => '92'),
            '20K107124N' => array('fl' => '92'),
            '20K107131N' => array('fl' => '90'),
            '20K107134N' => array('fl' => '89'),
            '20K107136N' => array('fl' => '92'),
            '20K107147N' => array('fl' => '92'),
            '20K107148N' => array('fl' => '92'),
            '20K107160N' => array('fl' => '92'),
            '20K107161N' => array('fl' => '86'),
            '20K107162N' => array('fl' => '90'),
            '20K107163N' => array('fl' => '90'),
            '20K107171N' => array('fl' => '90'),
            '20K107173N' => array('fl' => '90'),
            '20K107176N' => array('fl' => '90'),
            '20K107177N' => array('fl' => '86'),
            '20K107178N' => array('fl' => '90'),
            '20K107180N' => array('fl' => '92'),
            '20K107181N' => array('fl' => '92'),
            '20K107183N' => array('fl' => '90'),
            '20K107186N' => array('fl' => '90'),
            '20K107187N' => array('fl' => '90'),
            '20K107189N' => array('fl' => '89'),
            '20K107195N' => array('fl' => '89'),
            '20K107198N' => array('fl' => '90'),
            '20K107205N' => array('fl' => '92'),
            '20K107223N' => array('fl' => '92'),
            '20K107228N' => array('fl' => '89'),
            '20K107231N' => array('fl' => '92'),
            '20K107236N' => array('fl' => '90'),
            '20K107238N' => array('fl' => '86'),
            '20K107248N' => array('fl' => '90'),
            '20K107252N' => array('fl' => '92'),
            '20K107255N' => array('fl' => '90'),
            '20K107258N' => array('fl' => '90'),
            '20K107260N' => array('fl' => '90'),
            '20K107263N' => array('fl' => '92'),
            '20K107264N' => array('fl' => '90'),
            '20J107294N' => array('fl' => '92'),
            '20J107296N' => array('fl' => '92'),
            '20J107298N' => array('fl' => '89'),
            '20J107306N' => array('fl' => '92'),
            '20J107312N' => array('fl' => '92'),
            '20J107314N' => array('fl' => '90'),
            '20J107320N' => array('fl' => '89'),
            '20J107321N' => array('fl' => '92'),
            '20J107323N' => array('fl' => '92'),
            '20J107325N' => array('fl' => '92'),
            '19M107368N' => array('fl' => '92'),
            '19M107372N' => array('fl' => '90'),
            '19M107465N' => array('fl' => '90'),
            '19M107469N' => array('fl' => '92'),
            '19M107475N' => array('fl' => '92'),
            '19M107483N' => array('fl' => '90'),
            '19M107484N' => array('fl' => '92'),
            '19M107486N' => array('fl' => '92'),
            '19M107499N' => array('fl' => '90'),
            '19M107504N' => array('fl' => '90'),
            '19M107505N' => array('fl' => '92'),
            '19M107508N' => array('fl' => '92'),
            '19M107511N' => array('fl' => '90'),
            '19M107512N' => array('fl' => '86'),
            '19M107516N' => array('fl' => '86'),
            '19M107525N' => array('fl' => '90'),
            '19M107535N' => array('fl' => '92'),
            '19M107540N' => array('fl' => '89'),
            '19L107541N' => array('fl' => '89'),
            '19L107542N' => array('fl' => '92'),
            '19L107544N' => array('fl' => '90'),
            '19L107548N' => array('fl' => '92'),
            '19L107554N' => array('fl' => '92'),
            '19L107556N' => array('fl' => '90'),
            '19L107560N' => array('fl' => '89'),
            '19L107563N' => array('fl' => '90'),
            '19L107565N' => array('fl' => '90'),
            '19L107566N' => array('fl' => '92'),
            '19L107573N' => array('fl' => '89'),
            '19L107594N' => array('fl' => '90'),
            '19L107598N' => array('fl' => '92'),
            '19L107600N' => array('fl' => '92'),
            '19L107607N' => array('fl' => '89'),
            '19L107608N' => array('fl' => '92'),
            '19L107609N' => array('fl' => '90'),
            '20K107617N' => array('fl' => '92'),
            '19L107618N' => array('fl' => '90'),
            '19K107623N' => array('fl' => '92'),
            '19K107626N' => array('fl' => '92'),
            '19L107629N' => array('fl' => '89'),
            '20L107726N' => array('fl' => '86'),
            '20L107728N' => array('fl' => '90'),
            '20L107735N' => array('fl' => '89'),
            '20L107752N' => array('fl' => '90'),
            '20L107766N' => array('fl' => '90'),
            '20L107772N' => array('fl' => '90'),
            '20L107773N' => array('fl' => '92'),
            '20L107789N' => array('fl' => '90'),
            '20L107796N' => array('fl' => '89'),
            '20L107807N' => array('fl' => '90'),
            '20L107870N' => array('fl' => '89'),
            '20L107872N' => array('fl' => '90'),
            '20L107873N' => array('fl' => '90'),
            '20L107875N' => array('fl' => '86'),
            '20L107883N' => array('fl' => '89'),
            '20L107884N' => array('fl' => '89'),
            '20L107885N' => array('fl' => '90'),
            '20L107887N' => array('fl' => '90'),
            '20L107893N' => array('fl' => '92'),
            '20L107899N' => array('fl' => '90'),
            '20L107907N' => array('fl' => '92'),
            '20L107910N' => array('fl' => '90'),
            '20L107923N' => array('fl' => '92'),
            '20L107929N' => array('fl' => '89'),
            '20L107933N' => array('fl' => '90'),
            '20L107947N' => array('fl' => '92'),
            '20L107954N' => array('fl' => '90'),
            '20L107973N' => array('fl' => '89'),
            '20L107974N' => array('fl' => '90'),
            '20L107977N' => array('fl' => '90'),
            '20L108039N' => array('fl' => '92'),
            '20L108042N' => array('fl' => '92'),
            '20L108044N' => array('fl' => '92'),
            '20L108050N' => array('fl' => '92'),
            '20L108053N' => array('fl' => '90'),
            '20L108060N' => array('fl' => '89'),
            '20L108129N' => array('fl' => '90'),
            '20L108131N' => array('fl' => '92'),
            '20L108132N' => array('fl' => '90'),
            '20L108134N' => array('fl' => '90'),
            '20L108135N' => array('fl' => '92'),
            '20L108176N' => array('fl' => '90'),
            '20L108180N' => array('fl' => '86'),
            '20L108206N' => array('fl' => '89'),
            '20L108209N' => array('fl' => '90'),
            '20L108214N' => array('fl' => '90'),
            '20L108215N' => array('fl' => '89'),
            '20L108220N' => array('fl' => '92'),
            '20L108233N' => array('fl' => '92'),
            '20L108250N' => array('fl' => '90'),
            '20L108251N' => array('fl' => '86'),
            '20L108253N' => array('fl' => '92'),
            '20L108254N' => array('fl' => '90'),
            '20L108255N' => array('fl' => '90'),
            '20L108271N' => array('fl' => '90'),
            '20L108289N' => array('fl' => '90'),
            '20M108398N' => array('fl' => '89'),
            '20M108406N' => array('fl' => '92'),
            '20M108428N' => array('fl' => '92'),
            '20M108439N' => array('fl' => '89'),
            '20M108441N' => array('fl' => '92'),
            '20M108456N' => array('fl' => '92'),
            '20M108459N' => array('fl' => '92'),
            '20M108460N' => array('fl' => '90'),
            '20B108544N' => array('fl' => '92'),
            '20B108546N' => array('fl' => '90'),
            '20B108553N' => array('fl' => '90'),
            '20B108558N' => array('fl' => '92'),
            '20C108566N' => array('fl' => '90'),
            '20C108568N' => array('fl' => '89'),
            '20M108570N' => array('fl' => '89'),
            '20E108613N' => array('fl' => '90'),
            '20E108631N' => array('fl' => '90'),
            '20M108642N' => array('fl' => '90'),
            '20M108644N' => array('fl' => '90'),
            '20M108674N' => array('fl' => '90'),
            '20M108678N' => array('fl' => '90'),
            '20M108698N' => array('fl' => '92'),
            '20M108701N' => array('fl' => '90'),
            '20M108704N' => array('fl' => '92'),
            '20M108714N' => array('fl' => '92'),
            '20M108808N' => array('fl' => '92'),
            '20M108835N' => array('fl' => '92'),
            '21A108914N' => array('fl' => '92'),
            '21A108916N' => array('fl' => '89'),
            '21A108931N' => array('fl' => '89'),
            '21A108956N' => array('fl' => '90'),
            '21A108957N' => array('fl' => '92'),
            '21A108970N' => array('fl' => '90'),
            '21A108971N' => array('fl' => '90'),
            '21A108973N' => array('fl' => '90'),
            '21A108983N' => array('fl' => '89'),
            '21A108999N' => array('fl' => '89'),
            '21A109013N' => array('fl' => '90'),
            '21A109023N' => array('fl' => '92'),
            '21A109031N' => array('fl' => '89'),
            '21A109034N' => array('fl' => '89'),
            '21A109071N' => array('fl' => '89'),
            '21A109073N' => array('fl' => '90'),
            '21A109129N' => array('fl' => '90'),
            '21A109131N' => array('fl' => '92'),
            '21A109151N' => array('fl' => '92'),
            '21A109152N' => array('fl' => '86'),
            '21A109187N' => array('fl' => '90'),
            '21A109190N' => array('fl' => '90'),
            '21A109225N' => array('fl' => '92'),
            '21A109233N' => array('fl' => '92'),
            '21A109237N' => array('fl' => '90'),
            '21A109257N' => array('fl' => '90'),
            '21A109286N' => array('fl' => '86'),
            '21A109287N' => array('fl' => '89'),
            '21A109320N' => array('fl' => '92'),
            '21B109415N' => array('fl' => '89'),
            '21B109417N' => array('fl' => '92'),
            '21B109419N' => array('fl' => '89'),
            '21B109420N' => array('fl' => '89'),
            '21B109421N' => array('fl' => '89'),
            '21B109425N' => array('fl' => '92'),
            '21B109436N' => array('fl' => '92'),
            '21B109437N' => array('fl' => '90'),
            '21B110252N' => array('fl' => '89'),
            '21B110254N' => array('fl' => '90'),
            '21B110257N' => array('fl' => '90'),
            '21B110264N' => array('fl' => '86'),
            '21B110298N' => array('fl' => '89'),
            '21B110311N' => array('fl' => '89'),
            '21B110320N' => array('fl' => '86'),
            '21B110327N' => array('fl' => '92'),
            '21B110329N' => array('fl' => '90'),
            '21B110333N' => array('fl' => '92'),
            '21B110347N' => array('fl' => '86'),
            '21B110368N' => array('fl' => '86'),
            '21B110379N' => array('fl' => '89'),
            '21B110383N' => array('fl' => '92'),
            '21B110392N' => array('fl' => '89'),
            '21B110397N' => array('fl' => '90'),
            '21B110398N' => array('fl' => '90'),
            '21B110404N' => array('fl' => '90'),
            '21B110405N' => array('fl' => '90'),
            '21C110665N' => array('fl' => '92'),
            '21C110669N' => array('fl' => '90'),
            '21C110670N' => array('fl' => '90'),
            '21C110672N' => array('fl' => '90'),
            '21C110686N' => array('fl' => '90'),
            '21C110703N' => array('fl' => '89'),
            '21C110707N' => array('fl' => '92'),
            '21C110721N' => array('fl' => '89'),
            '21C110723N' => array('fl' => '92'),
            '21C110728N' => array('fl' => '82'),
            '21C110735N' => array('fl' => '89'),
            '21C110740N' => array('fl' => '90'),
            '21C110748N' => array('fl' => '90'),
            '21C110749N' => array('fl' => '89'),
            '21C110754N' => array('fl' => '89'),
            '21C110757N' => array('fl' => '90'),
            '21C110759N' => array('fl' => '90'),
            '21C110771N' => array('fl' => '90'),
            '21C110787N' => array('fl' => '92'),
            '21C110797N' => array('fl' => '86'),
            '21C110800N' => array('fl' => '92'),
            '21C110818N' => array('fl' => '90'),
            '21C110828N' => array('fl' => '90'),
            '21C110841N' => array('fl' => '90'),
            '21C110872N' => array('fl' => '90'),
            '21C110894N' => array('fl' => '90'),
            '21C110896N' => array('fl' => '89'),
            '21C110897N' => array('fl' => '89'),
            '21C110903N' => array('fl' => '89'),
            '21C110914N' => array('fl' => '90'),
            '21C110917N' => array('fl' => '92'),
            '21C110966N' => array('fl' => '90'),
            '21C110967N' => array('fl' => '90'),
            '21C110969N' => array('fl' => '90'),
            '21C110973N' => array('fl' => '89'),
            '21C110978N' => array('fl' => '90'),
            '21C110979N' => array('fl' => '89'),
            '21C110980N' => array('fl' => '89'),
            '21C111096N' => array('fl' => '90'),
            '12H000289A' => array('fl' => '89'),
            '12G000290A' => array('fl' => '86'),
            '12E000291A' => array('fl' => '85'),
            '19C000292A' => array('fl' => '82'),
            '19J000293A' => array('fl' => '92'),
            '13L000295A' => array('fl' => '82'),
            '13B000296A' => array('fl' => '86'),
            '12F000297A' => array('fl' => '90'),
            '18J000298A' => array('fl' => '86'),
            '18G000299A' => array('fl' => '89'),
            '18K000302A' => array('fl' => '92'),
            '19G000303A' => array('fl' => '89'),
            '18K000304A' => array('fl' => '92'),
            '18M000305A' => array('fl' => '89'),
            '18J000306A' => array('fl' => '86'),
            '18K000307A' => array('fl' => '86'),
            '18G000308A' => array('fl' => '90'),
            '18J000309A' => array('fl' => '85'),
            '19F000310A' => array('fl' => '86'),
            '16K000311A' => array('fl' => '86'),
            '19B000313A' => array('fl' => '89'),
            '18M000315A' => array('fl' => '92'),
            '18M000316A' => array('fl' => '85'),
            '18G000317A' => array('fl' => '90'),
            '17M000318A' => array('fl' => '82'),
            '17L000319A' => array('fl' => '82'),
            '17L000320A' => array('fl' => '90'),
            '16C000321A' => array('fl' => '92'),
            '16K000322A' => array('fl' => '86'),
            '18M000324A' => array('fl' => '90'),
            '13K000325A' => array('fl' => '82'),
            '16B000327A' => array('fl' => '89'),
            '13F000330A' => array('fl' => '82'),
            '17L000331A' => array('fl' => '90'),
            '17A000334A' => array('fl' => '85'),
            '16D000336A' => array('fl' => '86'),
            '16M000337A' => array('fl' => '82'),
            '16A000339A' => array('fl' => '86'),
            '13J000340A' => array('fl' => '86'),
            '13J000342A' => array('fl' => '89'),
            '09E000343A' => array('fl' => '85'),
            '10D000344A' => array('fl' => '89'),
            '18K000349A' => array('fl' => '89'),
            '10M000351A' => array('fl' => '92'),
            '13J000357A' => array('fl' => '85'),
            '20K000471A' => array('fl' => '90'),
            '20J000472A' => array('fl' => '82'),
            '19L000474A' => array('fl' => '85'),
            '19L000475A' => array('fl' => '90'),
            '19L000476A' => array('fl' => '89'),
            '20L000480A' => array('fl' => '85'),
            '20L000481A' => array('fl' => '92'),
            '20L000482A' => array('fl' => '92'),
            '20M000487A' => array('fl' => '92'),
            '20M000492A' => array('fl' => '86'),
            '19J103242N' => array('fl' => '85'),
            '12L104963N' => array('fl' => '85'),
            '17B105829N' => array('fl' => '82'),
            '19J105832N' => array('fl' => '82'),
            '17J105833N' => array('fl' => '82'),
            '17J105834N' => array('fl' => '82'),
            '18B105835N' => array('fl' => '82'),
            '19L107610N' => array('fl' => '85'),
            '19K045014I' => array('fl' => '89'),
            '19K045018I' => array('fl' => '92'),
            '19H045020I' => array('fl' => '92'),
            '14K105837N' => array('fl' => '85'),
            '13J105841N' => array('fl' => '85'),
            '20J500979O' => array('fl' => '82'),
            '20H500980O' => array('fl' => '82'),
            '19B000485A' => array('fl' => '82'),
            '20L108097N' => array('fl' => '92'),
            '20L045029I' => array('fl' => '89'),
            '20M045032I' => array('fl' => '92'),
            '21A045033I' => array('fl' => '90'),
            '18K010021B' => array('fl' => '82'),
            '02M102589N' => array('fl' => '86'),
            '13E102603N' => array('fl' => '86'),
            '19H500492O' => array('fl' => '82'),
            '18D010039B' => array('fl' => '82'),
            '16C045013I' => array('fl' => '92'),
            '18M000370A' => array('fl' => '82'),
            '18B000376A' => array('fl' => '82'),
            '18A065157K' => array('fl' => '82'),
            '92F105827N' => array('fl' => '82'),
            '19J000390A' => array('fl' => '82'),
            '18L000391A' => array('fl' => '82'),
            '18K000392A' => array('fl' => '82'),
            '18K000393A' => array('fl' => '82'),
            '15H000395A' => array('fl' => '82'),
            '10M000397A' => array('fl' => '82'),
            '10L000398A' => array('fl' => '82'),
            '19E102809N' => array('fl' => '92'),
            '19E102810N' => array('fl' => '92'),
            '19E102811N' => array('fl' => '92'),
            '19C102818N' => array('fl' => '89'),
            '19H102829N' => array('fl' => '92'),
            '19H102831N' => array('fl' => '90'),
            '19H102833N' => array('fl' => '92'),
            '19G102838N' => array('fl' => '90'),
            '19H102845N' => array('fl' => '92'),
            '19H102846N' => array('fl' => '92'),
            '19H102847N' => array('fl' => '92'),
            '19F102849N' => array('fl' => '92'),
            '18J102851N' => array('fl' => '89'),
            '18J102852N' => array('fl' => '90'),
            '18J102860N' => array('fl' => '92'),
            '18G102863N' => array('fl' => '89'),
            '18G102864N' => array('fl' => '89'),
            '19F102866N' => array('fl' => '89'),
            '18J102869N' => array('fl' => '92'),
            '19F102871N' => array('fl' => '92'),
            '19F102872N' => array('fl' => '92'),
            '19D102874N' => array('fl' => '92'),
            '19D102875N' => array('fl' => '92'),
            '19D102876N' => array('fl' => '92'),
            '19F102881N' => array('fl' => '90'),
            '19C102883N' => array('fl' => '89'),
            '19F102886N' => array('fl' => '92'),
            '19E102890N' => array('fl' => '92'),
            '19E102891N' => array('fl' => '92'),
            '19E102892N' => array('fl' => '92'),
            '18F102894N' => array('fl' => '90'),
            '19K102897N' => array('fl' => '92'),
            '19J102901N' => array('fl' => '89'),
            '19G102905N' => array('fl' => '92'),
            '19G102906N' => array('fl' => '92'),
            '19F102907N' => array('fl' => '90'),
            '17G102911N' => array('fl' => '90'),
            '19B102912N' => array('fl' => '90'),
            '17K102914N' => array('fl' => '90'),
            '17L102915N' => array('fl' => '92'),
            '17G102916N' => array('fl' => '90'),
            '16M102921N' => array('fl' => '90'),
            '17J102922N' => array('fl' => '90'),
            '17E102923N' => array('fl' => '89'),
            '17L102924N' => array('fl' => '89'),
            '17K102926N' => array('fl' => '92'),
            '17L102927N' => array('fl' => '89'),
            '17G102928N' => array('fl' => '90'),
            '17G102929N' => array('fl' => '90'),
            '17J102930N' => array('fl' => '90'),
            '19C102934N' => array('fl' => '90'),
            '17M102939N' => array('fl' => '89'),
            '18E102941N' => array('fl' => '92'),
            '17E102944N' => array('fl' => '92'),
            '18G102946N' => array('fl' => '90'),
            '17J102948N' => array('fl' => '92'),
            '18G102949N' => array('fl' => '92'),
            '18G102950N' => array('fl' => '90'),
            '18F102952N' => array('fl' => '89'),
            '18J102953N' => array('fl' => '90'),
            '18F102954N' => array('fl' => '90'),
            '18J102956N' => array('fl' => '90'),
            '17D102957N' => array('fl' => '89'),
            '12K102960N' => array('fl' => '92'),
            '12E102961N' => array('fl' => '92'),
            '13A102962N' => array('fl' => '92'),
            '13A102963N' => array('fl' => '90'),
            '17K102964N' => array('fl' => '92'),
            '13F102965N' => array('fl' => '90'),
            '12G102966N' => array('fl' => '90'),
            '13A102968N' => array('fl' => '90'),
            '13A102970N' => array('fl' => '92'),
            '16G102971N' => array('fl' => '92'),
            '16C102975N' => array('fl' => '92'),
            '17E102977N' => array('fl' => '90'),
            '14M102978N' => array('fl' => '90'),
            '18G102982N' => array('fl' => '92'),
            '18J102985N' => array('fl' => '90'),
            '17M102986N' => array('fl' => '90'),
            '16A102988N' => array('fl' => '92'),
            '17K102989N' => array('fl' => '92'),
            '17K102990N' => array('fl' => '90'),
            '17K102993N' => array('fl' => '92'),
            '17G102994N' => array('fl' => '89'),
            '17K102995N' => array('fl' => '90'),
            '17D102996N' => array('fl' => '90'),
            '17E102997N' => array('fl' => '92'),
            '14M103001N' => array('fl' => '90'),
            '17J103003N' => array('fl' => '89'),
            '16D103006N' => array('fl' => '90'),
            '17L103007N' => array('fl' => '90'),
            '16D103010N' => array('fl' => '92'),
            '16C103012N' => array('fl' => '92'),
            '17L103014N' => array('fl' => '89'),
            '12L103015N' => array('fl' => '90'),
            '12H103016N' => array('fl' => '90'),
            '14K103019N' => array('fl' => '90'),
            '08H103024N' => array('fl' => '92'),
            '09M103025N' => array('fl' => '92'),
            '10E103026N' => array('fl' => '90'),
            '13H103028N' => array('fl' => '92'),
            '13H103030N' => array('fl' => '92'),
            '09A103031N' => array('fl' => '92'),
            '15D103032N' => array('fl' => '92'),
            '17E103035N' => array('fl' => '90'),
            '17E103036N' => array('fl' => '90'),
            '12H103038N' => array('fl' => '90'),
            '12H103039N' => array('fl' => '90'),
            '17G103043N' => array('fl' => '92'),
            '17J103045N' => array('fl' => '90'),
            '16K103046N' => array('fl' => '92'),
            '17M104017N' => array('fl' => '89'),
            '17J104368N' => array('fl' => '92'),
            '19D104386N' => array('fl' => '92'),
            '19F104410N' => array('fl' => '85'),
            '19E104416N' => array('fl' => '85'),
            '19E104417N' => array('fl' => '85'),
            '19E104418N' => array('fl' => '85'),
            '19G104425N' => array('fl' => '85'),
            '19G104429N' => array('fl' => '85'),
            '18E104453N' => array('fl' => '85'),
            '19H104461N' => array('fl' => '85'),
            '18G104468N' => array('fl' => '85'),
            '17J104470N' => array('fl' => '85'),
            '18F104472N' => array('fl' => '85'),
            '18E104477N' => array('fl' => '85'),
            '18J104479N' => array('fl' => '89'),
            '17J104481N' => array('fl' => '85'),
            '18G104484N' => array('fl' => '85'),
            '18F104486N' => array('fl' => '85'),
            '18G104500N' => array('fl' => '85'),
            '17K104514N' => array('fl' => '85'),
            '17K104516N' => array('fl' => '85'),
            '17L104522N' => array('fl' => '85'),
            '17K104531N' => array('fl' => '85'),
            '17M104540N' => array('fl' => '85'),
            '15C104543N' => array('fl' => '85'),
            '16C104584N' => array('fl' => '85'),
            '14B104656N' => array('fl' => '90'),
            '12L105278N' => array('fl' => '86'),
            '12M105281N' => array('fl' => '86'),
            '11L105291N' => array('fl' => '86'),
            '12M105293N' => array('fl' => '86'),
            '12M105295N' => array('fl' => '86'),
            '10J105301N' => array('fl' => '86'),
            '10J105306N' => array('fl' => '86'),
            '10F105308N' => array('fl' => '86'),
            '08H105311N' => array('fl' => '86'),
            '13A105323N' => array('fl' => '86'),
            '12G105326N' => array('fl' => '86'),
            '13F105327N' => array('fl' => '86'),
            '12H105328N' => array('fl' => '86'),
            '12L105330N' => array('fl' => '86'),
            '12J105331N' => array('fl' => '86'),
            '13E105333N' => array('fl' => '86'),
            '13A105337N' => array('fl' => '92'),
            '13K105345N' => array('fl' => '86'),
            '11L105350N' => array('fl' => '86'),
            '11L105351N' => array('fl' => '86'),
            '11L105353N' => array('fl' => '86'),
            '14L105358N' => array('fl' => '86'),
            '16A105369N' => array('fl' => '86'),
            '16A105371N' => array('fl' => '86'),
            '16A105372N' => array('fl' => '86'),
            '13L105377N' => array('fl' => '86'),
            '13K105380N' => array('fl' => '86'),
            '13E105391N' => array('fl' => '92'),
            '13F105397N' => array('fl' => '86'),
            '13B105398N' => array('fl' => '92'),
            '13G105406N' => array('fl' => '86'),
            '15A105412N' => array('fl' => '86'),
            '15B105416N' => array('fl' => '86'),
            '16D105421N' => array('fl' => '86'),
            '15D105422N' => array('fl' => '86'),
            '15D105423N' => array('fl' => '86'),
            '15A105424N' => array('fl' => '86'),
            '15A105425N' => array('fl' => '86'),
            '15A105432N' => array('fl' => '86'),
            '16J105435N' => array('fl' => '86'),
            '16J105440N' => array('fl' => '86'),
            '17K105442N' => array('fl' => '86'),
            '17K105448N' => array('fl' => '86'),
            '17J105452N' => array('fl' => '86'),
            '17G105461N' => array('fl' => '86'),
            '17L105466N' => array('fl' => '86'),
            '17L105468N' => array('fl' => '90'),
            '16C105469N' => array('fl' => '86'),
            '17D105471N' => array('fl' => '86'),
            '17L105473N' => array('fl' => '86'),
            '16B105485N' => array('fl' => '86'),
            '17L105486N' => array('fl' => '86'),
            '17L105492N' => array('fl' => '86'),
            '17L105494N' => array('fl' => '86'),
            '17D105498N' => array('fl' => '86'),
            '17K105503N' => array('fl' => '86'),
            '17A105506N' => array('fl' => '86'),
            '16J105511N' => array('fl' => '86'),
            '16K105516N' => array('fl' => '86'),
            '17J105521N' => array('fl' => '90'),
            '17E105524N' => array('fl' => '86'),
            '17M105526N' => array('fl' => '86'),
            '17M105529N' => array('fl' => '86'),
            '18E105537N' => array('fl' => '86'),
            '17M105541N' => array('fl' => '86'),
            '17M105552N' => array('fl' => '86'),
            '17M105554N' => array('fl' => '86'),
            '17M105560N' => array('fl' => '86'),
            '18A105562N' => array('fl' => '86'),
            '18A105565N' => array('fl' => '86'),
            '18F105572N' => array('fl' => '86'),
            '18E105574N' => array('fl' => '86'),
            '18J105575N' => array('fl' => '86'),
            '18G105585N' => array('fl' => '86'),
            '18F105587N' => array('fl' => '86'),
            '18F105589N' => array('fl' => '86'),
            '18A105591N' => array('fl' => '86'),
            '18E105597N' => array('fl' => '86'),
            '17J105609N' => array('fl' => '86'),
            '18G105610N' => array('fl' => '86'),
            '17L105614N' => array('fl' => '86'),
            '17J105622N' => array('fl' => '86'),
            '18E105627N' => array('fl' => '86'),
            '18G105635N' => array('fl' => '86'),
            '18G105639N' => array('fl' => '86'),
            '18G105646N' => array('fl' => '86'),
            '18G105647N' => array('fl' => '86'),
            '18J105650N' => array('fl' => '86'),
            '18G105654N' => array('fl' => '86'),
            '18G105656N' => array('fl' => '86'),
            '18G105663N' => array('fl' => '86'),
            '19K105664N' => array('fl' => '86'),
            '19G105671N' => array('fl' => '86'),
            '19G105673N' => array('fl' => '86'),
            '18F105679N' => array('fl' => '86'),
            '19D105684N' => array('fl' => '86'),
            '18E105686N' => array('fl' => '86'),
            '19B105691N' => array('fl' => '86'),
            '19F105692N' => array('fl' => '86'),
            '19F105694N' => array('fl' => '86'),
            '18E105699N' => array('fl' => '86'),
            '18F105705N' => array('fl' => '86'),
            '19H105709N' => array('fl' => '86'),
            '19K105715N' => array('fl' => '86'),
            '19G105717N' => array('fl' => '86'),
            '19G105718N' => array('fl' => '86'),
            '19G105722N' => array('fl' => '86'),
            '19F105732N' => array('fl' => '82'),
            '19H105745N' => array('fl' => '90'),
            '19H105752N' => array('fl' => '86'),
            '19H105764N' => array('fl' => '86'),
            '19H105788N' => array('fl' => '86'),
            '19H105811N' => array('fl' => '86'),
            '19G105814N' => array('fl' => '86'),
            '19G105819N' => array('fl' => '86'),
            '16H065153K' => array('fl' => '82'),
            '12L105840N' => array('fl' => '85'),
            '19K105975N' => array('fl' => '90'),
            '19K105976N' => array('fl' => '90'),
            '19K105983N' => array('fl' => '85'),
            '20K106855N' => array('fl' => '86'),
            '20K106856N' => array('fl' => '86'),
            '20K106858N' => array('fl' => '86'),
            '20K106859N' => array('fl' => '92'),
            '20K106860N' => array('fl' => '86'),
            '20K106863N' => array('fl' => '86'),
            '20K106864N' => array('fl' => '86'),
            '20K106865N' => array('fl' => '86'),
            '20K106867N' => array('fl' => '86'),
            '20K106868N' => array('fl' => '86'),
            '20K106871N' => array('fl' => '86'),
            '20K106872N' => array('fl' => '86'),
            '20K106876N' => array('fl' => '86'),
            '20K106877N' => array('fl' => '86'),
            '20K106879N' => array('fl' => '86'),
            '20K106887N' => array('fl' => '86'),
            '20K106888N' => array('fl' => '86'),
            '20K106891N' => array('fl' => '89'),
            '20K106892N' => array('fl' => '89'),
            '20K106894N' => array('fl' => '90'),
            '20K106895N' => array('fl' => '92'),
            '20K106898N' => array('fl' => '90'),
            '20K106900N' => array('fl' => '92'),
            '20K106901N' => array('fl' => '92'),
            '20K106902N' => array('fl' => '92'),
            '20K106903N' => array('fl' => '92'),
            '20K106904N' => array('fl' => '90'),
            '20K106907N' => array('fl' => '90'),
            '20K106910N' => array('fl' => '90'),
            '17L090029M' => array('fl' => '82'),
            '20L000477A' => array('fl' => '92'),
            '20L108162N' => array('fl' => '85'),
            '20M000488A' => array('fl' => '82'),
            '20M000489A' => array('fl' => '82'),
            '19E000508A' => array('fl' => '89'),
            '21A090030M' => array('fl' => '82'),
            '21A090031M' => array('fl' => '82'),
            '19M107378N' => array('fl' => '82'),
            '19M107380N' => array('fl' => '92'),
            '19M107382N' => array('fl' => '82'),
            '19M107385N' => array('fl' => '82'),
            '19M107388N' => array('fl' => '86'),
            '19M107389N' => array('fl' => '86'),
            '19M107390N' => array('fl' => '86'),
            '19M107391N' => array('fl' => '92'),
            '19M107392N' => array('fl' => '86'),
            '19M107393N' => array('fl' => '92'),
            '19M107396N' => array('fl' => '86'),
            '19M107401N' => array('fl' => '86'),
            '19M107408N' => array('fl' => '92'),
            '19M107409N' => array('fl' => '86'),
            '19M107411N' => array('fl' => '86'),
            '19M107413N' => array('fl' => '86'),
            '19M107415N' => array('fl' => '86'),
            '19M107416N' => array('fl' => '86'),
            '19M107417N' => array('fl' => '86'),
            '19M107418N' => array('fl' => '86'),
            '19M107420N' => array('fl' => '86'),
            '19M107421N' => array('fl' => '86'),
            '19M107422N' => array('fl' => '86'),
            '19M107423N' => array('fl' => '86'),
            '19M107429N' => array('fl' => '82'),
            '19M107430N' => array('fl' => '86'),
            '19M107432N' => array('fl' => '86'),
            '19M107435N' => array('fl' => '86'),
            '19M107436N' => array('fl' => '92'),
            '19M107440N' => array('fl' => '86'),
            '19M107441N' => array('fl' => '86'),
            '19M107442N' => array('fl' => '90'),
            '19M107443N' => array('fl' => '86'),
            '19M107449N' => array('fl' => '92'),
            '19M107450N' => array('fl' => '92'),
            '19M107451N' => array('fl' => '90'),
            '19M107452N' => array('fl' => '86'),
            '19M107453N' => array('fl' => '86'),
            '19M107456N' => array('fl' => '86'),
            '19M107457N' => array('fl' => '86'),
            '19M107463N' => array('fl' => '90'),
            '19M107466N' => array('fl' => '90'),
            '19M107470N' => array('fl' => '90'),
            '19L107584N' => array('fl' => '90'),
            '19L107587N' => array('fl' => '90'),
            '17L107637N' => array('fl' => '92'),
            '12M107638N' => array('fl' => '86'),
            '19M107641N' => array('fl' => '85'),
            '19M107643N' => array('fl' => '85'),
            '19M107644N' => array('fl' => '85'),
            '19L107646N' => array('fl' => '85'),
            '19L107648N' => array('fl' => '85'),
            '19L107649N' => array('fl' => '85'),
            '19M107652N' => array('fl' => '85'),
            '19M107655N' => array('fl' => '85'),
            '19M107656N' => array('fl' => '85'),
            '19L107660N' => array('fl' => '85'),
            '19L107661N' => array('fl' => '85'),
            '19L107662N' => array('fl' => '85'),
            '19L107663N' => array('fl' => '85'),
            '19M107667N' => array('fl' => '85'),
            '20L107708N' => array('fl' => '92'),
            '20L107710N' => array('fl' => '90'),
            '20L107711N' => array('fl' => '89'),
            '20L107712N' => array('fl' => '90'),
            '20L107713N' => array('fl' => '86'),
            '20L107714N' => array('fl' => '90'),
            '20L107715N' => array('fl' => '90'),
            '20L107717N' => array('fl' => '90'),
            '20L107718N' => array('fl' => '92'),
            '20L107719N' => array('fl' => '89'),
            '20L107720N' => array('fl' => '90'),
            '20L107816N' => array('fl' => '86'),
            '20L107817N' => array('fl' => '86'),
            '20L107819N' => array('fl' => '86'),
            '20L107820N' => array('fl' => '86'),
            '20L107822N' => array('fl' => '86'),
            '20L107823N' => array('fl' => '86'),
            '20L107825N' => array('fl' => '92'),
            '20L107829N' => array('fl' => '86'),
            '20L107830N' => array('fl' => '86'),
            '20L107832N' => array('fl' => '86'),
            '20L107834N' => array('fl' => '86'),
            '20L107835N' => array('fl' => '86'),
            '20L107836N' => array('fl' => '86'),
            '20L107860N' => array('fl' => '86'),
            '20L107862N' => array('fl' => '90'),
            '20L107863N' => array('fl' => '92'),
            '20L107864N' => array('fl' => '90'),
            '20L107991N' => array('fl' => '86'),
            '20L107992N' => array('fl' => '86'),
            '20L107995N' => array('fl' => '86'),
            '20L107997N' => array('fl' => '86'),
            '20L108001N' => array('fl' => '85'),
            '20L108002N' => array('fl' => '86'),
            '20L108065N' => array('fl' => '85'),
            '20L108157N' => array('fl' => '85'),
            '20L108204N' => array('fl' => '90'),
            '20L108205N' => array('fl' => '89'),
            '20L108218N' => array('fl' => '86'),
            '20L108262N' => array('fl' => '86'),
            '20M108266N' => array('fl' => '90'),
            '20L108267N' => array('fl' => '90'),
            '20M108433N' => array('fl' => '85'),
            '20M108437N' => array('fl' => '86'),
            '20M108443N' => array('fl' => '85'),
            '20M108444N' => array('fl' => '90'),
            '20M108445N' => array('fl' => '86'),
            '20M108454N' => array('fl' => '86'),
            '20B108531N' => array('fl' => '92'),
            '20B108535N' => array('fl' => '92'),
            '20B108538N' => array('fl' => '85'),
            '20B108539N' => array('fl' => '85'),
            '20B108541N' => array('fl' => '85'),
            '20M108606N' => array('fl' => '90'),
            '20M108638N' => array('fl' => '90'),
            '20M108639N' => array('fl' => '92'),
            '20M108675N' => array('fl' => '89'),
            '20M108677N' => array('fl' => '86'),
            '20M108708N' => array('fl' => '86'),
            '20M108715N' => array('fl' => '86'),
            '20M108716N' => array('fl' => '86'),
            '20M108717N' => array('fl' => '86'),
            '20M108718N' => array('fl' => '86'),
            '20M108719N' => array('fl' => '86'),
            '20M108774N' => array('fl' => '86'),
            '20M108812N' => array('fl' => '86'),
            '20M108813N' => array('fl' => '86'),
            '21A108907N' => array('fl' => '86'),
            '21A108908N' => array('fl' => '86'),
            '21A108923N' => array('fl' => '86'),
            '21A108927N' => array('fl' => '86'),
            '21A108929N' => array('fl' => '86'),
            '21A108932N' => array('fl' => '86'),
            '21A108935N' => array('fl' => '90'),
            '21A108937N' => array('fl' => '92'),
            '21A108939N' => array('fl' => '86'),
            '21A108948N' => array('fl' => '86'),
            '21A108951N' => array('fl' => '92'),
            '21A108952N' => array('fl' => '86'),
            '21A108953N' => array('fl' => '90'),
            '21A108961N' => array('fl' => '82'),
            '21A108967N' => array('fl' => '86'),
            '21A108982N' => array('fl' => '90'),
            '21A108993N' => array('fl' => '86'),
            '21A109007N' => array('fl' => '89'),
            '21A109015N' => array('fl' => '89'),
            '21A109030N' => array('fl' => '90'),
            '21A109038N' => array('fl' => '90'),
            '21A109070N' => array('fl' => '92'),
            '02A109072N' => array('fl' => '92'),
            '21A109076N' => array('fl' => '86'),
            '21A109082N' => array('fl' => '86'),
            '21A109084N' => array('fl' => '86'),
            '21A109091N' => array('fl' => '86'),
            '21C109130N' => array('fl' => '86'),
            '21A109142N' => array('fl' => '85'),
            '21A109155N' => array('fl' => '86'),
            '21A109160N' => array('fl' => '86'),
            '21D109162N' => array('fl' => '86'),
            '21A109163N' => array('fl' => '86'),
            '21A109164N' => array('fl' => '86'),
            '21A109165N' => array('fl' => '86'),
            '21A109166N' => array('fl' => '86'),
            '21A109171N' => array('fl' => '86'),
            '21A109172N' => array('fl' => '90'),
            '21A109183N' => array('fl' => '86'),
            '21A109189N' => array('fl' => '86'),
            '85K109226N' => array('fl' => '86'),
            '21A109243N' => array('fl' => '86'),
            '21A109245N' => array('fl' => '86'),
            '21A109246N' => array('fl' => '86'),
            '21A109265N' => array('fl' => '86'),
            '21A109267N' => array('fl' => '86'),
            '21A109268N' => array('fl' => '86'),
            '21A109272N' => array('fl' => '86'),
            '21M109275N' => array('fl' => '86'),
            '21A109280N' => array('fl' => '85'),
            '21A109281N' => array('fl' => '85'),
            '21A109283N' => array('fl' => '85'),
            '21A109304N' => array('fl' => '90'),
            '21A109323N' => array('fl' => '85'),
            '21A109325N' => array('fl' => '90'),
            '21A109330N' => array('fl' => '90'),
            '21A109337N' => array('fl' => '90'),
            '21B109399N' => array('fl' => '90'),
            '21B109400N' => array('fl' => '86'),
            '21B109401N' => array('fl' => '92'),
            '21B109403N' => array('fl' => '86'),
            '21B109404N' => array('fl' => '86'),
            '21B109408N' => array('fl' => '89'),
            '21B109411N' => array('fl' => '92'),
            '21B109434N' => array('fl' => '85'),
            '21B109457N' => array('fl' => '85'),
            '21B109458N' => array('fl' => '85'),
            '21B109541N' => array('fl' => '85'),
            '21B109542N' => array('fl' => '85'),
            '21B109543N' => array('fl' => '85'),
            '21B110235N' => array('fl' => '92'),
            '97D110238N' => array('fl' => '89'),
            '21B110239N' => array('fl' => '90'),
            '21B110242N' => array('fl' => '92'),
            '21B110243N' => array('fl' => '89'),
            '21B110245N' => array('fl' => '89'),
            '21B110255N' => array('fl' => '90'),
            '21B110269N' => array('fl' => '85'),
            '21B110270N' => array('fl' => '90'),
            '21B110271N' => array('fl' => '86'),
            '21B110274N' => array('fl' => '86'),
            '21B110280N' => array('fl' => '92'),
            '21B110297N' => array('fl' => '90'),
            '21B110301N' => array('fl' => '90'),
            '21B110302N' => array('fl' => '90'),
            '21B110308N' => array('fl' => '90'),
            '21B110312N' => array('fl' => '90'),
            '21B110313N' => array('fl' => '90'),
            '21B110315N' => array('fl' => '89'),
            '21B110322N' => array('fl' => '90'),
            '21B110328N' => array('fl' => '90'),
            '21B110335N' => array('fl' => '92'),
            '21B110342N' => array('fl' => '82'),
            '21B110343N' => array('fl' => '86'),
            '21B110355N' => array('fl' => '92'),
            '21B110356N' => array('fl' => '86'),
            '21B110359N' => array('fl' => '86'),
            '21B110361N' => array('fl' => '86'),
            '21B110376N' => array('fl' => '86'),
            '21B110378N' => array('fl' => '90'),
            '21B110382N' => array('fl' => '86'),
            '21B110384N' => array('fl' => '86'),
            '21B110385N' => array('fl' => '90'),
            '21B110386N' => array('fl' => '86'),
            '21B110388N' => array('fl' => '90'),
            '21B110390N' => array('fl' => '89'),
            '21B110391N' => array('fl' => '92'),
            '21B110393N' => array('fl' => '92'),
            '21B110400N' => array('fl' => '86'),
            '21C110558N' => array('fl' => '92'),
            '21C110572N' => array('fl' => '89'),
            '21C110579N' => array('fl' => '86'),
            '21C110603N' => array('fl' => '86'),
            '21A110604N' => array('fl' => '86'),
            '21C110647N' => array('fl' => '86'),
            '21C110650N' => array('fl' => '86'),
            '21C110651N' => array('fl' => '92'),
            '21C110652N' => array('fl' => '92'),
            '21C110653N' => array('fl' => '92'),
            '21C110662N' => array('fl' => '86'),
            '21C110663N' => array('fl' => '89'),
            '21C110664N' => array('fl' => '89'),
            '21C110666N' => array('fl' => '92'),
            '21C110695N' => array('fl' => '89'),
            '21C110709N' => array('fl' => '92'),
            '21C110710N' => array('fl' => '92'),
            '21C110711N' => array('fl' => '92'),
            '21C110712N' => array('fl' => '90'),
            '21C110713N' => array('fl' => '89'),
            '21C110714N' => array('fl' => '92'),
            '21C110717N' => array('fl' => '92'),
            '21C110722N' => array('fl' => '89'),
            '21C110726N' => array('fl' => '90'),
            '21C110765N' => array('fl' => '89'),
            '21C110768N' => array('fl' => '92'),
            '21C110769N' => array('fl' => '90'),
            '21C110770N' => array('fl' => '89'),
            '21C110775N' => array('fl' => '89'),
            '21C110776N' => array('fl' => '89'),
            '21C000560A' => array('fl' => '82'),
            '21C110778N' => array('fl' => '92'),
            '21C110781N' => array('fl' => '92'),
            '21C110782N' => array('fl' => '86'),
            '21C110783N' => array('fl' => '89'),
            '21C110786N' => array('fl' => '89'),
            '21C110788N' => array('fl' => '89'),
            '21C110789N' => array('fl' => '86'),
            '21C110791N' => array('fl' => '89'),
            '21C110793N' => array('fl' => '92'),
            '21C110794N' => array('fl' => '89'),
            '21C110796N' => array('fl' => '90'),
            '21C110806N' => array('fl' => '92'),
            '21C110808N' => array('fl' => '92'),
            '21C110809N' => array('fl' => '86'),
            '21C110811N' => array('fl' => '86'),
            '21C110815N' => array('fl' => '89'),
            '21C110820N' => array('fl' => '86'),
            '21C110822N' => array('fl' => '86'),
            '21C110836N' => array('fl' => '86'),
            '21C110853N' => array('fl' => '89'),
            '21C110905N' => array('fl' => '86'),
            '21C110909N' => array('fl' => '86'),
            '92G110910N' => array('fl' => '86'),
            '21C110911N' => array('fl' => '86'),
            '21C110913N' => array('fl' => '86'),
            '21C110919N' => array('fl' => '89'),
            '21C045048I' => array('fl' => '86'),
            '21C110926N' => array('fl' => '89'),
            '21C110927N' => array('fl' => '89'),
            '21C110929N' => array('fl' => '89'),
            '21C110959N' => array('fl' => '85'),
            '21C110961N' => array('fl' => '85'),
            '21C110962N' => array('fl' => '85'),
            '21C110975N' => array('fl' => '90'),
            '21C110976N' => array('fl' => '89'),
            '21C111102N' => array('fl' => '90'),
            '21C111104N' => array('fl' => '92'),
            '21C111108N' => array('fl' => '90'),
            '21C111110N' => array('fl' => '92'),
            '21C111112N' => array('fl' => '89'),
            '21C111113N' => array('fl' => '92'),
            '21C111116N' => array('fl' => '90'),
            '21C111117N' => array('fl' => '89'),
            '21C111118N' => array('fl' => '90'),
            '21C045049I' => array('fl' => '90'),
            '21C111163N' => array('fl' => '90'),
            '15A105356N' => array('fl' => '86'),
            '13D105439N' => array('fl' => '86'),
            '18C105544N' => array('fl' => '86'),
            '18G105584N' => array('fl' => '86'),
            '18G105604N' => array('fl' => '86'),
            '18M105636N' => array('fl' => '86'),
            '19H105665N' => array('fl' => '86'),
            '19F105822N' => array('fl' => '86'),
            '19M107427N' => array('fl' => '86'),
            '20M108825N' => array('fl' => '86'),
            '21A108974N' => array('fl' => '86'),
            '21A108975N' => array('fl' => '86'),
            '21A109093N' => array('fl' => '86'),
            '21A109168N' => array('fl' => '86'),
            '21A109170N' => array('fl' => '86'),
            '21A109196N' => array('fl' => '86'),
            '15E065100K' => array('fl' => '82'),
            '13J065102K' => array('fl' => '82'),
            '17J065114K' => array('fl' => '82'),
            '11L102705N' => array('fl' => '82'),
            '16H065124K' => array('fl' => '82'),
            '13L065129K' => array('fl' => '82'),
            '09E102706N' => array('fl' => '82'),
            '13D104556N' => array('fl' => '85'),
            '17A105241N' => array('fl' => '90'),
            '12H105247N' => array('fl' => '92'),
            '19J105252N' => array('fl' => '89'),
            '15A105253N' => array('fl' => '90'),
            '18F105255N' => array('fl' => '82'),
            '14A105256N' => array('fl' => '92'),
            '18G105257N' => array('fl' => '92'),
            '14A105258N' => array('fl' => '92'),
            '18J105262N' => array('fl' => '89'),
            '18J105264N' => array('fl' => '92'),
            '17E105265N' => array('fl' => '90'),
            '18A105268N' => array('fl' => '89'),
            '11M105348N' => array('fl' => '92'),
            '20L065179K' => array('fl' => '82'),
            '20L065180K' => array('fl' => '82'),
            '14L102584N' => array('fl' => '90'),
            '14J102588N' => array('fl' => '86'),
            '13D102590N' => array('fl' => '92'),
            '13L102594N' => array('fl' => '92'),
            '13H102596N' => array('fl' => '86'),
            '13B102597N' => array('fl' => '85'),
            '13E102599N' => array('fl' => '86'),
            '12E102601N' => array('fl' => '90'),
            '11J102606N' => array('fl' => '92'),
            '13H102609N' => array('fl' => '86'),
            '11B102610N' => array('fl' => '85'),
            '11A102613N' => array('fl' => '85'),
            '10J102614N' => array('fl' => '86'),
            '08H102615N' => array('fl' => '92'),
            '10A102616N' => array('fl' => '86'),
            '08K102622N' => array('fl' => '92'),
            '08K102623N' => array('fl' => '86'),
            '18K102636N' => array('fl' => '89'),
            '17M102658N' => array('fl' => '90'),
            '17K102659N' => array('fl' => '89'),
            '17K102660N' => array('fl' => '90'),
            '17K102662N' => array('fl' => '92'),
            '16G102668N' => array('fl' => '90'),
            '17L102671N' => array('fl' => '89'),
            '15K102672N' => array('fl' => '86'),
            '19F102688N' => array('fl' => '90'),
            '19G102692N' => array('fl' => '86'),
            '19G102697N' => array('fl' => '86'),
            '18H075051L' => array('fl' => '82'),
            '17K075053L' => array('fl' => '82'),
            '08H075054L' => array('fl' => '82'),
            '18A075055L' => array('fl' => '82'),
            '18A075056L' => array('fl' => '82'),
            '09B075058L' => array('fl' => '82'),
            '19K075059L' => array('fl' => '82'),
            '14C075060L' => array('fl' => '82'),
            '13L075061L' => array('fl' => '82'),
            '11L055008J' => array('fl' => '89'),
            '14L055011J' => array('fl' => '90'),
            '15E055014J' => array('fl' => '90'),
            '11C075066L' => array('fl' => '82'),
            '15C055015J' => array('fl' => '92'),
            '09D055021J' => array('fl' => '92'),
            '15E065106K' => array('fl' => '82'),
            '13L065108K' => array('fl' => '82'),
            '09C065110K' => array('fl' => '82'),
            '15A065111K' => array('fl' => '82'),
            '15K065112K' => array('fl' => '82'),
            '14J065113K' => array('fl' => '82'),
            '14L065116K' => array('fl' => '82'),
            '05L065130K' => array('fl' => '82'),
            '18E065135K' => array('fl' => '82'),
            '18D065139K' => array('fl' => '82'),
            '11B000287A' => array('fl' => '82'),
            '19J500493O' => array('fl' => '90'),
            '17L500507O' => array('fl' => '89'),
            '17M500509O' => array('fl' => '90'),
            '17L500516O' => array('fl' => '86'),
            '13G500517O' => array('fl' => '90'),
            '14K500524O' => array('fl' => '86'),
            '14K500533O' => array('fl' => '86'),
            '11B500534O' => array('fl' => '90'),
            '11F500537O' => array('fl' => '86'),
            '11M500538O' => array('fl' => '85'),
            '09M500541O' => array('fl' => '92'),
            '19J500545O' => array('fl' => '85'),
            '09C500546O' => array('fl' => '92'),
            '19J500548O' => array('fl' => '86'),
            '19J500549O' => array('fl' => '90'),
            '19J500551O' => array('fl' => '90'),
            '19D103149N' => array('fl' => '86'),
            '17H000301A' => array('fl' => '82'),
            '19F104284N' => array('fl' => '85'),
            '19J104411N' => array('fl' => '85'),
            '13J104557N' => array('fl' => '85'),
            '07F104645N' => array('fl' => '85'),
            '14L075072L' => array('fl' => '85'),
            '11J075073L' => array('fl' => '90'),
            '14H075076L' => array('fl' => '85'),
            '17L075088L' => array('fl' => '92'),
            '19A000363A' => array('fl' => '82'),
            '19A000366A' => array('fl' => '82'),
            '16G000375A' => array('fl' => '82'),
            '17D000380A' => array('fl' => '82'),
            '14D090016M' => array('fl' => '82'),
            '11B090018M' => array('fl' => '92'),
            '08L090019M' => array('fl' => '90'),
            '17C090024M' => array('fl' => '82'),
            '15B090025M' => array('fl' => '85'),
            '19K105963N' => array('fl' => '89'),
            '18K105965N' => array('fl' => '92'),
            '19K105966N' => array('fl' => '85'),
            '18A075065L' => array('fl' => '82'),
            '11A500540O' => array('fl' => '82'),
            '20K065167K' => array('fl' => '82'),
            '20K107274N' => array('fl' => '86'),
            '20K107279N' => array('fl' => '92'),
            '20K500934O' => array('fl' => '90'),
            '20K500936O' => array('fl' => '89'),
            '20K500937O' => array('fl' => '89'),
            '20K500938O' => array('fl' => '92'),
            '19M107467N' => array('fl' => '86'),
            '19K107624N' => array('fl' => '90'),
            '20L501024O' => array('fl' => '90'),
            '14M108069N' => array('fl' => '90'),
            '16H000358A' => array('fl' => '82'),
            '12E000385A' => array('fl' => '82'),
            '17L000373A' => array('fl' => '82'),
            '18B000374A' => array('fl' => '82'),
            '16A105366N' => array('fl' => '86'),
            '17G105464N' => array('fl' => '82'),
            '17M105578N' => array('fl' => '86'),
            '18B105579N' => array('fl' => '86'),
            '18G105601N' => array('fl' => '82'),
            '18M105633N' => array('fl' => '86'),
            '18G105637N' => array('fl' => '86'),
            '19B105697N' => array('fl' => '86'),
            '18J105703N' => array('fl' => '86'),
            '19J105744N' => array('fl' => '86'),
            '19H105812N' => array('fl' => '86'),
            '19M107369N' => array('fl' => '86'),
            '19L107578N' => array('fl' => '86'),
            '20L107838N' => array('fl' => '86'),
            '21A109095N' => array('fl' => '86'),
            '21A109184N' => array('fl' => '86'),
            '21A109209N' => array('fl' => '86'),
            '21B110286N' => array('fl' => '82'),
            '21B110289N' => array('fl' => '82'),
            '21B110332N' => array('fl' => '86'),
            '18G000371A' => array('fl' => '82'),
            '16E000379A' => array('fl' => '82'),
            '20L000484A' => array('fl' => '82'),
            '15D102585N' => array('fl' => '92'),
            '15D102586N' => array('fl' => '86'),
            '14L102604N' => array('fl' => '92'),
            '14K102605N' => array('fl' => '92'),
            '08G102617N' => array('fl' => '92'),
            '13D102619N' => array('fl' => '92'),
            '97H102624N' => array('fl' => '86'),
            '19B102630N' => array('fl' => '86'),
            '19A102633N' => array('fl' => '86'),
            '18G102652N' => array('fl' => '86'),
            '15K102661N' => array('fl' => '86'),
            '15H102669N' => array('fl' => '86'),
            '19G102685N' => array('fl' => '82'),
            '19J102686N' => array('fl' => '86'),
            '19G102691N' => array('fl' => '89'),
            '19H055012J' => array('fl' => '89'),
            '19H055013J' => array('fl' => '89'),
            '19J055017J' => array('fl' => '82'),
            '14B055020J' => array('fl' => '92'),
            '10L065103K' => array('fl' => '82'),
            '12A065104K' => array('fl' => '82'),
            '13H065115K' => array('fl' => '82'),
            '08H000244A' => array('fl' => '82'),
            '17K103049N' => array('fl' => '85'),
            '17E103062N' => array('fl' => '85'),
            '17D103063N' => array('fl' => '85'),
            '19F103087N' => array('fl' => '85'),
            '19G103090N' => array('fl' => '85'),
            '19F103093N' => array('fl' => '85'),
            '19C103108N' => array('fl' => '85'),
            '14H103117N' => array('fl' => '85'),
            '08G000288A' => array('fl' => '82'),
            '17M500498O' => array('fl' => '90'),
            '15B500506O' => array('fl' => '86'),
            '13E500529O' => array('fl' => '85'),
            '12F500532O' => array('fl' => '92'),
            '09C500539O' => array('fl' => '86'),
            '13E500566O' => array('fl' => '82'),
            '09A500570O' => array('fl' => '82'),
            '10J020028C' => array('fl' => '82'),
            '18D020029C' => array('fl' => '82'),
            '13L500579O' => array('fl' => '82'),
            '09A500580O' => array('fl' => '82'),
            '11D055029J' => array('fl' => '82'),
            '18L104495N' => array('fl' => '85'),
            '15H104576N' => array('fl' => '85'),
            '17K010035B' => array('fl' => '82'),
            '17K010040B' => array('fl' => '82'),
            '13H010047B' => array('fl' => '82'),
            '15L075067L' => array('fl' => '85'),
            '15H075068L' => array('fl' => '89'),
            '14L075069L' => array('fl' => '92'),
            '15L075070L' => array('fl' => '90'),
            '12C075071L' => array('fl' => '82'),
            '13A075074L' => array('fl' => '82'),
            '13J075075L' => array('fl' => '92'),
            '07C075079L' => array('fl' => '82'),
            '16A075080L' => array('fl' => '90'),
            '16G075083L' => array('fl' => '82'),
            '16H075084L' => array('fl' => '85'),
            '18A075089L' => array('fl' => '89'),
            '18A075091L' => array('fl' => '89'),
            '18J075095L' => array('fl' => '89'),
            '18K075096L' => array('fl' => '86'),
            '19B075099L' => array('fl' => '86'),
            '10C075100L' => array('fl' => '92'),
            '18C025008D' => array('fl' => '82'),
            '19H025009D' => array('fl' => '82'),
            '17G045015I' => array('fl' => '90'),
            '15C045021I' => array('fl' => '89'),
            '09J500774O' => array('fl' => '82'),
            '13J500782O' => array('fl' => '82'),
            '17L105830N' => array('fl' => '82'),
            '93J090020M' => array('fl' => '82'),
            '08H090023M' => array('fl' => '86'),
            '15B102587N' => array('fl' => '92'),
            '16A102674N' => array('fl' => '89'),
            '20K500929O' => array('fl' => '86'),
            '20K500930O' => array('fl' => '85'),
            '20K500931O' => array('fl' => '86'),
            '20K500940O' => array('fl' => '92'),
            '20B107359N' => array('fl' => '86'),
            '19M075113L' => array('fl' => '90'),
            '19K500968O' => array('fl' => '89'),
            '19L107630N' => array('fl' => '92'),
            '20J107650N' => array('fl' => '85'),
            '20G055045J' => array('fl' => '82'),
            '20J500971O' => array('fl' => '82'),
            '20K500982O' => array('fl' => '90'),
            '20L108102N' => array('fl' => '90'),
            '20L108107N' => array('fl' => '90'),
            '20L075118L' => array('fl' => '90'),
            '20L075120L' => array('fl' => '90'),
            '20L108119N' => array('fl' => '85'),
            '20L108120N' => array('fl' => '85'),
            '20L108153N' => array('fl' => '82'),
            '20M075122L' => array('fl' => '90'),
            '20M501106O' => array('fl' => '85'),
            '20M501107O' => array('fl' => '92'),
            '21A108989N' => array('fl' => '89'),
            '21A109061N' => array('fl' => '92'),
            '21A501135O' => array('fl' => '86'),
            '21A109216N' => array('fl' => '92'),
            '21A109221N' => array('fl' => '86'),
            '21A075127L' => array('fl' => '90'),
            '95M075128L' => array('fl' => '90'),
            '21A109338N' => array('fl' => '92'),
            '21A109341N' => array('fl' => '90'),
            '21A075132L' => array('fl' => '90'),
            '21A055054J' => array('fl' => '89'),
            '21B501277O' => array('fl' => '92'),
            '21B110419N' => array('fl' => '89'),
            '21B501278O' => array('fl' => '89'),
            '21B110423N' => array('fl' => '90'),
            '21B045046I' => array('fl' => '90'),
            '21B110425N' => array('fl' => '90'),
            '21B075139L' => array('fl' => '92'),
            '21B110427N' => array('fl' => '90'),
            '21B075140L' => array('fl' => '90'),
            '21B075141L' => array('fl' => '92'),
            '21B075142L' => array('fl' => '92'),
            '21C110861N' => array('fl' => '89'),
            '21C110867N' => array('fl' => '86'),
            '21C075145L' => array('fl' => '92'),
            '21C045047I' => array('fl' => '85'),
            '21C501353O' => array('fl' => '86'),
            '21C075146L' => array('fl' => '90'),
            '21C111101N' => array('fl' => '92'),
            '21C111105N' => array('fl' => '90'),
            '21C111106N' => array('fl' => '92'),
            '21C111115N' => array('fl' => '86'),
            '21C111126N' => array('fl' => '89'),
            '21C111127N' => array('fl' => '90'),
            '21C111128N' => array('fl' => '90'),
            '21C111129N' => array('fl' => '90'),
            '21C111139N' => array('fl' => '90'),
            '21C111141N' => array('fl' => '90'),
            '21C111142N' => array('fl' => '89'),
            '21C111154N' => array('fl' => '90'),
            '21C111155N' => array('fl' => '89'),
            '21C111157N' => array('fl' => '90'),
            '21C111158N' => array('fl' => '90'),
            '21C055059J' => array('fl' => '90'),
            '21C075149L' => array('fl' => '89'),
            '21C075150L' => array('fl' => '90'),
            '21C111175N' => array('fl' => '92'),
            '18C055036J' => array('fl' => '82'),
            '12E055038J' => array('fl' => '82'),
            '18B055042J' => array('fl' => '82'),
            '11E102707N' => array('fl' => '92'),
            '11E102708N' => array('fl' => '90'),
            '08K102710N' => array('fl' => '90'),
            '12J102711N' => array('fl' => '90'),
            '13B102714N' => array('fl' => '90'),
            '12L102715N' => array('fl' => '92'),
            '15D102716N' => array('fl' => '90'),
            '14L102717N' => array('fl' => '90'),
            '13E102718N' => array('fl' => '92'),
            '17K102721N' => array('fl' => '92'),
            '15L102722N' => array('fl' => '92'),
            '15M102723N' => array('fl' => '92'),
            '17B102724N' => array('fl' => '90'),
            '16L102725N' => array('fl' => '92'),
            '18F102729N' => array('fl' => '92'),
            '16F102731N' => array('fl' => '90'),
            '18D102732N' => array('fl' => '90'),
            '18G102733N' => array('fl' => '89'),
            '18D102734N' => array('fl' => '90'),
            '17M102737N' => array('fl' => '90'),
            '12H102739N' => array('fl' => '90'),
            '18J102743N' => array('fl' => '90'),
            '18F102744N' => array('fl' => '89'),
            '18J102746N' => array('fl' => '92'),
            '18G102748N' => array('fl' => '92'),
            '17J102749N' => array('fl' => '90'),
            '19F102754N' => array('fl' => '89'),
            '19K102762N' => array('fl' => '92'),
            '19K102765N' => array('fl' => '92'),
            '19J102766N' => array('fl' => '89'),
            '19J102768N' => array('fl' => '90'),
            '19G102772N' => array('fl' => '92'),
            '19E102779N' => array('fl' => '92'),
            '18K102780N' => array('fl' => '92'),
            '19J102784N' => array('fl' => '92'),
            '19H102790N' => array('fl' => '90'),
            '19F102791N' => array('fl' => '90'),
            '19K102792N' => array('fl' => '92'),
            '19K102796N' => array('fl' => '89'),
            '19K102797N' => array('fl' => '89'),
            '19B102798N' => array('fl' => '92'),
            '19J102800N' => array('fl' => '90'),
            '19F102802N' => array('fl' => '92'),
            '19J102884N' => array('fl' => '92'),
            '14H102967N' => array('fl' => '92'),
            '19K105907N' => array('fl' => '90'),
            '20K106899N' => array('fl' => '89'),
            '20K106914N' => array('fl' => '92'),
            '20K106915N' => array('fl' => '92'),
            '20K106916N' => array('fl' => '92'),
            '20K106918N' => array('fl' => '92'),
            '20K106924N' => array('fl' => '90'),
            '20K106925N' => array('fl' => '90'),
            '20K106927N' => array('fl' => '89'),
            '20K106928N' => array('fl' => '92'),
            '20K106932N' => array('fl' => '92'),
            '20K106933N' => array('fl' => '90'),
            '20K106934N' => array('fl' => '92'),
            '20K106935N' => array('fl' => '92'),
            '20B055046J' => array('fl' => '82'),
            '20F055047J' => array('fl' => '82'),
            '20M055052J' => array('fl' => '82'),
            '21A055053J' => array('fl' => '82'),
            '21C055056J' => array('fl' => '85'),
            '20K106996N' => array('fl' => '92'),
            '19M107447N' => array('fl' => '92'),
            '19M107458N' => array('fl' => '90'),
            '19M107460N' => array('fl' => '89'),
            '19M107461N' => array('fl' => '89'),
            '19M107462N' => array('fl' => '90'),
            '19L107580N' => array('fl' => '89'),
            '19L107586N' => array('fl' => '89'),
            '20K107616N' => array('fl' => '92'),
            '19K107634N' => array('fl' => '89'),
            '19K107635N' => array('fl' => '89'),
            '20L107854N' => array('fl' => '90'),
            '20L107857N' => array('fl' => '92'),
            '20L107858N' => array('fl' => '92'),
            '20L108067N' => array('fl' => '89'),
            '20L108150N' => array('fl' => '90'),
            '20L108151N' => array('fl' => '90'),
            '20L108152N' => array('fl' => '90'),
            '20L108217N' => array('fl' => '90'),
            '20L108226N' => array('fl' => '89'),
            '20L108300N' => array('fl' => '90'),
            '20M108411N' => array('fl' => '92'),
            '20M108412N' => array('fl' => '92'),
            '20M108413N' => array('fl' => '92'),
            '20M108414N' => array('fl' => '92'),
            '20M108429N' => array('fl' => '92'),
            '20M108455N' => array('fl' => '90'),
            '20M108711N' => array('fl' => '92'),
            '20M108712N' => array('fl' => '92'),
            '21A109033N' => array('fl' => '89'),
            '21A109159N' => array('fl' => '89'),
            '21B110251N' => array('fl' => '90'),
            '21B110256N' => array('fl' => '90'),
            '21B110261N' => array('fl' => '90'),
            '21B110277N' => array('fl' => '89'),
            '21B110282N' => array('fl' => '89'),
            '21B110285N' => array('fl' => '92'),
            '21B110299N' => array('fl' => '90'),
            '21B110344N' => array('fl' => '92'),
            '21B110352N' => array('fl' => '89'),
            '21B110372N' => array('fl' => '90'),
            '21B110432N' => array('fl' => '89'),
            '21C110537N' => array('fl' => '90'),
            '21C110538N' => array('fl' => '90'),
            '21C110706N' => array('fl' => '89'),
            '21C110724N' => array('fl' => '92'),
            '21C110784N' => array('fl' => '89'),
            '21C110819N' => array('fl' => '92'),
            '21C110821N' => array('fl' => '92'),
            '21C110832N' => array('fl' => '89'),
            '21C110855N' => array('fl' => '92'),
            '21C110863N' => array('fl' => '90'),
            '21C110864N' => array('fl' => '90'),
            '21C110870N' => array('fl' => '90'),
            '21C110873N' => array('fl' => '92'),
            '21C110925N' => array('fl' => '90'),
            '13F065101K' => array('fl' => '82'),
            '16B065120K' => array('fl' => '82'),
            '17J065121K' => array('fl' => '82'),
            '18A065122K' => array('fl' => '82'),
            '18A065123K' => array('fl' => '82'),
            '17J065127K' => array('fl' => '82'),
            '17J065131K' => array('fl' => '82'),
            '18C065132K' => array('fl' => '82'),
            '18A065133K' => array('fl' => '82'),
            '19D065134K' => array('fl' => '82'),
            '17M065136K' => array('fl' => '82'),
            '19G065137K' => array('fl' => '82'),
            '16J065142K' => array('fl' => '82'),
            '18F065146K' => array('fl' => '82'),
            '19D065148K' => array('fl' => '82'),
            '16B000383A' => array('fl' => '82'),
            '19J000384A' => array('fl' => '82'),
            '18E065150K' => array('fl' => '82'),
            '18E065154K' => array('fl' => '82'),
            '12A065155K' => array('fl' => '82'),
            '08F065158K' => array('fl' => '82'),
            '20K065168K' => array('fl' => '82'),
            '20K065169K' => array('fl' => '82'),
            '20K065170K' => array('fl' => '82'),
            '20K107271N' => array('fl' => '82'),
            '20K000462A' => array('fl' => '82'),
            '20L065172K' => array('fl' => '82'),
            '20L065174K' => array('fl' => '82'),
            '20L065175K' => array('fl' => '82'),
            '20L065176K' => array('fl' => '82'),
            '20L065177K' => array('fl' => '82'),
            '20L065178K' => array('fl' => '82'),
            '20M065179K' => array('fl' => '82'),
            '20M065180K' => array('fl' => '82'),
            '20M065181K' => array('fl' => '82'),
            '18L102567N' => array('fl' => '90'),
            '18C102568N' => array('fl' => '90'),
            '13H102713N' => array('fl' => '90'),
            '19J102794N' => array('fl' => '90'),
            '19C102817N' => array('fl' => '86'),
            '19G102879N' => array('fl' => '89'),
            '19F102887N' => array('fl' => '89'),
            '19E102909N' => array('fl' => '89'),
            '19B102917N' => array('fl' => '89'),
            '19F102918N' => array('fl' => '90'),
            '13A102983N' => array('fl' => '90'),
            '17J103000N' => array('fl' => '90'),
            '19H103177N' => array('fl' => '86'),
            '19H103191N' => array('fl' => '92'),
            '19H103203N' => array('fl' => '90'),
            '19J103336N' => array('fl' => '90'),
            '19H103373N' => array('fl' => '90'),
            '19H103379N' => array('fl' => '90'),
            '19G103415N' => array('fl' => '90'),
            '19F103419N' => array('fl' => '90'),
            '19J103430N' => array('fl' => '90'),
            '19F103464N' => array('fl' => '89'),
            '19G103530N' => array('fl' => '90'),
            '19F103541N' => array('fl' => '90'),
            '19D103561N' => array('fl' => '90'),
            '19C103583N' => array('fl' => '89'),
            '19C103586N' => array('fl' => '89'),
            '19C103587N' => array('fl' => '89'),
            '18L103589N' => array('fl' => '89'),
            '19F103609N' => array('fl' => '90'),
            '19F103611N' => array('fl' => '90'),
            '19D103626N' => array('fl' => '92'),
            '19B103639N' => array('fl' => '92'),
            '18G103649N' => array('fl' => '90'),
            '18G103650N' => array('fl' => '92'),
            '19F103651N' => array('fl' => '89'),
            '19D103653N' => array('fl' => '92'),
            '19D103660N' => array('fl' => '90'),
            '19D103662N' => array('fl' => '89'),
            '19D103666N' => array('fl' => '89'),
            '19D103668N' => array('fl' => '90'),
            '19D103671N' => array('fl' => '89'),
            '18J103690N' => array('fl' => '90'),
            '18E103701N' => array('fl' => '90'),
            '19B103705N' => array('fl' => '92'),
            '19B103710N' => array('fl' => '89'),
            '19B103722N' => array('fl' => '89'),
            '19B103726N' => array('fl' => '92'),
            '19A103733N' => array('fl' => '89'),
            '18M103763N' => array('fl' => '92'),
            '18L103771N' => array('fl' => '92'),
            '18L103773N' => array('fl' => '89'),
            '18L103779N' => array('fl' => '92'),
            '18K103788N' => array('fl' => '90'),
            '18J103801N' => array('fl' => '92'),
            '18G103811N' => array('fl' => '90'),
            '18F103818N' => array('fl' => '89'),
            '18J103822N' => array('fl' => '92'),
            '18J103828N' => array('fl' => '90'),
            '18F103839N' => array('fl' => '92'),
            '18F103842N' => array('fl' => '89'),
            '18F103843N' => array('fl' => '89'),
            '18J103844N' => array('fl' => '90'),
            '18B103849N' => array('fl' => '90'),
            '18D103854N' => array('fl' => '92'),
            '18B103860N' => array('fl' => '90'),
            '18L103861N' => array('fl' => '90'),
            '18L103862N' => array('fl' => '92'),
            '18L103864N' => array('fl' => '92'),
            '18L103866N' => array('fl' => '92'),
            '18K103879N' => array('fl' => '92'),
            '18K103880N' => array('fl' => '90'),
            '18K103888N' => array('fl' => '92'),
            '18J103896N' => array('fl' => '86'),
            '18J103900N' => array('fl' => '92'),
            '18J103906N' => array('fl' => '90'),
            '18G103917N' => array('fl' => '89'),
            '18J103936N' => array('fl' => '92'),
            '18J103941N' => array('fl' => '92'),
            '18J103943N' => array('fl' => '92'),
            '18J103944N' => array('fl' => '92'),
            '18J103946N' => array('fl' => '90'),
            '18J103948N' => array('fl' => '90'),
            '18J103951N' => array('fl' => '92'),
            '18J103957N' => array('fl' => '90'),
            '18J103960N' => array('fl' => '92'),
            '18G103963N' => array('fl' => '92'),
            '18G103969N' => array('fl' => '90'),
            '17G103985N' => array('fl' => '92'),
            '17G103986N' => array('fl' => '90'),
            '17G103991N' => array('fl' => '90'),
            '17D103994N' => array('fl' => '86'),
            '17K104004N' => array('fl' => '89'),
            '17J104007N' => array('fl' => '90'),
            '17M104009N' => array('fl' => '90'),
            '17M104011N' => array('fl' => '86'),
            '17M104014N' => array('fl' => '89'),
            '17M104016N' => array('fl' => '92'),
            '17L104025N' => array('fl' => '89'),
            '18A104026N' => array('fl' => '92'),
            '17L104027N' => array('fl' => '89'),
            '17M104029N' => array('fl' => '89'),
            '17M104030N' => array('fl' => '92'),
            '17M104031N' => array('fl' => '90'),
            '17M104032N' => array('fl' => '86'),
            '17M104033N' => array('fl' => '89'),
            '17M104035N' => array('fl' => '92'),
            '17M104040N' => array('fl' => '92'),
            '18A104044N' => array('fl' => '89'),
            '18A104045N' => array('fl' => '92'),
            '17G104048N' => array('fl' => '92'),
            '18A104050N' => array('fl' => '90'),
            '18C104052N' => array('fl' => '92'),
            '18A104056N' => array('fl' => '92'),
            '18A104058N' => array('fl' => '92'),
            '18A104062N' => array('fl' => '92'),
            '18B104078N' => array('fl' => '90'),
            '18D104084N' => array('fl' => '86'),
            '18D104085N' => array('fl' => '92'),
            '18D104086N' => array('fl' => '92'),
            '18A104090N' => array('fl' => '90'),
            '18D104100N' => array('fl' => '92'),
            '18C104110N' => array('fl' => '92'),
            '18B104115N' => array('fl' => '89'),
            '18C104116N' => array('fl' => '90'),
            '18A104117N' => array('fl' => '92'),
            '17M104126N' => array('fl' => '90'),
            '17M104127N' => array('fl' => '92'),
            '17M104131N' => array('fl' => '90'),
            '17M104132N' => array('fl' => '89'),
            '17M104135N' => array('fl' => '86'),
            '17L104144N' => array('fl' => '86'),
            '18A104158N' => array('fl' => '86'),
            '18A104160N' => array('fl' => '86'),
            '17M104166N' => array('fl' => '90'),
            '17M104171N' => array('fl' => '90'),
            '17L104173N' => array('fl' => '90'),
            '17L104177N' => array('fl' => '86'),
            '18B104183N' => array('fl' => '90'),
            '17G104204N' => array('fl' => '86'),
            '17G104208N' => array('fl' => '86'),
            '17C104222N' => array('fl' => '90'),
            '16G104227N' => array('fl' => '86'),
            '16M104232N' => array('fl' => '92'),
            '16J104237N' => array('fl' => '92'),
            '16K104241N' => array('fl' => '90'),
            '16F104243N' => array('fl' => '90'),
            '17K104253N' => array('fl' => '86'),
            '17K104255N' => array('fl' => '92'),
            '17J104287N' => array('fl' => '90'),
            '17L104291N' => array('fl' => '89'),
            '17G104294N' => array('fl' => '90'),
            '17L104297N' => array('fl' => '89'),
            '17L104298N' => array('fl' => '86'),
            '17G104303N' => array('fl' => '92'),
            '17L104305N' => array('fl' => '89'),
            '16G104316N' => array('fl' => '86'),
            '17L104317N' => array('fl' => '92'),
            '17E104320N' => array('fl' => '90'),
            '16G104334N' => array('fl' => '90'),
            '17J104340N' => array('fl' => '90'),
            '17K104348N' => array('fl' => '92'),
            '17K104354N' => array('fl' => '86'),
            '17J104387N' => array('fl' => '89'),
            '15K104388N' => array('fl' => '92'),
            '17J104390N' => array('fl' => '90'),
            '15K104403N' => array('fl' => '90'),
            '16G104438N' => array('fl' => '90'),
            '15M104462N' => array('fl' => '90'),
            '15M104466N' => array('fl' => '92'),
            '15A104476N' => array('fl' => '86'),
            '15K104501N' => array('fl' => '86'),
            '15K104506N' => array('fl' => '92'),
            '16K104553N' => array('fl' => '92'),
            '17G104564N' => array('fl' => '92'),
            '16C104579N' => array('fl' => '90'),
            '17G104587N' => array('fl' => '90'),
            '17K104592N' => array('fl' => '92'),
            '17J104593N' => array('fl' => '86'),
            '17G104597N' => array('fl' => '90'),
            '19H104600N' => array('fl' => '86'),
            '17L104601N' => array('fl' => '90'),
            '17L104606N' => array('fl' => '92'),
            '17L104609N' => array('fl' => '90'),
            '17L104610N' => array('fl' => '92'),
            '18G104618N' => array('fl' => '86'),
            '18A104622N' => array('fl' => '86'),
            '18F104623N' => array('fl' => '86'),
            '18F104626N' => array('fl' => '86'),
            '16C104639N' => array('fl' => '92'),
            '13J104643N' => array('fl' => '86'),
            '18G104647N' => array('fl' => '86'),
            '15E104650N' => array('fl' => '90'),
            '13H104655N' => array('fl' => '86'),
            '16C104658N' => array('fl' => '86'),
            '16C104670N' => array('fl' => '92'),
            '19F104671N' => array('fl' => '86'),
            '13J104674N' => array('fl' => '86'),
            '13B104679N' => array('fl' => '86'),
            '15M104692N' => array('fl' => '90'),
            '13B104695N' => array('fl' => '92'),
            '16C104704N' => array('fl' => '86'),
            '16C104713N' => array('fl' => '86'),
            '16C104720N' => array('fl' => '86'),
            '16C104724N' => array('fl' => '90'),
            '15C104731N' => array('fl' => '86'),
            '17A104743N' => array('fl' => '86'),
            '16K104750N' => array('fl' => '90'),
            '16K104752N' => array('fl' => '90'),
            '16J104756N' => array('fl' => '90'),
            '16G104761N' => array('fl' => '92'),
            '16J104764N' => array('fl' => '90'),
            '16J104767N' => array('fl' => '90'),
            '16H104774N' => array('fl' => '86'),
            '14M104791N' => array('fl' => '92'),
            '15A104793N' => array('fl' => '90'),
            '14M104797N' => array('fl' => '90'),
            '14M104803N' => array('fl' => '92'),
            '14B104812N' => array('fl' => '92'),
            '14L104813N' => array('fl' => '86'),
            '15D104837N' => array('fl' => '92'),
            '14L104846N' => array('fl' => '92'),
            '15H104849N' => array('fl' => '90'),
            '16B104852N' => array('fl' => '92'),
            '15C104856N' => array('fl' => '86'),
            '15C104857N' => array('fl' => '86'),
            '17A104863N' => array('fl' => '89'),
            '14L104872N' => array('fl' => '90'),
            '14C104873N' => array('fl' => '86'),
            '16B104881N' => array('fl' => '86'),
            '13J104883N' => array('fl' => '86'),
            '13E104890N' => array('fl' => '92'),
            '15C104901N' => array('fl' => '90'),
            '13L104907N' => array('fl' => '86'),
            '14L104920N' => array('fl' => '90'),
            '14L104922N' => array('fl' => '92'),
            '13H104944N' => array('fl' => '86'),
            '13J104971N' => array('fl' => '86'),
            '12L104972N' => array('fl' => '90'),
            '15K104985N' => array('fl' => '90'),
            '12G104998N' => array('fl' => '90'),
            '15K104999N' => array('fl' => '92'),
            '15K105003N' => array('fl' => '90'),
            '17E105025N' => array('fl' => '90'),
            '13L105028N' => array('fl' => '90'),
            '14F105050N' => array('fl' => '90'),
            '14B105074N' => array('fl' => '92'),
            '14K105075N' => array('fl' => '92'),
            '11K105076N' => array('fl' => '86'),
            '14K105080N' => array('fl' => '90'),
            '12L105084N' => array('fl' => '90'),
            '13A105090N' => array('fl' => '86'),
            '12H105093N' => array('fl' => '86'),
            '13J105095N' => array('fl' => '92'),
            '13J105098N' => array('fl' => '92'),
            '14K105099N' => array('fl' => '90'),
            '10F105110N' => array('fl' => '86'),
            '15L105114N' => array('fl' => '92'),
            '15L105115N' => array('fl' => '90'),
            '12M105121N' => array('fl' => '86'),
            '11E105127N' => array('fl' => '90'),
            '12L105139N' => array('fl' => '86'),
            '14C105151N' => array('fl' => '92'),
            '14B105160N' => array('fl' => '92'),
            '14B105178N' => array('fl' => '90'),
            '10A105191N' => array('fl' => '86'),
            '12M105203N' => array('fl' => '92'),
            '10A105212N' => array('fl' => '92'),
            '10A105218N' => array('fl' => '92'),
            '10B105221N' => array('fl' => '86'),
            '09F105224N' => array('fl' => '92'),
            '14C105376N' => array('fl' => '86'),
            '17A105403N' => array('fl' => '86'),
            '17G105500N' => array('fl' => '86'),
            '17M105558N' => array('fl' => '86'),
            '18F105570N' => array('fl' => '86'),
            '18E105625N' => array('fl' => '86'),
            '18E105634N' => array('fl' => '86'),
            '18G105649N' => array('fl' => '86'),
            '19G105678N' => array('fl' => '86'),
            '19J105750N' => array('fl' => '86'),
            '19J105758N' => array('fl' => '86'),
            '19H105760N' => array('fl' => '86'),
            '19H105803N' => array('fl' => '89'),
            '15L105908N' => array('fl' => '92'),
            '19K105932N' => array('fl' => '86'),
            '20K106942N' => array('fl' => '90'),
            '20K106946N' => array('fl' => '90'),
            '20K106950N' => array('fl' => '92'),
            '20K106952N' => array('fl' => '89'),
            '20K106955N' => array('fl' => '92'),
            '20K106974N' => array('fl' => '89'),
            '20K106976N' => array('fl' => '90'),
            '20K106985N' => array('fl' => '89'),
            '20K107037N' => array('fl' => '92'),
            '20K107063N' => array('fl' => '90'),
            '20K107072N' => array('fl' => '89'),
            '20K107079N' => array('fl' => '90'),
            '20K107098N' => array('fl' => '89'),
            '20K107101N' => array('fl' => '89'),
            '20K107105N' => array('fl' => '89'),
            '20K107123N' => array('fl' => '90'),
            '20K107126N' => array('fl' => '90'),
            '20K107132N' => array('fl' => '90'),
            '20K107137N' => array('fl' => '89'),
            '20K107156N' => array('fl' => '90'),
            '20K107172N' => array('fl' => '86'),
            '20K107207N' => array('fl' => '89'),
            '20K107247N' => array('fl' => '92'),
            '20J107283N' => array('fl' => '92'),
            '19M107364N' => array('fl' => '92'),
            '19M107419N' => array('fl' => '86'),
            '19M107448N' => array('fl' => '90'),
            '19M107468N' => array('fl' => '90'),
            '19M107480N' => array('fl' => '90'),
            '19M107492N' => array('fl' => '86'),
            '19M107500N' => array('fl' => '92'),
            '19M107513N' => array('fl' => '90'),
            '19M107524N' => array('fl' => '89'),
            '19M107531N' => array('fl' => '86'),
            '19L107545N' => array('fl' => '90'),
            '19L107562N' => array('fl' => '89'),
            '19L107579N' => array('fl' => '89'),
            '19L107581N' => array('fl' => '89'),
            '19L107585N' => array('fl' => '89'),
            '19L107588N' => array('fl' => '89'),
            '19L107589N' => array('fl' => '89'),
            '19L107590N' => array('fl' => '89'),
            '19L107605N' => array('fl' => '92'),
            '17L107615N' => array('fl' => '86'),
            '19K107620N' => array('fl' => '89'),
            '19K107625N' => array('fl' => '89'),
            '17M107627N' => array('fl' => '90'),
            '20L107732N' => array('fl' => '86'),
            '20L107794N' => array('fl' => '90'),
            '20L107799N' => array('fl' => '90'),
            '19G107852N' => array('fl' => '89'),
            '20L107871N' => array('fl' => '90'),
            '20L107878N' => array('fl' => '90'),
            '20L107886N' => array('fl' => '89'),
            '20L107888N' => array('fl' => '89'),
            '20L107890N' => array('fl' => '90'),
            '20L107894N' => array('fl' => '90'),
            '20L107900N' => array('fl' => '92'),
            '20L107903N' => array('fl' => '86'),
            '20L107916N' => array('fl' => '89'),
            '20L107924N' => array('fl' => '92'),
            '20L107930N' => array('fl' => '90'),
            '20L107952N' => array('fl' => '90'),
            '20L107964N' => array('fl' => '90'),
            '20L107971N' => array('fl' => '89'),
            '20L108047N' => array('fl' => '89'),
            '20L108055N' => array('fl' => '90'),
            '20L108227N' => array('fl' => '92'),
            '20L108264N' => array('fl' => '90'),
            '20L108273N' => array('fl' => '92'),
            '20L108276N' => array('fl' => '92'),
            '20L108278N' => array('fl' => '92'),
            '20L108288N' => array('fl' => '90'),
            '20L108290N' => array('fl' => '90'),
            '20L108291N' => array('fl' => '92'),
            '20L108299N' => array('fl' => '90'),
            '20L108302N' => array('fl' => '90'),
            '20M108421N' => array('fl' => '86'),
            '20M108430N' => array('fl' => '89'),
            '20M108432N' => array('fl' => '89'),
            '20M108451N' => array('fl' => '92'),
            '20M108457N' => array('fl' => '90'),
            '20B108547N' => array('fl' => '89'),
            '20B108549N' => array('fl' => '92'),
            '20B108552N' => array('fl' => '89'),
            '20E108611N' => array('fl' => '92'),
            '20M108641N' => array('fl' => '92'),
            '20M108645N' => array('fl' => '92'),
            '20M108666N' => array('fl' => '92'),
            '20M108691N' => array('fl' => '89'),
            '20M108810N' => array('fl' => '89'),
            '20M108822N' => array('fl' => '90'),
            '21A108918N' => array('fl' => '89'),
            '21A108936N' => array('fl' => '89'),
            '21A108940N' => array('fl' => '89'),
            '21A108942N' => array('fl' => '92'),
            '21A108954N' => array('fl' => '90'),
            '21A108955N' => array('fl' => '90'),
            '21A109004N' => array('fl' => '89'),
            '21A109016N' => array('fl' => '90'),
            '21A109024N' => array('fl' => '86'),
            '21A109025N' => array('fl' => '89'),
            '21A109059N' => array('fl' => '92'),
            '21A109064N' => array('fl' => '89'),
            '21A109085N' => array('fl' => '89'),
            '21A109140N' => array('fl' => '92'),
            '21A109145N' => array('fl' => '90'),
            '21A109146N' => array('fl' => '90'),
            '21A109154N' => array('fl' => '89'),
            '21A109175N' => array('fl' => '89'),
            '21A109202N' => array('fl' => '89'),
            '21A109228N' => array('fl' => '89'),
            '21A109250N' => array('fl' => '89'),
            '21A109254N' => array('fl' => '86'),
            '21A109271N' => array('fl' => '92'),
            '21A109274N' => array('fl' => '89'),
            '21A109303N' => array('fl' => '89'),
            '21A109305N' => array('fl' => '89'),
            '21A109306N' => array('fl' => '89'),
            '21A109313N' => array('fl' => '89'),
            '21A109317N' => array('fl' => '89'),
            '21A109319N' => array('fl' => '92'),
            '21A109329N' => array('fl' => '89'),
            '21A109336N' => array('fl' => '92'),
            '21B109406N' => array('fl' => '89'),
            '21B109409N' => array('fl' => '89'),
            '21B109410N' => array('fl' => '89'),
            '21B109412N' => array('fl' => '89'),
            '21B109418N' => array('fl' => '89'),
            '21B109456N' => array('fl' => '92'),
            '21B110249N' => array('fl' => '89'),
            '21B110253N' => array('fl' => '89'),
            '21B110266N' => array('fl' => '89'),
            '21B110281N' => array('fl' => '89'),
            '21B110287N' => array('fl' => '89'),
            '21B110293N' => array('fl' => '89'),
            '21B110300N' => array('fl' => '92'),
            '21B110303N' => array('fl' => '90'),
            '21B110307N' => array('fl' => '89'),
            '21B110317N' => array('fl' => '90'),
            '21B110325N' => array('fl' => '89'),
            '21B110331N' => array('fl' => '92'),
            '21B110339N' => array('fl' => '89'),
            '21B110341N' => array('fl' => '90'),
            '21B110351N' => array('fl' => '86'),
            '21B110357N' => array('fl' => '89'),
            '21B110362N' => array('fl' => '89'),
            '21B110365N' => array('fl' => '89'),
            '21B110371N' => array('fl' => '89'),
            '21B110411N' => array('fl' => '89'),
            '21C110605N' => array('fl' => '89'),
            '21C110731N' => array('fl' => '89'),
            '21C110761N' => array('fl' => '89'),
            '21C110764N' => array('fl' => '90'),
            '21C110767N' => array('fl' => '92'),
            '21C110795N' => array('fl' => '89'),
            '21C110803N' => array('fl' => '89'),
            '21C110804N' => array('fl' => '89'),
            '21C110814N' => array('fl' => '89'),
            '21C110829N' => array('fl' => '89'),
            '21C110869N' => array('fl' => '92'),
            '21C110889N' => array('fl' => '90'),
            '21C110901N' => array('fl' => '90'),
            '21C110912N' => array('fl' => '90'),
            '21C110958N' => array('fl' => '89'),
            '21C111092N' => array('fl' => '89'),
            '21C111122N' => array('fl' => '89'),
            '21C111123N' => array('fl' => '89'),
            '19K104337N' => array('fl' => '85'),
            '19J104339N' => array('fl' => '85'),
            '19J104400N' => array('fl' => '85'),
            '19K104437N' => array('fl' => '85'),
            '17G104455N' => array('fl' => '85'),
            '18F104474N' => array('fl' => '85'),
            '17F104523N' => array('fl' => '85'),
            '17M104539N' => array('fl' => '85'),
            '17M104541N' => array('fl' => '85'),
            '15H104578N' => array('fl' => '85'),
            '13B105838N' => array('fl' => '85'),
            '19K105999N' => array('fl' => '85'),
            '19L107647N' => array('fl' => '85'),
            '19M107658N' => array('fl' => '85'),
            '19M107666N' => array('fl' => '85'),
            '20L107843N' => array('fl' => '85'),
            '20L108210N' => array('fl' => '85'),
            '18J500500O' => array('fl' => '82'),
            '19K500550O' => array('fl' => '82'),
            '17M105567N' => array('fl' => '86'),
            '19H105809N' => array('fl' => '86'),
            '19H105810N' => array('fl' => '86'),
            '20M108792N' => array('fl' => '82'),
            '19M107404N' => array('fl' => '86'),
            '19M107407N' => array('fl' => '86'),
            '19M107446N' => array('fl' => '86'),
            '20M108700N' => array('fl' => '86'),
            '21A108966N' => array('fl' => '86'),
            '21C110848N' => array('fl' => '86'),
            '21C110920N' => array('fl' => '86'),
            '21C110922N' => array('fl' => '86'),
            '21C110924N' => array('fl' => '86'),
            '21C110928N' => array('fl' => '86'),
            '16K104394N' => array('fl' => '86'),
            '18G105652N' => array('fl' => '86'),
            '19G105806N' => array('fl' => '86'),
            '19M107371N' => array('fl' => '86'),
            '19M107424N' => array('fl' => '86'),
            '19M107426N' => array('fl' => '86'),
            '17L102653N' => array('fl' => '82'),
            '19K104422N' => array('fl' => '85'),
            '17L104524N' => array('fl' => '85'),
            '17M104544N' => array('fl' => '85'),
            '18C104562N' => array('fl' => '85'),
            '14B104589N' => array('fl' => '85'),
            '16B104591N' => array('fl' => '85'),
            '16C104604N' => array('fl' => '85'),
            '15D104637N' => array('fl' => '85'),
            '14B104669N' => array('fl' => '85'),
            '13K104676N' => array('fl' => '85'),
            '13K104690N' => array('fl' => '85'),
            '13A104699N' => array('fl' => '85'),
            '13K104722N' => array('fl' => '85'),
            '20L108155N' => array('fl' => '92'),
            '21B110414N' => array('fl' => '90'),
            '21B110415N' => array('fl' => '90'),
            '19M107654N' => array('fl' => '85'),
            '19M107659N' => array('fl' => '85'),
            '21A109098N' => array('fl' => '85'),
            '18G104389N' => array('fl' => '85'),
            '15B104651N' => array('fl' => '85'),
            '20K106909N' => array('fl' => '92'),
            '20K107270N' => array('fl' => '82'),
            '19M107454N' => array('fl' => '90'),
            '19M107455N' => array('fl' => '89'),
            '19M107472N' => array('fl' => '89'),
            '19M065172K' => array('fl' => '82'),
            '19K107633N' => array('fl' => '92'),
            '20L107808N' => array('fl' => '90'),
            '20L107809N' => array('fl' => '92'),
            '20L107811N' => array('fl' => '92'),
            '20L107813N' => array('fl' => '90'),
            '20L107815N' => array('fl' => '90'),
            '20L107841N' => array('fl' => '85'),
            '20L107985N' => array('fl' => '92'),
            '20L108061N' => array('fl' => '92'),
            '20L108062N' => array('fl' => '92'),
            '20L108096N' => array('fl' => '90'),
            '20L108295N' => array('fl' => '92'),
            '20M108410N' => array('fl' => '90'),
            '20M108442N' => array('fl' => '85'),
            '21A108930N' => array('fl' => '90'),
            '21A065183K' => array('fl' => '82'),
            '21A109045N' => array('fl' => '92'),
            '21B109450N' => array('fl' => '90'),
            '21B110353N' => array('fl' => '92'),
            '21C110742N' => array('fl' => '92'),
            '89E110842N' => array('fl' => '82'),
            '21C110956N' => array('fl' => '89'),
            '21C110957N' => array('fl' => '86'),
            '21C110970N' => array('fl' => '85'),
            '17M104296N' => array('fl' => '85'),
            '17J104349N' => array('fl' => '85'),
            '16C104384N' => array('fl' => '85'),
            '15H104406N' => array('fl' => '85'),
            '17K104421N' => array('fl' => '85'),
            '13L104431N' => array('fl' => '85'),
            '18E104475N' => array('fl' => '85'),
            '15A104586N' => array('fl' => '85'),
            '21C110846N' => array('fl' => '85'),
            '18F104378N' => array('fl' => '85'),
            '19F104407N' => array('fl' => '85'),
            '19E104415N' => array('fl' => '85'),
            '19G104439N' => array('fl' => '85'),
            '17J104465N' => array('fl' => '85'),
            '16K104488N' => array('fl' => '85'),
            '18A104493N' => array('fl' => '85'),
            '18B104499N' => array('fl' => '85'),
            '13F104504N' => array('fl' => '85'),
            '15C104509N' => array('fl' => '85'),
            '15C104517N' => array('fl' => '85'),
            '15H104518N' => array('fl' => '85'),
            '13J104520N' => array('fl' => '85'),
            '16C104529N' => array('fl' => '85'),
            '15A104532N' => array('fl' => '85'),
            '15C104547N' => array('fl' => '85'),
            '11A104552N' => array('fl' => '85'),
            '16C104554N' => array('fl' => '85'),
            '16C104568N' => array('fl' => '85'),
            '16B104570N' => array('fl' => '85'),
            '11K104575N' => array('fl' => '85'),
            '15B104581N' => array('fl' => '85'),
            '19H105631N' => array('fl' => '85'),
            '20L108005N' => array('fl' => '85'),
            '20L108098N' => array('fl' => '85'),
            '20M108399N' => array('fl' => '85'),
            '19G104427N' => array('fl' => '85'),
            '18J104459N' => array('fl' => '85'),
            '13B105441N' => array('fl' => '85'),
            '14H105451N' => array('fl' => '85'),
            '14F105477N' => array('fl' => '85'),
            '14H105508N' => array('fl' => '85'),
            '10C105517N' => array('fl' => '85'),
            '15C105534N' => array('fl' => '85'),
            '15C105543N' => array('fl' => '85'),
            '17K105566N' => array('fl' => '85'),
            '18E105571N' => array('fl' => '85'),
            '19H105576N' => array('fl' => '85'),
            '19F105612N' => array('fl' => '85'),
            '19F105618N' => array('fl' => '85'),
            '19F105619N' => array('fl' => '85'),
            '19M107376N' => array('fl' => '85'),
            '18B500336O' => array('fl' => '82'),
            '17M500337O' => array('fl' => '85'),
            '18A500340O' => array('fl' => '90'),
            '18A500341O' => array('fl' => '86'),
            '18A500342O' => array('fl' => '92'),
            '18C500343O' => array('fl' => '90'),
            '18B500344O' => array('fl' => '92'),
            '18E500345O' => array('fl' => '90'),
            '18G500347O' => array('fl' => '90'),
            '18A500348O' => array('fl' => '89'),
            '18J500351O' => array('fl' => '92'),
            '18J500356O' => array('fl' => '92'),
            '18J500379O' => array('fl' => '89'),
            '17A500398O' => array('fl' => '86'),
            '17A500399O' => array('fl' => '86'),
            '16A500405O' => array('fl' => '86'),
            '18A500407O' => array('fl' => '86'),
            '14C500408O' => array('fl' => '92'),
            '14K500409O' => array('fl' => '90'),
            '14J500410O' => array('fl' => '90'),
            '17K500413O' => array('fl' => '92'),
            '17J500416O' => array('fl' => '90'),
            '16K500417O' => array('fl' => '90'),
            '17H500419O' => array('fl' => '86'),
            '15H500424O' => array('fl' => '90'),
            '18A500427O' => array('fl' => '86'),
            '18A500428O' => array('fl' => '86'),
            '18A500429O' => array('fl' => '92'),
            '18A500430O' => array('fl' => '86'),
            '17L500431O' => array('fl' => '90'),
            '18B500432O' => array('fl' => '86'),
            '17K500434O' => array('fl' => '92'),
            '17K500439O' => array('fl' => '92'),
            '16M500441O' => array('fl' => '90'),
            '17J500443O' => array('fl' => '85'),
            '17M500445O' => array('fl' => '89'),
            '17M500450O' => array('fl' => '86'),
            '17M500451O' => array('fl' => '89'),
            '13E500453O' => array('fl' => '92'),
            '12L500455O' => array('fl' => '90'),
            '11E500456O' => array('fl' => '90'),
            '12M500458O' => array('fl' => '90'),
            '10J500462O' => array('fl' => '92'),
            '16J500470O' => array('fl' => '90'),
            '13L500475O' => array('fl' => '90'),
            '13L500476O' => array('fl' => '85'),
            '17K500478O' => array('fl' => '86'),
            '10A500479O' => array('fl' => '92'),
            '16A500480O' => array('fl' => '90'),
            '14K500481O' => array('fl' => '92'),
            '17A500482O' => array('fl' => '90'),
            '13H500491O' => array('fl' => '85'),
            '15E500556O' => array('fl' => '86'),
            '13A500557O' => array('fl' => '86'),
            '15A500558O' => array('fl' => '86'),
            '17J500559O' => array('fl' => '86'),
            '17G500561O' => array('fl' => '86'),
            '14H500587O' => array('fl' => '85'),
            '18F500588O' => array('fl' => '85'),
            '16D500596O' => array('fl' => '85'),
            '18G500597O' => array('fl' => '85'),
            '15F500599O' => array('fl' => '85'),
            '14K500600O' => array('fl' => '85'),
            '14C500601O' => array('fl' => '85'),
            '17G500602O' => array('fl' => '85'),
            '16K500607O' => array('fl' => '85'),
            '13H500608O' => array('fl' => '85'),
            '18F500609O' => array('fl' => '86'),
            '17M500610O' => array('fl' => '86'),
            '18F500611O' => array('fl' => '86'),
            '16B105367N' => array('fl' => '86'),
            '16A105370N' => array('fl' => '86'),
            '18J105393N' => array('fl' => '86'),
            '17K105519N' => array('fl' => '86'),
            '17J105607N' => array('fl' => '86'),
            '19H105753N' => array('fl' => '86'),
            '18J500614O' => array('fl' => '86'),
            '18G500615O' => array('fl' => '86'),
            '18M500616O' => array('fl' => '86'),
            '18F500624O' => array('fl' => '86'),
            '18F500625O' => array('fl' => '86'),
            '18G500628O' => array('fl' => '86'),
            '18G500632O' => array('fl' => '86'),
            '18G500636O' => array('fl' => '86'),
            '18E500645O' => array('fl' => '86'),
            '18F500650O' => array('fl' => '86'),
            '18F500652O' => array('fl' => '89'),
            '17A500666O' => array('fl' => '90'),
            '14M500670O' => array('fl' => '86'),
            '17A500671O' => array('fl' => '86'),
            '17L500672O' => array('fl' => '86'),
            '17J500675O' => array('fl' => '86'),
            '13D500676O' => array('fl' => '86'),
            '17G500677O' => array('fl' => '86'),
            '17L500678O' => array('fl' => '86'),
            '17L500683O' => array('fl' => '86'),
            '17J500684O' => array('fl' => '86'),
            '16D500685O' => array('fl' => '86'),
            '16B500686O' => array('fl' => '86'),
            '16D500689O' => array('fl' => '86'),
            '15B500691O' => array('fl' => '86'),
            '17L500692O' => array('fl' => '86'),
            '17L500693O' => array('fl' => '86'),
            '17F500694O' => array('fl' => '86'),
            '17L500695O' => array('fl' => '86'),
            '17L500696O' => array('fl' => '86'),
            '17L500697O' => array('fl' => '86'),
            '17G500698O' => array('fl' => '86'),
            '17E500699O' => array('fl' => '86'),
            '16M500700O' => array('fl' => '86'),
            '16J500706O' => array('fl' => '86'),
            '17J500709O' => array('fl' => '86'),
            '16K500710O' => array('fl' => '86'),
            '17M500711O' => array('fl' => '86'),
            '16G500712O' => array('fl' => '86'),
            '17M500714O' => array('fl' => '86'),
            '17L500715O' => array('fl' => '86'),
            '17M500716O' => array('fl' => '86'),
            '18A500717O' => array('fl' => '86'),
            '18A500718O' => array('fl' => '86'),
            '17M500722O' => array('fl' => '86'),
            '17M500723O' => array('fl' => '89'),
            '14K500726O' => array('fl' => '86'),
            '17L500727O' => array('fl' => '86'),
            '11C500728O' => array('fl' => '86'),
            '18J500729O' => array('fl' => '86'),
            '09M500736O' => array('fl' => '86'),
            '13E500738O' => array('fl' => '86'),
            '13H500739O' => array('fl' => '86'),
            '07L500740O' => array('fl' => '86'),
            '17K500741O' => array('fl' => '86'),
            '09C500742O' => array('fl' => '86'),
            '12J500743O' => array('fl' => '86'),
            '13B500744O' => array('fl' => '86'),
            '14H500745O' => array('fl' => '86'),
            '12L500746O' => array('fl' => '86'),
            '12J500749O' => array('fl' => '86'),
            '16A500750O' => array('fl' => '86'),
            '15K500751O' => array('fl' => '86'),
            '13F500753O' => array('fl' => '86'),
            '14L500754O' => array('fl' => '86'),
            '13G500757O' => array('fl' => '86'),
            '13L500760O' => array('fl' => '86'),
            '16B500763O' => array('fl' => '86'),
            '16B500765O' => array('fl' => '86'),
            '17A500768O' => array('fl' => '86'),
            '15A500769O' => array('fl' => '86'),
            '17M500786O' => array('fl' => '82'),
            '15H500787O' => array('fl' => '82'),
            '18A500788O' => array('fl' => '82'),
            '18F500789O' => array('fl' => '82'),
            '17G500790O' => array('fl' => '82'),
            '16D500791O' => array('fl' => '82'),
            '18A500792O' => array('fl' => '82'),
            '20K106889N' => array('fl' => '86'),
            '20K500928O' => array('fl' => '90'),
            '19M107410N' => array('fl' => '86'),
            '20L107996N' => array('fl' => '86'),
            '21A109027N' => array('fl' => '86'),
            '21B109545N' => array('fl' => '86'),
            '21B501260O' => array('fl' => '90'),
            '21C501335O' => array('fl' => '90'),
            '20M108829N' => array('fl' => '85'),
            '13J105332N' => array('fl' => '86'),
            '20L107983N' => array('fl' => '86'),
            '20L108185N' => array('fl' => '85'),
            '21A109074N' => array('fl' => '90'),
            '21B110278N' => array('fl' => '90'),
            '19E103071N' => array('fl' => '85'),
            '19F103086N' => array('fl' => '85'),
            '19J103088N' => array('fl' => '85'),
            '12L103103N' => array('fl' => '85'),
            '19D103625N' => array('fl' => '85'),
            '18G103970N' => array('fl' => '85'),
            '17L104265N' => array('fl' => '85'),
            '17C104312N' => array('fl' => '85'),
            '15A104782N' => array('fl' => '85'),
            '16A104819N' => array('fl' => '85'),
            '11M105054N' => array('fl' => '85'),
            '11E105131N' => array('fl' => '85'),
            '20B107356N' => array('fl' => '85'),
            '20A107361N' => array('fl' => '85'),
            '20L108158N' => array('fl' => '85'),
            '19F105761N' => array('fl' => '86'),
            '19M107365N' => array('fl' => '86'),
            '19M107366N' => array('fl' => '82'),
            '19M107367N' => array('fl' => '86'),
            '19M107403N' => array('fl' => '86'),
            '19M107428N' => array('fl' => '86'),
            '19M107444N' => array('fl' => '86'),
            '21A109081N' => array('fl' => '86'),
            '19H500338O' => array('fl' => '82'),
            '19B500354O' => array('fl' => '92'),
            '19K500367O' => array('fl' => '89'),
            '19G500368O' => array('fl' => '90'),
            '19J500371O' => array('fl' => '92'),
            '19H500380O' => array('fl' => '89'),
            '19F500384O' => array('fl' => '90'),
            '19H500387O' => array('fl' => '89'),
            '19K500391O' => array('fl' => '89'),
            '19F500584O' => array('fl' => '85'),
            '19K500586O' => array('fl' => '85'),
            '19H500590O' => array('fl' => '86'),
            '19H500591O' => array('fl' => '85'),
            '19K500593O' => array('fl' => '85'),
            '19E104414N' => array('fl' => '85'),
            '18M500619O' => array('fl' => '86'),
            '19A500621O' => array('fl' => '86'),
            '19F500642O' => array('fl' => '86'),
            '19F500644O' => array('fl' => '86'),
            '19C500648O' => array('fl' => '86'),
            '19J500649O' => array('fl' => '86'),
            '19G500653O' => array('fl' => '82'),
            '19G500654O' => array('fl' => '82'),
            '19K500659O' => array('fl' => '86'),
            '19K500664O' => array('fl' => '86'),
            '19K500665O' => array('fl' => '86'),
            '19J500667O' => array('fl' => '86'),
            '19J500793O' => array('fl' => '82'),
            '19J500794O' => array('fl' => '82'),
            '19K500798O' => array('fl' => '92'),
            '20K500905O' => array('fl' => '92'),
            '20K500906O' => array('fl' => '92'),
            '20K500907O' => array('fl' => '92'),
            '20K500908O' => array('fl' => '92'),
            '20K500909O' => array('fl' => '92'),
            '20K500911O' => array('fl' => '90'),
            '20K500913O' => array('fl' => '92'),
            '20K500916O' => array('fl' => '92'),
            '20K500917O' => array('fl' => '86'),
            '20K500921O' => array('fl' => '86'),
            '20K500922O' => array('fl' => '92'),
            '20K500927O' => array('fl' => '82'),
            '19M500942O' => array('fl' => '86'),
            '19M500944O' => array('fl' => '86'),
            '19M500945O' => array('fl' => '92'),
            '19M500946O' => array('fl' => '92'),
            '19M500950O' => array('fl' => '86'),
            '19M500951O' => array('fl' => '86'),
            '19M500952O' => array('fl' => '86'),
            '19M500953O' => array('fl' => '92'),
            '19M500954O' => array('fl' => '89'),
            '19M500956O' => array('fl' => '86'),
            '19M500958O' => array('fl' => '86'),
            '19M500960O' => array('fl' => '89'),
            '19L500962O' => array('fl' => '86'),
            '19L500963O' => array('fl' => '86'),
            '19L500964O' => array('fl' => '90'),
            '19L500965O' => array('fl' => '89'),
            '19L500966O' => array('fl' => '92'),
            '20K500972O' => array('fl' => '85'),
            '20K500973O' => array('fl' => '85'),
            '20K500974O' => array('fl' => '85'),
            '20K500975O' => array('fl' => '85'),
            '19M500976O' => array('fl' => '85'),
            '19M500977O' => array('fl' => '85'),
            '19M500978O' => array('fl' => '85'),
            '20L500985O' => array('fl' => '86'),
            '20L500986O' => array('fl' => '92'),
            '20L500988O' => array('fl' => '86'),
            '20L500989O' => array('fl' => '89'),
            '20L500990O' => array('fl' => '89'),
            '20L500991O' => array('fl' => '89'),
            '20L500992O' => array('fl' => '86'),
            '20L500993O' => array('fl' => '86'),
            '20L500997O' => array('fl' => '90'),
            '20L501001O' => array('fl' => '90'),
            '20L501002O' => array('fl' => '89'),
            '20L501003O' => array('fl' => '90'),
            '20L501006O' => array('fl' => '86'),
            '20L501014O' => array('fl' => '89'),
            '20L501017O' => array('fl' => '86'),
            '20L501027O' => array('fl' => '92'),
            '20L501033O' => array('fl' => '86'),
            '20L501038O' => array('fl' => '86'),
            '20L501046O' => array('fl' => '92'),
            '20L501047O' => array('fl' => '89'),
            '20L501050O' => array('fl' => '89'),
            '20M501079O' => array('fl' => '90'),
            '20M501082O' => array('fl' => '86'),
            '20M501084O' => array('fl' => '82'),
            '20M501085O' => array('fl' => '86'),
            '20B501086O' => array('fl' => '86'),
            '20B501087O' => array('fl' => '92'),
            '20B501088O' => array('fl' => '92'),
            '20B501089O' => array('fl' => '90'),
            '20B501090O' => array('fl' => '92'),
            '20M501091O' => array('fl' => '86'),
            '20E501092O' => array('fl' => '86'),
            '20E501093O' => array('fl' => '86'),
            '20E501094O' => array('fl' => '92'),
            '20M501100O' => array('fl' => '86'),
            '21A108909N' => array('fl' => '86'),
            '21A501122O' => array('fl' => '86'),
            '21A501123O' => array('fl' => '85'),
            '21A501124O' => array('fl' => '86'),
            '21A501125O' => array('fl' => '85'),
            '21A501127O' => array('fl' => '86'),
            '21A501129O' => array('fl' => '92'),
            '21A501132O' => array('fl' => '86'),
            '21A501133O' => array('fl' => '86'),
            '21A501140O' => array('fl' => '89'),
            '21A501142O' => array('fl' => '82'),
            '21A501143O' => array('fl' => '90'),
            '21A501144O' => array('fl' => '86'),
            '21A501147O' => array('fl' => '86'),
            '21A501150O' => array('fl' => '86'),
            '21A501151O' => array('fl' => '86'),
            '21A501152O' => array('fl' => '86'),
            '21A501156O' => array('fl' => '86'),
            '21A501157O' => array('fl' => '86'),
            '21B501171O' => array('fl' => '86'),
            '21B501172O' => array('fl' => '90'),
            '98B501174O' => array('fl' => '89'),
            '21B501175O' => array('fl' => '86'),
            '21B501176O' => array('fl' => '90'),
            '21B501177O' => array('fl' => '92'),
            '21B501178O' => array('fl' => '86'),
            '21B501179O' => array('fl' => '90'),
            '21B501246O' => array('fl' => '92'),
            '21B501247O' => array('fl' => '90'),
            '21B501248O' => array('fl' => '92'),
            '21B501249O' => array('fl' => '86'),
            '21B501250O' => array('fl' => '86'),
            '21B501251O' => array('fl' => '89'),
            '21B501252O' => array('fl' => '92'),
            '21B501254O' => array('fl' => '89'),
            '21B501255O' => array('fl' => '89'),
            '21B501256O' => array('fl' => '86'),
            '21B501257O' => array('fl' => '86'),
            '21B501258O' => array('fl' => '92'),
            '21B501264O' => array('fl' => '90'),
            '21B501267O' => array('fl' => '86'),
            '21B501268O' => array('fl' => '90'),
            '21B501270O' => array('fl' => '90'),
            '21B501271O' => array('fl' => '90'),
            '21B501273O' => array('fl' => '92'),
            '21C501295O' => array('fl' => '90'),
            '21C501296O' => array('fl' => '92'),
            '21C501299O' => array('fl' => '90'),
            '21C501300O' => array('fl' => '90'),
            '21C501301O' => array('fl' => '90'),
            '21C501302O' => array('fl' => '90'),
            '21C501306O' => array('fl' => '92'),
            '21C501307O' => array('fl' => '90'),
            '21C501324O' => array('fl' => '90'),
            '21C501325O' => array('fl' => '86'),
            '21C501326O' => array('fl' => '86'),
            '21C501327O' => array('fl' => '89'),
            '21C501328O' => array('fl' => '92'),
            '21C501329O' => array('fl' => '89'),
            '21C501330O' => array('fl' => '90'),
            '21C501332O' => array('fl' => '86'),
            '21C501334O' => array('fl' => '92'),
            '21C501336O' => array('fl' => '92'),
            '21C501338O' => array('fl' => '86'),
            '21C501339O' => array('fl' => '86'),
            '21C501340O' => array('fl' => '89'),
            '21C501341O' => array('fl' => '89'),
            '21C501342O' => array('fl' => '90'),
            '21C501343O' => array('fl' => '92'),
            '21C501344O' => array('fl' => '86'),
            '21C501345O' => array('fl' => '92'),
            '21C501346O' => array('fl' => '86'),
            '21C501349O' => array('fl' => '90'),
            '21C501351O' => array('fl' => '86'),
            '21C501352O' => array('fl' => '86'),
            '99A501359O' => array('fl' => '90'),
            '21C501370O' => array('fl' => '92'),
            '21C501371O' => array('fl' => '90'),
            '21C501372O' => array('fl' => '86'),
            '21C501373O' => array('fl' => '90'),
            '21C501374O' => array('fl' => '86'),
            '21C501375O' => array('fl' => '86'),
            '21A108960N' => array('fl' => '86'),
            '21B110273N' => array('fl' => '86'),
            '21B110346N' => array('fl' => '86'),
            '19H104198N' => array('fl' => '86'),
            '18J104203N' => array('fl' => '86'),
            '15A104214N' => array('fl' => '86'),
            '16L104217N' => array('fl' => '86'),
            '17K104225N' => array('fl' => '86'),
            '16A105277N' => array('fl' => '86'),
            '11J105410N' => array('fl' => '86'),
            '13J105474N' => array('fl' => '86'),
            '13B105502N' => array('fl' => '86'),
            '11L105556N' => array('fl' => '86'),
            '16A105603N' => array('fl' => '86'),
            '15M105710N' => array('fl' => '86'),
            '16C105799N' => array('fl' => '86'),
            '15M105801N' => array('fl' => '86'),
            '19M107377N' => array('fl' => '86'),
            '19M107431N' => array('fl' => '86'),
            '20E108632N' => array('fl' => '86'),
            '21A109014N' => array('fl' => '86'),
            '21A109090N' => array('fl' => '86'),
            '21A109094N' => array('fl' => '86'),
            '21A109097N' => array('fl' => '86'),
            '19K500489O' => array('fl' => '82'),
            '19K500571O' => array('fl' => '82'),
            '18E000372A' => array('fl' => '82'),
            '12G000381A' => array('fl' => '82'),
            '21B501275O' => array('fl' => '82'),
            '21B501276O' => array('fl' => '82'),
            '21B000552A' => array('fl' => '92'),
            '21A000503A' => array('fl' => '82'),
            '17K102667N' => array('fl' => '89'),
            '19H102702N' => array('fl' => '86'),
            '15K075057L' => array('fl' => '82'),
            '11L075062L' => array('fl' => '89'),
            '13G075063L' => array('fl' => '82'),
            '13L065107K' => array('fl' => '82'),
            '10F065128K' => array('fl' => '82'),
            '12G500518O' => array('fl' => '86'),
            '16D040002G' => array('fl' => '82'),
            '13J500563O' => array('fl' => '82'),
            '18D500573O' => array('fl' => '82'),
            '08K055034J' => array('fl' => '82'),
            '17M010051B' => array('fl' => '82'),
            '18B075090L' => array('fl' => '82'),
            '17L075092L' => array('fl' => '89'),
            '08H075103L' => array('fl' => '92'),
            '12B025010D' => array('fl' => '82'),
            '08H090017M' => array('fl' => '82'),
            '13B075106L' => array('fl' => '82'),
            '20L501053O' => array('fl' => '92'),
            '20L501054O' => array('fl' => '90'),
            '20M055051J' => array('fl' => '82'),
            '21C075144L' => array('fl' => '90'),
            '21C501354O' => array('fl' => '92'),
            '21C075148L' => array('fl' => '90'),
            '13D105388N' => array('fl' => '86'),
            '15A105431N' => array('fl' => '86'),
            '19F103300N' => array('fl' => '85'),
            '17M104015N' => array('fl' => '85'),
            '20H107341N' => array('fl' => '85'),
            '20H107342N' => array('fl' => '85'),
            '20H107344N' => array('fl' => '85'),
            '20H107345N' => array('fl' => '85'),
            '20H107347N' => array('fl' => '85'),
            '20B107354N' => array('fl' => '85'),
            '20L107723N' => array('fl' => '85'),
            '20L108093N' => array('fl' => '85'),
            '20L108094N' => array('fl' => '85'),
            '20L108265N' => array('fl' => '85'),
            '20L108297N' => array('fl' => '85'),
            '20M108653N' => array('fl' => '85'),
            '20M108683N' => array('fl' => '85'),
            '21A109256N' => array('fl' => '85'),
            '21A109259N' => array('fl' => '85'),
            '21A109260N' => array('fl' => '85'),
            '21A109264N' => array('fl' => '85'),
            '21A109284N' => array('fl' => '85'),
            '21A109291N' => array('fl' => '85'),
            '21A109292N' => array('fl' => '85'),
            '21A109294N' => array('fl' => '85'),
            '21A109295N' => array('fl' => '85'),
            '21A109296N' => array('fl' => '85'),
            '21A109309N' => array('fl' => '85'),
            '21A109312N' => array('fl' => '85'),
            '21A109324N' => array('fl' => '85'),
            '21A109326N' => array('fl' => '85'),
            '81D109333N' => array('fl' => '85'),
            '21A109334N' => array('fl' => '85'),
            '21A109335N' => array('fl' => '85'),
            '21B109444N' => array('fl' => '85'),
            '21B109445N' => array('fl' => '85'),
            '21B110262N' => array('fl' => '85'),
            '21B110310N' => array('fl' => '85'),
            '21B110360N' => array('fl' => '85'),
            '21B110420N' => array('fl' => '85'),
            '21C110715N' => array('fl' => '85'),
            '21C110727N' => array('fl' => '85'),
            '21C110736N' => array('fl' => '85'),
            '21C110810N' => array('fl' => '85'),
            '21C110850N' => array('fl' => '85'),
            '21C110895N' => array('fl' => '85'),
            '21C110923N' => array('fl' => '85'),
            '21C110960N' => array('fl' => '85'),
            '21C110971N' => array('fl' => '85'),
            '21C110972N' => array('fl' => '85'),
            '21C110974N' => array('fl' => '85'),
            '19J000245A' => array('fl' => '82'),
            '19J000246A' => array('fl' => '82'),
            '19J000248A' => array('fl' => '82'),
            '19K000250A' => array('fl' => '82'),
            '19K000251A' => array('fl' => '82'),
            '18D000255A' => array('fl' => '82'),
            '19C000257A' => array('fl' => '82'),
            '19E000258A' => array('fl' => '82'),
            '16J000260A' => array('fl' => '82'),
            '16C000261A' => array('fl' => '82'),
            '18A000263A' => array('fl' => '82'),
            '18A000264A' => array('fl' => '82'),
            '18D000265A' => array('fl' => '82'),
            '18D000266A' => array('fl' => '82'),
            '17M000268A' => array('fl' => '82'),
            '19F000270A' => array('fl' => '82'),
            '15M000273A' => array('fl' => '82'),
            '15F000274A' => array('fl' => '82'),
            '13J000277A' => array('fl' => '82'),
            '11F000278A' => array('fl' => '82'),
            '11D000279A' => array('fl' => '82'),
            '08D000280A' => array('fl' => '82'),
            '19K000282A' => array('fl' => '82'),
            '17M000283A' => array('fl' => '82'),
            '19B000284A' => array('fl' => '82'),
            '11G000285A' => array('fl' => '82'),
            '12B000286A' => array('fl' => '82'),
            '20K000461A' => array('fl' => '82'),
            '19K000465A' => array('fl' => '82'),
            '19K000466A' => array('fl' => '82'),
            '19K000469A' => array('fl' => '82'),
            '21A000506A' => array('fl' => '82'),
            '21C000561A' => array('fl' => '82'),
            '21C000562A' => array('fl' => '82'),
            '21C000563A' => array('fl' => '82'),
            '21C000564A' => array('fl' => '82'),
            '21C000565A' => array('fl' => '82'),
            '21C000566A' => array('fl' => '82'),
            '21C000567A' => array('fl' => '82'),
            '21C000571A' => array('fl' => '82'),
            '17A000573A' => array('fl' => '82'),
            '19G999912Q' => array('fl' => '89'),
            '19H104358N' => array('fl' => '85'),
            '16J104527N' => array('fl' => '85'),
            '16B104566N' => array('fl' => '85'),
            '12L104715N' => array('fl' => '85'),
            '13J104718N' => array('fl' => '85'),
            '20G010065B' => array('fl' => '82'),
            '20L055049J' => array('fl' => '82'),
            '17G103073N' => array('fl' => '85'),
            '12H105280N' => array('fl' => '86'),
            '10L105284N' => array('fl' => '86'),
            '11L105287N' => array('fl' => '86'),
            '11L105289N' => array('fl' => '86'),
            '10A105299N' => array('fl' => '86'),
            '10A105300N' => array('fl' => '86'),
            '10A105303N' => array('fl' => '86'),
            '13B105319N' => array('fl' => '86'),
            '14H105325N' => array('fl' => '86'),
            '12H105342N' => array('fl' => '86'),
            '14H105343N' => array('fl' => '86'),
            '13J105347N' => array('fl' => '86'),
            '14K105375N' => array('fl' => '86'),
            '14L105390N' => array('fl' => '86'),
            '13A105401N' => array('fl' => '86'),
            '13B105402N' => array('fl' => '86'),
            '16L105409N' => array('fl' => '86'),
            '14K105437N' => array('fl' => '86'),
            '14K105443N' => array('fl' => '86'),
            '13E105445N' => array('fl' => '86'),
            '15E105476N' => array('fl' => '86'),
            '15B105487N' => array('fl' => '86'),
            '16G105514N' => array('fl' => '86'),
            '12F999904Q' => array('fl' => '89'),
            '12A999911Q' => array('fl' => '89'),
            '18G500494O' => array('fl' => '85'),
            '14K500523O' => array('fl' => '86'),
            '16H020026C' => array('fl' => '82'),
            '11J020027C' => array('fl' => '82'),
            '13A055024J' => array('fl' => '85'),
            '19J055027J' => array('fl' => '82'),
            '09M500772O' => array('fl' => '82'),
            '14H500775O' => array('fl' => '90'),
            '09C500778O' => array('fl' => '82'),
            '08B500778O' => array('fl' => '82'),
            '14L500779O' => array('fl' => '82'),
            '09L500780O' => array('fl' => '82'),
            '08K500784O' => array('fl' => '82'),
            '12M500785O' => array('fl' => '92'),
            '19K500804O' => array('fl' => '86'),
            '19J500333O' => array('fl' => '92'),
            '19F500382O' => array('fl' => '82'),
            '18B500386O' => array('fl' => '86'),
            '18A500396O' => array('fl' => '86'),
            '14M500402O' => array('fl' => '92'),
            '16L500440O' => array('fl' => '90'),
            '14F500448O' => array('fl' => '92'),
            '12L500469O' => array('fl' => '92'),
            '17K102998N' => array('fl' => '90'),
            '19D103658N' => array('fl' => '90'),
            '19B103723N' => array('fl' => '89'),
            '18J103827N' => array('fl' => '90'),
            '17M500594O' => array('fl' => '85'),
            '14K105643N' => array('fl' => '86'),
            '19F500657O' => array('fl' => '86'),
            '14M500674O' => array('fl' => '86'),
            '17G500687O' => array('fl' => '86'),
            '17L500690O' => array('fl' => '86'),
            '16G500707O' => array('fl' => '86'),
            '18C500721O' => array('fl' => '86'),
            '20K500932O' => array('fl' => '90'),
            '20K500933O' => array('fl' => '90'),
            '19K500969O' => array('fl' => '86'),
            '13D000470A' => array('fl' => '89'),
            '20L108111N' => array('fl' => '86'),
            '20L108112N' => array('fl' => '92'),
            '20L501025O' => array('fl' => '86'),
            '20L108159N' => array('fl' => '90'),
            '20L108167N' => array('fl' => '85'),
            '20L501052O' => array('fl' => '89'),
            '20L108286N' => array('fl' => '92'),
            '20L501058O' => array('fl' => '90'),
            '20M501104O' => array('fl' => '92'),
            '21A108990N' => array('fl' => '86'),
            '21A109062N' => array('fl' => '89'),
            '21A109115N' => array('fl' => '89'),
            '21A109218N' => array('fl' => '90'),
            '21A109339N' => array('fl' => '90'),
            '21A109343N' => array('fl' => '85'),
            '21C110878N' => array('fl' => '85'),
            '21C110880N' => array('fl' => '92'),
            '21C110883N' => array('fl' => '92'),
            '21C110884N' => array('fl' => '92'),
            '21C111103N' => array('fl' => '92'),
            '21C111130N' => array('fl' => '90'),
            '21C111133N' => array('fl' => '90'),
            '21C111135N' => array('fl' => '92'),
            '19J103175N' => array('fl' => '90'),
            '19H103184N' => array('fl' => '90'),
            '19H103198N' => array('fl' => '90'),
            '19H103200N' => array('fl' => '92'),
            '19F103211N' => array('fl' => '86'),
            '19K103220N' => array('fl' => '89'),
            '19K103224N' => array('fl' => '90'),
            '19J103230N' => array('fl' => '90'),
            '19J103234N' => array('fl' => '92'),
            '19J103236N' => array('fl' => '89'),
            '19J103244N' => array('fl' => '92'),
            '19J103249N' => array('fl' => '92'),
            '19J103250N' => array('fl' => '86'),
            '19J103251N' => array('fl' => '92'),
            '19J103257N' => array('fl' => '92'),
            '19J103260N' => array('fl' => '92'),
            '19J103273N' => array('fl' => '90'),
            '19J103277N' => array('fl' => '90'),
            '19F103286N' => array('fl' => '92'),
            '19J103337N' => array('fl' => '90'),
            '19J103356N' => array('fl' => '86'),
            '19H103369N' => array('fl' => '92'),
            '19H103380N' => array('fl' => '90'),
            '19J103401N' => array('fl' => '90'),
            '19J103425N' => array('fl' => '92'),
            '19J103442N' => array('fl' => '92'),
            '19J103445N' => array('fl' => '90'),
            '19J103447N' => array('fl' => '92'),
            '19J103448N' => array('fl' => '90'),
            '19H103452N' => array('fl' => '90'),
            '19H103453N' => array('fl' => '89'),
            '19K103479N' => array('fl' => '92'),
            '19K103482N' => array('fl' => '92'),
            '19K103490N' => array('fl' => '90'),
            '19J103506N' => array('fl' => '89'),
            '19J103510N' => array('fl' => '92'),
            '19G103517N' => array('fl' => '92'),
            '19G103520N' => array('fl' => '92'),
            '19E103553N' => array('fl' => '92'),
            '19D103569N' => array('fl' => '90'),
            '19C103582N' => array('fl' => '90'),
            '19F103601N' => array('fl' => '89'),
            '19F103605N' => array('fl' => '89'),
            '19F103606N' => array('fl' => '89'),
            '19F103608N' => array('fl' => '90'),
            '19F103616N' => array('fl' => '90'),
            '19F103617N' => array('fl' => '92'),
            '19F103619N' => array('fl' => '90'),
            '19C103629N' => array('fl' => '90'),
            '19C103634N' => array('fl' => '92'),
            '19B103638N' => array('fl' => '92'),
            '19D103654N' => array('fl' => '90'),
            '19D103659N' => array('fl' => '90'),
            '19D103663N' => array('fl' => '90'),
            '19D103670N' => array('fl' => '89'),
            '18L103682N' => array('fl' => '90'),
            '18K103689N' => array('fl' => '90'),
            '18J103693N' => array('fl' => '90'),
            '19D103694N' => array('fl' => '92'),
            '19B103713N' => array('fl' => '92'),
            '19B103715N' => array('fl' => '90'),
            '19A103731N' => array('fl' => '90'),
            '19A103741N' => array('fl' => '90'),
            '18M103745N' => array('fl' => '90'),
            '18K103752N' => array('fl' => '92'),
            '18F103753N' => array('fl' => '90'),
            '18L103777N' => array('fl' => '92'),
            '18J103798N' => array('fl' => '89'),
            '18J103804N' => array('fl' => '92'),
            '18G103812N' => array('fl' => '89'),
            '18G103815N' => array('fl' => '90'),
            '18G103817N' => array('fl' => '90'),
            '18J103826N' => array('fl' => '89'),
            '18J103832N' => array('fl' => '89'),
            '18D103857N' => array('fl' => '90'),
            '18D103858N' => array('fl' => '92'),
            '18L103865N' => array('fl' => '90'),
            '18K103883N' => array('fl' => '92'),
            '18K103884N' => array('fl' => '92'),
            '18K103886N' => array('fl' => '92'),
            '18J103899N' => array('fl' => '90'),
            '18J103934N' => array('fl' => '90'),
            '18J103935N' => array('fl' => '90'),
            '18J103945N' => array('fl' => '90'),
            '18J103947N' => array('fl' => '90'),
            '18J103958N' => array('fl' => '92'),
            '18J103964N' => array('fl' => '89'),
            '18G103968N' => array('fl' => '92'),
            '18G103974N' => array('fl' => '89'),
            '17J103981N' => array('fl' => '90'),
            '17G103988N' => array('fl' => '90'),
            '17L103995N' => array('fl' => '89'),
            '17L104000N' => array('fl' => '89'),
            '17L104001N' => array('fl' => '89'),
            '17J104008N' => array('fl' => '92'),
            '17M104020N' => array('fl' => '89'),
            '18A104059N' => array('fl' => '92'),
            '18A104061N' => array('fl' => '89'),
            '18C104063N' => array('fl' => '92'),
            '18A104065N' => array('fl' => '92'),
            '18B104079N' => array('fl' => '92'),
            '18A104091N' => array('fl' => '89'),
            '18D104098N' => array('fl' => '90'),
            '18C104101N' => array('fl' => '89'),
            '19C104108N' => array('fl' => '90'),
            '18C104113N' => array('fl' => '92'),
            '17M104122N' => array('fl' => '92'),
            '17M104128N' => array('fl' => '89'),
            '17L104151N' => array('fl' => '89'),
            '17L104152N' => array('fl' => '90'),
            '18A104161N' => array('fl' => '90'),
            '18A104165N' => array('fl' => '92'),
            '17M104167N' => array('fl' => '92'),
            '17M104169N' => array('fl' => '90'),
            '17L104181N' => array('fl' => '89'),
            '18C104184N' => array('fl' => '89'),
            '17L104186N' => array('fl' => '90'),
            '17H104197N' => array('fl' => '90'),
            '17D104210N' => array('fl' => '90'),
            '16J104226N' => array('fl' => '90'),
            '17G104229N' => array('fl' => '89'),
            '16K104239N' => array('fl' => '92'),
            '16G104242N' => array('fl' => '92'),
            '17J104246N' => array('fl' => '90'),
            '17G104248N' => array('fl' => '89'),
            '16K104254N' => array('fl' => '90'),
            '17K104260N' => array('fl' => '90'),
            '17K104263N' => array('fl' => '89'),
            '16K104266N' => array('fl' => '92'),
            '17L104273N' => array('fl' => '90'),
            '17J104279N' => array('fl' => '90'),
            '17F104295N' => array('fl' => '92'),
            '17L104311N' => array('fl' => '89'),
            '16K104327N' => array('fl' => '90'),
            '16J104330N' => array('fl' => '90'),
            '17G104332N' => array('fl' => '92'),
            '16D104336N' => array('fl' => '89'),
            '17K104345N' => array('fl' => '86'),
            '17K104350N' => array('fl' => '92'),
            '16E104351N' => array('fl' => '92'),
            '17J104353N' => array('fl' => '90'),
            '17J104361N' => array('fl' => '92'),
            '17J104365N' => array('fl' => '90'),
            '17J104369N' => array('fl' => '92'),
            '15K104375N' => array('fl' => '92'),
            '15E104385N' => array('fl' => '92'),
            '16K104391N' => array('fl' => '90'),
            '16A104456N' => array('fl' => '89'),
            '16A104480N' => array('fl' => '86'),
            '15M104483N' => array('fl' => '90'),
            '16C104492N' => array('fl' => '90'),
            '15K104497N' => array('fl' => '92'),
            '17G104551N' => array('fl' => '92'),
            '17J104567N' => array('fl' => '92'),
            '16K104569N' => array('fl' => '92'),
            '16E104613N' => array('fl' => '90'),
            '16D104619N' => array('fl' => '92'),
            '16D104634N' => array('fl' => '90'),
            '16D104636N' => array('fl' => '92'),
            '16D104638N' => array('fl' => '92'),
            '15L104641N' => array('fl' => '92'),
            '16B104660N' => array('fl' => '90'),
            '15B104667N' => array('fl' => '92'),
            '15K104673N' => array('fl' => '89'),
            '15K104675N' => array('fl' => '92'),
            '15A104685N' => array('fl' => '90'),
            '16B104689N' => array('fl' => '92'),
            '15A104706N' => array('fl' => '92'),
            '16C104711N' => array('fl' => '92'),
            '16C104716N' => array('fl' => '92'),
            '16C104719N' => array('fl' => '92'),
            '15D104728N' => array('fl' => '92'),
            '15C104732N' => array('fl' => '90'),
            '15C104734N' => array('fl' => '92'),
            '16E104740N' => array('fl' => '90'),
            '16K104751N' => array('fl' => '90'),
            '16J104754N' => array('fl' => '92'),
            '16G104765N' => array('fl' => '89'),
            '16D104768N' => array('fl' => '92'),
            '16D104770N' => array('fl' => '92'),
            '14F104776N' => array('fl' => '92'),
            '14A104780N' => array('fl' => '89'),
            '14A104781N' => array('fl' => '90'),
            '15A104784N' => array('fl' => '90'),
            '15A104786N' => array('fl' => '90'),
            '15A104787N' => array('fl' => '90'),
            '14M104789N' => array('fl' => '92'),
            '15A104795N' => array('fl' => '90'),
            '16J104809N' => array('fl' => '90'),
            '14L104811N' => array('fl' => '92'),
            '16A104824N' => array('fl' => '90'),
            '14L104835N' => array('fl' => '90'),
            '14B104841N' => array('fl' => '90'),
            '15D104845N' => array('fl' => '90'),
            '15C104860N' => array('fl' => '90'),
            '15A104861N' => array('fl' => '90'),
            '14L104865N' => array('fl' => '90'),
            '14L104868N' => array('fl' => '90'),
            '17A104871N' => array('fl' => '90'),
            '14B104877N' => array('fl' => '86'),
            '16B104879N' => array('fl' => '90'),
            '17A104887N' => array('fl' => '90'),
            '15A104888N' => array('fl' => '86'),
            '14K104892N' => array('fl' => '92'),
            '14K104894N' => array('fl' => '92'),
            '14K104896N' => array('fl' => '92'),
            '15D104904N' => array('fl' => '92'),
            '16L104909N' => array('fl' => '90'),
            '16A104910N' => array('fl' => '90'),
            '17A104914N' => array('fl' => '90'),
            '14L104921N' => array('fl' => '92'),
            '14J104926N' => array('fl' => '92'),
            '13L104929N' => array('fl' => '90'),
            '13J104930N' => array('fl' => '90'),
            '13D104932N' => array('fl' => '90'),
            '13C104934N' => array('fl' => '92'),
            '17E104937N' => array('fl' => '90'),
            '12H104940N' => array('fl' => '90'),
            '13A104943N' => array('fl' => '90'),
            '13F104955N' => array('fl' => '92'),
            '13B104956N' => array('fl' => '92'),
            '13B104957N' => array('fl' => '90'),
            '12L104962N' => array('fl' => '92'),
            '12L104965N' => array('fl' => '90'),
            '12G104966N' => array('fl' => '92'),
            '12L104967N' => array('fl' => '86'),
            '12L104969N' => array('fl' => '90'),
            '12L104970N' => array('fl' => '92'),
            '12L104973N' => array('fl' => '92'),
            '12E104974N' => array('fl' => '92'),
            '15K104982N' => array('fl' => '92'),
            '15K104987N' => array('fl' => '92'),
            '12G104993N' => array('fl' => '92'),
            '12L105013N' => array('fl' => '92'),
            '13H105020N' => array('fl' => '90'),
            '13L105021N' => array('fl' => '92'),
            '14H105023N' => array('fl' => '92'),
            '13L105032N' => array('fl' => '92'),
            '13K105034N' => array('fl' => '92'),
            '13K105035N' => array('fl' => '92'),
            '16H105041N' => array('fl' => '92'),
            '13K105042N' => array('fl' => '92'),
            '14F105043N' => array('fl' => '92'),
            '13J105049N' => array('fl' => '90'),
            '11M105052N' => array('fl' => '92'),
            '11L105056N' => array('fl' => '92'),
            '12G105057N' => array('fl' => '92'),
            '11L105063N' => array('fl' => '92'),
            '11J105073N' => array('fl' => '90'),
            '11J105083N' => array('fl' => '92'),
            '12L105086N' => array('fl' => '86'),
            '13E105091N' => array('fl' => '90'),
            '12G105092N' => array('fl' => '90'),
            '12M105100N' => array('fl' => '92'),
            '14K105101N' => array('fl' => '92'),
            '09A105105N' => array('fl' => '90'),
            '09M105107N' => array('fl' => '92'),
            '09M105109N' => array('fl' => '92'),
            '10A105112N' => array('fl' => '90'),
            '12H105113N' => array('fl' => '90'),
            '12M105122N' => array('fl' => '86'),
            '12M105124N' => array('fl' => '90'),
            '11F105128N' => array('fl' => '92'),
            '11C105129N' => array('fl' => '92'),
            '12A105135N' => array('fl' => '90'),
            '12L105136N' => array('fl' => '86'),
            '13J105138N' => array('fl' => '90'),
            '11C105157N' => array('fl' => '90'),
            '11B105162N' => array('fl' => '90'),
            '09E105163N' => array('fl' => '92'),
            '10J105165N' => array('fl' => '92'),
            '09M105169N' => array('fl' => '92'),
            '09M105171N' => array('fl' => '86'),
            '14H105179N' => array('fl' => '90'),
            '08H105181N' => array('fl' => '92'),
            '11A105186N' => array('fl' => '92'),
            '09M105204N' => array('fl' => '86'),
            '08A105208N' => array('fl' => '92'),
            '13D105215N' => array('fl' => '90'),
            '08A105228N' => array('fl' => '90'),
            '08M105239N' => array('fl' => '92'),
            '19K105871N' => array('fl' => '90'),
            '19K105872N' => array('fl' => '90'),
            '19K105873N' => array('fl' => '89'),
            '19K105882N' => array('fl' => '90'),
            '19K105886N' => array('fl' => '92'),
            '19K105889N' => array('fl' => '89'),
            '19K105902N' => array('fl' => '89'),
            '20K106945N' => array('fl' => '92'),
            '20K106962N' => array('fl' => '92'),
            '20K106969N' => array('fl' => '92'),
            '20K106973N' => array('fl' => '92'),
            '20K106982N' => array('fl' => '92'),
            '20K106997N' => array('fl' => '89'),
            '20K107001N' => array('fl' => '92'),
            '20K107006N' => array('fl' => '89'),
            '20K107008N' => array('fl' => '90'),
            '20K107013N' => array('fl' => '92'),
            '20K107027N' => array('fl' => '90'),
            '20K107028N' => array('fl' => '92'),
            '20K107038N' => array('fl' => '90'),
            '20K107040N' => array('fl' => '90'),
            '20K107046N' => array('fl' => '89'),
            '20K107048N' => array('fl' => '92'),
            '20K107050N' => array('fl' => '90'),
            '20K107051N' => array('fl' => '90'),
            '20K107066N' => array('fl' => '92'),
            '20K107089N' => array('fl' => '92'),
            '20K107103N' => array('fl' => '92'),
            '20K107139N' => array('fl' => '89'),
            '20K107144N' => array('fl' => '89'),
            '20K107149N' => array('fl' => '92'),
            '20K107152N' => array('fl' => '90'),
            '20K107153N' => array('fl' => '90'),
            '20K107154N' => array('fl' => '89'),
            '20K107165N' => array('fl' => '90'),
            '20K107170N' => array('fl' => '92'),
            '20K107174N' => array('fl' => '92'),
            '20K107175N' => array('fl' => '90'),
            '20K107184N' => array('fl' => '92'),
            '20K107188N' => array('fl' => '92'),
            '20K107192N' => array('fl' => '92'),
            '20K107193N' => array('fl' => '90'),
            '20K107200N' => array('fl' => '90'),
            '20K107201N' => array('fl' => '90'),
            '20K107206N' => array('fl' => '92'),
            '20K107211N' => array('fl' => '86'),
            '20K107214N' => array('fl' => '92'),
            '20K107215N' => array('fl' => '90'),
            '20K107220N' => array('fl' => '92'),
            '20K107224N' => array('fl' => '90'),
            '20K107225N' => array('fl' => '89'),
            '20K107230N' => array('fl' => '92'),
            '20K107237N' => array('fl' => '90'),
            '20K107250N' => array('fl' => '90'),
            '20K107256N' => array('fl' => '92'),
            '20K107257N' => array('fl' => '92'),
            '20K107259N' => array('fl' => '90'),
            '20J107292N' => array('fl' => '90'),
            '20J107295N' => array('fl' => '92'),
            '20J107302N' => array('fl' => '89'),
            '20J107305N' => array('fl' => '90'),
            '20J107311N' => array('fl' => '92'),
            '20J107313N' => array('fl' => '90'),
            '20J107319N' => array('fl' => '92'),
            '20J107324N' => array('fl' => '92'),
            '20J107326N' => array('fl' => '92'),
            '20J107333N' => array('fl' => '92'),
            '20J107336N' => array('fl' => '92'),
            '19M107464N' => array('fl' => '90'),
            '19M107474N' => array('fl' => '86'),
            '19M107477N' => array('fl' => '89'),
            '19M107487N' => array('fl' => '90'),
            '19M107490N' => array('fl' => '89'),
            '19M107491N' => array('fl' => '90'),
            '19M107493N' => array('fl' => '92'),
            '19M107494N' => array('fl' => '92'),
            '19M107496N' => array('fl' => '89'),
            '19M107497N' => array('fl' => '92'),
            '19M107498N' => array('fl' => '90'),
            '19M107502N' => array('fl' => '92'),
            '19M107507N' => array('fl' => '92'),
            '19M107510N' => array('fl' => '92'),
            '19M107519N' => array('fl' => '92'),
            '19M107528N' => array('fl' => '90'),
            '19M107532N' => array('fl' => '92'),
            '19M107536N' => array('fl' => '89'),
            '19M107538N' => array('fl' => '90'),
            '19L107543N' => array('fl' => '90'),
            '19L107546N' => array('fl' => '90'),
            '19L107550N' => array('fl' => '89'),
            '19L107555N' => array('fl' => '92'),
            '19L107591N' => array('fl' => '92'),
            '19L107595N' => array('fl' => '92'),
            '19L107599N' => array('fl' => '92'),
            '19L107601N' => array('fl' => '92'),
            '19L107602N' => array('fl' => '92'),
            '19L107604N' => array('fl' => '92'),
            '19L107613N' => array('fl' => '89'),
            '19L107614N' => array('fl' => '92'),
            '19L107619N' => array('fl' => '90'),
            '19L107621N' => array('fl' => '90'),
            '19K107622N' => array('fl' => '90'),
            '08F107639N' => array('fl' => '90'),
            '20K107673N' => array('fl' => '90'),
            '20L107730N' => array('fl' => '90'),
            '20L107736N' => array('fl' => '90'),
            '20L107738N' => array('fl' => '89'),
            '20L107746N' => array('fl' => '92'),
            '20L107750N' => array('fl' => '90'),
            '20L107758N' => array('fl' => '90'),
            '20L107761N' => array('fl' => '90'),
            '20L107764N' => array('fl' => '90'),
            '20L107765N' => array('fl' => '90'),
            '20L107768N' => array('fl' => '89'),
            '20L107771N' => array('fl' => '92'),
            '20L107781N' => array('fl' => '90'),
            '20L107803N' => array('fl' => '90'),
            '20L107806N' => array('fl' => '90'),
            '20L107845N' => array('fl' => '89'),
            '20L107849N' => array('fl' => '89'),
            '20L107867N' => array('fl' => '89'),
            '20L107874N' => array('fl' => '86'),
            '20L107898N' => array('fl' => '92'),
            '20L107901N' => array('fl' => '92'),
            '20L107909N' => array('fl' => '90'),
            '20L107919N' => array('fl' => '92'),
            '20L107961N' => array('fl' => '92'),
            '20L108036N' => array('fl' => '89'),
            '20L108037N' => array('fl' => '92'),
            '20L108045N' => array('fl' => '92'),
            '20L108048N' => array('fl' => '92'),
            '16B108070N' => array('fl' => '92'),
            '20L108124N' => array('fl' => '90'),
            '20L108139N' => array('fl' => '90'),
            '20L108141N' => array('fl' => '90'),
            '20L108143N' => array('fl' => '92'),
            '20L108145N' => array('fl' => '90'),
            '20L108181N' => array('fl' => '90'),
            '20L108225N' => array('fl' => '90'),
            '20L108234N' => array('fl' => '90'),
            '20L108261N' => array('fl' => '92'),
            '20L108263N' => array('fl' => '92'),
            '20L108294N' => array('fl' => '90'),
            '20M108401N' => array('fl' => '90'),
            '20M108409N' => array('fl' => '90'),
            '20M108427N' => array('fl' => '90'),
            '20M108431N' => array('fl' => '89'),
            '20M108435N' => array('fl' => '92'),
            '20M108440N' => array('fl' => '92'),
            '20M108453N' => array('fl' => '92'),
            '20M108500N' => array('fl' => '92'),
            '20M108501N' => array('fl' => '89'),
            '20B108555N' => array('fl' => '90'),
            '20B108560N' => array('fl' => '90'),
            '20E108630N' => array('fl' => '90'),
            '20M108648N' => array('fl' => '92'),
            '20M108649N' => array('fl' => '92'),
            '20M108659N' => array('fl' => '92'),
            '20M108661N' => array('fl' => '89'),
            '20M108722N' => array('fl' => '92'),
            '20M108724N' => array('fl' => '92'),
            '20M108818N' => array('fl' => '92'),
            '20M108824N' => array('fl' => '89'),
            '21A108913N' => array('fl' => '92'),
            '21A108922N' => array('fl' => '90'),
            '21A108924N' => array('fl' => '89'),
            '21A108933N' => array('fl' => '89'),
            '21A108938N' => array('fl' => '92'),
            '21A108949N' => array('fl' => '90'),
            '21A108987N' => array('fl' => '89'),
            '21A109000N' => array('fl' => '92'),
            '21A109010N' => array('fl' => '90'),
            '21A109012N' => array('fl' => '92'),
            '21A109017N' => array('fl' => '90'),
            '21A109019N' => array('fl' => '90'),
            '21A109020N' => array('fl' => '90'),
            '21A109021N' => array('fl' => '92'),
            '21A109035N' => array('fl' => '89'),
            '21A109042N' => array('fl' => '89'),
            '21A109046N' => array('fl' => '92'),
            '21A109049N' => array('fl' => '92'),
            '21A109054N' => array('fl' => '92'),
            '21A109065N' => array('fl' => '92'),
            '21A109120N' => array('fl' => '90'),
            '21A109121N' => array('fl' => '90'),
            '21A109124N' => array('fl' => '92'),
            '21A109136N' => array('fl' => '92'),
            '21A109141N' => array('fl' => '89'),
            '21A109147N' => array('fl' => '90'),
            '21A109174N' => array('fl' => '92'),
            '21A109176N' => array('fl' => '90'),
            '21A109179N' => array('fl' => '92'),
            '21A109197N' => array('fl' => '90'),
            '21A109235N' => array('fl' => '90'),
            '21A109247N' => array('fl' => '92'),
            '21A109248N' => array('fl' => '90'),
            '21A109261N' => array('fl' => '92'),
            '21A109273N' => array('fl' => '90'),
            '21A109289N' => array('fl' => '92'),
            '21A109308N' => array('fl' => '92'),
            '21B109422N' => array('fl' => '92'),
            '21B109432N' => array('fl' => '90'),
            '88D109440N' => array('fl' => '90'),
            '21B109442N' => array('fl' => '90'),
            '21B110244N' => array('fl' => '92'),
            '21B110247N' => array('fl' => '92'),
            '21B110248N' => array('fl' => '90'),
            '21B110279N' => array('fl' => '90'),
            '21B110284N' => array('fl' => '89'),
            '21B110295N' => array('fl' => '90'),
            '21B110323N' => array('fl' => '92'),
            '21B110326N' => array('fl' => '90'),
            '21B110336N' => array('fl' => '89'),
            '21B110340N' => array('fl' => '92'),
            '21B110348N' => array('fl' => '89'),
            '21B110354N' => array('fl' => '92'),
            '21B110358N' => array('fl' => '92'),
            '21B110369N' => array('fl' => '90'),
            '21B110374N' => array('fl' => '92'),
            '21B110377N' => array('fl' => '90'),
            '21B110389N' => array('fl' => '90'),
            '21B110399N' => array('fl' => '92'),
            '21B110401N' => array('fl' => '92'),
            '21B110402N' => array('fl' => '90'),
            '21C110649N' => array('fl' => '90'),
            '21C110667N' => array('fl' => '92'),
            '21C110671N' => array('fl' => '90'),
            '21C110682N' => array('fl' => '92'),
            '21C110688N' => array('fl' => '92'),
            '21C110691N' => array('fl' => '90'),
            '21C110704N' => array('fl' => '92'),
            '21C110719N' => array('fl' => '92'),
            '21C110725N' => array('fl' => '89'),
            '21C110730N' => array('fl' => '90'),
            '21C110744N' => array('fl' => '89'),
            '21C110745N' => array('fl' => '90'),
            '21C110746N' => array('fl' => '92'),
            '21C110752N' => array('fl' => '89'),
            '21C110758N' => array('fl' => '92'),
            '21C110760N' => array('fl' => '90'),
            '21C110763N' => array('fl' => '90'),
            '21C110777N' => array('fl' => '90'),
            '21C110799N' => array('fl' => '92'),
            '21C110805N' => array('fl' => '90'),
            '21C110824N' => array('fl' => '89'),
            '21C110827N' => array('fl' => '92'),
            '21C110839N' => array('fl' => '92'),
            '21C110849N' => array('fl' => '89'),
            '21C110865N' => array('fl' => '89'),
            '21C110868N' => array('fl' => '89'),
            '21A110890N' => array('fl' => '92'),
            '21C110891N' => array('fl' => '89'),
            '21A110892N' => array('fl' => '92'),
            '21C110893N' => array('fl' => '90'),
            '21C110900N' => array('fl' => '89'),
            '21C110906N' => array('fl' => '89'),
            '21C110953N' => array('fl' => '89'),
            '21C110954N' => array('fl' => '89'),
            '21C110955N' => array('fl' => '90'),
            '21C110963N' => array('fl' => '90'),
            '21C110965N' => array('fl' => '90'),
            '21C111093N' => array('fl' => '92'),
            '21C111124N' => array('fl' => '89'),
            '21C111161N' => array('fl' => '90'),
            '10C105307N' => array('fl' => '86'),
            '13F105346N' => array('fl' => '86'),
            '15H105360N' => array('fl' => '86'),
            '18G105658N' => array('fl' => '86'),
            '19G103204N' => array('fl' => '92'),
            '19F103213N' => array('fl' => '90'),
            '19J103239N' => array('fl' => '92'),
            '19J103248N' => array('fl' => '92'),
            '19J103271N' => array('fl' => '92'),
            '19F103283N' => array('fl' => '92'),
            '19F103306N' => array('fl' => '89'),
            '19F103312N' => array('fl' => '89'),
            '19K103330N' => array('fl' => '89'),
            '19J103339N' => array('fl' => '92'),
            '19K103388N' => array('fl' => '89'),
            '19G103403N' => array('fl' => '89'),
            '19H103455N' => array('fl' => '89'),
            '19K103477N' => array('fl' => '89'),
            '19J103508N' => array('fl' => '89'),
            '19F103540N' => array('fl' => '92'),
            '19F103549N' => array('fl' => '92'),
            '19D103568N' => array('fl' => '89'),
            '18F103594N' => array('fl' => '92'),
            '19F103614N' => array('fl' => '89'),
            '19F103620N' => array('fl' => '92'),
            '18J103644N' => array('fl' => '89'),
            '18J103646N' => array('fl' => '92'),
            '19B103703N' => array('fl' => '92'),
            '19B103707N' => array('fl' => '92'),
            '18K103750N' => array('fl' => '89'),
            '18L103782N' => array('fl' => '89'),
            '18K103871N' => array('fl' => '92'),
            '18G103921N' => array('fl' => '92'),
            '18J103929N' => array('fl' => '90'),
            '17G103992N' => array('fl' => '90'),
            '17K104005N' => array('fl' => '90'),
            '17M104022N' => array('fl' => '92'),
            '18B104049N' => array('fl' => '89'),
            '17M104134N' => array('fl' => '89'),
            '17M104139N' => array('fl' => '89'),
            '18C104142N' => array('fl' => '89'),
            '17L104213N' => array('fl' => '89'),
            '16L104233N' => array('fl' => '89'),
            '17G104247N' => array('fl' => '89'),
            '17L104277N' => array('fl' => '90'),
            '16K104304N' => array('fl' => '92'),
            '16L104324N' => array('fl' => '92'),
            '16K104325N' => array('fl' => '89'),
            '16K104331N' => array('fl' => '92'),
            '18B104338N' => array('fl' => '89'),
            '16C104364N' => array('fl' => '92'),
            '16D104367N' => array('fl' => '89'),
            '17G104376N' => array('fl' => '90'),
            '15M104395N' => array('fl' => '92'),
            '16K104398N' => array('fl' => '89'),
            '17A104432N' => array('fl' => '92'),
            '15L104469N' => array('fl' => '90'),
            '17L104599N' => array('fl' => '89'),
            '16K104621N' => array('fl' => '92'),
            '16E104630N' => array('fl' => '92'),
            '15C104733N' => array('fl' => '92'),
            '17A104744N' => array('fl' => '90'),
            '16H104775N' => array('fl' => '89'),
            '15A104796N' => array('fl' => '90'),
            '15A104798N' => array('fl' => '90'),
            '16A104821N' => array('fl' => '92'),
            '16A104826N' => array('fl' => '90'),
            '16A104842N' => array('fl' => '92'),
            '16A104862N' => array('fl' => '90'),
            '16A104900N' => array('fl' => '90'),
            '13L104908N' => array('fl' => '90'),
            '17A104913N' => array('fl' => '92'),
            '14L104918N' => array('fl' => '90'),
            '14L104925N' => array('fl' => '92'),
            '13L104931N' => array('fl' => '90'),
            '12C104948N' => array('fl' => '92'),
            '12C104951N' => array('fl' => '90'),
            '12H104954N' => array('fl' => '90'),
            '12K104975N' => array('fl' => '90'),
            '12E104983N' => array('fl' => '90'),
            '12L104984N' => array('fl' => '90'),
            '13A104989N' => array('fl' => '92'),
            '12F104991N' => array('fl' => '92'),
            '12F104996N' => array('fl' => '92'),
            '13A105004N' => array('fl' => '90'),
            '12C105005N' => array('fl' => '92'),
            '12F105006N' => array('fl' => '90'),
            '12C105009N' => array('fl' => '90'),
            '12L105011N' => array('fl' => '90'),
            '13L105014N' => array('fl' => '92'),
            '13E105027N' => array('fl' => '92'),
            '13K105036N' => array('fl' => '92'),
            '16H105040N' => array('fl' => '89'),
            '11L105058N' => array('fl' => '92'),
            '11M105059N' => array('fl' => '92'),
            '11L105060N' => array('fl' => '92'),
            '15K105061N' => array('fl' => '92'),
            '11L105062N' => array('fl' => '92'),
            '11L105064N' => array('fl' => '92'),
            '11K105070N' => array('fl' => '90'),
            '11K105071N' => array('fl' => '92'),
            '11J105077N' => array('fl' => '92'),
            '11J105078N' => array('fl' => '92'),
            '11J105081N' => array('fl' => '90'),
            '14K105102N' => array('fl' => '92'),
            '11G105118N' => array('fl' => '92'),
            '11E105130N' => array('fl' => '92'),
            '11E105132N' => array('fl' => '92'),
            '11E105137N' => array('fl' => '92'),
            '12A105140N' => array('fl' => '90'),
            '11J105142N' => array('fl' => '92'),
            '11D105145N' => array('fl' => '92'),
            '11D105146N' => array('fl' => '92'),
            '11D105155N' => array('fl' => '92'),
            '11A105156N' => array('fl' => '92'),
            '09L105175N' => array('fl' => '92'),
            '08M105176N' => array('fl' => '92'),
            '10C105177N' => array('fl' => '92'),
            '10M105183N' => array('fl' => '92'),
            '09M105188N' => array('fl' => '92'),
            '09M105193N' => array('fl' => '92'),
            '09L105194N' => array('fl' => '90'),
            '12M105199N' => array('fl' => '90'),
            '09G105201N' => array('fl' => '92'),
            '13E105206N' => array('fl' => '90'),
            '10C105210N' => array('fl' => '90'),
            '09M105213N' => array('fl' => '92'),
            '10C105214N' => array('fl' => '92'),
            '10F105217N' => array('fl' => '92'),
            '10G105222N' => array('fl' => '90'),
            '09A105230N' => array('fl' => '92'),
            '09C105232N' => array('fl' => '90'),
            '08H105237N' => array('fl' => '92'),
            '12L105846N' => array('fl' => '90'),
            '19K105849N' => array('fl' => '89'),
            '19K105851N' => array('fl' => '92'),
            '19K105853N' => array('fl' => '89'),
            '19K105855N' => array('fl' => '92'),
            '19K105857N' => array('fl' => '92'),
            '19K105858N' => array('fl' => '92'),
            '19K105861N' => array('fl' => '89'),
            '19K105868N' => array('fl' => '92'),
            '20K106971N' => array('fl' => '92'),
            '20K106977N' => array('fl' => '89'),
            '20K106986N' => array('fl' => '89'),
            '20K107015N' => array('fl' => '92'),
            '20K107022N' => array('fl' => '90'),
            '20K107025N' => array('fl' => '92'),
            '20K107035N' => array('fl' => '92'),
            '20K107036N' => array('fl' => '90'),
            '20K107041N' => array('fl' => '92'),
            '20K107054N' => array('fl' => '92'),
            '20K107068N' => array('fl' => '92'),
            '20K107069N' => array('fl' => '92'),
            '20K107085N' => array('fl' => '92'),
            '20K107091N' => array('fl' => '89'),
            '20K107093N' => array('fl' => '90'),
            '20K107118N' => array('fl' => '92'),
            '20K107145N' => array('fl' => '92'),
            '20K107146N' => array('fl' => '92'),
            '20K107164N' => array('fl' => '90'),
            '20K107168N' => array('fl' => '90'),
            '20K107210N' => array('fl' => '92'),
            '20K107213N' => array('fl' => '90'),
            '20K107217N' => array('fl' => '90'),
            '20K107218N' => array('fl' => '89'),
            '20K107240N' => array('fl' => '92'),
            '20K107241N' => array('fl' => '90'),
            '20K107244N' => array('fl' => '90'),
            '20K107249N' => array('fl' => '92'),
            '20K107265N' => array('fl' => '92'),
            '20J107284N' => array('fl' => '89'),
            '20J107286N' => array('fl' => '92'),
            '20J107299N' => array('fl' => '89'),
            '20J107303N' => array('fl' => '92'),
            '20J107304N' => array('fl' => '92'),
            '20J107310N' => array('fl' => '90'),
            '20J107322N' => array('fl' => '90'),
            '20J107327N' => array('fl' => '92'),
            '20J107332N' => array('fl' => '90'),
            '20J107334N' => array('fl' => '92'),
            '20J107338N' => array('fl' => '89'),
            '20A107360N' => array('fl' => '92'),
            '19M107373N' => array('fl' => '92'),
            '19M107375N' => array('fl' => '92'),
            '19M107473N' => array('fl' => '92'),
            '19M107488N' => array('fl' => '92'),
            '19M107495N' => array('fl' => '89'),
            '19M107501N' => array('fl' => '89'),
            '19M107509N' => array('fl' => '92'),
            '19M107520N' => array('fl' => '89'),
            '19M107537N' => array('fl' => '92'),
            '19L107549N' => array('fl' => '89'),
            '19L107553N' => array('fl' => '90'),
            '19L107557N' => array('fl' => '89'),
            '19L107568N' => array('fl' => '92'),
            '19L107574N' => array('fl' => '92'),
            '19L107611N' => array('fl' => '90'),
            '20L107742N' => array('fl' => '92'),
            '20L107747N' => array('fl' => '92'),
            '20L107756N' => array('fl' => '90'),
            '20L107759N' => array('fl' => '92'),
            '20L107769N' => array('fl' => '89'),
            '20L107776N' => array('fl' => '92'),
            '20L107778N' => array('fl' => '92'),
            '20L107780N' => array('fl' => '92'),
            '20L107793N' => array('fl' => '92'),
            '20L107798N' => array('fl' => '89'),
            '20L107805N' => array('fl' => '92'),
            '20L107848N' => array('fl' => '92'),
            '20L107868N' => array('fl' => '89'),
            '20L107880N' => array('fl' => '92'),
            '20L107896N' => array('fl' => '92'),
            '20L107904N' => array('fl' => '92'),
            '20L107943N' => array('fl' => '92'),
            '20L107955N' => array('fl' => '89'),
            '20L107970N' => array('fl' => '92'),
            '20L108035N' => array('fl' => '90'),
            '20L108038N' => array('fl' => '92'),
            '20L108040N' => array('fl' => '92'),
            '20L108052N' => array('fl' => '90'),
            '20L108054N' => array('fl' => '89'),
            '20L108122N' => array('fl' => '89'),
            '20L108123N' => array('fl' => '92'),
            '20L108137N' => array('fl' => '89'),
            '20L108229N' => array('fl' => '90'),
            '20L108281N' => array('fl' => '90'),
            '20M108404N' => array('fl' => '92'),
            '20M108416N' => array('fl' => '92'),
            '20M108419N' => array('fl' => '89'),
            '20M108438N' => array('fl' => '92'),
            '20M108462N' => array('fl' => '89'),
            '20M108468N' => array('fl' => '92'),
            '20B108545N' => array('fl' => '90'),
            '20E108609N' => array('fl' => '92'),
            '20M108663N' => array('fl' => '90'),
            '20M108680N' => array('fl' => '92'),
            '20M108681N' => array('fl' => '90'),
            '20M108699N' => array('fl' => '92'),
            '20M108702N' => array('fl' => '92'),
            '20M108709N' => array('fl' => '89'),
            '20M108723N' => array('fl' => '92'),
            '20M108838N' => array('fl' => '92'),
            '20M108841N' => array('fl' => '92'),
            '21A108915N' => array('fl' => '92'),
            '21A108926N' => array('fl' => '90'),
            '21A108962N' => array('fl' => '92'),
            '21A108980N' => array('fl' => '89'),
            '21A109011N' => array('fl' => '92'),
            '21A109018N' => array('fl' => '89'),
            '21A109055N' => array('fl' => '92'),
            '21A109060N' => array('fl' => '89'),
            '21A109077N' => array('fl' => '89'),
            '21A109083N' => array('fl' => '89'),
            '21A109089N' => array('fl' => '89'),
            '21A109116N' => array('fl' => '89'),
            '21A109123N' => array('fl' => '92'),
            '21A109149N' => array('fl' => '90'),
            '21A109153N' => array('fl' => '90'),
            '21A109182N' => array('fl' => '92'),
            '21A109191N' => array('fl' => '92'),
            '21A109203N' => array('fl' => '92'),
            '21A109242N' => array('fl' => '92'),
            '21A109290N' => array('fl' => '92'),
            '21A109302N' => array('fl' => '92'),
            '21B109413N' => array('fl' => '92'),
            '21B109414N' => array('fl' => '89'),
            '21B109426N' => array('fl' => '92'),
            '21B109427N' => array('fl' => '92'),
            '21B109429N' => array('fl' => '92'),
            '21B109430N' => array('fl' => '92'),
            '21B109433N' => array('fl' => '92'),
            '21B110240N' => array('fl' => '92'),
            '21B110267N' => array('fl' => '92'),
            '21B110268N' => array('fl' => '92'),
            '21B110275N' => array('fl' => '89'),
            '21B110276N' => array('fl' => '92'),
            '21B110283N' => array('fl' => '92'),
            '21B110306N' => array('fl' => '92'),
            '21H110314N' => array('fl' => '89'),
            '21B110366N' => array('fl' => '92'),
            '21B110367N' => array('fl' => '92'),
            '21B110380N' => array('fl' => '89'),
            '21B110387N' => array('fl' => '92'),
            '21C110582N' => array('fl' => '90'),
            '21C110668N' => array('fl' => '92'),
            '21C110673N' => array('fl' => '92'),
            '21C110678N' => array('fl' => '92'),
            '21C110681N' => array('fl' => '92'),
            '21C110687N' => array('fl' => '90'),
            '21C110689N' => array('fl' => '90'),
            '21C110692N' => array('fl' => '89'),
            '21C110701N' => array('fl' => '92'),
            '21C110716N' => array('fl' => '92'),
            '21C110718N' => array('fl' => '92'),
            '21C110751N' => array('fl' => '89'),
            '21C110780N' => array('fl' => '92'),
            '21C110798N' => array('fl' => '92'),
            '21C110830N' => array('fl' => '92'),
            '21C110845N' => array('fl' => '92'),
            '21C110847N' => array('fl' => '92'),
            '21C110854N' => array('fl' => '92'),
            '21A110875N' => array('fl' => '92'),
            '21C110887N' => array('fl' => '92'),
            '21C110898N' => array('fl' => '89'),
            '21C110904N' => array('fl' => '92'),
            '21C110921N' => array('fl' => '89'),
            '21C110968N' => array('fl' => '89'),
            '21C111120N' => array('fl' => '89'),
            '16B000328A' => array('fl' => '89'),
            '13J000341A' => array('fl' => '92'),
            '10F000352A' => array('fl' => '92'),
            '10M000354A' => array('fl' => '90'),
            '15L000356A' => array('fl' => '89'),
            '21A000512A' => array('fl' => '86'),
            '19H104725N' => array('fl' => '86'),
            '15J105482N' => array('fl' => '86'),
            '16H105655N' => array('fl' => '86'),
            '17M105661N' => array('fl' => '86'),
            '17M105680N' => array('fl' => '86'),
            '18F105701N' => array('fl' => '86'),
            '18E105707N' => array('fl' => '86'),
            '17L105777N' => array('fl' => '86'),
            '19F105798N' => array('fl' => '86'),
            '19M107381N' => array('fl' => '86'),
            '19M107383N' => array('fl' => '86'),
            '19M107437N' => array('fl' => '86'),
            '19M107438N' => array('fl' => '86'),
            '19L107570N' => array('fl' => '86'),
            '19L107571N' => array('fl' => '86'),
            '19L107572N' => array('fl' => '86'),
            '19L107575N' => array('fl' => '86')
        );

        foreach ($data as $key => $v) {
            DB::table('hr_as_basic_info')
                ->where('associate_id',$key)
                ->update(['as_floor_id' => $v]);
        }
    }






}
