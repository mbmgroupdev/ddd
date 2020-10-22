<?php

namespace App\Http\Controllers\Hr\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\AttendanceMBM;
use App\Models\Employee;
use DataTables, DB, Auth, ACL, stdClass;

class AttendanceOperationController extends Controller
{

    public function attendanceReportData(Request $request)
    {
        $data = [];
        $designation = designation_by_id();
        $shiftData = shift_by_code();
        $type = $request->type;
        $input = $request->all();
        if($type == 'All' || $type == 'Present' || $type == 'Present(Intime Empty)' || $type == 'Present(Outtime Empty)' || $type == 'Present (Halfday)' || $type == 'Present (Late)' || $type == 'Present (Late(Outtime Empty))'){
            $data = $this->getAllAtendanceData($request->all());
        }elseif($type == 'Absent'){
            $data = $this->getAbsentData($request->all());
        }else{
            $data = [];
        }
        $date = isset($request->report_from)?$request->report_from:date('Y-m-d');

        // return $data[0]->shift;
        return DataTables::of($data)->addIndexColumn()
        ->addColumn('associate_id', function($data) {
            // return $data->associate_id;
            $date = '';
            if(!isset($data->status)){
                $data->status = "Present";
            }
            if ($data->in_time != null) {
                $date = date('Y-F-d', strtotime($data->in_time));
            } elseif($data->in_time == null && $data->out_time == null && $data->status == 'Absent' ){
                $date = date('Y-F-d', strtotime($data->date));
            } 
            // elseif($data->in_time == null && $data->out_time == null && $data->status != 'Absent' && $data->status != 'Holiday' ){
            //     $date = date('Y-F-d', strtotime($data->leave_from));
            // } 
            elseif($data->in_time == null && $data->out_time == null && $data->status == 'Holiday' ){

                $date = date('Y-F-d', strtotime($data->dates));
            }

            else {
                $date = date('Y-F-d', strtotime($data->out_time));
            }

            list($year,$month,$day) = explode('-',$date);
            $yearMonth = date('Y-m', strtotime($date));
            $url = 'hr/operation/job_card?associate='.$data->associate_id.'&month_year='.$yearMonth;

            return '<a href="'.url($url).'" target="blank">'.$data->associate_id.'</a>';
        })
        ->addColumn('att_date', function ($data) use ($date) {
            if ($data->in_time != null) {
                return date('Y-m-d', strtotime($data->in_time));
            } elseif($data->in_time == null && $data->out_time == null && $data->status == 'Absent' ){
                return date('Y-m-d', strtotime($data->date));
            }
            // elseif($data->in_time == null && $data->out_time == null && $data->status != 'Absent' && $data->status != 'Holiday' ){
            //     return '<strong>'.date('Y-m-d', strtotime($data->leave_from)).'</strong>'.' To '.'<strong>'.date('Y-m-d', strtotime($data->leave_to)).'</strong>';
            // }
            elseif($data->in_time == null && $data->out_time == null && $data->status == 'Holiday' ){
                return $data->date;
            }
            else{
                return date('Y-m-d', strtotime($data->out_time));
            }
        })

        ->addColumn('oracle_id', function ($data) {
            if(!empty($data->as_oracle_code)){
                return $data->as_oracle_code;
            }
        })
        ->addColumn('hr_designation_name', function ($data) use ($designation) {
            return $designation[$data->as_designation_id]['hr_designation_name']??'';
        })
        ->addColumn('hr_shift_name', function ($data) use ($shiftData) {
            return $shiftData[$data->hr_shift_code]['hr_shift_name']??'';
        })
        ->addColumn('att_status', function ($data) use ($date) {
            // if(isset($data->reportType) && $data->reportType == 'all'){
            if(isset($data->reportType) && ($data->reportType == 'all' || $data->reportType == 'present' )){

                if($data->remarks == 'DSI'){
                    if($data->late_status == 1){
                        $data->status = 'Present (Late)';
                    } else {
                        $data->status = 'Present';
                    }
               }elseif ($data->remarks == null) {
                    if($data->late_status == 1){
                        $data->status = 'Present (Late)';
                    } else {
                        $data->status = 'Present';
                    }
                }elseif ($data->remarks == 'HD') {
                    $data->status = 'Present (Halfday)';
                }
                else{
                    if($data->late_status == 1){
                        $data->status = 'Present (Late)';
                    }else{
                        $data->status = 'Present';
                    }
                }

            }

            if(($data->status == 'Casual Leave' || $data->status == 'Maternity Leave' || $data->status == 'Sick Leave')){
               return '<span class="inline badge badge-warning">'.$data->leave_type.' Leave</span> ';
            }
            if($data->status == 'Absent') {

               return '<span class="inline badge badge-danger">Absent</span> ';
            }
            if($data->status == 'Holiday') {
               return '<span class="inline badge badge-danger">Holiday</span> ';
            }
            if($data->status == 'Present (Late)' || $data->status == 'Present (Halfday)'){

               if($data->in_time == null){
                 return '<span class="inline badge badge-warning">Late</span> <span class="inline badge badge-primary">Present</span> <button class="inline btn btn-sm btn-round btn-danger make-absent" data-asid="'.$data->associate_id.'">Make Absent</button> ';
               }elseif ($data->out_time == null && $data->remarks != 'HD') {
                 return '<span class="inline badge badge-warning">Late</span> <span class="inline badge badge-primary">Present</span> <button class="inline btn btn-sm btn-round btn-danger make-halfday" data-asid="'.$data->associate_id.'">Make Halfday</button> ';
               }elseif ($data->out_time == null && $data->remarks == 'HD') {
                 $time = explode(' ',$data->in_time);
                 if($data->late_status == 1){
                   return '<span class="inline badge badge-warning">Late</span> <span class="badge badge-primary">Present</span> <span class="inline badge badge-danger">Halfday</span> ';

                 } else {
                   return '<span class="inline badge badge-primary">Present</span> <span class="inline badge badge-danger">Halfday</span> ';

                 }
               }
               else{
                 return '<span class="inline badge badge-warning">Late</span span class="inline badge badge-primary">Present</span>';
               }
            }
            if($data->status == 'Present' || $data->status == 'Present (Halfday)') {
               if($data->in_time == null){
                 return '<span class="inline badge badge-primary">Present</span> <button class="inline btn btn-sm btn-round btn-danger make-absent" data-asid="'.$data->associate_id.'">Make Absent</button>';
               }elseif ($data->out_time == null && $data->remarks != 'HD') {
                 return '<span class="inline badge badge-primary">Present</span> <button class="inline btn btn-sm btn-round btn-danger make-halfday" data-asid="'.$data->associate_id.'">Make Halfday</button>';
               }elseif ($data->out_time == null && $data->remarks == 'HD') {
                 $time = explode(' ',$data->in_time);
                 if($data->late_status == 1){
                   return '<span class="inline badge badge-warning">Late</span> <span class=" inline badge badge-primary">Present</span> <span class="inline badge badge-danger">Halfday</span>';
                 } else {
                   return '<span class="inline badge badge-primary">Present</span> <span class="inline badge badge-danger">Halfday</span>';
                 }
               }
               else{
                 return '<span class="inline badge badge-primary">Present</span>';
               }
            }
        })
        ->addColumn('in_punch', function ($data) {

            if ($data->in_time !=null)
            {
                $inTime = date('H:i:s', strtotime($data->in_time));
                if($inTime == '00:00:00' || $data->remarks == 'DSI'){
                    return null;
                }
                return $inTime;
            }
        })
        ->addColumn('out_punch', function ($data) {
            if ($data->out_time != null)
            {
                $outTime = date('H:i:s', strtotime($data->out_time));
                if($outTime == '00:00:00'){
                    return null;
                }
                return $outTime;
            }
        })

        ->addColumn('ot', function ($data) {
          if ($data->as_ot == 1){
            return numberToTimeClockFormat($data->ot_hour);
            
          }else{
            return 'Non OT';
          }
        })
        ->rawColumns(['associate_id','ot','hr_designation_name','hr_shift_name', 'in_punch', 'out_punch', 'att_date','att_status','oracle_id'])
        ->make(true);
    }

