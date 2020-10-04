<?php

namespace App\Http\Controllers\Hr\TimeAttendance;
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
use Illuminate\Http\Request;
use PDF, Validator, Auth, ACL, DB, DataTables;

class AttendaceBulkManualController extends Controller
{
    public function bulkManual(Request $request)
    {
        try {
            $attendance = array();
            $info = array();
            $joinExist = array();
            $leftExist = array();
            if($request->month <= date('Y-m')){

                $result = $this->empAttendanceByMonth($request);
                
                $attendance = $result['attendance'];
                $info = $result['info'];
                $joinExist = $result['joinExist'];
                $leftExist = $result['leftExist'];
            }else{
                $info = 'No result found yet!';
            }
            return view("hr/timeattendance/attendance_bulk_manual",compact('attendance','info', 'joinExist', 'leftExist'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getLateStatus($unit,$shift_id,$date,$intime,$shift_start)
    {
        //return $unit. ' '.$shift_id;
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

    public function calculateOt($in,$out,$shift_start,$shift_end,$break, $otDay = 0, $nightFlag = 0)
    {
        // return [$in,$out,$shift_start,$shift_end,$break, $otDay];
        $cIn = strtotime(date("H:i", strtotime($in)));
        $cOut = strtotime(date("H:i", strtotime($out)));
        // -----
        $cShifStart = strtotime(date("H:i", strtotime($shift_start)));
        $cShifEnd = strtotime(date("H:i", strtotime($shift_end)));
        $cBreak = $break*60;

        // if ($cOut < ($cShifEnd+$cBreak)){
        //    $cOut = null;
        // }
        $overtimes = 0;
        if(!empty($cOut)) {
            // if Day shift employee are OT hour more then 8+ hour
            $remain_minutes = 0;
            if(($cIn > $cOut) && (strpos($out, ':') !== false) && $nightFlag == 0){
                list($timesplit1,$timesplit2,$timesplit3) = array_pad(explode(':',$out),3,0);
                $remain_minutes = ((int)$timesplit1*60)+((int)$timesplit2)+((int)$timesplit3>30?1:0);
                $cOut = strtotime('24:00');
            }
            if($otDay == 0){
                $total_minutes = ($cOut - ($cShifEnd+$cBreak))/60;
            }else{
                $total_minutes = ($cOut - ($cShifStart+$cBreak))/60;
            }
            if($nightFlag == 1 && $otDay != 0){
                $total_minutes = abs($total_minutes);
            }
            $total_minutes = $remain_minutes+$total_minutes;
            $minutes = ($total_minutes%60);
            $ot_minute = $total_minutes-$minutes;
            //round minutes
            // if($minutes >= 15 && $minutes < 45) $minutes = 30;
            // else if($minutes >= 45) $minutes = 60;
            // else $minutes = 0;
            $minutes = $this->otbuffer($minutes);
            if($ot_minute >= 0)
            $overtimes += ($ot_minute+$minutes);
        }
        $h = floor($overtimes/60) ? ((floor($overtimes/60)<10)?("0".floor($overtimes/60)):floor($overtimes/60)) : '00';
        $m = $overtimes%60 ? (($overtimes%60<10)? ("0".$overtimes%60):($overtimes%60)) : '00';
        return ($h.'.'.($m =='30'?'50':'00'));
    }

    public function getTableName($unit)
    {
        if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
            $tableName="hr_attendance_mbm";
        }else if($unit ==2){
            $tableName="hr_attendance_ceil";
        }else if($unit ==3){
            $tableName="hr_attendance_aql";
        }else if($unit ==6){
            $tableName="hr_attendance_ho";
        }else if($unit ==8){
            $tableName="hr_attendance_cew";
        }else{
            $tableName="hr_attendance_mbm";
        }
        return $tableName;
    }

    public function bulkManualStore(Request $request)
    {
       
        $unit=$request->unit_att;
        $info = Employee::where('as_id',$request->ass_id)->first();
        $tableName= $this->getTableName($unit);

        DB::beginTransaction();
        try {
            //new attendance entry
            if(isset($request->new_date)){
                foreach ($request->new_date as $key => $date) {
                    $checkDay = EmployeeHelper::employeeDateWiseStatus($date, $info->associate_id, $info->as_unit_id, $info->shift_roaster_status);
                    if($checkDay == 'open' || $checkDay == 'OT'){
                        $insert = [];
                        $insert['remarks'] = 'BM';
                        $insert['as_id'] = $request->ass_id;
                        $insert['hr_shift_code'] = $request->new_shift_code[$key];
                        $insert['updated_by'] = auth()->user()->associate_id;

                        $intime = $request->new_intime[$key];
                        if (strpos($intime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$intime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $intime = null;
                            }
                        }

                        $outtime = $request->new_outtime[$key];
                        if (strpos($outtime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$outtime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $outtime = null;
                            }
                        }

                        $shift_start = $request->new_shift_start[$key];
                        $shift_end = $request->new_shift_end[$key];
                        $break = $request->new_shift_break[$key];
                        $nightFlag = $request->new_shift_night[$key];

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
                            if($request->new_intime[$key] == '00:00:00' || $request->new_intime[$key] == null){
                                $empIntime = $shift_start;
                                $insert['remarks'] = 'DSI';
                            }else{
                                $empIntime = $intime;
                            }
                            $attInsert = 0;
                            $insert['in_time'] = $date.' '.$empIntime;
                            if($request->new_outtime[$key] == '00:00:00' || $request->new_outtime[$key] == null){
                                $insert['out_time'] = null;
                            }else{
                                $insert['out_time'] = $date.' '.$outtime;
                            }
                            if($checkDay == 'OT'){
                                $insert['late_status'] = 0;
                            }else if($intime != null){
                                $insert['in_unit'] = $unit;
                                $insert['late_status'] = $this->getLateStatus($unit, $request->new_shift_id[$key],$date,$intime,$shift_start);
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
                                /*$h = floor($overtimes/60) ? ((floor($overtimes/60)<10)?("0".floor($overtimes/60)):floor($overtimes/60)) : '00';
                                $m = $overtimes%60 ? (($overtimes%60<10)? ("0".$overtimes%60):($overtimes%60)) : '00';
                                $insert['ot_hour'] = ($h.'.'.($m =='30'?'50':'00'));*/
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

                }
            }

            //update
            
            if(isset($request->old_date)){
                foreach ($request->old_date as $key => $date) {
                    $checkDay = EmployeeHelper::employeeDateWiseStatus($date, $info->associate_id, $info->as_unit_id, $info->shift_roaster_status);
                    
                    if($checkDay == 'open' || $checkDay == 'OT'){
                        $Att = DB::table($tableName)
                        ->where('id', $key)
                        ->where('as_id',$request->ass_id)
                        ->first();

                        $event['unit'] = $unit;
                        $event['associate_id'] = $info->associate_id;
                        $event['date'] = $date;
                        $event['in_punch_new'] = $request->intime[$key];
                        $event['out_punch_new'] = $request->outtime[$key];
                        $event['ot_new'] = '';
                        $event['type'] = '';
                        $event['remarks'] = 'BM';

                        if($info->as_ot == 0){
                            $event['ot_new'] = 'Non OT';
                        }

                        $update['hr_shift_code'] = $request->this_shift_code[$key];
                        $update['updated_by'] = auth()->user()->associate_id;

                        $intime = $request->intime[$key];
                        
                        if (strpos($intime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$intime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $intime = null;
                            }
                        }
                        
                        $outtime = $request->outtime[$key];
                        if (strpos($outtime, ':') !== false) {
                            list($one,$two,$three) = array_pad(explode(':',$outtime),3,0);
                            if((int)$one+(int)$two+(int)$three == 0) {
                                $outtime = null;
                            }
                        }
                        $shift_start = $request->this_shift_start[$key];
                        $shift_end = $request->this_shift_end[$key];
                        $break = $request->this_shift_break[$key];
                        $nightFlag = $request->this_shift_night[$key];

                        if($intime == null && $outtime == null){
                            if($request->old_status[$key] == 'P' && $Att != null) {
                                $eventPrevious = $Att;
                                // remove present and insert absent
                                DB::table($tableName)
                                ->where('id', $key)
                                ->where('as_id',$request->ass_id)
                                ->delete();

                                // insert absent
                                $absentData = [
                                    'associate_id' => $info->associate_id,
                                    'date' => $date,
                                    'hr_unit' => $info->as_unit_id
                                ];
                                $getAbsent = Absent::where($absentData)->first();
                                if($getAbsent == null && $checkDay == 'open'){
                                    Absent::insert($absentData);
                                }

                                // add event history
                                $this->eventModified($info->associate_id,3,$eventPrevious,$absentData);
                                // present to absent
                            }else{
                                // insert absent
                                $absentData = [
                                    'associate_id' => $info->associate_id,
                                    'date' => $date,
                                    'hr_unit' => $info->as_unit_id
                                ];
                                $getAbsent = Absent::where($absentData)->first();
                                if($getAbsent == null && $checkDay == 'open'){
                                    Absent::insert($absentData);
                                }
                            }
                        }else{
                            $attInsert = 0;
                            if($request->intime[$key] == '00:00:00' || $request->intime[$key] == null){
                                $empIntime = $shift_start;
                                $update['remarks'] = 'BM';
                            }else{
                                $empIntime = $intime;
                                $update['remarks'] = 'BM';
                            }
                            $update['in_time'] = date('Y-m-d H:i:s', strtotime($date.' '.$empIntime));
                            $update['out_time'] = date('Y-m-d H:i:s', strtotime($date.' '.$outtime));
                            if($intime != null){
                                

                                $update['in_unit'] = $unit;
                                // if($outtime != null && $intime == null && $Att->remarks == 'DSI') {
                                //     // in time is yesterday
                                //     if(strtotime($intime) > strtotime($outtime)) {
                                //         $inDate = date('Y-m-d',strtotime($Att->in_time));
                                //         $outDate = date('Y-m-d',strtotime($Att->out_time));
                                //         // if in date and out date are Equuleus then in date are yesterday
                                //         if($inDate == $outDate) {
                                //             $date = date("Y-m-d", strtotime("-1 day", strtotime($date)));
                                //             $update['in_time'] = $date.' '.$intime;
                                //             // check in_time date already exist
                                //             $existAtt = DB::table($tableName)
                                //                 ->where('as_id',$request->ass_id)
                                //                 ->whereDate('in_time',$date)
                                //                 ->first();
                                //             if($existAtt) {
                                //                 return back()->with('error', $date.' in time already exist.');
                                //             }
                                //         }
                                //     }
                                // }
                                if($checkDay == 'OT'){
                                    $update['late_status'] = 0;
                                }else{

                                    $update['late_status'] = $this->getLateStatus($unit, $request->this_shift_id[$key],$date,$intime,$shift_start);
                                    
                                }
                            }else{
                                $update['late_status'] = 1;
                            }
                            if($outtime != null){
                                $update['out_unit'] = $unit;
                                $update['out_time'] = date('Y-m-d H:i:s', strtotime($date.' '.$outtime));
                                if($intime != null && $Att->remarks != 'DSI') {
                                    // out time is tomorrow
                                    if(strtotime($outtime) < strtotime($intime)) {
                                        $dateOModify = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                                        $update['out_time'] = date('Y-m-d H:i:s', strtotime($dateOModify.' '.$outtime));
                                    }
                                }
                                // set previous out date
                                if($Att->remarks == 'DSI') {
                                    $dateDSI = date("Y-m-d", strtotime($Att->out_time));
                                    $update['out_time'] = date('Y-m-d H:i:s', strtotime($dateDSI.' '.$outtime));
                                }
                            }
                            //check OT hour if out time exist
                            

                            /********************************************************
                             | Previously set outime with DSI. Thats why couldnt updated
                             |
                             *******************************************************/



                            if($intime != null && $outtime != null && $info->as_ot == 1){
                                $overtimes = EmployeeHelper::daliyOTCalculation($update['in_time'],$update['out_time'], $shift_start, $shift_end, $break, $nightFlag, $info->associate_id, $info->shift_roaster_status, $unit);
                                // dd($overtimes);
                                /*$h = floor($overtimes/60) ? ((floor($overtimes/60)<10)?("0".floor($overtimes/60)):floor($overtimes/60)) : '00';
                                $m = $overtimes%60 ? (($overtimes%60<10)? ("0".$overtimes%60):($overtimes%60)) : '00';
                                $update['ot_hour'] = ($h.'.'.($m =='30'?'50':'00'));*/
                                $update['ot_hour'] = $overtimes;
                            }else {
                                $update['ot_hour'] = 0;
                            }
                            
                            $event['ot_new'] = $update['ot_hour'];

                            if($request->old_status[$key] == 'A' && $Att == null) {
                                // insert present and remove absent
                                $update['as_id'] = $request->ass_id;
                                $update['in_date'] = date('Y-m-d', strtotime($update['in_time']));
                                DB::table($tableName)->insert($update);
                                
                                // remove absent
                                $absentWhere = [
                                    'as_id' => $info->as_id,
                                    'date' => $date,
                                    'hr_unit' => $info->as_unit_id
                                ];
                                Absent::where($absentWhere)->delete();
                                // add event history
                                $this->eventModified($info->associate_id,2,$absentWhere,$event); // absent to present
                            } else {
                                if(strtotime($update['in_time']) != strtotime($Att->in_time) || strtotime($update['out_time']) != strtotime($Att->out_time)) {
                                    $this->eventModified($info->associate_id,1,$Att,$event); // in/out modified
                                }
                                
                                DB::table($tableName)
                                ->where('id', $key)
                                ->where('as_id',$request->ass_id)
                                ->update($update);
                                $absentWhere = [
                                    'associate_id' => $info->associate_id,
                                    'date' => $date,
                                    'hr_unit' => $info->as_unit_id
                                ];
                                Absent::where($absentWhere)->delete();
                            }
                        }
                    }
                    else{
                        $absentWhere = [
                            'associate_id' => $info->associate_id,
                            'date' => $date,
                            'hr_unit' => $info->as_unit_id
                        ];
                        Absent::where($absentWhere)->delete();
                        // attendance delete
                        DB::table($tableName)
                        ->where('id', $key)
                        ->where('as_id',$request->ass_id)
                        ->delete();
                    }
                }
            }
            
            // sent to queue for salary calculation
            $year = date('Y', strtotime($request->month));
            $month = date('m', strtotime($request->month));
            //dd($year);exit;
            $yearMonth = $year.'-'.$month;
            if($month == date('m')){
                $totalDay = date('d');
            }else{
                $totalDay = Carbon::parse($yearMonth)->daysInMonth;
            }
            
            $queue = (new ProcessUnitWiseSalary($tableName, $month, $year, $request->ass_id, $totalDay))
            ->onQueue('salarygenerate')
            ->delay(Carbon::now()->addSeconds(2));
            dispatch($queue);
            DB::commit();
            return back()->with('success', " Updated Successfully!!");
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return $bug;
            return redirect()->back()->with('error',$bug);
        }
    }

    public function eventModified($user_asso, $type, $previous_event, $modified_event)
    {
        $eventHistory = [
            'user_id' => $user_asso,
            'event_date' => date('Y-m-d'),
            'type' => $type,
            'previous_event' => json_encode($previous_event),
            'modified_event' => json_encode($modified_event),
            'created_by' => Auth::user()->associate_id
        ];
        DB::table('event_history')->insert($eventHistory);
        return true;
    }

    public function empAttendanceByMonth($request)
    {
        
        $total_attend   = 0;
        $total_overtime = 0;
        $associate = $request->associate;
        $tempdate= "01-".$request->month;
        $explode = explode('-',$request->month);
        $month = $explode[1];
        $year  = $explode[0];
        #------------------------------------------------------
        // ASSOCIATE INFORMATION
        $fetchUser = Employee::where("associate_id", $associate);
        //check user exists
        if($fetchUser->exists()) {
          $info = $fetchUser->first();
          $getUnit = unit_by_id();
          $getLine = line_by_id();
          $getFloor = floor_by_id();
          $getDesignation = designation_by_id();
          $getSection = section_by_id();
          $subSection = subSection_by_id();
          $info->unit = $getUnit[$info->as_unit_id]['hr_unit_name'];
          $info->section = $getSection[$info->as_section_id]['hr_section_name']??'';
          $info->designation = $getDesignation[$info->as_designation_id]['hr_designation_name']??'';

          $date       = ($year."-".$month."-"."01");
          $startDay   = date('Y-m-d', strtotime($date));
          $endDay     = date('Y-m-t', strtotime($date));
          $toDay      = date('Y-m-d');
          //If end date is after current date then end day will be today
          if($endDay>$toDay) {
            $endDay= $toDay;
          }
          $tableName= get_att_table($info->as_unit_id).' AS a';
          $associate= $info->associate_id;

          $totalDays  = (date('d', strtotime($endDay))-date('d', strtotime($startDay)));
          $total_attends  = 0; $absent = 0; $x=1; $total_ot = 0;
          $attendance=[];
          // join exist this month
          $iEx = 0;
          $joinExist = false;
          if($info->as_doj != null) {
            list($yearE,$monthE,$dateE) = explode('-',$info->as_doj);
            if($year == $yearE && $month == $monthE) {
              $iEx = $dateE-1;
              $joinExist = true;
              $x = $dateE;
            }
          }
          // left,terminate,resign, suspend, delete exist
          $leftExist = false;
          if($info->as_status_date != null) {
            if(in_array($info->as_status,[0,2,3,4,5])!==false) {
              list($yearL,$monthL,$dateL) = explode('-',$info->as_status_date);
              if($year == $yearL && $month == $monthL) {
                if($joinExist == false) {
                  $iEx = 1;
                } else {
                  $iEx = $iEx+1;
                }
                $leftExist = true;
                $totalDays = $dateL;
              }
            }
          }

          $floor = $getFloor[$info->as_floor_id]['hr_floor_name']??'';
          $line = $getLine[$info->as_line_id]['hr_line_name']??'';
          // holiday roster
          $getHolidayRoster = DB::table('holiday_roaster')
          ->where('as_id',$associate)
          ->where('date', 'LIKE', $request->month_year.'%')
          ->get()
          ->keyBy('date')->toArray();

          // get attendance
          $getAttendance = DB::table($tableName)
                        ->where('a.as_id', $info->as_id)
                        ->where('a.in_date', 'LIKE', $request->month_year.'%')
                        ->get()
                        ->keyBy('in_date')->toArray();
          // yearly holiday roster planner
          $getHoliday = DB::table("hr_yearly_holiday_planner")
                      ->where('hr_yhp_status', 1)
                      ->where('hr_yhp_unit', $info->as_unit_id)
                      ->get()
                      ->keyBy('hr_yhp_dates_of_holidays')->toArray();

          for($i=$iEx; $i<=$totalDays; $i++) {
            $date       = ($year."-".$month."-".$x);
            $thisDay   = date('Y-m-d', strtotime($date));

            //shift in time
            $shift_day= "day_".(int)$x;
            $x++;
            //get shift code from shift roaster

            $shift_code = DB::table('hr_shift_roaster')
                              ->where('shift_roaster_associate_id',$associate)
                              ->where('shift_roaster_year', $year)
                              ->where('shift_roaster_month', $month)
                              ->pluck($shift_day)
                              ->first();
            if($shift_code){
              $shift = Shift::getCheckUniqueUnitIdShiftName($info->as_unit_id,$shift_code);
              $attendance[$i]['shift_id'] = $shift->hr_shift_id;
              $attendance[$i]['shift_code'] = $shift->hr_shift_code;
              $attendance[$i]['shift_start'] = $shift->hr_shift_start_time;
              $attendance[$i]['shift_end'] = $shift->hr_shift_end_time;
              $attendance[$i]['shift_break'] = $shift->hr_shift_break_time;
              $attendance[$i]['shift_night'] = $shift->hr_shift_night_flag;
            } else {
              $attendance[$i]['shift_id'] = $info->shift['hr_shift_id'];
              $attendance[$i]['shift_code'] = $info->shift['hr_shift_code'];
              $attendance[$i]['shift_start'] = $info->shift['hr_shift_start_time'];
              $attendance[$i]['shift_end'] = $info->shift['hr_shift_end_time'];
              $attendance[$i]['shift_break'] = $info->shift['hr_shift_break_time'];
              $attendance[$i]['shift_night'] = $info->shift['hr_shift_night_flag'];
            }
            $lineFloorInfo = DB::table('hr_station')
                             ->where('associate_id',$associate)
                             ->whereDate('start_date','<=',$thisDay)
                             ->whereDate('end_date','>=',$thisDay)
                             ->leftJoin('hr_floor','hr_station.changed_floor','hr_floor.hr_floor_id')
                             ->leftJoin('hr_line','hr_station.changed_line','hr_line.hr_line_id')
                             ->first();

            //Default Values
            $attendance[$i]['in_time']      = null;
            $attendance[$i]['att_id']       = null;
            $attendance[$i]['out_time']     = null;
            $attendance[$i]['late_status']  = null;
            $attendance[$i]['remarks']      = null;
            $attendance[$i]['date']         = $thisDay;
            $attendance[$i]['floor']        = !empty($lineFloorInfo->hr_floor_name)?$lineFloorInfo->hr_floor_name:$floor;
            $attendance[$i]['line']         = !empty($lineFloorInfo->hr_line_name)?$lineFloorInfo->hr_line_name:$line;
            $attendance[$i]['outside']      = null;
            $attendance[$i]['outside_msg']  = null;
            $attendance[$i]['overtime_time']    = null;
            $attendance[$i]['present_status']   ="A";
            $attendance[$i]['attPlusOT'] = null;

            //check leave first
            $leaveCheck = Leave::where('leave_ass_id', $associate)
                            ->where(function ($q) use($thisDay) {
                              $q->where('leave_from', '<=', $thisDay);
                              $q->where('leave_to', '>=', $thisDay);
                            })
                            ->first();
            if($leaveCheck){
              $attendance[$i]['present_status']=$leaveCheck->leave_type." Leave";
            } else {
                // check attendance
          
                $attendCheck = $getAttendance[$thisDay]??'';

                // check holiday
                $holidayRoaster = $getHolidayRoster[$thisDay]??'';

                if($holidayRoaster == ''){
                  // if shift assign then check yearly hoiliday
                  if((int)$info->shift_roaster_status == 0) {
                    $holidayCheck = $getHoliday[$thisDay]??'';
                    if($holidayCheck != ''){
                      if($holidayCheck->hr_yhp_open_status == 1) {
                        $attendance[$i]['present_status'] = "Weekend(General)";
                      }
                      else if($holidayCheck->hr_yhp_open_status == 2){
                        $attendance[$i]['present_status'] = "Weekend(OT)";
                        $attendance[$i]['attPlusOT'] = 'OT - '.$holidayCheck->hr_yhp_comments;
                      }
                      else if($holidayCheck->hr_yhp_open_status == 0){
                        $attendance[$i]['present_status'] = $holidayCheck->hr_yhp_comments;
                      }
                    }
                  }
                } else {
                  if($holidayRoaster->remarks == 'Holiday') {
                    $attendance[$i]['present_status'] = "Day Off";
                    if($holidayRoaster->comment != null) {
                      $attendance[$i]['present_status'] .= ' - '.$holidayRoaster->comment;
                    }
                  }
                  if($holidayRoaster->remarks == 'OT') {
                    $attendance[$i]['present_status'] = "OT";
                    $attendance[$i]['attPlusOT'] = 'OT - '.$holidayRoaster->comment;
                  }
                }

                if($attendCheck != ''){
                  $attendance[$i]['att_id'] = $attendCheck->id;
                  $intime = (!empty($attendCheck->in_time))?date("H:i:s", strtotime($attendCheck->in_time)):null;
                  $outtime = (!empty($attendCheck->out_time))?date("H:i:s", strtotime($attendCheck->out_time)):null;
                  if($attendCheck->remarks == 'DSI'){
                    $attendance[$i]['in_time'] = null;
                  }else{
                    $attendance[$i]['in_time'] = $intime;
                  }
                  $attendance[$i]['out_time'] = $outtime;
                  $attendance[$i]['overtime_time'] = (($info->as_ot==1)? $attendCheck->ot_hour:"");
                  $attendance[$i]['late_status']= $attendCheck->late_status;
                  $attendance[$i]['remarks']= $attendCheck->remarks;
                  $attendance[$i]['present_status']="P";
                  if($info->as_ot==1){
                    $total_ot += (float) $attendCheck->ot_hour;
                  }
                  $total_attends++;
                }
            }

            $outsideCheck= DB::table('hr_outside')
                          ->where('start_date','<=',$thisDay)
                          ->where('end_date','>=',$thisDay)
                          ->where('status',1)
                          ->where('as_id',$associate)
                          ->first();
            if($outsideCheck){
              $attendance[$i]['outside'] = 'Outside';
              if($outsideCheck->type==1){
                $attendance[$i]['outside_msg'] = 'Full Day';
              }else if($outsideCheck->type==2){
                $attendance[$i]['outside_msg'] = 'First Half' ;
              }else if($outsideCheck->type==3){
                $attendance[$i]['outside_msg'] = 'Second Half' ;
              }
            }
            if($attendance[$i]['present_status'] == 'A'){
              $absent++;
            }
          }
          //end of loop
          $info->present    = $total_attends;
          $info->absent     = $absent;
          $info->ot_hour    = $total_ot;
          $result ['info']  = $info;
          $result ['attendance']    = $attendance;
          $result ['joinExist']     = $joinExist;
          $result ['leftExist']     = $leftExist;
          //dd($result);exit;
          return $result;
        }
    }

}
