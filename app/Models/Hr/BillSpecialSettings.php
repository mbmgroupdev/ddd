<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class BillSpecialSettings extends Model
{
	protected $table = 'hr_bill_special_settings';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
