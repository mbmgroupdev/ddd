<?php
namespace App\Helpers;

use App\Helpers\Custom;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Absent;
use App\Models\Hr\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB;

class EmployeeHelper
{
	/* */
	//intimePunch  = Employee In Time punch
	//outtimePunch = Employee Out Time punch
	//shiftIntime  = Employee Shift In Time
	//shiftOuttime = Employee Shift Out Time
	//shiftBreak   = Employee Shift Break Time
	//shiftNight   = Employee Night Flag Status
	//eAsId        = Employee Associate Id
	//eSRStatus    = Employee Shift Roster Status
	//eUnit        = Employee Unit Id
	/**/
	public static function daliyOTCalculation($intimePunch, $outtimePunch, $shiftIntime, $shiftOuttime, $shiftBreak, $shiftNight, $eAsId, $eSRStatus, $eUnit)
	{

		$shiftIntime = date('Y-m-d', strtotime($intimePunch)).' '.$shiftIntime;
		if($shiftNight == 0){
			$shiftOuttime = date('Y-m-d', strtotime($shiftIntime)).' '.$shiftOuttime;
		}else{
			$shiftOuttime = date('Y-m-d', strtotime($outtimePunch)).' '.$shiftOuttime;
		}

	    $cOut = strtotime(date("H:i", strtotime($outtimePunch)));

	    $overtimes = 0;
	    // CALCULATE OVER TIME
	    if(!empty($cOut))
	    {  
		    $today = Carbon::parse($intimePunch)->format('Y-m-d');
		    $year = Carbon::parse($intimePunch)->format('Y');
		    $month = Carbon::parse($intimePunch)->format('m');
		    $otCheck = HolidayRoaster::getHolidayYearMonthAsIdDateWiseRemark($year, $month, $eAsId, $today, 'OT');
		    if($otCheck == null && $eSRStatus == 0){
		      $otCheck = YearlyHolyDay::getCheckUnitDayWiseHolidayStatus($eUnit, $today, 2);
		    }

		    $shiftIntime = strtotime($shiftIntime);
		    $shiftOuttime = strtotime($shiftOuttime);
		    $intimePunch = strtotime($intimePunch);
		    $outtimePunch = strtotime($outtimePunch);
		    if($shiftIntime < $intimePunch){
		    	$shiftIntime = $intimePunch;
		    }
		    if($otCheck != null){
		    	$date1 = $shiftIntime;
		    }else{
		    	$date1 = $shiftOuttime;
		    }
			$date2 = $outtimePunch;
			$diff = (($date2 - ($date1 + ($shiftBreak*60))))/3600;
			if($diff < 0){
				$diff = 0;
			}
			$diff = round($diff, 2);
			$diffExplode = explode('.', $diff);
			$minutes = (isset($diffExplode[1]) ? $diffExplode[1] : 0);
			$minutes = floatval('0.'.$minutes);
			if($minutes >= 0.25 && $minutes < 0.7833) $minutes = '.50';
		    else if($minutes >= 0.7833) $minutes = '1';
		    else $minutes = '0';
		    
		    $overtimes = $diffExplode[0]+$minutes;
		    $overtimes = number_format((float)$overtimes, 2, '.', '');
	    }
	    return $overtimes;
	}

	/**/

