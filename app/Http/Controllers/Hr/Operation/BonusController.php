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

	protected $unit_permissions;



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
    	$this->bonus_type    = $this->getBonusType($request->type_id);
    	$this->eligible_doj  = Carbon::parse($request->cut_date)
    		 					->subMonths(3)->toDateString();
        if(!empty($input['selected'])){
            $input['report_format'] = 0;
        }
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

    protected function getBonusType($id)
    {
    	return BonusType::where('id',$id)->first();
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
        $special = [];
        if($this->special_rule){
	        $special = collect($this->special_rule)
	        	->map(function($q){
	        		return collect($q)->map(function($p){
		        		$p['basic_percent'] = $p['basic_percent']/100;
		        		return $p;
	        		})->keyBy('id');
	        	})->all();
	        $special = collect($special)->toArray();
	    }



        $data = collect($data)
        	->map(function($q) use($from, $percent, $special){
        		$q->stamp = 10;
        		$fixed_amount = $this->bonus_amount;
        		
        		// amount based on custom rule

        		if($this->special_rule){
        			foreach ($special as $key => $v) {
        				if (isset($v[$q->{$key}])) {
        					$rule = $v[$q->{$key}];
        					if($rule['cutoff_date'] && $rule['cutoff_date'] != $this->cutoff_date){
        						$from = Carbon::parse($rule['cutoff_date']);
        					}
        					if($rule['basic_percent'] != null){
        						$percent = $rule['basic_percent'];
			        		}else{
			        			$fixed_amount = $rule['amount'];
			        		}
        					
        				}
        			}
        		}
        		// get month
        		$q->month = Carbon::parse($q->as_doj)->diffInMonths($from);
        		$bonus_month = $q->month > 12?12:$q->month;

        		// get bonus amount
        		if($fixed_amount > 0){
        			$q->bonus_amount = $fixed_amount;
        		}else{
	    			$q->bonus_amount = ceil(($q->ben_basic/12)*$bonus_month*$percent);
        		}

        		return $q;
        	});

        //return only below 12 month
        if($input['emp_type'] == 'partial'){
        	$data =	collect($data)->filter(function($q){
	        		return $q->month < 12;
	        })->values();
        }
        // apply custom filter for custom rule
        if($this->special_rule){	
	        $data =	collect($data)->filter(function($q) use ($special){
	        		$status = $q->month >= $this->bonus_type->eligible_month;
	        		foreach ($special as $key => $v) {
	        			if (isset($v[$q->{$key}])) {
        					$rule = $v[$q->{$key}];
        					$status = $q->month >= $rule['month'];
        				}
	        		}
	        		return $status;
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
     	$rule->bonus_type_id = $request->type_id;
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
    			'location_id' => $v->as_location,
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
    	try{

	    	$rule = $this->storeRule($request);
	    	if($rule->id){

		    	$input['report_format'] = $input['report_format']??1;
		    	$input['pay_status']    = $input['pay_status']??'all';
		    	$input['report_group'] = $input['report_group']??'as_department_id';
		    	$input['emp_type'] = $input['emp_type']??'all';

		    	$this->special_rule  = $request->special_rule??null;
		    	$this->cutoff_date   = $request->cut_date;
		    	$this->bonus_percent = $request->bonus_percent??null;
		    	$this->bonus_amount  = $request->bonus_amount??null;
		    	$this->bonus_type    = $this->getBonusType($request->type_id);
		    	$this->eligible_doj  = Carbon::parse($request->cut_date)
		    		 					->subMonths(3)->toDateString();

		    	$data = $this->getBonusEligible($input);
		    	$processdData = $this->formatData($data, $rule->id);
		    	
		    	$chunk = collect($processdData)
		    				->chunk(300);


		    	foreach ($chunk as $key => $n) {
		    		
		    		DB::table('hr_bonus_sheet')->insert(collect($n)->toArray());
		    	}
		    	return 'Bonus sheet has been proceed to Approval';
	    	}

	    	return 'Failed to proceed for Approval';

	    } catch(\Exception $e){

	    	return $e->getMessage();
	    }

    	
    }

    public function approvalProcess()
    {
    	try {
    		$unitBonus = BonusRule::orderBy('id', 'desc')->get();
    		$bonusType = bonus_type_by_id();
    		$unit = unit_by_id();
    		return view('hr/operation/bonus/process_index', compact('unitBonus', 'bonusType', 'unit'));
    	} catch (\Exception $e) {
    		toastr()->error($e->getMessage());
    		return back();
    	}
    }

    public function approvalSheet(Request $request)
    {
    	$input = $request->all();
    	// return $input;
    	try {
    		$bonusSheet = BonusRule::findOrFail($input['bonus_sheet']);
    		if($bonusSheet == null){
    			toastr()->error("Sheet Not Found!");
    			return back();
    		}
    		$unitList = unit_by_id();
    		$unitList = collect($unitList)->pluck('hr_unit_name', 'hr_unit_id');
    		$locationList = location_by_id();
    		$locationList = collect($locationList)->pluck('hr_location_name', 'hr_location_id');
    		$areaList = area_by_id();
    		$areaList = collect($areaList)->pluck('hr_area_name', 'hr_area_id');
    		$salaryMin = 0;
    		$salaryMax = 350000;

    		return view('hr/operation/bonus/process_report', compact('bonusSheet', 'unitList', 'locationList', 'input', 'areaList', 'salaryMin', 'salaryMax'));

    	} catch (\Exception $e) {
    		toastr()->error($e->getMessage());
    		return back();
    	}
    }

    public function disburse(Request $request)
    {
    	try {

            $data['year'] = DB::table('hr_bonus_rule as br')
            			->whereIn('br.unit_id',auth()->user()->unit_permissions())
            			->distinct()
            			->pluck('bonus_year','bonus_year');

            $data['bonus_type'] = collect(bonus_type_by_id())->pluck('bonus_type_name', 'id')->toArray(); 
            $data['unit'] 		= unit_by_id(); 

            $data['unitList']      = collect(unit_by_id())
                		->pluck('hr_unit_name', 'hr_unit_id')
                		->toArray();
            $data['locationList']  = collect(location_by_id())
            ->pluck('hr_location_name', 'hr_location_id')
            ->toArray();

            $data['areaList']  = collect(area_by_id())
            ->pluck('hr_area_name', 'hr_area_id')
            ->toArray();

            
            $data['salaryMin']     = 0;
            $data['salaryMax']     = 350000;


            return view('hr.operation.bonus.bonus_disburse', $data);

        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    // get unit wise bonus rules
    protected function getRuleId($bonus_type, $year)
    {
    	return DB::table('hr_bonus_rule')
    			->where('bonus_type_id', $bonus_type)
    			->where('bonus_year', $year)
    			->whereIn('unit_id',auth()->user()->unit_permissions())
    			->pluck('id');
    }

    protected function getBonusList($input)
    {
    	$rule_id = $this->getRuleId($input['bonus_type'],$input['bonus_year']);

    	return DB::table('hr_bonus_sheet as bns')
    			->select('bns.*','sub.*','e.as_name','e.as_gender','b.hr_bn_associate_name','e.as_doj','e.temp_id','e.as_oracle_code')
    			->whereIn('bns.bonus_rule_id',$rule_id)
    			->whereIn('bns.unit_id',auth()->user()->unit_permissions())
    			->whereIn('bns.location_id',auth()->user()->location_permissions())
    			->leftJoin('hr_as_basic_info as e','e.associate_id','bns.associate_id')
    			->leftJoin('hr_subsection as sub','bns.subsection_id', 'sub.hr_subsec_id')
    			->leftJoin('hr_employee_bengali as b','b.hr_bn_associate_id', 'bns.associate_id')
    			->when(!empty($input['unit']), function ($query) use($input){
	                if($input['unit'] == 145){
	                    return $query->whereIn('bns.unit_id',[1, 4, 5]);
	                }else{
	                    return $query->where('bns.unit_id',$input['unit']);
	                }
	            })
	            ->when(!empty($input['as_id']), function ($query) use($input){
	               return $query->whereIn('bns.associate_id',$input['as_id']);
	            })
	            ->when(!empty($input['location']), function ($query) use($input){
	               return $query->where('bns.location_id',$input['location']);
	            })
	            ->when(!empty($input['line_id']), function ($query) use($input){
	               return $query->where('e.as_line_id', $input['line_id']);
	            })
	            ->when(!empty($input['floor_id']), function ($query) use($input){
	               return $query->where('e.as_floor_id',$input['floor_id']);
	            })
	            ->when(!empty($input['otnonot']), function ($query) use($input){
	               return $query->where('bns.ot_status',$input['otnonot']);
	            })
	            ->when(!empty($input['area']), function ($query) use($input){
	               return $query->where('sub.hr_subsec_area_id',$input['area']);
	            })
	            ->when(!empty($input['department']), function ($query) use($input){
	               return $query->where('sub.hr_subsec_department_id',$input['department']);
	            })
	            ->when(!empty($input['section']), function ($query) use($input){
	               return $query->where('sub.hr_subsec_section_id', $input['section']);
	            })
	            ->when(!empty($input['subSection']), function ($query) use($input){
	               return $query->where('bns.subsection_id', $input['subSection']);
	            })
	            ->orderBy('e.as_oracle_sl')
	            ->orderBy('e.temp_id')
	            ->get();
    }

    public function unitWiseBonusSheet(Request $request)
    {
    	
    	$input = $request->all();
    	$bonusList =  $this->getBonusList($input);
    	$com['bonusList'] =  collect($bonusList)
    					->groupBy('unit_id',true)
    					->map(function($q){
    						return collect($q)->groupBy('location_id',true)
    								->map(function($p){
    									return collect($p)->chunk(10);
    								});
    					})
    					->all();

    	$com['unit'] 		= unit_by_id();
        $com['location'] 	= location_by_id();
        $com['line'] 		= line_by_id();
        $com['floor'] 		= floor_by_id();
        $com['department'] 	= department_by_id();
        $com['designation'] = designation_by_id();
        $com['section'] 	= section_by_id();
        $com['subSection'] 	= subSection_by_id();
        $com['area'] 		= area_by_id();

        ini_set('zlib.output_compression', 1);
        return view('hr.operation.bonus.bonus_sheet_unit', $com)->render();




    }

}
