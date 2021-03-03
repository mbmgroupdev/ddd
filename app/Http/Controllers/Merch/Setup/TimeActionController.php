<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\TnaLibrary;
use App\Models\Merch\TnaTemplate;
use App\Models\Merch\TnaTemplatetoLibrary;
Use DB, Validator, DataTables,DateTime;

class TimeActionController extends Controller
{
    public function timeActionForm()
    {
        $library=TnaLibrary::orderBy('id', 'asc')->get();
        return view('merch.time_action.library',compact('library'));
    }

    public function libraryStore(Request $request){

        $validator= Validator::make($request->all(),[
            'tna_lib_action' => 'required|max:145|unique:mr_tna_library,tna_lib_action',
            'tna_lib_code'   => 'required|max:65|unique:mr_tna_library,tna_lib_code'


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
            
            $input['tna_lib_action'] = $this->quoteReplaceHtmlEntry($request->tna_lib_action);
            $input['tna_lib_code'] = $this->quoteReplaceHtmlEntry($request->tna_lib_code);

            TnaLibrary::insertOrIgnore($input);
            $last_id = DB::getPDO()->lastInsertId();
            $this->logFileWrite("Time and Action Library Saved", $last_id);
            toastr()->success("Time and Action Library Saved Successfully");
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
        
    }

    public function libraryEdit($id)
    {
       $library=TnaLibrary::where('id',$id)->first();
        return view('merch.time_action.library_edit',compact('library'));
    }
    # Library Update
    public function libraryUpdate(Request $request){

        $validator= Validator::make($request->all(),[

            'lib_action'   => 'required|max:145|unique:mr_tna_library,tna_lib_action,'. $request->libid,
            'tna_code' => 'required|max:65|unique:mr_tna_library,tna_lib_code,' . $request->libid
        ]);

        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{

           $update=TnaLibrary::where('id', $request->libid)->update([
                'tna_lib_action' => $this->quoteReplaceHtmlEntry($request->lib_action),
                'tna_lib_code'   => $this->quoteReplaceHtmlEntry($request->tna_code)
           ]);

            if($update){

            // Log file
                $this->logFileWrite("Time and Action Library Updated", $request->libid);
                return redirect('merch/time_action/library')
                ->with('success', "Updated Successfully!!");
            }
            else{
                return back()
                ->withInput()
                ->with('error', 'Error Updating data!!');
            }
        }
    }
    public function libraryUpdateAjax(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        $input['tna_lib_action'] = $this->quoteReplaceHtmlEntry($input['tna_lib_action']);
        $input['tna_lib_code'] = $this->quoteReplaceHtmlEntry($input['tna_lib_code']);
        $input['updated_by'] = auth()->user()->id;
        try {
            $getType = TnaLibrary::where('id', $input['id'])
            ->update($input);

            $this->logFileWrite("Time and Action Library Updated", $request->id);
            $data['type'] = 'success';
            $data['msg'] = 'Time and Action Library Successfully Updated';
          
            return $data;
        } catch (\Exception $e) {
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }
    # Library Delete
    public function libraryDelete($id){

        TnaLibrary::where('id', $id)->delete();

        // Log File
        $this->logFileWrite("Time and Action Library Deleted", $id);
        return back()
        ->with('success', "Deleted Successfully!!");
    }

    #show Form
    public function templateForm()
    {
        $buyer=Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name','b_id');
        $library=TnaLibrary::get();
        $templates = DB::table('mr_tna_template AS t')
        ->select("t.*", "b.b_name")
        ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 't.mr_buyer_b_id')
        ->get();
        return view('merch.time_action.tna_template',compact('buyer','library','templates'));
    }
    # Template Store
    public function templateStore(Request $request){
       
        $validator= Validator::make($request->all(),[
            'tna_temp_name'   => 'required|max:45',
            'mr_buyer_b_id'           => 'required'
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back();
        }
        $input = $request->all();
        $input = $request->except('_token');

        DB::beginTransaction();
        try {
            $data= new TnaTemplate();
            $data->tna_temp_name = $this->quoteReplaceHtmlEntry($request->tna_temp_name) ;
            $data->mr_buyer_b_id   = $request->mr_buyer_b_id;
            $data->created_by   = auth()->user()->id;

            if($data->save()){
                $last_id = $data->id;

                for($j=0; $j<sizeof($request->tnalibrary); $j++)
                {
                   if($request->tnalibrary[$j] !=""){
                        TnaTemplatetoLibrary::insert([
                            'mr_tna_template_id' => $last_id,
                            'mr_tna_library_id'  => $request->tnalibrary[$j],
                            'tna_temp_logic'     => $request->logic[$j],
                            'offset_day'         => $request->tna_lib_offset[$j],
                        ]);
                    }
                }
              
                $this->logFileWrite("Time and Action Template Stored", $last_id);
                toastr()->success("Time and Action Template Stored");
                DB::commit();
            }else{
                toastr()->error('Error saving data!!');
            }
            
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
        
    }

    # Template Update
    public function templateEdit($id){

        $buyer=Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name','b_id');
        $library=TnaLibrary::get();
        $template= TnaTemplate::where('id', $id)->first();

        return view('merch.time_action.tna_template_edit',compact('buyer','library','template'));
    }

    # Template Update Action
    public function templateUpdate(Request $request){

        $validator= Validator::make($request->all(),[
            'template_name'   => 'required|max:45',
            'buyer'           => 'required'
        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{
            $update=TnaTemplate::where('id', $request->tna_id)->update([
               'tna_temp_name' => $this->quoteReplaceHtmlEntry($request->template_name),
               'mr_buyer_b_id' => $request->buyer
        ]);


            //dd($request->all());

        if(sizeof($request->tnalibrary)>0){

            $temlib=TnaTemplatetoLibrary::where('mr_tna_template_id', $request->tna_id)->delete();

            for($j=0; $j<sizeof($request->tnalibrary); $j++)
            {
                if($request->tnalibrary[$j] !=""){
                    $updatelib=TnaTemplatetoLibrary::insert([
                        'mr_tna_template_id' => $request->tna_id,
                        'mr_tna_library_id'  => $request->tnalibrary[$j],
                        'tna_temp_logic'     => $request->logic[$j],
                        'offset_day'         => $request->tna_lib_offset[$j],
                    ]);
                  }
                }
            }

            if ($update || $updatelib)
            {
              // Log file
                $this->logFileWrite("Time and Action Template updated", $request->tna_id);
                return redirect('merch/time_action/tna_template')
                    ->with('success', 'Update Successful.');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }

        }
    }

    # Delete Template
    public function templateDelete($id){

        TnaTemplate::where('id', $id)->delete();
        TnaTemplatetoLibrary::where('mr_tna_template_id', $id)->delete();
        // Log File
        $this->logFileWrite("TIme and Action Template Deleted", $id);
        return back()
        ->with('success', "Deleted Successfully!!");
    }

    # Write Every Events in Log File
    public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." ".Auth()->user()->associate_id." \"".$message."\" ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);
    }
}
