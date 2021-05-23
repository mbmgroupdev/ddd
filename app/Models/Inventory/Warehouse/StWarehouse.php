<?php

namespace App\Models\Inventory\Warehouse;

use App\Models\Inventory\Warehouse\StWarehouse;
use App\Models\Inventory\Warehouse\StWarehouseRack;
use DB;
use Illuminate\Database\Eloquent\Model;

class StWarehouse extends Model
{
    public $with = ['rack'];
    protected $table = "st_warehouse";
    protected $fillable = ['name', 'hr_location_id', 'fk_unit_id'];

    public static function getListOfWarehouse()
    {
        DB::statement(DB::raw('set @rownum=0'));
        return StWarehouse::
        select(DB::raw('@rownum := @rownum + 1 AS sl'), 'st_warehouse.id as id', 'st_warehouse.name as name', 'l.hr_location_name as location_name', 'u.hr_unit_name as unit_name')
            ->leftJoin('hr_location as l','st_warehouse.hr_location_id','=','l.hr_location_id')
            ->leftJoin('hr_unit as u','st_warehouse.fk_unit_id','=','u.hr_unit_id')
            ->orderBy('st_warehouse.id', 'desc')
            ->get();
    }
    public static function fetchAll(){
    	$data = StWarehouse::orderBy('id', 'DESC')->get();
    	foreach ($data as $d) {
    		$unit_name    = DB::table('hr_unit')->where('hr_unit_id','=', $d->fk_unit_id)->value('hr_unit_name');
    		$d->unit_name = $unit_name;
    	}
    	return $data;
    }

    public static function getEditRow($id){
    	$row = StWarehouse::where('id', $id)->first();
    	return $row;
    }

    public static function updateRow($row_data){
		StWarehouse::where('id', $row_data['warehouse_id'])->update([
						    				'name' 			=> $row_data['edit_warehouse_name'],
						    				'fk_unit_id' 	=> $row_data['edit_unit_id'],
						    				'fk_floor_id' 	=> $row_data['edit_floor_id']
									    	]);
    }

    public static function deleteRow($row){
    	StWarehouse::where('id', $row)->delete();
    }

    public function rack()
    {
        return $this->hasMany(StWarehouseRack::class, 'fk_ware_house_id', 'id');
    }
}
