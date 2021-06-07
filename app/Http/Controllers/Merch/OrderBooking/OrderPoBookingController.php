<?php

namespace App\Http\Controllers\Merch\OrderBooking;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\MrOrderBooking;
use App\Models\Merch\MrPoBomCostingBooking;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomCostingBooking;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\PoBooking;
use App\Models\Merch\PoBookingDetail;
use App\Models\Merch\ProductType;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use App\Models\Upazilla;
use DB,Validator, ACL, DataTables, Form;
use Illuminate\Http\Request;


use App\Repository\Merch\PoBomRepository;

class OrderPoBookingController extends Controller
{

  	private $poBomRepository;

    public function __construct(PoBomRepository  $poBomRepository)
    {
       	$this->poBomRepository =  $poBomRepository;
    }

	public function showList()
	{
		try {

			$unitList = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id');
			$buyerList = collect(buyer_by_id())->pluck('b_name', 'b_id');

			$prdtypList = ProductType::pluck('prd_type_name', 'prd_type_id');

			return view("merch.order_booking.order_po_booking.order_po_booking_list", compact('unitList','buyerList','prdtypList'));

		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getSupOrderList(Request $request)
	{

/*	    $supplier_order_list = DB::select (DB::raw("select b.order_code,
       c.item_name,
       ms.sup_name,
       a.precost_req_qty,
       b.order_delivery_date,
       a.order_id,
       a.mr_supplier_sup_id,
       b.unit_id
From mr_order_bom_costing_booking  a ,
          mr_order_entry  b,
          mr_supplier ms ,
          mr_cat_item  c
where b.order_id = a.order_id
  and c.id = a.mr_cat_item_id
  and ms.sup_id=a.mr_supplier_sup_id
  and a.mr_supplier_sup_id = ". $request->sup_id));

        return ['result' => view('merch.order_booking.order_po_booking.ajax_get_supplier_order', compact('supplier_order_list'))->render()];*/

		$team =[];

		$unitId = $request->unit_id;
		$orderId = $request->order_id;
		$buyerId  = $request->buyer_id;
		$supId  = $request->sup_id;
		$exOrderList  = $request->ex_order_list;

		$buyerOrderList = DB::table('mr_order_entry as a')->select([
			'a.order_id',
			'a.order_code',
			'a.unit_id',
			'b.mr_supplier_sup_id',
			'a.mr_buyer_b_id',
			'a.mr_style_stl_id',
			'a.order_qty',
			'a.order_delivery_date',
			'a.order_status',
			'b.mr_style_stl_id',
			'b.mr_material_category_mcat_id',
			'b.id as cos_book_id',
			'b.mr_cat_item_id',
			'b.clr_id',
			'b.delivery_date',
			'b.po_no',
			'u.hr_unit_name',
			'bu.b_name',
			'su.sup_name',
			'it.item_name'
		])
		->when($unitId, function($query, $unitId) {
			return $query->where('a.unit_id',$unitId);
		})
		->when($buyerId, function($query, $buyerId) {
			return $query->where('a.mr_buyer_b_id',$buyerId);
		})
		->when($orderId, function($query, $orderId) {
			return $query->where('a.order_id',$orderId);
		})
		->whereNotIn('a.order_status',['Completed','Inactive'])
		->join('mr_order_bom_costing_booking as b', function($join) use($supId) {
			$join->on('a.order_id','=','b.order_id');
			if($supId != null) {
				$join->where('b.mr_supplier_sup_id','=',$supId);
			}
		})
		->leftJoin('mr_cat_item as it', 'it.id', 'b.mr_cat_item_id')
		->leftJoin('mr_supplier as su', 'su.sup_id', 'b.mr_supplier_sup_id')
		->leftJoin('hr_unit as u', 'u.hr_unit_id', 'a.unit_id')
		->leftJoin('mr_buyer as bu', 'bu.b_id', 'a.mr_buyer_b_id')
		->when(!empty($team), function($query, $orderId) {
			return $query->whereIn('a.order_id',$orderId);
		})
		->groupBy('a.order_id')
		->orderBy('a.order_delivery_date','ASC')
		->get();

		$orderListData = DB::table('mr_order_entry')->select([
			'order_id',
			'order_code'
		])
		->when($unitId, function($query, $unitId) {
			return $query->where('unit_id',$unitId);
		})
		->when($buyerId, function($query, $buyerId) {
			return $query->where('mr_buyer_b_id',$buyerId);
		})
		->pluck('order_code','order_id')
		->toArray();
		$orderList = '<option value="">Select Order</option>';
		foreach($orderListData as $oId=>$oOrder) {
			$orderList .= '<option value="'.$oId.'">';
			$orderList .= $oOrder;
			$orderList .= '</option>';
		}

		return ['orderList' => $orderList, 'result' => view("merch.order_booking.order_po_booking.ajax_get_supplier_order", compact('buyerOrderList','exOrderList','supId','orderId'))->render()];

	}

	public function showForm()
	{
		try {

			$orderList = DB::table('mr_order_entry as a')
				->whereNotIn('a.order_status',['Completed','Inactive'])
				->pluck('order_code','order_id')->toArray();

			$buyer_permissions = auth()->user()->buyer_permissions;
			$buyerOrderList = DB::table('mr_order_entry as a')
				->select([
					'bu.b_name',
					'bu.b_id'
				])
				->whereIn('a.mr_buyer_b_id', explode(',',$buyer_permissions))
				->join('mr_buyer as bu', 'bu.b_id', 'a.mr_buyer_b_id')
				->groupBy('bu.b_id')
				->pluck('bu.b_name','bu.b_id')->toArray();

			$poBookingList = PoBooking::pluck('booking_ref_no','id')->toArray();

			$orderBomCostingBookingList = OrderBomCostingBooking::select([
				'mr_supplier_sup_id',
				'order_id',
				'po_no'
			])
			->groupBy('mr_supplier_sup_id')->get();

			$supplierList = [];
			foreach($orderBomCostingBookingList as $single) {
				if($single->supplier != null) {
					$supplierList[$single->mr_supplier_sup_id] = $single->supplier->sup_name;
				}
			}

			$unitList = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id');

			return view("merch.order_booking.order_po_booking.order_po_booking_form", compact('supplierList','unitList','poBookingList','buyerOrderList','orderList'));
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

		// for generate uniqe referance number for po booking table
		//
	public function encode($string)
	{
		$ans = array();
		$string = str_split($string);
				#go through every character, changing it to its ASCII value
		for ($i = 0; $i < count($string); $i++) {
						#ord turns a character into its ASCII values
			$ascii = (string) ord($string[$i]);
						#make sure it's 3 characters long
			if (strlen($ascii) < 1)
				$ascii = '0'.$ascii;
			$ans[] = $ascii;
		}
				#turn it into a string
		return implode('', $ans);
	}

	public function store(Request $request)
	{
	    //dd($request->all());
		try {
			$costingBookingIdList = $request->mr_order_bom_costing_booking_id;
			$orderIdOrderWise = $request->order_id;
			$supId = $request->mr_supplier_sup_id;
			$supplierList = $request->mr_supplier;
			$buyerId = $request->mr_buyer_b_id;
			$unitId = $request->unit_id;
			$deliveryDate = $request->delivery_date;
			$orderIdList = $request->mr_order_entry_order_id;
			$orderPoIdList = $request->mr_purchase_order_po_id;
			$orderClrList = $request->mr_material_color_clr_id;
			$orderSizeList = $request->size;
			$orderQtyList = $request->qty;
			$orderReQtyList = $request->req_qty;
			$orderBoQtyList = $request->booking_qty;
			$result = [];
			$poTable = [];
			$poTableDetailC = [];
			$poTableDetailS = [];
			$poTableDetailCS = [];
			$poTableDetailN = [];
			if($costingBookingIdList != null) {
				foreach($supplierList as $supplier_id) {
					$poBookingTable = PoBooking::get();
										// insert first time
					if($poBookingTable->isEmpty()) {
						$poTable['booking_ref_no'] = $this->encode($supplier_id);
					} else {
						$poBookingLastRow = DB::table('mr_po_booking')->orderBy('id', 'desc')->first();
						$poTable['booking_ref_no'] = $this->encode($poBookingLastRow->id.$supplier_id);
					}
					$poTable['mr_supplier_sup_id'] = $supplier_id;
					$poTable['mr_buyer_b_id'] = $buyerId;
					$poTable['unit_id'] = $unitId;
					$poTable['delivery_date'] = $deliveryDate;
					$poTable['created_by'] = auth()->user()->associate_id;
					$poTableId = PoBooking::insertGetId($poTable);

										// loop one
                    foreach($costingBookingIdList  as $costingBookingIdK=>$costingBookingId) {
												// loop two
						foreach($orderPoIdList[$costingBookingId] as $orderIdK=>$orderList) {
														// loop three
							foreach($orderIdList[$orderIdK][$supplier_id] as $poIdK=>$cosBookingIdList) {
																// loop four
								foreach($cosBookingIdList as $cosBookingIdListK=>$dependOn){

									if($dependOn == 1) {
																				// $result2[$cosBookingIdListK][$orderIdK][$poIdK][] = $dependOn;
										if(!isset($result[$cosBookingIdListK][$supplier_id][$poIdK])) {
																				// isset one
											$poTableDetailC['mr_order_entry_order_id'] = $orderIdK;
											$poTableDetailC['mr_purchase_order_po_id'] = $poIdK;
											$poTableDetailC['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
																						// po details table ref id
											$poTableDetailC['mr_po_booking_id'] = $poTableId;
																						// isset two
											if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK])){
												foreach($orderClrList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK] as $clrIdK=>$clrId) {
													$poTableDetailC['mr_material_color_clr_id'] = $clrId;
													$poTableDetailC['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId];
													$poTableDetailC['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId];
													$poTableDetailC['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId];
													$poTableDetailC['created_by'] = auth()->user()->associate_id;
													$result[$cosBookingIdListK][$supplier_id][$poIdK][] = $poTableDetailC;
													PoBookingDetail::insert($poTableDetailC);
												}
											}
																						// end isset two
										}
																				// end isset one
									} else if($dependOn == 2) {
																				// $result2[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][] = $dependOn;
										if(!isset($result[$cosBookingIdListK][$supplier_id][$poIdK])) {
																				// isset one
											$poTableDetailS['mr_order_entry_order_id'] = $orderIdK;
											$poTableDetailS['mr_purchase_order_po_id'] = $poIdK;
											$poTableDetailS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
																						// po details table ref id
											$poTableDetailS['mr_po_booking_id'] = $poTableId;
																						// isset two
											if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK])){
												foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK] as $sizeIdK=>$sizeName) {
													$poTableDetailS['size'] = $sizeIdK;
													$poTableDetailS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$sizeIdK];
													$poTableDetailS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$sizeIdK];
													$poTableDetailS['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$sizeIdK];
													$poTableDetailS['created_by'] = auth()->user()->associate_id;
													$result[$cosBookingIdListK][$supplier_id][$poIdK][] = $poTableDetailS;
													PoBookingDetail::insert($poTableDetailS);
												}
											}
																						// end isset two
										}
																				// end isset one
									} else if($dependOn == 3) {
																				// $poTableDetail[$orderIdK][$supplier_id][$poIdK][] = $dependOn;
										if(!isset($result[$cosBookingIdListK][$supplier_id][$poIdK])) {
																				// isset one
											$poTableDetailCS['mr_order_entry_order_id'] = $orderIdK;
											$poTableDetailCS['mr_purchase_order_po_id'] = $poIdK;
											$poTableDetailCS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
																						// po details table ref id
											$poTableDetailCS['mr_po_booking_id'] = $poTableId;
																						// isset two
											if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK])){
												foreach($orderClrList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK] as $clrIdK=>$clrId) {
													$poTableDetailCS['mr_material_color_clr_id'] = $clrId;
																										// isset three
													if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId])){
														foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId] as $sizeIdK=>$sizeName) {
															$poTableDetailCS['size'] = $sizeIdK;
															$poTableDetailCS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId][$sizeIdK];
															$poTableDetailCS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId][$sizeIdK];
															$poTableDetailCS['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][$clrId][$sizeIdK];
															$poTableDetailCS['created_by'] = auth()->user()->associate_id;
															$result[$cosBookingIdListK][$supplier_id][$poIdK][] = $poTableDetailCS;
															PoBookingDetail::insert($poTableDetailCS);
														}
													}
																										// end isset three
												}
											}
																						// end isset two
										}
																				// end isset one
									} else if($dependOn == 0) {
																				// $result2[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][] = $dependOn;

                                    if(!isset($result[$cosBookingIdListK][$supplier_id][$poIdK])) {
																				// isset one
											$poTableDetailN['mr_order_entry_order_id'] = $orderIdK;
											$poTableDetailN['mr_purchase_order_po_id'] = $poIdK;
																						// po details table ref id
											$poTableDetailN['mr_po_booking_id'] = $poTableId;
											$poTableDetailN['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
											$poTableDetailN['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][0];
											$poTableDetailN['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][0];
											$poTableDetailN['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$supplier_id][$poIdK][0];
											$poTableDetailN['created_by'] = auth()->user()->associate_id;
											$result[$cosBookingIdListK][$supplier_id][$poIdK][] = $poTableDetailN;
											PoBookingDetail::insert($poTableDetailN);
										}
																				// end isset one
									}
								}
																// end loop four
							}
														// end loop three
						}
												// end loop two
					}
					$orderWiseBookingCheck = [];
					if($orderIdOrderWise != null) {
						$orderWiseBookingCheck[] = $this->oneClickOrderWiseBookingConfirm($poTableId);
					}
				}
								// end loop one
				if($orderIdOrderWise != null) {
					return redirect('merch/order_po_booking')->with('success','Order place success.');
				} else {
					return redirect('merch/order_po_booking/confirm/'.$poTableId)->with('success','Please Confirm Purchase Order.');
				}
			} else {
				return redirect()->back()->with('error','No purchase order found.');
			}
						// dd($poTable, $poTableDetail);
		} catch(\Exception $e) {
			$bug = $e->getMessage();
			return $bug;
			return redirect()->back()->with('error',$e->getMessage());
		}
	}

	public function oneClickOrderWiseBookingForm($order,$boms,$colors,$sizes,$care_label,$filter,$poSizeQtyList,$poSizeQtyListC,$poSizeQtyListS,$poList, $itemUnique, $catCount,$poSizeQtyListN,$poSizeQtyListCN,$poSizeQtyListSN,$poBookingId,$poColorQtyList,$poColorQtyListC,$poColorQtyListN,$poColorQtyListCN)
	{


		$request = [];
		$request['mr_po_booking_id'] = $poBookingId;
		foreach($itemUnique as $item) {
			$itemIndex = 0;
			foreach($boms as $bom) {
				if($bom->item_name == $item) {
					$ptotal = ($bom->consumption * $bom->extra_percent)/100;
					$total  = $ptotal + $bom->consumption;
					if($bom->po_pos_cid != null) {
						$color_ids = [];
						$color_ids = array_keys($poSizeQtyListC[$bom->po_no]);
					}
					if($bom->depends_on == 1) {
						foreach($colors as $color) {
							if($bom->po_pos_cid != null) {
								if(in_array($color->clr_id, $color_ids)) {
									$posSubTotalQty = 0;
									if(isset($poColorQtyList[$bom->po_pos_cid])){
										foreach($poColorQtyList[$bom->po_pos_cid] as $poSizeQtyListSingle) {
											$posSubTotalQty += array_sum($poSizeQtyListSingle);
										}
									}
								}
							} else {
								$posSubTotalQty = 0;
								if(isset($poColorQtyListCN[$bom->order_id])){
									foreach($poColorQtyListCN[$bom->order_id][$color->clr_id] as $poSizeQtyListSingle) {
										$posSubTotalQty += $poSizeQtyListSingle;
									}
								}
							}

														// {{-- 0 check remaining --}}
							$posSubTotalQtyData = Custom::getOrderBookingReQtyColor($bom->id,$bom->item_id,$color->clr_id);
							$posSubTotalQtyRemainCheck = '';
							if(!empty($posSubTotalQtyData)) {
								$posSubTotalQtyRemainCheck = $posSubTotalQtyData['reqQty']->req_qty - $posSubTotalQtyData['bookingQty'];
								$posSubTotalQtyRemainCheck = Custom::fixedNumber($posSubTotalQtyRemainCheck,2,true);
							}
							if(is_numeric($posSubTotalQtyRemainCheck)) {
								$posSubTotalQtyRemainCheck = (int)$posSubTotalQtyRemainCheck <= 0?0:$posSubTotalQtyRemainCheck;
							} else {
								$posSubTotalQtyRemainCheck = (int)$posSubTotalQty <= 0?0:$posSubTotalQty;
							}
														// {{-- 0 check --}}
							if($posSubTotalQtyRemainCheck != 0) {
								$request['mr_order_bom_costing_booking_id'][$bom->id] = $bom->id;
								$request['mr_order_entry_order_id'][$bom->order_id][$bom->item_id][$bom->id] = $bom->depends_on;
								$request['mr_cat_item_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->item_id;
								$request['mr_cat_item_mcat_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->mcat_id;

								if($bom->po_pos_cid != null) {
									if(in_array($color->clr_id, $color_ids)) {
										$request['mr_material_color_clr_id'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $color->clr_id;
									}
								} else {
									$request['mr_material_color_clr_id'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $color->clr_id;
								}

																// total qty ========
								$posSubTotalQty = Custom::fixedNumber($posSubTotalQty,2,true);
								$request['qty'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $posSubTotalQty;

																// total required qty ========
								$posSubTotalQtyData = Custom::getOrderBookingReQtyColor($bom->id,$bom->item_id,$color->clr_id);
								$posSubTotalQtyRemain = '';
								if(!empty($posSubTotalQtyData)) {
									$posSubTotalQtyRemain = $posSubTotalQtyData['reqQty']->req_qty - $posSubTotalQtyData['bookingQty'];
									$posSubTotalQtyRemain = Custom::fixedNumber($posSubTotalQtyRemain,2,true);

									$posSubTotalQty = $posSubTotalQtyData['reqQty']->req_qty;
									$posSubTotalQty = Custom::fixedNumber($posSubTotalQty,2,true);
								} else {
									$posSubTotalQty = Custom::fixedNumber(($posSubTotalQty * $total),2,true);
								}
								$request['req_qty'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $posSubTotalQty;

																// total booking qty ========
								$posSubTotalQty = Custom::getOrderBookingQtyColor($poBookingId, $bom->id,$bom->item_id,$color->clr_id);
								$posSubTotalQtyDataZero = 1;
								if(gettype($posSubTotalQty) == 'double') {
									if((int)$posSubTotalQty == 0) {
										$posSubTotalQtyDataZero = 0;
									}
								}
								if(empty($posSubTotalQty) && $posSubTotalQtyDataZero==1) {
									$posSubTotalQty = 0;
									if($bom->po_pos_cid != null) {
										if(isset($poSizeQtyList[$bom->po_pos_cid])){
											foreach($poSizeQtyList[$bom->po_pos_cid] as $poSizeQtyListSingle) {
												foreach($poSizeQtyListSingle as $poSizeQtyListSingleS) {
													$posSubTotalQty += array_sum($poSizeQtyListSingleS);
												}
											}
										}
									} else {
										if(isset($poSizeQtyListCN[$bom->order_id])){
											foreach($poSizeQtyListCN[$bom->order_id][$color->clr_id] as $poSizeQtyListSingle) {
												$posSubTotalQty += array_sum($poSizeQtyListSingle);
											}
										}
									}
									$posSubTotalQty = $posSubTotalQty * $total;
								}
																// check remaining
								if(gettype($posSubTotalQtyRemain) == 'double') {
									if((int)$posSubTotalQtyRemain == 0) {
										$posSubTotalQty = 0;
									} else {
										$posSubTotalQty = $posSubTotalQtyRemain;
									}
								}
								$posSubTotalQty = Custom::fixedNumber($posSubTotalQty,2,true);
								$request['booking_qty'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $posSubTotalQty;

																// total value ========
								$posSubTotalQty = Custom::getOrderBookingValueQtyColor($poBookingId, $bom->id,$bom->item_id,$color->clr_id);
								$posSubTotalQtyDataZero = 1;
								if(gettype($posSubTotalQty) == 'double') {
									if((int)$posSubTotalQty == 0) {
										$posSubTotalQtyDataZero = 0;
									}
								}
								if(empty($posSubTotalQty) && $posSubTotalQtyDataZero == 1) {
									$posSubTotalQty = 0;
									if($bom->po_pos_cid != null) {
										if(isset($poSizeQtyList[$bom->po_pos_cid])){
											foreach($poSizeQtyList[$bom->po_pos_cid] as $poSizeQtyListSingle) {
												foreach($poSizeQtyListSingle as $poSizeQtyListSingleS) {
													$posSubTotalQty += array_sum($poSizeQtyListSingleS);
												}
											}
										}
									} else {
										if(isset($poSizeQtyListCN[$bom->order_id])){
											foreach($poSizeQtyListCN[$bom->order_id][$color->clr_id] as $poSizeQtyListSingle) {
												$posSubTotalQty += array_sum($poSizeQtyListSingle);
											}
										}
									}
									$posSubTotalQty = ($posSubTotalQty * $total) * $bom->precost_unit_price;
								}
																// check remaining
								if(gettype($posSubTotalQtyRemain) == 'double') {
									if((int)$posSubTotalQtyRemain == 0) {
										$posSubTotalQty = 0;
									} else {
										$posSubTotalQty = ($posSubTotalQtyRemain * $total) * $bom->precost_unit_price;
									}
								}
								$posSubTotalQty = Custom::fixedNumber($posSubTotalQty);
								$request['value'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $posSubTotalQty;
							}
						}
					} elseif($bom->depends_on == 2) {
						foreach($sizes as $size) {
							$poSizeQty = 0;
							if($bom->po_po_id != null){

								if(isset($poSizeQtyListS[$bom->po_po_id])){
									if(isset($poSizeQtyListS[$bom->po_po_id][$size->id])){
										$poSizeQty = array_sum($poSizeQtyListS[$bom->po_po_id][$size->id]);
									}
								}
							} else {
								if(isset($poSizeQtyListSN[$bom->order_id])){
									if(isset($poSizeQtyListSN[$bom->order_id][$size->id])){
										foreach($poSizeQtyListSN[$bom->order_id][$size->id] as $eachPoSizeQty) {
											$poSizeQty += array_sum($eachPoSizeQty);
										}
									}
								}
							}

													// {{-- 0 check remaining --}}
							$poSizeQtyData = Custom::getOrderBookingReQtySize($bom->id,$bom->item_id,$size->id);
							$posSizeRemainCheck = '';
							if(!empty($poSizeQtyData)) {
								$posSizeRemainCheck = $poSizeQtyData['reqQty']->req_qty - $poSizeQtyData['bookingQty'];
								$posSizeRemainCheck = Custom::fixedNumber($posSizeRemainCheck,2,true);
							}
							if(is_numeric($posSizeRemainCheck)) {
								$posSizeRemainCheck = (int)$posSizeRemainCheck <= 0?0:$posSizeRemainCheck;
							} else {
								$posSizeRemainCheck = (int)$poSizeQty <= 0?0:$poSizeQty;
							}
													// {{-- 0 check --}}
							if($posSizeRemainCheck != 0) {
								$request['mr_order_bom_costing_booking_id'][$bom->id] = $bom->id;
								$request['mr_order_entry_order_id'][$bom->order_id][$bom->item_id][$bom->id] = $bom->depends_on;
								$request['mr_cat_item_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->item_id;
								$request['mr_cat_item_mcat_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->mcat_id;

															// size ========
								$request['size'][$bom->id][$bom->order_id][$bom->item_id][$size->id] = $size->mr_product_pallete_name;

															// total qty ========
								$poSizeQty = Custom::fixedNumber($poSizeQty,2,true);
								$request['qty'][$bom->id][$bom->order_id][$bom->item_id][$size->id] = $poSizeQty;

															// total required qty ========
								$poSizeQtyData = Custom::getOrderBookingReQtySize($bom->id,$bom->item_id,$size->id);
								$posSizeRemain = '';
								if(!empty($poSizeQtyData)) {
									$poSizeQty = $poSizeQtyData['reqQty']->req_qty;
									$poSizeQty = Custom::fixedNumber($poSizeQty,2,true);

									$posSizeRemain = $poSizeQtyData['reqQty']->req_qty - $poSizeQtyData['bookingQty'];
									$posSizeRemain = Custom::fixedNumber($posSizeRemain,2,true);
								} else {
									$poSizeQty = Custom::fixedNumber(($poSizeQty * $total),2,true);
								}
								$request['req_qty'][$bom->id][$bom->order_id][$bom->item_id][$size->id] = $poSizeQty;

															// total booking qty ========
								$poSizeQty = Custom::getOrderBookingQtySize($poBookingId,$bom->id,$bom->item_id,$size->id);
								$poSizeQtyDataZero = 1;
								if(gettype($poSizeQty) == 'double') {
									if((int)$poSizeQty == 0) {
										$poSizeQtyDataZero = 0;
									}
								}
								if(empty($poSizeQty) && $poSizeQtyDataZero==1) {
									$poSizeQty = 0;
									if($bom->po_po_id != null){
										if(isset($poSizeQtyListS[$bom->po_po_id])){
											if(isset($poSizeQtyListS[$bom->po_po_id][$size->id])){
												$poSizeQty = array_sum($poSizeQtyListS[$bom->po_po_id][$size->id]);
											}
										}
									} else {
										if(isset($poSizeQtyListSN[$bom->order_id])){
											if(isset($poSizeQtyListSN[$bom->order_id][$size->id])){
												foreach($poSizeQtyListSN[$bom->order_id][$size->id] as $eachPoSizeQty) {
													$poSizeQty += array_sum($eachPoSizeQty);
												}
											}
										}
									}
									$poSizeQty = $poSizeQty * $total;
								}
															// check remaining
								if(gettype($posSizeRemain) == 'double') {
									if((int)$posSizeRemain == 0) {
										$poSizeQty = 0;
									} else {
										$poSizeQty = $posSizeRemain;
									}
								}
								$poSizeQty = Custom::fixedNumber($poSizeQty,2,true);
								$request['booking_qty'][$bom->id][$bom->order_id][$bom->item_id][$size->id] = $poSizeQty;

															// total value ========
								$poSizeQty = Custom::getOrderBookingValueQtySize($poBookingId,$bom->id,$bom->item_id,$size->id);
								$poSizeQtyDataZero = 1;
								if(gettype($poSizeQty) == 'double') {
									if((int)$poSizeQty == 0) {
										$poSizeQtyDataZero = 0;
									}
								}
								if(empty($poSizeQty) && $poSizeQtyDataZero==1) {
									$poSizeQty = 0;
									if($bom->po_po_id != null){
										if(isset($poSizeQtyListS[$bom->po_po_id])){
											if(isset($poSizeQtyListS[$bom->po_po_id][$size->id])){
												$poSizeQty = array_sum($poSizeQtyListS[$bom->po_po_id][$size->id]);
											}
										}
									} else {
										if(isset($poSizeQtyListSN[$bom->order_id])){
											if(isset($poSizeQtyListSN[$bom->order_id][$size->id])){
												foreach($poSizeQtyListSN[$bom->order_id][$size->id] as $eachPoSizeQty) {
													$poSizeQty += array_sum($eachPoSizeQty);
												}
											}
										}
									}
									$poSizeQty = ($poSizeQty * $total) * $bom->precost_unit_price;
								}
															// check remaining
								if(gettype($posSizeRemain) == 'double') {
									if((int)$posSizeRemain == 0) {
										$poSizeQty = 0;
									} else {
										$poSizeQty = ($posSizeRemain * $total) * $bom->precost_unit_price;
									}
								}
								$poSizeQty = Custom::fixedNumber($poSizeQty);
								$request['value'][$bom->id][$bom->order_id][$bom->item_id][$size->id] = $poSizeQty;
							}
						}
					} elseif($bom->depends_on == 3) {
						foreach($care_label as $color) {
							if($bom->po_pos_cid != null) {
								if(in_array($color->clr_id, $color_ids)) {
									$posSubTotalQtyCS = 0;
									if(isset($poSizeQtyListC[$bom->po_no][$color->clr_id])){
										foreach($poSizeQtyListC[$bom->po_no][$color->clr_id] as $clrId=>$pos_value) {
											foreach($pos_value as $sizId=>$pos_clr_value) {
												if(isset($pos_clr_value[$color->product_size_id])){
													$posSubTotalQtyCS += $pos_clr_value[$color->product_size_id];
												}
											}
										}
									}
								}
							} else {
								$posSubTotalQtyCS = 0;
								if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id])){
									if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id])){
										$posSubTotalQtyCS = array_sum($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id]);
									}
								}
							}

														// {{-- 0 check remaining --}}
							$posSubTotalQtyCSData = Custom::getOrderBookingReQtyColorSize($bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
							$posCSRemainCheck = '';
							if(!empty($posSubTotalQtyCSData)) {
								$posCSRemainCheck = $posSubTotalQtyCSData['reqQty']->req_qty - $posSubTotalQtyCSData['bookingQty'];
								$posCSRemainCheck = Custom::fixedNumber($posCSRemainCheck,2,true);
							}
							if(is_numeric($posCSRemainCheck)) {
								$posCSRemainCheck = (int)$posCSRemainCheck <= 0?0:$posCSRemainCheck;
							} else {
								$posCSRemainCheck = (int)$posSubTotalQtyCS <= 0?0:$posSubTotalQtyCS;
							}
														// {{-- 0 check --}}
							if($posSubTotalQtyCS != 0) {
								$request['mr_order_bom_costing_booking_id'][$bom->id] = $bom->id;
								$request['mr_order_entry_order_id'][$bom->order_id][$bom->item_id][$bom->id] = $bom->depends_on;
								$request['mr_cat_item_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->item_id;
								$request['mr_cat_item_mcat_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->mcat_id;

																// color =====
								if($bom->po_pos_cid != null) {
									if(in_array($color->clr_id, $color_ids)) {
										$request['mr_material_color_clr_id'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $color->clr_id;
									}
								} else {
									$request['mr_material_color_clr_id'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id] = $color->clr_id;
								}
																// size =====
								if($bom->po_pos_cid != null) {
									if(in_array($color->clr_id, $color_ids)) {
										$request['size'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id][$color->product_size_id] = $color->mr_product_pallete_name;
									}
								} else {
									$request['size'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id][$color->product_size_id] = $color->mr_product_pallete_name;
								}

																// total qty =====
								$posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);
								$request['qty'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id][$color->product_size_id] = $posSubTotalQtyCS;

																// total required qty =====
								$posSubTotalQtyCSData = Custom::getOrderBookingReQtyColorSize($bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
								$posCSRemain = '';
								if(!empty($posSubTotalQtyCSData)) {
									$posSubTotalQtyCS = $posSubTotalQtyCSData['reqQty']->req_qty;
									$posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);

									$posCSRemain = $posSubTotalQtyCSData['reqQty']->req_qty - $posSubTotalQtyCSData['bookingQty'];
									$posCSRemain = Custom::fixedNumber($posCSRemain,2,true);
								} else {
									$posSubTotalQtyCS = Custom::fixedNumber(($posSubTotalQtyCS * $total),2,true);
								}
								$request['req_qty'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id][$color->product_size_id] = $posSubTotalQtyCS;

																// total booking qty =====
								$posSubTotalQtyCS = Custom::getOrderBookingQtyColorSize($poBookingId,$bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
								$posSubTotalQtyCSDataZero = 1;
								if(gettype($posSubTotalQtyCS) == 'double') {
									if((int)$posSubTotalQtyCS == 0) {
										$posSubTotalQtyCSDataZero = 0;
									}
								}
								if(empty($posSubTotalQtyCS) && $posSubTotalQtyCSDataZero==1) {
									$posSubTotalQtyCS = 0;
									if($bom->po_pos_cid != null) {
										if(isset($poSizeQtyListC[$bom->po_no][$color->clr_id])){
											foreach($poSizeQtyListC[$bom->po_no][$color->clr_id] as $clrId=>$pos_value) {
												foreach($pos_value as $sizId=>$pos_clr_value) {
													if(isset($pos_clr_value[$color->product_size_id])){
														$posSubTotalQtyCS += $pos_clr_value[$color->product_size_id];
													}
												}
											}
										}
									} else {
										if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id])){
											if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id])){
												$posSubTotalQtyCS = array_sum($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id]);
											}
										}
									}
									$posSubTotalQtyCS = $posSubTotalQtyCS * $total;
								}
																// check remaining
								if(gettype($posCSRemain) == 'double') {
									if((int)$posCSRemain == 0) {
										$posSubTotalQtyCS = 0;
									} else {
										$posSubTotalQtyCS = $posCSRemain;
									}
								}
								$posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS,2,true);
								$request['booking_qty'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id][$color->product_size_id] = $posSubTotalQtyCS;

																// total value =====
								$posSubTotalQtyCS = Custom::getOrderBookingValueQtyColorSize($poBookingId,$bom->id,$bom->item_id,$color->clr_id,$color->product_size_id);
								$posSubTotalQtyCSDataZero = 1;
								if(gettype($posSubTotalQtyCS) == 'double') {
									if((int)$posSubTotalQtyCS == 0) {
										$posSubTotalQtyCSDataZero = 0;
									}
								}
								if(empty($posSubTotalQtyCS) && $posSubTotalQtyCSDataZero==1) {
									$posSubTotalQtyCS = 0;
									if($bom->po_pos_cid != null) {
										if(isset($poSizeQtyListC[$bom->po_no][$color->clr_id])){
											foreach($poSizeQtyListC[$bom->po_no][$color->clr_id] as $clrId=>$pos_value) {
												foreach($pos_value as $sizId=>$pos_clr_value) {
													if(isset($pos_clr_value[$color->product_size_id])){
														$posSubTotalQtyCS += $pos_clr_value[$color->product_size_id];
													}
												}
											}
										}
									} else {
										if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id])){
											if(isset($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id])){
												$posSubTotalQtyCS = array_sum($poSizeQtyListCN[$bom->order_id][$color->clr_id][$color->product_size_id]);
											}
										}
									}
									$posSubTotalQtyCS = ($posSubTotalQtyCS * $total) * $bom->precost_unit_price;
								}
																// check remaining
								if(gettype($posCSRemain) == 'double') {
									if((int)$posCSRemain == 0) {
										$posSubTotalQtyCS = 0;
									} else {
										$posSubTotalQtyCS = ($posCSRemain * $total) * $bom->precost_unit_price;
									}
								}
								$posSubTotalQtyCS = Custom::fixedNumber($posSubTotalQtyCS);
								$request['value'][$bom->id][$bom->order_id][$bom->item_id][$color->clr_id][$color->product_size_id] = $posSubTotalQtyCS;
							}
						}
					} elseif($bom->depends_on == 0) {
						$noDependTotalQty = 0;
						if($bom->po_no != null) {
							if(isset($poSizeQtyListS[$bom->po_no])){
								if(!empty($poSizeQtyListS[$bom->po_no])){
									foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
										$noDependTotalQty += array_sum($eachNoDependQty);
									}
								}
							}
						} else {
							$noDependTotalQty = $order->order_qty;
						}
						$itemPoCount = Custom::getItemPoCount($bom->id,$bom->order_id);
						$itemPoCount = !empty($itemPoCount)?count($itemPoCount):0;
						$noDependTotalQty = $noDependTotalQty*$itemPoCount;

												// {{-- 0 check remaining --}}
						$noDependTotalQtyData = Custom::getOrderBookingReQtyNoDepend($bom->id,$bom->item_id);
						$noDependTotalRemainCheck = '';
						if(!empty($noDependTotalQtyData)) {
							$noDependTotalRemainCheck = $noDependTotalQtyData['reqQty']->req_qty - $noDependTotalQtyData['bookingQty'];
							$noDependTotalRemainCheck = Custom::fixedNumber($noDependTotalRemainCheck,2,true);
						}
						if(is_numeric($noDependTotalRemainCheck)) {
							$noDependTotalRemainCheck = (int)$noDependTotalRemainCheck <= 0?0:$noDependTotalRemainCheck;
						} else {
							$noDependTotalRemainCheck = (int)$noDependTotalQty <= 0?0:$noDependTotalQty;
						}
												// {{-- 0 check --}}
						if($noDependTotalQty != 0) {
							$request['mr_order_bom_costing_booking_id'][$bom->id] = $bom->id;
							$request['mr_order_entry_order_id'][$bom->order_id][$bom->item_id][$bom->id] = $bom->depends_on;
							$request['mr_cat_item_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->item_id;
							$request['mr_cat_item_mcat_id'][$bom->id][$bom->order_id][$bom->item_id] = $bom->mcat_id;

														// qty ========
							$noDependTotalQty = Custom::fixedNumber($noDependTotalQty,2,true);
							$request['qty'][$bom->id][$bom->order_id][$bom->item_id][] = $noDependTotalQty;

														// required qty ========
							$noDependTotalQtyData = Custom::getOrderBookingReQtyNoDepend($bom->id,$bom->item_id);
							$noDependTotalRemain = '';
							if(!empty($noDependTotalQtyData)) {
								$noDependTotalQty = $noDependTotalQtyData['reqQty']->req_qty;
								$noDependTotalQty = Custom::fixedNumber($noDependTotalQty,2,true);

								$noDependTotalRemain = $noDependTotalQtyData['reqQty']->req_qty - $noDependTotalQtyData['bookingQty'];
								$noDependTotalRemain = Custom::fixedNumber($noDependTotalRemain,2,true);
							} else {
								$noDependTotalQty = Custom::fixedNumber(($noDependTotalQty * $total),2,true);
							}
							$request['req_qty'][$bom->id][$bom->order_id][$bom->item_id][] = $noDependTotalQty;

														// booking qty ========
							$noDependTotalQty = Custom::getOrderBookingQtyNoDepend($poBookingId,$bom->id,$bom->item_id);
							$noDependTotalQtyZero = 1;
							if(gettype($noDependTotalQty) == 'double') {
								if((int)$noDependTotalQty == 0) {
									$noDependTotalQtyZero = 0;
								}
							}
							if(empty($noDependTotalQty) && $noDependTotalQtyZero==1) {
								$noDependTotalQty = 0;
															// first time set default booking qty (required qty)
								if($bom->po_no != null) {
									if(isset($poSizeQtyListS[$bom->po_no])){
										if(!empty($poSizeQtyListS[$bom->po_no])){
											foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
												$noDependTotalQty += array_sum($eachNoDependQty);
											}
										}
									}
									$noDependTotalQty = $noDependTotalQty * $total;
								} else {
									$noDependTotalQty = $order->order_qty * $total;
								}
								$noDependTotalQty = $noDependTotalQty * $itemPoCount;
							}
														// check remaining
							if(gettype($noDependTotalRemain) == 'double') {
								if((int)$noDependTotalRemain == 0) {
									$noDependTotalQty = 0;
								} else {
									$noDependTotalQty = $noDependTotalRemain;
								}
							}
							$noDependTotalQty = Custom::fixedNumber($noDependTotalQty,2,true);
							$request['booking_qty'][$bom->id][$bom->order_id][$bom->item_id][] = $noDependTotalQty;

														// total value ========
							$noDependTotalQty = Custom::getOrderValueQtyNoDepend($poBookingId,$bom->id,$bom->item_id);
							$noDependTotalQtyZero = 1;
							if(gettype($noDependTotalQty) == 'double') {
								if((int)$noDependTotalQty == 0) {
									$noDependTotalQtyZero = 0;
								}
							}
							if(empty($noDependTotalQty) && $noDependTotalQtyZero==1) {
								$noDependTotalQty = 0;
															// first time set default booking qty (required qty)
								if($bom->po_no != null) {
									if(isset($poSizeQtyListS[$bom->po_no])){
										if(!empty($poSizeQtyListS[$bom->po_no])){
											foreach($poSizeQtyListS[$bom->po_no] as $eachNoDependQty){
												$noDependTotalQty += array_sum($eachNoDependQty);
											}
										}
									}
									$noDependTotalQty = ($noDependTotalQty * $total) * $bom->precost_unit_price;
								} else {
									$noDependTotalQty = ($order->order_qty * $total) * $bom->precost_unit_price;
								}
								$noDependTotalQty = $noDependTotalQty * $itemPoCount;
							}
														// check remaining
							if(gettype($noDependTotalRemain) == 'double') {
								if((int)$noDependTotalRemain == 0) {
									$noDependTotalQty = 0;
								} else {
									$noDependTotalQty = ($noDependTotalRemain * $total) * $bom->precost_unit_price;
								}
							}
							$noDependTotalQty = Custom::fixedNumber($noDependTotalQty);
							$request['value'][$bom->id][$bom->order_id][$bom->item_id][] = $noDependTotalQty;
						}
					}
					$itemIndex++;
				}
			}
		}
				// return $request;
		return $this->oneClickConfirmStore((object)$request);
	}

	public function oneClickOrderWiseBookingConfirm($poBookingId)
	{
		try {
			if($poBookingId != null) {
				if(PoBooking::find($poBookingId) != null) {
					$tableData      = [];
					$supplierList   = [];
										// po order information
					$poBooking = PoBooking::where('id',$poBookingId)->first();
					$poBookingDetails = PoBookingDetail::where('mr_po_booking_id',$poBookingId)
					->groupBy('mr_order_entry_order_id')
					->pluck('mr_order_entry_order_id','id')
					->toArray();
					if(!empty($poBookingDetails)) {
						foreach($poBookingDetails as $k=>$orderId) {
							$tableData[] = $this->getPoOneClickOrderItemForConfirm($orderId, $poBooking->mr_supplier_sup_id, $poBooking->unit_id, $poBookingId);
						}
					}
					return $tableData;
				}
			}
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function update(Request $request)
	{
		try {
			$costingBookingIdList = $request->mr_order_bom_costing_booking_id;
			$supId = $request->mr_supplier_sup_id;
			$buyerId = $request->mr_buyer_b_id;
			$unitId = $request->unit_id;
			$deliveryDate = $request->delivery_date;
			$orderIdList = $request->mr_order_entry_order_id;
			$orderPoIdList = $request->mr_purchase_order_po_id;
			$orderClrList = $request->mr_material_color_clr_id;
			$orderSizeList = $request->size;
			$orderQtyList = $request->qty;
			$orderReQtyList = $request->req_qty;
			$orderBoQtyList = $request->booking_qty;
			$result = [];
			$result2 = [];
			$poTable = [];
			$poTableDetailC = [];
			$poTableDetailS = [];
			$poTableDetailCS = [];
			$poTableDetailN = [];
			if($costingBookingIdList != null) {
				PoBookingDetail::where('mr_po_booking_id',$request->po_booking_id)->delete();
								// update deliver date
				PoBooking::where('id',$request->po_booking_id)->update(['delivery_date'=>$deliveryDate]);
								// loop one
				foreach($costingBookingIdList  as $costingBookingIdK=>$costingBookingId) {
										// loop two
					foreach($orderPoIdList[$costingBookingId] as $orderIdK=>$orderList) {
												// loop three
						foreach($orderIdList[$orderIdK] as $poIdK=>$cosBookingIdList) {
														// loop four
							foreach($cosBookingIdList as $cosBookingIdListK=>$dependOn){
								if($dependOn == 1) {
																		// $result2[$cosBookingIdListK][$orderIdK][$poIdK][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$poIdK])) {
																		// isset one
										$poTableDetailC['mr_order_entry_order_id'] = $orderIdK;
										$poTableDetailC['mr_purchase_order_po_id'] = $poIdK;
										$poTableDetailC['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
																				// po details table ref id
										$poTableDetailC['mr_po_booking_id'] = $request->po_booking_id;
																				// isset two
										if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$poIdK])){
											foreach($orderClrList[$cosBookingIdListK][$orderIdK][$poIdK] as $clrIdK=>$clrId) {
												$poTableDetailC['mr_material_color_clr_id'] = $clrId;
												$poTableDetailC['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId];
												$poTableDetailC['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId];
												$poTableDetailC['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId];
												$poTableDetailC['created_by'] = auth()->user()->associate_id;
												$result[$cosBookingIdListK][$poIdK][] = $poTableDetailC;
												PoBookingDetail::insert($poTableDetailC);
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 2) {
																		// $result2[$cosBookingIdListK][$orderIdK][$poIdK][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$poIdK])) {
																		// isset one
										$poTableDetailS['mr_order_entry_order_id'] = $orderIdK;
										$poTableDetailS['mr_purchase_order_po_id'] = $poIdK;
										$poTableDetailS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$poTableDetailS['mr_material_color_clr_id'] = '';
																				// po details table ref id
										$poTableDetailS['mr_po_booking_id'] = $request->po_booking_id;
																				// isset two
										if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$poIdK])){
											foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$poIdK] as $sizeIdK=>$sizeName) {
												$poTableDetailS['size'] = $sizeIdK;
												$poTableDetailS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$sizeIdK];
												$poTableDetailS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$sizeIdK];
												$poTableDetailS['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$sizeIdK];
												$poTableDetailS['created_by'] = auth()->user()->associate_id;
												$result[$cosBookingIdListK][$poIdK][] = $poTableDetailS;
												PoBookingDetail::insert($poTableDetailS);
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 3) {
																		// $result2[$cosBookingIdListK][$orderIdK][$poIdK][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$poIdK])) {
																		// isset one
										$poTableDetailCS['mr_order_entry_order_id'] = $orderIdK;
										$poTableDetailCS['mr_purchase_order_po_id'] = $poIdK;
										$poTableDetailCS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
																				// po details table ref id
										$poTableDetailCS['mr_po_booking_id'] = $request->po_booking_id;
																				// isset two
										if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$poIdK])){
											foreach($orderClrList[$cosBookingIdListK][$orderIdK][$poIdK] as $clrIdK=>$clrId) {
												$poTableDetailCS['mr_material_color_clr_id'] = $clrId;
																								// isset three
												if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId])){
													foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId] as $sizeIdK=>$sizeName) {
														$poTableDetailCS['size'] = $sizeIdK;
														$poTableDetailCS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId][$sizeIdK];
														$poTableDetailCS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId][$sizeIdK];
														$poTableDetailCS['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$poIdK][$clrId][$sizeIdK];
														$poTableDetailCS['created_by'] = auth()->user()->associate_id;
														$result[$cosBookingIdListK][$poIdK][] = $poTableDetailCS;
														PoBookingDetail::insert($poTableDetailCS);
													}
												}
																								// end isset three
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 0) {
																		// $result2[$cosBookingIdListK][$orderIdK][$poIdK][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$poIdK])) {
																		// isset one
										$poTableDetailN['mr_order_entry_order_id'] = $orderIdK;
										$poTableDetailN['mr_purchase_order_po_id'] = $poIdK;
																				// po details table ref id
										$poTableDetailN['mr_po_booking_id'] = $request->po_booking_id;
										$poTableDetailN['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$poTableDetailN['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$poIdK][0];
										$poTableDetailN['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$poIdK][0];
										$poTableDetailN['booking_qty'] = $orderBoQtyList[$cosBookingIdListK][$orderIdK][$poIdK][0];
										$poTableDetailN['created_by'] = auth()->user()->associate_id;
										$result[$cosBookingIdListK][$poIdK][] = $poTableDetailN;
										PoBookingDetail::insert($poTableDetailN);
									}
																		// end isset one
								}
							}
														// end loop four
						}
												// end loop three
					}
										// end loop two
				}
								// end loop one
				return redirect('merch/order_po_booking/confirm/'.$request->po_booking_id)->with('success','Please Confirm Purchase Order.');
			} else {
				return redirect()->back()->with('error','No purchase order found.');
			}
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function confirm(Request $request)
	{
		try {
			$poBookingId = $request->poBookingId;
			if($poBookingId != null) {
				if(PoBooking::find($poBookingId) != null) {
					$tableData      = '';
					$supplierList   = [];
										// po order information
					$poBooking = PoBooking::where('id',$poBookingId)->first();
					$poBookingDetails = PoBookingDetail::where('mr_po_booking_id',$poBookingId)
					->groupBy('mr_order_entry_order_id')
					->pluck('mr_order_entry_order_id','id')
					->toArray();
					if(!empty($poBookingDetails)) {
						foreach($poBookingDetails as $k=>$orderId) {
							$tableData .= $this->getPoOrderItemForConfirm($orderId, $poBooking->mr_supplier_sup_id, $poBooking->unit_id, $poBookingId);
						}
					} else {
						$tableData .= '<td colspan="16">No Data Found</td>';
					}
					return view("merch.order_booking.order_po_booking.order_po_booking_confirm_form", compact('tableData','poBooking'));
				}
			}
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function oneClickConfirmStore($request)
	{
				// return dd($request->all());
		try {
			$costingBookingIdList = $request->mr_order_bom_costing_booking_id;
			$orderIdList = $request->mr_order_entry_order_id;
			$itemIdList = $request->mr_cat_item_id;
			$itemCatIdList = $request->mr_cat_item_mcat_id;
			$orderClrList = isset($request->mr_material_color_clr_id)?$request->mr_material_color_clr_id:[];
			$orderSizeList = isset($request->size)?$request->size:[];
			$orderQtyList = $request->qty;
			$orderReQtyList = $request->req_qty;
			$bookingQtyList = $request->booking_qty;
			$bookingValueQtyList = $request->value;
			$result = [];
			$result2 = [];
			$poTable = [];
			$oBookingC = [];
			$oBookingS = [];
			$oBookingCS = [];
			$oBookingN = [];
			if($costingBookingIdList != null) {

				$orderBookingData = MrOrderBooking::where('mr_po_booking_id',$request->mr_po_booking_id)->get();
								// remove previous
				if(!$orderBookingData->isEmpty()){
					MrOrderBooking::where('mr_po_booking_id',$request->mr_po_booking_id)->delete();
				}
								// loop one
				foreach($costingBookingIdList  as $costingBookingIdK=>$costingBookingId) {
										// loop two
					foreach($itemIdList[$costingBookingId] as $orderIdK=>$orderList) {
												// loop three
						foreach($orderIdList[$orderIdK] as $itemId=>$cosBookingIdList) {
														// loop four
							foreach($cosBookingIdList as $cosBookingIdListK=>$dependOn){
								if($dependOn == 1) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingC['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$oBookingC['mr_cat_item_id'] = $itemId;
																				// po order id
										$oBookingC['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingC['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
																				// isset two
										if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$itemId])){
											foreach($orderClrList[$cosBookingIdListK][$orderIdK][$itemId] as $clrIdK=>$clrId) {
												$oBookingC['mr_material_color_id'] = $clrId;
												$oBookingC['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$oBookingC['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$oBookingC['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$oBookingC['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$result[$cosBookingIdListK][$itemId][] = $oBookingC;
												MrOrderBooking::insert($oBookingC);
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 2) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$oBookingS['mr_cat_item_id'] = $itemId;
																				// po order id
										$oBookingS['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingS['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
																				// isset two
										if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId])){
											foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId] as $sizeIdK=>$sizeName) {
												$oBookingS['size'] = $sizeIdK;
												$oBookingS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$oBookingS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$oBookingS['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$oBookingS['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$result[$cosBookingIdListK][$itemId][] = $oBookingS;
												MrOrderBooking::insert($oBookingS);
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 3) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingCS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$oBookingCS['mr_cat_item_id'] = $itemId;
																				// po order id
										$oBookingCS['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingCS['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
																				// isset two
										if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$itemId])){
											foreach($orderClrList[$cosBookingIdListK][$orderIdK][$itemId] as $clrIdK=>$clrId) {
												$oBookingCS['mr_material_color_id'] = $clrId;
																								// isset three
												if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId][$clrId])){
													foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId][$clrId] as $sizeIdK=>$sizeName) {
														$oBookingCS['size'] = $sizeIdK;
														$oBookingCS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$oBookingCS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$oBookingCS['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$oBookingCS['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$result[$cosBookingIdListK][$itemId][] = $oBookingCS;
														MrOrderBooking::insert($oBookingCS);
													}
												}
																								// end isset three
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 0) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingN['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
																				// po order id
										$oBookingN['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingN['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
										$oBookingN['mr_cat_item_id'] = $itemId;
										$oBookingN['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$oBookingN['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$oBookingN['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$oBookingN['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$result[$cosBookingIdListK][$itemId][] = $oBookingN;
										MrOrderBooking::insert($oBookingN);
									}
																		// end isset one
								}
							}
														// end loop four
						}
												// end loop three
					}
										// end loop two
				}
								// end loop one
				return true;
			} else {
				return false;
			}
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function confirmStore(Request $request)
	{
				// return dd($request->all());
		try {
			$costingBookingIdList = $request->mr_order_bom_costing_booking_id;
			$orderIdList = $request->mr_order_entry_order_id;
			$itemIdList = $request->mr_cat_item_id;
			$itemCatIdList = $request->mr_cat_item_mcat_id;
			$orderClrList = $request->mr_material_color_clr_id;
			$orderSizeList = $request->size;
			$orderQtyList = $request->qty;
			$orderReQtyList = $request->req_qty;
			$bookingQtyList = $request->booking_qty;
			$bookingValueQtyList = $request->value;
			$result = [];
			$result2 = [];
			$poTable = [];
			$oBookingC = [];
			$oBookingS = [];
			$oBookingCS = [];
			$oBookingN = [];
			if($costingBookingIdList != null) {

				$orderBookingData = MrOrderBooking::where('mr_po_booking_id',$request->mr_po_booking_id)->get();
								// remove previous
				if(!$orderBookingData->isEmpty()){
					MrOrderBooking::where('mr_po_booking_id',$request->mr_po_booking_id)->delete();
				}
								// loop one
				foreach($costingBookingIdList  as $costingBookingIdK=>$costingBookingId) {
										// loop two
					foreach($itemIdList[$costingBookingId] as $orderIdK=>$orderList) {
												// loop three
						foreach($orderIdList[$orderIdK] as $itemId=>$cosBookingIdList) {
														// loop four
							foreach($cosBookingIdList as $cosBookingIdListK=>$dependOn){
								if($dependOn == 1) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingC['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$oBookingC['mr_cat_item_id'] = $itemId;
																				// po order id
										$oBookingC['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingC['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
																				// isset two
										if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$itemId])){
											foreach($orderClrList[$cosBookingIdListK][$orderIdK][$itemId] as $clrIdK=>$clrId) {
												$oBookingC['mr_material_color_id'] = $clrId;
												$oBookingC['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$oBookingC['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$oBookingC['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$oBookingC['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId];
												$result[$cosBookingIdListK][$itemId][] = $oBookingC;
												MrOrderBooking::insert($oBookingC);
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 2) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$oBookingS['mr_cat_item_id'] = $itemId;
																				// po order id
										$oBookingS['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingS['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
																				// isset two
										if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId])){
											foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId] as $sizeIdK=>$sizeName) {
												$oBookingS['size'] = $sizeIdK;
												$oBookingS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$oBookingS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$oBookingS['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$oBookingS['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][$sizeIdK];
												$result[$cosBookingIdListK][$itemId][] = $oBookingS;
												MrOrderBooking::insert($oBookingS);
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 3) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingCS['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
										$oBookingCS['mr_cat_item_id'] = $itemId;
																				// po order id
										$oBookingCS['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingCS['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
																				// isset two
										if(isset($orderClrList[$cosBookingIdListK][$orderIdK][$itemId])){
											foreach($orderClrList[$cosBookingIdListK][$orderIdK][$itemId] as $clrIdK=>$clrId) {
												$oBookingCS['mr_material_color_id'] = $clrId;
																								// isset three
												if(isset($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId][$clrId])){
													foreach($orderSizeList[$cosBookingIdListK][$orderIdK][$itemId][$clrId] as $sizeIdK=>$sizeName) {
														$oBookingCS['size'] = $sizeIdK;
														$oBookingCS['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$oBookingCS['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$oBookingCS['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$oBookingCS['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][$clrId][$sizeIdK];
														$result[$cosBookingIdListK][$itemId][] = $oBookingCS;
														MrOrderBooking::insert($oBookingCS);
													}
												}
																								// end isset three
											}
										}
																				// end isset two
									}
																		// end isset one
								} else if($dependOn == 0) {
																		// $result2[$cosBookingIdListK][$orderIdK][$itemId][] = $dependOn;
									if(!isset($result[$cosBookingIdListK][$itemId])) {
																		// isset one
										$oBookingN['mr_order_bom_costing_booking_id'] = $cosBookingIdListK;
																				// po order id
										$oBookingN['mr_po_booking_id'] = $request->mr_po_booking_id;
										$oBookingN['mr_cat_item_mcat_id'] = $itemCatIdList[$cosBookingIdListK][$orderIdK][$itemId];
										$oBookingN['mr_cat_item_id'] = $itemId;
										$oBookingN['qty'] = $orderQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$oBookingN['req_qty'] = $orderReQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$oBookingN['booking_qty'] = $bookingQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$oBookingN['value'] = $bookingValueQtyList[$cosBookingIdListK][$orderIdK][$itemId][0];
										$result[$cosBookingIdListK][$itemId][] = $oBookingN;
										MrOrderBooking::insert($oBookingN);
									}
																		// end isset one
								}
							}
														// end loop four
						}
												// end loop three
					}
										// end loop two
				}
								// end loop one
				return redirect('merch/order_po_booking')->with('success','Success Confirm Purchase Order.');
			} else {
				return redirect('merch/order_po_booking/confirm/'.$request->mr_po_booking_id)->with('error','Error Confirm Purchase Order.');
			}
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function poBookingEdit(Request $request)
	{
		try {
			if($request->poBookingId != null) {
				if(PoBooking::find($request->poBookingId) != null) {
					$tableData      = '';
					$supplierList   = [];
										// po order information
					$poBooking = PoBooking::where('id',$request->poBookingId)->first();
					$poBookingDetails = PoBookingDetail::where('mr_po_booking_id',$request->poBookingId)
					->groupBy('mr_order_entry_order_id')
					->pluck('mr_order_entry_order_id','id')
					->toArray();

					// get order wise po data
					if(!empty($poBookingDetails)) {
						foreach($poBookingDetails as $k=>$orderId) {
							$tableData .= $this->getPoOrderItemForEdit($orderId, $poBooking->mr_supplier_sup_id,$poBooking->unit_id, $request->poBookingId);
						}
					} else {
						$tableData .= '<td colspan="16">No Data Found</td>';
					}
										// order booking
					$orderBooking = MrOrderBooking::where('mr_po_booking_id',$request->poBookingId)->count();
					$orderBookingExist = false;
					if($orderBooking > 0) {
						$orderBookingExist = true;
					}
										// get unit wise order
					$unitOrderData = $this->getSupOrderListForEdit($poBooking->unit_id,$poBooking->mr_supplier_sup_id, $poBooking->mr_buyer_b_id, $poBookingDetails,$orderBookingExist);
										// included
					$orderBomCostingBookingList = OrderBomCostingBooking::select([
						'mr_supplier_sup_id',
						'order_id',
						'po_no'
					])
					->groupBy('mr_supplier_sup_id')->get();
					$buyer_permissions = auth()->user()->buyer_permissions;
					$buyerOrderList = DB::table('mr_order_entry as a')->select([
						'bu.b_name',
						'bu.b_id'
					])
					->whereIn('a.mr_buyer_b_id', explode(',',$buyer_permissions))
					->join('mr_buyer as bu', 'bu.b_id', 'a.mr_buyer_b_id')
					->groupBy('bu.b_id')
					->orderBy('order_delivery_date','ASC')
					->pluck('bu.b_name','bu.b_id')
					->toArray();
					foreach($orderBomCostingBookingList as $single) {
						if($single->supplier != null) {
							$supplierList[$single->mr_supplier_sup_id] = $single->supplier->sup_name;
						}
					}
					$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
					return view("merch.order_booking.order_po_booking.order_po_booking_edit", compact('supplierList','unitList','poBooking','tableData','unitOrderData','poBookingDetails','buyerOrderList','orderBookingExist'));
				} else {
					return redirect('merch/order_po_booking')->with('error','Purchase Order Not Found.');
				}
			} else {
				return redirect('merch/order_po_booking')->with('error','Purchase Order Error.');
			}
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getPoOrderListData()
	{
		$team = [];


		$queryD = PoBooking::with(['getSupplierInfo','getUnitInfo','buyer','orderBooking']);
		if(!empty($team)) {
			$queryD->whereIn('created_by', $team);
		}
		$data = $queryD->orderBy('id','desc')->get();
		return DataTables::of($data)->addIndexColumn()
		->addColumn('supplier', function($data){
			return isset($data->getSupplierInfo->sup_name)?$data->getSupplierInfo->sup_name:'';
		})
		->addColumn('unit', function($data){
			return isset($data->getUnitInfo['hr_unit_name'])?$data->getUnitInfo['hr_unit_name']:'';
		})
		->addColumn('buyer', function($data){
			return isset($data->buyer->b_name)?$data->buyer->b_name:'';
		})
		->addColumn('bookingQty', function($data){

			$returnNumber = 0;
			if(!$data->poDetails->isEmpty()){
				if(is_numeric($data->poDetails->sum('booking_qty'))) {
					$returnNumber = number_format((float)$data->poDetails->sum('booking_qty'), 2, '.', '');
					if(fmod($returnNumber, 1) == 0.00){
						$returnNumber = round($data->poDetails->sum('booking_qty'), 0);
					}
				}
			}else{
                $returnNumber = $data->poDetails->sum('booking_qty');
            }

			return $returnNumber;
		})
		->addColumn('deliveryDate', function($data){
			return isset($data->delivery_date)?$data->delivery_date:'';
		})
		->addColumn('action', function($data){
			$action_buttons= "<div class=\"btn-group\">
			<a href=".url('merch/order_po_booking/edit/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\">
			<i class=\"ace-icon fa fa-pencil bigger-120\"></i>
			</a> ";
			if(!$data->orderBooking->isEmpty()){
				$action_buttons .= "
				<a href=".url('merch/order_po_booking/confirm/'.$data->id.'?mode=edit')." class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" title=\"Confirm\">
				<i class=\"ace-icon fa fa-list bigger-120\"></i>
				</a> ";
			}
			$action_buttons.= "</div>";
			return $action_buttons;
		})
		->rawColumns(['action','supplier','unit','orderPo','bookingQty','deliveryDate'])
		->make(true);
	}

	public function getPoOrderInfo(Request $request)
	{
		return $request->po_booking_id;
	}

	public function getPoOrderItem(Request $request)
	{
		try {
			return $this->getPoOrderItemGlobal($request->order_id, $request->supplier_id, 'insert');

		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getPoOrderItemForEdit($orderId, $supplierId, $unitId, $poBookingId)
	{
		try {
						// return $itemUnique;
			return $this->getPoOrderItemGlobal($orderId, $supplierId, 'edit', $unitId, $poBookingId);
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getPoOrderItemForConfirm($orderId, $supplierId, $unitId, $poBookingId)
	{
		try {
			return $this->getPoOrderItemGlobal($orderId, $supplierId, 'confirm', $unitId, $poBookingId);
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getPoOneClickOrderItemForConfirm($orderId, $supplierId, $unitId, $poBookingId)
	{
		try {
			return $this->getPoOrderItemGlobal($orderId, $supplierId, 'oneClickOrder', $unitId, $poBookingId);
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function getPoOrderItemGlobal($orderId, $supplierId,  $flag = '', $unitId = '', $poBookingId = '')
	{
		$order = DB::table('mr_order_entry')
			->leftjoin('hr_unit','mr_order_entry.unit_id','=','hr_unit.hr_unit_id')
			->leftjoin('mr_buyer','mr_order_entry.mr_buyer_b_id','=','mr_buyer.b_id')
			->leftjoin('mr_brand','mr_order_entry.mr_brand_br_id','mr_brand.br_id')
			->leftjoin('mr_season','mr_order_entry.mr_season_se_id','=','mr_season.se_id')
			->leftjoin('mr_style','mr_order_entry.mr_style_stl_id','=','mr_style.stl_id')
			->where('mr_order_entry.order_id',$orderId)
			->first();

		$poList = DB::table('mr_purchase_order')
		->select('po_id','mr_order_entry_order_id','po_no','po_qty')
		->where('mr_order_entry_order_id',$orderId)->get();

		$boms = $this->poBomRepository->bomInfo($orderId, $supplierId);




		//dd($boms);
				// dd($poList,$boms);exit;

/*		$colors = DB::table('mr_order_entry')
		->groupBy('mr_material_color.clr_name')
		->selectRaw('sum(mr_po_sub_style.po_sub_style_qty) as sum, mr_material_color.clr_name as clr_name, mr_material_color.clr_id')
		->leftJoin('mr_purchase_order','mr_order_entry.order_id','=','mr_purchase_order.mr_order_entry_order_id')
		->leftJoin('mr_po_sub_style','mr_purchase_order.po_id','=','mr_po_sub_style.po_id')
		->leftJoin('mr_material_color','mr_po_sub_style.clr_id','=','mr_material_color.clr_id')
		->where('mr_order_entry.order_id', $orderId)
		->orderBy('mr_material_color.clr_name','ASC')
		->get();*/
		$colors = DB::table('mr_order_entry')
		->groupBy('mr_material_color.clr_name')
		->selectRaw('sum(mr_purchase_order.po_qty) as sum, mr_material_color.clr_name as clr_name, mr_material_color.clr_id')
		->leftJoin('mr_purchase_order','mr_order_entry.order_id','=','mr_purchase_order.mr_order_entry_order_id')
		->leftJoin('mr_material_color','mr_purchase_order.clr_id','=','mr_material_color.clr_id')
		->where('mr_order_entry.order_id', $orderId)
		->orderBy('mr_material_color.clr_name','ASC')
		->get();
				//dd($colors);exit;

		$sizes = DB::table('mr_order_entry')
		->leftJoin('mr_stl_size_group','mr_order_entry.mr_style_stl_id','=','mr_stl_size_group.mr_style_stl_id')
		->leftJoin('mr_product_size','mr_stl_size_group.mr_product_size_group_id','=','mr_product_size.mr_product_size_group_id')
		->where('mr_order_entry.order_id',$orderId)
		->orderBy('mr_product_size.mr_product_pallete_name','ASC')
		->get();
				//dd($sizes);

		$care_label = DB::table('mr_order_entry')
		->groupBy('mr_material_color.clr_name','mr_product_size.mr_product_pallete_name')
		->selectRaw('mr_material_color.clr_name as clr_name, mr_product_size.mr_product_pallete_name as mr_product_pallete_name, mr_material_color.clr_id,mr_product_size.id as product_size_id,mr_material_color.clr_id')

		->leftJoin('mr_purchase_order','mr_order_entry.order_id','=','mr_purchase_order.mr_order_entry_order_id')
		->leftJoin('mr_po_sub_style','mr_purchase_order.po_id','=','mr_po_sub_style.po_id')
		->leftJoin('mr_material_color','mr_po_sub_style.clr_id','=','mr_material_color.clr_id')
		->leftJoin('mr_stl_size_group','mr_order_entry.mr_style_stl_id','=','mr_stl_size_group.mr_style_stl_id')
		->leftJoin('mr_product_size','mr_stl_size_group.mr_product_size_group_id','=','mr_product_size.mr_product_size_group_id')
		->where('mr_order_entry.order_id',$orderId)

		->get();

		//dd($care_label);

				// po sub item qty amount
		$poDataList = DB::table('mr_purchase_order as a')
		->select([
			'a.po_id',
			'a.mr_order_entry_order_id',
			'a.clr_id',
			'b.mr_product_size_id'
		])
		->join('mr_po_size_qty as b', function($query) {
			$query->on('b.po_id','=','a.po_id');
		})
		->where('a.mr_order_entry_order_id',$orderId)
		->get();

		$poSizeQtyList = [];
		$poSizeQtyListS = [];

		$poSizeQtyListN = [];
		$poSizeQtyListSN = [];

		$poColorQtyListN = [];
		$poColorQtyListCN = [];

		$poColorQtyList = [];
		$poColorQtyListC = [];

		$poSizeQtyListC = [];
		$poSizeQtyListCN = [];


		foreach($poDataList as $key=>$poDataL) {

            $poSizeAr[$poDataL->po_id] = DB::table('mr_po_size_qty')
			->where('po_id',$poDataL->po_id)->get();

            $poColorAr[$poDataL->po_id] = DB::table('mr_purchase_order')
			->where('po_id',$poDataL->po_id)->get();

			foreach($poSizeAr[$poDataL->po_id] as $key1=>$poSizeSingle) {
				$poSizeQtyList[$poDataL->clr_id][$poDataL->po_id][$key][$poSizeSingle->mr_product_size_id] = $poSizeSingle->qty;
				$poSizeQtyListC[$poDataL->po_id][$poDataL->clr_id][$poDataL->po_id][$key][$poSizeSingle->mr_product_size_id] = $poSizeSingle->qty;
				$poSizeQtyListS[$poDataL->po_id][$poSizeSingle->mr_product_size_id][$key] = $poSizeSingle->qty;
			}

			foreach($poColorAr[$poDataL->po_id] as $key1=>$poColorSingle) {
				$poColorQtyList[$poDataL->clr_id][$poDataL->po_id][$key] = $poColorSingle->po_qty;
				$poColorQtyListC[$poDataL->po_id][$poDataL->clr_id][$poDataL->po_id][$key] = $poColorSingle->po_qty;
			}
		}

		foreach($boms as $bom) {
			if($bom->po_po_id == null) {
				foreach($poDataList as $key=>$poDataL) {
					$poSizeArN[$bom->order_id][$key] = DB::table('mr_po_size_qty')
					->where('po_id',$poDataL->po_id)->get();
					$poColorArN[$bom->order_id][$key] = DB::table('mr_purchase_order')
					->where('po_id',$poDataL->po_id)->get();
					foreach($poSizeArN[$bom->order_id][$key] as $key1=>$orderSizeSingle) {
						$poSizeQtyListN[$bom->order_id][$key][$key1] = $orderSizeSingle->qty;
						$poSizeQtyListCN[$bom->order_id][$poDataL->clr_id][$orderSizeSingle->mr_product_size_id][$key] = $orderSizeSingle->qty;
						$poSizeQtyListSN[$bom->order_id][$orderSizeSingle->mr_product_size_id][$poDataL->clr_id][$key] = $orderSizeSingle->qty;
					}

					foreach($poColorArN[$bom->order_id][$key] as $key2=>$orderColorSingle) {
						$poColorQtyListN[$bom->order_id][$key][$key2] = $orderColorSingle->po_sub_style_qty;
						$poColorQtyListCN[$bom->order_id][$poDataL->clr_id][$key] = $orderColorSingle->po_sub_style_qty;
					}
				}
			}
		}



		$filter = array();

		foreach($care_label as $filter_result) {
			$filter[$filter_result->clr_id] = $filter_result->clr_name;
		}
		$filter = array_unique($filter);
		$bomsCat = array_column($boms->toArray(), 'mcat_name');
		$bomsItem = array_column($boms->toArray(), 'item_name');
		$itemUnique = array_unique($bomsItem);
		$catCount = array_count_values($bomsCat);

        if($flag == 'insert') {
		return view('merch.order_booking.order_po_booking.ajax_get_supplier_item',
				compact('order','boms','colors','sizes','care_label','filter','poSizeQtyList','poSizeQtyListC','poSizeQtyListS','poList', 'itemUnique', 'catCount','poSizeQtyListN','poSizeQtyListCN','poSizeQtyListSN','poColorQtyList','poColorQtyListC','poColorQtyListN','poColorQtyListCN'))->render();
		}
		if($flag == 'edit') {
			return view('merch.order_booking.order_po_booking.ajax_get_supplier_item_for_edit',
				compact('order','boms','colors','sizes','care_label','filter','poSizeQtyList','poSizeQtyListC','poSizeQtyListS','poList', 'itemUnique', 'catCount','poSizeQtyListN','poSizeQtyListCN','poSizeQtyListSN', 'orderId','supplierId','unitId','poBookingId','poColorQtyList','poColorQtyListC','poColorQtyListN','poColorQtyListCN'))->render();
		}
		if($flag == 'confirm') {
			return view('merch.order_booking.order_po_booking.ajax_get_supplier_item_for_confirm',
				compact('order','boms','colors','sizes','care_label','filter','poSizeQtyList','poSizeQtyListC','poSizeQtyListS','poList', 'itemUnique', 'catCount','poSizeQtyListN','poSizeQtyListCN','poSizeQtyListSN', 'orderId','supplierId','unitId','poBookingId','poColorQtyList','poColorQtyListC','poColorQtyListN','poColorQtyListCN'))->render();
		}
		if($flag == 'oneClickOrder') {
			return $this->oneClickOrderWiseBookingForm($order,$boms,$colors,$sizes,$care_label,$filter,$poSizeQtyList,$poSizeQtyListC,$poSizeQtyListS,$poList, $itemUnique, $catCount,$poSizeQtyListN,$poSizeQtyListCN,$poSizeQtyListSN,$poBookingId,$poColorQtyList,$poColorQtyListC,$poColorQtyListN,$poColorQtyListCN);
		}
	}

	public function getSupOrderListForEdit($unitId, $supplierId, $buyerId, $orderList, $orderBookingExist)
	{
		try {

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
				$team = $executive_associateId;
			}else{
				$team =[];
			}
			$orderId = '';
			$unitId = $unitId;
			$supId  = $supplierId;
			$buyId  = $buyerId;
			if(!empty($team)){
				$buyerOrderList = DB::table('mr_order_entry as a')->select([
					'a.order_id',
					'a.order_code',
					'a.unit_id',
					'b.mr_supplier_sup_id',
					'a.mr_buyer_b_id',
					'a.mr_style_stl_id',
					'a.order_qty',
					'a.order_delivery_date',
					'a.order_status',
					'b.mr_style_stl_id',
					'b.mr_material_category_mcat_id',
					'b.id as cos_book_id',
					'b.mr_cat_item_id',
					'b.clr_id',
					'b.delivery_date',
					'b.po_no',
					'u.hr_unit_name',
					'bu.b_name',
					'su.sup_name',
					'it.item_name'
				])
				->when($unitId, function($query, $unitId) {
					return $query->where('a.unit_id',$unitId);
				})
				->when($buyId, function($query, $buyId) {
					return $query->where('a.mr_buyer_b_id',$buyId);
				})
				->when($orderId, function($query, $orderId) {
					return $query->where('a.order_id',$orderId);
				})
				->whereNotIn('a.order_status',['Completed','Inactive'])
				->join('mr_order_bom_costing_booking as b', function($join) use($supId) {
					$join->on('a.order_id','=','b.order_id');
					if($supId != null) {
						$join->where('b.mr_supplier_sup_id','=',$supId);
					}
				})
				->leftJoin('mr_cat_item as it', 'it.id', 'b.mr_cat_item_id')
				->leftJoin('mr_supplier as su', 'su.sup_id', 'b.mr_supplier_sup_id')
				->leftJoin('hr_unit as u', 'u.hr_unit_id', 'a.unit_id')
				->leftJoin('mr_buyer as bu', 'bu.b_id', 'a.mr_buyer_b_id')
				->whereIn('a.created_by', $team)
				->groupBy('a.order_id')
				->get();
			}else{
				$buyerOrderList = DB::table('mr_order_entry as a')->select([
					'a.order_id',
					'a.order_code',
					'a.unit_id',
					'b.mr_supplier_sup_id',
					'a.mr_buyer_b_id',
					'a.mr_style_stl_id',
					'a.order_qty',
					'a.order_delivery_date',
					'a.order_status',
					'b.mr_style_stl_id',
					'b.mr_material_category_mcat_id',
					'b.id as cos_book_id',
					'b.mr_cat_item_id',
					'b.clr_id',
					'b.delivery_date',
					'b.po_no',
					'u.hr_unit_name',
					'bu.b_name',
					'su.sup_name',
					'it.item_name'
				])
				->when($unitId, function($query, $unitId) {
					return $query->where('a.unit_id',$unitId);
				})
				->when($buyId, function($query, $buyId) {
					return $query->where('a.mr_buyer_b_id',$buyId);
				})
				->when($orderId, function($query, $orderId) {
					return $query->where('a.order_id',$orderId);
				})
				->whereNotIn('a.order_status',['Completed','Inactive'])
				->join('mr_order_bom_costing_booking as b', function($join) use($supId) {
					$join->on('a.order_id','=','b.order_id');
					if($supId != null) {
						$join->where('b.mr_supplier_sup_id','=',$supId);
					}
				})
				->leftJoin('mr_cat_item as it', 'it.id', 'b.mr_cat_item_id')
				->leftJoin('mr_supplier as su', 'su.sup_id', 'b.mr_supplier_sup_id')
				->leftJoin('hr_unit as u', 'u.hr_unit_id', 'a.unit_id')
				->leftJoin('mr_buyer as bu', 'bu.b_id', 'a.mr_buyer_b_id')
				->groupBy('a.order_id')
				->get();
			}

			return view("merch.order_booking.order_po_booking.ajax_get_supplier_order_for_edit", compact('buyerOrderList','orderList','orderBookingExist'))->render();
		} catch(\Exception $e) {
			return $e->getMessage();
		}
	}


}
