<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class Increment extends Model
{
    protected $table = "hr_increment_process";

    protected $guarded = "id";

    public $timestamps = false;
}
