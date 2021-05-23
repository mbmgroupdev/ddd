<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\InspectionDetailsAccessories;

class InspectionDetailsAccessories extends Model
{
    protected $table = "st_inspection_details_accessories";
    public $timestamps = false;

    // All Points  list
    public static function getInspectionDetails()
    {
    	return InspectionDetailsAccessories::get();
    }

}
