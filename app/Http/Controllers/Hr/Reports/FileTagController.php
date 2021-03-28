<?php

namespace App\Http\Controllers\Hr\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\Floor;
use DB, ACL, DataTables,Validator;

class FileTagController extends Controller
{
    public function showForm(){
    	$employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');
    	return view('hr/reports/file_tag',compact('employeeTypes','unitList'));
    }
        # Associate ID CARD Searc

    public function fileTagSearch(Request $request)
    {

        if (!is_array($request->associate_id) || sizeof($request->associate_id) == 0)
            return response()->json(['filetag'=>'<div class="alert alert-danger">Please Select Associate ID</div>', 'printbutton'=>'']);

        $designation = designation_by_id();
        $unit = unit_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $employees = [];
        $employees = DB::table('hr_as_basic_info as b')
            ->select(
                'b.as_id',
                'b.associate_id',
                'b.as_oracle_code',
                'b.as_emp_type_id',
                'b.temp_id',
                'b.as_pic',
                'b.as_gender',
                'b.as_name',
                'b.as_doj',
                'b.as_unit_id',
                'b.as_designation_id',
                'b.as_department_id',
                'b.as_section_id',
                'b.as_unit_id',
                'bn.hr_bn_associate_name'
            )
            ->leftJoin('hr_employee_bengali as bn', 'bn.hr_bn_associate_id','b.associate_id')
            ->whereIn('associate_id', $request->associate_id)
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->get();
            
        $data['filetag'] = view('hr.common.file_tag_view', compact('employees','designation','department','section','unit'))->render();

        $data['printbutton'] = "";
        if (strlen($data['filetag'])>1)
        {
            $data['printbutton'] .= "<button onclick=\"printContent('idCardPrint')\" type=\"button\" class=\"btn btn-success btn-xs\"><i class=\"fa fa-print\" title=\"Print\"></i></button>";
        }

        return response()->json($data);
    }
}
