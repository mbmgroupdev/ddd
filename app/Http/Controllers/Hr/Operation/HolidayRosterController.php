<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\Custom;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HolidayRosterController extends Controller
{
    public function assignMulti(Request $request)
    {
    	$data['type'] = 'error';
    	$input = $request->all();
    	// return $input;
    	DB::beginTransaction();
	    $results = array();
	    try {
	        $assignDates = !empty($input['assignDates']) ? explode(',', $input['assignDates']): '';

	        foreach ($request->assigned as $associate_id) {

	          	if($assignDates != ''){
	            	$value = $this->employeeWiseRosterSave($associate_id, $assignDates, $request->type, $request->comment);
	            	$results = array_merge($results, $value);
	          	}

	        }

	        DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = $results;
	        return $data;
	    } catch (Exception $e) {
	        DB::rollback();
	        $data['message'][] = $e->getMessage();
	        return $data;
	    }
    }

    public function employeeWiseRosterSave($associate_id, $selectedDates, $type, $comment)
    {
	    DB::beginTransaction();
	    try {
	    	$results = array();
        	$getEmployee = Employee::select('as_id','shift_roaster_status', 'as_unit_id', 'as_ot')->where('associate_id', $associate_id)->first();

        	$yearMonth = date('Y-m', strtotime($selectedDates[0]));
            $lock['month'] = date('m', strtotime($yearMonth));
            $lock['year'] = date('Y', strtotime($yearMonth));
            $lock['unit_id'] = $getEmployee->as_unit_id;
            $lockActivity = monthly_activity_close($lock);
            if($lockActivity == 0){
		        foreach ($selectedDates as $selectedDate) {
		        	$flag = 1;
		        	if($getEmployee != null){

		        		$dayCheck = EmployeeHelper::employeeDateWiseStatus($selectedDate, $associate_id, $getEmployee->as_unit_id, $getEmployee->shift_roaster_status);
		        		if($type == 'OT' && $getEmployee->as_ot == 0){
	        				$type = 'Holiday';
	        			}
		        		if($type == 'Holiday'){
	        				if(in_array($dayCheck, ['open','OT'])){
	        					$flag = 0;
	        				}
	        			}elseif($type == 'General'){
	        				if(in_array($dayCheck, ['Holiday','OT'])){
	        					$flag = 0;
	        				}
	        			}else{
	        				if(in_array($dayCheck, ['Holiday','open'])){
	        					$flag = 0;
	        				}
	        			}
		        		
		        		if($flag == 0){
		        			$year = date('Y',strtotime($selectedDate));
					        $month = date('m',strtotime($selectedDate));
		        			// check shift employee and already holiday
		        			$exFlag = 0;
		        			if($getEmployee->shift_roaster_status == 0 && $type == 'Holiday'){
					        	$getDayStatus = YearlyHolyDay::getCheckUnitDayWiseHolidayStatusMulti($getEmployee->as_unit_id, date('Y-m-d', strtotime($selectedDate)), [0]);
		        				
					        	if($getDayStatus != null){
					        		
					        		DB::table('holiday_roaster')->where('date',$selectedDate)->where('as_id',$associate_id)->delete();
					        		$exFlag = 1;
					        	}
					        }
					        if($exFlag == 0){
					        	$exist = DB::table('holiday_roaster')->where('date',$selectedDate)->where('as_id',$associate_id)->first();
					          	
					          	if($exist){
					            	DB::table('holiday_roaster')->where('date',$selectedDate)->where('as_id',$associate_id)->update([
					              		'remarks'=>$type,
					              		'comment'=>$comment
					            	]);
					          	}else{
					            	DB::table('holiday_roaster')->insert([
					             		'year'=>$year,
					             		'month'=>$month,
					             		'date'=>$selectedDate,
					             		'as_id'=>$associate_id,
					             		'remarks'=>$type,
					             		'comment'=>$comment,
					             		'status'=>1
					            	]);
					          	}
					        }
		        			

				          	$today = date('Y-m-d');
				          	$yearMonth = $year.'-'.$month;
				          	if($today >= $selectedDate){
				            	// if type holiday then employee absent delete
				            	if($type == 'Holiday'){
				              		$getStatus = EmployeeHelper::employeeAttendanceAbsentDelete($associate_id, $selectedDate);
				            	}

				            	if($type == 'General'){
					              $getStatus = EmployeeHelper::employeeDayStatusCheckActionAbsent($associate_id, $selectedDate);
					              
					            }
				            	// if type OT then employee attendance OT count change
					            if($type == 'OT' || $type == 'General'){
					            	// re check attendance
              						$history = EmployeeHelper::attendanceReCalculation($getEmployee->as_id, $selectedDate);
					              	// check exists attendance
					              	$getStatus = EmployeeHelper::employeeAttendanceOTUpdate($associate_id, $selectedDate);
					              	$undecr = DB::table('hr_attendance_undeclared')
						              ->where('as_id', $getEmployee->as_id)
						              ->where('punch_date', $selectedDate)
						              ->update([
						              	'flag' => 1
						              ]);
					            }
					              	
				              	$tableName = get_att_table($getEmployee->as_unit_id);
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
				          	$results[] = $associate_id.' - '.$selectedDate.' - '.$type.' - Assign Successfully ';
		        		}else{
		        			$results[] = $associate_id.' - '.$selectedDate.' - Already '.$type;
		        		}
		        	}
			          	
		        }
		    }else{
		    	$results[] = 'Monthly salary has been locked!';
		    }

	        DB::commit();
	        return $results;
	    } catch (\Exception $e) {
	        DB::rollback();
	        $bug = $e->getMessage();
	        // return $bug;
	        return ["error"];
	    }

    }

    public function undecrlarEmployee(Request $request)
    {
    	$data['type'] = 'error';
    	$input = $request->all();

    	DB::beginTransaction();
	    $results = array();
	    try {
	        $assignDates = !empty($input['assignDates']) ? explode(',', $input['assignDates']): '';

	        foreach ($request->assigned as $associate_id) {

	          	if($assignDates != ''){
	            	$value = $this->employeeWiseRosterSave($associate_id, $assignDates, $request->type, $request->comment);
	            	$results = array_merge($results, $value);
	          	}

	        }

	        DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = $results;
	        return $data;
	    } catch (Exception $e) {
	        DB::rollback();
	        $data['message'][] = $e->getMessage();
	        return $data;
	    }
    }
}
