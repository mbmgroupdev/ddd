<?php

namespace App\Http\Controllers\Commercial\Import\PI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Supplier;
use App\Models\Merch\Buyer;
use App\Models\Merch\OrderEntry;
use App\Models\Commercial\CmPiMaster;
use App\Models\Commercial\CmPiBom;
use DB, Validator, DataTables;

class PiMasterFabricController extends Controller
{
	// PI Mater_fabric/ Accessories file assign Form by commercial
    public function showForm()
    {
    	//file list
    	$fileList = DB::table("cm_file")
    		->pluck("file_no", "id  AS file_id");

    	$supplierList= Supplier::pluck('sup_name', 'sup_id');
    	$buyerList = Buyer::getBuyerList();


    	return view("commercial.import.pi.pi_master_fabric", compact(
    		"fileList",
    		"supplierList",
    		"buyerList"
    	));
    }

    // get Order List by Supplier Id
    /* "Order List" will be found in the following tables
    *	get "sales contract list" from cm_exp_lc_entry table by file ID
	*	then get order list from cm_sales_contract_order according to the "sales Contract
	*	List"
    */

	public function getOrderList(Request $request)
    {
    	// dd($request->all());

    	$data= "";
    	if (!empty($request->file_id) && !empty($request->sup_id)){

    		$file_id= $request->file_id;
    		$sup_id= $request->sup_id;

    		//sales Contract List by File ID
    		$saleCotractList= DB::table('cm_exp_lc_entry')
								->where('cm_file_id', $file_id)
								->pluck('cm_sales_contract_id')
								->toArray();


			// Now get order list from sales Cotract list(if exists)
			if(sizeof($saleCotractList)){

				$orderList= DB::table('cm_sales_contract_order')
								->whereIn('cm_sales_contract_id', $saleCotractList)
								->pluck('mr_order_entry_order_id')
								->toArray();
				$order_list_filtered_by_sup_id= DB::table('mr_order_bom_costing_booking')
													->where('mr_supplier_sup_id', $sup_id)
													->groupBy('order_id')
													->pluck('order_id')
													->toArray();
				$orders= array_intersect($orderList, $order_list_filtered_by_sup_id);


				$orderInfo= OrderEntry::whereIn('order_id', $orders)
								->select([
									'order_id',
									'order_code',
									'order_qty',
									'mr_style_stl_id',
									'order_code',
									'order_delivery_date AS delivery_date',
									's.stl_no'
								])
								->leftJoin('mr_style AS s', 'mr_style_stl_id', 's.stl_id')
								->get();
				if($orderInfo->count()>0){
					foreach($orderInfo AS $info){
						// Supplier list
						$supplierInfo= DB::table('mr_supplier AS s')
								->select([
									's.sup_name'
								])
								->where('s.sup_id',$sup_id)
								->first();


								$info->suppliers=$supplierInfo->sup_name;

						$data.= '<tr>
								    <td>'.$info->order_code.'</td>
								    <td>'.$info->stl_no.'</td>
								    <td>'.$info->delivery_date.'</td>
								    <td>'.$info->order_qty.'</td>
								    <td>'.$info->suppliers.'</td>
								    <td><div class="checkbox">
								        <label>
								            <input name="select_order[]" type="checkbox" class="ace order_select" id="order_select" value="'.$info->order_id.'">
								            <span class="lbl"></span>
								        </label>
								        </div>
								    </td>
								</tr>';
					}
					return response()->json($data);
				}

			}
    	}
    	//$data= "<tr><td colspan='6' style='text-align: center;'>No order found</td></tr>";
    	$data= "";
    	return response()->json($data);
    }

    // get File List by buyer Id

	public function getFileList(Request $request)
    {
    	// dd($request->all());

    	$data= "";
    	if (!empty($request->buyer_id) ){

    		$b_id= $request->buyer_id;

    		//sales Contract List by File ID
    		$saleCotractList= DB::table('cm_sales_contract AS s')
					    		->select(
											"elc.cm_file_id"
										)
							  ->leftJoin("cm_exp_lc_entry AS elc", "elc.cm_sales_contract_id", "s.id")
							  ->where("s.mr_buyer_b_id",$b_id)->get();


			// Now get File list from sales Cotract list(if exists)
			    $list = "<option value=\"\">Select File </option>";
				if($saleCotractList->count()>0){

				   foreach ($saleCotractList as  $sales)
		            {
		            	$fileList= DB::table('cm_file AS f')
					    		->select(
											"f.file_no",
											"f.id"
										)
					    		->where("f.id",$sales->cm_file_id)->get();

		            	 foreach ($fileList as  $value) {

		                   $list.= "<option value=\"$value->id\">$value->file_no</option>";
		                 }
		            }
					return response()->json($list);
				}


    	}
    	$list= "<option value=\"\">Select Buyer Again</option>";
    	return response()->json($list);
    }

    // get Supplier List by buyer Id

