<?php

namespace App\Models\Hr;

use App\Models\Hr\Designation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
	use SoftDeletes;

	protected $table = 'hr_designation';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public static function getDesignationEmpTypeIdWise($id)
    {
    	return Designation::where('hr_designation_emp_type', $id)->where('hr_designation_status', 1)->get();
    }
}
