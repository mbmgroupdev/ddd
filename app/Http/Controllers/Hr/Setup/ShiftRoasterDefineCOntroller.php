<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\Floor;
use App\Models\Hr\Line;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use Validator, DB, DataTables, ACL, Collection;

class ShiftRoasterDefineCOntroller extends Controller
{
    public function shiftRoasterDefine()
    {
        //ACL::check(["permission" => "hr_time_shift_assign"]);
        #-----------------------------------------------------------#
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_short_name', 'hr_unit_id');

        $shiftList = Shift::where('hr_shift_status', 1)->pluck("hr_shift_name", "hr_shift_id");

        // $sectionList = Section::where('hr_section_status',1)->pluck('hr_section_name','hr_section_id');
        // $subsectionList = Subsection::where('hr_subsec_status',1)->pluck('hr_subsec_name','hr_subsec_id');
        $areaList = Area::where('hr_area_status',1)->pluck('hr_area_name','hr_area_id');
        // dd($sectionList, $subsectionList);
        return view('hr/operation/shift_roaster_define', compact('shiftList', 'employeeTypes', 'unitList','areaList'));
    }

    public function getAssociateByTypeUnitShiftRoaster(Request $request)
    {
        $employees = Employee::where(function($query) use ($request){
            if ($request->emp_type != null)
            {
                $query->where('as_emp_type_id', $request->emp_type);
            }
            if ($request->unit != null)
            {
                $query->where('as_unit_id', $request->unit);
            }
            if ($request->otnonot != null)
            {
                $query->where('as_ot', $request->otnonot);
            }
            if ($request->shift != null)
            {
                // $query->where('as_shift_id', $request->shift);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->area != null)
            {
                $query->where('as_area_id', $request->area);
            }
            if ($request->department != null)
            {
                $query->where('as_department_id', $request->department);
            }
            if ($request->section != null)
            {
                $query->where('as_section_id', $request->section);
            }
            if ($request->subsection != null)
            {
                $query->where('as_subsection_id', $request->subsection);
            }
            $query->where("as_status", 1);
        })
        ->where('shift_roaster_status', $request->current_status)
        ->whereIn('as_unit_id', auth()->user()->unit_permissions())
        ->get();

        // show user id
        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
        $data['result'] = "";
        $data['total']= count($employees);
        foreach($employees as $employee)
        {
        	if($employee->shift_roaster_status == 0){
        		$current_status_val = '<span style="color: forestgreen;">Shift</span>';
        	}
        	elseif($employee->shift_roaster_status == 1){
        		$current_status_val = '<span style="color: darkblue;">Roster</span>';
        	}
            $image = ($employee->as_pic == null?'/assets/images/avatars/profile-pic.jpg': $employee->as_pic);
	        $data['result'].= "<tr class='add'>
	                    <td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".$image."' class='small-image' onError='this.onerror=null;this.src=\"/assets/images/avatars/avatar2.png\"'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td>
	                    <td>$employee->as_name </td><td>$current_status_val </td></tr>";

        }

        return $data;
    }

    public function shiftRoasterStatusSave(Request $request){
    	#----Saving the satus..
    	// dd($request->all());
        if(empty($request->assigned)){
            return back()->with('error', 'Select at least One Employee');
        }
        else{
        	$list_emp = '';
        	foreach ($request->assigned as $as_id => $associate_id) {
        		Employee::where('as_id', $as_id)->update([
        				'shift_roaster_status' => $request->target_status
        		]);
        		$list_emp .= $as_id.', ';
        	}
        	// dd($list_emp);
        	$this->logFileWrite('Shift Roster Status Updated for: ', $list_emp);
        	return back()->with('success', 'Shift Roster Status Updated');
        }
    }
}
