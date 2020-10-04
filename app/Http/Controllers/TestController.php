<?php

namespace App\Http\Controllers;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Leave;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use PDF, Validator, Auth, ACL, DB, DataTables;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {


        $att = DB::table('hr_attendance_mbm')
                ->where('in_date', '>=', '2020-09-01')
                ->where('in_date', '<=', '2020-09-30')
                ->pluck('as_id');

        $data = DB::table('hr_as_basic_info')
                ->whereIN('as_id', $att)
                ->pluck('associate_id');

        $salary = DB::table('hr_monthly_salary as s')
                    ->leftJoin('hr_as_basic_info as b','b.associate_id','s.as_id')
                    ->whereIn('b.as_unit_id', [1,4,5])
                    ->where('month','09')
                    ->where('year','2020')
                    ->whereNotIn('s.as_id', $data)
                    ->get();

        dd($salary);




    	$getData = [];
    
	    $data = [];
	    $absent = DB::table('hr_absent')
	        ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'hr_absent.associate_id')
	        ->whereBetween('hr_absent.date',['2020-09-16','2020-09-30'])
	        ->get();

	    foreach ($absent as $key => $ab) {

	        foreach ($getData as $key1=> $value) {
	            if($ab->as_oracle_code == $value['PID'] && $ab->date == date('Y-m-d', strtotime($value['WD']))){
	                $data[$key]['as_id'] = $ab->as_id;
	                $data[$key]['associate_id'] = $ab->associate_id;
	                $data[$key]['in_date'] = $ab->date;
	                $data[$key]['in_time'] = $value['IN_TIME'];
	                $data[$key]['out_time'] = $value['OUT_TIME'];
	                $data[$key]['as_unit_id'] = $ab->as_unit_id;
	                $data[$key]['shift_roaster_status'] = $ab->shift_roaster_status;

	                break;
	            }
	        }
	        
	    }
	    $status = [];

	    foreach ($data as $key => $att) {
	        
	        $status[$key] = $this->bulkManualStore($att);
	    }

	    dd($data,$status);
    }


    public function bulkManualStore($request)
    {
        // dd($request->all());
        $unit=$request['as_unit_id'];
        $info = Employee::where('as_id',$request['as_id'])->first();
        $tableName= get_att_table($unit);
        $date = $request['in_date'];
        $month = '09';
        $year = '2020';

        if(strlen($request['in_time']) > 2){
        	$intime = date('H:i:s', strtotime($request['in_time']));
        }else{
        	$intime = date('H:i:s', strtotime($request['in_time'].'.0'));
        }

        if(strlen($request['out_time']) > 2){
        	$outtime = date('H:i:s', strtotime($request['out_time']));
        }else{
        	$outtime = date('H:i:s', strtotime($request['out_time'].'.0'));
        }


        

        $day_of_date = date('j', strtotime($date));
    	$day_num = "day_".$day_of_date;
		$shift= DB::table("hr_shift_roaster")
		->where('shift_roaster_month', $month)
		->where('shift_roaster_year', $year)
		->where("shift_roaster_user_id", $info->as_id)
		->select([
			$day_num,
            'hr_shift.hr_shift_id',
			'hr_shift.hr_shift_start_time',
			'hr_shift.hr_shift_end_time',
            'hr_shift.hr_shift_code',
            'hr_shift.hr_shift_break_time',
            'hr_shift.hr_shift_night_flag'
		])
        ->leftJoin('hr_shift', function($q) use($day_num, $unit) {
            $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
            $q->where('hr_shift.hr_shift_unit_id', $unit);
        })
        ->orderBy('hr_shift.hr_shift_id', 'desc')
		->first();
        
		if(!empty($shift) && $shift->$day_num != null){
			$shift_start= $shift->hr_shift_start_time;
			$shift_end= $shift->hr_shift_end_time;
			$break= $shift->hr_shift_break_time;
			$nightFlag= $shift->hr_shift_night_flag;
			$shiftCode= $shift->hr_shift_code;
			$new_shift_id = $shift->hr_shift_id;
		}else{
			$shift_start= $info->shift['hr_shift_start_time'];
			$shift_end= $info->shift['hr_shift_end_time'];
			$break= $info->shift['hr_shift_break_time'];
			$nightFlag= $info->shift['hr_shift_night_flag'];
			$shiftCode= $info->shift['hr_shift_code'];
			$new_shift_id= $info->shift['hr_shift_id'];
		}

        DB::beginTransaction();
        try {
                    $checkDay = EmployeeHelper::employeeDateWiseStatus($date, $info->associate_id, $info->as_unit_id, $info->shift_roaster_status);
                    if($checkDay == 'open' || $checkDay == 'OT'){
                        $insert = [];
                        $insert['remarks'] = 'BM';
                        $insert['as_id'] = $info->as_id;
                        $insert['hr_shift_code'] = $shiftCode;

                        
                        if (strpos($intime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$intime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $intime = null;
                            }
                        }

                        
                        if (strpos($outtime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$outtime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $outtime = null;
                            }
                        }

                        

                        if($intime == null && $outtime == null){
                            $absentData = [
                                'associate_id' => $info->associate_id,
                                'date' => $date,
                                'hr_unit' => $info->as_unit_id
                            ];
                            $getAbsent = Absent::where($absentData)->first();
                            if($getAbsent == null && $checkDay == 'open'){
                                Absent::insert($absentData);
                            }
                        }else{
                            if($intime == '00:00:00' || $intime == null){
                                $empIntime = $shift_start;
                                $insert['remarks'] = 'DSI';
                            }else{
                                $empIntime = $intime;
                            }
                            $attInsert = 0;
                            $insert['in_time'] = $date.' '.$empIntime;
                            if($outtime == '00:00:00' || $outtime == null){
                                $insert['out_time'] = null;
                            }else{
                                $insert['out_time'] = $date.' '.$outtime;
                            }
                            if($checkDay == 'OT'){
                                $insert['late_status'] = 0;
                            }else if($intime != null){
                                $insert['in_unit'] = $unit;
                                $insert['late_status'] = $this->getLateStatus($unit, $new_shift_id,$date,$intime,$shift_start);
                            }else{
                                $insert['late_status'] = 1;
                            }
                            if($outtime != null){
                                $insert['out_unit'] = $unit;
                                $insert['out_time'] = $date.' '.$outtime;
                                if($intime != null) {
                                    // out time is tomorrow
                                    if(strtotime($intime) > strtotime($outtime)) {
                                        $dateModify = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                        $insert['out_time'] = $dateModify.' '.$outtime;
                                    }
                                }
                            }

                            //check OT hour if out time exist
                            if($intime != null && $outtime != null && $info->as_ot != 0 && $insert['remarks'] != 'DSI'){
                                $overtimes = EmployeeHelper::daliyOTCalculation($insert['in_time'], $insert['out_time'], $shift_start, $shift_end, $break, $nightFlag, $info->associate_id, $info->shift_roaster_status, $unit);
                                $insert['ot_hour'] = $overtimes;
                            }else{
                                $insert['ot_hour'] = 0;
                            }
                            $insert['in_date'] = date('Y-m-d', strtotime($insert['in_time']));
                            DB::table($tableName)->insert($insert);
                            
                            //
                            $absentWhere = [
                                'associate_id' => $info->associate_id,
                                'date' => $date,
                                'hr_unit' => $info->as_unit_id
                            ];
                            Absent::where($absentWhere)->delete();
                            
                        }
                    }else{
                        $absentWhere = [
                            'associate_id' => $info->associate_id,
                            'date' => $date,
                            'hr_unit' => $info->as_unit_id
                        ];
                        Absent::where($absentWhere)->delete();
                    }

              

            //dd($year);exit;
            $yearMonth = $year.'-'.$month;
            if($month == date('m')){
                $totalDay = date('d');
            }else{
                $totalDay = Carbon::parse($yearMonth)->daysInMonth;
            }

            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
             
        }
    }



    public function getLateStatus($unit,$shift_id,$date,$intime,$shift_start)
    {
        $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($unit, $shift_id);
        if($getLateCount != null){
            if(date('Y-m-d', strtotime($date))>= $getLateCount->date_from && date('Y-m-d', strtotime($date)) <= $getLateCount->date_to){
                $lateTime = $getLateCount->value;
            }else{
                $lateTime = $getLateCount->default_value;
            }
        }else{
            $lateTime = 180;
        }
        $shiftinTime = (strtotime(date("H:i:s", strtotime($shift_start)))+$lateTime);
        if(strtotime(date('H:i:s', strtotime($intime))) > $shiftinTime){
            $late = 1;
        }else{
            $late = 0;
        }
        return $late;
    }

    



    

}
