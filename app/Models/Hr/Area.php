<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Employee;
use App\Models\Hr\Department;
use DB;

class Area extends Model
{
	use SoftDeletes;

	protected $table = 'hr_area';
	protected $primaryKey = 'hr_area_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getActiveArea()
    {
    	return DB::table('hr_area')->where('hr_area_status', 1)->get();
    }

    public static function getActiveAreaList()
    {
        return DB::table('hr_area')->where('hr_area_status', 1)->pluck('hr_area_name', 'hr_area_id');
    }
    public function getAreaWiseEmp($unitId, $areaId)
    {
        $where = [
            'as_unit_id' => $unitId,
            'as_area_id' => $areaId,
            'as_status'  => 1
        ];
        return Employee::where($where)->get();
    }

    public function department()
    {
        return $this->hasMany('App\Models\Hr\Department', 'hr_department_area_id', 'hr_area_id')->orderBy('sequence', 'ASC');
    }

    public function section()
    {
        return $this->hasMany('App\Models\Hr\Section', 'hr_section_area_id', 'hr_area_id');
    }

    public function getAreaName($areaId)
    {
        return $this->where('hr_area_id',$areaId)->first();
    }

    public function getDepartmentCount($areaId)
    {
        return Department::where(['hr_department_area_id' => $areaId, 'hr_department_status' => 1])->count();
    }

    public static function getAreatListAsObject(){
        return Area::select(['hr_area_id','hr_area_name'])->get();
    }
}
