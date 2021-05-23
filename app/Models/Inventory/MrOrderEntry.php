<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\MrOrderBomCostingBooking;

class MrOrderEntry extends Model
{
    public $timestamps = false;
    protected $table = "mr_order_entry";
    public function MrOrderBomCostingBooking()
    {
        return $this->hasMany(MrOrderBomCostingBooking::class);
    }
}
