<?php

namespace App\Models\Inventory;

use App\Models\Inventory\StInspectionDetailsFabric;
use Illuminate\Database\Eloquent\Model;

class StInspectionMaster extends Model
{
	public $with = ['st_inspection_details_fabric'];
    protected $table = "st_inspection_master";
    public $timestamps = true;

    public function st_inspection_details_fabric()
    {
    	return $this->hasMany(StInspectionDetailsFabric::class,'st_inspection_master_id','id');
    }

    public function st_raw_material_receive()
    {
        return $this->hasMany(StRawMaterialReceive::class,'id', 'st_raw_material_receive_id');
     
    }

    
}
