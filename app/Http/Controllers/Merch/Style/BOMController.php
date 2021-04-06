<?php

namespace App\Http\Controllers\Merch\Style;

use App\Http\Controllers\Controller;
use App\Models\Merch\BomCosting;
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

class BOMController extends Controller
{
	public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }

    public function index()
    {
    	$buyerList = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())
                    ->pluck('b_name', 'b_id');

        $seasonList = Season::pluck('se_name','se_id');
    	return view("merch.style_bom.style_bom_list", compact(
            'buyerList',
            'seasonList'
        ));
    }
    public function getListData()
    {
    	$data = DB::table("mr_style AS s")
    		->select(
    			"s.stl_id",
    			"sb.mr_style_stl_id",
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
			->leftJoin("mr_stl_bom_n_costing AS sb", "sb.mr_style_stl_id", "=", "s.stl_id")
			->leftJoin("mr_buyer AS b", "b.b_id", "=", "s.mr_buyer_b_id")
            ->whereIn('b.b_id', auth()->user()->buyer_permissions())
			->leftJoin("mr_product_type AS t", "t.prd_type_id", "=", "s.prd_type_id")
			->leftJoin("mr_garment_type AS g", "g.gmt_id", "=", "s.gmt_id")
			->leftJoin("mr_season AS se", "se.se_id", "=", "s.mr_season_se_id")
            ->leftJoin("mr_brand AS br", "br.br_id", "=", "s.mr_brand_br_id")
			->groupBy("s.stl_id")
            ->orderBy('s.stl_id', 'desc')
			->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('stl_type', function ($data) {
                if ($data->stl_type == "Bulk")
                {
                    return $data->stl_type;
                }
                else
                {
                    return $data->stl_type;
                }
            })
            ->editColumn('se_name', function ($data)
            {
                return htmlspecialchars_decode($data->se_name);
            })
            ->editColumn('action', function ($data) {
                $return = "<div class=\"btn-group\">";
            	if (empty($data->mr_style_stl_id))
            	{
            		$return .= "<a href=".url('merch/style/bom/'.$data->stl_id)." class=\"btn btn-sm btn-warning\" data-toggle=\"tooltip\" title=\"Create Style BOM\">BOM</a>";
            	}
            	else
            	{
                    $return .= "<a href=".url('merch/style/bom/'.$data->stl_id)." class=\"btn btn-sm btn-success\" data-toggle=\"tooltip\" title=\"Edit Style BOM\">
                     		<i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                        </a>
                        <a href=".url('merch/style_bom/'.$data->stl_id.'/delete')." class=\"btn btn-sm btn-danger\" data-toggle=\"tooltip\" onClick=\"return window.confirm('Are you sure?')\" title=\"Delete\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";
            	}
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'stl_type', 'stl_no', 'b_name', 'br_name', 'stl_product_name', 'se_name', 'action'
            ])
            ->make(true);
    }

    public function show(Request $request, $id)
    {
		try {
			$style = Style::getStyleIdWiseStyleInfo($id, ['stl_id', 'mr_buyer_b_id', 'stl_type', 'stl_no', 'stl_product_name', 'stl_description', 'stl_smv', 'stl_img_link', 'stl_status', 'bom_status', 'costing_status']);
	    	
			if($style == null){
				toastr()->error("Style Not Found!");
				return back();
			}
			
			$uom = uom_by_id();
			$uom = collect($uom)->pluck('measurement_name','id');
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
				->join(DB::raw('(' . $supplierDataSql. ') AS s'), function($join) use ($supplierData) {
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
            	->join(DB::raw('(' . $uomData_sql. ') AS u'), function($join) use ($uomData) {
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
			$samples = SampleStyle::getStyleIdWiseSampleName($id);
		    $operations = OperationCost::getStyleIdWiseOperationCostName($id);
		    $machines = StyleSpecialMachine::getStyleIdWiseSpMachineName($id);
		    $getColor = DB::table("mr_material_color")->select('clr_id AS id', 'clr_name AS text')->get();
		    $itemCategory = item_category_by_id();
		    
		    return view('merch.style_bom.index', compact('style', 'samples', 'operations', 'machines', 'getColor', 'itemCategory', 'uom', 'groupStyleBom', 'getArticle', 'getSupplier', 'getItems'));
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
    	DB::beginTransaction();
    	try {
    		$oldItem = array_filter($input['bomitemid'], 'strlen');
	    	$getItemBom = BomCosting::getStyleWiseItem($input['stl_id'], ['id']);
	    	$getBomId = collect($getItemBom->toArray())->pluck('id')->toArray();
	    	// return $getBomId;
	    	$itemDiff = array_diff($getBomId, $oldItem);
	    	for ($d=0; $d < count($itemDiff); $d++) { 
	    		BomCosting::whereIn('id', $itemDiff)->delete();
	    	}

    		$sl = 1;
    		$updateBOM = [];
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
            			'sl' => $sl,
            		];
            		if($input['bomitemid'][$i] != null){ 
            			// update
            			$updateBOM[] = 
					    [
							'data' => $bom,
							'keyval' => $input['bomitemid'][$i]
					    ];
						
            			// BomCosting::where('id', $input['bomitemid'][$i])->update($bom);
            		}else{
            			// create
            			$bomId = BomCosting::create($bom)->id;
            			$data['value'][$i] = $bomId;
            		}
            		
            		$sl++;
            	}
	
            }

            // update stl_bom_n_costing
            if(count($updateBOM) > 0){
            	(new QueryExtra)
			    ->table('mr_stl_bom_n_costing')
			    ->whereKey('id')
			    ->bulkup($updateBOM);
            }
            
            // style update
            $getStyle = Style::getStyleIdWiseStyleInfo($input['stl_id'], 'bom_status');

            if($input['bom_status'] == 1 && $getStyle->bom_status == 0 ){
            	Style::where('stl_id', $input['stl_id'])->update(['bom_status' => 1]);
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
