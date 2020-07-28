<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
	use SoftDeletes;

	protected $table = 'hr_designation';
	//protected $primaryKey = ['id'];
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
