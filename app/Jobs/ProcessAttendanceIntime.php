<?php

namespace App\Jobs;

use App\Helpers\EmployeeHelper;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Shift;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAttendanceIntime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;
    public $tableName;
    public $tId;
    public $unitId;
    public function __construct($tableName, $tId, $unitId)
    {
        $this->tableName = $tableName;
        $this->tId = $tId;
        $this->unitId = $unitId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $getEmpAtt = DB::table($this->tableName)->where('id', $this->tId)->first();
        if($getEmpAtt != null){
            $getEmployee = Employee::
                where('as_id', $getEmpAtt->as_id)
                ->first();
            if($getEmployee != null && $getEmployee->shift != null){

                // check today holiday but working day count
                $today = Carbon::parse($getEmpAtt->in_date)->format('Y-m-d');
                $year = Carbon::parse($getEmpAtt->in_time)->format('Y');
                $month = Carbon::parse($getEmpAtt->in_time)->format('m');
                $yearMonth = $year.'-'.$month; 
                //check absent table if exists then delete
                $getAbsent = Absent::
                where('date', $today)
                ->where('associate_id', $getEmployee->associate_id)
                ->first();
                if($getAbsent != null){
                    Absent::
                    where('id', $getAbsent->id)
                    ->delete();
                }

                // check today holiday but working day count
                $dayStatus = EmployeeHelper::employeeDateWiseStatus($today, $getEmployee->associate_id, $getEmployee->as_unit_id, $getEmployee->shift_roaster_status);
                
                $cIn = strtotime(date("H:i", strtotime($getEmpAtt->in_time)));
                
                // -----
                $unitId = $getEmployee->as_unit_id;
                $day_of_date = date('j', strtotime($getEmpAtt->in_time));
                $day_num = "day_".$day_of_date;
                $shift= DB::table("hr_shift_roaster")
                ->where('shift_roaster_month', $month)
                ->where('shift_roaster_year', $year)
                ->where("shift_roaster_user_id", $getEmployee->as_id)
                ->select([
                    $day_num,
                    'hr_shift.hr_shift_id',
                    'hr_shift.hr_shift_start_time',
                    'hr_shift.hr_shift_end_time',
                    'hr_shift.hr_shift_break_time',
                    'hr_shift.hr_shift_name'
                ])
                ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                    $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                    $q->where('hr_shift.hr_shift_unit_id', $unitId);
                })
                ->orderBy('hr_shift.hr_shift_id', 'desc')
                ->first();
                
                if(!empty($shift) && $shift->$day_num != null){
                    $cShifStart = strtotime(date("H:i", strtotime($shift->hr_shift_start_time)));
                    $cShifEnd = strtotime(date("H:i", strtotime($shift->hr_shift_end_time)));
                    $cBreak = $shift->hr_shift_break_time*60;
                    $shiftName = $shift->hr_shift_name;
                }
                else{
                    $cShifStart = strtotime(date("H:i", strtotime($getEmployee->shift['hr_shift_start_time'])));
                    $cShifEnd = strtotime(date("H:i", strtotime($getEmployee->shift['hr_shift_end_time'])));
                    $cBreak = $getEmployee->shift['hr_shift_break_time']*60;
                    $shiftName = $getEmployee->as_shift_id;
                }
                
                $cOut = null;
                $overtimes = 0;
                //late count
                $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($getEmployee->as_unit_id, $shiftName);
                if($getLateCount != null){
                    if($today >= $getLateCount->date_from && $today <= $getLateCount->date_to){
                        $lateTime = $getLateCount->value;
                    }else{
                        $lateTime = $getLateCount->default_value;
                    }
                }else{
                    $lateTime = 2;
                }
                $inTime = ($cShifStart+($lateTime * 60));

                if($dayStatus == 'OT'){
                    $late = 0;
                }else if($cIn > $inTime  || $getEmpAtt->remarks == 'DSI'){
                    $late = 1;
                }else{
                    $late = 0;
                }

                // update attendance table ot_hour   
                DB::table($this->tableName)
                ->where('id', $this->tId)
                ->update([
                    'late_status' => $late
                ]);

                $queuesal = 'salarygenerate';
                if($this->unitId == 2){
                    $queuesal = 'ceilsalarygenerate';
                }
                if($day_of_date == 31){
                    if($month == date('m')){
                        $totalDay = date('d');
                    }else{
                        $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                    }
                    $queue = (new ProcessUnitWiseSalary($this->tableName, $month, $year, $getEmployee->as_id, $totalDay))
                            ->onQueue($queuesal)
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
                }
                 
            }
        }
            
    }
}
