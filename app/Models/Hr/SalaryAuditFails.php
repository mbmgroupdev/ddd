<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class SalaryAuditFails extends Model
{
	protected $table = 'salary_audit_fails';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
