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
	     // return $input;
	     try {
	        $assignDates = !empty($input['assignDates']) ? explode(',', $input['assignDates']): '';
	        // $subDates = !empty($input['subDates']) ? explode(',', $input['subDates']): '';

	        foreach ($request->assigned as $associate_id) {
	          	if($assignDates != ''){
	            	$result = $this->employeeWiseRosterSave($associate_id, $assignDates, $request->type, $request->comment);
	            	// return $result;
	          	}

	          	// if(($input['subtype'] != null) && ($subDates != '')){
	          	//   $result = $this->employeeWiseRosterSave($associate_id, $subDates, $request->subtype, $request->subcomment);
	          	// }
	        }
	        DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = 'Holiday Roaster Saved Successfully';
	        return $data;
	    } catch (Exception $e) {
	        DB::rollback();
	        $data['message'] = $e->getMessage();
	        return $data;
	    }
    }

    public function employeeWiseRosterSave($associate_id, $selectedDates, $type, $comment)
    {
	    DB::beginTransaction();
	    try {
	        foreach ($selectedDates as $selectedDate) {
	        	$getSiEmployee = Employee::select('as_id','shift_roaster_status', 'as_unit_id')->where('associate_id', $associate_id)->first();
	        	$flag = 0;
	        	if($getSiEmployee != null){
	        		if($getSiEmployee->shift_roaster_status == 0){
	        			if($type == 'Holiday'){
	        				$openS = 0;
	        			}elseif($type == 'General'){
	        				$openS = 1;
	        			}else{
	        				$openS = 2;
	        			}
	        			$holidayPlanner = DB::table('hr_yearly_holiday_planner')
	        			->where('hr_yhp_dates_of_holidays', $selectedDate)
	        			->where('hr_yhp_unit', $getSiEmployee->as_unit_id)
	        			->where('hr_yhp_status', 1)
	        			->where('hr_yhp_open_status', $openS)
	        			->first();
	        			if($holidayPlanner != null){
	        				$flag = 1;
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
				              	$getEmployee = Employee::getEmployeeAssIdWiseSelectedField($associate_id, ['as_id', 'as_unit_id']);
				              	$tableName = Custom::unitWiseAttendanceTableName($getEmployee->as_unit_id);
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
	        		}
	        	}
		          	
	        }

	        DB::commit();
	        return "success";
	    } catch (\Exception $e) {
	        DB::rollback();
	        $bug = $e->getMessage();
	        return "error";
	    }

    }
}
