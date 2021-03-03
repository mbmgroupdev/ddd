<?php

namespace App\Http\Controllers\Merch\Costing;

use App\Http\Controllers\Controller;
use App\Models\Merch\OrderEntry;
use App\Models\Hr\Unit;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\OperationCost;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\Supplier;
use App\Models\Merch\MrOrderBooking;
use App\Models\Merch\OrderBOM;
use App\Models\Commercial\PiForwardDetails;
use App\Models\Merch\OrderBomCostingBooking;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\BomCosting;
use App\Models\Merch\PoBooking;
use App\Models\Merch\PoBookingPi;
use App\Models\Merch\PoBookingDetail;
use App\Models\Merch\ProductType;
use App\Models\Merch\MrPoBomOtherCosting;
use App\Models\Commercial\CmPiBom;
use App\Models\Commercial\CmPiMaster;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use App\Models\Merch\MrPiItemUnitPrice;
use App\Models\Merch\PurchaseOrder;
use DB,Validator, ACL, DataTables, Form, stdClass;
use Illuminate\Http\Request;

class CostingController extends Controller
{
	public function showList()
	{
		try {
			$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
			$buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
			$brandList= Brand::pluck('br_name','br_id');
			$styleList= Style::pluck('stl_no', 'stl_id');
			$seasonList= Season::pluck('se_name', 'se_id');
			return view("merch.costing.costing_list", compact('buyerList', 'seasonList', 'unitList', 'brandList', 'styleList'));
		}catch(\Exception $e) {
			return $e->getMessage();
		}
	}


	public function getPoCost($order_id){
		$cost = PurchaseOrder::select(
					DB::raw('sum(po_qty * country_fob) as total_fob'),
					DB::raw('sum(po_qty) as po_qty')
				)
				->groupBy('mr_order_entry_order_id')
				->where('mr_order_entry_order_id', $order_id)
				->first();

		$data = new stdClass();
		$data->cost = 0;
		$data->po_qty = 0;
		if(isset($cost->total_fob)){
			$data->cost = round(($cost->total_fob/$cost->po_qty),4);
			$data->po_qty = $cost->po_qty;
		}
		return $data;

	}

	public function getPiCost($order_id){

		$piCost = DB::table('cm_pi_bom as cpb')
				->leftJoin('mr_order_booking as b','b.id','cpb.mr_order_booking_id')
				->leftJoin("mr_pi_item_unit_price AS p", function($join) {
					$join->on("p.mr_po_booking_id", "=", "b.mr_po_booking_id");
					$join->on("p.mr_cat_item_id", "=", "b.mr_cat_item_id");
					$join->on("p.cm_pi_master_id", "=", "cpb.cm_pi_master_id");
				})
				->select(
					DB::raw('sum(cpb.pi_qty * p.unit_price) / sum(cpb.pi_qty)  as price'),
					'b.mr_cat_item_id as item'
				)
				->groupBy('b.mr_cat_item_id')
				->where('cpb.mr_order_entry_order_id',$order_id);

        $piCost_sql = $piCost->toSql();
        $check = $piCost->get();

        if(count($check) < 1){
        	$cost = null;
        	return $cost;
        }


         $query1 = DB::table('mr_order_bom_costing_booking AS c')
         			->select(
         				DB::raw('sum((c.precost_unit_price - a.price)*(c.consumption+(c.consumption*(c.extra_percent / 100)))) as diff')
         			)
                    ->where('c.order_id',$order_id);
         			$query1->leftJoin(DB::raw('(' . $piCost_sql. ') AS a'), function($join) use ($piCost) {
                        $join->on('a.item', '=', 'c.mr_cat_item_id')->addBinding($piCost->getBindings()); ;
                    });

            $cost = $query1->first()->diff??0;

		return $cost;

	}


