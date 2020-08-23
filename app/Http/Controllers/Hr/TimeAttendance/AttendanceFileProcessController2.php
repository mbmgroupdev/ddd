<?php

namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAttendanceIntime;
use App\Jobs\ProcessAttendanceOuttime;
use App\Jobs\ProcessEmployeeAbsent;
use App\Models\Hr\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\YearlyHolyDay;
use Carbon\Carbon;
use DB, Validator, Input, FastExcel, File;
use Illuminate\Http\Request;

class AttendanceFileProcessController2 extends Controller
{
    public function importFile(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'unit' => 'required|min:1|max:11',
            'file' => 'required',
            'device' => 'required_if:unit,==,3'
        ]);
        $input = $request->all();
        if ($validator->fails()) 
        {
            return back()
            ->withErrors($validator)
            ->withInput();
        }

        try {
            $input = $request->all();
            $data['unit'] = $input['unit'];
            $data['device'] = $input['device'];
            $fileData = file_get_contents($request->file('file'));
            $dataResult = explode(PHP_EOL, $fileData);
            $checkData = json_encode($dataResult);
            if(empty($checkData)){
                return back()->with('error', 'There is error in your file');
            }
            $dataChunk = array_chunk($dataResult, 50);
            $data['arrayDataCount'] = count($dataResult);
            $data['chunkValues'] = $dataChunk;
            return view('hr.timeattendance.att_status', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Error');
        }
    }

    public function attFileProcess(Request $request)
    {
    	$data = array();
    	$data['status'] = 'success';
    	$fileDate = array();
    	$msg = array();
    	$input = $request->all();
    	$unit = $input['unit'];
    	try {
    		foreach($input['getdata'] as $key => $value) {
    			$lineData = $value;
    			$rfid="";
                $checktime = null;
    			if(($unit==1 || $unit==4 || $unit==5 || $unit==9) && !empty($lineData) && (strlen($lineData)>1)){
    				$sl = substr($lineData, 0, 2);
    				$date   = substr($lineData, 3, 8);
    				$time   = substr($lineData, 12, 6);
    				$rfid = substr($lineData, 19, 10);
    				$checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
    			}
    			else if($unit==2 && !empty($lineData) && (strlen($lineData)>1)){
    				$sl = substr($lineData, 0, 2);
    				$date   = substr($lineData, 2, 8);
    				$rfid = substr($lineData, 16, 10);
    				$time   = substr($lineData, 10, 6);
    				$checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null); 
    			}
    			else if($unit==8  &&  !empty($lineData) && (strlen($lineData)>1)){
		            // if(strlen($lineData)>0){
    				$lineData = explode(" ", $lineData);
    				$rfid = $lineData[0];
    				$date = $lineData[1];
    				$time = $lineData[2];
    				$checktime = ((!empty($date) && !empty($time))?date("Y-m-d H:i:s", strtotime("$date $time")):null);
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
                        $msg[] = $value." - AQL device mismatch. ";
                        break;
                    } 
                }
                else if($unit==1001 && !empty($lineData) && (strlen($lineData)>1)){
                    $lineData = preg_replace('/\s+/', ' ', $lineData);
                    $valueExloade = explode(',', $lineData);
                    $dateExp = explode('/', $valueExloade[1]);
                    $dateTimeFormat = $dateExp[2].'-'.$dateExp[1].'-'.$dateExp[0].' '.$valueExloade[2];
                    $date  =  date("Y-m-d H:i:s", strtotime(str_replace("/", "-", $dateTimeFormat)));
                    $rfidNameExloade = explode('-', $valueExloade[4]);
                    $rfid = $rfidNameExloade[0];
                    $checktime = (!empty($date)?date("Y-m-d H:i:s", strtotime($date)):null);

                }else{
                	if($value != null){
                		$msg[] = $value." - Unit do not match, issue data ";
                	}
                }
                
                $today = Carbon::parse($checktime)->format('Y-m-d');
            	//get Employee Information from as_basic_info table according to the RFID
                if(strlen($rfid)>0){
                	if($unit == 3 && $input['device'] == 2){
                		$as_info = Employee::
                		where('as_id', $asId)
                		->select([
                			'as_unit_id',
                			'as_id',
                			'as_shift_id',
                			'as_ot',
                            'shift_roaster_status',
                            'associate_id'
                		])
                		->first(); 
                	}else{
                		$as_info = Employee::
                		where('as_rfid_code', $rfid)
                		->select([
                			'as_unit_id',
                			'as_id',
                			'as_shift_id',
                			'as_ot',
                            'shift_roaster_status',
                            'associate_id'
                		])
                		->first(); 
                	}
                	if(!empty($as_info) && strlen($rfid)>0 && $checktime != null){
                        $month = date('m', strtotime($checktime));
                        $year = date('Y', strtotime($checktime));
                        $today = date("Y-m-d", strtotime($checktime));

                        // leave check individual
                        $getLeave = DB::table('hr_leave')
                        ->where('leave_ass_id', $as_info->associate_id)
                        ->where('leave_from', '>=', $today)
                        ->where('leave_to', '<=', $today)
                        ->where('leave_status',1)
                        ->first();

                        if($getLeave != null){
                            $msg[] = $value." - ".$today." Leave for this employee";
                            continue;
                        }
                        $checkHolidayFlag = 0;
                        // check holiday individual 
                        $getHoliday = HolidayRoaster::getHolidayYearMonthAsIdDateWise($year, $month, $as_info->associate_id, $today);
                        if($getHoliday != null && $getHoliday->remarks == 'Holiday'){
                            $checkHolidayFlag = 1;
                            // $msg[] = $value." - ".$today." Holiday for roster this employee";
                            // continue;
                        }else if($getHoliday == null){
                            if($as_info->shift_roaster_status == 0){
                                $getYearlyHoliday = YearlyHolyDay::getCheckUnitDayWiseHoliday($as_info->as_unit_id, $today);
                                if($getYearlyHoliday != null && $getYearlyHoliday->hr_yhp_open_status == 0){
                                    $checkHolidayFlag = 1;
                                    // $msg[] = $value." - ".$today." Holiday for this employee";
                                    // continue;
                                }
                            }
                        }
                    
                    	//Select table Name as associates Unit ID
                		if($as_info->as_unit_id ==1 || $as_info->as_unit_id ==4 || $as_info->as_unit_id ==5 || $as_info->as_unit_id ==9){
                			$tableName="hr_attendance_mbm";
                		}
                		else if($as_info->as_unit_id ==2){
                			$tableName="hr_attendance_ceil";
                		}
                		else if($as_info->as_unit_id ==3){
                			$tableName="hr_attendance_aql";
                		}
                		else if($as_info->as_unit_id ==8){
                			$tableName="hr_attendance_cew";
                		}
                		else{
                			$tableName="hr_attendance_mbm";
                		}
                    	//get shift Code
                		$shift_code = null;
                		$shift_start = null;
                		$shift_end = null;
                        $unitId = $as_info->as_unit_id;
                        
                		$day_of_date = date('j', strtotime($checktime));
                		$day_num = "day_".$day_of_date;

                		$shift= DB::table("hr_shift_roaster")
                		->where('shift_roaster_month', $month)
                		->where('shift_roaster_year', $year)
                		->where("shift_roaster_user_id", $as_info->as_id)
                		->select([
                			$day_num,
                            'hr_shift.hr_shift_id',
                			'hr_shift.hr_shift_start_time',
                			'hr_shift.hr_shift_end_time',
                            'hr_shift.hr_shift_code'
                		])
                        ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                            $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                            $q->where('hr_shift.hr_shift_unit_id', $unitId);
                        })
                        ->orderBy('hr_shift.hr_shift_id', 'desc')
                		->first();
                        

                		if(!empty($shift) && $shift->$day_num != null){
                			$shift_code= $shift->hr_shift_code;
                			$shift_start= $shift->hr_shift_start_time;
                			$shift_end= $shift->hr_shift_end_time;
                		}
                		else{
                			$shift_code= $as_info->shift['hr_shift_code'];
                			$shift_start= $as_info->shift['hr_shift_start_time'];
                			$shift_end= $as_info->shift['hr_shift_end_time'];
                		}
                		// return $shift_code.' '.$shift_start.' '.$shift_end;
                		if($shift_code != null && $shift_start != null && $shift_end !=null){
                			$punch_date = date('Y-m-d', strtotime($checktime));
                			$shift_start = $punch_date." ".$shift_start;
                			$shift_end = $punch_date." ".$shift_end;
                			$shift_in_time= (int)strtotime($shift_start);
                			$shift_out_time= (int)strtotime($shift_end);
                        	// if shift end time is less than shift start time then add one day to shift end time
                			if($shift_out_time < $shift_in_time){
                				$shift_out_time= $shift_out_time+86400;
                			}
                        	//shift start range
                			$shift_start_begin= $shift_in_time-7200;
                			$shift_start_end= $shift_in_time+14399;
                        	//shift end rage
                			$shift_end_begin= $shift_start_end+1;
                			$shift_end_end= $shift_out_time+28800;
                        	//check time
                			$check_time= (int)strtotime($checktime);
                        	//get existing punch
                			$last_punch= DB::table($tableName)
                			->where('as_id', $as_info->as_id)
                			->whereDate('in_time', '=', date("Y-m-d", strtotime($checktime)))
                			->orderBy('id', "DESC")
                			->first();
                			if($shift_start_begin<= $check_time && $check_time <= $shift_start_end){
                            	//if in_time is empty insert new else do nothing
                				if(empty($last_punch->in_time) && $checkHolidayFlag == 0){
                					$punchId = DB::table($tableName)
                					->insertGetId([
                						'as_id' => $as_info->as_id,
                						'in_time' => $checktime,
                						'hr_shift_code' => $shift_code,
                						'in_unit' => $unit
                					]);
                                	// ProcessAttendanceIntime queue run
                					$queue = (new ProcessAttendanceIntime($tableName, $punchId, $unit))
                					->delay(Carbon::now()->addSeconds(2));
                					dispatch($queue);
                				}else if($checkHolidayFlag == 1){
                                    $msg[] = $value." - ".$today." Holiday for this employee";
                                    continue;
                                }
                			}
                			else if($shift_end_begin<= $check_time && $check_time <= $shift_end_end){
                				if(!empty($last_punch)){ 
                					DB::table($tableName)
                					->where('id', $last_punch->id)
                					->where('as_id', $as_info->as_id)
                					->update([
                						'out_time' => $checktime,
                						'out_unit' => $unit
                					]);
                					if($as_info->as_ot == 1){
                                    	// ProcessAttendanceOuttime queue run
                						$queue = (new ProcessAttendanceOuttime($tableName, $last_punch->id, $unit))
                						->delay(Carbon::now()->addSeconds(2));
                						dispatch($queue);
                					}
                				}
                				else{ 
                					$lastPunchId = DB::table($tableName)
                					->insertGetId([
                						'as_id' => $as_info->as_id,
                						'in_time' => date('Y-m-d H:i:s', strtotime($shift_start)),
                						'out_time' => $checktime,
                						'hr_shift_code' => $shift_code,
                						'remarks'       => "DSI",
                						'in_unit' => $unit,
                						'out_unit' => $unit
                					]);

                                	// ProcessAttendanceIntime queue run  becuse this user not found in time punch  
                					$queue = (new ProcessAttendanceIntime($tableName, $lastPunchId, $unit))
                					->delay(Carbon::now()->addSeconds(2));
                					dispatch($queue);
                				}
                			}
                			else{
                				$last_punch= DB::table($tableName)
                				->where('as_id', $as_info->as_id)
                				->whereDate('in_time', '=', date("Y-m-d", strtotime("-1 days $checktime")))
                				->orderBy('id', "DESC")
                				->first();
                				if(!empty($last_punch)){
                					DB::table($tableName)
                					->where('id', $last_punch->id)
                					->where('as_id', $as_info->as_id)
                					->update([
                						'out_time' => $checktime,
                						'out_unit' => $unit
                					]);
                					if($as_info->as_ot == 1){
                                    	// ProcessAttendanceOuttime queue run
                						$queue = (new ProcessAttendanceOuttime($tableName, $last_punch->id, $unit))
                						->delay(Carbon::now()->addSeconds(2));
                						dispatch($queue);
                					}
                				}
                				else{
                					$shift_code_new=null;
                					if($day_of_date == 1){
                						$day_of_date= date("Y-m-d", $check_time-86400);
                						$day_of_date= date('j', strtotime($day_of_date));
                						$month= $month-1;
                						if($month ==1){
                							$month=12;
                							$year= $year-1;
                						}
                						$day_num= "day_".($day_of_date);
                					}
                					else{
                						$day_num= "day_".($day_of_date-1);
                					}
                					$shift= DB::table("hr_shift_roaster")
                					->where('shift_roaster_month', $month)
                					->where('shift_roaster_year', $year)
                					->where("shift_roaster_user_id", $as_info->as_id)
                					->orderBy('shift_roaster_id', "DESC")
                					->select([
                						$day_num,
                						'hr_shift.hr_shift_start_time',
                						'hr_shift.hr_shift_end_time',
                                        'hr_shift.hr_shift_code'
                					])
                                    ->leftJoin('hr_shift', function($q) use($day_num, $unitId) {
                                        $q->on('hr_shift.hr_shift_name', 'hr_shift_roaster.'.$day_num);
                                        $q->where('hr_shift.hr_shift_unit_id', $unitId);
                                    })
                                    ->orderBy('hr_shift.hr_shift_id', 'desc')
                					->first();
                					if(!empty($shift)){
                						$shift_code_new= $shift->hr_shift_code;
                						$shift_start_new= $shift->hr_shift_start_time;
                						$shift_end_new= $shift->hr_shift_end_time;
                					}
                					else{
                						$shift_code_new= $as_info->shift['hr_shift_code'];
                						$shift_start_new= $as_info->shift['hr_shift_start_time'];
                						$shift_end_new= $as_info->shift['hr_shift_end_time'];
                					}

                					if(!empty($shift_code_new)){
                						$shift_start = $punch_date." ".$shift_start_new;
                						$shift_end = $punch_date." ".$shift_end_new;
                						$shift_in_time_new= (int)strtotime($shift_start);
                						$shift_out_time_new= (int)strtotime($shift_end);
                                    	// if shift end time is less than shift start time then add one day to shift end time
                						if($shift_out_time_new < $shift_in_time_new){
                							$shift_out_time_new= $shift_out_time_new+86400;
                						}
                                    	//shift start range
                						$shift_start_begin_new= $shift_in_time_new-7200;
                						$shift_start_end_new= $shift_in_time_new+14399;
                                    	//shift end rage
                						$shift_end_begin_new= $shift_start_end_new+1;
                						$shift_end_end_new= $shift_out_time_new+21600;
                						if($shift_end_begin_new<= $check_time && $check_time <= $shift_end_end_new){
                							$lastPunchId = DB::table($tableName)
                							->insertGetId([
                								'as_id' => $as_info->as_id,
                								'in_time' => date("Y-m-d H:i:s", strtotime($shift_start)),
                								'out_time'      => $checktime,
                								'hr_shift_code' => $shift_code_new,
                								'remarks'       => "DSI",
                								'in_unit' => $unit,
                								'out_unit' => $unit
                							]);

                                        	// ProcessAttendanceIntime queue run  becuse this user not found in time punch  
                							$queue = (new ProcessAttendanceIntime($tableName, $lastPunchId, $unit))
                							->delay(Carbon::now()->addSeconds(2));
                							dispatch($queue);
                						}
                					}
                				}
                			}

                			if($fileDate == ''){
			                	$fileDate[] = $today;
			                }else{
			                	array_push($fileDate, $today);
			                }
			                $data['date'] = array_unique($fileDate);
                		}else{
                			if($value != null){
			                	$msg[] = $value." - shift Name/shift start/shift end null ";
			                }
                		}
                	}else{
                		if($value != null){
		                	$msg[] = $value." - Basic info/rfid/checktime null ";
		                }
                	}
                }else{
                	if($value != null){
	                	$msg[] = $value." - rfid null";
	                }
                	//break;
                }
			}
            $data['msg'] = $msg;

    		return $data;
    	} catch (\Exception $e) {
    		$data['status'] = 'error';
    		$data['result'] = $e->getMessage();
    		return $data;
    	}
    }

    public function unitAbsent(Request $request)
    {
        $input = $request->all();
        if($input['unit'] ==1 || $input['unit'] ==4 || $input['unit'] ==5 || $input['unit'] ==9){
            $tableName="hr_attendance_mbm";
        }
        else if($input['unit'] ==2){
            $tableName="hr_attendance_ceil";
        }
        else if($input['unit'] ==3){
            $tableName="hr_attendance_aql";
        }
        else if($input['unit'] ==8){
            $tableName="hr_attendance_cew";
        }else{
            $tableName = '';
        }

        try {
                // process absent queue run
            $queue = (new ProcessEmployeeAbsent($tableName, $input['fileDate'], $input['unit']))
            ->delay(Carbon::now()->addSeconds(2));
            dispatch($queue);
            
            return "success";
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;   
        }
    }
}
