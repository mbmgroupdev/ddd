<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Area extends Model
{
	use SoftDeletes;

	protected $table = 'hr_area';
	protected $primaryKey = ['hr_area_id'];
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getActiveArea()
    {
    	return DB::table('hr_area')->where('hr_area_status', 1)->get();
    }
}
