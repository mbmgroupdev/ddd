<?php

namespace App\Jobs;

use App\Helpers\EmployeeHelper;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Shift;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAttendanceInOutTime implements ShouldQueue
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
        $getEmployee = Employee::
            where('as_id', $getEmpAtt->as_id)
            ->first();
        // ------------------------------------------------- 
        //punch_in punch out

        if($getEmployee != null && $getEmployee->shift != null){
            $today = Carbon::parse($getEmpAtt->in_date)->format('Y-m-d');
            $year = Carbon::parse($getEmpAtt->in_time)->format('Y');
            $month = Carbon::parse($getEmpAtt->in_time)->format('m');
            //check absent table if exists then delete
            $getAbsent = Absent::
            where('hr_unit', $getEmployee->as_unit_id)
            ->where('date', $today)
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
            $cOut = strtotime(date("H:i", strtotime($getEmpAtt->out_time)));
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
                'hr_shift.hr_shift_night_flag',
                'hr_shift.hr_shift_name',
                'hr_shift.bill_eligible'
            ])
            ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                $q->where('hr_shift.hr_shift_unit_id', $unitId);
            })
            ->orderBy('hr_shift.hr_shift_id', 'desc')
            ->first();
            
            if(!empty($shift) && $shift->$day_num != null){
                $cShifStartTime = strtotime(date("H:i", strtotime($shift->hr_shift_start_time)));
                $cShifStart = $shift->hr_shift_start_time;
                $cShifEnd = $shift->hr_shift_end_time;
                $cBreak = $shift->hr_shift_break_time;
                $nightFlag = $shift->hr_shift_night_flag;
                $shiftName = $shift->hr_shift_name;
                $billEligible = $shift->bill_eligible;
            }
            else{
                $cShifStartTime = strtotime(date("H:i", strtotime($getEmployee->shift['hr_shift_start_time'])));
                $cShifStart = $getEmployee->shift['hr_shift_start_time'];
                $cShifEnd = $getEmployee->shift['hr_shift_end_time'];
                $cBreak = $getEmployee->shift['hr_shift_break_time'];
                $nightFlag = $getEmployee->shift['hr_shift_night_flag'];
                $shiftName = $getEmployee->as_shift_id;
                $billEligible = $getEmployee->shift['bill_eligible'];
            }

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
            $inTime = ($cShifStartTime+($lateTime * 60));

            if($dayStatus == 'OT'){
                $late = 0;
            }else if($cIn > $inTime  || $getEmpAtt->remarks == 'DSI'){
                $late = 1;
            }else{
                $late = 0;
            }

            // CALCULATE OVER TIME
            if(!empty($cOut))
            {
                if($getEmployee->as_ot == 1 && $getEmpAtt->remarks != 'DSI'){
                    $otHour = EmployeeHelper::daliyOTCalculation($getEmpAtt->in_time, $getEmpAtt->out_time, $cShifStart, $cShifEnd, $cBreak, $nightFlag, $getEmployee->associate_id, $getEmployee->shift_roaster_status, $getEmployee->as_unit_id);
                }else{
                    $otHour = 0;
                }

                // update attendance table ot_hour   
                DB::table($this->tableName)
                ->where('id', $this->tId)
                ->update([
                    'ot_hour' => $otHour,
                    'late_status' => $late
                ]);

                // bill announce 

                if($getEmployee->as_ot == 1 && $billEligible != null){
                    if($cOut > strtotime(date("H:i", strtotime($billEligible)))){

                        $bill = EmployeeHelper::dailyBillCalculation($getEmployee->as_unit_id, $getEmpAtt->in_date, $getEmpAtt->as_id, $nightFlag, $getEmployee->as_designation_id);
                    }
                }
                
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
