<?php

namespace App\Http\Controllers\Inventory\Issues;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Requisition\StRMRequisitionByProduction;
use App\Models\Inventory\Requisition\StRMRequisitionBySubstore;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Inventory\Issues\StRMIssue;
use App\Models\Inventory\Issues\StRMIssueItem;
use App\Models\Inventory\RmRequisition;
use DB;
use Response;
use Validator;

class RawMeterialIssueController extends Controller
{
    public function index($req_no, $req_type){
    	// $requisition_no = 1;
        // $req_no = request()->req_no;
    	$rm_details = RmRequisition::fetchAllWithConnectivityByRequisitionNo($req_no, $req_type);
    	$issued_by  = Auth()->user()->associate_id;
        $uom = DB::table('uom')->select('id', 'measurement_name')->get();

    	// dd($rm_details, $issued_by);
        if($req_type==1)
    	   return view('inventory.issues.raw_meterial_issue_production',compact('rm_details', 'issued_by', 'uom'));
        else
           return view('inventory.issues.raw_meterial_issue_substore',compact('rm_details', 'issued_by', 'uom')); 
    }

    // public function indexSubstore(){
    //     // $requisition_no = 1;
    //     $rm_details = StRMRequisitionBySubstore::fetchAllWithConnectivityByRequisitionNo();
    //     $issued_by  = Auth()->user()->associate_id;

    //     // dd($rm_details, $issued_by);
    //     return view('inventory.issues.raw_meterial_issue_substore',compact('rm_details', 'issued_by'));
    // }

    public function storeData(Request $request){
    	// dd($request->all());
    	// dd($request->issued_qty);
    	$validator = Validator::make($request->all(),[
                        'issue_date'   => 'required',
                        'issued_qty'   => 'required'
                ]);
            
	    if($validator->fails()){
	        return back()
	            ->withInput()
	            ->with('error', "Incorrect Input!!");
	    }
	    DB::beginTransaction();
	    try{
	    	$last_id = StRMIssue::storeNewData($request->all(), $request->requisition_type);
	    	StRMIssueItem::storeNewData($request->all(), $request->requisition_type, $last_id);

	    	$this->logFileWrite("Inventory>Issues> RM Issue Saved", $last_id);
	    	DB::commit();
	    	
	    	return back()->with('success', "RM Issue Saved");
	    
	    }catch(\Exception $e){
	    	DB::rollback();
	    	$msg = $e->getMessage();
	    	return back()->with('error', $msg );
	    }
    }

    public function viewProductionData(){

    	$data = StRMIssue::viewDataAlongWithDependency(0); // 0 =requisition type-->production
    	// dd($data);
    	return view('inventory.issues.raw_meterial_issue_production_view', compact('data') );

    }

    public function deleteProductionData($id){
        StRMIssue::where('id', $id)->delete();
        StRMIssueItem::where('st_rm_issue_id', $id)->delete();
        $this->logFileWrite("Inventory>Issues> RM Issue Deleted", $id);
        return back()->with('success', "RM Issue Deleted");
    }

    public function viewSubstoreData(){

        $data = StRMIssue::viewDataAlongWithDependency(1);  // 1 =requisition type-->substore
        // dd($data);
        return view('inventory.issues.raw_meterial_issue_substore_view', compact('data') );

    }

    public function deleteSubstoreData($id){
        StRMIssue::where('id', $id)->delete();
        StRMIssueItem::where('st_rm_issue_id', $id)->delete();
        $this->logFileWrite("Inventory>Issues> RM Issue Deleted", $id);
        return back()->with('success', "RM Issue Deleted");
    }
}
