<?php

namespace App\Models\Hr;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class AttendanceUndeclared extends Model
{
	protected $table = 'hr_attendance_undeclared';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function employee()
    {
    	return $this->belongsTo(Employee::class, 'as_id', 'as_id');
    }
}
