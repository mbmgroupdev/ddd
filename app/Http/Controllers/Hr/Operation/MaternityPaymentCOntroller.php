<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use App\Models\Hr\Absent;
use App\Models\Hr\MaternityLeave;
use App\Models\Hr\MaternityMedical;
use App\Models\Hr\MaternityMedicalRecord;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\SalaryAdjustDetails;
use DB,Response,DataTables,Validator;

class MaternityPaymentController extends Controller
{

	public function index()
	{
		return view('hr.operation.maternity.maternity_list');
	}

	public function showForm()
	{

		return view('hr.operation.maternity.maternity_application');
	}

	public function listData()
	{
		$data = DB::table('hr_maternity_leave as l')
				->select('l.*','b.as_name','m.id as medical')
				->leftJoin('hr_as_basic_info AS b','l.associate_id', 'b.associate_id')
				->leftJoin('hr_maternity_medical AS m','l.id', 'm.hr_maternity_leave_id')
				->orderBy('l.id','DESC')
				->get();

		return DataTables::of($data)->addIndexColumn()
	            ->editColumn('associate_id', function ($data) {
		                return HtmlFacade::link("hr/recruitment/employee/show/{$data->associate_id}",$data->associate_id,['class' => 'employee-att-details']);
	            })
	            ->addColumn('action', function($data){
	            	$buttons = '<div class="center">';
	            	if($data->medical){
	            		$buttons .= '<a class="btn btn-sm btn-primary" href=""><i class="las la-eye"></i></a>';
	            	}else{
	            		$buttons .= ' <a class="btn btn-sm btn-success" href=""><i class="las la-pen"></i></a>';
	            	}
	            	$buttons .= ' <a class="btn btn-sm btn-primary" href="'.url('hr/operation/maternity-medical-process/'.$data->id).'"><i class="las la-stethoscope"></i></a>';
	            	if($data->doctors_clearence){
	            		$buttons .= ' <a class="btn btn-sm btn-danger" href="">Pay</a> <a class="btn btn-sm btn-warning" href=""><i class="las la-file-invoice-dollar"></i></a>';
	            	}
	            	$buttons .= '</div>';
	            	return $buttons;
	            })
	            ->rawColumns(['associate_id','status','action'])
	            ->make(true);

		return [];
	}

