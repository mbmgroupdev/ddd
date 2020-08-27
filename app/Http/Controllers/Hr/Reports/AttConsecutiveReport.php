<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Employee;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use DB, PDF;
use Illuminate\Http\Request;

class AttConsecutiveController extends Controller
{
	public function absentPresentIndex()
  {

    #-----------------------------------------------------------#
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


    return view('hr/operation/absent_or_attendance_list', compact('unitList','floorList','lineList','areaList','deptList','sectionList','subSectionList', 'data'));
  }
}