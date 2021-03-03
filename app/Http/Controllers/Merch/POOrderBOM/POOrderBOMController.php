<?php

namespace App\Http\Controllers\Merch\POOrderBOM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\BomCostingHistory;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\CatItemUom;
use App\Models\Merch\OrdBomGmtColor;
use App\Models\Merch\OrdBomItemColorMeasurement;
use App\Models\Merch\OrdBomPlacement;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderDetailsBooking;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use App\Models\UOM;
use App\Models\Merch\MrPoBomCostingBooking;
use App\Models\Merch\MrPoBomOtherCosting;
use App\Models\Merch\MrPoOperationNCost;
use DB, Validator, Response, Form, Exception, DataTables;


class POOrderBOMController extends Controller
{


	public function poBOMList(){
		$orders = DB::table('mr_po_bom_costing_booking as b')
							->leftJoin('mr_order_entry as g', 'g.order_id', 'b.order_id')
							->distinct('b.order_id')
							->pluck('g.order_code')
							->toArray();
		$po_nos = DB::table('mr_po_bom_costing_booking as b')
							->leftJoin('mr_purchase_order as f', 'f.po_id', 'b.po_id')
							->distinct('b.po_id')
							->pluck('f.po_no')
							->toArray();
		$mat_categories = DB::table('mr_po_bom_costing_booking as b')
							->leftJoin('mr_material_category as c', 'c.mcat_id', 'b.mr_material_category_mcat_id')
							->distinct('b.mr_material_category_mcat_id')
							->pluck('c.mcat_name')
							->toArray();
		$mat_items = DB::table('mr_po_bom_costing_booking as b')
							->leftJoin('mr_cat_item as d', 'd.id', 'b.mr_cat_item_id')
							->distinct('b.mr_cat_item_id')
							->pluck('d.item_name')
							->toArray();
		$colors = DB::table('mr_po_bom_costing_booking as b')
							->leftJoin('mr_material_color as e', 'e.clr_id', 'b.clr_id')
							->distinct('b.clr_id')
							->pluck('e.clr_name')
							->toArray();

		// dd($orders,$po_nos,$mat_categories,$mat_items,$colors);

		return view('merch.po_bom.po_bom_list', compact('orders','po_nos','mat_categories','mat_items','colors'));
	}

	public function poBOMListData(Request $request){
		// $data = MrPoBomCostingBooking::get();
		if(auth()->user()->hasRole('merchandiser')){
			$lead_associateId[] = auth()->user()->associate_id;
		 $team_members = DB::table('hr_as_basic_info as b')
				->where('associate_id',auth()->user()->associate_id)
				->leftJoin('mr_excecutive_team','b.as_id','mr_excecutive_team.team_lead_id')
				->leftJoin('mr_excecutive_team_members','mr_excecutive_team.id','mr_excecutive_team_members.mr_excecutive_team_id')
				->pluck('member_id');
		$team_members_associateId = DB::table('hr_as_basic_info as b')
																 ->whereIn('as_id',$team_members)
																 ->pluck('associate_id');
		 $team = array_merge($team_members_associateId->toArray(),$lead_associateId);
		 //dd($team);exit;
	 }elseif (auth()->user()->hasRole('merchandising_executive')) {
				 $executive_associateId[] = auth()->user()->associate_id;

				 $teamid = DB::table('hr_as_basic_info as b')
						->where('associate_id',auth()->user()->associate_id)
						->leftJoin('mr_excecutive_team_members','b.as_id','mr_excecutive_team_members.member_id')
						->pluck('mr_excecutive_team_id');
				$team_lead = DB::table('mr_excecutive_team')
							 ->whereIn('id',$teamid)
							 ->leftJoin('hr_as_basic_info as b','mr_excecutive_team.team_lead_id','b.as_id')
							 ->pluck('associate_id');
				$team_members_associateId = DB::table('mr_excecutive_team_members')
																					->whereIn('mr_excecutive_team_id',$teamid)
																					->leftJoin('hr_as_basic_info as b','mr_excecutive_team_members.member_id','b.as_id')
																				 ->pluck('associate_id');
																				 //dd($team_members_associateId);exit;
			$team = array_merge($team_members_associateId->toArray(),$team_lead->toArray());
			//dd($team);exit;

		}else{
		 $team =[];
		}
  if(!empty($team)){
		$data = DB::table('mr_po_bom_costing_booking as b')
						->select([
							'b.*',
							'c.mcat_name',
							'd.item_name',
							'e.clr_name',
							'f.po_no',
							'g.order_code'

							])
						->leftJoin('mr_material_category as c', 'c.mcat_id', 'b.mr_material_category_mcat_id')
						->leftJoin('mr_cat_item as d', 'd.id', 'b.mr_cat_item_id')
						->leftJoin('mr_material_color as e', 'e.clr_id', 'b.clr_id')
						->leftJoin('mr_purchase_order as f', 'f.po_id', 'b.po_id')
						->leftJoin('mr_order_entry as g', 'g.order_id', 'b.order_id')
						->whereIn('g.created_by', $team)
						->groupBy('b.po_id', 'b.clr_id')
						->orderBy('b.id', 'DESC')
						->get();

	}else{
		$data = DB::table('mr_po_bom_costing_booking as b')
						->select([
							'b.*',
							'c.mcat_name',
							'd.item_name',
							'e.clr_name',
							'f.po_no',
							'g.order_code'

							])
						->leftJoin('mr_material_category as c', 'c.mcat_id', 'b.mr_material_category_mcat_id')
						->leftJoin('mr_cat_item as d', 'd.id', 'b.mr_cat_item_id')
						->leftJoin('mr_material_color as e', 'e.clr_id', 'b.clr_id')
						->leftJoin('mr_purchase_order as f', 'f.po_id', 'b.po_id')
						->leftJoin('mr_order_entry as g', 'g.order_id', 'b.order_id')
						->groupBy('b.po_id', 'b.clr_id')
						->orderBy('b.id', 'DESC')
						->get();
	}

		// dd($data);exit;
		return DataTables::of($data)->addIndexColumn()
									->addColumn('action', function ($data) {
					                    // $action_buttons= '<a href= "{{url(\'merch/orders/po_bom/'.$data->po_id.'/'.$data->order_id.'/'.$data->clr_id.'/view\')}}" class="btn btn-primary btn-xs btn-round"><i class="fa fa-edit bigger 110"></i></a>';
					                    $action_buttons= "<a href=".url('merch/orders/po_bom/'.$data->po_id.'/'.$data->order_id.'/'.$data->clr_id.'/view')." class=\"btn btn-xs btn-primary btn-round\" data-toggle=\"tooltip\" title=\"PO BOM Edit\"><i class=\"fa fa-edit bigger-120\"></i></a>";

					                        return $action_buttons;
					                    })
					                ->rawColumns(['action'])
					                ->toJson();
	}

