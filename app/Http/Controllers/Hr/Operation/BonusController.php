<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\BonusType;
use App\Models\Hr\BonusRule;
use Illuminate\Http\Request;
use Carbon\Carbon;

use DB;

class BonusController extends Controller
{
	protected $cutoff_date;

	protected $eligible_doj;

	protected $bonus_type;

	protected $special_rule;

	protected $bonus_percent;

	protected $bonus_amount;

	public function __construct()
	{
		ini_set('zlib.output_compression', 1);
	}

    public function index()
    {
    	$bonusType = BonusType::pluck('bonus_type_name', 'id');
    	return view('hr.operation.bonus.index', compact('bonusType'));
    }

    public function process(Request $request)
    {
    	$input = $request->all();

    	$input['report_format'] = $input['report_format']??1;
    	$input['pay_status']    = $input['pay_status']??'all';
    	$input['report_group'] = $input['report_group']??'as_department_id';

    	$this->special_rule  = $request->special_rule??null;
    	$this->cutoff_date   = $request->cut_date;
    	$this->bonus_percent = $request->bonus_percent??null;
    	$this->bonus_amount  = $request->bonus_amount??null;
    	$this->eligible_doj  = Carbon::parse($request->cut_date)
    		 					->subMonths(3)->toDateString();

    	return $this->getBonusEligibleList($input);
    }

    protected function getBonusEligibleList($input)
    {
    	$data = $this->getBonusEligible($input);

    	$com['summary'] 	= $this->makeSummary($data);

    	$list = collect($data)
        	->groupBy($input['report_group'],true);

        if($input['report_format'] == 1){
        	$list = $list->map(function($q){
        		$q = collect($q);
		    	$sum  = (object)[];
		    	$sum->ot 		= $q->where('as_ot', 1)->count();
		    	$sum->ot_amount 	= $q->where('as_ot', 1)->sum('bonus_amount');
		    	$sum->nonot 			= $q->where('as_ot', 0)->count();
		    	$sum->nonot_amount 	= $q->where('as_ot', 0)->sum('bonus_amount');
		    	return $sum;
		    })->all();
        }


        $com['uniqueGroup'] = $list;
    	$com['input']		= $input;
    	$com['format']		= $input['report_group'];
    	$com['unit'] 		= unit_by_id();
        $com['location'] 	= location_by_id();
        $com['line'] 		= line_by_id();
        $com['floor'] 		= floor_by_id();
        $com['department'] 	= department_by_id();
        $com['designation'] = designation_by_id();
        $com['section'] 	= section_by_id();
        $com['subSection'] 	= subSection_by_id();
        $com['area'] 		= area_by_id();

        return view('hr.operation.bonus.bonus_eligible_list',$com)->render();

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
    		->where(function($q) use ($input){
    			if($input['emp_type'] == 'maternity'){
    				$q->where('e.as_status',6);
    			}else{
    				$q->whereIn('e.as_status',[1,6]); //maternity & active
    			}
    		})
    		
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
            ->when(empty($this->special_rule), function($q){
            	$q->where('e.as_doj','<=', $this->eligible_doj);
            })
            ->orderBy('ben.ben_current_salary','DESC')
            ->get();

        $from = Carbon::parse($this->cutoff_date);
        $percent = $this->bonus_percent/100;

        

        $data = collect($data)
        	->map(function($q) use($from, $percent){
        		$q->month = Carbon::parse($q->as_doj)->diffInMonths($from);
        		$bonus_month = $q->month > 12?12:$q->month;
        		if($percent!=null){
	    			$q->bonus_amount = ceil(($q->ben_basic/12)*$bonus_month*$percent);
        		}else{
        			$q->bonus_amount = $this->bonus_amount;
        		}
        		$q->pay_status = 1;
        		$q->bank_payable = $q->bonus_amount;
        		$q->cash_payable = $q->bonus_amount;
        		$q->stamp = 10;
        		// map based on custom rule
        		return $q;
        	});

        //return only below 12 month
        if($input['emp_type'] == 'partial'){
        	$data =	collect($data)->filter(function($q){
	        		return $q->month < 12;
	        })->values();
        }
        // custom filter for custom rule
        if($this->special_rule){	
	        $data =	collect($data)->filter(function($q){
	        		return $q->month >= 3;
	        })->values();
        }

        return $data;
    }


