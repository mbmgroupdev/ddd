<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\AssetReceiveItem;

class AssetReceiveItem extends Model
{
    protected $table= 'st_asset_receive_item';
    public $timestamps = false;

   // All Asset  list
    public static function getAssetReceive()
    {
    	return AssetReceiveItem::get();
    }
}
