<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\GarmentsType;
use App\Models\Merch\ProductType;
use DB, Validator;

class GarmentsTypeController extends Controller
{
	// Show Style Type Form
    public function showForm()
    {
    	$productList = ProductType::pluck("prd_type_name", "prd_type_id");
    	$garments = GarmentsType::leftJoin("mr_product_type", "mr_product_type.prd_type_id", "=", "mr_garment_type.prd_id")
    			->orderBy("gmt_name", "ASC")->get();
    	return view("merch.setup.garments_type", compact("garments", "productList"));
    }

    // Save Data
    public function store(Request $request)
    {
        
    	$validator= Validator::make($request->all(),[
            'prd_id'   =>'required|max:11',
            'gmt_name' =>'required|max:50',
            'gmt_remarks' =>'max:128',
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
        $input['gmt_name'] = $this->quoteReplaceHtmlEntry($request->gmt_name);
        $input['gmt_remarks'] = $this->quoteReplaceHtmlEntry($request->gmt_remarks);

        try {
            GarmentsType::insertOrIgnore($input);
            $last_id = DB::getPDO()->lastInsertId();
            $this->logFileWrite("Garments Type Saved", $last_id);
            toastr()->success("Garments Type Saved Successfully");
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
        
    }

    // Show Edit From
    public function edit(Request $request)
    {
    	$productList = ProductType::pluck("prd_type_name", "prd_type_id");
    	$garment = GarmentsType::where("gmt_id", $request->id)->first();
    	return view("merch.setup.garments_type_edit", compact("garment", "productList"));
    }

    // Update Data
    public function update(Request $request)
    {
    	$validator= Validator::make($request->all(),[
            'gmt_id'   =>'required|max:11',
            'prd_type_id'   =>'required|max:11',
            'gmt_name' =>'required|max:50',
            'gmt_remarks' =>'max:128',
    	]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        
        try {
            $update = GarmentsType::where('gmt_id', $request->gmt_id)
            ->update([
                'prd_id'   => $request->prd_type_id,
                'gmt_name' => $this->quoteReplaceHtmlEntry($request->gmt_name),
                'gmt_remarks' => $this->quoteReplaceHtmlEntry($request->gmt_remarks),
            ]);

            $this->logFileWrite("Garments Type Updated", $request->gmt_id );
            return redirect("merch/setup/garments_type")
                    ->with('success', 'Update Successful.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    public function updateAjax(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        $input['gmt_name'] = $this->quoteReplaceHtmlEntry($request->gmt_name);
        $input['gmt_remarks'] = $this->quoteReplaceHtmlEntry($request->gmt_remarks);
        $input['updated_by'] = auth()->user()->id;

        try {
          $getType = GarmentsType::where('gmt_id', $input['gmt_id'])
          ->update($input);
          $data['prd_type_name'] = DB::table('mr_product_type')
          ->where('prd_type_id', $input['prd_id'])
          ->pluck('prd_type_name')
          ->first();
          $this->logFileWrite("Garments Type Updated", $request->gmt_id);
          $data['type'] = 'success';
          $data['msg'] = 'Garments Type Successfully updated';
          
          return $data;
        } catch (\Exception $e) {
          $data['msg'] = $e->getMessage();
          return $data;
        }
    }

    // Delete
    public function destroy(Request $request)
    {
        try {
            GarmentsType::where('gmt_id',  $request->id)->delete();
            $this->logFileWrite("Garments Type Deleted", $request->id );
            toastr()->success("Garments Type Delete Successfully");
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
}
