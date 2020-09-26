<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use Illuminate\Http\Request;
use DB;

class ShiftRosterController extends Controller
{
    public function assignMulti(Request $request)
    {
    	$input = $request->all();
    	$data['type'] = 'error';
    	DB::beginTransaction();
    	try {
    		$shift = Shift::getShiftNameGetId($input['target_shift']);
    		foreach ($input['associate'] as $key => $ass_id) {
                for($j=$input['start_day']; $j<=$input['end_day']; $j++)
                {
                    $day= "day_".$j;
                    $roster = ShiftRoaster::where('shift_roaster_associate_id', $ass_id)
                    ->where('shift_roaster_year', date('Y'))
                    ->where('shift_roaster_month', date('n'))
                    ->first();

                    
                    if($roster != null){
                        $roster->update([$day => $shift]);
                        $getId = $roster->shift_roaster_id;
                    }else{
                    	$getBasic = Employee::getEmployeeAssociateIdWise($ass_id);
                        $getId = ShiftRoaster::create([
                            'shift_roaster_associate_id' => $ass_id,
                            'shift_roaster_user_id' => $getBasic->as_id,
                            'shift_roaster_year' => date('Y'),
                            'shift_roaster_month' => date('n'),
                            $day => $shift
                        ])->shift_roaster_id;
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
