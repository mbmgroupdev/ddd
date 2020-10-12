<?php
namespace App\Http\Controllers\Hr\TimeAttendance;
use App\Helpers\Custom;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use App\Models\Hr\YearlyHolyDay;
use App\Models\Hr\EmpType;
use App\Models\Hr\Area;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\ProcessUnitWiseSalary;
use App\Jobs\ProcessAttendanceInOutTime;
use Validator, Auth, ACL, DB, DataTables;
class AttendaceManualController extends Controller
{
    # show form
    public function showForm()
    {
        $unitList  = Unit::where('hr_unit_status', '1')->whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
        $unitList->put(1001,  "Common Unit");

        return view('hr.timeattendance.attendance_manual', compact('unitList'));
    }

    # post data
    public function saveData(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'hr_att_as_id'      => 'required|max:10|min:10',
            'hr_att_date'       => 'required|date',
            'hr_att_start_time' => 'max:10',
            'hr_att_end_time'   => 'max:10',
            'remarks'           => 'max:45'
        ]);
        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->with('error', 'Please fillup all required fields!.');
        }
        else
        {
            // format date time
            $date = (!empty($request->hr_att_date)?date('Y-m-d', strtotime($request->hr_att_date)):null);
            $startTime = (!empty($request->hr_att_start_time)?date('H:i:s', strtotime($request->hr_att_start_time)):null);
            $endTime = (!empty($request->hr_att_end_time)?date('H:i:s', strtotime($request->hr_att_end_time)):null);
            if($startTime){
            $in = date('Y-m-d H:i:s', strtotime("$date $startTime"));
            }
            else{
                $in = null;
            }
            if($endTime){
            $out = date('Y-m-d H:i:s', strtotime("$date $endTime"));
            }
            else{
                $out= null;
            }

            $associate= DB::table('hr_as_basic_info')
                ->where('associate_id', $request->hr_att_as_id)
                ->select([
                    'as_id',
                    'associate_id',
                    'as_unit_id'
                ])
                ->first();
            $unit= $associate->as_unit_id;
            $tableName= get_att_table($unit).' AS a';
            // checking existing punch
            $ispunched = DB::table($tableName)
                        ->where('a.as_id', $associate->as_id)
                        ->whereDate('a.in_time', $date)
                        ->select('a.*')
                        ->first();
            // geting shift code
            $day_input= date('d', strtotime($date));
            $day= "day_".ltrim($day_input,'0');
            $shift = DB::table('hr_shift_roaster')
                ->where('shift_roaster_associate_id', $request->hr_att_as_id)
                ->pluck($day)
                ->first();
            if($shift == null){
                DB::table('hr_as_basic_info AS b')
                        ->where('b.associate_id', $request->hr_att_as_id)
                        ->leftJoin('hr_shift', function($q) {
                            $q->on('hr_shift.hr_shift_name', 'b.as_shift_id')
                              ->on('hr_shift.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE hr_shift.hr_shift_name = b.as_shift_id AND hr_shift.hr_shift_unit_id = b.as_unit_id )"));
                        })
                        ->pluck('hr_shift.hr_shift_code')
                        ->first();
            }
            if($shift == null){
                return back()
                ->with('error', "No shift assigned for this associate!!");
            }
            else{
                $shift_code= $shift;
            }
            $user= Auth::user()->associate_id;
            $up_date= date('Y:m:d H:i:s');
            if($ispunched == null)
            {
                $tableName = substr($tableName, 0, -5);
                    DB::table($tableName)
                    ->insert([
                        'as_id' => $associate->as_id,
                        'in_date' => date('Y-m-d', strtotime($in)),
                        'in_time' => $in,
                        'out_time' => $out,
                        'hr_shift_code' => $shift_code,
                        'remarks' => $request->remarks,
                        'updated_by' => $user,
                        'updated_at' => $up_date
                    ]);
                return back()
                    ->with('success', "Attendance Added Successfully!");
            }
            else
            {
                if($in==null)
                    $in= $ispunched->in_time;
                if($out==null)
                    $out= $ispunched->out_time;
                DB::table($tableName)
                ->where('a.as_id', $associate->as_id)
                ->whereDate('a.in_time', $date)
                ->update([
                    'a.in_date'       => date('Y-m-d', strtotime($in)),
                    'a.in_time'       => $in,
                    'a.out_time'      => $out,
                    'a.hr_shift_code' => $shift_code,
                    'a.remarks' =>  $request->remarks,
                    'a.updated_by' =>  $user,
                    'a.updated_at' =>  $up_date
                ]);
                return back()
                    ->with('success', "Attendance Updated Successfully!");
            }
        }
    }

    public function getExistingPunch(Request $request)
    {
        $associate= DB::table('hr_as_basic_info')
                ->where('associate_id', $request->id)
                ->select([
                    'as_id',
                    'associate_id',
                    'as_unit_id',
                    'as_shift_id',
                    'as_ot'
                ])
                ->first();
        $unit= $associate->as_unit_id;
        $tableName= get_att_table($unit).' AS a';
        
        $data = array();
        // if any punch of today exists then show it
        $ifexists= DB::table($tableName)
                    ->where('a.as_id', $associate->as_id)
                    ->whereDate('a.in_time', $request->date)
                    ->select([
                        'a.as_id',
                        'a.in_time',
                        'a.out_time',
                        'a.ot_hour',
                        'a.hr_shift_code'
                    ]);
        if ($ifexists->exists())
        {
            $data['status'] = true;
            //if in_time exist then show in_time else in_time=null
            if($ifexists->first()->in_time){
                $data['in_time'] = date("H:i", strtotime($ifexists->first()->in_time));
            }
            else{
                $data['in_time'] = null;
            }
            //if out_time exist then show out_time else out_time=null
            if($ifexists->first()->out_time){
                $data['out_time'] = date("H:i", strtotime($ifexists->first()->out_time));
                $data['ot_time']  = date("H:i",strtotime($ifexists->first()->ot_hour));
                
              }
            else{
              
                $data['out_time']= null;
              }
        }
        else
        {
            $data['status'] = false;
        }
       if($associate->as_ot == 1){
         if(!isset($associate->as_shift_id)){
            $shiftinfo = DB::table('hr_shift')->where('hr_shift_unit_id',$unit)->first();
         }else{
            $shiftinfo = DB::table('hr_shift')->where('hr_shift_id',$associate->as_shift_id)->first();
         }
         $data['hr_shift_start_time'] = $shiftinfo->hr_shift_start_time;
         $data['hr_shift_end_time'] = $shiftinfo->hr_shift_end_time;
         $data['hr_shift_break_time'] = $shiftinfo->hr_shift_break_time;
       }
        return $data;
    }

    public function calculateOt(Request $request)
    {
        
        $employee = Employee::where('associate_id',$request->associateId)
        ->with('shift')
        ->first();
        $shiftData = DB::table('hr_shift');
        $shiftDataSql = $shiftData->toSql();
        // 
        $overtimes = 0;
        if($employee != null && $employee->as_ot == 1)
        {
            $day_of_date = date('j', strtotime($request->att_date));
            $month = date('m', strtotime($request->att_date));
            $year = date('Y', strtotime($request->att_date));
            $day_num = "day_".$day_of_date;
            $shift= DB::table("hr_shift_roaster")
            ->where('shift_roaster_month', $month)
            ->where('shift_roaster_year', $year)
            ->where("shift_roaster_user_id", $employee->as_id)
            ->select([
                $day_num,
                's.hr_shift_id',
                's.hr_shift_start_time',
                's.hr_shift_end_time',
                's.hr_shift_break_time',
                's.hr_shift_night_flag'
            ])
            ->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function($q) use ($shiftData, $day_num, $employee) {
                $q->on('s.hr_shift_name', 'hr_shift_roaster.'.$day_num)->addBinding($shiftData->getBindings());
                $q->where('s.hr_shift_unit_id', $employee->as_unit_id);
            })
            ->orderBy('s.hr_shift_id', 'desc')
            ->first(); 
            if(!empty($shift) && $shift->$day_num != null){
                $cShifStart = $shift->hr_shift_start_time;
                $cShifEnd = $shift->hr_shift_end_time;
                $cBreak = $shift->hr_shift_break_time;
                $nightFlag = $shift->hr_shift_night_flag;
            }
            else{
                $cShifStart = $employee->shift['hr_shift_start_time'];
                $cShifEnd = $employee->shift['hr_shift_end_time'];
                $cBreak = $employee->shift['hr_shift_break_time'];
                $nightFlag = $employee->shift['hr_shift_night_flag'];
            }
            $intime = $request->att_date.' '.$request->in_time;
            $outtime = strtotime($request->in_time)>strtotime($request->out_time)?date('Y-m-d',strtotime("+1 day", strtotime($request->att_date))).' '.$request->out_time:$request->att_date.' '.$request->out_time;
            $outTimeEx = explode(' ', $outtime);
            $overtimes = 0;
            if(isset($outTimeEx[1]) && $outTimeEx[1] != '00:00:00'){
                $overtimes = EmployeeHelper::daliyOTCalculation($intime, $outtime, $cShifStart, $cShifEnd, $cBreak, $nightFlag, $employee->associate_id, $employee->shift_roaster_status, $employee->as_unit_id);
            }

            
            $overtime = numberToTimeClockFormat($overtimes);
            return json_encode(['s_ot' => ($overtimes), 'n_ot' => ($overtime)]);

        }else{
            return json_encode(0);
        }
    }

    public function manualAttLog()
    {
        return view('hr/timeattendance/attendance_manual_log');
    }

    public function manualAttLogData()
    {
        DB::statement(DB::raw("SET @s:=0"));
        $data= DB::select("
            SELECT @s:=@s+1 AS serial_no, a.*,  b.as_name, b.associate_id
            FROM hr_attendance_mbm AS a
            LEFT JOIN hr_as_basic_info AS b ON b.as_id = a.as_id
            WHERE a.remarks != '' OR a.remarks != NULL
        ");

        return Datatables::of($data)
          ->editColumn('in_time', function($data){
              if($data->in_time!=null)
                  return date("H:i", strtotime($data->in_time));
              else
                  return null;
          })
          ->editColumn('out_time', function($data){
              if($data->out_time!=null)
                  return date("H:i", strtotime($data->out_time));
              else
                  return null;
          })
          ->toJson();
    }

    public function defaultPunch()
    {
        //$this->testService();
        //dd(date('H:i:s', strtotime('17.17')));
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
          ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
          ->orderBy('hr_unit_name', 'desc')
          ->pluck('hr_unit_short_name', 'hr_unit_id');

        $shiftList = Shift::where('hr_shift_status', 1)->pluck("hr_shift_name", "hr_shift_id");
        $areaList = Area::where('hr_area_status',1)->pluck('hr_area_name','hr_area_id');

        return view('hr/timeattendance/default_punch',compact('employeeTypes','unitList','shiftList','areaList'));
    }

    public function storeDefaultPunch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_from'              => 'required|date',
            'date_to'              => 'required|date'
        ]);
        if ($validator->fails())
        {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Please fill up all required fields!');
        }
        $start_date = $request->date_from;
        $end_date = $request->date_to;
        $totalDays  = (date('d', strtotime($end_date))-date('d', strtotime($start_date)));
        $dates = [];
        for($i=0; $i<=$totalDays; $i++) {
            $date = date('Y-m-d', strtotime("+".$i." day", strtotime($start_date)));
            $day_of_date = date('j', strtotime($date));
            $year = Carbon::parse($date)->format('Y');
            $month = Carbon::parse($date)->format('m');
            $day_num = "day_".$day_of_date;

            foreach ($request->assigned as $key => $associate) {
                $getEmployee = Employee::where('associate_id', $associate)->first();
                if($getEmployee){
                    $table = get_att_table($getEmployee->as_unit_id);

                    $dayStatus = EmployeeHelper::employeeDateWiseStatus($date, $getEmployee->associate_id, $getEmployee->as_unit_id, $getEmployee->shift_roaster_status);

                    $dates[$date] = $dayStatus;

                    // check att 
                    $check = DB::table($table)->where(['as_id' => $getEmployee->as_id, 'in_date' => $date ])->first();
                    // get shift in time
                    if(($dayStatus == 'open' && $check == null)  || ($dayStatus == 'OT' && $check == null)){
                        $shift = DB::table("hr_shift_roaster")
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
                            ->leftJoin('hr_shift', function($q) use($day_num, $getEmployee) {
                                $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                                $q->where('hr_shift.hr_shift_unit_id', $getEmployee->as_unit_id);
                            })
                            ->orderBy('hr_shift.hr_shift_id', 'desc')
                            ->first();
                        
                        if(!empty($shift) && $shift->$day_num != null){
                            $in_time = $date.' '.$shift->hr_shift_start_time;
                            $out_time = $date.' '.$shift->hr_shift_end_time;
                            if($shift->hr_shift_start_time > $shift->hr_shift_end_time){
                                $out_time = date('Y-m-d', strtotime("+1 day", strtotime($date))).' '.$shift->hr_shift_end_time;

                            }
                            $shift_code = $shift->hr_shift_code??'';

                        }
                        else{
                            $in_time = $date.' '.$getEmployee->shift['hr_shift_start_time'];
                            $out_time = $date.' '.$getEmployee->shift['hr_shift_end_time'];
                            if($getEmployee->shift['hr_shift_start_time'] > $getEmployee->shift['hr_shift_end_time']){
                                $out_time = date('Y-m-d', strtotime("+1 day", strtotime($date))).' '.$getEmployee->shift['hr_shift_end_time'];

                            }
                            $shift_code = $getEmployee->shift['hr_shift_code'];
                        }

                        

                        $lastPunchId = DB::table($table)
                                 ->insertGetId([
                                     'as_id' => $getEmployee->as_id,
                                     'in_date' => $date,
                                     'in_time' => $in_time,
                                     'out_time'=> $out_time,
                                     'hr_shift_code' => $shift_code,
                                     'ot_hour' => 0,
                                     'late_status' => 0,
                                     'remarks'=>'DP',
                                     'updated_by' => auth()->user()->associate_id,
                                     'updated_at' => NOW()
                                ]);

                        $queue = (new ProcessAttendanceInOutTime($table, $lastPunchId, $getEmployee->as_unit_id))
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);

                        $this->logFileWrite('Default punch entry for '.$getEmployee->associate_id, $lastPunchId );
                    }

                }
                
            }
            
        }

        return back()->with('success', "Attendance Updated Successfully!");



    }

    public function testService()
    {
        $record =       
        $loops = [];
        foreach ($record as $key => $rr) {
            if($rr['OUT_TIME'] > 0){
                $time = date('H:i:s', strtotime($rr['OUT_TIME']));
                $innnn = date('H:i:s', strtotime($rr['IN_TIME']));
                $date = date('Y-m-d', strtotime($rr['WD']));
                $day_of_date = date('j', strtotime($date));
                $year = '2020';
                $month = '09';
                $day_num = "day_".$day_of_date;

                $getEmployee = Employee::where('as_oracle_code', $rr['PID'])->first();
                if($getEmployee){
                    $table = get_att_table($getEmployee->as_unit_id);

                    $dayStatus = EmployeeHelper::employeeDateWiseStatus($date, $getEmployee->associate_id, $getEmployee->as_unit_id, $getEmployee->shift_roaster_status);

                    $dates[$date] = $dayStatus;

                    $getEmpAtt = DB::table($table)->where(['as_id' => $getEmployee->as_id, 'in_date' => $date ])->first();
                    // check att 
                    // get shift in time
                    if($getEmpAtt != null){

                        $shift = DB::table("hr_shift_roaster")
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
                            ->leftJoin('hr_shift', function($q) use($day_num, $getEmployee) {
                                $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                                $q->where('hr_shift.hr_shift_unit_id', $getEmployee->as_unit_id);
                            })
                            ->orderBy('hr_shift.hr_shift_id', 'desc')
                            ->first();

                        $in_time = $date.' '.$innnn;
                        
                        if(!empty($shift) && $shift->$day_num != null){

                            $out_time = $date.' '.$time;

                            if($shift->hr_shift_start_time > $shift->hr_shift_end_time){
                                $out_time = date('Y-m-d', strtotime("+1 day", strtotime($date))).' '.$time;

                            }
                            $shift_code = $shift->hr_shift_code??'';

                            $cShifStartTime = strtotime(date("H:i", strtotime($shift->hr_shift_start_time)));
                            $cShifStart = $shift->hr_shift_start_time;
                            $cShifEnd = $shift->hr_shift_end_time;
                            $cBreak = $shift->hr_shift_break_time;
                            $nightFlag = $shift->hr_shift_night_flag;

                        }
                        else{
                            if($getEmployee->shift){
                                $cShifStartTime = strtotime(date("H:i", strtotime($getEmployee->shift['hr_shift_start_time'])));
                                $cShifStart = $getEmployee->shift['hr_shift_start_time'];
                                $cShifEnd = $getEmployee->shift['hr_shift_end_time'];
                                $cBreak = $getEmployee->shift['hr_shift_break_time'];
                                $nightFlag = $getEmployee->shift['hr_shift_night_flag'];

                               

                                $out_time = $date.' '.$time;
                                if($getEmployee->shift['hr_shift_start_time'] > $getEmployee->shift['hr_shift_end_time']){
                                    $out_time = date('Y-m-d', strtotime("+1 day", strtotime($date))).' '.$time;

                                }
                            }
                        }

                        if($getEmployee->as_ot == 1 && $getEmpAtt->remarks != 'DSI'){
                            $otHour = EmployeeHelper::daliyOTCalculation($in_time, $out_time, $cShifStart, $cShifEnd, $cBreak, $nightFlag, $getEmployee->associate_id, $getEmployee->shift_roaster_status, $getEmployee->as_unit_id);
                        }else{
                            $otHour = 0;
                        }
                        // update attendance table ot_hour   
                        $loops[$getEmpAtt->id] = DB::table($table)
                        ->where('id', $getEmpAtt->id)
                        ->update([
                            'ot_hour' => $otHour,
                            'out_time' => $out_time,
                            'remarks' => 'BM'
                        ]);

                        
                        $yearMonth = $year.'-'.$month; 
                        if($month == date('m')){
                            $totalDay = date('d');
                        }else{
                            $totalDay = Carbon::parse($yearMonth)->daysInMonth;
                        }
                        $queue = (new ProcessUnitWiseSalary($table, $month, $year, $getEmployee->as_id, $totalDay))
                                ->onQueue('salarygenerate')
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);

                        
                    }
                    

                }
            }
                
        }

        dd($loops);


    }


    public function employeeByField(Request $request)
    {
        $employees = Employee::where(function($query) use ($request){
            if ($request->emp_type != null)
            {
                $query->where('as_emp_type_id', $request->emp_type);
            }
            if ($request->unit != null)
            {
                $query->where('as_unit_id', $request->unit);
            }
            if ($request->otnonot != null)
            {
                $query->where('as_ot', $request->otnonot);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->section != null)
            {
                $query->where('as_section_id', $request->section);
            }
            if ($request->subsection != null)
            {
                $query->where('as_subsection_id', $request->subsection);
            }
            if ($request->designation != null)
            {
                $query->where('as_designation_id', $request->status);
            }
            $query->where("as_status", 1);
        })
        ->whereIn('as_unit_id', auth()->user()->unit_permissions())
        ->get();

      
        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
        $data['result'] = "";
        $data['total'] = 0;

        $designation = designation_by_id();
        foreach($employees as $key => $employee){
            $deg = $designation[$employee->as_designation_id]['hr_designation_name'];
            $data['total'] += 1;
            $image = emp_profile_picture($employee);
            $data['result'].= "<tr class='add'>
                <td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img style='height: 30px;' src='".$image."' class='small-image' onError='this.onerror=null;this.src=\"/assets/images/avatars/avatar2.png\"'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td>
                <td>$employee->as_name </td><td>$deg </td></tr>";
               

        }
        return $data;
    }
}
