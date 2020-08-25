<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use DB;

class HrMonthlySalary extends Model
{
	protected $table = 'hr_monthly_salary';
    protected $guarded = ['id'];

    protected $dates = [
        'created_at', 'updated_at'
    ];
    
    public static function getSalaryListWithEIdFormTo($data)
    {
    	return HrMonthlySalary::
    	where('as_id', $data['as_id'])
    	->where('year', '>=', $data['formYear'])
    	->where('year', '<=', $data['toYear'])
    	->where('month', '>=', $data['formMonth'])
    	->where('month', '<=', $data['toMonth'])
    	->orderBy('as_id', 'desc')
    	->get();
    }

    public static function getSalaryListFilterWise($as_id, $month, $year, $min, $max)
    {
        $query = HrMonthlySalary::where('as_id', $as_id);

        if($month != ''){
            $query->where('month', $month);
        }

        if($year != ''){
            $query->where('year', $year);
        }

        if($min != ''){
            $query->where('gross', '>=', $min);
        }

        if($max != ''){
            $query->where('gross', '<=', $max);
        }

        return $query->first();

    }



    public function salary_add_deduct()
    {
    	return $this->belongsTo('App\Models\Hr\Employee', 'as_id', 'associate_id');
    }
    public function activeEmployee()
    {
    	return $this->belongsTo('App\Models\Hr\Employee', 'as_id', 'associate_id')->where('as_status', 5);
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Hr\Employee', 'as_id', 'associate_id');
    }

    public function employee_bengali()
    {
    	return $this->belongsTo('App\Models\Hr\EmployeeBengali', 'as_id', 'hr_bn_associate_id');
    }

    public function add_deduct()
    {
    	return $this->belongsTo('App\Models\Hr\SalaryAddDeduct', 'salary_add_deduct_id', 'id');
    }

    public function benefits()
    {
    	return $this->belongsTo('App\Models\Hr\Benefits', 'as_id', 'ben_as_id');
    }

    // public function leave_adjust_master(){
    //     return $this->hasOne(SalaryAdjustMaster::class, ['associate_id'], ['as_id']);
    //     // return $this->hasOne(SalaryAdjustMaster::class, ['associate_id', 'month', 'year'], ['as_id', 'month', 'year']);
    // }

    public static function getEmployeeSalary($data)
    {
        return HrMonthlySalary::
        where('as_id', $data['as_id'])
        ->where('year', $data['year'])
        ->where('month', $data['month'])
        ->first();
    }

    public static function getYearlyActivityMonthWise($asId, $year)
    {
        return DB::table('hr_monthly_salary')
            ->select('late_count', 'present', 'holiday', 'absent', 'leave', 'month', 'ot_hour')
            ->where('as_id', $asId)
            ->where('year', $year)
            ->orderBy('month', 'asc')
            ->groupBy('month')
            ->get();
    }

    public static function getYearlySalaryMonthWise($asId, $year)
    {
        return DB::table('hr_monthly_salary')->select('month','total_payable')
            ->where('as_id', $asId)
            ->where('year', $year)
            ->orderBy('month', 'asc')
            ->groupBy('month')
            ->get();
    }
}
