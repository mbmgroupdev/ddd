<?php
namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMonthlySalary;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Hr\Benefits;
use App\Models\Employee;
use App\Models\Hr\Leave;
use App\Models\Hr\LeaveApproval;
use App\Models\Hr\SalaryAdjustDetails;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use DB,DataTables, ACL, Validator, Auth, stdClass,DateTime, DatePeriod, DateInterval;
use Illuminate\Http\Request;

class AllLeavesController extends Controller
{
   public function allLeaves()
   {
        $unit=Unit::pluck('hr_unit_name');
        #--------------------------------------------------------#
   	    return view('hr/timeattendance/all_leaves', compact('unit'));
   }
   public function allLeavesData()
   {
      #-----------------------------------------------------------#
        $data = DB::table('hr_leave AS l')
            ->select([
               'l.id',
               'l.leave_ass_id',
               'b.as_name',
               'l.leave_type',
               'l.leave_status',
               'l.leave_from',
               'l.leave_to',
               'l.created_at',
               'u.hr_unit_name',
               DB::raw("(DATEDIFF(leave_to, leave_from)+1 ) AS days")
            ])
            ->leftJoin('hr_as_basic_info AS b', 'l.leave_ass_id', '=', 'b.associate_id')
            ->leftJoin('hr_unit AS u', 'b.as_unit_id', '=', 'u.hr_unit_id' )
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->orderBy('l.id','desc')
            ->get();

        $perm = false;
        if(auth()->user()->canAny(['Manage Leave','Leave Approve']) || auth()->user()->hasRole('Super Admin')){
            $perm = true;
        }

        return DataTables::of($data)
            ->addColumn('leave_duration', function ($data) {
             $start= (!empty($data->leave_from)?(date("d-M-Y", strtotime($data->leave_from))):null);
             $end= (!empty($data->leave_to)?(date("d-M-Y", strtotime($data->leave_to))):null);
             if($start==$end){
                $leave_duration=$start;
             }else{
                $leave_duration= $start. " to " . $end;
             }
             return $leave_duration;
            })
            ->addColumn('leave_status', function ($data) {
               if ($data->leave_status == 1)
                  return  "<span class='label label-success label-xs'> Approved
                    </span>";
               else if ($data->leave_status == 2)
                  return  "<span  class='label label-danger label-xs'> Declined
                    </span>";
               else
                  return  "<span class='label label-primary label-xs' style='width: 70px;'>Applied
                    </span>";
            })
            ->addColumn('action', function ($data) use ($perm) {
                  if($perm){
                    if(date('Y-m-d',strtotime($data->created_at)) == date('Y-m-d')){
                      return "<a href=".url('hr/timeattendance/leave_delete/'.$data->id)." class=\"btn btn-xs btn-danger btn-round\" onclick=\"return confirm('Are you sure you want to delete this item?');\" data-toggle=\"tooltip\" title=\"Delete\">

                              <i class=\"ace-icon fa fa-trash bigger-120\"></i> 

                          </a>";
                    }
                    return '';
                    return "<div class=\"btn-group\">

                        <a href=\"#\" class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">

                            <i class=\"ace-icon fa fa-eye bigger-120\"></i>

                        </a>

                    </div>";
                  }else{
                    return "<div class=\"btn-group\">

                        <a href=".url('hr/timeattendance/leave_approve/'.$data->id)." class=\"btn btn-xs btn-success btn-round\" data-toggle=\"tooltip\" title=\"View\">

                            <i class=\"ace-icon fa fa-eye bigger-120\"></i>

                        </a>
                        <a href=".url('hr/timeattendance/leave_edit/'.$data->id)." class=\"btn btn-xs btn-info btn-round \" data-toggle=\"tooltip\" title=\"Edit\">

                            <i class=\"ace-icon fa fa-edit bigger-110\"></i>

                        </a>
                        <a href=".url('hr/timeattendance/leave_delete/'.$data->id)." class=\"btn btn-xs btn-danger btn-round\" onclick=\"return confirm('Are you sure you want to delete this item?');\" data-toggle=\"tooltip\" title=\"Delete\">

                            <i class=\"ace-icon fa fa-trash bigger-120\"></i>

                        </a>

                    </div>";
                  }
            })
            ->rawColumns(['serial_no','leave_status','action'])
            ->make(true);
   }

// dd(count($previous_leaves));

   public function leaveView($id)
   {
      $previous_leaves= DB::table('hr_leave AS l')->where('l.leave_ass_id', '=',"XTQGMOKVJI")->get();
        $leave= DB::table('hr_leave AS l')
            ->where('l.id', '=', $id)
            ->first();
        if($leave == null){
            return view('hr/timeattendance/all_leave')
                ->with('error', 'No record found!!');
        }
        else{
            return view('hr/timeattendance/leave_approve', compact('leave', 'previous_leaves'));
        }
    }

    ##Edit..
    public function editLeave($id){
      $leave_data = DB::table('hr_leave as b')
                                ->select([
                                      'b.*',
                                      'c.as_name'
                                  ])
                                ->leftJoin('hr_as_basic_info as c', 'c.associate_id', 'b.leave_ass_id')
                                ->where('b.id', $id)
                                ->first();

      return view('hr.timeattendance.leave_worker_edit', compact('leave_data'));
    }

