<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class OrderBomOtherCosting extends Model
{
    protected $table= 'mr_order_bom_other_costing';
    public $timestamps= false;

    public static function getOrderIdWiseOrderOtherCosting($ordId)
    {
    	return DB::table('mr_order_bom_other_costing')
			->where('mr_order_entry_order_id', $ordId)
			->first();
    }
}
