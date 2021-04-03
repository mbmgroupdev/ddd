<?php

namespace App\Http\Controllers\Merch\Setup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Operation;
use Validator, DB;
class OperationController extends Controller
{
    public function operation(){
      $operations= Operation::get();

      return view('merch/setup/operation', compact('operations'));
    }

    public function fetchOperations()
    {
        $operationList  = Operation::get();
        $operationData = view('merch.common.get_operation', compact('operationList'))->render();
        /*if($operationList) {
            $operationData= '<div class="col-xs-12"><div class="checkbox">';
            foreach ($operationList as $operation) {
                $checked="";
                if($operation->opr_type == 1){
                    $checked.="checked readonly onclick='return false;'";
                }
                $operationData.= "<label style='padding:0px;'>
                            <input name='operations[]' type='checkbox' data-content-type='".$operation->opr_type."' value='".$operation->opr_id."'".$checked.">
                            <span class='lbl'>".$operation->opr_name."</span>
                        </label>";
            }
            $operationData.="</div></div>";
        }*/
        return json_encode($operationData);
    }

    public function operationStore(Request $request){
        $validator= Validator::make($request->all(),[
             'opr_name'   =>'required|max:50'

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
            $input['opr_name'] = $this->quoteReplaceHtmlEntry($request->opr_name);

            Operation::insertOrIgnore($input);
            $last_id = DB::getPDO()->lastInsertId();
            $this->logFileWrite("Operation Saved", $last_id);
            toastr()->success("Operation Saved Successfully");
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function operationDelete($id){
        
        Operation::where('opr_id', $id)->delete();

        $this->logFileWrite("Operation Deleted", $id);
        return back()
        ->with('success', "Operation  Deleted Successfully!!");
    }

    public function operationEdit($id){

        $operation=Operation::where('opr_id', $id)->first();

        return view('merch/setup/operation_edit',compact('operation'));

    }

    public function operationUpdate(Request $request){

       $validator= Validator::make($request->all(),[
            'op_name'        =>'required|max:50'
        ]);

        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{

            $operation = Operation::where('opr_id', $request->op_id)->update([
                   'opr_name'     => $this->quoteReplaceHtmlEntry($request->op_name)

               ]);
            $this->logFileWrite("Operation Saved", $request->op_id);

            return redirect('merch/setup/operation')
                    ->with('success', "Operation Successfully updated!!!");

        }
    }
    public function operationUpdateAjax(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        $input['opr_name'] = $this->quoteReplaceHtmlEntry($input['opr_name']);
        $input['updated_by'] = auth()->user()->id;
        try {
          $getType = Operation::where('opr_id', $input['opr_id'])
          ->update($input);

          $this->logFileWrite("Operation Updated", $request->opr_id);
          $data['type'] = 'success';
          $data['msg'] = 'Operation Successfully updated';
          
          return $data;
        } catch (\Exception $e) {
          $data['msg'] = $e->getMessage();
          return $data;
        }
    }
}
