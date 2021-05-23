<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use DB;

class StSubStoreInfo extends Model
{
	protected $table = "st_sub_store_info";
	public $timestamps = false;

	public static function getIdAndFloor(){
	    $data	= StSubStoreInfo::select([
							'id',
							'floor'
							])
							->get();
		return $data;
	}
}