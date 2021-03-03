<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Spmachine;

use Validator, DB;
class SpmachineController extends Controller
{
    public function spmachine(){
      $spmachine= Spmachine::get();

      return view('merch/setup/spmachine', compact('spmachine'));

    }

    public function spmachineStore(Request $request){
      $validator= Validator::make($request->all(),[
           'spmachine_name'   =>'required|max:50'

      ]);
      if($validator->fails()){
          foreach ($validator->errors()->all() as $message){
              toastr()->error($message);
          }
          return back();
      }
      $input = $request->all();
      $input = $request->except('_token');
      $input['created_by'] = auth()->user()->id;
      try {
        $input['spmachine_name'] = $this->quoteReplaceHtmlEntry($request->spmachine_name);

        Spmachine::insertOrIgnore($input);
        $last_id = DB::getPDO()->lastInsertId();
        $this->logFileWrite("Machine Saved", $last_id);
        toastr()->success("Machine Saved Successfully");
        return back();
      } catch (\Exception $e) {
          $bug = $e->getMessage();
          toastr()->error($bug);
          return back();
      }
    }

    public function spmachineDelete($id){
        Spmachine::where('spmachine_id', $id)->delete();
        $this->logFileWrite("Special Machine Deleted", $id );
        return back()
        ->with('success', "Operation  Deleted Successfully!!");
    }

    public function spmachineEdit($id){
        $machine=Spmachine::where('spmachine_id', $id)->first();

        return view('merch/setup/spmachine_edit',compact('machine'));

    }

    public function spmachineUpdate(Request $request){
        $validator= Validator::make($request->all(),[
            'sm_name'        =>'required|max:50'

        ]);

        if($validator->fails()){

            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{

          $smachine = Spmachine::where('spmachine_id', $request->spm_id)->update([
                 'spmachine_name'     => $this->quoteReplaceHtmlEntry($request->sm_name)

             ]);
          $this->logFileWrite("Special Machine Updated", $request->spm_id );
          return redirect('merch/setup/spmachine')
                  ->with('success', "Operation Successfully updated!!!");
       }

    }
    public function spmachineUpdateAjax(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        $input['spmachine_name'] = $this->quoteReplaceHtmlEntry($input['spmachine_name']);
        $input['updated_by'] = auth()->user()->id;
        try {
            $getType = Spmachine::where('spmachine_id', $input['spmachine_id'])
            ->update($input);

            $this->logFileWrite("Special Machine Updated", $request->spmachine_id);
            $data['type'] = 'success';
            $data['msg'] = 'Special Machine Successfully updated';
          
            return $data;
        } catch (\Exception $e) {
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }
}
