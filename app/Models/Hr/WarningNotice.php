<?php

namespace App\Models\Hr;

use App\Models\Hr\WarningNotice;
use Illuminate\Database\Eloquent\Model;

class WarningNotice extends Model
{
	protected $table = 'hr_warning_notice';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public static function getEmployeeMonthWiseNotice($data)
    {
    	return WarningNotice::
    	where('associate_id', $data['associate'])
    	->where('month_year', $data['month_year'])
    	->first();
    }
}
