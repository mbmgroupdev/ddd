<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Exports\Hr\SalaryExport;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Location;
use App\Models\Hr\SalaryAudit;
use App\Models\Hr\SalaryIndividualAudit;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use DB, DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class MonthlyActivityReportController extends Controller
{
    public function salary()
    {
        if(auth()->user()->hasRole('Buyer Mode')){
            return redirect('hrm/reports/monthly-salary');
        }
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
        return view('hr/reports/monthly_activity/salary.index', compact('unitList','areaList', 'salaryMin', 'salaryMax', 'locationList'));
        
    }

    public function salaryReport(Request $request)
    {
        $input = $request->all();
        try {
            ini_set('zlib.output_compression', 1);
            $yearMonth = explode('-', $input['month']);
            $month = $yearMonth[1];
            $year = $yearMonth[0];

            // $auditFlag = 1;
            // $audit['unit_id'] = $input['unit'];
            // $audit['year'] = $year;
            // $audit['month'] = $month;
            // $salaryStatus = SalaryAudit::checkSalaryAuditStatus($audit);
            
            // if($salaryStatus == null){
            //     $auditFlag = 0;
            // }else{
            //     if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null){
            //         $auditFlag = 0;
            //     }
            // }
            
            // if($auditFlag == 0){
            //     return '<div class="iq-card-body"><h2 class="text-red text-center">Monthly Salary Of '.date('M Y', strtotime($input['month'])).' Not Generate Yet!</h2></div>';
            // }

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

            // employee sub section sql binding
            $subSectionData = DB::table('hr_subsection');
            $subSectionDataSql = $subSectionData->toSql();

            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];

            $queryData = DB::table('hr_monthly_salary AS s')
            ->whereNotIn('s.as_id', config('base.ignore_salary'));
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $queryData->where('s.as_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $queryData->where('s.year', $year)
            ->where('s.month', $month)
            ->whereIn('s.unit_id', auth()->user()->unit_permissions())
            ->whereIn('s.location_id', auth()->user()->location_permissions())
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
            ->when($request['otnonot']!=null, function ($query) use($input){
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
            
            if(($input['report_format'] == 1 || $input['report_format'] == 2)  && $input['report_group'] != null){
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
            if( $input['report_format'] == 2){
                $format = $request['report_group'];
                $unit = unit_by_id();
                $line = line_by_id();
                $floor = floor_by_id();
                $department = department_by_id();
                $designation = designation_by_id();
                $section = section_by_id();
                $subSection = subSection_by_id();
                $area = area_by_id();
                $location = location_by_id();

                if($format == 'as_unit_id'){
                    $head = 'Unit';
                    $gp = $unit;
                    $dt = 'hr_unit_name';
                }elseif($format == 'as_line_id'){
                    $gp = $line;
                    $dt = 'hr_line_name';
                    $head = 'Line';
                }elseif($format == 'as_floor_id'){
                    $head = 'Floor';
                    $dt = 'hr_floor_name';
                    $gp = $floor;
                }elseif($format == 'as_department_id'){
                    $head = 'Department';
                    $dt = 'hr_department_name';
                    $gp = $department;
                }elseif($format == 'as_designation_id'){
                    $head = 'Designation';
                    $gp = $designation;
                    $dt = 'hr_designation_name';
                }elseif($format == 'as_section_id'){
                    $head = 'Section';
                    $gp = $section;
                    $dt = 'hr_section_name';
                }elseif($format == 'as_subsection_id'){
                    $head = 'Sub Section';
                    $dt = 'hr_subsec_name';
                    $gp = $subSection;
                }

                $emps = collect($getEmployee)->keyBy($request['report_group']);
                $chart_data = [];
                foreach ($emps as $key => $v) {
                    $name = $gp[$key][$dt]??'';
                    $chart_data[$name ] = $v->groupTotal;
                    
                }
                $vx['ct'] = array_keys($chart_data);
                $vx['dt'] = array_values($chart_data);
                $vx['hd'] = $head;

                return response($vx);

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

    public function salaryReportModal(Request $request)
    {
        $data['type'] = 'error';
        $input = $request->all();
        try {
            if($input['as_id'] == null){
                $data['message'] = 'Employee Id Not Found!';
                return $data;
            }
            if(isset($input['year']) && $input['year'] != null){
                $year = $input['year'];
            }else{
                $year = date('Y');
            }
            // get yearly report
            $getData = HrMonthlySalary::getYearlySalaryMonthWise($input['as_id'], $year);
            $activity = '';
            if(count($getData) == 0){
                $activity.= '<tr>';
                $activity.= '<td colspan="2" class="text-center"> No Data Found! </td>';
                $activity.= '</tr>';
            }else{
                foreach ($getData as $el) {
                    $activity.= '<tr>';
                    $activity.='<td>'.date("F", mktime(0, 0, 0, $el->month, 1)).'</td>';
                    $activity.='<td>'.$el->total_payable.'</td>';
                    $activity.= '</tr>';
                }
            }
            $data['value'] = $activity;
            $data['type'] = 'success';
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    public function empSalaryModal(Request $request)
    {
        $input = $request->all();
        // return $input;
        try {
            $data['as_id'] = $input['as_id'];
            $data['month'] = date('m', strtotime($input['year_month']));
            $data['year'] = date('Y', strtotime($input['year_month']));
            $salary = HrMonthlySalary::getEmployeeSalaryWithMonthWise($data);
            // salary adjust
            $salaryAddDeduct = DB::table('hr_salary_add_deduct')
                ->where('year', $data['year'])
                ->where('month', $data['month'])
                ->where('associate_id', $data['as_id'])
                ->get()->keyBy('associate_id')->toArray();
            $firstDateMonth = $data['year'].'-'.$data['month'].'-01';
            $lastDateMonth = Carbon::parse($firstDateMonth)->endOfMonth()->toDateString();

            $salaryIncrement = DB::table('hr_increment')
            ->select('associate_id','increment_amount')
            ->where('associate_id', $data['as_id'])
            ->where('effective_date','>=',$firstDateMonth)
            ->where('effective_date','<=', $lastDateMonth)
            ->get()->keyBy('associate_id')->toArray();

            // salary adjustment 
            $salaryAdjust = DB::table('hr_salary_adjust_master AS m')
            ->select(DB::raw("concat(IFNULL(d.date, ''),' ',IFNULL(d.comment, '')) as data"),'m.associate_id','d.*')
            ->where('m.month', $data['month'])
            ->where('m.year', $data['year'])
            ->where('m.associate_id', $data['as_id'])
            ->leftjoin('hr_salary_adjust_details AS d', 'm.id', 'd.salary_adjust_master_id')
            ->get()
            ->groupBy('associate_id', true)
            ->map(function($q){
                return collect($q)->groupBy('type')
                        ->map(function($p){
                            $s = (object) array();
                            $s->sum = collect($p)->sum('amount');
                            $s->days = implode(',', collect($p)->pluck('data')->toArray());

                            return $s;
                        });
            });
            // dd($salary->employee['as_doj']);
            return view('hr.reports.monthly_activity.salary.employee-single-salary', compact('salary', 'salaryIncrement', 'salaryAdjust', 'salaryAddDeduct'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }
    public function salaryAudit(Request $request)
    {
        $input = $request->all();
        if($input['month'] != null && $input['unit'] != null){

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

            return view('hr/reports/monthly_activity/salary/audit', compact('unitList','areaList', 'salaryMin', 'salaryMax', 'input','locationList'));
        }else{
            toastr()->error('Something Wrong!');
            return back();
        }
        
    }

    public function attendance()
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
        return view('hr/reports/monthly_activity/attendance.index', compact('unitList','areaList', 'salaryMin', 'salaryMax', 'locationList'));
    }

    public function attendanceData(Request $request)
    {
        ini_set('zlib.output_compression', 1);
        $input = $request->all();
        if($input['unit'] == '' && $input['location'] == ''){
            $input['unit'] = 1;
        }
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
        $input['shift_roaster_status'] = isset($request['shift_roaster_status'])?$request['shift_roaster_status']:'';

        $getDesignation = designation_by_id();
        $getDepartment = department_by_id();
        $section = section_by_id();
        $floor = floor_by_id();
        $line = line_by_id();
        // employee basic sql binding
        $employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();
        // employee basic sql binding
        $designationData = DB::table('hr_designation');
        $designationData_sql = $designationData->toSql();
        // employee sub section sql binding
        $subSectionData = DB::table('hr_subsection');
        $subSectionDataSql = $subSectionData->toSql();

        $queryData = DB::table('hr_monthly_salary AS s');
        $queryData->where('s.year', $year)
        ->where('s.month', $month)
        ->whereIn('s.unit_id', auth()->user()->unit_permissions())
        ->whereIn('s.location_id', auth()->user()->location_permissions())
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
        ->when(!empty($input['area']), function ($query) use($input){
           return $query->where('subsec.hr_subsec_area_id',$input['area']);
        })
        ->when(!empty($input['department']), function ($query) use($input){
           return $query->where('subsec.hr_subsec_department_id',$input['department']);
        })
        ->when(!empty($input['floor_id']), function ($query) use($input){
           return $query->where('emp.as_floor_id', $input['floor_id']);
        })
        ->when(!empty($input['line_id']), function ($query) use($input){
           return $query->where('emp.as_line_id', $input['line_id']);
        })
        ->when(!empty($input['section']), function ($query) use($input){
           return $query->where('subsec.hr_subsec_section_id', $input['section']);
        })
        ->when(!empty($input['subSection']), function ($query) use($input){
           return $query->where('s.sub_section_id', $input['subSection']);
        })
        
        ->when($request['otnonot']!=null, function ($query) use($input){
           return $query->where('s.ot_status',$input['otnonot']);
        })
        ->when(!empty($input['shift_roaster_status']), function ($query) use($input){
           return $query->where('emp.shift_roaster_status', $input['shift_roaster_status']);
        });
        $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
            $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
        });
        $queryData->leftjoin(DB::raw('(' . $designationData_sql. ') AS deg'), function($join) use ($designationData) {
            $join->on('deg.hr_designation_id','emp.as_designation_id')->addBinding($designationData->getBindings());
        });
        $queryData->leftjoin(DB::raw('(' . $subSectionDataSql. ') AS subsec'), function($join) use ($subSectionData) {
            $join->on('subsec.hr_subsec_id','s.sub_section_id')->addBinding($subSectionData->getBindings());
        });
        $data = $queryData->orderBy('deg.hr_designation_position', 'asc')->get();


        return Datatables::of($data)
            ->addIndexColumn()
            /*->addColumn('pic', function($data){
                return '<img src="'.emp_profile_picture($data).'" class="small-image min-img-file">';
            })
           */
            ->addColumn('associate_id', function($data) use ($input){
                $month = $input['month'];
                $jobCard = url("hr/operation/job_card?associate=$data->associate_id&month_year=$month");
                // return '<a href="'.$jobCard.'" target="_blank">'.$data->associate_id.'</a>';
                return '<a class="job_card" data-name="'.$data->as_name.'" data-associate="'.$data->associate_id.'" data-month-year="'.$month.'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">'.$data->associate_id.'</a> <br> '.$data->as_oracle_code;
            })
            ->addColumn('as_name', function($data){
                return $data->as_name.'<br>'.$data->as_contact;
            })
            ->addColumn('hr_designation_name', function($data){
                return $data->hr_designation_name??'';
            })
            ->addColumn('hr_department_name', function($data) use ($getDepartment){
                return $getDepartment[$data->hr_subsec_department_id]['hr_department_name']??'';
            })
            ->addColumn('hr_section_name', function($data) use ($section){
                return $section[$data->hr_subsec_section_id]['hr_section_name']??'';
            })
            ->addColumn('hr_line_name', function($data) use ($line){
                return $line[$data->as_line_id]['hr_line_name']??'';
            })
            ->addColumn('hr_subsection_name', function($data){
                return $data->hr_subsec_name??'';
            })
            ->addColumn('ot_hour', function($data){
                return numberToTimeClockFormat($data->ot_hour);
            })
            ->addColumn('total_day', function($data){
                return ($data->present + $data->holiday + $data->leave);
            })
            ->rawColumns(['DT_RowIndex', 'associate_id', 'as_name', 'hr_designation_name', 'hr_department_name', 'present', 'absent', 'leave', 'holiday', 'ot_hour', 'total_day','hr_line_name'])
            ->make(true);
    }

    public function salaryReportExcel(Request $request)
    {
        $input = $request->all();
        return Excel::download(new SalaryExport($input), 'salary.xlsx');
    }

    public function groupSalary(Request $request)
    {
        $input = $request->all();
        try {
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
            // employee sub section sql binding
            $subSectionData = DB::table('hr_subsection');
            $subSectionDataSql = $subSectionData->toSql();
            
            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];

            $queryData = DB::table('hr_monthly_salary AS s')
            ->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->where('emp.'.$input['report_group'], $input['selected']);
            $queryData->where('s.year', $year)
            ->where('s.month', $month)
            ->whereIn('s.unit_id', auth()->user()->unit_permissions())
            ->whereIn('s.location_id', auth()->user()->location_permissions())
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
            ->when($request['otnonot']!=null, function ($query) use($input){
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
            if(isset($input['audit']) && $input['audit'] == 'Audit'){
                $employeeList = array_column($getEmployee->toArray(), 'as_id');
                $auditedEmployee = SalaryIndividualAudit::with('user')->where('month', $month)->where('year', $year)->whereIn('as_id', $employeeList)->get()->keyBy('as_id');

            }

            return view('hr.reports.monthly_activity.salary.group_salary_details', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalSalary', 'totalEmployees', 'totalOtHour','totalOTAmount', 'totalCashSalary', 'totalBankSalary', 'totalTax', 'totalStamp', 'auditedEmployee'));
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }
}
