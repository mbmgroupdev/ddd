<?php

namespace App\Http\Controllers\Inventory\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\AssetItem;
use DB, Validator, DataTables;

class AssetItemController extends Controller
{
	//show Input Form
    public function showForm(){
    	return view('inventory/setup/asset_item/asset_form');
    }

    //store item
    public function storeItem(Request $request){
    	// dd($request->all());

    	$validator= Validator::make($request->all(), [
    		'item_type' => 'required',
    		'item_name' => 'required| max:45',
    		'item_description' => 'max:45'
    	]);
    	if($validator->fails()){
    		return redirect('inventory/setup/asset/create')
    				->withInput()
    				->withError('Incorrect Input!!');;
    	}
    	else{
    		try {
    			DB::beginTransaction();

    			$item= new AssetItem();

    			$item->item_type 		= $request->item_type;
    			$item->item_name 		= $request->item_name;
    			$item->item_description	= $request->item_description;

    			$item->save();

    			$this->logFileWrite('Asset Item Entry', $item->id);

    			DB::commit();

    			return redirect('inventory/setup/asset/create')
    				->with('success', "Asset Item saved successfully!");;
    			
    		} catch (\Exception $e) {
                DB::rollback();
    			$msg = $e->getMessage();
    			return redirect('inventory/setup/asset/create')
    				->withInput()
    				->withError($msg);;
    		}
    	}
    }

    //get Item List For DataTables
    public function getItemData(){
    	$data= AssetItem::get();
    	return DataTables::of($data)->addIndexColumn()
    						->addColumn('action', function($data){
    							$return = "<div class=\"btn-group\">";

                    			$return .= "<a href=".url('inventory/setup/asset/delete/'.$data->id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete Asset Item\" onclick=\"return confirm('Are you sure?')\"><i class=\"ace-icon fa fa-trash bigger-120\"></i></a>";

		            			$return .= "</div>";

    							return $return;
    						})
    						->rawColumns(['action'])
    						->toJson();
    }

    //delete Asset Item
    public function deleteItem($id){
    	DB::beginTransaction();
    	try {
    		AssetItem::where('id', $id)->delete();
    		DB::commit();
    		$this->logFileWrite('Asset Item Deleted', $id);

    		return redirect('inventory/setup/asset/create')
    				->with('success', "Asset Item deleted successfully!");;

    	} catch (\Exception $e) {
             DB::rollback();
    		$msg = $e->getMessage();
    		return redirect('inventory/setup/asset/create')
    				->withError($msg);;
    	}
    }
}