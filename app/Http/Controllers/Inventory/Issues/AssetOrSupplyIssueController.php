<?php

namespace App\Http\Controllers\Inventory\Issues;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Requisition\StAssetRequisition;
use App\Models\Inventory\Issues\StAssetIssue;
use App\Models\Inventory\Issues\StAssetIssueItem;
use App\Models\Inventory\StSubStoreInfo;
use Response, DB, Validator, Exception;

class AssetOrSupplyIssueController extends Controller
{ 
    public function index(){
        // dd(request()->req_no);exit;
        $req_no = request()->req_no;

    	$asset_data = StAssetRequisition::fetchAllWithConnectivityByRequisitionNo($req_no); //need to pass a "requisition no" as 																						prameter//

    	$asset_data->requition_no = $asset_data[0]->asset_requisition_no;
    	$asset_data->st_asset_requisition_id = $asset_data[0]->id;

    	$issued_by  = Auth()->user()->id;
    	// dd($issued_by);
    	$sub_store  = StSubStoreInfo::getIdAndFloor();

    	// dd($asset_data);
        $uom = DB::table('uom')->select('id', 'measurement_name')->get();

    	return view('inventory.issues.asset_or_supply_issue', compact('asset_data', 'issued_by','sub_store', 'uom') );
    }

    public function storeData(Request $request){
    	// dd($request->all());

    	$validator = Validator::make($request->all(),[
                        'issue_date'   => 'required'
                ]);
            
	    if($validator->fails()){
	        return back()
	            ->withInput()
	            ->with('error', "Incorrect Input!!");
	    }
	    DB::beginTransaction();
	    try{
	    	// dd($request->all());

	    	$last_id = StAssetIssue::insertData($request->all());
	    	StAssetIssueItem::insertData($request->all(), $last_id);

	    	$this->logFileWrite("Inventory>Issue> Asset Issue Saved", $last_id);
	    	DB::commit();
	    	return back()->with('success', "Asset Issue Saved");

	    }catch(\Exception $e){
	    		DB::rollback();
	    		$msg = $e->getMessage();
	    		return back()->with('error', $msg);
	    }

    }

    public function viewData(){

    	$data = StAssetIssue::viewDataAlongWithDependency();
    	//'issued_by' fields is holding 'id'. Now fecthing the 'associate_id' of them 
	    	foreach ($data as $d) {
	    		$issued_by_ass_id  = Auth()->user()->where('id',$d->issued_by)->value('associate_id');
	    		$d->issued_by = $issued_by_ass_id;
	    	}
    	// ----

    	// dd($data);
    	return view('inventory.issues.asset_or_supply_issue_view', compact('data') );

    }

    public function deleteData($id){
        StAssetIssue::where('id', $id)->delete();
        StAssetIssueItem::where('st_asset_issue_id', $id)->delete();
        $this->logFileWrite("Inventory>Issue> Asset Issue Deleted", $id);
        return back()->with('success', 'Asset Issue Deleted');
    }


}
