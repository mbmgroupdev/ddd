<?php

namespace App\Http\Controllers\Hr\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hr\Location;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use DB;

class FileController extends Controller
{
	public function index()
	{
		$data['unitList']  = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->orderBy('hr_unit_name', 'desc')
                ->pluck('hr_unit_name', 'hr_unit_id');

        $data['locationList']  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');

        $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');

        $data['files'] = [
        	'job_application' => 'Job Application',
        	'appointment_letter' => 'Appointment Letter',
        	'nominee' => 'Nominee',
        	'background_verification' => 'Background Verification',
            'night_concern'=> 'Night Concern'
        ];

        return view('hr.employee.files.index', $data);
	}

	public function getFile(Request $request)
	{
		$input = $request->all();

		$input['area']       = isset($request['area'])?$request['area']:'';
		$input['unit']       = isset($request['unit'])?$request['unit']:'';
		$input['location']   = isset($request['location'])?$request['location']:'';
        $input['department'] = isset($request['department'])?$request['department']:'';
        $input['otnonot'] = isset($request['otnonot'])?$request['otnonot']:'';
        $input['line']    = isset($request['line_id'])?$request['line_id']:'';
        $input['floor']   = isset($request['floor_id'])?$request['floor_id']:'';
        $input['section'] = isset($request['section'])?$request['section']:'';
        $input['subsec']  = isset($request['subSection'])?$request['subSection']:'';
        $input['as_id']  = isset($request['as_id'])?$request['as_id']:'';
        $input['doj_from']  = isset($request['doj_from'])?$request['doj_from']:'';
        $input['doj_to']  = isset($request['doj_to'])?$request['doj_to']:'';

        try{
        	ini_set('zlib.output_compression', 1);
        	// employee basic sql binding
            $emp = DB::table('hr_as_basic_info as b')
            	->select(
            		'b.*','a.*','bn.*','be.*',
            		# permanent district & upazilla
	                "per_dist.dis_name AS permanent_district",
	                "per_dist.dis_name_bn AS permanent_district_bn",
	                "per_upz.upa_name AS permanent_upazilla",
	                "per_upz.upa_name_bn AS permanent_upazilla_bn",
	                # present district & upazilla
	                "pres_dist.dis_name AS present_district",
	                "pres_dist.dis_name_bn AS present_district_bn",
	                "pres_upz.upa_name AS present_upazilla",
	                "pres_upz.upa_name_bn AS present_upazilla_bn"
            	)
	            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
	            ->whereIn('b.as_location', auth()->user()->location_permissions())
	            ->when(!empty($input['unit']), function ($q) use($input){
	               return $q->where('b.as_unit_id',$input['unit']);
	            })
	            ->when(!empty($input['area']), function ($q) use($input){
	               return $q->where('b.as_area_id',$input['area']);
	            })
	            ->when(!empty($input['department']), function ($q) use($input){
	               return $q->where('b.as_department_id',$input['department']);
	            })
	            ->when(!empty($input['section']), function ($q) use($input){
	               return $q->where('b.as_section_id',$input['section']);
	            })
	            ->when(!empty($input['subsec']), function ($q) use($input){
	               return $q->where('b.as_subsection_id',$input['subsec']);
	            })
	            ->when(!empty($input['line']), function ($q) use($input){
	               return $q->where('b.as_line_id',$input['line']);
	            })
	            ->when(!empty($input['floor']), function ($q) use($input){
	               return $q->where('b.as_floor_id',$input['floor']);
	            })
	            ->when(!empty($input['location']), function ($q) use($input){
	               return $q->where('b.as_location',$input['location']);
	            })
	            ->when(!empty($input['otnonot']), function ($q) use($input){
	               return $q->where('b.as_ot',$input['otnonot']);
	            })
	            ->when(!empty($input['as_id']), function ($q) use($input){
	               return $q->whereIn('b.associate_id',$input['as_id']);
	            })
	            ->when(!empty($input['doj_from']), function ($q) use($input){
	               return $q->where('b.as_doj','>=',$input['doj_from']);
	            })
	            ->when(!empty($input['doj_to']), function ($q) use($input){
	               return $q->where('b.as_doj','<=',$input['doj_to']);
	            })
	            //->where('b.as_status', 1)
	            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "b.associate_id")
	            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
	            ->leftJoin('hr_benefits AS be', 'be.ben_as_id', '=' , 'b.associate_id')
	            #permanent district & upazilla
	            ->leftJoin('hr_dist AS per_dist', 'per_dist.dis_id', '=', 'a.emp_adv_info_per_dist')
	            ->leftJoin('hr_upazilla AS per_upz', 'per_upz.upa_id', '=', 'a.emp_adv_info_per_upz')
	            #present district & upazilla
	            ->leftJoin('hr_dist AS pres_dist', 'pres_dist.dis_id', '=', 'a.emp_adv_info_pres_dist')
	            ->leftJoin('hr_upazilla AS pres_upz', 'pres_upz.upa_id', '=', 'a.emp_adv_info_pres_upz')
	            ->orderBy('b.as_oracle_sl')
	            ->orderBy('b.temp_id');

		    if($input['files'] == 'night_concern'){
		    	$emp->where('as_gender','Female');
		    }

		    $data['employees'] = $emp->get();

	        $data['unit'] = unit_by_id();
            $data['line'] = line_by_id();
            $data['floor'] = floor_by_id();
            $data['department'] = department_by_id();
            $data['designation'] = designation_by_id();
            $data['section'] = section_by_id();
            $data['subSection'] = subSection_by_id();
            $data['area'] = area_by_id();
            $data['location'] = location_by_id();

            $view = '';
            if($input['files'] == 'night_concern'){
		        $view = view('hr.employee.files.night_concern', $data)->render();
		    }else if($input['files'] == 'job_application'){
		        $view = view('hr.employee.files.job_application', $data)->render();
		    }else if($input['files'] == 'appointment_letter'){
		        $view = view('hr.employee.files.appointment_letter_bn', $data)->render();
		    }else if($input['files'] == 'nominee'){
		        $view = view('hr.employee.files.nominee', $data)->render();
		    }else if($input['files'] == 'background_verification'){
		        $view = view('hr.employee.files.background_verification', $data)->render();
		    }

	        return response([
	        	'view' => $view
	        ]);

        }catch(\Exception $e){
        	return response(['msg' => $e->getMessage()]);
        }

	}
}
