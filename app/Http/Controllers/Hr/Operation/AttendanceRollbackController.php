<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use Illuminate\Http\Request;
use DB;
class AttendanceRollbackController extends Controller
{
    public function index()
    {
    	return view('hr.operation.attendance_rollback.index');
    }

    public function getDate(Request $request)
    {
        $input = $request->all();
        $tableName = get_att_table($input['unit']);
        // return $tableName;
        try {
            $getAtt = DB::table($tableName)
            ->select('in_date')
            ->orderBy('in_date', 'desc')
            ->first();
            if($getAtt != null){
                $data = date('Y-m-d', strtotime($getAtt->in_date));
                return response()->json(['type'=>'success','value'=> $data]);
            }else{
                return response()->json(['type'=>'error','message'=>"No Date Found!"]);
            }

        } catch (\Exception $e) {
            return response()->json(['type'=>'error','message'=>"Something wrong, plese try again"]);
        }
    }

    public function process(Request $request)
    {
    	$input = $request->all();
    	$tableName = get_att_table($input['unit']);
    	DB::beginTransaction();
    	try {
    		// date wise attendance table data delete
    		$getAtt = DB::table($tableName)
    		->where('in_time', 'LIKE', $input['day']."%")
    		->delete();

    		// date wise absent table data delete
    		$getAtt = Absent:: where('date', $input['day']);
            if($input['unit'] == 1){
                $getAtt->whereIn('hr_unit', [1,4,5]);
            }else{

    		  $getAtt->where('hr_unit', $input['unit']);
            }
    		$getAtt->delete();

    		// process salary 
    		/*$year = Carbon::parse($getEmpAtt->in_time)->format('Y');
            $month = Carbon::parse($getEmpAtt->in_time)->format('m');
            $yearMonth = $year.'-'.$month; 
            if($month == date('m')){
                $totalDay = date('d');
            }else{
                $totalDay = Carbon::parse($yearMonth)->daysInMonth;
            }
            $queue = (new ProcessUnitWiseSalary($this->tableName, $month, $year, $getEmployee->as_id, $totalDay))
                    ->onQueue('salarygenerate')
                    ->delay(Carbon::now()->addSeconds(2));
                    dispatch($queue);*/
            DB::commit();
            $this->logFileWrite("Attendance file rollback ".$tableName ." - ".$input['day'], $input['unit']);
            return redirect()->back()->with('success', "Processing Successfully Done.");
    	
    	} catch (\Exception $e) {
    		DB::rollback();
    		$bug = $e->getMessage();
    		return redirect()->back()->with('error', $bug);
    	}
    }
}
