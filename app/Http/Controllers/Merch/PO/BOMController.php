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

    public function ajaxStore(Request $request)
    {
    	$input = $request->all();
    	$data['type'] = 'error';
    	$data['value'] = [];
    	// return $input;
    	DB::beginTransaction();
    	try {
    		// check order exists
    		$oldItem = array_filter($input['bomitemid'], 'strlen');
	    	$getItemBom = PoBOM::getPoWiseItem($input['po_id'], ['id']);
	    	$itemBomCount = count($getItemBom);
	    	// return $itemBomCount;
	    	$getBomId = collect($getItemBom->toArray())->pluck('id')->toArray();
	    	$itemDiff = array_diff($getBomId, $oldItem);

	    	for ($d=0; $d < count($itemDiff); $d++) { 
	    		PoBOM::whereIn('id', $itemDiff)->delete();
	    	}

    		$sl = 1;
    		for ($i=0; $i<sizeof($input['itemid']); $i++){
    			$itemId = $input['itemid'][$i];
            	if($itemId != null){
            		$bom = [
            			'mr_style_stl_id' => $input['stl_id'],
            			'mr_material_category_mcat_id' => $input['itemcatid'][$i],
            			'mr_cat_item_id' => $itemId,
            			'item_description' => $input['description'][$i],
            			'clr_id' => $input['color'][$i],
            			'size' => $input['size_width'][$i],
            			'mr_supplier_sup_id' => $input['supplierid'][$i],
            			'mr_article_id' => $input['articleid'][$i],
            			'uom' => $input['uomname'][$i],
            			'consumption' => $input['consumption'][$i],
            			'extra_percent' => $input['extraper'][$i],
            			'order_id' => $input['order_id'],
            			'depends_on' => $input['depends_on'][$i],
            			'sl' => $sl,
            			'ord_bom_id' => $input['ord_bom_id'][$i],
            			'po_id' => $input['po_id']
            		];
            		if($input['bomitemid'][$i] != null && $itemBomCount > 0){ 
            			// update
            			PoBOM::where('id', $input['bomitemid'][$i])->update($bom);
            		}else{
            			// create
            			$bom['created_by'] = auth()->user()->id;
            			$bomId = PoBOM::create($bom)->id;
            			$data['value'][$i] = $bomId;
            		}
            		
            		$sl++;
            	}
	
            }

            //log_file_write("BOM Successfully Save", $input['stl_id']);
            DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = "BOM Successfully Save.";
	        return response()->json($data);
    	} catch (\Exception $e) {
    		DB::rollback();
    		$bug = $e->getMessage();
	        $data['message'] = $bug;
	        return response()->json($data);
    	}
    }
}
