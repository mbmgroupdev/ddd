<?php

namespace App\Models\Hr;

use App\Models\Hr\SalaryIndividualAudit;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SalaryIndividualAudit extends Model
{
	protected $table = 'salary_audit_individual';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'audit_by', 'id');
    }
    public static function checkSalaryIndividualAuditStatus($data)
    {
    	return SalaryIndividualAudit::where('unit_id', $data['unit_id'])->where('month', $data['month'])->where('year', $data['year'])->where('as_id', $data['as_id'])->first();
    }

    public static function getSalaryIndividualAuditMonthWise($data)
    {
        return SalaryIndividualAudit::where('unit_id', $data['unit_id'])->where('month', $data['month'])->where('year', $data['year'])->get();
    }

    public static function getSalaryIndividualAuditMonthWiseDelete($data)
    {
        return SalaryIndividualAudit::where('unit_id', $data['unit_id'])->where('month', $data['month'])->where('year', $data['year'])->delete();
    }

    public static function getSalaryIndividualAuditMonthStatusWise($data, $status)
    {
        return SalaryIndividualAudit::where('unit_id', $data['unit_id'])->where('month', $data['month'])->where('year', $data['year'])->where('status', $status)->get();
    }
}
