<?php
namespace App\Http\Controllers\Hr\TimeAttendance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMonthlySalary;
use App\Models\Employee;
use App\Jobs\ProcessUnitWiseSalary;
use Carbon\Carbon;
use DB ,DataTables;

class FridayShiftController extends Controller
{
	protected $shift;

	public function __construct()
	{
		$this->shift = collect(shift_by_code())
			->where('hr_shift_name','Friday OT')
			->first();
	}
	public function otUpdate(Request $request)
	{
		return $request->all();
	}

	public function save(Request $request)
	{
		if($request->intime && $request->outtime){

			$dt = array(
                'in_date' => $request->date,
                'as_id' => $request->as_id,
                'hr_shift_code' => $this->shift['hr_shift_code']
            );
            $start =  $request->date." ".$this->shift['hr_shift_start_time'];
            $intime =  $request->date." ".$request->intime;
            $outtime =  $request->date." ".$request->outtime;

            $dt['ot_hour'] = $this->fullot($intime, $start, $outtime,  $this->shift['hr_shift_break_time']);
            $dt['in_time'] = $intime;
            $dt['out_time'] = $outtime;
            
                
            DB::table('hr_att_special')
                ->insert($dt);


            $queue = (new ProcessUnitWiseSalary('hr_attendance_cew', date('m', strtotime($request->date)), date('Y', strtotime($request->date)), $request->as_id, date('t', strtotime($request->date))))
                    ->onQueue('salarygenerate')
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);

            return response(['status' => true]);
		}

		return response(['status' => false]);
	}

	public function fullot($start, $shift_start, $end, $break)
    {
        $start = $start < $shift_start? $shift_start:$start;
        $diff = (strtotime($end) - (strtotime($start) + ($break*60)))/3600;
        $diff = $diff < 0 ? 0:$diff;

        $part    = explode('.', $diff);
        $minutes = (isset($part[1]) ? $part[1] : 0);
        $minutes = floatval('0.'.$minutes);
        // return $minutes;
        if($minutes > 0.16667 && $minutes <= 0.75) $minutes = $minutes;
        else if($minutes >= 0.75) $minutes = 1;
        else $minutes = 0;
        
        if($minutes > 0 && $minutes != 1){
            $min = (int)round($minutes*60);
            $minOT = min_to_ot();
            $minutes = $minOT[$min]??0;
        }

        $overtimes = $part[0] + $minutes;
        $overtimes = number_format((float)$overtimes, 3, '.', '');

        return  $overtimes;
    }
}