<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceInOutTime;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessAttendanceOuttime;
use App\Models\Employee;
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
    
    //approve Location
    public function approveLocation(Request $request){
        //dd($request->all());exit;
        $employee = Employee::where('associate_id',$request->as_id)->first();
        $table = get_att_table($employee->as_unit_id);
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
                          $lastPunchId = DB::table($table)
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
                                         $queue = (new ProcessAttendanceInOutTime($table, $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);
                        }
                    }elseif($request->type == 2){
                       //2=1st half 
                         for($i=0; $i<=$totalDays; $i++) {                   
                          $date = date('Y-m-d', strtotime("+".$i." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($table)
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
                                         $queue = (new ProcessAttendanceIntime($table, $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);

                        }

                    }elseif($request->type == 3){
                        //3=2nd half
                        for($i=0; $i<=$totalDays; $i++) { 
                          $date = date('Y-m-d', strtotime("+".$i." day", strtotime($start_date)));
                          $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                          //dd($outtime);exit;
                          $lastPunchId = DB::table($table)
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
                                          $queue = (new ProcessAttendanceOuttime($table, $lastPunchId, $employee->as_unit_id))
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
    public function storeData(Request $request)
    {
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
            
            DB::beginTransaction();
            try {
                $approved_on= date('Y-m-d H:i:s');
                $applied_on= date('Y-m-d H:i:s');
                $approved_by= auth()->user()->associate_id;

                $out= new Outsides();
                $out->as_id = $request->employee_id;
                $out->start_date = $request->from_date;
                $out->end_date = $request->to_date;
                $out->requested_location = $request->requested_location;
                $out->requested_place = $request->requested_place;
                $out->type = $request->type;
                $out->comment = $request->comment;
                $out->status = 1;
                $out->applied_on = $applied_on;
                $out->approved_on = $approved_on;
                $out->approved_by = $approved_by;
                $out->save();
                $ids= $out->id;

                $employee = Employee::where('associate_id',$request->employee_id)->first();
                $table = get_att_table($employee->as_unit_id);
                
                $start_date = $request->from_date;
                $end_date = $request->to_date;
                $totalDays  = (date('d', strtotime($end_date))-date('d', strtotime($start_date)));

              if($request->type == 1){
                    //1=full day                        
                    for($j=0; $j<=$totalDays; $j++) {
                      $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));
                      $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                      //dd($outtime);exit;
                      $lastPunchId = DB::table($table)
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
                            $queue = (new ProcessAttendanceInOutTime($table, $lastPunchId, $employee->as_unit_id))
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);
                    }
                }elseif($request->type == 2){
                   //2=1st half 
                     for($j=0; $j<=$totalDays; $j++) {                   
                      $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));
                      $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                      //dd($outtime);exit;
                      $lastPunchId = DB::table($table)
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
                                     $queue = (new ProcessAttendanceIntime($table, $lastPunchId, $employee->as_unit_id))
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);

                    }

                }elseif($request->type == 3){
                    //3=2nd half
                    for($j=0; $j<=$totalDays; $j++) { 
                      $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));
                      $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));
                      //dd($outtime);exit;
                      $lastPunchId = DB::table($table)
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
                                      $queue = (new ProcessAttendanceOuttime($table, $lastPunchId, $employee->as_unit_id))
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);

                    }                        
                }

                log_file_write("Location Changed for ".$employee->as_id, $ids);
                DB::commit();
                return redirect()->back()->with("success", "Outside Entry Successfull.");
                
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
