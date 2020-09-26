<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\Hr\Unit;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
	use SoftDeletes;

	protected $table = 'hr_unit';
	protected $primaryKey = 'hr_unit_id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public static function unitList()
    {
        return Unit::pluck('hr_unit_name', 'hr_unit_id'); 
       
    }
    
    public static function unitWithSelectedId($id){
      return Unit::where('hr_unit_id', $id)->get();
    }

    public static function unitName($id)
    {
      $unitName= Unit::where('hr_unit_id',$id)->first(['hr_unit_name']); 
      return $unitName;
    }

    public static function getUnitNameBangla($id)
    {
      $unitName= Unit::where('hr_unit_id',$id)->first(['hr_unit_name_bn']); 
      return $unitName;
    }


    public function getUnitWiseEmp($unitId)
    {
      $where = [
        'as_status' => 1,
        'as_unit_id' => $unitId
      ];
      return Employee::where($where)->get();
    }

    public static function unitListAsObject()
    {
      $unitList = Unit::select('hr_unit_name', 'hr_unit_id')->get(); 
      return $unitList;
    }
    public static function getActiveUnit()
    {
    	return DB::table('hr_unit')->where('hr_unit_status', 1)->orderBy('hr_unit_name', 'desc')->get();
    }
}
