<?php
namespace App\Http\Controllers\Hr\TimeAttendance;
use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Jobs\BuyerManualLeaveApproveProcess;
use App\Jobs\ProcessMonthlySalary;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Hr\Benefits;
use App\Models\Employee;
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

            $avail = emp_remain_leave_check($check);
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
                    
                    $checkAbsent = Absent::checkDateRangeEmployeeAbsent($startDate, $endDate, $request->leave_ass_id);
                    $absentCount = count($checkAbsent);
                    if($absentCount > 0){
                        foreach ($checkAbsent as $absent) {
                            $getAbsent = Absent::findOrFail($absent->id);
                            $getAbsent->delete();
                        }
                    }


                    $getEmployee = Employee::getEmployeeAssociateIdWise($request->leave_ass_id);
                    $tableName = get_att_table($getEmployee->as_unit_id);
                    // attendance remove 
                    $attendance = DB::table($tableName)
                                ->where('as_id', $getEmployee->as_id)
                                ->where('in_date','>=', $startDate)
                                ->where('in_date','<=', $endDate)
                                ->delete();

                    // check previous month leave
                    if($leaveMonth == $lastMonth){
                        // check activity lock/unlock
                        $yearMonth = date('Y-m', strtotime('-1 month'));
                        $lock['month'] = date('m', strtotime($yearMonth));
                        $lock['year'] = date('Y', strtotime($yearMonth));
                        $lock['unit_id'] = $getEmployee->as_unit_id;
                        $lockActivity = monthly_activity_close($lock);
                        if($lockActivity == 0){
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
                                $perDayBasic = ceil($getBenefit->ben_basic / 30);
                                $getSalaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($request->leave_ass_id, $month, $year);
                                
                                if($getSalaryAdjust == null){
                                    $mId = SalaryAdjustMaster::insertEmployeeIdMonthYearWise($request->leave_ass_id, $month, $year); 
                                    // leave update
                                    $leave = Leave::findOrFail($store->id);
                                    $leave->update(['leave_comment' => 'Adjustment for '.date('F, Y', strtotime($today))]);
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
                        
                    }else if($leaveMonth == date('m')){
                        $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $getEmployee->as_id, date('d')))
                                ->onQueue('salarygenerate')
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);

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

    # store data
    public function leaveApprove($id, $type)
    {
        
        return $id;
        return $input;
        DB::beginTransaction();
        try {
            $check = new stdClass();
            $check->associate_id = $request->leave_ass_id;
            $check->leave_type = $request->leave_type;
            $check->from_date = $request->leave_from;
            $check->to_date = $request->leave_to;
            $check->sel_days = (int)date_diff(date_create($request->leave_from),date_create($request->leave_to))->format("%a");

            $avail = emp_remain_leave_check($check);
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
                    
                    $checkAbsent = Absent::checkDateRangeEmployeeAbsent($startDate, $endDate, $request->leave_ass_id);
                    $absentCount = count($checkAbsent);
                    if($absentCount > 0){
                        foreach ($checkAbsent as $absent) {
                            $getAbsent = Absent::findOrFail($absent->id);
                            $getAbsent->delete();
                        }
                    }


                    $getEmployee = Employee::getEmployeeAssociateIdWise($request->leave_ass_id);
                    $tableName = get_att_table($getEmployee->as_unit_id);
                    // attendance remove 
                    $attendance = DB::table($tableName)
                                ->where('as_id', $getEmployee->as_id)
                                ->where('in_date','>=', $startDate)
                                ->where('in_date','<=', $endDate)
                                ->delete();

                    // check previous month leave
                    if($leaveMonth == $lastMonth){
                        // check activity lock/unlock
                        $yearMonth = date('Y-m', strtotime('-1 month'));
                        $lock['month'] = date('m', strtotime($yearMonth));
                        $lock['year'] = date('Y', strtotime($yearMonth));
                        $lock['unit_id'] = $getEmployee->as_unit_id;
                        $lockActivity = monthly_activity_close($lock);
                        if($lockActivity == 0){
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
                                $perDayBasic = ceil($getBenefit->ben_basic / 30);
                                $getSalaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($request->leave_ass_id, $month, $year);
                                
                                if($getSalaryAdjust == null){
                                    $mId = SalaryAdjustMaster::insertEmployeeIdMonthYearWise($request->leave_ass_id, $month, $year); 
                                    // leave update
                                    $leave = Leave::findOrFail($store->id);
                                    $leave->update(['leave_comment' => 'Adjustment for '.date('F, Y', strtotime($today))]);
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
                        
                    }else if($leaveMonth == date('m')){
                        $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $getEmployee->as_id, date('d')))
                                ->onQueue('salarygenerate')
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);

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


    public function LeaveStatusCheckAndUpdate(){
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
                }
            }
        }



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
                }
            }
        }
    }
}
