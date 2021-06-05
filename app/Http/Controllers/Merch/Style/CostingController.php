<?php

namespace App\Http\Controllers\Merch\Style;

use App\Http\Controllers\Controller;
use App\Models\Merch\BomCosting;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\Buyer;
use App\Models\Merch\OperationCost;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use App\Models\Merch\StyleSpecialMachine;
use App\Packages\QueryExtra\QueryExtra;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CostingController extends Controller
{
	public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function index()
    {
    	$buyerList  = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())
    	->pluck('b_name', 'b_id');
    	$seasonList = Season::pluck('se_name','se_id');
    	return view("merch.style_costing.style_costing_list", compact(
    		'buyerList',
    		'seasonList'
    	));
    }

    public function getListData(Request $request)
    {
    	$data = DB::table("mr_stl_bom_n_costing AS sb")
    		->select(
    		"s.stl_id",
    		"sb.mr_style_stl_id",
    		"sb.bom_term",
    		"s.stl_type",
    		"s.stl_no",
    		"b.b_name",
        	"br.br_name",
    		"t.prd_type_name",
    		"g.gmt_name",
    		"s.stl_product_name",
    		"s.stl_description",
    		"se.se_name",
    		"s.stl_smv",
    		"s.stl_img_link",
    		"s.stl_status"
    	)
    	->leftJoin("mr_style AS s", "s.stl_id", "=",  "sb.mr_style_stl_id")
    	->leftJoin("mr_buyer AS b", "b.b_id", "=", "s.mr_buyer_b_id")
    	->whereIn('b.b_id', auth()->user()->buyer_permissions())
    	->leftJoin("mr_product_type AS t", "t.prd_type_id", "=", "s.prd_type_id")
    	->leftJoin("mr_garment_type AS g", "g.gmt_id", "=", "s.gmt_id")
    	->leftJoin("mr_season AS se", "se.se_id", "=", "s.mr_season_se_id")
      	->leftJoin("mr_brand AS br", "br.br_id", "=", "s.mr_brand_br_id")
    	->groupBy("s.stl_id")
    	->orderBy('s.stl_id', 'desc')
    	->get();

    	$approvalLevel = DB::table('mr_stl_costing_approval')
		->leftJoin('users','mr_stl_costing_approval.submit_to','users.associate_id')
		->where('status',1)
		->get()
		->groupBy('mr_style_stl_id', true)
		->toArray();
		//dd($data);exit;
    	return DataTables::of($data)
    	->addIndexColumn()
    	->editColumn('stl_type', function ($data) {
    		if ($data->stl_type == "Bulk")
    		{
    			return "<span class='text-primary'>$data->stl_type</span>";
    		}
    		else
    		{
    			return "<span class='text-warning'>$data->stl_type</span>";
    		}
    	})
    	->editColumn('stl_status', function ($data) {
    		if ($data->stl_status == "0")
    		{
    			return '<span class="badge badge-pill badge-primary">Created</span>';
    		}
    		else if($data->stl_status == "1")
    		{
    			if(isset($approvalLevel[$data->mr_style_stl_id]) && $approvalLevel[$data->mr_style_stl_id] != null){
    				$appro = $approvalLevel[$data->mr_style_stl_id];
    				return "<span class=\"badge badge-pill badge-danger\" rel='tooltip' data-tooltip=\"In Level-$appro->level To $appro->name\" data-tooltip-location='top' >
    				Pending</span>";
    			}else{
    				return "<span class=\"badge badge-pill badge-danger\" rel='tooltip' data-tooltip=\"In Level-Unknown To Unknown\" data-tooltip-location='top' >
    				Pending</span>";
    			}
    		}else if($data->stl_status == "2")
    		{
    			return '<span class="badge badge-pill badge-success">Approved</span>';
    		}
    	})
    	->editColumn('action', function ($data) {
    		$return = "<div class=\"btn-group\">";
    		if (empty($data->bom_term))
    		{
    			// $return .= "<a href=".url('merch/style_costing/'.$data->stl_id.'/create')." class=\"btn btn-sm btn-warning\" data-toggle=\"tooltip\" title=\"Pre-Costing$data->bom_term\"><i class='las la-donate'></i></a>";
    			$return .= "<a href=".url('merch/style/costing/'.$data->stl_id)." class=\"btn btn-sm btn-warning\" data-toggle=\"tooltip\" title=\"Pre-Costing$data->bom_term\"><i class='las la-donate'></i></a>";
    		}
    		else
    		{
    			$return .= "<a href=".url('merch/style/costing/'.$data->stl_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Edit Costing\">
    			<i class=\"ace-icon fa fa-pencil bigger-120\"></i>
    			</a>";
    		}
    		$return .= "</div>";
    		return $return;
    	})
    	->rawColumns([
    		'stl_type', 'stl_no', 'b_name', 'br_name', 'stl_product_name', 'stl_smv', 'se_name', 'stl_status', 'action'
    	])
    	->make(true);
    }

    public function show(Request $request, $id)
    {
    	try {
    		$style = Style::getStyleIdWiseStyleInfo($id, ['stl_id', 'mr_buyer_b_id', 'stl_type', 'stl_no', 'stl_product_name', 'stl_description', 'stl_smv', 'stl_img_link', 'stl_status','bom_status' , 'costing_status']);

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
            $updateCosting = [];
    		for ($i=0; $i < sizeof($input['itemid']); $i++){
    			$itemId = $input['itemid'][$i];
            	if($itemId != null){
/*            		$term = "C&F";
        			if($input['precost_fob'][$i] > 0 || $input['precost_lc'][$i] > 0 || $input['precost_freight'][$i] > 0){
        				$term = "FOB";
        			}*/

            		$bom = [
            			'bom_term' =>$input['terms'][$i] ,
            			'precost_fob' => $input['precost_fob'][$i],
            			'precost_lc' => $input['precost_lc'][$i],
            			'precost_freight' => $input['precost_freight'][$i],
            			'precost_unit_price' => $input['precost_unit_price'][$i]
            		];

                    $updateCosting[] =
                    [
                        'data' => $bom,
                        'keyval' => $input['bomitemid'][$i]
                    ];
            		// BomCosting::where('id', $input['bomitemid'][$i])->update($bom);
            	}
            }

            // update mr_stl_bom_n_costing
            if(count($updateCosting) > 0){
                (new QueryExtra)
                ->table('mr_stl_bom_n_costing')
                ->whereKey('id')
                ->bulkup($updateCosting);
            }

            // mr_style_operation_n_cost - update
            if(isset($input['style_op_id'])){
                $updateOpCost = [];
            	for ($s=0; $s < sizeof($input['style_op_id']); $s++) {
					$spItem = [
						"style_op_id" => $request->style_op_id[$s],
						"uom"         => $request->spuom[$s],
						"unit_price"  => $request->spunitprice[$s]
					];

					// DB::table("mr_style_operation_n_cost")
					// ->where("style_op_id", $request->style_op_id[$s])
					// ->update($spItem);

                    $updateOpCost[] =
                    [
                        'data' => $spItem,
                        'keyval' => $request->style_op_id[$s]
                    ];

					// $this->logFileWrite("Style Operation updated", $request->style_op_id[$s]);
				}

                // update mr_style_operation_n_cost
                if(count($updateOpCost) > 0){
                    (new QueryExtra)
                    ->table('mr_style_operation_n_cost')
                    ->whereKey('style_op_id')
                    ->bulkup($updateOpCost);
                }
            }

			// mr_stl_bom_other_costing - insert
			$otherCosting = BomOtherCosting::updateOrCreate(
				[
					"mr_style_stl_id" => $request->stl_id
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

            // style update
            $getStyle = Style::getStyleIdWiseStyleInfo($input['stl_id'], 'costing_status');

            if($input['costing_status'] == 1 && $getStyle->costing_status == 0 ){
                Style::where('stl_id', $input['stl_id'])->update(['costing_status' => 1]);
            }

            //log_file_write("Costing Successfully Save", $input['stl_id']);
            DB::commit();
	        $data['type'] = 'success';
	        $data['message'] = "Costing Successfully Save.";
            $data['url'] = url()->previous();
	        return response()->json($data);
    	} catch (\Exception $e) {
    		DB::rollback();
    		$bug = $e->getMessage();
	        $data['message'] = $bug;
	        return response()->json($data);
    	}
    }
}
