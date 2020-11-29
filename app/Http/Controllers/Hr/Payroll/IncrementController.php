<?php

namespace App\Http\Controllers\Hr\Payroll;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\Designation;
use App\Models\Hr\SalaryStructure;
use App\Models\Hr\Unit;
use App\Models\Hr\EmpType;
use App\Models\Employee;
use App\Models\Hr\Increment;
use App\Models\Hr\Promotion;
use App\Models\Hr\FixedSalary;
use App\Models\Hr\IncrementType;
use App\Models\Hr\OtherBenefits;
use App\Models\Hr\OtherBenefitAssign;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\SalaryAdjustDetails;
use Carbon\Carbon;
use Validator,DB, DataTables, ACL,Auth;

class IncrementController extends Controller
{
    public function index(Request $request)
    {

    	$unitList  = Unit::where('hr_unit_status', '1')
		    ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
		    ->pluck('hr_unit_name', 'hr_unit_id');
	    $floorList= [];
	    $lineList= [];
 
	    $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

	    $deptList= [];

	    $sectionList= [];

	    $subSectionList= [];

	    $data['salaryMin']      = Benefits::getSalaryRangeMin();
	    $data['salaryMax']      = Benefits::getSalaryRangeMax();


	    return view('hr.payroll.increment.index', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'data'));

    }

    public function getEligibleList(Request $request)
    {
    	$inc_month = $request->month;
    	$effective_date = Carbon::parse($request->month.'-01');
    	$range_start = $effective_date->copy()->subYear()->toDateString();
    	$range_end = $effective_date->copy()->endOfMonth()->toDateString();
    	$gazette_date = '2018-12-01';
    	$eligible_date = Carbon::parse($range_end)->endOfMonth()->toDateString();

    	$increment = DB::table('hr_increment')
    				 ->where('effective_date','>=',$range_start)
    				 ->where('effective_date','<=',$range_end)
    				 ->pluck('associate_id')->toArray();

    	
    	$gazette = DB::table('hr_as_basic_info')
    				->where('as_doj', '<=', $gazette_date)
    				->where('as_emp_type_id', 3)
    				->whereIn('as_unit_id', auth()->user()->unit_permissions())
    				->whereIn('as_location', auth()->user()->location_permissions())
    				->pluck('associate_id')->toArray();

    	$no_associate = array_merge($increment,$gazette);

    	$eligible = DB::table('hr_as_basic_info')
    				->leftJoin('hr_benefits')
    				->where('as_doj','<=',$eligible_date)
    				->whereIn('as_unit_id', auth()->user()->unit_permissions())
    				->whereIn('as_location', auth()->user()->location_permissions())
    				->whereNotIn('associate_id',$no_associate)
    				->get();

    }
}