<?php

namespace App\Models\Hr;

use App\Models\Hr\SalaryAdjustDetails;
use Illuminate\Database\Eloquent\Model;

class SalaryAdjustDetails extends Model
{
    protected $table = 'hr_salary_adjust_details';
    protected $fillable = ['salary_adjust_master_id', 'date', 'amount', 'status', 'type'];

    public static function getCheckEmployeeWiseMasterDetails($data)
    {
    	return SalaryAdjustDetails::
        where([
            'salary_adjust_master_id' => $data['master_id'],
            'date' => $data['date'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'status' => $data['status']
            ])
        ->first();
    }

    public static function insertMasterDetails($data)
    {
    	return SalaryAdjustDetails::
        insert([
            'salary_adjust_master_id' => $data['master_id'],
            'date' => $data['date'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'status' => $data['status']
            ]);
    }

}
