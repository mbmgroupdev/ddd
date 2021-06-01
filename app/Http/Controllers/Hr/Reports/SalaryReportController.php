<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Exports\Hr\SalarySheetExport;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\SalaryRepository;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB, DataTables;
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
    public function index(Request $request)
    {
        $yearMonth = $request->year_month??date('Y-m');
        if(date('d') < 10){
            $yearMonth = date('Y-m', strtotime('-1 month'));
        }
    	$data['yearMonth'] = $yearMonth;
    	$data['months'] = monthly_navbar($data['yearMonth']);
        $data['salaryMax'] = Benefits::getSalaryRangeMax();
    	return view('hr.reports.salary.index', $data);
    }

    public function report(Request $request)
    {
        $getSalary = $this->processSalary($request);
    	$result = $this->salary->getSalaryReport($request, $getSalary);
        if(isset($request->export)){
            $filename = 'Salary Report - ';
            $filename .= '.xlsx';
            return Excel::download(new SalarySheetExport($result, 'report'), $filename);
        }
    	return view('hr.reports.salary.report', $result)->render();
    }

    public function processSalary($request)
    {
        $getSalary = $this->salary->getSalaryByMonth($request);
        if(count($getSalary) > 0){
            $getEmployee = collect($this->employee->getEmployeeByAssociateId(['associate_id', 'as_name', 'as_line_id', 'as_floor_id', 'as_oracle_code', 'as_doj']))->keyBy('associate_id');
            $dataRow = $this->salary->getSalaryByFilter($request, $getSalary, $getEmployee);
            $getSalary = $this->employee->getEmployeeByFilter($request, $dataRow);
        }
        return $getSalary;
    }

    public function salaryDataTable(Request $request)
    {
        $data = $this->processSalary($request);
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $line = line_by_id();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('pic', function($data){
                return 'q';
            })
            ->addColumn('associate_id', function($data) use ($request){
                $month = $request->year_month;
                $jobCard = url("hr/operation/job_card?associate=$data->as_id&month_year=$month");
                return '<a class="job_card" data-name="'.$data->as_name.'" data-associate="'.$data->as_id.'" data-month-year="'.$month.'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">'.$data->as_id.'</a> <br> '.$data->as_oracle_code;
            })
            ->addColumn('as_name', function($data){
                return $data->as_name.'<br>';
            })
            ->addColumn('hr_designation_name', function($data) use ($designation){
                return $designation[$data->as_designation_id]['hr_designation_name']??'';
            })
            ->addColumn('hr_department_name', function($data) use ($department){
                return $department[$data->as_department_id]['hr_department_name']??'';
            })
            ->addColumn('hr_section_name', function($data) use ($section){
                return $section[$data->as_section_id]['hr_section_name']??'';
            })
            ->addColumn('hr_subsection_name', function($data) use ($subSection){
                return $subSection[$data->as_subsection_id]['hr_subsec_name']??'';
            })
            ->addColumn('hr_line_name', function($data) use ($line){
                return $line[$data->as_line_id]['hr_line_name']??'';
            })
            ->addColumn('ot_hour', function($data){
                return numberToTimeClockFormat($data->ot_hour);
            })
            ->addColumn('total_day', function($data){
                return ($data->present + $data->holiday + $data->leave);
            })
            ->rawColumns(['DT_RowIndex', 'pic', 'associate_id', 'as_name', 'hr_designation_name', 'hr_department_name','hr_line_name', 'present', 'absent', 'leave', 'holiday', 'ot_hour', 'total_day'])
            ->make(true);
    }

    public function bankSheetReport(Request $request){
        $getSalary = $this->processSalary($request);
        $result = $this->salary->getSalaryReport($request, $getSalary);

        return view('hr.payroll.bank_part.reports', $result)->render();
    }
}
