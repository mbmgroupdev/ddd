<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class BomCosting extends Model
{
	// public $with = ['cat_item', 'article', 'construction', 'composition', 'supplier'];
    protected $table= 'mr_stl_bom_n_costing';
    public $timestamps= false;

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
}
