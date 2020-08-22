<?php

namespace App\Models\Hr;

use App\Models\Hr\Employee;
use App\Models\Hr\ShiftRoaster;
use Illuminate\Database\Eloquent\Model;

class ShiftRoaster extends Model
{
    protected $table= 'hr_shift_roaster';

    public $timestamps= false;

    public static function getRoasterEmployeeRangWise($year, $month)
    {
    	return ShiftRoaster::
    	where('shift_roaster_year', '>=', $year)
    	->where('shift_roaster_month', '>=', $month)
    	->get();
    }

    public function employee()
    {
    	return $this->belongsTo(Employee::class, 'shift_roaster_user_id', 'as_id');
    }
}
