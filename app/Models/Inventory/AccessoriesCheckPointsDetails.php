<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\AccessoriesCheckPointsDetails;

class AccessoriesCheckPointsDetails extends Model
{
    protected $table = "st_inspection_details_points_accessories";
    public $timestamps = false;

    // All Points  list
    public static function getDetailsPointsList()
    {
    	return AccessoriesCheckPointsDetails::get();
    }

}
