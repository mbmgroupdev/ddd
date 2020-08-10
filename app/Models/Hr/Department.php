<?php

namespace App\Models\Hr;

use App\Models\Hr\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
	use SoftDeletes;
	 
	protected $table = 'hr_department';
	protected $primaryKey = 'hr_department_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getDepartmentAreaIdWise($id)
    {
    	return Department::where('hr_department_area_id', $id)->where('hr_department_status', 1)->get();
    }
}
