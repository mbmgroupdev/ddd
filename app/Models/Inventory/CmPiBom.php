<?php

namespace App\Models\Inventory;

use App\Models\Inventory\MrArticle;
use App\Models\Inventory\MrCatItem;
use Illuminate\Database\Eloquent\Model;

use App\Models\Merch\MrOrderBooking;

class CmPiBom extends Model
{
  public $with = ['MrOrderBooking'];
    protected $table = 'cm_pi_bom';

    public function MrOrderBooking()
    {
        return $this->belongsTo(MrOrderBooking::class);
    }
}
