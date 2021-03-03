<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class MrPoBomCostingBooking extends Model
{
    //
    protected $table= 'mr_po_bom_costing_booking';
    public $timestamps= false;

    public static function storeData($datas, $poId, $clrId){

    	foreach ($datas as $data) {
    		// $exists = MrPoBomCostingBooking::where(['po_id'=>$poId, 'clr_id'=>$clrId])->exists();
      //       if(!$exists){
            	//insert
		    	$row = new MrPoBomCostingBooking();
		    	$row->mr_style_stl_id 	= $data->mr_style_stl_id;
		    	$row->mr_material_category_mcat_id = $data->mr_material_category_mcat_id;
		    	$row->mr_cat_item_id 	= $data->mr_cat_item_id;
		    	$row->item_description 	= $data->item_description;
		    	$row->clr_id 			= $data->clr_id;
		    	$row->size 				= $data->size;
		    	$row->mr_supplier_sup_id = $data->mr_supplier_sup_id;
		    	$row->mr_article_id 	= $data->mr_article_id;
		    	$row->mr_composition_id = $data->mr_composition_id;
		    	$row->mr_construction_id = $data->mr_construction_id;
		    	$row->uom 				= $data->uom;
		    	$row->consumption 		= $data->consumption;
		    	$row->extra_percent 	= $data->extra_percent;
		    	$row->bom_term 			= $data->bom_term;
		    	$row->precost_fob 		= $data->precost_fob;
		    	$row->precost_lc 		= $data->precost_lc;
		    	$row->precost_freight 	= $data->precost_freight;
		    	$row->precost_req_qty 	= $data->precost_req_qty;
		    	$row->precost_unit_price = $data->precost_unit_price;
		    	$row->precost_value 	= $data->precost_value;
		    	$row->order_id 			= $data->order_id;
		    	$row->booking_qty 		= $data->booking_qty;
		    	$row->delivery_date 	= $data->delivery_date;
		    	$row->depends_on 		= $data->depends_on;
		    	$row->po_id 			= $poId;
		    	$row->clr_id 			= $clrId;
		    	$row->save();
            // }
    	}

    	
    	return 1;

    }

    public static function deleteRowPOWise($po_id){
    	MrPoBomCostingBooking::where('po_id', $po_id)->delete();
    }

    public static function deleteRowCatItemWise($cat_item_id){
    	MrPoBomCostingBooking::where('mr_cat_item_id', $cat_item_id)->delete();
    }


}
