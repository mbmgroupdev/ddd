<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class OperationLoadEmployeeController extends Controller
{
    public function getShiftEmployee(Request $request)
    {
    	$input = $request->all();
    	// return $input;
    	try {
            
	        if($request->searchDate != null) {
	            $year = date('Y', strtotime($request->searchDate));
	            $month = date('n', strtotime($request->searchDate));
	            $day = date('j', strtotime($request->searchDate));
	        }else{
	        	$year  = date('Y');
		        $month = date('n');
		        $day   = date('j');
	        }
    		$queryData = DB::table('hr_as_basic_info AS emp')
    		->where('emp.as_status', 1)
    		->where('emp.as_unit_id', $input['unit'])
    		->whereNotNull('emp.as_shift_id')
    		->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['shift']), function ($query) use($input){
               return $query->where('emp.as_shift_id',$input['shift']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['emp_type']), function ($query) use($input){
               return $query->where('emp.as_emp_type_id',$input['emp_type']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subsection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subsection']);
            });
            
            $getEmployee = $queryData->select('emp.as_id', 'emp.associate_id', 'emp.as_name', 'emp.as_oracle_code', 'emp.as_shift_id', 'emp.as_gender', 'emp.as_pic')->get();

            // today shift roster
            $employees = $queryData->select('emp.as_id')->pluck('emp.as_id')->toArray();
            // return $employees;
            $todayShift = DB::table('hr_shift_roaster')
        	->select('shift_roaster_user_id','day_'.$day)
            ->where('shift_roaster_year', $year)
            ->where('shift_roaster_month', $month)
            ->whereIn('shift_roaster_user_id', $employees)
            ->get()->keyBy('shift_roaster_user_id')->toArray();

            $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
	        $data['result'] = "";

	        $data['shiftRosterCount'] = [];
	        $data['shiftDefaultCount'] = [];
	        // $data['shiftRosterCount2'] = [];
	        $data['total'] = 0;
	        $today = 'day_'.$day;
	        // dd($todayShift[55]->$today??'');
	        foreach($getEmployee as $employee)
        	{
        		$checkShift = $todayShift[$employee->as_id]??'';
        		if(($checkShift != '') && ($todayShift[$employee->as_id]->$today != '')){
        			$shiftCode = $todayShift[$employee->as_id]->$today.' - Change';
        		}else{
        			$shiftCode = $employee->as_shift_id.' - Default';
        		}
                $image = emp_profile_picture($employee);
        		$data['total'] += 1;
                $data['result'].= "<tr class='add'><td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".$image."' class='small-image' style='height:40px;width:auto'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td><td>$employee->as_oracle_code </td><td>$employee->as_name </td><td>$shiftCode </td></tr>";
        	}

            return $data;
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		return 'error';	
    	}
    }

    public function getHolidayRosterEmployee(Request $request)
    {
        $input = $request->all();
        // return $input;
        try {
            $year  = date('Y');
            $month = date('n');
            $day   = date('j');

            $queryData = DB::table('hr_as_basic_info AS emp')
            ->where('emp.as_status', 1)
            ->where('emp.as_unit_id', $input['unit'])
            ->whereNotNull('emp.as_shift_id')
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['shift_roster_status']), function ($query) use($input){
               return $query->where('emp.shift_roaster_status',$input['shift_roster_status']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['emp_type']), function ($query) use($input){
               return $query->where('emp.as_emp_type_id',$input['emp_type']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subsection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subsection']);
            });
            
            $getEmployee = $queryData->select('emp.as_id', 'emp.associate_id', 'emp.as_oracle_code', 'emp.as_name', 'emp.as_shift_id', 'emp.as_gender', 'emp.as_pic')->get();

            // today shift roster
            $employees = $queryData->select('emp.as_id')->pluck('emp.as_id')->toArray();
            
            $todayShift = DB::table('hr_shift_roaster')
            ->select('shift_roaster_user_id','day_'.$day)
            ->where('shift_roaster_year', $year)
            ->where('shift_roaster_month', $month)
            ->whereIn('shift_roaster_user_id', $employees)
            // ->pluck('day_'.$day)
            ->get()->keyBy('shift_roaster_user_id')->toArray();

            $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";
            $data['result'] = "";

            $data['shiftRosterCount'] = [];
            $data['shiftDefaultCount'] = [];
            // $data['shiftRosterCount2'] = [];
            $data['total'] = 0;
            $today = 'day_'.$day;
            // dd($todayShift[55]->$today??'');
            foreach($getEmployee as $employee)
            {
                $checkShift = $todayShift[$employee->as_id]??'';
                if(($checkShift != '') && ($todayShift[$employee->as_id]->$today != '')){
                    $shiftCode = $todayShift[$employee->as_id]->$today.' - Change';
                }else{
                    $shiftCode = $employee->as_shift_id.' - Default';
                }
                $image = emp_profile_picture($employee);
                $data['total'] += 1;
                $data['result'].= "<tr class='add'><td><input type='checkbox' value='$employee->associate_id' name='assigned[$employee->as_id]'/></td><td><span class=\"lbl\"> <img src='".$image."' class='small-image' style='height:40px;width:auto'> </span></td><td><span class=\"lbl\"> $employee->associate_id</span></td><td><span class=\"lbl\"> $employee->as_oracle_code</span></td><td>$employee->as_name </td><td>$shiftCode </td></tr>";
            }

            return $data;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug; 
        }
    }
}