	public function getSupplierList(Request $request)
    {
    	// dd($request->all());

    	$data= "";
    	if (!empty($request->file_id) ){

    		$file_id= $request->file_id;

    		//sales Contract List by File ID
    		$supplierList= DB::table('cm_file AS f')
					    		->select(
											"sup.sup_id",
											"sup.sup_name"
											)
                ->leftJoin("cm_exp_lc_entry AS lc", "lc.cm_file_id", "f.id")
                ->leftJoin("cm_sales_contract AS sc", "sc.id", "lc.cm_sales_contract_id")
							  ->leftJoin("cm_sales_contract_order AS co", "co.cm_sales_contract_id", "sc.id")
							  ->leftJoin("mr_order_entry AS oe", "oe.order_id", "co.mr_order_entry_order_id")
							  ->leftJoin("mr_order_bom_costing_booking AS ocb", "ocb.order_id", "oe.order_id")
							  ->leftJoin("mr_supplier AS sup", "sup.sup_id", "ocb.mr_supplier_sup_id")
                ->groupBy("ocb.mr_supplier_sup_id")
							  ->where("f.id",$file_id)
							  ->get();


			// Now get File list from sales Cotract list(if exists)
			    $list = "<option value=\"\">Select Supplier </option>";
				if($supplierList->count()>0){

				   foreach ($supplierList as  $value)
		            {
		                $list .= "<option value=\"$value->sup_id\">$value->sup_name</option>";
		            }
					return response()->json($list);
				}


    	}
    	$list= "<option value=\" \">Select Buyer Again</option>";
    	return response()->json($list);
    }

    // get Order List by File Id


	public function getOrderListByFile(Request $request)
    {
    	//dd($request->all());

    	$data_order= "";
    	if (!empty($request->file_id)){

    		$file_id= $request->file_id;


    		//sales Contract List by File ID
    		$saleCotractList= DB::table('cm_exp_lc_entry')
								->where('cm_file_id', $file_id)
								->pluck('cm_sales_contract_id')
								->toArray();


			// Now get order list from sales Cotract list(if exists)
			if(sizeof($saleCotractList)){

				$orderList= DB::table('cm_sales_contract_order')
								->whereIn('cm_sales_contract_id', $saleCotractList)
								->pluck('mr_order_entry_order_id')
								->toArray();
				/*$order_list_filtered_by_sup_id= DB::table('mr_order_bom_costing_booking')
													->where('mr_supplier_sup_id', $sup_id)
													->groupBy('order_id')
													->pluck('order_id')
													->toArray();*/
				//$orders= array_intersect($orderList, $order_list_filtered_by_sup_id);


				$orderInfo= OrderEntry::whereIn('order_id', $orderList)
								->select([
									'order_id',
									'order_code',
									'order_qty',
									'mr_style_stl_id',
									'order_code',
									'order_delivery_date AS delivery_date',
									's.stl_no'
								])
								->leftJoin('mr_style AS s', 'mr_style_stl_id', 's.stl_id')
								->get();
				if($orderInfo->count()>0){
					foreach($orderInfo AS $info){

						$supplierInfo= DB::table('mr_order_bom_costing_booking AS ob')
								->select([
									's.sup_name'
								])
								->leftJoin('mr_supplier AS s', 's.sup_id', 'ob.mr_supplier_sup_id')
								->where('ob.order_id',$info->order_id)
								->get();
								$suppliers=" ";

								foreach($supplierInfo AS $supinfo){
									    	$suppliers.=$supinfo->sup_name.", ";

									    }
								$info->suppliers=$suppliers;

						$data_order.= '<tr>
								    <td>'.$info->order_code.'</td>
								    <td>'.$info->stl_no.'</td>
								    <td>'.$info->delivery_date.'</td>
								    <td>'.$info->order_qty.'</td>
								    <td>'.$info->suppliers.'</td>
								    <td></td>
								</tr>';
					}
					return response()->json($data_order);
				}

			}
    	}
    	// $data_order= "<tr><td colspan='6' style='text-align: center;'>No order found</td></tr>";
    	$data_order= "";
    	return response()->json($data_order);
    }


