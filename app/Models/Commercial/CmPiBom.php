<?php

namespace App\Models\Commercial;

use App\Models\Merch\OrderBomCostingBooking;
use App\Models\Merch\MrOrderBooking;
use Illuminate\Database\Eloquent\Model;

class CmPiBom extends Model
{
    public $with = ['MrOrderBooking'];
    protected $table = 'cm_pi_bom';
    // public function costing_booking(){
    // 	return $this->belongsTo(OrderBomCostingBooking::class, 'mr_order_bom_costing_booking_id', 'id');
    // }
    public function MrOrderBooking()
    {
        return $this->belongsTo(MrOrderBooking::class,'mr_order_booking_id','id');
    }
}
