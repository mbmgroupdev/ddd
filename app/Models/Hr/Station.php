<?php

namespace App\Models\Hr;

use App\Models\Hr\Station;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table= "hr_station";
    public $timestamps= false;


    public static function checkDateWiseExists($date, $asId)
    {
    	return Station::
    	where('associate_id', $asId)
    	->where('start_date', '<=', $date)
    	->where('end_date', '>=', $date)
    	->first();
    }

    public static function checkDateRangeWiseStartDateExists($asId, $startDate, $endDate)
    {
    	return Station::
    	where('associate_id', $asId)
    	->whereBetween('start_date',array($startDate, $endDate))
    	->first();
    }

    public static function checkDateRangeWiseEndDateExists($asId, $startDate, $endDate)
    {
    	return Station::
    	where('associate_id', $asId)
    	->whereBetween('end_date',array($startDate, $endDate))
    	->first();
    }

    public function floor()
    {
        return $this->belongsTo('App\Models\Hr\Floor', 'changed_floor', 'hr_floor_id');
    }
    public function line()
    {
        return $this->belongsTo('App\Models\Hr\Line', 'changed_line', 'hr_line_id');
    }
}
