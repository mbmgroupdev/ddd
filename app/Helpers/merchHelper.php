<?php

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
       return  Cache::remember('buyer_by_id', Carbon::now()->addHour(12), function () {
            return DB::table('mr_buyer')->get()->keyBy('b_id')->toArray();
        });      

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
if(!function_exists('custom_date_format')){
    function custom_date_format($date){
        return $date != '' || $date != null ? date('F d, Y', strtotime($date)):'';
    }
}