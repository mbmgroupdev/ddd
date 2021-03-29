<?php

namespace App\Http\Controllers\Merch;

use App\Http\Controllers\Controller;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\PurchaseOrder;
use App\Models\Merch\Reservation;
use App\Models\Merch\Style;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unitList= unit_by_id();
        $unitList= collect($unitList)->pluck('hr_unit_name', 'hr_unit_id');
        $buyerList= buyer_by_id();
        $buyerList= collect($buyerList)->pluck('b_name', 'b_id');
        $brandList= brand_by_id();
        $seasonList= season_by_id();
        $seasonList= collect($seasonList)->pluck('se_name', 'se_id');
        return view("merch/orders/order_list", compact('unitList','buyerList','brandList','seasonList'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = OrderEntry::orderInfoWithStyle($id);
        $pagesize = '';
        return view('merch.orders.show', compact('order', 'pagesize'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $order = OrderEntry::orderInfoWithStyle($id);
            $unitList = unit_by_id();
            $buyerList = buyer_by_id();
            $seasonList = season_by_id();
            $getRes = Reservation::getReservationIdWiseReservation($order->res_id);
            $getOrd = OrderEntry::getResIdWiseOrder($order->res_id);
            $resQty = (($getRes->res_quantity??0) - $getOrd->sum) + $order->order_qty;

            $poQty = PurchaseOrder::getPoOrderSumQtyOrderIdWise($id);
            $poQty = $poQty??0;

            return view('merch.orders.edit', compact('order', 'unitList', 'buyerList', 'poQty', 'resQty', 'seasonList'));
        } catch (\Exception $e) {
            return 'error';
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $data['type'] = 'error';
        $yearMonth = explode('-', $input['ord_year_month']);
        $input['order_month'] = $yearMonth[1];
        $input['order_year'] = $yearMonth[0];
        // return $input;
        DB::beginTransaction();
        try {
            // get order
            $getOrder = OrderEntry::getOrderInfoIdWise($id);
            if($getOrder == null){
                $data['message'] = "Order Not Found!";
            }

            // update order
            $ordId = OrderEntry::where('order_id', $id)->update([
                'order_month'         => $input['order_month'],
                'order_year'          => $input['order_year'],
                'order_ref_no'        => $input['order_ref_no'],
                'order_qty'           => $input['order_qty'],
                'order_delivery_date' => $input['order_delivery_date'],
                'pcd'                 => $input['pcd'],
                'updated_by'          => auth()->user()->id
            ]);

            $data['url'] = url()->previous();
            $data['message'] = "Order Successfully Update.";
            
            DB::commit();
            $data['type'] = 'success';
            return response()->json($data);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function list()
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
            $team = array_merge($team_members_associateId->toArray(),$team_lead->toArray());

        }else{
            $team =[];
        }
        $getBuyer = buyer_by_id();
        $getUnit = unit_by_id();
        $getSeason = season_by_id();
        // return $getUnit;
        $queryData = DB::table('mr_order_entry AS OE')
            ->select([
                "OE.order_id",
                "OE.order_code",
                "OE.mr_buyer_b_id",
                "OE.unit_id",
                "stl.stl_year",
                "stl.mr_season_se_id",
                "stl.stl_no",
                "OE.order_ref_no",
                "OE.order_qty",
                "OE.order_delivery_date",
                "OE.created_by"
            ])
            ->whereIn('OE.mr_buyer_b_id', auth()->user()->buyer_permissions());
            if(!empty($team)){
                $queryData->whereIn('OE.created_by', $team);
            }
            $queryData->leftJoin('mr_style AS stl', 'stl.stl_id', "OE.mr_style_stl_id")
            ->orderBy('order_id', 'DESC');
        $data = $queryData->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('order_code', function ($data){
                return '<a class="add-new" data-orderid="'.$data->order_id.'" data-type="Order View" data-toggle="tooltip" data-placement="top" title="" data-original-title="Order View">'.$data->order_code.'</a>';
            })
            ->addColumn('b_name', function ($data) use ($getBuyer){
                return $getBuyer[$data->mr_buyer_b_id]->b_name??'';
            })
            ->addColumn('hr_unit_name', function ($data) use ($getUnit){
                return $getUnit[$data->unit_id]['hr_unit_name']??'';
            })
            ->addColumn('se_name', function ($data) use ($getSeason){
                return $getSeason[$data->mr_season_se_id]->se_name??''. '-'.$data->stl_year;
            })
            ->editColumn('order_delivery_date', function($data){
                return custom_date_format($data->order_delivery_date);
            })
            ->addColumn('action', function ($data) {
                $action_buttons = "<div class=\"btn-group\">
                    <a href='#' class=\"btn btn-sm btn-secondary add-new\" data-type=\"Order Edit\" data-toggle=\"tooltip\" title=\"Order Edit\" data-orderid=\"$data->order_id\">
                    <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>
                    <a href='".url("merch/order/bom/$data->order_id")."' class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Order BOM\">
                    <i class=\"las la-clipboard-list\"></i>
                    </a>
                    <a href='".url("merch/order/costing/$data->order_id")."' class=\"btn btn-sm btn-warning\" data-toggle=\"tooltip\" title=\"Order Costing\">
                    <i class=\"las la-clipboard-list\"></i>
                    </a>
                    <a href='".url("merch/po-order?order_id=$data->order_id")."' class=\"btn btn-sm btn-success\" data-toggle=\"tooltip\" title=\"Order PO\">
                    <i class=\"las la-shopping-cart\"></i>
                    </a>
                    </div>";
                return $action_buttons;
            })
            ->rawColumns(['order_code', 'order_ref_no', 'hr_unit_name', 'b_name', 'se_name', 'stl_no', 'order_qty', 'order_delivery_date', 'action'])
            ->make(true);
    }
}
