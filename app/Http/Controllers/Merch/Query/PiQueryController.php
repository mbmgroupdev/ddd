<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\Supplier;
use App\Models\Merch\OrderEntry;
use App\Models\Commercial\CmPiMaster;
use App\Models\Merch\PoBooking;
use App\Models\Merch\PoBookingPi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class PiQueryController extends Controller
{
    public function merchPiQuery(Request $request)
    {
        try{

            return $this->merchPiQueryGlobal($request->all());
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

            $showTitle = 'Proforma Invoice'.' - '.ucwords($request['type']) ;
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



    public function getPiInfo($datecon,$condition){
     
        $pis = CmPiMaster::where(function($query) use ($datecon) {
                        if(!empty($datecon->date)){
                            $query->whereDate('pi_date', '=', $datecon->date);
                        }
                        if(!empty($datecon->month)){
                            $query->whereMonth('pi_date', '=', $datecon->month);
                            $query->whereYear('pi_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->year) && empty($datecon->month)){
                            $query->whereYear('pi_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->from)){
                            $query->whereBetween('pi_date', [$datecon->from,$datecon->to]);
                        }
                    })
                    ->where($condition)
                    ->orderBy('id','DESC')
                    ->get();

        return $pis;


    }

    public function merchPiQueryGlobal($request)
    {
        try {
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            $piinfo = $this->getPiInfo($datecon,[]);
              //select cnt_name, cnt_id


            $globalinfo = new stdClass;
            $globalinfo->unit = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->count();
            $globalinfo->buyer = Buyer::count();
            $globalinfo->supplier = Supplier::count();
            $globalinfo->pi = $piinfo->count();
            $globalinfo->qty = $piinfo->sum('total_pi_qty');
            $globalinfo->value = $piinfo->sum('total_pi_value');
            //dd($globalinfo);
            $result['page'] = view('merch.query.pi.piinfo',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }



    public function merchPiQueryUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $request['view']='allunit';
            unset($request['unit']);
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);

            $units = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->get();

            //dd($units);
            $unit_data=[];
            $condition=[];
            foreach ($units as $key => $unit) {
                $condition=['unit_id' => $unit->hr_unit_id ];
                $unit_data[$key]= new stdClass;
                $unit_data[$key]->id = $unit->hr_unit_id;
                $unit_data[$key]->name = $unit->hr_unit_name;

                $piinfo = $this->getPiInfo($datecon,$condition);
                $unit_data[$key]->buyer = $piinfo->groupBy('mr_buyer_b_id')->count();
                $unit_data[$key]->supplier = $piinfo->groupBy('mr_supplier_sup_id')->count();
                $unit_data[$key]->pi = $piinfo->count();
                $unit_data[$key]->qty = $piinfo->sum('total_pi_qty');
                $unit_data[$key]->value = $piinfo->sum('total_pi_value');
            }

            //return dd($unit_data);
            $result = [];
            $result['page'] = view('merch.query.pi.allunit',
                compact('unit_data', 'request',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function merchPiQueryBuyer(Request $request)
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

            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }

            if (isset($request1['unit'])) {
                $piBuyers = CmPiMaster::where('unit_id',$request1['unit'])
                            ->pluck('mr_buyer_b_id')
                            ->toArray();
                $bCondition = $piBuyers;

                $condition['unit_id']=  $request1['unit'];
                
            }else{
                $bCondition = auth()->user()->buyer_permissions();

            }
            $buyers = Buyer::whereIn('b_id', $bCondition)->get();
            foreach ($buyers as $key => $buyer) {
                $condition['mr_buyer_b_id'] = $buyer->b_id;
                $buyer_data[$key]= new stdClass;
                $buyer_data[$key]->id = $buyer->b_id;
                $buyer_data[$key]->name = $buyer->b_name;
                
                $piinfo = $this->getPiInfo($datecon,$condition);
                $buyer_data[$key]->supplier = $piinfo->groupBy('mr_supplier_sup_id')->count();
                $buyer_data[$key]->pi = $piinfo->count();
                $buyer_data[$key]->qty = $piinfo->sum('total_pi_qty');
                $buyer_data[$key]->value = $piinfo->sum('total_pi_value');
               
            }

            //return dd($buyer_data);
            $result = [];
            $result['page'] = view('merch.query.pi.allbuyer',
                compact('buyer_data', 'request1',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchPiQuerySupplier(Request $request)
    {
        try {
            // get previous url params

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            //return dd($request1);
            $request1['view']= 'allsupplier';

            $datecon = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            $supplier_data = [];

            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            $data=[];
            if (isset($request1['unit'])){
                $condition['unit_id']=  $request1['unit'];
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            if (isset($request1['buyer'])){
                $condition['mr_buyer_b_id']= $request1['buyer'];
                $data['buyerinfo'] = Buyer::where('b_id', $request1['buyer'])->first();
            }

            if (isset($request1['unit']) || isset($request1['buyer'])) {
                $bCondition = CmPiMaster::where($condition) 
                                        ->pluck('mr_supplier_sup_id')
                                        ->toArray();
                $suppliers = Supplier::whereIn('sup_id', $bCondition)->get();
                
            }else{
                $suppliers = Supplier::get();

            }

            //dd($condition);
            foreach ($suppliers as $key => $supplier) {
                $condition['mr_supplier_sup_id']=  $supplier->sup_id ;
                $supplier_data[$key]= new stdClass;
                $supplier_data[$key]->id = $supplier->sup_id;
                $supplier_data[$key]->name = $supplier->sup_name;
                
                $piinfo = $this->getPiInfo($datecon,$condition);
                $supplier_data[$key]->pi = $piinfo->count();
                $supplier_data[$key]->qty = $piinfo->sum('total_pi_qty');
                $supplier_data[$key]->value = $piinfo->sum('total_pi_value');
               
            }

            
            

            //return dd($supplier_data);
            $result = [];
            $result['page'] = view('merch.query.pi.allsupplier',
                compact('supplier_data', 'request1',  'showTitle','data'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchPiQueryPi(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'allpi';
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->supplier)) {
                $request1['supplier']= $request->supplier;
            }
            $data=[];
            if(isset($request1['buyer'])){
                $data['buyerinfo'] = Buyer::where('b_id', $request1['buyer'])->first();
            }
            if(isset($request1['unit'])){
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            if(isset($request1['supplier'])){
                $data['supplier'] = Supplier::where('sup_id',$request1['supplier'])->first();
            }
            $showTitle = $this->pageTitle($request1);


            $result['page'] = view('merch.query.pi.allpi',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  merchPiQueryListPi(Request $request){
        try {

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
            }
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->supplier)) {
                $request1['supplier']= $request->supplier;
            }
            //return dd($request1);

            $condition=[];
            if(isset($request1['unit'])){
                $condition['unit_id'] = $request1['unit'];
            }
            if(isset($request1['buyer'])){
                $condition['mr_buyer_b_id'] = $request1['buyer'];
            }
            if(isset($request1['supplier'])){
                $condition['mr_supplier_sup_id'] = $request1['supplier'];
            }
            $datecon = $this->getSearchType($request1);
            $pidata = $this->getPiInfo($datecon,$condition);
            //return dd($condition);
            return DataTables::of($pidata)->addIndexColumn()
                      ->addColumn('supplier', function($pidata){
                          return Supplier::where('sup_id',$pidata->mr_supplier_sup_id)->first()->sup_name??'';
                      })
                      ->addColumn('booking', function($pidata){
                          $poDetails = PoBookingPi::where('mr_po_booking_pi.cm_pi_master_id',$pidata->id)
                                        ->leftJoin('mr_po_booking As pb','pb.id','mr_po_booking_pi.mr_po_booking_id')
                                        ->pluck('booking_ref_no');

                            $poText = '';
                            foreach($poDetails as $po) {
                              $poText .= '<span class="label label-info arrowed-right arrowed-in">'.$po.'</span> <br>';
                            }
                        return $poText;
                      })
                      ->rawColumns(['supplier','booking'])
                      ->toJson();


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
}
