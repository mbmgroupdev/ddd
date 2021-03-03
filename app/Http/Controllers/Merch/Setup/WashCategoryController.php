<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\WashCategory;
use Validator, DB;
class WashCategoryController extends Controller
{
    public function showForm(){

        $wash_category=DB::table('mr_wash_category')->get();
    	
    	return view('merch/setup/wash_category',compact('wash_category') );
    }
    
    public function saveForm(Request $request){
        $validator= Validator::make($request->all(),[
            'category_name' => 'required'
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
            $input['category_name'] = $this->quoteReplaceHtmlEntry($request->category_name);

            WashCategory::insertOrIgnore($input);
            $last_id = DB::getPDO()->lastInsertId();
            $this->logFileWrite("Wash Category Saved", $last_id);
            toastr()->success("Wash Category Saved Successfully");
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }


    public function editForm($id){
        $wash= WashCategory::where('id', $id)->first();
        return view('merch/setup/wash_category_edit', compact('wash'));
    }

    public function updateForm(Request $request){
        $validator= Validator::make($request->all(),[
            'wash_category' => 'required'
           
        ]);
        if($validator->fails())
        {
            return back()
                ->withInput()
                ->with('error', "Incorrect Input!!");
        }
        else{
            WashCategory::where('id', $request->id)
                    ->update([
                        'category_name' => $request->wash_category
                        
                    ]);

            $this->logFileWrite("Wash Category Updated", $request->id );
            return back()
                ->with('success', "Wash Type updated Successfully!");
        }
    }
    public function deleteEntry($id){
        WashCategory::where('id', $id)->delete();

        $this->logFileWrite("Wash Category Deleted", $id );
        return redirect('merch/setup/wash_category')
                ->with('Success', "Wash Category deleted Successfully!!");
    }
    public function updateAjax(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        $input['category_name'] = $this->quoteReplaceHtmlEntry($input['category_name']);
        $input['updated_by'] = auth()->user()->id;
        try {
            $getType = WashCategory::where('id', $input['id'])
            ->update($input);

            $this->logFileWrite("Wash Category Updated", $request->id);
            $data['type'] = 'success';
            $data['msg'] = 'Wash Category Successfully Updated';
          
            return $data;
        } catch (\Exception $e) {
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }
    
}