    public function getAllAtendanceData($request)
    {
    	$attData = array();
    	$getEmployee = DB::table('hr_as_basic_info')->where('as_unit_id', $request['unit']);
        $employeeToSql = $getEmployee->toSql();

    	//if(in_array($request['unit'], [1,4,5,9])){
    		$tableName = get_att_table($request['unit']);
            $attData = DB::table($tableName)
            ->select($tableName.'.*', 'b.as_ot', 'b.as_oracle_code', 'b.as_id', 'b.associate_id', 'b.as_name', 'b.as_designation_id')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->whereBetween('in_date', [$request['report_from'], $request['report_to']]);
        	if($request['type'] == 'All'){
        		$attData->addSelect(DB::raw("'all' as reportType"));
        	}elseif($request['type'] == 'Present'){
        		$attData->addSelect(DB::raw("'present' as reportType"));
        		$attData->whereNotNull('in_time');
        		$attData->whereNotNull('out_time');
        		$attData->where('remarks', '!=', 'DSI');
        	}elseif($request['type'] == 'Present(Intime Empty)'){
                $attData->where(function ($query) {
                   $query->whereNull('in_time')
                         ->orWhere('remarks', 'DSI');
                });
        		$attData->whereNotNull('out_time');
        		$attData->addSelect(DB::raw("'present' as reportType"));
        	}elseif($request['type'] == 'Present(Outtime Empty)'){
        		$attData->whereNotNull('in_time');
        		$attData->whereNull('out_time');
        		$attData->where('remarks', '!=', 'DSI');
        		$attData->addSelect(DB::raw("'present' as reportType"));
        	}elseif($request['type'] == 'Present (Halfday)'){
        		$attData->where('remarks', 'HD');
        		$attData->addSelect(DB::raw("'present' as reportType"));
        	}elseif($request['type'] == 'Present (Late)'){
        		$attData->where('remarks', '!=', 'DSI');
        		$attData->where('late_status', 1);
        		$attData->addSelect(DB::raw("'present' as reportType"));
        	}elseif($request['type'] == 'Present (Late(Outtime Empty))'){
        		$attData->where('remarks', '!=', 'DSI');
        		$attData->where('late_status', 1);
        		$attData->whereNull('out_time');
        		$attData->addSelect(DB::raw("'present' as reportType"));
        	}
    	//}
    	$attData->leftJoin(DB::raw('(' . $employeeToSql. ') AS b'), function($join) use ($getEmployee,$tableName) {
            $join->on($tableName.'.as_id', '=', 'b.as_id')->addBinding($getEmployee->getBindings());
        });
        $attData->where('b.as_status', 1);
        if($request['area'] != null){
            $attData->where('b.as_area_id', $request['area']);
        }
        if($request['department'] != null){
            $attData->where('b.as_department_id', $request['department']);
        }
        if($request['section'] != null){
            $attData->where('b.as_section_id', $request['section']);
        }
        if($request['subSection'] != null){
            $attData->where('b.as_subsection_id', $request['subSection']);
        }
        if($request['floor_id'] != null){
            $attData->where('b.as_floor_id', $request['floor_id']);
        }
        if($request['line_id'] != null){
            $attData->where('b.as_line_id', $request['line_id']);
        }
        
        if($request['otnonot'] != null){
            $attData->where('b.as_ot', $request['otnonot']);
        }
        if($request['ot_hour'] != null){
            if($request['condition'] == 'Less Than'){
                $sign = '<';
            }else if($request['condition'] == 'Greater Than'){
                $sign = '>';
            }else{
                $sign = '=';
            }
            $attData->where($tableName.'.ot_hour', $sign, $request['ot_hour']);
        }
        
    	$getAttData = $attData->get();
    	return $getAttData;
    }
    public function getAbsentData($request){
        $unit = unit_by_id();
        $areaid       = isset($request['area'])?$request['area']:'';
        $otnonot      = isset($request['otnonot'])?$request['otnonot']:'';
        $departmentid = isset($request['department'])?$request['department']:'';
        $lineid       = isset($request['line_id'])?$request['line_id']:'';
        $florid       = isset($request['floor_id'])?$request['floor_id']:'';
        $section      = isset($request['section'])?$request['section']:'';
        $subSection   = isset($request['subSection'])?$request['subSection']:'';

        // employee basic sql binding
        $employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();

        $queryData = DB::table('hr_absent')
            ->where('hr_unit',$request['unit'])
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->whereBetween('date',array($request['report_from'],$request['report_to']))
            ->when(!empty($areaid), function ($query) use($areaid){
               return $query->where('emp.as_area_id',$areaid);
            })
            ->when(!empty($departmentid), function ($query) use($departmentid){
               return $query->where('emp.as_department_id',$departmentid);
            })
            ->when(!empty($lineid), function ($query) use($lineid){
               return $query->where('emp.as_line_id', $lineid);
            })
            ->when(!empty($florid), function ($query) use($florid){
               return $query->where('emp.as_floor_id',$florid);
            })
            ->when($request['otnonot']!=null, function ($query) use($otnonot){
               return $query->where('emp.as_ot',$otnonot);
            })
            ->when(!empty($section), function ($query) use($section){
               return $query->where('emp.as_section_id', $section);
            })
            ->when(!empty($subSection), function ($query) use($subSection){
               return $query->where('emp.as_subsection_id', $subSection);
            });
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','hr_absent.associate_id')->addBinding($employeeData->getBindings());
            });
            $queryData->leftJoin('hr_shift', function($q) {
                $q->on('hr_shift.hr_shift_name', 'emp.as_shift_id')
                   ->on('hr_shift.hr_shift_id', DB::raw("(select max(hr_shift_id) from hr_shift WHERE hr_shift.hr_shift_name = emp.as_shift_id AND hr_shift.hr_shift_unit_id = emp.as_unit_id )"));
            });
            
            $absentData = $queryData->where('emp.as_status', 1)->get();

            $data = [];
              $i = 0;
            foreach ($absentData as $absent) {
                $d = new Employee; // creating a blank object
                $d->associate_id = $absent->associate_id;
                $d->as_unit_id   = $absent->hr_unit;
                $d->as_name     = $absent->as_name;
                $d->as_pic      = $absent->as_pic;
                $d->as_oracle_code      = $absent->as_oracle_code;
                $d->as_gender    = $absent->as_gender;
                $d->as_emp_type_id = $absent->as_emp_type_id;
                $d->in_time     = null;
                $d->out_time     = null;
                $d->ot_hour     = 0;

                $d->hr_shift_code = $absent->hr_shift_code;
                $d->hr_shift_break_time = $absent->hr_shift_break_time;
                $d->hr_shift_start_time = $absent->hr_shift_start_time;
                $d->hr_shift_end_time = $absent->hr_shift_end_time;
                $d->hr_shift_name      = $absent->as_shift_id;
                $d->hr_unit_name      = $unit[$absent->hr_unit]['hr_unit_name'];
                $d->as_designation_id = $absent->as_designation_id;
                $d->as_ot              = $absent->as_ot;
                $d->date              = $absent->date;
                $d->status              = 'Absent';

                $data[$i] = $d; //assigning object into array
                $i++;
            }
            return $data;

    }

    public function activityLock(Request $request)
    {
        $data = 1;
        $input = $request->all();
        // return $input;
        try {
            $lock['month'] = date('m', strtotime($input['date']));
            $lock['year'] = date('Y', strtotime($input['date']));
            $lock['unit_id'] = $input['unit'];
            $data = monthly_activity_close($lock);
            return $data;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $data;
        }
    }

}
