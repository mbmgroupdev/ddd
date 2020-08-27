<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Floor extends Model
{
	use SoftDeletes;
	 
	protected $table = 'hr_floor';
	protected $primaryKey = 'hr_floor_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getFloorList()
    {
    	return Floor::pluck('hr_floor_name', 'hr_floor_id');
    }

    public static function getSelectedFloorIdName($unit_id){
        return Floor::select(['hr_floor_id', 'hr_floor_name'])->where('hr_floor_unit_id',$unit_id)->get();
    }

    public static function getFloorNameBangla($id)
    {
        return Floor::where('hr_floor_id',$id)->first(['hr_floor_name_bn']);
    }


    public function getFloorWiseEmp($unitId, $areaId, $department_id, $floor_id)
    {
        $where = [
            'as_unit_id' => $unitId,
            'as_area_id' => $areaId,
            'as_department_id' => $department_id,
            'as_floor_id' => $floor_id,
            'as_status' => 1
        ];
        return Employee::where($where)->get();
    }

    public function getSectionCount($areaId,$departmentId)
    {
        return Section::where(['hr_section_area_id' => $areaId, 'hr_section_department_id' => $departmentId, 'hr_section_status' => 1])->count();
    }
}
