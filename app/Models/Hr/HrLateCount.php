<?php

namespace App\Models\Hr;

use App\Models\Hr\HrLateCount;
use App\Models\Hr\Unit;
use App\Models\Hr\Shift;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class HrLateCount extends Model
{
	// public $with = ['shift'];
    protected $table = "hr_late_count";
    protected $fillable = ['hr_unit_id', 'hr_shift_name', 'default_value', 'date_from', 'date_to', 'value', 'created_by', 'updated_by'];
    use Compoships;
    public function unit()
    {
    	return $this->hasOne(Unit::class,'hr_unit_id','hr_unit_id');
    }
    public function shift()
    {
        return $this->hasOne(Shift::class, ['hr_shift_unit_id', 'hr_shift_name'], ['hr_unit_id', 'hr_shift_name'])->latest();
    }

    public static function getUnitWiseCheckExists($unitId)
    {
    	return HrLateCount::where('hr_unit_id', $unitId)->first();
    }
  
    public static function getUnitShiftNameWiseCheckExists($value)
    {
        return HrLateCount::
        where('hr_unit_id', $value['hr_unit_id'])
        ->where('hr_shift_name', $value['hr_shift_name'])
        ->first();
    }

    public static function getUnitShiftIdWiseCheckExists($unitId, $shiftName)
    {
        return HrLateCount::where('hr_unit_id', $unitId)->where('hr_shift_name', $shiftName)->latest()->first();
    }

    public static function getUnitShiftWiseCheckExists($unitId, $shiftId)
    {
        return HrLateCount::where(['hr_unit_id' => $unitId, 'hr_shift_id' => $shiftId])->first();
    }

    public static function getCheckExistsLateCount($data)
    {
        return HrLateCount::
        where('hr_unit_id', $data->hr_unit_id)
        ->where('hr_shift_id', $data->hr_shift_id)
        ->where('date_from', $data->date_from)
        ->where('date_to', $data->date_to)
        ->where('value', $data->time)
        ->first();
    }
}
