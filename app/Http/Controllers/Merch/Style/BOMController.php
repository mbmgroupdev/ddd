<?php

namespace App\Http\Controllers\Merch\Style;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merch\BomCosting;
use DB;

class BOMController extends Controller
{
    public function show(Request $request, $id)
    {
		try {
			$buyerData = DB::table('mr_buyer');
	        $buyerDataSql = $buyerData->toSql();

	        $productTypeData = DB::table('mr_product_type');
	        $productTypeDataSql = $productTypeData->toSql();

	        $garmentTypeData = DB::table('mr_garment_type');
	        $garmentTypeDataSql = $garmentTypeData->toSql();

	        $seasonData = DB::table('mr_season');
	        $seasonDataSql = $seasonData->toSql();
	    	$queryData = DB::table("mr_style AS s")
	    		->select(
	    			"s.stl_id",
	    			"s.stl_type",
	    			"s.stl_no",
	    			"b.b_name",
	    			"t.prd_type_name",
	    			"g.gmt_name",
	    			"s.stl_product_name",
	    			"s.stl_description",
	    			"se.se_name",
	    			"s.stl_smv",
	    			"s.stl_img_link",
	    			"s.stl_addedby",
	    			"s.stl_added_on",
	    			"s.stl_updated_by",
	    			"s.stl_updated_on",
	    			"s.stl_status"
	    		)
	            ->whereIn('b.b_id', auth()->user()->buyer_permissions());
	            $queryData->leftjoin(DB::raw('(' . $buyerDataSql. ') AS b'), function($join) use ($buyerData) {
	                $join->on("b.b_id", "s.mr_buyer_b_id")->addBinding($buyerData->getBindings());
	            });
	            $queryData->leftjoin(DB::raw('(' . $productTypeDataSql. ') AS t'), function($join) use ($productTypeData) {
	                $join->on("t.prd_type_id", "s.prd_type_id")->addBinding($productTypeData->getBindings());
	            });
	            $queryData->leftjoin(DB::raw('(' . $garmentTypeDataSql. ') AS g'), function($join) use ($garmentTypeData) {
	                $join->on("g.gmt_id", "s.gmt_id")->addBinding($garmentTypeData->getBindings());
	            });
	            $queryData->leftjoin(DB::raw('(' . $seasonDataSql. ') AS se'), function($join) use ($seasonData) {
	                $join->on("se.se_id", "s.mr_season_se_id")->addBinding($seasonData->getBindings());
	            });
				
			$style = $queryData->where("s.stl_id", $id)->first();
			
			if($style != null){
				$uom= DB::table('uom')->pluck('measurement_name','id');

				$getStyleBom = DB::table('mr_stl_bom_n_costing AS b')
				->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'b.sl')
				->where('b.mr_style_stl_id', $id)
				->orderBy('b.sl', 'asc')
				->get();
				$getSupplier = array();
				$getArticle = array();
				$getItems = array();
				$groupStyleBom = collect($getStyleBom->toArray())->groupBy('mcat_id',true);

				if(count($getStyleBom) > 0){
					// get Supplier
					$getCat = array_keys($groupStyleBom->toArray());
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
	            	
	            	$getItemSupplier = array_column($getStyleBom->toArray(), 'mr_supplier_sup_id');
	            	$getItemSup = array_unique($getItemSupplier);
	            	// get Article
	            	$getArticle = DB::table('mr_article')
	            	->select('id', 'art_name', 'mr_supplier_sup_id')
	            	->whereIn('mr_supplier_sup_id', $getItemSup)
	            	->get()
	            	->groupBy('mr_supplier_sup_id',true)
	            	->toArray();

	            	// item 
	            	$itemsId = array_column($getStyleBom->toArray(), 'mr_cat_item_id');
	            	$getItems = DB::table('mr_cat_item AS i')
		            ->select('i.id','i.item_name','i.item_code')
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
			    	->where("ss.stl_id", $id)
			    	->first();

		        //operations
			    $operations = DB::table("mr_style_operation_n_cost AS oc")
			    	->select("o.opr_name")
			    	->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
			    	->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
			    	->where("oc.mr_style_stl_id", $id)
			    	->first();

		        //machines
			    $machines = DB::table("mr_style_sp_machine AS sm")
			    	->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
			    	->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
			    	->where("sm.stl_id", $id)
			    	->first();
			    $getColor = DB::table("mr_material_color")->select('clr_id AS id', 'clr_name AS text')->get();
			    $itemCategory = DB::table('mr_material_category')->get();
			    
			    return view('merch.style_bom.index', compact('style', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupStyleBom', 'getArticle', 'getSupplier', 'getItems'));
			}
			toastr()->error("Style Not Found!");
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
    	// return $input;
    	try {
    		$sl = 1;
    		for ($i=0; $i<sizeof($input['itemid']); $i++){
    			$itemId = $input['itemid'][$i];
            	if($itemId != null){
            		BomCosting::updateOrCreate([
            			'mr_style_stl_id' => $input['stl_id'],
            			'mr_material_category_mcat_id' => $input['itemcatid'][$i],
            		],[
            			'mr_cat_item_id' => $itemId,
            			'item_description' => $input['description'][$i],
            			'clr_id' => $input['color'][$i],
            			'size' => $input['size_width'][$i],
            			'mr_supplier_sup_id' => $input['supplierid'][$i],
            			'mr_article_id' => $input['articleid'][$i],
            			'uom' => $input['uomname'][$i],
            			'consumption' => $input['consumption'][$i],
            			'extra_percent' => $input['extraper'][$i],
            			'sl' => $sl,
            		]);
            		$sl++;
            	}
	
            }

            //log_file_write("BOM Successfully Save", $input['stl_id']);

	        $data['type'] = 'success';
	        $data['message'] = "BOM Successfully Save.";
	        return response()->json($data);
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
	        $data['message'] = $bug;
	        return response()->json($data);
    	}
    }
}
