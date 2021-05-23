<?php

namespace App\Models\Inventory\Warehouse;

use App\Models\Inventory\Warehouse\StWarehouseRackRow;
use Illuminate\Database\Eloquent\Model;

class StWarehouseRack extends Model
{
    public $with = ['row'];
    protected $table = "st_warehouse_rack";


    public static function insertData($data){
    	// dd($data);
    	$in = new StWarehouseRack();
    	$in->name 				= $data['rack_name'];
    	$in->fk_ware_house_id 	= $data['warehouse_id'];
    	$in->save();

    	return $in->id;
    }

    public static function fetchAll(){
    	$data = StWarehouseRack::orderBy('id', 'DESC')->get();
    	foreach ($data as $d) {
    		$w_name    = StWarehouse::where('id','=', $d->fk_ware_house_id)->value('name');
    		$d->warehouse_name = $w_name;
    	}
    	return $data;
    }

    public static function getEditRow($id){
    	$row = StWarehouseRack::where('id', $id)->first();
    	return $row;
    }

    public static function updateRow($row_data){
		StWarehouseRack::where('id', $row_data['rack_id'])->update([
						    				'name' 				=> $row_data['edit_rack_name'],
						    				'fk_ware_house_id' 	=> $row_data['edit_warehouse_id']
									    	]);
    }

    public static function deleteRow($row){
    	StWarehouseRack::where('id', $row)->delete();
    }

    public function row()
    {
        return $this->hasMany(StWarehouseRackRow::class, 'fk_rack_id', 'id');
    }
}
