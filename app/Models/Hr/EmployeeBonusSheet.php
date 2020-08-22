<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class EmployeeBonusSheet extends Model
{
    protected $table=  'hr_bonus_sheet';

    protected $guarded = ['id','created_at','updated_at'];
}
