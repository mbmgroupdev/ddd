<?php

namespace App\Models\Merch;
use Illuminate\Database\Eloquent\Model;
use DB;
class OrderEntry extends Model
{
    protected $table= 'mr_order_entry';
    protected $primaryKey = 'order_id';
    protected $fillable = ['order_code', 'res_id', 'unit_id', 'mr_buyer_b_id', 'mr_brand_br_id', 'order_month', 'order_year', 'mr_season_se_id', 'mr_style_stl_id', 'order_ref_no', 'order_qty', 'order_delivery_date', 'order_status', 'order_entry_source', 'pcd', 'bom_status', 'costing_status', 'created_by', 'updated_by'];
    public $timestamps= false;

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
        return $this->belongsTo('App\Models\HR\Unit', 'unit_id', 'hr_unit_id');
    }

    public  function buyer()
    {
        return $this->belongsTo('App\Models\Merch\Buyer', 'mr_buyer_b_id', 'b_id');
    }

    public  function season()
    {
        return $this->belongsTo('App\Models\Merch\Season', 'mr_season_se_id', 'se_id');
    }


    public  function order_costing()
    {
        return $this->belongsTo('App\Models\Merch\OrderBomOtherCosting', 'order_id', 'mr_order_entry_order_id');
    }

    public static function getCheckOrderExistCode($value)
    {
        return OrderEntry::where('order_code', $value)->exists();
    }

    public static function getCheckLastOrderNumber($code)
    {
        return OrderEntry::select(DB::raw('RIGHT(order_code,3) AS sl' ))->where('order_code', 'LIKE', $code.'%')->orderBy('sl', 'desc')->pluck('sl')->first();
    }

    public static function getCheckOrderExists($value)
    {
        return OrderEntry::where('mr_style_stl_id', $value['mr_style_stl_id'])
        ->where('order_month', $value['order_month'])
        ->where('order_year', $value['order_year'])
        ->where('order_delivery_date', $value['order_delivery_date'])
        ->exists();
    }

    public static function getOrderInfoIdWise($orderId)
    {
        return OrderEntry::where('order_id', $orderId)->first();
    }

    public static function orderInfoWithStyle($id)
    {
        return OrderEntry::with(['style'])
            ->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions())
            ->where("order_id", $id)
            ->first();
    }

    public static function getOrderQtySumResIdWise($resId)
    {
        return DB::table('mr_order_entry')
            ->select('res_id', DB::raw("SUM(order_qty) AS qty"))
            ->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions())
            ->where('res_id', $resId)
            ->first();
    }

    public static function getOrderListWithStyleResIdWise($resId=null)
    {
        $query = OrderEntry::with(['style'])
            ->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions());
        if($resId != null){
            $query->where('res_id', $resId);
        }
        $data = $query->orderBy('order_id', 'DESC')->get();
        return $data;
    }

    public static function getReservationWiseOrderQty()
    {
        return DB::table('mr_order_entry')
            ->select('res_id', DB::raw("SUM(order_qty) AS qty"))
            ->groupBy('res_id')
            ->get()
            ->keyBy('res_id', true);
    }
}
