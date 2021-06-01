<?php

namespace App\Http\Controllers\Inventory\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse\StWarehouse;
use App\Models\Inventory\Warehouse\StWarehouseRack;
use App\Models\Inventory\Warehouse\StWarehouseRackRow;
use DB, Response, Exception, Validator, DataTables;

class WarehouseRackRowEnrtyController extends Controller
{
    public function index(){
    	$rack_row = StWarehouseRack::select('id','name')->get();
    	return view('inventory.setup.warehouse.rack_row_entry', compact('rack_row'));
    }
    public function rowIndex($wid,$rackid){
        $rack_row = StWarehouseRack::select('id','name')
                    ->where('id',$rackid)
                    ->first();
                    //dd($rack_row);exit;
        $warehouse = StWarehouse::select('id','name')
                    ->where('id',$wid)
                    ->first();
        return view('inventory.setup.warehouse.row_entry_from_rack', compact('rack_row','warehouse'));
    }

    public function storeData(Request $request){
    	// dd($request->all());exit;
    	$validator = Validator::make($request->all(),[
                     'rack_row_name' 	=> 'required|max:45',
                     'rack_id' 			=> 'required'	  
                ]);
            
    	if($validator->fails()){
        	return back()
            ->withInput()
            ->with('error', "Sometihing wrong with input");
     	}

     	DB::beginTransaction();
     	try{
     		
     		$name = $request->rack_row_name;
     		$name = $this->quoteReplaceHtmlEntry($name);
     		$request->rack_row_name = $name;
     		// dd($request->all());
     		$last_id = StWarehouseRackRow::insertData($request->all());
     		$this->logFileWrite("Inventory>Setup> Warehouse Rack Row Saved", $last_id);
     		DB::commit();

     		return back()->with('success', 'Warehouse Rack Row Saved');

     	}catch(\Exception $e){
     		DB::rollback();
     		$msg = $e->getMessage();
     		return back()->withInput()->with('error',$msg);
     	}
    }


    public function fetchAllData(){
	
			$data =  StWarehouseRackRow::fetchAll();
            // dd($data);exit;
            return DataTables::of($data)
          /// Query for Action
            ->editColumn('action', function ($data) {
                    $btn = "
                    <a href=".url('inventory/setup/rack_row_colmn_from_row/'.$data->warehouse_id.'/'.$data->fk_rack_id.'/'.$data->id )." class=\"btn btn-xs btn-info\" rel='tooltip' data-tooltip-location='top' data-tooltip=\"Column Entry\">
                    <i class=\"fa fa-database\"></i>
                    </a>
                    <input type=\"hidden\" id=\"edit_data_id\" value=".$data->id.">
                    <button class=\"btn btn-xs btn-success edit_modal_button\" data-toggle=\"modal\" data-target=\"#edit-modal\" data-toggle=\"tooltip\" title=\"View\">
                        <i class=\"fa fa-pencil\"></i>
                        </button>
                    </div>
                   <a href=".url('inventory/setup/rack_row_entry/warehouse_rack_row_delete/'.$data->id )." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"delete\" onclick=\"return confirm('Are you sure you want to delete this?');\">
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
			$data = StWarehouseRackRow::getEditRow($r->row_id);
			return Response::json($data);
	}

	public function updateData(Request $request){
		// dd($request->all());
		$validator = Validator::make($request->all(),[
                     'edit_rack_row_name' 	 => 'required|max:45',
                     'edit_rack_id' 		 => 'required'	  
                ]);
            
    	if($validator->fails()){
        	return back()
            ->withInput()
            ->with('error', "Sometihing wrong with input");
     	}

     	DB::beginTransaction();
     	try{
     		
            $name = $request->edit_rack_row_name;
            $name = $this->quoteReplaceHtmlEntry($name);
            $request->edit_rack_row_name = $name;
            
     		StWarehouseRackRow::updateRow($request->all());

     		$this->logFileWrite("Inventory>Setup> Warehouse Rack Row Updated", $request->rack_id);
     		DB::commit();

     		return back()->with('success', 'Warehouse Rack Row Updated');

     	}catch(\Exception $e){
     		DB::rollback();
     		$msg = $e->getMessage();
     		return back()->withInput()->with('error',$msg);
     	}		

	}

	public function deleteData($id){
		StWarehouseRackRow::deleteRow($id);
     	$this->logFileWrite("Inventory>Setup> Warehouse Rack Row Deleted", $id);
     	return back()->with('success', "Warehouse Rack Row Deleted");
	}
}
