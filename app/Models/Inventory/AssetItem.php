<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\AssetItem;

class AssetItem extends Model
{
    protected $table= 'st_asset_item';
    public $timestamps = false;

    // All Asset   list
    public static function getAssetList()
    {
    	return AssetItem::pluck('item_name','id');
    }
    // All Asset details  list
    public static function getAssetDetailsList()
    {
    	return AssetItem::get();
    }
}
