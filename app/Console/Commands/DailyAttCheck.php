<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAttendanceInOutTime;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessEmployeeAbsent;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class DailyAttCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily attendance check from aql table at 12 PM ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $newAtt= DB::table('hr_attendance_aql')->where('flag', 0)->get();
        foreach ($newAtt as $key => $att) {

            if($att->out_time== null){
                $queue = (new ProcessAttendanceIntime('hr_attendance_aql', $att->id , 3))
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);

            }else{
                $queue = (new ProcessAttendanceInOutTime('hr_attendance_aql', $att->id, 3))
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);
            }

            DB::table('hr_attendance_aql')->where('id', $att->id)->update(['flag'=>1]);

            $this->info('Attendance record for row no '.$att->id.' has been inserted.');           
        }

        //absent check aql
        $date[] = date('Y-m-d');
        $queue = (new ProcessEmployeeAbsent('hr_attendance_aql', $date, '3'))
        ->delay(Carbon::now()->addSeconds(2));
        dispatch($queue);
    }
}
