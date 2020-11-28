<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;
use DB;

class BillOperationController extends Controller
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
            $data['departmentList'] = Department::where('hr_department_status', '1')->pluck('hr_department_name', 'hr_department_id');
            return view('hr.operation.bill.index', $data);
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function filterWise(Request $request)
    {
    	$input = $request->all();
    	$input['department'] = $input['department']??'';
    	$input['section'] = $input['section']??'';
    	$input['subSection'] = $input['subSection']??'';
    	try {
    		// return $input;
    		// employee info
    		$employeeData = DB::table('hr_as_basic_info');
	        $employeeDataSql = $employeeData->toSql();
            
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

	        $queryData = DB::table('hr_bill as s')
	        ->whereBetween('s.bill_date', [$input['from_date'],$input['to_date']])
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())

            ->when(!empty($input['unit']), function ($query) use($input){
               return $query->where('emp.as_unit_id',$input['unit']);
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('emp.as_location',$input['location']);
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
                $queryData->where('emp.as_ot',$input['otnonot']);
            }
            if(isset($input['pay_status']) && $input['pay_status'] != null){
                $queryData->where('s.pay_status', $input['pay_status']);
            }
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
	        $listData = clone $queryData;
	        $queryData->select('emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'emp.as_location', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code', 'emp.as_unit_id','emp.as_id','emp.associate_id', DB::raw('sum(amount) as totalAmount'), DB::raw('count(*) as totalDay'), DB::raw("SUM(IF(pay_status=0,1,0)) AS dueDay"), DB::raw("SUM(IF(pay_status=0,amount,0)) AS dueAmount"))->groupBy('emp.as_id');
	        $totalAmount =  array_sum(array_column($queryData->get()->toArray(),'dueAmount'));
	        $getBillList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->get();

            $getBillLists = $listData->select('s.*')->orderBy('s.bill_date', 'asc')->get()->groupBy('as_id',true);
            $totalEmployees = count($getBillLists);

            // employee designation
            $designation = designation_by_id();
            $section = section_by_id();
            // return $designation;

            $unitDataSet = $getBillList->toArray();
            $unitList = array_column($unitDataSet, 'as_unit_id');
            $uniqueUnit = array_unique($unitList);
            $getBillDataSet = array_chunk($unitDataSet, 25, true);
            $pageHead['totalBill'] = $totalAmount;
            $pageHead['totalEmployees'] = $totalEmployees;
            // dd($getBillDataSet);
            return view('hr.operation.bill.report', compact('getBillList', 'designation', 'section', 'uniqueUnit', 'input', 'getBillDataSet', 'getBillLists', 'pageHead'));
    	} catch (\Exception $e) {
    		// return 'error';
    		return $e->getMessage();
    	}
    }

    public function review(Request $request)
    {
    	$input =$request->all();
    	if(count($input['pay_id']) == 0){
    		return 'warning';
    	}
    	try {
    		$employeeData = DB::table('hr_as_basic_info');
	        $employeeDataSql = $employeeData->toSql();
            
            // employee bangla info
            $employeeBanData = DB::table('hr_employee_bengali');
            $employeeBanDataSql = $employeeBanData->toSql();

	        $queryData = DB::table('hr_bill as s')
	        ->whereBetween('s.bill_date', [$input['from_date'],$input['to_date']])
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->where('s.pay_status', 0)
            ->whereIn('s.as_id', $input['pay_id']);
            $queryData->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
            });

            $queryData->leftjoin(DB::raw('(' . $employeeBanDataSql. ') AS bemp'), function($join) use ($employeeBanData) {
                $join->on('bemp.hr_bn_associate_id','emp.associate_id')->addBinding($employeeBanData->getBindings());
            });
    		$listData = clone $queryData;
	        $queryData->select('emp.as_doj', 'emp.as_ot', 'emp.as_designation_id', 'emp.as_section_id', 'emp.as_location', 'bemp.hr_bn_associate_name', 'emp.as_oracle_code', 'emp.as_unit_id','emp.as_id','emp.associate_id', DB::raw('sum(amount) as totalAmount'), DB::raw('count(*) as totalDay'), DB::raw("SUM(IF(pay_status=0,1,0)) AS dueDay"), DB::raw("SUM(IF(pay_status=0,amount,0)) AS dueAmount"))->groupBy('emp.as_id');
	        $totalAmount =  array_sum(array_column($queryData->get()->toArray(),'dueAmount'));
	        $getBillList = $queryData->orderBy('emp.as_oracle_sl', 'asc')->get();

            $getBillLists = $listData->select('s.*')->orderBy('s.bill_date', 'asc')->get()->groupBy('as_id',true);
            $totalEmployees = count($getBillLists);

            // employee designation
            $designation = designation_by_id();
            $section = section_by_id();
            // return $designation;

            $unitDataSet = $getBillList->toArray();
            $unitList = array_column($unitDataSet, 'as_unit_id');
            $uniqueUnit = array_unique($unitList);
            $getBillDataSet = array_chunk($unitDataSet, 25, true);
            $pageHead['totalBill'] = $totalAmount;
            $pageHead['totalEmployees'] = $totalEmployees;
            // dd($getBillDataSet);
            return view('hr.operation.bill.review', compact('getBillList', 'designation', 'section', 'uniqueUnit', 'input', 'getBillDataSet', 'getBillLists', 'pageHead'));
    	} catch (\Exception $e) {
    		return 'error';
    		$data['msg'] = $e->getMessage();
    		return $data;
    	}
    }

    public function pay(Request $request)
    {
    	$data['type'] = 'error';
    	$input =$request->all();
    	if(count($input['pay_id']) == 0){
    		$data['msg'] = 'No Employee Found, Please Select Employee and try again';
    		return $data;
    	}
    	DB::beginTransaction();
    	try {
	        $queryData = DB::table('hr_bill')
	        ->whereBetween('bill_date', [$input['from_date'],$input['to_date']])
            ->where('pay_status', 0)
            ->whereIn('as_id', $input['pay_id'])
            ->update([
            	'pay_status' => 1
            ]);

            DB::commit();
            $data['type'] = 'success';
            $data['msg'] = 'Successfully Payment Done';
            return $data;
    	} catch (\Exception $e) {
    		DB::rollback();
    		$data['msg'] = $e->getMessage();
    		return $data;
    	}
    }
}
