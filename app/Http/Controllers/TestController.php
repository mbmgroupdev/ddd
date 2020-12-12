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
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessAttendanceInOutTime;

class TestController extends Controller
{
    public function test()
    {
        return $this->inoutProcess();
        return $this->testMail();

        return $this->getLeftEmployee();


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
                    ->unit_permissions())->whereIn('b.as_status', [2,3,4,5])
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->where('b.as_status_date', '>=', '2020-11-01')->where('b.as_status_date', '<=', '2020-11-30')->get();

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

    public function jobcardupdate()
    {
        $data = DB::table('hr_attendance_ceil')
            ->where('in_date','2020-11-01')
            ->get();

        foreach ($data as $key => $v) 
        {


            $queue = (new ProcessAttendanceIntime('hr_attendance_ceil', $v->id, 2))
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
            
        }
        return 'success';

    } 


    function time($number){
        // $number = round($number,1);
        $hour = explode(".", $number);

        if(isset($hour[1])){
            $hour[1] = $hour[1];
        }else{
            $hour[1] = '00';
        }

        if(isset($hour[2])){
            $hour[2] = $hour[2];
        }else{
            $hour[2] = '00';
        }
        
        if(empty($hour[0])){
            $hour[0] = '00';
        }
        return sprintf("%02d",$hour[0]).':'.sprintf("%02d",$hour[1]).':'.$hour[2];
    }