	public function orderWiseCosting($id,Request $request){
		try {




				$order = OrderEntry::with('style','brand','unit','buyer','season','order_costing')
							->where('order_id', $id)
							->first();

				$order_items = OrderBomCostingBooking::where('order_id',$id)->pluck('mr_cat_item_id');
				$style_items = BomCosting::where('mr_style_stl_id', $order->mr_style_stl_id)
								->pluck('mr_cat_item_id');

				$order_cost = OrderBomCostingBooking::where('order_id',$id)
								->leftJoin("mr_cat_item AS i","i.id", "=", "mr_order_bom_costing_booking.mr_cat_item_id")
								->leftJoin("mr_material_sub_cat as scat", "i.mr_material_sub_cat_id", "scat.msubcat_id")
								->orderBy("i.tab_index")
								->orderBy("scat.subcat_index")
								->get();
				$style_cost = BomCosting::select(
									DB::raw('(precost_unit_price*(consumption+(consumption*(extra_percent / 100)))) as price'),
									'mr_stl_bom_n_costing.*'
								)
								->where('mr_style_stl_id', $order->mr_style_stl_id)
								->get()
								->keyBy('mr_cat_item_id');
				//dd($style_cost);

				$style_old = array_diff($style_items->toArray(),$order_items->toArray());
				//dd($style_old);

				$style_op_cost = OperationCost::where('mr_style_stl_id', $order->mr_style_stl_id)
									->get();
				$style_other_cost = BomOtherCosting::where('mr_style_stl_id', $order->mr_style_stl_id)->first();

				$pi_cost = DB::table('cm_pi_bom as cpb')
							->leftJoin('mr_order_booking as b','b.id','cpb.mr_order_booking_id')
							->leftJoin("mr_pi_item_unit_price AS p", function($join) {
								$join->on("p.mr_po_booking_id", "=", "b.mr_po_booking_id");
								$join->on("p.mr_cat_item_id", "=", "b.mr_cat_item_id");
								$join->on("p.cm_pi_master_id", "=", "cpb.cm_pi_master_id");
							})
							->select(
								DB::raw('sum(cpb.pi_qty * p.unit_price) / sum(cpb.pi_qty)  as price'),
								'b.mr_cat_item_id as item'
							)
							->groupBy('b.mr_cat_item_id')
							->where('cpb.mr_order_entry_order_id',$id)
							->pluck('price','item');

				$total_pi_cost = $this->getPiCost($id);

				if($total_pi_cost != null){
					$total_pi_cost =  round($order->order_costing['agent_fob']-floatval($total_pi_cost),6);
				}

				$total_po_cost = round($this->getPoCost($id)->cost,6);

				//dd($style_cost);
				return view('merch.costing.costing', compact(
							'order',
							'order_cost',
							'style_cost',
							'pi_cost',
							'total_pi_cost',
							'style_other_cost',
							'style_op_cost',
							'total_po_cost',
							'style_old'
						));

			}catch(\Exception $e) {
			return $e->getMessage();
		}
	}


	public function listData()
	{
		//dd($this->getPiCost(5));
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

		$b_permissions = explode(',', auth()->user()->buyer_permissions);


		$data = OrderEntry::with('style','brand','unit','buyer','order_costing')
					->where(function($query) use ($team,$b_permissions) {
                        if(count($team)>0){
			            	$query->whereIn('created_by', $team);
			            }
			            if(count($b_permissions)>0){
			            	$query->whereIn('mr_buyer_b_id', $b_permissions);
			            }
			        })
			        ->orderBy('order_id','DESC')
					->get();
		//dd($order);

		return DataTables::of($data)->addIndexColumn()
				->addColumn('action', function ($data) {

					$action_buttons= "<div class=\"btn-group\">
					<a href=".url('merch/costing-compare/'.$data->order_id)." class=\"btn btn-xs btn-primary btn-round\" data-toggle=\"tooltip\" title=\"Costing Compare\">
						View
					</a>";
					$action_buttons.= "</div>";
					return $action_buttons;

				})
				->addColumn('style_cost', function($data){
					return BomOtherCosting::where('mr_style_stl_id',$data->mr_style_stl_id)->first()->agent_fob??0;
				})
				->addColumn('order_cost', function($data){
					return $data->order_costing['agent_fob']??0;
				})
				->addColumn('po_cost', function($data){
					$cost = $this->getPoCost($data->order_id);
					return "<a style='cursor:pointer;color:#000;' rel='tooltip' data-tooltip-location='left' data-tooltip='Approximate average cost for ".$cost->po_qty."' aria-disabled='true'>".$cost->cost."</a>";
				})
				->addColumn('pi_cost', function($data){
					$pi = $this->getPiCost($data->order_id);
					if($pi == null){
						return 'No Cost';
					}else{

						$cost = $data->order_costing['agent_fob']-floatval($pi);
						return "<a style='cursor:pointer;color:#000;' rel='tooltip' data-tooltip-location='left' data-tooltip='Approximate Cost' aria-disabled='true'>".round($cost,6)."</a>";
					}
				})
				->rawColumns(['action','style_cost','order_cost','po_cost','pi_cost'])
				->toJson();
	}
}
