<?php

namespace App\Http\Controllers\Merch\OrderBreakdown;

use App\Models\Merch\OrderDetailsBooking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use DataTables;

class OrderBreakDownController extends Controller
{
    public function index()
    {
    	return view('merch.order_breakdown.order_breakdown');
    }

    public function orderData()
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
      if(!empty($team)){
        $orders = DB::table('mr_order_entry as moe')
                              ->select(
                              	"moe.order_id",
                                  "moe.order_code",
                                  "hu.hr_unit_name",
                                  "mb.b_name",
                                  "mbr.br_name",
                                  "ms.se_name",
                                  "mstl.stl_no",
                                  "moe.order_qty",
                                  "moe.created_by",
                                  "moe.order_delivery_date"
                              )
              ->leftJoin("hr_unit AS hu", "hu.hr_unit_id", "=",  "moe.unit_id")
              ->leftJoin("mr_buyer As mb", "mb.b_id","=","moe.mr_buyer_b_id")
              ->leftJoin("mr_brand As mbr", "mbr.br_id","=", "moe.mr_brand_br_id")
              ->leftJoin("mr_season As ms","ms.se_id","=","moe.mr_season_se_id")
              ->leftJoin("mr_style As mstl", "mstl.stl_id","=","moe.mr_style_stl_id")
              ->whereIn('moe.created_by', $team)
              ->orderBy('moe.order_id','desc')
              ->get();
      }else{
        $orders = DB::table('mr_order_entry as moe')
                              ->select(
                              	"moe.order_id",
                                  "moe.order_code",
                                  "hu.hr_unit_name",
                                  "mb.b_name",
                                  "mbr.br_name",
                                  "ms.se_name",
                                  "mstl.stl_no",
                                  "moe.order_qty",
                                  "moe.created_by",
                                  "moe.order_delivery_date"
                              )
              ->leftJoin("hr_unit AS hu", "hu.hr_unit_id", "=",  "moe.unit_id")
              ->leftJoin("mr_buyer As mb", "mb.b_id","=","moe.mr_buyer_b_id")
              ->leftJoin("mr_brand As mbr", "mbr.br_id","=", "moe.mr_brand_br_id")
              ->leftJoin("mr_season As ms","ms.se_id","=","moe.mr_season_se_id")
              ->leftJoin("mr_style As mstl", "mstl.stl_id","=","moe.mr_style_stl_id")
              ->orderBy('moe.order_id','desc')
              ->get();
      }

        //dd($orders);exit;


        return DataTables::of($orders)
            ->addIndexColumn()
            ->editColumn('action', function ($orders) {
                $return = "<div class=\"btn-group\">";
                $dd = DB::table('mr_order_detail_n_booking')->where('mr_order_entry_order_id',$orders->order_id)->first();
                if(!empty($dd)){
                    $return .= "
                                  <a href=".url('merch/order_breakdown_edit/'.$orders->order_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                                 <i class=\"ace-icon fa fa-edit \"></i>
                                  </a>
                                  ";
                }else{
                    $return .= "<a href=".url('merch/order_breakdown/show/'.$orders->order_id)." class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" title=\"Show\">
                                 <i class=\"ace-icon fa fa-plus \"></i>
                                  </a>
                                  ";
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'action'
            ])

            ->make(true);
    }

