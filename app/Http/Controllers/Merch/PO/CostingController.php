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
}
