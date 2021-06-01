<?php

namespace App\Jobs;

use App\Repository\Hr\SalaryRepository;
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
    public function handle(SalaryRepository $salary)
    {
        $salary->employeeMonthlySalaryProcess($this->asId, $this->month, $this->year, $this->totalDay);
    }
}
