<?php

namespace App\Http\Controllers\hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Repository\Hr\AttendanceProcessRepository;
use App\Repository\Hr\EmployeeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceFormController extends Controller
{
    protected $employee;
    public function __construct(EmployeeRepository $employee){
        $this->employee = $employee;
    }
    public function index(){
        $data['yearMonth'] = $request->year_month??date('Y-m');
        return view('hr.operation.attendance_form.index', $data);
    }

    public function getFridays($year,$month){
        $date = "$year-$month-01";
        $first_day = date('N',strtotime($date));
        $first_day = 7 - $first_day - 1;
        $last_day =  date('t',strtotime($date));
        $days = array();
        for($i=$first_day; $i<=$last_day; $i=$i+7 ){
            $days[] = $i;
        }
        return  $days;
    }

    public function report(Request $request)
    {
        $total_days_month = date('t', strtotime($request->year_month.'-01'));
        $month_year = $request->year_month;
        $getEmployee = $this->employee->getEmployeeByAssociateId();
        $data['results'] = $this->employee->getEmployeeByFilter($request, $getEmployee);
        $data['total_days_month'] =$total_days_month;
        $data['month_year'] = $month_year;
//        dd($data);
        return view('hr.operation.attendance_form.report', $data)->render();
    }
}
