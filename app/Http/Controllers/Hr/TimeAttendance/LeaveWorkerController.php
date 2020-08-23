<?php
namespace App\Http\Controllers\Hr\TimeAttendance;
use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Jobs\BuyerManualLeaveApproveProcess;
use App\Jobs\ProcessMonthlySalary;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Hr\Benefits;
use App\Models\Hr\Employee;
use App\Models\Hr\Leave;
use App\Models\Hr\LeaveApproval;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use Auth, Validator, ACL, DB,stdClass,DateTime, DatePeriod, DateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveWorkerController extends Controller
{
    # show form
    public function showForm()
    {
        //ACL::check(["permission" => "hr_time_worker_leave"]);
    	return view('hr/timeattendance/leave_worker');
    }
    # store data
    public function saveData(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'leave_ass_id'            => 'required',
    		'leave_type'              => 'required',
    		'leave_from'              => 'required|date',
    		'leave_to'                => 'date',
    		'leave_applied_date'      => 'required|date',
            'leave_supporting_file'   => 'mimes:docx,doc,pdf,jpg,png,jpeg|max:1024',
        ]);
    	if ($validator->fails())
    	{
    		return back()
    			->withInput()
    			->withErrors($validator)
    			->with('error', 'Please fill up all required fields!');
    	}
        $input = $request->all();
        // return $input;
        DB::beginTransaction();
        try {
            $check = new stdClass();
            $check->associate_id = $request->leave_ass_id;
            $check->leave_type = $request->leave_type;
            $check->from_date = $request->leave_from;
            $check->to_date = $request->leave_to;
            $check->sel_days = (int)date_diff(date_create($request->leave_from),date_create($request->leave_to))->format("%a");

            $avail = $this->leaveLeangthCheck($check);
            //dd($avail->stat);
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
                $store->leave_ass_id           = $request->leave_ass_id;
                $store->leave_type             = $request->leave_type;
                $store->leave_from             = $startDate;
                $store->leave_to               = $endDate;
                $store->leave_applied_date     = (!empty($request->leave_applied_date)?date('Y-m-d', strtotime($request->leave_applied_date)):null);
                $store->leave_supporting_file  = $leave_supporting_file;
                $store->leave_comment          = $request->leave_comment;
                $store->leave_updated_at       = date('Y-m-d H:i:s');
                $store->leave_updated_by       = Auth::user()->associate_id;
                $store->leave_status           = 1;
                if ($store->save())
                {
                    // check exists then absent data delete
                    $today = date('Y-m-d');
                    $day = date('d');
                    $month = date('m');
                    $year = date('Y');
                    $leaveMonth = date("m", strtotime($endDate));
                    $currentDate = Carbon::now();
                    $lastMonth = $currentDate->startOfMonth()->subMonth()->format('m');
                    if($endDate <= $today){
                        $checkAbsent = Absent::checkDateRangeEmployeeAbsent($startDate, $endDate, $request->leave_ass_id);
                        $absentCount = count($checkAbsent);
                        if($absentCount > 0){
                            foreach ($checkAbsent as $absent) {
                                $getAbsent = Absent::findOrFail($absent->id);
                                $getAbsent->delete();
                            }
                        }

                        $getEmployee = Employee::getEmployeeAssociateIdWise($request->leave_ass_id);
                        $tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
                        // attendance remove 
                        $attendance = DB::table($tableName)
                                    ->where('as_id', $getEmployee->as_id)
                                    ->whereDate('in_time','>=', $startDate)
                                    ->whereDate('in_time','<=', $endDate)
                                    ->delete();

                        $lockDate = Custom::getLockDate();
                        // check previous month leave
                        if($leaveMonth == $lastMonth){
                            if($day < $lockDate){
                                if($getEmployee != null){
                                    $yearLeave = Carbon::parse($request->leave_from)->format('Y');
                                    $monthLeave = Carbon::parse($request->leave_from)->format('m');
                                    $yearMonth = $yearLeave.'-'.$monthLeave; 
                                    if($monthLeave == date('m')){
                                        $totalDay = date('d');
                                    }else{
                                        $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                                    }
                                    $queue = (new ProcessUnitWiseSalary($tableName, $monthLeave, $yearLeave, $getEmployee->as_id, $totalDay))
                                            ->onQueue('salarygenerate')
                                            ->delay(Carbon::now()->addSeconds(2));
                                            dispatch($queue);
                                }
                                
                            }else{
                                if($absentCount > 0){
                                    $getBenefit = Benefits::getEmployeeAssIdwise($request->leave_ass_id);
                                    $perDayBasic = $getBenefit->ben_basic / 30;
                                    $getSalaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($request->leave_ass_id, $month, $year);
                                    
                                    if($getSalaryAdjust == null){
                                        $mId = SalaryAdjustMaster::insertEmployeeIdMonthYearWise($request->leave_ass_id, $month, $year); 
                                    }else{
                                        $mId = $getSalaryAdjust->id;
                                    }
                                    foreach ($checkAbsent as $absent) {
                                        $getData['master_id'] = $mId;
                                        $getData['date'] = $absent->date;
                                        $getData['amount'] = $perDayBasic;
                                        $getData['type'] = 1;
                                        $getData['status'] = 1;
                                        $getMasterDetails = SalaryAdjustDetails::getCheckEmployeeWiseMasterDetails($getData);
                                        if($getMasterDetails == null){
                                            SalaryAdjustDetails::insertMasterDetails($getData);
                                        }
                                        
                                    }
                                }
                            }
                            
                        }
                    }
                    $msg = 'Leave Entry Saved';
                    DB::commit();
                    $this->logFileWrite($msg, $store->id );
                    return back()->with('success', $msg);
                }else{
                    return back()->with('error', 'Please try again.');
                }
            }else{
                return back()->withInput()->with('error', $avail->msg);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return back()->with('error', $bug);
        }

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

    public function leaveLeangthCheck($request){

        $associate_id= $request->associate_id;
        $info = Employee::select(
                        'as_id',
                        'associate_id',
                        'as_unit_id',
                        'as_gender'
                    )
                    ->where('associate_id', $request->associate_id)
                    ->first();
        $table = $this->getTableName($info->as_unit_id);
        $statement = [];
        $statement['stat'] = "false";
        // Earned Leave Restriction
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
                    $statement['msg'] = 'This employee has only '.$earnedTotal.' day(s) of Earned Leave';
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
            $casual = 10-$leaves->casual;
            if($request->sel_days < $casual){
                $statement['stat'] = "true";
            }else{
                $statement['msg'] = 'This employee have '.$casual.' day(s) of Casual Leave';
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
                $statement['msg'] = 'This employee have '.$sick.' day(s) of Sick(14) Leave';
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
                $statement['msg'] = 'This employee has only '.$remain.' day(s) remain';   
            }else{
                $statement['msg'] = 'This employee has already taken Maternity Leave';   
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
            $statement['msg'] = 'Employee is already taken leave at <br>';
            foreach ($period as $dt) {
                $leave = Leave::whereDate('leave_from','<=', $dt->format("Y-m-d"))
                            ->whereDate('leave_to','>=', $dt->format("Y-m-d"))
                            ->where('leave_ass_id', $request->associate_id)
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


    //Maternity Leave Status Check Function for cron job

    public function LeaveStatusCheckAndUpdate(){
        #-- This job will run at the beggining of the day --#


        /*
            #----------- Leave starts today ---------------- #

            1. Get leave list from today
            2. Update leave status as running (leave_complete_status=1)
            3. If there is any Maternity Leave then update the basic information table as Maternity (as_status=6)

        */
        $leave_exists= Leave::where('leave_status', 1)
                            ->where('leave_complete_status', 0)
                            ->whereDate('leave_from', date('Y-m-d'))
                            ->get();
        if(!empty($leave_exists)){

            foreach($leave_exists AS $leave){
                DB::table('hr_leave')
                    ->where('id', $leave->id)
                    ->update([
                        'leave_complete_status' => 1
                        ]);

               // $this->logFileWrite('Leave Status Updated!', $leave->leave_ass_id);

                if($leave->leave_type == "Maternity"){
                    DB::table('hr_as_basic_info')
                        ->where('associate_id', $leave->leave_ass_id)
                        ->update([
                            'as_status' => 6
                            ]);
                   // $this->logFileWrite('Maternity Leave Status Updated!', $leave->leave_ass_id);
                }
            }
        }


        /*
            #----------- Leave Ends today ---------------- #
            1. Get leave list from today
            2. Update leave status as completed (leave_complete_status=2)
            3. If there is any Maternity Leave then update the basic information table as Active (as_status=1)

        */

        $leave_exists= Leave::where('leave_status', 1)
                            ->where('leave_complete_status', 1)
                            ->whereDate('leave_to', date('Y-m-d', strtotime("-1 days")))
                            ->get();

        if(!empty($leave_exists)){

            foreach($leave_exists AS $leave){
                DB::table('hr_leave')
                    ->where('id', $leave->id)
                    ->update([
                        'leave_complete_status' => 2
                        ]);

               // $this->logFileWrite('Leave Status Updated!', $leave->leave_ass_id);

                if($leave->leave_type == "Maternity"){
                    DB::table('hr_as_basic_info')
                        ->where('associate_id', $leave->leave_ass_id)
                        ->update([
                            'as_status' => 1
                            ]);
                   // $this->logFileWrite('Maternity Leave Status Updated!', $leave->leave_ass_id);
                }
            }
        }
    }
}
