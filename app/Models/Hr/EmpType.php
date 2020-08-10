<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class EmpType extends Model
{
	use SoftDeletes;

	protected $table = 'hr_emp_type';
	protected $primaryKey = 'emp_type_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getActiveEmpType()
    {
    	return DB::table('hr_emp_type')->where('hr_emp_type_status', 1)->get();
    }
}
