<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Floor extends Model
{
	use SoftDeletes;
	 
	protected $table = 'hr_floor';
	protected $primaryKey = ['hr_floor_id'];
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}
