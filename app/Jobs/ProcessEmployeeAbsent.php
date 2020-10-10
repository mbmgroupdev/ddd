<?php

namespace App\Jobs;

use App\Jobs\ProcessMonthlySalary;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEmployeeAbsent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;
    public $timeout=500;
    public $tableName;
    public $dates;
    public $unitId;
    public function __construct($tableName, $dates, $unitId)
    {
        $this->tableName = $tableName;
        $this->dates = $dates;
        $this->unitId = $unitId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->unitId == 1){
            $getEmp = DB::table('hr_as_basic_info')
            ->select('as_id')
            ->where('as_status', 1)
            ->whereIn('as_unit_id', [1, 4, 5, 9])
            ->pluck('as_id')
            ->toArray();
        }else{
          $getEmp = DB::table('hr_as_basic_info')
            ->select('as_id')
            ->where('as_status', 1)
            ->where('as_unit_id', $this->unitId)
            ->pluck('as_id')
            ->toArray();  
        }
        
        foreach($this->dates as $date){
            $year = Carbon::parse($date)->format('Y');
            $month = Carbon::parse($date)->format('m');
            $getData = DB::table($this->tableName)
            ->select('as_id')
            ->where('in_date', $date)
            ->pluck('as_id')
            ->toArray(); 
            $arrayDiff = array_diff($getEmp, $getData);
            foreach ($arrayDiff as $key => $value) {
                $getEmployee = Employee::where('as_id', $value)->first();
                if($getEmployee != null){
                    $flag = 0;
                    $shiftFlag = 0;
                    // if($date == date('Y-m-d') && date('H:i') < '12:00'){
                    //     $flag = 1;
                    // }
                    if($flag == 0){
                        $unitId = $getEmployee->as_unit_id;
                            
                        $day_of_date = date('j');
                        $day_num = "day_".$day_of_date;
                        $shift= DB::table("hr_shift_roaster")
                        ->where('shift_roaster_month', $month)
                        ->where('shift_roaster_year', $year)
                        ->where("shift_roaster_user_id", $getEmployee->as_id)
                        ->select([
                            $day_num,
                            'hr_shift.hr_shift_start_time'
                        ])
                        ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                            $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                            $q->where('hr_shift.hr_shift_unit_id', $unitId);
                        })
                        ->orderBy('hr_shift.hr_shift_id', 'desc')
                        ->first();
                        
                        if(!empty($shift) && $shift->$day_num != null){
                            $cShifStartTime = strtotime(date("H:i", strtotime($shift->hr_shift_start_time)));
                        }
                        else{
                            $cShifStartTime = strtotime(date("H:i", strtotime($getEmployee->shift['hr_shift_start_time'])));
                        }
                        if($cShifStartTime > '16:00'){
                            $shiftFlag = 1;
                        }
                    }

                    if($date >= $getEmployee->as_doj && $shiftFlag == 0){
                        $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWiseRemarkMulti($year, $month, $getEmployee->associate_id, $date, ['Holiday', 'OT']);
                        if($getHoliday == null && $getEmployee->shift_roaster_status == 0){
                            $getHoliday = YearlyHolyDay::getCheckUnitDayWiseHolidayStatusMulti($getEmployee->as_unit_id, $date, [0, 2]);
                        }
                        
                        if($getHoliday == null){
                            $getLeave = DB::table('hr_leave')
                            ->where('leave_ass_id', $getEmployee->associate_id)
                            ->where('leave_from', '<=', $date)
                            ->where('leave_to', '>=', $date)
                            ->where('leave_status',1)
                            ->first();
                            //
                            $getAbsent = DB::table('hr_absent')
                            ->where('associate_id', $getEmployee->associate_id)
                            ->where('hr_unit', $getEmployee->as_unit_id)
                            ->where('date', $date)
                            ->first();

                            if($getLeave == '' && $getAbsent == ''){
                                $status = $this->absentCreate($getEmployee->associate_id, $getEmployee->as_unit_id, $date);
                                if($status == 'success'){ 
                                    $yearMonth = $year.'-'.$month; 
                                    if($month == date('m')){
                                        $totalDay = date('d');
                                    }else{
                                        $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                                    }
                                    $queue = (new ProcessUnitWiseSalary($this->tableName, $month, $year, $getEmployee->as_id, $totalDay))
                                            ->onQueue('salarygenerate')
                                            ->delay(Carbon::now()->addSeconds(2));
                                            dispatch($queue);
                                }
                            }
                        }
                    } 
                }
            }
        }

        // update cache
        cache_daily_operation($this->unitId);
    }

    public function absentCreate($associateId, $unitId, $date)
    {
        try {
            $id = DB::table('hr_absent')
            ->insertGetId([
                'associate_id' => $associateId,
                'hr_unit'  => $unitId,
                'date'  => $date
            ]); 

            if(!empty($id)){
                return 'success';
            }else{
                return 'error';
            }
        } catch (\Exception $e) {
            /*$bug = $e->errorInfo[1];
            // $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                return 'duplicate';
            }*/
            return 'error';
        }
    }
}
