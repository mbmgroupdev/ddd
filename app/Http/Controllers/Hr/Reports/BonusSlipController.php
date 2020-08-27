<?php

namespace App\Http\Controllers\Hr\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Location;
use App\Models\Hr\Floor;
use App\Models\Hr\Unit;
use App\Models\Hr\BonusType;
use App\Models\Hr\EmployeeBonusSheet;
use DB, PDF, DateTime, Response ;

class BonusSlipController extends Controller
{
    public function showForm(Request $request)
    {


    	$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
    	$deptList= Department::pluck('hr_department_name', 'hr_department_id');

    	if(!empty($request)){

    		//request with all field 
    		if(!empty($request->unit_id) && !empty($request->floor_id) && !empty($request->festive_name) && !empty($request->year) && !empty($request->department_id) && !empty($request->last_join_date)){

    			$info= DB::table('hr_as_basic_info AS b')
    					->where('b.as_unit_id', $request->unit_id)
    					->where('b.as_floor_id', $request->floor_id)
    					->where('b.as_department_id', $request->department_id)
    					->where('b.as_doj', "<=", $request->last_join_date)
    					->where('b.as_status', 1)
    					->select([
    						'b.associate_id',
    						'b.as_doj',
    						'b.as_name',
    						'b.temp_id',
    						'empb.hr_bn_associate_name',
    						'd.hr_designation_name_bn'
    					])
    					->leftJoin('hr_employee_bengali AS empb', 'empb.hr_bn_associate_id', 'b.associate_id')
    					->leftJoin('hr_designation AS d', 'd.hr_designation_id', 'b.as_designation_id')
                        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
    					->get();
    			
    		}
    		else{

    			$info= DB::table('hr_as_basic_info AS b')
    					->where('b.as_unit_id', $request->unit_id)
    					->where('b.as_floor_id', $request->floor_id)
    					->where('b.as_doj', "<=", date('Y-m-d',strtotime($request->last_join_date)))
    					->where('b.as_status', 1)
    					->select([
    						'b.associate_id',
    						'b.as_doj',
                            'b.as_name',
    						'b.temp_id',
    						'empb.hr_bn_associate_name',
                            'd.hr_designation_name_bn'
    					])
    					->leftJoin('hr_employee_bengali AS empb', 'empb.hr_bn_associate_id', 'b.associate_id')
                        ->leftJoin('hr_designation AS d', 'd.hr_designation_id', 'b.as_designation_id')
                        ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
    					->get();
    			// dd($info);
    		}
            
    		if(!empty($info)){
    			$todayDate= date('d-m-Y');
    			$sl=1;
    			foreach($info AS $emp){
    				$emp->sl = $sl;
    				$sl++;
    				$jobDuration=0;
    				$basic=0;
    				$salary=0;
    				$bonus=0;
    				$jobDurationRatio=0;
    				


    				$from= date('d-m-Y', strtotime($emp->as_doj));
    				$to= date('d-m-Y', strtotime($todayDate));

    				$fromDay= date('d', strtotime($from));
    				$toDay= date('d', strtotime($to));

    				$fromMonth= date('m',strtotime($from));
    				$toMonth= date('m',strtotime($to));

    				$fromYear= date('Y', strtotime($from));
    				$toYear= date('Y', strtotime($to));
    				//only month difference between ctoday's date and joining date
    				$jobDuration= (($toYear-$fromYear)*12)+($toMonth-$fromMonth);
    				//exact month duration calculating according to days 
    				if($toDay>$fromDay) $jobDuration++;

    				

    				//-- Calculating bonus according salary
    				
    				$ben_salary= DB::table('hr_benefits')
    									->where('ben_as_id', $emp->associate_id)
    									->where('ben_status', 1)
    									->orderBy('ben_id', 'DESC')
    									->select([
    										'ben_basic',
    										'ben_current_salary'
    									])
    									->first();
    				if(!empty($ben_salary)){
    					$salary= $ben_salary->ben_current_salary;
    					$basic= round($ben_salary->ben_basic,2);
    				}
    				else{
    					$salary=0;
    					$basic=0;
    				}
    				//calculation bonus according job duration
    				if($jobDuration>=12){
    					$bonus= $basic;
    				}
    				else{
    					$bonus= round((($basic/12)*$jobDuration),2);
    				}
    				//jobduation /12 ration
    				if($jobDuration>=12){
    					$jobDurationRatio=null;
    				}
    				else{
    					$jobDurationRatio=  $jobDuration."/12";
    				}

    				$status_check= DB::table('hr_leave')
    								->where('leave_ass_id', $emp->associate_id)
    								->where('leave_type', "Maternity")
    								->where('leave_status', 1)
    								->where('leave_from', '<=', $todayDate)
    								->where('leave_to', ">=", $todayDate)
    								->exists();
    				if($status_check){
    					$status= "M";
    				}
    				else{
    					$status= "A";
    				}


    				
    				$emp->jobDuration = $jobDuration;
    				$emp->basic = $basic;
    				$emp->salary = $salary;
    				$emp->bonus = $bonus;
    				$emp->jobDurationRatio = $jobDurationRatio;
    				$emp->status = $status;

    				
    			}
    		}
    		//unit, floor, department, lastjoin date, festival name
    		$unit_name= Unit::where('hr_unit_id', $request->unit_id)
    						->pluck('hr_unit_name_bn')
    						->first();
    		$floor_name= Floor::where('hr_floor_id', $request->floor_id)
    						->pluck('hr_floor_name_bn')
    						->first();
    		if(!empty($request->department_id)){

    			$department_name= Department::where('hr_department_id', $request->department_id)
    							->pluck('hr_department_name_bn')
    							->first();
    		}
    		else{
    			$department_name=null;
    		}
    		$last_join_date= $request->last_join_date;

    		if($request->festive_name == 1){
    			$festive_name= "ঈদ-উল-ফিতর-".$request->year;
    		}
    		else{
    			$festive_name= "ঈদ-উল-আযহা-".$request->year;
    		}
    		$other_info= (object)[];
    		$other_info->unit_name = $unit_name;
    		$other_info->floor_name = $floor_name;
    		$other_info->department_name = $department_name;
    		$other_info->last_join_date = $last_join_date;
    		$other_info->festive_name = $festive_name;

            $floorList= DB::table('hr_floor')
                            ->where('hr_floor_unit_id', $request->unit_id)
                            ->pluck('hr_floor_name', 'hr_floor_id');
    	}


        if ($request->get('pdf') == true) { ;
            $pdf = PDF::loadView('hr/reports/bonus_slip_pdf', [
                'other_info' => $other_info,
                'info' => $info,
            ]);
            return $pdf->download('Bonus_Slip_Report_'.date('d_F_Y').'.pdf');
        }


    	return view('hr/reports/bonus_slip', compact('unitList', 'deptList', 'info','other_info', 'floorList'));
    }


