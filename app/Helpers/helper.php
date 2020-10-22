<?php
use App\Models\Employee;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Line;
use App\Models\Hr\SalaryAudit;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use App\Models\Hr\EarnedLeave;
use App\Models\Hr\Leave;
use App\Models\District;
use App\Models\Upazilla;
use App\Models\UserLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


if(!function_exists('check_permission')){
    
    function check_permission($permission)
    {
        $perm = false;
        if(auth()->user()->can($permission) || auth()->user()->hasRole('Super Admin')){
            $perm = true;
        }

        return $perm;
    }
}

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

if(!function_exists('maritial_bangla')){
    function maritial_bangla($info)
    {
        if($info == 'Married'){
            $status = 'বিবাহিত';
        }else if($info == 'Unmarried'){
            $status = 'অবিবাহিত';
        }else{
            $status = '';
        }
        return $status;
    }
}

if(!function_exists('religion_bangla')){
    function religion_bangla($info)
    {
        if($info == 'Islam'){
            $status = 'ইসলাম';
        }else if($info == 'Hinduism'){
            $status = 'হিন্দু';
        }else if($info == 'Christians'){
            $status = 'খ্রিস্টান';
        }else if($info == 'Buddhists'){
            $status = 'বৌদ্ধ';
        }else{
            $status = '';
        }
        return $status;
    }
}

