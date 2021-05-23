<?php

namespace App\Http\Controllers\Commercial\Query;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use Carbon\Carbon;
use Validator, Auth, ACL, DB, DataTables, Exception, stdClass;

class SalesContractQueryController extends Controller
{
    public function index(Request $request){
    	try{

    		return $this->salesContractQueryGlobal($request->all());

    	}catch(\Exception $e){
    		return $e->getMessage();
    	}
    }

    public function getSearchType($request)
    {
        $datecon= new stdClass;
        if($request['type'] == 'month') {
            $datecon->month = date('m', strtotime($request['month']));
            $datecon->year = date('Y', strtotime($request['month']));
        }else if ($request['type'] == 'range') {
            $datecon->from = $request['rangeFrom'];
            $datecon->to = $request['rangeTo'];
        }else if($request['type'] == 'year') {
            $datecon->year = $request['year'];
        }else if($request['type'] == 'date') {
            $datecon->date = $request['date'];
        }else{
            $datecon->date = date('Y-m-d');
        }
        return $datecon;
    }

    public function pageTitle($request){
    		// dd($request['scSearchBy']);exit;
    		if($request['scSearchBy'] == 'sc_created'){
    			$on = ' On Created ';
    		}
    		else if($request['scSearchBy'] == 'sc_expired'){
    			$on = ' On Expired ';
    		}

            $showTitle = 'Salescontract'.$on.' - '.ucwords($request['type']) ;
            if(isset($request['date']))
            {
                $showTitle =$showTitle.': '.$request['date'];
            }
            if(isset($request['month']))
            {
                $showTitle =$showTitle.': '.$request['month'];
            }
            if(isset($request['year']))
            {
                $showTitle =$showTitle.': '.$request['year'];
            }
            if($request['type']=='range'){
                $showTitle =$showTitle.': '.$request['rangeFrom'].' to '.$request['rangeTo'];
            }

            return $showTitle;
    }

    public function salesContractQueryGlobal($request){
    	// dd($request);
    	try {


            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            // dd($datecon, $showTitle, $request['scSearchBy']);exit;
            // $sales_contract_info = $this->salesContractInfo($datecon,$request['scSearchBy'], []);
            // dd('Fetched Data:', sizeof($sales_contract_info));exit;
            // dd('Fetched Data:', $sales_contract_info);exit;

            // $total_unit  = DB::table('cm_sales_contract')->distinct('hr_unit_id')->count('hr_unit_id');
            // $total_buyer = DB::table('cm_sales_contract')->distinct('mr_buyer_b_id')->count('mr_buyer_b_id');

            $total_unit  = DB::table('hr_unit')->count('hr_unit_id');
            $total_buyer = DB::table('mr_buyer')->count('b_id');
			
			// dd($total_unit, $total_buyer);exit;            

			$result['page'] = view('commercial.query.salescontract.unit_buyer_salescontract',
                compact('showTitle','request','total_unit', 'total_buyer'))->render();
            $result['url'] = url('commercial/query?').http_build_query($request);
            return $result;


        }catch(\Exception $e) {
            return $e->getMessage();
        }


    	
    }

