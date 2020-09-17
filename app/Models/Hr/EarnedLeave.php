<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class EarnedLeave extends Model
{
	protected $table = 'hr_earned_leave';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