    public function show($id)
    {
//        dd($id);
      try {
      	$order = DB::table('mr_order_entry')
      	          ->join('hr_unit','mr_order_entry.unit_id','=','hr_unit.hr_unit_id')
      	          ->join('mr_buyer','mr_order_entry.mr_buyer_b_id','=','mr_buyer.b_id')
      	          ->join('mr_season','mr_order_entry.mr_season_se_id','=','mr_season.se_id')
      	          ->join('mr_style','mr_order_entry.mr_style_stl_id','=','mr_style.stl_id')
      	          ->join('mr_brand','mr_style.mr_brand_br_id','mr_brand.br_id')
      	          ->where('mr_order_entry.order_id',$id)
      	          ->first();

          $poList = DB::table('mr_purchase_order as a')
                      ->select(
                        'a.po_id',
                        'a.mr_order_entry_order_id',
                        'a.po_no',
                        'a.po_qty',
                        'b.cnt_name',
                        'mc.clr_name'
                      )
                      ->leftJoin('mr_country as b','b.cnt_id','a.po_delivery_country')
                      ->leftJoin("mr_material_color AS mc", "mc.clr_id", "a.clr_id")
                      ->where('a.mr_order_entry_order_id',$id)->get();

          //dd($poList);

          $boms = DB::table("mr_order_bom_costing_booking AS b")
              ->select(
                  "b.*",
                  "c.mcat_name",
                  "i.item_name",
                  "i.item_code",
                  "i.dependent_on",
                  "mc.clr_code",
                  "s.sup_name",
                  "a.art_name",
                  "com.comp_name",
                  "con.construction_name",
                  "OE.order_qty",
                  'PO.po_no as po_po_no',
                  'PO.po_id as po_po_id',
                  'PO.po_qty as po_po_qty',
                  'POS.po_sub_style_id as po_pos_id',
                  'POS.clr_id as po_pos_cid',
                  'POS.po_sub_style_qty as po_pos_sqty'
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

              ->leftJoin('mr_purchase_order AS PO', 'PO.po_id', 'b.po_no')
              ->leftJoin('mr_po_sub_style AS POS', 'POS.po_id', 'PO.po_id')
              ->orderBy("b.mr_material_category_mcat_id",'ASC')
              ->groupBy("i.id")
              ->get();
          // dd($poList,$boms);exit;


          $colors = DB::table('mr_order_entry')
              ->groupBy('mr_material_color.clr_name')
              ->selectRaw('sum(mr_po_sub_style.po_sub_style_qty) as sum, mr_material_color.clr_name as clr_name, mr_material_color.clr_id')
              ->leftJoin('mr_purchase_order','mr_order_entry.order_id','=','mr_purchase_order.mr_order_entry_order_id')
              ->leftJoin('mr_po_sub_style','mr_purchase_order.po_id','=','mr_po_sub_style.po_id')
              ->leftJoin('mr_material_color','mr_po_sub_style.clr_id','=','mr_material_color.clr_id')
              ->where('mr_order_entry.order_id',$id)
              ->orderBy('mr_material_color.clr_name','ASC')
              ->get();
          //dd($colors);exit;

          $sizes = DB::table('mr_order_entry')
              ->leftJoin('mr_stl_size_group','mr_order_entry.mr_style_stl_id','=','mr_stl_size_group.mr_style_stl_id')
              ->leftJoin('mr_product_size','mr_stl_size_group.mr_product_size_group_id','=','mr_product_size.mr_product_size_group_id')
              ->where('mr_order_entry.order_id',$id)
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
              ->where('mr_order_entry.order_id',$id)

              ->get();

          // po sub item qty amount
          $poDataList = DB::table('mr_purchase_order as a')
          ->select([
              'a.po_id',
              'a.mr_order_entry_order_id',
              'b.clr_id',
              'b.po_sub_style_id',
              'b.mr_product_size_id'
          ])
          ->join('mr_po_sub_style as b', function($query) {
              $query->on('b.po_id','=','a.po_id');
          })
          ->where('a.mr_order_entry_order_id',$id)
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
              $poSizeAr[$poDataL->po_sub_style_id] = DB::table('mr_po_size_qty')
                                                          ->where('mr_po_sub_style_id',$poDataL->po_sub_style_id)->get();
              $poColorAr[$poDataL->po_sub_style_id] = DB::table('mr_po_sub_style')
                                                          ->where('po_sub_style_id',$poDataL->po_sub_style_id)->get();
              foreach($poSizeAr[$poDataL->po_sub_style_id] as $key1=>$poSizeSingle) {
                  $poSizeQtyList[$poDataL->clr_id][$poDataL->po_sub_style_id][$key][$poSizeSingle->mr_product_size_id] = $poSizeSingle->qty;
                  $poSizeQtyListC[$poDataL->po_id][$poDataL->clr_id][$poDataL->po_id][$key][$poSizeSingle->mr_product_size_id] = $poSizeSingle->qty;
                  $poSizeQtyListS[$poDataL->po_id][$poSizeSingle->mr_product_size_id][$key] = $poSizeSingle->qty;
              }
              foreach($poColorAr[$poDataL->po_sub_style_id] as $key1=>$poColorSingle) {
                  $poColorQtyList[$poDataL->clr_id][$poDataL->po_sub_style_id][$key] = $poColorSingle->po_sub_style_qty;
                  $poColorQtyListC[$poDataL->po_id][$poDataL->clr_id][$poDataL->po_id][$key] = $poColorSingle->po_sub_style_qty;
              }
          }

          foreach($boms as $bkey=>$bom) {
              if($bom->po_pos_id == null) {
                  foreach($poDataList as $key=>$poDataL) {
                      $poSizeArN[$bom->order_id][$key] = DB::table('mr_po_size_qty')
                                                                  ->where('mr_po_sub_style_id',$poDataL->po_sub_style_id)->get();
                      $poColorArN[$bom->order_id][$key] = DB::table('mr_po_sub_style')
                                                          ->where('po_sub_style_id',$poDataL->po_sub_style_id)->get();
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
          // dd($poColorQtyListC);
          // end po sub item qty amount

          $filter = array();

          foreach($care_label as $filter_result) {
              $filter[$filter_result->clr_id] = $filter_result->clr_name;
          }
          $filter = array_unique($filter);
          $bomsCat = array_column($boms->toArray(), 'mcat_name');
          $bomsItem = array_column($boms->toArray(), 'item_name');
          $itemUnique = array_unique($bomsItem);
          $catCount = array_count_values($bomsCat);
          // return $itemUnique;
          // dd($poSizeQtyListS, $sizes, $poSizeAr, $poDataList, $colors, $filter,$poSizeQtyList,$poSizeQtyListC,$boms);exit;
          // dd($poSizeQtyListN);exit;

      	return view('merch.order_breakdown.order_breakdown_show',
              compact('order','boms','colors','sizes','care_label','filter','poSizeQtyList','poSizeQtyListC','poSizeQtyListS','poList', 'itemUnique', 'catCount','poSizeQtyListN','poSizeQtyListCN','poSizeQtyListSN','poColorQtyList','poColorQtyListC','poColorQtyListN','poColorQtyListCN'));
      } catch(\Exception $e) {
          return $e->getMessage();
      }
    }

    public function store(Request $request)
    {
      if ($request->isMethod('post')) {
          $data = $request->all();
          //echo "<pre>";print_r($data);die;
             // dd($data);exit;
           $order = new OrderDetailsBooking();
           $order = [];
           $input = $request->all();

           for($i=0; $i<sizeof($request->items); $i++){

               //dd($request->items);exit;
               if(isset($input['bom_costing_booking_id-'.$i])) {
                   $bom_id = $input['bom_costing_booking_id-'.$i];
                   $order_id = $input['order_entry_order_id-'.$i];
                   $cat_id = $input['cat_item_id-'.$i];
                   $cat_mcat = $input['cat_item_mcat_id-'.$i];

                   for ($k=0; $k<sizeof($bom_id); $k++)
                   {
                       $order['mr_order_bom_costing_booking_id'] = $bom_id[$k];
                       $order['mr_order_entry_order_id'] = $order_id[$k];
                       $order['mr_cat_item_id'] = $cat_id[$k];
                       $order['mr_cat_item_mcat_id'] = $cat_mcat[$k];
                   }
               }

               /*--------color dependent----------*/

               if (isset($input['clr_id-' . $i])) {
                   $color = $input['clr_id-' . $i];
                   $qtys = $input['qty-'.$i];
                   $req_qty = $input['req_qty-' . $i];

                   for ($j = 0; $j < sizeof($color); $j++) {
                       $order['mr_material_color_clr_id'] = $color[$j];
                       $order['qty'] = $qtys[$j];
                       $order['size']=null;
                       $order['req_qty'] = $req_qty[$j];
                       $data [] = $order;
                       DB::table('mr_order_detail_n_booking')->insert($order);
                   }


               }

               /*--------size dependent----------*/

               if (isset($input['sizer-'.$i]))
               {
                   //$color = $input['clr_id-' . $i];
                   $sizes = $input['sizer-'.$i];
                   $sqs = $input['size_qtyss-'.$i];
                   $req_qtys = $input['req_qtyr-'.$i];

                   for($c=0; $c < sizeof($sizes); $c++)
                  {
                      $order['mr_material_color_clr_id'] = null;
                      $order['size'] = $sizes[$c];
                      $order['qty'] = $sqs[$c];
                      $order['req_qty'] = $req_qtys[$c];
                      $data [] = $order;
                      DB::table('mr_order_detail_n_booking')->insert($order);
                  }
              }

              /*--------color & size dependent----------*/

               if(isset($input['s_qtyss-'.$i]))
               {
                   $sizess = $input['sizes-' . $i];
                   $yqs = $input['s_qtyss-'.$i];
                   $colors = $input['clr_ids-'.$i];
                   $req_qtyes = $input['req_qtys-' . $i];


                   for ($z = 0; $z < sizeof($colors); $z++) {
                       $order['mr_material_color_clr_id'] = $colors[$z];
                       $order['size'] = $sizess[$z];
                       $order['qty'] = $yqs[$z];
                       $order['req_qty'] = $req_qtyes[$z];

                       $data [] = $order;
                       DB::table('mr_order_detail_n_booking')->insert($order);
                   }

               }

               /*--------No dependency----------*/

               if (isset($input['order_qty-'.$i]))
               {
                   $order['qty'] = $input['order_qty-'.$i][0];
                   $order['req_qty'] = $input['order_req_qty-'.$i][0];
                   $order['mr_material_color_clr_id'] = null;
                   $order['size'] = null;

                   $data [] = $order;
                   DB::table('mr_order_detail_n_booking')->insert($order);
               }
           }

           $this->logFileWrite("Order Break Down Saved where order id is", $data['order_entry_order_id-0'][0]);
          // return redirect('merch.order_breakdown')->with('success', "Order Break Down Information Successfully Added!!!");
          //return back()->with('success', "Order Break Down Information Successfully Added!!!");
          return redirect('merch/order_breakdown_edit/'.$data['order_entry_order_id-0'][0])->with('success', 'Order Break Down Information Successfully Added!!!');;
      }
    }

    public function edit($id)
    {
      try {
        $order = DB::table('mr_order_entry')
        ->join('hr_unit','mr_order_entry.unit_id','=','hr_unit.hr_unit_id')
        ->join('mr_buyer','mr_order_entry.mr_buyer_b_id','=','mr_buyer.b_id')
        ->join('mr_brand','mr_order_entry.mr_brand_br_id','mr_brand.br_id')
        ->join('mr_season','mr_order_entry.mr_season_se_id','=','mr_season.se_id')
        ->join('mr_style','mr_order_entry.mr_style_stl_id','=','mr_style.stl_id')
        ->where('mr_order_entry.order_id',$id)
        ->first();


        $order_break = DB::table('mr_order_detail_n_booking')
        //->groupBy('mr_order_detail_n_booking.mr_material_color_clr_id')
        ->select(
            "mr_order_detail_n_booking.*",
            "mr_order_bom_costing_booking.item_description",
            "mr_order_bom_costing_booking.consumption",
            "mr_order_bom_costing_booking.extra_percent",
            "mr_order_bom_costing_booking.uom",
            "mr_order_bom_costing_booking.precost_unit_price",
            "mr_order_bom_costing_booking.order_id",
            "mr_order_bom_costing_booking.mr_cat_item_id",
            "mr_order_bom_costing_booking.mr_material_category_mcat_id",
            "mr_material_category.mcat_name",
            "mr_cat_item.item_name",
            "mr_order_bom_costing_booking.depends_on",
            "mr_cat_item.item_code",
            "mr_cat_item.dependent_on",
            "mr_supplier.sup_name",
            "mr_article.art_name",
            "mr_composition.comp_name",
            "mr_construction.construction_name",
            "mr_material_color.clr_name",
            "mr_material_color.clr_id"
        )

        ->leftJoin('mr_material_color','mr_order_detail_n_booking.mr_material_color_clr_id','=','mr_material_color.clr_id')

        ->leftJoin('mr_order_bom_costing_booking','mr_order_detail_n_booking.mr_order_bom_costing_booking_id','=','mr_order_bom_costing_booking.id')

        ->leftJoin('mr_material_category','mr_order_bom_costing_booking.mr_material_category_mcat_id','=','mr_material_category.mcat_id')

        ->leftJoin('mr_cat_item','mr_order_bom_costing_booking.mr_cat_item_id','=','mr_cat_item.id')

        ->leftJoin('mr_supplier','mr_order_bom_costing_booking.mr_supplier_sup_id','=','mr_supplier.sup_id')

        ->leftJoin('mr_article','mr_order_bom_costing_booking.mr_article_id','=','mr_article.id')

        ->leftJoin('mr_composition','mr_order_bom_costing_booking.mr_composition_id','=','mr_composition.id')

        ->leftJoin('mr_construction','mr_order_bom_costing_booking.mr_construction_id','=','mr_construction.id')

        ->where('mr_order_detail_n_booking.mr_order_entry_order_id',$id)
        ->get()
        ->toArray();
        //dd($order_break);
        $catRow = array_column($order_break, 'item_name');
        $vals = array_count_values($catRow);
        //echo "<pre>"; print_r($vals);exit;

        $colors = DB::table('mr_order_entry')
          ->groupBy('mr_material_color.clr_name')
          ->selectRaw('sum(mr_po_sub_style.po_sub_style_qty) as sum, mr_material_color.clr_name as clr_name, mr_material_color.clr_id')
          //->select( "mr_order_entry.order_id as id", 'mr_material_color.clr_name', 'mr_po_sub_style.po_sub_style_qty')

          ->leftJoin('mr_purchase_order','mr_order_entry.order_id','=','mr_purchase_order.mr_order_entry_order_id')
          ->leftJoin('mr_po_sub_style','mr_purchase_order.po_id','=','mr_po_sub_style.po_id')
          ->leftJoin('mr_material_color','mr_po_sub_style.clr_id','=','mr_material_color.clr_id')
          ->where('mr_order_entry.order_id',$id)
          ->get();
        //dd($colors);exit;
        return view('merch.order_breakdown.order_breakdownEdit',compact('order','colors', 'order_break','vals'));
      } catch (\Exception $e) {
          $bug1 = $e->getMessage();
          return redirect()->back()->with('error', $bug1);
      }
    }

    public function update(Request $request)
    {
      if ($request->isMethod('post')){
          $data = $request->all();
          //echo "<pre>";print_r($data);die;
          //dd($data);exit;

          $orders = DB::table('mr_order_detail_n_booking')->get();
          //dd($order);exit;
          $order = [];
          $input = $request->all();

          for($i=0; $i<sizeof($orders); $i++){

              //dd($request->items);exit;
              if(isset($input['bom_costing_booking_id-'.$i])) {
                  $mr_order_id = $input['id-'.$i];
                  $bom_id = $input['bom_costing_booking_id-'.$i];
                  $order_id = $input['order_entry_order_id-'.$i];
                  $cat_id = $input['cat_item_id-'.$i];
                  $cat_mcat = $input['cat_item_mcat_id-'.$i];

                  for ($k=0; $k<sizeof($bom_id); $k++)
                  {
                      $order['id'] = $mr_order_id[$k];
                      $order['mr_order_bom_costing_booking_id'] = $bom_id[$k];
                      $order['mr_order_entry_order_id'] = $order_id[$k];
                      $order['mr_cat_item_id'] = $cat_id[$k];
                      $order['mr_cat_item_mcat_id'] = $cat_mcat[$k];
                  }

              }

              if (isset($input['clr_id-' . $i])) {
                  $mr_order_id = $input['id-'.$i];
                  $color = $input['clr_id-' . $i];
                  $qtys = $input['qty-'.$i];
                  $req_qty = $input['req_qty-' . $i];

                  for ($j = 0; $j < sizeof($color); $j++) {
                      //dd($color);exit;
                      $order['id'] = $mr_order_id[$j];
                      $order['mr_material_color_clr_id'] = $color[$j];
                      $order['qty'] = $qtys[$j];
                      $order['req_qty'] = $req_qty[$j];
                      $data [] = $order;
                      DB::table('mr_order_detail_n_booking')->where('id',$mr_order_id)->update($order);
                  }

              }

              if (isset($input['sizer-'.$i]))
              {
                  $mr_order_id = $input['id-'.$i];
                  $sizes = $input['sizer-'.$i];
                  $sqs = $input['size_qtyss-'.$i];
                  $req_qtys = $input['req_qtyr-'.$i];

                  for($c=0; $c < sizeof($sizes); $c++)
                  {
                      $order['id'] = $mr_order_id[$c];
                      $order['mr_material_color_clr_id'] = null;
                      $order['size'] = $sizes[$c];
                      $order['qty'] = $sqs[$c];
                      $order['req_qty'] = $req_qtys[$c];
                      $data [] = $order;
                      DB::table('mr_order_detail_n_booking')->where('id',$mr_order_id)->update($order);
                  }
              }


              if(isset($input['s_qtyss-'.$i]))
              {
                  $mr_order_id = $input['id-'.$i];
                  $sizess = $input['sizes-' . $i];
                  $yqs = $input['s_qtyss-'.$i];
                  $colors = $input['clr_ids-'.$i];
                  $req_qtyes = $input['req_qtys-' . $i];


                  for ($z = 0; $z < sizeof($colors); $z++) {
                      $order['id'] = $mr_order_id[$z];
                      $order['mr_material_color_clr_id'] = $colors[$z];
                      $order['size'] = $sizess[$z];
                      $order['qty'] = $yqs[$z];
                      $order['req_qty'] = $req_qtyes[$z];

                      $data [] = $order;
                      DB::table('mr_order_detail_n_booking')->where('id',$mr_order_id)->update($order);
                  }

              }

              if (isset($input['order_qty'.$i]))
              {
                  $order['qty'] = $input['order_qty'];

                  $data [] = $order;
                  DB::table('mr_order_detail_n_booking')->insert($order);
              }

              if (isset($input['order_req_qty'.$i]))
              {
                  $order['req_qty'] = $input['order_req_qty'];

                  $data [] = $order;
                  DB::table('mr_order_detail_n_booking')->insert($order);
              }

          }

          $this->logFileWrite("Order Break Down Updated where order id is", $data['order_entry_order_id-0'][0]);

          return back()->with('success', "Order Break Down Information Successfully Updated!!!");
      }
    }

}
