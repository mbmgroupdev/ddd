<?php

namespace App\Http\Controllers\Commercial\Shipment;

use App\Http\Controllers\Controller;
use App\Models\Merch\Supplier;
use App\Models\Merch\Buyer;
use App\Models\Merch\OrderBomCostingBooking;
use App\Models\Commercial\CmFile;
use App\Models\Commercial\CmPiBom;
use App\Models\Commercial\CmPiMaster;
use App\Models\Commercial\CmInvoicePiBom;
use App\Models\Merch\OrderDetailsBooking;
use App\Models\Merch\OrderEntry;
use App\Models\Commercial\PiForwardDetails;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables, stdClass;

class ShipmentController extends Controller
{

    public function index(Request $request)
    {
        try{
            $ratio = new stdClass();
            $ratio->pi = $this->getPiRatio();
            $ratio->ilc = $this->getIlcRatio();
            $ratio->order = $this->getOrderRatio();

            $latest = new stdClass();
            $latest->pi = $this->getLatestPiShipment();
            $latest->lc = $this->getLatestLcShipment();
            $latest->order = $this->getLatestOrderShipment();
            return view('commercial.shipment.shipment', compact('ratio','latest'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getPiRatio(){
        $cpb = CmPiBom::sum('pi_qty');
        if($cpb==0){
           $cpb = 1; 
        }
        //dd($cpb);
        $cipb = CmInvoicePiBom::sum('shipped_qty')??0;
        $ratio = round(($cipb/$cpb)*100,2);
        return $ratio;
    }

    public function getIlcRatio(){
        $cpb = DB::table('cm_pi_bom as cpb')
                ->Join('cm_pi_forwarding_details as cpfd', 'cpfd.cm_pi_master_id','cpb.cm_pi_master_id')
                ->sum('cpb.pi_qty');

        if($cpb==0){
           $cpb = 1; 
        }
        $cipb = CmInvoicePiBom::sum('shipped_qty')??0;
        $ratio = round(($cipb/$cpb)*100,2);
        return $ratio;
    }
    public function getOrderRatio(){
        $cpb = DB::table('mr_order_booking as ob')
                ->Join("mr_order_bom_costing_booking AS bom","bom.id",'ob.mr_order_bom_costing_booking_id' )
                ->leftJoin('mr_order_entry as o','o.order_id','bom.order_id')
                ->whereNotIn('o.order_status',['Completed','Inactive'])
                ->sum('ob.booking_qty');
        //dd($cpb);
        if($cpb==0){
           $cpb = 1; 
        }
        $cipb = CmInvoicePiBom::sum('shipped_qty')??0;
        $ratio = round(($cipb/$cpb)*100,2);
        return $ratio;
    }

    public function getLatestPiShipment(){
        $latest = DB::table('cm_invoice_pi_bom as cipb')
                ->select(
                    'cpm.pi_no',
                    'inv.invoice_no',
                    'inv.invoice_date',
                    DB::raw('sum(shipped_qty) as shipped')
                )
                ->leftJoin('cm_pi_master as cpm', 'cpm.id','cipb.cm_pi_master_id')
                ->leftJoin('cm_imp_invoice as inv', 'inv.id','cipb.cm_imp_invoice_id')
                ->where('cipb.shipped_qty','!=', 0)
                ->groupBy('cipb.cm_imp_invoice_id')
                ->groupBy('cipb.cm_pi_master_id')
                ->orderBy('inv.id','DESC')
                ->take(3)
                ->get();
        return $latest;

    }

    public function getLatestLcShipment(){
        $latest = DB::table('cm_invoice_pi_bom as cipb')
                ->select(
                    'lc.lc_no',
                    'inv.invoice_no',
                    'inv.invoice_date',
                    DB::raw('sum(shipped_qty) as shipped')
                )
                ->Join('cm_pi_forwarding_details as cpfd', 'cpfd.cm_pi_master_id','cipb.cm_pi_master_id')
                ->leftJoin('cm_imp_invoice as inv', 'inv.id','cipb.cm_imp_invoice_id')
                ->leftJoin('cm_btb as lc', 'lc.cm_pi_forwarding_master_id','cpfd.cm_pi_forwarding_master_id')
                ->where('cipb.shipped_qty','!=', 0)
                ->groupBy('cipb.cm_imp_invoice_id')
                ->groupBy('cpfd.cm_pi_forwarding_master_id')
                ->orderBy('inv.id','DESC')
                ->take(3)
                ->get();
        return $latest;
    }

    public function getLatestOrderShipment(){
        $latest = DB::table('cm_invoice_pi_bom as cipb')
                ->select(
                    'o.order_code',
                    'inv.invoice_no',
                    'inv.invoice_date',
                    DB::raw('sum(shipped_qty) as shipped')
                )
                ->Join('cm_pi_bom as cpb', 'cpb.id','cipb.cm_pi_bom_id')
                ->leftJoin('cm_imp_invoice as inv', 'inv.id','cipb.cm_imp_invoice_id')
                ->leftJoin('mr_order_entry as o', 'o.order_id','cpb.mr_order_entry_order_id')
                ->where('cipb.shipped_qty','!=', 0)
                ->groupBy('cipb.cm_imp_invoice_id')
                ->groupBy('cpb.mr_order_entry_order_id')
                ->orderBy('inv.id','DESC')
                ->take(3)
                ->get();
        //dd($latest);
        return $latest;
    }

    public function getShipmentHistory($where){
        $shipment= DB::table('cm_invoice_pi_bom as cipb')
                ->select(
                    DB::raw('sum(cipb.shipped_qty) as shipped_qty'),
                    'i.invoice_no',
                    'i.invoice_date',
                    'cb.bank_name',
                    'd.transp_doc_no1',
                    'lc.lc_no',
                    'f.file_no'
                )
                ->leftJoin('cm_pi_bom as cpb','cpb.id','cipb.cm_pi_bom_id')
                ->leftJoin('cm_pi_master as cpm','cpm.id','cipb.cm_pi_master_id')
                ->leftJoin('cm_imp_invoice as i','i.id','cipb.cm_imp_invoice_id')
                ->leftJoin('cm_imp_data_entry as d', 'd.id','i.cm_imp_data_entry_id')
                ->leftJoin('cm_btb as lc','lc.id','d.cm_btb_id')
                //->leftJoin('mr_order_entry as o','o.order_id','cpb.mr_order_entry_order_id')
                ->leftJoin('cm_file as f','f.id','d.cm_file_id')
                ->where($where)
                ->leftJoin('cm_bank as cb','cb.id','d.cm_bank_id')
                ->groupBy('cipb.cm_imp_invoice_id')
                ->get();

        return view("commercial.shipment.get_shipment_history",compact('shipment'))->render(); 

    }


    public function shipmentDetails(Request $request){
        $result = [];
        $type = $request->type;
        $request1['type'] =  $request->type;
        if($type == 'pi'){
            $result['page'] = view("commercial.shipment.pi_list")->render();    
        }
        if($type == 'order'){
            $result['page'] = view("commercial.shipment.order_list", compact('type'))->render();    
        }
        if($type == 'fabric' || $type == 'sewing' || $type == 'finishing'){
            $result['page'] = view("commercial.shipment.cat_order_list", compact('type'))->render();    
        }
        if($type == 'lc'){
            $result['page'] = view("commercial.shipment.lc_list")->render();    
        }
        $result['url'] = url('commercial/shipment?').http_build_query($request1);
        return $result;
    }

    public function piShipmentList(Request $request){

        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();

        $query1 = DB::table('cm_pi_bom as cpb')
                ->select(
                    'cpm.pi_no',
                    'cpm.id as pi_id',
                    DB::raw('sum(cipb.shipped_qty) as shipped'),
                    DB::raw('sum(cpb.pi_qty) as pi_qty'),
                    'lc.lc_no',
                    'lc.id as btb_id',
                    'cpm.total_pi_value',
                    'cpm.mr_supplier_sup_id'
                )
                ->Join('cm_pi_master as cpm', 'cpm.id','cpb.cm_pi_master_id')
                ->leftJoin('cm_pi_forwarding_details as cpfd', 'cpfd.cm_pi_master_id','cpb.cm_pi_master_id')
                ->leftJoin('mr_order_entry as o', 'o.order_id','cpb.mr_order_entry_order_id')
                ->whereNotIn('o.order_status',['Inactive','Completed'])
                ->leftJoin('cm_btb as lc', 'lc.cm_pi_forwarding_master_id','cpfd.cm_pi_forwarding_master_id');
                $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
                    $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
                });

        $piWise = $query1->groupBy('cpb.cm_pi_master_id')
                         ->orderBy('cpb.id', 'DESC')
                         ->get();

        //dd($piWise);

        return DataTables::of($piWise)->addIndexColumn()
                
                ->addColumn('lc_no', function ($piWise) {
                    if($piWise->lc_no){
                        $pi = $piWise->lc_no;
                    }else{
                        $pi = '<span style="color:#ff0000;">No L/C Created</span>';
                    }
                    return $pi;
                })
                ->editColumn('shipped', function ($piWise) {
                    if($piWise->shipped){
                        $pi = 'Shipped Qty: <b>'.round($piWise->shipped,2).'</b>';
                    }else{
                        $pi = '<span style="color:#ff0000;">Not Started</span>';
                    }
                    return $pi;
                })
                ->addColumn('rem', function ($piWise) {
                    $rem = $piWise->pi_qty-$piWise->shipped;
                    return round($rem,2);
                })
                ->addColumn('supplier', function($piWise){
                    return Supplier::where('sup_id',$piWise->mr_supplier_sup_id)->first()->sup_name??'';
                })
                ->addColumn('status', function ($piWise) {
                    $shipped_pi = $piWise->shipped??0;
                    $percent = round(($shipped_pi/$piWise->pi_qty)*100,1);
                    return $percent.'%';
                })
               ->editColumn('pi_no', function ($piWise){
                    return HtmlFacade::link("commercial/shipment/pi/{$piWise->pi_id}",$piWise->pi_no,[])??'';
                })
                ->rawColumns(['lc_no','shipped','rem','status','pi_no'])
                ->make(true);

    }
    public function lcShipmentList(Request $request){

        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();

        $query1 = DB::table('cm_pi_bom as cpb')
                ->select(
                    //DB::raw('group_concat(distinct cpm.pi_no) as pi'),
                    DB::raw('sum(cipb.shipped_qty) as shipped'),
                    DB::raw('sum(cpb.pi_qty) as pi_qty'),
                    'lc.lc_no',
                    'lc.id as btb_id',
                    'lc.lc_date',
                    'lc.lc_status',
                    'cpm.total_pi_value',
                    'cpm.mr_supplier_sup_id'
                )
                ->Join('cm_pi_master as cpm', 'cpm.id','cpb.cm_pi_master_id')
                ->leftJoin('cm_pi_forwarding_details as cpfd', 'cpfd.cm_pi_master_id','cpb.cm_pi_master_id')
                ->leftJoin('mr_order_entry as o', 'o.order_id','cpb.mr_order_entry_order_id')
                ->whereNotIn('o.order_status',['Inactive','Completed'])
                ->Join('cm_btb as lc', 'lc.cm_pi_forwarding_master_id','cpfd.cm_pi_forwarding_master_id');
                $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
                    $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
                });

        $lcWise = $query1->groupBy('lc.id')
                         ->orderBy('cpb.id', 'DESC')
                         ->get();

        //dd($lcWise);

        return DataTables::of($lcWise)->addIndexColumn()
                ->editColumn('shipped', function ($lcWise) {
                    if($lcWise->shipped){
                        $pi = 'Shipped Qty: <b>'.round($lcWise->shipped,2).'</b>';
                    }else{
                        $pi = '<span style="color:#ff0000;">Not Started</span>';
                    }
                    return $pi;
                })
                ->addColumn('rem', function ($lcWise) {
                    $rem = $lcWise->pi_qty-$lcWise->shipped;
                    return round($rem,2);
                })
                ->addColumn('status', function ($lcWise) {
                    $shipped_pi = $lcWise->shipped??0;
                    $percent = round(($shipped_pi/$lcWise->pi_qty)*100,1);
                    return $percent.'%';
                })
               ->editColumn('lc_no', function ($lcWise){
                    return HtmlFacade::link("commercial/shipment/lc/{$lcWise->btb_id}",$lcWise->lc_no,[])??'';
                })
                ->rawColumns(['lc_no','shipped','rem','status'])
                ->make(true);

    }

