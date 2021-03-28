<?php

namespace App\Http\Controllers\Merch\PO;

use App\Http\Controllers\Controller;
use App\Models\Merch\Article;
use App\Models\Merch\CatItemUom;
use App\Models\Merch\McatItem;
use App\Models\Merch\OperationCost;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\PoBOM;
use App\Models\Merch\PurchaseOrder;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\StyleSpecialMachine;
use App\Models\Merch\Supplier;
use App\Models\Merch\SupplierItemType;
use DB;
use Illuminate\Http\Request;

class BOMController extends Controller
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
			$getBom = PoBOM::getPoIdWisePoBOM($id);
			$groupBom = collect($getBom->toArray())->groupBy('mcat_id',true);
			$getCat = array_keys($groupBom->toArray());
			// supplier
			$getSupplierCat = SupplierItemType::getSupplierItemTypeCatIdsWise($getCat);
			$getSupplier = collect($getSupplierCat)->groupBy('mcat_id',true);
			$getItemSupplier = array_column($getBom->toArray(), 'mr_supplier_sup_id');
	        $getItemSup = array_unique($getItemSupplier);
			// article
			$getArticle = Article::getArticleSupplierIdsWise($getItemSup);
			$getArticle = collect($getArticle->toArray())->groupBy('mr_supplier_sup_id',true);

        	// item 
        	$itemsId = array_column($getBom->toArray(), 'mr_cat_item_id');
        	$getItems = McatItem::getItemListItemIdsWise($itemsId);
        	$getItems = collect($getItems->toArray())->keyBy('id');
     		// item UOM
        	$getItemUom = CatItemUom::getItemWithUomItemIdWise($itemsId);
        	$getItemUom = collect($getItemUom->toArray())->groupBy('id', true);

        	$uom = uom_by_id();
        	// item wise UOM
        	$getItems = collect($getItems)->map(function($item) use ($getItemUom, $uom){
        		if(isset($getItemUom[$item->id])){
        			$itemUom  = collect($getItemUom[$item->id])->pluck('text', 'id');
        			$item->uom = $itemUom;
        		}else{
        			$item->uom = collect($uom)->pluck('measurement_name','id');
        		}
        		return $item;
        	});

			$samples = SampleStyle::getStyleIdWiseSampleName($order->mr_style_stl_id);
		    $operations = OperationCost::getStyleIdWiseOperationCostName($order->mr_style_stl_id);
		    $machines = StyleSpecialMachine::getStyleIdWiseSpMachineName($order->mr_style_stl_id);
		    // cache data
		    $getUnit = unit_by_id();
        	$getItem = item_by_id();
		    $getColor = material_color_by_id();
		    $getColor = collect($getColor)->map(function($q){
		    	$p =  (object)[];
		    	$p->id = $q->clr_id;
		    	$p->text = $q->clr_name;
		    	return $p;
		    });

		    $itemCategory = item_category_by_id();
		    $getBuyer = buyer_by_id();
		    
		    return view('merch.po.bom', compact('po','order', 'samples', 'operations', 'machines',  'groupBom', 'getItems', 'getUnit', 'getSupplier', 'getArticle', 'getItem', 'getColor', 'itemCategory', 'getBuyer'));
			
		} catch (\Exception $e) {
			$bug = $e->getMessage();
		    toastr()->error($bug);
		    return back();
		}
    }
}
