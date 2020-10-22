<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use DB, Validator;
use Illuminate\Http\Request;
class SalarySheetGenerateController extends Controller
{
    public function index()
    {
    	try {
            $data['unitList']      = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->pluck('hr_unit_name', 'hr_unit_id');
            $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
            $data['floorList']     = Floor::getFloorList();
            $data['deptList']      = Department::getDeptList();
            $data['sectionList']   = Section::getSectionList();
            $data['subSectionList'] = Subsection::getSubSectionList();
        	return view('hr.reports.salary_generate.index', $data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function process(Request $request)
    {
    	$validator = Validator::make($request->all(),[
            'unit'           => 'required',
            'month'          => 'required',
            'year'           => 'required',
            // 'disbursed_date' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        if($input['unit'] ==1 || $input['unit']==4 || $input['unit']==5 || $input['unit']==9){
            $tableName="hr_attendance_mbm";
        } else if($input['unit'] ==2){
            $tableName="hr_attendance_ceil";
        } else if($input['unit'] ==3){
            $tableName="hr_attendance_aql";
        } else if($input['unit'] ==6){
            $tableName="hr_attendance_ho";
        } else if($input['unit'] ==8){
            $tableName="hr_attendance_cew";
        } else{
            $tableName="hr_attendance_mbm";
        }
        $input['employee_status'] = 1;
        $getEmployees = Employee::getEmployeeFilterWise($input)->toArray();
        $getData = array_column($getEmployees, 'as_id');
        $chunkValues = array_chunk($getData, 50);
        $yearMonth = $input['year'].'-'.$input['month']; 
        if($input['month'] == date('m')){
        	$totalDay = date('d');
        }else{
        	$totalDay = Carbon::parse($yearMonth)->daysInMonth;
        }
        $data['unit'] = $input['unit'];
        $data['month'] = $input['month'];
        $data['year'] = $input['year'];
        $data['table'] = $tableName;
        $data['totalDay'] = $totalDay;

        return view('hr.reports.salary_generate.process', compact('chunkValues', 'data'));
    }

    public function processSalary(Request $request)
    {
    	$input = $request->all();
    	try {
    		foreach($input['getdata'] as $key => $asId) {
	    		$queue = (new ProcessUnitWiseSalary($input['data']['table'], $input['data']['month'], $input['data']['year'], $asId, $input['data']['totalDay']))
	                ->onQueue('salarygenerate')
	                ->delay(Carbon::now()->addSeconds(2));
	                dispatch($queue);
	    	}
	    	return "success";
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		return $bug;
    	}
    }
}
