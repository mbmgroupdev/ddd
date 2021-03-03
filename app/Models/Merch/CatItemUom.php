<?php

namespace App\Models\Merch;

use App\Models\Merch\CatItemUom;
use App\Models\UOM;
use Illuminate\Database\Eloquent\Model;

class CatItemUom extends Model
{
	// public $with = ['uom'];
    protected $table= 'mr_cat_item_uom';
    public $timestamps= false;

    public static function getItemWiseUom($itemId)
    {
    	return CatItemUom::where('mr_cat_item_id', $itemId)->get();
    }

    public function uom()
    {
    	return $this->belongsTo(UOM::class, 'uom_id', 'id');
    }
}