	public function leaveApplication(Request $request)
	{
		$validator= Validator::make($request->all(),[
            'associate'=>'required',
    		'applied_date'=>'required'
    	]);
    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput()
    			->with('error', 'Please fillup all required fields!');
    	}
    	else
        {
        	$check = MaternityLeave::where(['associate_id' => $request->associate, 'status' => 0])->first();
        	if($check){
        		return back()
    			->withInput()
    			->with('error', 'Already have a pending request!');
        	}
         	DB::beginTransaction();
         	try {
         		$usg_report = '';
         		if($request->hasFile('usg_report')){
	                $file = $request->file('usg_report');
	                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
	                $dir  = '/assets/files/maternity/';
	                $file->move( public_path($dir) , $filename );
	                $usg_report = $dir.$filename;
	            }

         		$application = new MaternityLeave();
         		$application->associate_id = $request->associate;
         		$application->applied_date = $request->applied_date;
         		$application->no_of_son = $request->no_of_son;
         		$application->no_of_daughter = $request->no_of_daughter;
         		$application->last_child_age = $request->last_child_age;
         		$application->husband_name = $request->husband_name;
         		$application->husband_age = $request->husband_age;
         		$application->husband_occupasion = $request->husband_occupasion;
         		$application->usg_report = $usg_report ;
         		$application->created_by = auth()->id();
         		$application->save();

         		DB::commit();
         		log_file_write("Materny leave application for ".$request->associate_id, $application->id);

            	return back()
                    ->with('success', 'Maternity leave application saved succesfully and proceeded to doctor for verification!');
            }
            catch (\Exception $e) {
            	DB::rollback();
            	$bug = $e->getMessage();
            	return redirect()->back()->with('error',$bug);
          }

    	}
	}

	public function view($id, Request $request)
	{
		$leave = MaternityLeave::with('medical','medical.record')->findOrFail($id);

		return view('hr.operation.maternity.maternity_view','leave');

	}

	public function medicalProcess($id, Request $request)
	{

		$leave = MaternityLeave::with('medical','medical.record')->findOrFail($id);

		$employee = get_employee_by_id($leave->associate_id);

		return view('hr.operation.maternity.maternity_doctor',compact('leave','employee'));

	}

	public function storeMedicalBasic(Request $request)
	{
		$validator= Validator::make($request->all(),[
            'hr_maternity_leave_id'=>'required',
    		'blood_group'=>'required',
    		'edd' => 'required'

    	]);
    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput()
    			->with('error', 'Please fillup all required fields!');
    	}
    	else
        {
        	
         	DB::beginTransaction();
         	try {

         		$check = MaternityMedical::where(['hr_maternity_leave_id' => $request->hr_maternity_leave_id])->first();
	        	if($check){
	        		// update data
	        		$check->hr_maternity_leave_id = $request->hr_maternity_leave_id;
	         		$check->blood_group = $request->blood_group;
	         		$check->lmp = $request->lmp;
	         		$check->edd = $request->edd;
	         		$check->anemia = $request->anemia;
	         		$check->heart = $request->heart;
	         		$check->lungs = $request->lungs;
	         		$check->rash = $request->rash;
	         		$check->others = $request->others;
	         		$check->past_major_diseases = $request->past_major_diseases;
	         		$check->pregnant_complexity = $request->pregnant_complexity;
	         		$check->pregnant_complexity_details = $request->pregnant_complexity_details;
	         		$check->operation = $request->operation;
	         		$check->operation_details = $request->operation_details;
	         		$check->stl_rtl = $request->stl_rtl;
	         		$check->stl_rtl_details = $request->stl_rtl_details;
	         		$check->drug_addiction = $request->drug_addiction;
	         		$check->drug_addiction_details = $request->drug_addiction_details;
	         		$check->alergy = $request->alergy;

	         		$check->save();

	         		DB::commit();
	         		log_file_write("Materny leave medical information for ".$request->associate_id, $check->id);
	            	return back()
	                    ->with('success', 'Maternity basic checkup updated successfully!');

	        	}else{
	         		$med = new MaternityMedical();;
	         		$med->hr_maternity_leave_id = $request->hr_maternity_leave_id;
	         		$med->blood_group = $request->blood_group;
	         		$med->lmp = $request->lmp;
	         		$med->edd = $request->edd;
	         		$med->anemia = $request->anemia;
	         		$med->heart = $request->heart;
	         		$med->lungs = $request->lungs;
	         		$med->rash = $request->rash;
	         		$med->others = $request->others;
	         		$med->past_major_diseases = $request->past_major_diseases;
	         		$med->pregnant_complexity = $request->pregnant_complexity;
	         		$med->pregnant_complexity_details = $request->pregnant_complexity_details;
	         		$med->operation = $request->operation;
	         		$med->operation_details = $request->operation_details;
	         		$med->stl_rtl = $request->stl_rtl;
	         		$med->stl_rtl_details = $request->stl_rtl_details;
	         		$med->drug_addiction = $request->drug_addiction;
	         		$med->drug_addiction_details = $request->drug_addiction_details;
	         		$med->alergy = $request->alergy;
	         		$med->created_by = auth()->id();
	         		$med->save();

	         		DB::commit();
	         		log_file_write("Materny leave medical for ".$request->associate_id, $med->id);
	            	return back()
	                    ->with('success', 'Maternity basic checkup saved successfully! Please fillup first routine checkup!');

	        	}


            }
            catch (\Exception $e) {
            	DB::rollback();
            	$bug = $e->getMessage();
            	return redirect()->back()->with('error',$bug);
          }

    	}
	}


	public function storeMedicalRecord(Request $request)
	{
		$validator= Validator::make($request->all(),[
            'hr_maternity_medical_id'=>'required',
    		'checkup_date'=>'required',
    		'weight' => 'required',
    		'bp' => 'required',
    		'next_checkup_date' => 'required'

    	]);
    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput()
    			->with('error', 'Please fillup all required fields!');
    	}
    	else
        {
        	
         	DB::beginTransaction();
         	try {

         		/*$check = MaternityMedicalRecord::findOrFail($request->id)->first();
	        	if($check){
	        		// update data
	         		$//check->save();

	         		DB::commit();
	         		log_file_write("Materny leave medical record information for ".$request->associate_id, $check->id);
	            	return back()
	                    ->with('success', 'Maternity routine checkup updated successfully!');


	        		DB::commit();
	         		log_file_write("Materny leave medical information application for ".$request->associate_id, $application->id);
	            	return back()
	                    ->with('success', 'Maternity leave application saved succesfully and proceeded to doctor for verification!');
	        	}else{*/
	         		$med = new MaternityMedicalRecord();;
	         		$med->hr_maternity_medical_id = $request->hr_maternity_medical_id;
	         		$med->checkup_date = $request->checkup_date;
	         		$med->weight = $request->weight;
	         		$med->bp = $request->bp;
	         		$med->edema = $request->edema;
	         		$med->jaundice = $request->jaundice;
	         		$med->uterus_height = $request->uterus_height;
	         		$med->baby_position = $request->baby_position;
	         		$med->baby_movement = $request->baby_movement;
	         		$med->baby_heartbeat = $request->baby_heartbeat;
	         		$med->albumine = $request->albumine;
	         		$med->sugar = $request->sugar;
	         		$med->others = $request->others;
	         		$med->next_checkup_date = $request->next_checkup_date;
	         		$med->comments = $request->comments;
	         		$med->created_by = auth()->id();
	         		$med->save();

	         		DB::commit();
	         		log_file_write("Materny leave medical routine checkup for ".$request->associate_id, $med->id);
	            	return back()
	                    ->with('success', 'Maternity routine checkup saved successfully!');

	        	/*}*/


            }
            catch (\Exception $e) {
            	DB::rollback();
            	$bug = $e->getMessage();
            	return redirect()->back()->with('error',$bug);
          }

    	}
	}


	public function getMaternityEmployees(Request $request){
		

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

		return Response::json($emp_list);
	}

	public function getMaternityEmployeeDetails(Request $request)
	{

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