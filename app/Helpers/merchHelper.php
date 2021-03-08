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