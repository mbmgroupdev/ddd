<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use DB;

class StRawMaterialItemReceive extends Model
{
    protected $table = "st_raw_material_item_receive";
    public $timestamps = false;

    public static function get_rm_receive_items()
    {
    	return DB::table('st_raw_material_item_receive AS a')
		        ->select([
		            'a.id AS st_raw_mir_id',
		            'b.id AS st_raw_mr_id',
		            'a.cm_pi_master_id',
		            'a.receive_date',
		            'a.receive_qty',
		            'a.mr_cat_item_id',
		            'b.grn_no',
		            'b.cm_imp_invoice_id',
		            'c.item_name',
		            'f.sup_name',
                    'd.invoice_no'
		        ])
		        ->leftJoin('st_raw_material_receive AS b', 'b.id', 'a.st_raw_material_receive_id')
		        ->leftJoin('mr_cat_item AS c', 'c.id', 'a.mr_cat_item_id')
		        ->leftJoin('cm_imp_invoice AS d', 'd.id', 'b.cm_imp_invoice_id')
		        ->leftJoin('cm_imp_data_entry AS e', 'e.id', 'd.cm_imp_data_entry_id')
		        ->leftJoin('mr_supplier AS f', 'f.sup_id', 'e.mr_supplier_sup_id')
		        ->get();
    }

    public static function get_rm_receive_fabric_items()
    {
    	// return DB::table('st_inspection_details_fabric AS a')
		   //      ->select([
		   //      	'b.id AS inspection_master_id',
		   //      	'b.id AS st_raw_mir_id',
		   //          'b.id AS st_raw_mr_id',
		   //          'b.inspection_date',
		   //          'b.total_avg_point',
		   //          'b.inspected_by',
		   //          'd.item_name',
		   //          'f.mr_cat_item_id',
		   //          'e.grn_no'
		   //      ])
		   //      ->where('b.type',1) // fabric
		   //      ->leftJoin('st_inspection_master AS b','b.id','a.st_inspection_master_id')
		   //      // ->leftJoin('st_inspection_point_details_fabric AS c','c.st_inspection_details_fabric_id','a.id')
		   //      ->leftJoin('mr_cat_item AS d', 'd.id', 'b.mr_cat_item_id')
		   //      ->leftJoin('st_raw_material_receive AS e', 'e.id', 'b.st_raw_material_receive_id')
		   //      ->leftJoin('st_raw_material_item_receive AS f', 'f.id', 'b.st_raw_material_item_receive_id')
		   //      ->get();
		   //
	   	//// $data = DB::table('st_raw_material_item_receive AS a')
	      //   ->select([
	      //       'a.id AS st_raw_mir_id',
	      //       'b.id AS st_raw_mr_id',
	      //       'a.cm_pi_master_id',
	      //       'a.receive_date',
	      //       'a.receive_qty',
	      //       'a.mr_cat_item_id',
	      //       'b.grn_no',
	      //       'b.cm_imp_invoice_id',
	      //       'c.item_name',
	      //       'f.sup_name',
	      //       'g.id AS inspection_master_id'
	      //   ])
	      //   ->leftJoin('st_raw_material_receive AS b', 'b.id', 'a.st_raw_material_receive_id')
	      //   ->leftJoin('mr_cat_item AS c', 'c.id', 'a.mr_cat_item_id')
	      //   ->leftJoin('cm_imp_invoice AS d', 'd.id', 'b.cm_imp_invoice_id')
	      //   ->leftJoin('cm_imp_data_entry AS e', 'e.id', 'd.cm_imp_data_entry_id')
	      //   ->leftJoin('mr_supplier AS f', 'f.sup_id', 'e.mr_supplier_sup_id')
	      //   ->leftJoin('st_inspection_master AS g','g.st_raw_material_item_receive_id','a.id')
	      //   ->get();
	        // $data2 = [];
	        // foreach($data as $k=>$each) {
	        // 	$data2[$k] = $each;
	        // }
	    
        $inspectionMaster =  DB::table('st_inspection_master AS a')
        ->select([
        	'a.id AS imaster_id',
        	'a.inspection_date',
        	'a.total_avg_point',
        	'a.width',
        	'a.weight',
        	'd.id AS item_id',
        	'd.mcat_id AS item_cat_id',
        	'b.grn_no',
        	'e.invoice_no',
        	'c.cm_pi_master_id',
        	'd.item_name',
        	'c.receive_qty',
        	'c.receive_date'
        ])
        ->where('a.type',1) // fabric
        ->leftJoin('st_raw_material_receive AS b', 'b.id', 'a.st_raw_material_receive_id')
        ->leftJoin('st_raw_material_item_receive AS c', 'c.id', 'a.st_raw_material_item_receive_id')
        // ->leftJoin('st_inspection_details_fabric AS b', 'b.st_inspection_master_id', 'a.id')
        ->leftJoin('mr_cat_item AS d', 'd.id', 'a.mr_cat_item_id')
        ->leftJoin('cm_imp_invoice AS e', 'e.id', 'b.cm_imp_invoice_id')
        ->get();
        foreach($inspectionMaster as $k=>$iMaster) {
        	// total pass/fail count
        	$passStatusFabDetailsCount = 0;
	    	$failStatusFabDetailsCount = 0;
        	$inspectionMaster[$k]->st_inspection_details_fabric = DB::table('st_inspection_details_fabric')->where('st_inspection_master_id',$iMaster->imaster_id)->get();
        	foreach($inspectionMaster[$k]->st_inspection_details_fabric as $k1=>$iFabricDetails){
        		if($iFabricDetails->status === 1) {
        			$passStatusFabDetailsCount++;
        		}
        		if($iFabricDetails->status === 0) {
        			$failStatusFabDetailsCount++;
        		}
        		$inspectionMaster[$k]->st_inspection_details_fabric[$k1]->st_inspection_point_details_fabric = DB::table('st_inspection_point_details_fabric')->where('st_inspection_details_fabric_id',$iFabricDetails->id)->get();
        	}
        	// comparison pass/fail count
	        if($passStatusFabDetailsCount >= $failStatusFabDetailsCount) {
	        	$inspectionMaster[$k]->totalStatus = 1;
	        } else {
	        	$inspectionMaster[$k]->totalStatus = 0;
	        }
        }
        return $inspectionMaster;
    }

    /*public static function get_rm_receive_fabric_itemsbyitem($itemid)
    {
    	
	    
        $inspectionMaster =  DB::table('st_inspection_master AS a')
        ->select([
        	'a.id AS imaster_id',
        	'a.inspection_date',
        	'a.total_avg_point',
        	'a.width',
        	'a.weight',
        	'd.id AS item_id',
        	'd.mcat_id AS item_cat_id',
        	'b.grn_no',
        	'e.invoice_no',
        	'c.cm_pi_master_id',
        	'd.item_name',
        	'c.receive_qty',
        	'c.receive_date'
        ])
        ->where('a.mr_cat_item_id',$itemid) // fabric
        ->leftJoin('st_raw_material_receive AS b', 'b.id', 'a.st_raw_material_receive_id')
        ->leftJoin('st_raw_material_item_receive AS c', 'c.id', 'a.st_raw_material_item_receive_id')
        // ->leftJoin('st_inspection_details_fabric AS b', 'b.st_inspection_master_id', 'a.id')
        ->leftJoin('mr_cat_item AS d', 'd.id', 'a.mr_cat_item_id')
        ->leftJoin('cm_imp_invoice AS e', 'e.id', 'b.cm_imp_invoice_id')
        ->get();
      
        return $inspectionMaster;
    }*/
}
