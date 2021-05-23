<?php

namespace App\Http\Controllers\Hr\Buyer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\Hr\BuyerSalaryExport;
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
use Maatwebsite\Excel\Facades\Excel;
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
            /*$ignore = 1;

            if($input['unit'] != null && $input['department'] == ''  && $input['section'] == ''){
                $ignore = 0;
            }

            if(isset($input['line'])){
                if($input['line'] == 324) $ignore = 0;
            }*/
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
           /* if($ignore == 1){
                $queryData->where( function ($q) use ($ignore){
                    return  $q->where('emp.as_line_id','!=', 324)
                        ->orWhereNull('emp.as_line_id');
                });
            }*/
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
                
                $view =  view('hr.buyer.front.salary_sheet_group', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input','buyer'))->render();
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
            return view($viewPage, compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input','buyer'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }

    public function reports()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('hr_unit_name', 'desc')
        ->pluck('hr_unit_name', 'hr_unit_id');

        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');

        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        $salaryMin = Benefits::getSalaryRangeMin();
        $salaryMax = Benefits::getSalaryRangeMax();
        return view('hr.buyer.reports.salary_index', compact('unitList','areaList', 'salaryMin', 'salaryMax', 'locationList'));
    }

    public function salaryReport(Request $request)
    {
        $input = $request->all();
        try {
            $buyer = DB::table('hr_buyer_template')->where('table_alias', auth()->user()->name)->first();
            $salarytable = 'hr_buyer_salary_'.$buyer->table_alias;

            ini_set('zlib.output_compression', 1);
            $yearMonth = explode('-', $input['month']);
            $month = $yearMonth[1];
            $year = $yearMonth[0];


            $input['area']       = isset($request['area'])?$request['area']:'';
            $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';
            $input['location'] = isset($request['location'])?$request['location']:'';

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();

            // employee benefit sql binding
            $benefitData = DB::table('hr_benefits');
            $benefitData_sql = $benefitData->toSql();

            // employee basic sql binding
            $designationData = DB::table('hr_designation');
            $designationData_sql = $designationData->toSql();

            // employee sub section sql binding
            $subSectionData = DB::table('hr_subsection');
            $subSectionDataSql = $subSectionData->toSql();

            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];

            $queryData = DB::table($salarytable.' AS s')
            ->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions());
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $queryData->where('s.as_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $queryData->where('s.year', $year)
            ->where('s.month', $month)
            ->whereBetween('s.gross', [$input['min_sal'], $input['max_sal']])
            ->when(!empty($input['unit']), function ($query) use($input){
               return $query->where('s.unit_id',$input['unit']);
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
            ->when(!empty($input['pay_status']), function ($query) use($input){
                if($input['pay_status'] == 'cash'){
                    return $query->where('s.cash_payable', '>', 0);
                }elseif($input['pay_status'] != 'all'){
                    return $query->where('s.pay_type', $input['pay_status']);
                }
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('subsec.hr_subsec_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('subsec.hr_subsec_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('s.ot_status',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('subsec.hr_subsec_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('s.subsection_id', $input['subSection']);
            })
            ->orderBy('emp.as_department_id', 'ASC');
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $designationData_sql. ') AS deg'), function($join) use ($designationData) {
                $join->on('deg.hr_designation_id','s.designation_id')->addBinding($designationData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $subSectionDataSql. ') AS subsec'), function($join) use ($subSectionData) {
                $join->on('subsec.hr_subsec_id','s.subsection_id')->addBinding($subSectionData->getBindings());
            });
           
            
            if($input['report_format'] == 1 && $input['report_group'] != null){
                $queryData->select(DB::raw('count(*) as total'), DB::raw('sum(total_payable) as groupTotal'),DB::raw('COUNT(CASE WHEN s.ot_status = 1 THEN s.ot_status END) AS ot, COUNT(CASE WHEN s.ot_status = 0 THEN s.ot_status END) AS nonot'),
                DB::raw('sum(salary_payable) as groupSalary'), DB::raw('sum(cash_payable) as groupCashSalary'),DB::raw('sum(stamp) as groupStamp'),DB::raw('sum(tds) as groupTds'), DB::raw('sum(bank_payable) as groupBankSalary'), DB::raw('sum(ot_hour) as groupOt'), DB::raw('sum(ot_hour * ot_rate) as groupOtAmount'),DB::raw("SUM(IF(ot_status=0,total_payable,0)) AS totalNonOt"),DB::raw("SUM(s.food_deduct) AS foodDeduct"));
                if($input['report_group'] == 'as_unit_id'){
                    $queryData->addSelect('s.unit_id AS as_unit_id');
                    $queryData->groupBy('s.unit_id');
                }elseif($input['report_group'] == 'as_designation_id'){
                    $queryData->addSelect('s.designation_id AS as_designation_id');
                    $queryData->groupBy('s.designation_id');
                }elseif($input['report_group'] == 'as_subsection_id'){
                    $queryData->addSelect('s.sub_section_id AS as_subsection_id');
                    $queryData->groupBy('s.sub_section_id');
                }elseif($input['report_group'] == 'as_department_id'){
                    $queryData->addSelect('subsec.hr_subsec_department_id AS as_department_id');
                    $queryData->groupBy('subsec.hr_subsec_department_id');
                }elseif($input['report_group'] == 'as_section_id'){
                    $queryData->addSelect('subsec.hr_subsec_section_id AS as_section_id');
                    $queryData->groupBy('subsec.hr_subsec_section_id');
                }else{
                    $queryData->addSelect('emp.'.$input['report_group']);
                    $queryData->groupBy('emp.'.$input['report_group']);
                }
            }else{
                $queryData->select('s.unit_id AS as_unit_id','emp.associate_id AS associate_id', 's.designation_id AS as_designation_id','subsec.hr_subsec_area_id AS as_area_id', 'subsec.hr_subsec_department_id AS as_department_id', 'subsec.hr_subsec_section_id AS as_section_id', 's.subsection_id AS as_subsection_id', 's.pay_type AS bank_name');
                $queryData->addSelect('deg.hr_designation_position','deg.hr_designation_name', 'ben.bank_no','emp.as_id','emp.as_gender', 'emp.as_oracle_code', 'emp.as_line_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 's.present', 's.absent', 's.ot_hour', 's.ot_rate', 's.total_payable','s.salary_payable', 's.bank_payable', 's.cash_payable', 's.tds', 's.stamp', 's.pay_status',DB::raw('(ot_hour * ot_rate) as otAmount'));
                
            }

            $getEmployee = $queryData->orderBy('deg.hr_designation_position', 'asc')->get();

            if($input['report_format'] == 1 && $input['report_group'] != null){
                $totalSalary = round(array_sum(array_column($getEmployee->toArray(),'groupTotal')));
                $totalCashSalary = round(array_sum(array_column($getEmployee->toArray(),'groupCashSalary')));
                $totalBankSalary = round(array_sum(array_column($getEmployee->toArray(),'groupBankSalary')));
                $totalStamp = round(array_sum(array_column($getEmployee->toArray(),'groupStamp')));
                $totalTax = round(array_sum(array_column($getEmployee->toArray(),'groupTds')));
                $totalEmployees = array_sum(array_column($getEmployee->toArray(),'total'));
                $totalOtHour = array_sum(array_column($getEmployee->toArray(),'groupOt'));
                $totalOTAmount = round(array_sum(array_column($getEmployee->toArray(),'groupOtAmount')));
            }else{
                $datas = collect($getEmployee);
                $totalSalary = round($datas->sum("total_payable"));
                $totalCashSalary = round($datas->sum("cash_payable"));
                $totalBankSalary = round($datas->sum("bank_payable"));
                $totalStamp = round($datas->sum("stamp"));
                $totalTax = round($datas->sum("tds"));
                $totalOtHour = ($datas->sum("ot_hour"));
                $totalOTAmount = round($datas->sum('otAmount'));

                $totalEmployees = count($getEmployee);
            }

            if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
                $getEmployeeArray = $getEmployee->toArray();
                $formatBy = array_column($getEmployeeArray, $request['report_group']);
                $uniqueGroups = array_unique($formatBy);
                if (!array_filter($uniqueGroups)) {
                    $uniqueGroups = ['all'];
                    $format = '';
                }
            }
            $uniqueGroupEmp = [];
            if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
                $uniqueGroupEmp = collect($getEmployee)->groupBy($request['report_group'],true);
                
            }
            // dd($uniqueGroupEmp);
            if($input['pay_status'] == null){

                $view = view('hr.reports.monthly_activity.salary.report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalSalary', 'totalEmployees', 'totalOtHour','totalOTAmount', 'totalCashSalary', 'totalBankSalary', 'totalTax', 'totalStamp'))->render();
            }else{
                $view = view('hr.reports.monthly_activity.salary.report_payment_wise', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalSalary', 'totalEmployees', 'totalOtHour','totalOTAmount', 'totalCashSalary', 'totalBankSalary', 'totalTax', 'totalStamp', 'uniqueGroupEmp'))->render();
            }
            return $view;
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }

    public function salaryReportExcel(Request $request)
    {
        $input = $request->all();
        return Excel::download(new BuyerSalaryExport($input), 'salary.xlsx');
    }

    public function groupSalary(Request $request)
    {
        $input = $request->all();
        try {

            $buyer = DB::table('hr_buyer_template')->where('table_alias', auth()->user()->name)->first();
            $salarytable = 'hr_buyer_salary_'.$buyer->table_alias;

            $yearMonth = explode('-', $input['month']);
            $month = $yearMonth[1];
            $year = $yearMonth[0];

            $input['area']       = isset($request['area'])?$request['area']:'';
            $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();

            // employee benefit sql binding
            $benefitData = DB::table('hr_benefits');
            $benefitData_sql = $benefitData->toSql();

            // employee basic sql binding
            $designationData = DB::table('hr_designation');
            $designationData_sql = $designationData->toSql();

            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];

            $queryData = DB::table($salarytable.' AS s')
            ->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->where('emp.'.$input['report_group'], $input['selected']);
            $queryData->where('s.year', $year)
            ->where('s.month', $month)
            ->whereBetween('s.gross', [$input['min_sal'], $input['max_sal']])
            ->when(!empty($input['unit']), function ($query) use($input){
               return $query->where('emp.as_unit_id',$input['unit']);
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('emp.as_location',$input['location']);
            })
            ->when(!empty($input['employee_status']), function ($query) use($input){
                if($input['employee_status'] == 25){
                    return $query->whereIn('s.emp_status', [2,5]);
                }else{
                   return $query->where('s.emp_status', $input['employee_status']);

                }
            })
            ->when(!empty($input['pay_status']), function ($query) use($input){
                if($input['pay_status'] == "cash"){
                    return $query->where('ben.ben_cash_amount', '>', 0);
                }elseif($input['pay_status'] != 'cash' && $input['pay_status'] != 'all'){
                    return $query->where('ben.bank_name',$input['pay_status']);
                }
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            })
            ->orderBy('emp.as_department_id', 'ASC');
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $designationData_sql. ') AS deg'), function($join) use ($designationData) {
                $join->on('deg.hr_designation_id','emp.as_designation_id')->addBinding($designationData->getBindings());
            });

            $queryData->select('deg.hr_designation_position','deg.hr_designation_name', 'ben.bank_name','ben.bank_no', 'ben.ben_tds_amount','emp.as_id','emp.as_gender', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_unit_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_section_id', 's.present', 's.absent', 's.ot_hour', 's.ot_rate', 's.total_payable','s.salary_payable', 's.bank_payable', 's.cash_payable', 's.tds', 's.stamp', 's.pay_status');
            $totalSalary = round($queryData->sum("s.total_payable"));
            $totalCashSalary = round($queryData->sum("s.cash_payable"));
            $totalBankSalary = round($queryData->sum("s.bank_payable"));
            $totalStamp = round($queryData->sum("s.stamp"));
            $totalTax = round($queryData->sum("s.tds"));
            $totalOtHour = ($queryData->sum("s.ot_hour"));
            $totalOTAmount = round($queryData->sum(DB::raw('s.ot_hour * s.ot_rate')));

            $getEmployee = $queryData->orderBy('deg.hr_designation_position', 'asc')->get();
            
            $totalEmployees = count($getEmployee);
            $auditedEmployee = [];
            
            return view('hr.reports.monthly_activity.salary.group_salary_details', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalSalary', 'totalEmployees', 'totalOtHour','totalOTAmount', 'totalCashSalary', 'totalBankSalary', 'totalTax', 'totalStamp', 'auditedEmployee'));
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }

    public function payslip(Request $request)
    {

        $data = $request->all();
        $unitList      = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_name', 'desc')
            ->pluck('hr_unit_name', 'hr_unit_id');
        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');

         $areaList = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        
        $salaryMin      = Benefits::getSalaryRangeMin();
        $salaryMax     = Benefits::getSalaryRangeMax();
        $getYear      = HrMonthlySalary::select('year')->distinct('year')->orderBy('year', 'desc')->pluck('year');

        $floorList= DB::table('hr_floor')
                        ->where('hr_floor_unit_id', $request->unit)
                        ->pluck('hr_floor_name', 'hr_floor_id');

        $deptList= DB::table('hr_department')
                        ->where('hr_department_area_id', $request->area)
                        ->pluck('hr_department_name', 'hr_department_id');
        $sectionList= DB::table('hr_section')
                        ->where('hr_section_department_id', $request->department)
                        ->pluck('hr_section_name', 'hr_section_id');


        $subSectionList= DB::table('hr_subsection')
                        ->where('hr_subsec_section_id', $request->section)
                        ->pluck('hr_subsec_name', 'hr_subsec_id');

        return view("hr.buyer.front.payslip_index", compact(
            "data",
            "unitList",
            "areaList",
            "floorList",
            "deptList",
            "sectionList",
            "subSectionList",
            "getYear",
            "locationList",
            "salaryMin",
            "salaryMax"
        ));
    }

    public function unitWisePayslip(Request $request)
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

            if($input['unit'] != null && $input['department'] == ''  && $input['section'] == ''){
                $ignore = 0;
            }

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
            
            $perPage = $input['perpage']??4;
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


                $view =  view('hr.buyer.front.buyer_pay_slip', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input','buyer'))->render();
           
            return response(['view' => $view]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }
}
