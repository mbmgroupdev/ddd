<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\Supplier;
use App\Models\Merch\OrderEntry;
use App\Models\Commercial\CmBookingMaster;
use App\Models\Merch\MrOrderBooking;
use App\Models\Merch\PoBooking;
use App\Models\Merch\PoBookingBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class BookingQueryController extends Controller
{
    public function merchBookingQuery(Request $request)
    {
        try{
            return $this->merchBookingQueryGlobal($request->all());
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

    public function pageTitle($request)
    {
        $showTitle = 'Order Booking: '.' - '.ucwords($request['type']) ;
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

    public function getBookingInfo($datecon,$condition,$unique=false)
    {
        $Bookings = MrOrderBooking::where(function($query) use ($datecon) {
            if(!empty($datecon->date)){
                $query->whereDate('created_at', '=', $datecon->date);
            }
            if(!empty($datecon->month)){
                $query->whereMonth('created_at', '=', $datecon->month);
                $query->whereYear('created_at', '=', $datecon->year);
            }
            if(!empty($datecon->year) && empty($datecon->month)){
                $query->whereYear('created_at', '=', $datecon->year);
            }
            if(!empty($datecon->from)){
                $query->whereBetween('created_at', [$datecon->from,$datecon->to]);
            }
        })
        ->where($condition)
        ->orderBy('id','DESC')
        ->when($unique != '', function($q) use ($unique){
            $q->groupBy($unique);
        })
        ->get();
        return $Bookings;
    }

    public function getPoBookingInfo($datecon,$condition,$unique=false)
    {
        $Bookings = PoBooking::where(function($query) use ($datecon) {
            if(!empty($datecon->date)){
                $query->whereDate('created_at', '=', $datecon->date);
            }
            if(!empty($datecon->month)){
                $query->whereMonth('created_at', '=', $datecon->month);
                $query->whereYear('created_at', '=', $datecon->year);
            }
            if(!empty($datecon->year) && empty($datecon->month)){
                $query->whereYear('created_at', '=', $datecon->year);
            }
            if(!empty($datecon->from)){
                $query->whereBetween('created_at', [$datecon->from,$datecon->to]);
            }
        })
        ->where($condition)
        ->orderBy('id','DESC')
        ->when($unique != '', function($q) use ($unique){
            $q->groupBy($unique);
        })
        ->get();
        return $Bookings;
    }

    public function merchBookingQueryGlobal($request)
    {
        try {
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            $BookinginfoAll = $this->getBookingInfo($datecon,[]);
            $BookinginfoUnique = $this->getBookingInfo($datecon,[],'mr_po_booking_id');
            $SupplierinfoUnique = $this->getPoBookingInfo($datecon,[],'mr_supplier_sup_id');
            $BuyerinfoUnique = $this->getPoBookingInfo($datecon,[],'mr_buyer_b_id');
              //select cnt_name, cnt_id

            $globalinfo = new stdClass;
            $globalinfo->unit = Unit::where('hr_unit_status',1)->whereIn('hr_unit_id', auth()->user()->unit_permissions())->count();
            $globalinfo->buyer = $BuyerinfoUnique->count();
            $globalinfo->supplier = $SupplierinfoUnique->count();
            $globalinfo->booking = $BookinginfoUnique->count();
            $globalinfo->bqty = $BookinginfoAll->sum('booking_qty');
            $globalinfo->value = $BookinginfoAll->sum('value');
            $globalinfo->rqty = $BookinginfoAll->sum('req_qty');
            //dd($globalinfo);
            $result['page'] = view('merch.query.booking.bookinginfo',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchBookingQueryUnit(Request $request)
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

                $Bookinginfo = $this->getPoBookingInfo($datecon,$condition);
                $unit_data[$key]->buyer = $Bookinginfo->groupBy('mr_buyer_b_id')->count();
                $unit_data[$key]->supplier = $Bookinginfo->groupBy('mr_supplier_sup_id')->count();
                $unit_data[$key]->Booking = $Bookinginfo->count();
                if(!empty($Bookinginfo->toArray())) {
                    $po_booking_ids = [];
                    $orderBooking = [];
                    $po_booking_ids = array_column($Bookinginfo->toArray(),'id');
                    $orderBooking = MrOrderBooking::whereIn('mr_po_booking_id',$po_booking_ids)->get();
                    $unit_data[$key]->rqty = $orderBooking->sum('req_qty');
                    $unit_data[$key]->bqty = $orderBooking->sum('booking_qty');
                    $unit_data[$key]->value = $orderBooking->sum('value');
                } else {
                    $unit_data[$key]->rqty = 0;
                    $unit_data[$key]->bqty = 0;
                    $unit_data[$key]->value = 0;
                }
                // $unit_data[$key]->qty = $Bookinginfo->sum('total_Booking_qty');
                // $unit_data[$key]->value = $Bookinginfo->sum('total_Booking_value');
            }

            $result = [];
            $result['page'] = view('merch.query.booking.allunit',
                compact('unit_data', 'request',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchBookingQueryBuyer(Request $request)
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
                $BookingBuyers = CmBookingMaster::where('unit_id',$request1['unit'])
                            ->pluck('mr_buyer_b_id')
                            ->toArray();
                $bCondition = $BookingBuyers;

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
                
                $Bookinginfo = $this->getBookingInfo($datecon,$condition);
                $buyer_data[$key]->supplier = $Bookinginfo->groupBy('mr_supplier_sup_id')->count();
                $buyer_data[$key]->Booking = $Bookinginfo->count();
                $buyer_data[$key]->qty = $Bookinginfo->sum('total_Booking_qty');
                $buyer_data[$key]->value = $Bookinginfo->sum('total_Booking_value');
            }
            //return dd($buyer_data);
            $result = [];
            $result['page'] = view('merch.query.booking.allbuyer',
                compact('buyer_data', 'request1',  'showTitle'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchBookingQuerySupplier(Request $request)
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
                $bCondition = CmBookingMaster::where($condition) 
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
                
                $Bookinginfo = $this->getBookingInfo($datecon,$condition);
                $supplier_data[$key]->Booking = $Bookinginfo->count();
                $supplier_data[$key]->qty = $Bookinginfo->sum('total_Booking_qty');
                $supplier_data[$key]->value = $Bookinginfo->sum('total_Booking_value');
            }

            //return dd($supplier_data);
            $result = [];
            $result['page'] = view('merch.query.booking.allsupplier',
                compact('supplier_data', 'request1',  'showTitle','data'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchBookingQueryBooking(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'allBooking';
            $dataList = [];
            $where = [];
            $datecon = $this->getSearchType($request1);
            if (!empty($request->buyer)) {
                $request1['buyer']= $request->buyer;
                $where['mr_buyer_b_id']= $request->buyer;
            }
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
                $where['unit_id']= $request->unit;
            }
            if (!empty($request->supplier)) {
                $request1['supplier']= $request->supplier;
                $where['mr_supplier_sup_id']= $request->supplier;
            }

            $dataList = $this->getPoBookingInfo($datecon,$where);
            $showTitle = $this->pageTitle($request1);

            // return dd($dataList[0]->orderBooking());
            $result['page'] = view('merch.query.booking.allBooking',
                compact('data','request1','showTitle','dataList'))->render();
            $result['url'] = url('merch/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function merchBookingQueryListBooking(Request $request)
    {
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
            $Bookingdata = $this->getPoBookingInfo($datecon,$condition);
            // return dd($Bookingdata);
            return DataTables::of($Bookingdata)->addIndexColumn()
                      ->addColumn('ref_no', function($Bookingdata){
                          return $Bookingdata->booking_ref_no;
                      })
                      ->addColumn('unit', function($Bookingdata){
                          return $Bookingdata->getUnitInfo->hr_unit_name;
                      })
                      ->addColumn('supplier', function($Bookingdata){
                          return $Bookingdata->getSupplierInfo->sup_name;
                      })
                      ->addColumn('buyer', function($Bookingdata){
                          return $Bookingdata->buyer->b_name;
                      })
                      ->addColumn('item', function($Bookingdata){
                            $itemCount = [];
                            $poDetails = $Bookingdata->poDetails;
                            if(!$poDetails->isEmpty()) {
                                foreach($poDetails as $k=>$item) {
                                    $itemCount[$item->mr_order_bom_costing_booking_id] = $item->mr_order_bom_costing_booking_id;
                                }
                            }
                          return count($itemCount);
                      })
                      ->addColumn('req_qty', function($Bookingdata){
                            $reqQty = 0;
                            $poDetails = $Bookingdata->poDetails;
                            if(!$poDetails->isEmpty()) {
                                $reqQty = $poDetails->sum('req_qty');
                            }
                            return $reqQty;
                      })
                      ->addColumn('booking_qty', function($Bookingdata){
                          return '';
                      })
                      ->addColumn('delivery_date', function($Bookingdata){
                          return $Bookingdata->delivery_date;
                      })
                      ->rawColumns(['ref_no','unit','supplier','buyer','item','req_qty','booking_qty','delivery_date'])
                      ->toJson();


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
}