    //new show form -----------------------------------------
    public function newShowForm(Request $request)
    {
        

        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');

        $deptList= Department::pluck('hr_department_name', 'hr_department_id');

        $bonus_types = BonusType::select('id','bonus_type_name')->get();

        $floorList= DB::table('hr_floor')
                            ->where('hr_floor_unit_id', $request->unit_id)
                            ->pluck('hr_floor_name', 'hr_floor_id');



        return view('hr/reports/new_bonus_slip', compact('unitList', 'deptList','floorList', 'bonus_types'));
    }


    public function generate(Request $request)
    {
        $content = '';
        //request with all field 
        if(!empty($request->unit_id) && !empty($request->bonus_process_date) && !empty($request->bonus_type_id)){

            $condition = [
                'as_unit_id' => $request->unit_id,
                'as_status' => 1
            ];

            $other_info = [
                'unit' => Unit::where('hr_unit_id', $request->unit_id)->first()->hr_unit_name_bn??'',
                'bonus_process_date' => $request->bonus_process_date
            ];

            if(isset($request->floor_id)){
                $condition['as_floor_id'] = $request->floor_id;
                $other_info['floor'] = Floor::where('hr_floor_id',$request->floor_id)->first()->hr_floor_name_bn??'';
            }
            if(isset($request->department_id)){
                $condition['as_department_id'] = $request->department_id;
                $other_info['department'] = Department::where('hr_department_id',$request->department_id)->first()->hr_department_name_bn??'';
            }

            // values from library
            $library = [
                'cut_off_day' => 90, // 3 months 
            ];

            $eligible_date = date('Y-m-d', strtotime("-".($library['cut_off_day'])." days", strtotime($request->bonus_process_date)));

            


            $employees = Employee::with('benefits')
                        ->where($condition)
                        ->where('as_doj', "<=", $eligible_date)
                        ->where('associate_id','!=','17F005001B')
                        ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                        ->orderBy('associate_id', 'asc')
                        ->get();
            $associate_ids = $employees->pluck('associate_id')->toArray();

            // check first bonus already generated or not

            $bonus_check = EmployeeBonusSheet::where([
                'unit_id' => $request->unit_id,
                'bonus_type_id' => $request->bonus_type_id
            ])
            ->whereIn('associate_id', $associate_ids)
            ->get()->keyBy('associate_id');

            $bonus_lib = BonusType::find($request->bonus_type_id);
            //dd($bonus_lib);

            $other_info['bonus'] = $bonus_check->sum('bonus_amount');
            $other_info['employee'] = count($bonus_check);
            $other_info['join_date'] = $eligible_date;
            $other_info['bonus_lib'] = $bonus_lib;



            //dd(count($associate_ids),count($bonus_check), $associate_ids);
            if(count($associate_ids) == count($bonus_check) && ($bonus_lib->bonus_process_date == $request->bonus_process_date) ){
                $info = $bonus_check;
            }else{

                $info = []; $other_info['bonus'] = 0; $other_info['employee'] = 0;

                foreach ($employees as $key => $emp) {

                     
                    if($emp->benefits){
                        $ben_basic = $emp->benefits['ben_basic']??0;
                    }else{
                        $ben_basic = 0;
                    }

                    $job_duration = $emp->job_duration($request->bonus_process_date);
                   

                    if($job_duration < 12){
                        $bonus = ceil(((( $ben_basic * $bonus_lib->percent_of_basic)/100.0)/12)*$job_duration);
                    }else{
                        $bonus = ceil(($ben_basic * $bonus_lib->percent_of_basic)/100.0);
                    }



                    // this could be conditional
                    //$insert['stamp_price'] = 10;

                    // increment total and employee count
                    
                    $other_info['employee'] += 1;

                    if(isset($bonus_check[$emp->associate_id])){
                        // minimize redundancy
                        // bonus from database
                        if($bonus_check[$emp->associate_id]['bonus_amount'] == $bonus){
                            $other_info['bonus'] += $bonus_check[$emp->associate_id]['bonus_amount'];
                            $info[$emp->associate_id] = $bonus_check[$emp->associate_id];      
                        }else{
                            $other_info['bonus'] += $bonus;

                            $sheet = EmployeeBonusSheet::find($bonus_check[$emp->associate_id]['id']);
                            $sheet->bonus_amount = $bonus;
                            $sheet->save();
                            $info[$emp->associate_id] = $sheet;
                        }

                    }else{
                        $other_info['bonus'] += $bonus;

                        $sheet = new EmployeeBonusSheet();
                        $sheet->unit_id = $request->unit_id;
                        $sheet->bonus_type_id = $request->bonus_type_id;
                        $sheet->associate_id = $emp->associate_id;
                        $sheet->bonus_amount = $bonus;
                        $sheet->gross_salary = $emp->benefits['ben_current_salary']??0;
                        $sheet->basic = $ben_basic;
                        $sheet->duration = $job_duration;
                        $sheet->stamp_price = 10;
                        $sheet->save();

                        $info[$emp->associate_id] = $sheet;

                    }

                }
                $lib_update = $bonus_lib->update([
                    'bonus_process_date' => $request->bonus_process_date
                ]);
            }
            
            //dd($info);

            // insert bonus data into bonus sheet
            //$test = DB::table('hr_bonus_sheet')->insertIgnore($info);
            $location = Location::get()->keyBy('hr_location_id');
            $location_wise = $employees->groupBy('as_location');
            
            $content =  view('hr.common.employee_bonus_sheet', compact('location_wise','location','info','other_info'))->render();
            
            
        }
        //dd($info['00B101319N']);
            
        /*if ($request->get('pdf') == true) { 
            $pdf = PDF::loadView('hr/reports/bonus_slip_pdf', [
                'other_info' => $other_info,
                'info' => $info,
            ]);
            return $pdf->download('Bonus_Slip_Report_'.date('d_F_Y').'.pdf');
        }*/


        return $content;
        

    }

