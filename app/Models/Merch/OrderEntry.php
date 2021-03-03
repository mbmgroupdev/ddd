<?php

namespace App\Models\Merch;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderEntry extends Model
{
    protected $table= 'mr_order_entry';
    public $timestamps= false;
    protected $with= ['style'];

    public static function getResIdWiseOrder($rId)
    {
    	return OrderEntry::where('res_id', $rId)->select(DB::raw("SUM(order_qty) AS sum"))->first();
    }

    public static function orders()
    {
        return OrderEntry::all();
    }

    public  function style()
    {
        return $this->belongsTo('App\Models\Merch\Style', 'mr_style_stl_id', 'stl_id');
    }

    public  function brand()
    {
        return $this->belongsTo('App\Models\Merch\Brand', 'mr_brand_br_id', 'br_id');
    }

    public  function unit()
    {
        return $this->belongsTo('App\Models\Hr\Unit', 'unit_id', 'hr_unit_id');
    }

    public  function season()
    {
        return $this->belongsTo('App\Models\Merch\Season', 'mr_season_se_id', 'se_id');
    }

    public  function buyer()
    {
        return $this->belongsTo('App\Models\Merch\Buyer', 'mr_buyer_b_id', 'b_id');
    }

    public  function order_costing()
    {
        return $this->belongsTo('App\Models\Merch\OrderBomOtherCosting', 'order_id', 'mr_order_entry_order_id');
    }
}
