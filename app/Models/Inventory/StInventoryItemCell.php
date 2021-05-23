<?php

namespace App\Models\Inventory;

use App\Models\Inventory\StInventoryEntry;
use Illuminate\Database\Eloquent\Model;

class StInventoryItemCell extends Model
{
	public $with = ['inventory_entry'];
    protected $table = "st_inventory_item_cell";

    public function inventory_entry()
    {
        return $this->belongsTo(StInventoryEntry::class, 'st_inventory_entry_id', 'id');
    }
    
}
