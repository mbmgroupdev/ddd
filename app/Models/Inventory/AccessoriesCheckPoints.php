<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\AccessoriesCheckPoints;

class AccessoriesCheckPoints extends Model
{
    protected $table = "st_accessories_check_points";
    public $timestamps = false;

    // All Points  list
    public static function getCheckPointsList()
    {
    	return AccessoriesCheckPoints::get();
    }

}
