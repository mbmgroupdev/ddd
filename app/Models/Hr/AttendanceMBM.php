<?php

namespace App\Models\Hr;

use App\Models\Hr\AttendanceMBM;
use Illuminate\Database\Eloquent\Model;

class AttendanceMBM extends Model
{
	// public $with = ['shift'];
    protected $table= "hr_attendance_mbm";
    protected $fillable = ['as_id', 'in_time', 'out_time', 'hr_shift_code', 'remarks', 'ot_hour', 'late_status', 'in_unit', 'out_unit'];

    public function employee()
    {
    	return $this->belongsTo('App\Models\Employee', 'as_id', 'as_id');
    }

    public function shift()
    {
    	return $this->belongsTo('App\Models\Hr\Shift', 'hr_shift_code', 'hr_shift_code');
    }
}
