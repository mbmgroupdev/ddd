<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class PurchaseOrder extends Model
{
    protected $table= 'mr_purchase_order';
    protected $primaryKey = 'po_id';
    protected $fillable = ['mr_order_entry_order_id', 'po_no', 'po_qty', 'po_ex_fty', 'po_delivery_country', 'country_fob', 'remarks', 'port_id', 'clr_id'];
    public $timestamps= false;

    public static function getPOCheckUniqueWiseExists($value)
    {
    	return DB::table('mr_purchase_order')
    	->where('po_no', $value['po_no'])
    	->where('po_delivery_country', $value['po_delivery_country'])
    	->where('clr_id', $value['clr_id'])
    	->exists();
    }

    public static function getPoOrderSumQtyOrderIdWise($orderId)
    {
        return DB::table('mr_purchase_order')
        ->where('mr_order_entry_order_id', $orderId)
        ->select(DB::raw("SUM(po_qty) AS sum"))
        ->pluck('sum')
        ->first();
    }
}