    public function inoutProcess()
    {
        $data = array(
            0 => array('as_id' => '3866', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            1 => array('as_id' => '3881', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            2 => array('as_id' => '3881', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            3 => array('as_id' => '3924', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            4 => array('as_id' => '3949', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            5 => array('as_id' => '3949', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            6 => array('as_id' => '3949', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            7 => array('as_id' => '3949', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            8 => array('as_id' => '3949', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            9 => array('as_id' => '3949', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            10 => array('as_id' => '3949', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            11 => array('as_id' => '3949', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            12 => array('as_id' => '3949', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            13 => array('as_id' => '3949', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            14 => array('as_id' => '3949', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            15 => array('as_id' => '3949', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            16 => array('as_id' => '3956', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            17 => array('as_id' => '3956', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            18 => array('as_id' => '3956', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            19 => array('as_id' => '3956', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            20 => array('as_id' => '3956', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            21 => array('as_id' => '4499', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            22 => array('as_id' => '4499', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            23 => array('as_id' => '4499', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            24 => array('as_id' => '4499', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            25 => array('as_id' => '4500', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            26 => array('as_id' => '4501', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            27 => array('as_id' => '4502', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            28 => array('as_id' => '4502', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            29 => array('as_id' => '4502', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            30 => array('as_id' => '4502', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            31 => array('as_id' => '4511', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            32 => array('as_id' => '4511', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            33 => array('as_id' => '4511', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            34 => array('as_id' => '4511', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            35 => array('as_id' => '4515', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            36 => array('as_id' => '4515', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            37 => array('as_id' => '4518', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            38 => array('as_id' => '4519', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            39 => array('as_id' => '4520', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            40 => array('as_id' => '4521', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            41 => array('as_id' => '4521', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            42 => array('as_id' => '4521', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            43 => array('as_id' => '4521', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            44 => array('as_id' => '4521', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            45 => array('as_id' => '4521', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            46 => array('as_id' => '4521', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            47 => array('as_id' => '4521', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            48 => array('as_id' => '4521', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            49 => array('as_id' => '4521', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            50 => array('as_id' => '4521', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            51 => array('as_id' => '4521', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            52 => array('as_id' => '4521', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            53 => array('as_id' => '4521', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            54 => array('as_id' => '4521', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            55 => array('as_id' => '4521', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            56 => array('as_id' => '4521', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            57 => array('as_id' => '4521', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            58 => array('as_id' => '4521', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            59 => array('as_id' => '4521', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            60 => array('as_id' => '4521', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            61 => array('as_id' => '4521', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            62 => array('as_id' => '4521', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            63 => array('as_id' => '4521', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            64 => array('as_id' => '4521', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            65 => array('as_id' => '4521', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            66 => array('as_id' => '4526', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            67 => array('as_id' => '4526', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            68 => array('as_id' => '4526', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            69 => array('as_id' => '4526', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            70 => array('as_id' => '4526', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            71 => array('as_id' => '4526', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            72 => array('as_id' => '4526', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            73 => array('as_id' => '4526', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            74 => array('as_id' => '4526', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            75 => array('as_id' => '4526', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            76 => array('as_id' => '4526', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            77 => array('as_id' => '4526', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            78 => array('as_id' => '4526', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            79 => array('as_id' => '4526', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            80 => array('as_id' => '4526', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            81 => array('as_id' => '4526', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            82 => array('as_id' => '4526', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            83 => array('as_id' => '4528', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            84 => array('as_id' => '4528', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            85 => array('as_id' => '4528', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            86 => array('as_id' => '4528', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            87 => array('as_id' => '4528', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            88 => array('as_id' => '4528', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            89 => array('as_id' => '4528', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            90 => array('as_id' => '4528', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            91 => array('as_id' => '4528', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            92 => array('as_id' => '4528', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            93 => array('as_id' => '4528', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            94 => array('as_id' => '4528', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            95 => array('as_id' => '4528', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            96 => array('as_id' => '4528', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            97 => array('as_id' => '4528', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            98 => array('as_id' => '4528', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            99 => array('as_id' => '4528', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            100 => array('as_id' => '4530', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            101 => array('as_id' => '4530', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            102 => array('as_id' => '4530', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            103 => array('as_id' => '4530', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            104 => array('as_id' => '4530', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            105 => array('as_id' => '4530', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            106 => array('as_id' => '4530', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            107 => array('as_id' => '4530', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            108 => array('as_id' => '4530', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            109 => array('as_id' => '4530', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            110 => array('as_id' => '4530', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            111 => array('as_id' => '4530', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            112 => array('as_id' => '4530', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            113 => array('as_id' => '4530', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            114 => array('as_id' => '4530', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            115 => array('as_id' => '4530', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            116 => array('as_id' => '4530', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            117 => array('as_id' => '4535', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            118 => array('as_id' => '4535', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            119 => array('as_id' => '4535', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            120 => array('as_id' => '4535', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            121 => array('as_id' => '4536', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            122 => array('as_id' => '4536', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            123 => array('as_id' => '4536', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            124 => array('as_id' => '4536', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            125 => array('as_id' => '4536', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            126 => array('as_id' => '4536', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            127 => array('as_id' => '4536', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            128 => array('as_id' => '4536', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            129 => array('as_id' => '4536', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            130 => array('as_id' => '4536', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            131 => array('as_id' => '4536', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            132 => array('as_id' => '4536', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            133 => array('as_id' => '4536', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            134 => array('as_id' => '4537', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            135 => array('as_id' => '4537', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            136 => array('as_id' => '4537', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            137 => array('as_id' => '4537', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            138 => array('as_id' => '4537', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            139 => array('as_id' => '4537', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            140 => array('as_id' => '4537', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            141 => array('as_id' => '4537', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            142 => array('as_id' => '4537', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            143 => array('as_id' => '4537', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            144 => array('as_id' => '4537', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            145 => array('as_id' => '4537', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            146 => array('as_id' => '4537', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            147 => array('as_id' => '4537', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            148 => array('as_id' => '4537', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            149 => array('as_id' => '4537', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            150 => array('as_id' => '4537', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            151 => array('as_id' => '4537', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            152 => array('as_id' => '4538', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            153 => array('as_id' => '4538', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            154 => array('as_id' => '4538', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            155 => array('as_id' => '4538', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            156 => array('as_id' => '4538', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            157 => array('as_id' => '4538', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            158 => array('as_id' => '4538', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            159 => array('as_id' => '4538', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            160 => array('as_id' => '4538', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            161 => array('as_id' => '4538', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            162 => array('as_id' => '4538', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            163 => array('as_id' => '4538', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            164 => array('as_id' => '4538', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            165 => array('as_id' => '4538', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            166 => array('as_id' => '4538', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            167 => array('as_id' => '4538', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            168 => array('as_id' => '4538', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            169 => array('as_id' => '4538', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            170 => array('as_id' => '4538', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            171 => array('as_id' => '4538', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            172 => array('as_id' => '4538', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            173 => array('as_id' => '4538', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            174 => array('as_id' => '4538', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            175 => array('as_id' => '4538', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            176 => array('as_id' => '4538', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            177 => array('as_id' => '4538', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            178 => array('as_id' => '4540', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            179 => array('as_id' => '4540', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            180 => array('as_id' => '4540', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            181 => array('as_id' => '4540', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            182 => array('as_id' => '4540', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            183 => array('as_id' => '4540', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            184 => array('as_id' => '4540', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            185 => array('as_id' => '4540', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            186 => array('as_id' => '4540', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            187 => array('as_id' => '4540', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            188 => array('as_id' => '4540', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            189 => array('as_id' => '4540', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            190 => array('as_id' => '4540', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            191 => array('as_id' => '4540', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            192 => array('as_id' => '4540', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            193 => array('as_id' => '4540', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            194 => array('as_id' => '4540', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            195 => array('as_id' => '4540', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            196 => array('as_id' => '4540', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            197 => array('as_id' => '4541', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            198 => array('as_id' => '4545', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            199 => array('as_id' => '4548', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            200 => array('as_id' => '4549', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            201 => array('as_id' => '4549', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            202 => array('as_id' => '4549', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            203 => array('as_id' => '4549', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            204 => array('as_id' => '4549', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            205 => array('as_id' => '4569', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            206 => array('as_id' => '4569', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            207 => array('as_id' => '4569', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            208 => array('as_id' => '4569', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            209 => array('as_id' => '4569', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            210 => array('as_id' => '4569', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            211 => array('as_id' => '4569', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            212 => array('as_id' => '4569', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            213 => array('as_id' => '4569', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            214 => array('as_id' => '4569', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            215 => array('as_id' => '4569', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            216 => array('as_id' => '4569', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            217 => array('as_id' => '4569', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            218 => array('as_id' => '4569', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            219 => array('as_id' => '4569', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            220 => array('as_id' => '4569', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            221 => array('as_id' => '4613', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            222 => array('as_id' => '4622', 'in_date' => '2020-11-01', 'in_time' => '7.56', 'out_time' => '20.21'),
            223 => array('as_id' => '4622', 'in_date' => '2020-11-02', 'in_time' => '7.54', 'out_time' => '20'),
            224 => array('as_id' => '4622', 'in_date' => '2020-11-03', 'in_time' => '8', 'out_time' => '20.03'),
            225 => array('as_id' => '4622', 'in_date' => '2020-11-04', 'in_time' => '7.58', 'out_time' => '20.35'),
            226 => array('as_id' => '4622', 'in_date' => '2020-11-05', 'in_time' => '7.45', 'out_time' => '20.45'),
            227 => array('as_id' => '4622', 'in_date' => '2020-11-07', 'in_time' => '7.42', 'out_time' => '20.45'),
            228 => array('as_id' => '4622', 'in_date' => '2020-11-08', 'in_time' => '7.56', 'out_time' => '20.36'),
            229 => array('as_id' => '4622', 'in_date' => '2020-11-09', 'in_time' => '7.54', 'out_time' => '20.35'),
            230 => array('as_id' => '4622', 'in_date' => '2020-11-10', 'in_time' => '7.58', 'out_time' => '20.31'),
            231 => array('as_id' => '4622', 'in_date' => '2020-11-11', 'in_time' => '7.45', 'out_time' => '20.45'),
            232 => array('as_id' => '4622', 'in_date' => '2020-11-12', 'in_time' => '7.48', 'out_time' => '20.32'),
            233 => array('as_id' => '4622', 'in_date' => '2020-11-14', 'in_time' => '7.3', 'out_time' => '20.3'),
            234 => array('as_id' => '4622', 'in_date' => '2020-11-15', 'in_time' => '7.54', 'out_time' => '20.34'),
            235 => array('as_id' => '4622', 'in_date' => '2020-11-16', 'in_time' => '7.32', 'out_time' => '20.35'),
            236 => array('as_id' => '4622', 'in_date' => '2020-11-17', 'in_time' => '7.24', 'out_time' => '20.35'),
            237 => array('as_id' => '4622', 'in_date' => '2020-11-18', 'in_time' => '7.26', 'out_time' => '20.35'),
            238 => array('as_id' => '4622', 'in_date' => '2020-11-19', 'in_time' => '7.54', 'out_time' => '20.45'),
            239 => array('as_id' => '4622', 'in_date' => '2020-11-21', 'in_time' => '7.25', 'out_time' => '20.36'),
            240 => array('as_id' => '4622', 'in_date' => '2020-11-22', 'in_time' => '7.5', 'out_time' => '20.35'),
            241 => array('as_id' => '4622', 'in_date' => '2020-11-23', 'in_time' => '7.45', 'out_time' => '20.45'),
            242 => array('as_id' => '4622', 'in_date' => '2020-11-24', 'in_time' => '7.46', 'out_time' => '20.36'),
            243 => array('as_id' => '4622', 'in_date' => '2020-11-25', 'in_time' => '7.45', 'out_time' => '20.35'),
            244 => array('as_id' => '4622', 'in_date' => '2020-11-26', 'in_time' => '7.48', 'out_time' => '20.35'),
            245 => array('as_id' => '4622', 'in_date' => '2020-11-28', 'in_time' => '7.45', 'out_time' => '20.45'),
            246 => array('as_id' => '4622', 'in_date' => '2020-11-29', 'in_time' => '7.45', 'out_time' => '20.25'),
            247 => array('as_id' => '4622', 'in_date' => '2020-11-30', 'in_time' => '7.45', 'out_time' => '20.24'),
            248 => array('as_id' => '4712', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            249 => array('as_id' => '4718', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            250 => array('as_id' => '5791', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            251 => array('as_id' => '5791', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            252 => array('as_id' => '5791', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            253 => array('as_id' => '5793', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            254 => array('as_id' => '5793', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            255 => array('as_id' => '5793', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            256 => array('as_id' => '5793', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            257 => array('as_id' => '5793', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            258 => array('as_id' => '5793', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            259 => array('as_id' => '5793', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            260 => array('as_id' => '5793', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            261 => array('as_id' => '5793', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            262 => array('as_id' => '5793', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            263 => array('as_id' => '5793', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            264 => array('as_id' => '5793', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            265 => array('as_id' => '5793', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            266 => array('as_id' => '5793', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            267 => array('as_id' => '5860', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            268 => array('as_id' => '5860', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            269 => array('as_id' => '6969', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            270 => array('as_id' => '6970', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            271 => array('as_id' => '6970', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            272 => array('as_id' => '6970', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            273 => array('as_id' => '6978', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            274 => array('as_id' => '6979', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            275 => array('as_id' => '6980', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            276 => array('as_id' => '6982', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            277 => array('as_id' => '6982', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            278 => array('as_id' => '6983', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            279 => array('as_id' => '6983', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            280 => array('as_id' => '6983', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            281 => array('as_id' => '6983', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            282 => array('as_id' => '6985', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            283 => array('as_id' => '6985', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            284 => array('as_id' => '6985', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            285 => array('as_id' => '6985', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            286 => array('as_id' => '7030', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            287 => array('as_id' => '7072', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            288 => array('as_id' => '7072', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            289 => array('as_id' => '7072', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            290 => array('as_id' => '7072', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            291 => array('as_id' => '7072', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            292 => array('as_id' => '7072', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            293 => array('as_id' => '7072', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            294 => array('as_id' => '7072', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            295 => array('as_id' => '7076', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            296 => array('as_id' => '7090', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            297 => array('as_id' => '7090', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            298 => array('as_id' => '7090', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            299 => array('as_id' => '7090', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            300 => array('as_id' => '7090', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            301 => array('as_id' => '7090', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            302 => array('as_id' => '7092', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            303 => array('as_id' => '7092', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            304 => array('as_id' => '7092', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            305 => array('as_id' => '7092', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            306 => array('as_id' => '7092', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            307 => array('as_id' => '7092', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            308 => array('as_id' => '7092', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            309 => array('as_id' => '7092', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            310 => array('as_id' => '7092', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            311 => array('as_id' => '7092', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            312 => array('as_id' => '7092', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            313 => array('as_id' => '7092', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            314 => array('as_id' => '7092', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            315 => array('as_id' => '7092', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            316 => array('as_id' => '7092', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            317 => array('as_id' => '7092', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            318 => array('as_id' => '7095', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            319 => array('as_id' => '7095', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            320 => array('as_id' => '7095', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            321 => array('as_id' => '7095', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            322 => array('as_id' => '7095', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            323 => array('as_id' => '7095', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            324 => array('as_id' => '7095', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            325 => array('as_id' => '7095', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            326 => array('as_id' => '7095', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            327 => array('as_id' => '7095', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            328 => array('as_id' => '7095', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            329 => array('as_id' => '7095', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            330 => array('as_id' => '7095', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            331 => array('as_id' => '7095', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            332 => array('as_id' => '7095', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            333 => array('as_id' => '7095', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            334 => array('as_id' => '7095', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            335 => array('as_id' => '7095', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            336 => array('as_id' => '7095', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            337 => array('as_id' => '7095', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            338 => array('as_id' => '7095', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            339 => array('as_id' => '7095', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            340 => array('as_id' => '7095', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            341 => array('as_id' => '7095', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            342 => array('as_id' => '7097', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            343 => array('as_id' => '7097', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            344 => array('as_id' => '7097', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            345 => array('as_id' => '7097', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            346 => array('as_id' => '7097', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            347 => array('as_id' => '7097', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            348 => array('as_id' => '7097', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            349 => array('as_id' => '7097', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            350 => array('as_id' => '7097', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            351 => array('as_id' => '7097', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            352 => array('as_id' => '7097', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            353 => array('as_id' => '7097', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            354 => array('as_id' => '7097', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            355 => array('as_id' => '7097', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            356 => array('as_id' => '7097', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            357 => array('as_id' => '7097', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            358 => array('as_id' => '7097', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            359 => array('as_id' => '7097', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            360 => array('as_id' => '7097', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            361 => array('as_id' => '7097', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            362 => array('as_id' => '7097', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            363 => array('as_id' => '7097', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            364 => array('as_id' => '7097', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            365 => array('as_id' => '7097', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            366 => array('as_id' => '7097', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            367 => array('as_id' => '7098', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            368 => array('as_id' => '7112', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            369 => array('as_id' => '7112', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            370 => array('as_id' => '7112', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            371 => array('as_id' => '7112', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            372 => array('as_id' => '7112', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            373 => array('as_id' => '7112', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            374 => array('as_id' => '7112', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            375 => array('as_id' => '7112', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            376 => array('as_id' => '7112', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            377 => array('as_id' => '7112', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            378 => array('as_id' => '7112', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            379 => array('as_id' => '7112', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            380 => array('as_id' => '7112', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            381 => array('as_id' => '7112', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            382 => array('as_id' => '7135', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            383 => array('as_id' => '7135', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            384 => array('as_id' => '7846', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            385 => array('as_id' => '7883', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            386 => array('as_id' => '7884', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            387 => array('as_id' => '7885', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            388 => array('as_id' => '7885', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            389 => array('as_id' => '7887', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            390 => array('as_id' => '7887', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            391 => array('as_id' => '7887', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            392 => array('as_id' => '7887', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            393 => array('as_id' => '7887', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            394 => array('as_id' => '7887', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            395 => array('as_id' => '7887', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            396 => array('as_id' => '7887', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            397 => array('as_id' => '7887', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            398 => array('as_id' => '7887', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            399 => array('as_id' => '7887', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            400 => array('as_id' => '7887', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            401 => array('as_id' => '7887', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            402 => array('as_id' => '7887', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            403 => array('as_id' => '7887', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            404 => array('as_id' => '7887', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            405 => array('as_id' => '7887', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            406 => array('as_id' => '7887', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            407 => array('as_id' => '7887', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            408 => array('as_id' => '7887', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            409 => array('as_id' => '7887', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            410 => array('as_id' => '7888', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            411 => array('as_id' => '7888', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            412 => array('as_id' => '7888', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            413 => array('as_id' => '7888', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            414 => array('as_id' => '7888', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            415 => array('as_id' => '7888', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            416 => array('as_id' => '7888', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            417 => array('as_id' => '7888', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            418 => array('as_id' => '7888', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            419 => array('as_id' => '7888', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            420 => array('as_id' => '7888', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            421 => array('as_id' => '7888', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            422 => array('as_id' => '7888', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            423 => array('as_id' => '7888', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            424 => array('as_id' => '7888', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            425 => array('as_id' => '7888', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            426 => array('as_id' => '7888', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            427 => array('as_id' => '7888', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            428 => array('as_id' => '7888', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            429 => array('as_id' => '7888', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            430 => array('as_id' => '7888', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            431 => array('as_id' => '7888', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            432 => array('as_id' => '7888', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            433 => array('as_id' => '7888', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            434 => array('as_id' => '7888', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            435 => array('as_id' => '7888', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            436 => array('as_id' => '7889', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            437 => array('as_id' => '7889', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            438 => array('as_id' => '7889', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            439 => array('as_id' => '7889', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            440 => array('as_id' => '7889', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            441 => array('as_id' => '7889', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            442 => array('as_id' => '7889', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            443 => array('as_id' => '7889', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            444 => array('as_id' => '7889', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            445 => array('as_id' => '7889', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            446 => array('as_id' => '7889', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            447 => array('as_id' => '7889', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            448 => array('as_id' => '7889', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            449 => array('as_id' => '7889', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            450 => array('as_id' => '7889', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            451 => array('as_id' => '7889', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            452 => array('as_id' => '7889', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            453 => array('as_id' => '7889', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            454 => array('as_id' => '7889', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            455 => array('as_id' => '7889', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            456 => array('as_id' => '7889', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            457 => array('as_id' => '7889', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            458 => array('as_id' => '7889', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            459 => array('as_id' => '7889', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            460 => array('as_id' => '7889', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            461 => array('as_id' => '7889', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            462 => array('as_id' => '7891', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            463 => array('as_id' => '7891', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            464 => array('as_id' => '7891', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            465 => array('as_id' => '7891', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            466 => array('as_id' => '7891', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            467 => array('as_id' => '7891', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            468 => array('as_id' => '7891', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            469 => array('as_id' => '7891', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            470 => array('as_id' => '7891', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            471 => array('as_id' => '7891', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            472 => array('as_id' => '7891', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            473 => array('as_id' => '7891', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            474 => array('as_id' => '7891', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            475 => array('as_id' => '7891', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            476 => array('as_id' => '7891', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            477 => array('as_id' => '7891', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            478 => array('as_id' => '7893', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            479 => array('as_id' => '7893', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            480 => array('as_id' => '7893', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            481 => array('as_id' => '7893', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            482 => array('as_id' => '7893', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            483 => array('as_id' => '7893', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            484 => array('as_id' => '7893', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            485 => array('as_id' => '7893', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            486 => array('as_id' => '7893', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            487 => array('as_id' => '7893', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            488 => array('as_id' => '7893', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            489 => array('as_id' => '7893', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            490 => array('as_id' => '7893', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            491 => array('as_id' => '7893', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            492 => array('as_id' => '7893', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            493 => array('as_id' => '7894', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            494 => array('as_id' => '7894', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            495 => array('as_id' => '7894', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            496 => array('as_id' => '7894', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            497 => array('as_id' => '7894', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            498 => array('as_id' => '7894', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            499 => array('as_id' => '7894', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            500 => array('as_id' => '7894', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            501 => array('as_id' => '7894', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            502 => array('as_id' => '7894', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            503 => array('as_id' => '7894', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            504 => array('as_id' => '7894', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            505 => array('as_id' => '7894', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            506 => array('as_id' => '7894', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            507 => array('as_id' => '7894', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            508 => array('as_id' => '7894', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            509 => array('as_id' => '7894', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            510 => array('as_id' => '7894', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            511 => array('as_id' => '7894', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            512 => array('as_id' => '7894', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            513 => array('as_id' => '7894', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            514 => array('as_id' => '7894', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            515 => array('as_id' => '7894', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            516 => array('as_id' => '7894', 'in_date' => '2020-11-28', 'in_time' => '0', 'out_time' => '0'),
            517 => array('as_id' => '7894', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            518 => array('as_id' => '7894', 'in_date' => '2020-11-30', 'in_time' => '0', 'out_time' => '0'),
            519 => array('as_id' => '7904', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            520 => array('as_id' => '9740', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            521 => array('as_id' => '9740', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            522 => array('as_id' => '9740', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            523 => array('as_id' => '9740', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            524 => array('as_id' => '9740', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            525 => array('as_id' => '9740', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            526 => array('as_id' => '9740', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            527 => array('as_id' => '9740', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            528 => array('as_id' => '9740', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            529 => array('as_id' => '9841', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            530 => array('as_id' => '9841', 'in_date' => '2020-11-21', 'in_time' => '0', 'out_time' => '0'),
            531 => array('as_id' => '9841', 'in_date' => '2020-11-29', 'in_time' => '0', 'out_time' => '0'),
            532 => array('as_id' => '10152', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            533 => array('as_id' => '10171', 'in_date' => '2020-11-01', 'in_time' => '0', 'out_time' => '0'),
            534 => array('as_id' => '10171', 'in_date' => '2020-11-02', 'in_time' => '0', 'out_time' => '0'),
            535 => array('as_id' => '10171', 'in_date' => '2020-11-03', 'in_time' => '0', 'out_time' => '0'),
            536 => array('as_id' => '10171', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            537 => array('as_id' => '10171', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            538 => array('as_id' => '10171', 'in_date' => '2020-11-08', 'in_time' => '0', 'out_time' => '0'),
            539 => array('as_id' => '10171', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            540 => array('as_id' => '10171', 'in_date' => '2020-11-10', 'in_time' => '0', 'out_time' => '0'),
            541 => array('as_id' => '10171', 'in_date' => '2020-11-11', 'in_time' => '0', 'out_time' => '0'),
            542 => array('as_id' => '10171', 'in_date' => '2020-11-12', 'in_time' => '0', 'out_time' => '0'),
            543 => array('as_id' => '10171', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            544 => array('as_id' => '10171', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            545 => array('as_id' => '10171', 'in_date' => '2020-11-17', 'in_time' => '0', 'out_time' => '0'),
            546 => array('as_id' => '10171', 'in_date' => '2020-11-18', 'in_time' => '0', 'out_time' => '0'),
            547 => array('as_id' => '10171', 'in_date' => '2020-11-19', 'in_time' => '0', 'out_time' => '0'),
            548 => array('as_id' => '10171', 'in_date' => '2020-11-22', 'in_time' => '0', 'out_time' => '0'),
            549 => array('as_id' => '10171', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            550 => array('as_id' => '10171', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            551 => array('as_id' => '10171', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
            552 => array('as_id' => '10171', 'in_date' => '2020-11-26', 'in_time' => '0', 'out_time' => '0'),
            553 => array('as_id' => '10174', 'in_date' => '2020-11-09', 'in_time' => '0', 'out_time' => '0'),
            554 => array('as_id' => '10174', 'in_date' => '2020-11-23', 'in_time' => '0', 'out_time' => '0'),
            555 => array('as_id' => '10237', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            556 => array('as_id' => '10289', 'in_date' => '2020-11-03', 'in_time' => '7.54', 'out_time' => '16.58'),
            557 => array('as_id' => '10289', 'in_date' => '2020-11-04', 'in_time' => '7.54', 'out_time' => '17'),
            558 => array('as_id' => '10289', 'in_date' => '2020-11-05', 'in_time' => '7.56', 'out_time' => '16.58'),
            559 => array('as_id' => '10408', 'in_date' => '2020-11-04', 'in_time' => '0', 'out_time' => '0'),
            560 => array('as_id' => '10408', 'in_date' => '2020-11-05', 'in_time' => '0', 'out_time' => '0'),
            561 => array('as_id' => '10408', 'in_date' => '2020-11-07', 'in_time' => '0', 'out_time' => '0'),
            562 => array('as_id' => '10718', 'in_date' => '2020-11-07', 'in_time' => '7.55', 'out_time' => '17.01'),
            563 => array('as_id' => '10872', 'in_date' => '2020-11-14', 'in_time' => '0', 'out_time' => '0'),
            564 => array('as_id' => '10872', 'in_date' => '2020-11-15', 'in_time' => '0', 'out_time' => '0'),
            565 => array('as_id' => '10872', 'in_date' => '2020-11-16', 'in_time' => '0', 'out_time' => '0'),
            566 => array('as_id' => '11044', 'in_date' => '2020-11-24', 'in_time' => '0', 'out_time' => '0'),
            567 => array('as_id' => '11044', 'in_date' => '2020-11-25', 'in_time' => '0', 'out_time' => '0'),
        );
        foreach ($data as $key => $v) 
        {

            $shift = DB::table('hr_shift')
                     ->get()->keyBy('hr_shift_code');

            $att = DB::table('hr_attendance_ceil')
            ->where('as_id',$v['as_id'])
            ->where('in_date',$v['in_date'])
            ->first();

            if($att){

                if($v['in_time'] > 0 && $v['out_time'] > 0){

                    $out = $this->time($v['out_time']);
                    $in = $this->time($v['in_time']);
                    $intime = date('Y-m-d H:i:s',strtotime($v['in_date'].' '.$in));
                    if($v['in_time'] > $v['out_time']){
                        $d = Carbon::create($v['in_date'])->addDay()->toDateString();
                        $outtime = date('Y-m-d H:i:s',strtotime($d.' '.$out));
                    }else{
                        $outtime = date('Y-m-d H:i:s',strtotime($v['in_date'].' '.$out));
                    }
                    $queue = (new ProcessAttendanceInOutTime('hr_attendance_ceil', $att->id, 2))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);

                }else{
                    $intime = $v['in_date'].' '.$shift[$att->hr_shift_code]->hr_shift_start_time;
                    $outtime = null;
                    $queue = (new ProcessAttendanceIntime('hr_attendance_ceil', $att->id, 2))
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
                }
                DB::table('hr_attendance_ceil')
                ->where('id', $att->id)
                ->update([
                    'out_time' => $outtime,
                    'in_time' => $intime
                ]);



                
            }

            
        }
        return 'success';

    } 


    public function outtimeProcess()
    {
        $data = [];
        foreach ($data as $key => $v) 
        {
            $att = DB::table('hr_attendance_ceil as a')
            ->leftJoin('hr_as_basic_info as b','a.as_id', 'b.as_id' )
            ->where('b.associate_id',$key)
            ->where('a.in_date',$v['date'])
            ->first();

            $out = $this->time($v['out']);
            if(strtotime(date('H:i:s',strtotime($att->in_time))) > strtotime(date('H:i:s',strtotime($out)))){
                $d = Carbon::create($v['date'])->addDay()->toDateString();
                $outtime = date('Y-m-d H:i:s',strtotime($v['date'].' '.$out));
            }else{
                $outtime = date('Y-m-d H:i:s',strtotime($v['date'].' '.$out));
            }

            DB::table('hr_attendance_ceil')
            ->where('id', $att->id)
            ->update([
                'out_time' => $outtime
            ]);



            $queue = (new ProcessAttendanceOuttime('hr_attendance_ceil', $att->id, 2))
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
            
        }
        return 'success';

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
                    ->where('s.month', 11)
                    ->where('s.year', 2020)
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
                'Total Day' => $e->present + $e->leave + $e->holiday + $e->absent,
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
                'Current Salary' => $e->ben_current_salary,
                'Basic' => $e->ben_basic,
                'House Rent' => $e->ben_house_rent,
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


    public function testMail()
    {
        $data = [];

        Mail::to('rakib@mbmdhaka.com')->send(new TestMail($data));
    }
    

}
