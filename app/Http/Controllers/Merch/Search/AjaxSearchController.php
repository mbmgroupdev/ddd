<?php

namespace App\Http\Controllers\Merch\Search;

use App\Http\Controllers\Controller;
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

    public function SeasonWiseStyle(Request $request)
    {
        $getStyle = DB::table('mr_style')
        ->select('stl_id AS id', 'stl_no AS text')
        ->where('mr_buyer_b_id', $request->mr_buyer_b_id)
        ->where('mr_season_se_id', $request->mr_season_se_id)
        ->orderBy('stl_id', 'desc')
        ->get();
        
        return $getStyle;
    }
}
