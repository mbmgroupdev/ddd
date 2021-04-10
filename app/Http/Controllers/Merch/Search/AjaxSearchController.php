<?php

namespace App\Http\Controllers\Merch\Search;

use App\Http\Controllers\Controller;
use App\Models\Merch\Style;
use App\Models\Merch\Supplier;
use DB;
use Illuminate\Http\Request;

class AjaxSearchController extends Controller
{
    public function item(Request $request)
    {
    	$data = array();
    	$input = $request->all();
    	$getItems = array();
    	if(!empty($input['category'])){
    		$queryData = DB::table('mr_cat_item AS i')
            ->select('i.id','i.mcat_id','i.item_name','i.item_code', 'i.dependent_on')
            ->where('i.mcat_id', $input['category'])
            ->when(!empty($input['keyvalue']), function ($query) use($input){
                return $query->where('i.item_name','LIKE', $input['keyvalue'].'%')->orWhere('i.item_code','LIKE', $input['keyvalue'].'%');
            });

            
            $getItems = $queryData->limit(10)->get();
            if(count($getItems) > 0){
            	$data['items'] = $getItems;

            	$getUom = DB::table('uom')
            	->select('measurement_name AS id','measurement_name AS text')
            	->get();

            	$uomData = DB::table('uom');
            	$uomData_sql = $uomData->toSql();

            	$itemsId = array_column($getItems->toArray(), 'id');
            	
            	$getItemUom = DB::table('mr_cat_item_uom AS iu')
            	->select('u.measurement_name AS id', 'u.measurement_name AS text','iu.mr_cat_item_id')
            	->whereIn('iu.mr_cat_item_id', $itemsId)
            	->leftjoin(DB::raw('(' . $uomData_sql. ') AS u'), function($join) use ($uomData) {
	                $join->on('iu.uom_id','u.id')->addBinding($uomData->getBindings());
	            })
            	->get()
            	->groupBy('mr_cat_item_id',true)
            	->toArray();

            	foreach ($getItems as $key => $item) {
            		if(isset($getItemUom[$item->id])){
            			$item->uom = $getItemUom[$item->id];
            		}else{
            			$item->uom = $getUom;
            		}
            	}

            	$getCatWiseSupplier = DB::table('mr_supplier_item_type')
            	->where('mcat_id', $input['category'])
            	->pluck('mr_supplier_sup_id');

            	$getSupplier = DB::table('mr_supplier')
            	->select('sup_id AS id', 'sup_name AS text')
            	->whereIn('sup_id', $getCatWiseSupplier)
            	->get();
            	$data['supplier'] = $getSupplier;
            }else{
            	$val['item_name'] = '';
            	$val['item_code'] = 'No Item Found!';
            	$data['items'][] = $val;
            }
    	}
    	return $data;
    }

    public function article(Request $request)
    {
    	$input = $request->all();
    	$getArticle = DB::table('mr_article')
    	->select('id', 'art_name AS text')
    	->where('mr_supplier_sup_id', $input['mr_supplier_sup_id'])
    	->get();
    	
    	return $getArticle;
    }

    public function buyerWiseSeason(Request $request)
    {
        $getSeason = DB::table('mr_season')
        ->select('se_id AS id', 'se_name AS text')
        ->where('b_id', $request->b_id)
        ->orderBy('se_id', 'desc')
        ->get();
        
        return $getSeason;
    }
    public function buyerStlSeason(Request $request)
    {
        $season = DB::table('mr_season');
        $season_sql = $season->toSql();

        $getSeason = DB::table('mr_style AS stl')
        ->select(DB::raw('CONCAT_WS("-", se_id, stl_year) AS id'), DB::raw('CONCAT_WS("-", se_name, stl_year) AS text'))
        ->leftjoin(DB::raw('(' . $season_sql. ') AS s'), function($join) use ($season) {
            $join->on('stl.mr_season_se_id','s.se_id')->addBinding($season->getBindings());
        })
        ->where('s.b_id', $request->b_id)
        ->orderBy('text', 'desc')
        ->groupBy('text')
        ->get();

        return $getSeason;
    }

    public function seasonWiseStyle(Request $request)
    {
        $seasonIdYear = explode('-', $request->mr_season_se_id);
        $seasonId = $seasonIdYear[0]; 
        $year = $seasonIdYear[1]; 
        $status = 0;
        if(isset($request->stl_type) && $request->stl_type == 'Bulk'){
            $status = 1;
        }
        $query = DB::table('mr_style')
            ->select('stl_id AS id', 'stl_no AS text')
            ->where('mr_buyer_b_id', $request->mr_buyer_b_id)
            ->where('mr_season_se_id', $seasonId)
            ->where('stl_year', $year);
        if(isset($request->stl_type)){
            $query->where('bom_status', $status);
            $query->where('costing_status', $status);
        }
        $getStyle = $query->orderBy('stl_id', 'desc')->get();
        
        return $getStyle;
    }

    public function orderNo(Request $request)
    {
        $data = []; 
        if($request->has('keyword')){

            $styleData = DB::table('mr_style');
            $styleSqlData = $styleData->toSql();

            $search = $request->keyword;
            $data = DB::table('mr_order_entry as o')
                ->select("o.order_id", DB::raw('CONCAT_WS(" - ", order_code, stl_no) AS order_stl'))
                ->whereIn('o.mr_buyer_b_id', auth()->user()->buyer_permissions())
                ->join(DB::raw('(' . $styleSqlData. ') AS stl'), function($join) use ($styleData) {
                    $join->on('stl.stl_id', "o.mr_style_stl_id")->addBinding($styleData->getBindings());
                })
                ->where(function($q) use($search) {
                    $q->where("o.order_code", "LIKE" , "%{$search}%");
                    $q->orWhere("stl.stl_no", "LIKE" , "%{$search}%");
                })
                ->take(10)
                ->get();
        }

        return response()->json($data);
    }

    public function bulkStyleNo(Request $request)
    {
        $data = []; 
        if($request->has('keyword')){
            $search = $request->keyword;
            $data = DB::table('mr_style as s')
                ->select("s.stl_id", 's.stl_no')
                ->whereIn('s.mr_buyer_b_id', auth()->user()->buyer_permissions())
                ->where("s.stl_no", "LIKE" , "%{$search}%")
                ->where('s.bom_status',1)
                ->where('s.costing_status',1)
                ->take(10)
                ->get();
        }

        return response()->json($data);
    }

    public function port(Request $request)
    {
        $getPort = DB::table('cm_port')
        ->select('id AS id', 'port_name AS text')
        ->where('cnt_id', $request->cnt_id)
        ->orderBy('id', 'desc')
        ->get();
        
        return $getPort;
    }

    public function styleInfo(Request $request)
    {
        // return $request->all();
        $getStyle = Style::getStyleIdWiseStyleInfo($request->stl_id, $request->key);
        if(in_array('mr_brand_br_id', $request->key)){
            $brand = brand_by_id();
            $getStyle->brand = $brand[$getStyle->mr_brand_br_id]->br_name??'';
        }
        return response()->json($getStyle);
    }
}