    //PO Order BOM form
	public function showFormPOBOM($po_id, $order_id, $clr_id)
	{
		if(isset($po_id)) {
			$where = ['mr_order_entry_order_id'=>$order_id, 'po_id' => $po_id];
			$poExist = DB::table('mr_purchase_order')->where($where)->first();
			if(empty($poExist)) {
				return redirect('merch/orders/order_edit/'.$order_id)->with('error', 'Order and PO does not match.');
			}
		}
		$poId = $po_id;
		$po_no = DB::table('mr_purchase_order')->where('po_id', $po_id)->value('po_no');
		$color_name = DB::table('mr_material_color')->where('clr_id', $clr_id)->value('clr_name');

		$order= DB::table('mr_order_entry AS OE')
					->where('OE.order_id', $order_id)
					->select([
						"OE.order_id",
						"OE.order_code",
						"u.hr_unit_name",
						"b.b_name",
						"br.br_name",
						"s.se_name",
						"stl.stl_no",
						"OE.mr_style_stl_id",
						"OE.order_ref_no",
						"OE.order_qty",
						"OE.order_delivery_date"
					])
					->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'OE.unit_id')
					->leftJoin('mr_buyer AS b', 'b.b_id', 'OE.mr_buyer_b_id')
					->whereIn('b.b_id', auth()->user()->buyer_permissions())
					->leftJoin('mr_brand AS br', 'br.br_id', 'OE.mr_brand_br_id')
					->leftJoin('mr_season AS s', 's.se_id', 'OE.mr_season_se_id')
					->leftJoin('mr_style AS stl', 'stl.stl_id', "OE.mr_style_stl_id")
					->first();
		// dd($order);
		//roder id
		$order_id= $order_id;
		$isBom= MrPoBomCostingBooking::where(['po_id' => $po_id, 'clr_id' => $clr_id ])->exists();

		//Not Used on Blade..
		$isDBooking= OrderDetailsBooking::where('mr_order_entry_order_id', $order_id)->exists();

		//style id
		$stl_id= $order->mr_style_stl_id;

		//check whether BOM exists or not
		$stl_exists= DB::table('mr_po_bom_costing_booking')
						->where([
									'mr_style_stl_id' => $stl_id,
									'order_id' => $order_id,
									'po_id' => $po_id,
									'clr_id' => $clr_id
								])
						// ->where('mr_style_stl_id', $stl_id)
						// ->where('order_id', $order_id)
						->exists();



		// If BOM exists then show edit option
		if($isBom){


			$existItem = MrPoBomCostingBooking::where([ 'po_id'=>$po_id, 'clr_id' => $clr_id ])
                        ->pluck('mr_cat_item_id')
                        ->toArray();

	        //show category select is Modal
	       	$modalCats = DB::table("mr_material_category AS c")->get();
	        $catItem = [];
	        $cat = [];
	        foreach ($modalCats as $category){
	            $subItem = DB::table("mr_cat_item AS i")
	                        ->select(
	                            "i.mcat_id",
	                            "i.item_name",
	                            "i.id",
	                            "s.msubcat_id",
	                            "s.msubcat_name"
	                        )
	                        ->leftJoin("mr_material_sub_cat AS s", 's.msubcat_id', 'i.mr_material_sub_cat_id')
	                        ->where("i.mcat_id", $category->mcat_id)
	                        ->orderBy('i.mr_material_sub_cat_id','ASC')
	                        ->orderBy('i.id','ASC')
	                        ->get();
	            $cat[$category->mcat_id] = $category;
	            $catItem[$category->mcat_id] = collect($subItem)->groupBy('msubcat_name',true)->toArray();
	        }
	        $modalItem = view('merch.common.get_cat_item_modal', compact('cat','catItem','existItem'));


	        $itemBom = $this->getOrderPOItemBomData($po_id, $clr_id);



		}
		else{
			//---------- BOM ITEM MODAL----------------
			$existItem = DB::table('mr_stl_bom_n_costing')
                        ->where('mr_style_stl_id',$stl_id)
                        ->pluck('mr_cat_item_id')
                        ->toArray();

	        //show category select is Modal
	        $modalCats = DB::table("mr_material_category AS c")->get();
	        $catItem = [];
	        $cat = [];
	        foreach ($modalCats as $category){
	            $subItem = DB::table("mr_cat_item AS i")
	                        ->select(
	                            "i.mcat_id",
	                            "i.item_name",
	                            "i.id",
	                            "s.msubcat_id",
	                            "s.msubcat_name"
	                        )
	                        ->leftJoin("mr_material_sub_cat AS s", 's.msubcat_id', 'i.mr_material_sub_cat_id')
	                        ->where("i.mcat_id", $category->mcat_id)
	                        ->orderBy('i.mr_material_sub_cat_id','ASC')
	                        ->orderBy('i.id','ASC')
	                        ->get();
	            $cat[$category->mcat_id] = $category;
	            $catItem[$category->mcat_id] = collect($subItem)->groupBy('msubcat_name',true)->toArray();
	        }
	        $modalItem = view('merch.common.get_cat_item_modal', compact('cat','catItem','existItem'));
		    //---------- END BOM ITEM MODAL----------------

			/*
			* LOAD BOM ITEM DATA
			*---------------------------------------------
			*/
			//$requested->stl_id = $stl_id;
			$itemBom = $this->getStyleItemBomData($stl_id);

		}

