<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\BonusType;
use Illuminate\Http\Request;
use Carbon\Carbon;

use DB;

class BonusController extends Controller
{
	protected $cutoff_date;

	protected $eligible_doj;

	protected $bonus_type;

	protected $special;

	protected $ben_percent;

    public function index()
    {
    	$bonusType = BonusType::pluck('bonus_type_name', 'id');
    	return view('hr.operation.bonus.index', compact('bonusType'));
    }

    public function process(Request $request)
    {
    	$input = $request->all();
    	$input['report_format'] = 0;
    	$input['pay_status'] = 'all';
    	$directRule = '';
    	$specialRule = '';

    	$unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();

    	$this->cutoff_date = $request->cutoff_date;
    	$this->ben_percent = 100;
    	$this->eligible_doj = Carbon::parse($request->cutoff_date)
    		 					->subMonths(3)->toDateString();

    	$eligible_list = $this->getBonusEligible($input);

    	return view('hr.operation.bonus.index', compact('bonusType'));
    }

    protected function getBonusEligible($input)
    {

    	DB::statement( DB::raw('SET @cutoff_date = "'.$this->cutoff_date.'"'));
    	$data =  DB::table('hr_as_basic_info as e')
    		->select(
    			'e.*',
    			'ben.*'
    		)
    		->leftJoin('hr_benefits as ben', 'e.associate_id', 'ben.ben_as_id')
    		->whereIn('e.as_status',[1,6]) //maternity & active
    		->whereIn('e.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('e.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('e.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('e.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('e.as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('e.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('e.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('e.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('e.as_floor_id',$input['floor_id']);
            })
            ->when(!empty($input['otnonot']), function ($query) use($input){
               return $query->where('e.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('e.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id'){
                    if($input['selected'] == 'null'){
                        return $query->whereNull('e.'.$input['report_group']);
                    }else{
                        return $query->where('e.'.$input['report_group'], $input['selected']);
                    }
                }
            })
            ->get();

        $from = Carbon::parse($this->cutoff_date);
        $percent = $this->ben_percent/100;

        return collect($data)
        	->map(function($q) use($from, $percent){
        		$q->month = Carbon::parse($q->as_doj)->diffInMonths($from);
        		$bonus_month = $q->month > 12?12:$q->month;
    			$q->amount = ceil(($q->ben_basic/12)*$bonus_month*$percent);
        		return $q;
        	})
        	->filter(function($q){
        		return $q->month >= 3;
        	})->values()->all()
        	->groupBy('as_department_id');
    }


    protected function storeRule()
    {

    }
}
