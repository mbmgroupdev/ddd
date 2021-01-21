<?php

namespace App\Jobs;

use App\Helpers\EmployeeHelper;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\Bills;
use App\Models\Hr\HrLateCount;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAttendanceFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 2;
    public $tableName;
    public $tId;
    public $shiftName;
    public $shiftStartTime;
    public $billEligible;
    public $shiftNightFlag;
    public function __construct($tableName, $tId, $shiftName, $shiftStartTime, $billEligible, $shiftNightFlag)
    {
        $this->tableName = $tableName;
        $this->tId = $tId;
        $this->shiftName = $shiftName;
        $this->shiftStartTime = $shiftStartTime;
        $this->billEligible = $billEligible;
        $this->shiftNightFlag = $shiftNightFlag;
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
                $today = Carbon::parse($getEmpAtt->in_date)->format('Y-m-d');
                
                // check today holiday but working day count
                $dayStatus = EmployeeHelper::employeeDateWiseStatus($today, $getEmployee->associate_id, $getEmployee->as_unit_id, $getEmployee->shift_roaster_status);
                
                $cIn = strtotime(date("H:i", strtotime($getEmpAtt->in_time)));
                $cOut = strtotime(date("H:i", strtotime($getEmpAtt->out_time)));
                // -----
                $unitId = $getEmployee->as_unit_id;

                //late count
                $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($getEmployee->as_unit_id, $this->shiftName);
                if($getLateCount != null){
                    if($today >= $getLateCount->date_from && $today <= $getLateCount->date_to){
                        $lateTime = $getLateCount->value;
                    }else{
                        $lateTime = $getLateCount->default_value;
                    }
                }else{
                    $lateTime = 2;
                }
                $shiftStartTime = strtotime(date("H:i", strtotime($this->shiftStartTime)));
                $inTime = ($shiftStartTime+($lateTime * 60));

                if($dayStatus == 'OT'){
                    $late = 0;
                }else if($cIn > $inTime  || $getEmpAtt->remarks == 'DSI'){
                    $late = 1;
                }else{
                    $late = 0;
                }

                // get line id
                $lineFloorInfo = DB::table('hr_station')
                ->where('associate_id', $getEmployee->associate_id)
                ->whereDate('start_date','<=',$today)
                ->where(function ($q) use($today) {
                  $q->whereDate('end_date', '>=', $today);
                  $q->orWhereNull('end_date');
                })
                ->first();
                // attendance update
                DB::table($this->tableName)
                ->where('id', $this->tId)
                ->update([
                    'line_id' => !empty($lineFloorInfo->changed_line)?$lineFloorInfo->changed_line:$getEmployee->as_line_id,
                    'late_status' => $late
                ]);

                //check absent and delete
                $getAbsent = Absent::
                where('date', $today)
                ->where('associate_id', $getEmployee->associate_id)
                ->delete();

                // bill calculation
                if($getEmpAtt->out_time != null && ($this->billEligible != null || $this->billEligible != '00:00:00')){
                    $indate = $getEmpAtt->in_date;
                    if($this->shiftNightFlag == 1){
                        $indate = Carbon::createFromFormat('Y-m-d', $indate);
                        $indate = $indate->copy()->addDays(1);
                    }
                    $shiftElig = strtotime($indate.' '.$this->billEligible);

                    if(strtotime($getEmpAtt->out_time) > $shiftElig){
                        $bill = EmployeeHelper::dailyBillCalculation($getEmployee->as_ot, $getEmployee->as_unit_id, $getEmpAtt->in_date, $getEmpAtt->as_id, $this->shiftNightFlag, $getEmployee->as_designation_id);
                    }else{
                        $getBill = Bills::where('as_id', $getEmpAtt->as_id)->where('bill_date', $getEmpAtt->in_date)->first();
                        if($getBill != null){
                            Bills::where('id', $getBill->id)->delete();
                        }
                    }

                }
            }
        }
    }
}
