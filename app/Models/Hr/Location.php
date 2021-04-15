<?php

namespace App\Models\Hr;

use App\Models\Hr\Location;
use Illuminate\Database\Eloquent\Model;
use DB;

class Location extends Model
{

	protected $table = 'hr_location';
	protected $primaryKey = 'hr_location_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
    
    public static function getLocationDistinct()
    {
    	return Location::groupBy('hr_location_name')->get();
    }

 	public static function getLocationNameBangla($id)
 	{
 		$locationName = '';
 		$location = Location::where('hr_location_id',$id)->first();
 		if($location) {
 			if($location->hr_location_name_bn != null) {
 				$locationName = $location->hr_location_name_bn;
 			} else {
 				$locationName = $location->hr_location_name;
 			}
 		}
 		return $locationName;

 	}

    public static function getUnitWiseLocation($unitId)
    {
        return DB::table('hr_location')
        ->where('hr_location_unit_id', $unitId)
        ->first();
    }
}
