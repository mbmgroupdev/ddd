<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\ProductType;
use Validator, DB;

class ProductTypeController extends Controller
{
	// Show product Type Form
    public function showForm()
    {
    	$products = ProductType::orderBy("prd_type_name", "ASC")->get();
    	return view("merch.setup.product_type", compact("products"));
    }

    // Save Data
    public function store(Request $request)
    {
    	$validator= Validator::make($request->all(),[
            'prd_type_name' =>'required|max:50|unique:mr_product_type',
    	]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back();
            
        }
        $input = $request->all();
        $input = $request->except('_token');
        try {
            $input['created_by'] = auth()->user()->id;
            $input['prd_type_name'] = $this->quoteReplaceHtmlEntry($request->prd_type_name);

            ProductType::insertOrIgnore($input);
            $last_id = DB::getPDO()->lastInsertId();
            $this->logFileWrite("Product Type Saved", $last_id);
            toastr()->success("Product Type Saved Successfully");
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
    	$product = ProductType::where("prd_type_id", $request->id)->first();
    	return view("merch.setup.product_type_edit", compact("product"));
    }

    // Save Data
    public function update(Request $request)
    {
    	$validator= Validator::make($request->all(),[
            'prd_type_id'   =>'required|max:11',
            'prd_type_name' =>'required|max:50',
    	]);

        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }

        try {
            $update = ProductType::where('prd_type_id', $request->prd_type_id)
            ->update([
                'prd_type_name' => $this->quoteReplaceHtmlEntry($request->prd_type_name)
            ]);

            $this->logFileWrite("Product Type Updated", $request->prd_type_id );
            return redirect("merch/setup/product_type")
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
        $input['prd_type_name'] = $this->quoteReplaceHtmlEntry($input['prd_type_name']);
        $input['updated_by'] = auth()->user()->id;
        try {
          $getType = ProductType::where('prd_type_id', $input['prd_type_id'])
          ->update($input);

          $this->logFileWrite("Product Updated", $request->prd_type_id);
          $data['type'] = 'success';
          $data['msg'] = 'Product Type Successfully updated';
          
          return $data;
        } catch (\Exception $e) {
          $data['msg'] = $e->getMessage();
          return $data;
        }
    }

    // Delete
    public function destroy(Request $request)
    {
        ProductType::where('prd_type_id',  $request->id)->delete();
        
        $this->logFileWrite("Product Type Deleted", $request->id);
        return back()->with('success', "Delete Successful.");
    }

}
