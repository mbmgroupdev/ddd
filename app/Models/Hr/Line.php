<?php

namespace App\Models\Hr;

use App\Models\Hr\Line;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Line extends Model
{
	use SoftDeletes;

	protected $table = 'hr_line';
	protected $primaryKey = 'hr_line_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getSelectedLineIdName($floor_id){
    	return Line::select(['hr_line_id','hr_line_name'])->where('hr_line_floor_id',$floor_id)->get();
    }
}
