<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class MaternityMedical extends Model
{
	protected $table = 'hr_maternity_medical';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function record()
    {
    	return $this->hasMany('App\Models\Hr\MaternityMedicalRecord', 'hr_maternity_medical_id', 'id');
    }
}
