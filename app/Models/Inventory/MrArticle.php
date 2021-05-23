<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\MrOrderBomCostingBooking;

class MrArticle extends Model
{
    protected $table = "mr_article";
    public function MrOrderBomCostingBooking()
    {
        return $this->hasMany(MrOrderBomCostingBooking::class);
    }
}
