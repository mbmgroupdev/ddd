<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\Style;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\ProductType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class OrderQueryController extends Controller
{
    public function merchOrderQuery(Request $request)
    {
        try{

            return $this->merchOrderQueryGlobal($request->all());
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

        if($request['category'] == 'orderbycreate'){
            $datecon->searchfield = 'created_at';
        }
        if($request['category'] == 'orderbydelivery'){
            $datecon->searchfield = 'order_delivery_date';
        }
        return $datecon;
    }

    public function pageTitle($request){
            if($request['category'] == 'orderbycreate'){
                $cat = 'Order (by Create Date)';
            }
            if($request['category'] == 'orderbydelivery'){
                $cat = 'Order (by Delivery Date)';
            }

            $showTitle = $cat.' - '.ucwords($request['type']) ;
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



    public function getOrderInfo($datecon,$condition,$status){

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

    
        $operation_id_for_wash = DB::table('mr_operation')->where('opr_name', 'Wash')->value('opr_id');
       
       if(!empty($team)){
         $order = OrderEntry::select([
                                    'order_code',
                                    'order_id',
                                    'mr_order_entry.created_by',
                                    'mr_order_entry.unit_id',
                                    'mr_order_entry.mr_buyer_b_id',
                                    'c.mr_style_stl_id',
                                    'order_delivery_date',
                                    'order_qty',
                                    'c.agent_fob',
                                    DB::raw('(order_qty * c.agent_fob) AS total_fob'),
                                    's.prd_type_id',
                                    's.stl_no as ref_1',
                                    's.stl_product_name as ref_2',
                                    's.stl_description as gdes',
                                    'st_bom_cost.bom_stl_cost_wash as wsh',
                                    DB::raw('round((st_bom_cost.bom_stl_cost_wash * order_qty), 2) as wsh_ern'),
                                    'mr_order_entry.created_by',
                                    'order_status',
                                    'd.cm',
                                    DB::raw('(d.cm * order_qty) as cm_ern'),
                                    'season.se_name as season',
                                    'resv.res_sewing_smv as smv',
                                    DB::raw('round((order_qty * resv.res_sewing_smv)/60, 2) as sah'),
                                    DB::raw('round((d.cm / resv.res_sewing_smv),3) as cm_by_smv'),
                                    'file.file_no'
                                    // 'op_n_cost.unit_price as wsh',
                                    // DB::raw('round(order_qty * op_n_cost.unit_price, 2) as wsh_ern')

                                ])
                     ->where(function($query) use ($datecon, $status) {
                         if(!empty($datecon->date)){
                             $query->whereDate($datecon->searchfield, '=', $datecon->date);
                         }
                         if(!empty($datecon->month)){
                             $query->whereMonth('mr_order_entry.'.$datecon->searchfield, '=', $datecon->month);
                             $query->whereYear('mr_order_entry.'.$datecon->searchfield, '=', $datecon->year);
                         }
                         if(!empty($datecon->year) && empty($datecon->month)){
                             $query->whereYear('mr_order_entry.'.$datecon->searchfield, '=', $datecon->year);
                         }
                         if(!empty($datecon->from)){
                             $query->whereBetween('mr_order_entry.'.$datecon->searchfield, [$datecon->from,$datecon->to]);
                         }
                         if(isset($status)&& $status=='Completed'){
                             $query->where('order_status','=', 'Completed' );
                         }
                         if(isset($status)&& $status=='Incomplete'){
                             $query->where('order_status','!=', 'Completed' );
                         }
                         /*if(isset($prdtype)){
                             $query->where('s.prd_type_id',$prdtype);
                         }*/
                     })
                     ->where($condition)
                     ->whereIn('created_by', $team)
                     ->leftJoin('mr_stl_bom_other_costing AS c', 'mr_order_entry.mr_style_stl_id','=', 'c.mr_style_stl_id' )
                     ->leftJoin('mr_bom_style_costing AS st_bom_cost', 'st_bom_cost.stl_id','=', 'mr_order_entry.mr_style_stl_id' )
                     ->leftJoin('mr_order_bom_other_costing AS d', 'd.mr_order_entry_order_id', 'mr_order_entry.order_id')
                     ->leftJoin('mr_style AS s','mr_order_entry.mr_style_stl_id','=','s.stl_id')
                     ->leftJoin('mr_season AS season','season.se_id','=','mr_order_entry.mr_season_se_id')
                     ->leftJoin('mr_capacity_reservation AS resv','resv.res_id','=','mr_order_entry.res_id')
                     ->leftJoin('cm_sales_contract_order AS sc_order','sc_order.mr_order_entry_order_id','=','mr_order_entry.order_id')
                     ->leftJoin('cm_exp_lc_entry AS exp_lc','exp_lc.cm_sales_contract_id','=','sc_order.cm_sales_contract_id')
                     ->leftJoin('cm_file AS file', 'file.id', 'exp_lc.cm_file_id')
                     
                     // ->leftJoin('mr_order_operation_n_cost AS op_n_cost', 'op_n_cost.mr_order_entry_order_id', 'mr_order_entry.order_id')
                     // ->where('op_n_cost.mr_operation_opr_id', $operation_id_for_wash)
                     
                     ->orderBy('order_id','DESC')
                     ->get();

                     foreach ($order as $odr) {
                        $wsh = DB::table('mr_order_operation_n_cost')
                                    ->where([
                                                'mr_order_entry_order_id'=>$odr->order_id, 
                                                'mr_operation_opr_id'    =>$operation_id_for_wash
                                            ])
                                    ->value('unit_price');
                        $odr->wsh = $wsh;
                        $odr->wsh_ern = round($odr->order_qty*$wsh, 2);
                     }
       }else{
         $order = OrderEntry::select([
                                'order_code',
                                'order_id',
                                'mr_order_entry.created_by',
                                'mr_order_entry.unit_id',
                                'mr_order_entry.mr_buyer_b_id',
                                'mr_order_entry.order_ref_no',
                                'c.mr_style_stl_id','order_delivery_date',
                                'order_qty',
                                'c.agent_fob',
                                DB::raw('(order_qty * c.agent_fob) AS total_fob'),
                                's.prd_type_id',
                                's.stl_no as ref_1',
                                's.stl_product_name as ref_2',
                                's.stl_description as gdes',
                                'st_bom_cost.bom_stl_cost_wash as wsh',
                                DB::raw('round((st_bom_cost.bom_stl_cost_wash * order_qty), 2) as wsh_ern'),
                                'mr_order_entry.created_by',
                                'order_status',
                                'd.cm',
                                DB::raw('(d.cm * order_qty) as cm_ern'),
                                'season.se_name as season',
                                'resv.res_sewing_smv as smv',
                                DB::raw('round((order_qty * resv.res_sewing_smv)/60 ,2) as sah'),
                                DB::raw('round((d.cm / resv.res_sewing_smv),3) as cm_by_smv'),
                                'file.file_no'
                                // 'op_n_cost.unit_price as wsh',
                                // DB::raw('round(order_qty * op_n_cost.unit_price, 2) as wsh_ern')
                        ])
                     ->where(function($query) use ($datecon, $status) {
                         if(!empty($datecon->date)){
                             $query->whereDate('mr_order_entry.'.$datecon->searchfield, '=', $datecon->date);
                         }
                         if(!empty($datecon->month)){
                             $query->whereMonth('mr_order_entry.'.$datecon->searchfield, '=', $datecon->month);
                             $query->whereYear('mr_order_entry.'.$datecon->searchfield, '=', $datecon->year);
                         }
                         if(!empty($datecon->year) && empty($datecon->month)){
                             $query->whereYear('mr_order_entry.'.$datecon->searchfield, '=', $datecon->year);
                         }
                         if(!empty($datecon->from)){
                             $query->whereBetween('mr_order_entry.'.$datecon->searchfield, [$datecon->from,$datecon->to]);
                         }
                         if(isset($status)&& $status=='Completed'){
                             $query->where('order_status','=', 'Completed' );
                         }
                         if(isset($status)&& $status=='Incomplete'){
                             $query->where('order_status','!=', 'Completed' );
                         }
                         /*if(isset($prdtype)){
                             $query->where('s.prd_type_id',$prdtype);
                         }*/
                     })
                     ->where($condition)
                     ->leftJoin('mr_stl_bom_other_costing AS c', 'mr_order_entry.mr_style_stl_id','=', 'c.mr_style_stl_id')
                     ->leftJoin('mr_bom_style_costing AS st_bom_cost', 'st_bom_cost.stl_id','=', 'mr_order_entry.mr_style_stl_id')
                     ->leftJoin('mr_order_bom_other_costing AS d', 'd.mr_order_entry_order_id', 'mr_order_entry.order_id')
                     ->leftJoin('mr_style AS s','mr_order_entry.mr_style_stl_id','=','s.stl_id')
                     ->leftJoin('mr_season AS season','season.se_id','=','mr_order_entry.mr_season_se_id')
                     ->leftJoin('mr_capacity_reservation AS resv','resv.res_id','=','mr_order_entry.res_id')
                     ->leftJoin('cm_sales_contract_order AS sc_order','sc_order.mr_order_entry_order_id','=','mr_order_entry.order_id')
                     ->leftJoin('cm_exp_lc_entry AS exp_lc','exp_lc.cm_sales_contract_id','=','sc_order.cm_sales_contract_id')
                     ->leftJoin('cm_file AS file', 'file.id', 'exp_lc.cm_file_id')
                     
                     // ->leftJoin('mr_order_operation_n_cost AS op_n_cost', 'op_n_cost.mr_order_entry_order_id', 'mr_order_entry.order_id')
                     // ->where('op_n_cost.mr_operation_opr_id', $operation_id_for_wash)
                     
                     ->orderBy('order_id','DESC')
                     ->get();

                     foreach ($order as $odr) {
                        $wsh = DB::table('mr_order_operation_n_cost')
                                    ->where([
                                                'mr_order_entry_order_id'=>$odr->order_id, 
                                                'mr_operation_opr_id'    =>$operation_id_for_wash
                                            ])
                                    ->value('unit_price');
                        $odr->wsh = $wsh;
                        $wsh_ern = round($odr->order_qty*$wsh, 2);
                        if($wsh_ern == 0){
                            $odr->wsh_ern = '';
                        }
                        else{
                            $odr->wsh_ern = $wsh_ern;   
                        }
                     }
       }


        // dd($order);
        return $order;


    }

    public function merchOrderQueryGlobal($request)
    {
        try {
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            $orderinfo = $this->getOrderInfo($datecon,[],null);

            $ptypes = ProductType::get();  //select prd_type_id, prd_type_name

            //dd($orderinfo);

            $globalinfo = new stdClass;
            $globalinfo->unit = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->get()->count();
            $globalinfo->buyer = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->get()->count();

            $globalinfo->prdtype = new stdClass;
            foreach ($ptypes as $key => $type) {
                $typename =$type['prd_type_name'];
                $typeid =$type['prd_type_id'];
                $typeorder=$this->getOrderInfo($datecon,['s.prd_type_id' => $typeid],null);
                $globalinfo->prdtype->$typeid = new stdClass;
                $globalinfo->prdtype->$typeid->name = $typename;
                $globalinfo->prdtype->$typeid->order = $typeorder->count();
                $globalinfo->prdtype->$typeid->qty = $typeorder->sum('order_qty');
                $globalinfo->prdtype->$typeid->fob = round($typeorder->sum('total_fob'),2);
            }
            $del_order=$this->getOrderInfo($datecon,[],'Completed');
            $globalinfo->del_order = $del_order->count()??0;
            $globalinfo->del_qty = $del_order->sum('order_qty')??0;
            $globalinfo->del_fob = round($del_order->sum('total_fob'),2)??0;
            //dd($globalinfo);
            $result['page'] = view('merch.query.order.orderinfo',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }



    public function merchOrderQueryUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $request['view']='allunit';
            unset($request['unit'],$request['buyer']);
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);

            $units = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->get();

            $unit_data=[];
            $condition=[];
            foreach ($units as $key => $unit) {
                $condition=['mr_order_entry.unit_id'=>$unit->hr_unit_id];
                $orderinfo = $this->getOrderInfo($datecon,$condition,null);

                $unit_data[$key]=new stdClass;
                $unit_data[$key]->id = $unit->hr_unit_id;
                $unit_data[$key]->name = $unit->hr_unit_name;
                $unit_data[$key]->buyer  = count($orderinfo->unique('mr_order_entry.mr_buyer_b_id'));
                $unit_data[$key]->order  = $orderinfo->count();
                $unit_data[$key]->qty  = $orderinfo->sum('order_qty');
                $unit_data[$key]->tfob  = round($orderinfo->sum('total_fob'),2);

                $del_order=$this->getOrderInfo($datecon,$condition,'Completed');
                $unit_data[$key]->del_order = $del_order->count()??0;
                $unit_data[$key]->del_qty = $del_order->sum('order_qty')??0;
                $unit_data[$key]->del_fob = round($del_order->sum('total_fob'),2)??0;

            }
            $result = [];
            $result['page'] = view('merch.query.order.allunit',
                compact('unit_data', 'request',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchOrderQueryBuyer(Request $request)
    {
        try {
            // get previous url params

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view']= 'allbuyer';
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }

            //return $request;
            unset($request1['buyer']);
            $datecon = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);
            $unit=[];
            $buyer_data = [];
            if (isset($request1['unit'])) {

                $condition = ['mr_order_entry.unit_id'=>$request1['unit']];
                $unit = Unit::where('hr_unit_id',$request1['unit'])->first();
                $buyers = $this->getOrderInfo($datecon,$condition,null)->unique('mr_order_entry.mr_buyer_b_id');
                foreach ($buyers as $key => $buyer) {
                    $condition = [
                        'mr_order_entry.mr_buyer_b_id'=>$buyer->mr_buyer_b_id,
                        'mr_order_entry.unit_id'=>$request1['unit']
                        ];
                    $buyerinfo = Buyer::where('b_id',$buyer->mr_buyer_b_id)->first();
                    $orderinfo = $this->getOrderInfo($datecon,$condition,null);
                    $buyer_data[$key]= new stdClass;
                    $buyer_data[$key]->id = $buyerinfo->b_id;
                    $buyer_data[$key]->name = $buyerinfo->b_name;
                    $buyer_data[$key]->order  = $orderinfo->count();
                    $buyer_data[$key]->qty  = $orderinfo->sum('order_qty');
                    $buyer_data[$key]->tfob  = round($orderinfo->sum('total_fob'),2);

                    $condition=[
                    'mr_order_entry.mr_buyer_b_id'=>$buyer->mr_buyer_b_id,
                    'order_status' => 'Completed'
                    ];
                    $del_order=$this->getOrderInfo($datecon,$condition,null);
                    $buyer_data[$key]->del_order = $del_order->count()??0;
                    $buyer_data[$key]->del_qty = $del_order->sum('order_qty')??0;
                    $buyer_data[$key]->del_fob = round($del_order->sum('total_fob'),2)??0;

                }
            }else{
                $buyers = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->get();
                foreach ($buyers as $key => $buyer) {
                    $condition = [
                        'mr_order_entry.mr_buyer_b_id'=>$buyer->b_id
                        ];
                    $orderinfo = $this->getOrderInfo($datecon, $condition,null);
                    $buyer_data[$key]= new stdClass;
                    $buyer_data[$key]->id = $buyer->b_id;
                    $buyer_data[$key]->name = $buyer->b_name;
                    $buyer_data[$key]->order  = $orderinfo->count();
                    $buyer_data[$key]->qty  = $orderinfo->sum('order_qty');
                    $buyer_data[$key]->tfob  = round($orderinfo->sum('total_fob'),2);

                $del_order=$this->getOrderInfo($datecon,$condition,'Completed');
                $buyer_data[$key]->del_order = $del_order->count()??0;
                $buyer_data[$key]->del_qty = $del_order->sum('order_qty')??0;
                $buyer_data[$key]->del_fob = round($del_order->sum('total_fob'),2)??0;
                }
            }

            //return dd($buyer_data);
            $result = [];
            $result['page'] = view('merch.query.order.allbuyer',
                compact('buyer_data', 'request1',  'showTitle','unit'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchOrderQueryOrder(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'allorder';
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }
            if (!empty($request->status)) {
                $request1['status']= $request->status;
            }
            //return dd($request1);
            $data=[];
            if(isset($request1['unit'])){
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            if(isset($request1['buyer'])){
                $data['buyerinfo'] = Buyer::where('b_id', $request1['buyer'])->first();
            }

            $showTitle = $this->pageTitle($request1);


            $result['page'] = view('merch.query.order.allorder',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  merchOrderQueryListOrder(Request $request){
        try {

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }
            if (!empty($request->status)) {
                $request1['status']= $request->status;
            }

            //return dd($request1);

            $condition=[];
            if(isset($request1['unit'])){
                $condition['mr_order_entry.unit_id'] = $request1['unit'];
            }
            if(isset($request1['buyer'])){
                $condition['mr_order_entry.mr_buyer_b_id'] = $request1['buyer'];
            }
            if(isset($request1['product'])){
                $condition['s.prd_type_id'] = $request1['product'];
            }
            $status=null;
            if(isset($request1['status'])){
                $status = $request1['status'];
            }
            $datecon = $this->getSearchType($request1);
            $orderinfo = $this->getOrderInfo($datecon,$condition, $status);
            //return dd($orderinfo);
            return DataTables::of($orderinfo)->addIndexColumn()
                ->editColumn('order_code', function ($orderinfo) {
                    return HtmlFacade::link("merch/orders/order_profile_show/{$orderinfo->order_id}",$orderinfo->order_code,['class' => 'employee-att-details']);
                })
                ->editColumn('unit', function ($orderinfo) {
                    return Unit::where('hr_unit_id',$orderinfo->unit_id)->first()->hr_unit_name??'';
                })
                ->editColumn('buyer_name', function ($orderinfo) {
                    return Buyer::where('b_id', $orderinfo->mr_buyer_b_id)->first()->b_name??'';
                })
                ->editColumn('prdtype', function ($orderinfo) {
                    return ProductType::where('prd_type_id', $orderinfo->prd_type_id)->first()->prd_type_name??''; //select prd_type_id, prd_type_name
                })
                ->editColumn('style', function ($orderinfo) {
                    return Style::where('stl_id', $orderinfo->mr_style_stl_id)->first()->stl_no??'';
                })
                ->editColumn('total_fob', function ($orderinfo) {
                    return round($orderinfo->total_fob,2);
                })
                ->editColumn('created_by', function ($orderinfo) {
                    if($orderinfo->created_by){
                        return HtmlFacade::link("hr/recruitment/employee/show/{$orderinfo->created_by}",$orderinfo->created_by,[])??'';
                    }else
                        return '';
                })
                ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
}
