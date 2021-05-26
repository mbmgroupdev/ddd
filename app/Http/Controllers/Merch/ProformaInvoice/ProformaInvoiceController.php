<?php

namespace App\Http\Controllers\Merch\ProformaInvoice;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\Supplier;
use App\Models\Merch\MrOrderBooking;
use App\Models\Merch\OrderBOM;
use App\Models\Commercial\PiForwardDetails;
use App\Models\Merch\OrderBomCostingBooking;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\PoBooking;
use App\Models\Merch\PoBookingPi;
use App\Models\Merch\PoBookingDetail;
use App\Models\Merch\ProductType;
use App\Models\Commercial\CmPiBom;
use App\Models\Commercial\CmPiMaster;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use App\Models\Merch\MrPiItemUnitPrice;
use DB,Validator, ACL, DataTables, Form, stdClass;
use Illuminate\Http\Request;

class ProformaInvoiceController extends Controller
{
	public function showList()
	{
		try {
			$hi ='';
			return view("merch.proforma_invoice.pi_list", compact('hi'));
		}catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getPIListData()
	{
		//dd('00');exit;
		if(auth()->user()->hasRole('merchandiser')){
			$lead_asid = DB::table('hr_as_basic_info as b')
				 ->where('associate_id',auth()->user()->associate_id)
				 ->pluck('as_id');
		 $team_members = DB::table('hr_as_basic_info as b')
				->where('associate_id',auth()->user()->associate_id)
				->leftJoin('mr_excecutive_team','b.as_id','mr_excecutive_team.team_lead_id')
				->leftJoin('mr_excecutive_team_members','mr_excecutive_team.id','mr_excecutive_team_members.mr_excecutive_team_id')
				->pluck('member_id');
		 $team = array_merge($team_members->toArray(),$lead_asid->toArray());
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
		}else{
		 $team =[];
		}
		if(!empty($team)){
			$data = CmPiMaster::where('total_pi_qty','>',0)
			                  ->leftJoin('cm_pi_bom','cm_pi_master.id','cm_pi_bom.cm_pi_master_id')
												->leftJoin('mr_order_entry','cm_pi_bom.mr_order_entry_order_id','mr_order_entry.order_id')
												->whereIn('mr_order_entry.created_by', $team)
			                  ->orderBy('cm_pi_master.id','DESC')
												->groupBy('cm_pi_master.id')
												->get();
		}else{
			$data = CmPiMaster::where('total_pi_qty','>',0)
			                  ->leftJoin('cm_pi_bom','cm_pi_master.id','cm_pi_bom.cm_pi_master_id')
												->leftJoin('mr_order_entry','cm_pi_bom.mr_order_entry_order_id','mr_order_entry.order_id')
			                  ->orderBy('cm_pi_master.id','DESC')
												->groupBy('cm_pi_master.id')
												->get();
		}



		return DataTables::of($data)->addIndexColumn()
				->addColumn('supplier', function($data){
					return Supplier::where('sup_id',$data->mr_supplier_sup_id)->first()->sup_name??'';
				})
				->addColumn('booking', function($data){
					$poDetails = PoBookingPi::where('mr_po_booking_pi.cm_pi_master_id',$data->cm_pi_master_id)
					->leftJoin('mr_po_booking As pb','pb.id','mr_po_booking_pi.mr_po_booking_id')
					->pluck('booking_ref_no');
					$poText = '';
					foreach($poDetails as $po) {
						$poText .= '<span class="label label-info arrowed-right arrowed-in">'.$po.'</span> <br>';
					}
					return $poText;
				})
				->addColumn('action', function($data){
					$action_buttons= "<div class=\"btn-group\">
					<a href=".url('merch/proforma_invoice/edit/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\">
					<i class=\"ace-icon fa fa-pencil bigger-120\"></i>
					</a>
					<a href=".url('merch/proforma_invoice/view/'.$data->id)." class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" title=\"View\">
					<i class=\"ace-icon fa fa-eye bigger-120\"></i>
					</a>
					<a href=".url('merch/proforma_invoice/delete/'.$data->id)." onclick=\"return confirm('Are you sure you want to delete this PI')\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"View\">
					<i class=\"ace-icon fa fa-trash bigger-120\"></i>
					</a> ";
					$action_buttons.= "</div>";
					return $action_buttons;
				})
				->rawColumns(['action','supplier','booking'])
				->toJson();
	}


	public function showForm()
	{
		try {
			$buyer_permissions = auth()->user()->buyer_permissions;
			$buyerOrderList = DB::table('mr_order_entry as a')->select([
									'bu.b_name',
									'bu.b_id'
								])
								->whereIn('a.mr_buyer_b_id', explode(',',$buyer_permissions))
								->leftJoin('mr_buyer as bu', 'bu.b_id', 'a.mr_buyer_b_id')
								->groupBy('bu.b_id')
								->pluck('bu.b_name','bu.b_id')->toArray();

		  	//$poBookingList = PoBooking::pluck('booking_ref_no','id')->toArray();
			$orderBomCostingBookingList = OrderBomCostingBooking::select([
												'mr_supplier_sup_id'
											])
											->get()->unique('mr_supplier_sup_id');
			//dd($orderBomCostingBookingList);

			$supplierList = [];
			foreach($orderBomCostingBookingList as $single) {
				if($single->supplier != null) {
					$supplierList[$single->mr_supplier_sup_id] = $single->supplier->sup_name;
				}
			}
			$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
			return view("merch.proforma_invoice.pi_booking", compact('supplierList','unitList','buyerOrderList'));
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getBookingList(Request $request)
	{
		try{
			return $this->getBookingListGlobal($request->unit_id,$request->buyer_id,$request->sup_id,[],'');

		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}


	public function getBookingListGlobal($unitId,$buyerId,$supId,$checkedBooking,$pi_id)
	{
		//dd($checkedBooking);
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
		} elseif (auth()->user()->hasRole('merchandising_executive')) {
			$executive_associateId[] = auth()->user()->associate_id;
			$team = $executive_associateId;
		} else {
			$team =[];
		}


		$bookingList = DB::table('mr_po_booking as pob')->select([
			'pob.*',
			'u.hr_unit_name',
			'bu.b_name',
			'su.sup_name',
			DB::raw('SUM(ob.booking_qty) as booking')
		])
		->where(function($query) use ($supId) {
			if($supId!=null){
				$query->where('pob.mr_supplier_sup_id', $supId);
			}
		})
		->where(function($query) use ($team) {
			if(!empty($team)){
				$query->whereIn('pob.created_by', $team);
			}
		})
		->where('pob.unit_id', $unitId)
		->where('pob.mr_buyer_b_id', $buyerId)
		->leftJoin('mr_supplier as su', 'su.sup_id', 'pob.mr_supplier_sup_id')
		->leftJoin('hr_unit as u', 'u.hr_unit_id', 'pob.unit_id')
		->leftJoin('mr_buyer as bu', 'bu.b_id', 'pob.mr_buyer_b_id')
		->leftJoin('mr_order_booking as ob', 'ob.mr_po_booking_id', 'pob.id')
		->groupBy('ob.mr_po_booking_id')
		->get();
		return view("merch.proforma_invoice.ajax_get_booking", compact('bookingList','supId','checkedBooking','pi_id'))->render();
	}
	public function getBookingItem(Request $request)
	{
		try {
			$booking_id= $request->booking_id;
			$pi_id = $request->pi_id??'';
			$BookingInfo = PoBooking::where('id',$booking_id)->first();
			$booking = DB::table('mr_order_booking As mob')
			->select(
				"mc.clr_name",
				"mob.*",
				"c.mcat_name",
				"c.mcat_id",
				"sz.mr_product_pallete_name",
				"i.item_name",
				"i.item_code",
				"i.id as item_id",
				"i.dependent_on",
				"s.sup_name",
				"a.art_name",
				"com.comp_name",
				"con.construction_name",
				"b.item_description",
				"b.uom",
				"b.consumption",
				"b.extra_percent",
				"b.precost_unit_price",
				"b.order_id",
				"o.order_code"
			)
			->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
			->leftJoin("mr_material_category AS c", function($join) {
				$join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
			})
			->leftJoin("mr_cat_item AS i", function($join) {
				$join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
				$join->on("i.id", "=", "b.mr_cat_item_id");
			})
			->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
			->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
			->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
			->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
			->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
			->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
			->leftJoin("mr_order_entry AS o","o.order_id","b.order_id")
			->where('mr_po_booking_id', $booking_id)
			->orderBy("mob.id")
			->get();
			$bookColl= collect($booking)->groupBy('mr_order_bom_costing_booking_id', true);
			$bookCo=collect($bookColl)->groupBy('mr_cat_item_mcat_id', true);

			return view('merch.proforma_invoice.get_items',
				compact('bookCo','BookingInfo','pi_id'))->render();

		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getBomBookingItemForEdit($booking_id,$masterId,$view)
	{
		try {
			$checkForwarding = PiForwardDetails::where('cm_pi_master_id',$masterId)->first();
			$readOnly = '';
			if($checkForwarding){
				$readOnly = 'readonly="readonly"';
			}
			//$booking_id= $request->booking_id;
			$BookingInfo = PoBooking::where('id',$booking_id)->first();
			$booking = DB::table('mr_order_booking As mob')
			->select(
				"mc.clr_name",
				"mob.booking_qty",
				"mob.size",
				"sz.mr_product_pallete_name",
				"mob.id",
				"c.mcat_name",
				"c.mcat_id",
				"i.item_name",
				"i.item_code",
				"i.id as item_id",
				"i.dependent_on",
				"s.sup_name",
				"a.art_name",
				"com.comp_name",
				"con.construction_name",
				"b.item_description",
				"b.uom",
				"b.consumption",
				"b.extra_percent",
				"b.precost_unit_price",
				"cpb.currency",
				"cpb.pi_qty",
				"cpb.shipped_date",
				"cpb.mr_po_booking_id",
				"mob.mr_order_bom_costing_booking_id",
				"mob.mr_cat_item_id",
				"b.order_id",
				"o.order_code",
				"up.id as up_id",
				"up.unit_price"
			)
			->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
			->leftJoin("mr_material_category AS c", function($join) {
				$join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
			})
			->leftJoin("mr_cat_item AS i", function($join) {
				$join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
				$join->on("i.id", "=", "b.mr_cat_item_id");
			})
			->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
			->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
			->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
			->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
			->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
			->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
			->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
			->leftJoin("mr_order_entry AS o","o.order_id","b.order_id")
			->leftJoin("mr_pi_item_unit_price AS up", function($join) {
				$join->on("up.mr_po_booking_id", "=", "cpb.mr_po_booking_id");
				$join->on("up.mr_cat_item_id", "=", "b.mr_cat_item_id");
				$join->on("up.cm_pi_master_id", "=", "cpb.cm_pi_master_id");
			})
			->where('cpb.mr_po_booking_id', $booking_id)
			->where('cpb.cm_pi_master_id',$masterId)
			->orderBy("mob.id")
			->get();
			$bookColl= collect($booking)->groupBy('mr_order_bom_costing_booking_id', true);
			$bookCo=collect($bookColl)->groupBy('mr_cat_item_id', true);


			//$item_price = MrPiItemUnitPrice::where('cm_pi_master_id',$masterId)
						  /*->where('mr_po_booking_id', $booking_id)
						  ->pluck('')*/

			//dd($bookCo);
		  	if($view=='edit')
		  	{
		  		return view('merch.proforma_invoice.get_items_edit',
		  		compact('bookCo','BookingInfo','readOnly'))->render();
		  	}else if($view=='view'){
		  		return view('merch.proforma_invoice.get_items_view',
		  		compact('bookCo','BookingInfo'))->render();
		  	}

		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}


	public function store(Request $request)
	{
		//dd($request->all());
		$validator= Validator::make($request->all(), [
			'pi_no'         => 'required',
			'pi_date'       => 'required',
			'pi_category'   => 'required',
			'pi_last_date'  => 'required',
			'ship_mode'     => 'required',
			'pi_status'     => 'required'
		]);
		//If Validation fails back to previous psage
		if($validator->fails()){
			return back()
			->withInput()
			->with('error', "Please fill the required fields!!");
		}
		try {
			$piMaster = new CmPiMaster();
			$piMaster->mr_supplier_sup_id = $request->mr_supplier_sup_id;
			$piMaster->mr_buyer_b_id = $request->mr_buyer_b_id;
			$piMaster->unit_id = $request->unit_id;
			$piMaster->pi_no = $request->pi_no;
			$piMaster->pi_date = $request->pi_date;
			$piMaster->pi_category = $request->pi_category;
			$piMaster->pi_last_date = $request->pi_last_date;
			$piMaster->ship_mode = $request->ship_mode;
			$piMaster->pi_status = $request->pi_status;
			$piMaster->total_pi_qty = $request->total_pi_qty;
			$piMaster->total_pi_value = $request->total_pi_value;
			$piMaster->save();
			//Iif pi master saved
			if($piMaster->id){
				if(count($request->booking)>0){
					foreach ($request->booking as $key => $po) {
						$poBookingPi = new PoBookingPi();
						$poBookingPi->cm_pi_master_id = $piMaster->id;
						$poBookingPi->mr_po_booking_id = $key;
						$poBookingPi->save();
						foreach ($po as $key1 => $booking) {
							$piBom = new CmPiBom();
							$piBom->mr_order_booking_id = $booking['id'];
							$piBom->mr_order_entry_order_id = $booking['order_id'];
							$piBom->currency = $booking['currency'];
							$piBom->pi_qty = $booking['pi_qty'];
							$piBom->shipped_date = $booking['shipped_date'];
							$piBom->cm_pi_master_id = $piMaster->id;
							$piBom->mr_po_booking_id = $key;
							$piBom->save();

						}
					}
				}
				if(count($request->item_price)>0){
					foreach ($request->item_price as $booking => $items) {
						foreach ($items as $item => $uprice) {
							$price = new MrPiItemUnitPrice();
							$price->mr_po_booking_id = $booking;
							$price->mr_cat_item_id = $item;
							$price->cm_pi_master_id = $piMaster->id;
							$price->unit_price = $uprice;
							$price->save();
						}

					}
				}

				$this->logFileWrite("Proforma Invoice created", $piMaster->id);
				return redirect('merch/proforma_invoice/edit/'.$piMaster->id)->with('success', "Proforma Invoice Created successfully!!");

			}else{
				return back()->withInput()->with('error', 'Proforma Invoice not saved');
			}

		}
		catch(\Exception $e) {
			return back()->withInput()->with('error', $e->getMessage());
		}
	}
	public function edit($pi_id)
	{
		try {
			$checkForwarding = PiForwardDetails::where('cm_pi_master_id',$pi_id)->first();
			$forMsg = '';
			if($checkForwarding){
				$forMsg = '<span style="color:#ff0000;">* PI Forwarded to BTB. You can not change PI Qty.</span>' ;
			}

			$checkedBooking = PoBookingPi::where('cm_pi_master_id',$pi_id)->pluck('mr_po_booking_id')->toArray();
			$piMaster = CmPiMaster::find($pi_id);
			if($piMaster != null){
				$bookingTable = $this->getBookingListGlobal($piMaster->unit_id,$piMaster->mr_buyer_b_id, $piMaster->mr_supplier_sup_id, $checkedBooking,$pi_id);
				//dd($checkedBooking);
				$itemTable = '';
				foreach ($checkedBooking as $key => $value) {
					$itemTable .= $this->getBomBookingItemForEdit($value,$pi_id,'edit');
				}

				//dd($itemTable);


				$buyerList = Buyer::where('b_id',$piMaster->mr_buyer_b_id)->pluck('b_name','b_id');
				$supplierList = Supplier::where('sup_id',$piMaster->mr_supplier_sup_id)->pluck('sup_name','sup_id');
				$unitList= Unit::where('hr_unit_id',$piMaster->unit_id)->pluck('hr_unit_name', 'hr_unit_id');
				return view("merch.proforma_invoice.pi_booking_edit", compact('pi_id','supplierList','unitList','buyerList', 'piMaster','bookingTable','checkedBooking','itemTable','forMsg'));
			}else{
				return back()->with('error', 'PI Not Found');
			}
			
		} catch(\Exception $e) {
			return back();
		}
	}

	public function delete($pi_id)
	{
		try {
			CmPiMaster::where('id',$pi_id)->delete();
			PoBookingPi::where('cm_pi_master_id',$pi_id)->delete();
			CmPiBom::where('cm_pi_master_id',$pi_id)->delete();
			return redirect('merch/proforma_invoice/')->with('success', "Proforma Invoice Deleted successfully!!");
		} catch(\Exception $e) {
			$bug = $e->getMessage();
			return back()->with('error', $bug);
		}
	}

	public function view($pi_id)
	{
		try {
			$checkedBooking = PoBookingPi::where('cm_pi_master_id',$pi_id)->pluck('mr_po_booking_id')->toArray();
			$piMaster = CmPiMaster::find($pi_id);
			if($piMaster != null){
				$itemTable = '';
				foreach ($checkedBooking as $key => $value) {
					$itemTable .= $this->getBomBookingItemForEdit($value,$pi_id,'view');
				}

				//dd($itemTable);

				$poDetails = PoBookingPi::where('mr_po_booking_pi.cm_pi_master_id',$pi_id)
				->leftJoin('mr_po_booking As pb','pb.id','mr_po_booking_pi.mr_po_booking_id')
				->pluck('booking_ref_no');

				$poText = '';
				foreach($poDetails as $po) {
					$poText .= '<span class="label label-info arrowed-right arrowed-in">'.$po.'</span> <br>';
				}

				$piMaster;
				$buyer = Buyer::where('b_id',$piMaster->mr_buyer_b_id)->first()->b_name??'';
				$supplier = Supplier::where('sup_id',$piMaster->mr_supplier_sup_id)->first()->sup_name??'';
				$unit= Unit::where('hr_unit_id',$piMaster->unit_id)->first()->hr_unit_name??'';

				return view("merch.proforma_invoice.pi_booking_view", compact('pi_id','supplier','unit','buyer', 'piMaster','checkedBooking','itemTable','poText'));
			}else{
				return back()->with('error', 'PI Not Found');
			}

			
		} catch(\Exception $e) {
			$bug = $e->getMessage();
			return back()->with('error', $bug);
		}
	}

	public function update(Request $request)
	{
		//dd($request->all());
		$validator= Validator::make($request->all(), [
			'pi_id'         => 'required',
			'pi_no'         => 'required',
			'pi_date'       => 'required',
			'pi_category'   => 'required',
			'pi_last_date'  => 'required',
			'ship_mode'     => 'required',
			'pi_status'     => 'required'
		]);
		//If Validation fails back to previous psage
		if($validator->fails()){
			return back()
			->withInput()
			->with('error', "Please fill the required fields!!");
		}
		try {
			$piMaster = CmPiMaster::find($request->pi_id);
			$piMaster->pi_no = $request->pi_no;
			$piMaster->pi_date = $request->pi_date;
			$piMaster->pi_category = $request->pi_category;
			$piMaster->pi_last_date = $request->pi_last_date;
			$piMaster->ship_mode = $request->ship_mode;
			$piMaster->pi_status = $request->pi_status;
			$piMaster->total_pi_qty = $request->total_pi_qty;
			$piMaster->total_pi_value = $request->total_pi_value;
			$piMaster->save();
			//dd( $piMaster->id);

			$cpb_booking_id = CmPiBom::where('cm_pi_master_id',$request->pi_id)
								->pluck('mr_po_booking_id')
								->unique()->values()->toArray();
			$old_booking_id = [];
			if(isset($request->old_booking_id)){
				$old_booking_id = $request->old_booking_id;
			}
			// delete unchecked items
			$to_delete = array_diff($cpb_booking_id,$old_booking_id);

			if(count($to_delete)>0){
				foreach ($to_delete as $key => $val) {
					CmPiBom::where([
						'mr_po_booking_id'=> $val,
						'cm_pi_master_id' => $request->pi_id
					])->delete();
					$bookingUpdate = PoBookingPi::where([
						'mr_po_booking_id'=> $val,
						'cm_pi_master_id' => $request->pi_id
					])->delete();
				}
			}

			// update items

			$to_update = array_diff($old_booking_id,$to_delete );
			if(count($to_update)>0){
				foreach ($to_update as $key => $val) {
					///dd($request->booking[$val]);
					foreach ($request->booking[$val] as $key1 => $booking) {
						//dd($booking['id']);
						$aa = DB::table('cm_pi_bom')
						->where('mr_order_booking_id',$booking['id'])
						->where('cm_pi_master_id',$request->pi_id)
						->update([
							'currency' => $booking['currency'],
							'pi_qty' => $booking['pi_qty'],
							'shipped_date' => $booking['shipped_date']
						]);
					}
				}

			}

			//insert new checked items
			if(isset($request->booking_id)){
				//dd($request->booking_id);
				foreach ($request->booking_id as $key => $po) {
					$poBookingPi = new PoBookingPi();
					$poBookingPi->cm_pi_master_id = $piMaster->id;
					$poBookingPi->mr_po_booking_id = $po;
					$poBookingPi->save();
					foreach ($request->booking[$po] as $key1 => $booking) {
						$piBom = new CmPiBom();
						$piBom->mr_order_booking_id = $booking['id'];
						$piBom->mr_order_entry_order_id = $booking['order_id'];
						$piBom->currency = $booking['currency'];
						$piBom->pi_qty = $booking['pi_qty'];
						$piBom->shipped_date = $booking['shipped_date'];
						$piBom->cm_pi_master_id = $piMaster->id;
						$piBom->mr_po_booking_id = $po;
						$piBom->save();
				//dd($piBom );
					}
				}
			}

			MrPiItemUnitPrice::where('cm_pi_master_id',$request->pi_id)->delete();
			if(count($request->item_price)>0){
				foreach ($request->item_price as $booking => $items) {
					foreach ($items as $item => $uprice) {
						$price = new MrPiItemUnitPrice();
						$price->mr_po_booking_id = $booking;
						$price->mr_cat_item_id = $item;
						$price->cm_pi_master_id = $request->pi_id;
						$price->unit_price = $uprice;
						$price->save();
					}

				}
			}

			return redirect('merch/proforma_invoice/edit/'.$request->pi_id)->with('success', "Proforma Invoice Updated successfully!!");

		}
		catch(\Exception $e) {
			return back()->withInput()->with('error', $e->getMessage());
		}
	}

	public function checkPi(Request $request)
	{
		$status = 'no';
		try {
			$getPi = CmPiMaster::where('pi_no',$request->pi_no)->first();
			if($getPi){
				$status = 'yes';
			}
			return $status;
		} catch (\Exception $e) {
			return $status;
		}
	}
	public function logFileWrite($message, $event_id)
	{
		$log_message = date("Y-m-d H:i:s")." ".Auth()->user()->associate_id." \"".$message."\" ".$event_id.PHP_EOL;
		$log_message .= file_get_contents("assets/log.txt");
		file_put_contents("assets/log.txt", $log_message);
	}

}
