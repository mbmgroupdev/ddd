<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class MrPoBomOtherCosting extends Model
{
   protected $table  = 'mr_po_bom_other_costing';
   public $timestamps= false;

   public static function storeData($datas, $poId, $clrId){
      foreach ($datas as $data) {
            // $exists = MrPoBomOtherCosting::where(['po_id'=>$poId, 'clr_id'=>$clrId])->exists();
            // if(!$exists){
                  //Insert

            		$row = new MrPoBomOtherCosting();
            		$row->mr_style_stl_id 	= $data->mr_style_stl_id;
            		$row->testing_cost 		= $data->testing_cost;
            		$row->cm 				= $data->cm;
            		$row->commercial_cost 	= $data->commercial_cost;
            		$row->net_fob 			= $data->net_fob;
            		$row->buyer_comission_percent = $data->buyer_comission_percent;
            		$row->buyer_fob 		= $data->buyer_fob;
            		$row->agent_comission_percent = $data->agent_comission_percent;
            		$row->agent_fob 		= $data->agent_fob;
            		$row->mr_order_entry_order_id = $data->mr_order_entry_order_id;
            		$row->po_id 			= $poId;
                  $row->clr_id         = $clrId;
         		   $row->save();              
            // }
      }

   	return 1;
   }

   public static function deleteRowPOWise($po_id){
      MrPoBomOtherCosting::where('po_id', $po_id)->delete();
   }
}
