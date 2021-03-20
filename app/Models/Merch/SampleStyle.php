<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class SampleStyle extends Model
{
    protected $table= 'mr_stl_sample';
    public $timestamps= false;

    public static function getStyleIdWiseSampleName($stlId)
    {
    	return DB::table("mr_stl_sample AS ss")
		    	->select(DB::raw("GROUP_CONCAT(st.sample_name SEPARATOR ', ') AS name"))
		    	->leftJoin("mr_sample_type AS st", "st.sample_id", "ss.sample_id")
		    	->where("ss.stl_id", $stlId)
		    	->first();
    }
}
