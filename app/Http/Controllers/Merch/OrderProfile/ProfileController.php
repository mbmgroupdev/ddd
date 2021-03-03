<?php

namespace App\Http\Controllers\Merch\OrderProfile;

use App\Models\Hr\Unit;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\Season;
use App\Models\Merch\Style;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use stdClass,PDF;

class ProfileController extends Controller
{
    public function index()
    {
        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
        $buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
        $brandList= Brand::pluck('br_name','br_id');
        $styleList= Style::pluck('stl_no', 'stl_id');
        $seasonList= Season::pluck('se_name', 'se_id');

        return view("merch/orders/order_profile", compact('unitList','buyerList','brandList','styleList','seasonList'));
    }

    public function orderProfileData()
    {
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
  		}else{
  		 $team =[];
  		}
        DB::statement(DB::raw('set @rowrum=0'));
        if(!empty($team)){
          $data= DB::table('mr_order_entry AS OE')
              //->where('OE.order_status', "Active")
              ->select([
                  DB::raw('@rownum := @rownum + 1 as DT_RowIndex'),
                  "OE.order_id",
                  "OE.order_code",
                  "u.hr_unit_name",
                  "b.b_name",
                  "br.br_name",
                  "s.se_name",
                  "stl.stl_no",
                  "OE.order_ref_no",
                  "OE.order_qty",
                  "OE.created_by",
                  "OE.order_delivery_date"
              ])
              ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'OE.unit_id')
              ->leftJoin('mr_buyer AS b', 'b.b_id', 'OE.mr_buyer_b_id')
              ->whereIn('b.b_id', auth()->user()->buyer_permissions())
              ->leftJoin('mr_brand AS br', 'br.br_id', 'OE.mr_brand_br_id')
              ->leftJoin('mr_season AS s', 's.se_id', 'OE.mr_season_se_id')
              ->leftJoin('mr_style AS stl', 'stl.stl_id', "OE.mr_style_stl_id")
              ->whereIn('OE.created_by', $team)
              ->orderBy('order_id', 'DESC')
              ->get();
        }else{
          $data= DB::table('mr_order_entry AS OE')
              //->where('OE.order_status', "Active")
              ->select([
                  DB::raw('@rownum := @rownum + 1 as DT_RowIndex'),
                  "OE.order_id",
                  "OE.order_code",
                  "u.hr_unit_name",
                  "b.b_name",
                  "br.br_name",
                  "s.se_name",
                  "stl.stl_no",
                  "OE.order_ref_no",
                  "OE.order_qty",
                  "OE.created_by",
                  "OE.order_delivery_date"
              ])
              ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'OE.unit_id')
              ->leftJoin('mr_buyer AS b', 'b.b_id', 'OE.mr_buyer_b_id')
              ->whereIn('b.b_id', auth()->user()->buyer_permissions())
              ->leftJoin('mr_brand AS br', 'br.br_id', 'OE.mr_brand_br_id')
              ->leftJoin('mr_season AS s', 's.se_id', 'OE.mr_season_se_id')
              ->leftJoin('mr_style AS stl', 'stl.stl_id', "OE.mr_style_stl_id")
              ->orderBy('order_id', 'DESC')
              ->get();
        }


        return DataTables::of($data)->addIndexColumn()
            ->addColumn('po', function($data){
                $pos= DB::table('mr_purchase_order AS po')
                    ->where('mr_order_entry_order_id', $data->order_id)
                    ->select([
                        "po.*"
                    ])
                    ->get();
                $poColumn="";
                foreach ($pos as $po) {
                    $poColumn.='<p>'.$po->po_no .'</p>';
                }
                return $poColumn;
            })
            ->addColumn('action', function ($data) {
                $action_buttons= "<div class=\"btn-group\" style=\"width:55px\">
                            <a href=".url('merch/orders/order_profile_show/'.$data->order_id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\" style=\"height:25px; width:26px;\">
                                <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                            </a>
                            ";
                $action_buttons.= "</div>";
                return $action_buttons;
            })
            ->rawColumns(['action', 'po'])
            ->toJson();
    }

    public function getOrderSteps($id,$unit,$buyer){
      $data = [];
      $data['bom'] = DB::table("mr_order_bom_costing_booking")
                      ->where('order_id', $id)
                      ->first();

      $data['costing'] = OrderBomOtherCosting::where('mr_order_entry_order_id', $id)->first();

      $data['po'] = DB::table('mr_purchase_order')
                    ->where('mr_order_entry_order_id', $id)
                    ->first();

      $data['booking'] = DB::table('mr_po_booking_detail')
                          ->where('mr_order_entry_order_id', $id)
                          ->first();

      $data['unit'] = $unit;
      $data['buyer'] = $buyer;

      return view("merch/orders/order_steps", compact('data','id'))->render();

    }

    public function orderProfileShow($id)
    {
        $order = DB::table('mr_order_entry')
            ->leftJoin('mr_capacity_reservation','mr_order_entry.res_id','=','mr_capacity_reservation.res_id')
            ->leftJoin('hr_unit','mr_capacity_reservation.hr_unit_id','=','hr_unit.hr_unit_id')
            ->leftJoin('mr_brand','mr_order_entry.mr_brand_br_id','=','mr_brand.br_id')
            ->leftJoin('mr_product_type','mr_capacity_reservation.prd_type_id','=','mr_product_type.prd_type_id')
            ->leftJoin('mr_style','mr_order_entry.mr_style_stl_id','=','mr_style.stl_id')
            ->leftJoin('mr_garment_type','mr_style.gmt_id','=','mr_garment_type.gmt_id')
            ->leftJoin('mr_season','mr_style.mr_season_se_id','=','mr_season.se_id')
            ->leftJoin('mr_purchase_order','mr_order_entry.order_id','=','mr_purchase_order.mr_order_entry_order_id')
            ->leftJoin('mr_country','mr_purchase_order.po_delivery_country','=','mr_country.cnt_id')
            ->leftJoin('mr_order_bom_costing_booking','mr_order_entry.order_id','=','mr_order_bom_costing_booking.order_id')
            ->leftJoin('mr_material_category','mr_order_bom_costing_booking.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
            ->leftJoin('mr_cat_item','mr_order_bom_costing_booking.mr_cat_item_id','=','mr_cat_item.id')
            ->leftJoin('mr_composition','mr_order_bom_costing_booking.mr_composition_id','=','mr_composition.id')
            ->leftJoin('mr_construction','mr_order_bom_costing_booking.mr_construction_id','=','mr_construction.id')
            ->leftJoin('mr_order_costing_approval','mr_order_bom_costing_booking.id','=','mr_order_costing_approval.mr_order_bom_n_costing_id')
            ->leftJoin('mr_order_tna','mr_order_entry.order_id','=','mr_order_tna.order_id')
            ->leftJoin('mr_tna_template','mr_order_tna.mr_tna_template_id','=','mr_tna_template.id')
            ->leftJoin('mr_buyer','mr_order_entry.mr_buyer_b_id','=','mr_buyer.b_id')
            ->where('mr_order_entry.order_id',$id)
            ->first();


        $boms = DB::table("mr_order_bom_costing_booking AS b")
            ->select(
                "b.*",
                "c.mcat_name",
                "i.item_name",
                "i.item_code",
                "mc.clr_code",
                "s.sup_name",
                "a.art_name",
                "com.comp_name",
                "con.construction_name",
                "OE.order_qty"

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
            ->where("b.order_id", $id)
            ->leftJoin('mr_order_entry AS OE', 'OE.order_id', 'b.order_id')
            ->orderBy("b.mr_material_category_mcat_id")
            ->get();

            $febric=[]; $trims=[]; $washInfo=[];
            $reconData= new stdClass;
            $reconData->fabs = new stdClass;
            $reconData->trims = new stdClass;


            foreach ($boms as $key => $bom){
                $mcat = $bom->mr_material_category_mcat_id;
                if($mcat == 1 ){
                    $febric[$key] = new stdClass;
                    $febric[$key]->price = $bom->precost_unit_price??0;
                    $febric[$key]->yy = ($bom->extra_percent / 100)+($bom->consumption);
                    $febric[$key]->cost = ($bom->precost_unit_price)*($febric[$key]->yy);
                }
                if($mcat == 2 || $mcat == 3){
                    $trims[$key] = new stdClass;
                    $trims[$key]->t_sum = $bom->precost_unit_price??0;
                }
            }
            $reconData->fabs = $febric;
            $reconData->trims = $trims;

            $wash = OrderOperationNCost::where([
                        'mr_order_entry_order_id'=>$id,
                        'mr_operation_opr_id' => 9
                        ])
                        ->get();
            $reconData->wash = new stdClass;
            foreach ($wash as $key => $ws){
                $washInfo[$key] = new stdClass;
                $washInfo[$key]->cost = $ws->unit_price;
            }
            $reconData->wash = $washInfo;



             $special_operation = DB::table("mr_order_operation_n_cost AS oc")
                ->select(
                    "oc.*",
                    "o.opr_name"
                )
                ->leftJoin("mr_operation AS o", "o.opr_id", "=", "oc.mr_operation_opr_id")
                ->where("oc.mr_order_entry_order_id", $id)
                ->where("oc.opr_type", 2)
                ->get();




           //dd($reconData);exit;
//        $febric_price = $boms->consumption;

        $other_cost = OrderBomOtherCosting::where('mr_order_entry_order_id', $id)->get();
        $other_cost1 = OrderBomOtherCosting::where('mr_order_entry_order_id', $id)->first();
        //dd($other_cost);exit;

        $steps = $this->getOrderSteps($id,$order->hr_unit_id,$order->b_id);

        //order wise tna action

        $order_approve = DB::table('mr_order_entry')
            ->join('mr_order_bom_costing_booking','mr_order_entry.order_id','=','mr_order_bom_costing_booking.order_id')
            ->join('mr_order_costing_approval','mr_order_bom_costing_booking.id','=','mr_order_costing_approval.mr_order_bom_n_costing_id')
            ->where('mr_order_entry.order_id',$order->order_id)
            ->get();
//          dd($order_approve);exit;

        $order_tna = DB::table('mr_order_entry')
            ->join('mr_order_tna_action','mr_order_entry.order_id','=','mr_order_tna_action.mr_order_entry_order_id')
            ->join('mr_tna_library','mr_order_tna_action.mr_tna_library_id','=','mr_tna_library.id')
            ->where('mr_order_entry.order_id',$order->order_id)
            ->get();
//        dd($order_tna);exit;

        $order_purchase = DB::table('mr_purchase_order')
            ->join('mr_country','mr_purchase_order.po_delivery_country','=','mr_country.cnt_id')
            ->where('mr_purchase_order.mr_order_entry_order_id',$id)
            ->get();

        $order_booking = DB::table('mr_po_booking_detail as a')
            ->select('b.booking_ref_no','d.b_name','s.sup_name')
            ->where('a.mr_order_entry_order_id',$id)
            ->leftJoin('mr_po_booking as b','a.mr_po_booking_id','=','b.id')
            ->leftJoin("mr_supplier as s", "s.sup_id", "b.mr_supplier_sup_id")
            ->leftJoin('mr_buyer as d','d.b_id','=','b.mr_buyer_b_id')
            ->distinct('b.id')
            ->get();
        //dd($order_booking);

        return view('merch/orders/order_profile_show',
            compact('order','boms','other_cost','order_tna','order_approve','order_purchase','other_cost1','special_operation','reconData','id','order_booking','steps'));
    }



    public function orderProfilePdf($id)
    {
        $order = DB::table('mr_order_entry')
            ->leftJoin('mr_capacity_reservation','mr_order_entry.res_id','=','mr_capacity_reservation.res_id')
            ->leftJoin('hr_unit','mr_capacity_reservation.hr_unit_id','=','hr_unit.hr_unit_id')
            ->leftJoin('mr_brand','mr_order_entry.mr_brand_br_id','=','mr_brand.br_id')
            ->leftJoin('mr_product_type','mr_capacity_reservation.prd_type_id','=','mr_product_type.prd_type_id')
            ->leftJoin('mr_style','mr_order_entry.mr_style_stl_id','=','mr_style.stl_id')
            ->leftJoin('mr_garment_type','mr_style.gmt_id','=','mr_garment_type.gmt_id')
            ->leftJoin('mr_season','mr_style.mr_season_se_id','=','mr_season.se_id')
            ->leftJoin('mr_purchase_order','mr_order_entry.order_id','=','mr_purchase_order.mr_order_entry_order_id')
            ->leftJoin('mr_country','mr_purchase_order.po_delivery_country','=','mr_country.cnt_id')
            ->leftJoin('mr_order_bom_costing_booking','mr_order_entry.order_id','=','mr_order_bom_costing_booking.order_id')
            ->leftJoin('mr_material_category','mr_order_bom_costing_booking.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
            ->leftJoin('mr_cat_item','mr_order_bom_costing_booking.mr_cat_item_id','=','mr_cat_item.id')
            ->leftJoin('mr_composition','mr_order_bom_costing_booking.mr_composition_id','=','mr_composition.id')
            ->leftJoin('mr_construction','mr_order_bom_costing_booking.mr_construction_id','=','mr_construction.id')
            ->leftJoin('mr_order_costing_approval','mr_order_bom_costing_booking.id','=','mr_order_costing_approval.mr_order_bom_n_costing_id')
            ->leftJoin('mr_order_tna','mr_order_entry.order_id','=','mr_order_tna.order_id')
            ->leftJoin('mr_tna_template','mr_order_tna.mr_tna_template_id','=','mr_tna_template.id')
            ->leftJoin('mr_buyer','mr_order_entry.mr_buyer_b_id','=','mr_buyer.b_id')
            ->where('mr_order_entry.order_id',$id)
            ->first();

        $boms = DB::table("mr_order_bom_costing_booking AS b")
            ->select(
                "b.*",
                "c.mcat_name",
                "i.item_name",
                "i.item_code",
                "mc.clr_code",
                "s.sup_name",
                "a.art_name",
                "com.comp_name",
                "con.construction_name",
                "OE.order_qty"

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
            ->where("b.order_id", $id)
            ->leftJoin('mr_order_entry AS OE', 'OE.order_id', 'b.order_id')
            ->orderBy("b.mr_material_category_mcat_id")
            ->get();

            $febric=[]; $trims=[]; $washInfo=[];
            $reconData= new stdClass;
            $reconData->fabs = new stdClass;
            $reconData->trims = new stdClass;


            foreach ($boms as $key => $bom){
                $mcat = $bom->mr_material_category_mcat_id;
                if($mcat == 1 ){
                    $febric[$key] = new stdClass;
                    $febric[$key]->price = $bom->precost_unit_price??0;
                    $febric[$key]->yy = ($bom->extra_percent / 100)+($bom->consumption);
                    $febric[$key]->cost = ($bom->precost_unit_price)*($febric[$key]->yy);
                }
                if($mcat == 2 || $mcat == 3){
                    $trims[$key] = new stdClass;
                    $trims[$key]->t_sum = $bom->precost_unit_price??0;
                }
            }
            $reconData->fabs = $febric;
            $reconData->trims = $trims;

            $wash = OrderOperationNCost::where([
                        'mr_order_entry_order_id'=>$id,
                        'mr_operation_opr_id' => 9
                        ])
                        ->get();
            $reconData->wash = new stdClass;
            foreach ($wash as $key => $ws){
                $washInfo[$key] = new stdClass;
                $washInfo[$key]->cost = $ws->unit_price;
            }
            $reconData->wash = $washInfo;



             $special_operation = DB::table("mr_order_operation_n_cost AS oc")
                ->select(
                    "oc.*",
                    "o.opr_name"
                )
                ->leftJoin("mr_operation AS o", "o.opr_id", "=", "oc.mr_operation_opr_id")
                ->where("oc.mr_order_entry_order_id", $id)
                ->where("oc.opr_type", 2)
                ->get();




           //dd($reconData);exit;
//        $febric_price = $boms->consumption;

        $other_cost = OrderBomOtherCosting::where('mr_order_entry_order_id', $id)->get();
        $other_cost1 = OrderBomOtherCosting::where('mr_order_entry_order_id', $id)->first();
        //dd($other_cost);exit;

        //order wise tna action

        $order_approve = DB::table('mr_order_entry')
            ->join('mr_order_bom_costing_booking','mr_order_entry.order_id','=','mr_order_bom_costing_booking.order_id')
            ->join('mr_order_costing_approval','mr_order_bom_costing_booking.id','=','mr_order_costing_approval.mr_order_bom_n_costing_id')
            ->where('mr_order_entry.order_id',$order->order_id)
            ->get();
//          dd($order_approve);exit;

        $order_tna = DB::table('mr_order_entry')
            ->join('mr_order_tna_action','mr_order_entry.order_id','=','mr_order_tna_action.mr_order_entry_order_id')
            ->join('mr_tna_library','mr_order_tna_action.mr_tna_library_id','=','mr_tna_library.id')
            ->where('mr_order_entry.order_id',$order->order_id)
            ->get();
//        dd($order_tna);exit;

        $order_purchase = DB::table('mr_purchase_order')
            ->join('mr_country','mr_purchase_order.po_delivery_country','=','mr_country.cnt_id')
            ->where('mr_purchase_order.mr_order_entry_order_id',$id)
            ->get();

        $order_booking = DB::table('mr_po_booking_detail as a')
            ->select('b.booking_ref_no','d.b_name','s.sup_name')
            ->where('a.mr_order_entry_order_id',$id)
            ->leftJoin('mr_po_booking as b','a.mr_po_booking_id','=','b.id')
            ->leftJoin("mr_supplier as s", "s.sup_id", "b.mr_supplier_sup_id")
            ->leftJoin('mr_buyer as d','d.b_id','=','b.mr_buyer_b_id')
            ->distinct('b.id')
            ->get();


        $pdf = PDF::loadView('merch/orders/order_profile_pdf', [
                'order'           =>$order,
                'boms'          =>$boms,
                'other_cost'         =>$other_cost,
                'order_tna'        =>$order_tna,
                'order_approve'     =>$order_approve,
                'order_purchase'     =>$order_purchase,
                'other_cost1'     =>$other_cost1,
                'special_operation'  =>$special_operation,
                'reconData'          =>$reconData,
                'order_booking'     =>$order_booking
              ]);
        return $pdf->download('Order_Profile_'.date('d_F_Y').'.pdf');
        // return view('merch/orders/order_profile_pdf',
        //     compact('order','boms','other_cost','order_tna','order_approve','order_purchase','other_cost1','special_operation','reconData'));
    }

}
