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
    			$info = Employee::getEmployeeAssociateIdWise($input['associate']);
    			$notice = WarningNotice::getEmployeeMonthWiseNotice($input);
    			$firstManagerBan = '';
    			$secondManagerBan = '';
    			if($notice != null){
    				$firstManagerBan = $this->employeeBanglaName($notice->first_manager);
    				$secondManagerBan = $notice->second_manager != null?$this->employeeBanglaName($notice->second_manager):'';
    			}
    			// return $firstManagerBan;
    		}
    		return view('hr.operation.warning_notice.index', compact('info', 'notice', 'firstManagerBan', 'secondManagerBan'));
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
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
            	return '<a href="'.$jobCard.'" target="_blank">'.$data->associate_id.'</a>';
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
    	// return $input;
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
    		$data['first_manager'] = $this->employeeBanglaName($input['first_manager']);
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
    		$data['second_manager'] = $this->employeeBanglaName($input['second_manager']);
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
    		$data['third_manager'] = $this->employeeBanglaName($input['third_manager']);
    		return $data;
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		$data['msg'] = $bug;
    		return $data;
    	}
    }

    public function employeeBanglaName($value)
    {
    	return DB::table('hr_employee_bengali')->select('hr_bn_associate_name')->where('hr_bn_associate_id', $value)->pluck('hr_bn_associate_name')->first();
    }
}
