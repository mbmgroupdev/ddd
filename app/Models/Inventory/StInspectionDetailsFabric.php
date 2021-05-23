<?php

namespace App\Models\Inventory;

use App\Models\Inventory\StInspectionPointDetailsFabric;
use Illuminate\Database\Eloquent\Model;

class StInspectionDetailsFabric extends Model
{
	public $with = ['st_inspection_point_details_fabric'];
    protected $table = "st_inspection_details_fabric";
    public $timestamps = true;

    public function st_inspection_point_details_fabric()
    {
    	return $this->hasMany(StInspectionPointDetailsFabric::class,'st_inspection_details_fabric_id','id');
    }
}
