<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class StRawMaterialReceive extends Model
{
	public $with = ['StRawMaterialItemReceive'];
    protected $table = "st_raw_material_receive";
    public $timestamps = false;

    public function StRawMaterialItemReceive()
    {
    	return $this->hasMany(StRawMaterialItemReceive::class, 'st_raw_material_receive_id', 'id');
    }

    public function InspectionMaster() {
     return $this->belongsTo(StInspectionMaster::class,'st_raw_material_receive_id', 'id');

        //return $this->hasMany('App\Models\Inventory\Article');
    }
}
