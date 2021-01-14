<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Area;
use App\Models\Hr\Benefits;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\SalaryAudit;
use App\Models\Hr\SalaryAuditHistory;
use App\Models\Hr\SalaryIndividualAudit;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Auth, DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SalaryProcessController extends Controller
{
    public function index()
    {
        //
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
            /*$audit = 1;
            $input['unit_id'] = $input['unit'];
            $salaryStatus = SalaryAudit::checkSalaryAuditStatus($input);
            
            if($salaryStatus == null){
                $audit = 0;
            }else{
                if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null){
                    $audit = 0;
                }
            }
            
            if($audit == 0){
                return view('hr.operation.salary.salary_status', compact('salaryStatus', 'input'));
            }*/
            if(!in_array($input['unit'], [14,145])){
                $getUnit = Unit::getUnitNameBangla($input['unit']);
            }else if($input['unit'] == 145){
                $getUnit = 'MBM + MFW +SRT';
            }else if($input['unit'] == 14){
                $getUnit = 'MBM + MFW';
            }else if($input['unit'] == 15){
                $getUnit = 'MBM + SRT';
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
            
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

            $queryData = DB::table('hr_monthly_salary as s')
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->where('s.year', $input['year'])
            ->where('s.month', $input['month'])
            ->whereBetween('s.gross', [$input['min_sal'], $input['max_sal']])
            ->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->when(!empty($input['unit']), function ($query) use($input){
                if(!in_array($input['unit'], [14,145,15])){
                    return $query->where('emp.as_unit_id',$input['unit']);
                }else{
                    if($input['unit'] == 14)
                        $unit = [1,4];
                    else if($input['unit'] == 145)
                        $unit = [1,4,5];
                    else if($input['unit'] == 15)
                        $unit = [1,5];
                    else
                        $unit = [];

                    return $query->whereIn('emp.as_unit_id',$unit);
                }
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
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            });
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
                $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
            });
            
            if(!empty($input['pay_status'])){
                // employee benefit sql binding
                $benefitData = DB::table('hr_benefits');
                $benefitData_sql = $benefitData->toSql();
                $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                    $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
                });
                if($input['pay_status'] == "cash"){
                    $queryData->where('ben.ben_cash_amount', '>', 0);
                }elseif($input['pay_status'] != 'cash'){
                    $queryData->where('ben.bank_name',$input['pay_status']);
                }
            }
            

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });

                
            $queryData->select('s.*', 'emp.as_doj','emp.associate_id', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'emp.as_location', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code', 'emp.as_unit_id',DB::raw('s.ot_hour * s.ot_rate as ot_amount'));
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
                $locationList = array_column($locationDataSet, 'as_location');
                $uniqueLocation = array_unique($locationList);
            }elseif($input['unit'] == null){
                $locationList = array_column($locationDataSet, 'as_unit_id');
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
            if($input['unit'] == null){
                
                return view('hr.operation.salary.load_salary_sheet_unit', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input'));
            }else{
                return view('hr.operation.salary.load_salary_sheet', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }

    public function employeeWise(Request $request)
    {
        
        $input = $request->all();
        $input['month'] = date('m', strtotime($input['emp_month_year']));
        $input['year'] = date('Y', strtotime($input['emp_month_year']));
        try {

            // $audit = 1;
            // $input['unit_id'] = $input['unit'];
            // $salaryStatus = SalaryAudit::checkSalaryAuditStatus($input);
            
            // if($salaryStatus == null){
            //     $audit = 0;
            // }else{
            //     if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null){
            //         $audit = 0;
            //     }
            // }
            
            // if($audit == 0){
            //     return view('hr.operation.salary.salary_status', compact('salaryStatus', 'input'));
            // }

            // $getUnit = Unit::getUnitNameBangla($input['unit']);
            $info = [];
            
            // employee info
            $employeeData = DB::table('hr_as_basic_info');
            $employeeDataSql = $employeeData->toSql();
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

            $queryData = DB::table('hr_monthly_salary as s')
            ->where('s.year', $input['year'])
            ->where('s.month', $input['month'])
            ->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->whereIn('s.as_id', $input['as_id']);
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
                
            $getSalaryList = $queryData->select('s.*', 'emp.as_doj', 'emp.as_ot','emp.as_oracle_code', 'emp.as_designation_id', 'emp.as_section_id', 'emp.as_location','emp.as_unit_id', 'bemp.hr_bn_associate_name')->get();
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
            if($input['formattype'] == 0){
                $viewPage = 'hr.operation.salary.load_salary_sheet_employee';
            }else{
                $viewPage = 'hr.operation.salary.load_salary_sheet';
            }
            return view($viewPage, compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation', 'getSection', 'input'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }

    public function generate(Request $request)
    {
        try {
            $units = Cache::rememberForever('units', function() {
                return DB::table('hr_unit')->orderBy('hr_unit_name', 'desc')->get();
            });

            if($request->unit != null && $request->month_year != null){
                $input = $request->all();
                $input['unit_id'] = $input['unit'];
                $input['month'] = date('m', strtotime($input['month_year']));
                $input['year'] = date('Y', strtotime($input['month_year']));
                $salaryStatus = SalaryAudit::checkSalaryAuditStatus($input);
                $auditHistory = SalaryAuditHistory::checkSalaryAduitHistory($input);
                return view('hr.operation.salary.aduit_status', compact('salaryStatus', 'input', 'auditHistory'));
            }

            return view('hr.operation.salary.generate', compact('units'));

        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }

    public function salaryAuditStatus(Request $request)
    {
        $input = $request->all();
        // return $input
        $data['type'] = 'error';
        if($input['month_year'] == ''){
            $data['message'] = 'Something Wrong, please Reload The Page';
            return $data;
        }
        DB::beginTransaction();
        try {
            $aduit['month'] = date('m', strtotime($input['month_year'])); 
            $aduit['year'] = date('Y', strtotime($input['month_year'])); 
            $aduit['unit_id'] = $input['unit']; 
            $salaryAuditStatus = SalaryAudit::checkSalaryAuditStatus($aduit);
            if($salaryAuditStatus != null){
                if($salaryAuditStatus->initial_audit == null){
                    $aduit['initial_audit'] = Auth::user()->id;
                    $aduit['initial_comment'] = $input['comment'];
                    $aduitHistory['stage'] = 2;
                    // special comment process
                    $getIndividualAudit = SalaryIndividualAudit::getSalaryIndividualAuditMonthStatusWise($aduit, 2);
                    if(count($getIndividualAudit) > 0){
                        $aduitHistory['special_comment'] = $getIndividualAudit->toJson();
                    }
                }elseif($salaryAuditStatus->accounts_audit == null){
                    $aduit['accounts_audit'] = Auth::user()->id;
                    $aduit['accounts_comment'] = $input['comment'];
                    $aduitHistory['stage'] = 3;
                }elseif($salaryAuditStatus->management_audit == null){
                    $aduit['management_audit'] = Auth::user()->id;
                    $aduit['management_comment'] = $input['comment'];
                    $aduitHistory['stage'] = 4;
                }
                if($input['status'] == 1){
                    $salaryAuditStatus->update($aduit);
                }else{
                    $audit = SalaryAudit::findOrFail($salaryAuditStatus->id);
                    $audit->delete();
                    // audit individual
                    // SalaryIndividualAudit::getSalaryIndividualAuditMonthWiseDelete($aduit);
                }
            }else{
                if($input['status'] == 1){
                    $aduit['hr_audit'] = Auth::user()->id;
                    $aduit['hr_comment'] = $input['comment'];
                    $aduitHistory['stage'] = 1;
                    SalaryAudit::create($aduit);
                }
            }
            
            // audit history
            $aduitHistory['month'] = $aduit['month']; 
            $aduitHistory['year'] = $aduit['year']; 
            $aduitHistory['unit_id'] = $input['unit']; 
            $aduitHistory['audit_id'] = Auth::user()->id; 
            $aduitHistory['status'] = $input['status']; 
            $aduitHistory['comment'] = $input['comment'];
            SalaryAuditHistory::create($aduitHistory); 
            
            $data['type'] = 'success';
            $data['message'] = 'Audit Successfully Done';
            $unit = $input['unit'];
            $month = $input['month_year'];
            $data['url'] = url('hr/operation/salary-generate?month='.$month.'&unit='.$unit);
            DB::commit();
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'] = $e->getMessage();
            return $data;

        }
    }
    public function individualAudit(Request $request)
    {
        $input = $request->all();
        
        $data['type'] = 'error';
        if($input['month_year'] == '' || $input['as_id'] == ''){
            $data['message'] = 'Something Wrong, please reload the page and try again!';
            return $data;
        }
        try {
            $input['month'] = date('m', strtotime($input['month_year'])); 
            $input['year'] = date('Y', strtotime($input['month_year']));
            $getEmployee = Employee::select('as_id', 'as_unit_id')->where('as_id', $input['as_id'])->first();
            if($getEmployee == null){
                $data['message'] = 'Something Wrong, please reload the page and try again!';
                return $data;
            }
            $input['unit_id'] = $getEmployee->as_unit_id;
            $input['audit_by'] = auth()->user()->id;
            $checkAudit = SalaryIndividualAudit::checkSalaryIndividualAuditStatus($input);
            unset($input['month_year']);
            // return $input;
            if($checkAudit != null){
                $salaryIndividual = SalaryIndividualAudit::findOrFail($checkAudit->id);
                $salaryIndividual->update($input);
                $data['count'] = 0;
            }else{
                SalaryIndividualAudit::create($input);
                $data['count'] = 1;
            }
            if($input['status'] == '1'){
                $data['message'] = 'Audit Passed';
            }else{
                $data['message'] = 'Audit Failed';
            }
            $data['type'] = 'success';
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;

        }
        
    }

    public function failHistory($id)
    {
        $getHistory = SalaryAuditHistory::findOrFail($id);
        if($getHistory != null){
            $getEmployee = Employee::where('as_unit_id', $getHistory->unit_id)->get()->keyBy('as_id');
            return view('hr.operation.salary.audit_history', compact('getHistory', 'getEmployee'));
        }else{
            return "Something Wrong!, Please try again";
        }
        
    }
}
