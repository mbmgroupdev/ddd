<?php

namespace App\Jobs;

use App\Models\Hr\AttendanceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAttendanceHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 2;
    public $rowData;
    public function __construct($rowData)
    {
        $this->rowData = $rowData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            AttendanceHistory::insertOrIgnore($this->rowData);

            // AttendanceHistory::insertOrIgnore([
            //     'as_id' => $this->asId,
            //     'unit_id' => $this->unitId,
            //     'att_date' => $this->date,
            //     'raw_data' => $this->record
            // ]);
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
