<?php

namespace App\Models\Merch;

use App\Models\Merch\StyleSizeGroup;
use Illuminate\Database\Eloquent\Model;
use DB;

class StyleSizeGroup extends Model
{
    protected $table= 'mr_stl_size_group';
    public $timestamps= false;

    public static function getSizeGroupIdStyleIdWise($styleId)
    {
    	return StyleSizeGroup::select('mr_product_size_group_id')->where('mr_style_stl_id', $styleId)
    	->get();
    }

    public static function getSizeGroupIdStyleWise($stlId)
    {
    	return DB::table('mr_stl_size_group')
              ->where('mr_style_stl_id', $stlId)
              ->pluck('mr_product_size_group_id');
    }
}
