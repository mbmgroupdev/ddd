<?php

namespace App\Models\Hr;

use App\Models\Hr\Benefits;
use Illuminate\Database\Eloquent\Model;

class Benefits extends Model
{
    protected $table= "hr_benefits";
    public $timestamps = false;
    protected $guarded = [];

    public static function getSalaryRangeMin()
    {
    	return Benefits::min('ben_current_salary');
    }

    public static function getSalaryRangeMax()
    {
    	return Benefits::max('ben_current_salary');
    }

    public static function getEmployeeAssIdwise($assId)
    {
        return Benefits::
        where('ben_as_id', $assId)
        ->first();
    }
}
