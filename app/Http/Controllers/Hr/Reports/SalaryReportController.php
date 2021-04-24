<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\SalaryRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryReportController extends Controller
{
	protected $salary;
	protected $employee;
	public function __construct(SalaryRepository $salary, EmployeeRepository $employee)
	{
	    ini_set('zlib.output_compression', 1);
	    $this->salary = $salary;
	    $this->employee = $employee;
	}
    public function index()
    {
    	$yearMonth = $request->year_month??date('Y-m');
    	$months = monthly_navbar($yearMonth);
    	return view('hr.reports.salary.index', compact('yearMonth','months'));
    }

    public function report(Request $request)
    {
    	$getSalary = $this->salary->getSalaryByMonth($request);
    	if(count($getSalary) > 0){
    		$asIds = collect($getSalary)->pluck('as_id');
	    	$getEmployee = collect($this->employee->getEmployeeByAssociateId($asIds, ['associate_id', 'as_name', 'as_line_id', 'as_floor_id']))->keyBy('associate_id');
	    	$getSalary = $this->salary->getSalaryByFilter($request, $getSalary, $getEmployee);
    	}
    	
    	$result = $this->salary->getSalaryReport($request, $getSalary);
        
    	return view('hr.reports.salary.report', $result)->render();
    }
}
