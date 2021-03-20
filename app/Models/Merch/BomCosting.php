<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class BomCosting extends Model
{
	// public $with = ['cat_item', 'article', 'construction', 'composition', 'supplier'];
    protected $table= 'mr_stl_bom_n_costing';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function cat_item()
    {
        return $this->belongsTo(McatItem::class, 'mr_cat_item_id', 'id');
    }
    public function article()
    {
        return $this->belongsTo(Article::class, 'mr_article_id', 'id');
    }
    public function construction()
    {
        return $this->belongsTo(Construction::class, 'mr_construction_id', 'id');
    }
    public function composition()
    {
        return $this->belongsTo(Composition::class, 'mr_composition_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'mr_supplier_sup_id', 'sup_id');
    }

    public static function getStyleWiseItem($stlId, $selectedField)
    {
        $query = DB::table('mr_stl_bom_n_costing')
        ->where('mr_style_stl_id', $stlId);
        if($selectedField != 'all'){
            $query->select($selectedField);
        }
        return $query->get();
    }

    public static function getStyleIdWiseStyleBOM($stlId)
    {
        return DB::table('mr_stl_bom_n_costing')
            ->select('id', 'mr_material_category_mcat_id AS mcat_id', 'mr_cat_item_id', 'item_description', 'clr_id', 'size', 'mr_supplier_sup_id', 'mr_article_id', 'uom', 'consumption', 'bom_term', 'precost_fob', 'precost_lc', 'precost_freight', 'precost_unit_price', 'extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'sl')
            ->where('mr_style_stl_id', $stlId)
            ->orderBy('sl', 'asc')
            ->get();
    }

    public static function getStyleWiseItemUnitPrice($stlId)
    {
        return DB::table('mr_stl_bom_n_costing')
            ->select('id', 'mr_cat_item_id', DB::raw('((precost_unit_price + precost_fob + precost_lc + precost_freight)  * ((consumption*extra_percent)/100 + consumption)) AS unitprice'), 'precost_unit_price')
            ->where('mr_style_stl_id', $stlId)
            ->orderBy('sl', 'asc')
            ->get();
    }
}
