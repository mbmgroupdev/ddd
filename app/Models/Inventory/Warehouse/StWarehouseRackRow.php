<?php

namespace App\Models\Inventory\Warehouse;

use Illuminate\Database\Eloquent\Model;

class StWarehouseRackRow extends Model
{
    public $with = ['colmn'];
    protected $table = "st_warehouse_rack_row";


    public static function insertData($data){
    	// dd($data);
    	$in = new StWarehouseRackRow();
    	$in->name 			= $data['rack_row_name'];
    	$in->fk_rack_id 	= $data['rack_id'];
    	$in->save();

    	return $in->id;
    }

    public static function fetchAll(){
    	$data = StWarehouseRackRow::orderBy('id', 'DESC')->get();
    	foreach ($data as $d) {
    		$r_name    = StWarehouseRack::where('id','=', $d->fk_rack_id)->value('name');
    		$d->rack_name = $r_name;
            $wid=StWarehouseRack::where('id','=', $d->fk_rack_id)->value('fk_ware_house_id');
            $d->warehouse_id = $wid;
    	}
    	return $data;
    }

    public static function getEditRow($id){
    	$row = StWarehouseRackRow::where('id', $id)->first();
    	return $row;
    }

    public static function updateRow($row_data){
		StWarehouseRackRow::where('id', $row_data['rack_row_id'])->update([
						    				'name' 			=> $row_data['edit_rack_row_name'],
						    				'fk_rack_id' 	=> $row_data['edit_rack_id']
									    	]);
    }

    public static function deleteRow($row){
    	StWarehouseRackRow::where('id', $row)->delete();
    }

    public function colmn()
    {
        return $this->hasMany(StWarehouseRackRowColmn::class, 'fk_row_id', 'id');
    }
}
