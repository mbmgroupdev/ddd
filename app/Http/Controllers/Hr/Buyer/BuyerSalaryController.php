<?php

namespace App\Http\Controllers\Hr\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Employee;
use App\Models\Hr\Area;
use App\Models\Hr\Benefits;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Location;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\Hr\YearlyHolyDay;

use Carbon\Carbon;
use DB;

class BuyerSalaryController extends Controller
{
    public function index()
    {
        
        try {
            $data['unitList']      = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->orderBy('hr_unit_name', 'desc')
                ->pluck('hr_unit_name', 'hr_unit_id');
            
            $data['locationList']  = Location::where('hr_location_status', '1')
	            ->whereIn('hr_location_id', auth()->user()->location_permissions())
	            ->orderBy('hr_location_name', 'desc')
	            ->pluck('hr_location_name', 'hr_location_id');

            $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
            $data['floorList']     = Floor::getFloorList();
            $data['salaryMin']      = Benefits::getSalaryRangeMin();
            $data['salaryMax']      = Benefits::getSalaryRangeMax();

            return view('hr.buyer.front.salary_index', $data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function unitWise(Request $request)
    {
        $buyer = DB::table('hr_buyer_template')->where('table_alias', auth()->user()->name)->first();
    	$salarytable = 'hr_buyer_salary_'.$buyer->table_alias;

        $input = $request->all();
        $input['unit'] = $input['unit']??'';
        $input['department'] = $input['department']??'';
        $input['section'] = $input['section']??'';
        $input['subSection'] = $input['subSection']??'';
        $input['month'] = date('m', strtotime($input['month_year']));
        $input['year'] = date('Y', strtotime($input['month_year']));
        
        try {
            ini_set('zlib.output_compression', 1);
            // ignore line
            $ignore = 1;

            if(isset($input['line'])){
                if($input['line'] == 324) $ignore = 0;
            }
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
            if(isset($input['subSection'])){
                $info['sub_sec'] = Subsection::where('hr_subsec_id',$input['subSection'])->first()->hr_subsec_name_bn??'';
            }
            // employee info
            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();
            
            // employee bang la info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

            // employee sub section sql binding
            $subSectionData = DB::table('hr_subsection');
            $subSectionDataSql = $subSectionData->toSql();

            $queryData = DB::table($salarytable.' as s')
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->where('s.year', $input['year'])
            ->where('s.month', $input['month'])
            ->whereBetween('s.gross', [$input['min_sal'], $input['max_sal']])
            //->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->when(!empty($input['unit']), function ($query) use($input){
                if(!in_array($input['unit'], [14,145,15])){
                    return $query->where('s.unit_id',$input['unit']);
                }else{
                    if($input['unit'] == 14)
                        $unit = [1,4];
                    else if($input['unit'] == 145)
                        $unit = [1,4,5];
                    else if($input['unit'] == 15)
                        $unit = [1,5];
                    else
                        $unit = [];

                    return $query->whereIn('s.unit_id',$unit);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('s.location_id',$input['location']);
            })
            ->when(!empty($input['employee_status']), function ($query) use($input){
                if($input['employee_status'] == 25){
                    return $query->whereIn('s.emp_status', [2,5]);
                }else{
                   return $query->where('s.emp_status', $input['employee_status']);

                }
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('subsec.hr_subsec_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('subsec.hr_subsec_department_id',$input['department']);
            })
            ->when(!empty($input['line']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line']);
            })
            ->when(!empty($input['floor']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('subsec.hr_subsec_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('s.subsection_id', $input['subSection']);
            });
            if($ignore == 1){
                $queryData->where( function ($q) use ($ignore){
                    return  $q->where('emp.as_line_id','!=', 324)
                        ->orWhereNull('emp.as_line_id');
                });
            }
            if(isset($input['otnonot']) && $input['otnonot'] != null){
                $queryData->where('s.ot_status',$input['otnonot']);
            }
            if(isset($input['disbursed']) && $input['disbursed'] != null){
                if($input['disbursed'] == 1){
                    $queryData->where('s.disburse_date', '!=', null);
                }else{
                    $queryData->where('s.disburse_date', null);
                }
            }
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $subSectionDataSql. ') AS subsec'), function($join) use ($subSectionData) {
                $join->on('subsec.hr_subsec_id','s.subsection_id')->addBinding($subSectionData->getBindings());
            });
            
            if(!empty($input['pay_status'])){
                // employee benefit sql binding
                $benefitData = DB::table('hr_benefits');
                $benefitData_sql = $benefitData->toSql();
                $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                    $join->on('ben.ben_as_id','s.as_id')->addBinding($benefitData->getBindings());
                });
                if($input['pay_status'] == "cash"){
                    $queryData->where('s.cash_payable', '>', 0);
                }elseif($input['pay_status'] != 'all'){
                    $queryData->where('s.pay_type',$input['pay_status']);
                }
            }
            

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });

                
            $queryData->select('s.*','emp.associate_id', 'emp.as_doj', 's.unit_id AS as_unit_id','s.location_id AS as_location', 's.designation_id AS as_designation_id', 's.ot_status AS as_ot', 'emp.as_section_id', 's.location_id', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code',DB::raw('s.ot_hour * s.ot_rate as ot_amount'), 'subsec.hr_subsec_area_id AS as_area_id', 'subsec.hr_subsec_department_id AS as_department_id', 'subsec.hr_subsec_section_id AS as_section_id');
            $getSalaryList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->get();
            // dd($getSalaryList);
            $totalSalary = round($getSalaryList->sum("total_payable"));
            $totalCashSalary = round($getSalaryList->sum("cash_payable"));
            $totalBankSalary = round($getSalaryList->sum("bank_payable"));
            $totalStamp = round($getSalaryList->sum("stamp"));
            $totalTax = round($getSalaryList->sum("tds"));
            $totalOtHour = ($getSalaryList->sum("ot_hour"));
            $totalOTAmount = round($getSalaryList->sum("ot_amount"));
            $totalEmployees = count($getSalaryList);
            // return $totalEmployees;
            $employeeAssociates = collect($getSalaryList)->pluck('associate_id')->toArray();

            // salary adjust
            $salaryAddDeduct = DB::table('hr_salary_add_deduct')
                ->where('year', $input['year'])
                ->where('month', $input['month'])
                ->whereIn('associate_id', $employeeAssociates)
                ->get()->keyBy('associate_id')->toArray();
            // employee designation
            $designation = designation_by_id();
            $getSection = section_by_id();
            // return $designation;

            $locationDataSet = $getSalaryList->toArray();
            // return $locationDataSet;
            if($input['unit'] != null){
                $locationList = array_column($locationDataSet, 'unit_id');
                $uniqueLocation = array_unique($locationList);
            }elseif($input['unit'] == null){
                $locationList = array_column($locationDataSet, 'unit_id');
                $uniqueLocation = array_unique($locationList);
            }
            
            $perPage = $input['perpage']??6;
            $locationDataSet = array_chunk($locationDataSet, $perPage, true);
            // dd($uniqueLocatiosn);
            // $title = $getUnit->hr_unit_name_bn;
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['unit_name']      = $getUnit->hr_unit_name_bn??'';
            $pageHead['for_date']       = $input['month_year'];
            $pageHead['floor_name']     = $input['floor']??'';
            $pageHead['month']     = $input['month'];
            $pageHead['year']     = $input['year'];
            $pageHead['totalSalary'] = $totalSalary;
            $pageHead['totalOtHour'] = $totalOtHour;
            $pageHead['totalOTAmount'] = $totalOTAmount;
            $pageHead['totalStamp'] = $totalStamp;
            $pageHead['totalEmployees'] = $totalEmployees;
            $pageHead = (object)$pageHead;

            /*if($input['unit'] == null){*/
                
                $view =  view('hr.buyer.front.salary_sheet_group', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input'))->render();
            /*}else{
                $view = view('hr.operation.salary.salary_sheet_group', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input'))->render();
            }*/

            return response(['view' => $view]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }

    public function employeeWise(Request $request)
    {
        $buyer = DB::table('hr_buyer_template')->where('table_alias', auth()->user()->name)->first();
        $salarytable = 'hr_buyer_salary_'.$buyer->table_alias;

        $input = $request->all();
        $input['month'] = date('m', strtotime($input['emp_month_year']));
        $input['year'] = date('Y', strtotime($input['emp_month_year']));
        try {

            $info = [];
            
            // employee info
            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

            // employee sub section sql binding
            $subSectionData = DB::table('hr_subsection');
            $subSectionDataSql = $subSectionData->toSql();

            $queryData = DB::table($salarytable.' as s')
            ->where('s.year', $input['year'])
            ->where('s.month', $input['month'])
            ->whereIn('emp.associate_id', $input['as_id']);
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $subSectionDataSql. ') AS subsec'), function($join) use ($subSectionData) {
                $join->on('subsec.hr_subsec_id','emp.as_subsection_id')->addBinding($subSectionData->getBindings());
            });
                
            $getSalaryList = $queryData->select('s.*','emp.associate_id', 'emp.as_doj', 's.ot_status AS as_ot','emp.as_oracle_code', 's.designation_id AS as_designation_id', 'emp.as_location','s.unit_id AS as_unit_id', 'bemp.hr_bn_associate_name', 'subsec.hr_subsec_area_id AS as_area_id', 'subsec.hr_subsec_department_id AS as_department_id', 'subsec.hr_subsec_section_id AS as_section_id')->get();
            // dd($getSalaryList);
            $employeeAssociates = $queryData->select('emp.associate_id')->pluck('emp.associate_id')->toArray();
            // salary adjust
            $salaryAddDeduct = DB::table('hr_salary_add_deduct')
                ->where('year', $input['year'])
                ->where('month', $input['month'])
                ->whereIn('associate_id', $employeeAssociates)
                ->get()->keyBy('associate_id')->toArray();
            // employee designation
            $designation = designation_by_id();
            $getSection = section_by_id();
            // return $designation;

            $locationDataSet = $getSalaryList->toArray();
            // return $locationDataSet;
            $locationList = array_column($locationDataSet, 'as_location');
            $uniqueLocation = array_unique($locationList);
            $locationDataSet = array_chunk($locationDataSet, 5, true);
            // $title = $getUnit->hr_unit_name_bn;
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['unit_name']      = '';
            $pageHead['for_date']       = $input['emp_month_year'];
            $pageHead['floor_name']     = '';
            $pageHead['month']     = $input['month'];
            $pageHead['year']     = $input['year'];
            $pageHead = (object)$pageHead;
            /*if($input['formattype'] == 0){*/
                $viewPage = 'hr.buyer.front.salary_sheet_single';
            /*}else{
                $viewPage = 'hr.operation.salary.load_salary_sheet';
            }*/
            return view($viewPage, compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }
}
