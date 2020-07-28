<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

	protected $table = 'hr_location';
	protected $primaryKey = ['hr_location_id'];
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
