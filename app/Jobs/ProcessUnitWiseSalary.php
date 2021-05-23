<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Hr\Benefits;
use App\Repository\Hr\AttendanceProcessRepository;
use App\Repository\Hr\SalaryRepository;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class ProcessUnitWiseSalary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    // public $tries = 3;
    public $timeout=500;
    public $tableName;
    public $month;
    public $year;
    public $asId;
    public $totalDay;
    
    public function __construct($tableName, $month, $year, $asId, $totalDay)
    {
        $this->tableName = $tableName;
        $this->month = $month;
        $this->year = $year;
        $this->asId = $asId;
        $this->totalDay = $totalDay;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttendanceProcessRepository $attProcess, SalaryRepository $salary)
    {

        $getEmployee = Employee::where('as_id', $this->asId)->first(['as_id', 'as_doj', 'as_unit_id', 'associate_id', 'as_status_date', 'as_ot', 'shift_roaster_status', 'as_emp_type_id', 'as_designation_id', 'as_subsection_id', 'as_location', 'as_status', 'as_gender']);
        
        $row['month'] = $this->month;
        $row['year'] = $this->year;
        $row['yearMonth'] = date('Y-m', strtotime($row['year'].'-'.$row['month']));
        $row['totalDay'] = $this->totalDay;
        $row['tableName'] = get_att_table($getEmployee->as_unit_id);
        try {
            if($getEmployee != null && date('Y-m', strtotime($getEmployee->as_doj)) <= $row['yearMonth']){
                $row['unit_id'] = $getEmployee->as_unit_id;
                // check lock month
                $checkLock = monthly_activity_close($row);
                if($checkLock == 1){
                    return 'error';
                }
                // get employee benefit
                $getBenefit = Benefits::getEmployeeAssIdwise($getEmployee->associate_id);
                if($getBenefit == null){
                    return 'error';
                }

                $row = array_merge($row, $getEmployee->toArray());
                // employee basic info
                $startNendInfo = $attProcess->getEmployeeMonthStartNEndInfo($row);
                $row = array_merge($row, $startNendInfo);
                // attendance count like - present, holiday, leave, absent, late etc.
                $attCount = $attProcess->makeEmployeeAttendanceCount($row);
                $row = array_merge($row, $getBenefit->toArray(), $attCount);
                // all benefit calculate for pay
                $benefit = $salary->makeEmployeeBenefitValue($row);
             
                // salary store
                $salary->slaryStore($benefit);
            }
            return 'success';

        } catch (\Exception $e) {
            DB::table('error')->insert(['msg' => $this->asId.' '.$e->getMessage()]);
            /*$bug = $e->errorInfo[1];
            // $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                // duplicate
            }*/
        }
    }
}
