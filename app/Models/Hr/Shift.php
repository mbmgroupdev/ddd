<?php

namespace App\Models\Hr;

use App\Models\Hr\Employee;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{ 
    protected $table= 'hr_shift';
    public $timestamps= false;

    use Compoships;
    public static function getShiftIdWise($id)
    {
    	return Shift::where('hr_shift_id', $id)->first();
    }

    public static function checkExistsTimeWiseShift($data)
    {
    	return Shift::
    	where('hr_shift_id', $data['hr_shift_id'])
    	->where('hr_shift_name', $data['hr_shift_name'])
    	->where('hr_shift_start_time', $data['hr_shift_start_time'])
    	->where('hr_shift_end_time', $data['hr_shift_end_time'])
    	->where('hr_shift_break_time', $data['hr_shift_break_time'])
    	->first();
    }

    public static function getShiftsByUnitIdWiseUqiue($unit_id){
        return Shift::
        where('hr_shift_unit_id', $unit_id)
        ->select('hr_shift_name')
        ->distinct('hr_shift_name')
        ->get()
        ->toArray();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'hr_shift_unit_id', 'hr_unit_id');
    }

    public static function checkExistsShiftCode($unit, $code)
    {
        return Shift::
        where('hr_shift_unit_id', $unit)
        ->where('hr_shift_code', $code)
        ->first();
    }

    public static function getCheckUniqueUnitIdShiftName($unit, $shiftName)
    {
        return Shift::
        where('hr_shift_unit_id', $unit)
        ->where('hr_shift_name', $shiftName)
        ->latest()
        ->first();
    }
}
