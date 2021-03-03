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

        
        return $this->salarygenerate();
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

    public function processLeftSalary()
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
    }

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


    public function salarygenerate()
    {
        $as_id = 5754;
        $getEmployee = Employee::where('as_id', 5754)->first();
        $year = 2021;
        $month = '02';
        $ttday = 28;
        $table = 'hr_attendance_ceil';
        $yearMonth = $year.'-'.$month;
        $monthDayCount  = Carbon::parse($yearMonth)->daysInMonth;
        $partial = 0;
        try {
            if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $yearMonth){
                // check lock month
                $checkL['month'] = $month;
                $checkL['year'] = $year;
                $checkL['unit_id'] = $getEmployee->as_unit_id;
                $checkLock = monthly_activity_close($checkL);
                if($checkLock == 1){
                    return 'error';
                }
                //  get benefit employee associate id wise
                $getBenefit = Benefits::
                where('ben_as_id', $getEmployee->associate_id)
                ->first();

                $empdoj = $getEmployee->as_doj;
                $empdojMonth = date('Y-m', strtotime($getEmployee->as_doj));
                $empdojDay = date('d', strtotime($getEmployee->as_doj));

                $totalDay = $ttday;
                $today = $yearMonth.'-01';
                $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
                if($empdojMonth == $yearMonth){
                    $totalDay = $ttday - ((int) $empdojDay-1);
                    $firstDateMonth = $getEmployee->as_doj;
                }

                if($getBenefit != null){
                    
                    if($getEmployee->as_status_date != null){
                        $sDate = $getEmployee->as_status_date;
                        $sYearMonth = Carbon::parse($sDate)->format('Y-m');
                        $sDay = Carbon::parse($sDate)->format('d');


                        if($yearMonth == $sYearMonth){
                            $firstDateMonth = $getEmployee->as_status_date;
                            $totalDay = $ttday - ((int) $sDay-1);

                            if($sDay > 1){
                                $partial = 1;
                            }
                        }
                    }

                    
                    if($monthDayCount > $ttday){
                        $lastDateMonth = $yearMonth.'-'.$ttday;
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
                    $getPresentOT = DB::table($table)
                        ->select([
                            DB::raw('count(as_id) as present'),
                            DB::raw('SUM(ot_hour) as ot'),
                            DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                            DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')

                        ])
                        ->where('as_id', $this->asId)
                        ->where('in_date','>=',$firstDateMonth)
                        ->where('in_date','<=', $lastDateMonth)
                        ->first();
                    
                    $lateCount = 0;
                    $halfCount = 0;
                    $presentOt
                    if(!isset($getPresentOT->present)){
                        $getPresentOT->present = 0;
                        $lateCount = $getPresentOT->late??0;
                        $halfCount = $getPresentOT->halfday??0;
                        $presentOt = $getPresentOT->ot??0;
                    }

                    $diffExplode = explode('.', $presentOt);
                    
                    $minutes = (isset($diffExplode[1]) ? $diffExplode[1] : 0);
                    $minutes = floatval('0.'.$minutes);
                    if($minutes > 0 && $minutes != 1){
                        $min = (int)round($minutes*60);
                        $minOT = min_to_ot();
                        $minutes = $minOT[$min]??0;
                    }

                    $presentOt = $diffExplode[0] + $minutes;

                    
                    

                    // check OT roaster employee
                    $rosterOTCount = HolidayRoaster::where('year', $year)
                    ->where('month', $month)
                    ->where('as_id', $getEmployee->associate_id)
                    ->where('date','>=', $firstDateMonth)
                    ->where('date','<=', $lastDateMonth)
                    ->where('remarks', 'OT')
                    ->get();
                    $rosterOtData = $rosterOTCount->pluck('date');

                    $otDayCount = 0;
                    $totalOt = count($rosterOTCount);
                    // return $rosterOTCount;
                    foreach ($rosterOTCount as $otc) {
                        $checkAtt = DB::table($table)
                        ->where('as_id', $getEmployee->as_id)
                        ->where('in_date', $otc->date)
                        ->first();
                        if($checkAtt != null){
                            $otDayCount += 1;
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
                        $getHoliday = $getHoliday + ($totalOt - $otDayCount);
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
                            $query = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
                                ->where('hr_yhp_dates_of_holidays','>=', $empdoj)
                                ->where('hr_yhp_open_status', 0);
                            if(count($rosterOtData) > 0){
                                $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                            }
                            $shiftHolidayCount = $query->count();
                        }else{
                            $query = YearlyHolyDay::
                                where('hr_yhp_unit', $getEmployee->as_unit_id)
                                ->where('hr_yhp_dates_of_holidays','>=', $firstDateMonth)
                                ->where('hr_yhp_dates_of_holidays','<=', $lastDateMonth)
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

                    

                    $getAbsent = $totalDay - ($getPresentOT->present + $getHoliday + $leaveCount);
                    if($getAbsent < 0){
                        $getAbsent = 0;
                    }
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
                    $getAbsentDeduct = (int)($getAbsent * $perDayBasic);
                    $getHalfDeduct = (int)($halfCount * ($perDayBasic / 2));

                    $stamp = 10;
                    $payStatus = 1; // cash pay
                    if($getBenefit->ben_bank_amount != 0 && $getBenefit->ben_cash_amount != 0){
                        $payStatus = 3; // partial pay
                    }elseif($getBenefit->ben_bank_amount != 0){
                        $payStatus = 2; // bank pay
                    }

                    if($getBenefit->ben_cash_amount == 0 && $getEmployee->as_emp_type_id == 3){
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
                      if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $partial == 1 ){
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
                            $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('m');
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

                    $leaveAdjust = ceil((float)$leaveAdjust);

                    if(($empdojMonth == $yearMonth && date('d', strtotime($getEmployee->as_doj)) > 1) || $monthDayCount > $ttday || $partial == 1){
                        $perDayGross   = $getBenefit->ben_current_salary/$monthDayCount;
                        $totalGrossPay = ($perDayGross * $totalDay);
                        $salaryPayable = $totalGrossPay - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }else{

                        $salaryPayable = $getBenefit->ben_current_salary - ($getAbsentDeduct + $getHalfDeduct + $deductCost + $stamp);
                    }

                    $ot = ((float)($overtime_rate) * ($presentOt));
                    
                    $totalPayable = ceil((float)($salaryPayable + $ot + $deductSalaryAdd + $attBonus + $productionBonus + $leaveAdjust));

                    // cash & bank part
                    $tds = $getBenefit->ben_tds_amount??0;
                    if($payStatus == 1){
                        $tds = 0;
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

                    if($bankPayable > 0 && $tds > 0 && $bankPayable > $tds){
                        $bankPayable = $bankPayable - $tds;
                    }else{
                        $tds = 0;
                    }

                    if($getSalary == null){
                        $salary = [
                            'as_id' => $getEmployee->associate_id,
                            'ot_status' => $getEmployee->as_ot,
                            'unit_id' => $getEmployee->as_unit_id,
                            'designation_id' => $getEmployee->as_designation_id,
                            'sub_section_id' => $getEmployee->as_subsection_id,
                            'location_id' => $getEmployee->as_location,
                            'pay_type' => $getBenefit->bank_name,
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
                            'ot_hour' => $presentOt,
                            'attendance_bonus' => $attBonus,
                            'production_bonus' => $productionBonus,
                            'leave_adjust' => $leaveAdjust,
                            'stamp' => $stamp,
                            'pay_status' => $payStatus,
                            'emp_status' => $getEmployee->as_status,
                            'total_payable' => $totalPayable,
                            'cash_payable' => $cashPayable,
                            'bank_payable' => $bankPayable,
                            'tds' => $tds
                        ];
                        HrMonthlySalary::insert($salary);
                    }else{
                        $salary = [
                            'ot_status' => $getEmployee->as_ot,
                            'unit_id' => $getEmployee->as_unit_id,
                            'designation_id' => $getEmployee->as_designation_id,
                            'sub_section_id' => $getEmployee->as_subsection_id,
                            'location_id' => $getEmployee->as_location,
                            'pay_type' => $getBenefit->bank_name,
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
                            'ot_hour' => $presentOt,
                            'attendance_bonus' => $attBonus,
                            'production_bonus' => $productionBonus,
                            'leave_adjust' => $leaveAdjust,
                            'stamp' => $stamp,
                            'pay_status' => $payStatus,
                            'emp_status' => $getEmployee->as_status,
                            'total_payable' => $totalPayable,
                            'cash_payable' => $cashPayable,
                            'bank_payable' => $bankPayable,
                            'tds' => $tds
                        ];
                        HrMonthlySalary::where('id', $getSalary->id)->update($salary);
                    }
                }
            }
            return 'success';

        } catch (\Exception $e) {
            return $e;
            DB::table('error')->insert(['msg' => $this->asId.' '.$e->getMessage()]);
            /*$bug = $e->errorInfo[1];
            // $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                // duplicate
            }*/
        }
    

    }


}
