<?php

namespace App\Http\Controllers\Hr\Ess;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Leave;
use App\Models\Hr\Employee;
use Auth, DB, Validator, ACL,DateTime, DatePeriod, DateInterval, stdClass;

class LeaveApplicationController extends Controller
{
	public function showForm()
    {
        // ACL::check(["permissio" => "hr_ess_leave_application"]);
        #-----------------------------------------------------------#

		return view('hr/ess/leave_application');
	}

	public function saveData(Request $request)
    {
        // ACL::check(["permission" => "hr_ess_leave_application"]);
        #-----------------------------------------------------------#
		$validator= Validator::make($request->all(),[
    		'leave_type'              => 'required',
    		'leave_from'              => 'required|date',
    		'leave_to'                => 'max:50',
    		'leave_applied_date'      => 'required|date',
            'leave_supporting_file'   => 'mimes:docx,doc,pdf,jpg,png,jpeg|max:1024'
		]);
    	if ($validator->fails())
    	{
    		return back()
    			->withInput()
    			->withErrors($validator)
    			->with('error', 'Please fillup all required fields!');
    	}
    	else
    	{
            $check = new stdClass();
            $check->associate_id = Auth()->user()->associate_id;
            $check->leave_type = $request->leave_type;
            $check->leave_id = $request->hidden_id;
            $check->from_date = $request->leave_from;
            $check->to_date = $request->leave_to;
            $check->sel_days = (int)date_diff(date_create($request->leave_from),date_create($request->leave_to))->format("%a");

            $avail = $this->thisleaveLeangthCheck($check);

            if($avail->stat != 'false'){

                $leave_supporting_file = null;
                if($request->hasFile('leave_supporting_file')){
                    $file = $request->file('leave_supporting_file');
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $dir  = '/assets/files/leaves/';
                    $file->move( public_path($dir) , $filename );
                    $leave_supporting_file = $dir.$filename;
                }

            	// Format Date
            	$startDate = (!empty($request->leave_from)?date('Y-m-d', strtotime($request->leave_from)):null);
            	$endDate = (!empty($request->leave_to)?date('Y-m-d', strtotime($request->leave_to)):$startDate);

                //-----------Store Data---------------------
        		$store = new Leave;
                $store->leave_ass_id = Auth()->user()->associate_id;
        		$store->leave_type   = $request->leave_type;
        		$store->leave_from   = $startDate;
        		$store->leave_to     = $endDate;
        		$store->leave_applied_date = (!empty($request->leave_applied_date)?date('Y-m-d', strtotime($request->leave_applied_date)):null);
        		$store->leave_supporting_file         = $leave_supporting_file;
                $store->leave_updated_at         = date('Y-m-d H:i:s');
                $store->leave_updated_by         = "XTQGMOKVJI";
        		$store->leave_status         = 0;
        		if ($store->save())
        		{
                    $this->logFileWrite("Leave Application Entry Saved", $store->id );
        			return back()
        				->with('success', 'Save successful.');
        		}
    			else{
    				return back()
    				->withInput()
    				->with('error','Error!!! Please try again!');
    			}
            }else{
                return back()
                ->withInput()
                ->with('error', $avail->msg);
            }
		}
	}
    public function leaveHistory(Request $request){
        $history = DB::table('hr_leave')
            ->select(
                "*",
                DB::raw("
                    CASE
                        WHEN leave_status = '0' THEN 'Applied'
                        WHEN leave_status = '1' THEN 'Approved'
                        WHEN leave_status = '2' THEN 'Declined'
                    END AS leave_status
                ")
            )
            ->where("leave_ass_id", $request->associate_id)
            ->get();

        return response()->json($history);
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

   
    public function associatesLeave(Request $request)
    {
        $info = Employee::select(
                        'as_id',
                        'associate_id',
                        'as_unit_id',
                        'as_gender',
                        'as_name',
                        'as_oracle_code',
                        'as_doj',
                        'as_pic'
                    )
                    ->where('associate_id', $request->associate_id)
                    ->orWhere('as_oracle_code', $request->associate_id)
                    ->first();
        $table = get_att_table($info->as_unit_id).' AS a';

        $leaves = DB::table('hr_leave')
                ->select(
                    DB::raw("
                        YEAR(leave_from) AS year,
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual,
                        SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned,
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick,
                        SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity,
                        SUM(CASE WHEN leave_type = 'Special' THEN DATEDIFF(leave_to, leave_from)+1 END) AS special,
                        SUM(DATEDIFF(leave_to, leave_from)+1) AS total
                    ")
                )
                ->where('leave_status', '1')
                ->where(DB::raw("YEAR(leave_from)"),date('Y'))
                ->where("leave_ass_id", $request->associate_id)
                ->first();

        $earnleaves = DB::table("hr_leave") 
                    ->select(
                        DB::raw("
                            SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned
                        ")
                    )
                    ->where("leave_ass_id", $info->associate_id) 
                    ->where("leave_status", "1") 
                    ->first();

        $yearAtt = DB::table($table)
                    ->select(DB::raw('count(as_id) as att'))
                    ->where('a.as_id',$info->as_id)
                    ->groupBy(DB::raw('Year(in_time)'))
                    ->get();


        $earnedTotal = 0;
        if($yearAtt!= null){
            foreach ($yearAtt as $key => $Att) {
                $earnedTotal += intval($Att->att/18);    
            }
            
        }

        $remainTotal = $earnedTotal-$earnleaves->earned+$leaves->earned;
        $due = $remainTotal-$leaves->earned;

        $earnedLeaves[date('Y')]['remain'] = $due;
        $earnedLeaves[date('Y')]['enjoyed'] = $leaves->earned??0;
        $earnedLeaves[date('Y')]['earned'] = $remainTotal;

        return view('hr.timeattendance.associates_leave',compact('earnedLeaves','leaves','info'))->render();
    }

    public function leaveCheck(Request $request){

        $associate_id= $request->associate_id;
        $info = Employee::select(
                        'as_id',
                        'associate_id',
                        'as_unit_id',
                        'as_gender'
                    )
                    ->where('associate_id', $request->associate_id)
                    ->first();
        $table = get_att_table($info->as_unit_id).' AS a';
        $statement = [];
        $statement['stat'] = "false";
        // Earned Leave Restriction
        if($request->leave_type== "Earned"){
            $leaves = DB::table("hr_leave") 
                        ->select(
                            DB::raw("
                                SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned
                            ")
                        )
                        ->where("leave_ass_id", $info->associate_id) 
                        ->where("leave_status", "1") 
                        ->first();

            $yearAtt = DB::table($table)
                        ->select(DB::raw('count(as_id) as att'))
                        ->where('a.as_id',$info->as_id)
                        ->groupBy(DB::raw('Year(in_time)'))
                        ->first();
            //dd($yearAtt);
            $earnedTotal = 0;
            if($yearAtt!= null){
                foreach ($yearAtt as $key => $att) {
                    $earnedTotal += intval($att/18);    
                }         
            }
            if($leaves->earned < $earnedTotal){
                $statement['stat'] = "true";
                $statement['msg'] = $earnedTotal;
            }else{
                if($earnedTotal >0){
                    $statement['msg'] = 'This employee has already taken '.$earnedTotal.'day(s) of Earned('.$earnedTotal.') Leave';
                }else{
                    $statement['msg'] = 'This employee have 0 Earned Leave';
                }
            }
        }
        // Casual Leave Restriction
        if($request->leave_type== "Casual"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual
                    ")
                )
                ->where("leave_ass_id", $info->associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            if($leaves->casual < 10){
                $statement['stat'] = "true";
                $statement['msg'] = $leaves->casual;
            }else{
                $statement['msg'] = 'This employee has taken 10 day(s) of Casual(10) Leave';
            }
        }
        // Sick Leave Restriction
        if($request->leave_type== "Sick"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick
                    ")
                )
                ->where("leave_ass_id", $info->associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            if($leaves->sick < 14){
                $statement['stat'] = "true";
                $statement['msg'] = $leaves->sick;
            }else{
                $statement['msg'] = 'This employee has taken 14 day(s) of Sick(14) Leave';
            }
        }
        // Maternity Leave Restriction
        if($request->leave_type== "Maternity"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity
                    ")
                )
                ->where("leave_ass_id", $info->associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            //dd($leaves);
            if($leaves->maternity < 112 && $info->as_gender== "Female"){
                $statement['stat'] = "true";
                $statement['msg'] = $leaves->maternity;
            }else if($info->as_gender != "Female"){
                $statement['msg'] = 'Male Employees are not eligible for Maternity leave';   
            }else{
                $statement['msg'] = 'This employee has taken Maternity leave already!';
            }
        }
        if($request->leave_type== "Special"){
            $statement['stat'] = "true";
        }
        return $statement;
    }
    public function leaveLeangthCheck(Request $request){

        $associate_id= $request->associate_id;
        $info = Employee::select(
                        'as_id',
                        'associate_id',
                        'as_unit_id',
                        'as_gender'
                    )
                    ->where('associate_id', $request->associate_id)
                    ->first();
        $table = get_att_table($info->as_unit_id).' AS a';
        $statement = [];
        $statement['stat'] = "false";
        if(isset($request->usertype) && $request->usertype == 'ess'){
            $hello = 'You have';
        }else{
            $hello = 'This employee has';
        }
        if($request->leave_type== "Earned"){

            $yearAtt = DB::table($table)
                        ->select(DB::raw('count(as_id) as att'))
                        ->where('a.as_id',$info->as_id)
                        ->groupBy(DB::raw('Year(in_time)'))
                        ->first();
            //dd($yearAtt);
            $earnedTotal = 0;
            if($yearAtt!= null){
                foreach ($yearAtt as $key => $att) {
                    $earnedTotal += intval($att/18);    
                }
                
            }
            if($earnedTotal > $request->sel_days){
                $statement['stat'] = "true";
            }else{
                if($earnedTotal >0){
                    $statement['msg'] = $hello.' only '.$earnedTotal.' day(s) of Earned Leave';
                }else{
                    $statement['msg'] = $hello.' 0 Earned Leave';
                }
            }
        }
        // Casual Leave Restriction
        if($request->leave_type== "Casual"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual
                    ")
                )
                ->where("leave_ass_id", $info->associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            $casual = 10-$leaves->casual;
            if($request->sel_days < $casual){
                $statement['stat'] = "true";
            }else{
                $statement['msg'] = $hello.' '.$casual.' day(s) of Casual Leave';
            }
        }
        // Sick Leave Restriction
        if($request->leave_type== "Sick"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick
                    ")
                )
                ->where("leave_ass_id", $info->associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            $sick = 14-$leaves->sick;
            if($request->sel_days < $sick){
                $statement['stat'] = "true";
            }else{
                $statement['msg'] = $hello.' '.$sick.' day(s) of Sick(14) Leave';
            }
        }
        // Maternity Leave Restriction
        if($request->leave_type== "Maternity"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity
                    ")
                )
                ->where("leave_ass_id", $request->associate_id) 
                ->where("leave_status", 1) 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            $remain = 112-($leaves->maternity??0);
            //dd($request->sel_days);
            if($leaves == null || ($leaves != null && $request->sel_days< $remain) ) {
                $statement['stat'] = "true";
            }else if (($leaves != null && $request->sel_days > $remain)){
                $statement['msg'] = $hello.' only '.$remain.' day(s) remain';   
            }else{
                $statement['msg'] = $hello.' already taken Maternity Leave';   
            }
        }
        if($request->leave_type== "Special"){
            $statement['stat'] = "true";
        }

        if($statement['stat'] == 'true'){
            $from_date = new \DateTime($request->from_date);
            $to_date = new \DateTime($request->to_date);
            $to_date->modify("+1 day");
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($from_date, $interval, $to_date);
            //dd($period );
            $statement['msg'] = $hello.' already taken/applied for leave at <br>';
            foreach ($period as $dt) {
                $leave = Leave::whereDate('leave_from','<=', $dt->format("Y-m-d"))
                            ->whereDate('leave_to','>=', $dt->format("Y-m-d"))
                            ->where('leave_ass_id', $request->associate_id)
                            //->where("leave_status", "1")
                            ->when($request, function($query) use ($request) {
                                if(isset($request->leave_id)){
                                    $query->where('id', '!=', $request->leave_id);
                                }
                            })
                            ->first();
                if($leave){
                    if($leave->leave_status == 1){
                        $status = '<span style="color:#00b300;">Approved</span>';
                    }else if($leave->leave_status == 0){
                        $status = 'Applied';
                    }else{
                        $status = '';
                    }
                    $statement['stat'] = "false";
                    $statement['msg'] .= $dt->format("Y-m-d").' <span style="color:#000;">--- '.$leave->leave_type.'---</span> '.$status.'<br>';
                }
            }
        }
        return $statement;
    }

    public function thisleaveLeangthCheck($request){

        $associate_id = $request->associate_id;
        $info = Employee::select(
                        'as_id',
                        'associate_id',
                        'as_unit_id',
                        'as_gender'
                    )
                    ->where('associate_id', $request->associate_id)
                    ->first();

        $table = get_att_table($info->as_unit_id).' AS a';
        $statement = [];
        $statement['stat'] = "false";
        if(isset($request->usertype) && $request->usertype == 'ess'){
            $hello = 'You have';
        }else{
            $hello = 'This employee has';
        }
        if($request->leave_type== "Earned"){

            $yearAtt = DB::table($table)
                        ->select(DB::raw('count(as_id) as att'))
                        ->where('a.as_id',$info->as_id)
                        ->groupBy(DB::raw('Year(in_time)'))
                        ->first();
            //dd($yearAtt);
            $earnedTotal = 0;
            if($yearAtt!= null){
                foreach ($yearAtt as $key => $att) {
                    $earnedTotal += intval($att/18);    
                }
                
            }
            if($earnedTotal > $request->sel_days){
                $statement['stat'] = "true";
            }else{
                if($earnedTotal >0){
                    $statement['msg'] = $hello.' only '.$earnedTotal.' day(s) of Earned Leave';
                }else{
                    $statement['msg'] = $hello.' 0 Earned Leave';
                }
            }
        }
        // Casual Leave Restriction
        if($request->leave_type== "Casual"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual
                    ")
                )
                ->where("leave_ass_id", $info->associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            $casual = 10-$leaves->casual;
            if($request->sel_days < $casual){
                $statement['stat'] = "true";
            }else{
                $statement['msg'] = $hello.' '.$casual.' day(s) of Casual Leave';
            }
        }
        // Sick Leave Restriction
        if($request->leave_type== "Sick"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick
                    ")
                )
                ->where("leave_ass_id", $info->associate_id) 
                ->where("leave_status", "1") 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            $sick = 14-$leaves->sick;
            if($request->sel_days < $sick){
                $statement['stat'] = "true";
            }else{
                $statement['msg'] = $hello.' '.$sick.' day(s) of Sick(14) Leave';
            }
        }
        // Maternity Leave Restriction
        if($request->leave_type== "Maternity"){
            $leaves = DB::table("hr_leave") 
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity
                    ")
                )
                ->where("leave_ass_id", $request->associate_id) 
                ->where("leave_status", 1) 
                ->where(function ($q){
                    $q->where(DB::raw("YEAR(leave_from)"), '=', date("Y"));
                }) 
                ->first();
            $remain = 112-($leaves->maternity??0);
            //dd($request->sel_days);
            if($leaves == null || ($leaves != null && $request->sel_days< $remain) ) {
                $statement['stat'] = "true";
            }else if (($leaves != null && $request->sel_days > $remain)){
                $statement['msg'] = $hello.' only '.$remain.' day(s) remain';   
            }else{
                $statement['msg'] = $hello.' already taken Maternity Leave';   
            }
        }
        if($request->leave_type== "Special"){
            $statement['stat'] = "true";
        }

        if($statement['stat'] == 'true'){
            $from_date = new \DateTime($request->from_date);
            $to_date = new \DateTime($request->to_date);
            $to_date->modify("+1 day");
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($from_date, $interval, $to_date);
            //dd($period );
            $statement['msg'] = $hello.' already taken/applied for leave at <br>';
            foreach ($period as $dt) {
                $leave = Leave::whereDate('leave_from','<=', $dt->format("Y-m-d"))
                            ->whereDate('leave_to','>=', $dt->format("Y-m-d"))
                            ->where('leave_ass_id', $request->associate_id)
                            //->where("leave_status", "1")
                            ->when($request, function($query) use ($request) {
                                if(isset($request->leave_id)){
                                    $query->where('id', '!=', $request->leave_id);
                                }
                            })
                            ->first();
                if($leave){
                    if($leave->leave_status == 1){
                        $status = '<span style="color:#00b300;">Approved</span>';
                    }else if($leave->leave_status == 0){
                        $status = 'Applied';
                    }else{
                        $status = '';
                    }
                    $statement['stat'] = "false";
                    $statement['msg'] .= $dt->format("Y-m-d").' <span style="color:#000;">--- '.$leave->leave_type.'---</span> '.$status.'<br>';
                }
            }
        }
        return (object) $statement;
    }
    public function attendanceCheck(Request $request)
    {
        $associate_id= $request->associate_id;
        $info = Employee::select(
                        'as_id',
                        'associate_id',
                        'as_unit_id',
                        'as_gender'
                    )
                    ->where('associate_id', $request->associate_id)
                    ->first();
        $table = get_att_table($info->as_unit_id).' AS a';

        $from_date   = new \DateTime($request->from_date);
        $to_date     = new \DateTime($request->to_date);
        $to_date->modify("+1 day");
        $interval    = DateInterval::createFromDateString('1 day');
        $period      = new DatePeriod($from_date, $interval, $to_date);
        //dd($period );
        $statement = [];
        $statement['stat'] = true; 
        $statement['msg']  = 'This employee already has atteandance at ';
        foreach ($period as $dt) {
            $check = DB::table($table)
                     ->where('a.as_id',$info->as_id)
                     ->whereDate('in_time',$dt->format("Y-m-d"))
                     ->first();
            if($check){
                $statement['stat'] = false; 
                $statement['msg'] .= $dt->format("Y-m-d");
            }
        }

        return $statement;

    }

}
