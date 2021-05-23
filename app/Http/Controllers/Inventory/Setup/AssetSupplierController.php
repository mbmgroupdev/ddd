<?php

namespace App\Http\Controllers\Inventory\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Country;
use App\Models\Inventory\AssetSupplier;
use DB, Validator, DataTables;
class AssetSupplierController extends Controller
{
    //Show Entry Form
    public function showForm(){
    	$countryList= Country::pluck('cnt_name', 'cnt_id');
    	return view('inventory/setup/asset_supplier/supplier_form', compact('countryList'));
    }

    //supplier Store
    public function storeSupplier(Request $request){
    	// dd($request->all());
    	$validator= Validator::make($request->all(), [
    		'sup_name' => 'required|max:45',
    		'cnt_id' => 'required|max:11',
    		'sup_address' => 'required|max:128',
    		'sup_type' => 'required'
    	]);
    	if($validator->fails()){
    		return redirect('inventory/setup/supplier/create')
    				->withInput()
    				->with('error', 'Incorrect Input!!');
    	}
    	else{
    		try {
    			DB::beginTransaction();

    			$supplier= new AssetSupplier();

    			$supplier->sup_name = $request->sup_name;
    			$supplier->cnt_id = $request->cnt_id;
    			$supplier->sup_address = $request->sup_address;
    			$supplier->sup_type = $request->sup_type;
    			$supplier->status = 1;

    			$supplier->save();
    			DB::commit();
    			$this->logFileWrite("Asset Supplier saved", $supplier->id);

    			return redirect('inventory/setup/supplier/create')
    					->with("success", "Asset Supplier saved successfully!");
    			
    		} catch (Exception $e) {
    			DB::rollback();
    			$msg = $e->getMessage();
    			return redirect('inventory/setup/supplier/create')
    					->withInput()
    					->with('error', $msg);

    		}
    	}
    }

    //get supplier data
    public function getSupplierData(){
    	$data= AssetSupplier::get();

    	return DataTables::of($data)->addIndexColumn()
    					->addColumn('cnt_name', function($data){
    						return Country::where('cnt_id', $data->cnt_id)->value('cnt_name');
    					})
    					->addColumn('action', function($data){
    							$return = "<div class=\"btn-group\">";

                    			$return .= "<a href=".url('inventory/setup/supplier/delete/'.$data->sup_id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete Asset Supplier\" onclick=\"return confirm('Are you sure?')\"><i class=\"ace-icon fa fa-trash bigger-120\"></i></a>";

		            			$return .= "</div>";

    							return $return;
    						})
    					->rawColumns(['action'])
    					->toJson();
    }

    //Delete Asset Supplier
    public function deleteSupplier($id){

    	DB::beginTransaction();
    	try {
    		AssetSupplier::where('sup_id', $id)->delete();
    		DB::commit();
    		$this->logFileWrite('Asset Supplier Deleted', $id);

    		return redirect('inventory/setup/supplier/create')
    				->with('success', "Asset Supplier deleted successfully!");;

    	} catch (\Exception $e) {
             DB::rollback();
    		$msg = $e->getMessage();
    		return redirect('inventory/setup/supplier/create')
    				->withError($msg);;
    	}
    }
}
