<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Benefits;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
    	$unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');
        $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');
        $salaryMin = Benefits::getSalaryRangeMin();
        $salaryMax = Benefits::getSalaryRangeMax();
        return view("hr.reports.analytics.index", compact("unitList","areaList", 'locationList', 'salaryMin', 'salaryMax'));
    }
}
