<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class HrMonthlySalary extends Model
{
	protected $table = 'hr_monthly_salary';
    protected $guarded = ['id'];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
