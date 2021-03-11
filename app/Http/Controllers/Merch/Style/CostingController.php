<?php

namespace App\Http\Controllers\Merch\Style;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merch\BomCosting;
use App\Models\Merch\BomOtherCosting;
use DB;

class CostingController extends Controller
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
				$uom = uom_by_id();
  				$uom = collect($uom)->pluck('measurement_name','id');
				$getStyleBom = DB::table('mr_stl_bom_n_costing AS b')
				->select('b.id', 'b.mr_material_category_mcat_id AS mcat_id', 'b.mr_cat_item_id', 'b.item_description', 'b.clr_id', 'b.size', 'b.mr_supplier_sup_id', 'b.mr_article_id', 'b.uom', 'b.consumption', 'b.bom_term', 'b.precost_fob', 'b.precost_lc', 'b.precost_freight', 'b.precost_unit_price', 'b.extra_percent', DB::raw('(consumption/100)*extra_percent AS qty'), DB::raw('((consumption/100)*extra_percent)+consumption AS total'), 'b.sl')
				->where('b.mr_style_stl_id', $id)
				->orderBy('b.sl', 'asc')
				->get();
				// special operation
				$operationData = DB::table('mr_operation');
				$operationSql = $operationData->toSql();
				$specialOperation = DB::table("mr_style_operation_n_cost AS oc")
				->select("oc.*","o.opr_name")
				->leftjoin(DB::raw('(' . $operationSql. ') AS o'), function($join) use ($operationData) {
	                $join->on("oc.mr_operation_opr_id", "o.opr_id")->addBinding($operationData->getBindings());
	            })
				->where("oc.mr_style_stl_id", $id)
				->where("oc.opr_type", 2)
				->get();
				// other costing
				$otherCosting = DB::table('mr_stl_bom_other_costing')
				->where('mr_style_stl_id', $id)
				->first();

				$getSupplier = array();
				$getArticle = array();
				$getItems = array();
				$groupStyleBom = collect($getStyleBom->toArray())->groupBy('mcat_id',true);

				if(count($getStyleBom) > 0){
					// get Supplier
					
	            	$getItemSupplier = array_column($getStyleBom->toArray(), 'mr_supplier_sup_id');
	            	$getItemSup = array_unique($getItemSupplier);
	            	$getSupplier = DB::table('mr_supplier AS s')
					->select('s.sup_id', 's.sup_name')
	            	->whereIn('s.sup_id', $getItemSup)
	            	->get()
	            	->keyBy('sup_id',true)
	            	->toArray();
	            	// get Article
	            	$getArticle = DB::table('mr_article')
	            	->select('id', 'art_name')
	            	->whereIn('mr_supplier_sup_id', $getItemSup)
	            	->get()
	            	->keyBy('id',true)
	            	->toArray();
	            	// item 
	            	$itemsId = array_column($getStyleBom->toArray(), 'mr_cat_item_id');
	            	$getItems = DB::table('mr_cat_item AS i')
		            ->select('i.id','i.item_name','i.item_code')
		            ->whereIn('i.id', $itemsId)
		            ->get()
		            ->keyBy('id')
		            ->toArray();
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
			    $getColor = material_color_by_id();
			    $itemCategory = item_category_by_id();

			    return view('merch.style_costing.index', compact('style', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupStyleBom', 'getArticle', 'getSupplier', 'getItems', 'specialOperation', 'otherCosting'));
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