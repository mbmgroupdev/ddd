<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\BonusRule;
use App\Models\Hr\Location;
use DB;
use Illuminate\Http\Request;

class BonusSheetController extends Controller
{
	public function __construct()
	{
		ini_set('zlib.output_compression', 1);
	}

	public function index()
	{
		$bonusSheet = BonusRule::getApprovalGroupBonusList();
		$bonusSheet = collect($bonusSheet)->pluck('text', 'id');
    	$unitList = unit_by_id();
		$unitList = collect($unitList)->pluck('hr_unit_name', 'hr_unit_id');
		$locationList = location_by_id();
		$locationList = collect($locationList)->pluck('hr_location_name', 'hr_location_id');
		$areaList = area_by_id();
		$areaList = collect($areaList)->pluck('hr_area_name', 'hr_area_id');
		$salaryMin = 0;
		$salaryMax = Benefits::getSalaryRangeMax();
		return view('hr.reports.bonus.index', compact('bonusSheet', 'unitList', 'locationList', 'areaList', 'salaryMin', 'salaryMax'));
	}

    public function report(Request $request)
    {
    	$input = $request->all();
    	try {
    		
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';

            // group unit and location set
            $location = location_by_id();
            $unit = unit_by_id();
            $line = line_by_id();
            $floor = floor_by_id();
            $department = department_by_id();
            $designation = designation_by_id();
            $section = section_by_id();
            $subsection = subSection_by_id();
            $area = area_by_id();
            $bonusType = bonus_type_by_id();

            if(isset($input['group_unit'])){
            	$groupUnit = $input['group_unit'];
            	$getLocation = Location::getUnitWiseLocation($input['group_unit']);
            	$groupLocation = $getLocation->hr_location_id;
            	if($input['group_unit'] == 1){
            		$groupUnit = collect($unit)->pluck('hr_unit_id');
            		$groupLocation = [6,8,10,12,13,14];
            	}
            }
            
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
            $bonusSheet = BonusRule::findOrFail($input['sheet_id']);
            $queryData = DB::table('hr_bonus_sheet AS s')
            ->whereNotIn('s.associate_id', config('base.ignore_salary'));
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $queryData->where('s.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $queryData->where('s.bonus_rule_id', $input['sheet_id']);
            if(isset($input['group_unit'])){
            	$queryData->whereIn('s.unit_id', $groupUnit)
            			  ->whereIn('s.location_id', $groupLocation);
            }else{
            	$queryData->whereIn('s.unit_id', auth()->user()->unit_permissions())
            			  ->whereIn('s.location_id', auth()->user()->location_permissions());
            }

            $queryData->whereBetween('s.gross_salary', [$input['min_sal'], $input['max_sal']])
            ->when(!empty($input['unit']), function ($query) use($input){
               return $query->where('s.unit_id',$input['unit']);
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('s.location_id',$input['location']);
            })
            ->when(!empty($input['emp_type'] && $input['emp_type'] != 'all'), function ($query) use($input){
            	if($input['emp_type'] == 'lessyear'){
            		return $query->where('s.duration', '<', 12);
            	}elseif($input['emp_type'] == 'partial'){
                    return $query->where('s.type', 'partial');
                }elseif($input['emp_type'] == 'special'){
                    return $query->where('s.type', 'special');
                }
            	$status = 6;
            	if($input['emp_type'] == 'active'){
            		$status = 1;
            	}
                return $query->where('s.emp_status', $status);
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
            ->when(!empty($input['selected']), function ($query) use($input){
            	
                if($input['selected'] == 'null'){
                    return $query->whereNull($input['report_group']);
                }else{
                    return $query->where('emp.'.$input['report_group'], $input['selected']);
                }
            })
            ->orderBy('emp.as_department_id', 'ASC');
            if(!empty($input['selected'])){
            	$input['report_format'] = 0;
            }
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','s.associate_id')->addBinding($employeeData->getBindings());
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
            // dd($input);
            $queryGet = clone $queryData;
            
            if(($input['report_format'] == 1 || $input['report_format'] == 2) && $input['report_group'] != null){
                $queryData->select(DB::raw('count(*) as total'), DB::raw('sum(net_payable) as groupTotal'),DB::raw('COUNT(CASE WHEN s.ot_status = 1 THEN s.ot_status END) AS ot, COUNT(CASE WHEN s.ot_status = 0 THEN s.ot_status END) AS nonot'), DB::raw('sum(cash_payable) as groupCashSalary'),DB::raw('sum(stamp) as groupStamp'), DB::raw('sum(bank_payable) as groupBankSalary'), DB::raw("SUM(IF(ot_status=0,net_payable,0)) AS totalNonOt"), DB::raw("SUM(IF(ot_status=1,net_payable,0)) AS totalOt"));
                if($input['report_group'] == 'as_unit_id'){
                    $queryData->addSelect('s.unit_id AS as_unit_id');
                    $queryData->groupBy('s.unit_id');
                }elseif($input['report_group'] == 'as_designation_id'){
                    $queryData->addSelect('s.designation_id AS as_designation_id');
                    $queryData->groupBy('s.designation_id');
                }elseif($input['report_group'] == 'as_subsection_id'){
                    $queryData->addSelect('s.subsection_id AS as_subsection_id');
                    $queryData->groupBy('s.subsection_id');
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
                $queryData->select('s.unit_id AS as_unit_id','s.associate_id', 's.designation_id AS as_designation_id','subsec.hr_subsec_area_id AS as_area_id', 'subsec.hr_subsec_department_id AS as_department_id', 'subsec.hr_subsec_section_id AS as_section_id', 's.subsection_id AS as_subsection_id','deg.hr_designation_position','deg.hr_designation_name', 'ben.bank_no','emp.as_id','emp.as_gender', 'emp.as_oracle_code', 'emp.as_line_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_doj', 's.net_payable', 's.bank_payable', 's.cash_payable', 's.stamp', 's.pay_status', 's.gross_salary', 's.basic', 's.duration', 's.bonus_amount');
            }

            $getEmployee = $queryData->orderBy('s.bonus_amount', 'desc')->get();
            
            if($input['report_format'] == 1 && $input['report_group'] != null){
                $totalAmount = round(array_sum(array_column($getEmployee->toArray(),'groupTotal')));
                $totalCashSalary = round(array_sum(array_column($getEmployee->toArray(),'groupCashSalary')));
                $totalBankSalary = round(array_sum(array_column($getEmployee->toArray(),'groupBankSalary')));
                $totalStamp = round(array_sum(array_column($getEmployee->toArray(),'groupStamp')));
                $totalEmployees = array_sum(array_column($getEmployee->toArray(),'total'));
               
            }else{
                $datas = collect($getEmployee);
                $totalAmount = round($datas->sum("net_payable"));
                $totalCashSalary = round($datas->sum("cash_payable"));
                $totalBankSalary = round($datas->sum("bank_payable"));
                $totalStamp = round($datas->sum("stamp"));
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

            $summary 	= $this->makeSummary($queryGet->get());
            
            $view = view('hr.reports.bonus.report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalAmount', 'totalEmployees','totalCashSalary', 'totalBankSalary', 'totalStamp', 'uniqueGroupEmp', 'location', 'unit', 'area', 'department', 'designation', 'section', 'subsection','summary', 'bonusType', 'bonusSheet'))->render();
            return $view;
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }
    protected function makeSummary($data)
    {
    	$data = collect($data);
        $sum  = (object)[];
        $sum->maternity         = $data->where('emp_status', 6)->count();
        $sum->maternity_amount  = $data->where('emp_status', 6)->sum('bonus_amount');
        $sum->active            = $data->where('emp_status', 1)->count();
        $sum->active_amount     = $data->where('emp_status', 1)->sum('bonus_amount');
        $sum->ot                = $data->where('ot_status', 1)->count();
        $sum->ot_amount         = $data->where('ot_status', 1)->sum('bonus_amount');
        $sum->nonot             = $data->where('ot_status', 0)->count();
        $sum->nonot_amount      = $data->where('ot_status', 0)->sum('bonus_amount');
        $sum->partial           = $data->where('duration','<' ,12)->count();
        $sum->partial_amount    = $data->where('duration','<' ,12)->sum('bonus_amount');
        $sum->stamp             = $data->sum('stamp');
        $cash = $data->where('cash_payable', '>', 0);
        $sum->cash_emp          = $cash->count();
        $sum->cash_amount       = $cash->sum('cash_payable');

         
        $group = collect($data)
                    ->whereIn('pay_status', [2,3])
                    ->groupBy('bank_name', true)
                    ->map(function($q){
                        $p = (object)[];
                        $p->emp = collect($q)->count();
                        $p->amount = collect($q)->sum('bank_payable');
                        return $p;
                    })->all();
        $sum->payment_group = $group;
       

        return $sum;
    }

    public function audit(Request $request)
    {
    	$input = $request->all();
    	// return $input;
    	$data['type'] = 'error';
    	try {
    		if($input['status'] == 1){
    			$bonusRule = BonusRule::findOrFail($input['id']);
    			$bonusRule->update([
    				'approved_at' => date('Y-m-d H:i:s'),
    				'approved_by' => auth()->user()->id
    			]);
    		}

    		$data['type'] = 'success';
            $data['message'] = 'Process Successfully Done';
            $data['url'] = url('hr/payroll/bonus-sheet-process');
            return $data;
    	} catch (\Exception $e) {
    		DB::rollback();
            $data['message'] = $e->getMessage();
            return $data;
    	}
    }
}
