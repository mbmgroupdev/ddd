<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Unit extends Model
{
	use SoftDeletes;

	protected $table = 'hr_unit';
	protected $primaryKey = 'hr_unit_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getActiveUnit()
    {
    	return DB::table('hr_unit')->where('hr_unit_status', 1)->get();
    }
}
