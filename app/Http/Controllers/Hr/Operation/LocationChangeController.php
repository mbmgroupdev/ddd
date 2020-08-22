<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceInOutTime;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessAttendanceOuttime;
use App\Models\Hr\Employee;
use App\Models\Hr\Location;
use App\Models\Hr\Outsides;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use DB,Response, Validator;
use Illuminate\Http\Request;

class LocationChangeController extends Controller
{

    //show Locaiton Change List
    public function showList(){
        $requestList= Outsides::orderBy('id', 'DESC')->get();

        foreach ($requestList as $value) {
            if(is_numeric($value->requested_location)){
                $value->location_name= Location::where('hr_location_id', $value->requested_location)->pluck('hr_location_name')->first();
            }
            else{
                $value->location_name= $value->requested_location;
            }
        }
        // dd($requestList);
        return view('hr/operation/location_change_list', compact('requestList'));
    }
    public function getTableName($unit)
        {
            $tableName = "";
            //CEIL
            if($unit == 2){
               $tableName= "hr_attendance_ceil AS a";
            }
            //AQl
            else if($unit == 3){
                $tableName= "hr_attendance_aql AS a";
            }
            // MBM
            else if($unit == 1 || $unit == 4 || $unit == 5 || $unit == 9){
                $tableName= "hr_attendance_mbm AS a";
            }
            //HO
            else if($unit == 6){
                $tableName= "hr_attendance_ho AS a";
            }
            // CEW
            else if($unit == 8){
                $tableName= "hr_attendance_cew AS a";
            }
            else{
                $tableName= "hr_attendance_mbm AS a";
            }
            return $tableName;
        }

