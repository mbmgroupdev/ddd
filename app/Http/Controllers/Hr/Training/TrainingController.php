<?php

namespace App\Http\Controllers\Hr\Training;

use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Training;
use App\Models\Hr\TrainingNames;
use Carbon\Carbon;
use DB, Validator, DataTables, ACL;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
	// Show Add Training Form
    public function showForm()
    {

        $getData = DB::table('hr_shift_roaster')
        ->where('day_1', 'Day Early')
        ->get();
        $getDate = ['2020-09-01', '2020-09-02', '2020-09-03', '2020-09-04', '2020-09-05','2020-09-06', '2020-09-07', '2020-09-08', '2020-09-09', '2020-09-10', '2020-09-11','2020-09-12', '2020-09-13', '2020-09-14', '2020-09-15'];
        foreach ($getData as $data) {
            $getEmployee = Employee::where('associate_id', $data->shift_roaster_associate_id)->first();
            $tableName = get_att_table($getEmployee->as_unit_id);
            $unitId = $getEmployee->as_unit_id;
            foreach ($getDate as $adate) {
                $today = date('Y-m-d', strtotime($adate));
                $month = date('m', strtotime($today));            
                $year = date('Y', strtotime($today));
                $dayStatus = EmployeeHelper::employeeDateWiseStatus($today, $getEmployee->associate_id, $getEmployee->as_unit_id, $getEmployee->shift_roaster_status); 
                $getEmpAtt = DB::table($tableName)
                ->where('as_id', $getEmployee->as_id)
                ->where('in_date', $today)
                ->first();
                if($getEmpAtt != null){
                    $cIn = strtotime(date("H:i", strtotime($getEmpAtt->in_time)));
                    $cOut = strtotime(date("H:i", strtotime($getEmpAtt->out_time)));
                    $day_of_date = date('j', strtotime($getEmpAtt->in_time));
                    $day_num = "day_".$day_of_date;
                    $shift= DB::table("hr_shift_roaster")
                    ->where('shift_roaster_month', $month)
                    ->where('shift_roaster_year', $year)
                    ->where("shift_roaster_user_id", $getEmployee->as_id)
                    ->select([
                        $day_num,
                        'hr_shift.hr_shift_id',
                        'hr_shift.hr_shift_start_time',
                        'hr_shift.hr_shift_end_time',
                        'hr_shift.hr_shift_break_time',
                        'hr_shift.hr_shift_night_flag'
                    ])
                    ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                        $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                        $q->where('hr_shift.hr_shift_unit_id', $unitId);
                    })
                    ->orderBy('hr_shift.hr_shift_id', 'desc')
                    ->first();
                    
                    if(!empty($shift) && $shift->$day_num != null){
                        $cShifStartTime = strtotime(date("H:i", strtotime($shift->hr_shift_start_time)));
                        $cShifStart = $shift->hr_shift_start_time;
                        $cShifEnd = $shift->hr_shift_end_time;
                        $cBreak = $shift->hr_shift_break_time;
                        $nightFlag = $shift->hr_shift_night_flag;
                    }
                    else{
                        $cShifStartTime = strtotime(date("H:i", strtotime($getEmployee->shift['hr_shift_start_time'])));
                        $cShifStart = $getEmployee->shift['hr_shift_start_time'];
                        $cShifEnd = $getEmployee->shift['hr_shift_end_time'];
                        $cBreak = $getEmployee->shift['hr_shift_break_time'];
                        $nightFlag = $getEmployee->shift['hr_shift_night_flag'];
                    }

                    //late count
                    $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($getEmployee->as_unit_id, $getEmployee->shift['hr_shift_name']);
                    if($getLateCount != null){
                        if($today >= $getLateCount->date_from && $today <= $getLateCount->date_to){
                            $lateTime = $getLateCount->value;
                        }else{
                            $lateTime = $getLateCount->default_value;
                        }
                    }else{
                        $lateTime = 3;
                    }
                    $inTime = ($cShifStartTime+($lateTime * 60));

                    if($dayStatus == 'OT'){
                        $late = 0;
                    }else if($cIn > $inTime  || $getEmpAtt->remarks == 'DSI'){
                        $late = 1;
                    }else{
                        $late = 0;
                    }

                    // CALCULATE OVER TIME
                    if(!empty($cOut))
                    {
                        if($getEmployee->as_ot == 1 && $getEmpAtt->remarks != 'DSI'){
                            $otHour = EmployeeHelper::daliyOTCalculation($getEmpAtt->in_time, $getEmpAtt->out_time, $cShifStart, $cShifEnd, $cBreak, $nightFlag, $getEmployee->associate_id, $getEmployee->shift_roaster_status, $getEmployee->as_unit_id);
                        }else{
                            $otHour = 0;
                        }

                        // update attendance table ot_hour   
                        DB::table($tableName)
                        ->where('id', $getEmpAtt->id)
                        ->update([
                            'ot_hour' => $otHour,
                            'late_status' => $late
                        ]);
                        
                        
                    }
                }
                

                $yearMonth = $year.'-'.$month; 
                if($month == date('m')){
                    $totalDay = date('d');
                }else{
                    $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                }
                $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $getEmployee->as_id, $totalDay))
                        ->onQueue('salarygenerate')
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);


            }            
            
        }




        return 'done';











    	$trainingNames = TrainingNames::where('hr_tr_status', '1')
    					->pluck('hr_tr_name', 'hr_tr_name_id');

    	return view('hr/training/add_training', compact('trainingNames'));
    }

    # Store Training
    public function saveTraining(Request $request)
    {
        //ACL::check(["permission" => "hr_training_add"]);
        #-----------------------------------------------------------#

    	$validator = Validator::make($request->all(), [
            'tr_as_tr_id'     => 'required|max:11',
            'tr_trainer_name' => 'required|max:128',
            'tr_description'  => 'required|max:1024',
            'tr_start_date'   => 'required|date',
            'tr_end_date'     => 'date|nullable',
            'tr_start_time'   => 'required|max:5',
            'tr_end_time'     => 'required|max:5'
        ]);


        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            //-----------Store Data---------------------
        	$store = new Training;
			$store->tr_as_tr_id  = $request->tr_as_tr_id;
			$store->tr_trainer_name = $request->tr_trainer_name;
			$store->tr_description = $request->tr_description;
			$store->tr_start_date = (!empty($request->tr_start_date)?date('Y-m-d',strtotime($request->tr_start_date)):null);
			$store->tr_end_date = (!empty($request->tr_end_date)?date('Y-m-d',strtotime($request->tr_end_date)):null);
			$store->tr_start_time = (!empty($request->tr_start_time)?date('H:i',strtotime($request->tr_start_time)):null);
			$store->tr_end_time = (!empty($request->tr_end_time)?date('H:i',strtotime($request->tr_end_time)):null);

			if ($store->save())
			{
                $this->logFileWrite("Training Entry Saved", $store->tr_id);
        		return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
			}
			else
			{
        		return back()
        			->withInput()->with('error', 'Please try again.');
			}
        }
    }


    # training list
    public function trainingList()
    {
        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        return view('hr/training/training_list');
    }

    # training data
    public function getData()
    {
        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('hr_training AS tr')
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                'tr.*',
                'tn.hr_tr_name AS training_name'
            )
            ->leftJoin('hr_training_names AS tn','tn.hr_tr_name_id', '=', 'tr.tr_as_tr_id')
            ->orderBy('tr.tr_start_date','desc')
            ->orderBy('tr.tr_id','desc')
            ->get();

        return DataTables::of($data)
            ->addColumn('schedule_date', function ($data) {

                if($data->tr_start_date != null)
                {
                    $start_date=date('d-M-Y',strtotime($data->tr_start_date));

                    if (!empty($data->tr_end_date))
                    {
                        $end_date=date('d-M-Y',strtotime($data->tr_end_date));
                    }
                    else
                    {
                        $end_date = "Continue";
                    }

                    return "<strong>Start : </strong><span>$start_date</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$end_date</span>";
                }
                else
                {
                    return "<strong>Start : </strong><span>$data->tr_start_date</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$data->tr_end_date</span>";
                }
            })
            ->addColumn('schedule_time', function ($data) {
                return "<strong>Start : </strong><span>$data->tr_start_time</span><br/><strong>End&nbsp;&nbsp;&nbsp;: </strong><span>$data->tr_end_time</span>";
            })
            ->addColumn('action', function ($data) {
                if ($data->tr_status == 1)
                    return "<div class=\"btn-group\">
                            <button type=\"button\" disabled class='btn btn-xs btn-success' style='width:55px;'>Active</button>
                            <a href=".url('hr/training/training_status/'.$data->tr_id."/inactive")." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Inactive Now!\" style='width:29px;'>
                            <i class=\"ace-icon fa fa-times bigger-120\"></i>
                        </div>";
                else
                    return "<div class=\"btn-group\">
                            <button type=\"button\" disabled class='btn btn-xs btn-danger'>Inactive</button>
                            <a href=".url('hr/training/training_status/'.$data->tr_id."/active")." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Active Now!\">
                            <i class=\"ace-icon fa fa-check bigger-120\"></i>
                        </div>";
            })
            ->rawColumns(['serial_no', 'schedule_date', 'schedule_time', 'action'])
            ->toJson();
    }


    # training Status
    public function trainingStatus(Request $request)
    {

        //ACL::check(["permission" => "hr_training_list"]);
        #-----------------------------------------------------------#

        if ($request->status == 'active')
        {
            Training::where('tr_id', $request->id)
            ->update(['tr_status'=>'1']);

            $this->logFileWrite("Training Activated", $request->id);
            return back()->with('success', 'Training is Activated!');
        }
        else
        {
            Training::where('tr_id', $request->id)
            ->update(['tr_status'=>'0']);

            $this->logFileWrite("Training Inactivated", $request->id);
            return back()->with('success', 'Training is Inactivated!');

        }

    }
}