if(!function_exists('monthly_activity_close')){
    function monthly_activity_close($data){
        $flag = 1; // lock
        $salaryStatus = SalaryAudit::checkSalaryAuditStatus($data);
        if($salaryStatus == null){
            $flag = 0; // unlock
        }else{
            if($salaryStatus->hr_audit == null){
                $flag = 0; // unlock
            }
        }
        
        return $flag;
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

if(!function_exists('num_to_bn_month')){
    function num_to_bn_month($value)
    {

        $month = array('','জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর');

        return $month[(int) $value];
    }
}

if(!function_exists('date_to_bn_month')){
    function date_to_bn_month($date)
    {
        $n_month = date('n', strtotime($date));
        $n_year = eng_to_bn(date('Y', strtotime($date)));

        $month = array('','জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর');

        return $month[$n_month].', '.$n_year;
    }
}

if(!function_exists('bn_money')){
    function bn_money($value)
    {
        return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $value);
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
            $user_log= new UserLog();
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

if(!function_exists('emp_remain_leave_check')){
    function emp_remain_leave_check($request)
    {        
        $statement = [];
        $statement['stat'] = "false";
        $associate_id = $request->associate_id;
        if(auth()->user()->associate_id == $associate_id){
            $hello = 'You have';
        }else{
            $hello = 'This employee has';
        }
        if($request->leave_type== "Earned"){

            $earned = DB::table('hr_earned_leave')
                        ->select(DB::raw('sum(earned - enjoyed) as l'))
                        ->where('associate_id', $associate_id)
                        ->groupBy('associate_id')->first()->l??0;
            //dd($earned, $request->sel_days);
            $avail = (int) ($earned/2);
            if($avail >= $request->sel_days){
                $statement['stat'] = "true";
            }else{
                $statement['stat'] = "false";
                if($earned >0){
                    $statement['msg'] = $hello.' only '.$earned.' day(s) of Earned Leave and you can take only '.$avail. ' day(s)' ;
                }else{
                    $statement['msg'] = $hello.' no earned leave';
                }
            }
        }

        if($request->leave_type== "Casual"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual
                    ")
                )
                ->where("leave_ass_id", $associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();

            $casual = 10-$leaves->casual;
            if($request->sel_days <= $casual){
                $statement['stat'] = "true";
            }else{
                $statement['msg'] = $hello.' '.$casual.' day(s) of Casual Leave';
            }
        }
        // Sick Leave Restriction
        if($request->leave_type== "Sick"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick
                    ")
                )
                ->where("leave_ass_id", $associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();

            $sick = 14-$leaves->sick;
            if($request->sel_days <= $sick){
                $statement['stat'] = "true";
            }else{
                $statement['msg'] = $hello.' '.$sick.' day(s) of Sick(14) Leave';
            }
        }
        // Maternity Leave Restriction
        if($request->leave_type== "Maternity"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity
                    ")
                )
                ->where("leave_ass_id", $request->associate_id) 
                ->where("leave_status", 1) 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            $remain = 112-($leaves->maternity??0);
            //dd($request->sel_days);
            if($leaves == null || ($leaves != null && $request->sel_days< $remain) ) {
                $statement['stat'] = "true";
            }else if (($leaves != null && $request->sel_days > $remain)){
                $statement['msg'] = $hello.' only '.$remain.' day(s) remain';   
            }else{
                $statement['msg'] = $hello.' already taken Maternity Leave';   
            }
        }
        if($request->leave_type == "Special"){
            $statement['stat'] = "true";
        }

        if($statement['stat'] == 'true'){
            $from_date = new \DateTime($request->from_date);
            $to_date = new \DateTime($request->to_date);
            $to_date->modify("+1 day");
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($from_date, $interval, $to_date);
            //dd($period );
            $statement['msg'] = $hello.' already taken/applied for leave at <br>';
            foreach ($period as $dt) {
                $leave = Leave::where('leave_from','<=', $dt->format("Y-m-d"))
                            ->where('leave_to','>=', $dt->format("Y-m-d"))
                            ->where('leave_ass_id', $request->associate_id)
                            ->when($request, function($query) use ($request) {
                                if(isset($request->leave_id)){
                                    $query->where('id', '!=', $request->leave_id);
                                }
                            })
                            ->first();
                if($leave){
                    if($leave->leave_status == 1){
                        $status = '<span style="color:#00b300;">Approved</span>';
                    }else if($leave->leave_status == 0){
                        $status = 'Applied';
                    }else{
                        $status = '';
                    }
                    $statement['stat'] = "false";
                    $statement['msg'] .= $dt->format("Y-m-d").' <span style="color:#000;">--- '.$leave->leave_type.'---</span> '.$status.'<br>';
                }
            }
        }

        return $statement;
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
                'u.hr_unit_address',
                'u.hr_unit_address_bn',
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
            
        if($emp){
            $emp->as_pic = emp_profile_picture($emp);
        }

        return $emp;
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
    function get_earned_leave($leaves = [], $as_id, $associate_id, $unit_id)
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

        return Employee::select(
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
            ->where('as_status',1)
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->first();
    }
}

if(!function_exists('cache_att_all')){
    function cache_att_all()
    {
        Cache::put('att_mbm', cache_att_mbm(), 1000000);
        Cache::put('att_aql', cache_att_aql(), 1000000);
        Cache::put('att_ceil', cache_att_ceil(), 1000000);
        Cache::put('att_mbm2', cache_att_mbm2(), 1000000);
        Cache::put('att_mfw', cache_att_mfw(), 1000000);
    }
}


if(!function_exists('cache_today_att')){
    function cache_today_att()
    {

        $today = date("Y-m-d");
        $data = [];
        
        $mbm = DB::table('hr_attendance_mbm AS a')
                ->where('a.in_date', $today)
                ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                ->pluck('b.associate_id','b.associate_id')->toArray();

        $ceil =  DB::table('hr_attendance_ceil AS a')
                ->where('a.in_date', $today)
                ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                ->pluck('b.associate_id','b.associate_id')->toArray();

        $aql = DB::table('hr_attendance_aql AS a')
                ->where('a.in_date', $today)
                ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                ->pluck('b.associate_id','b.associate_id')->toArray();

        $cew = DB::table('hr_attendance_cew AS a')
                ->where('a.in_date', $today)
                ->leftJoin('hr_as_basic_info AS b', 'b.as_id', 'a.as_id')
                ->pluck('b.associate_id','b.associate_id')->toArray();

        $data['present']  = array_merge($mbm,$ceil,$aql,$cew);



        $data['leave'] = DB::table('hr_leave')
                 ->where('leave_from', '<=', $today)
                 ->where('leave_to',   '>=', $today)
                 ->where('leave_status', '=', 1)
                 ->pluck('leave_ass_id','leave_ass_id')->toArray();

        $data['absent'] = DB::table('hr_absent')
                       ->where('date',$today)
                       ->pluck('associate_id','associate_id')->toArray();

        $data['holiday'] = DB::table('holiday_roaster')
                           ->where('date',$today)
                           ->where('remarks','Holiday')
                           ->pluck('as_id','as_id')->toArray();
        return [
            'date' => $today,
            'data' => $data
        ];

    }
}


if(!function_exists('cache_daily_operation')){
    function cache_daily_operation($unit = null)
    {   

        Cache::put('today_att', cache_today_att($unit), 1000000);
        cache_att_all();
        Cache::put('monthly_ot', cache_monthly_ot(), 1000000);
        Cache::put('monthly_salary', cache_monthly_salary(), 1000000);
    }
}

if(!function_exists('cache_att_mbm')){
    function cache_att_mbm()
    {
        return DB::table('hr_attendance_mbm as m')
        ->select('b.as_id', 'm.in_date')
        ->whereMonth('m.in_date',date('m'))
        ->whereYear('m.in_date',date('Y'))
        ->leftJoin('hr_as_basic_info as b','b.as_id','m.as_id')
        ->where('b.as_unit_id', 1)
        ->get()
        ->groupBy('in_date')->toArray();       
    }
}

if(!function_exists('cache_att_mbm2')){
    function cache_att_mbm2()
    {
        return DB::table('hr_attendance_mbm as m')
        ->select('b.as_id', 'm.in_date')
        ->whereMonth('m.in_date',date('m'))
        ->whereYear('m.in_date',date('Y'))
        ->leftJoin('hr_as_basic_info as b','b.as_id','m.as_id')
        ->where('b.as_unit_id', 5)
        ->get()
        ->groupBy('in_date')->toArray();          
    }
}

if(!function_exists('cache_att_mfw')){
    function cache_att_mfw()
    {
        return DB::table('hr_attendance_mbm as m')
        ->select('b.as_id', 'm.in_date')
        ->whereMonth('m.in_date',date('m'))
        ->whereYear('m.in_date',date('Y'))
        ->leftJoin('hr_as_basic_info as b','b.as_id','m.as_id')
        ->where('b.as_unit_id', 4)
        ->get()
        ->groupBy('in_date')->toArray();          
    }
}

if(!function_exists('cache_att_aql')){
    function cache_att_aql()
    {
        return DB::table('hr_attendance_aql')
            ->select('as_id', 'in_date')
            ->whereMonth('in_date',date('m'))
            ->whereYear('in_date',date('Y'))
            ->get()
            ->groupBy('in_date')->toArray();  
    }
}

if(!function_exists('cache_att_ceil')){
    function cache_att_ceil()
    {
        return DB::table('hr_attendance_ceil')
            ->select('as_id', 'in_date')
            ->whereMonth('in_date',date('m'))
            ->whereYear('in_date',date('Y'))
            ->get()
            ->groupBy('in_date')->toArray(); 
    }
}

if(!function_exists('cache_monthly_ot')){
    function cache_monthly_ot()
    {
        return Cache::remember('monthly_ot', 10000, function  (){
            
            return HrMonthlySalary::selectRaw(
                'as_id, ot_hour, CONCAT(year,"-",month) as ym'
            )
            ->get()
            ->groupBy('ym')
            ->toArray();
        });      
    }
}

if(!function_exists('cache_monthly_salary')){
    function cache_monthly_salary()
    {
        return Cache::remember('monthly_salary', 10000, function  (){
            
            return HrMonthlySalary::selectRaw(
                'as_id, salary_payable, (ot_hour*ot_rate) as ot, CONCAT(year,"-",month) as ym'
            )
            ->get()
            ->groupBy('ym')
            ->toArray();
        });
    }
}


if(!function_exists('unit_wise_today_att')){
    function unit_wise_today_att()
    {
        Cache::put('today_att', cache_today_att(), 10000);
    }

}
if(!function_exists('location_by_id')){
    function location_by_id()
    {
       return  Cache::remember('location', Carbon::now()->addHour(23), function () {
            return Location::get()->keyBy('hr_location_id')->toArray();
        });      

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
       return  Cache::remember('unit', Carbon::now()->addHour(12), function () {
            return Unit::orderBy('hr_unit_name','DESC')->get()->keyBy('hr_unit_id')->toArray();
        });      

    }
}

if(!function_exists('unit_list')){
    function unit_list()
    {
       return  Cache::remember('unit_list', Carbon::now()->addHour(12), function () {
            return Unit::select('hr_unit_name', 'hr_unit_id')->where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_name', 'desc')
            ->pluck('hr_unit_name', 'hr_unit_id');
        });      

    }
}

if(!function_exists('permitted_units')){
    function permitted_units()
    {
       $units =  Cache::remember('permitted_units', Carbon::now()->addHour(12), function () {
            return Unit::where('hr_unit_status', '1')
            ->orderBy('hr_unit_name', 'desc')
            ->pluck('hr_unit_short_name', 'hr_unit_id');
        });  
        $permit = auth()->user()->unit_permissions();
        $uname = '';
        foreach ($permit as $key => $u) {
              $uname .= ' '.($units[$u]??'').',';
              if($key == 2) 
                $uname .= '<br>'; 
        } 

        return $uname;
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




if(!function_exists('district_by_id')){
    function district_by_id()
    {
       
       return  Cache::rememberForever('district_by_id', function () {
            return District::pluck('dis_name', 'dis_id')->toArray();
        });      

    }
}

if(!function_exists('upzila_by_id')){
    function upzila_by_id()
    {
       return  Cache::rememberForever('upzila_by_id', function () {
            return Upazilla::pluck('upa_name', 'upa_id')->toArray();
        });      

    }
}

// ot format calculation 
function numberToTimeClockFormat($number){
    // $number = round($number,1);
    $hour = explode(".", $number);

    if(isset($hour[1])){
        $hour[1] = '0.'.$hour[1];    
        $hour[1] = ($hour[1]*60);     
        $hour[1] = sprintf("%02d", round($hour[1]));     
        // return $hour[1];
    }else{
        $hour[1] = '00';
    }
    
    if(empty($hour[0])){
        $hour[0] = 0;
    }
    return $hour[0].':'.$hour[1];
}




