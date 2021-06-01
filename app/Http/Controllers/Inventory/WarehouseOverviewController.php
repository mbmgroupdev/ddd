<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse\StWarehouse;
use DB, DataTables, Response, Validator;

class WarehouseOverviewController extends Controller
{
    public function index(){

    	$warehouse = StWarehouse::select('id','name')->get();
    	return view('inventory.sections.warehouse_overview', compact('warehouse'));
    }

    public function getData(Request $request){

        try {
            $getWarehouse = StWarehouse::findOrFail($request->warehouse_id);
            //return $getWarehouse->rack[0]->row[0]->colmn[0]->inventory_item->inventory_entry->type_wise->mcat_name;
            // return $getWarehouse;
    	    return view('inventory.sections.warehouse_overview_data', compact('getWarehouse'));
            } 
        catch (\Exception $e) {
            return "error";
        }
    }
}