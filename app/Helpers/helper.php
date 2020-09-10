<?php
use App\Models\Employee;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Line;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\UserLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

if(!function_exists('get_att_table')){
	
	function get_att_table($unit = null)
	{
		$tableName = "";

        if($unit== 1 || $unit == 4 || $unit ==5 || $unit ==9){
            $tableName= "hr_attendance_mbm";
        }
        else if($unit==2){
            $tableName= "hr_attendance_ceil";
        }
        else if($unit==3){
            $tableName= "hr_attendance_aql";
        }
        else if($unit==8){
            $tableName= "hr_attendance_cew";
        }

        return $tableName;
	}
}

if(!function_exists('sselected')){
    function sselected($value1, $value2)
    {
        if($value1 == $value2) {
            return "selected='selected'";
        }
        return '';
    }
}

if(!function_exists('salary_lock_date')){
    function salary_lock_date()
    {
        return  Cache::remember('salary_lock_date', 100000000, function () {
            return DB::table('hr_system_setting')->first()->salary_lock;
        }); 
        
    }
}

if(!function_exists('number_to_time')){
    function number_to_time($number)
    {
        $number = round($number,1);
        $hour = explode(".", $number);
        if(isset($hour[1])){
            return $hour[0].':'.round($hour[1]*6);   
        }else
            return $hour[0];
    }
}

if(!function_exists('eng_to_bn')){
    function eng_to_bn($value)
    {
        $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',',');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',',');

        return str_replace($en, $bn, $value);
    }
}


if(!function_exists('num_to_word')){
    function num_to_word($num)
    {
        $num = str_replace(array(',', ' '), '' , trim($num));
        if(! $num) {
            return false;
        }
        $num = (int) $num;
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ( $tens < 20 ) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }
}


if(!function_exists('number_to_time_format')){
    function number_to_time_format($number)
    {
        $number = round($number,1);
        $hour = explode(".", $number);
        if(isset($hour[1])){
            $hour[1] = round($hour[1]*6);    
        }else{
            $hour[1] = '00';
        }
        return $hour[0].':'.$hour[1];
    }
}


if(!function_exists('log_file_write')){

    function log_file_write($message, $event_id)
    {
        $log_message = date("Y-m-d H:i:s")." \"".auth()->user()->associate_id."\" ".$message." ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);

        // store user log
        $logs = UserLog::where('log_as_id', auth()->id())->orderBy('updated_at','ASC')->get();

        if(count($logs)<3){
            $user_log= new UserLog;
        }else{
            $user_log = $logs->first();
            $user_log->id = $logs->first()->id;
        }
            $user_log->log_as_id = auth()->id();
            $user_log->log_message = $message;
            $user_log->log_table = '';
            $user_log->log_row_no = $event_id;
            $user_log->save();
    }
}

if(!function_exists('get_unit_name_by_id')){
    function get_unit_name_by_id($id)
    {
        $unit_name = '';
        if(is_numeric($id)) {
            $unit = unit_by_id();
            $unit_name = $unit[$id]->hr_unit_short_name??'';
           
        } 

        return $unit_name;
    }
}

if(!function_exists('emp_status_name')){
    function emp_status_name($status)
    {
        $name = '';
        if($status == 2) {
            $name = 'resign';
        } else if($status == 3) {
            $name = 'terminate';
        } else if($status == 4) {
            $name = 'suspend';
        } else if($status == 5) {
            $name = 'left';
        } else if($status == 6) {
            $name = 'maternity';
        }
        return $name;
    }
}

if(!function_exists('num_to_time')){
    function num_to_time($number){
        $number = round($number,1);
        $hour = explode(".", $number);
        if(isset($hour[1])){
            return $hour[0].':'.round($hour[1]*6);   
        }else
            return $hour[0];
    }
}

if(!function_exists('emp_profile_picture')){
    function emp_profile_picture($employee)
    {
        $default = ($employee->as_gender == 'Female'?'/assets/images/user/1.jpg':'/assets/images/user/09.jpg');

        if($employee->as_pic != null && file_exists(public_path($employee->as_pic))){
            $image = $employee->as_pic;
        }else{
            $image = $default;
        }
        return $image;
    }
}

