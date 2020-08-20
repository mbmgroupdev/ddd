<?php
use App\Models\Hr\Designation;
use App\Models\Employee;
use App\Models\UserLog;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
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