<?php

namespace App\Http\Controllers\Merch;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Brand;
use App\Models\Merch\Buyer;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\ProductType;
use App\Models\Merch\Reservation;
use App\Models\Merch\Style;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
        $buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
        $prdtypList= ProductType::pluck('prd_type_name', 'prd_type_id');
        return view('merch/reservation/index', compact('unitList', 'buyerList', 'prdtypList'));
    }
    public function getData(){

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
            $team = array_merge($team_members_associateId->toArray(),$team_lead->toArray());
        }else{
            $team =[];
        }
        $queueData = DB::table('mr_capacity_reservation AS cr')
            ->select(
                'cr.*'
            )
            ->whereIn('cr.b_id', auth()->user()->buyer_permissions());
            if(!empty($team)){
                $queueData->whereIn('cr.res_created_by', $team);
            }
            $queueData->orderBy('cr.id', 'DESC');
        $data = $queueData->get();
        $ordered = DB::table('mr_order_entry')
            ->select('res_id', DB::raw("SUM(order_qty) AS qty"))
            // ->whereIn('unit_id', auth()->user()->unit_permissions())
            // ->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions())
            // ->pluck('sum', 'res_id')
            ->groupBy('res_id')
            ->get()
            ->keyBy('res_id', true);
        // dd($ordered);
        $getUnit = unit_by_id();
        $getBuyer = buyer_by_id();
        $getProductType = product_type_by_id();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('hr_unit_name', function($data) use ($getUnit){
                return $getUnit[$data->hr_unit_id]['hr_unit_name']??'';
            })
            ->addColumn('b_name', function($data) use ($getBuyer){
                return $getBuyer[$data->b_id]->b_name??'';
            })
            ->addColumn('month_year', function($data){
                $month_year= date('F', mktime(0, 0, 0, $data->res_month, 10)). "-". $data->res_year;
                return $month_year;
            })
            ->addColumn('prd_type_name', function($data) use ($getProductType){
                return $getProductType[$data->prd_type_id]->prd_type_name??'';
            })
            ->addColumn('projection', function($data){
                return $data->res_quantity;
            })
            ->addColumn('confirmed', function($data) use ($ordered){
                return $ordered[$data->id]->qty??0;

            })
            ->addColumn('balance', function($data) use ($ordered){
                return $data->res_quantity - (isset($ordered[$data->id])?($ordered[$data->id]->qty??0):0);

            })
            ->addColumn('status', function ($data){
                $yearMonth = $data->res_year.'-'.$data->res_month;
                $resLastMonth = date('Y-m', strtotime('-1 month', strtotime($yearMonth)));
                if(strtotime(date('Y-m')) > strtotime($resLastMonth)){
                  $rdata = '<button class="btn btn-xs btn-danger btn-round" rel="tooltip" data-tooltip="Date Expired" data-tooltip-location="top" >
                            Closed</button>';

               }else{
                    $rdata = '';
               }
               return $rdata;

            })
            ->addColumn('action', function($data) use ($ordered){
                $yearMonth = $data->res_year.'-'.$data->res_month;
                $resLastMonth = date('Y-m', strtotime('-1 month', strtotime($yearMonth)));
                $flag = 0;
                if(strtotime(date('Y-m')) > strtotime($resLastMonth)){
                    $flag = 1;
                }
                $action_buttons= "<div>
                    <a href='#' class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit Reservation\">
                        <i class=\"ace-icon fa fa-pencil \"></i>
                    </a> ";
                    if($data->res_quantity > (isset($ordered[$data->id])?($ordered[$data->id]->qty??0):0) && $flag == 0) {
                        $action_buttons.= "<a href='#' class=\"btn btn-xs add-new btn-primary\" data-toggle='tooltip' title=\"Order Entry\" data-type='order' data-resid=\"$data->id\">
                            <i class=\"ace-icon fa fa-cart-plus \"></i>
                        </a>";
                    }
                $action_buttons.= "</div>";
                return $action_buttons;
            })
            ->rawColumns(['hr_unit_name', 'b_name', 'month_year', 'prd_type_name', 'res_sah', 'projection', 'confirmed', 'balance','status','action'])
            ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
        return view('merch/reservation/create', compact('unitList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data['type'] = 'error';
        $yearMonth = explode('-', $input['res_year_month']);
        $input['res_month'] = $yearMonth[1];
        $input['res_year'] = $yearMonth[0];
        $orderNo = make_order_number($input, $input['res_year']);

        DB::beginTransaction();
        try {
            // check reservation exists
            $getRes = Reservation::checkReservationExists($input);
            if($getRes == true){
                $data['message'] = "Reservation already exists.";
                return response()->json($data);
            }

            // create reservation
            $resId = Reservation::insertGetId([
                'hr_unit_id'     => $input['hr_unit_id'],
                'b_id'           => $input['b_id'],
                'res_month'      => $input['res_month'],
                'res_year'       => $input['res_year'],
                'prd_type_id'    => $input['prd_type_id'],
                'res_quantity'   => $input['res_quantity'],
                'res_sah'        => $input['res_sah'],
                'res_sewing_smv' => $input['res_sewing_smv'],
                'res_status'     => 1,
                'created_by'     => auth()->user()->id
            ]);
            $data['url'] = url()->previous();
            $data['message'] = "Reservation Successfully Save.";
            // check & create order base on mr_style_stl_id
            if($input['mr_style_stl_id'] != null && $input['mr_style_stl_id'] != ''){
                // check order qty > reservation qty
                if($input['order_qty'] > $input['res_quantity']){
                    DB::rollback();
                    $data['message'] = "Order quantity can't large then Reservation Quantity!";
                    return response()->json($data);
                }

                // order entry & check
                $orderNo = make_order_number($input, $input['res_year']);
                $order = [
                    'order_code'          => $orderNo,
                    'res_id'              => $resId,
                    'unit_id'             => $input['hr_unit_id'],
                    'mr_buyer_b_id'       => $input['b_id'],
                    'order_month'         => $input['res_month'],
                    'order_year'          => $input['res_year'],
                    'mr_style_stl_id'     => $input['mr_style_stl_id'],
                    'order_ref_no'        => $input['order_ref_no'],
                    'order_qty'           => $input['order_qty'],
                    'order_delivery_date' => $input['order_delivery_date'],
                    'pcd'                 => $input['pcd'],
                    'order_status'        => 1,
                    'order_entry_source'  => 0,
                    'created_by'          => auth()->user()->id
                ];
                $checkOrder = OrderEntry::getCheckOrderExists($order);
                if($checkOrder == true){
                    DB::rollback();
                    $data['message'] = "Order Already Exists";
                    return response()->json($data);
                }

                $orderId = OrderEntry::insertGetId($order);
                $data['url'] = url('/merch/order/bom/').'/'.$orderId;
                $data['message'] = "Order Entry Successfully.";
            }

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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

    public function orderEntry($resid)
    {
        try {
            $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
            
            $reservation = DB::table('mr_capacity_reservation')
                        ->where('id', $resid)
                        ->first();
            $reservation->res_month = date('F', mktime(0, 0, 0, $reservation->res_month, 10));

            $ordered = DB::table('mr_order_entry')
                        ->where('res_id', $resid)
                        ->select(DB::raw("SUM(order_qty) AS sum"))
                        ->first();
            $reservation->res_quantity = $reservation->res_quantity - $ordered->sum;
            $brandList = Brand::where('b_id', $reservation->b_id)->pluck('br_name','br_id');

            $styleList = Style::where('mr_buyer_b_id', $reservation->b_id)
                        ->where('stl_type', "Bulk")
                        ->pluck('stl_no', 'stl_id');
            return view('merch/orders/create', compact('unitList','reservation', 'brandList', 'styleList'));
        } catch (\Exception $e) {
            return 'error';
        }
        
    }
}
