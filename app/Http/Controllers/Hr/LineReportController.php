<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Hr\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Unit;
use App\Models\Hr\Line;
use App\Models\Hr\Station;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, DB, DataTables, stdClass;

class LineReportController extends Controller
{
    public function index()
    {
    	$lines = Employee::where('as_line_id', '!=', null)
    	->get()
    	->groupBy('as_line_id')

    	dd($lines);

    	return view('hr.line_report', compact('lines'));
    }
}