<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\AssetReceiveFromSupplier;

class AssetReceiveFromSupplier extends Model
{
    protected $table= 'st_asset_receive_from_suppler';
    public $timestamps = false;

   // All Asset  list
    public static function getAssetReceiveSupplier()
    {
    	return AssetReceiveFromSupplier::get();
    }
}
