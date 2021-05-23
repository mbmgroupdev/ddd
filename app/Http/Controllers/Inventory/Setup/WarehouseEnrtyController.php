<?php

namespace App\Http\Controllers\Inventory\Setup;

use App\Http\Controllers\Controller;
use App\Models\Hr\Location;
use App\Models\Inventory\Warehouse\StWarehouse;
use DB, Response, Exception, Validator, DataTables;
use Illuminate\Http\Request;

class WarehouseEnrtyController extends Controller
{
    public function index(){
    	$unit  = DB::table('hr_unit')->select(['hr_unit_id','hr_unit_name'])->get();
        $getLocation = Location::get();
        return view('inventory.setup.warehouse.warehouse_entry', compact('unit','getLocation'));
    }

    public function storeData(Request $request){
    	$validator = Validator::make($request->all(),[
           'name'           => 'required|max:45',
           'hr_location_id' => 'required'	  
       ]);

    	if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
       $input = $request->all();
       try{
        $input['name'] = $this->quoteReplaceHtmlEntry($input['name']);
        $last_id = StWarehouse::create($input)->id;
        $this->logFileWrite("Inventory>Setup> Warehouse Saved", $last_id);
        return back()->with('success', 'Warehouse Saved');
        }catch(\Exception $e){
            $msg = $e->getMessage();
            return back()->withInput()->with('error',$msg);
        }
    }

     public function fetchAllData(){
        $data = StWarehouse::getListOfWarehouse();
        return DataTables::of($data)->addIndexColumn()
        ->addColumn('action', function($data){
         $action_buttons= "<input type=\"hidden\" id=\"edit_data_id\" value=".$data->id.">
            
            </div>
            <a href=".url('inventory/setup/rack_entry/'.$data->id )." class=\"btn btn-xs btn-info\" rel='tooltip' data-tooltip-location='top' data-tooltip=\"Rack Entry\">
            <i class=\"fa fa-database\"></i>
            </a>
            <a href=".url('inventory/setup/warehouse_entry_delete/'.$data->id )." class=\"btn btn-xs btn-danger\" rel='tooltip' data-tooltip-location='top' data-tooltip=\"delete\" onclick=\"return confirm('Are you sure you want to delete this?');\">
            <i class=\"fa fa-trash\"></i>
            </a>";
            $action_buttons.= "</div>";

            return $action_buttons;
        })
        ->make(true);
        // <button class=\"btn btn-xs btn-success edit_modal_button\" data-toggle=\"modal\" data-target=\"#edit-modal\" rel='tooltip' data-tooltip-location='top' data-tooltip=\"Update Info\">
        //     <i class=\"fa fa-pencil\"></i>
        //     </button>
    }

    public function editData(Request $r){
        $data = StWarehouse::getEditRow($r->row_id);
        return Response::json($data);
    }

    public function updateData(Request $request){
        		// dd($request->all());
        $validator = Validator::make($request->all(),[
            'edit_warehouse_name' => 'required|max:45',
            'edit_unit_id' 		  => 'required'	  
        ]);

        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Sometihing wrong with input");
        }

        DB::beginTransaction();
        try{
        $name = $request->edit_warehouse_name;
             		// dd($name);
        $name = $this->quoteReplaceHtmlEntry($name);
        $request->edit_warehouse_name = $name;
        StWarehouse::updateRow($request->all());

        $this->logFileWrite("Inventory>Setup> Warehouse Updated", $request->warehouse_id);
        DB::commit();

        return back()->with('success', 'Warehouse Updated');

        }catch(\Exception $e){
            DB::rollback();
            $msg = $e->getMessage();
            return back()->withInput()->with('error',$msg);
        }		

    }

    public function deleteData($id){
      StWarehouse::deleteRow($id);
      $this->logFileWrite("Inventory>Setup> Warehouse Deleted", $id);
      return back()->with('success', "Warehouse Deleted");
    }

}
