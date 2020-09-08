<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class SalaryAudit extends Model
{
	protected $table = 'salary_audit';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public static function checkSalaryAuditStatus($data)
    {
    	return SalaryAudit::where('unit_id', $data['unit_id'])->where('month', $data['month'])->where('year', $data['year'])->first();
    }
}