    protected function makeSummary($data)
    {
    	$data = collect($data);
    	$sum  = (object)[];
    	$sum->maternity 		= $data->where('as_status', 6)->count();
    	$sum->maternity_amount 	= $data->where('as_status', 6)->sum('bonus_amount');
    	$sum->active 			= $data->where('as_status', 1)->count();
    	$sum->active_amount 	= $data->where('as_status', 1)->sum('bonus_amount');
    	$sum->ot 				= $data->where('as_ot', 1)->count();
    	$sum->ot_amount 		= $data->where('as_ot', 1)->sum('bonus_amount');
    	$sum->nonot 			= $data->where('as_ot', 0)->count();
    	$sum->nonot_amount 		= $data->where('as_ot', 0)->sum('bonus_amount');
    	$sum->partial 			= $data->where('month','<' ,12)->count();
    	$sum->partial_amount 	= $data->where('month','<' ,12)->sum('bonus_amount');

    	return $sum;
    }


    protected function storeRule($request)
    {
    	$rule = new BonusRule();
    	$rule->unit_id = auth()->user()->unit_permissions()[0];
     	$rule->bonus_type_id = 1;
     	$rule->bonus_year = date('Y', strtotime($request->cut_date));
     	$rule->amount = $request->bonus_amount;
     	$rule->percent_of_basic = $request->bonus_percent;
     	$rule->cutoff_date = $request->cut_date;
     	if($request->special_rule){

	     	$rule->special_rule = json_encode($request->special_rule);
     	}
     	$rule->created_by = auth()->id();
     	$rule->save();

     	return $rule;
    }

    protected function formatData($data, $rule_id)
    {
    	$insert = [];

    	foreach ($data as $k => $v) {
    		$insert[$k] = [
    			'unit_id' => $v->as_unit_id,
    			'bonus_rule_id' => $rule_id,
    			'associate_id' => $v->associate_id,
    			'bonus_amount' => $v->bonus_amount,
    			'gross_salary' => $v->ben_current_salary,
    			'basic' => $v->ben_basic,
    			'medical' => $v->ben_medical,
    			'transport' => $v->ben_transport,
    			'food' => $v->ben_food,
    			'duration' => $v->month,
    			'stamp' => 10,
    			'pay_status' => 1,
    			'emp_status' => $v->as_status,
    			'net_payable' => ($v->bonus_amount - 10),
    			'cash_payable' => ($v->bonus_amount - 10),
    			'subsection_id' => $v->as_subsection_id,
    			'designation_id' => $v->as_designation_id,
    			'ot_status' => $v->as_ot,
    		];
    	}
    	return $insert;
    }

    public function toAproval(Request $request)
    {
    	
    	$rule = $this->storeRule($request);

    	$input['report_format'] = $input['report_format']??1;
    	$input['pay_status']    = $input['pay_status']??'all';
    	$input['report_group'] = $input['report_group']??'as_department_id';
    	$input['emp_type'] = $input['emp_type']??'all';

    	$this->special_rule  = $request->special_rule??null;
    	$this->cutoff_date   = $request->cut_date;
    	$this->bonus_percent = $request->bonus_percent??null;
    	$this->bonus_amount  = $request->bonus_amount??null;
    	$this->eligible_doj  = Carbon::parse($request->cut_date)
    		 					->subMonths(3)->toDateString();

    	$data = $this->getBonusEligible($input);
    	$processdData = $this->formatData($data, $rule->id);
    	
    	$chunk = collect($processdData)
    				->chunk(300);


    	foreach ($chunk as $key => $n) {
    		
    		DB::table('hr_bonus_sheet')->insert(collect($n)->toArray());
    	}

    	return 'success';
    	
    }


}
