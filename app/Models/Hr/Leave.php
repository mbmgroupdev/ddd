<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
	protected $table = 'hr_leave';
    protected $guarded = [];

    protected $dates = [
        'created_at','leave_from','leave_to'
    ];

    public static function getDateStatusWiseEmployeeLeaveCheck($assId, $date, $status)
    {
    	return Leave::
        where('leave_ass_id', $assId)
        ->where('leave_from', '<=', $date)
        ->where('leave_to', '>=', $date)
        ->where('leave_status', $status)
        ->first();
    }
}
