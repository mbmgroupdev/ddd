<?php

namespace App\Http\Controllers\Merch\Orders;

use App\Http\Controllers\Controller;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\OrderBOM;
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
	        $queryData = OrderEntry::with(['style', 'season'])
	        	->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions());

			$order = $queryData->where("order_id", $id)->first();

			if($order != null){
				$uom = uom_by_id();
  				$uom = collect($uom)->pluck('measurement_name','id');
  				$getBom = DB::table('mr_order_bom_costing_booking AS b')
				->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'b.sl', 'b.depends_on', 'b.order_id', 'b.stl_bom_id')
				->where('b.order_id', $id)
				->orderBy('b.sl', 'asc')
				->get();

				if(count($getBom) == 0){
					$getBom = DB::table('mr_stl_bom_n_costing AS b')
					->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'b.sl', 'b.id AS stl_bom_id')
					->where('b.mr_style_stl_id', $order->mr_style_stl_id)
					->orderBy('b.sl', 'asc')
					->get();
				}
				// return $getBom;
				$getSupplier = array();
				$getArticle = array();
				$getItems = array();
				$groupBom = collect($getBom->toArray())->groupBy('mcat_id',true);

				if(count($getBom) > 0){
					// get Supplier
					$getCat = array_keys($groupBom->toArray());
					$supplierData = DB::table('mr_supplier');
					$supplierDataSql = $supplierData->toSql();
					$getSupplier = DB::table('mr_supplier_item_type AS si')
					->select('s.sup_id', 's.sup_name', 'si.mcat_id')
					->leftjoin(DB::raw('(' . $supplierDataSql. ') AS s'), function($join) use ($supplierData) {
		                $join->on('s.sup_id','si.mr_supplier_sup_id')->addBinding($supplierData->getBindings());
		            })
	            	->whereIn('si.mcat_id', $getCat)
	            	->get()
	            	->groupBy('mcat_id',true)
	            	->toArray();
	            	
	            	$getItemSupplier = array_column($getBom->toArray(), 'mr_supplier_sup_id');
	            	$getItemSup = array_unique($getItemSupplier);
	            	// get Article
	            	$getArticle = DB::table('mr_article')
	            	->select('id', 'art_name', 'mr_supplier_sup_id')
	            	->whereIn('mr_supplier_sup_id', $getItemSup)
	            	->get()
	            	->groupBy('mr_supplier_sup_id',true)
	            	->toArray();

	            	// item 
	            	$itemsId = array_column($getBom->toArray(), 'mr_cat_item_id');
	            	$getItems = DB::table('mr_cat_item AS i')
		            ->select('i.id','i.item_name','i.item_code', 'i.dependent_on')
		            ->whereIn('i.id', $itemsId)
		            ->get()
		            ->keyBy('id')
		            ->toArray();

	            	$uomData = DB::table('uom');
            		$uomData_sql = $uomData->toSql();
	            	
	            	$getItemUom = DB::table('mr_cat_item_uom AS iu')
	            	->select('u.id AS id', 'u.measurement_name AS text','iu.mr_cat_item_id')
	            	->whereIn('iu.mr_cat_item_id', $itemsId)
	            	->leftjoin(DB::raw('(' . $uomData_sql. ') AS u'), function($join) use ($uomData) {
		                $join->on('iu.uom_id','u.id')->addBinding($uomData->getBindings());
		            })
	            	->get()
	            	->groupBy('mr_cat_item_id',true)
	            	->toArray();
	            	// return $getItemUom;
	            	foreach ($getItems as $key => $item) {
	            		if(isset($getItemUom[$item->id])){
	            			$itemUom  = collect($getItemUom[$item->id])->pluck('text', 'id');
	            			$item->uom = $itemUom;
	            		}else{
	            			$item->uom = $uom;
	            		}
	            	}
				}
				// sample
				$samples = DB::table("mr_stl_sample AS ss")
			    	->select(DB::raw("GROUP_CONCAT(st.sample_name SEPARATOR ', ') AS name"))
			    	->leftJoin("mr_sample_type AS st", "st.sample_id", "ss.sample_id")
			    	->where("ss.stl_id", $order->mr_style_stl_id)
			    	->first();

		        //operations
			    $operations = DB::table("mr_style_operation_n_cost AS oc")
			    	->select("o.opr_name")
			    	->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
			    	->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
			    	->where("oc.mr_style_stl_id", $order->mr_style_stl_id)
			    	->first();

		        //machines
			    $machines = DB::table("mr_style_sp_machine AS sm")
			    	->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
			    	->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
			    	->where("sm.stl_id", $order->mr_style_stl_id)
			    	->first();
			    $getColor = DB::table("mr_material_color")->select('clr_id AS id', 'clr_name AS text')->get();
			    $itemCategory = item_category_by_id();
			    $getUnit = unit_by_id();
			    $getBuyer = buyer_by_id();
			    return view('merch.order_bom.index', compact('order', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupBom', 'getArticle', 'getSupplier', 'getItems', 'getUnit', 'getBuyer'));
			}
			toastr()->error("Order Not Found!");
			return back();
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
	    	$getItemBom = OrderBOM::getOrderWiseItem($input['order_id'], ['id']);
	    	$itemBomCount = count($getItemBom);
	    	$getBomId = collect($getItemBom->toArray())->pluck('id')->toArray();
	    	$itemDiff = array_diff($getBomId, $oldItem);
	    	// return $itemDiff;
	    	for ($d=0; $d < count($itemDiff); $d++) { 
	    		OrderBOM::whereIn('id', $itemDiff)->delete();
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
            			'stl_bom_id' => $input['stl_bom_id'][$i],
            		];
            		if($input['bomitemid'][$i] != null && $itemBomCount > 0){ 
            			// update
            			OrderBOM::where('id', $input['bomitemid'][$i])->update($bom);
            		}else{
            			// create
            			$bomId = OrderBOM::create($bom)->id;
            			$data['value'][$i] = $bomId;
            		}

            		// PO create or update before
            		
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
