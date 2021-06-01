<?php

namespace App\Models\Inventory\Warehouse;

use App\Models\Inventory\StInventoryItemCell;
use Illuminate\Database\Eloquent\Model;

class StWarehouseRackRowColmn extends Model
{
    public $with = ['inventory_item'];
    protected $table = "st_warehouse_rack_row_colmn";


    public static function insertData($data){
    	// dd($data);
    	$in = new StWarehouseRackRowColmn();
    	$in->name 			= $data['rack_row_col_name'];
    	$in->fk_rack_id 	= $data['rack_id'];
    	$in->fk_row_id 		= $data['rack_row_id'];
    	$in->save();

    	return $in->id;
    }

    public static function fetchAll(){
    	$data = StWarehouseRackRowColmn::orderBy('id','DESC')->get();
    	foreach ($data as $d) {
    		$r_name    	= StWarehouseRack::where('id','=', $d->fk_rack_id)->value('name');
    		$rr_name    = StWarehouseRackRow::where('id','=', $d->fk_row_id)->value('name');
    		$d->rack_name 	  = $r_name;
    		$d->rack_row_name = $rr_name;
    	}
    	return $data;
    }

    public static function getEditRow($id){
    	$row = StWarehouseRackRowColmn::where('id', $id)->first();
    	return $row;
    }

    public static function updateRow($row_data){
		StWarehouseRackRowColmn::where('id', $row_data['rack_row_col_id'])->update([
						    				'name' 			=> $row_data['edit_rack_row_col_name'],
						    				'fk_rack_id' 	=> $row_data['edit_rack_id'],
						    				'fk_row_id' 	=> $row_data['edit_rack_row_id']
									    	]);
    }

    public static function deleteRow($row){
    	StWarehouseRackRowColmn::where('id', $row)->delete();
    }

    public function inventory_item()
    {
        return $this->hasOne(StInventoryItemCell::class, 'st_warehouse_rack_row_column_id', 'id');
    }
}
