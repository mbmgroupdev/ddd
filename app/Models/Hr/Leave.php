<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
	protected $table = 'hr_leave';
    protected $guarded = [];

    protected $dates = [
        'created_at'
    ];
}
