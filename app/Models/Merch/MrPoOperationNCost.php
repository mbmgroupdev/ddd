<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class MrPoOperationNCost extends Model
{
    protected $table= 'mr_po_operation_n_cost';
    public $timestamps= false;

    public static function storeData($datas, $poId, $clrId){
    	foreach ($datas as $data) {
    		// $exists = MrPoOperationNCost::where(['po_id'=>$poId, 'clr_id'=>$clrId])->exists();
      //       if(!$exists){
            	//insert
		    	$row = new MrPoOperationNCost();
		    	$row->mr_style_stl_id 	= $data->mr_style_stl_id;
		    	$row->mr_operation_opr_id = $data->mr_operation_opr_id;
		    	$row->opr_type 			= $data->opr_type;
		    	$row->uom 				= $data->uom;
		    	$row->unit_price 		= $data->unit_price;
		    	$row->mr_order_entry_order_id = $data->mr_order_entry_order_id;
		    	$row->po_id 			= $poId;
		    	$row->clr_id 			= $clrId;
		    	$row->save();
            // }
    	}
    		
    	return 1; 
    }

    public static function deleteRowPOWise($po_id){
    	MrPoOperationNCost::where('po_id', $po_id)->delete();
    }
}