if(!function_exists('get_employee_by_id'))
{
    function get_employee_by_id($associate_id = null)
    {
        $emp = Employee::select(
                'hr_as_basic_info.*',
                'u.hr_unit_id',
                'u.hr_unit_name',
                'u.hr_unit_short_name',
                'u.hr_unit_name_bn',
                'f.hr_floor_name',
                'f.hr_floor_name_bn',
                'l.hr_line_name',
                'l.hr_line_name_bn',
                'dp.hr_department_name',
                'dp.hr_department_name_bn',
                'dg.hr_designation_name',
                'dg.hr_designation_name_bn',
                'a.*',
                'be.*',
                'm.*',
                'e.hr_emp_type_name',
                'ar.hr_area_name',
                'se.hr_section_name',
                'se.hr_section_name_bn',
                'sb.hr_subsec_name',
                'sb.hr_subsec_name_bn',
                'bn.*',
                # unit/floor/line/shif
                DB::raw("
                    CONCAT_WS('. ',
                        CONCAT('Unit: ', u.hr_unit_short_name),
                        CONCAT('Floor: ', f.hr_floor_name),
                        CONCAT('Line: ', l.hr_line_name)
                    ) AS unit_floor_line
                "),
                # permanent district & upazilla
                "per_dist.dis_name AS permanent_district",
                "per_dist.dis_name_bn AS permanent_district_bn",
                "per_upz.upa_name AS permanent_upazilla",
                "per_upz.upa_name_bn AS permanent_upazilla_bn",
                # present district & upazilla
                "pres_dist.dis_name AS present_district",
                "pres_dist.dis_name_bn AS present_district_bn",
                "pres_upz.upa_name AS present_upazilla",
                "pres_upz.upa_name_bn AS present_upazilla_bn"
            )
            ->leftJoin('hr_area AS ar', 'ar.hr_area_id', '=', 'hr_as_basic_info.as_area_id')
            ->leftJoin('hr_section AS se', 'se.hr_section_id', '=', 'hr_as_basic_info.as_section_id')
            ->leftJoin('hr_subsection AS sb', 'sb.hr_subsec_id', '=', 'hr_as_basic_info.as_subsection_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'hr_as_basic_info.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'hr_as_basic_info.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'hr_as_basic_info.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'hr_as_basic_info.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'hr_as_basic_info.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'hr_as_basic_info.as_designation_id')
            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "hr_as_basic_info.associate_id")
            ->leftJoin('hr_benefits AS be',function ($leftJoin) {
                $leftJoin->on('be.ben_as_id', '=' , 'hr_as_basic_info.associate_id') ;
                $leftJoin->where('be.ben_status', '=', '1') ;
            })
            ->leftJoin('hr_med_info AS m', 'm.med_as_id', '=', 'hr_as_basic_info.associate_id')

            #permanent district & upazilla
            ->leftJoin('hr_dist AS per_dist', 'per_dist.dis_id', '=', 'a.emp_adv_info_per_dist')
            ->leftJoin('hr_upazilla AS per_upz', 'per_upz.upa_id', '=', 'a.emp_adv_info_per_upz')
            #present district & upazilla
            ->leftJoin('hr_dist AS pres_dist', 'pres_dist.dis_id', '=', 'a.emp_adv_info_pres_dist')
            ->leftJoin('hr_upazilla AS pres_upz', 'pres_upz.upa_id', '=', 'a.emp_adv_info_pres_upz')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'hr_as_basic_info.associate_id')
            ->where("hr_as_basic_info.associate_id", $associate_id)
            ->whereIn('hr_as_basic_info.as_unit_id', auth()->user()->unit_permissions())
            ->first();
        $emp->as_pic = emp_profile_picture($emp);

        return$emp;
    }
}

if(!function_exists('get_complete_user_info')){
    function get_complete_user_info($associate_id = null)
    {
        $info= DB::table('hr_as_basic_info AS b')
            ->select(
                'b.*',
                'a.*',
                'be.*',
                'm.*',
                'bn.*'
            )
            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "b.associate_id")
            ->leftJoin('hr_benefits AS be',function ($leftJoin) {
                $leftJoin->on('be.ben_as_id', '=' , 'b.associate_id') ;
                $leftJoin->where('be.ben_status', '=', '1') ;
            })
            ->leftJoin('hr_med_info AS m', 'm.med_as_id', '=', 'b.associate_id')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
            ->where("b.associate_id", $associate_id)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->first();

            $infocount=0; $totalinfo=0;
            foreach ($info as $key =>$infovalue)
            {
                if($infovalue!=null){ $infocount++;}
                $totalinfo++;
            }
            $per_complete=round((($infocount/$totalinfo)*100), 2);
        return $per_complete;
    }
}

