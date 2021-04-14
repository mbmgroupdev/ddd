<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\BonusType;
use App\Models\Hr\EmployeeBonusSheet;
use Illuminate\Support\Facades\Cache;
use Validator,Response;

class BonusTypeController extends Controller
{
    public function index()
    {
    	$bonus_types = BonusType::orderBy('id', 'DESC')->get();
    	return view('hr.setup.bonus_type', compact('bonus_types'));
    }

    public function entrySave(Request $request)
    {
    	$validator = Validator::make($request->all(),[
        'bonus_type_name' => 'required|max:50',
        'eligible_month' => 'required'
      ]);
      if($validator->fails()){
        return back()->withInput()->with('error', "Incorrect Input!!");
      }
            
      try{
        
        $bonus = new BonusType();
        $bonus->bonus_type_name = $this->quoteReplaceHtmlEntry($request->bonus_type_name);
        $bonus->eligible_month = $request->eligible_month;
        if($bonus->save()){
          log_file_write('Bonus Type "'.$bonus->bonus_type_name.'" added', $bonus->id );
          Cache::pull('bonus_type_by_id');
          return back()->with('success', 'Bonus Type Saved Successfully ');
        }else{
            return back()->withInput()->with('error', "Incorrect Input!!");
        }
      }catch(\Exception $e){
        return back()->withInput()->with('error', $e->getMessage());
      }
    }

    //Edit
    public function editDataFetch(Request $req){
      $data = BonusType::where('id',$req->bt_id)->first();
      // dd($data);
      return Response::json($data);
    }

    //-------update
    public function entryUpdate(Request $req){

      $validator = Validator::make($req->all(),[
        'bonus_type_name' => 'required|max:50',
        'eligible_month' => 'required'
      ]);

      if($validator->fails()){
        return back()
          ->withInput()
          ->with('error', "Incorrect Input!!");
      }
        
      try{
        $input = $req->all();
        $input['bonus_type_name'] = $this->quoteReplaceHtmlEntry($req->bonus_type_name);
        unset($input['_token']);
        BonusType::where('id', $req->id)->update($input);

        log_file_write('Bonus Type Updated Successfully',$req->edit_id );
        Cache::pull('bonus_type_by_id');
        return back()->with('success', 'Bonus Type Updated Successfully ');

       }catch(\Exception $e){
          return back()->withInput()->with('error', $e->getMessage());
       }
    }


    //Delete
    public function entryDelete($id)
    {
    	BonusType::where('id',$id)->delete();
    	Cache::pull('bonus_type_by_id');
    	log_file_write('Bonus Type Deleted',$id );

		return back()->with('success', 'Bonus Type Deleted Successfully ');
    }
}
