<?php

namespace App\Http\Controllers\Inventory\Requisition;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\AssetItem;
use App\Models\Inventory\AssetRequisition;
use App\Models\Inventory\AssetRequisitionItem;
use App\Models\Inventory\AssetReceiveFromSupplier;
use App\Models\Inventory\Issues\StAssetIssue;

use DB, Validator,ACL, Auth,DataTables;

class AssetRequisitionController extends Controller
{
    //Asset Requisition Form
    public function showForm(){
        $uom = DB::table('uom')->select('id', 'measurement_name')->get();
    	// $item_types= AssetItem::pluck('item_type', 'id');
        // dd($item_types);
        return view("inventory/requisition/asset_requisition", compact('uom'));
    }

    //get Item List
    public function getItemList(Request $requst){
    	$items= AssetItem::where('item_type', $requst->item_type)
    						->pluck('item_name', 'id');

    	$itemList='<option value="">Select Item</option>';
    	foreach ($items as $key => $value) {
    		$itemList.= '<option value="'.$key.'">'.$value.'</option>';
    	}
    	return $itemList;
    }

    //get Item Description
    public function getItemDescription(Request $request){
    	return AssetItem::where('id', $request->item_id)->pluck('item_description')->first();
    }

    //store requisition
    public function storeRequisition(Request $request){
    	// dd($request->all());
    	$validator= Validator::make($request->all(),[
    		'item_type' 	    => 'required',
    		'requisition_date' 	=> 'required|date',
    		'item_id*' 	        => 'required',
    		'requested_qty*' 	=> 'required',
    	]);

    	if($validator->fails()){
    		return back()
    				->withInput()
    				->with('error', 'Incorrect Input');
    	}
    	else{
	    	DB::beginTransaction();
	    	try {

	    		// $num= AssetRequisition::count('id');
	    		// $num++;
                $num = date('mdis')*rand(1,99);
                // dd($num);exit;


	    		$requisition= new AssetRequisition();
	    		$requisition->item_type = $request->item_type;
	    		$requisition->requisition_date = $request->requisition_date;
	    		$requisition->asset_requisition_no = $num;
	    		$requisition->requested_by = auth()->user()->associate_id;
                $requisition->requsition_type = $request->requisition_type; 
	    		$requisition->save();
	    		$last_id= $requisition->id;

	    		if(isset($request->item_id) && !empty($request->item_id) && sizeof($request->item_id)>0){
	    			for($i=0; $i<sizeof($request->item_id); $i++) {
	    				AssetRequisitionItem::insert([
	    					'st_asset_requisition_id' 	=> $last_id,
	    					'requested_qty' 			=> $request->requested_qty[$i],
                            'uom_id'                    => $request->uom_id[$i],
	    					'st_asset_item_id' 			=> $request->item_id[$i],
	    				]);
	    			}
	    		}
	    		$this->logFileWrite("Asset Requisition Insert", $last_id );
	    		DB::commit();
	    		return back()
	    			->with('success', 'Asset Requisition Saved Successfully!');
	    		
	    	} catch (\Exception $e) {
	    		DB::rollback();
	    		$msg= $e->getMessage();
	    		return back()
	    			->withInput()
	    			->with('error',$msg);
	    	}
    	}
    }

    //show Requisition List
    public function requisitionList(){
    	return view("inventory/requisition/asset_requisition_list");
    }

    //show Requisition List
    public function getRequisitionListData(){

    	$data= AssetRequisition::orderBy('id', 'DESC')->get();
    	// $val= AssetRequisitionItem::where('st_asset_requisition_id', $data->id)
    											// ->pluck(DB::raw("SUM(requested_qty)"))->first();
    	// dd($val);

    	return DataTables::of($data)->addIndexColumn()
    			->addColumn('action', function ($data){

    				$is_issued = StAssetIssue::where('st_asset_requisition_id', $data->id)->exists();
                    // dd($is_issued);
                    $return = "<div class=\"btn-group\">";
                    if($data->requsition_type == 1){
                       if(!empty($is_issued)){
                            $return .= "<a href=".url('inventory/issue/asset_issue_view')."  class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Already Issued\" style=\"width:80px; text-align: left;\" >
                                <i class=\"ace-icon fa fa-external-link-square bigger-120\"></i>&nbspIssued
                            </a>&nbsp";
                        }
                        else{
                            $return .= "<a href=".url('inventory/issue/asset_issue/'.$data->asset_requisition_no)." class=\"btn btn-xs btn-info\" data-toggle=\"tooltip\" title=\"Issue Requisition\" style=\"width:80px; text-align: left;\" >
                                <i class=\"ace-icon fa fa-external-link-square bigger-120\"></i>&nbspIssue
                            </a>&nbsp";
                        }         
                    }
                     

                    if($data->requsition_type==2){
                         $chk= AssetReceiveFromSupplier::where('st_asset_requisition_id',$data->id)->first();
                         // dd($chk);
                           if(!empty($chk)){
                            $return .= "<a href=".url('inventory/asset_receive_list')." class=\"btn btn-xs  btn-success\" data-toggle=\"tooltip\" title=\"Where Invoice No is -".$chk->invoice_no."-\" style=\"width:80px; text-align: left;\">
                                <i class=\"ace-icon fa fa-arrow-circle-down bigger-120\"></i>&nbspReceived</a>";
                             }
                            else{ $return .= "<a href=".url('inventory/asset_receive/'.$data->id)." class=\"btn btn-xs  btn-info\" style=\"width:80px; text-align: left;\">
                                <i class=\"ace-icon fa fa-arrow-circle-up bigger-120\"></i>&nbspReceive</a>";
                             }  
                    }
                    
                    $return .= "<a href=".url('inventory/requisition/asset/requisition_delete/'.$data->id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete Requisition\" onclick=\"return confirm('Are you sure?')\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";

		            $return .= "</div>";

    				return $return;
    			})
    			->addColumn('total_quantity', function($data){
    				$val= AssetRequisitionItem::where('st_asset_requisition_id', $data->id)
    											->value(DB::raw("SUM(requested_qty)"));
    				return $val;
    			})
    			->rawColumns(['action', 'total_quantity'])
    			->toJson();

    }

    //delete Requisition
    public function deleteRequisition($id){
    	DB::beginTransaction();
    	try {
    		AssetRequisition::where('id', $id)->delete();
    		AssetRequisitionItem::where('st_asset_requisition_id', $id)->delete();
    		DB::commit();
    		$this->logFileWrite("Asset Requisition Delete", $id );

    		return redirect('inventory/requisition/asset/requisition_list')
    		->with('success', 'Requistion Deleted Successfully!!');
    	} catch (\Exception $e) {
    		DB::rollback();
	    		$msg= $e->getMessage();
	    		return redirect('inventory/requisition/asset/requisition_list')
	    			->with('error',$msg);
    	}
    }
}