    //get BOM information by Selected order id and Supplier ID
    public function getBomInfo(Request $request){
    	// dd($request->all());

    	if(!empty($request->order_id) && !empty($request->sup_id)){
    		$order_id= $request->order_id;
    		$sup_id = $request->sup_id;


    		$bomList= DB::table('mr_order_bom_costing_booking AS b')
    					->where('b.order_id', $order_id)
    					->where('b.mr_supplier_sup_id', $sup_id)
    					->select(
							"b.id",
							"b.mr_style_stl_id",
							"b.mr_material_category_mcat_id",
							"c.mcat_name",
							"b.mr_cat_item_id",
							"i.item_name",
							"i.item_code",
							"b.item_description",
							"mc.clr_code",
							"b.size",
							"s.sup_name",
							"a.art_name",
							"com.comp_name",
							"con.construction_name",
							"b.consumption",
							"b.extra_percent",
							"b.uom",
							"b.precost_unit_price",
							"b.precost_value",
              "b.precost_req_qty",
							'b.order_id'
						)
						->leftJoin("mr_material_category AS c", function($join) {
							$join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
						})
						->leftJoin("mr_cat_item AS i", function($join) {
							$join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
							$join->on("i.id", "=", "b.mr_cat_item_id");
						})
						->leftJoin("mr_material_color AS mc", "mc.clr_id", "b.clr_id")
						->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
						->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
						->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
						->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
						->orderBy("b.mr_material_category_mcat_id")
						->get();

    		$fabric_cat="";
    		$sewing_cat="";
    		$finish_cat="";
    		if($bomList->count()>0){
    			foreach($bomList AS $bom){
    				if($bom->mr_material_category_mcat_id == 1){
    					$fabric_cat.= '
			                <tr id="'.$bom->order_id.'" style="background-color: white;">
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->mcat_name .'<input type="hidden" name="bom_id[]" value="'.$bom->id.'"/></td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_description .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->clr_code .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->art_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->comp_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->uom .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->consumption .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->extra_percent .'</td>
		                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="fab_precost_qty">'. $bom->precost_req_qty .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" id="fab_unit_price">'. $bom->precost_unit_price .'</td>
			                    <td style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-left: 0px;padding-right: 0px;">
			                    	<select name="currency[]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px;">
			                            <option value="USD" selected="selected">USD</option>
			                            <option value="EUR">EUR</option>
			                            <option value="GBP">GBP</option>
			                            <option value="TK">TK</option>
			                        </select>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;  padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Qty" type="text" name="pi_qty[]" class="col-xs-12 form-control input-sm fab_pi_qty" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="0">
			                    </td>
			                    <td class="ss" style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-right: 0px;padding-left:0px; padding-left:0px;">
			                    	<input placeholder="PI Value" type="text" name="pi_value[]" class="col-xs-12 form-control input-sm fab_pi_value" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="0" readonly>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input type="date" name="ship_date[]" class="col-xs-12 form-control input-sm" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px; border:none;">
			                    </td>
			                    <td style="margin:0px; padding:0px;">
			                    <a type="button" class="btn btn-xs btn-danger remove_row" title="Remove BOM"  style="margin:0px; padding:0px;" id="remove_row"><i class="ace-icon fa fa-remove"></i>
				                        </a>
			                    </td>
			                </tr>';
    				}
    				if($bom->mr_material_category_mcat_id == 2){
    					$sewing_cat.=  '
			                <tr id="'.$bom->order_id.'" style="background-color: white;">
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->mcat_name .'<input type="hidden" name="bom_id[]" value="'.$bom->id.'"/></td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_description .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->clr_code .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->art_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->comp_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->uom .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->consumption .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->extra_percent .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="sw_precost_qty">'.$bom->precost_req_qty.'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="sw_unit_price">'.$bom->precost_unit_price.'</td>
			                    <td style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-left: 0px;padding-right: 0px;">
			                    	<select name="currency[]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px;">
			                            <option value="USD" selected="selected">USD</option>
			                            <option value="EUR">EUR</option>
			                            <option value="GBP">GBP</option>
			                            <option value="TK">TK</option>
			                        </select>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;  padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Qty" type="text" name="pi_qty[]" class="col-xs-12 form-control input-sm sw_pi_qty" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="0">
			                    </td>
			                    <td class="ss" style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-right: 0px;padding-left:0px;">
			                    	<input placeholder="PI Value" type="text" name="pi_value[]" class="col-xs-12 form-control input-sm sw_pi_value" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="0" readonly>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input type="date" name="ship_date[]" class="col-xs-12 form-control input-sm" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px; border:none;">
			                    </td>
			                    <td style="margin:0px; padding:0px;">
			                    <a type="button" class="btn btn-xs btn-danger remove_row" title="Remove BOM"  style="margin:0px; padding:0px;" id="remove_row"><i class="ace-icon fa fa-remove"></i>
				                        </a>
			                    </td>
			                </tr>';
    				}
    				if($bom->mr_material_category_mcat_id == 3){
						$finish_cat.=  '
			                <tr id="'.$bom->order_id.'" style="background-color: white;">
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->mcat_name .'<input type="hidden" name="bom_id[]" value="'.$bom->id.'"/></td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_description .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->clr_code .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->art_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->comp_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->uom .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->consumption .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->extra_percent .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="fin_precost_qty">'. $bom->precost_req_qty .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="fin_unit_price">'. $bom->precost_unit_price .'</td>
			                    <td style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-left: 0px;padding-right: 0px;">
			                    	<select name="currency[]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px;">
			                            <option value="USD" selected="selected">USD</option>
			                            <option value="EUR">EUR</option>
			                            <option value="GBP">GBP</option>
			                            <option value="TK">TK</option>
			                        </select>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;  padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Qty" type="text" name="pi_qty[]" class="col-xs-12 form-control input-sm fin_pi_qty" data-validation="required number" data-validation-allowing="float" style="margin:0px; padding:0px; border:none;" value="0">
			                    </td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Value" type="text" name="pi_value[]" class="col-xs-12 form-control input-sm fin_pi_value" data-validation="required number" data-validation-allowing="float"  style="margin:0px; padding:0px; border:none;" value="0" readonly>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input type="date" name="ship_date[]" class="col-xs-12 form-control input-sm" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px; border:none;">
			                    </td>
			                    <td style="margin:0px; padding:0px;">
			                    <a type="button" class="btn btn-xs btn-danger remove_row" title="Remove BOM"  style="margin:0px; padding:0px;" id="remove_row"><i class="ace-icon fa fa-remove"></i>
				                        </a>
			                    </td>
			                </tr>';
    				}
    			}
    		}
    		$data["fabric"]= $fabric_cat;
    		$data["sewing"]= $sewing_cat;
    		$data["finishing"]= $finish_cat;
    		$data["category"]=$bom->mr_material_category_mcat_id;
    		return response()->json($data);
    	}

    	$data["fabric"]= "";
		$data["sewing"]= "";
		$data["finishing"]= "";
		$data["category"]=$bom->mr_material_category_mcat_id;
		return response()->json($data);
    }

