<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\WarningNotice;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;

class WarningNoticeController extends Controller
{
    public function index(Request $request)
    {
    	$input = $request->all();
    	$info = '';
    	$notices = '';
    	
    	try {
    		if($input['associate'] != null && $input['month_year'] != null){
                $designation = designation_by_id();
                $department = department_by_id();

    			$info = DB::table('hr_as_basic_info as b')
                    ->select(
                        'b.*','bn.*',
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
                    ->where('associate_id', $input['associate'])
                    ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "b.associate_id")
                    ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
                    #permanent district & upazilla
                    ->leftJoin('hr_dist AS per_dist', 'per_dist.dis_id', '=', 'a.emp_adv_info_per_dist')
                    ->leftJoin('hr_upazilla AS per_upz', 'per_upz.upa_id', '=', 'a.emp_adv_info_per_upz')
                    #present district & upazilla
                    ->leftJoin('hr_dist AS pres_dist', 'pres_dist.dis_id', '=', 'a.emp_adv_info_pres_dist')
                    ->leftJoin('hr_upazilla AS pres_upz', 'pres_upz.upa_id', '=', 'a.emp_adv_info_pres_upz')
                    ->first();

                $info->department = isset($department[$info->as_department_id])?$department[$info->as_department_id]:null;
                $info->designation = isset($designation[$info->as_designation_id])?$designation[$info->as_designation_id]:null;

    			$notice = WarningNotice::getEmployeeMonthWiseNotice($input);
                $getUnit = unit_by_id();
                $unitAddress = $getUnit[$info->as_unit_id];
                
    			$firstManagerBan = '';
    			$secondManagerBan = '';
    			if($notice != null){
    				$firstManagerBan = $this->getManagerInfo($notice->first_manager);
    				$secondManagerBan = $notice->second_manager != null?$this->getManagerInfo($notice->second_manager):'';
    			}
    			// dd($firstManagerBan);
    		    return view('hr.operation.warning_notice.index', compact('info', 'notice', 'firstManagerBan', 'secondManagerBan', 'unitAddress'));
    		}else{
                toastr()->error('Something wrong, please try again');
                return back();
            }
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
            toastr()->error($bug);
    		return back();
    	}
    }

    public function list(Request $request)
    {
    	$input = $request->all();
    	if(!isset($input['month_year'])){
    		$input['month_year'] = date('Y-m');
    	}
    	// return $input;
    	return view('hr.reports.warning_notices', compact('input'));
    }

    public function listData(Request $request)
    {
    	$input = $request->all();
    	$data = WarningNotice::
        where('month_year', $input['month_year'])
        ->with(array('employee'=>function($data){
            $data->whereIn('as_unit_id', auth()->user()->unit_permissions());
        }))
        ->get();
    	
    	$getSection = section_by_id();
    	$getDesignation = designation_by_id();
    	$getUnit = unit_by_id();
    	return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('pic', function($data){
            	return '<img src="'.emp_profile_picture($data->employee).'" class="small-image min-img-file">';
            })
            ->addColumn('associate_id', function($data) use ($input){
            	$month = $input['month_year'];
            	$jobCard = url("hr/operation/job_card?associate=$data->associate_id&month_year=$month");
            	// return '<a href="'.$jobCard.'" target="_blank">'.$data->associate_id.'</a>';
                return '<a class="job_card" data-name="'.$data->employee['as_name'].'" data-associate="'.$data->associate_id.'" data-month-year="'.$month.'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">'.$data->associate_id.'</a>';
            })
            ->addColumn('hr_unit_name', function($data) use ($getUnit){
            	return $getUnit[$data->employee['as_unit_id']]['hr_unit_short_name']??'';
            })
            ->addColumn('as_name', function($data){
            	return $data->employee['as_name']. ' '.$data->employee['as_contact'];
            })
            ->addColumn('section', function($data) use ($getSection){
            	return $getSection[$data->employee['as_section_id']]['hr_section_name']??'';
            })
            ->addColumn('hr_designation_name', function($data) use ($getDesignation){
            	return $getDesignation[$data->employee['as_designation_id']]['hr_designation_name']??'';
            })
            ->addColumn('action', function($data) use ($input){
            	$month = $input['month_year'];
            	$url = url("hr/operation/warning-notice?associate=$data->associate_id&month_year=$month");
            	return '<a class="btn btn-sm btn-success" href="'.$url.'" target="_blank" data-toggle="tooltip" data-placement="top" title="" data-original-title="Action This Employee"><i class="fa fa-eye"></i></a>';
            })
            ->rawColumns([
                'pic', 'associate_id', 'hr_unit_name', 'as_name', 'reason', 'section', 'hr_designation_name', 'action'
            ])
            ->make(true);
    }

