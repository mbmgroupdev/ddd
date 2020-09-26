<?php

namespace App\Http\Controllers\Hr\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\YearlyHolyDay;
use App\Models\Hr\Attendace;
use App\Models\Employee;
use App\Models\Hr\Leave;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use DB, ACL, PDF;

class EmployeeLeaveController extends Controller
{
	public function index(){
		$unit=Unit::select('hr_unit_id', 'hr_unit_name')->get();
		$unit_id='';
		$from='';
		$to='';
		return view("hr/reports/leave_of_employees_report", compact('unit', 'unit_id', 'from', 'to'));
	}

	public function searchResult(Request $request){
		// dd($request->all());
		// if(!isset($request->from_date)){ dd($request->all()); }
		$data = DB::table('hr_leave as L')
						->leftJoin('hr_as_basic_info as BI', 'BI.associate_id', 'L.leave_ass_id')
						->leftJoin('hr_unit as U', 'U.hr_unit_id', 'BI.as_unit_id')
						->leftJoin('hr_department AS DP', 'DP.hr_department_id', 'BI.as_department_id')
						->leftJoin('hr_designation as DG', 'DG.hr_designation_id', 'BI.as_designation_id')
						->select([
							'L.*',
							'BI.as_name',
							'U.hr_unit_id',
							'U.hr_unit_name',
							'DP.hr_department_name',
							'DG.hr_designation_name',
						])
						->where(function ($query) use ($request) {
								if(isset($request->unit_id) && !isset($request->from_date) && !isset($request->to_date)){
							    		$query->where(['U.hr_unit_id' => $request->unit_id]);
									}

								if(isset($request->unit_id) && !isset($request->from_date) && isset($request->to_date)){
										$query->where([
											['U.hr_unit_id', $request->unit_id], 
											['L.leave_to','<=',date('Y-m-d', strtotime($request->to_date))]
										]);	
									}

								if(isset($request->unit_id) && isset($request->from_date) && !isset($request->to_date)){
										$query->where([
											['U.hr_unit_id', $request->unit_id],
											['L.leave_from','>=', date('Y-m-d', strtotime($request->from_date))]
										]);	
									}

								if(!isset($request->unit_id) && isset($request->from_date) && isset($request->to_date)){
										$query->where([
											['L.leave_from','>=', date('Y-m-d', strtotime($request->from_date))],
											['L.leave_to','<=',date('Y-m-d', strtotime($request->to_date))]
										]);	
									}

								if(!isset($request->unit_id) && !isset($request->from_date) && isset($request->to_date)){
										$query->where([
											['L.leave_to','<=',date('Y-m-d', strtotime($request->to_date))]
										]);	
									}

								if(!isset($request->unit_id) && isset($request->from_date) && !isset($request->to_date)){
										$query->where([
											['L.leave_from','>=', date('Y-m-d', strtotime($request->from_date))]
										]);	
									}
								if(isset($request->unit_id) && isset($request->from_date) && isset($request->to_date)){
										$query->where([
											['U.hr_unit_id', '=', $request->unit_id],
											['L.leave_from','>=', date('Y-m-d', strtotime($request->from_date))],
											['L.leave_to','<=',date('Y-m-d', strtotime($request->to_date))]
										]);	
								}

						})
						->get();
						
						// dd($data);exit;

		$grouped_data = $data->groupBy('hr_unit_name');
		// dd($grouped_data);
		$unit_id='';
		$from='';
		$to='';

		if(isset($request->unit_id)){ $unit_id = $request->unit_id; }
		if(isset($request->from_date)){ $from = $request->from_date; }
		if(isset($request->to_date)){ $to = $request->to_date; }

		// dd($unit_id,  $from, $to);

		$unit=Unit::select('hr_unit_id', 'hr_unit_name')->get();
		return 	view("hr/reports/leave_of_employees_report", compact('unit','grouped_data','unit_id', 'from', 'to'));
	}
}