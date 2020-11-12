<?php

namespace App\Http\Controllers\Hr\Notification;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Hr\Benefits;
use App\Models\Employee;
use App\Models\Hr\Leave;
use App\Models\Hr\LoanApplication;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use Carbon\Carbon;
use DB, DataTables;
use Illuminate\Http\Request;

class NotifLeaveController extends Controller
{
    public function LeaveList(){
    	return view('hr/notification/leave_app_list');
    }

    public function LeaveData(){ 

        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('hr_leave AS l')
            ->where('l.leave_status', '=', '0')
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                'l.id',
                'l.leave_ass_id',
                'b.as_name',
                'l.leave_type',
                DB::raw("CONCAT(l.leave_from, ' to ', l.leave_to) AS leave_duration"),
                'l.leave_supporting_file',
                'l.leave_comment'
            )
            ->leftJoin('hr_as_basic_info AS b', 'l.leave_ass_id', '=', 'b.associate_id')
            ->orderBy('l.id','desc')
            ->get();

        return DataTables::of($data) 
            ->addColumn('action', function ($data) {
                return "<div class=\"btn-group\">  
                    <a href=".url('hr/notification/leave/leave_approve/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                        <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                    </a>
                </div>";
            })  
            ->rawColumns(['serial_no','action'])
            ->toJson();
    }
    public function LeaveView($id){

        $leave= DB::table('hr_leave')
            ->where('hr_leave.id', '=', $id)
            ->first();

        if($leave == null){
            return view('hr/notification/leave_app_list')
                ->with('error', 'No record found!!');
        }
        else{
            return view('hr/notification/leave_approve', compact('leave'));
        }

    }
    public function LeaveStatus(Request $request)
    {
        try {
            if ($request->has('approve'))
            { 
                DB::table('hr_leave')->where('hr_leave.id', '=', $request->id)
                    ->update(['leave_status' => 1]);
                $getLeave = Leave::where('id', $request->id)->first();
                if($getLeave != null){
                  //check exists then absent data delete 
                  $today = date('Y-m-d');
                  $day = date('d');
                  $month = date('m');
                  $year = date('Y');
                  $leaveMonth = date("m", strtotime($getLeave->leave_to));
                  $currentDate = Carbon::now();
                  $lastMonth = $currentDate->startOfMonth()->subMonth()->format('m');
                  
                    $checkAbsent = Absent::checkDateRangeEmployeeAbsent($getLeave->leave_from, $getLeave->leave_to, $getLeave->leave_ass_id);
                    if(count($checkAbsent) > 0){
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
                                ->whereDate('in_time','>=', $getLeave->leave_from)
                                ->whereDate('in_time','<=', $getLeave->leave_to)
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
                                $yearLeave = Carbon::parse($getLeave->leave_from)->format('Y');
                                $monthLeave = Carbon::parse($getLeave->leave_from)->format('m');
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
                            if(count($checkAbsent) > 0){
                                $getBenefit = Benefits::getEmployeeAssIdwise($getLeave->leave_ass_id);
                                $perDayBasic = $getBenefit->ben_basic / 30;
                                $getSalaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($getLeave->leave_ass_id, $month, $year);
                                
                                if($getSalaryAdjust == null){
                                    $mId = SalaryAdjustMaster::insertEmployeeIdMonthYearWise($getLeave->leave_ass_id, $month, $year); 
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
                $this->logFileWrite("Leave Status Updated", $request->id );
                return redirect()->intended('hr/notification/leave/leave_app_list')
                        ->with('success','Leave Approved Successfully');
            }
            else
            {
                DB::table('hr_leave')->where('hr_leave.id', '=', $request->id)
                    ->update(['leave_status' => 2]);
                    $this->logFileWrite("Leave Status Updated", $request->id );

                return redirect()->intended('hr/notification/leave/leave_app_list')
                        ->with('success','Leave Rejected Successfully');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    } 

}

