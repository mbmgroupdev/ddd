<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB, Response;

class RetirementPolicyContorller extends Controller
{
    public function index(){
    	return view('hr.setup.retirement_policy');
    }

    public function getEmployeeList(Request $request){
    	$employees = DB::table('hr_as_basic_info')->where('as_emp_type_id', $request->emp_type_id)
    											  ->where('as_status', 1)
    											  ->select(['as_id', 'as_name'])
    											  ->get();

    	$list = '<option value="">Select Employee</option>';
    	foreach ($employees as $emp) {
    		$list .= '<option value="'.$emp->as_id.'">'.$emp->as_name.'</option>';
    	}

    	return Response::json($list);
    }

    public function getEmployeeDetails(Request $request){
    	// dd($request->all());
    	$details = DB::table('hr_as_basic_info as b')->select([
    												
    												'b.as_doj',
    												'b.as_pic',
    												'b.associate_id',
    												'b.as_gender',
    												'c.hr_unit_name',
    												'd.hr_location_name',
    												'e.hr_department_name',
    												'f.hr_designation_name'
    											])
    											->leftJoin('hr_unit as c','c.hr_unit_id','=','b.as_unit_id')
    											->leftJoin('hr_location as d','d.hr_location_id','=','b.as_location')
    											->leftJoin('hr_department as e','e.hr_department_id','=','b.as_department_id')
    											->leftJoin('hr_designation as f','f.hr_designation_id','=','b.as_designation_id')
    											->where('b.as_id','=',$request->emp_id)
    											->first();
        $date1 = strtotime($details->as_doj);
        $date2 = strtotime(date('Y-m-d'));  
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

    	return Response::json($details);

    }

    public function saveData(Request $request){
    	dd($request->all());
    }

}