    public function firstStep(Request $request)
    {
    	$data['type'] = 'error';
    	$input = $request->all();
        
    	try {
    		$check['associate'] = $input['associate_id'];
    		$check['month_year'] = $input['month_year'];
    		$notice = WarningNotice::getEmployeeMonthWiseNotice($check);
    		if($notice != null){
    			$notice = WarningNotice::findOrFail($notice->id);
    			$notice->update($input);
    		}else{
    			WarningNotice::create($input)->id;
    		}
    		$data['type'] = 'success';
    		$data['msg'] = $input['associate_id'].' - Employee First Warning Notice Generate Successfully';
    		$data['issue_date'] = eng_to_bn(date('d-m-Y', strtotime($input['first_step_date'])));
    		$data['start_date'] = eng_to_bn(date('d-m-Y', strtotime($input['start_date'])));
    		$data['first_response'] = eng_to_bn($input['first_response']);
    		$data['first_manager'] = $this->getManagerInfo($input['first_manager']);
           
    		return $data;
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		$data['msg'] = $bug;
    		return $data;
    	}
    }

    public function secondStep(Request $request)
    {
    	$data['type'] = 'error';
    	$input = $request->all();
    	// return $input;
    	try {
    		$check['associate'] = $input['associate_id'];
    		$check['month_year'] = $input['month_year'];
    		$notice = WarningNotice::getEmployeeMonthWiseNotice($check);
    		if($notice != null){
    			$notice = WarningNotice::findOrFail($notice->id);
    			$notice->update($input);
    		}else{
    			$data['msg'] = 'Something wrong, Please Reload Page!';
    			return $data;
    		}
    		$data['type'] = 'success';
    		$data['msg'] = $input['associate_id'].' - Employee Second Warning Notice Generate Successfully';
    		$data['second_issue_date'] = eng_to_bn(date('d-m-Y', strtotime($input['second_step_date'])));
    		$data['issue_date'] = eng_to_bn(date('d-m-Y', strtotime($notice->first_step_date)));
    		$data['start_date'] = eng_to_bn(date('d-m-Y', strtotime($notice->start_date)));
    		$data['second_response'] = eng_to_bn($input['second_response']);
    		$data['second_manager'] = $this->getManagerInfo($input['second_manager']);
            
    		return $data;
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		$data['msg'] = $bug;
    		return $data;
    	}
    }

    public function thirdStep(Request $request)
    {
    	$data['type'] = 'error';
    	$input = $request->all();
    	// return $input;
    	try {
    		$check['associate'] = $input['associate_id'];
    		$check['month_year'] = $input['month_year'];
    		$notice = WarningNotice::getEmployeeMonthWiseNotice($check);
    		if($notice != null){
    			$notice = WarningNotice::findOrFail($notice->id);
    			$notice->update($input);
    		}else{
    			$data['msg'] = 'Something wrong, Please Reload Page!';
    			return $data;
    		}
    		$data['type'] = 'success';
    		$data['msg'] = $input['associate_id'].' - Employee Third Warning Notice Generate Successfully';
    		$data['third_issue_date'] = eng_to_bn(date('d-m-Y', strtotime($input['third_step_date'])));
    		$data['second_issue_date'] = eng_to_bn(date('d-m-Y', strtotime($notice->second_step_date)));
    		$data['issue_date'] = eng_to_bn(date('d-m-Y', strtotime($notice->first_step_date)));
    		$data['start_date'] = eng_to_bn(date('d-m-Y', strtotime($notice->start_date)));
    		$data['first_response'] = eng_to_bn($notice->first_response);
    		$data['second_response'] = eng_to_bn($notice->second_response);
    		$data['third_manager'] = $this->getManagerInfo($input['third_manager']);
            
    		return $data;
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		$data['msg'] = $bug;
    		return $data;
    	}
    }

    public function getManagerInfo($value)
    {
        $designation = designation_by_id();
        $department = department_by_id();
        $unit = unit_by_id();

    	$data =  DB::table('hr_as_basic_info as b')
            ->leftJoin('hr_employee_bengali as bn','b.associate_id','bn.hr_bn_associate_id')
            ->select(
                'b.as_unit_id',
                'bn.hr_bn_associate_name as name',
                'b.as_department_id as department',
                'b.as_designation_id as designation')
            ->where('hr_bn_associate_id', $value)
            ->first();

        if($data){
            $data->department = isset($department[$data->department])?$department[$data->department]['hr_department_name_bn']:'';
            $data->designation = isset($designation[$data->designation])?$designation[$data->designation]['hr_designation_name_bn']:'';
            $data->unit = isset($unit[$data->as_unit_id])?$unit[$data->as_unit_id]['hr_unit_name_bn']:'';
        }

        return $data;
    }
}
