<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\BonusType;
use App\Models\Hr\EmployeeBonusSheet;
use Validator,Response;

class BonusTypeController extends Controller
{
    public function index()
    {
    	$bonus_types = BonusType::orderBy('id', 'DESC')->get();
        $not_action_list = [];
        $i=0;
        foreach ($bonus_types as $bt_lib) {
          $val = EmployeeBonusSheet::where('bonus_type_id', $bt_lib->id)->value('bonus_type_id');
          if(!empty($val)){
            $not_action_list[$i++] = $val;
          }
        }

    	return view('hr.setup.bonus_type', compact('bonus_types', 'not_action_list'));
    }

    public function entrySave(Request $request)
    {
    	if($request->bonus_amount=='' && $request->bonus_percent==''){
    		return back()->with('error', "Please Input 'Amount' or '% of Basic' ");
    	}
    	else{
    		  $validator = Validator::make($request->all(),[
    		  		'bonus_type_name' => 'required|max:50',
					'month'           => 'required'	
    		  ]);
    		if($validator->fails()){
                return back()->withInput()->with('error', "Incorrect Input!!");
    	    }
              
            try{

                $dates = explode('-', $request->month);
                $bonus = new BonusType();
                $bonus->bonus_type_name = $this->quoteReplaceHtmlEntry($request->bonus_type_name);
                $bonus->month = $dates[1];
                $bonus->year = $dates[0];
                $bonus->amount = $request->bonus_amount??null;
                $bonus->percent_of_basic = $request->bonus_percent??null;

               	if($bonus->save()){
               		log_file_write('Bonus Type "'.$bonus->bonus_type_name.'" added for '.$bonus->month.'-'.$bonus->year, $bonus->id );

               		return back()->with('success', 'Bonus Type Saved Successfully ');
               	}else{
               			return back()->withInput()->with('error', "Incorrect Input!!");
               	}
               }catch(\Exception $e){
               		return back()->withInput()->with('error', $e->getMessage());
               }
        }
    }

    //Edit
    public function editDataFetch(Request $req){
      $data = BonusType::where('id',$req->bt_id)->first();
      // dd($data);
      return Response::json($data);
    }

    //-------Updation
    public function entryUpdate(Request $req){

      if($req->edit_bonus_amount=='' && $req->edit_bonus_percent==''){
        return back()->with('error', "Please Input 'Amount' or '% of Basic' ");
      }
      else{
          $validator = Validator::make($req->all(),[
              'edit_bonus_type_name' => 'required|max:50',
              'edit_month'           => 'required',
              'edit_year'        => 'required' 
          ]);



           if($validator->fails()){
                    return back()
                        ->withInput()
                        ->with('error', "Incorrect Input!!");
            }
              
              try{
                $name = $req->edit_bonus_type_name;
                $name = $this->quoteReplaceHtmlEntry($name);
                $req->edit_bonus_type_name = $name;
                
                BonusType::updateData($req->all());

                log_file_write('Bonus Type Udated Successfully',$req->edit_id );

                return back()->with('success', 'Bonus Type Updated Successfully ');

               }catch(\Exception $e){
                  return back()->withInput()->with('error', $e->getMessage());
               }
        }
    }


    //Delete
    public function entryDelete($id)
    {
    	BonusType::where('id',$id)->delete();
    	
    	log_file_write('Bonus Type Deleted',$id );

		return back()->with('success', 'Bonus Type Deleted Successfully ');
    }
}