    //approve Location
    public function approveLocation(Request $request){
        //dd($request->all());exit;
        $employee = Employee::where('associate_id',$request->as_id)->first();
        $table = $this->getTableName($employee->as_unit_id);
        $tableNmae = explode(' ',$table);
                    //dd($employee);exit;
        $id= $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $totalDays  = (date('d', strtotime($end_date))-date('d', strtotime($start_date)));
        //dd($totalDays);exit;
        DB::beginTransaction();
        try {
            Outsides::where('id', $id)
                    ->update([
                        'status' => 1, 
                        'approved_on' => date('Y-m-d H:i:s'), 
                        'approved_by' => auth()->user()->associate_id
                    ]);

                    if($request->type == 1){
                        //1=full day                        
                        for($i=0; $i<=$totalDays; $i++) {
                          $date = date('Y-m-d', strtotime("+".$i." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($tableNmae[0])
                                         ->insertGetId([
                                             'as_id' => $employee->as_id,
                                             'in_date' => $date,
                                             'in_time' => $date.' '.$employee->shift['hr_shift_start_time'],
                                             'out_time'=> $date.' '.$outtime,
                                             'hr_shift_code' => $employee->shift['hr_shift_code'],
                                             'ot_hour' => 0,
                                             'late_status' => 0,
                                             'remarks'=>'BM',
                                             'updated_by' => auth()->user()->associate_id,
                                             'updated_at' => NOW()
                                            ]);
                                         $queue = (new ProcessAttendanceInOutTime($tableNmae[0], $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);
                        }
                    }elseif($request->type == 2){
                       //2=1st half 
                         for($i=0; $i<=$totalDays; $i++) {                   
                          $date = date('Y-m-d', strtotime("+".$i." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($tableNmae[0])
                                         ->insertGetId([
                                             'as_id' => $employee->as_id,
                                             'in_date' => $date,
                                             'in_time' => $date.' '.$employee->shift['hr_shift_start_time'],
                                             'out_time'=> '',
                                             'hr_shift_code' => $employee->shift['hr_shift_code'],
                                             'ot_hour' => 0,
                                             'late_status' => 0,
                                             'remarks'=>'BM',
                                             'updated_by' => auth()->user()->associate_id,
                                             'updated_at' => NOW()
                                            ]);
                                         $queue = (new ProcessAttendanceIntime($tableNmae[0], $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);

                        }

                    }elseif($request->type == 3){
                        //3=2nd half
                        for($i=0; $i<=$totalDays; $i++) { 
                          $date = date('Y-m-d', strtotime("+".$i." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($tableNmae[0])
                                         ->insertGetId([
                                             'as_id' => $employee->as_id,
                                             'in_date' => $date,
                                             'in_time' => '',
                                             'out_time'=> $date.' '.$outtime,
                                             'hr_shift_code' => $employee->shift['hr_shift_code'],
                                             'ot_hour' => 0,
                                             'late_status' => 0,
                                             'remarks'=>'BM',
                                             'updated_by' => auth()->user()->associate_id,
                                             'updated_at' => NOW()
                                            ]);
                                          $queue = (new ProcessAttendanceOuttime($tableNmae[0], $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);

                        }                        
                    }
            DB::commit();
            return back()
                ->with('success', "Outside Request Approved");
        } catch (\Exception $e) {
            DB::rollback();
            $msg= $e->getMessage();
            return back()
                ->with('error', $msg);
        }
    }

    //Reject Location
    public function rejectLocation(Request $request){
        $id= $request->id;
        DB::beginTransaction();
        try {
            Outsides::where('id', $id)
                    ->update([
                        'status' => 2, 
                        'approved_on' => date('Y-m-d H:i:s'), 
                        'approved_by' => auth()->user()->associate_id
                    ]);

            DB::commit();
            return back()
                ->with('error', "Outsides Request Rejected");
        } catch (\Exception $e) {
            DB::rollback();
            $msg= $e->getMessage();
            return back()
                ->with('error', $msg);
        }
    }

    //Show Location Change enttry Form
    public function showForm(){

        $employees = Employee::getSelectIdNameEmployee();
        // $units = Unit::unitListAsObject();
        $locationList= Location::pluck('hr_location_name', 'hr_location_id');
        $locationList['Outside']= "Outside";
        return view('hr/operation/location_change_entry', compact('employees', 'locationList'));
    }

    //store form data
    public function storeData(Request $request){
        $validator= Validator::make($request->all(),[
            'employee_id'           => 'required',
            'requested_location'    => 'required',
            'type'                  => 'required',
            'from_date'             => 'required',
            'to_date'               => 'required'
        ]);
        if($validator->fails()){
            return back()
                ->withErrors($validator);
        }
        else{

            //dd($request->all());exit;
            
            DB::beginTransaction();
            try {
                $approved_on= date('Y-m-d H:i:s');
                $applied_on= date('Y-m-d H:i:s');
                $approved_by= auth()->user()->associate_id;
                // $ids= [];
                for($i=0; $i< sizeof($request->employee_id); $i++){
                    $out= new Outsides();
                    $out->as_id = $request->employee_id[$i];
                    $out->start_date = $request->from_date[$i];
                    $out->end_date = $request->to_date[$i];
                    $out->requested_location = $request->requested_location[$i];
                    $out->requested_place = $request->requested_place[$i];
                    $out->type = $request->type[$i];
                    $out->comment = $request->comment[$i];
                    $out->status = 1;
                    $out->applied_on = $request->applied_on;
                    $out->approved_on = $request->approved_on;
                    $out->approved_by = $request->approved_by;
                    $out->save();
                    $ids= $out->id;

                    $employee = Employee::where('associate_id',$request->employee_id[$i])->first();
                    $table = $this->getTableName($employee->as_unit_id);
                    $tableNmae = explode(' ',$table);
                                //dd($request->all());exit;
                    
                    $start_date = $request->from_date[$i];
                    $end_date = $request->to_date[$i];
                    $totalDays  = (date('d', strtotime($end_date))-date('d', strtotime($start_date)));

                  if($request->type[$i] == 1){
                        //1=full day                        
                        for($j=0; $j<=$totalDays; $j++) {
                          $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($tableNmae[0])
                                         ->insertGetId([
                                             'as_id' => $employee->as_id,
                                             'in_date' => $date,
                                             'in_time' => $date.' '.$employee->shift['hr_shift_start_time'],
                                             'out_time'=> $date.' '.$outtime,
                                             'hr_shift_code' => $employee->shift['hr_shift_code'],
                                             'ot_hour' => 0,
                                             'late_status' => 0,
                                             'remarks'=>'BM',
                                             'updated_by' => auth()->user()->associate_id,
                                             'updated_at' => NOW()
                                            ]);
                                $queue = (new ProcessAttendanceInOutTime($tableNmae[0], $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);
                        }
                    }elseif($request->type[$i] == 2){
                       //2=1st half 
                         for($j=0; $j<=$totalDays; $j++) {                   
                          $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($tableNmae[0])
                                         ->insertGetId([
                                             'as_id' => $employee->as_id,
                                             'in_date' => $date,
                                             'in_time' => $date.' '.$employee->shift['hr_shift_start_time'],
                                             'out_time'=> null,
                                             'hr_shift_code' => $employee->shift['hr_shift_code'],
                                             'ot_hour' => 0,
                                             'late_status' => 0,
                                             'remarks'=>'BM',
                                             'updated_by' => auth()->user()->associate_id,
                                             'updated_at' => NOW()
                                            ]);
                                         $queue = (new ProcessAttendanceIntime($tableNmae[0], $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);

                        }

                    }elseif($request->type[$i] == 3){
                        //3=2nd half
                        for($j=0; $j<=$totalDays; $j++) { 
                          $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($tableNmae[0])
                                         ->insertGetId([
                                             'as_id' => $employee->as_id,
                                             'in_date' => $date,
                                             'in_time' => null,
                                             'out_time'=> $date.' '.$outtime,
                                             'hr_shift_code' => $employee->shift['hr_shift_code'],
                                             'ot_hour' => 0,
                                             'late_status' => 0,
                                             'remarks'=>'BM',
                                             'updated_by' => auth()->user()->associate_id,
                                             'updated_at' => NOW()
                                            ]);
                                          $queue = (new ProcessAttendanceOuttime($tableNmae[0], $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);

                        }                        
                    }

                }

                $this->logFileWrite("Location Changed", $ids);
                DB::commit();
                return back()
                    ->with("success", "Outside Entry Successfull.");
                
            } catch (\Exception $e) {
                DB::rollback();
                $msg= $e->getMessage();
                return back()
                    ->withErrors($msg);
            }
        }
    }

    public function index(){
    	$employees = Employee::getSelectIdNameEmployee();
    	// dd($employees);
    	$units = Unit::unitListAsObject();
    	return view('hr.unitchange.employee_unit_change', compact('employees', 'units') );
    }

    //get unit info
    public function getUnit(Request $req){
    	$unit_name_id = DB::table('hr_as_basic_info as emp')
                            ->join('hr_unit as u', 'u.hr_unit_id', '=', 'emp.as_unit_id')
    						->where('emp.associate_id', '=', $req->emp_id )
    						->select([
    							'u.hr_unit_id', 'u.hr_unit_name'
    						])
                            ->first();

    	return Response::json($unit_name_id);
    }

    public function entrySave(Request $request){
    	dd($request->all());

        
    }

    public function unitChangeList(){
    	return view('hr.unitchange.employee_unit_change_list');
    }
}
