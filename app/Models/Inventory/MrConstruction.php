<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\MrOrderBomCostingBooking;

class MrConstruction extends Model
{
    protected $table = "mr_construction";
    public function MrOrderBomCostingBooking()
    {
        return $this->hasMany(MrOrderBomCostingBooking::class);
    }
}
