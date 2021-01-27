<?php

namespace App\Models\Hr;

use App\Models\Hr\AttendanceHistory;
use Illuminate\Database\Eloquent\Model;

class AttendanceHistory extends Model
{
	protected $table = 'hr_attendance_history';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public static function checkExistsAttendanceHistory($asId, $unitId, $date, $record)
    {
    	return AttendanceHistory::where('unit_id', $unitId)
    	->where('as_id', $asId)
    	->where('att_date', $date)
    	->where('raw_data', $record)
    	->first();
    }
}
