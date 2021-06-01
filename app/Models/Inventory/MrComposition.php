<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\MrOrderBomCostingBooking;

class MrComposition extends Model
{
    protected $table = "mr_composition";

    public function MrOrderBomCostingBooking()
    {
        return $this->hasMany(MrOrderBomCostingBooking::class);
    }
}
