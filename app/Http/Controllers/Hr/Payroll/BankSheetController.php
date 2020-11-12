<?php

namespace App\Http\Controllers\Hr\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;
use DB;

class BankSheetController extends Controller
{
    public function index()
    {
    	$unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('hr_unit_name', 'desc')
        ->pluck('hr_unit_name', 'hr_unit_id');

        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');

        return view('hr/payroll/bank_part.index', compact('unitList','locationList'));
    }

    public function report(Request $request)
    {
    	$input = $request->all();
    	// return $input;
    	try {
            $yearMonth = explode('-', $input['month']);
            $month = $yearMonth[1];
            $year = $yearMonth[0];
            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();

            // employee benefit sql binding
            $benefitData = DB::table('hr_benefits');
            $benefitData_sql = $benefitData->toSql();

            $getEmployee = array();

            $queryData = DB::table('hr_monthly_salary AS s')
            ->whereNotIn('s.as_id', config('base.ignore_salary'))
            ->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions());
            if(count($input['unit']) > 0){
            	$queryData->whereIn('as_unit_id', $input['unit']);
            }
            if(isset($input['location']) &&count($input['location']) > 0){
            	$queryData->whereIn('as_location', $input['location']);
            }
            $queryData->where('s.year', $year)
            ->where('s.emp_status', 1)
            ->where('s.month', $month)
            ->where('s.bank_payable', '>', 0)
            ->when(!empty($input['pay_status']), function ($query) use($input){
                return $query->where('ben.bank_name',$input['pay_status']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            });
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','s.as_id')->addBinding($employeeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                $join->on('ben.ben_as_id','emp.associate_id')->addBinding($benefitData->getBindings());
            });

            $queryData->select('ben.bank_name','ben.bank_no', 'ben.ben_tds_amount','emp.as_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_unit_id', 'emp.as_pic', 'emp.as_name', 's.total_payable', 's.bank_payable', 's.tds', 's.pay_status');
            $totalSalary = round($queryData->sum("s.total_payable"));
            $totalBankSalary = round($queryData->sum("s.bank_payable"));
            // $totalStamp = round($queryData->sum("s.stamp"));
            $totalTax = round($queryData->sum("s.tds"));

            $getEmployee = $queryData->orderBy('s.bank_payable', 'desc')->get();
            $totalEmployees = count($getEmployee);
            return view('hr.payroll.bank_part.reports', compact('getEmployee', 'input', 'totalSalary', 'totalEmployees', 'totalBankSalary', 'totalTax'));
        } catch (\Exception $e) {
            return $e->getMessage();
            return 'error';
        }
    }
}