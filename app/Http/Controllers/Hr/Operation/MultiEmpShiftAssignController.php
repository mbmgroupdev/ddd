<?php

namespace App\Http\Controllers\Hr\Operation;

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
use Validator, DB, DataTables, ACL, Collection, Response;

class MultiEmpShiftAssignController extends Controller
{
   	public function index(){
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

   		return view('hr.operation.multi_employee_shift_assign',  compact('shiftList', 'employeeTypes', 'unitList', 'areaList'));
   	}

   	public function getCurrentShiftsUnitWise(Request $request){
   		$c_shifts=Employee::where('as_unit_id', $request->unit_id)
   							->where('as_shift_id', '!=', null)
   							//->where('shift_roaster_status', 0)
   							->where('as_status', 1)
   							->distinct('as_shift_id')
   							->pluck('as_shift_id')
   							->toArray();
   		// $t_shifts=Shift::where('hr_shift_unit_id', $request->unit_id)
   		// 					->pluck('hr_shift_name')
   		// 					->toArray();
      if($request->unit_id != null){
        $t_shifts = DB::select("SELECT
                    s1.hr_shift_id,
                    s1.hr_shift_name,
                    s1.hr_shift_code,
                    s1.hr_shift_start_time,
                    s1.hr_shift_end_time,
                    s1.hr_shift_break_time
                    FROM hr_shift s1
                    LEFT JOIN hr_shift s2
                    ON (s1.hr_shift_unit_id = s2.hr_shift_unit_id AND s1.hr_shift_name = s2.hr_shift_name AND s1.hr_shift_id < s2.hr_shift_id)
                    LEFT JOIN hr_unit AS u
                    ON u.hr_unit_id = s1.hr_shift_unit_id
                    WHERE s2.hr_shift_id IS NULL AND s1.hr_shift_unit_id= $request->unit_id
                  ");
      }else{
        $t_shifts = array();
      }
      
  	 	$shifts['current'] = $c_shifts;						
  	 	$shifts['target']  = $t_shifts;

   		return Response::json($shifts);	
   	}

   	public function getTargetShiftsUnitWise(Request $request){
   		return Response::json($shifts);	
   	}

    public function getAssociateByTypeUnitShiftRoaster(Request $request)
    {   
    	// dd($request->all());exit;
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
            if ($request->current_shift != null)
            {
                $query->where('as_shift_id', $request->current_shift);
            }
            $query->where("as_status", 1);
        })
        //->where('shift_roaster_status', 0)
        ->whereIn('as_unit_id', auth()->user()->unit_permissions())
        ->get();

        // show user id
        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
        $data['result'] = "";
        $data['total'] = count($employees);
        foreach($employees as $employee)
        {
        	$image = ($employee->as_pic == null?'/assets/images/avatars/profile-pic.jpg': $employee->as_pic);
	        $data['result'].= "<tr class='add'>
	                    <td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".$image."' class='small-image' onError='this.onerror=null;this.src=\"/assets/images/avatars/avatar2.png\"'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td>
	                    <td>$employee->as_name </td><td>$employee->as_shift_id </td></tr>";
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
      				'as_shift_id' => $request->target_shift
      		]);
      		$list_emp .= $as_id.', ';
      	}
      	// dd($list_emp);
      	log_file_write('Shift Assign Updated for: ', $list_emp);
      	return back()->with('success', 'Shift Assign Updated');
      }
    }
}
