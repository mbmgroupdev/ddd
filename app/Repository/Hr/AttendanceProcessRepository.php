<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\AttendanceProcessInterface;
use App\Models\Hr\Leave;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Support\Collection;

class AttendanceProcessRepository implements AttendanceProcessInterface
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function attDataChecking($input)
    {
        $getData = $this->unitWiseDataProcess($input);
        $data = collect($input['getData'])->map(function($q) {
            $p = (object)[];

        });
    }
    
    protected function unitWiseDataProcess($input)
    {
        $unit = $input['unit'];
        return collect($input['getData'])->map(function($q) use ($unit){
            $lineData = $q;
            if(!empty($lineData) && (strlen($lineData)>1)){
                return;
            }

            if(($unit==1 || $unit==4 || $unit==5)){
                $sl = substr($lineData, 0, 2);
                $date   = substr($lineData, 3, 8);
                $time   = substr($lineData, 12, 6);
                $rfid = substr($lineData, 19, 10);
                $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
            }else if($unit==2){
                $sl = substr($lineData, 0, 2);
                $date   = substr($lineData, 2, 8);
                $rfid = substr($lineData, 16, 10);
                $time   = substr($lineData, 10, 6);
                $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
            }else if($unit==8  &&  !empty($lineData) && (strlen($lineData)>1)){
                $lineData = explode(" ", $lineData);
                if(isset($lineData[2])){
                    $rfid = $lineData[0];
                    $date = $lineData[1];
                    $time = $lineData[2];
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }else{
                    $msg[] = " Punch Problem!";
                    return;
                }
                
            }
            else if($unit==3  &&  !empty($lineData) && (strlen($lineData)>1)){
                if($input['device'] == 1){
                    $sl = substr($lineData, 0, 2);
                    $date   = substr($lineData, 2, 8);
                    $rfid = substr($lineData, 16, 10);
                    $time   = substr($lineData, 10, 6);
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }elseif($input['device'] == 2){
                    $rfid = '0'; // only unit 3 device automation
                    $lineData = preg_split("/[\t]/", $lineData);
                    $asId = $lineData[0];
                    $checktime = explode(" ", $lineData[1]);
                    $date = $checktime[0];
                    $time = $checktime[1];
                    $checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
                }else{
                    $msg[] = $value." - AQL device mismatch.";
                    return false;
                }
            }
            else if($unit==1001){
                $lineData = preg_replace('/\s+/', ' ', $lineData);
                $valueExloade = explode(',', $lineData);
                $dateExp = explode('/', $valueExloade[1]);
                if(count($dateExp) > 1 && count($valueExloade) > 2){
                    $dateTimeFormat = $dateExp[2].'-'.$dateExp[1].'-'.$dateExp[0].' '.$valueExloade[2];
                    $date  =  date("Y-m-d H:i:s", strtotime(str_replace("/", "-", $dateTimeFormat)));
                    $rfidNameExloade = explode('-', $valueExloade[4]);
                    $rfid = $rfidNameExloade[0];
                    $checktime = (!empty($date)?date("Y-m-d H:i:s", strtotime($date)):null);
                }
                
            }else{
                if($value != null){
                    $msg[] = $value." - Unit do not match, issue data ";
                    return false;
                }
            }
        });
    }

    public function makeEmployeeAttendanceCount($value='')
    {
        $leaveData = $this->getEmployeeLeaveData($value);
        $leaveDate = array_keys($leaveData);
        $holidayDate = $this->getEmployeeHolidayDate($value);
        $holidayDate = array_diff($holidayDate, $leaveDate);
        $date = array_merge($leaveDate, $holidayDate);

        $this->attendanceRemovalOnOffDay($value, $date);

        $data['holidayCount'] = count($holidayDate);
        $data['leaveCount'] = count($leaveDate);
        $punchCategory = $this->getEmployeePunchCategoryCount($value);
        $data['presentCount'] = $punchCategory['presentCount'];
        $data['lateCount'] = $punchCategory['lateCount'];
        $data['halfCount'] = $punchCategory['halfCount'];
        $data['otCount'] = $punchCategory['otCount'];

        $data['absentCount'] = $value['totalDay'] - ($data['presentCount'] + $data['holidayCount'] + $data['leaveCount']);
        if($data['absentCount'] < 0){
            $data['absentCount'] = 0;
        }
        return $data;
    }

    public function getEmployeeHolidayDate($value='')
    {
        $getRosterPlanner = $this->getEmployeeRosterPlannerDate($value, [0,2]);
        return array_merge($getRosterPlanner['holiday'], $getRosterPlanner['ot']);
    }

    public function getEmployeeRosterPlannerDate($value, $parameter)
    {
        $rosterDate = $this->getEmployeeRosterDate($value);
        $plannerDate = ($value['shift_roaster_status']==0)?$this->getEmployeePlannerDate($value):[];

        // Planner List
        $plannerHoliday = isset($plannerDate[0])?$plannerDate[0]->toArray():[];
        $plannerGeneral = isset($plannerDate[1])?$plannerDate[1]->toArray():[];
        $plannerOT = isset($plannerDate[2])?$plannerDate[2]->toArray():[];

        // Roster List
        $rosterHoliday = isset($rosterDate['Holiday'])?$rosterDate['Holiday']->toArray():[];
        $rosterHoliday = array_diff($rosterHoliday, $plannerHoliday);

        $rosterGeneral = isset($rosterDate['General'])?$rosterDate['General']->toArray():[];
        $rosterGeneral = array_diff($rosterGeneral, $plannerGeneral);

        $rosterOT = isset($rosterDate['OT'])?$rosterDate['OT']->toArray():[];
        $rosterOT = array_diff($rosterOT, $plannerOT);

        // get Holiday
        $result['holiday'] = []; 
        if(in_array(0, $parameter)){
            $plannerHoliday = array_diff($plannerHoliday, $rosterGeneral);
            $plannerHoliday = array_diff($plannerHoliday, $rosterOT);
            $holidayMerge = array_merge($rosterHoliday, $plannerHoliday);
            $result['holiday'] = array_unique($holidayMerge);
        }  

        // get General
        $result['general'] = []; 
        if(in_array(1, $parameter)){
            $plannerGeneral = array_diff($plannerGeneral, $rosterHoliday);
            $plannerGeneral = array_diff($plannerGeneral, $rosterOT);
            $generalMerge = array_merge($rosterGeneral, $plannerGeneral);
            $result['general'] = array_unique($generalMerge);
        }

        // get OT 
        $otDate = []; 
        if(in_array(2, $parameter)){
            $plannerOT = array_diff($plannerOT, $rosterHoliday);
            $plannerOT = array_diff($plannerOT, $rosterGeneral);
            $otMerge = array_merge($rosterOT, $plannerOT);
            $otDate = array_unique($otMerge);
            if(count($otDate)){
                $attDate = DB::table($value['tableName'])
                ->where('as_id', $value['as_id'])
                ->whereIn('in_date', $otDate)
                ->pluck('in_date');
                $otDate = array_diff($otDate, $attDate->toArray());
            }
        }

        $result['ot'] = $otDate;
        return $result;
    }

    public function getEmployeeRosterDate($value='')
    {
        $rosterStatus = DB::table('holiday_roaster')
        ->select('remarks','date')
        //->where('year', $value['year'])
        //->where('month', $value['month'])
        ->where('as_id', $value['associate_id'])
        ->where('date','>=', $value['firstDayMonth'])
        ->where('date','<=', $value['lastDayMonth'])
        ->get();
        return collect($rosterStatus)->groupBy('remarks', true)->map(function($q){
            return collect($q)->pluck('date');
        });
    }

    public function getEmployeePlannerDate($value='')
    {
        $yearMonth = date('Y-m', strtotime($value['year'].'-'.$value['month']));
        $query = DB::table('hr_yearly_holiday_planner')
        ->select('hr_yhp_open_status', 'hr_yhp_dates_of_holidays')
            ->where('hr_yhp_unit', $value['as_unit_id'])
            ->where('hr_yhp_dates_of_holidays','>=', $value['firstDayMonth'])
            ->where('hr_yhp_dates_of_holidays','<=', $value['lastDayMonth']);
            if($value['empdojMonth'] == $yearMonth){
                $query->where('hr_yhp_dates_of_holidays','>=', $value['as_doj']);
            }

        $plannerStatus = $query->get();
        return collect($plannerStatus)->groupBy('hr_yhp_open_status', true)->map(function($q){
            return collect($q)->pluck('hr_yhp_dates_of_holidays');
        });
    }

    public function getEmployeeRosterPlannerDateWithKey($value, $parameter)
    {
        $rosterDate = $this->getEmployeeRosterDateWithKey($value);
        $plannerDate = ($value['shift_roaster_status']==0)?$this->getEmployeePlannerDateWithKey($value):[];

        // Planner List
        $plannerHoliday = isset($plannerDate[0])?$plannerDate[0]->toArray():[];
        $plannerGeneral = isset($plannerDate[1])?$plannerDate[1]->toArray():[];
        $plannerOT = isset($plannerDate[2])?$plannerDate[2]->toArray():[];
        // Roster List
        $rosterHoliday = isset($rosterDate['Holiday'])?$rosterDate['Holiday']->toArray():[];
        $rosterHoliday = array_diff_key($rosterHoliday, $plannerHoliday);

        $rosterGeneral = isset($rosterDate['General'])?$rosterDate['General']->toArray():[];
        $rosterGeneral = array_diff_key($rosterGeneral, $plannerGeneral);

        $rosterOT = isset($rosterDate['OT'])?$rosterDate['OT']->toArray():[];
        $rosterOT = array_diff_key($rosterOT, $plannerOT);

        // get Holiday
        $result['holiday'] = []; 
        if(in_array(0, $parameter)){
            $plannerHoliday = array_diff_key($plannerHoliday, $rosterGeneral);
            $plannerHoliday = array_diff_key($plannerHoliday, $rosterOT);
            $holidayMerge = array_merge($rosterHoliday, $plannerHoliday);
            $holidayUnique = array_unique(array_keys($holidayMerge)); 
            $holidayFlip = array_flip($holidayUnique);
            $result['holiday'] = array_intersect_key($holidayMerge,$holidayFlip);
            // $result['holiday'] = array_unique($holidayMerge);
        }  

        // get General
        $result['general'] = []; 
        if(in_array(1, $parameter)){
            $plannerGeneral = array_diff_key($plannerGeneral, $rosterHoliday);
            $plannerGeneral = array_diff_key($plannerGeneral, $rosterOT);
            $generalMerge = array_merge($rosterGeneral, $plannerGeneral);
            $generalUnique = array_unique(array_keys($generalMerge)); 
            $generalFlip = array_flip($generalUnique);
            $result['general'] = array_intersect_key($generalMerge,$generalFlip);
            // $result['general'] = array_unique($generalMerge);
        }

        // get OT 
        $otDate = []; 
        $attDate = []; 
        if(in_array(2, $parameter)){
            $plannerOT = array_diff_key($plannerOT, $rosterHoliday);
            $plannerOT = array_diff_key($plannerOT, $rosterGeneral);
            $otMerge = array_merge($rosterOT, $plannerOT);
            $otUnique = array_unique(array_keys($otMerge)); 
            $otFlip = array_flip($otUnique);
            $otDate = array_intersect_key($otMerge,$otFlip);
            // $otDate = array_unique($otMerge);
            if(count($otDate)){
                $attDate = DB::table($value['tableName'])
                ->where('as_id', $value['as_id'])
                ->whereIn('in_date', array_keys($otDate))
                ->pluck('remarks', 'in_date')
                ->toArray();
                $otDate = array_diff_key($otDate, $attDate);
            }
        }

        $result['ot'] = $otDate;

        // get Present OT
        $result['presentOt'] = $attDate;
        return $result;
    }

    public function getEmployeeRosterDateWithKey($value='')
    {
        $rosterStatus = DB::table('holiday_roaster')
        ->select('remarks','date', 'comment')
        ->where('year', $value['year'])
        ->where('month', $value['month'])
        ->where('as_id', $value['associate_id'])
        ->where('date','>=', $value['firstDayMonth'])
        ->where('date','<=', $value['lastDayMonth'])
        ->get();
        return collect($rosterStatus)->groupBy('remarks', true)->map(function($q){
            return collect($q)->pluck('comment', 'date');
        });
    }

    public function getEmployeePlannerDateWithKey($value='')
    {
        $yearMonth = date('Y-m', strtotime($value['year'].'-'.$value['month']));
        $query = DB::table('hr_yearly_holiday_planner')
        ->select('hr_yhp_open_status', 'hr_yhp_dates_of_holidays', 'hr_yhp_comments')
            ->where('hr_yhp_unit', $value['as_unit_id'])
            ->where('hr_yhp_dates_of_holidays','>=', $value['firstDayMonth'])
            ->where('hr_yhp_dates_of_holidays','<=', $value['lastDayMonth']);
            if($value['empdojMonth'] == $yearMonth){
                $query->where('hr_yhp_dates_of_holidays','>=', $value['as_doj']);
            }

        $plannerStatus = $query->get();
        return collect($plannerStatus)->groupBy('hr_yhp_open_status', true)->map(function($q){
            return collect($q)->pluck('hr_yhp_comments', 'hr_yhp_dates_of_holidays');
        });
    }

    public function getEmployeeLeaveCount($value='')
    {
        return DB::table('hr_leave')
        ->where('leave_status', 1)
        ->select(
            DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
        )
        
        ->where('leave_ass_id', $value['associate_id'])
        ->where('leave_from', '>=', $value['firstDayMonth'])
        ->where('leave_to', '<=', $value['lastDayMonth'])
        ->first()->total??0;
        
    }

    public function getEmployeeLeaveData($value='')
    {
        $leaveDate = DB::table('hr_leave')
        ->where('leave_status', 1)
        ->where('leave_ass_id', $value['associate_id'])
        ->where('leave_from', '>=', $value['firstDayMonth'])
        ->where('leave_to', '<=', $value['lastDayMonth'])
        ->get();
        $dates = [];
        foreach ($leaveDate as $key => $leave) {
            $period = CarbonPeriod::create($leave->leave_from, $leave->leave_to);
            foreach ($period as $date) {
                $comment = ($leave->leave_comment!='')?('- '.$leave->leave_comment):'';
                $dates[$date->format('Y-m-d')] = $leave->leave_type.' Leave '.$comment;
            }
        }
        
        return $dates;
    }

    public function getEmployeePunchCategoryCount($value='')
    {
        
        $data = DB::table($value['tableName'])
            ->select([
                DB::raw('count(as_id) as present'),
                DB::raw('SUM(ot_hour) as ot'),
                DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late'),
                DB::raw('COUNT(CASE WHEN remarks ="HD" THEN 1 END) AS halfday')
            ])
            ->where('as_id', $value['as_id'])
            ->where('in_date','>=', $value['firstDayMonth'])
            ->where('in_date','<=', $value['lastDayMonth'])
            ->first();
        $row['presentCount'] = $data->present??0;
        $row['lateCount'] = $data->late??0;
        $row['halfCount'] = $data->halfday??0;
        $row['otCount'] = ($value['as_ot']==1)?($data->ot??0):0;
        if($value['as_ot']==1){
            // check if Friday has extra OT
            if($value['shift_roaster_status'] == 1 ){
                $fridayOt = collect($this->getEmployeeSpecialInfo($value))->sum('ot_hour');

                $otHour = $row['otCount'] + $fridayOt;
            }

            $diffExplode = explode('.', $otHour);
            $minutes = (isset($diffExplode[1]) ? $diffExplode[1] : 0);
            $minutes = floatval('0.'.$minutes);
            if($minutes > 0 && $minutes != 1){
                $min = (int)round($minutes*60);
                $minOT = min_to_ot();
                $minutes = $minOT[$min]??0;
            }

            $row['otCount'] = $diffExplode[0]+$minutes;
        }
        return $row;
    }

    public function getEmployeeMonthStartNEndInfo($value='')
    {
        $partial = 0;
        $empdojDay = date('d', strtotime($value['as_doj']));
        $empdojMonth = date('Y-m', strtotime($value['as_doj']));
        $totalDay = $value['totalDay'];
        $today = $value['yearMonth'].'-01';
        $monthDayCount  = Carbon::parse($value['yearMonth'])->daysInMonth;
        $firstDateMonth = Carbon::parse($today)->startOfMonth()->toDateString();
        if($empdojMonth == $value['yearMonth']){
            $totalDay = $totalDay - ((int) $empdojDay-1);
            $firstDateMonth = date('Y-m-d', strtotime($value['as_doj']));
        }
        
        if($value['as_status_date'] != null){
            $sDate = $value['as_status_date'];
            $sYearMonth = Carbon::parse($sDate)->format('Y-m');
            $sDay = Carbon::parse($sDate)->format('d');

            if($value['yearMonth'] == $sYearMonth){
                $firstDateMonth = $value['as_status_date'];
                $totalDay = $totalDay - ((int) $sDay-1);

                if($sDay > 1){
                    $partial = 1;
                }
            }
        }

        
        if($monthDayCount > $totalDay){
            $lastDateMonth = $value['yearMonth'].'-'.$value['totalDay'];
        }else{
            if($value['yearMonth'] == date('Y-m')){
                $lastDateMonth = date('Y-m-d');
                $totalDay = $totalDay - (date('t') - date('j'));
                if($monthDayCount > $totalDay){
                    $partial = 1;
                }
            }else{
                $lastDateMonth = Carbon::parse($today)->endOfMonth()->toDateString();
            }
        }

        return [
            'empdojMonth' => $empdojMonth,
            'firstDayMonth' => $firstDateMonth,
            'lastDayMonth' => $lastDateMonth,
            'partial' => $partial,
            'totalDay' => $totalDay, 
            'monthDayCount' => $monthDayCount
        ];
    }

    public function attendanceRemovalOnOffDay($value, $date)
    {
        // present delete
        $present = DB::table($value['tableName'])
        ->where('as_id', $value['as_id'])
        ->whereIn('in_date', $date)
        ->delete();
        // absent delete
        $absent = DB::table('hr_absent')
        ->where('associate_id', $value['associate_id'])
        ->whereIn('date', $date)
        ->delete();

        return 'success';
    }

    public function getEmployeePresentInfo($value='')
    {
        return DB::table($value['tableName'])
        ->where('as_id', $value['as_id'])
        ->where('in_date','>=', $value['firstDayMonth'])
        ->where('in_date','<=', $value['lastDayMonth'])
        ->orderBy('in_date')
        ->get();
    }

    public function getEmployeeOutsideInfo($value='')
    {
        $outsideInfo = DB::table('hr_outside')
        ->where('as_id', $value['associate_id'])
        ->where('start_date', '>=', $value['firstDayMonth'])
        ->where('end_date', '<=', $value['lastDayMonth'])
        ->where('status', 1)
        ->get();
        $dates = [];
        foreach ($outsideInfo as $key => $outside) {
            $period = CarbonPeriod::create($outside->start_date, $outside->end_date);
            foreach ($period as $date) {
                $dates[$date->format('Y-m-d')] = $outside->requested_location;
            }
        }
        
        return $dates;
    }

    public function getEmployeeSpecialInfo($value='')
    {
        return DB::table('hr_att_special')
        ->where('as_id', $value['as_id'])
        ->where('in_date','>=', $value['firstDayMonth'])
        ->where('in_date','<=', $value['lastDayMonth'])
        ->orderBy('in_date')
        ->get();
    }
}