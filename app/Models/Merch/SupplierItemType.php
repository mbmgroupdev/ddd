<?php

namespace App\Models\Merch;

use App\Models\Merch\Supplier;
use Illuminate\Database\Eloquent\Model;
use DB;

class SupplierItemType extends Model
{
    protected $table= 'mr_supplier_item_type';
    public $timestamps= false;

    public function supplier()
    {
    	return $this->belongsTo(Supplier::class, 'mr_supplier_sup_id', 'sup_id');
    }

    public static function getSupplierItemTypeCatIdsWise($catIds)
    {
        $supplierData = DB::table('mr_supplier');
        $supplierDataSql = $supplierData->toSql();
        return DB::table('mr_supplier_item_type AS si')
            ->select('s.sup_id', 's.sup_name', 'si.mcat_id')
            ->join(DB::raw('(' . $supplierDataSql. ') AS s'), function($join) use ($supplierData) {
                $join->on('s.sup_id','si.mr_supplier_sup_id')->addBinding($supplierData->getBindings());
            })
            ->whereIn('si.mcat_id', $catIds)
            ->get();
    }
}
