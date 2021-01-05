<?php

namespace App\Http\Controllers\Hr\Recruitment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessUnitWiseSalary;
use App\Models\Hr\Benefits;
use App\Models\Hr\Designation;
use App\Models\Hr\SalaryStructure;
use App\Models\Hr\Unit;
use App\Models\Hr\EmpType;
use App\Models\Employee;
use App\Models\Hr\Increment;
use App\Models\Hr\Promotion;
use App\Models\Hr\FixedSalary;
use App\Models\Hr\IncrementType;
use App\Models\Hr\OtherBenefits;
use App\Models\Hr\OtherBenefitAssign;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\SalaryAdjustDetails;
use Carbon\Carbon;
use Validator,DB, DataTables, ACL,Auth;
class BenefitController extends Controller
{
    public function benefits(Request $request)
    {
        
        $id=$request->associate_id;
        $structure= DB::table('hr_salary_structure')->where('status', 1)->select(['hr_salary_structure.*'])->first();


        return view('hr/recruitment/benefits', compact('structure','id'));
    }

    public function benefitStore(Request $request)
    {
        $user= Auth::user()->associate_id;
        $validator= Validator::make($request->all(), [
            'ben_joining_salary'  => 'required',
            'salary_type'         => 'required',
            'ben_basic'           => 'required',
            'ben_house_rent'      => 'required',
            'ben_medical'         => 'required',
            'ben_transport'       => 'required',
            'ben_food'            => 'required'
        ]);

        /*$validator->sometimes('ben_as_id', 'unique:hr_benefits', function($request) {
            return $request->ben_id != '';
        });*/

        $validator->sometimes('ben_cash_amount', 'required', function($request) {
            return $request->salary_type == 'Cash';
        });
        $validator->sometimes('ben_bank_amount', 'required', function($request) {
            return $request->salary_type == 'Bank';
        });

        if($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fileds!.');
        }
       
        try {
            $benefits = Benefits::where('ben_as_id', $request->ben_as_id)->first();
            if(!$benefits){
                $benefits= new Benefits();
                $benefits->ben_as_id           = $request->ben_as_id ;
                $benefits->ben_joining_salary  = $request->ben_joining_salary ;
            }
            $benefits->ben_current_salary      = $request->ben_joining_salary ;
            $benefits->ben_cash_amount         = $request->ben_cash_amount??0 ;
            $benefits->bank_name               = $request->bank_name??null ;
            $benefits->bank_no                 = $request->bank_no??null ;
            $benefits->ben_bank_amount         = $request->ben_bank_amount??0 ;
            $benefits->ben_tds_amount          = $request->ben_tds_amount??0 ;
            $benefits->ben_basic               = $request->ben_basic ;
            $benefits->ben_house_rent          = $request->ben_house_rent ;
            $benefits->ben_medical             = $request->ben_medical ;
            $benefits->ben_transport           = $request->ben_transport ;
            $benefits->ben_food                = $request->ben_food;
            $benefits->ben_status              = 1 ;
            $benefits->ben_updated_by          = $user;
            $benefits->ben_updated_at          = date('Y-m-d H:i:s');

            if ($benefits->save())
            {
                // process salary
                $emp = DB::table('hr_as_basic_info')->where('associate_id',$request->ben_as_id)->first();
                $tableName = get_att_table($emp->as_unit_id);

                $queue = (new ProcessUnitWiseSalary($tableName, date('m'), date('Y'), $emp->as_id, date('d')))
                        ->onQueue('salarygenerate')
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);

                // update previous month also
                $yearMonth = date("Y-m-d", strtotime("-1 months"));
                $lock['month'] = date('m', strtotime($yearMonth));
                $lock['year'] = date('Y', strtotime($yearMonth));
                $lock['unit_id'] = $emp->as_unit_id;
                $lockActivity = monthly_activity_close($lock);
                
                if($lockActivity  == 0){
                    
                    $queue = (new ProcessUnitWiseSalary($tableName, $lock['month'], $lock['year'] , $emp->as_id, date('t', strtotime($yearMonth))))
                            ->onQueue('salarygenerate')
                            ->delay(Carbon::now()->addSeconds(2));
                            dispatch($queue);
                }

               
                log_file_write("Employee benefits updated successfully", $benefits->ben_id);

                return redirect('hr/payroll/employee-benefit?associate_id='.$request->ben_as_id)
                        ->withInput()
                        ->with('success', 'Employee benefits updated successfully.');

            }
            else
            {
                return back()
                        ->withInput()->with('error', 'Please try again.');
                
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

 
    public function benefitList()
    {
        
        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');

        return view('hr/payroll/benefit_list', compact('unitList'));
    }

    public function benefitListData()
    {
        
        $data = DB::table('hr_benefits AS b')
                ->where('b.ben_status',1)
                ->select(
                    'b.ben_id',
                    'b.ben_as_id',
                    'b.ben_joining_salary',
                    'b.ben_current_salary',
                    'b.ben_basic',
                    'b.ben_house_rent',
                    'b.ben_cash_amount',
                    'b.ben_bank_amount',
                    'b.ben_tds_amount',
                    'b.bank_name',
                    'b.bank_no',
                    'a.as_name',
                    'a.as_oracle_code',
                    'a.as_unit_id',
                    'u.hr_unit_name AS unit_name'
                )
                ->leftJoin('hr_as_basic_info as a', 'a.associate_id', '=', 'b.ben_as_id')
                ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'a.as_unit_id')
                ->whereIn('a.as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('a.as_location', auth()->user()->location_permissions())
                ->whereNotIn('a.as_id', auth()->user()->management_permissions())
                ->where('a.as_status',1)
                ->orderBy('b.ben_id', 'desc')
                ->get();

            $perm = false;
            if(auth()->user()->canany(['Assign Benifit']) || auth()->user()->hasRole('Super Admin') ){
                $perm = true;
            }

            return DataTables::of($data)

            ->addColumn('payment_method', function($data){
                if($data->ben_bank_amount == 0 && $data->ben_cash_amount > 0){
                    $method = "Cash";
                }elseif($data->ben_bank_amount > 0 && $data->ben_cash_amount == 0){
                    $method = $data->bank_name.' '.$data->bank_no;
                }else{
                    $method = $data->bank_name." & Cash ".$data->bank_no;
                }
                return $method;
            })
            
            ->addColumn('action', function ($data) use ($perm) {
                if($perm){

                    return "<div class=\"btn-group\">
                        <a href=".url('hr/payroll/benefit/'.$data->ben_as_id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                            <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                        </a> 
                        <a href=".url('hr/payroll/employee-benefit?associate_id='.$data->ben_as_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                            <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                        </a>
                    </div>";
                }else{
                    return '';
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function benefitEdit($id)
    {

        $get_as_id = Employee::where('associate_id', $id)->first(['as_id']);
        $m_restriction =  auth()->user()->permitted_associate()->toArray(); //dd($m_restriction);
        $as_id=$get_as_id->as_id;

        // check if  id is restricted
        if (in_array($as_id, $m_restriction)) {

            return redirect()->to('hr/payroll/benefit_list')->with('error', 'You do not have permission!');
        }

        // check if  id is not restricted
        else  {

            $benefits= DB::table('hr_benefits AS b')
                ->where('b.ben_as_id','=', $id)
                ->where('b.ben_status','=','1')
                ->select(
                    'b.*'
                )
                ->first();
            $fixedSalary= DB::table('hr_fixed_emp_salary AS f')
                ->where('f.as_id','=', $id)
                ->select(
                    'f.*'
                )
                ->first();
                $structure= DB::table('hr_salary_structure')
                                ->where('status', 1)
                                ->select([
                                    'hr_salary_structure.*'
                                ])
                                ->first();
                // dd($structure);
                if(!empty($structure)){
                    $benefits->ben_medical= $structure->medical;
                    $benefits->ben_food= $structure->food;
                    $benefits->ben_transport= $structure->transport;

                    $basic=($benefits->ben_current_salary-($structure->medical+$structure->transport+$structure->food))/$structure->basic;
                    $benefits->ben_basic= number_format($basic, 3, '.', '');

                    $current = ($benefits->ben_current_salary-($structure->medical+$structure->transport+$structure->food))-$basic;
                    $benefits->ben_house_rent =number_format($current, 3, '.', '');
                }
            //Extra benefit item list
            $other_bnf_items= OtherBenefits::get();
            //associates existing Extra benefits
            $other_bnf_list= OtherBenefitAssign::where('associate_id', $id)->orderBy('item_id', "ASC")->pluck('item_id');
            $other_bnf_data= DB::table('hr_other_benefit_assign as b')
                                ->select([
                                    'b.*',
                                    'c.*'
                                ])
                                ->leftJoin('hr_other_benefit_item as c', 'b.item_id','=','c.id')
                                ->where('associate_id', $id)
                                ->get();


            //this code will add an extra column CHK to check whether one item is
            //selected for that user or not, if seleted then we will show the checkbox as
            //checked
            foreach ($other_bnf_items as $obi) {
                $chk=false;
                for($i=0; $i<sizeof($other_bnf_list); $i++) {
                    if($obi->id == $other_bnf_list[$i]){
                        $chk=true;
                        break;
                    }
                }
                if($chk){
                    $obi->chk=1;
                }
                else{
                    $obi->chk=0;
                }
            }
            //end chk code
            // dd($other_bnf_items, $other_bnf_list, $other_bnf_data);
            return view('hr/payroll/benefit_edit',compact('benefits', 'structure', 'other_bnf_items','fixedSalary', 'other_bnf_data'));
        }
    }


    public function benefitUpdate(Request $request)
    {
        $user= Auth::user()->associate_id;
        $validator= Validator::make($request->all(), [
            'ben_id'                  => 'required|max:11',
            'ben_as_id'               => 'required|max:10|min:10|alpha_num',
            'ben_current_salary'      => 'required',
            'ben_cash_amount'         => 'required',
            'ben_bank_amount'         => 'required',
            'ben_basic'               => 'required',
            'ben_house_rent'          => 'required',
            'ben_medical'             => 'required',
            'ben_transport'           => 'required',
            'ben_food'                => 'required'
        ]);

        if($validator->fails())
        {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fileds!.');
        }
        else
        {
            DB::table('hr_benefits')
            ->where('ben_as_id', $request->ben_as_id)
            ->update([
                'ben_as_id'               => $request->ben_as_id ,
                'ben_current_salary'      => $request->ben_current_salary,
                'ben_cash_amount'         => $request->ben_cash_amount,
                'ben_bank_amount'         => $request->ben_bank_amount,
                'ben_basic'               => $request->ben_basic,
                'ben_house_rent'          => $request->ben_house_rent,
                'ben_medical'             => $request->ben_medical,
                'ben_transport'           => $request->ben_transport,
                'ben_food'                => $request->ben_food,
                'ben_status'              => 1 ,
                'ben_updated_by'        => $user,
                'ben_updated_at'        => date('Y-m-d H:i:s')
            ]);

            // Full Salary Amount Update if Fixed checked
            if($request->fixed_check){


                    $check=FixedSalary::where('as_id',$request->ben_as_id)->exists();

                    // If Fixed Salary already exists then update
                    if($check){
                            DB::table('hr_fixed_emp_salary')
                               ->where('as_id', $request->ben_as_id)
                               ->update([

                                'joining_salary'    => $request->ben_joining_salary_fixed,
                                'fixed_amount'      => $request->ben_joining_salary_fixed,
                                'cash_amount'       => $request->ben_cash_amount_fixed,
                                'bank_amount'      => $request->ben_bank_amount_fixed,
                                'basic'             => $request->ben_basic_fixed,
                                'house_rent'        => $request->ben_house_rent_fixed,
                                'medical'           => $request->ben_medical_fixed,
                                'transport'         => $request->ben_transport_fixed,
                                'food'              => $request->ben_food_fixed,
                                'status'            => 1,
                                'updated_by'        => $user,
                                'updated_at'        => NOW()
                            ]);
                               $id=DB::table('hr_fixed_emp_salary')->where('as_id', $request->ben_as_id)->value('id');
                               log_file_write("Fixed Salary Updated", $id );

                            }
                    // If  Fixed Salary  Not exists then insert
                    else{

                            $fixSalary= new FixedSalary();
                            $fixSalary->as_id               = $request->ben_as_id ;
                            $fixSalary->joining_salary      = $request->ben_joining_salary_fixed ;
                            $fixSalary->fixed_amount        = $request->ben_joining_salary_fixed ;
                            $fixSalary->cash_amount         = $request->ben_cash_amount_fixed ;
                            $fixSalary->bank_amount         = $request->ben_bank_amount_fixed ;
                            $fixSalary->basic               = $request->ben_basic_fixed ;
                            $fixSalary->house_rent          = $request->ben_house_rent_fixed ;
                            $fixSalary->medical             = $request->ben_medical_fixed ;
                            $fixSalary->transport           = $request->ben_transport_fixed ;
                            $fixSalary->food                = $request->ben_food_fixed;
                            $fixSalary->status              = 1 ;
                            $fixSalary->created_by          = $user;
                            $fixSalary->created_at          = date('Y-m-d H:i:s');
                            $fixSalary->save();

                            log_file_write("Fixed Salary Saved", $fixSalary->id);
                    }

            }

            $id = DB::table('hr_benefits')->where('ben_as_id', $request->ben_as_id)->value('ben_id');
            log_file_write("Benefits Entry Updated", $id);

            return back()
                ->with('success', 'Benefit Updated Successfully!');
        }
    }
    // Other Benefit Sote
    public function otherBenefitStore(Request $request){
        $user= Auth::user()->associate_id;

        //delete if other benefits exists
        OtherBenefitAssign::where('associate_id', $request->other_associate_id)->delete();

        if($request->has('item_id')){
            for($i=0; $i<sizeof($request->item_id); $i++){
                $data= new OtherBenefitAssign();
                $data->item_id = $request->item_id[$i];
                $data->item_description = $request->item_description[$i];
                $data->item_amount = $request->item_amount[$i];
                $data->associate_id = $request->other_associate_id;
                $data->updated_by = $user;
                $data->save();

                $id = $data->id;
                log_file_write("Other Benefits Entry Saved", $id);
            }

            return back()
                    ->with('success', 'Other Benefit saved Successfully!!');
        }
        else{
            return back()
            ->withInput()
            ->with('error', "save unsuccessfull!!!");
        }

    }

    public function getBenefitByID(Request $request)
    {
        $result['employee'] = get_employee_by_id($request->id);
        $result['benefit']= DB::table('hr_benefits')
                    ->where('ben_as_id', $request->id)
                    ->select('hr_benefits.*')
                    ->orderBy('ben_id', 'DESC')
                    ->first();

        if($result['benefit']){
            $result['flag']= true;
        }
        else{
            $result['flag']= false;
        }

        return response()->json($result);
    }

    

    # Associate Unit by Floor List
    public function getAssociates(Request $request)
    {
        // $date = date("Y-m-d", strtotime("$request->date"));
        $date = date("Y-m-d");
        // dd($date);

        // employee type wise data
        $employees = [];
        // if (!empty($request->emp_type) && !empty($request->unit) && !empty($request->date))
        if (!empty($request->emp_type) && !empty($request->unit) )
        {
            $employees = DB::table('hr_benefits AS b')
                            ->leftJoin('hr_as_basic_info AS a', 'b.ben_as_id', 'a.associate_id')
                            ->whereDate('a.as_doj', "<=", $date)
                            ->where('a.as_emp_type_id', $request->emp_type)
                            ->where('a.as_unit_id', $request->unit)
                            ->get();
        }
        else if (!empty($request->unit))
        {
            $employees = DB::table('hr_benefits AS b')
                            ->leftJoin('hr_as_basic_info AS a', 'b.ben_as_id', 'a.associate_id')
                            ->whereDate('a.as_doj', "<=", $date)
                            ->where('a.as_unit_id', $request->unit)
                            ->get();
        }
        // else if (!empty($request->date))
        // {
        //     $employees = DB::table('hr_benefits AS b')
        //                     ->whereDate('a.as_doj', "<=", $date)
        //                     ->leftJoin('hr_as_basic_info AS a', 'b.ben_as_id', 'a.associate_id')
        //                     ->get();
        // }

        // show user id
        $data['filter'] = "<input type=\"text\" id=\"AssociateSearch\" placeholder=\"Search an Associate\" autocomplete=\"off\" class=\"form-control\"/>";

        $data['result'] = "";
        $data['total'] = 0;
        foreach($employees as $employee)
        {
            $data['total'] += 1;
            $data['result'].= "<tr class='add'>
                                <td style=\"text-align: center;\"><input name=\"associate_id[]\" type=\"checkbox\" style=\"zoom: 1.5;\" value=\"$employee->associate_id\"></td>
                                <td><span class=\"lbl\">$employee->associate_id</span></td>
                                <td>$employee->as_name </td>
                               </tr>";
        }
        //dd($data);
        return $data;
    }

    //Arear salary give
    public function arearSalaryGive($associate_id){
        //Arear Salary ----
        $arrear_data = DB::table('hr_salary_adjust_master as b')->where('b.associate_id', $associate_id)
                ->select([
                    'b.month',
                    'b.associate_id',
                    'b.year',
                    'c.amount',
                    'c.status',
                    'd.as_name',
                    'd.as_contact',
                    'e.hr_unit_name',
                    'e.hr_unit_name_bn',
                    'f.hr_department_name',
                    'f.hr_department_name_bn',
                    'g.hr_designation_name',
                    'g.hr_designation_name_bn',
                    'h.hr_bn_associate_name',
                ])
                ->leftJoin('hr_salary_adjust_details as c', 'c.salary_adjust_master_id', 'b.id')
                ->leftJoin('hr_as_basic_info as d','d.associate_id', 'b.associate_id')
                ->leftJoin('hr_unit as e','e.hr_unit_id', 'd.as_unit_id')
                ->leftJoin('hr_department as f','f.hr_department_id', 'd.as_department_id')
                ->leftJoin('hr_designation as g','g.hr_designation_id', 'd.as_designation_id')
                ->leftJoin('hr_employee_bengali as h','h.hr_bn_associate_id', 'd.associate_id')
                ->get();
        // dd($arrear_data);
                return view('hr/payroll/arear_salary_disburse', compact('arrear_data'));
    }

    //ajax call to update disbursement of arear salary
    public function arearSalarySave(Request $request){
        // dd($request->all());
        // $ids = SalaryAdjustMaster::where('associate_id',$request->ass_id)->pluck('id')->toArray();
        $ids = DB::table('hr_salary_adjust_master as b')
                                ->where(['b.associate_id'=>$request->ass_id, 'c.status' => 0])
                                ->leftJoin('hr_salary_adjust_details as c', 'c.salary_adjust_master_id', 'b.id')
                                ->pluck('b.id')->toArray();
        // dd($ids);
        //update status
        $lim = $request->not_given_months-$request->no_of_month;
        for($i=0; $i<$lim; $i++) {
            array_pop($ids);
        }
        // dd($ids);
        SalaryAdjustDetails::whereIn('salary_adjust_master_id', $ids)->update([
                                            'status'=>'1'
                                        ]);
        return back()->with('success', "Voucher Upadated");

    }

    public function promotion()
    {
        // ACL::check(["permission" => "hr_payroll_benefit_list"]);
        #-----------------------------------------------------------#
        $designationList = Designation::where('hr_designation_status', 1)->pluck("hr_designation_name", "hr_designation_id");
        
        return view('hr/payroll/promotion', compact('designationList'));
    }


    public function promotionList()
    {
        return view('hr/payroll/promotion_list');
    }

    public function promotionListData(Request $request)
    {
        if(isset($request->year)){
            $year = $request->year;
        }else{
            $year = date('Y');
        }
        $designation = designation_by_id();
        $department = department_by_id();
        $section = section_by_id();
        $unit = unit_by_id();
        $data = DB::table('hr_promotion AS inc')
                    ->select([
                        'inc.*',
                        'b.as_name',
                        'b.as_gender',
                        'b.as_doj',
                        'b.as_section_id',
                        'b.as_department_id',
                        'b.as_unit_id',
                        'b.as_emp_type_id',
                        'bn.hr_bn_associate_name',
                    ])
                    ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                    ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', 'b.associate_id')
                    ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
                    ->whereIn('b.as_location', auth()->user()->location_permissions())
                    ->whereYear('inc.effective_date', $year)
                    ->orderBy('inc.effective_date','desc')
                    ->get();

        $perm = check_permission('Manage Promotion');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('as_unit_id', function($data) use ($unit){
                return $unit[$data->as_unit_id]?$unit[$data->as_unit_id]['hr_unit_short_name']:'';
            })
            ->editColumn('previous_designation_id', function ($data) use ($designation) {
                return $designation[$data->previous_designation_id]['hr_designation_name']??'';
            })
            ->editColumn('current_designation_id', function ($data) use ($designation) {
                return $designation[$data->current_designation_id]['hr_designation_name']??'';
            })
            ->addColumn('month', function ($data) {
                return date('F', strtotime($data->effective_date));
            })
            ->addColumn('action', function ($data) use ($perm, $designation,$section,$department) {
                if($perm){

                    if($data->as_emp_type_id == 3){

                        $letter = array(
                            'name' => $data->hr_bn_associate_name,
                            'prev_desg' => $designation[$data->previous_designation_id]['hr_designation_name_bn']??'',
                            'curr_desg' => $designation[$data->current_designation_id]['hr_designation_name_bn']??'',
                            'section' => $section[$data->as_section_id]['hr_section_name_bn']??'',
                            'effective_date' => eng_to_bn($data->effective_date),
                            'associate_id' => $data->associate_id
                        );
                    }else{
                        $letter = array(
                            'name' => $data->as_name,
                            'prev_desg' => $designation[$data->previous_designation_id]['hr_designation_name']??'',
                            'curr_desg' => $designation[$data->current_designation_id]['hr_designation_name']??'',
                            'department' => $department[$data->as_department_id]['hr_department_name']??'',
                            'effective_date' => $data->effective_date,
                            'associate_id' => $data->associate_id,
                            'title' => ($data->as_gender == 'Male' ? 'Mr. ': 'Mrs. ').$data->as_name,
                            'doj' => $data->as_doj
                        );
                    }

                    $output = "<div class=\"btn-group\">
                        <a type=\"button\" href=".url('hr/payroll/promotion_edit/'.$data->id)." class=\"btn btn-xs btn-primary\"><i class=\"fa fa-pencil\"></i></a> &nbsp;";
                    if($data->as_emp_type_id == 3){
                        $output .="<button type=\"button\" onclick='printLetter(".json_encode($letter).")' class=\"btn btn-xs btn-danger\"><i class=\"fa fa-print\"></i></a></button"; 
                    }else{
                        $output .="<button type=\"button\" onclick='printEnLetter(".json_encode($letter).")' class=\"btn btn-xs btn-danger\"><i class=\"fa fa-print\"></i></a></button"; 
                    }
                    $output .= "</div>";
                    return $output;
                }else{
                    return '';
                }
            })
            ->rawColumns(['action','month'])
            ->make(true);  
                             
    }

    public function storePromotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'associate_id'           => 'required',
            'previous_designation_id' => 'required',
            'previous_designation'   => 'required',
            'current_designation_id' => 'required|max:11',
            'effective_date'         => 'required|date',
        ]);

        if ($validator->fails())
        {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fileds!.');
        }
        else
        {
            $check = Promotion::where('associate_id', $request->associate_id)->where('status', 0)->first();

            if($check){
                return back()
                        ->withInput()->with('error', 'This employe has already a pending request! <a href="'.url('hr/payroll/promotion_edit/'.$check->id).'">View</a>');
            }else{


                $store = new Promotion;
                $store->associate_id = $request->associate_id;
                $store->previous_designation_id = $request->previous_designation_id;
                $store->current_designation_id  = $request->current_designation_id;
                $store->effective_date          = $request->effective_date;
                if($request->effective_date <= date('Y-m-d')){
                    $store->status          = 1;  
                }

                if( $store->save())
                {

                    if($store->status == 1){
                        Employee::where("associate_id", $request->associate_id)
                        ->update([
                            'as_designation_id' => $request->current_designation_id
                        ]);
                    }

                    log_file_write($request->associate_id." Associate Promoted!", $store->id);
                    return back()
                        ->with('success', 'Promotion data stored! Employee will get promoted on the effective date!');
                }
                else
                {
                    return back()
                        ->withInput()->with('error', 'Please try again.');
                }
            }
        }
    }
    //Promotion Edit
    public function promotionEdit($id)
    {
        $designationList = Designation::where('hr_designation_status', 1)->pluck("hr_designation_name", "hr_designation_id");

        $promotion= DB::table('hr_promotion AS p')
                        ->select(
                            'p.*',
                            'b.as_name',
                            'b.as_gender',
                            'b.as_doj',
                            'b.as_pic',
                            'b.as_section_id',
                            'b.as_department_id',
                            'b.as_designation_id',
                            's.hr_section_name'
                        )
                        ->where('p.id', $id)
                        ->leftJoin('hr_as_basic_info as b','p.associate_id', 'b.associate_id')
                        ->leftJoin("hr_section AS s", "s.hr_section_id", "=", "b.as_section_id")
                        ->first();
        $designation = designation_by_id();
        $history = Promotion::where('associate_id', $promotion->associate_id)
                            ->orderBy('effective_date', 'DESC')->get();

        $isLatest = 1;

        

        if(count($history)>0){
            if($history->first()->id != $promotion->id){
                $isLatest = 0; 
            }
            $historyview = "";

            foreach ($history as $key => $h) {
                if($h->status == 0){
                    $historyview .= '<div class="promotions"> <i class="text-danger las la-exclamation-circle"></i>  ';
                }else{
                    $historyview .= '<div> <i class="text-success las la-check-circle"></i>  ';
                }
                $historyview .= $designation[$h->current_designation_id]['hr_designation_name']. '- <span style="font-size:11px;">'.date('d M, Y', strtotime($h->effective_date)).'</span></div>';
            }
            $last = $history->last();
            $historyview .= '<div> <i class="text-success las la-check-circle"></i> '.$designation[$h->previous_designation_id]['hr_designation_name']. '- <span class="badge bg-primary">Joined ('.date('d M, Y', strtotime($promotion->as_doj)).')</span></div>';
        }else{
            $historyview = "<span class='text-danger'>No record found!</span>";
        }

        return view('hr/payroll/promotion_edit', compact('promotion', 'designationList','historyview', 'designation','isLatest'));
    }

    public function updatePromotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promotion_id'           => 'required',
            'associate_id'           => 'required',
            'previous_designation_id' => 'required|max:11',
            'current_designation_id' => 'required|max:11',
            'effective_date'         => 'required|date'
        ]);

        if ($validator->fails())
        {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fileds!.');
        }
        else
        {
            $promotion = Promotion::where('id', $request->promotion_id)->first();
            Promotion::where('id', $request->promotion_id)->update([
                'previous_designation_id' => $request->previous_designation_id,
                'current_designation_id' => $request->current_designation_id,
                'effective_date'         => $request->effective_date,
            ]);

            if($promotion->status == 1){
                Employee::where("associate_id", $request->associate_id)
                ->update([
                    'as_designation_id' => $request->current_designation_id
                ]);
            }

            log_file_write("Promotion Updated", $request->promotion_id);

                return back()
                    ->with('success', 'Promotion updated Successfully!');
        }
    }


