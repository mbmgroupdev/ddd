<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class BomOtherCosting extends Model
{
    protected $table= 'mr_stl_bom_other_costing';
    protected $guarded = [];
    protected $dates = [
        'created_at', 'updated_at'
    ];

    public static function getStyleIdWiseStyleOtherCosting($stlId)
    {
    	return DB::table('mr_stl_bom_other_costing')
			->where('mr_style_stl_id', $stlId)
			->first();
    }
}
