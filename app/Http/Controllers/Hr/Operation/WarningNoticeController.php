<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\WarningNotice;
use Illuminate\Http\Request;

class WarningNoticeController extends Controller
{
    public function index(Request $request)
    {
    	$input = $request->all();
    	$info = '';
    	$notices = '';
    	try {
    		if($input['associate'] != null && $input['month_year'] != null){
    			$info = Employee::getEmployeeAssociateIdWise($input['associate']);
    			$notices = WarningNotice::getEmployeeMonthWiseNotice($input);
    		}
    		return view('hr.operation.warning_notice.index', compact('info', 'notices'));
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		return back();
    	}
    }

    public function list(Request $request)
    {
    	$input = $request->all();
    	return view('hr.reports.warning_notices', compact('input'));
    }
}
