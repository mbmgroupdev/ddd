<?php

namespace App\Models\Merch;

use App\Models\Merch\Style;
use App\Models\Merch\StyleImage;
use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    protected $table= 'mr_style';
    public $timestamps= false;
    // public $rules = [
    //
    //         "stl_no" => "required|max:30|unique:mr_style,mr_buyer_b_id,prd_type_id,mr_season_se_id",
    //      ];

    public static function checkStyleNoTextWise($stl_no)
    {
    	return Style::select('stl_no')->where('stl_no', $stl_no)->first();
    }

    public function style_image()
    {
    	return $this->hasMany(StyleImage::class, 'mr_stl_id', 'stl_id');
    }

    public  function order()
    {
        return $this->hasMany('App\Models\Merch\OrderEntry', 'stl_id', 'mr_style_stl_id');
    }
}
