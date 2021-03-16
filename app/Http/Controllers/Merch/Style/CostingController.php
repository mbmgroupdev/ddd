<?php

namespace App\Http\Controllers\Merch\Style;

use App\Http\Controllers\Controller;
use App\Models\Merch\BomCosting;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\OperationCost;
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
    		$style = Style::getStyleIdWiseStyleInfo($id, ['stl_id', 'mr_buyer_b_id', 'stl_type', 'stl_no', 'stl_product_name', 'stl_description', 'stl_smv', 'stl_img_link', 'stl_status']);
	    	
			if($style == null){
				toastr()->error("Style Not Found!");
				return back();
			}

			$getStyleBom = BomCosting::getStyleIdWiseStyleBOM($id);
			$groupStyleBom = collect($getStyleBom->toArray())->groupBy('mcat_id',true);
			$specialOperation = OperationCost::getStyleIdWiseOperationInfo($id, 2);
			$otherCosting = BomOtherCosting::getStyleIdWiseStyleOtherCosting($id);
			$samples = SampleStyle::getStyleIdWiseSampleName($id);
		    $operations = OperationCost::getStyleIdWiseOperationCostName($id);
		    $machines = StyleSpecialMachine::getStyleIdWiseSpMachineName($id);
		    // cache data
		    $getSupplier = supplier_by_id();
        	$getArticle = article_by_id();
        	$getItem = item_by_id();
		    $getColor = material_color_by_id();
		    $itemCategory = item_category_by_id();
		    $getBuyer = buyer_by_id();
		    $uom = uom_by_id();
			$uom = collect($uom)->pluck('measurement_name','id');

		    return view('merch.style_costing.index', compact('style', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupStyleBom', 'getArticle', 'getSupplier', 'getItem', 'specialOperation', 'otherCosting', 'getBuyer'));
			
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
