<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\Custom;
use App\Helpers\EmployeeHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Employee;
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
	        // $subDates = !empty($input['subDates']) ? explode(',', $input['subDates']): '';

	        foreach ($request->assigned as $associate_id) {
	          	if($assignDates != ''){
	            	$value = $this->employeeWiseRosterSave($associate_id, $assignDates, $request->type, $request->comment);
	            	$results = array_merge($results, $value);
	            	// return $result;
	          	}

	          	// if(($input['subtype'] != null) && ($subDates != '')){
	          	//   $result = $this->employeeWiseRosterSave($associate_id, $subDates, $request->subtype, $request->subcomment);
	          	// }
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
	        foreach ($selectedDates as $selectedDate) {
	        	$getEmployee = Employee::select('as_id','shift_roaster_status', 'as_unit_id', 'as_ot')->where('associate_id', $associate_id)->first();
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
	        			
	        			$exist = DB::table('holiday_roaster')->where('date',$selectedDate)->where('as_id',$associate_id)->first();
			          	$year = date('Y',strtotime($selectedDate));
			          	$month = date('m',strtotime($selectedDate));
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

			          	$today = date('Y-m-d');
			          	$yearMonth = $year.'-'.$month;
			          	if($today > $selectedDate){
			            	$modifyFlag = 0;
			            	// if type holiday then employee absent delete
			            	if($type == 'Holiday'){
			              		$getStatus = EmployeeHelper::employeeAttendanceAbsentDelete($associate_id, $selectedDate);
			              		if($getStatus == 'success'){
			                		$modifyFlag = 1;
		              			}
			            	}
			            	// if type OT then employee attendance OT count change
				            if($type == 'OT' || $type == 'General'){
				              	// check exists attendance
				              	$getStatus = EmployeeHelper::employeeAttendanceOTUpdate($associate_id, $selectedDate);
				              	if($getStatus == 'success'){
				                	$modifyFlag = 1;
				              	}
				            }

				            if($modifyFlag == 1){
				              	
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
			          	}
			          	$results[] = $associate_id.' - '.$selectedDate.' - '.$type.' - Assign Successfully ';
	        		}else{
	        			$results[] = $associate_id.' - '.$selectedDate.' - Already '.$type;
	        		}
	        	}
		          	
	        }

	        DB::commit();
	        return $results;
	    } catch (\Exception $e) {
	        DB::rollback();
	        $bug = $e->getMessage();
	        return "error";
	    }

    }
}