    public function getOrderShipment($request){
        $where = [];
        if(isset($request->type)){

            if($request->type == 'fabric'){
                $where['ob.mr_cat_item_mcat_id'] = 1;
            }else if($request->type == 'sewing'){
                $where['ob.mr_cat_item_mcat_id'] = 2;
            }else if($request->type == 'finishing'){
                $where['ob.mr_cat_item_mcat_id'] = 3;
            }
        }

        if(isset($request->order_id)){
            $where['bom.order_id'] = $request->order_id;
        }

        if(isset($request->cat_id)){
            $where['ob.mr_cat_item_mcat_id'] = $request->cat_id;
        }

        //dd($where);

        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();
        //booking qty order

        $query1 = DB::table('cm_pi_bom as cpb')
                ->select(
                    //DB::raw('group_concat(distinct cpm.pi_no) as pi'),
                    DB::raw('sum(cipb.shipped_qty) as shipped'),
                    DB::raw('sum(cpb.pi_qty) as pi_qty'),
                    'cpb.mr_order_booking_id as booking_id'
                );
        $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
            $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
        });

        $cpbData = $query1->groupBy('cpb.mr_order_booking_id');
        $cpbData_sql = $cpbData->toSql();



        $query2 = DB::table('mr_order_booking as ob')->select([
                        DB::raw('SUM(ob.booking_qty) as booking'),
                        DB::raw('sum(b.shipped) as shipped'),
                        DB::raw('sum(b.pi_qty) as pi_qty'),
                        'o.order_code',
                        'o.order_id',
                        'o.order_delivery_date'
                    ])
                    ->Join("mr_order_bom_costing_booking AS bom","bom.id",'ob.mr_order_bom_costing_booking_id' )
                    ->leftJoin('mr_order_entry as o', 'o.order_id','bom.order_id')
                    ->whereNotIn('o.order_status',['Inactive','Completed'])
                    ->where($where);
        $query2->leftJoin(DB::raw('(' . $cpbData_sql. ') AS b'), function($join) use ($cpbData) {
            $join->on('b.booking_id', '=', 'ob.id')->addBinding($cpbData->getBindings());
        });
        if(isset($request->order_id)){          
            $orderWise = $query2->groupBy('bom.order_id')->first();
        }else{
            $orderWise = $query2->groupBy('bom.order_id')->get();
        }
        //dd($orderWise);
        return $orderWise;
    }

    public function orderShipmentList(Request $request){
        //shipped qty pi
        $orderWise = $this->getOrderShipment($request);

        return DataTables::of($orderWise)->addIndexColumn()
                ->editColumn('shipped', function ($orderWise) {
                    if($orderWise->shipped){
                        $pi = 'Shipped Qty: <b>'.round($orderWise->shipped,2).'</b>';
                    }else{
                        $pi = '<span style="color:#ff0000;">Not Started</span>';
                    }
                    return $pi;
                })
                ->addColumn('rem', function ($orderWise) {
                    $rem = $orderWise->booking-$orderWise->shipped;
                    return round($rem,2);
                })
                ->editColumn('booking', function ($orderWise) {
                    $booking = round($orderWise->booking,2);
                    return $booking;
                })
                ->addColumn('status', function ($orderWise) {
                    $shipped_pi = $orderWise->shipped??0;
                    $percent = round(($shipped_pi/$orderWise->booking)*100,1);
                    return $percent.'%';
                })
               ->editColumn('order_code', function ($orderWise){
                    return HtmlFacade::link("commercial/shipment/order/{$orderWise->order_id}",$orderWise->order_code,[])??'';
                })
                ->rawColumns(['order_code','shipped','rem','status'])
                ->make(true);

    }

    public function catOrderShipmentList(Request $request){


        $orderWise = $this->getOrderShipment($request);

        return DataTables::of($orderWise)->addIndexColumn()
                ->editColumn('shipped', function ($orderWise) {
                    if($orderWise->shipped){
                        $pi = 'Shipped Qty: <b>'.round($orderWise->shipped,2).'</b>';
                    }else{
                        $pi = '<span style="color:#ff0000;">Not Started</span>';
                    }
                    return $pi;
                })
                ->addColumn('rem', function ($orderWise) {
                    $rem = $orderWise->booking-$orderWise->shipped;
                    return round($rem,2);
                })
                ->editColumn('booking', function ($orderWise) {
                    $booking = round($orderWise->booking,2);
                    return $booking;
                })
                ->addColumn('status', function ($orderWise) {
                    $shipped_pi = $orderWise->shipped??0;
                    $percent = round(($shipped_pi/$orderWise->booking)*100,1);
                    return $percent.'%';
                })
               ->editColumn('order_code', function ($orderWise){
                    return HtmlFacade::link("commercial/shipment/order/{$orderWise->order_id}",$orderWise->order_code,[])??'';
                })
                ->rawColumns(['order_code','shipped','rem','status'])
                ->make(true);
    }

    public function piShipment($pi_id){
        $piInfo = CmPiMaster::where('id',$pi_id)->first();
        $piInfo->pi_qty = CmPiBom::where('cm_pi_master_id',$pi_id)->sum('pi_qty');
        $piInfo->shipped_qty = CmInvoicePiBom::where('cm_pi_master_id',$pi_id)->sum('shipped_qty')??0;
        $piInfo->buyer = buyer::where('b_id',$piInfo->mr_buyer_b_id)->first()->b_name;
        $piInfo->supplier = Supplier::where('sup_id',$piInfo->mr_supplier_sup_id)->first()->sup_name;
        //dd($piInfo);
        $lc_no = DB::table('cm_btb')
                ->leftJoin('cm_pi_forwarding_details as pi','pi.cm_pi_forwarding_master_id','cm_btb.cm_pi_forwarding_master_id')
                ->where('pi.cm_pi_master_id',$pi_id)
                ->first()
                ->lc_no??'';
        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();

        $query1 = DB::table('cm_pi_bom as cpb')
                ->select(
                    DB::raw('sum(cipb.shipped_qty) AS shipped_qty'),
                    DB::raw('sum(cpb.pi_qty) AS pi_qty'),
                    'o.order_code',
                    'o.order_id'
                )
                ->Join('cm_pi_master as cpm', 'cpm.id','cpb.cm_pi_master_id')
                ->leftJoin('cm_pi_forwarding_details as cpfd', 'cpfd.cm_pi_master_id','cpb.cm_pi_master_id')
                ->leftJoin('mr_order_entry as o', 'o.order_id','cpb.mr_order_entry_order_id')
                ->whereNotIn('o.order_status',['Inactive','Completed'])
                ->leftJoin('cm_btb as lc', 'lc.cm_pi_forwarding_master_id','cpfd.cm_pi_forwarding_master_id');
                $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
                    $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
                });
        $orderWise = clone $query1->where('cpb.cm_pi_master_id',$pi_id)
                                  ->groupBy('cpb.mr_order_entry_order_id')
                                  ->get();

        $piWise = clone $query1->where('cpb.cm_pi_master_id',$pi_id)
                               ->orderBy('cpb.id', 'ASC')
                               ->get();
        $piWiseData = collect($piWise)->groupBy('order_code',true);

        
        $where['pi_id'] = $pi_id;
        $bomItem = [];
        foreach ($orderWise as $key => $order) {
            $where['order_id'] = $order->order_id;
            $bomItem[$order->order_id]  = $this->getBomItem($where);
        }

        $shipment = $this->getShipmentHistory(['cipb.cm_pi_master_id'=>$pi_id]);

        return view("commercial.shipment.piwise_shipment", compact('piInfo','piWiseData','orderWise','lc_no','bomItem','shipment'));
    }
    public function lcShipment($lc_id){
        $lcInfo = DB::table('cm_btb')->where('id',$lc_id)->first();
        $lcPi = PiForwardDetails::where('cm_pi_forwarding_master_id',$lcInfo->cm_pi_forwarding_master_id)
                ->pluck('cm_pi_master_id')->toArray();

        $lcInfo->pi_qty = CmPiBom::whereIn('cm_pi_master_id',$lcPi)->sum('pi_qty');
        $lcInfo->shipped_qty = CmInvoicePiBom::whereIn('cm_pi_master_id',$lcPi)->sum('shipped_qty')??0;
        $lcInfo->supplier = Supplier::where('sup_id',$lcInfo->mr_supplier_sup_id)->first()->sup_name;
        $lcInfo->file = DB::table('cm_file')->where('id',$lcInfo->cm_file_id)->first()->file_no;
        
        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();

        $query1 = DB::table('cm_pi_bom as cpb')
                ->select(
                    DB::raw('sum(cipb.shipped_qty) AS shipped_qty'),
                    DB::raw('sum(cpb.pi_qty) AS pi_qty'),
                    'o.order_code',
                    'o.order_id'
                )
                ->Join('cm_pi_master as cpm', 'cpm.id','cpb.cm_pi_master_id')
                ->leftJoin('cm_pi_forwarding_details as cpfd', 'cpfd.cm_pi_master_id','cpb.cm_pi_master_id')
                ->leftJoin('mr_order_entry as o', 'o.order_id','cpb.mr_order_entry_order_id')
                ->whereNotIn('o.order_status',['Inactive','Completed'])
                ->leftJoin('cm_btb as lc', 'lc.cm_pi_forwarding_master_id','cpfd.cm_pi_forwarding_master_id')
                ->whereIn('cpb.cm_pi_master_id',$lcPi);
                $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
                    $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
                });
        $orderWise =  $query1->groupBy('cpb.mr_order_entry_order_id')
                             ->get();

        $where['pi_group'] = $lcPi;
        $bomItem = [];
        foreach ($orderWise as $key => $order) {
            $where['order_id'] = $order->order_id;
            $bomItem[$order->order_id]  = $this->getBomItem($where);
        }


        $shipment = $this->getShipmentHistory(['lc.id'=>$lc_id]);


        return view("commercial.shipment.lc_shipment", compact('lcInfo','orderWise','bomItem','shipment'));
    }

    public function getBomItem($where){
        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();

        $query1 = DB::table('mr_order_booking As mob')
                ->select(
                  "mc.clr_name",
                  "mob.*",
                  "c.mcat_name",
                  "c.mcat_id",
                  "sz.mr_product_pallete_name",
                  "i.item_name",
                  "i.item_code",
                  "i.id as item_id",
                  "i.dependent_on",
                  "s.sup_name", 
                  "a.art_name",
                  "com.comp_name",
                  "con.construction_name",
                  "b.item_description",
                  "b.uom",
                  "b.consumption",
                  "b.extra_percent",
                  "b.precost_unit_price",
                  "b.order_id",
                  "o.order_code",
                  "cpb.pi_qty",
                  "cpb.currency",
                  "cipb.shipped_qty"
                )
                ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                ->leftJoin("mr_material_category AS c", function($join) {
                  $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
                })
                ->leftJoin("mr_cat_item AS i", function($join) {
                    $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                    $join->on("i.id", "=", "b.mr_cat_item_id");
                })
                ->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
                ->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
                ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
                ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
                ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
                ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
                ->leftJoin("mr_order_entry AS o","o.order_id","b.order_id")
                ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                ->where(function($condition) use ($where){
                        if (!empty($where['order_id'])) {
                            $condition->where('o.order_id', $where['order_id']);
                        }
                        if (!empty($where['pi_id'])) {
                            $condition->where('cpb.cm_pi_master_id', $where['pi_id']);
                        }
                        if (!empty($where['pi_group'])) {
                            $condition->whereIn('cpb.cm_pi_master_id', $where['pi_group']);
                        }
                })
                ->orderBy("mob.id");
        $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
            $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
        });
        $booking = $query1->get();
        $bookColl = collect($booking)->groupBy('mr_order_bom_costing_booking_id', true);
        $bom = collect($bookColl)->groupBy('mr_cat_item_mcat_id', true);

        return view("commercial.shipment.get_order_bom",compact('bom'))->render();  
    }

    public function orderShipment($order_id){

        $orderInfo = OrderEntry::where('order_id',$order_id)->first();
        $orderInfo->pi_qty = CmPiBom::where('mr_order_entry_order_id',$order_id)->sum('pi_qty');
        $orderInfo->booking_qty = DB::table('mr_order_booking as ob')
                                    ->Join("mr_order_bom_costing_booking AS bom","bom.id",'ob.mr_order_bom_costing_booking_id' )
                                    ->where('bom.order_id',$order_id)
                                    ->sum('ob.booking_qty');
        $orderInfo->shipped_qty = DB::table('cm_pi_bom as cpb')
                                    ->Join('cm_invoice_pi_bom as cipb','cpb.id','cipb.cm_pi_bom_id')
                                    ->where('cpb.mr_order_entry_order_id',$order_id)
                                    ->sum('cipb.shipped_qty')??0;

        $orderInfo->buyer = buyer::where('b_id',$orderInfo->mr_buyer_b_id)->first()->b_name;
        

       


        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();

        $query1 = DB::table('cm_pi_bom as cpb')
                ->select(
                    DB::raw('sum(cipb.shipped_qty) AS shipped_qty'),
                    DB::raw('sum(cpb.pi_qty) AS pi_qty'),
                    'cpm.pi_no',
                    'cpm.id',
                    'lc.lc_no'
                )
                ->Join('cm_pi_master as cpm', 'cpm.id','cpb.cm_pi_master_id')
                ->leftJoin('cm_pi_forwarding_details as cpfd', 'cpfd.cm_pi_master_id','cpb.cm_pi_master_id')
                ->leftJoin('mr_order_entry as o', 'o.order_id','cpb.mr_order_entry_order_id')
                ->whereNotIn('o.order_status',['Inactive','Completed'])
                ->leftJoin('cm_btb as lc', 'lc.cm_pi_forwarding_master_id','cpfd.cm_pi_forwarding_master_id')
                ->where('o.order_id',$order_id);
                $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
                    $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
                });

        $piWise =   $query1->groupBy('cpb.cm_pi_master_id')
                           ->orderBy('cpm.id', 'DESC')
                           ->get();
        
        $where['order_id'] = $order_id;
        $bomItem = [];
        foreach ($piWise as $key => $pi) {
            $where['pi_id'] = $pi->id;
            $bomItem[$pi->id]  = $this->getBomItem($where);
        }

        $shipment = $this->getShipmentHistory(['cpb.mr_order_entry_order_id'=>$order_id]);

        return view("commercial.shipment.order_shipment", compact('orderInfo','piWise','bomItem','shipment'));
    }

    public function getItemWiseBom($where){
        $query= DB::table('cm_invoice_pi_bom')
                ->select(
                    DB::raw('sum(shipped_qty) AS shipped_qty'),
                    'cm_pi_bom_id as cipb_id'
                );
        $shippedData = $query->groupBy('cm_pi_bom_id');
        $shippedData_sql = $shippedData->toSql();
        //booking qty order
        $query1 = DB::table('cm_pi_bom as cpb')
                ->select(
                    //DB::raw('group_concat(distinct cpm.pi_no) as pi'),
                    DB::raw('sum(cipb.shipped_qty) as shipped'),
                    DB::raw('sum(cpb.pi_qty) as pi_qty'),
                    'cpb.mr_order_booking_id as booking_id',
                    'cpb.currency'
                );
        $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
            $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
        });

        $cpbData = $query1->groupBy('cpb.mr_order_booking_id');
        $cpbData_sql = $cpbData->toSql();

        //dd($cpbData->get());

        $query2 = DB::table('mr_order_booking as ob')->select([
                        'ob.*',
                        'b.shipped as shipped_qty',
                        'b.pi_qty',
                        'o.order_code',
                        'o.order_id',
                        'o.order_delivery_date',
                        "c.mcat_name",
                        "c.mcat_id",
                        "sz.mr_product_pallete_name",
                        "i.item_name",
                        "i.item_code",
                        "i.id as item_id",
                        "i.dependent_on",
                        "s.sup_name", 
                        "a.art_name",
                        "com.comp_name",
                        "con.construction_name",
                        "bom.item_description",
                        "bom.uom",
                        "bom.consumption",
                        "bom.extra_percent",
                        "bom.precost_unit_price",
                        "b.currency",
                        "mc.clr_name",
                    ])
                    ->leftJoin("mr_order_bom_costing_booking AS bom","bom.id",'ob.mr_order_bom_costing_booking_id' )
                    ->leftJoin("mr_material_category AS c", function($join) {
                      $join->on("c.mcat_id", "=", "bom.mr_material_category_mcat_id");
                    })
                    ->leftJoin("mr_cat_item AS i", function($join) {
                        $join->on("i.mcat_id", "=", "bom.mr_material_category_mcat_id");
                        $join->on("i.id", "=", "bom.mr_cat_item_id");
                    })
                    ->leftJoin("mr_material_color AS mc", "mc.clr_id", "ob.mr_material_color_id")
                    ->leftJoin("mr_product_size AS sz", "sz.id","ob.size")
                    ->leftJoin("mr_supplier AS s", "s.sup_id", "bom.mr_supplier_sup_id")
                    ->leftJoin("mr_article AS a", "a.id", "bom.mr_article_id")
                    ->leftJoin("mr_composition AS com", "com.id", "bom.mr_composition_id")
                    ->leftJoin("mr_construction AS con", "con.id", "bom.mr_construction_id")
                    ->leftJoin('mr_order_entry as o', 'o.order_id','bom.order_id')
                    ->whereNotIn('o.order_status',['Inactive','Completed'])
                    ->where($where);
        $query2->Join(DB::raw('(' . $cpbData_sql. ') AS b'), function($join) use ($cpbData) {
            $join->on('b.booking_id', '=', 'ob.id')->addBinding($cpbData->getBindings());
        });

    
        $bom = $query2->get();
        //dd(array_sum(array_column($bom->toArray(), 'shipped_qty')));
        $booking = $query2->get();
        $bookColl = collect($booking)->groupBy('mr_order_bom_costing_booking_id', true);
        $bom = collect($bookColl)->groupBy('mr_cat_item_mcat_id', true);

        //dd($bom);

        return view("commercial.shipment.get_order_bom", compact('bom'))->render();  
    }



    public function catOrderShipment($id, $category){
        $request = new stdClass();
        $request->order_id = $id;
        $request->cat_id = $category;

        $orderWise = $this->getOrderShipment($request);

        $orderInfo = OrderEntry::where('order_id',$id)->first();
        $orderInfo->pi_qty = $orderWise->pi_qty;
        $orderInfo->booking_qty = $orderWise->booking;
        $orderInfo->shipped_qty = $orderWise->shipped;

        $orderInfo->buyer = buyer::where('b_id',$orderInfo->mr_buyer_b_id)->first()->b_name;
        

       



        $itemWise = OrderBomCostingBooking::where([
                        'mr_material_category_mcat_id' => $category,
                        'order_id' => $id
                    ])
                    ->pluck('mr_cat_item_id');
        //dd($itemWise);

        $where['bom.order_id'] = $id;
        $where['ob.mr_cat_item_mcat_id'] = $category;
        $bomItem = [];
        foreach ($itemWise as $key => $item) {
            $where['bom.mr_cat_item_id'] = $item;
            $bomItem[$item]  = $this->getItemWiseBom($where);
        }
        //dd($bomItem);

        $shipment = $this->getShipmentHistory(['cpb.mr_order_entry_order_id'=>$id]);

        return view("commercial.shipment.cat_order_shipment", compact('orderInfo','itemWise','bomItem','shipment'));
    }

    


}