    public function salescontractUnits(Request $request){

    	$parts = parse_url(url()->previous());
    	// dd($parts);exit;
        parse_str($parts['query'], $request1);
        $datecon = $this->getSearchType($request1);
        $condition = [];
        // return dd($request1);
        $request1['view']= 'allunit';

    	$showTitle = $this->pageTitle($request1);


    	// $total_units  = DB::table('cm_sales_contract as b')->distinct('b.hr_unit_id')
    	// 							->leftJoin('hr_unit as c', 'c.hr_unit_id', 'b.hr_unit_id')
    	// 							->select(['c.hr_unit_short_name','b.hr_unit_id'])
    	// 							->get();
    	// dd($request, $total_units);exit;
    	$total_units  = DB::table('hr_unit')->select(['hr_unit_short_name','hr_unit_id', 'hr_unit_name'])
    										->get();

    	if($request1['scSearchBy'] == 'sc_created'){
	    	foreach ($total_units as $unit) {
	    		$unit_wise_total_sc = DB::table('cm_sales_contract as b')
	    									->where('b.hr_unit_id', $unit->hr_unit_id)
	    									->where(function($query) use ($datecon) {
						                        if(!empty($datecon->month)){
						                            $query->whereMonth('b.created_at', '=', $datecon->month);
						                            $query->whereYear('b.created_at', '=', $datecon->year);
						                        }
						                        if(!empty($datecon->year) && empty($datecon->month)){
						                            $query->whereYear('b.created_at', '=', $datecon->year);
						                        }
						                        if(!empty($datecon->from)){
						                            $query->whereBetween('b.created_at', [$datecon->from,$datecon->to]);
						                        }

											})
											->where($condition)
											->select([
												DB::raw('COUNT(b.id) as total_sc'),
												DB::raw('SUM(b.initial_value) as total_value')
											])
											->get();
	    						// 			->count('b.id');
	    		$unit->total_sc = $unit_wise_total_sc;
	    	}
    	}
    	else if($request1['scSearchBy'] == 'sc_expired'){
    		foreach ($total_units as $unit) {
	    		$unit_wise_total_sc = DB::table('cm_sales_contract_amend as a')
	    										->leftJoin('cm_sales_contract as sc', 'sc.id', 'a.cm_sales_contract_id')
	    										->where('sc.hr_unit_id', $unit->hr_unit_id)
		    									->where(function($query) use ($datecon) {
							                        if(!empty($datecon->month)){
							                            $query->whereMonth('a.expire_date', '=', $datecon->month);
							                            $query->whereYear('a.expire_date', '=', $datecon->year);
							                        }
							                        if(!empty($datecon->year) && empty($datecon->month)){
							                            $query->whereYear('a.expire_date', '=', $datecon->year);
							                        }
							                        if(!empty($datecon->from)){
							                            $query->whereBetween('a.expire_date', [$datecon->from,$datecon->to]);
							                        }

												})
												->where($condition)
												->orderBy('a.id', 'DESC')
												->distinct('a.cm_sales_contract_id')
												->where($condition)
												->select([
													DB::raw('COUNT(a.id) as total_sc'),
													DB::raw('SUM(sc.initial_value) as total_value')
												])
												->get();
		    									// ->count('a.id');

		    		$unit->total_sc = $unit_wise_total_sc;	
    		}
    	}							
    	// dd($request1, $datecon, $total_units);exit;

    	$result['page'] = view('commercial.query.salescontract.unit_wise_salescontract',
            compact('showTitle','request1','total_units'))->render();
        $result['url'] = url('commercial/query?').http_build_query($request1);
        return $result;

    }



