<?php

namespace App\Http\Controllers\Hr\Ess;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Leave;
use App\Models\Employee;
use Auth, DB, Validator, ACL,DateTime, DatePeriod, DateInterval, stdClass;

class LeaveApplicationController extends Controller
{
	public function showForm()
    {

		return view('hr/ess/leave_application');
	}

	public function saveData(Request $request)
    {
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

            $avail = emp_remain_leave_check($request);

            if($avail['stat'] != 'false'){

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
                ->with('error', $avail['msg']);
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

        $leaves = DB::table('hr_leave')
                ->select(
                    DB::raw("
                        YEAR(leave_from) AS year,
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual,
                        SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned,
                        SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick,
                        SUM(CASE WHEN leave_type = 'Special' THEN DATEDIFF(leave_to, leave_from)+1 END) AS special,
                        SUM(DATEDIFF(leave_to, leave_from)+1) AS total
                    ")
                )
                ->where('leave_status', '1')
                ->where(DB::raw("YEAR(leave_from)"), date('Y'))
                ->where("leave_ass_id", $request->associate_id)
                ->first();

        $earned = DB::table('hr_earned_leave')
                    ->where('associate_id', $request->associate_id)
                    ->get()->keyBy('leave_year');
        $remain = 0;
        foreach ($earned as $key => $lv) {
            $remain += ($lv->earned - $lv->enjoyed);
        }
        $earnedLeaves[date('Y')]['remain'] = $remain+$leaves->earned;
        $earnedLeaves[date('Y')]['enjoyed'] = $leaves->earned;
        $earnedLeaves[date('Y')]['earned'] = $remain;

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

        $statement = [];
        $statement['stat'] = "false";
        // Earned Leave Restriction
        if($request->leave_type== "Earned"){

            /*$earned = DB::table('hr_earned_leave')
                        ->select(DB::raw('sum(earned - enjoyed) as l'))
                        ->where('associate_id', $associate_id)
                        ->groupBy('associate_id')->first()->l??0 ;

            $avail = (int) ($earned/2);
            if($avail >= 1){
                $statement['stat'] = "true";
            }else{
                $statement['stat'] = "false";
                $statement['msg'] = 'This employee has  '.$earned.' day(s) of Earned Leave and can take only '.$avail. ' day(s)' ;
            }*/
            $statement['stat'] = "true";
        }
        // Casual Leave Restriction
        if($request->leave_type== "Casual"){
            $leaves = DB::table("hr_leave")
                ->select(
                    DB::raw("
                        SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual
                    ")
                )
                ->where("leave_ass_id", $request->associate_id)
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
                ->where("leave_ass_id", $request->associate_id)
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

        if($request->leave_type== "Special"){
            $statement['stat'] = "true";
        }
        return $statement;
    }

    public function leaveLeangthCheck(Request $request){

        return emp_remain_leave_check($request);
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

        $statement = [];
        $statement['stat'] = true;
        $statement['msg']  = 'This employee already has atteandance at ';
        foreach ($period as $dt) {
            $check = DB::table($table)
                     ->where('a.as_id',$info->as_id)
                     ->where('in_date',$dt->format("Y-m-d"))
                     ->first();
            if($check){
                $statement['stat'] = false;
                $statement['msg'] .= $dt->format("Y-m-d");
            }
        }

        return $statement;

    }

}
