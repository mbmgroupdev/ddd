<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use DB;

class AdvanceInfo extends Model
{
    protected $table= "hr_as_adv_info";
    public $timestamps = false;
    protected $guarded = [];

    public static function checkExistID($id)
    {
    	return DB::table('hr_as_adv_info')
    	->where('emp_adv_info_nid', $id)
    	->first();
    }
}
