<?php

namespace App\Models\Merch;

use App\Models\Merch\ProductSize;
use Illuminate\Database\Eloquent\Model;
use DB;

class ProductSize extends Model
{
    protected $table= 'mr_product_size';
    public $timestamps= false;

    public static function getPalleteNameSizeGroupIdWise($sizeGroupId, $value)
    {
    	return ProductSize::select('mr_product_pallete_name')->where('mr_product_size_group_id', $sizeGroupId)
    	 ->where('mr_product_pallete_name', 'LIKE', '%'. $value .'%')
    	->get();
    }

	public static function getProductSizeGroupIdWiseInfo($sizeId)
    {
    	return DB::table('mr_product_size')->select('mr_product_size.*', DB::raw("'0' as value"))->where('mr_product_size_group_id', $sizeId)->orderBy('mr_product_size_serial', 'asc')->get();
    }    
}
