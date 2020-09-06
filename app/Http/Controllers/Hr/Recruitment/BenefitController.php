<?php

namespace App\Http\Controllers\Hr\Recruitment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'ben_as_id'           => 'unique:hr_benefits|max:10|min:10|alpha_num',
            'ben_joining_salary'  => 'required',
            'ben_cash_amount'     => 'required',
            'ben_bank_amount'     => 'required',
            'ben_basic'           => 'required',
            'ben_house_rent'      => 'required',
            'ben_medical'         => 'required',
            'ben_transport'       => 'required',
            'ben_food'            => 'required'
        ]);

        if($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fileds!.');
        }
        else
        {
            $benefits= new Benefits();
            $benefits->ben_as_id               = $request->ben_as_id ;
            $benefits->ben_joining_salary      = $request->ben_joining_salary ;
            $benefits->ben_current_salary      = $request->ben_joining_salary ;
            $benefits->ben_cash_amount         = $request->ben_cash_amount ;
            $benefits->ben_bank_amount         = $request->ben_bank_amount ;
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

                    if($request->fixed_check){
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

                    }

                    log_file_write("Benefits Entry Saved", $benefits->ben_id);

                    return back()
                        ->withInput()
                        ->with('success', 'Save Successful.');

                }
                else
                {
                    return back()
                        ->withInput()->with('error', 'Please try again.');
                }
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
                    'a.as_name',
                    'a.as_unit_id',
                    'u.hr_unit_name AS unit_name'
                )
                ->leftJoin('hr_as_basic_info as a', 'a.associate_id', '=', 'b.ben_as_id')
                ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'a.as_unit_id')
                ->whereIn('a.as_unit_id', auth()->user()->unit_permissions())
                ->whereNotIn('a.as_id', auth()->user()->management_permissions()) 
                ->orderBy('b.ben_id', 'desc')
                ->get();

            return DataTables::of($data)
            ->addColumn('action', function ($data) {
                return "<div class=\"btn-group\">
                    <a href=".url('hr/payroll/benefit/'.$data->ben_as_id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                        <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                    </a>
                    <a href=".url('hr/payroll/benefit_edit/'.$data->ben_as_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>
                </div>";
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function benefitEdit($id)
    {

        $get_as_id = Employee::where('associate_id', $id)->first(['as_id']);
        $m_restriction=  auth()->user()->management_permissions(); //dd($m_restriction);
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
        //ACL::check(["permission" => "hr_payroll_benefit_list"]);
        #-----------------------------------------------------------#
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
        $result['employee'] = Employee::where('associate_id',$request->id)->first()->toArray();
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

    public function showIncrementForm()
    {
        // ACL::check(["permission" => "hr_payroll_benefit_list"]);
        #-----------------------------------------------------------#
        $unitList = Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $typeList= IncrementType::pluck('increment_type', 'id');

        /*$incrementList= DB::table('hr_increment AS inc')
                            ->where('status', 0)
                            ->select([
                                'inc.id',
                                'inc.associate_id',
                                'b.as_name',
                                'inc.increment_type',
                                'inc.increment_amount',
                                'inc.amount_type',
                                'inc.eligible_date',
                                'inc.effective_date',
                                'c.increment_type AS inc_type_name',
                            ])
                            ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                            ->leftJoin('hr_increment_type AS c', 'c.id', 'inc.increment_type' )
                            ->orderBy('inc.id','desc')
                            ->get();*/

        //Arear Salary List----
        /*$associate_ids = DB::table('hr_salary_adjust_master as b')
                                ->groupBy('b.associate_id')
                                ->pluck('b.associate_id')
                                ->toArray();*/
        // dd($associate_ids);
        /*foreach ($associate_ids as $key => $ass) {
            $data = DB::table('hr_salary_adjust_master as b')->where('b.associate_id', $ass)
                    ->select([
                        'b.month',
                        'b.associate_id',
                        'b.year',
                        'c.amount',
                        'c.status',
                        'd.as_name',
                        'd.as_contact',
                        'e.hr_unit_name',
                        'f.hr_department_name',
                    ])
                    ->leftJoin('hr_salary_adjust_details as c', 'c.salary_adjust_master_id', 'b.id')
                    ->leftJoin('hr_as_basic_info as d','d.associate_id', 'b.associate_id')
                    ->leftJoin('hr_unit as e','e.hr_unit_id', 'd.as_unit_id')
                    ->leftJoin('hr_department as f','f.hr_department_id', 'd.as_department_id')
                    ->where('c.type', 3)
                    ->get();
            $arrear_data[$key] = $data;
        }*/


        // dd($arrear_data);

        return view('hr/payroll/increment', compact('unitList','employeeTypes', 'typeList'));
    }

    public function storeIncrement(Request $request)
    {
        $created_by= Auth::user()->associate_id;

        if(empty($request->associate_id) || !is_array($request->associate_id))
        {
            return back()
                ->withInput()
                ->with('error', 'Please select at least one associate.');
        }
        
        // Declare and define two dates 
        $date1 = strtotime($request->applied_date);  
        $date2 = strtotime($request->effective_date);  
        //extract years
        $year1 = date('Y', $date1);
        // $year2 = date('Y', $date2);
        $year2 = date('Y');
        //extract month
        $month1 = date('m', $date1);
        // $month2 = date('m', $date2);
        $month2 = date('m');
        //month difference
        $month_diff = (($year2 - $year1) * 12) + ($month2 - $month1);

        // dd($request->applied_date, $request->effective_date,"Difference: ".$month_diff.' Months ');



        for($i=0; $i<sizeof($request->associate_id); $i++)
        {
            $salary= DB::table('hr_benefits')
                            ->where('ben_as_id', $request->associate_id[$i])
                            ->pluck('ben_current_salary')
                            ->first();

            $doj= DB::table('hr_as_basic_info')
                    ->where('associate_id',$request->associate_id[$i] )
                    ->pluck('as_doj')
                    ->first();
            $eligible_at = date("Y-m-d", strtotime("$doj + 1 year"));
            // $eligible_at = $request->elligible_date;        

            $increment= new Increment();
            $increment->associate_id = $request->associate_id[$i] ;
            $increment->current_salary = $salary;
            $increment->increment_type = $request->increment_type;
            $increment->increment_amount = $request->increment_amount ;
            $increment->amount_type = $request->amount_type ;
            $increment->applied_date = $request->applied_date ;
            $increment->eligible_date = $eligible_at ;
            $increment->effective_date = $request->effective_date ;
            $increment->status = 0 ;
            $increment->created_by = $created_by;
            $increment->created_at = date('Y-m-d H:i:s') ;
            $increment->save();

            log_file_write("Increment Entry Saved", $increment->id);


            //Keeping the not given increment amount---- SalaryAdjustMaster, SalaryAdjustDetails
             $basic = DB::table('hr_benefits')
                            ->where('ben_as_id', $request->associate_id[$i])
                            ->pluck('ben_basic')
                            ->first();
             if($request->amount_type == 1){
                    $_amount = $request->increment_amount;
                }
             else{
                    $_amount = ($basic/100)*$increment->increment_amount;
                }

             $y = (int)$year1;
             $m = (int)$month1;

             for($j=0; $j<$month_diff; $j++ ){
                    //-----------master data insert
                        $master = new SalaryAdjustMaster();
                        $master->associate_id = $request->associate_id[$i];
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
                        $detail->type                    = 3;
                        $detail->save();
                    
             }   
            //-----------------------------------------------------------------------------
        }


        return back()
            ->with('success', "Increment Saved Successfully!");
    }

    //Edit Increment
    public function editIncrement($id){

        $unitList = Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        $employeeTypes  = EmpType::where('hr_emp_type_status', '1')->pluck('hr_emp_type_name', 'emp_type_id');
        $typeList= IncrementType::pluck('increment_type', 'id');
        $increment= DB::table('hr_increment AS inc')
                        ->where('id', $id)
                        ->select([
                            'inc.*',
                            'b.as_emp_type_id',
                            'b.as_unit_id'
                        ])
                        ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'inc.associate_id')
                        ->first();
        return view('hr/payroll/increment_edit', compact('unitList', 'employeeTypes', 'typeList', 'increment'));
    }

    //Update Increment
    public function updateIncrement(Request $request){

        Increment::where('id', $request->increment_id)
                    ->update([
                          "increment_type"      => $request->increment_type,
                          "applied_date"        => $request->applied_date,
                          "effective_date"      => $request->effective_date,
                          "increment_amount"    => $request->increment_amount,
                          "amount_type"         => $request->amount_type
                    ]);

        log_file_write("Increment Updated", $request->increment_id);
        return back()
            ->with('success', "Increment updated Successfully!");
    }

    //Increment corn job
    public function incrementJobs(){

        $today= date('Y-m-d');
        $todays_increment= DB::table('hr_increment')
                ->where('effective_date', '<=', $today)
                ->where('status', 0)
                ->limit(10)
                ->get();

        $salary_structure= DB::table('hr_salary_structure AS s')
                                ->where('status', 1)
                                ->select('s.*')
                                ->orderBy('id', 'DESC')
                                ->first();

        if(!empty($todays_increment) && !empty($salary_structure)){

            foreach ($todays_increment as $key => $increment) {

                if($increment->amount_type ==1)
                {
                    $ben_current_salary= $increment->current_salary+ $increment->increment_amount;
                }
                else{
                    $ben_current_salary= $increment->current_salary+ (($increment->current_salary/100)*$increment->increment_amount);
                }

                $ben_medical= $salary_structure->medical;
                $ben_transport= $salary_structure->transport;
                $ben_food= $salary_structure->food;

                $ben_basic= (($ben_current_salary-($salary_structure->medical+$salary_structure->transport+$salary_structure->food))/$salary_structure->basic);
                //$ben_basic= (($ben_current_salary/100)*$salary_structure->basic);
                $ben_house_rent= $ben_current_salary - ($ben_basic+$salary_structure->medical+$salary_structure->transport+$salary_structure->food);
                

                $bank= DB::table('hr_benefits')->where('ben_as_id', $increment->associate_id)
                            ->where('ben_status', 1)
                            ->first();

                //paid in bank
                if(!empty($bank)){
                    if($bank->ben_bank_amount>= $ben_current_salary ){
                        $bank_paid= $ben_current_salary;
                        $cash_paid= 0;
                    }
                    else{
                        $bank_paid= $bank->ben_bank_amount;
                        $cash_paid= $ben_current_salary-$bank->ben_bank_amount;
                    }
                }
                else{
                    $bank_paid= $ben_current_salary;
                        $cash_paid= 0;
                }


                Benefits::where('ben_as_id', $increment->associate_id)
                    ->update([
                        'ben_cash_amount' => $cash_paid,
                        'ben_bank_amount' => $bank_paid,
                        'ben_current_salary' => $ben_current_salary,
                        'ben_basic' => $ben_basic,
                        'ben_house_rent' => $ben_house_rent,
                        'ben_medical' => $ben_medical,
                        'ben_transport' => $ben_transport,
                        'ben_food' => $ben_food
                        ]);

                $id = Benefits::where('ben_as_id', $increment->associate_id)->value('ben_id');
                log_file_write("Jobs Benefits Updated", $id );

                Increment::where('associate_id', $increment->associate_id)
                            ->where('status', 0)
                            ->update([
                                'status' => 1
                            ]);
            }
        }
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

    public function storePromotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'associate_id'           => 'required|min:10|max:10',
            'previous_designation_id' => 'required|max:11',
            'previous_designation'   => 'required|max:64',
            'current_designation_id' => 'required|max:11',
            'eligible_date'          => 'required|date',
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
            $store = new Promotion;
            $store->associate_id = $request->associate_id;
            $store->previous_designation_id = $request->previous_designation_id;
            $store->current_designation_id  = $request->current_designation_id;
            $store->eligible_date           = $request->eligible_date;
            $store->effective_date          = $request->effective_date;

            if ( $store->save())
            {
                log_file_write("Associate Promoted Saved", $store->id);
                return back()
                    ->with('success', 'Associate Promoted Successfully!');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
        }
    }
    //Promotion Edit
    public function promotionEdit($id){
        $designationList = Designation::where('hr_designation_status', 1)->pluck("hr_designation_name", "hr_designation_id");
        $promotion= DB::table('hr_promotion AS p')
                        ->select(
                            'p.*',
                            'd.hr_designation_name AS prev_desg'
                        )
                        ->where('id', $id)
                        ->leftJoin('hr_designation AS d', 'p.previous_designation_id', 'd.hr_designation_id')
                        ->first();
        return view('hr/payroll/promotion_edit', compact('promotion', 'designationList'));
    }
    public function updatePromotion(Request $request){
        $validator = Validator::make($request->all(), [
            'promotion_id'           => 'required|max:11',
            'associate_id'           => 'required|min:10|max:10',
            'previous_designation_id' => 'required|max:11',
            'current_designation_id' => 'required|max:11',
            'eligible_date'          => 'required|date',
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
            Promotion::where('id', $request->promotion_id)
                    ->update([
                        'associate_id'           => $request->associate_id,
                        'previous_designation_id' => $request->previous_designation_id,
                        'current_designation_id' => $request->current_designation_id,
                        'eligible_date'          => $request->eligible_date,
                        'effective_date'         => $request->effective_date,
                    ]);

            log_file_write("Promotion Updated", $request->promotion_id);

                return back()
                    ->with('success', 'Promotion updated Successfully!');
        }
    }


    //corn jobs
    public function promotionJobs()
    {
        $records = Promotion::where("status", 0)
            ->whereDate("effective_date", "<=", date("Y-m-d"));

        if ($records->exists())
        {
            $items = $records->limit(10)->get();
            foreach ($items as $item)
            {
                Employee::where("associate_id", $item->associate_id)
                ->update([
                    'as_designation_id' => $item->current_designation_id
                ]);

                $id = Employee::where("associate_id", $item->associate_id)->value('as_id');
                // log_file_write("Employee Designation Updated", $id);

                Promotion::where("id", $item->id)
                ->update([
                    'status' => 1
                ]);

                // log_file_write("Promotion Status Updated", $item->id);
            }
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

            $query = DB::table("hr_benefits AS ben")
                ->select("b.associate_id", "b.as_doj", "b.as_designation_id", "d.hr_designation_name",'b.as_name','b.as_pic')
                ->leftJoin("hr_as_basic_info AS b", "b.associate_id", "=", "ben.ben_as_id")
                ->leftJoin("hr_designation AS d", "d.hr_designation_id", "=", "b.as_designation_id")
                ->where("b.associate_id",  $request->associate_id);

            if ($query->exists())
            {
                $info = $query->first();
                $date = $info->as_doj;
                $data['eligible_date'] = date("Y-m-d", strtotime("$date + 1 year"));
                $data['as_name'] = $info->as_name;
                $data['as_pic'] = $info->as_pic??'assets/images/user/09.jpg';
                $data['previous_designation'] = $info->hr_designation_name;
                $data['previous_designation_id'] = $info->as_designation_id;

                //update designations
                $position = Designation::where("hr_designation_id", "=", $info->as_designation_id)->value('hr_designation_position');
                $designations = Designation::where('hr_designation_position', ">", $position)
                ->where('hr_designation_status', 1)
                ->orderBy('hr_designation_position', 'ASC')
                ->get();

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
