<?php

namespace App\Models\Inventory\Issues;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Issues\StRMIssueItem;
use App\Models\Inventory\Requisition\StRMRequisitionByProduction;
use DB;

class StRMIssue extends Model
{
    protected $table = "st_rm_issue";


    public static function storeNewData($data, $typ){
    	$ob = new StRMIssue();
    	$ob->st_rm_requisition	= $data['requisition_for'];
    	$ob->comments			= $data['comments'];	
    	$ob->issue_date			= $data['issue_date'];
    	$ob->issued_by			= $data['issued_by'];
    	$ob->requisition_type	= $typ;
    	$ob->save();

    	return $ob->id;
    }

    public static function viewDataAlongWithDependency($requisition_type){
    		$data = StRMIssue::where('requisition_type', $requisition_type)->orderBy('id', 'DESC')->get();
    		// foreach ($data as $d) {
    		// 	$requisition_no = DB::table('st_rm_requisition_by_production')
    		// 								->where('id', $d->st_rm_requisition)
    		// 								->value('requisition_no');
    		//     $details = StRMRequisitionByProduction::fetchAllWithConnectivityByRequisitionNo($requisition_no); 
    		// 	$d->details = $details;

    		// 	$sub_data = StRMIssueItem::where('st_rm_issue_id', $d->id)->get();
    		// 	$d->st_issue_item_data = $sub_data;
    			
    		// }

    		foreach ($data as $d) {
    			$requisition_no = DB::table('st_rm_requisition as r')
    										->join('mr_order_entry as o', 'r.mr_order_entry_order_id', 'o.order_id')
    										->where('id', $d->st_rm_requisition)
    										->select(['r.requisition_no', 'o.order_code'])
    										->get();
    			$d->odr_and_req = $requisition_no; 	
    		}
    		// dd($data);

    		return $data;
    }
}
