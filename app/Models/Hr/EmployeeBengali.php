<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class EmployeeBengali extends Model
{
    protected $table = "hr_employee_bengali";

    protected $guarded = ["hr_bn_id"];

    public $timestamps = false;
}