    public function disburse(Request $request)
    {
        $disburse = EmployeeBonusSheet::find($request->id);

        if($disburse->associate_id == $request->associate_id){
            $disburse->update([
                'disburse_date' => date('Y-m-d')
            ]);

        }

        return 'success';
    }



    //get floor list by unit
    public function floorByUnit(Request $request){
    	$floorlist= Floor::where('hr_floor_unit_id', $request->unit)
    					->select([
    						'hr_floor_name', 
    						'hr_floor_id'
    					])
    					->get();

    	$data= '<option value="">Select Floor</option>';
    	if($floorlist){
	    	foreach ($floorlist as $floor) {
	    		$data.='<option value="'.$floor->hr_floor_id.'">'.$floor->hr_floor_name.'</option>';
	    	}
    	}
    	else{
    		$data= '<option value="">No Floor found</option>';
    	}
    	return $data;
    }


    public function btMonthYear(Request $req){

        $bonus_type = BonusType::where('id', $req->bt_id)
                                ->select(['month', 'year'])
                                ->first();
        // dd($bonus_type);
        $bonus_type->ck_month = $bonus_type->month;            
        $dateObj   = DateTime::createFromFormat('!m', $bonus_type->month);
        $bonus_type->month = $dateObj->format('F'); 

        return Response::json($bonus_type);

    }
}
