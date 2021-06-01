<?php

namespace App\Models\Inventory;

use App\Models\Inventory\CmPiBom;
use App\Models\Inventory\MrArticle;
use App\Models\Inventory\MrCatItem;
use App\Models\Inventory\MrOrderEntry;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\MrConstruction;
use App\Models\Merch\MrOrderBooking;
use App\Models\Inventory\MrComposition;

class MrOrderBomCostingBooking extends Model
{
    public $with = ['MrCatItem','MrArticle','MrConstruction','MrComposition'];
    protected $table = 'mr_order_bom_costing_booking';
    // public function CmPiBom()
    // {
    //     return $this->hasMany(CmPiBom::class);
    // }

    public function MrOrderBooking()
    {
        return $this->hasMany(MrOrderBooking::class);
    }
    public function MrCatItem()
    {
        return $this->belongsTo(MrCatItem::class);
    }
    public function MrArticle()
    {
        return $this->belongsTo(MrArticle::class);
    }
    public function MrConstruction()
    {
        return $this->belongsTo(MrConstruction::class);
    }
    public function MrComposition()
    {
        return $this->belongsTo(MrComposition::class);
    }
    public function MrOrderEntry()
    {
        return $this->belongsTo(MrOrderEntry::class, 'order_id', 'order_id');
    }
}
