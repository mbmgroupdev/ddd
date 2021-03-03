<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Merch\Season;
use App\Models\Merch\Buyer;
use App\Models\Merch\Style;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\ProductType;
use App\Models\Merch\BomOtherCosting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class StyleQueryController extends Controller
{
    public function merchStyleQuery(Request $request)
    {
        try{

            return $this->merchStyleQueryGlobal($request->all());
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

            $showTitle = 'Style'.' - '.ucwords($request['type']) ;
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



    public function getStyleInfo($datecon,$condition){
        $style = Style::where(function($query) use ($datecon) {
                        if(!empty($datecon->date)){
                            $query->whereDate('stl_added_on', '=', $datecon->date);
                        }
                        if(!empty($datecon->month)){
                            $query->whereMonth('stl_added_on', '=', $datecon->month);
                            $query->whereYear('stl_added_on', '=', $datecon->year);
                        }
                        if(!empty($datecon->year) && empty($datecon->month)){
                            $query->whereYear('stl_added_on', '=', $datecon->year);
                        }
                        if(!empty($datecon->from)){
                            $query->whereBetween('stl_added_on', [$datecon->from,$datecon->to]);
                        }
                    })
                    ->where($condition)
                    ->orderBy('stl_id','DESC')
                    ->get();

        return $style;


    }

    public function merchStyleQueryGlobal($request)
    {
        try {
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            $styleinfo = $this->getStyleInfo($datecon,[]);
            $ptypes = ProductType::get();  //select prd_type_id, prd_type_name
            $globalinfo = new stdClass;
            $globalinfo->buyer = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->get()->count();

            $globalinfo->bulk = new stdClass;
            foreach ($ptypes as $key => $type) {
                $typename =$type['prd_type_name'];
                $typeid =$type['prd_type_id'];
                $globalinfo->bulk->$typeid = new stdClass;
                $globalinfo->bulk->$typeid->name = $typename;
                $globalinfo->bulk->$typeid->value = $this->getStyleInfo($datecon,['prd_type_id' => $typeid, 'stl_type' => 'Bulk' ])->count();
            }
            $globalinfo->development = new stdClass;
            foreach ($ptypes as $key => $type) {
                $typename =$type['prd_type_name'];
                $typeid =$type['prd_type_id'];
                $globalinfo->development->$typeid = new stdClass;
                $globalinfo->development->$typeid->name = $typename;
                $globalinfo->development->$typeid->value = $this->getStyleInfo($datecon,['prd_type_id' => $typeid, 'stl_type' => 'Development' ])->count();
            }
            $globalinfo->total = new stdClass;
            foreach ($ptypes as $key => $type) {
                $typename =$type['prd_type_name'];
                $typeid =$type['prd_type_id'];
                $globalinfo->total->$typeid = new stdClass;
                $globalinfo->total->$typeid->name = $typename;
                $globalinfo->total->$typeid->value = $this->getStyleInfo($datecon,['prd_type_id' => $typeid ])->count();
            }

            //return dd($globalinfo);

            $result['page'] = view('merch.query.style.styleinfo',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }




    public function merchStyleQueryBuyer(Request $request)
    {
        try {
            // get previous url params

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            //return dd($request1);
            $request1['view']= 'allbuyer';
            unset($request1['buyer'],$request1['product']);
            $datecon = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            $buyer_data = [];

            $buyers = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->get();
            foreach ($buyers as $key => $buyer) {
                $buyer_data[$key]= new stdClass;
                $buyer_data[$key]->id = $buyer->b_id;
                $buyer_data[$key]->name = $buyer->b_name;
                $buyer_data[$key]->data = new stdClass;
                $ptypes = ProductType::get();
                foreach ($ptypes as $key1 => $type) {
                    $typename =$type['prd_type_name'];
                    $typeid =$type['prd_type_id'];
                    $buyer_data[$key]->data->$typeid = new stdClass;
                    $buyer_data[$key]->data->$typeid->name = $typename;
                    $buyer_data[$key]->data->$typeid->value = $this->getStyleInfo($datecon,['prd_type_id' => $typeid, 'mr_buyer_b_id'=>$buyer->b_id ])->count();
                }
            }
            //return dd($buyer_data);
            $result = [];
            $result['page'] = view('merch.query.style.allbuyer',
                compact('buyer_data', 'request1',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchStyleQueryStyle(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'allstyle';
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            if (!empty($request->ptype)) {
                $request1['ptype']= $request->ptype;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }
            $data=[];
            if(isset($request1['buyer'])){
                $data['buyerinfo'] = Buyer::where('b_id', $request1['buyer'])->first();
            }

            $showTitle = $this->pageTitle($request1);


            $result['page'] = view('merch.query.style.allstyle',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  merchStyleQueryListStyle(Request $request){
        try {
            //return 'hi';
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            if (!empty($request->ptype)) {
                $request1['ptype']= $request->ptype;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }

            $condition=[];
            if(isset($request1['buyer'])){
                $condition['mr_buyer_b_id'] = $request1['buyer'];
            }
            if(isset($request1['ptype'])){
                $condition['stl_type'] = $request1['ptype'];
            }
            if(isset($request1['product'])){
                $condition['prd_type_id'] = $request1['product'];
            }
            $datecon = $this->getSearchType($request1);
            $styleinfo = $this->getStyleInfo($datecon,$condition);

            return DataTables::of($styleinfo)->addIndexColumn()
                ->editColumn('stl_no', function ($styleinfo) {
                    return HtmlFacade::link("merch/style/style_profile/{$styleinfo->stl_id}",$styleinfo->stl_no);
                })
                ->editColumn('buyer_name', function ($styleinfo) {
                    return Buyer::where('b_id', $styleinfo->mr_buyer_b_id)->first()->b_name??'';
                })
                ->editColumn('season', function ($styleinfo) {
                    return Season::where('se_id', $styleinfo->mr_season_se_id)->first()->se_name??'';
                })
                ->editColumn('total_order', function ($styleinfo) {
                    return OrderEntry::where('mr_style_stl_id',$styleinfo->stl_id)->count();
                })
                ->editColumn('fob', function ($styleinfo) {
                    return BomOtherCosting::where('mr_style_stl_id',$styleinfo->stl_id)->first()->agent_fob??'0';
                })
                ->editColumn('stl_status', function ($styleinfo) {
                    if($styleinfo->stl_status==0){
                        return 'Created';
                    }else if($styleinfo->stl_status==1){
                        return 'Pending';
                    }else if($styleinfo->stl_status==2){
                        return 'Approved';
                    }

                })
                ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
}
