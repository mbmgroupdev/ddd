<?php

namespace App\Http\Controllers\Hr\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;

use DB, Response, Validator;


class SubstituteHolidayController extends Controller
{
    public function index(){
    	$employees = Employee::pluck('as_name', 'as_id');
    	// dd($employees);
    	return view('hr.operation.substitute_holiday', compact('employees'));
    }

    public function saveData(Request $request){
    	dd($request->all());
    }
}
