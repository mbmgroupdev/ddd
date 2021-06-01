<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use DB;

class RmRequisition extends Model
{
    protected $table= 'st_rm_requisition';
    // public $timestamps= false;

     // $requisition_no need to pass to this function
    public static function fetchAllWithConnectivityByRequisitionNo($req_no, $req_type){
    	$data = DB::table('st_rm_requisition as rp')->select([
    										'rp.*',
    										'odr.order_code',
    										'cat.item_name',
                                            'cat.item_code',
    										'art.art_name',
    										'cstn.construction_name',
    										'cmtn.comp_name',
    										'pro_item.requested_qty as issued_qty',
    										'pro_item.uom_id',
    										'pro_item.id as st_rm_req_item_id'
    									])
    									->leftJoin('st_rm_requisition_item as pro_item', 
    										               'rp.id', 'pro_item.st_rm_requisition_id' )
    									->leftJoin('mr_order_entry as odr', 'rp.mr_order_entry_order_id', 'odr.order_id' )
    									->leftJoin('mr_order_bom_costing_booking as bom', 
    														'pro_item.mr_order_bom_costing_booking_id', 'bom.id' )
    									->leftJoin('mr_cat_item as cat', 'bom.mr_cat_item_id', 'cat.id' )
    									->leftJoin('mr_article as art','bom.mr_article_id', 'art.id' )
    									->leftJoin('mr_construction as cstn', 'bom.mr_construction_id', 'cstn.id' )
    									->leftJoin('mr_composition as cmtn', 'bom.mr_composition_id', 'cmtn.id' )
    									// ->groupBy('bom.order_id')
    									->where(['rp.requisition_no'=> $req_no, 'rp.requisition_type'=> $req_type ])
    									->get();
    	// foreach ($data as $d) {
    		
    	// }
        return $data;
    }
}
