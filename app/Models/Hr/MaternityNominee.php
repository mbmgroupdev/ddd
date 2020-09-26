<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class MaternityNominee extends Model
{
	protected $table = 'hr_maternity_nominee';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
