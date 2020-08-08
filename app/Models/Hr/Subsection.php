<?php

namespace App\Models\Hr;

use App\Models\Hr\Subsection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subsection extends Model
{
	use SoftDeletes;

	protected $table = 'hr_subsection';
	protected $primaryKey = 'hr_subsec_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getSubSectionSectionIdWise($id)
    {
    	return Subsection::where('hr_subsec_section_id', $id)->where('hr_subsec_status', 1)->get();
    }
}
