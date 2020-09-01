<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class SalaryAuditHistory extends Model
{
	protected $table = 'salary_audit_history';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