if(!function_exists('get_earned_leave')){
    function get_earned_leave($leaves, $as_id, $associate_id, $unit_id)
    {
        $table = get_att_table($unit_id).' AS a';
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
        return $earnedLeaves;

    }
}


/*-------------------------------------
 * Cache methods
 *------------------------------------*/
if(!function_exists('employee_count')){
    function employee_count()
    {
        # Count Total, Male & Female Employee
        $employee_count = Cache::remember('employee_count', 20000, function  () {

            return  Employee::select(
                DB::raw("
                  COUNT(CASE WHEN as_gender = 'Male' THEN as_id END) AS males,
                  COUNT(CASE WHEN as_gender = 'Female' THEN as_id END) AS females,
                  COUNT(CASE WHEN as_ot = '0' THEN as_id END) AS non_ot,
                  COUNT(CASE WHEN as_ot = '1' THEN as_id END) AS ot,
                  COUNT(CASE WHEN as_status != '1' THEN as_id END) AS inactive,
                  COUNT(CASE WHEN as_status = '1' THEN as_id END) AS active,
                  COUNT(CASE WHEN as_doj = CURDATE() THEN as_id END) AS todays_join,
                  COUNT(*) AS total
                ")
            )
            //->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->where('as_status',1) // checking status
            ->first()->toArray();
        });

        return $employee_count;
    }
}

if(!function_exists('cache_att_all')){
    function cache_att_all()
    {
        Cache::put('att_mbm', cache_att_mbm(), 1000000);
        Cache::put('att_aql', cache_att_aql(), 1000000);
        Cache::put('att_ceil', cache_att_ceil(), 1000000);
    }
}


if(!function_exists('cache_daily_operation')){
    function cache_daily_operation($unit = null)
    {   
        if($unit == null){
            $user = auth()->user();
            if($user){
                $unit = auth()->user()->employee?auth()->user()->employee['as_unit_id']:1;
                Cache::put('today_att'.$unit, unit_wise_today_att($unit), 1000000);
            }else{
                Cache::put('today_att1', unit_wise_today_att(1), 1000000);
            }
        }else{
            Cache::put('today_att'.$unit, unit_wise_today_att($unit), 1000000);
        }
        cache_att_all();
        Cache::put('monthly_ot', cache_monthly_ot(), 1000000);
        Cache::put('monthly_salary', cache_monthly_salary(), 1000000);
    }
}

if(!function_exists('cache_att_mbm')){
    function cache_att_mbm()
    {
        return DB::table('hr_attendance_mbm as m')
        ->selectRaw('count(*) as present, m.in_date')
        ->whereMonth('m.in_date',date('m'))
        ->whereYear('m.in_date',date('Y'))
        ->leftJoin('hr_as_basic_info as b','b.as_id','m.as_id')
        /*->whereMonth('m.in_date','12')
        ->whereYear('m.in_date','2019')*/
        ->where('b.as_unit_id', 1)
        ->groupBy('m.in_date')
        ->pluck('present','m.in_date');        
    }
}

if(!function_exists('cache_att_aql')){
    function cache_att_aql()
    {
        return DB::table('hr_attendance_aql')
            ->selectRaw('count(*) as present,in_date')
            ->whereMonth('in_date',date('m'))
            ->whereYear('in_date',date('Y'))
            /*->whereMonth('in_date','12')
            ->whereYear('in_date','2019')*/
            ->groupBy('in_date')
            ->pluck('present','in_date');
    }
}

if(!function_exists('cache_att_ceil')){
    function cache_att_ceil()
    {
        return DB::table('hr_attendance_ceil')
            ->selectRaw('count(*) as present,in_date')
            ->whereMonth('in_date',date('m'))
            ->whereYear('in_date',date('Y'))
            /*->whereMonth('in_date','12')
            ->whereYear('in_date','2019')*/
            ->groupBy('in_date')
            ->pluck('present','in_date');
    }
}

if(!function_exists('cache_monthly_ot')){
    function cache_monthly_ot()
    {
        return HrMonthlySalary::selectRaw('sum(ot_hour) as ot, CONCAT(year,"-",month) as ym')
            ->groupBy('month','year')
            ->orderBy('id','DESC')
            ->pluck('ot','ym');      
    }
}

if(!function_exists('cache_monthly_salary')){
    function cache_monthly_salary()
    {
        return HrMonthlySalary::selectRaw(
            'round(sum(salary_payable)/100000,0) as salary, round(sum(ot_hour*ot_rate)/100000,0) as ot, CONCAT(year,"-",month) as ym')
        ->groupBy('month','year')
        ->orderBy('id','DESC')
        ->get()
        ->keyBy('ym')
        ->toArray();
    }
}


if(!function_exists('unit_wise_today_att')){
    function unit_wise_today_att($unit)
    {
        //$today = date("2019-12-31");
        $today = date("Y-m-d");
        $table = get_att_table($unit);

        $present = 0;
        $late = 0;
        $leave   = 0;
        $totalUser    = 0;
        $absent  = 0;

    
        $present  = DB::table($table.' AS a')
                        ->select(
                                DB::raw("DISTINCT(a.as_id)"),
                                "a.hr_shift_code"
                              )
                        ->where('a.in_date', $today)
                        ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                        ->where('b.as_unit_id', $unit)
                        ->get()
                        ->count();

        $late  = DB::table($table.' AS a')
                        ->select('late_status')
                        ->where('a.in_date', $today)
                        ->where('a.late_status', 1)
                        ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                        ->where('b.as_unit_id', $unit)
                        ->get()
                        ->count();
      
        /*----------------Leave------------------*/
        $leave = DB::table('hr_leave AS l')
                 ->where('l.leave_from', '<=', $today)
                 ->where('l.leave_to',   '>=', $today)
                 ->where('l.leave_status', '=', 1)
                 ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'l.leave_ass_id')
                 ->where('b.as_unit_id', $unit)
                 ->count();

        $query1 = DB::table('hr_as_basic_info AS b')
                  ->where('as_status', 1);
        $query1->where('hdr.date','LIKE',$today);
        $query1->where('hdr.remarks', 'Holiday');
        $query1->Join('holiday_roaster AS hdr', 'hdr.as_id', 'b.associate_id');

        $holiday = $query1->get()->count();

        $employee = Employee::where("as_status", 1)->where('as_unit_id', $unit)->count();

        $absent = DB::table('hr_absent')
                   ->where('date',$today)
                   ->where('hr_unit', $unit)
                   ->get()
                   ->count();

        $unit_info= Unit::where('hr_unit_id', $unit)->first();

        $today_att = [
          'employee'=> $employee,
          'present' => $present,
          'late'    => $late,
          'absent'  => $absent,
          'leave'   => $leave,
          'holiday' => $holiday,
          'unit'    => $unit_info->hr_unit_short_name??'',
          'unit_id' => $unit,
          'date'    => $today
        ];

        return $today_att;
    }

}
if(!function_exists('designation_by_id')){
    function designation_by_id()
    {
       return  Cache::remember('designation', 10000000, function () {
            return Designation::get()->keyBy('hr_designation_id')->toArray();
        });      

    }
}