    //corn jobs
    public function promotionJobs()
    {
        $records = Promotion::where("status", 0)
                  ->where("effective_date", "<=", date("Y-m-d"))
                  ->get();

        foreach ($records as $item)
        {
            Employee::where("associate_id", $item->associate_id)
            ->update([
                'as_designation_id' => $item->current_designation_id
            ]);

            Promotion::where("id", $item->id)
            ->update([
                'status' => 1
            ]);
        }
    }


    # Search Associate ID returns NAME & ID
    public function searchPromotedAssociate(Request $request)
    {
        $data = [];
        if($request->has('keyword'))
        {
            $search = $request->keyword;
            $data = DB::table("hr_benefits AS ben")
                ->select("b.associate_id", DB::raw('CONCAT_WS(" - ", b.associate_id, b.as_name) AS associate_name'))
                ->leftJoin("hr_as_basic_info AS b", "b.associate_id", "=", "ben.ben_as_id")
                ->where("b.associate_id", "LIKE" , "%{$request->keyword}%" )
                ->orWhere('b.as_name', "LIKE" , "%{$request->keyword}%" )
                ->get();
        }
        return response()->json($data);
    }

    # Search Associate Promotion Info
    public function promotedAssociateInfo(Request $request)
    {
        if($request->has('associate_id'))
        {

            $info = DB::table("hr_as_basic_info AS b")
                    ->select("b.associate_id", "b.as_doj", "b.as_designation_id", "d.hr_designation_name",'b.as_name','b.as_pic', 'd.hr_designation_position','b.as_gender','b.as_emp_type_id','s.hr_section_name')
                    ->leftJoin("hr_designation AS d", "d.hr_designation_id", "=", "b.as_designation_id")
                    ->leftJoin("hr_section AS s", "s.hr_section_id", "=", "b.as_section_id")
                    ->where("b.associate_id",  $request->associate_id)
                    ->first();

            if($info)
            {
                $date = $info->as_doj;
                $data['as_name'] = $info->as_name;
                $data['as_pic'] = emp_profile_picture($info);
                $data['previous_designation'] = $info->hr_designation_name;
                $data['section'] = $info->hr_section_name;
                $data['previous_designation_id'] = $info->as_designation_id;

                //update designations
                $designations = Designation::where('hr_designation_emp_type', $info->as_emp_type_id)
                ->where('hr_designation_status', 1)
                ->orderBy('hr_designation_position', 'ASC')
                ->get();

                $designation = designation_by_id();

                $history = Promotion::where('associate_id', $request->associate_id)
                            ->orderBy('effective_date', 'DESC')->get();

                if(count($history)>0){
                    $data['history'] = "";

                    foreach ($history as $key => $h) {
                        if($h->status == 0){
                            $data['history'] .= '<div class="promotions"> <i class="text-danger las la-exclamation-circle"></i>  ';
                        }else{
                            $data['history'] .= '<div> <i class="text-success las la-check-circle"></i>  ';
                        }
                        $data['history'] .= $designation[$h->current_designation_id]['hr_designation_name']. '- <span style="font-size:11px;">'.date('d M, Y', strtotime($h->effective_date)).'</span></div>';
                    }
                    $last = $history->last();
                    if($last && isset($designation[$last->previous_designation_id])){

                        $data['history'] .= '<div> <i class="text-success las la-check-circle"></i> '.$designation[$last->previous_designation_id]['hr_designation_name']. '- <span class="badge bg-primary">Joined ('.date('d M, Y', strtotime($date)).')</span></div>';
                    }
                }else{
                    $data['history'] = "<span class='text-danger'>No record found!</span>";
                }

                $data['designation'] = "<option value=''>Select Promoted Designation</option>";
                foreach ($designations as $value)
                {
                    $data['designation'] .= "<option value='$value->hr_designation_id'>$value->hr_designation_name</option>";
                }

                $data['status'] = true;
            }
            else
            {
                $data['status'] = false;
                $data['error'] = "Requested Associate's ID $request->associate_id don't have available data!";
            }
        }
        else
        {
            $data['status'] = false;
            $data['error'] = "No Associate Found!";
        }
        return response()->json($data);
    }

