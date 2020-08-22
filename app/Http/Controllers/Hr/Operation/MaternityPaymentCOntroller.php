<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use Illuminate\Http\Request;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\SalaryAdjustDetails;
use DB,Response;
class MaternityPaymentCOntroller extends Controller
{
	public function index(){

		// $maternity_list = DB::table('hr_leave as b')->select([
		// 							'b.*',
		// 							'c.as_name',
		// 							'd.hr_unit_name_bn',
		// 							'd.hr_unit_short_name',
		// 							'e.hr_bn_associate_name',
		// 							'f.hr_department_name',
		// 							'f.hr_department_name_bn',
		// 							'g.hr_section_name',
		// 							'g.hr_section_name_bn',
		// 							'h.hr_subsec_name',
		// 							'h.hr_subsec_name_bn'
		// 						])
		// 						->leftJoin('hr_as_basic_info as c', 'c.associate_id', 'b.leave_ass_id')
		// 						->leftJoin('hr_unit as d', 'd.hr_unit_id', 'c.as_unit_id')
		// 						->leftJoin('hr_employee_bengali as e', 'e.hr_bn_associate_id', 'b.leave_ass_id')
		// 						->leftJoin('hr_department as f', 'f.hr_department_id', 'c.as_department_id')
		// 						->leftJoin('hr_section as g', 'g.hr_section_id', 'c.as_section_id')
		// 						->leftJoin('hr_subsection as h', 'h.hr_subsec_id', 'c.as_subsection_id')
		// 						->leftJoin('hr_salary_adjust_master as i', 'i.associate_id', 'b.leave_ass_id')
		// 						->leftJoin('hr_salary_adjust_details as j', 'j.salary_adjust_master_id', 'i.id')
		// 						->where(['b.leave_type'=>'Maternity', 'j.status' => 0])
		// 						->get();

								// dd($maternity_list);exit;
		$units = DB::table('hr_unit')->pluck('hr_unit_name', 'hr_unit_id');
		// dd($units);

		return view('hr.operation.maternity_payment', compact('units'));
	}

	public function getMaternityEmployees(Request $request){
		// dd($request->all());

		$emp_list = DB::table('hr_leave as b')->select([
										'b.leave_ass_id',
										'b.leave_status',
										'c.as_id',
										'c.as_name'
									])
								->leftJoin('hr_as_basic_info as c', 'c.associate_id','=','b.leave_ass_id')
								->where([
											'b.leave_type' => 'Maternity',
											'b.leave_status' => $request->approval_status, 
											'c.as_unit_id' => $request->unit_id 
										])
								->get();

								// dd($emp_list);exit;
		return Response::json($emp_list);
	}

	public function getMaternityEmployeeDetails(Request $request){

		$emp_details = DB::table('hr_leave as b')->select([
									'b.*',
									'c.as_name',
									'd.hr_unit_name_bn',
									'd.hr_unit_short_name',
									'd.hr_unit_address_bn',
									'e.hr_bn_associate_name',
									'f.hr_department_name',
									'f.hr_department_name_bn',
									'g.hr_section_name',
									'g.hr_section_name_bn',
									'h.hr_subsec_name',
									'h.hr_subsec_name_bn',
									'k.hr_designation_name_bn',
									'j.status as maternity_salary_given_status'
								])
								->leftJoin('hr_as_basic_info as c', 'c.associate_id', 'b.leave_ass_id')
								->leftJoin('hr_unit as d', 'd.hr_unit_id', 'c.as_unit_id')
								->leftJoin('hr_employee_bengali as e', 'e.hr_bn_associate_id', 'b.leave_ass_id')
								->leftJoin('hr_department as f', 'f.hr_department_id', 'c.as_department_id')
								->leftJoin('hr_section as g', 'g.hr_section_id', 'c.as_section_id')
								->leftJoin('hr_subsection as h', 'h.hr_subsec_id', 'c.as_subsection_id')
								->leftJoin('hr_salary_adjust_master as i', 'i.associate_id', 'b.leave_ass_id')
								->leftJoin('hr_salary_adjust_details as j', 'j.salary_adjust_master_id', 'i.id')
								->leftJoin('hr_designation as k', 'k.hr_designation_id', 'c.as_designation_id')
								->where([
											'c.as_id'=>$request->emp_id
										])
								->first();

		// dd($emp_details);exit;

		//maternity payble calculation...
		$month_duration = (int) (date_diff(date_create($emp_details->leave_to), date_create($emp_details->leave_from))->format("%a")/ 30);
		$from_month =(int) date('m', strtotime($emp_details->leave_from));
		$from_Y 	=(int) date('Y', strtotime($emp_details->leave_from));
		$to_month  	=(int) date('m', strtotime($emp_details->leave_to));
		$to_Y  		=(int) date('Y', strtotime($emp_details->leave_to));

		// dd($from_month, $from_Y, $to_month, $to_Y);

		$current_salary = DB::table('hr_benefits')->where('ben_as_id', $emp_details->leave_ass_id)
												  ->select('ben_basic', 'ben_current_salary')
												  ->first();
		$total_payable  = $current_salary->ben_current_salary * $month_duration;
		// dd($total_payable);exit;
		
		$emp_details->month_duration = $month_duration; 
		$emp_details->current_salary = $current_salary->ben_current_salary;
		$emp_details->total_payable  = $total_payable;
		$emp_details->from_month  = $from_month;
		$emp_details->from_Y  = $from_Y;
		$emp_details->to_month  = $to_month;
		$emp_details->to_Y  = $to_Y;
		$emp_details->basic_sal  = $current_salary->ben_basic;

		// dd($emp_details);exit;

		return Response::json($emp_details);

	}

	public function saveMaternityDisburse(Request $request){

		try{
				$m = (int) date('m', strtotime($request->from));
				$y = (int) date('Y', strtotime($request->from));
				$month_diff = (int) $request->duration;
				$_amount    = (float) $request->amount;

				// dd($m,$y,$month_diff,$_amount,$request->emp_ass );exit;

				for($j=0; $j<$month_diff; $j++ ){
				//-----------master data insert
				    $master = new SalaryAdjustMaster();
				    $master->associate_id = $request->emp_ass;
				    if($m > 12){
				        $m=1;
				        $y+=1;
				        $master->month    = $m;
				        $master->year     = $y;
				        $m+=1;
				    }
				    else{
				        $master->month    = $m;
				        $master->year     = $y;
				        $m+=1;
				    }
				    
				    $master->save();


				//-----------details insert
				    $detail = new SalaryAdjustDetails();
				    $detail->salary_adjust_master_id = $master->id;
				    $detail->date                    = date('Y-m-d');
				    $detail->amount                  = $_amount;
				    $detail->type                    = 4;  //4 is for maternity salary...
				    $detail->status                  = 1;  //1 means given
				    $detail->save();
				}   

			 	return Response::json(1);

		}catch(\Exception $e){
			return Response::json(0);
		}
			
        //-----------------------------------------------------------------------------
	}


}