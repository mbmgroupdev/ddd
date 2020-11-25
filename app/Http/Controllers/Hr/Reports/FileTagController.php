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
        # Associate ID CARD Search
    public function fileTagSearch(Request $request)
    {
        // ACL::check(["permission" => "hr_time_id_card"]);
        #-----------------------------------------------------------#

        if (!is_array($request->associate_id) || sizeof($request->associate_id) == 0)
            return response()->json(['filetag'=>'<div class="alert alert-danger">Please Select Associate ID</div>', 'printbutton'=>'']);

        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $employees = [];
        $employees = DB::table('hr_as_basic_info')
            ->select(
                'as_id',
                'associate_id',
                'as_oracle_code',
                'temp_id',
                'as_pic',
                'as_name',
                'as_doj',
                'as_unit_id',
                'as_designation_id',
                'as_department_id',
                'as_section_id',
                'as_unit_id'
            )
            ->whereIn('associate_id', $request->associate_id)
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->get();

        $data['filetag'] = "<style media='print'>.page-break{page-break-after: always;}</style>";
        if (count($employees)>0)
        {
            $page = array_chunk($employees->toArray(), 3);
            foreach ($page as $key => $emps) {
                # code...
                foreach ($emps as $associate)
                {
                        $data['filetag'] .= "
                        <div style=\"border-style: solid;width:900px;margin: 10px auto;\">
                        <p style=\"text-align:center; font-size:72px; font-weight:700; margin:0px; padding:0px;\">".strtoupper($associate->as_name)."</p>
                        <p style=\"text-align:center; font-weight:600; font-size:48px; margin:0px; padding:0px;\">".
                                    (!empty($associate->associate_id)?
                                    (substr_replace($associate->associate_id, "<big style='font-size:72px; font-weight:700'>$associate->temp_id</big>", 3, 6)):
                                    null) ." <span style='font-size:40px'>(".$associate->as_oracle_code.")</span><br></p>
                        <p style=\"text-align:center; font-size:36px; font-weight:700; margin:0px; padding:0px;\">".strtoupper($designation[$associate->as_designation_id]['hr_designation_name'])."</p>
                        <p style=\"text-align:center; font-size:36px; font-weight:700; margin:0px; padding:0px;\">Section: ".strtoupper($section[$associate->as_section_id]['hr_section_name'])."</p>
                        <p style=\"text-align:center; font-size:60px; font-weight:700; margin:0px; padding:0px;\">".date('d-M-Y',strtotime($associate->as_doj))."</p>
                        </div>";
                }
                $data['filetag'] .= "<div class='page-break'></div>";
            }
        }
        else
        {
            $data['filetag'] = '<div class="alert alert-danger">No File Tag Found!</div>';
        }

        $data['printbutton'] = "";
        if (strlen($data['filetag'])>1)
        {
            $data['printbutton'] .= "<button onclick=\"printContent('idCardPrint')\" type=\"button\" class=\"btn btn-success btn-xs\"><i class=\"fa fa-print\" title=\"Print\"></i></button>";
        }

        return response()->json($data);
    }
}
