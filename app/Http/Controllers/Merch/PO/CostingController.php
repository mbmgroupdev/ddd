<?php

namespace App\Http\Controllers\Merch\PO;

use App\Http\Controllers\Controller;
use App\Models\Merch\MrPoBomOtherCosting;
use App\Models\Merch\MrPoOperationNCost;
use App\Models\Merch\OperationCost;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\PoBOM;
use App\Models\Merch\PurchaseOrder;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\StyleSpecialMachine;
use Illuminate\Http\Request;
use DB;

class CostingController extends Controller
{
	public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function show(Request $request, $id)
    {
    	try {
    		$po = PurchaseOrder::findOrFail($id);
			if($po == null){
				toastr()->error("PO Not Found!");
				return back();
			}
    		$orderId = $po->mr_order_entry_order_id;
			$order = OrderEntry::orderInfoWithStyle($orderId);
    		
			if($order == null){
				toastr()->error("Order Not Found!");
				return back();
			}

			// PO BOM & Costing
			$getBom = PoBOM::getPoIdWisePoBOM($id);
			$groupBom = collect($getBom->toArray())->groupBy('mcat_id',true);

			$specialOperation = MrPoOperationNCost::getPoIdWiseOperationInfo($id, 2);
			$otherCosting = MrPoBomOtherCosting::getOpIdWisePoOtherCosting($id);
			
			// order costing info
			$orderCosting = OrderBOM::getOrderIdWiseOrderBOM($orderId);
			$orderCosting = collect($orderCosting->toArray())->keyBy('id')->toArray();

			$ordSPOperation = OrderOperationNCost::getOrderIdWiseOperationInfo($orderId, 2);
			$ordOthCosting = OrderBomOtherCosting::getOrderIdWiseOrderOtherCosting($orderId);
			// other operation
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

		    return view('merch.po.costing', compact('po','order', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupBom', 'getArticle', 'getSupplier', 'getItem', 'specialOperation', 'otherCosting', 'getBuyer', 'getUnit', 'orderCosting', 'ordSPOperation', 'ordOthCosting'));
			
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
            		PoBOM::where('id', $input['bomitemid'][$i])->update($bom);
            	}
            }
            
            // mr_po_operation_n_cost - update
            if(isset($input['order_op_id'])){
            	for ($s=0; $s < sizeof($input['order_op_id']); $s++) {
					
					MrPoOperationNCost::updateOrCreate(
					[
						"id"                      => $request->op_id[$s],
						"mr_order_entry_order_id" => $request->order_id,
						"po_id"                   => $request->po_id
					],
					[
						"mr_operation_opr_id" => $request->mr_operation_opr_id[$s],
						"opr_type" 		      => $request->opr_type[$s],
						"uom"                 => $request->spuom[$s],
						"unit_price" 	      => $request->spunitprice[$s]
					]
				);

					// $this->logFileWrite("Style Operation updated", $request->style_op_id[$s]);
				}
            }
            
			// mr_po_bom_other_costing - insert or Update
			MrPoBomOtherCosting::updateOrCreate(
			[
				"po_id"                   => $request->po_id,
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

			// update purchase order
			PurchaseOrder::where('po_id', $input['po_id'])->update(['country_fob' => $request->agent_fob]);

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