if(!function_exists('shift_by_code')){
    function shift_by_code()
    {
       return  Cache::remember('shift_code', 10000000, function () {
            return Shift::get()->keyBy('hr_shift_code')->toArray();
        });      

    }
}

if(!function_exists('unit_by_id')){
    function unit_by_id()
    {
       return  Cache::remember('unit', 10000000, function () {
            return Unit::orderBy('hr_unit_name','DESC')->get()->keyBy('hr_unit_id')->toArray();
        });      

    }
}



if(!function_exists('line_by_id')){
    function line_by_id()
    {
       return  Cache::remember('line', 10000000, function () {
            return Line::get()->keyBy('hr_line_id')->toArray();
        });      

    }
}

if(!function_exists('floor_by_id')){
    function floor_by_id()
    {
       return  Cache::remember('floor', 10000000, function () {
            return Floor::get()->keyBy('hr_floor_id')->toArray();
        });      

    }
}

if(!function_exists('department_by_id')){
    function department_by_id()
    {
       return  Cache::remember('department', 10000000, function () {
            return Department::get()->keyBy('hr_department_id')->toArray();
        });      

    }
}

if(!function_exists('section_by_id')){
    function section_by_id()
    {
       return  Cache::remember('section', 10000000, function () {
            return Section::get()->keyBy('hr_section_id')->toArray();
        });      

    }
}
if(!function_exists('subSection_by_id')){
    function subSection_by_id()
    {
       return  Cache::remember('subSection', 10000000, function () {
            return Subsection::get()->keyBy('hr_subsec_id')->toArray();
        });      

    }
}

if(!function_exists('area_by_id')){
    function area_by_id()
    {
       return  Cache::remember('area', 10000000, function () {
            return Area::get()->keyBy('hr_area_id')->toArray();
        });      

    }
}

