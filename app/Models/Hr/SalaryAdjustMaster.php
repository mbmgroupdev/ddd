<?php

namespace App\Models\Hr;

use App\Models\Hr\SalaryAdjustMaster;
use Illuminate\Database\Eloquent\Model;

class SalaryAdjustMaster extends Model
{
    protected $table = 'hr_salary_adjust_master';
    protected $fillable = ['associate_id', 'month', 'year'];

    public static function getCheckEmployeeIdMonthYearWise($assId, $month, $year)
    {
    	return SalaryAdjustMaster::
        where('associate_id', $assId)
        ->where('month', $month)
        ->where('year', $year)
        ->first();
    }

    public static function insertEmployeeIdMonthYearWise($assId, $month, $year)
    {
    	return SalaryAdjustMaster::
        insertGetId([
            'associate_id' => $assId,
            'month'        => $month,
            'year'         => $year
        ]);
    }

    public static function getMonthYearWiseSalaryAdjust($data)
    {
        return SalaryAdjustMaster::
        where('month', $data['month'])
        ->where('year', $data['year'])
        ->get();
    }

    public function salary_adjust()
    {
        return $this->hasMany(SalaryAdjustDetails::class, 'salary_adjust_master_id', 'id')->where('type',1);
    }
}
