<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\JobCardInterface;
use Illuminate\Support\Collection;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\Unit;

use DB;

class JobCardRepository implements JobCardInterface
{
	protected $month;

	protected $monthYear;

	protected $year;

	protected $associateId;

	protected $employee;

	protected $attTable;

	protected $startDate; 

	protected $endDate;

	public function __construct($associate_id, $month_year = date('Y-m'))
	{
		$this->associateId = $associate_id;
		$this->monthYear   = $month_year;
        $this->month 	   = date('m', strtotime($this->monthYear));
        $this->year 	   = date('Y', strtotime($this->monthYear));

	}

	protected function employee()
	{
		$this->employee = DB::table("hr_as_basic_info AS b")
			->where("associate_id", $this->associateId)
			->first();

		$this->previousHistory();

	}

	protected function previousHistory()
	{
		if(strtotime(date('Y-m')) > strtotime($this->monthYear)){
			$subsection = subSection_by_id();

            $salary = DB::table('hr_monthly_salary as s')
                ->where('s.as_id', $this->associateId)
                ->where('s.month', $this->month)
                ->where('s.year', $this->year)
                ->first();


            if($salary){
            	$subsection = $subsection[$salary->sub_section_id];
            	$q->section_id = $subsection['hr_subsec_section_id'];
    			$q->department_id = $subsection['hr_subsec_department_id'];
    			$q->area_id = $subsection['hr_subsec_area_id'];

	            if($sal->designation_id != $this->employee->as_designation_id){
	                $this->employee->pre_designation = $sal->designation_id;
	            }
	            if($this->employee->as_section_id != $sal->hr_subsec_section_id){
	                $this->employee->pre_section = $sal->section_id;
	            }
	            if($this->employee->as_unit_id != $sal->unit_id){
	                $this->employee->pre_unit = $sal->unit_id;
	            }
            }

        }
	}

	protected function attTable()
	{
		$this->attTable = get_att_table($this->employee->as_unit_id);
	}

	protected function dateRange()
	{
		$date              = ($this->year."-".$this->month."-"."01");
        $this->startDate   = date('Y-m-d', strtotime($date));
        $this->endDate     = date('Y-m-t', strtotime($date));
        $toDay             = date('Y-m-d');

        $this->endDate = $this->endDate > $toDay?$toDay:$this->endDate;

        // if the employee join this month
        if($this->employee->as_doj != null) {
            list($yearE,$monthE,$dateE) = explode('-',$this->employee->as_doj);
            if($this->year == $yearE && $this->month == $monthE) {
                $this->startDate = $this->employee->as_doj;
            }
        }

        // if the employee has status date
        if($this->employee->as_status_date != null) {
            list($yearL,$monthL,$dateL) = explode('-',$this->employee->as_status_date);
            if($this->year == $yearL && $this->month == $monthL) {
                // if rejoin/ return from maternity
                if($this->employee->as_status == 1){
                    $this->startDate = $this->employee->as_status_date;
                }

                // left,terminate,resign, suspend, delete
                if(in_array($this->employee->as_status,[0,2,3,4,5])!=false) {
                    $this->endDate = $this->employee->as_status_date;
                }
            }
        }
	}

	protected function planner()
	{
		return 	DB::table("hr_yearly_holiday_planner")
                ->where('hr_yhp_status', 1)
                ->where('hr_yhp_unit', $this->employee->as_unit_id)
                ->where('hr_yhp_dates_of_holidays','>=', $this->startDate)
                ->where('hr_yhp_dates_of_holidays','<=', $this->endDate)
                ->get()
                ->keyBy('hr_yhp_dates_of_holidays')->toArray();
	}

	protected function friday()
	{
		return 	DB::table('hr_att_special')
            ->where('as_id', $this->employee->as_id)
            ->where('in_date','>=', $this->startDae)
            ->where('in_date','<=', $this->endDate)
            ->get()
            ->keyBy('in_date');
	}