		/*
		* END BOM ITEM DATA
		*---------------------------------------------
		*/
        //sampleTypes
		$samples = DB::table("mr_stl_sample AS ss")
						->select(DB::raw("GROUP_CONCAT(st.sample_name SEPARATOR ', ') AS name"))
						->leftJoin("mr_sample_type AS st", "st.sample_id", "ss.sample_id")
						->where("ss.stl_id", $stl_id)
						->first();
        //operations
		$operations = DB::table("mr_po_operation_n_cost AS oc")
						->select("o.opr_name")
						->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
						->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
						->where("oc.mr_style_stl_id", $stl_id)
						->first();


        //machines
		$machines = DB::table("mr_style_sp_machine AS sm")
						->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
						->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
						->where("sm.stl_id", $stl_id)
						->first();

		$check_costing= DB::table('mr_po_bom_costing_booking')
							->where([
										'po_id'  => $po_id,
										'clr_id' => $clr_id
									])
							// ->where('po_id', $po_id)
							->where('bom_term', '!=', null)
							->exists();

   		$countryList = DB::table('mr_country')->pluck('cnt_name','cnt_id');
	 	$items = DB::table("mr_material_category")
					->get();
	 	$itemList = "";

		//Loop  for Selected category list
		foreach ($items as $cat)
		{
			$itemList .= "<div class=\"col-sm-4\">";


			$name = strtolower(str_replace(" ", "_", $cat->mcat_name));

			$sl = 1;
			$itemList .= "<ul class=\"list-unstyled\" style=\"padding-top: 15px;\">";

			$itemList .= "<li>
						 <label>
						 <input name=\"selected_item[]\" type=\"checkbox\" value=\" $cat->mcat_id \" class=\"ace checkbox-input\"><span class=\"lbl\" style=\"font-size: 14px;\">$cat->mcat_name</span>
						 </label>
					</li>";
			$itemList .= "</ul>";
			$itemList .= "</div>";
		}

