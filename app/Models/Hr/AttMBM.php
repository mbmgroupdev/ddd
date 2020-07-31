<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class AttMBM extends Model
{
	protected $table = 'hr_attendance_mbm';
    protected $guarded = ['id'];

    protected $with = ['employee'];

    protected $dates = [
        'created_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'as_id', 'as_id');
    }
}