    ##Update..
    public function updateLeave(Request $request){
      $validator = Validator::make($request->all(), [
        'leave_ass_id'            => 'required',
        'leave_type'              => 'required',
        'leave_from'              => 'required|date',
        'leave_applied_date'      => 'required|date',
        'leave_supporting_file'   => 'mimes:docx,doc,pdf,jpg,png,jpeg|max:1024',
        'leave_comment'           => 'max:128'
        ]);
      if ($validator->fails())
      {
        return back()
          ->withInput()
          ->withErrors($validator)
          ->with('error', 'Please fill up all required fields!');
      }
        DB::beginTransaction();
        try {
            $check = new stdClass();
            $check->associate_id = $request->leave_ass_id;
            $check->leave_type = $request->leave_type;
            $check->leave_id = $request->hidden_id;
            $check->from_date = $request->leave_from;
            $check->to_date = $request->leave_to;
            $check->sel_days = (int)date_diff(date_create($request->leave_from),date_create($request->leave_to))->format("%a");

            $avail = $this->leaveLeangthCheck($check);
            //dd($avail->stat);
            if($avail->stat != 'false'){

              $id = (int)$request->hidden_id;
              // dd($id);

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

              // dd($startDate, $endDate);
              //-----------Update Data---------------------
              $update = Leave::where('id',$id)->update([
                'leave_ass_id'           => $request->leave_ass_id,
                'leave_type'             => $request->leave_type,
                'leave_from'             => $startDate,
                'leave_to'               => $endDate,
                'leave_applied_date'     => (!empty($request->leave_applied_date)?date('Y-m-d', strtotime($request->leave_applied_date)):null),
                'leave_supporting_file'  => $leave_supporting_file,
                'leave_comment'          => $request->leave_comment,
                'leave_updated_at'       => date('Y-m-d H:i:s'),
                'leave_updated_by'       => Auth::user()->associate_id
              ]);

              if ($update)
              {
                  // check exists then absent data delete
                  $today = date('Y-m-d');
                  $day = date('d');
                  $month = date('m');
                  $year = date('Y');
                  $leaveMonth = date("m", strtotime($endDate));
                  $currentDate = Carbon::now();
                  $lastMonth = $currentDate->startOfMonth()->subMonth()->format('m');
                  if($endDate < $today){
                      $checkAbsent = Absent::checkDateRangeEmployeeAbsent($startDate, $endDate, $request->leave_ass_id);
                      if(count($checkAbsent) > 0){
                          foreach ($checkAbsent as $absent) {
                              $getAbsent = Absent::findOrFail($absent->id);
                              $getAbsent->delete();
                          }
                      }

                      // check previous month leave
                      if($leaveMonth == $lastMonth){
                          $getEmployee = Employee::getEmployeeAssociateIdWise($request->leave_ass_id);
                          // check activity lock/unlock
                          $yearMonth = date('Y-m', strtotime('-1 month'));
                          $lock['month'] = date('m', strtotime($yearMonth));
                          $lock['year'] = date('Y', strtotime($yearMonth));
                          $lock['unit_id'] = $getEmployee->as_unit_id;
                          $lockActivity = monthly_activity_close($lock);
                          if($lockActivity == 0){
                              if($getEmployee != null){
                                  $tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
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
                              if(count($checkAbsent) > 0){
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


                  DB::commit();
                  $this->logFileWrite("Leave Updated", $id);
                  return redirect('hr/timeattendance/leave_edit/'.$id)
                      ->withInput()
                      ->with('success', 'Update Successful.');
              }
              else
              {
                  return back()
                      ->withInput()->with('error', 'Please try again.');
              }
            }else{
                return back()
                ->withInput()
                ->with('error', $avail->msg);
            }
            
          }catch (\Exception $e) {
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
            $statement['msg'] = 'This employee has already taken/applied for leave at <br>';
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

    ##Delete..
    public function deleteLeave($id){
      DB::table('hr_leave')->where('id', $id)->delete();
      return back()->with('success', 'Leave Deleted');
    }

    public function leaveStatus(Request $request)
    {
        if ($request->has('approve'))
        {
            DB::table('hr_leave AS l')->where('l.id', '=', $request->id)
              ->update([
                'leave_comment' => $request->leave_comment,
                'leave_updated_at' => date('Y-m-d H:i:s'),
                'leave_updated_by' => auth()->user()->associate_id,
                'leave_status' => 1
             ]);
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
              if($getLeave->leave_to < $today){
                $checkAbsent = Absent::checkDateRangeEmployeeAbsent($getLeave->leave_from, $getLeave->leave_to, $getLeave->leave_ass_id);
                if(count($checkAbsent) > 0){
                  foreach ($checkAbsent as $absent) {
                    $getAbsent = Absent::findOrFail($absent->id);
                    $getAbsent->delete();
                  }
                }
                
                // check previous month leave
                if($leaveMonth == $lastMonth){
                  $getEmployee = Employee::getEmployeeAssociateIdWise($getLeave->leave_ass_id);
                    // check activity lock/unlock
                    $yearMonth = date('Y-m', strtotime('-1 month'));
                    $lock['month'] = date('m', strtotime($yearMonth));
                    $lock['year'] = date('Y', strtotime($yearMonth));
                    $lock['unit_id'] = $getEmployee->as_unit_id;
                    $lockActivity = monthly_activity_close($lock);
                    if($lockActivity == 0){
                      if($getEmployee != null){
                          $tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
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
            }
            $this->logFileWrite("Leave Status Updated", $request->id);
            return redirect()->intended('hr/timeattendance/all_leaves')
                    ->with('success','Leave Approved Successfully');
        }
        else
        {
            DB::table('hr_leave AS l')->where('l.id', '=', $request->id)
                ->update([
                  'leave_comment' => $request->leave_comment,
                  'leave_updated_at' => date('Y-m-d H:i:s'),
                  'leave_updated_by' => "XTQGMOKVJI",
                  'leave_status' => 2
               ]);
            $this->logFileWrite("Leave Status Updated", $request->id);
            return redirect()->intended('hr/timeattendance/all_leaves')
                    ->with('success','Leave Rejected Successfully');
        }
    }
}
