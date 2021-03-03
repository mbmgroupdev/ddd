<?php

namespace App\Http\Controllers\Merch\Style;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
				
			$style = $queryData->where("s.stl_id", $id)
				->first();
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
		    $getColor = DB::table("mr_material_color")->get();
		    $itemCategory = DB::table('mr_material_category')->get();
		    return view('merch.style_bom.create', compact('style', 'samples', 'operations', 'machines', 'getColor', 'itemCategory'));
		} catch (\Exception $e) {
			$bug = $e->getMessage();
		    toastr()->error($bug);
		    return back();
		}
    }
}
