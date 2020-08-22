<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table= "hr_events";
    protected $fillable=['event_title', 'event_start', 'event_end'];
}
