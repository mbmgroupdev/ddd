<?php

namespace App\Models\Inventory\Issues;

use Illuminate\Database\Eloquent\Model;

class StRMIssueItem extends Model
{
    protected $table = "st_rm_issue_item";

    public static function storeNewData($data, $typ, $last_id){

    	for ($i=0; $i < sizeof($data['issued_qty']) ; $i++) { 
    		$ob = new StRMIssueItem();
    		$ob->st_rm_issue_id 		   	= $last_id;
    		$ob->st_rm_requisition_item_id 	= $data['st_rm_request_item_id'][$i];
    		$ob->issue_qty                 	= $data['issued_qty'][$i];
            $ob->uom_id                     = $data['uom_id'][$i];
    		$ob->requisition_type			= $typ;
    		$ob->save();
    	}
    }
}