	/**/
	public static function employeeDateWiseMakeAbsent($asId, $date)
	{
		$result = array();
		$result['status'] = 'error';
		$getEmployee = Employee::where('as_id', $asId)->first();
		if($getEmployee == null){
			$result['message'] = "Employee not found!";
			return $result;
		}
		$year = Carbon::parse($date)->format('Y');
        $month = Carbon::parse($date)->format('m');
        $tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
		try {
			DB::table($tableName)
            ->where('as_id', $getEmployee->as_id)
            ->whereDate('in_time', date('Y-m-d',strtotime($date)))
            ->delete();

			$getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWiseRemarkMulti($year, $month, $getEmployee->associate_id, $date, ['Holiday', 'OT']);
	        if($getHoliday == null && $getEmployee->shift_roaster_status == 0){
	            $getHoliday = YearlyHolyDay::getCheckUnitDayWiseHolidayStatusMulti($getEmployee->as_unit_id, $date, [0, 2]);
	        }
	        
	        if($getHoliday == null){
	            $getLeave = DB::table('hr_leave')
	            ->where('leave_ass_id', $getEmployee->associate_id)
	            ->where('leave_from', '<=', $date)
	            ->where('leave_to', '>=', $date)
	            ->where('leave_status',1)
	            ->first();
	            //
	            $getAbsent = DB::table('hr_absent')
	            ->where('associate_id', $getEmployee->associate_id)
	            ->where('hr_unit', $getEmployee->as_unit_id)
	            ->where('date', $date)
	            ->first();

	            if($getLeave == '' && $getAbsent == ''){
	               $id = DB::table('hr_absent')
	                ->insertGetId([
	                    'associate_id' => $getEmployee->associate_id,
	                    'hr_unit'  => $getEmployee->as_unit_id,
	                    'date'  => $date
	                ]); 
	                
	                $result['message'] = 'Successfully make absent this day';
	            }else{
	            	$result['message'] = 'Leave for this day / Absent not found';
	            }
	        }else{
	        	$result['message'] = 'Holiday for this day';
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
	        $result['status'] = 'success';
	        return $result;
		} catch (\Exception $e) {
			$bug = $e->getMessage();
			$result['message'] = $bug;
			return $result;
		}
	}

	public static function employeeAttendanceAbsentDelete($associateId, $date)
	{
		try {
			$flag = 0;
			$getAbsent = Absent::getAbsentCheckExists($associateId, $date);
			if($getAbsent != null){
				Absent::where(
					'id', $getAbsent->id)
				->delete();
				$flag = 1;
			}

			$getEmployee = Employee::getEmployeeAssIdWiseSelectedField($associateId, ['as_id', 'as_unit_id']);
			$tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
			$getAttendance = DB::table($tableName)
				->where('as_id', $getEmployee->as_id)
				->whereDate('in_time', $date)
				->first();
			if($getAttendance != null){
				DB::table($tableName)
				->where('id', $getAttendance->id)
				->delete();
				$flag = 1;
			}

			if($flag == 1){
				return 'success';
			}
			return 'not found';
		} catch (\Exception $e) {
			$bug = $e->getMessage();
			return $bug;
		}
	}

	public static function employeeStatusDateWiseAbsentDelete($associateId, $date)
	{
		try {
			$flag = 0;
			$getAbsent = Absent::getAbsentCheckExists($associateId, $date);
			if($getAbsent != null){
				Absent::where(
					'id', $getAbsent->id)
				->delete();
				$flag = 1;
			}

			if($flag == 1){
				return 'success';
			}
			return 'not found';
		} catch (\Exception $e) {
			$bug = $e->getMessage();
			return $bug;
		}
	}

	public static function employeeAttendanceOTUpdate($associateId, $date)
	{
		try {
			$flag = 0;
			$year = date('Y',strtotime($date));
          	$month = date('m',strtotime($date));
			$getEmployee = Employee::getEmployeeAssociateIdWise($associateId);
			if($getEmployee != null && $getEmployee->as_ot == 1){
				$tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
				$getAttendance = DB::table($tableName)
				->where('as_id', $getEmployee->as_id)
				->whereDate('in_time', $date)
				->first();
				if($getAttendance != null){
					if($getAttendance->out_time != null || $getAttendance->out_time != ''){
						$unitId = $getEmployee->as_unit_id;
						$day_of_date = date('j', strtotime($date));
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
	                        'hr_shift.hr_shift_code',
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
	            			$shiftIntime= $shift->hr_shift_start_time;
	            			$shiftOuttime= $shift->hr_shift_end_time;
	            			$shiftBreak= $shift->hr_shift_break_time;
	            			$shiftNight= $shift->hr_shift_night_flag;
	            		}else{
	            			$shiftIntime= $getEmployee->shift['hr_shift_start_time'];
	            			$shiftOuttime= $getEmployee->shift['hr_shift_end_time'];
	            			$shiftBreak= $getEmployee->shift['hr_shift_break_time'];
	            			$shiftNight= $getEmployee->shift['hr_shift_night_flag'];
	            		}

						$overtimes = self::daliyOTCalculation($getAttendance->in_time, $getAttendance->out_time, $shiftIntime, $shiftOuttime, $shiftBreak, $shiftNight, $associateId, $getEmployee->shift_roaster_status, $getEmployee->as_unit_id);
						
						/*$h = floor($overtimes/60) ? ((floor($overtimes/60)<10)?("0".floor($overtimes/60)):floor($overtimes/60)) : '00';
	                    $m = $overtimes%60 ? (($overtimes%60<10)? ("0".$overtimes%60):($overtimes%60)) : '00';
	                    $otHour = ($h.'.'.($m =='30'?'50':'00'));*/
	                    
						if($getAttendance->ot_hour != $overtimes){
							DB::table($tableName)
							->where('id', $getAttendance->id)
							->update(['ot_hour' => $overtimes]);

							$flag = 1;
						}
					}
				}
			}
			

			if($flag == 1){
				return 'success';
			}else{
				return 'not found';
			}
		} catch (\Exception $e) {
			$bug = $e->getMessage();
			return $bug;
		}
	}

	/**/
	// $srStatus = employee shift roster status
	public static function employeeDateWiseStatus($date, $assId, $unit, $srStatus)
	{
		$month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        $today = date("Y-m-d", strtotime($date));
        $day = 'open';
        // leave check individual
        $getLeave = Leave::getDateStatusWiseEmployeeLeaveCheck($assId, $today, 1);

        if($getLeave != null){
            $day = 'leave';
        }else{
        	$getDayStatus = HolidayRoaster::getHolidayYearMonthAsIdDateWiseRemarkMulti($year, $month, $assId, $today, ['Holiday', 'OT', 'General']);
	        if($getDayStatus != null){
	        	if($getDayStatus->remarks == 'General'){
	        		$day = 'open';
	        	}else{
	        		$day = $getDayStatus->remarks;
	        	}
	        }else if($srStatus == 0){
	        	$getDayStatus = YearlyHolyDay::getCheckUnitDayWiseHolidayStatusMulti($unit, $today, [0, 2]);
	        	if($getDayStatus != null){
	        		if($getDayStatus->hr_yhp_open_status == 0){
	        			$day = 'Holiday';
	        		}else{
	        			$day = 'OT';
	        		}
	        	}
	        }else{
	        	$day = 'open';
	        }
        }
        return $day;
	}

}