    //PI Master Fbric Form save
    public function saveForm(Request $request){
    	 //dd($request->all());
    	$validator= Validator::make($request->all(),[
					"pi_no" 				=> "required|max:20",
					"cm_file_id" 			=> "required|max:11",
					"mr_supplier_sup_id" 	=> "required|max:11",
					"pi_date" 				=> "required",
					"pi_category" 			=> "required",
					"pi_last_date" 			=> "required",
					"ship_mode" 			=> "required",
					"pi_status" 			=> "required"
					]);
    	if($validator->fails()){
    		return back()
    				->with('error', "Incorrect Input!");
    	}
    	else{
    		$input= new CmPiMaster();
    		$input->pi_no 				= $request->pi_no;
    		$input->cm_file_id 			= $request->cm_file_id;
    		$input->mr_supplier_sup_id 	= $request->mr_supplier_sup_id;
    		$input->pi_date 			= $request->pi_date;
    		$input->pi_category 		= $request->pi_category;
    		$input->pi_last_date 		= $request->pi_last_date;
    		$input->ship_mode 			= $request->ship_mode;
    		$input->pi_status 			= $request->pi_status;
    		$input->total_pi_value 		= $request->final_total_pi_value;
    		if($input->save()){
    			$master_id= $input->id;
    			//Keep Log
                $this->logFileWrite("CM PI Master Fabric Created", $master_id);
    			if(sizeof($request->bom_id)>0){

    				for($i=0; $i<sizeof($request->bom_id); $i++){

    					$bom= DB::table('mr_order_bom_costing_booking')
    						->where('id', $request->bom_id[$i])
    						->first();
    					$piBom = new CmPiBom();
    					$piBom->mr_order_entry_order_id 		= $bom->order_id ;
    					$piBom->mr_order_bom_costing_booking_id = $bom->id ;
    					$piBom->mr_material_category_mcat_id 	= $bom->mr_material_category_mcat_id ; 
    					$piBom->mr_cat_item_id 				 	= $bom->mr_cat_item_id ;
    					$piBom->description 					= $bom->item_description ;
    					$piBom->mr_material_color_clr_id 		= $bom->clr_id ;
    					$piBom->mr_article_id 					= $bom->mr_article_id ;
    					$piBom->mr_composition_id 				= $bom->mr_composition_id ;
    					$piBom->mr_construction_id 				= $bom->mr_construction_id ;
    					$piBom->uom 							= $bom->uom ;
    					$piBom->consumption 					= $bom->consumption ;
    					$piBom->extra 							= $bom->extra_percent ;
    					$piBom->total_qty 						= $bom->precost_value ;
    					$piBom->unit_price 						= $bom->precost_unit_price ;
    					$piBom->currency 						= $request->currency[$i] ;
    					$piBom->pi_qty 							= $request->pi_qty[$i] ;
    					$piBom->shipped_date 					= $request->ship_date[$i] ;
    					$piBom->cm_pi_master_id 				= $master_id ;

    					$piBom->save();
    					//Keep Log
                    	$this->logFileWrite("CM PI BOM Created", $piBom->id);
    				}
    			}
    			return back()
    					->with('success', 'Saved Successfully!');
    		}
    		else{
    			return back()
    					->with('error', 'Failed saving data!');
    		}
    	}
    }

    public function piList(){
    	$buyerList= Buyer::pluck('b_name');
    	return view('commercial.import.pi.pi_master_fabric_list', compact('buyerList'));
    }

    public function piListData(){
    	$data= DB::table('cm_pi_master AS pim')
    			->select([
    				'pim.id',
    				'pim.pi_no',
    				'f.file_no',
    				's.sup_name',
    				'pim.pi_date',
    				'pim.pi_category',
    				'pim.pi_last_date',
    				'pim.ship_mode',
    				'pim.pi_status'
    			])
    			->leftJoin('cm_file AS f', 'f.id', 'pim.cm_file_id')
    			->leftJoin('mr_supplier AS s', 's.sup_id', 'pim.mr_supplier_sup_id')
    			->orderBy('pim.id', 'DESC')
    			->get();

    	return DataTables::of($data)->addIndexColumn()
				->addColumn('action', function ($data) {
	                $action_buttons= "<div class=\"btn-group\">
                            <a href=".url('commercial/import/pi/master_fabric/'.$data->id.'/edit')." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\" style=\"height:25px; width:26px;\">
                                <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                            </a>
                            <a href=".url('commercial/import/pi/master_fabric/'.$data->id.'/delete')." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\" style=\"height:25px; width:26px;\">
                                <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                            </a> ";
	                    $action_buttons.= "</div>";
	                    return $action_buttons;
	                })
				->rawColumns(['action'])
				->make(true);
    }

