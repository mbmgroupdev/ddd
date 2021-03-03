<?php

namespace App\Models\Merch;

use App\Models\Hr\Unit;
use App\Models\Merch\Supplier;
use Illuminate\Database\Eloquent\Model;
use App\Models\Merch\PoBookingDetail;
use App\Models\Inventory\MrOrderBomCostingBooking;
use App\Models\Inventory\CmPiBom;

class MrOrderBooking extends Model
{
    // public $with = ['MrOrderBomCostingBooking'];
    protected $table = 'mr_order_booking';
    public $timestamps = false;

    public function MrOrderBomCostingBooking()
    {
        return $this->belongsTo(MrOrderBomCostingBooking::class);
    }

    public function CmPiBom()
    {
        return $this->hasMany(CmPiBom::class);
    }

}
