<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class AttendanceBonusConfig extends Model
{
    protected $table= "hr_attendance_bonus_dynamic";
    public $timestamps = false;
    protected $guarded = [];
}
