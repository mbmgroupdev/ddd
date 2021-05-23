<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\StWareHouseEntry;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function rental(Request $request)
    {
      $obj = new StWareHouseEntry;
      $obj->warehouse_name = $request->warehouse_name;
      $obj->area_size = $request->area_size;
      $obj->rack_no = $request->rack_no;
      $obj->location_name = $request->location_name;
      $obj->floor_no = $request->floor_no;
      $obj->row_no = $request->row_no;
      $obj->incharge_name = $request->incharge_name;
      $obj->in_gate = $request->in_gate;
      $obj->out_gate = $request->out_gate;
      $obj->vehicle_type = $request->vehicle_type;
      $obj->vehicle_no = $request->vehicle_no;
      $obj->save();
      return back()->with('success','Entry Saved');
    }
    public function rentalEdit(Request $request, $id)
    {
      $data = StWareHouseEntry::find($id);
      return view('inventory.sections.rentaledit', compact('data'));
    }
    public function rentalUpdate(Request $request, $id)
    {
      $obj = StWareHouseEntry::find($id);
      $obj->warehouse_name = $request->warehouse_name;
      $obj->area_size = $request->area_size;
      $obj->rack_no = $request->rack_no;
      $obj->location_name = $request->location_name;
      $obj->floor_no = $request->floor_no;
      $obj->row_no = $request->row_no;
      $obj->incharge_name = $request->incharge_name;
      $obj->in_gate = $request->in_gate;
      $obj->out_gate = $request->out_gate;
      $obj->vehicle_type = $request->vehicle_type;
      $obj->vehicle_no = $request->vehicle_no;
      $obj->save();
      return back()->with('success','Update Saved');
    }
}