    //CM PI Master Fabric Delete
    public function piDelete($id){
    	DB::table('cm_pi_master')
    		->where('id', $id)
    		->delete();
    	//Keep Log
        $this->logFileWrite("CM PI Master Fabric Deleted", $id);
    	DB::table('cm_pi_bom')
    		->where('cm_pi_master_id', $id)
    		->delete();
    	//Keep Log
        $this->logFileWrite("CM PI BOM Deleted", $id);
        return back()
        		->with('success', "PI Master Fabric Deleted!");
    }

    // PI Mater_fabric Edit
    public function editForm($id)
    {




    	$buyerList = Buyer::getBuyerList();

    	// Buyer ID
		$buyerid= DB::table('cm_pi_bom AS b')

		          ->leftJoin('mr_order_entry AS o','o.order_id', 'b.mr_order_entry_order_id')
		          ->leftJoin('mr_buyer AS bu','bu.b_id', 'o.mr_buyer_b_id')

				  ->where('b.cm_pi_master_id', $id)
				  ->first(['bu.b_id']);

    	//CM PI Master Info
    	$piMaster= CmPiMaster::where('id', $id)->first();

    	//file list
    	$fileList = DB::table("cm_file")
    	           ->where('id',$piMaster->cm_file_id)
    	           ->pluck("file_no", "id  AS file_id");
    	//supplier List
    	$supplierList=Supplier::where('sup_id',$piMaster->mr_supplier_sup_id)
    	              ->pluck("sup_name", "sup_id");


    	//extract order lists from order
    	$cm_orders= CmPiBom::where('cm_pi_master_id', $id)
    						->groupBy('mr_order_entry_order_id')
    						->pluck('mr_order_entry_order_id')
    						->toArray();

    	$file_id= $piMaster->cm_file_id;
    	$sup_id= $piMaster->mr_supplier_sup_id;


		//sales Contract List by File ID
		$saleCotractList= DB::table('cm_exp_lc_entry')
							->where('cm_file_id', $file_id)
							->pluck('cm_sales_contract_id')
							->toArray();
		//data
		$order_list_data="";
		// Now get order list from sales Cotract list(if exists)
		if(sizeof($saleCotractList)){

			$orderList= DB::table('cm_sales_contract_order')
							->whereIn('cm_sales_contract_id', $saleCotractList)
							->pluck('mr_order_entry_order_id')
							->toArray();
			$order_list_filtered_by_sup_id= DB::table('mr_order_bom_costing_booking')
												->where('mr_supplier_sup_id', $sup_id)
												->groupBy('order_id')
												->pluck('order_id')
												->toArray();
			$orders= array_intersect($orderList, $order_list_filtered_by_sup_id);


			$orderInfo= OrderEntry::whereIn('order_id', $orders)
							->select([
								'order_id',
								'order_code',
								'order_qty',
								'mr_style_stl_id',
								'order_code',
								'order_delivery_date AS delivery_date',
								's.stl_no'
							])
							->leftJoin('mr_style AS s', 'mr_style_stl_id', 's.stl_id')
							->get();
			if($orderInfo->count()>0){
				foreach($orderInfo AS $info){
					if(in_array($info->order_id, $cm_orders)){
						$checked="checked";
					}
					else{
						$checked="";
					}
					$order_list_data.= '<tr>
							    <td>'.$info->order_code.'</td>
							    <td>'.$info->stl_no.'</td>
							    <td>'.$info->delivery_date.'</td>
							    <td>'.$info->order_qty.'</td>
							    <td>'.$info->delivery_date.'</td>
							    <td><div class="checkbox">
							        <label>
							            <input name="select_order[]" type="checkbox" class="ace order_select" id="order_select" value="'.$info->order_id.'" '.$checked.'>
							            <span class="lbl"></span>
							        </label>
							        </div>
							    </td>
							</tr>';
				}
			}
		}

		//SHow Checked BOM list

		$selected_bom_list= DB::table('cm_pi_bom AS b')
								->where('cm_pi_master_id', $id)
								->select([
									"b.*",
									"c.mcat_name",
									"mc.clr_code",
									"a.art_name",
									"i.item_name",
									"i.item_code",
									"com.comp_name",
									"con.construction_name",
								])
								->leftJoin("mr_material_category AS c", function($join) {
									$join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
								})
								->leftJoin("mr_material_color AS mc", "mc.clr_id", "b.mr_material_color_clr_id")
								->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
								->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
								->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
								->leftJoin('mr_order_bom_costing_booking AS mobc', 'mobc.id', 'b.mr_order_bom_costing_booking_id')

								->leftJoin("mr_cat_item AS i", function($join) {
									$join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
									$join->on("i.id", "=", "mobc.mr_cat_item_id");
								})
								->orderBy("b.mr_material_category_mcat_id")
								->get();
       //dd($selected_bom_list);exit;

			$fabric_cat="";
    		$sewing_cat="";
    		$finish_cat="";

    		$fabric_total_qty=0;
    		$fabric_total_pi_qty=0;
    		$fabric_total_pi_value=0;
    		$sewing_total_qty=0;
    		$sewing_total_pi_qty=0;
    		$sewing_total_pi_value=0;
    		$finishing_total_qty=0;
    		$finishing_total_pi_qty=0;
    		$finishing_total_pi_value=0;
    		$grand_total_qty=0;
    		$grand_total_pi_qty=0;
    		$grand_total_pi_value=0;


    		if($selected_bom_list->count()>0){
    			foreach($selected_bom_list AS $bom){
    				if($bom->mr_material_category_mcat_id == 1){
    					$fabric_total_qty+=$bom->total_qty;
						$fabric_total_pi_qty+=$bom->pi_qty;
						$fabric_total_pi_value+=($bom->pi_qty* $bom->unit_price);
    					$fabric_cat.= '
			                <tr id="'.$bom->mr_order_entry_order_id.'" style="background-color: white;">
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->mcat_name .'<input type="hidden" name="bom_id[]" value="'.$bom->mr_order_bom_costing_booking_id.'"/></td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->description .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->clr_code .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->art_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->comp_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->uom .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->consumption .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->extra .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="fab_precost_qty">'. $bom->total_qty .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" id="fab_unit_price">'. $bom->unit_price .'</td>
			                    <td style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-left: 0px;padding-right: 0px;">
			                    	<select name="currency[]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px;">
			                            <option value="USD" '.(($bom->currency=="USD")? "USD":null).'>USD</option>
			                            <option value="EUR"  '.(($bom->currency=="EUR")? "EUR":null).'>EUR</option>
			                            <option value="GBP"  '.(($bom->currency=="GBP")? "GBP":null).'>GBP</option>
			                            <option value="TK"  '.(($bom->currency=="TK")? "TK":null).'>TK</option>
			                        </select>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;  padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Qty" type="text" name="pi_qty[]" class="col-xs-12 form-control input-sm fab_pi_qty" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none; !important;" value="'. $bom->pi_qty .'">
			                    </td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Value" type="text" name="pi_value[]" class="col-xs-12 form-control input-sm fab_pi_value" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="'. ($bom->pi_qty* $bom->unit_price) .'" readonly>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input type="date" name="ship_date[]" class="col-xs-12 form-control input-sm" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="'. $bom->shipped_date .'">
			                    </td>
			                    <td style="margin:0px; padding:0px;">
			                    <a type="button" class="btn btn-xs btn-danger remove_row" title="Remove BOM"  style="margin:0px; padding:0px;" id="remove_row"><i class="ace-icon fa fa-remove"></i>
				                        </a>
			                    </td>
			                </tr>';
    				}
    				if($bom->mr_material_category_mcat_id == 2){
    					$sewing_total_qty+=$bom->total_qty;
						$sewing_total_pi_qty+=$bom->pi_qty;
						$sewing_total_pi_value+=($bom->pi_qty* $bom->unit_price);

    					$sewing_cat.=  '
			                <tr id="'.$bom->mr_order_entry_order_id.'"style="background-color: white;" >
			                    <td class="ss"  style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->mcat_name .'<input type="hidden" name="bom_id[]" value="'.$bom->mr_order_bom_costing_booking_id.'"/></td>
			                    <td  class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_name .'</td>
			                    <td  class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->description .'</td>
			                    <td  class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->clr_code .'</td>
			                    <td class="ss"  style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->art_name .'</td>
			                    <td class="ss"  style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->comp_name .'</td>
			                    <td class="ss"  style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->uom .'</td>
			                    <td class="ss"  style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->consumption .'</td>
			                    <td class="ss"  style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->extra .'</td>
			                    <td  class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="sw_precost_qty">'. $bom->total_qty .'</td>
			                    <td class="ss"  style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="sw_unit_price">'. $bom->unit_price .'</td>
			                    <td style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-left: 0px;padding-right: 0px;">
			                    	<select name="currency[]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px;">
			                            <option value="USD" '.(($bom->currency=="USD")? "USD":null).'>USD</option>
			                            <option value="EUR"  '.(($bom->currency=="EUR")? "EUR":null).'>EUR</option>
			                            <option value="GBP"  '.(($bom->currency=="GBP")? "GBP":null).'>GBP</option>
			                            <option value="TK"  '.(($bom->currency=="TK")? "TK":null).'>TK</option>
			                        </select>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;  padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Qty" type="text" name="pi_qty[]" class="col-xs-12 form-control input-sm sw_pi_qty" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="'. $bom->pi_qty .'">
			                    </td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Value" type="text" name="pi_value[]" class="col-xs-12 form-control input-sm sw_pi_value" data-validation="required number" data-validation-allowing="float" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="'. ($bom->pi_qty* $bom->unit_price) .'" readonly>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input type="date" name="ship_date[]" class="col-xs-12 form-control input-sm" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="'. $bom->shipped_date .'">
			                    </td>
			                    <td style="margin:0px; padding:0px;">
			                    <a type="button" class="btn btn-xs btn-danger remove_row" title="Remove BOM"  style="margin:0px; padding:0px;" id="remove_row"><i class="ace-icon fa fa-remove"></i>
				                        </a>
			                    </td>
			                </tr>';
    				}
    				if($bom->mr_material_category_mcat_id == 3){
    					$finishing_total_qty+=$bom->total_qty;
						$finishing_total_pi_qty+=$bom->pi_qty;
						$finishing_total_pi_value+=($bom->pi_qty* $bom->unit_price);

						$finish_cat.=  '
			                <tr id="'.$bom->mr_order_entry_order_id.'" style="background-color: white;">
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->mcat_name .'<input type="hidden" name="bom_id[]" value="'.$bom->mr_order_bom_costing_booking_id.'"/></td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->item_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->description .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->clr_code .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->art_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->comp_name .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->uom .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->consumption .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;">'. $bom->extra .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="fin_precost_qty">'. $bom->total_qty .'</td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px;" class="fin_unit_price">'. $bom->unit_price .'</td>
			                    <td  style="margin-bottom:0px;padding-bottom:0px;padding-top:0px;padding-left: 0px;padding-right: 0px;">
			                    	<select name="currency[]" class="col-xs-12 input-sm form-control" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px;">
			                            <option value="USD" '.(($bom->currency=="USD")? "USD":null).'>USD</option>
			                            <option value="EUR"  '.(($bom->currency=="EUR")? "EUR":null).'>EUR</option>
			                            <option value="GBP"  '.(($bom->currency=="GBP")? "GBP":null).'>GBP</option>
			                            <option value="TK"  '.(($bom->currency=="TK")? "TK":null).'>TK</option>
			                        </select>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;  padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Qty" type="text" name="pi_qty[]" class="col-xs-12 form-control input-sm fin_pi_qty" data-validation="required number" data-validation-allowing="float" style="margin:0px; padding:0px; border:none;" value="'. $bom->pi_qty .'">
			                    </td>
			                    <td class="ss" style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input placeholder="PI Value" type="text" name="pi_value[]" class="col-xs-12 form-control input-sm fin_pi_value" data-validation="required number" data-validation-allowing="float"  style="margin:0px; padding:0px; border:none;" value="'. ($bom->pi_qty* $bom->unit_price) .'" readonly>
			                    </td>
			                    <td style="margin-bottom:0px; padding-bottom:0px; padding-top:0px; padding-right: 0px; padding-left:0px;">
			                    	<input type="date" name="ship_date[]" class="col-xs-12 form-control input-sm" data-validation="required" data-validation-optional="true" style="margin:0px; padding:0px; border:none;" value="'. $bom->shipped_date .'">
			                    </td>
			                    <td style="margin:0px; padding:0px;">
			                    <a type="button" class="btn btn-xs btn-danger remove_row" title="Remove BOM"  style="margin:0px; padding:0px;" id="remove_row"><i class="ace-icon fa fa-remove"></i>
				                        </a>
			                    </td>
			                </tr>';
    				}
    			}
    		}
    		//Grand Total Count
        // dd("Fab:",$fabric_cat, "Sew:",$sewing_cat, "Fin:",$finish_cat );exit;
    		$grand_total_qty=($fabric_total_qty+$sewing_total_qty+$finishing_total_qty);
			$grand_total_pi_qty=($fabric_total_pi_qty+$sewing_total_pi_qty+$finishing_total_pi_qty);
			$grand_total_pi_value=($fabric_total_pi_value+$sewing_total_pi_value+$finishing_total_pi_value);

			$fabric_cat_tmp = $fabric_cat;
			$sewing_cat_tmp = $sewing_cat;
			$finish_cat_tmp = $finish_cat;

    	return view("commercial.import.pi.pi_master_fabric_edit", compact(
    		"fileList",
    		"supplierList",
    		"piMaster",
    		"order_list_data",
    		"fabric_cat_tmp",
    		"sewing_cat_tmp",
    		"finish_cat_tmp",
    		"fabric_cat",
    		"sewing_cat",
    		"finish_cat",
    		"fabric_total_qty",
    		"fabric_total_pi_qty",
    		"fabric_total_pi_value",
    		"sewing_total_qty",
    		"sewing_total_pi_qty",
    		"sewing_total_pi_value",
    		"finishing_total_qty",
    		"finishing_total_pi_qty",
    		"finishing_total_pi_value",
    		"grand_total_qty",
    		"grand_total_pi_qty",
    		"grand_total_pi_value",
    		"buyerList",
    		"buyerid"
    	));
    }
    public function updateForm(Request $request){
      //dd($request->all());exit;
    	$pi_id= $request->id;
    	$validator= Validator::make($request->all(),[
					"pi_no" 				=> "required|max:20",
					"cm_file_id" 			=> "required|max:11",
					"mr_supplier_sup_id" 	=> "required|max:11",
					"pi_date" 				=> "required",
					"pi_category" 			=> "required",
					"pi_last_date" 			=> "required",
					"ship_mode" 			=> "required",
					"pi_status" 			=> "required"
					]);
    	if($validator->fails()){
    		return back()
    				->with('error', "Incorrect Input!");
    	}
    	else{
    		CmPiMaster::where('id', $pi_id)
    					->update([
    						"pi_no" 			=> $request->pi_no,
				    		"cm_file_id" 		=> $request->cm_file_id,
				    		"mr_supplier_sup_id"=> $request->mr_supplier_sup_id,
				    		"pi_date" 			=> $request->pi_date,
				    		"pi_category" 		=> $request->pi_category,
				    		"pi_last_date" 		=> $request->pi_last_date,
				    		"ship_mode" 		=> $request->ship_mode,
				    		"pi_status" 		=> $request->pi_status,
				    		"total_pi_value"    => $request->final_total_pi_value
    					]);

    		$this->logFileWrite("CM PI Master Fabric Updated", $pi_id);
        if(!isset($request->bom_id) || empty($request->bom_id)){
          $request->bom_id = [];
        }
    		if(sizeof($request->bom_id)>0){
    			//delete the rows which are removed while editing
    			$existing_boms= CmPiBom::where('cm_pi_master_id', $pi_id)->pluck('mr_order_bom_costing_booking_id')->toArray();
    			for($i=0; $i<sizeof($existing_boms); $i++){
    				if(!in_array($existing_boms[$i], $request->bom_id)){
    					CmPiBom::where('mr_order_bom_costing_booking_id', $existing_boms[$i])
    								->delete();
    					$this->logFileWrite("CM PI BOM Deleted", $existing_boms[$i]);
    				}
    			}
    			//existing boms after deletion
    			$existing_boms= CmPiBom::where('cm_pi_master_id', $pi_id)->pluck('mr_order_bom_costing_booking_id')->toArray();
    			// now update CM PI BOMs
          // if(!isset($request->bom_id) || empty($request->bom_id)){
          //   $request->bom_id = [];
          // }
    			for($i=0; $i<sizeof($request->bom_id); $i++){
    				$bom= DB::table('mr_order_bom_costing_booking')
    						->where('id', $request->bom_id[$i])
    						->first();
    				if(in_array($request->bom_id[$i], $existing_boms)){
    					//  if this bom exists then update it
    					CmPiBom::where('mr_order_bom_costing_booking_id', $request->bom_id[$i])
    					->update([
    						"mr_order_entry_order_id" 			=> $bom->order_id ,
	    					"mr_order_bom_costing_booking_id" 	=> $bom->id ,
	    					"mr_material_category_mcat_id" 		=> $bom->mr_material_category_mcat_id ,
	    					"mr_cat_item_id" 					=> $bom->mr_cat_item_id ,
	    					"description"						=> $bom->item_description ,
	    					"mr_material_color_clr_id" 			=> $bom->clr_id ,
	    					"mr_article_id"						=> $bom->mr_article_id ,
	    					"mr_composition_id"					=> $bom->mr_composition_id ,
	    					"mr_construction_id"				=> $bom->mr_construction_id ,
	    					"uom" 								=> $bom->uom ,
	    					"consumption" 						=> $bom->consumption ,
	    					"extra" 							=> $bom->extra_percent ,
	    					"total_qty" 						=> $bom->precost_value ,
	    					"unit_price" 						=> $bom->precost_unit_price ,
	    					"currency" 							=> $request->currency[$i] ,
	    					"pi_qty" 							=> $request->pi_qty[$i] ,
	    					"shipped_date" 						=> $request->ship_date[$i] ,
	    					"cm_pi_master_id" 					=> $pi_id
    					]);

    					// Update Total PI Value
    					CmPiMaster::where('id', $pi_id)
    					->update([

				    		"total_pi_value"    => $request->final_total_pi_value
    					]);

    					$this->logFileWrite("CM PI BOM Updated", $request->bom_id[$i]);
    				}
    				else{
    					//if not exists then new entry

    					$piBom = new CmPiBom();
    					$piBom->mr_order_entry_order_id 		= $bom->order_id ;
    					$piBom->mr_order_bom_costing_booking_id = $bom->id ;
    					$piBom->mr_material_category_mcat_id 	= $bom->mr_material_category_mcat_id ;
    					$piBom->mr_cat_item_id 					= $bom->mr_cat_item_id ;
    					$piBom->description 					= $bom->item_description ;
    					$piBom->mr_material_color_clr_id 		= $bom->clr_id ;
    					$piBom->mr_article_id 					= $bom->mr_article_id ;
    					$piBom->mr_composition_id 				= $bom->mr_composition_id ;
    					$piBom->mr_construction_id 				= $bom->mr_construction_id ;
    					$piBom->uom 							= $bom->uom ;
    					$piBom->consumption 					= $bom->consumption ;
    					$piBom->extra 							= $bom->extra_percent ;
    					$piBom->total_qty 						= $bom->precost_value ;
    					$piBom->unit_price 						= $bom->precost_unit_price ;
    					$piBom->currency 						= $request->currency[$i] ;
    					$piBom->pi_qty 							= $request->pi_qty[$i] ;
    					$piBom->shipped_date 					= $request->ship_date[$i] ;
    					$piBom->cm_pi_master_id 				= $pi_id ;

    					$piBom->save();

                       // Update Total PI Value
    					CmPiMaster::where('id', $pi_id)
    					->update([

				    		"total_pi_value"    => $request->final_total_pi_value
    					]);

    					//Keep Log
                    	$this->logFileWrite("CM PI BOM Created", $piBom->id);
    				}
    			}
    		}
    	}
    	return back()
    			->with("success", "PI Master Fabric updated Successfully!");
    }

    //Write Every Events in Log File
    public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." ".Auth()->user()->associate_id." \"".$message."\" ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);
    }
}
