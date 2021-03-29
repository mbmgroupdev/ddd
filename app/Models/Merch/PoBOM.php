<?php

namespace App\Models\Merch;

use App\Models\Merch\PoBOM;
use Illuminate\Database\Eloquent\Model;
use DB;

class PoBOM extends Model
{
	// public $with = ['category', 'item'];
    protected $table= 'mr_po_bom_costing_booking';
    protected $guarded = [];

    public static function getOrderBomOrderIdWiseSelectItemIdName($orderId)
    {
    	return OrderBOM::where('order_id', $orderId)
    	->get();
    }

    public function category()
	{
	    return $this->belongsTo('App\Models\Merch\MainCategory', 'mr_material_category_mcat_id', 'mcat_id');
	}

	public function item()
	{
	    return $this->belongsTo('App\Models\Merch\McatItem', 'mr_cat_item_id', 'id');
	}

	public function supplier()
	{
	    return $this->belongsTo('App\Models\Merch\Supplier', 'mr_supplier_sup_id', 'sup_id');
	}

	public function article()
	{
	    return $this->belongsTo('App\Models\Merch\Article', 'mr_article_id', 'id');
	}

	public function composition()
	{
	    return $this->belongsTo('App\Models\Merch\Composition', 'mr_composition_id', 'id');
	}


	public function construction()
	{
	    return $this->belongsTo('App\Models\Merch\Construction', 'mr_construction_id', 'id');
	}

	  // public function order_bom_placement()
	  // {
	  // 	return $this->hasMany('App\Models\Merch\OrdBomPlacement', ['order_id', 'mr_cat_item_id'], ['order_id', 'item_id']);
	  // }

	public static function getPoIdWisePoBOM($poId)
    {
        return DB::table('mr_po_bom_costing_booking')
			->select('id', 'mr_material_category_mcat_id AS mcat_id', 'mr_cat_item_id', 'item_description', 'clr_id', 'size', 'mr_supplier_sup_id', 'mr_article_id', 'uom', 'consumption', 'bom_term', 'precost_fob', 'precost_lc', 'precost_freight', 'precost_unit_price', 'extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'sl', 'depends_on', 'order_id', 'ord_bom_id', 'po_id')
			->where('po_id', $poId)
			->orderBy('sl', 'asc')
			->get();
    }

    public static function getPoWiseItem($poId, $selectedField)
    {
        $query = DB::table('mr_po_bom_costing_booking')
        ->where('po_id', $poId);
        if($selectedField != 'all'){
            $query->select($selectedField);
        }
        return $query->get();
    }

}