    //Buyer Wise Fetching
    public function salescontractBuyers(Request $request){
    	$parts = parse_url(url()->previous());
    	// dd($parts);exit;
        parse_str($parts['query'], $request1);
        $datecon = $this->getSearchType($request1);
        $condition = [];
        // return dd($request1);
        $request1['view']= 'allbuyers';

    	$showTitle = $this->pageTitle($request1);


    	// $total_buyers  = DB::table('cm_sales_contract as b')->distinct('b.mr_buyer_b_id')
    	// 							->leftJoin('mr_buyer as c', 'c.b_id', 'b.mr_buyer_b_id')
    	// 							->select(['c.b_name','b.mr_buyer_b_id'])
    	// 							->get();
    	// dd($request, $total_units);exit;
    	$total_buyers  = DB::table('mr_buyer')
    								->select(['b_name','b_id as mr_buyer_b_id'])
    								->get();

    	if($request1['scSearchBy'] == 'sc_created'){
	    	foreach ($total_buyers as $buyer) {
	    		$buyer_wise_total_sc = DB::table('cm_sales_contract as b')
	    									->where('b.mr_buyer_b_id', $buyer->mr_buyer_b_id)
	    									->where(function($query) use ($datecon) {
						                        if(!empty($datecon->month)){
						                            $query->whereMonth('b.created_at', '=', $datecon->month);
						                            $query->whereYear('b.created_at', '=', $datecon->year);
						                        }
						                        if(!empty($datecon->year) && empty($datecon->month)){
						                            $query->whereYear('b.created_at', '=', $datecon->year);
						                        }
						                        if(!empty($datecon->from)){
						                            $query->whereBetween('b.created_at', [$datecon->from,$datecon->to]);
						                        }

											})
											->where($condition)
											->select([
												DB::raw('COUNT(b.id) as total_sc'),
												DB::raw('SUM(b.initial_value) as total_value')
											])
											->get();
	    									// ->count('b.id');
	    		$buyer->total_sc = $buyer_wise_total_sc;
	    	}
    	}
    	else if($request1['scSearchBy'] == 'sc_expired'){
    		foreach ($total_buyers as $buyer) {
	    		$buyer_wise_total_sc = DB::table('cm_sales_contract_amend as a')
	    										->leftJoin('cm_sales_contract as sc', 'sc.id', 'a.cm_sales_contract_id')
												->where('sc.mr_buyer_b_id', $buyer->mr_buyer_b_id)
		    									->where(function($query) use ($datecon) {
							                        if(!empty($datecon->month)){
							                            $query->whereMonth('a.expire_date', '=', $datecon->month);
							                            $query->whereYear('a.expire_date', '=', $datecon->year);
							                        }
							                        if(!empty($datecon->year) && empty($datecon->month)){
							                            $query->whereYear('a.expire_date', '=', $datecon->year);
							                        }
							                        if(!empty($datecon->from)){
							                            $query->whereBetween('a.expire_date', [$datecon->from,$datecon->to]);
							                        }

												})
												->where($condition)
												->orderBy('a.id', 'DESC')
												->distinct('a.cm_sales_contract_id')
												->select([
													DB::raw('COUNT(a.id) as total_sc'),
													DB::raw('SUM(sc.initial_value) as total_value')
												])
												->get();
		    									// ->count('a.id');

		    		$buyer->total_sc = $buyer_wise_total_sc;	
    		}
    	}							
    	// dd($request1, $datecon, $total_buyers);exit;

    	$result['page'] = view('commercial.query.salescontract.buyer_wise_salescontract',
            compact('showTitle','request1','total_buyers'))->render();
        $result['url'] = url('commercial/query?').http_build_query($request1);
        return $result;
    }

    //List Page Call
    public function salescontractUnitBuyerWise(Request $request){
    	try{
	    	$parts = parse_url(url()->previous());
	        parse_str($parts['query'], $request1);

    		$showTitle = $this->pageTitle($request1);

    		$request1['unit_name'] = ''; 
			$request1['buyer_name'] = '';
	        
	        if(isset($request->unit_id)){
	        	$request1['unit']=$request->unit_id;
	        	$request1['unit_name']  = DB::table('hr_unit')->where('hr_unit_id',$request->unit_id)->value('hr_unit_short_name');
	        
	        }
	        if(isset($request->buyer_id)){
	        	$request1['buyer']=$request->buyer_id;
	        	$request1['buyer_name'] = DB::table('mr_buyer')->where('b_id', $request->buyer_id)->value('b_name');
	        }
	        
	    	$result['page'] = view('commercial.query.salescontract.salescontract_list',
            compact('showTitle','request1'))->render();
	        $result['url'] = url('commercial/query?').http_build_query($request1);
	        return $result;

    	}catch(\Exception $e){
    		return $e->getMessage();
    	}
    }

    public function salescontractList(Request $request){
    	try{
    		$parts = parse_url(url()->previous());
	        parse_str($parts['query'], $request1);
	        // dd($request->all());exit;
	        $request1['unit_id']  = $request->unit;
	        $request1['buyer_id'] = $request->buyer;

	       
	        // dd($request1);exit;

	        $datecon 	= $this->getSearchType($request1);
	    	
	    	//Fetching the data using unit_id
	    	if($request1['scSearchBy'] == 'sc_created'){
	    		$search_by  = 'sc_created';
	    	}
	    	else if($request1['scSearchBy'] == 'sc_expired'){
	    		$search_by  = 'sc_expired';	
	    	}
	    	$condition = [];
	    	if(isset($request1['unit_id'])){
	    		$condition['sc.hr_unit_id'] = $request1['unit_id'];
	    	}
	    	if(isset($request1['buyer_id'])){
	    		$condition['sc.mr_buyer_b_id'] = $request1['buyer_id'];
	    	}

    		//$condition  = ['sc.hr_unit_id'=> $request1['unit_id'], 'sc.mr_buyer_b_id'=> $request1['buyer_id'] ];
    		$scInfoList = $this->salescontractInfo($datecon, $search_by, $condition);
    		
    		// dd('Here',$request1, $scInfoList);exit;
    		return DataTables::of($scInfoList)
	    				->addIndexColumn()
	    				->make(true);
	    	


    	}catch(\Exception $e){
    		return $e->getMessage();
    	}
    }

