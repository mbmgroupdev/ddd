<?php

namespace App\Models\Hr;

use App\Models\Hr\SalaryAudit;
use App\User;
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

    public function hr()
    {
        $info = '';
        if($this->hr_audit){
            $info =  User::find($this->hr_audit);
        }
        
        return $info;

    }

    public function audit()
    {
        $info = '';
        if($this->initial_audit){
            $info =  User::find($this->initial_audit);
        }
        return $info;
    }

    public function accounts()
    {
        $info = '';
        if($this->accounts_audit){
            $info =  User::find($this->accounts_audit);
        }
        return $info;
    }

    public function management()
    {
        $info = '';
        if($this->management_audit){
            $info =  User::find($this->management_audit);
        }
        return $info;
    }
}
