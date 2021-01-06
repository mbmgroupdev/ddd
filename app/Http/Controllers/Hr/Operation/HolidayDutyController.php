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

class HolidayDutyController extends Controller
{
	public function index()
	{
		$unitList = permitted_unit_short();
		return view('hr.operation.holiday_duty.index',compact('unitList'));
	}


	public function getData(Request $request)
	{
		if(isset($request->unit)){
			$unit = $request->unit;
		}else{
			$unit = auth()->user()->unit_permissions();
		}
		$employees = DB::table('hr_as_basic_info')
					 ->where('as_doj', '>', $request->substitute_date)
					 ->where('as_emp_type', 3)
					 ->whereIn('as_location', auth()->user()->location_permissions())
					 ->whereIn('as_unit_id', $unit)
					 ->pluck('as_id');

		
 		return view('hr.operation.holiday_duty.index',compact('unitList'));
	}
}