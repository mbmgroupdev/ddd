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
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\YearlyHolyDay;
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
    public function showForm()
    {

        $locationList= Location::pluck('hr_location_name', 'hr_location_name');
        $locationList['default'] = 'Default Location';
        $locationList['Outside']= "Outside";
        $locationList['WFHOME']= "Work from Home";

        return view('hr/operation/location_change_entry', compact( 'locationList'));
    }

    private function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];

        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
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

                

                $employee = Employee::where('associate_id',$request->employee_id)->first();
                $table = get_att_table($employee->as_unit_id);
                
                $start_date = $request->from_date;
                if($employee->as_doj > $request->from_date){
                    $start_date = $employee->as_doj;
                }
                $end_date = $request->to_date;

                if($request->requested_location != 'default'){
                    $out= new Outsides();
                    $out->as_id = $request->employee_id;
                    $out->start_date = $start_date;
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

                }



                $totalDays  = (date('d', strtotime($end_date))-date('d', strtotime($start_date)));

                if($employee->shift_roaster_status == 1){
                    // check holiday roaster employee
                    $getHoliday = HolidayRoaster::where('as_id', $employee->associate_id)
                    ->where('date','>=', $start_date)
                    ->where('date','<=', $end_date)
                    ->where('remarks', 'Holiday')
                    ->pluck('date','date')->toArray();
                }else{
                    // check holiday roaster employee
                    $RosterHolidayCount = HolidayRoaster::where('as_id', $employee->associate_id)
                    ->where('date','>=', $start_date)
                    ->where('date','<=', $end_date)
                    ->where('remarks', 'Holiday')
                    ->pluck('date','date')->toArray();
                    // check General roaster employee
                    $RosterGeneralCount = HolidayRoaster::where('as_id', $employee->associate_id)
                    ->where('date','>=', $start_date)
                    ->where('date','<=', $end_date)
                    ->where('remarks', 'General')
                    ->pluck('date','date')->toArray();
                     // check holiday shift employee
                    
                    
                    $shiftHolidayCount = YearlyHolyDay::
                        where('hr_yhp_unit', $employee->as_unit_id)
                        ->where('hr_yhp_dates_of_holidays','>=', $start_date)
                        ->where('hr_yhp_dates_of_holidays','<=', $end_date)
                        ->where('hr_yhp_open_status', 0)
                        ->pluck('hr_yhp_dates_of_holidays','hr_yhp_dates_of_holidays')->toArray();
                    
                    
                    if(count($RosterHolidayCount) > 0 || count($RosterGeneralCount) > 0){
                        $all = array_merge($RosterHolidayCount,$shiftHolidayCount);

                        $getHoliday = array_diff($all, $RosterGeneralCount);
                    }else{
                        $getHoliday = $shiftHolidayCount;
                    }
                }





                $attendance = DB::table($table)->where('in_date', '>=', $start_date)
                                ->where('in_date','<=', $end_date)
                                ->where('as_id', $employee->as_id)
                                ->pluck('in_date','in_date')->toArray();

                $leave = DB::table('hr_leave')
                          ->where('leave_ass_id', $employee->associate_id)
                          ->where('leave_status',1)
                          ->whereYear('leave_from',date('Y', strtotime($start_date)))
                          ->get();
                $leave_date = [];
                foreach ($leave as $key => $l) {
                    $l_date =  $this->generateDateRange(Carbon::parse($l->leave_from),Carbon::parse($l->leave_to));
                    $leave_date = array_merge($leave_date, $l_date);
                }



               



                for($j=0; $j<=$totalDays; $j++) {

                    $date = date('Y-m-d', strtotime("+".$j." day", strtotime($start_date)));

                    if(!in_array($date, $getHoliday) && !in_array($date, $attendance) && !in_array($date, $leave_date)){
                        $outtime = date('H:i:s',strtotime($employee->shift['hr_shift_end_time'])+($employee->shift['hr_shift_break_time']*60));

                        $attData = array(
                             'as_id' => $employee->as_id,
                             'in_date' => $date,
                             'hr_shift_code' => $employee->shift['hr_shift_code'],
                             'ot_hour' => 0,
                             'late_status' => 0,
                             'remarks'=>'BM',
                             'updated_by' => auth()->user()->associate_id,
                             'updated_at' => NOW()
                        );

                        if($request->type == 1 || $request->type == 2){
                             $attData['in_time'] = $date.' '.$employee->shift['hr_shift_start_time'];
                        }
                        if($request->type == 1 || $request->type == 3){
                             $attData['out_time'] = $date.' '.$outtime;
                        }

                        

                        $lastPunchId = DB::table($table)
                                         ->insertGetId($attData);

                        if($request->type == 1){
                            $queue = (new ProcessAttendanceInOutTime($table, $lastPunchId, $employee->as_unit_id))
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);
                        }else if($request->type == 2){
                            $queue = (new ProcessAttendanceIntime($table, $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);
                        }else if($request->type == 3){
                            $queue = (new ProcessAttendanceOuttime($table, $lastPunchId, $employee->as_unit_id))
                                    ->delay(Carbon::now()->addSeconds(2));
                                    dispatch($queue);

                        }
                    }

                }

                if($request->requested_location != 'default'){
                    log_file_write("Location Changed for ".$employee->as_id, $ids);

                }else{
                    log_file_write("Default punch for ".$employee->as_id. 'from '.$start_date.' to'.' $end_date', null);
                }
                // regenerate salary sheet
                
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
