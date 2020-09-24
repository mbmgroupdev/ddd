<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
	protected $table = 'hr_voucher';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
