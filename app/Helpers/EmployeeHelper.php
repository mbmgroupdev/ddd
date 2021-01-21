<?php
namespace App\Helpers;

use App\Helpers\Custom;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\BillSettings;
use App\Models\Hr\BillSpecialSettings;
use App\Models\Hr\Bills;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\YearlyHolyDay;
use App\Models\Hr\AttendanceBonusConfig;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\SalaryAdjustMaster;
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
		$overtimes = 0;
		$outTimeEx = explode(' ', $outtimePunch);
		if(isset($outTimeEx[1]) && $outTimeEx[1] != '00:00:00'){
			$shiftIntime = date('Y-m-d', strtotime($intimePunch)).' '.$shiftIntime;
			if($shiftNight == 0){
				$shiftOuttime = date('Y-m-d', strtotime($shiftIntime)).' '.$shiftOuttime;
			}else{
				$shiftOuttime = date('Y-m-d', strtotime($outtimePunch)).' '.$shiftOuttime;
			}

		    $cOut = strtotime(date("H:i", strtotime($outtimePunch)));
		    // CALCULATE OVER TIME
		    if(!empty($cOut))
		    {  
			    $today = Carbon::parse($intimePunch)->format('Y-m-d');
			    $year = Carbon::parse($intimePunch)->format('Y');
			    $month = Carbon::parse($intimePunch)->format('m');
			    $dayname = Carbon::parse($intimePunch)->format('l');
			    $employee = Employee::where('associate_id', $eAsId)->first();

			    if(date('H:i:s', strtotime($shiftIntime)) < date('H:i:s', strtotime('14:00:00'))  && $dayname == 'Friday' && in_array($eUnit, [1,4,5])){
			    	$shiftBreak = 90;
			    	/*224 = security, 350/428 = cook*/
			    	if($employee->as_designation_id == 224 || $employee->as_designation_id == 350 || $employee->as_designation_id == 428){
			    		$shiftBreak = 30;
			    	}
			    }

			    $checkBillHour = (strtotime($outtimePunch) - strtotime($shiftIntime))/3600;
			    $breakCount = 0;
			    $breakDiff = 0;
			    if($checkBillHour > 6){
			    	$breakCount = 1;
			    }

			    if(!in_array($eUnit, [1,4,5])){
			    	$otDiff = ((strtotime($outtimePunch) - (strtotime($shiftOuttime) + ($shiftBreak*60))))/3600;
			    	if($otDiff > 0){
						$breakDiff = $otDiff/(6+($shiftBreak/60)); // 6 hour before start break eligible
					}
			    }

			    $shiftBreak = $shiftBreak * ((int)$breakDiff + $breakCount);

			    $otCheck = HolidayRoaster::getHolidayYearMonthAsIdDateWiseRemark($year, $month, $eAsId, $today, 'OT');
			    // if($otCheck != null && (date('H:i:s', strtotime($shiftIntime)) < date('H:i:s', strtotime('09:00:00')) && date('H:i:s', strtotime($outTimeEx[1])) < date('H:i:s', strtotime('13:30:00')))){
			    // 	$shiftBreak = 0;
			    // }

			    if($otCheck == null && $eSRStatus == 0){
			      $otCheck = YearlyHolyDay::getCheckUnitDayWiseHolidayStatus($eUnit, $today, 2);
			    }
			    // return $otCheck;

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
				$diff = ($date2 - ($date1 + ($shiftBreak*60)))/3600;
				if($diff < 0){
					$diff = 0;
				}
				// $diff = round($diff, 2);
				$diffExplode = explode('.', $diff);
				// return $diff;
				$minutes = (isset($diffExplode[1]) ? $diffExplode[1] : 0);
				$minutes = floatval('0.'.$minutes);
				// return $minutes;
				if($minutes > 0.16667 && $minutes <= 0.75) $minutes = $minutes;
			    else if($minutes >= 0.75) $minutes = 1;
			    else $minutes = 0;
			    
			    $overtimes = $diffExplode[0]+$minutes;
			    $overtimes = number_format((float)$overtimes, 2, '.', '');
		    }
		    return $overtimes;
		}
		
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

	// daily bill calculation
	public static function dailyBillCalculation($asOt, $unit, $date, $asId, $shiftNight, $designationId)
	{
		try {
			$billSetting = BillSettings::where('unit_id', $unit)->where('status', 1)->whereNull('end_date')->first();
			$flag = 0;
			if($billSetting != null){
				if($billSetting->as_ot == 2){
					$flag = 1;
				}
				if($flag == 0){
					if($billSetting->as_ot == $asOt){
						$flag = 1;
					}
				}

				if($flag == 1){
					$billSpecial = BillSpecialSettings::where('bill_id', $billSetting->id)->where('designation_id', $designationId)->whereNull('end_date')->where('status', 1)->first();
					if($billSpecial != null){
						$tiffin = $billSpecial->tiffin_bill;
						$dinner = $billSpecial->dinner_bill;
					}else{
						$tiffin = $billSetting->tiffin_bill;
						$dinner = $billSetting->dinner_bill;
					}
					$getBill = Bills::where('as_id', $asId)->where('bill_date', $date)->first();
					$bills = [
						'as_id' => $asId,
						'bill_date' => $date,
						'bill_type' => $shiftNight==1?2:1,
						'amount' => $shiftNight==1?$dinner:$tiffin
					];
					if($getBill != null){
						Bills::where('id', $getBill->id)
						->update($bills);
					}else{
						$bills['pay_status'] = 0;
						Bills::insert($bills);
					}
				}
				
			} 
			return "success";
		} catch (\Exception $e) {
			$bug = $e->getMessage();
			return $bug;
			return 'error';
		}
	}


	public static function getHoliday($employee, $first_day, $last_day)
	{
		$table = get_att_table($employee->as_unit_id);
		$empdojMonth = date('Y-m', strtotime($employee->as_doj));
		$year = date('Y', strtotime($first_day));
		$month = date('m', strtotime($first_day));
		$yearMonth = date('Y-m', strtotime($first_day));
		// check OT roaster employee
        $rosterOTCount = HolidayRoaster::where('year', $year)
        ->where('month', $month)
        ->where('as_id', $employee->associate_id)
        ->where('date','>=', $first_day)
        ->where('date','<=', $last_day)
        ->where('remarks', 'OT')
        ->get();

        $rosterOtData = $rosterOTCount->pluck('date')->toArray();

        $otDayCount = 0;
        $totalOt = count($rosterOTCount);

        $otDayCount = DB::table($table)
			            ->where('as_id', $employee->as_id)
			            ->whereIn('in_date', $rosterOtData)
			            ->count();
       

        if($employee->shift_roaster_status == 1){
            // check holiday roaster employee
            $getHoliday = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $last_day)
            ->where('remarks', 'Holiday')
            ->count();
            $getHoliday = $getHoliday + ($totalOt - $otDayCount);
        }else{
            // check holiday roaster employee
            $RosterHolidayCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $last_day)
            ->where('remarks', 'Holiday')
            ->count();
            // check General roaster employee
            $RosterGeneralCount = HolidayRoaster::where('year', $year)
            ->where('month', $month)
            ->where('as_id', $employee->associate_id)
            ->where('date','>=', $first_day)
            ->where('date','<=', $last_day)
            ->where('remarks', 'General')
            ->count();
            
             // check holiday shift employee
            
            if($empdojMonth == $yearMonth){
                $query = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $last_day)
                    ->where('hr_yhp_dates_of_holidays','>=', $employee->as_doj)
                    ->where('hr_yhp_open_status', 0);
                if(count($rosterOtData) > 0){
                    $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
                $shiftHolidayCount = $query->count();
            }else{
                $query = YearlyHolyDay::
                    where('hr_yhp_unit', $employee->as_unit_id)
                    ->where('hr_yhp_dates_of_holidays','>=', $first_day)
                    ->where('hr_yhp_dates_of_holidays','<=', $last_day)
                    ->where('hr_yhp_open_status', 0);
                if(count($rosterOtData) > 0){
                    $query->whereNotIn('hr_yhp_dates_of_holidays', $rosterOtData);
                }
                $shiftHolidayCount = $query->count();
            }
            $shiftHolidayCount = $shiftHolidayCount + ($totalOt - $otDayCount);

            if($RosterHolidayCount > 0 || $RosterGeneralCount > 0){
                $getHoliday = ($RosterHolidayCount + $shiftHolidayCount) - $RosterGeneralCount;
            }else{
                $getHoliday = $shiftHolidayCount;
            }
        }

        $getHoliday = $getHoliday < 0 ? 0:$getHoliday;

        return $getHoliday;
	}

	public static function processPartialSalary($employee, $salary_date, $status)
    {
        $month = date('m', strtotime($salary_date));
        $year = date('Y', strtotime($salary_date));
        $total_day = date('d', strtotime($salary_date));

        $yearMonth = $year.'-'.$month;
        $empdoj = $employee->as_doj;
        $empdojMonth = date('Y-m', strtotime($employee->as_doj));
        $empdojDay = date('d', strtotime($employee->as_doj));

        $first_day = Carbon::parse($salary_date)->firstOfMonth()->format('Y-m-d');
        if($empdojMonth ==  $yearMonth){
            $first_day = $employee->as_doj;
            $total_day = $total_day - $empdojDay + 1;
        }


        $table = get_att_table($employee->as_unit_id);
        $att = DB::table($table)
                ->select(
                    DB::raw('COUNT(*) as present'),
                    DB::raw('SUM(ot_hour) as ot_hour'),
                    DB::raw('COUNT(CASE WHEN late_status =1 THEN 1 END) AS late')
                )
                ->where('as_id',$employee->as_id)
                ->where('in_date','>=',$first_day)
                ->where('in_date','<=', $salary_date)
                ->first();

        $late = $att->late??0;
        $overtimes = $att->ot_hour??0; 
        $present = $att->present??0;

        $getSalary = DB::table('hr_monthly_salary')
                    ->where([
                        'as_id' => $employee->associate_id,
                        'month' => $month,
                        'year' => $year
                    ])
                    ->first();

        $getHoliday = self::getHoliday($employee,$first_day, $salary_date);

        
        // get leave employee wise

        $leaveCount = DB::table('hr_leave')
			        ->select(
			            DB::raw("SUM(DATEDIFF(leave_to, leave_from)+1) AS total")
			        )
			        ->where('leave_ass_id', $employee->associate_id)
			        ->where('leave_from', '>=', $first_day)
			        ->where('leave_to', '<=', $salary_date)
			        ->first()->total??0;

        // get absent employee wise
        $getAbsent = $total_day - ($present + $getHoliday + $leaveCount);
        if($getAbsent < 0){
            $getAbsent = 0;
        }

        // get salary add deduct id form salary add deduct table
        $getAddDeduct = SalaryAddDeduct::
        where('associate_id', $employee->associate_id)
        ->where('month',  $month)
        ->where('year',  $year)
        ->first();

        if($getAddDeduct != null){
            $deductCost = ($getAddDeduct->advp_deduct + $getAddDeduct->cg_deduct + $getAddDeduct->food_deduct + $getAddDeduct->others_deduct);
            $deductSalaryAdd = $getAddDeduct->salary_add;
            $productionBonus = $getAddDeduct->bonus_add;
            $deductId = $getAddDeduct->id;
        }else{
            $deductCost = 0;
            $deductSalaryAdd = 0;
            $deductId = null;
            $productionBonus = 0;
        }

        $dateCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        //get add absent deduct calculation
        $perDayBasic = round(($employee->ben_basic / 30),2);
        $perDayGross = round(($employee->ben_current_salary /  $dateCount),2);
        $getAbsentDeduct = $getAbsent * $perDayBasic;

        //stamp = 10 by default all employee;
        

        if($employee->as_ot == 1){
            $overtime_rate = number_format((($employee->ben_basic/208)*2), 2, ".", "");
        } else {
            $overtime_rate = 0;
        }
        $overtime_salary = 0;
        

        $attBonus = 0;
        $totalLate = $late;
        $salary_date = $present + $getHoliday + $leaveCount;
        
        $salary = [
            'as_id' => $employee->associate_id,
            'month' => $month,
            'year'  => $year,
            'gross' => $employee->ben_current_salary??0,
            'basic' => $employee->ben_basic??0,
            'house' => $employee->ben_house_rent??0,
            'medical' => $employee->ben_medical??0,
            'transport' => $employee->ben_transport??0,
            'food' => $employee->ben_food??0,
            'late_count' => $late,
            'present' => $present,
            'holiday' => $getHoliday,
            'absent' => $getAbsent,
            'leave' => $leaveCount,
            'absent_deduct' => $getAbsentDeduct,
            'salary_add_deduct_id' => $deductId,
            'ot_rate' => $overtime_rate,
            'ot_hour' => $overtimes,
            'attendance_bonus' => $attBonus,
            'production_bonus' => $productionBonus,
            'emp_status' => $status,
            'stamp' => 0,
            'pay_status' => 1,
            'bank_payable' => 0,
            'tds' => 0,
            'created_by' => auth()->id()
            
        ];
        
        

        $stamp = 0;

        $salaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($employee->associate_id, $month, $year);
        $leaveAdjust = 0.00;
        if($salaryAdjust != null){
            if(isset($salaryAdjust->salary_adjust)){
                foreach ($salaryAdjust->salary_adjust as $leaveAd) {
                    $leaveAdjust += $leaveAd->amount;
                }
            }
        }

        $leaveAdjust = round($leaveAdjust, 2);
        
        // get salary payable calculation
        $salaryPayable = round(((($perDayGross*$total_day) - ($getAbsentDeduct + ($deductCost)))), 2);
        $ot = ($overtime_rate*$overtimes);

        $totalPayable = ceil((float)($salaryPayable + $ot + $deductSalaryAdd  + $productionBonus + $leaveAdjust));
        
        $salary['total_payable'] = $totalPayable;
        $salary['cash_payable'] = $totalPayable;
        $salary['salary_payable'] = $salaryPayable;
        $salary['leave_adjust'] = $leaveAdjust;


        $getSalary = HrMonthlySalary::
                    where('as_id', $employee->associate_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

        if($getSalary == null){
            DB::table('hr_monthly_salary')->insert($salary);
        }else{
            DB::table('hr_monthly_salary')->where('id', $getSalary->id)->update($salary);  
        }
        $salary['adjust'] = $leaveAdjust - $deductCost + $deductSalaryAdd + $productionBonus;
        $salary['per_day_basic'] = $perDayBasic;
        $salary['per_day_gross'] = $perDayGross;
        $salary['salary_date'] = $total_day;
        $salary['disburse_date'] = null;
        

        return $salary;
    }

    public static function getHolidayDate($getEmployee, $startDate, $endEnd)
    {
    	$dates = [];
    	if($getEmployee != null){
    		$empdoj = $getEmployee['as_doj'];
    		if($getEmployee['shift_roaster_status'] == 1){
	            // check holiday roaster employee
	            $getHoliday = HolidayRoaster::where('as_id', $getEmployee['associate_id'])
	            ->where('date','>=', $startDate)
	            ->where('date','<=', $endEnd)
	            ->where('remarks', 'Holiday')
	            ->pluck('date');
	            if(count($getHoliday) > 0){
	            	if(count($dates) > 0){
		            	array_push($dates, $getHoliday->toArray());
		            }
	            }
	            
	        }else{
	            // check holiday roaster employee
	            $rosterHolidayCount = HolidayRoaster::where('as_id', $getEmployee['associate_id'])
	            ->where('date','>=', $startDate)
	            ->where('date','<=', $endEnd)
	            ->where('remarks', 'Holiday')
	            ->pluck('date');

	            if(count($rosterHolidayCount) > 0){
	            	if(count($dates) > 0){
		            	array_push($dates, $rosterHolidayCount->toArray());
		            }
	            }

	            $getHoliday = YearlyHolyDay::
                where('hr_yhp_unit', $getEmployee['as_unit_id'])
                ->where('hr_yhp_dates_of_holidays','>=', $startDate)
                ->where('hr_yhp_dates_of_holidays','<=', $endEnd)
                ->where('hr_yhp_open_status', 0)
                ->pluck('hr_yhp_dates_of_holidays');
                if(count($getHoliday) > 0){
	            	if(count($dates) > 0){
		            	array_push($dates, $getHoliday->toArray());
		            }
	            }
	            
	        }
    	}

    	return array_unique($getHoliday->toArray());

    }


}