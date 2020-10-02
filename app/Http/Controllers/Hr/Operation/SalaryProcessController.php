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
            $audit = 1;
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
            }

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

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
	            
	        $getSalaryList = $queryData->select('s.*', 'emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_location', 'bemp.hr_bn_associate_name')->get();
            // return $getSalaryList;
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
            // dd($getSalaryList);
            // return $locationDataSet;
            $locationList = array_column($locationDataSet, 'as_location');
            $uniqueLocation = array_unique($locationList);
            $locationDataSet = array_chunk($locationDataSet, 5, true);
            // $title = $getUnit->hr_unit_name_bn;
            $pageHead['current_date']   = date('Y-m-d');
            $pageHead['current_time']   = date('H:i');
            $pageHead['unit_name']      = $getUnit->hr_unit_name_bn;
            $pageHead['for_date']       = $input['month_year'];
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
            ->whereIn('s.as_id', $input['as_id']);
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
                
            $getSalaryList = $queryData->select('s.*', 'emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_location', 'bemp.hr_bn_associate_name')->get();
            // dd($getSalaryList);
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
            $pageHead['unit_name']      = '';
            $pageHead['for_date']       = $input['emp_month_year'];
            $pageHead['floor_name']     = '';
            $pageHead['month']     = $input['month'];
            $pageHead['year']     = $input['year'];
            $pageHead = (object)$pageHead;
            return view('hr.operation.salary.load_salary_sheet', compact('uniqueLocation', 'getSalaryList', 'pageHead','locationDataSet', 'info', 'salaryAddDeduct', 'designation'));
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
                return view('hr.operation.salary.aduit_status', compact('salaryStatus', 'input'));
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
            if($input['status'] == 1){
                if($salaryAuditStatus != null){
                    if($salaryAuditStatus->initial_audit == null){
                        $aduit['initial_audit'] = Auth::user()->id;
                        $aduit['initial_comment'] = $input['comment'];
                        $aduitHistory['stage'] = 2;
                    }elseif($salaryAuditStatus->accounts_audit == null){
                        $aduit['accounts_audit'] = Auth::user()->id;
                        $aduit['accounts_comment'] = $input['comment'];
                        $aduitHistory['stage'] = 3;
                    }elseif($salaryAuditStatus->management_audit == null){
                        $aduit['management_audit'] = Auth::user()->id;
                        $aduit['management_comment'] = $input['comment'];
                        $aduitHistory['stage'] = 4;
                    }
                    $salaryAuditStatus->update($aduit);
                }else{
                    $aduit['hr_audit'] = Auth::user()->id;
                    $aduit['hr_comment'] = $input['comment'];
                    $aduitHistory['stage'] = 1;
                    SalaryAudit::create($aduit);
                }
            }else{
                $audit = SalaryAudit::findOrFail($salaryAuditStatus->id);
                $audit->delete();
            }
            
            // audit history
            $aduitHistory['month'] = $aduit['month']; 
            $aduitHistory['year'] = $aduit['year']; 
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
}
