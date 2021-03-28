<?php

use App\Models\Merch\OrderEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

if(!function_exists('item_category_by_id')){
    function item_category_by_id()
    {
       return  Cache::remember('item_category_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_material_category')->get()->keyBy('mcat_id')->toArray();
        });      

    }
}

if(!function_exists('uom_by_id')){
    function uom_by_id()
    {
       return  Cache::remember('uom_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('uom')->get()->keyBy('id')->toArray();
        });      

    }
}

if(!function_exists('country_by_id')){
    function country_by_id()
    {
       return  Cache::remember('country_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_country')->get()->keyBy('cnt_id')->toArray();
        });      

    }
}

if(!function_exists('port_by_id')){
    function port_by_id()
    {
       return  Cache::remember('port_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('cm_port')->get()->keyBy('id')->toArray();
        });      

    }
}

if(!function_exists('product_type_by_id')){
    function product_type_by_id()
    {
       return  Cache::remember('product_type_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_product_type')->get()->keyBy('prd_type_id')->toArray();
        });      

    }
}

if(!function_exists('supplier_by_id')){
    function supplier_by_id()
    {
       return  Cache::remember('supplier_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_supplier')->get()->keyBy('sup_id')->toArray();
        });      

    }
}

if(!function_exists('article_by_id')){
    function article_by_id()
    {
       return  Cache::remember('article_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_article')->get()->keyBy('id')->toArray();
        });      

    }
}

if(!function_exists('item_by_id')){
    function item_by_id()
    {
       return  Cache::remember('item_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_cat_item')->get()->keyBy('id')->toArray();
        });      

    }
}

if(!function_exists('buyer_by_id')){
    function buyer_by_id()
    {
        $buyer_permissions = auth()->user()->buyer_permissions();
        $data = Cache::remember('buyer_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_buyer')
            ->get()->keyBy('b_id')->toArray();
        });
        return collect($data)
        ->filter(function($q) use ($buyer_permissions){
            return in_array($q->b_id, $buyer_permissions);
        })
        ->values()
        ->keyBy('b_id');      

    }
}
if(!function_exists('brand_by_id')){
    function brand_by_id()
    {
        $buyer_permissions = auth()->user()->buyer_permissions();
        $data = Cache::remember('brand_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_brand')
            ->get()->keyBy('br_id')->toArray();
        });
        return collect($data)
        ->filter(function($q) use ($buyer_permissions){
            return in_array($q->b_id, $buyer_permissions);
        })
        ->values()
        ->keyBy('br_id');     

    }
}
if(!function_exists('season_by_id')){
    function season_by_id()
    {
        $buyer_permissions = auth()->user()->buyer_permissions();
        $data = Cache::remember('season_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_season')
            ->get()->keyBy('se_id')->toArray();
        });  
        return collect($data)
        ->filter(function($q) use ($buyer_permissions){
            return in_array($q->b_id, $buyer_permissions);
        })
        ->values()
        ->keyBy('se_id');      

    }
}

if(!function_exists('material_color_by_id')){
    function material_color_by_id()
    {
       return  Cache::remember('material_color_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_material_color')->get()->keyBy('clr_id')->toArray();
        });      

    }
}

if(!function_exists('size_by_id')){
    function size_by_id()
    {
       return  Cache::remember('size_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_product_size')->get()->keyBy('id')->toArray();
        });      

    }
}
if(!function_exists('custom_date_format')){
    function custom_date_format($date){
        return $date != '' || $date != null ? date('F d, Y', strtotime($date)):'';
    }
}

if(!function_exists('make_order_number')){
    function make_order_number($data, $year = null){
        $season = season_by_id();
        $buyer = buyer_by_id();
        $buyerName = 'N/A';
        $seasonName = 'N/A';
        $yearShort = date('y');
        if(isset($data['b_id'])){
            $buyerName = substr($buyer[$data['b_id']]->b_name, 0, 3);
        }

        if(isset($data['mr_season_se_id'])){
            $seasonName = substr($season[$data['mr_season_se_id']]->se_name, 0, 2);
        }
        if($year != ''){
            $yearShort = date('y', strtotime($year));
        }
        $code = strtoupper($yearShort.$seasonName.$buyerName);

        $orderNo = OrderEntry::getCheckLastOrderNumber($code);

        $sl = 1;
        if($orderNo != null){
            $sl = $orderNo + 1;
        }
        $sl = str_pad(($sl), 3, "0", STR_PAD_LEFT);
        return checkOrderNumber($code, $sl);
    }

    function checkOrderNumber($code, $sl)
    {
        $codeNo = $code.$sl;
        $getCheck = OrderEntry::getCheckOrderExistCode($codeNo);
        if($getCheck == true){
            $sl = str_pad(($sl + 1), 3, "0", STR_PAD_LEFT);
            return $this->checkOrderNumber($code, $sl);
        }
        return $codeNo;
    }
}

if(!function_exists('merchandisers_team')){

    function merchandisers_team()
    {

    }
}