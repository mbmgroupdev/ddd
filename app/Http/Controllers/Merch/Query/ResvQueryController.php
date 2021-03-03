<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Merch\Buyer;
use App\Models\Merch\Reservation;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\ProductType;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class ResvQueryController extends Controller
{
    public function merchResvQuery(Request $request)
    {
        try{

            return $this->merchResvQueryGlobal($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSearchType($request)
    {
        $datecon= new stdClass;
        if($request['type'] == 'month') {
            $datecon->month = date('m', strtotime($request['month']));
            $datecon->myear = date('Y', strtotime($request['month']));
        }else if ($request['type'] == 'range') {
            $datecon->from = date('Y-m', strtotime($request['monthFrom']));
            $datecon->to = date('Y-m', strtotime($request['monthTo']));
        }else if ($request['type'] == 'year') {
            $datecon->year = $request['year'];
        }
        return $datecon;
    }

    public function pageTitle($request){

            $showTitle = 'Reservation'.' - '.ucwords($request['type']) ;
            if(isset($request['month']))
            {
                $showTitle =$showTitle.': '.$request['month'];
            }
            if(isset($request['year']))
            {
                $showTitle =$showTitle.': '.$request['year'];
            }
            if($request['type']=='range'){
                $showTitle =$showTitle.': '.$request['monthFrom'].' to '.$request['monthTo'];
            }

            return $showTitle;
    }



    public function getResvInfo($datecon,$condition){

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
        $asid = DB::table('hr_as_basic_info as b')
           ->where('associate_id',auth()->user()->associate_id)
           ->pluck('as_id');
           $team = $asid;
      }else{
       $team =[];
      }

      if(!empty($team)){
        $resv = Reservation::select('mr_capacity_reservation.*',DB::raw("SUM(o.order_qty) AS confirmed"),DB::raw("COUNT(o.order_qty) AS total_order"))
                    ->where(function($query) use ($datecon) {
                        if(!empty($datecon->month)){
                            $query->where('res_month',$datecon->month);
                            $query->where('res_year', $datecon->myear);
                        }
                        if(!empty($datecon->year)){
                            $query->where('res_year', $datecon->year);
                        }
                        if(!empty($datecon->from)){
                            $query->whereBetween(DB::raw("CONCAT(res_year, '-', res_month)"),[$datecon->from,$datecon->to]);
                        }
                    })
                    ->leftJoin('mr_order_entry AS o','mr_capacity_reservation.res_id','o.res_id')
                    ->where($condition)
                    ->whereIn('mr_capacity_reservation.res_created_by', $team)
                    ->groupBy('mr_capacity_reservation.res_id')
                    ->orderBy('mr_capacity_reservation.res_month','DESC')
                    ->get();
      }else{
        $resv = Reservation::select('mr_capacity_reservation.*',DB::raw("SUM(o.order_qty) AS confirmed"),DB::raw("COUNT(o.order_qty) AS total_order"))
                    ->where(function($query) use ($datecon) {
                        if(!empty($datecon->month)){
                            $query->where('res_month',$datecon->month);
                            $query->where('res_year', $datecon->myear);
                        }
                        if(!empty($datecon->year)){
                            $query->where('res_year', $datecon->year);
                        }
                        if(!empty($datecon->from)){
                            $query->whereBetween(DB::raw("CONCAT(res_year, '-', res_month)"),[$datecon->from,$datecon->to]);
                        }
                    })
                    ->leftJoin('mr_order_entry AS o','mr_capacity_reservation.res_id','o.res_id')
                    ->where($condition)
                    ->groupBy('mr_capacity_reservation.res_id')
                    ->orderBy('mr_capacity_reservation.res_month','DESC')
                    ->get();
      }


        return $resv;
    }

    public function merchResvQueryGlobal($request)
    {
        try {


            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            //$pp=$this->getResvInfo($datecon,[]);
            //return dd($pp);
            $ptypes = ProductType::get();  //select prd_type_id, prd_type_name
            $globalinfo = new stdClass;
            $globalinfo->unit = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->get()->count();
            $globalinfo->buyer = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->get()->count();
            $globalinfo->resv = $this->getResvInfo($datecon,[])->count();

            $globalinfo->data = new stdClass;
            foreach ($ptypes as $key => $type) {
                $typename =$type['prd_type_name'];
                $typeid =$type['prd_type_id'];
                $globalinfo->data->$typeid = new stdClass;
                $globalinfo->data->$typeid->name = $typename;
                $globalinfo->data->$typeid->reserved = $this->getResvInfo($datecon,['prd_type_id' => $typeid])->sum('res_quantity');
                $globalinfo->data->$typeid->confirmed = intval($this->getResvInfo($datecon,['prd_type_id' => $typeid])->sum('confirmed'));
                $globalinfo->data->$typeid->balance=  ($globalinfo->data->$typeid->reserved)- ($globalinfo->data->$typeid->confirmed);
            }

            $result['page'] = view('merch.query.reservation.resvinfo',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }



    public function merchResvQueryUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $request['view']='allunit';
            unset($request['unit'],$request['product'],$request['buyer']);
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);

            $units = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->get();

            $unit_data=[];
            $condition=[];
            foreach ($units as $key => $unit) {
                $condition=['hr_unit_id'=>$unit->hr_unit_id];
                $unit_data[$key]= new stdClass;
                $unit_data[$key]->id = $unit->hr_unit_id;
                $unit_data[$key]->name = $unit->hr_unit_name;
                $unit_data[$key]->resv = $this->getResvInfo($datecon,$condition)->count();
                $unit_data[$key]->data = new stdClass;
                $ptypes = ProductType::get();
                foreach ($ptypes as $key1 => $type) {
                    $typename =$type['prd_type_name'];
                    $typeid =$type['prd_type_id'];
                    $unit_data[$key]->data->$typeid = new stdClass;
                    $unit_data[$key]->data->$typeid->name = $typename;
                    $unit_data[$key]->data->$typeid->reserved = $this->getResvInfo($datecon,['prd_type_id' => $typeid, 'hr_unit_id' => $unit->hr_unit_id ])->sum('res_quantity');
                    $unit_data[$key]->data->$typeid->confirmed = intval($this->getResvInfo($datecon,['prd_type_id' => $typeid, 'hr_unit_id' => $unit->hr_unit_id ])->sum('confirmed'));
                    $unit_data[$key]->data->$typeid->balance=($unit_data[$key]->data->$typeid->reserved)-($unit_data[$key]->data->$typeid->confirmed);
                }

            }

            //return dd($unit_data);
            $result = [];
            $result['page'] = view('merch.query.reservation.allunit',
                compact('unit_data', 'request',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchResvQueryBuyer(Request $request)
    {
        try {
            // get previous url params

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            //return dd($request1);
            $request1['view']= 'allbuyer';

            $datecon = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            $buyer_data = [];

            $buyers = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->get();
            foreach ($buyers as $key => $buyer) {
                $buyer_data[$key]= new stdClass;
                $buyer_data[$key]->id = $buyer->b_id;
                $buyer_data[$key]->name = $buyer->b_name;
                $buyer_data[$key]->resv = $this->getResvInfo($datecon,['b_id' => $buyer->b_id ])->count();
                $buyer_data[$key]->data = new stdClass;
                $ptypes = ProductType::get();
                foreach ($ptypes as $key1 => $type) {
                    $typename =$type['prd_type_name'];
                    $typeid =$type['prd_type_id'];
                    $buyer_data[$key]->data->$typeid = new stdClass;
                    $buyer_data[$key]->data->$typeid->name = $typename;
                    $buyer_data[$key]->data->$typeid->reserved = $this->getResvInfo($datecon,['prd_type_id' => $typeid, 'b_id' => $buyer->b_id ])->sum('res_quantity');
                    $buyer_data[$key]->data->$typeid->confirmed = intval($this->getResvInfo($datecon,['prd_type_id' => $typeid, 'b_id' => $buyer->b_id ])->sum('confirmed'));
                    $buyer_data[$key]->data->$typeid->balance=($buyer_data[$key]->data->$typeid->reserved)-($buyer_data[$key]->data->$typeid->confirmed);
                }
            }
            //return dd($buyer_data);
            $result = [];
            $result['page'] = view('merch.query.reservation.allbuyer',
                compact('buyer_data', 'request1',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchResvQueryResv(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'reservation';
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }

            $data=[];
            if(isset($request1['unit'])){
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            if(isset($request1['buyer'])){
                $data['buyerinfo'] = Buyer::where('b_id', $request1['buyer'])->first();
            }

            $showTitle = $this->pageTitle($request1);


            $result['page'] = view('merch.query.reservation.allresv',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  merchResvQueryListResv(Request $request){
        try {
            //return 'hi';
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            $condition=[];
            if(isset($request1['unit'])){
                $condition['hr_unit_id'] = $request1['unit'];
            }
            if(isset($request1['product'])){
                $condition['prd_type_id'] = $request1['product'];
            }
            if(isset($request1['buyer'])){
                $condition['b_id'] = $request1['buyer'];
            }
            $datecon = $this->getSearchType($request1);
            $resvinfo = $this->getResvInfo($datecon,$condition);
            //return $resvinfo;
            return DataTables::of($resvinfo)->addIndexColumn()
                ->editColumn('unit', function ($resvinfo) {
                    return Unit::where('hr_unit_id',$resvinfo->hr_unit_id)->first()->hr_unit_name??'';
                })
                ->editColumn('buyer_name', function ($resvinfo) {
                    return Buyer::where('b_id', $resvinfo->b_id)->first()->b_name??$resvinfo->b_id??'';
                })
                ->editColumn('product', function ($resvinfo) {
                    return ProductType::where('prd_type_id', $resvinfo->prd_type_id)->first()->prd_type_name??''; //prd_type_id, prd_type_name
                })
                ->addColumn('month_year', function($resvinfo){
                    $month_year= date('F', mktime(0, 0, 0, $resvinfo->res_month, 10)). "-". $resvinfo->res_year;

                    return $month_year;
                })
                ->addColumn('order', function($resvinfo){
                    $orders= OrderEntry::select('order_id','order_code')
                                ->where('res_id',$resvinfo->res_id)
                                ->get();
                    $data='';
                    foreach ($orders as $key => $order) {
                        $data.= HtmlFacade::link("merch/orders/order_profile_show/{$order->order_id}",$order->order_code,[]).' ';
                    }

                    return html_entity_decode($data);
                })
                ->editColumn('balance', function ($resvinfo) {
                    return intval($resvinfo->res_quantity-$resvinfo->confirmed);

                })
                ->rawColumns(['order','month_year'])
                ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
}
