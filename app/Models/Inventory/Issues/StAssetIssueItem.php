<?php

namespace App\Models\Inventory\Issues;

use Illuminate\Database\Eloquent\Model;

class StAssetIssueItem extends Model
{
    protected $table = "st_asset_issue_item";

    public static function insertData($data, $last_id){

    	for ($i=0; $i < sizeof($data['issued_qty']) ; $i++){
    		$ob = new StAssetIssueItem();
    		$ob->st_asset_issue_id = $last_id;
    		$ob->st_asset_item_id  = $data['st_asset_item_id'][$i];
    		$ob->issued_qty        = $data['issued_qty'][$i];
            $ob->uom_id            = $data['uom_id'][$i];
    		$ob->save();
    	}
    }
}
