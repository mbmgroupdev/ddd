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
        return  Cache::remember('salary_lock_date', 10000000, function () {
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




/*-------------------------------------
 * Cache methods
 *------------------------------------*/

if(!function_exists('cache_att_all')){
    function cache_att_all()
    {
        Cache::put('att_mbm', cache_att_mbm(), 10000);
        Cache::put('att_aql', cache_att_aql(), 10000);
        Cache::put('att_ceil', cache_att_ceil(), 10000);
    }
}


if(!function_exists('cache_daily_operation')){
    function cache_daily_operation($unit = null)
    {   
        if($unit == null){
            $user = auth()->user();
            if($user){
                $unit = auth()->user()->employee?auth()->user()->employee['as_unit_id']:1;
                Cache::put('today_att', unit_wise_today_att($unit), 10000);
            }
        }else{
            Cache::put('today_att', unit_wise_today_att($unit), 10000);
        }
        cache_att_all();
        Cache::put('monthly_ot', cache_monthly_ot(), 10000);
        Cache::put('monthly_salary', cache_monthly_salary(), 10000);
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
                        ->whereDate('a.in_time', $today)
                        ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                        ->where('b.as_unit_id', $unit)
                        ->get()
                        ->count();

        $late  = DB::table($table.' AS a')
                        ->select('late_status')
                        ->whereDate('a.in_time', $today)
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
                   ->whereDate('date',$today)
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
       return  Cache::remember('designation', 1000000, function () {
            return Designation::get()->keyBy('hr_designation_id')->toArray();
        });      

    }
}

if(!function_exists('shift_by_code')){
    function shift_by_code()
    {
       return  Cache::remember('shift_code', 1000000, function () {
            return Shift::get()->keyBy('hr_shift_code')->toArray();
        });      

    }
}

if(!function_exists('unit_by_id')){
    function unit_by_id()
    {
       return  Cache::remember('unit', 1000000, function () {
            return Unit::get()->keyBy('hr_unit_id')->toArray();
        });      

    }
}



if(!function_exists('line_by_id')){
    function line_by_id()
    {
       return  Cache::remember('line', 1000000, function () {
            return Line::get()->keyBy('hr_line_id')->toArray();
        });      

    }
}

if(!function_exists('floor_by_id')){
    function floor_by_id()
    {
       return  Cache::remember('floor', 1000000, function () {
            return Floor::get()->keyBy('hr_floor_id')->toArray();
        });      

    }
}

if(!function_exists('department_by_id')){
    function department_by_id()
    {
       return  Cache::remember('department', 1000000, function () {
            return Department::get()->keyBy('hr_department_id')->toArray();
        });      

    }
}

if(!function_exists('section_by_id')){
    function section_by_id()
    {
       return  Cache::remember('section', 1000000, function () {
            return Section::get()->keyBy('hr_section_id')->toArray();
        });      

    }
}
if(!function_exists('subSection_by_id')){
    function subSection_by_id()
    {
       return  Cache::remember('subSection', 1000000, function () {
            return Subsection::get()->keyBy('hr_subsec_id')->toArray();
        });      

    }
}

if(!function_exists('area_by_id')){
    function area_by_id()
    {
       return  Cache::remember('area', 1000000, function () {
            return Area::get()->keyBy('hr_area_id')->toArray();
        });      

    }
}

