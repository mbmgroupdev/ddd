<?php

namespace App\Repository\Hr;

use App\Contracts\Hr\EmployeeInterface;
use Illuminate\Support\Collection;
use App\Models\Employee;
use App\Models\Hr\HolidayRoaster;
use App\Models\Hr\Leave;
use App\Models\Hr\Unit;

use DB;

class EmployeeRepository implements EmployeeInterface
{
	public function getEmployees($input, $date = null)
	{
		$date = $date??date('Y-m-d');

		return DB::table('hr_as_basic_info')
            ->whereIn('as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('as_floor_id',$input['floor_id']);
            })
            ->when($input['otnonot']!=null, function ($query) use($input){
               return $query->where('as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id'){
                    if($input['selected'] == 'null'){
                        return $query->whereNull($input['report_group']);
                    }else{
                        return $query->where($input['report_group'], $input['selected']);
                    }
                }
            })
            ->where(function($q) use ($date){
                $q->where(function($qa) use ($date){
                    $qa->where('as_status',1);
                    $qa->where('as_doj' , '<=', $date);
                });
                $q->orWhere(function($qa) use ($date){
                    $qa->whereIn('as_status',[2,3,4,5,6,7,8]);
                    $qa->where('as_status_date' , '>=', $date);
                });

            })
            ->orderBy('temp_id', 'ASC')
            ->get();
	}
}