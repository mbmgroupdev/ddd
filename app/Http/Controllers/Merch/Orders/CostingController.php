<?php

namespace App\Http\Controllers\Merch\Orders;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\BomCosting;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\OperationCost;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use App\Models\Merch\StyleSpecialMachine;
use App\Packages\QueryExtra\QueryExtra;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CostingController extends Controller
{
	public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function index()
	{

		$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
		$buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
		$brandList= Brand::pluck('br_name','br_id');
		$styleList= Style::pluck('stl_no', 'stl_id');
		$seasonList= Season::pluck('se_name', 'se_id');
		return view("merch.order_costing.order_costing_list", compact('buyerList', 'seasonList', 'unitList', 'brandList', 'styleList'));
	}

    //get List Data
	public function getListData()
	{
		if(auth()->user()->hasRole('merchandiser')){
			$lead_associateId[] = auth()->user()->associate_id;
		 	$team_members = DB::table('hr_as_basic_info as b')
				->where('associate_id',auth()->user()->associate_id)
				->leftJoin('mr_excecutive_team','b.as_id','mr_excecutive_team.team_lead_id')
				->leftJoin('mr_excecutive_team_members','mr_excecutive_team.id','mr_excecutive_team_members.mr_excecutive_team_id')
				->pluck('member_id');
			$team_members_associateId = DB::table('hr_as_basic_info as b')
									->whereIn('as_id',$team_members)
									->pluck('associate_id');
		 	$team = array_merge($team_members_associateId->toArray(), $lead_associateId);
		 
	 	}elseif (auth()->user()->hasRole('merchandising_executive')) {
			$executive_associateId[] = auth()->user()->associate_id;
			$team = $executive_associateId;
		}else{
		 	$team =[];
		}
		
		$getBuyer = buyer_by_id();
		$getSeason = season_by_id();
		$getBrand = brand_by_id();
		$query= DB::table('mr_order_entry AS OE')
		->select([
			"OE.order_id",
			"OE.order_code",
			"stl.stl_no",
			"stl.stl_year",
			"stl.mr_season_se_id",
			"stl.mr_brand_br_id",
			"OE.order_ref_no",
			"OE.mr_buyer_b_id",
			"OE.order_qty",
			"OE.order_delivery_date",
			"OE.unit_id",
			"OE.order_status"
		])
		->whereIn('OE.mr_buyer_b_id', auth()->user()->buyer_permissions())
		->leftJoin('mr_style AS stl', 'stl.stl_id', "OE.mr_style_stl_id");
		if(!empty($team)){
			$query->whereIn('OE.created_by', $team);
		}
		$data = $query->orderBy('OE.order_id', 'DESC')
		->get();
		$getUnit = unit_by_id();

		return DataTables::of($data)->addIndexColumn()
		->addIndexColumn()
		->editColumn('hr_unit_name', function($data) use ($getUnit){
			return $getUnit[$data->unit_id]['hr_unit_name']??'';
		})
		->editColumn('b_name', function($data) use ($getBuyer){
			return $getBuyer[$data->mr_buyer_b_id]->b_name??'';
		})
		->editColumn('br_name', function($data) use ($getBrand){
			return $getBrand[$data->mr_brand_br_id]->br_name??'';
		})
		->editColumn('se_name', function($data) use ($getSeason){
			return $getSeason[$data->mr_season_se_id]->se_name??''. '-'.$data->stl_year;
		})
		->editColumn('order_delivery_date', function($data){
			return custom_date_format($data->order_delivery_date);
		})
		->addColumn('action', function ($data) {
			if(empty($data->bom_term)){
				$action_buttons= "<div class=\"btn-group\">
				<a href=".url('merch/order/costing/'.$data->order_id)." class=\"btn btn-xs btn-primary btn-round\" data-toggle=\"tooltip\" title=\"Add Costing\">
				<i class=\"ace-icon fa fa-plus bigger-120\"></i>
				</a>";

				$action_buttons.= "</div>";
				return $action_buttons;
			}
			else{
				$action_buttons= "<div class=\"btn-group\">
				<a href=".url('merch/order/costing/'.$data->order_id)." class=\"btn btn-xs btn-success btn-round\" data-toggle=\"tooltip\" title=\"Edit Costing\">
				<i class=\"ace-icon fa fa-pencil bigger-120\"></i>
				</a>";
				$action_buttons.= "</div>";
				return $action_buttons;
			}
		})
		->editColumn('order_delivery_date', function($data){
			return custom_date_format($data->order_delivery_date);
		})
		->editColumn('order_status', function($data){
			if($data->order_status == "Costed")
			{
				return "<button class=\"btn btn-xs btn-success btn-round\" rel='tooltip' data-tooltip=\"Approved\" data-tooltip-location='left'>Approved</button>";
			}
			else if($data->order_status == "Active")
			{
				return "<button class=\"btn btn-xs btn-primary btn-round\" rel='tooltip' data-tooltip=\"Active\" data-tooltip-location='left'>Active</button>";
			}
			else if($data->order_status == "Approval Pending")
			{
				$approvalLevel = DB::table('mr_order_costing_approval')
	    			->leftJoin('users','mr_order_costing_approval.submit_to','users.associate_id')
	    			->where('mr_order_bom_n_costing_id',$data->order_id)
	    			->where('status',1)
	    			->first();

					return "<button class=\"btn btn-xs btn-danger btn-round\" rel='tooltip' data-tooltip=\"In Level-$approvalLevel->level To $approvalLevel->name\" data-tooltip-location='left' >
    				Pending</button>";
			}
			else return $data->order_status;
		})
		->rawColumns([
            'order_code', 'hr_unit_name', 'b_name', 'br_name', 'se_name', 'stl_no', 'order_qty', 'order_delivery_date', 'order_status', 'action'
        ])
        ->make(true);
	}

    public function show(Request $request, $id)
    {
    	try {
    		$queryData = OrderEntry::with(['style'])
	        	->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions());

			$order = $queryData->where("order_id", $id)->first();
    		
			if($order == null){
				toastr()->error("Order Not Found!");
				return back();
			}

			$getBom = OrderBOM::getOrderIdWiseOrderBOM($id);
			$getBOMCollect = collect($getBom->toArray())->pluck('bom_term')->toArray();
			$bomCosting = 1;
			// get style info
			$styleCosting = BomCosting::getStyleWiseItemUnitPrice($order->mr_style_stl_id);
			$styleCosting = collect($styleCosting->toArray())->keyBy('id')->toArray();
			$styleSpOperation = OperationCost::getStyleIdWiseOperationInfo($order->mr_style_stl_id, 2);
			$styleSpOperation = collect($styleSpOperation->toArray())->keyBy('style_op_id')->toArray();
			$styleOtherCosting = BomOtherCosting::getStyleIdWiseStyleOtherCosting($order->mr_style_stl_id);

			$checkCosting = array_filter($getBOMCollect);
			if(count($checkCosting) == 0){
				$bomCosting = 0;
				$specialOperation = $styleSpOperation;
				$otherCosting = $styleOtherCosting;
			}else{
				$specialOperation = OrderOperationNCost::getOrderIdWiseOperationInfo($id, 2);
				$otherCosting = OrderBomOtherCosting::getOrderIdWiseOrderOtherCosting($id);
			}

			// dd($styleSpOperation);
			$groupBom = collect($getBom->toArray())->groupBy('mcat_id',true);
			
			$samples = SampleStyle::getStyleIdWiseSampleName($order->mr_style_stl_id);
		    $operations = OperationCost::getStyleIdWiseOperationCostName($order->mr_style_stl_id);
		    $machines = StyleSpecialMachine::getStyleIdWiseSpMachineName($order->mr_style_stl_id);
		    // cache data
		    $getUnit = unit_by_id();
		    $getSupplier = supplier_by_id();
        	$getArticle = article_by_id();
        	$getItem = item_by_id();
		    $getColor = material_color_by_id();
		    $itemCategory = item_category_by_id();
		    $getBuyer = buyer_by_id();
		    $uom = uom_by_id();
			$uom = collect($uom)->pluck('measurement_name','id');

		    return view('merch.order_costing.index', compact('order', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupBom', 'getArticle', 'getSupplier', 'getItem', 'specialOperation', 'otherCosting', 'getBuyer', 'getUnit', 'styleCosting', 'bomCosting', 'styleSpOperation', 'styleOtherCosting'));
			
		} catch (\Exception $e) {
			$bug = $e->getMessage();
		    toastr()->error($bug);
		    return back();
		}
    }

    public function ajaxStore(Request $request)
    {
    	$input = $request->all();
    	$data['type'] = 'error';
    	// return $input;
    	DB::beginTransaction();
    	try {
    		// BOM costing update
    		$updateCosting = [];
    		for ($i=0; $i < sizeof($input['itemid']); $i++){
    			$itemId = $input['itemid'][$i];
            	if($itemId != null){
            		$term = "C&F";
        			if($input['precost_fob'][$i] > 0 || $input['precost_lc'][$i] > 0 || $input['precost_freight'][$i] > 0){
        				$term = "FOB";
        			}

            		$bom = [
            			'bom_term' => $term,
            			'precost_fob' => $input['precost_fob'][$i],
            			'precost_lc' => $input['precost_lc'][$i],
            			'precost_freight' => $input['precost_freight'][$i],
            			'precost_unit_price' => $input['precost_unit_price'][$i]
            		];

            		$updateCosting[] = 
                    [
                        'data' => $bom,
                        'keyval' => $input['bomitemid'][$i]
                    ];
            		// OrderBOM::where('id', $input['bomitemid'][$i])->update($bom);
            	}
            }

            // update mr_order_bom_costing_booking
            if(count($updateCosting) > 0){
                (new QueryExtra)
                ->table('mr_order_bom_costing_booking')
                ->whereKey('id')
                ->bulkup($updateCosting);
            }
            
            // mr_order_operation_n_cost - update
            if(isset($input['order_op_id'])){
            	$updateOpCost = [];
            	for ($s=0; $s < sizeof($input['order_op_id']); $s++) {
					
					// OrderOperationNCost::updateOrCreate(
					// [
					// 	"order_op_id"             => $request->order_op_id[$s],
					// 	"mr_style_stl_id"         => $request->stl_id,
					// 	"mr_order_entry_order_id" => $request->order_id
					// ],
					// [
					// 	"mr_operation_opr_id" => $request->mr_operation_opr_id[$s],
					// 	"opr_type" 		      => $request->opr_type[$s],
					// 	"uom"                 => $request->spuom[$s],
					// 	"unit_price" 	      => $request->spunitprice[$s]
					// ]);

					$spItem = [
						"opr_type" 	 => $request->opr_type[$s],
						"uom"        => $request->spuom[$s],
						"unit_price" => $request->spunitprice[$s]
					];

					$updateOpCost[] = 
                    [
                        'data' => $spItem,
                        'keyval' => $request->order_op_id[$s]
                    ];
					// $this->logFileWrite("Style Operation updated", $request->style_op_id[$s]);
				}

				// update mr_order_operation_n_cost
                if(count($updateOpCost) > 0){
                    (new QueryExtra)
                    ->table('mr_order_operation_n_cost')
                    ->whereKey('order_op_id')
                    ->bulkup($updateOpCost);
                }


            }
            
			// mr_stl_bom_other_costing - insert
			OrderBomOtherCosting::updateOrCreate(
			[
				"mr_style_stl_id" => $request->stl_id,
				"mr_order_entry_order_id" => $request->order_id
			],
			[
				"cm"           	  => $request->cm,
				"net_fob" 		  => $request->net_fob,
				"agent_fob"       => $request->agent_fob,
				"buyer_fob" 	  => $request->buyer_fob,
				"testing_cost" 	  => $request->testing_cost,
				"commercial_cost" => $request->commercial_cost,
				"buyer_comission_percent" => $request->buyer_comission_percent,
				"agent_comission_percent" => $request->agent_comission_percent
			]);

            //log_file_write("Costing Successfully Save", $input['stl_id']);
            DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = "Costing Successfully Save.";
	        return response()->json($data);
    	} catch (\Exception $e) {
    		DB::rollback();
    		$bug = $e->getMessage();
	        $data['message'] = $bug;
	        return response()->json($data);
    	}
    }
}
