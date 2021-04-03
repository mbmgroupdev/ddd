<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class PoSizeQty extends Model
{
	protected $table = 'mr_po_size_qty';
	protected $primaryKey = 'id';
    protected $guarded = [];

    public static function getPoSizeQtyPoIdWise($poId)
    {
    	return DB::table('mr_po_size_qty')
    	->where('po_id', $poId)
    	->get();
    }
}
