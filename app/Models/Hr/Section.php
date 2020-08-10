<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
	use SoftDeletes;

	protected $table = 'hr_section';
	protected $primaryKey = 'hr_section_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getSectionDepartmentIdWise($id)
    {
        return Section::where('hr_section_department_id', $id)->where('hr_section_status', 1)->get();
    }
    public static function getSectionList()
    {
     	return Section::pluck('hr_section_name', 'hr_section_id');

    }

    public function getSectionWiseEmp($unitId, $areaId, $department_id, $floor_id, $section_id)
    {
    	$where = [
            'as_unit_id' => $unitId,
            'as_area_id' => $areaId,
            'as_department_id' => $department_id,
            'as_floor_id' => $floor_id,
            'as_section_id' => $section_id,
            'as_status' => 1
        ];
        return Employee::where($where)->get();
    }

    public function getSubsectionCount($areaId,$departmentId,$sectionId)
    {
    	return Subsection::where(['hr_subsec_area_id' => $areaId, 'hr_subsec_department_id' => $departmentId, 'hr_subsec_section_id' => $sectionId, 'hr_subsec_status' => 1])->count();
    }
}
