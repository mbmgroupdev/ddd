<?php

namespace App\Http\Controllers\Merch;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\ProductType;
use App\Models\Merch\Reservation;
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
            ->select('res_id', DB::raw("SUM(order_qty) AS sum"))
            ->whereIn('unit_id', auth()->user()->unit_permissions())
            ->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions())
            ->pluck('sum', 'res_id');

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
                return $ordered[$data->id]??0;

            })
            ->addColumn('balance', function($data) use ($ordered){
                return $data->res_quantity - (isset($ordered[$data->id])?($ordered[$data->id]??0):0);

            })
            ->addColumn('status', function ($data){
                
                if((date('Y') == (int)$data->res_year) && (date('m') >= (int)$data->res_month - 1) ){
                  $rdata = '<button class="btn btn-xs btn-danger btn-round" rel="tooltip" data-tooltip="Date Expired" data-tooltip-location="top" >
                            Closed</button>';

               }else{
                    $rdata = '';
               }
               return $rdata;

            })
            ->addColumn('action', function($data) use ($ordered){
                $mytime = date("Y-m-d");
                $mnth = explode('-',$mytime);
               
                if(((int)$mnth[0] == (int)$data->res_year) && (((int)$mnth[1]) >= (int)$data->res_month - 1) ){
                    $rd = 'style="pointer-events: none;"';//1=closed
                }else{
                    $rd = '';//2=not closed
                }
                $action_buttons= "<div>
                    <a href='#' class=\"btn btn-sm btn-success\" data-toggle=\"tooltip\" title=\"Edit Reservation\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a> ";
                    if($data->res_quantity > (isset($ordered[$data->id])?($ordered[$data->id]??0):0)) {
                        $action_buttons.= "<a href='#' class=\"btn btn-sm btn-primary\" data-toggle='tooltip' title=\"Order Entry\">
                            <i class=\"ace-icon fa fa-cart-plus bigger-120\"></i>
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
        $data['value'] = [];
        $yearMonth = explode('-', $input['res_year_month']);
        $input['res_month'] = $yearMonth[1];
        $input['res_year'] = $yearMonth[0];
        return $input;
        DB::beginTransaction();
        try {
            // check reservation exists
            $getRes = Reservation::checkReservationExists($input);
            if($getRes == true){
                $data['message'] = "Reservation already exists.";
                return response()->json($data);
            }

            // create reservation
            $resId = Reservation::create($input)->id;
            // check & create order base on mr_style_stl_id
            // if()
            DB::commit();
            $data['type'] = 'success';
            $data['message'] = "Reservation Successfully Save.";
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
}
