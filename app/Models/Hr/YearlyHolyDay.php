<?php

namespace App\Models\Hr;

use App\Models\Hr\YearlyHolyDay;
use Illuminate\Database\Eloquent\Model;

class YearlyHolyDay extends Model
{
    protected $table= "hr_yearly_holiday_planner";
    public $timestamps= false;
    protected $guarded = [];

    public static function getCheckUnitDayWiseHoliday($unit, $day, $status = null)
    {
    	$queue = YearlyHolyDay::
        where('hr_yhp_unit', $unit)
        ->where('hr_yhp_dates_of_holidays', $day);
        if($status == 'non-ot'){
            $queue->where('hr_yhp_open_status', '!=', 2);
        }
        return $queue->first();
    }

    public static function getCheckUnitDayWiseHolidayStatus($unit, $day, $status)
    {
        return YearlyHolyDay::
        where('hr_yhp_unit', $unit)
        ->where('hr_yhp_dates_of_holidays', $day)
        ->where('hr_yhp_open_status', $status)
        ->first();
    }

    public static function getCheckUnitDayWiseHolidayStatusMulti($unit, $day, $status)
    {
        return YearlyHolyDay::
        where('hr_yhp_unit', $unit)
        ->where('hr_yhp_dates_of_holidays', $day)
        ->where(function ($query) use ($status) {
            $query->whereIn('hr_yhp_open_status', $status);
        })
        ->first();
    }
}