		return view("merch.po_bom.po_order_bom", compact(
			"poId",
			"order",
			"samples",
			"samples",
			"operations",
			"machines",
			"modalItem",
			"isBom",
			'check_costing',
			'countryList',
			'itemList',
			'isDBooking',
			'itemBom',
			'modalCats',
			'po_no',
			'color_name',
			'clr_id'
		));
	}

	public function getStyleItemBomData($stl_id)
    {
        $items = DB::table("mr_cat_item")
                    ->orderBy('mcat_id','ASC')
                    ->orderBy('mr_material_sub_cat_id','ASC')
                    ->orderBy('id','ASC')
                    ->get();

        $bomItem     = "";
        foreach ($items as $key => $thisitem) {

            $category_id = $thisitem->mcat_id;
            $item_id = $thisitem->id;

            $boms = null;
            if(isset($stl_id)){
                $boms = DB::table("mr_stl_bom_n_costing AS b")
                        ->select(
                            "b.id as stl_bom",
                            "b.mr_style_stl_id",
                            "b.item_description",
                            "b.clr_id",
                            "b.size",
                            "b.mr_supplier_sup_id",
                            "b.mr_article_id",
                            "b.mr_composition_id",
                            "com.comp_name",
                            "b.mr_construction_id",
                            "con.construction_name",
                            "b.consumption",
                            "b.extra_percent",
                            "b.uom"
                        )
                        ->leftJoin('mr_construction AS con', 'con.id', 'b.mr_construction_id')
                        ->leftJoin('mr_composition AS com', 'com.id', 'b.mr_composition_id')
                        ->where([
                            "b.mr_style_stl_id" => $stl_id,
                            "b.mr_material_category_mcat_id" => $category_id,
                            "b.mr_cat_item_id" => $item_id
                        ])
                        ->first();

                if($boms){
                    $boms->extra_qty = number_format(($boms->consumption/100)*$boms->extra_percent,2);
                    $boms->total_value = number_format(($boms->consumption+$boms->extra_qty),2);
                }
            }
           // dd($boms);


            $item = $this->item($item_id, $category_id);
            // get color list with name
            $color = $this->color("clr_id[]", $boms->clr_id??null, [
                "class" => "form-control input-sm no-select color select2",
                "placeholder"     => "Select"
            ]);

            // get supplier list by category id
            $cl='';
            if($category_id == 1){
                $cl = 'fab-sup';
            }else if($category_id == 2){
                $cl = 'sew-sup';
            }else if($category_id == 3){
                $cl = 'fin-sup';
            }

            $supplier = $this->getSupplierWithAdd($category_id, "mr_supplier_sup_id[]", $boms->mr_supplier_sup_id??null, [
                "class" => "form-control input-sm no-select supplier select2 ".$cl,
                "placeholder"     => "Select",
                "data-validation" => "required"

            ]);

            $uom = $this->uomItemWise($item_id, "uom[]", $boms->uom??'', [
                "class" => "form-control input-sm no-select",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);
            //dd($uom);


            $article = $this->article($boms->mr_supplier_sup_id??null, "mr_article_id[]", $boms->mr_article_id??null, [
                        "class" => "form-control input-sm no-select bom_article",
                        "placeholder"     => "Select"
                    ]);
            // get UoM list
            $uom = $this->uomItemWise($item_id, "uom[]", $boms->uom??null, [
                "class" => "form-control input-sm no-select select2",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);

            $bomItem .= view('merch.common.get_order_item_bom', compact('item','color','supplier','uom','article','boms'))->render();
        }

        return $bomItem;
    }

    public function getOrderPOItemBomData($po_id, $clr_id)
    {
        $items = DB::table("mr_cat_item")
                    ->orderBy('mcat_id','ASC')
                    ->orderBy('mr_material_sub_cat_id','ASC')
                    ->orderBy('id','ASC')
                    ->get();

        $bomItem     = "";
        foreach ($items as $key => $thisitem) {

            $category_id = $thisitem->mcat_id;
            $item_id = $thisitem->id;

            $boms = null;
            if(isset($po_id)){
            	$boms = DB::table("mr_po_bom_costing_booking AS b")
						->select(
							"b.id as bom_id",
							"b.mr_style_stl_id",
							"b.item_description",
							"b.clr_id",
							"b.size",
							"b.mr_supplier_sup_id",
							"b.mr_article_id",
							"b.mr_composition_id",
							"b.mr_construction_id",
							"b.consumption",
							"b.extra_percent",
							"b.depends_on",
							"b.uom" ,
							"b.po_no" ,
							"b.po_id" ,
							"comp.comp_name",
							"cons.construction_name"
						)
						->leftJoin('mr_composition AS comp', 'comp.id', 'b.mr_composition_id')
						->leftJoin('mr_construction AS cons', 'cons.id', 'b.mr_construction_id')
						->where([
                            "b.po_id" => $po_id,
                            "b.clr_id" => $clr_id,
                            "b.mr_material_category_mcat_id" => $category_id,
                            "b.mr_cat_item_id" => $item_id
                        ])
                        ->first();

                if($boms){
                    $boms->extra_qty = number_format(($boms->consumption/100)*$boms->extra_percent,2);
                    $boms->total_value = number_format(($boms->consumption+$boms->extra_qty),2);
                }
            }
           // dd($boms);


            $item = $this->item($item_id, $category_id);
            // get color list with name
            $color = $this->color("clr_id[]", $boms->clr_id??null, [
                "class" => "form-control input-sm no-select color select2",
                "placeholder"     => "Select"
            ]);

            // get supplier list by category id
            $cl='';
            if($category_id == 1){
                $cl = 'fab-sup';
            }else if($category_id == 2){
                $cl = 'sew-sup';
            }else if($category_id == 3){
                $cl = 'fin-sup';
            }

            $supplier = $this->getSupplierWithAdd($category_id, "mr_supplier_sup_id[]", $boms->mr_supplier_sup_id??null, [
                "class" => "form-control input-sm no-select supplier select2 ".$cl,
                "placeholder"     => "Select",
                "data-validation" => "required"

            ]);

            $uom = $this->uomItemWise($item_id, "uom[]", $boms->uom??'', [
                "class" => "form-control input-sm no-select",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);
            //dd($uom);


            $article = $this->article($boms->mr_supplier_sup_id??null, "mr_article_id[]", $boms->mr_article_id??null, [
                        "class" => "form-control input-sm no-select bom_article",
                        "placeholder"     => "Select"
                    ]);
            // get UoM list
            $uom = $this->uomItemWise($item_id, "uom[]", $boms->uom??null, [
                "class" => "form-control input-sm no-select select2",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);

            $bomItem .= view('merch.common.get_order_item_bom', compact('item','color','supplier','uom','article','boms'))->render();
        }

        return $bomItem;
    }

    //Get Item Data
	public function getItemData(Request $request)
	{
		$item_id     = $request->item_id;
		$category_id = $request->category_id;
		$po_id 		 = $request->po_id;
		$bomItem     = "";

		$color_dep="";
		$size_dep="";
		$color_hidden="";
		$size_hidden="";
    	#-------------------------------------
		if (!empty($item_id) && !empty($item_id))
		{
			// get category & item
			$item = $this->item($item_id, $category_id);
			if($item->dependent_on==1){
				$color_dep="checked='true'";
				$size_hidden= 'name="size_depends[]"';
			}
			elseif($item->dependent_on==2){
				$size_dep="checked";
				$color_hidden= 'name="color_depends[]"';
			}
			elseif($item->dependent_on==3){
				$color_dep="checked";
				$size_dep="checked";
			}
			else{
				$size_hidden= 'name="size_depends[]"';
				$color_hidden= 'name="color_depends[]"';
			}
			// get color list with name
			$color = $this->color("clr_id[]", "", [
				"class" => "form-control input-sm no-select color",
				"placeholder"     => "Select",

			]);

			// get supplier list by category id
			$supplier = $this->supplier($category_id, "mr_supplier_sup_id[]", "", [
				"class" => "form-control input-sm no-select supplier",
				"placeholder"     => "Select"
			]);

			// get UoM list
			$uom = $this->uomItemWise($item_id, "uom[]", "", [
				"class" => "form-control input-sm no-select select2",
				"placeholder"     => "Select",

			]);
			$poStyle = '';
			if($po_id != null) {
				$poStyle = "style=\"background-color: lightgoldenrodyellow;\"";
			}
			$bomItem .= "<tr id=\"$item->id\" $poStyle>
			<td class='vertical-align-center'><span>$item->mcat_name</span>
				<input type=\"hidden\" name=\"mr_material_category_mcat_id[]\" value=\"$item->mcat_id\">
				<input type=\"hidden\" name=\"style_primary_key_id[]\" value=\"0\">
				<input type=\"hidden\" name=\"order_primary_key_id[]\" value=\"0\">
			</td>
			<td class='vertical-align-center'>$item->item_name
				<input type=\"hidden\" name=\"mr_cat_item_id[]\" value=\"$item->id\">
			</td>
			<td>

				<label>
		            <input name=\"color_depends[]\" type=\"checkbox\" value=\"1\" data-validation-optional=\"true\" class=\"ace color_depends\" data-validation=\"checkbox_group\" data-validation-qty=\"min1\" $color_dep>
		            <span class=\"lbl\">Color</span>
					<input $color_hidden type=\"hidden\" value=\"0\" class=\"ace color_depends\">
		        </label>
		        <label>
		            <input name=\"size_depends[]\" type=\"checkbox\" value=\"2\" class=\"ace size_depends\" $size_dep>
		            <span class=\"lbl\">Size</span>
		        	<input $size_hidden type=\"hidden\" value=\"0\" class=\"ace size_depends\">
		        </label>

		    </td>
			<td><input type=\"text\" name=\"item_description[]\" class=\"form-control input-sm bg_field\" placeholder=\"Description\"/></td>
			<td><div class='input-group'>$supplier<span class='input-group-btn'><button type='button' id=\"add_new_supplier_button\"  data-toggle='modal' data-target='.newSupplierModal' class='btn btn-xs btn-primary'>+</button></span></div></td>
			<td id='article'><select name=\"mr_article_id[]\" class='form-control input-sm no-select bom_article'  data-validation-optional=\"true\"><option value=\"\">Select</option></td>
			<td></td>
			<td></td>
			<td>$uom</td>
			<td><input type=\"text\" data-toggle=\"tooltip\" title=\"$item->mcat_name > $item->item_name \"  name=\"consumption[]\" class=\"form-control input-sm calc consumption\"  placeholder=\"Consumption\" onclick=\"this.select()\" value=\"0\" oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');\"/></td>
			<td><input type=\"text\" data-toggle=\"tooltip\" title=\"$item->mcat_name > $item->item_name \" name=\"extra_percent[]\" class=\"form-control input-sm calc extra\"  placeholder=\"Extra\"  onclick=\"this.select()\" value=\"0\" oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');\"/></td>
			<td><input type=\"text\" class=\"form-control input-sm qty\"  placeholder=\"Extra Qty\"  readonly value=\"0\"/></td>
			<td><input type=\"text\" class=\"form-control input-sm calc total\"  placeholder=\"Total\"  readonly value=\"0.00\"/></td>
			</tr>";
		}
		$result['category_id'] = $category_id;
		$result['value'] = $bomItem;

		//---! Button id added (#add_new_supplier_button) on 03-10-2019---

		return $result;
	}

	// get category & item
	public function item($item_id = "", $category_id = ""){

		return DB::table("mr_cat_item AS i")
		->select(
			"i.id",
			"c.mcat_id",
			"c.mcat_name",
			"i.item_name",
			"i.item_code",
			"i.dependent_on"
		)
		->leftJoin("mr_material_category AS c", "c.mcat_id", "i.mcat_id")
		->where("i.id", $item_id)
		->where("i.mcat_id", $category_id)
		->first();
	}

	// get color list with name
	public function color($name = "", $selected = "", $option = []){

		$colors = DB::table("mr_material_color")
		->pluck("clr_code", "clr_id");
		$selectedColor = DB::table("mr_material_color")
		->where("clr_id", $selected)
		->value("clr_code");

		$option["style"] = "background:$selectedColor";

		return Form::select($name, $colors, $selected, $option);
	}

	// get supplier list by item id
	public function supplier($mcat_id = "", $name = "", $selected = "", $option = []){

		$suppliers = DB::table("mr_supplier_item_type AS si")
		->leftJoin("mr_supplier AS s", "s.sup_id", "=", "si.mr_supplier_sup_id")
		->where("si.mcat_id", $mcat_id)
		->pluck("s.sup_name", "s.sup_id");

		return Form::select($name, $suppliers, $selected, $option);
	}

	public function getSupplierWithAdd($mcat_id = "", $name = "", $selected = "", $option = [])
    {
    	$suppliers = DB::table("mr_supplier_item_type AS si")
    		->leftJoin("mr_supplier AS s", "s.sup_id", "=", "si.mr_supplier_sup_id")
    		->where("si.mcat_id", $mcat_id)
    		->pluck("s.sup_name", "s.sup_id");

        $html = "<div class='input-group'>";
        $html .=  Form::select($name, $suppliers, $selected, $option);
        //edited on 03-10-2019--->>
        $html .= "<span class='input-group-btn'><button type='button' id='add_new_supplier_button'  data-toggle='modal' data-cat='".$mcat_id."' data-target='.newSupplierModal' class='btn btn-xs btn-primary'>+</button></span></div>";
        return $html;

    	//return Form::select($name, $suppliers, $selected, $option);
    }

	// get article list by supplier id
	public function article($supplier_id="", $name="", $selected="", $option = []){

		if (request()->has("supplier_id"))
		{
			$supplier_id = request()->get("supplier_id");
			$name        = request()->get("name");
			$selected    = request()->get("selected");
			$option      = request()->get("option");
		}


		$articles = DB::table("mr_article")
					->where(function($query) use ($supplier_id) {
                        if($supplier_id!=''){
                        	$query->where("mr_supplier_sup_id", $supplier_id);
                        }
                    })
					->pluck("art_name", "id");

		$html = "<div class='input-group'>";
		$html .=  Form::select($name, $articles, $selected, $option);
		$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newArticleModal' class='btn btn-xs btn-primary'>+</button></span></div>";
		return $html;
	}

	// get composition list by supplier id
	public function composition($supplier_id="", $name="", $selected="", $option = []){

		if (request()->has("supplier_id"))
		{
			$supplier_id = request()->get("supplier_id");
			$name        = request()->get("name");
			$selected    = request()->get("selected");
			$option      = request()->get("option");
		}

		$compositions = DB::table("mr_composition")
		->where("mr_supplier_sup_id", $supplier_id)
		->pluck("comp_name", "id");

		$html = "<div class='input-group'>";
		$html .=  Form::select($name, $compositions, $selected, $option);
		$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newCompositionModal' class='btn btn-xs btn-primary'>+</button></span></div>";
		return $html;
	}

	// get construction list by supplier id
	public function construction($supplier_id="", $name="", $selected="", $option = []){

		if (request()->has("supplier_id"))
		{
			$supplier_id = request()->get("supplier_id");
			$name        = request()->get("name");
			$selected    = request()->get("selected");
			$option      = request()->get("option");
		}

		$constructions = DB::table("mr_construction")
		->where("mr_supplier_sup_id", $supplier_id)
		->pluck("construction_name", "id");

		$html = "<div class='input-group'>";
		$html .=  Form::select($name, $constructions, $selected, $option);
		$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newConstructionModal' class='btn btn-xs btn-primary'>+</button></span></div>";
		return $html;
	}
	// get item wise UoM list
    public function uomItemWise($item, $name = "", $selected = "", $option = [])
    {
        $getUom = CatItemUom::getItemWiseUom($item);
        if(count($getUom) > 0){
            $list = array();
            foreach($getUom as $uom){
                $value = $uom->uom['measurement_name'];
                $list[$value] = $uom->uom['measurement_name'];
            }
        }else{
            $getUom = UOM::get()->toArray();
            $list = array_column($getUom, 'measurement_name', 'measurement_name');
        }
    	return Form::select($name, $list, $selected, $option);
    }

	// get UoM list
	public function uom($name = "", $selected = "", $option = []){

		$list = [
			"Millimeter" => "Millimeter",
			"Centimeter" => "Centimeter",
			"Meter" => "Meter",
			"Inch" => "Inch",
			"Feet" => "Feet",
			"Yard" => "Yard",
			"Piece" => "Piece"
		];
		return Form::select($name, $list, $selected, $option);
	}

    // create new article by supplier id
	public function createArticle(Request $request){

		$data = array();
        $comp_name="";
        $cons_name="";
    	if (!empty($request->supplier_id) && !empty($request->article_name))
    	{
            DB::beginTransaction();  //new line added 03-10-2019
    		try
    		{
				$id = DB::table("mr_article")->insertGetId([
					"art_name" => $request->article_name,
					"mr_supplier_sup_id" => $request->supplier_id,
				]);

                //If Request Has Compostion Name then store
                if($request->has('art_composition')){
                    DB::table('mr_composition')->insert([
                        "comp_name" => $request->art_composition,
                        "mr_supplier_sup_id" => $request->supplier_id,
                        "mr_article_id"=> $id
                    ]);
                    $comp_name= $request->art_composition;
                }

                //If Request Has Construction Name then store
                if($request->has('art_construction')){
                    DB::table('mr_construction')->insert([
                        "construction_name" => $request->art_construction,
                        "mr_supplier_sup_id" => $request->supplier_id,
                        "mr_article_id"=> $id
                    ]);
                    $cons_name= $request->art_construction;
                }

		    	$articles = DB::table("mr_article")
		    		->where("mr_supplier_sup_id", $request->supplier_id)
		    		->pluck("art_name", "id");



		    	$html = "<div class='input-group'>";
		    	$html .=  Form::select($request->name, $articles, $id, $request->option);
		    	$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newArticleModal' class='btn btn-xs btn-primary'>+</button></span></div>";

		    	$data["status"] = true;
		    	$data["message"] = "Saved successful";
		    	$data["result"] = $html;

                DB::commit();  //new line added 03-10-2019
    		}
    		catch(\Exception $e)
    		{
                DB::rollback(); //new line added 03-10-2019
		    	$data["status"] = false;
		    	// $data["message"] = "Article already exists!";
                $data["message"] = "Article already exists!\n".$e->getMessage();
    		}
    	}
    	else
    	{
    		$data['status'] = false;
    		$data['message'] = "Please fill up all required fields!";
    	}
        $data["comp_name"]= $comp_name;
        $data["cons_name"]= $cons_name;


    	return Response::json($data);

		// $data = array();
		// if (!empty($request->supplier_id) && $request->article_name)
		// {
		// 	try
		// 	{
		// 		$id = DB::table("mr_article")->insertGetId([
		// 			"art_name" => $request->article_name,
		// 			"mr_supplier_sup_id" => $request->supplier_id,
		// 		]);

		// 		$articles = DB::table("mr_article")
		// 		->where("mr_supplier_sup_id", $request->supplier_id)
		// 		->pluck("art_name", "id");

		// 		$html = "<div class='input-group'>";
		// 		$html .=  Form::select($request->name, $articles, $id, $request->option);
		// 		$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newArticleModal' class='btn btn-xs btn-primary'>+</button></span></div>";

		// 		$data["status"] = true;
		// 		$data["message"] = "Saved successful";
		// 		$data["result"] = $html;
		// 	}
		// 	catch(\Exception $e)
		// 	{
		// 		$data["status"] = false;
		// 		$data["message"] = "Article already exists!";
		// 	}
		// }
		// else
		// {
		// 	$data['status'] = false;
		// 	$data['message'] = "Please fill up all required fields!";
		// }

		// return Response::json($data);
	}

    // create new composition by supplier id
	public function createComposition(Request $request){

		$data = array();

		if (!empty($request->supplier_id) && $request->composition_name)
		{
			try
			{
				$id = DB::table("mr_composition")->insertGetId([
					"comp_name" => $request->composition_name,
					"mr_supplier_sup_id" => $request->supplier_id,
				]);

				$compositions = DB::table("mr_composition")
				->where("mr_supplier_sup_id", $request->supplier_id)
				->pluck("comp_name", "id");

				$html = "<div class='input-group'>";
				$html .=  Form::select($request->name, $compositions, $id, $request->option);
				$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newCompositionModal' class='btn btn-xs btn-primary'>+</button></span></div>";

				$data["status"] = true;
				$data["message"] = "Saved successful";
				$data["result"] = $html;
			}
			catch(\Exception $e)
			{
				$data["status"] = false;
				$data["message"] = "Composition already exists!";
			}
		}
		else
		{
			$data['status'] = false;
			$data['message'] = "Please fill up all required fields!";
		}

		return Response::json($data);
	}

    // create new construction by supplier id
	public function createConstruction(Request $request){

		$data = array();

		if (!empty($request->supplier_id) && $request->construction_name)
		{
			try
			{
				$id = DB::table("mr_construction")->insertGetId([
					"construction_name" => $request->construction_name,
					"mr_supplier_sup_id" => $request->supplier_id,
				]);

				$construction = DB::table("mr_construction")
				->where("mr_supplier_sup_id", $request->supplier_id)
				->pluck("construction_name", "id");

				$html = "<div class='input-group'>";
				$html .=  Form::select($request->name, $construction, $id, $request->option);
				$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newCompositionModal' class='btn btn-xs btn-primary'>+</button></span></div>";

				$data["status"] = true;
				$data["message"] = "Saved successful";
				$data["result"] = $html;
			}
			catch(\Exception $e)
			{
				$data["status"] = false;
				$data["message"] = "Construction already exists!";
			}
		}
		else
		{
			$data['status'] = false;
			$data['message'] = "Please fill up all required fields!";
		}

		return Response::json($data);
	}

  /**
   * Edit the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

  	public function previewOrder($orderId){
  		$order= DB::table('mr_order_entry AS OE')
			  	->where('OE.order_id', $orderId)
			  	->select([
			  		"OE.order_id",
			  		"OE.order_code",
			  		"u.hr_unit_name",
			  		"b.b_name",
			  		"br.br_name",
			  		"s.se_name",
			  		"stl.stl_no",
			  		"OE.mr_style_stl_id",
			  		"OE.order_ref_no",
			  		"OE.order_qty",
			  		"OE.order_delivery_date"
			  	])
			  	->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'OE.unit_id')
			  	->leftJoin('mr_buyer AS b', 'b.b_id', 'OE.mr_buyer_b_id')
			  	->whereIn('b.b_id', auth()->user()->buyer_permissions())
			  	->leftJoin('mr_brand AS br', 'br.br_id', 'OE.mr_brand_br_id')
			  	->leftJoin('mr_season AS s', 's.se_id', 'OE.mr_season_se_id')
			  	->leftJoin('mr_style AS stl', 'stl.stl_id', "OE.mr_style_stl_id")
			  	->first();

  		$orderItems = OrderBOM::getOrderBomOrderIdWiseSelectItemIdName($orderId);
  		$orderDetails = OrdBomPlacement::getOrderItemDetailsOrderIdWise($orderId);
		//echo "<pre>"; print_r($orderDetails); exit;
  		return view("merch.order_bom.order_bom_preview", compact('order', 'orderItems', 'orderDetails'));
  	}

  	//store and update Order BOMS
  	public function storePOData(Request $request)
  	{
  		// dd($request->all());
  		try{
		  	$validator = Validator::make($request->all(), [
		  		"mr_style_stl_id"    => "required"
		  	]);

		  	if ($validator->fails()){
		  		return back()
		  		->withErrors($validator)
		  		->withInput()
		  		->with('error', "Incorrect Input!! 1");
		  	}
		  	else{
		    	//get order ID
		  		$order_id 	= $request->hidden_order_id;
		  		$po_id 		= $request->hidden_po_id;
		  		$color_id 	= $request->hidden_color_id;
		  		$pre_cat_items = MrPoBomCostingBooking::where(['po_id'=>$po_id, 'clr_id'=>$color_id ])->pluck('mr_cat_item_id')->toArray();
		  		$new_cat_items = $request->mr_cat_item_id;
		  		$delete_cat_items = array_diff($pre_cat_items, $new_cat_items);
		  		// dd("Pre", $pre_cat_items , "new",$new_cat_items, "diff",$delete_cat_items);

		  		foreach ($delete_cat_items as $key => $cat_item_id) {
		  			MrPoBomCostingBooking::deleteRowCatItemWise($cat_item_id);
		  		}
				//check bom available or not for this order
		  		$bom_exists= MrPoBomCostingBooking::where(['po_id'=>$po_id, 'clr_id'=>$color_id ])->exists();
		  		if($bom_exists){
					//Delete BOM which are deselected while Editing
					$isSelected= MrPoBomCostingBooking::where(['po_id'=>$po_id, 'clr_id'=>$color_id ])
										//->where('mr_style_stl_id', null)
										->pluck('id');

					if((isset($request->order_primary_key_id))== false){
						$request->order_primary_key_id = [ 0 => '0' ];
					}

					//delete de-selected options
					// if(!empty($isSelected)){
					// 	for($i=0; $i<sizeof($isSelected); $i++){
					// 		if(!(in_array($isSelected[$i], $request->order_primary_key_id))){

					// 			$getOrdBom = MrPoBomCostingBooking::findOrFail($isSelected[$i]);
					// 			$getPlacement = OrdBomPlacement::where('order_id', $getOrdBom->order_id)->where('item_id', $getOrdBom->mr_cat_item_id)->get();

					// 			$totalPlacement = count($getPlacement);

					//       		for($dp=0; $dp < $totalPlacement; $dp++) {
					//           		$getOrdBomPlacement = OrdBomPlacement::findOrFail($getPlacement[$dp]->id);

					//               	foreach($getOrdBomPlacement->gmt_color as $gColor) {
					//                   	$getGmtColor = OrdBomGmtColor::findOrFail($gColor->id);
					//                   	//delete item color measuremt
					//                   	OrdBomItemColorMeasurement::deleteOrdBomItemColorMeasurementGmtColorIdWise($getGmtColor->id);
					//                   	//delete gmt color
					//                   	$getGmtColor->delete();
					//               	}

					//               	$getOrdBomPlacement->delete();
					//       		}
					// 			MrPoBomCostingBooking::where('id', $isSelected[$i])->delete();
					// 		}
					// 	}

					// }
					// return dd($request->all());
					if(!empty($request->mr_material_category_mcat_id)){
			  			for($i=0; $i<sizeof($request->mr_material_category_mcat_id); $i++){
			  				$depends_on= $request->color_depends[$i]+ $request->size_depends[$i];
		  					//get construction and compostion of the article id
							$comp= DB::table('mr_composition')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
							$cons= DB::table('mr_construction')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();

		  					if($request->order_primary_key_id[$i] !=0 ){
								MrPoBomCostingBooking::where('id', $request->order_primary_key_id[$i])
										->update([
											"mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
											"mr_cat_item_id"     => $request->mr_cat_item_id[$i],
											"item_description"   => $request->item_description[$i],
											"clr_id"             => $color_id,
											"size"               => $request->size[$i],
											"mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
											"mr_article_id"      => $request->mr_article_id[$i],
											"mr_composition_id"  => $comp,
											"mr_construction_id" => $cons,
											"uom"            => $request->uom[$i],
											"consumption"    => $request->consumption[$i],
											"extra_percent"  => $request->extra_percent[$i],
											"order_id"  => $order_id,
											"po_id"  => $po_id,
											"depends_on"  => $depends_on
										]);

										$this->logFileWrite("PO Order BOM Updated", $request->order_primary_key_id[$i]);
							}
			  				else{
		  						$insert = array();
		  						$insert = array(
		  							"mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
		  							"mr_cat_item_id"     => $request->mr_cat_item_id[$i],
		  							"item_description"   => $request->item_description[$i],
		  							"clr_id"             => $color_id,
		  							"size"               => $request->size[$i],
		  							"mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
		  							"mr_article_id"      => $request->mr_article_id[$i],
		  							"mr_composition_id"  => $comp,
		  							"mr_construction_id" => $cons,
		  							"uom"            => $request->uom[$i],
		  							"consumption"    => $request->consumption[$i],
		  							"extra_percent"  => $request->extra_percent[$i],
		  							"order_id"  => $order_id,
		  							"po_id"  => $po_id,
		  							"depends_on"  => $depends_on
		  						);
		  						// if($po_id!=null) {
		  						// 	$insert['po_id'] = $po_id;
		  						// }
		  						$x = MrPoBomCostingBooking::insertGetId($insert);

								$ob_id = DB::getPdo()->lastInsertId();
		  						$this->logFileWrite("PO Order BOM Updated", $ob_id);
			  				}
			  			}
			  		}
		  	// 		if($po_id!=null) {
		  	// 			// if -- order already finaly approved then change (order statys = active) and add (new row in costing approval)
		  	// 			$orderStatus = DB::table("mr_order_entry")->where("order_id",  $order_id)->first();
		  	// 			if($orderStatus->order_status == 'Costed') {
		  	// 				$levelHierarchy = DB::table("mr_approval_hirarchy")
					// 			->where("mr_approval_type", "Order Costing")
					// 			->where("unit", $orderStatus->unit_id)
					// 			->first();
		  	// 				$insert = [
		  	// 					"title" => "precost",
					// 			"submit_by" => auth()->user()->associate_id,
					// 			"submit_to" => $levelHierarchy->level_1,
					// 			"comments"  => '',
					// 			"status"    => 1,
					// 			"created_on"  => date("Y-m-d H:i:s"),
					// 			"mr_order_bom_n_costing_id"  => $order_id,
					// 			"level"     => 1,
		  	// 				];
		  	// 				DB::table("mr_order_costing_approval")->insert($insert);
		  	// 				DB::table("mr_order_entry")->where("order_id",  $order_id)->update(['order_status' => 'Approval Pending']);
		  	// 			}
					// 	return redirect('merch/order_costing/'.$order_id.'/create/'.$po_id);
					// } else {
		  	// 			return back()->with('success', 'Updated successful.');
					// }
		  		}
		  		else{
					/*
					* Order BOMs insert
					*/
					if (is_array($request->mr_material_category_mcat_id) && sizeof($request->mr_material_category_mcat_id) > 0){
						$insert = array();
						for ($i=0; $i<sizeof($request->mr_material_category_mcat_id); $i++)
						{
							$depends_on= $request->color_depends[$i]+ $request->size_depends[$i];

							//get construction and compostion of the article id
							$cons= DB::table('mr_construction')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
							$comp= DB::table('mr_composition')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();

							$stl_bom_id=null;
							if($request->style_primary_key_id[$i]!=0){
								$stl_bom_id=$request->style_primary_key_id[$i];
							}
							$insert = array(
								"mr_style_stl_id" => $stl_bom_id,
								"mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
								"mr_cat_item_id"     => $request->mr_cat_item_id[$i],
								"item_description"   => $request->item_description[$i],
								"clr_id"             => $color_id,
								"size"               => $request->size[$i],
								"mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
								"mr_article_id"      => $request->mr_article_id[$i],
								"mr_composition_id"  => $comp,
								"mr_construction_id" => $cons,
								"uom"            => $request->uom[$i],
								"consumption"    => $request->consumption[$i],
								"extra_percent"  => $request->extra_percent[$i],
								"order_id"  => $order_id,
								"po_id"  => $po_id,
								"depends_on"  => $depends_on
							);
							// if($po_no!=null) {
	  				// 			$insert['po_no'] = $po_no;
	  				// 		}
							MrPoBomCostingBooking::insert($insert);

							$ob_id = DB::getPdo()->lastInsertId();
	  						$this->logFileWrite("PO Order BOM Saved", $ob_id);
						}
					}
					// if($po_no!=null) {
					// 	return redirect('merch/order_costing/'.$order_id.'/create/'.$po_no);
					// } else {
					// 	return back()->with('success', 'Save successful!');
					// }
				}

				return redirect('merch/orders/po_costing/'.$po_id.'/'.$order_id.'/'.$color_id.'/edit')->with('success', 'BOM Saved successful! Please Update Costing');
			}
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}


}