    # show associate benefit
    public function showAssociateBenefit(Request $request)
    {
        $info = DB::table("hr_as_basic_info AS b")
            ->select("b.associate_id", "b.as_name", "b.as_pic","b.as_gender", "d.hr_designation_name", "dpt.hr_department_name", "u.hr_unit_name")
            ->where('b.associate_id', $request->associate_id)
            ->leftJoin("hr_designation AS d", "d.hr_designation_id", "b.as_designation_id")
            ->leftJoin("hr_department AS dpt", "dpt.hr_department_id", "b.as_department_id")
            ->leftJoin("hr_unit AS u", "u.hr_unit_id", "b.as_unit_id")
            ->first();

        $benefit = Benefits::where('ben_as_id', $request->associate_id)
            ->first();

        $promotions = DB::table("hr_promotion AS p")
            ->select(
                "d1.hr_designation_name AS previous_designation",
                "d2.hr_designation_name AS current_designation",
                "p.eligible_date",
                "p.effective_date"
            )
            ->leftJoin("hr_designation AS d1", "d1.hr_designation_id", "=", "p.previous_designation_id")
            ->leftJoin("hr_designation AS d2", "d2.hr_designation_id", "=", "p.current_designation_id")
            ->where('p.associate_id', $request->associate_id)
            ->orderBy('p.effective_date', "DESC")
            ->get();
        $increments = Increment::where('associate_id', $request->associate_id)->orderBy('effective_date', 'DESC')->get();

        return view('hr/payroll/benefit', compact('info', 'benefit', 'promotions', 'increments'));
    }

}
