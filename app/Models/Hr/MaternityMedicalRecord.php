<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class MaternityMedicalRecord extends Model
{
	protected $table = 'hr_maternity_medical_record';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];
}
