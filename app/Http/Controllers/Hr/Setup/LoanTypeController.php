<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\LoanType;
use Validator,ACL,DB;

class LoanTypeController extends Controller
{
    public function addloanType(){
        //ACL::check(["permission" => "hr_setup"]);
        $loantype=DB::table('hr_loan_type')
                  ->select('hr_loan_type_name','id')
                  ->get();
    	return view('hr/setup/loan_type', compact('loantype'));
    }
    public function storeloanType(Request $request){
        //ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
    	$validator= Validator::make($request->all(),[
    		'hr_loan_type_name' => 'required|max:128'
    	]);
    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput();
    	}
    	else{
    		$type= new LoanType();
    		$type->hr_loan_type_name= $request->hr_loan_type_name;
    		if($type->save())
    		{
                $this->logFileWrite("Loan Type Entry Saved",$type->id );
    			return back()
    			->with('success', "Saved Successfully");
    		}
    		else{
    			return back()
    			->withInput()
    			->with('error', "Error! Please try again!");
    		}
    	}
    }

    public function loanTypeDelete($id){
        DB::table('hr_loan_type')->where('id',$id)->delete();

        return redirect('/hr/setup/loan_type')->with('success', "Successfuly deleted Loan Type");
    }

    public function loanTypeEdit($id){

        $loantypeLibrary =DB::table('hr_loan_type')
                  ->get();

        $loantype = DB::table('hr_loan_type')->where('id', $id)->first();


        return view('/hr/setup/loan_type_edit', compact('loantype','loantypeLibrary'));
    }

    public function updateLoanType(Request $request){
        //ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
        $validator= Validator::make($request->all(),[
            'hr_loan_type_name' => 'required|max:128'
        ]);
        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }
        else{

            DB::table('hr_loan_type')->where('id', $request->loan_type_id)
            ->update([
                'hr_loan_type_name' => $request->hr_loan_type_name

            ]);


            return redirect('/hr/setup/loan_type')->with('success', 'Successfuly updated Seb-Section');

            }
    }
}
