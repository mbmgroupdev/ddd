<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UOM extends Model
{
    protected $table = 'uom';
    protected $fillable = ['measurement_name', 'measurement_short_name'];
}
