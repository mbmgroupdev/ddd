<?php

namespace App\Http\Controllers\Inventory\RmRequisition;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Merch\OrderEntry;
use App\Models\Merch\MainCategory;
use App\Models\Merch\OrderBomCostingBooking;
use App\Models\Inventory\RmRequisition;
use App\Models\Inventory\RmRequisitionItem;
use App\Models\Inventory\Issues\StRMIssue;
use DB, DataTables, Validator;

class RmRequisitionController extends Controller
{
    //show RM Requistion Form
    public function showForm(){
    	$requisition_type= request()->requisition_type;
    	$categoryList	= MainCategory::pluck('mcat_name', 'mcat_id');
    	$orderList 		= OrderEntry::pluck('order_code', 'order_id');
        $uom = DB::table('uom')->select('id', 'measurement_name')->get();
    	return view("inventory/rm_requisition/rm_requisition_form", compact('categoryList', 'orderList', 'requisition_type','uom'));
    }

    //get Item List
    public function getItemList(Request $request){
    	$items= DB::table('mr_order_bom_costing_booking AS b')
    				->where('b.order_id', $request->order_no)
					->where('b.mr_material_category_mcat_id', $request->item_type)
					->leftJoin('mr_cat_item AS c', 'b.mr_cat_item_id', 'c.id')
					->pluck('c.item_name', 'b.id');

		$itemList='<option value="">Select Item</option>';
    	foreach ($items as $key => $value) {
    		$itemList.= '<option value="'.$key.'">'.$value.'</option>';
    	}
    	return $itemList;
    }

    //get item information
    public function getItemInfo(Request $request){
    	$item_info= DB::table('mr_order_bom_costing_booking AS b')
    					->where('b.id', $request->item_id)
    					->leftJoin('mr_article AS a', 'a.id', 'b.mr_article_id')
    					->leftJoin('mr_construction AS con', 'con.id', 'b.mr_construction_id')
    					->leftJoin('mr_composition AS com', 'com.id', 'b.mr_composition_id')
    					->select([
    						'a.art_name',
    						'con.construction_name',
    						'com.comp_name'
    					])
    					->first();
    	$data['article']= $item_info->art_name;
    	$data['construction']= $item_info->construction_name;
    	$data['composition']= $item_info->comp_name;
    	return $data;
    }

    // store requisition
    public function storeRequisition(Request $request){
    	// dd($request->all());
    	$validator= Validator::make($request->all(), [
    		'mr_order_entry_order_id' 		=> 'required|max:11',
    		'mr_material_category_mcat_id' 	=> 'required|max:11',
    		'requisition_date' 				=> 'required',
    		'item_id*' 						=> 'required|max:11',
    		'requested_qty*' 				=> 'required',
    	]);

    	if($validator->fails()){
    		return redirect('inventory/rm_requisition/create'."?requisition_type=".$request->requisition_type)
    				->withInput()
    				->with('error', 'Incorrect Input!');
    	}
    	else{
    		try {
    			DB::beginTransaction();
                $req_no = date('mdis')*rand(1,99);
    			$requisition= new RmRequisition();
    			// $req_no= (RmRequisition::count())+1;
    			$requisition->mr_order_entry_order_id = $request->mr_order_entry_order_id;
    			$requisition->mr_material_category_mcat_id = $request->mr_material_category_mcat_id;
    			$requisition->requisition_no = $req_no;
    			$requisition->requisition_date = $request->requisition_date;
    			$requisition->requested_by = auth()->user()->associate_id;
    			$requisition->requisition_type = $request->requisition_type;

    			$requisition->save();

    			$last_id= $requisition->id;

    			if(isset($request->item_id) && (!empty($request->item_id)) && (sizeof($request->item_id)>0)){
    				for($i=0; $i<sizeof($request->item_id); $i++){
    					RmRequisitionItem::insert([
    						'st_rm_requisition_id' 	=> $last_id,
    						'mr_order_bom_costing_booking_id' 	=> $request->item_id[$i],
    						'requested_qty' 					=> $request->requested_qty[$i],
                            'uom_id'                     => $request->uom_id[$i]
    					]);
    				}
    			}

    			$this->logFileWrite("RM Requisition By Sub Store saved", $last_id);

    			DB::commit();

    			return redirect('inventory/rm_requisition/create'."?requisition_type=".$request->requisition_type)
    					->with('success', 'RM Requisition by Sub Store saved Successfully!!');
    			
    		} catch (\Exception $e) {
    			DB::rollback();
    			$msg = $e->getMessage();
    			return redirect('inventory/rm_requisition/create'."?requisition_type=".$request->requisition_type)
    					->with('error', $msg);
    		}
    	}
    }

    //show RM Requisition by Sub Store List
    public function showList(){
    	return view('inventory/rm_requisition/rm_requisition_list');
    }

    //get List Data
    public function getRmRequisitionListData(){
    	$data= DB::table('st_rm_requisition AS rbs')
    			->select([
    				'rbs.*',
    				'oe.order_code',
    				'cat.mcat_name'
    			])
    			->leftJoin('mr_order_entry AS oe', 'oe.order_id', 'rbs.mr_order_entry_order_id')
    			->leftJoin('mr_material_category AS cat', 'cat.mcat_id', 'rbs.mr_material_category_mcat_id')
    			->orderBy('rbs.id','DESC')
                ->get();

    	return DataTables::of($data)->addIndexColumn()
    			->addColumn('action', function ($data){
                    $is_issued = StRMIssue::where('st_rm_requisition', $data->id)->first();
                    // dd($is_issued);

    				$return = "<div class=\"btn-group\">";

                    if(!empty($is_issued)){
                        
                        if($is_issued->requisition_type==0)
                        $return .= "<a  href=".url('inventory/issue/raw_meterial_issue_production_view')."  class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Already Issued\" >
                            <i class=\"ace-icon fa fa-external-link-square bigger-120\"></i>&nbspIssued
                        </a>&nbsp";
                        
                        else
                        $return .= "<a  href=".url('inventory/issue/raw_meterial_issue_substore_view')." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Already Issued\" >
                            <i class=\"ace-icon fa fa-external-link-square bigger-120\"></i>&nbspIssued
                        </a>&nbsp";
                    }
                    else{
                        $return .= "<a href=".url('inventory/issue/raw_meterial_issue_production/'.$data->requisition_no.'/'.$data->requisition_type)." class=\"btn btn-xs btn-info\" data-toggle=\"tooltip\" title=\"Issue Requisition\" >
                            <i class=\"ace-icon fa fa-external-link-square bigger-120\"></i>&nbspIssue
                        </a>&nbsp";
                    }

                    $return .= "<a href=".url('inventory/rm_requisition/delete/'.$data->id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete RM Requisition\" onclick=\"return confirm('Are you sure?')\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";

		            $return .= "</div>";

    				return $return;
    			})
    			->rawColumns(['action'])
    			->toJson();
    }

    //Delete RM Requisition by Sub Store Delete

    public function deleteRmRequisition($id=null){

    	DB::beginTransaction();
    	try {
    		RmRequisition::where('id', $id)->delete();
			RmRequisitionItem::where('st_rm_requisition_id', $id)->delete();

			DB::commit();
			$this->logFileWrite(' RM Requisition by Sub Store Deleted', $id);
			return redirect('inventory/rm_requisition/list')
    					->with('success', 'RM Requisition by Sub Store deleted Successfully!!');

    	} catch (\Exception $e) {
    		DB::rollback();
    			$msg = $e->getMessage();
    			return redirect('inventory/rm_requisition/list')
    					->with('error', $msg);
    	}

    	
    }

}
