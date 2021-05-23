<?php

namespace App\Models\Inventory\Issues;

use Illuminate\Database\Eloquent\Model;
use DB;

class StAssetIssue extends Model
{
     protected $table = "st_asset_issue";

     public static function insertData($data){
     		$ob = new StAssetIssue();
     		$ob->st_asset_requisition_id  	=  $data['st_asset_requisition_id'];
     		$ob->issue_date  				=  $data['issue_date'];
     		$ob->issued_by					=  $data['issued_by'];
            $ob->st_sub_store_info_id       =  $data['st_sub_store_info_id'];

     		$ob->save();

     		return $ob->id;
     }

     public static function viewDataAlongWithDependency(){
     	$data = StAssetIssue::orderBy('id','DESC')->get();
     	foreach ($data as $d) {
     		$req_no_n_type = DB::table('st_asset_requisition as ar')
    										->where('ar.id', $d->st_asset_requisition_id)
    										->select(['ar.item_type', 'ar.asset_requisition_no'])
    										->get();
    		$d->req_no_n_type = $req_no_n_type;
     	}
     	// dd($data);
     	return $data;
     }
}
