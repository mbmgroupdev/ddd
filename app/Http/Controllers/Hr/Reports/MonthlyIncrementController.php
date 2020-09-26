<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Increment;
use App\Models\Hr\Leave;
use App\Models\Hr\Unit;
use App\Models\Hr\Disciplinary;
use Carbon\Carbon;
use DB, DateTime, PDF;
use Illuminate\Http\Request;
class MonthlyIncrementController extends Controller
{
    public function increment(Request $request)
    {
		$info ='';
		$departments ='';
    	$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
    	$deptList= Department::pluck('hr_department_name', 'hr_department_id');
        $effmonth = date("m", strtotime($request->month));
        $type_of_list = '';
    	if(!empty($request->unit_id)){
            $previous_12 = Carbon::createFromFormat('Y-m-d',$request->month)
                            ->modify('-12 months')
                            ->startOfMonth()
                            ->format('Y-m-d');
            $previous_1 = Carbon::createFromFormat('Y-m-d',$request->month)
                            ->modify('-1 months')
                            ->endOfMonth()
                            ->format('Y-m-d');
    		if(!empty($request->department_id)){
    			$info= DB::table('hr_increment AS inc')
                            ->where('inc.status', 0)
                            ->where('increment_type', "2")
                            ->whereMonth('effective_date', $effmonth)
                            ->leftJoin('hr_as_basic_info AS b', 'inc.associate_id', 'b.associate_id')
    						->where('b.as_unit_id', $request->unit_id)
    						->where('b.as_department_id', $request->department_id)
                            ->whereIn('b.as_status', [1,6])
    						->select([
    							'b.as_id',
    							'b.associate_id',
    							'b.as_name',
    							'b.as_doj',
    							'b.as_department_id',
                                'b.as_status',
    							'dep.hr_department_name',
    							'd.hr_designation_name',
    							's.hr_section_name',
    							'l.hr_line_name',
                                'inc.current_salary',
                                'inc.increment_amount',
                                'inc.amount_type',
                                'inc.effective_date'

    						])
    						->leftJoin('hr_department AS dep', 'dep.hr_department_id', 'b.as_department_id')
    						->leftJoin('hr_designation AS d', 'b.as_designation_id', 'd.hr_designation_id')
    						->leftJoin('hr_section AS s', 'b.as_section_id', 'hr_section_id')
    						->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                            ->groupBy('b.associate_id')
    						->get();
    		}
    		else{
                //for incremented -- fetch data
                if($request->increment_report_type == 'incremented'){
                $type_of_list = "Increment List Month of";
    			$info= DB::table('hr_increment AS inc')
                            ->where('inc.status', 0)
                            ->where('inc.increment_type', "2")
                            ->whereMonth('inc.effective_date', $effmonth)
                            ->leftJoin('hr_as_basic_info AS b', 'inc.associate_id', 'b.associate_id')
    						// ->where('as_unit_id', $request->unit_id)
                            ->where(['b.as_unit_id'=> $request->unit_id, 'b.as_emp_type_id'=> $request->associate_type])
                            ->whereIn('b.as_status', [1,6])
    						->select([
    							'b.as_id',
    							'b.associate_id',
    							'b.as_name',
    							'b.as_doj',
    							'b.as_department_id',
                                'b.as_status',
    							'dep.hr_department_name',
    							'd.hr_designation_name',
    							's.hr_section_name',
    							'l.hr_line_name',
                                'inc.current_salary',
                                'inc.increment_amount',
                                'inc.amount_type',
                                'inc.effective_date',
                                'inc.status'

    						])
    						->leftJoin('hr_department AS dep', 'dep.hr_department_id', 'b.as_department_id')
    						->leftJoin('hr_designation AS d', 'b.as_designation_id', 'd.hr_designation_id')
    						->leftJoin('hr_section AS s', 'b.as_section_id', 'hr_section_id')
    						->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                            ->groupBy('b.associate_id')
    						->get();
                    // dd($info);
                }

                //for increment eligibility--fetch data
                if($request->increment_report_type == 'eligiblility'){
                    $type_of_list = "Increment Eligiblility List";
                    if($request->associate_type == 1 || $request->associate_type == 2){
                        $year = date("Y", strtotime($request->month));
                        $month = date("m", strtotime($request->month));
                        // dd($month, $year);
                        $info= DB::table('hr_as_basic_info AS b')
                                    ->where(['b.as_unit_id'=> $request->unit_id, 'b.as_emp_type_id'=> $request->associate_type])
                                    ->whereIn('b.as_status', [1,6])
                                    ->leftJoin('hr_increment AS inc', 'inc.associate_id', 'b.associate_id' )
                                    ->select([
                                        'b.as_id',
                                        'b.associate_id',
                                        'b.as_name',
                                        'b.as_doj',
                                        'b.as_department_id',
                                        'b.as_status',
                                        'dep.hr_department_name',
                                        'd.hr_designation_name',
                                        's.hr_section_name',
                                        'l.hr_line_name',
                                        'inc.current_salary',
                                        'inc.increment_amount',
                                        'inc.amount_type',
                                        'inc.effective_date',
                                        'inc.status as increment_status',
                                        'inc.increment_type',
                                        'inc.status'

                                    ])
                                    ->leftJoin('hr_department AS dep', 'dep.hr_department_id', 'b.as_department_id')
                                    ->leftJoin('hr_designation AS d', 'b.as_designation_id', 'd.hr_designation_id')
                                    ->leftJoin('hr_section AS s', 'b.as_section_id', 'hr_section_id')
                                    ->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                                    ->whereMonth('b.as_doj','=', $month)
                                    ->whereYear('b.as_doj','<', $year)
                                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                                    ->groupBy('b.associate_id')
                                    ->get();
                        // dd($info);
                    }

                    if($request->associate_type == 3){
                        $till_date = date('Y-m-d',strtotime('-365 day',  strtotime($request->month)));
                         $info= DB::table('hr_as_basic_info AS b')
                                ->where(['b.as_unit_id'=> $request->unit_id, 'b.as_emp_type_id'=> $request->associate_type])
                                ->whereIn('b.as_status', [1,6])
                                ->leftJoin('hr_increment AS inc', 'inc.associate_id', 'b.associate_id' )
                                ->select([
                                    'b.as_id',
                                    'b.associate_id',
                                    'b.as_name',
                                    'b.as_doj',
                                    'b.as_department_id',
                                    'b.as_status',
                                    'dep.hr_department_name',
                                    'd.hr_designation_name',
                                    's.hr_section_name',
                                    'l.hr_line_name',
                                    'inc.current_salary',
                                    'inc.increment_amount',
                                    'inc.amount_type',
                                    'inc.effective_date',
                                    'inc.status as increment_status',
                                    'inc.increment_type',
                                    'inc.status'

                                ])
                                ->leftJoin('hr_department AS dep', 'dep.hr_department_id', 'b.as_department_id')
                                ->leftJoin('hr_designation AS d', 'b.as_designation_id', 'd.hr_designation_id')
                                ->leftJoin('hr_section AS s', 'b.as_section_id', 'hr_section_id')
                                ->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                                ->where('b.as_doj','<=', $till_date)
                                ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                                ->groupBy('b.associate_id')
                                ->get();
                    }
                }

                //for increment pending--fetch data
                if($request->increment_report_type == 'increment_pending'){
                    $type_of_list = "Increment Pending List";
                    //for managment and staff
                    if($request->associate_type == 1 || $request->associate_type == 2){
                        $year = date("Y", strtotime($request->month));
                        $month = date("m", strtotime($request->month));
                        $info= DB::table('hr_as_basic_info AS b')
                                    ->where(['b.as_unit_id'=> $request->unit_id, 'b.as_emp_type_id'=> $request->associate_type])
                                    ->whereIn('b.as_status', [1,6])
                                    ->leftJoin('hr_increment AS inc', function($query) use($request){
                                        $query->on('inc.associate_id', '=', 'b.associate_id')
                                                ->where('inc.effective_date','<=', $request->month);
                                    })
                                    ->select([
                                        'b.as_id',
                                        'b.associate_id',
                                        'b.as_name',
                                        'b.as_doj',
                                        'b.as_department_id',
                                        'b.as_status',
                                        'dep.hr_department_name',
                                        'd.hr_designation_name',
                                        's.hr_section_name',
                                        'l.hr_line_name',
                                        'inc.current_salary',
                                        'inc.increment_amount',
                                        'inc.amount_type',
                                        'inc.effective_date',
                                        'inc.status as increment_status',
                                        'inc.increment_type',
                                        'inc.status'

                                    ])
                                    ->leftJoin('hr_department AS dep', 'dep.hr_department_id', 'b.as_department_id')
                                    ->leftJoin('hr_designation AS d', 'b.as_designation_id', 'd.hr_designation_id')
                                    ->leftJoin('hr_section AS s', 'b.as_section_id', 'hr_section_id')
                                    ->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                                    ->whereMonth('b.as_doj','=', $month)
                                    ->whereYear('b.as_doj','<', $year)
                                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                                    ->groupBy('b.associate_id')
                                    ->get();
                    }
                    //for worker--fetch data
                    if($request->associate_type == 3){
                            $till_date = date('Y-m-d',strtotime('-365 day',  strtotime($request->month)));
                            $info= DB::table('hr_as_basic_info AS b')
                                            ->where(['b.as_unit_id'=> $request->unit_id, 'b.as_emp_type_id'=> $request->associate_type])
                                            ->whereIn('b.as_status', [1,6])
                                            ->leftJoin('hr_increment AS inc', function($query) use($request){
                                                $query->on('inc.associate_id', '=', 'b.associate_id')
                                                        ->where('inc.effective_date','<=', $request->month);
                                            })
                                            ->select([
                                                'b.as_id',
                                                'b.associate_id',
                                                'b.as_name',
                                                'b.as_doj',
                                                'b.as_department_id',
                                                'b.as_status',
                                                'dep.hr_department_name',
                                                'd.hr_designation_name',
                                                's.hr_section_name',
                                                'l.hr_line_name',
                                                'inc.current_salary',
                                                'inc.increment_amount',
                                                'inc.amount_type',
                                                'inc.effective_date',
                                                'inc.status as increment_status',
                                                'inc.increment_type',
                                                'inc.status'

                                            ])
                                            ->leftJoin('hr_department AS dep', 'dep.hr_department_id', 'b.as_department_id')
                                            ->leftJoin('hr_designation AS d', 'b.as_designation_id', 'd.hr_designation_id')
                                            ->leftJoin('hr_section AS s', 'b.as_section_id', 'hr_section_id')
                                            ->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
                                            ->where('b.as_doj','<=',$till_date)
                                            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                                            ->groupBy('b.associate_id')
                                            ->get();
                                // dd($info);
                    }
                 }
    		}
            // dd($info);
    		foreach($info AS $associate){
    			$education= DB::table('hr_education AS e')
    					->where('e.education_as_id', $associate->associate_id)
    					->orderBy('e.education_level_id',"DESC")
    					->leftJoin('hr_education_degree_title AS t', 't.id', 'e.education_degree_id_1')
    					->orderBy('e.education_degree_id_1', "DESC")
    					->pluck('t.education_degree_title')
    					->first();
    			$associate->edu= $education;

				//calculating without pay days
                $wp = 0;
                $wp = Absent::where('associate_id',$associate->associate_id)
                ->whereBetween('date',[$previous_12,$previous_1])
                ->groupBy('associate_id')
                ->count();
    			$associate->without_pay=$wp;

    			//calculating with pay (Leaves) day
                $awpCount = 0;
                $awp=[]; //absent with pay (leave)
                $awp = Leave::where('leave_ass_id',$associate->associate_id)
                ->where('leave_from','<=',$previous_12)
                ->where('leave_to','>=',$previous_1)
                ->where('leave_status',1)
                ->get()->toArray();
                if(!empty($awp)) {
                    foreach($awp as $ak=>$ap) {
                        $from = Carbon::createFromFormat('Y-m-d',$ap['leave_from']);
                        $to = Carbon::createFromFormat('Y-m-d',$ap['leave_to']);
                        $diff_in_days = $to->diffInDays($from);
                        $awpCount += $diff_in_days+1;
                    }
                }
    			$associate->with_pay=$awpCount;

                // current salary
                $benefit_for_total_pay=DB::table('hr_benefits')
                                    ->where('ben_as_id', $associate->associate_id)
                                    ->where('ben_status', 1)
                                    ->orderBy('ben_id','DESC')
                                    ->first();
                $associate->total_pay = isset($benefit_for_total_pay->ben_current_salary)?$benefit_for_total_pay->ben_current_salary:'';

                // last increment
                $increments = Increment::where('associate_id', $associate->associate_id)->orderBy('effective_date', 'DESC')->first();
                // last increment date
                $associate->last_inc_date = isset($increments->effective_date)?date('Y-m-d',strtotime($increments->effective_date)):'';
                $associate->last_inc_id = isset($increments->id)?$increments->id:'';
                // last increment amount
                $incrementAmount = '';
                if(isset($increments->increment_amount)) {
                    if ($increments->amount_type==2) {
                        $incrementAmount = ($increments->current_salary/100)*$increments->increment_amount;
                    } else {
                        $incrementAmount = $increments->increment_amount;
                    }
                }
                $associate->lat_inc_amount = $incrementAmount;

                // status
    			if($associate->as_status == 6){
    				$associate->status = "M";
    			}
    			else{
    				$associate->status = "A";
    			}
    		}

    	}
    	$unit= Unit::where('hr_unit_id', $request->unit_id)
    				->pluck('hr_unit_name')
    				->first();
        $associate_type = $request->associate_type;
        $increment_report_type = $request->increment_report_type;
        if(!empty($info)){
            $departments= $info->unique('hr_department_name');
        }

    	// $month= $request->month;
        $date= $request->month;

        // Generate PDF
        if ($request->get('pdf') == true) {
            $pdf = PDF::loadView('hr/reports/monthly_increment_pdf', [
                'deptList' => $deptList,
                'info'     => $info,
                'departments' => $departments,
                'unit'     => $unit,
                'month'    => $month
            ]);
            return $pdf->download('Monthly_Increment_Report_'.date('d_F_Y').'.pdf'); 
        }
    	return view('hr/reports/monthly_increment',compact('unitList','deptList', 'info', 'departments', 'unit','date','associate_type','increment_report_type', 'type_of_list'));
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

    public function empPerformance(Request $request)
    {
        try {
            $uriIncrementId = $request->query('incId');
            // dd($uriIncrementId);
            $associateId = $request->associate_id;
            $date = $request->date;
            $employeeDetails = Employee::where('associate_id',$associateId)->get()->first();
            if(isset($employeeDetails->associate_id)) {
                $previous_12 = Carbon::createFromFormat('Y-m-d',$date)
                            ->modify('-12 months')
                            ->startOfMonth()
                            ->format('Y-m-d');
                $previous_1 = Carbon::createFromFormat('Y-m-d',$date)
                                ->modify('-1 months')
                                ->endOfMonth()
                                ->format('Y-m-d');
                // absent
                $absentCount = Absent::where('associate_id',$employeeDetails->associate_id)->whereBetween('date',[$previous_12,$previous_1])->count();
                // leave
                $leaveCount = 0;
                $leave = []; //absent with pay (leave)
                $leave = Leave::where('leave_ass_id',$employeeDetails->associate_id)
                ->where('leave_from','<=',$previous_12)
                ->where('leave_to','>=',$previous_1)
                ->where('leave_status',1)
                ->get()->toArray();

                if(!empty($leave)) {
                    foreach($leave as $ak=>$ap) {
                        $from = Carbon::createFromFormat('Y-m-d',$ap['leave_from']);
                        $to = Carbon::createFromFormat('Y-m-d',$ap['leave_to']);
                        $diff_in_days = $to->diffInDays($from);
                        $leaveCount += $diff_in_days+1;
                    }
                }
                $tableName = $this->getTableName($employeeDetails->as_unit_id);
                // Present
                $getPresentOT = DB::table($tableName)
                ->select([
                    \DB::raw('count(as_id) as present'),
                    \DB::raw('SUM(ot_hour) as ot')
                ])
               ->where('as_id', $employeeDetails->as_id)
               ->whereDate('in_time','>=',$previous_12)
               ->whereDate('in_time','<=',$previous_1)
               ->first();
                // late count
                $lateCount = DB::table($tableName)
                ->where('as_id', $employeeDetails->as_id)
                ->whereDate('in_time','>=',$previous_12)
                ->whereDate('in_time','<=',$previous_1)
                ->where('late_status', 1)
                ->count();
                // halfCount
                $halfCount = DB::table($tableName)
                ->where('as_id', $employeeDetails->as_id)
                ->whereDate('in_time','>=',$previous_12)
                ->whereDate('in_time','<=',$previous_1)
                ->where('remarks', 'HD')
                ->count();

                // disciplinary list
                DB::statement(DB::raw('set @serial_no=0'));
                $disciplinaryList = DB::table('hr_dis_rec AS r')
                    ->select(
                        DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                        'r.dis_re_id',
                        'r.dis_re_offender_id',
                        'r.dis_re_griever_id',
                        'r.dis_re_req_remedy',
                        'r.dis_re_discussed_date',
                        'r.dis_re_doe_from',
                        'r.dis_re_doe_to',
                        'i.hr_griv_issue_name AS issue',
                        's.hr_griv_steps_name AS step'
                    )
                    ->leftJoin('hr_grievance_issue AS i', 'i.hr_griv_issue_id', '=', 'r.dis_re_issue_id')
                    ->leftJoin('hr_grievance_steps AS s', 's.hr_griv_steps_id', '=', 'r.dis_re_issue_id')
                    ->where('r.dis_re_offender_id',$employeeDetails->associate_id)
                    ->whereDate('r.dis_re_doe_from','>=',$previous_12)
                    ->whereDate('r.dis_re_doe_to','<=',$previous_1)
                    ->orderBy('r.dis_re_discussed_date','desc')
                    ->get()->toArray();

                // designation employee list
                $designationUpdate = '';
                $employeeInfo = Employee::where('associate_id',Auth()->user()->associate_id)->first();
                $designationInfo = DB::table('hr_designation')->where('hr_designation_id',$employeeInfo->as_designation_id)->first();
                $positionList = DB::table('hr_designation as d')
                ->leftJoin('hr_as_basic_info as b', 'b.as_designation_id','d.hr_designation_id')
                ->where('d.hr_designation_emp_type',1)
                ->where('d.hr_designation_position','<',$designationInfo->hr_designation_position)
                ->get()->toArray();

                // aproval history
                $incrementData = [];
                $approvalComplete = [];
                if($uriIncrementId != null) {
                    $approvalHistory = DB::table('hr_increment_approval')
                    ->where(['associate_id' => $associateId, 'hr_increment_id' => $uriIncrementId])
                    ->orWhere('status',0)
                    ->orderBy('date','ASC')
                    ->get()->toArray();

                    // complete approval
                    $approvalComplete = DB::table('hr_increment_approval')
                    ->where(['associate_id' => $associateId, 'hr_increment_id' => $uriIncrementId,'status' => 2])
                    ->get()->toArray();
                    // increment data
                    $incrementData = DB::table('hr_increment')->where('id',$uriIncrementId)->first();
                    if(empty($incrementData)) {
                        return back()->with('Employee information not match.');
                    }
                    if($incrementData->associate_id != $employeeDetails->associate_id) {
                        return back()->with('Employee information not match.');
                    }
                } else {
                    $approvalHistory = DB::table('hr_increment_approval')
                    ->where(['associate_id' => $associateId, 'status' => 0])
                    ->orderBy('date','ASC')
                    ->get()->toArray();
                }

                // dd($incrementData);
                return view('hr/reports/employee_performance',compact('associateId','employeeDetails','absentCount','leaveCount','getPresentOT','lateCount','halfCount','disciplinaryList','positionList','date','approvalHistory','incrementData','approvalComplete'));
            } else {
                return 'Employee Not Found.';
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function empPerformanceSave(Request $request)
    {
        try {
            if(isset($request->reject)) {
                $data = [
                    'associate_id' => $request->associate_id,
                    'date' => $request->date,
                    'comments' => $request->comments,
                    'submit_by' => Auth()->user()->associate_id,
                    'status' => 0
                ];
                $lastId = DB::table('hr_increment_approval')->insertGetId($data);
                $this->logFileWrite("Employee Performance reject in (hr_increment_approval)", $lastId);
                return back()->with('success', 'Reject Successful.');
            }

            $salary= DB::table('hr_benefits')
                        ->where('ben_as_id', $request->associate_id)
                        ->pluck('ben_current_salary')
                        ->first();

            $doj= DB::table('hr_as_basic_info')
                    ->where('associate_id',$request->associate_id )
                    ->pluck('as_doj')
                    ->first();
            $eligible_at = date("Y-m-d", strtotime("$doj + 1 year"));
            $created_by = Auth()->user()->associate_id;

            $incrementId = $request->incrementid!=null?$request->incrementid:'';
            if(empty($incrementId)) {
                $incrementData = [
                    'associate_id' => $request->associate_id ,
                    'current_salary' => $salary,
                    'increment_type' => 2,
                    'increment_amount' => $request->increment_amount ,
                    'amount_type' => $request->amount_type ,
                    'applied_date' => $request->applied_date ,
                    'eligible_date' => $eligible_at ,
                    'effective_date' => $request->effective_date ,
                    'status' => 0 ,
                    'created_by' => $created_by,
                    'created_at' => Carbon::now()->toDateTimeString()
                ];
                $incrementId = DB::table('hr_increment')->insertGetId($incrementData);
                $this->logFileWrite("Increment Entry Saved", $incrementId);
            }

            $data = [
                'associate_id' => $request->associate_id,
                'date' => $request->date,
                'hr_increment_id' => $incrementId,
                'comments' => $request->comments,
                'submit_by' => Auth()->user()->associate_id,
                'created_on' => Carbon::now()->toDateTimeString()
            ];

            $msg = '';
            if($request->forward == null) {
                $data['status'] = 2;
                $msg = 'Approved';
            } else {
                $data['status'] = 1;
                $data['submit_to'] = $request->forward;
                $msg = 'Forward';
            }
            // dd($data);
            $lastId = DB::table('hr_increment_approval')->insertGetId($data);
            $this->logFileWrite("Employee Performance approved in (hr_increment_approval)", $lastId);

            return redirect('hr/reports/monthy_increment')->with('success', $msg.' Successful.');
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function empPerformanceIncrementList(Request $request)
    {
        $associateId = $request->associate_id;
        $incrementApproval = DB::table('hr_increment_approval')
        ->where(['submit_to' => auth()->user()->associate_id, 'status' => 1])
        ->get()
        ->toArray();
        if(!empty($incrementApproval)) {
            $where = [];
            $completeCheck = [];
            $incrementApprovalList = [];
            foreach($incrementApproval as $k=>$increment) {
                $where = [
                    'associate_id' => $increment->associate_id,
                    'hr_increment_id' => $increment->hr_increment_id,
                    'status' => 2
                ];
                $completeCheck = DB::table('hr_increment_approval')->where($where)->get()->toArray();
                if(empty($completeCheck)) {
                    $incrementApprovalList[$k] = $increment;
                }
            }
        }
        return view('hr/reports/employee_performance_increment_list',compact('associateId','incrementApprovalList'));
    }
}
