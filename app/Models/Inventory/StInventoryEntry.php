<?php

namespace App\Models\Inventory;

use App\Models\Inventory\StRawMaterialReceive;
use App\Models\Merch\MaterialCategory;
use Illuminate\Database\Eloquent\Model;

class StInventoryEntry extends Model
{
	public $with = ['grn','type_wise'];
    protected $table = "st_inventory_entry";

    public function grn()
    {
        return $this->belongsTo(StRawMaterialReceive::class, 'st_raw_material_receive_id','id');
    }
    public function type_wise()
    {
        return $this->belongsTo(MaterialCategory::class, 'type','mcat_id');
    }
}
