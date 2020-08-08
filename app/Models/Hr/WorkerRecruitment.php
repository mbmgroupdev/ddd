<?php

namespace App\Models\Hr;

use App\Models\Hr\WorkerRecruitment;
use Illuminate\Database\Eloquent\Model;

class WorkerRecruitment extends Model
{
	protected $table = 'hr_worker_recruitment';
	protected $primaryKey = ['worker_id'];
    protected $guarded = [];

    protected $dates = [
        'worker_created_at', 'updated_at', 'worker_dob', 'worker_doj'
    ];

    public static function checkRecruitmentWorker($data)
    {
    	return WorkerRecruitment::where('worker_name', $data['worker_name'])
    	->where('worker_dob', $data['worker_dob'])
    	->where('worker_contact', $data['worker_contact'])
    	->exists();
    }
}
