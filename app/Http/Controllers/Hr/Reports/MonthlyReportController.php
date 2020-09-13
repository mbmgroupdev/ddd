<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Unit;
use App\Models\Hr\Leave;
use DB;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    public function index()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        return view('hr/reports/monthly_activity/index', compact('unitList','areaList'));
    }
}