<?php

namespace App\Http\Controllers\Inventory\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse\StWarehouse;
use App\Models\Inventory\Warehouse\StWarehouseRack;
use DB, Response, Exception, Validator, DataTables;

class WarehouseRackEnrtyController extends Controller
{
    public function index(){
    	$warehouse = StWarehouse::select('id','name')->get();
    	return view('inventory.setup.warehouse.rack_entry', compact('warehouse'));
    }

    public function rackIndex($id){
        $warehouse = StWarehouse::select('id','name')
                    ->where('id',$id)
                    ->first();
        return view('inventory.setup.warehouse.rack_entry_from_warehouse', compact('warehouse'));
    }

    public function storeData(Request $request){
    	// dd($request->all());exit;
    	$validator = Validator::make($request->all(),[
                     'rack_name' 	=> 'required|max:45',
                     'warehouse_id' => 'required'	  
                ]);
            
    	if($validator->fails()){
        	return back()
            ->withInput()
            ->with('error', "Sometihing wrong with input");
     	}

     	DB::beginTransaction();
     	try{
     		
     		$name = $request->rack_name;
     		$name = $this->quoteReplaceHtmlEntry($name);
     		$request->rack_name = $name;
     		// dd($request->all());
     		$last_id = StWarehouseRack::insertData($request->all());
     		$this->logFileWrite("Inventory>Setup> Warehouse Rack Saved", $last_id);
     		DB::commit();

     		return back()->with('success', 'Warehouse Rack Saved');

     	}catch(\Exception $e){
     		DB::rollback();
     		$msg = $e->getMessage();
     		return back()->withInput()->with('error',$msg);
     	}
    }


    public function fetchAllData(){
	
			$data =  StWarehouseRack::fetchAll();
            // dd($data);exit;
            return DataTables::of($data)
          /// Query for Action
            ->editColumn('action', function ($data) {
                    $btn = "
                    <a href=".url('inventory/setup/rack_row_entry/'.$data->fk_ware_house_id.'/'.$data->id )." class=\"btn btn-xs btn-info\" rel='tooltip' data-tooltip-location='top' data-tooltip=\"Row Entry\">
                    <i class=\"fa fa-database\"></i>
                    </a>
                    <input type=\"hidden\" id=\"edit_data_id\" value=".$data->id.">
                    <button class=\"btn btn-xs btn-success edit_modal_button\" data-toggle=\"modal\" data-target=\"#edit-modal\" data-toggle=\"tooltip\" title=\"View\">
                        <i class=\"fa fa-pencil\"></i>
                        </button>
                    </div>
                   <a href=".url('inventory/setup/rack_entry/rack_entry_delete/'.$data->id )." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"delete\" onclick=\"return confirm('Are you sure you want to delete this?');\">
                        <i class=\"fa fa-trash\"></i>
                        </a>
                    ";

                // $btn ="";
                return $btn;
              })
            ->rawColumns(['action'])
            ->toJson();	
	}

	public function editData(Request $r){
			$data = StWarehouseRack::getEditRow($r->row_id);
			return Response::json($data);
	}

	public function updateData(Request $request){
		// dd($request->all());
		$validator = Validator::make($request->all(),[
                     'edit_rack_name' 	 	  => 'required|max:45',
                     'edit_warehouse_id' 		  => 'required'	  
                ]);
            
    	if($validator->fails()){
        	return back()
            ->withInput()
            ->with('error', "Sometihing wrong with input");
     	}

     	DB::beginTransaction();
     	try{

            $name = $request->edit_rack_name;
            $name = $this->quoteReplaceHtmlEntry($name);
            $request->edit_rack_name = $name;
     		
     		StWarehouseRack::updateRow($request->all());

     		$this->logFileWrite("Inventory>Setup> Warehouse Rack Updated", $request->rack_id);
     		DB::commit();

     		return back()->with('success', 'Warehouse Rack Updated');

     	}catch(\Exception $e){
     		DB::rollback();
     		$msg = $e->getMessage();
     		return back()->withInput()->with('error',$msg);
     	}		

	}

	public function deleteData($id){
		StWarehouseRack::deleteRow($id);
     	$this->logFileWrite("Inventory>Setup> Warehouse Rack Deleted", $id);
     	return back()->with('success', "Warehouse Rack Deleted");
	}
}