    public function salesContractInfo($datecon,$search_by,$condition){
    	// dd($datecon, $search_by, $condition);
    	if($search_by == 'sc_created'){
    		$sales_contract_data = DB::table('cm_sales_contract as sc')
    								->select([
    									'sc.*',
    									// 'a.*',
    									// 'b.*',
    									'c.*',
    									'd.*',
    									'e.*',
    									'sc.id as sc_id'
    									])
									// ->leftJoin('cm_sales_contract_amend as a', 'a.cm_sales_contract_id', 'sc.id')
									// ->leftJoin('cm_sales_contract_order as b', 'b.cm_sales_contract_amend_id','a.id')
									->leftJoin('hr_unit as c', 'c.hr_unit_id', 'sc.hr_unit_id')
									->leftJoin('mr_buyer as d', 'd.b_id', 'sc.mr_buyer_b_id')
									->leftJoin('cm_bank as e', 'e.id', 'sc.btb_bank_id')
									->where(function($query) use ($datecon) {
					                        if(!empty($datecon->month)){
					                            $query->whereMonth('sc.created_at', '=', $datecon->month);
					                            $query->whereYear('sc.created_at', '=', $datecon->year);
					                        }
					                        if(!empty($datecon->year) && empty($datecon->month)){
					                            $query->whereYear('sc.created_at', '=', $datecon->year);
					                        }
					                        if(!empty($datecon->from)){
					                            $query->whereBetween('sc.created_at', [$datecon->from,$datecon->to]);
					                        }
										})
										->where($condition)
										->orderBy('sc.id', 'DESC')
										// ->groupBy('a.cm_sales_contract_id')
										->get();
			// dd($sales_contract_data);exit;
		}
		else if($search_by == 'sc_expired'){
			$sales_contract_data = DB::table('cm_sales_contract_amend as a')
    								->select([
    									'a.*',
    									'sc.*',
    									// 'b.*',
    									'c.*',
    									'd.*',
    									'e.*',
    									'sc.id as sc_id'
    									])
									->leftJoin('cm_sales_contract as sc', 'sc.id', 'a.cm_sales_contract_id')
									// ->leftJoin('cm_sales_contract_order as b', 'b.cm_sales_contract_amend_id','a.id')
									->leftJoin('hr_unit as c', 'c.hr_unit_id', 'sc.hr_unit_id')
									->leftJoin('mr_buyer as d', 'd.b_id', 'sc.mr_buyer_b_id')
									->leftJoin('cm_bank as e', 'e.id', 'sc.btb_bank_id')
									->where(function($query) use ($datecon) {
										if(!empty($datecon->month)){
				                            $query->whereMonth('a.expire_date', '=', $datecon->month);
				                            $query->whereYear('a.expire_date', '=', $datecon->year);
				                        }
				                        if(!empty($datecon->year) && empty($datecon->month)){
				                            $query->whereYear('a.expire_date', '=', $datecon->year);
				                        }
				                        if(!empty($datecon->from)){
				                            $query->whereBetween('a.expire_date', [$datecon->from,$datecon->to]);
				                        }
									})
									->where($condition)
									->orderBy('a.id', 'DESC')
									->distinct('a.cm_sales_contract_id')
									->get();

									// dd($sales_contract_data);exit;
		}

		return $sales_contract_data;

    }

    function printUnitSC(Request $request){
    	// dd($request);exit;
    	$total_units = $request->data;
        $title = $request->title;
        return view('commercial.query.salescontract.print_units_sc',compact('total_units','title'))->render();
    }
    function printBuyerSC(Request $request){
    	// dd($request);exit;
    	$total_buyers = $request->data;
        $title = $request->title;
        return view('commercial.query.salescontract.print_buyers_sc',compact('total_buyers','title'))->render();
    }
}
