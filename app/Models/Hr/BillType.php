<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class BillType extends Model
{
	protected $table = 'hr_bill_type';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
