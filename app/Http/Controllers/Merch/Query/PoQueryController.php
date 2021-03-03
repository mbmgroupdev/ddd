<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\Style;
use App\Models\Merch\Country;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\ProductType;
use App\Models\Merch\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class PoQueryController extends Controller
{
    public function merchPoQuery(Request $request)
    {
        try{

            return $this->merchPoQueryGlobal($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSearchType($request)
    {
        $datecon= new stdClass;
        if($request['type'] == 'month') {
            $datecon->month = date('m', strtotime($request['month']));
            $datecon->year = date('Y', strtotime($request['month']));
        }else if ($request['type'] == 'range') {
            $datecon->from = $request['rangeFrom'];
            $datecon->to = $request['rangeTo'];
        }else if($request['type'] == 'year') {
            $datecon->year = $request['year'];
        }else if($request['type'] == 'date') {
            $datecon->date = $request['date'];
        }else{
            $datecon->date = date('Y-m-d');
        }
        return $datecon;
    }

    public function pageTitle($request){

            $showTitle = 'Purchase Order'.' - '.ucwords($request['type']) ;
            if(isset($request['date']))
            {
                $showTitle =$showTitle.': '.$request['date'];
            }
            if(isset($request['month']))
            {
                $showTitle =$showTitle.': '.$request['month'];
            }
            if(isset($request['year']))
            {
                $showTitle =$showTitle.': '.$request['year'];
            }
            if($request['type']=='range'){
                $showTitle =$showTitle.': '.$request['rangeFrom'].' to '.$request['rangeTo'];
            }

            return $showTitle;
    }



    public function getPoInfo($datecon,$condition){
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

      if(!empty($team)){
        $order = PurchaseOrder::select('o.order_id','o.order_code','o.created_by','o.mr_buyer_b_id','o.order_delivery_date','o.order_qty','mr_purchase_order.*')
                    ->where(function($query) use ($datecon) {
                        if(!empty($datecon->date)){
                            $query->whereDate('o.order_delivery_date', '=', $datecon->date);
                        }
                        if(!empty($datecon->month)){
                            $query->whereMonth('o.order_delivery_date', '=', $datecon->month);
                            $query->whereYear('o.order_delivery_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->year) && empty($datecon->month)){
                            $query->whereYear('o.order_delivery_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->from)){
                            $query->whereBetween('o.order_delivery_date', [$datecon->from,$datecon->to]);
                        }
                        /*if(isset($prdtype)){
                            $query->where('s.prd_type_id',$prdtype);
                        }*/
                    })
                    ->where($condition)
                    ->whereIn('o.created_by', $team)
                    ->leftJoin('mr_order_entry AS o', 'mr_purchase_order.mr_order_entry_order_id','=', 'o.order_id' )
                    ->orderBy('po_id','DESC')
                    ->get();

      }else{
        $order = PurchaseOrder::select('o.order_id','o.order_code','o.mr_buyer_b_id','o.order_delivery_date','o.order_qty','mr_purchase_order.*')
                    ->where(function($query) use ($datecon) {
                        if(!empty($datecon->date)){
                            $query->whereDate('o.order_delivery_date', '=', $datecon->date);
                        }
                        if(!empty($datecon->month)){
                            $query->whereMonth('o.order_delivery_date', '=', $datecon->month);
                            $query->whereYear('o.order_delivery_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->year) && empty($datecon->month)){
                            $query->whereYear('o.order_delivery_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->from)){
                            $query->whereBetween('o.order_delivery_date', [$datecon->from,$datecon->to]);
                        }
                        /*if(isset($prdtype)){
                            $query->where('s.prd_type_id',$prdtype);
                        }*/
                    })
                    ->where($condition)
                    ->leftJoin('mr_order_entry AS o', 'mr_purchase_order.mr_order_entry_order_id','=', 'o.order_id' )
                    ->orderBy('po_id','DESC')
                    ->get();

      }


        return $order;


    }

    public function merchPoQueryGlobal($request)
    {
        try {
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            $poinfo = $this->getPoInfo($datecon,[]);
              //select cnt_name, cnt_id


            $globalinfo = new stdClass;
            $globalinfo->country = count($poinfo->unique('po_delivery_country'));
            $globalinfo->po =$poinfo->count();
            $globalinfo->qty =$poinfo->sum('po_qty');
            $globalinfo->country_fob = $poinfo->sum('country_fob');
            //dd($globalinfo);
            $result['page'] = view('merch.query.po.poinfo',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }



    public function merchPoQueryCountry(Request $request)
    {
        try {
            // get previous url params

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view']= 'country';
            unset($request1['country']);
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }

            //select cnt_name, cnt_id

            $datecon = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);
            $country_data = [];
            $cnts = $this->getPoInfo($datecon,[])->unique('po_delivery_country');

           /* return dd($cnts);*/
            foreach ($cnts as $key => $country) {
                $condition = [
                    'po_delivery_country'=>$country->po_delivery_country,
                    ];
                $cninfo = Country::where('cnt_id',$country->po_delivery_country)->first();
                $poinfo = $this->getPoInfo($datecon,$condition);
                $country_data[$key]= new stdClass;
                $country_data[$key]->id = $cninfo->cnt_id??'';
                $country_data[$key]->name = $cninfo->cnt_name??'';
                $country_data[$key]->po  = $poinfo->count();
                $country_data[$key]->order  = count($poinfo->unique('order_id'));
                $country_data[$key]->qty  = $poinfo->sum('po_qty');
                $country_data[$key]->country_fob  = round($poinfo->sum('country_fob'),2);
            }

            //return dd($country_data);
            $result = [];
            $result['page'] = view('merch.query.po.allcountry',
                compact('country_data', 'request1',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchPoQueryPo(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'allpo';
            if (!empty($request->country)) {
                $request1['country']= $request->country;
            }
            //return dd($request1);
            $data=[];
            if(isset($request1['country'])){
                $data['country'] = Country::where('cnt_id',$request1['country'])->first();
            }
            $showTitle = $this->pageTitle($request1);


            $result['page'] = view('merch.query.po.allpo',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  merchPoQueryListPo(Request $request){
        try {

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if (!empty($request->country)) {
                $request1['country']= $request->country;
            }

            //return dd($request1);

            $condition=[];
            if(isset($request1['country'])){
                $condition['po_delivery_country'] = $request1['country'];
            }
            $datecon = $this->getSearchType($request1);
            $poinfo = $this->getPoinfo($datecon,$condition);
            //return dd($condition);
            return DataTables::of($poinfo)->addIndexColumn()
                ->editColumn('order_code', function ($poinfo) {
                    return HtmlFacade::link("merch/orders/order_profile_show/{$poinfo->order_id}",$poinfo->order_code,['class' => '']);
                })
                ->editColumn('buyer_name', function ($poinfo) {
                    return Buyer::where('b_id', $poinfo->mr_buyer_b_id)->first()->b_name??'';
                })
                ->editColumn('country', function ($poinfo) {
                    return Country::where('cnt_id', $poinfo->po_delivery_country)->first()->cnt_name??'';
                })
                ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
}
