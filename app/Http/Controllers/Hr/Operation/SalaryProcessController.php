<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hr\Area;
use App\Models\Hr\Benefits;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Helpers\Custom;
use Auth, DB;

class SalaryProcessController extends Controller
{
	public function index()
    {
        // try {
        //     $data['unitList']      = Unit::where('hr_unit_status', '1')
        //         ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        //         ->pluck('hr_unit_name', 'hr_unit_id');
        //     $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        //     $data['floorList']     = Floor::getFloorList();
        //     $data['deptList']      = Department::getDeptList();
        //     $data['sectionList']   = Section::getSectionList();
        //     $data['subSectionList'] = Subsection::getSubSectionList();
        //     $data['salaryMin']      = Benefits::getSalaryRangeMin();
        //     $data['salaryMax']      = Benefits::getSalaryRangeMax();
        //     $data['getYear']       = HrMonthlySalary::select('year')->distinct('year')->orderBy('year', 'desc')->pluck('year');
        //     return view('hr.operation.salary.index', $data);
        // } catch(\Exception $e) {
        //     return $e->getMessage();
        // }
    }
    public function unitWise(Request $request)
    {
    	$input = $request->all();
    	$input['department'] = $input['department']??'';
    	$input['section'] = $input['section']??'';
    	$input['subSection'] = $input['subSection']??'';
    	$input['month'] = date('m', strtotime($input['month_year']));
    	$input['year'] = date('Y', strtotime($input['month_year']));
    	// return $input;
    	try {
            $getUnit = Unit::getUnitNameBangla($input['unit']);
    		$info = [];
            if(isset($input['area'])){
                $info['area'] = Area::where('hr_area_id',$input['area'])->first()->hr_area_name_bn??'';
            }
            if(isset($input['floor'])){
                $info['floor'] = Floor::where('hr_floor_id',$input['floor'])->first()->hr_floor_name_bn??'';
            }
            if(isset($input['department'])){
                $info['department'] = Department::where('hr_department_id',$input['department'])->first()->hr_department_name_bn??'';
            }
            if(isset($input['section'])){
                $info['section'] = Section::where('hr_section_id',$input['section'])->first()->hr_section_name_bn??'';
            }
            if(isset($input['sub_section'])){
                $info['sub_section'] = Subsection::where('hr_subsec_id',$input['sub_section'])->first()->hr_subsec_name_bn??'';
            }
            // employee info
    		$employeeData = DB::table('hr_as_basic_info');
	        $employeeDataSql = $employeeData->toSql();
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

	        $queryData = DB::table('hr_monthly_salary as s')
            ->where('s.year', $input['year'])
            ->where('s.month', $input['month'])
            ->whereBetween('s.gross', [$input['min_sal'], $input['max_sal']])
            ->where('emp.as_unit_id', $input['unit'])
            ->where('emp.as_status', $input['employee_status'])
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['line']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line']);
            })
            ->when(!empty($input['floor']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor']);
            })
            ->when(!empty($input['otnonot']), function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            });
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
	            
	        $getSalaryList = $queryData->select('s.*', 'emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_location', 'bemp.hr_bn_associate_name')->get();

            $employeeAssociates = $queryData->select('emp.associate_id')->pluck('emp.associate_id')->toArray();
            // salary adjust
            $salaryAddDeduct = DB::table('hr_salary_add_deduct')
                ->where('year', $input['year'])
                ->where('month', date('n', strtotime($input['month'])))
                ->whereIn('associate_id', $employeeAssociates)
                ->get()->keyBy('associate_id')->toArray();
            // employee designation
            $designation = designation_by_id();
            // return $designation;

            $locationDataSet = $getSalaryList->toArray();
            // return $locationDataSet;
            $locationList = array_column($locationDataSet, 'as_location');
            $uniqueLocation = array_unique($locationList);
            $locationDataSet = array_chunk($locationDataSet, 5, true);
            // $title = $getUnit->hr_unit_name_bn;
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['unit_name']      = $getUnit->hr_unit_name_bn;
            $pageHead['for_date']       = Custom::engToBnConvert($input['month'].' - '.$input['year']);
            $pageHead['floor_name']     = $input['floor'];
            $pageHead['month']     = $input['month'];
            $pageHead['year']     = $input['year'];
            $pageHead = (object)$pageHead;
            return view('hr.operation.salary.load_salary_sheet', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation'));
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		return $bug;
    	}
    }
}
