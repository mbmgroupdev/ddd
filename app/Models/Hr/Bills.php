<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
	protected $table = 'hr_bill';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
