<?php

namespace App\Models\Merch;

use App\Models\Merch\CatItemUom;
use App\Models\UOM;
use Illuminate\Database\Eloquent\Model;
use DB;

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

    public static function getItemWithUomItemIdWise($itemids)
    {
        $uomData = DB::table('uom');
        $uomData_sql = $uomData->toSql();
        return DB::table('mr_cat_item_uom AS iu')
            ->select('u.id AS id', 'u.measurement_name AS text','iu.mr_cat_item_id')
            ->whereIn('iu.mr_cat_item_id', $itemids)
            ->leftjoin(DB::raw('(' . $uomData_sql. ') AS u'), function($join) use ($uomData) {
                $join->on('iu.uom_id','u.id')->addBinding($uomData->getBindings());
            })
            ->get();
    }
}