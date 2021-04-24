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

    public function getEmployeesByStatus($input)
    {
        $input['area']        = isset($input['area'])?$input['area']:'';
        $input['department']  = isset($input['department'])?$input['department']:'';
        $input['line_id']     = isset($input['line_id'])?$input['line_id']:'';
        $input['floor_id']    = isset($input['floor_id'])?$input['floor_id']:'';
        $input['section']     = isset($input['section'])?$input['section']:'';
        $input['subSection']  = isset($input['subSection'])?$input['subSection']:'';
        $input['otnonot']     = isset($input['otnonot'])?$input['otnonot']:'';
        $input['min_salary']  = (double)(isset($input['min_salary'])?$input['min_salary']:'');
        $input['max_salary']  = (double)(isset($input['max_salary'])?$input['max_salary']:'');

        $query = DB::table('hr_as_basic_info AS b')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('b.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('b.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('b.as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('b.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('b.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('b.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('b.as_floor_id',$input['floor_id']);
            })
            ->when($input['otnonot']!=null, function ($query) use($input){
               return $query->where('b.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('b.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('b.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id'){
                    if($input['selected'] == 'null'){
                        return $query->whereNull('b.'.$input['report_group']);
                    }else{
                        return $query->where('b.'.$input['report_group'], $input['selected']);
                    }
                }
            })
            ->when(!empty($input['status']), function ($query) use($input){
               return $query->where('b.as_status',$input['status']);
            });
        if(!empty($input['max_salary']) && !empty($input['max_salary'])){
            $getBenefit = DB::table('hr_benefits');
            $getBenefitSql = $getBenefit->toSql();
            $query->leftjoin(DB::raw('(' . $getBenefitSql. ') AS ben'), function($join) use ($getBenefit) {
              $join->on('ben.ben_as_id', '=', 'b.associate_id')->addBinding($getBenefit->getBindings());
            })
            ->whereBetween('ben.ben_current_salary',array($input['min_salary'],$input['max_salary']));

        }
        return $query->orderBy('temp_id', 'ASC')->get();
    }

    public function getEmployeeByAssociateId($asIds, $selected = null){
        $query = DB::table('hr_as_basic_info')
        ->whereIn('associate_id', $asIds);
        if($selected != null){
            $query->select($selected);
        }
        return $query->get();
    }
}