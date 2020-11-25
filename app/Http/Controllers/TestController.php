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


class TestController extends Controller
{
    public function test()
    {
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subsection = subSection_by_id();
        $unit = unit_by_id();
        $disctrict = district_by_id();
        $upzilla = upzila_by_id();

        $data = DB::table('hr_as_basic_info as b')
        ->leftJoin('hr_benefits as ben','ben.ben_as_id','b.associate_id')
        ->leftJoin('hr_as_adv_info as adv','adv.emp_adv_info_as_id','b.associate_id')
        ->leftJoin('hr_employee_bengali as bn','bn.hr_bn_associate_id','b.associate_id')
        ->whereIn('as_unit_id',[1,4,5])
        ->where('as_status',1)
        ->get();


        $benefit = [];
        $bank_cash = [];
        $father = [];
        $mother = [];
        $maritial_status = [];
        $religion = [];
        $present_road = [];
        $present_po = [];
        $present_upz = [];
        $present_dist = [];
        $perm_vill = [];
        $perm_po = [];
        $perm_upz = [];
        $perm_dist = [];
        $bn_name = [];
        $bn_father = [];
        $bn_mother = [];
        $bn_pres_road = [];
        $bn_pres_po = [];
        $bn_per_vill = [];
        $bn_per_po= [];
        $image = [];

        $bangla = [];
        $image_miss = [];
        $benefit_miss = [];
        $advance_info = [];
        foreach ($data as $key => $e) {
            if(!$e->ben_current_salary || ($e->ben_cash_amount == 0 && $e->ben_bank_amount == 0)){
                $benefit_miss[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_fathers_name || !$e->emp_adv_info_mothers_name || !$e->emp_adv_info_marital_stat || !$e->emp_adv_info_religion || !$e->emp_adv_info_per_vill || !$e->emp_adv_info_per_po){
                $advance_info[] = $e->associate_id;
            }

            if(!$e->hr_bn_associate_name || !$e->hr_bn_father_name || !$e->hr_bn_mother_name || !$e->hr_bn_permanent_village){
                $bangla[] = $e->associate_id;
            }
           
            /*if(!$e->ben_current_salary){
                $benefit[] = $e->associate_id;
            }
            if($e->ben_cash_amount == 0 && $e->ben_bank_amount == 0){
                $bank_cash[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_fathers_name){
                $father[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_mothers_name){
                $mother[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_marital_stat){
                $maritial_status[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_religion){
                $religion[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_per_vill){
                $perm_vill[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_per_po){
                $perm_po[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_per_dist){
                $perm_dist[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_per_upz){
                $perm_upz[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_pres_road){
                $present_road[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_pres_po){
                $present_po[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_pres_dist){
                $present_dist[] = $e->associate_id;
            }
            if(!$e->emp_adv_info_pres_upz){
                $present_upz[] = $e->associate_id;
            }

            if(!$e->hr_bn_associate_name){
                $bn_name[] = $e->associate_id;
            }
            if(!$e->hr_bn_father_name){
                $bn_father[] = $e->associate_id;
            }
            if(!$e->hr_bn_mother_name){
                $bn_mother[] = $e->associate_id;
            }
            if(!$e->hr_bn_permanent_village){
                $bn_per_vill[] = $e->associate_id;
            }
            if(!$e->hr_bn_permanent_po){
                $bn_per_po[] = $e->associate_id;
            }
            if(!$e->hr_bn_present_road){
                $bn_pres_road[] = $e->associate_id;
            }
            if(!$e->hr_bn_present_po){
                $bn_pres_po[] = $e->associate_id;
            }*/
            if(!$e->as_pic){
                $image[] = $e->associate_id;
            }
            
        }


        $arr = array(
           'Benefit' => $benefit_miss,
           'Advance Info' => $advance_info,
           'Bangla' => $bangla,
           'Image' => $image
        );

        return (new FastExcel(collect($arr)))->download('Employee Missing List.xlsx');

        $total = count($data);

        $text = 'Benefit '. count($benefit).' '.((round(count($benefit)/$total,4))*100).'%<br>';
        $text .= 'Bank/cash '. count($bank_cash).' '.((round(count($bank_cash)/$total,4))*100).'%<br>';
        $text .= 'Father Name '. count($father).' '.((round(count($father)/$total,4))*100).'%<br>';
        $text .= 'Mother Name '. count($mother).' '.((round(count($mother)/$total,4))*100).'%<br>';
        $text .= 'Maritial Status '. count($maritial_status).' '.((round(count($maritial_status)/$total,4))*100).'%<br>';
        $text .= 'Present Road '. count($present_road).' '.((round(count($present_road)/$total,4))*100).'%<br>';
        $text .= 'Present PO '. count($present_po).' '.((round(count($present_po)/$total,4))*100).'%<br>';
        $text .= 'Present Upzilla '. count($present_upz).' '.((round(count($present_upz)/$total,4))*100).'%<br>';
        $text .= 'Present District '. count($present_dist).' '.((round(count($present_dist)/$total,4))*100).'%<br>';
        $text .= 'Permanent Village '. count($perm_vill).' '.((round(count($perm_vill)/$total,4))*100).'%<br>';
        $text .= 'Permanent PO '. count($perm_po).' '.((round(count($perm_po)/$total,4))*100).'%<br>';
        $text .= 'Permanent Upzilla '. count($perm_upz).' '.((round(count($perm_upz)/$total,4))*100).'%<br>';
        $text .= 'Permanent District '. count($perm_dist).' '.((round(count($perm_dist)/$total,4))*100).'%<br>';
        $text .= 'Name (Bangla) '. count($bn_name).' '.((round(count($bn_name)/$total,4))*100).'%<br>';
         $text .= 'Father Name (Bangla) '. count($bn_father).' '.((round(count($bn_father)/$total,4))*100).'%<br>';
        $text .= 'Mother Name (Bangla) '. count($bn_mother).' '.((round(count($bn_mother)/$total,4))*100).'%<br>';
        $text .= 'Present Road (Bangla) '. count($bn_pres_road).' '.((round(count($bn_pres_road)/$total,4))*100).'%<br>';
        $text .= 'Present PO  (Bangla) '. count($bn_pres_po).' '.((round(count($bn_pres_po)/$total,4))*100).'%<br>';
        $text .= 'Permanent Village  (Bangla) '. count($bn_per_vill).' '.((round(count($bn_per_vill)/$total,4))*100).'%<br>';
        $text .= 'Permanent PO  (Bangla) '. count($bn_per_po).' '.((round(count($bn_per_po)/$total,4))*100).'%<br>';
        $text .= 'Image '. count($image).' '.((round(count($image)/$total,4))*100).'%<br>';
       
        echo html_entity_decode($text);
       
        dd($text,$benefit,
        $bank_cash,
        $father,
        $mother,
        $maritial_status,
        $religion,
        $present_road,
        $present_po,
        $present_upz,
        $present_dist,
        $perm_vill,
        $perm_po,
        $perm_upz,
        $perm_dist,
        $bn_name,
        $bn_father,
        $bn_mother,
        $bn_pres_road,
        $bn_pres_po,
        $bn_per_vill,
        $bn_per_po,
        $image);




        foreach ($data as $key => $a) {
            # code...
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
                    'Father' => $a->emp_adv_info_fathers_name??'',
                    'Mother' => $a->emp_adv_info_mothers_name??'',
                    'Maritial Status' => $a->emp_adv_info_marital_stat??'',
                    'Spouse' => $a->emp_adv_info_spouse??'',
                    'Contact' => $a->as_contact,
                    'Passport' => $a->emp_adv_info_passport??'',
                    'Permanent District' => $district[($a->emp_adv_info_per_dist??0)]??'',
                    'Permanent Upazila' => $upzilla[($a->emp_adv_info_per_upz??0)]??'',
                    'Permanent Post' => $a->emp_adv_info_per_po??'',
                    'Permanent Village' => $a->emp_adv_info_per_vill??'',
                    'Present District' => $district[($a->emp_adv_info_pres_dist??0)]??'',
                    'Present Upazila' => $upzilla[($a->emp_adv_info_pres_upz??0)]??'',
                    'Present Post' => $a->emp_adv_info_pres_po??'',
                    'Present Road' => $a->emp_adv_info_pres_road??'',
                    'Present House' => $a->emp_adv_info_pres_house_no??'',
                    'NID' => $a->emp_adv_info_nid??''
                );
        }

            return (new FastExcel(collect($excel)))->download('Employee.xlsx');









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
       /* $excel = Employee::select('as_oracle_code', 'as_name')->whereNull('as_ot')->where('as_status', 1)->where('as_unit_id', 2)->get()->toArray();
        
        return (new FastExcel(collect($excel)))->download('OT Status Missing CEIL.xlsx');
        $data = DB::table('hr_absent')->select('associate_id', DB::raw('count(*) as count') )->where('date','>=', '2020-10-01')->where('date','<=', '2020-10-31')->groupBy('associate_id')->pluck('count','associate_id');

        $miss = [];
        foreach ($data as $key => $value) {
            $d = DB::table('hr_monthly_salary')->where('month', 10)->where('year', 2020)->where('as_id', $key)->first();
            if($d){

                if($d->absent != $value){
                    $miss[$d->as_id] = $d;
                }
            }
        }
        dd($miss);*/


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


    public function processPartialSalary($employee, $salary_date, $status)
    {
        $month = date('m', strtotime($salary_date));
        $year = date('Y', strtotime($salary_date));
        $first_day = Carbon::create($salary_date)->firstOfMonth()->format('Y-m-d');
        $dayCount = 31;

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

        $totalDays = date('j', strtotime($salary_date));
        $totalAbsent = $totalDays - ($present + $getHoliday);
        $absentPayable = $totalAbsent - $leaveCount;

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
        $perDayGross = round(($employee->ben_current_salary / $dayCount),2);
        $getAbsentDeduct = $absentPayable * $perDayBasic;

        //stamp = 10 by default all employee;
        

        if($employee->as_ot == 1){
            $overtime_rate = number_format((($employee->ben_basic/208)*2), 2, ".", "");
        } else {
            $overtime_rate = 0;
        }
        $overtime_salary = 0;
        

        $attBonus = 0;
        $totalLate = $late;
        
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
            'absent' => $totalAbsent,
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
        $salaryPayable = ceil(((($perDayGross*$totalDays) - ($getAbsentDeduct + ($deductCost)))));
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
        $salary['salary_date'] = $totalDays;
        

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
        $educationDegree = DB::table('hr_education_degree_title')->pluck('education_degree_title', 'id');
        $educations = DB::table('hr_education AS e')
        ->select(DB::raw('t.*'))
        ->from(DB::raw('(SELECT * FROM hr_education ORDER BY id DESC) t'))
        ->groupBy('t.education_as_id')
        ->pluck('education_degree_id_1', 'education_as_id');

        dd($educations);
        // $start = date('Y-m-d H:s' ,strtotime('2020-11-15 08:00'));
        // $end = date('Y-m-d H:s' ,strtotime('2020-11-15 16:00'));
        // // return $end;
        // $dif = strtotime($start) - strtotime($end);
        // return $dif;
        // $getEmployee = DB::table('hr_as_basic_info AS b')
        // ->where('as_unit_id', 5)
        // ->pluck('associate_id');
        // $shiftRoster = [];
        // for ($i=1; $i < 32 ; $i++) { 
        //     $getRoster = DB::table('hr_shift_roaster')
        //     ->whereIn('shift_roaster_associate_id', $getEmployee)
        //     ->where('day_'.$i, 'NIGHT')
        //     ->get();
        //     if(count($getRoster) > 0){
        //         /*$shiftRoster[] = DB::table('hr_shift_roaster')
        //         ->where('shift_roaster_id', $getRoster->shift_roaster_id)
        //         ->update(['day_'.$i => 'CUTTING NIGHT']);*/
        //         $shiftRoster[] = $getRoster;
        //     }
        // }


        // dd($shiftRoster);

        // $getAtt = DB::table('hr_attendance_mbm')
        // ->select('hr_shift_code')
        // ->distinct()
        // ->pluck('hr_shift_code');

        // $getA = DB::table('hr_attendance_mbm')->select(DB::raw('DISTINCT hr_shift_code, COUNT(*) AS count_pid'))->groupBy('hr_shift_code')->orderBy('count_pid', 'desc')->get();
        // // dd($getA);exit;

        // $getShift = DB::table('hr_shift AS s')
        // ->whereIn('s.hr_shift_unit_id', [5])
        // ->whereIn('s.hr_shift_code', $getAtt)
        // ->pluck('hr_shift_name', 'hr_shift_code');
        // // dd($getShift);

        // $getShift = DB::table('hr_shift AS s')
        // ->whereIn('s.hr_shift_unit_id', [5])
        // ->whereNotIn('s.hr_shift_name', $getShift)
        // ->pluck('hr_shift_name', 'hr_shift_code'); //ONESMS ONEMMSMS2
        // // $getShift = DB::table('hr_shift AS s')
        // // ->whereIn('s.hr_shift_unit_id', [5])
        // // ->where('s.hr_shift_name', 'SECURITY Morning Shift -6')
        // // ->pluck('hr_shift_name', 'hr_shift_code');
        // dd($getShift);
        // $shiftRoster = [];
        // for ($i=1; $i < 32 ; $i++) { 
        //     $getRoster = DB::table('hr_shift_roaster')
        //     ->where('shift_roaster_month', '>=',9)
        //     ->whereIn('day_'.$i, $getShift)
        //     ->get();
        //     if(count($getRoster) > 0){
        //         $shiftRoster[] = $getRoster;
        //     }
        // }
        
        // $basic = DB::table("hr_as_basic_info")
        // ->whereIn('as_shift_id', $getShift)
        // ->get();


        // dd($shiftRoster);

        // $getData = DB::table('hr_monthly_salary AS s')
        // ->join('hr_as_basic_info AS b', 's.as_id', 'b.associate_id')
        // ->where('b.as_unit_id', 2)
        // ->where('b.as_location', 7)
        // ->where('s.month', '10')
        // ->where('s.present', '>', 1)
        // ->get();
        // dd($getData);

        
        // $user = DB::table('hr_as_basic_info')->where('as_doj', 'like','2020-11%')->get();
        // $data = [];
        // foreach ($user as $key => $e) {
        //     $query[] = DB::table('hr_monthly_salary')
        //                               ->where('as_id', $e->associate_id)
        //                               ->where('month',10)
        //                               ->get()->toArray();
            
        // }
        // dd($query);
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2020-11-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('holiday_roaster')
        //                               ->where('as_id', $e->associate_id)
        //                               ->whereDate('date','<',$e->as_doj)
        //                               ->get()->toArray();
            
        // }
        // dd($query);
        // $user = DB::table('hr_as_basic_info')->where('as_doj', '>=','2020-11-01')->get();
        //     $data = [];
        // foreach ($user as $key => $e) {
        //     $query = DB::table('hr_absent')
        //                               ->where('date', 'like', '2020-11%')
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
        //         for($i=1; $i<=12; $i++) {
        //         $date = date('Y-m-d', strtotime('2020-11-'.$i));
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
            // $leave_array = [];
            // $absent_array = [];
            // for($i=1; $i<=13; $i++) {
            // $date = date('Y-m-d', strtotime('2020-11-'.$i));
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
            // return "done";
            // dump($leave_array,$absent_array);
            // dd('end');

            // $leave_array = [];
            // $absent_array = [];
            // for($i=1; $i<=14; $i++) {
            // $date = date('Y-m-d', strtotime('2020-11-'.$i));
            // $leave = DB::table('hr_leave AS l')
            //         ->where('l.leave_from', '<=', $date)
            //         ->where('l.leave_to',   '>=', $date)
            //         ->where('l.leave_status', '=', 1)
            //         ->leftJoin('hr_as_basic_info AS b', function($q){
            //             $q->on('b.associate_id', 'l.leave_ass_id');
            //         })
            //         ->pluck('b.associate_id','b.as_id');
            // $leave_array[] = $leave;
            // $absent_array[] = DB::table('hr_absent')
            //         ->whereDate('date', $date)
            //         ->whereIn('associate_id', $leave)
            //         ->get()->toArray();
            // }
            // dump($leave_array,$absent_array);
            // dd('end');
    }
    

}
