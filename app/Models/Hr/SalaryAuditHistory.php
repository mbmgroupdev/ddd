<?php

namespace App\Models\Hr;

use App\Models\Hr\SalaryAuditHistory;
use Illuminate\Database\Eloquent\Model;

class SalaryAuditHistory extends Model
{
	protected $table = 'salary_audit_history';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public static function checkSalaryAduitHistory($data)
    {
    	return SalaryAuditHistory::where('unit_id', $data['unit_id'])->where('month', $data['month'])->where('year', $data['year'])->orderBy('id', 'asc')->get();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'audit_id', 'id');
    }
}
