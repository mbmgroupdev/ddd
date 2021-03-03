<?php

namespace App\Models\Merch;

use App\Models\Merch\Supplier;
use Illuminate\Database\Eloquent\Model;

class SupplierItemType extends Model
{
    protected $table= 'mr_supplier_item_type';
    public $timestamps= false;

    public function supplier()
    {
    	return $this->belongsTo(Supplier::class, 'mr_supplier_sup_id', 'sup_id');
    }
}
