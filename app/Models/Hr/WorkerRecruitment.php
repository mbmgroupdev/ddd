<?php

namespace App\Models\Hr;

use App\Models\Hr\Area;
use App\Models\Hr\Designation;
use App\Models\Hr\EmpType;
use App\Models\Hr\Unit;
use App\Models\Hr\WorkerRecruitment;
use Illuminate\Database\Eloquent\Model;

class WorkerRecruitment extends Model
{
	protected $table = 'hr_worker_recruitment';
	protected $primaryKey = 'worker_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'worker_dob', 'worker_doj'
    ];

    public static function checkRecruitmentWorker($data)
    {
    	return WorkerRecruitment::where('worker_name', $data['worker_name'])
    	->where('worker_dob', $data['worker_dob'])
    	->where('worker_contact', $data['worker_contact'])
    	->exists();
    }

    public function employee_type()
    {
        return $this->belongsTo(EmpType::class, 'worker_emp_type_id', 'emp_type_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'worker_designation_id', 'hr_designation_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'worker_unit_id', 'hr_unit_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'worker_area_id', 'hr_area_id');
    }
}