	protected function leave(): Collection
	{
		$leaveCheck = Leave::where('leave_ass_id', $this->associateId)
                    ->where(function ($q) {
                        $q->where('leave_from', '<=', $this->startDate);
                        $q->where('leave_to', '>=', $this->endDate);
                    })
                    ->where('leave_status',1)
                    ->get();
	}

	public function jobCard($associate_id, $month_year)
	{

	}

	public  function empAttendanceByMonth($request)
    {



        //check user exists
        if($fetchUser->exists()) {
            $getUnit = unit_by_id();
            $getLine = line_by_id();
            $getFloor = floor_by_id();
            $getDesignation = designation_by_id();
            $getSection = section_by_id();
            $subSection = subSection_by_id();



 

            $totalDays  = (date('d', strtotime($this->endDate)) - date('d', strtotime($this->startDae))) + 1;


            $floor = $getFloor[$this->employee->as_floor_id]['hr_floor_name']??'';
            $line = $getLine[$this->employee->as_line_id]['hr_line_name']??'';

            // holiday roster
            $getHolidayRoster = HolidayRoaster::where('as_id',$associate)
                ->where('date','>=', $this->startDae)
                ->where('date','<=', $this->endDate)
                ->get()
                ->keyBy('date')->toArray();

            // get attendance
            $getAttendance = DB::table($tableName)
                ->where('as_id', $this->employee->as_id)
                ->where('in_date','>=', $this->startDae)
                ->where('in_date','<=', $this->endDate)
                ->get()
                ->keyBy('in_date')->toArray();

            // yearly holiday planner
            $getHoliday = $this->planner();

            if($this->employee->shift_roaster_status == 1 ){

                $friday_att = $this->friday();
            }
                  
            for($i=$iEx; $i<=$totalDays; $i++) {
                $date      = ($year."-".$month."-".$x++);
                $thisDay   = date('Y-m-d', strtotime($date));


                $lineFloorInfo = DB::table('hr_station')
                    ->where('associate_id',$associate)
                    ->whereDate('start_date','<=',$thisDay)
                    ->where(function ($q) use($thisDay) {
                        $q->whereDate('end_date', '>=', $thisDay);
                        $q->orWhereNull('end_date');
                    })
                    ->first();

                $attendance[$i] = array(
                    'in_time' => null,
                    'out_time' => null,
                    'overtime_time' => null,
                    'late_status' => null,
                    'present_status' =>"A",
                    'remarks' => null,
                    'date' => $thisDay,
                    'floor' => !empty($lineFloorInfo->changed_floor)?($getFloor[$lineFloorInfo->changed_floor]['hr_floor_name']??''):$floor,
                    'line' => !empty($lineFloorInfo->changed_line)?($getLine[$lineFloorInfo->changed_line]['hr_line_name']??''):$line,
                    'outside' => null,
                    'outside_msg' => null,
                    'attPlusOT' => null,
                    'day_status' => "A"
                );

                if($leaveCheck){
                    $attendance[$i]['present_status'] = $leaveCheck->leave_type." Leave <br><b>".$leaveCheck->leave_comment.'</b>';
                    $attendance[$i]['day_status'] = "P";
                } else {
                    $attendCheck = $getAttendance[$thisDay]??'';
                    // check holiday
                    $holidayRoaster = $getHolidayRoster[$thisDay]??'';

                    if($holidayRoaster == ''){
                        // $holidayEmployee = Employee::where('associate_id',$associate)->first();
                        // if shift assign then check yearly hoiliday
                        if((int)$this->employee->shift_roaster_status == 0) {

                            $holidayCheck = $getHoliday[$thisDay]??'';
                            if($holidayCheck != ''){
                                $attendance[$i]['day_status'] = isset($getAttendance[$thisDay])?'P':'';
                                if($holidayCheck->hr_yhp_open_status == 1) {
                                    $attendance[$i]['present_status'] = "Weekend(General)".(isset($getAttendance[$thisDay])?'':' - A');

                                } if($holidayCheck->hr_yhp_open_status == 2){
                                    $attendance[$i]['present_status'] = "Weekend(OT)";
                                    $attendance[$i]['attPlusOT'] = 'OT - '.$holidayCheck->hr_yhp_comments;
                                }else if($holidayCheck->hr_yhp_open_status == 0){
                                    $attendance[$i]['present_status'] = $holidayCheck->hr_yhp_comments;
                                    $attendance[$i]['day_status'] = "W";
                                }
                            }
                        }
                    } else {
                        if($holidayRoaster['remarks'] == 'Holiday') {
                            $attendance[$i]['present_status'] = "Day Off";
                            if($holidayRoaster['comment'] != null) {
                                $attendance[$i]['present_status'] .= ' - '.$holidayRoaster['comment'];
                            }
                            $attendance[$i]['day_status'] = "W";
                        }

                        if($holidayRoaster['remarks'] == 'OT') {
                            $attendance[$i]['day_status'] = isset($getAttendance[$thisDay])?'P':'';
                            $attendance[$i]['present_status'] = "OT";
                            $attendance[$i]['attPlusOT'] = 'OT - '.$holidayRoaster['comment'];
                        }
                    }

                    if($attendCheck != ''){
                        $intime = (!empty($attendCheck->in_time))?date("H:i", strtotime($attendCheck->in_time)):null;
                        $outtime = (!empty($attendCheck->out_time))?date("H:i", strtotime($attendCheck->out_time)):null;
                        if($attendCheck->remarks == 'DSI'){
                            $attendance[$i]['in_time'] = null;
                        }else{
                            $attendance[$i]['in_time'] = $intime;
                        }   
                        $attendance[$i]['out_time'] = $outtime;
                        $attendance[$i]['late_status']= $attendCheck->late_status;
                        $attendance[$i]['remarks']= $attendCheck->remarks;
                        $attendance[$i]['day_status'] = "P";
                        $attendance[$i]['present_status']=$attendance[$i]['attPlusOT']? "P (".$attendance[$i]['attPlusOT'].")":'P';
                        if($flag == 1){
                            $attendance[$i]['overtime_time'] = (($sal->ot_status==1)? $attendCheck->ot_hour:"");
                            if($sal->ot_status==1){
                                $total_ot+= (float) $attendCheck->ot_hour;
                            }
                        }else{
                            $attendance[$i]['overtime_time'] = (($this->employee->as_ot==1)? $attendCheck->ot_hour:"");
                            if($this->employee->as_ot==1){
                                $total_ot+= (float) $attendCheck->ot_hour;
                            }
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
                    $loc = $outsideCheck->requested_location;
                    if($outsideCheck->requested_location == 'WFHOME'){
                        $attendance[$i]['outside'] = 'Work from Home';
                        $loc = 'Home';
                    }else if ($outsideCheck->requested_location == 'Outside'){
                        $attendance[$i]['outside'] = 'Outside';
                        $loc = $outsideCheck->requested_place;
                    }else{
                       $attendance[$i]['outside'] = $outsideCheck->requested_location;
                    }
                    if($outsideCheck->type==1){
                        $attendance[$i]['outside_msg'] = 'Full Day at '.$loc;
                    }else if($outsideCheck->type==2){
                        $attendance[$i]['outside_msg'] = 'First Half at '.$loc;
                    }else if($outsideCheck->type==3){
                        $attendance[$i]['outside_msg'] = 'Second Half at '.$loc;
                    }
                }

                if($attendance[$i]['present_status'] == 'A' || $attendance[$i]['present_status'] == 'Weekend(General) - A'){
                    $absent++;
                }

            }

            $this->employee->present = $total_attends;
            $this->employee->absent = $absent;
            if(count($friday_att) > 0){
                $total_ot += collect($friday_att)->sum('ot_hour');
            }

            $this->employee->ot_hour = $total_ot;

            $result['attendance']   = $attendance;
            $result['info']         = $this->employee;
            $result['joinExist']    = $joinExist;
            $result['leftExist']    = $leftExist;
            $result['friday']       = $friday_att;

            return $result;
        }


    }
}