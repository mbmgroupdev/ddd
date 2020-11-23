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
    	
    }
}