<?php

namespace App\Models\Hr;

use App\Models\Hr\HolidayRoaster;
use Illuminate\Database\Eloquent\Model;

class HolidayRoaster extends Model
{
    protected $table = "holiday_roaster";
    protected $guarded = [];

    public $timestamps = false;

    public static function getHolidayYearMonthAsIdDateWise($year, $month, $asId, $date)
    {
      // dd($date);exit;
    	return HolidayRoaster::
    	select('remarks')
    	->where('year', $year)
    	->where('month', $month)
    	->where('as_id', $asId)
    	->where('date', $date)
    	->first();
    }

    public static function getHolidayYearMonthAsIdDateWiseRemark($year, $month, $asId, $date, $remark)
    {
    	return HolidayRoaster::where('year', $year)
    	->where('month', $month)
    	->where('as_id', $asId)
    	->where('date', $date)
    	->where('remarks', $remark)
    	->first();
    }

    public static function getHolidayYearMonthAsIdDateWiseRemarkMulti($year, $month, $asId, $date, $remarks)
    {
        return HolidayRoaster::where('year', $year)
        ->where('month', $month)
        ->where('as_id', $asId)
        ->where('date', $date)
        ->where(function ($query) use ($remarks) {
            $query->whereIn('remarks', $remarks);
        })
        ->first();
    }
}
