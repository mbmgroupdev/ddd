<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\Employee;
use App\Models\Merch\MrExcecutiveTeamMembers;
use App\Models\Merch\MrExcecutiveTeam;
use App\Models\Merch\Style;
use App\Models\Merch\Buyer;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\ProductType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class TeamQueryController extends Controller
{
    public function merchTeamQuery(Request $request)
    {
        try{


            return $this->merchTeamQueryGlobal($request->all());
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


            $showTitle = 'Order (by team)'.' - '.ucwords($request['type']) ;
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

    public function getMembers($condition){
        $leads = MrExcecutiveTeamMembers::where(function($query) use ($condition) {
                        if(isset($condition['team.mr_excecutive_team_id'])){
                            $query->where('mr_excecutive_team_members.mr_excecutive_team_id', $condition['team.mr_excecutive_team_id']);
                        }
                    })
                    ->leftJoin('hr_as_basic_info as e','e.as_id','=','mr_excecutive_team_members.member_id')
                    ->pluck('associate_id');
        $members = MrExcecutiveTeam::where(function($query) use ($condition) {
                        if(isset($condition['team.mr_excecutive_team_id'])){
                            $query->where('mr_excecutive_team.id', $condition['team.mr_excecutive_team_id']);
                        }
                    })
                    ->leftJoin('hr_as_basic_info as e','e.as_id','=','mr_excecutive_team.team_lead_id')
                    ->pluck('associate_id');

        $members = $members->merge($leads)->unique();
        return $members;
    }



    public function getOrderInfo($datecon,$condition,$status){

        $members = $this->getMembers($condition);
        unset($condition['team.mr_excecutive_team_id']);
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

        //return $members;
        if(!empty($team)){
          $order = OrderEntry::select('order_code','order_id','mr_order_entry.unit_id','mr_order_entry.mr_buyer_b_id','c.mr_style_stl_id','order_delivery_date','order_qty','c.agent_fob', DB::raw('(order_qty * c.agent_fob) AS total_fob'),'s.prd_type_id','mr_order_entry.created_by','order_status','team.mr_excecutive_team_id')
                      ->where(function($query) use ($datecon, $status) {
                          if(!empty($datecon->date)){
                              $query->whereDate('mr_order_entry.created_at', '=', $datecon->date);
                          }
                          if(!empty($datecon->month)){
                              $query->whereMonth('mr_order_entry.created_at', '=', $datecon->month);
                              $query->whereYear('mr_order_entry.created_at', '=', $datecon->year);
                          }
                          if(!empty($datecon->year) && empty($datecon->month)){
                              $query->whereYear('mr_order_entry.created_at', '=', $datecon->year);
                          }
                          if(!empty($datecon->from)){
                              $query->whereBetween('mr_order_entry.created_at', [$datecon->from,$datecon->to]);
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
                      ->leftJoin('mr_stl_bom_other_costing AS c', 'mr_order_entry.mr_style_stl_id','=', 'c.mr_style_stl_id' )
                      ->leftJoin('mr_style AS s','mr_order_entry.mr_style_stl_id','=','s.stl_id')
                      ->leftJoin('hr_as_basic_info as emp','mr_order_entry.created_by','=','emp.associate_id')
                      ->leftJoin('mr_excecutive_team_members as team','emp.as_id','=','team.member_id')
                      ->whereIn('mr_order_entry.created_by', $team)
                      ->orderBy('order_id','DESC')
                      ->get();
        }else{
          $order = OrderEntry::select('order_code','order_id','mr_order_entry.unit_id','mr_order_entry.mr_buyer_b_id','c.mr_style_stl_id','order_delivery_date','order_qty','c.agent_fob', DB::raw('(order_qty * c.agent_fob) AS total_fob'),'s.prd_type_id','mr_order_entry.created_by','order_status','team.mr_excecutive_team_id')
                      ->where(function($query) use ($datecon, $status) {
                          if(!empty($datecon->date)){
                              $query->whereDate('mr_order_entry.created_at', '=', $datecon->date);
                          }
                          if(!empty($datecon->month)){
                              $query->whereMonth('mr_order_entry.created_at', '=', $datecon->month);
                              $query->whereYear('mr_order_entry.created_at', '=', $datecon->year);
                          }
                          if(!empty($datecon->year) && empty($datecon->month)){
                              $query->whereYear('mr_order_entry.created_at', '=', $datecon->year);
                          }
                          if(!empty($datecon->from)){
                              $query->whereBetween('mr_order_entry.created_at', [$datecon->from,$datecon->to]);
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
                      ->leftJoin('mr_stl_bom_other_costing AS c', 'mr_order_entry.mr_style_stl_id','=', 'c.mr_style_stl_id' )
                      ->leftJoin('mr_style AS s','mr_order_entry.mr_style_stl_id','=','s.stl_id')
                      ->leftJoin('hr_as_basic_info as emp','mr_order_entry.created_by','=','emp.associate_id')
                      ->leftJoin('mr_excecutive_team_members as team','emp.as_id','=','team.member_id')
                      ->whereIn('mr_order_entry.created_by', $members)
                      ->orderBy('order_id','DESC')
                      ->get();
        }



        return $order;


    }

    public function merchTeamQueryGlobal($request)
    {
        try {
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            //$orderinfo = $this->getOrderInfo($datecon,[],null);

            $ptypes = ProductType::get();  //select prd_type_id, prd_type_name

            //dd($orderinfo);

            $globalinfo = new stdClass;
            $globalinfo->unit = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->get()->count();
            $userasId = DB::table('hr_as_basic_info')
                          ->where('associate_id',auth()->user()->associate_id)
                          ->first();
            if(auth()->user()->hasRole('merchandiser')){
              $globalinfo->team = MrExcecutiveTeam::where('team_lead_id',$userasId->as_id)->get()->count();
            }elseif (auth()->user()->hasRole('merchandising_executive')) {
              $globalinfo->team = 1;
            }else{
             $globalinfo->team = MrExcecutiveTeam::get()->count();
            }


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
            $result['page'] = view('merch.query.team.teaminfo',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }



    public function merchTeamQueryUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $request['view']='allunit';
            unset($request['unit'],$request['team'],$request['executive'],$request['status'],$request['product']);
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
                $unit_data[$key]->team = MrExcecutiveTeam::where('unit_id','=',$unit->hr_unit_id)->count();
                $unit_data[$key]->order  = $orderinfo->count();
                $unit_data[$key]->qty  = $orderinfo->sum('order_qty');
                $unit_data[$key]->tfob  = round($orderinfo->sum('total_fob'),2);

                $del_order=$this->getOrderInfo($datecon,$condition,'Completed');
                $unit_data[$key]->del_order = $del_order->count()??0;
                $unit_data[$key]->del_qty = $del_order->sum('order_qty')??0;
                $unit_data[$key]->del_fob = round($del_order->sum('total_fob'),2)??0;

            }
            $result = [];
            $result['page'] = view('merch.query.team.allunit',
                compact('unit_data', 'request',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchTeamQueryTeam(Request $request)
    {
        try {
            // get previous url params

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view']= 'allteam';
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }

            //return $request;
            unset($request1['team'],$request1['executive'],$request1['status'],$request1['product']);
            $datecon = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);
            $unit=[]; $condition=[]; $team_data = [];
            $userasId = DB::table('hr_as_basic_info')
                          ->where('associate_id',auth()->user()->associate_id)
                          ->first();
            if (isset($request1['unit'])) {
                $teams = MrExcecutiveTeam::where('unit_id','=',$request1['unit'])->get();
                $condition['mr_order_entry.unit_id'] =$request1['unit'];
                $unit = Unit::where('hr_unit_id',$request1['unit'])->first();
            }else{
              if(auth()->user()->hasRole('merchandiser')){
                $teams = MrExcecutiveTeam::where('team_lead_id',$userasId->as_id)->get();
              }else{
               $teams = MrExcecutiveTeam::get();
              }
            }

            foreach ($teams as $key => $team) {
                $condition['team.mr_excecutive_team_id'] = $team->id;

                $orderinfo = $this->getOrderInfo($datecon,$condition,null);
                $team_data[$key]= new stdClass;
                $team_data[$key]->id = $team->id;
                $team_data[$key]->name = $team->team_name;
                $team_data[$key]->lead = Employee::where('as_id','=',$team->team_lead_id)->first()->as_name??'';
                $team_data[$key]->member = $this->getMembers($condition)->count();
                $team_data[$key]->order  = $orderinfo->count();
                $team_data[$key]->qty  = $orderinfo->sum('order_qty');
                $team_data[$key]->tfob  = round($orderinfo->sum('total_fob'),2);


                $del_order=$this->getOrderInfo($datecon,$condition,'Completed');
                $team_data[$key]->del_order = $del_order->count()??0;
                $team_data[$key]->del_qty = $del_order->sum('order_qty')??0;
                $team_data[$key]->del_fob = round($del_order->sum('total_fob'),2)??0;

            }


            //return dd($team_data);
            $result = [];
            $result['page'] = view('merch.query.team.allteam',
                compact('team_data', 'request1',  'showTitle','unit'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }


    public function merchTeamQueryExecutive(Request $request)
    {
        try {
            // get previous url params

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view']= 'executive';

            unset($request1['executive'],$request1['status'],$request1['product']);
            if (!empty($request->team)) {
                $request1['team']= $request->team;
            }
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }

            $data=[];
            if(isset($request1['unit'])){
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            $condition=[];
            if(isset($request1['team'])){
                $data['teaminfo'] = MrExcecutiveTeam::where('id', $request1['team'])->first();
                $condition['team.mr_excecutive_team_id'] = $request1['team'];
            }

            //select cnt_name, cnt_id

            $datecon = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);
            $executive_data = [];
            $executives = $this->getMembers($condition);

            //dd($executives);
            foreach ($executives as $key => $executive) {
                $empinfo= Employee::where('associate_id','=',$executive)->first();
                $condition['mr_order_entry.created_by'] = $executive;
                $orderinfo = $this->getOrderInfo($datecon,$condition,null);
                $executive_data[$key]= new stdClass;
                $executive_data[$key]->id = $executive;
                $executive_data[$key]->name = $empinfo->as_name??$executive;
                $executive_data[$key]->as_id = $empinfo->associate_id??$executive;
                $executive_data[$key]->order  = $orderinfo->count();
                $executive_data[$key]->qty  = $orderinfo->sum('order_qty');
                $executive_data[$key]->tfob  = round($orderinfo->sum('total_fob'),2);


                $del_order=$this->getOrderInfo($datecon,$condition,'Completed');
                $executive_data[$key]->del_order = $del_order->count()??0;
                $executive_data[$key]->del_qty = $del_order->sum('order_qty')??0;
                $executive_data[$key]->del_fob = round($del_order->sum('total_fob'),2)??0;

            }


            //return dd($executive_data);
            $result = [];
            $result['page'] = view('merch.query.team.allexecutive',
                compact('executive_data', 'request1',  'showTitle','data'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }



    public function merchTeamQueryOrder(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'allorder';
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->team)) {
                $request1['team']= $request->team;
            }
            if (!empty($request->executive)) {
                $request1['executive']= $request->executive;
            }
            if (!empty($request->status)) {
                $request1['status']= $request->status;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }
            //return dd($request1);
            $data=[];
            if(isset($request1['unit'])){
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            if(isset($request1['team'])){
                $data['teaminfo'] = MrExcecutiveTeam::where('id', $request1['team'])->first();
            }
            if(isset($request1['executive'])){
                $data['executive'] = Employee::where('associate_id', $request1['executive'])->first();
            }
            //dd($data);

            $showTitle = $this->pageTitle($request1);


            $result['page'] = view('merch.query.team.allorder',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  merchTeamQueryListOrder(Request $request){
        try {

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->team)) {
                $request1['team']= $request->team;
            }
            if (!empty($request->executive)) {
                $request1['executive']= $request->executive;
            }
            if (!empty($request->status)) {
                $request1['status']= $request->status;
            }
            if (!empty($request->product)) {
                $request1['product']= $request->product;
            }

            //return dd($request1);

            $condition=[];
            if(isset($request1['unit'])){
                $condition['mr_order_entry.unit_id'] = $request1['unit'];
            }
            if(isset($request1['team'])){
                $condition['team.mr_excecutive_team_id'] = $request1['team'];
            }
            if(isset($request1['executive'])){
                $condition['mr_order_entry.created_by'] = $request1['executive'];
            }
            if(isset($request1['product'])){
                $condition['s.prd_type_id'] = $request1['product'];
            }

            //dd($condition);
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
                ->editColumn('created_by', function ($orderinfo) {
                    return HtmlFacade::link("hr/recruitment/employee/show/{$orderinfo->created_by}",$orderinfo->created_by,[])??'';
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
                ->editColumn('team', function ($orderinfo) {
                    return MrExcecutiveTeam::where('id', $orderinfo->mr_excecutive_team_id)->first()->team_name??'';
                })
                ->editColumn('total_fob', function ($orderinfo) {
                    return round($orderinfo->total_fob,2);
                })
                ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
}
