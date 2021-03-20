<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class StyleSpecialMachine extends Model
{
    protected $table= 'mr_style_sp_machine';
    public $timestamps= false;

    public static function getStyleIdWiseSpMachineName($stlId)
    {
    	return DB::table("mr_style_sp_machine AS sm")
		    	->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
		    	->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
		    	->where("sm.stl_id", $stlId)
		    	->first();
    }
}
