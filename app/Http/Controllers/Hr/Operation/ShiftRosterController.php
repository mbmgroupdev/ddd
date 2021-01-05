<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use Illuminate\Http\Request;
use App\Jobs\ProcessAttendanceOuttime;
use Carbon\Carbon;
use DB;

class ShiftRosterController extends Controller
{
    public function assignMulti(Request $request)
    {
    	$input = $request->all();
    	$data['type'] = 'error';
    	DB::beginTransaction();
    	try {
    		$shift = $input['target_shift'];
            if (isset($request->month)) {
                 $year = date('Y', strtotime($request->month));
                 $month = date('n', strtotime($request->month));
                 $m = date('Y-m', strtotime($request->month));
             } else{
                $year = date('Y');
                $month = date('n');
                $m = date('Y-m');
             }
    		foreach ($input['associate'] as $key => $ass_id) {
                $emp = DB::table('hr_as_basic_info')
                        ->where('associate_id',$ass_id)
                        ->first();
                $att_table = get_att_table($emp->as_unit_id);
                for($j=$input['start_day']; $j<=$input['end_day']; $j++)
                {
                    $day= "day_".$j;
                    $roster = ShiftRoaster::where('shift_roaster_associate_id', $ass_id)
                    ->where('shift_roaster_year', $year)
                    ->where('shift_roaster_month', $month)
                    ->first();

                    
                    if($roster != null){
                        $roster->update([$day => $shift]);
                        $getId = $roster->shift_roaster_id;
                    }else{
                    	$getBasic = Employee::getEmployeeAssociateIdWise($ass_id);
                        $getId = ShiftRoaster::create([
                            'shift_roaster_associate_id' => $ass_id,
                            'shift_roaster_user_id' => $getBasic->as_id,
                            'shift_roaster_year' => $year,
                            'shift_roaster_month' => $month,
                            $day => $shift
                        ])->shift_roaster_id;

                    }
                    $date = date('Y-m-d', strtotime($m.'-'.$j));
                    if($date <= date('Y-m-d')){
                        $att = DB::table($att_table)->where('as_id',$emp->as_id)->where('in_date',$date)->first();
                        if($att){

                            $queue = (new ProcessAttendanceOuttime($att_table, $att->id, $emp->as_unit_id))
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);
                        }
                    }

                    log_file_write("Shift Roster Day Wise Updated", $getId);
                }
            }
            DB::commit();
            $data['type'] = 'success';
            $data['message'] = "Shift Roster Assign Successfully Done";
    		return $data;
    	} catch (\Exception $e) {
    		DB::rollback();
    		$data['message'] = $e->getMessage();
    		return $data;
    	}
    }
}
