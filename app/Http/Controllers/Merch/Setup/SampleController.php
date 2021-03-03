<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\SampleType;
use App\Models\Merch\Buyer;
use Validator, DB;
class SampleController extends Controller
{
    // Sample type form
  public function sampleType(){

    $buyer= Buyer::pluck('b_name','b_id');
    $sample= DB::table('mr_sample_type as s')
    ->Select(
      's.*',
      'b.b_id',
      'b.b_name'
    )
    ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.b_id')
    ->orderBy('s.sample_id', 'desc')
    ->get();


    return view('merch/setup/sample_type', compact('sample','buyer'));

  }

    // Sample type  Store
  public function sampletypestore(Request $request){

    $validator= Validator::make($request->all(),[
      'sample_name'       =>'required|max:50',
      'buyer'             =>'required'

    ]);
    
    if($validator->fails()){
        foreach ($validator->errors()->all() as $message){
            toastr()->error($message);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }
    $input = $request->all();

    $data = array();
    try {
      foreach($request->sample_name as $sampslename){
        $data[] = [
          'b_id' => $request->buyer,
          'sample_name' => $this->quoteReplaceHtmlEntry($sampslename),
          'created_by' => auth()->user()->id
        ];
      }
      SampleType::insertOrIgnore($data);
      $last_id = DB::getPDO()->lastInsertId();
      $this->logFileWrite("Sample Saved", $last_id);
      toastr()->success("Sample Saved Successfully");
      return back();
    } catch (\Exception $e) {
      $bug = $e->getMessage();
      toastr()->error($bug);
      return back();
    }
  }

  /// Sample Type Delete

  public function sampletypeDelete($id){
    try {
      SampleType::where('sample_id', $id)->delete();
      $this->logFileWrite("Sample Deleted", $id);
      toastr()->success("Sample Type  Deleted Successfully!!");
      return back();
    } catch (\Exception $e) {
      $bug = $e->getMessage();
      toastr()->error($bug);
      return back();
    }
  }

  /// Sample Type Update
  public function sampletypeEdit($id){
    $buyer= Buyer::pluck('b_name','b_id');
    $sample= DB::table('mr_sample_type as s')
    ->Select(
      's.*',
      'b.b_id',
      'b.b_name'
    )
    ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.b_id')
    ->where('sample_id', $id)
    ->first();

    return view('merch/setup/sample_type_edit',compact('sample','buyer'));

  }

  public function sampletypeUpdate(Request $request){

        #-----------------------------------------------------------#

    $validator= Validator::make($request->all(),[
      'sample_name'       =>'required|max:50',
      'buyer'             =>'required'
    ]);

    if($validator->fails()){
      return back()
      ->withInput()
      ->with('error', "Incorrect Input!");
    }
    else{

      SampleType::where('sample_id', $request->sample_id)->update([
       'sample_name'     => $this->quoteReplaceHtmlEntry($request->sample_name),
       'b_id'            => $request->buyer

     ]);
     $this->logFileWrite("Sample Updated", $request->sample_id);
     return redirect('merch/setup/sampletype')
     ->with('success', "Sample Type Successfully updated!!!");
    }

  }
  public function sampletypeUpdateAjax(Request $request)
  {
    $input = $request->all();
    $data['type'] = 'error';
    $input['sample_name'] = $this->quoteReplaceHtmlEntry($input['sample_name']);
    $input['updated_by'] = auth()->user()->id;
    try {
      $getSample = SampleType::where('sample_id', $input['sample_id'])
      ->update($input);
      $this->logFileWrite("Sample Updated", $request->sample_id);
      $data['type'] = 'success';
      $data['msg'] = 'Sample Type Successfully updated';
      $data['buyer_name'] = DB::table('mr_buyer')
      ->where('b_id', $input['b_id'])
      ->pluck('b_name')
      ->first();
      return $data;
    } catch (\Exception $e) {
      $data['msg'] = $e->getMessage();
      return $data;
    }
  }
  public function sampletypeCheck(Request $request)
  {
    if (!empty($request->keyword)){


      $result= SampleType::where('sample_name', $request->keyword)
       ->where('b_id',$request->b_id)
       ->first();
      if(!empty($result)) return 1;
      else return 0;
    }

  }
}
