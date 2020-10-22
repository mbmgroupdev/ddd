<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
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

    public static function getDeptList()
    {
        return Department::pluck('hr_department_name', 'hr_department_id');
    }


    public static function getDepartmentAreaIdWise($id)
    {
    	return Department::where('hr_department_area_id', $id)->where('hr_department_status', 1)->get();
    }

    
    public function getDeptWiseEmp($unitId, $areaId, $department_id)
    {
        $where = [
            'as_unit_id' => $unitId,
            'as_area_id' => $areaId,
            'as_department_id' => $department_id,
            'as_status' => 1
        ];
        return Employee::where($where)->get();
    }

    public function getFloorCount($unitId)
    {
        return Floor::where(['hr_floor_unit_id' => $unitId, 'hr_floor_status' => 1])->count();
    }

    public static function getDeptListAsObject()
    {
        return Department::select('hr_department_name', 'hr_department_id')->get();
    }

    public function section()
    {
        return $this->hasMany('App\Models\Hr\Section', 'hr_section_department_id', 'hr_department_id');
    }


    public static function getSelctedDepartmentIdName($area_id)
    {
        return Department::select('hr_department_name', 'hr_department_id')->where('hr_department_area_id',$area_id)->get();
    }
}
