<?php

namespace App\Exports\Hr;

use App\Models\Hr\HrMonthlySalary;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalaryExport implements FromView, WithHeadingRow
{
	use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function view(): View
    {
    	$input = $this->data;
    	$yearMonth = explode('-', $input['month']);
        $month = $yearMonth[1];
        $year = $yearMonth[0];
        $input['area']       = isset($input['area'])?$input['area']:'';
        $input['otnonot']    = isset($input['otnonot'])?$input['otnonot']:'';
        $input['department'] = isset($input['department'])?$input['department']:'';
        $input['line_id']    = isset($input['line_id'])?$input['line_id']:'';
        $input['floor_id']   = isset($input['floor_id'])?$input['floor_id']:'';
        $input['section']    = isset($input['section'])?$input['section']:'';
        $input['subSection'] = isset($input['subSection'])?$input['subSection']:'';

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
        $format = $input['report_group'];
        $uniqueGroups = ['all'];

        $queryData = DB::table('hr_monthly_salary AS s')
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
            // if($input['pay_status'] == "cash"){
            //     return $query->where('s.pay_status', 1);
            // }elseif($input['pay_status'] != 'cash' && $input['pay_status'] != 'all'){
            //     return $query->where('s.pay_status', 1)->where('ben.bank_name',$input['pay_status']);
            // }
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
        ->when($input['otnonot']!=null, function ($query) use($input){
           return $query->where('s.ot_status',$input['otnonot']);
        })
        ->when(!empty($input['section']), function ($query) use($input){
           return $query->where('subsec.hr_subsec_section_id', $input['section']);
        })
        ->when(!empty($input['subSection']), function ($query) use($input){
           return $query->where('s.sub_section_id', $input['subSection']);
        })
        ->orderBy('emp.as_department_id', 'ASC');
        $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
            $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
        });
        $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
            $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
        });
        $queryData->leftjoin(DB::raw('(' . $designationData_sql. ') AS deg'), function($join) use ($designationData) {
            $join->on('deg.hr_designation_id','s.designation_id')->addBinding($designationData->getBindings());
        });
        $queryData->leftjoin(DB::raw('(' . $subSectionDataSql. ') AS subsec'), function($join) use ($subSectionData) {
            $join->on('subsec.hr_subsec_id','s.sub_section_id')->addBinding($subSectionData->getBindings());
        });
        // salary add deduct
        $addDeductData = DB::table('hr_salary_add_deduct');
        $addDeductDataSql = $addDeductData->toSql();
        $queryData->leftjoin(DB::raw('(' . $addDeductDataSql. ') AS deduct'), function($join) use ($addDeductData) {
            $join->on('s.salary_add_deduct_id','deduct.id')->addBinding($addDeductData->getBindings());
        });
        
        if($input['report_format'] == 1 && $input['report_group'] != null){
            $queryData->select(DB::raw('count(*) as total'), DB::raw('sum(total_payable) as groupTotal'),DB::raw('COUNT(CASE WHEN s.ot_status = 1 THEN s.ot_status END) AS ot, COUNT(CASE WHEN s.ot_status = 0 THEN s.ot_status END) AS nonot'),
            DB::raw('sum(salary_payable) as groupSalary'), DB::raw('sum(cash_payable) as groupCashSalary'),DB::raw('sum(stamp) as groupStamp'),DB::raw('sum(tds) as groupTds'), DB::raw('sum(bank_payable) as groupBankSalary'), DB::raw('sum(ot_hour) as groupOt'), DB::raw('sum(ot_hour * ot_rate) as groupOtAmount'),DB::raw("SUM(IF(ot_status=0,total_payable,0)) AS totalNonOt"),DB::raw("SUM(deduct.food_deduct) AS foodDeduct"));
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
            $queryData->select('s.unit_id AS as_unit_id','s.as_id AS associate_id', 's.designation_id AS as_designation_id','subsec.hr_subsec_area_id AS as_area_id', 'subsec.hr_subsec_department_id AS as_department_id', 'subsec.hr_subsec_section_id AS as_section_id', 's.sub_section_id AS as_subsection_id', 's.pay_type AS bank_name');
            $queryData->addSelect('deg.hr_designation_position','deg.hr_designation_name', 'ben.bank_no','emp.as_id','emp.as_gender', 'emp.as_oracle_code','emp.as_doj', 'emp.as_line_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 's.present', 's.absent', 's.ot_hour', 's.ot_rate', 's.total_payable','s.salary_payable', 's.bank_payable', 's.cash_payable', 's.tds', 's.stamp', 's.pay_status','s.gross', 's.basic', 's.house', 's.medical', 's.transport', 's.food');
            $totalSalary = round($queryData->sum("s.total_payable"));
            $totalCashSalary = round($queryData->sum("s.cash_payable"));
            $totalBankSalary = round($queryData->sum("s.bank_payable"));
            $totalStamp = round($queryData->sum("s.stamp"));
            $totalTax = round($queryData->sum("s.tds"));
            $totalOtHour = ($queryData->sum("s.ot_hour"));
            $totalOTAmount = round($queryData->sum(DB::raw('s.ot_hour * s.ot_rate')));
        }

        $getEmployee = $queryData->orderBy('deg.hr_designation_position', 'asc')->get();
        // dd($getEmployee);
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
            $totalEmployees = count($getEmployee);
        }
        
        if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
            $getEmployeeArray = $getEmployee->toArray();
            $formatBy = array_column($getEmployeeArray, $input['report_group']);
            $uniqueGroups = array_unique($formatBy);
            if (!array_filter($uniqueGroups)) {
                $uniqueGroups = ['all'];
                $format = '';
            }
        }
        $uniqueGroupEmp = [];
        if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
            $uniqueGroupEmp = collect($getEmployee)->groupBy($input['report_group'],true);
            
        }
        return view('hr.reports.monthly_activity.salary.excel', compact('uniqueGroupEmp','uniqueGroups', 'format', 'getEmployee', 'input', 'totalSalary', 'totalEmployees', 'totalOtHour','totalOTAmount', 'totalCashSalary', 'totalBankSalary', 'totalTax', 'totalStamp'));
    }
    public function headingRow(): int
    {
        return 3;
    }
}
