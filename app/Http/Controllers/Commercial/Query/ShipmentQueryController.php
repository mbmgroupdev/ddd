<?php

namespace App\Http\Controllers\Commercial\Query;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\Supplier;
use App\Models\Merch\OrderEntry;
use App\Models\Commercial\CmPiMaster;
use App\Models\Commercial\ImportDataEntry;
use App\Models\Merch\PoBooking;
use App\Models\Merch\PoBookingPi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables,stdClass;

class ShipmentQueryController extends Controller
{
    public function commShipmentQuery(Request $request)
    {
        try{
        	//dd($request->all());
        	

            return $this->commShipmentQueryGlobal($request->all());
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

            $showTitle = 'Shipment'.' - '.ucwords($request['type']) ;
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



    public function getShipmentInfo($datecon,$condition){
     
        $shipment = DB::table('cm_imp_data_entry as e')
        			->join('cm_imp_invoice as i','e.id','=','i.cm_imp_data_entry_id')
        			->where(function($query) use ($datecon) {
                        if(!empty($datecon->date)){
                            $query->whereDate('e.transp_doc_date', '=', $datecon->date);
                        }
                        if(!empty($datecon->month)){
                            $query->whereMonth('e.transp_doc_date', '=', $datecon->month);
                            $query->whereYear('e.transp_doc_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->year) && empty($datecon->month)){
                            $query->whereYear('e.transp_doc_date', '=', $datecon->year);
                        }
                        if(!empty($datecon->from)){
                            $query->whereBetween('e.transp_doc_date', [$datecon->from,$datecon->to]);
                        }
                    })
                    ->where($condition)
                    ->whereIn('hr_unit', auth()->user()->unit_permissions())
                    ->orderBy('e.id','DESC')
                    ->get();

        return $shipment;


    }

    public function commShipmentQueryGlobal($request)
    {
        try {
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            $piinfo = $this->getShipmentInfo($datecon,[]);
              //select cnt_name, cnt_id
            //dd($piinfo);
            unset($request['scSearchBy']);

            $globalinfo = new stdClass;
            $globalinfo->unit = $piinfo->unique('hr_unit')->count();
            $globalinfo->supplier = $piinfo->unique('mr_supplier_sup_id')->count();
            $globalinfo->pi = $piinfo->count();
            $globalinfo->qty = $piinfo->sum('qty');
            $globalinfo->value = $piinfo->sum('value');
            //dd($globalinfo);
            $result['page'] = view('commercial.query.shipment.shipment',
                compact('showTitle', 'request','globalinfo'))->render();
            $result['url'] = url('commercial/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }



    public function commShipmentQueryUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $request['view']='allunit';
            unset($request['unit']);
            $datecon = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);

            $getUnit = $this->getShipmentInfo($datecon,[])->unique('hr_unit')->pluck('hr_unit');

            $units = Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
            			->whereIn('hr_unit_id',$getUnit)
            			->get();

            //dd($units);
            $unit_data=[];
            $condition=[];
            foreach ($units as $key => $unit) {
                $condition=['hr_unit' => $unit->hr_unit_id ];
                $unit_data[$key]= new stdClass;
                $unit_data[$key]->id = $unit->hr_unit_id;
                $unit_data[$key]->name = $unit->hr_unit_name;

                $piinfo = $this->getShipmentInfo($datecon,$condition);
                $unit_data[$key]->supplier = $piinfo->unique('mr_supplier_sup_id')->count();
                $unit_data[$key]->pi = $piinfo->count();
                $unit_data[$key]->qty = $piinfo->sum('qty');
                $unit_data[$key]->value = $piinfo->sum('value');
            }

            //return dd($unit_data);
            $result = [];
            $result['page'] = view('commercial.query.shipment.allunit',
                compact('unit_data', 'request',  'showTitle'))->render();
            $result['url'] = url('commercial/query?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    

    public function commShipmentQuerySupplier(Request $request)
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

            $data=[];
            $condition = [];
            if (isset($request1['unit'])){
                $condition['hr_unit']=  $request1['unit'];
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            $bCondition = $this->getShipmentInfo($datecon,$condition)->unique('mr_supplier_sup_id')->pluck('mr_supplier_sup_id');

            $suppliers = Supplier::whereIn('sup_id', $bCondition)->get();
               
           


            //dd($condition);
            foreach ($suppliers as $key => $supplier) {
                $condition['mr_supplier_sup_id']=  $supplier->sup_id ;
                $supplier_data[$key]= new stdClass;
                $supplier_data[$key]->id = $supplier->sup_id;
                $supplier_data[$key]->name = $supplier->sup_name;
                
                $piinfo = $this->getShipmentInfo($datecon,$condition);
                $supplier_data[$key]->pi = $piinfo->count();
                $supplier_data[$key]->qty = $piinfo->sum('qty');
                $supplier_data[$key]->value = $piinfo->sum('value');
               
            }

            
            

            //return dd($supplier_data);
            $result = [];
            $result['page'] = view('commercial.query.shipment.allsupplier',
                compact('supplier_data', 'request1',  'showTitle','data'))->render();
            $result['url'] = url('commercial/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function commShipmentQueryShipment(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());

            parse_str($parts['query'], $request1);
            $request1['view']= 'shipment';
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->supplier)) {
                $request1['supplier']= $request->supplier;
            }
            $data=[];
            if(isset($request1['unit'])){
                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
            }
            if(isset($request1['supplier'])){
                $data['supplier'] = Supplier::where('sup_id',$request1['supplier'])->first();
            }
            $showTitle = $this->pageTitle($request1);


            $result['page'] = view('commercial.query.shipment.allshipment',
                compact('data','request1','showTitle'))->render();
            $result['url'] = url('commercial/query?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function  commShipmentQueryListShipment(Request $request){
        try {

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if (!empty($request->unit)) {
                $request1['unit']= $request->unit;
            }
            if (!empty($request->supplier)) {
                $request1['supplier']= $request->supplier;
            }
            //return dd($request1);

            $condition=[];
            if(isset($request1['unit'])){
                $condition['hr_unit'] = $request1['unit'];
            }
            if(isset($request1['supplier'])){
                $condition['mr_supplier_sup_id'] = $request1['supplier'];
            }
            $datecon = $this->getSearchType($request1);
            $pidata = $this->getShipmentInfo($datecon,$condition);
            //return dd($condition);
            return DataTables::of($pidata)->addIndexColumn()
                      ->addColumn('supplier', function($pidata){
                          return Supplier::where('sup_id',$pidata->mr_supplier_sup_id)->first()->sup_name??'';
                      })
                      ->addColumn('unit', function($pidata){
                          return Unit::where('hr_unit_id',$pidata->hr_unit)->first()->hr_unit_name??'';
                      })
                      ->rawColumns(['supplier','unit'])
                      ->toJson();


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }

    public function print(Request $request)
    {
    	$info = [];
    	if(isset($request->supplier)) {
            $info['supplier'] = Supplier::where('sup_id',$request->supplier)->first()->sup_name??'';
        }

        if(isset($request->unit)) {
            $info['unit'] = Unit::where('hr_unit_id',$request->unit)->first()->hr_unit_name??'';
        }
    	$type= $request->type;
        $data = $request->data;
        $title = $request->title;
        return view('commercial.query.shipment.print',compact('data','title','type','info'))->render();
    }
}
