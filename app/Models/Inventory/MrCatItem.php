<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\MrOrderBomCostingBooking;

class MrCatItem extends Model
{
    protected $table = "mr_cat_item";
    public function MrOrderBomCostingBooking()
    {
        return $this->hasMany(MrOrderBomCostingBooking::class);
    }
}
