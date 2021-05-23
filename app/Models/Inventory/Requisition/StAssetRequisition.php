<?php

namespace App\Models\Inventory\Requisition;

use Illuminate\Database\Eloquent\Model;
use DB;

class StAssetRequisition extends Model
{
   protected $table = "st_asset_requisition";

   // $asset_req_no --parameter
   public static function fetchAllWithConnectivityByRequisitionNo($req_no){   //there should be the parameter "requisition no"
		$data = DB::table('st_asset_requisition as ar')
									->select([
											'ar.*',
											'ari.requested_qty as issued_qty',
											'ari.st_asset_item_id',
											'ari.uom_id',
											'ai.item_name',
											'ai.item_description'
									])
									->leftJoin('st_asset_requisition_item as ari', 'ar.id', 'ari.st_asset_requisition_id')
									->leftJoin('st_asset_item as ai', 'ari.st_asset_item_id', 'ai.id')
									->where('ar.asset_requisition_no', '=', $req_no)
									->get();
		//dd($data);
		return $data;
   }

}
