<?php

namespace App\Http\Controllers\Hr\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Employee;
use App\Models\Hr\HrAllGivenBenefits;
use DB, Response, Auth, Exception, DataTables;

class BenefitsCalculationController extends Controller
{
    public function index(){
    	return view('hr.payroll.benefits');
    }
    # Search Associate ID returns NAME & ID
    public function associtaeSearch(Request $request)
    {

     //dd(auth()->user()->management_permissions());
     if(auth()->user()->hasRole('power user 3')){
       $cantacces = ['power user 2','advance user 2'];
     }elseif (auth()->user()->hasRole('power user 2')) {
       $cantacces = ['power user 3','advance user 2'];
     }elseif (auth()->user()->hasRole('advance user 2')) {
       $cantacces = ['power user 3','power user 2'];
     }else{
       $cantacces = [];
     }
     $userIdNotAccessible = DB::table('roles')
               ->whereIn('name',$cantacces)
               ->leftJoin('model_has_roles','roles.id','model_has_roles.role_id')
               ->pluck('model_has_roles.model_id');

         $asIds = DB::table('users')
                  ->whereIn('id',$userIdNotAccessible)
                  ->pluck('associate_id');


        $data = [];
        if($request->has('keyword'))
        {
            $search = $request->keyword;
            $data = Employee::select("associate_id", DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS associate_name'))
                ->where(function($q) use($search) {
                    $q->where("associate_id", "LIKE" , "%{$search}%");
                    $q->orWhere("as_name", "LIKE" , "%{$search}%");
                })
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereNotIn('as_id', auth()->user()->management_permissions())
                ->whereNotIn('associate_id',$asIds)
                // ->where('as_status', 1)
                ->take(20)
                ->get();
        }

        return response()->json($data);
    }

    public function getEmployeeDetails(Request $request){
    	try{
    		// dd($request->all());
             //check if already given
            $given_benefit_data = DB::table('hr_all_given_benefits')->where('associate_id', $request->emp_id)->first();
            // dd($given_benefit_data);exit;

	    	$details = DB::table('hr_as_basic_info as b')
	    							->select([
												
												'b.*',
												'c.hr_unit_name',
												'd.hr_location_name',
												'e.hr_department_name',
												'f.hr_designation_name',
												'g.ben_current_salary',
												'g.ben_basic',

												'dd.hr_unit_name_bn',
												'dd.hr_unit_short_name',
												'dd.hr_unit_address_bn',
												'ee.hr_bn_associate_name',
												'ff.hr_department_name',
												'ff.hr_department_name_bn',
												'kk.hr_designation_name_bn'
											])
											->leftJoin('hr_unit as c','c.hr_unit_id','=','b.as_unit_id')
											->leftJoin('hr_location as d','d.hr_location_id','=','b.as_location')
											->leftJoin('hr_department as e','e.hr_department_id','=','b.as_department_id')
											->leftJoin('hr_designation as f','f.hr_designation_id','=','b.as_designation_id')
											->leftJoin('hr_benefits as g','g.ben_as_id','=','b.associate_id')

											->leftJoin('hr_unit as dd', 'dd.hr_unit_id', 'b.as_unit_id')
											->leftJoin('hr_employee_bengali as ee', 'ee.hr_bn_associate_id', 'b.associate_id')
											->leftJoin('hr_department as ff', 'ff.hr_department_id', 'b.as_department_id')
											->leftJoin('hr_designation as kk', 'kk.hr_designation_id', 'b.as_designation_id')
											
											->where('b.associate_id','=',$request->emp_id)
											->first();
	        $date1 = strtotime($details->as_doj);
            if(!empty($given_benefit_data)){
                // dd($given_benefit_data, 'sdsddsf');exit;
               $date2 = strtotime(date('Y-m-d', strtotime($given_benefit_data->created_at) ) );
               // dd($date2, date('Y-m-d', strtotime($given_benefit_data->created_at)) );exit; 

               $details->already_given = 'yes';   
               $details->given_benefit_data = $given_benefit_data;   
            }
            else{
	           $date2 = strtotime(date('Y-m-d'));

               $details->already_given = 'no';  
            }
	        // dd($date1,$date2,$details->as_doj );

	        // Formulate the Difference between two dates 
	        $diff = abs($date2 - $date1);            
	        // To get the year divide the resultant date into 
	        // total seconds in a year (365*60*60*24) 
	        $years = floor($diff / (365*60*60*24));  
	        // To get the month, subtract it with years and 
	        // divide the resultant date into 
	        // total seconds in a month (30*60*60*24) 
	        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  
	        // To get the day, subtract it with years and  
	        // months and divide the resultant date into 
	        // total seconds in a days (60*60*24) 
	        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	        $details->service_years  = $years; 
	        $details->service_months = $months;
	        $details->service_days   = $days;
	    	// dd($details);
	        // dd("Difference: ". $years.' Years '.$months.' Months '.$days.' Days');


	        //Earned Leave Section.......
	        $leaves = DB::table('hr_leave')
                ->select(
                    DB::raw("
                        YEAR(leave_from) AS year,
                        SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned
	                    ")
	                )
	                ->where('leave_status', '1')
	                ->where("leave_ass_id", $details->associate_id)
	                ->groupBy('year')
	                ->orderBy('year', 'DESC')
	                ->get();
            $total_earnedLeaves = $this->earnedLeave($leaves,$details->as_id,$details->associate_id,$details->as_unit_id);
            // dd($total_earnedLeaves);exit;
	        //Earned Leave Section End...
	        $details->total_earnedLeaves_details = $total_earnedLeaves;
	        // dd($details);


	    	return Response::json($details);

    	}catch(\Exception $e){
    		return $e->getMessage();
    	}
    	

    }

    /*get employee attendance table*/
    public function getTableName($unit)
	{
	    $tableName = "";
	    //CEIL
	    if($unit == 2){
	        $tableName= "hr_attendance_ceil AS a";
	    }
	    //AQl
	    else if($unit == 3){
	        $tableName= "hr_attendance_aql AS a";
	    }
	    // MBM
	    else if($unit == 1 || $unit == 4 || $unit == 5 || $unit == 9){
	        $tableName= "hr_attendance_mbm AS a";
	    }
	    //HO
	    else if($unit == 6){
	        $tableName= "hr_attendance_ho AS a";
	    }
	    // CEW
	    else if($unit == 8){
	        $tableName= "hr_attendance_cew AS a";
	    }
	    else{
	        $tableName= "hr_attendance_mbm AS a";
	    }
	    return $tableName;
	}

    //Get earned leave
    public function earnedLeave($leaves, $as_id, $associate_id, $unit_id)
    {
        $table = $this->getTableName($unit_id);
        $leavesForEarned = collect($leaves)->sortBy('year');

            
        $earnedLeaves = [];
        if(count($leavesForEarned)>0){
            $remainEarned = 0;
            foreach($leavesForEarned AS $yearlyLeave){
                
                $attendance = DB::table($table)
                                ->where('a.as_id',$as_id)
                                ->whereYear('a.in_time', $yearlyLeave->year)
                                ->count();

                $earnedTotal = intval($attendance/18)+$remainEarned;
                

                $enjoyed = DB::table("hr_leave")
                            ->select(
                                DB::raw("
                                    SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS enjoyed
                                ")
                            )
                            ->where("leave_ass_id", $associate_id)
                            ->where("leave_status", "1")
                            ->where(DB::raw("YEAR(leave_from)"), '=', $yearlyLeave->year)
                            ->value("enjoyed");

                $remainEarned = $earnedTotal-$enjoyed;

                $earnedLeaves[$yearlyLeave->year]['remain'] = $remainEarned;
                $earnedLeaves[$yearlyLeave->year]['enjoyed'] = $enjoyed;
                $earnedLeaves[$yearlyLeave->year]['earned'] = $earnedTotal;

            }   
        }else{
            $yearAtt = DB::table($table)
                            ->select(DB::raw('count(as_id) as att'))
                            ->where('a.as_id',$as_id)
                            ->groupBy(DB::raw('Year(in_time)'))
                            ->first();
            //dd($yearAtt);
            $earnedTotal = 0;
            if($yearAtt!= null){
                foreach ($yearAtt as $key => $att) {
                    $earnedTotal += intval($att/18);    
                }
                
            }
            $earnedLeaves[date('Y')]['remain'] = $earnedTotal;
            $earnedLeaves[date('Y')]['enjoyed'] = 0;
            $earnedLeaves[date('Y')]['earned'] = $earnedTotal;
        }

        //Total the results
        $total_earned = 0;
        $total_enjoy  = 0;
        $total_remain = 0;
        foreach ($earnedLeaves as $el) {
        	$total_earned += $el['earned'];
        	$total_enjoy  += $el['enjoyed'];
        	$total_remain += $el['remain'];
        }

        $total_earnedLeaves['total_earned'] = $total_earned; 
        $total_earnedLeaves['total_enjoy']  = $total_enjoy;
        $total_earnedLeaves['total_remain'] = $total_remain;

        return $total_earnedLeaves;  
    }


    public function saveBenefits(Request $request){
    	try{
            // dd($request->all());exit;
            $ck = HrAllGivenBenefits::storeBenefits($request->all());
            if($ck == 1){
                if($request->benefit_on == 'on_resign'){
                    $status = 2;
                }
                else if($request->benefit_on == 'on_dismiss') {
                    $status = 4;
                }
                else if($request->benefit_on == 'on_terminate') {
                    $status = 3;
                }
                else if($request->benefit_on == 'on_death') {
                    $status = 7;
                }
                //updating employee status....
                DB::table('hr_as_basic_info')
                        ->where('associate_id', $request->associate_id)
                        ->update([
                             'as_status' => $status  
                        ]);

                return 1;
            }
        }catch(\Exception $e){
            return back()->with($e->getMessage());
        }
    }

    public function givenBenefitList(){
        $unitList = DB::table('hr_unit')->pluck('hr_unit_short_name')->toArray();
        return view('hr.payroll.given_benefits_list', compact('unitList'));
    }

    public function getGivenBenefitData(Request $request){
        $data = DB::table('hr_all_given_benefits as b')
                        ->select([
                            'b.*',
                            'c.as_name',
                            'd.hr_unit_short_name as unit_name'
                        ])
                        ->leftJoin('hr_as_basic_info as c', 'c.associate_id', 'b.associate_id')
                        ->leftJoin('hr_unit as d', 'd.hr_unit_id', 'c.as_unit_id')
                        ->orderBy('b.id', 'DESC')
                        ->get();

        // dd($data);exit;
        return DataTables::of($data)->addIndexColumn()
                ->editColumn('benefit_on', function($data){
                    if($data->benefit_on == 'on_resign'){
                        return 'Resign Benefits';
                    }
                    else if($data->benefit_on == 'on_dismiss'){
                        return 'Dismiss Benefits';
                    }
                    else if($data->benefit_on == 'on_terminate'){
                        return 'Termination Benefits';
                    }
                    else if($data->benefit_on == 'on_death'){
                        return 'Death Benefits';
                    }
                })
                ->addColumn('total_amount', function($data){
                    return $data->earn_leave_amount+
                            $data->service_benefits+
                            $data->subsistance_allowance+
                            $data->notice_pay+
                            $data->termination_benefits+
                            $data->natural_death_benefits+
                            $data->on_duty_accidental_death_benefits;
                })
                ->rawColumns(['benefit_on','total_amount'])
                ->toJson();
    }

    
}
