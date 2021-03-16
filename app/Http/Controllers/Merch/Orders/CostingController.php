<?php

namespace App\Http\Controllers\Merch\Orders;

use App\Http\Controllers\Controller;
use App\Models\Merch\BomCosting;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\OperationCost;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\Style;
use App\Models\Merch\StyleSpecialMachine;
use DB;
use Illuminate\Http\Request;

class CostingController extends Controller
{
	public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function show(Request $request, $id)
    {
    	try {
    		$queryData = OrderEntry::with(['style', 'season'])
	        	->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions());

			$order = $queryData->where("order_id", $id)->first();
    		
			if($order == null){
				toastr()->error("Order Not Found!");
				return back();
			}

			$getBom = OrderBOM::getOrderIdWiseOrderBOM($id);
			$getBOMCollect = collect($getBom->toArray())->pluck('bom_term')->toArray();

			$checkCosting = array_filter($getBOMCollect);
			if(count($checkCosting) == 0){
				// $getBom = BomCosting::getStyleIdWiseStyleBOM($order->mr_style_stl_id);
				$specialOperation = OperationCost::getStyleIdWiseOperationInfo($order->mr_style_stl_id, 2);
				$otherCosting = BomOtherCosting::getStyleIdWiseStyleOtherCosting($order->mr_style_stl_id);
			}else{
				$specialOperation = OrderOperationNCost::getOrderIdWiseOperationInfo($id, 2);
				$otherCosting = OrderBomOtherCosting::getOrderIdWiseOrderOtherCosting($id);
				// $styleCosting = BomCosting::getStyleWiseItem($order->mr_style_stl_id, ['mr_style_stl_id', 'mr_cat_item_id', 'precost_unit_price']);
			}

			$styleCosting = BomCosting::getStyleWiseItem($order->mr_style_stl_id, ['mr_cat_item_id', 'precost_unit_price']);
			$styleCosting = collect($styleCosting->toArray())->pluck('precost_unit_price','mr_cat_item_id')->toArray();
			return $styleCosting;


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

		    return view('merch.order_costing.index', compact('order', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupBom', 'getArticle', 'getSupplier', 'getItem', 'specialOperation', 'otherCosting', 'getBuyer', 'getUnit'));
			
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
            		BomCosting::where('id', $input['bomitemid'][$i])->update($bom);
            	}
            }
            
            // mr_style_operation_n_cost - update
            if(isset($input['style_op_id'])){
            	for ($s=0; $s < sizeof($input['style_op_id']); $s++) {
					$spItem = [
						"style_op_id" => $request->style_op_id[$s],
						"uom"         => $request->spuom[$s],
						"unit_price"  => $request->spunitprice[$s]
					];
					DB::table("mr_style_operation_n_cost")
					->where("style_op_id", $request->style_op_id[$s])
					->update($spItem);

					// $this->logFileWrite("Style Operation updated", $request->style_op_id[$s]);
				}
            }
            
			// mr_stl_bom_other_costing - insert
			$otherCosting = BomOtherCosting::updateOrCreate(
				[
					"mr_style_stl_id" => $request->stl_id,
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

				]
			);

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
