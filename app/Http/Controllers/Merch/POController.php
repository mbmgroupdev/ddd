<?php

namespace App\Http\Controllers\Merch;

use App\Http\Controllers\Controller;
use App\Models\Merch\MrPoBomOtherCosting;
use App\Models\Merch\MrPoOperationNCost;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomOtherCosting;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\OrderOperationNCost;
use App\Models\Merch\PoBOM;
use App\Models\Merch\PoSizeQty;
use App\Models\Merch\ProductSize;
use App\Models\Merch\PurchaseOrder;
use App\Models\Merch\StyleSizeGroup;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class POController extends Controller
{
    public function __construct()
    {
        ini_set('zlib.output_compression', 1);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        return view("merch.po.list");
    }

    public function list(){

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
        $getBuyer = buyer_by_id();
        $getUnit = unit_by_id();
        $getSeason = season_by_id();
        $getCountry = country_by_id();
        $getColor = material_color_by_id();

        $orderData = DB::table('mr_order_entry');
        $getOrderSql = $orderData->toSql();

        $styleData = DB::table('mr_style');
        $styleSqlData = $styleData->toSql();

        $queryData = DB::table('mr_purchase_order AS po')
            ->select(["o.order_id","o.order_code","o.mr_buyer_b_id","o.unit_id","o.created_by",'po.po_no','po.po_qty', 'po.po_ex_fty', 'po.po_delivery_country', 'po.country_fob', 'po.clr_id', 'po.po_id', 'stl.stl_no'
            ])
            ->whereIn('o.mr_buyer_b_id', auth()->user()->buyer_permissions());
            if(!empty($team)){
                $queryData->whereIn('o.created_by', $team);
            }
            $queryData->join(DB::raw('(' . $getOrderSql. ') AS o'), function($join) use ($orderData) {
                $join->on('o.order_id','po.mr_order_entry_order_id')->addBinding($orderData->getBindings());
            })->join(DB::raw('(' . $styleSqlData. ') AS stl'), function($join) use ($styleData) {
                $join->on('stl.stl_id', "o.mr_style_stl_id")->addBinding($styleData->getBindings());
            })->orderBy('o.order_id', 'DESC')->orderBy('po.po_id', 'desc');
            
        $data = $queryData->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('b_name', function ($data) use ($getBuyer){
                return $getBuyer[$data->mr_buyer_b_id]->b_name??'';
            })
            ->addColumn('hr_unit_name', function ($data) use ($getUnit){
                return $getUnit[$data->unit_id]['hr_unit_name']??'';
            })
            ->addColumn('po_color', function ($data) use ($getColor){
                return $getColor[$data->clr_id]->clr_name??'';
            })
            ->addColumn('po_country', function ($data) use ($getCountry){
                return $getCountry[$data->po_delivery_country]->cnt_name??'';
            })
            
            ->editColumn('po_ex_fty', function($data){
                return custom_date_format($data->po_ex_fty);
            })
            ->addColumn('action', function ($data) {
                $action_buttons = "<div class=\"btn-group\">
                    <a href='".url('merch/po/').'/'.$data->po_id.'/edit'."' class=\"btn btn-xs btn-secondary\" data-toggle=\"tooltip\" title=\"PO Edit\">
                    <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>
                    <a href='".url("merch/po-bom/$data->po_id")."' class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"PO BOM\">
                    <i class=\"las la-clipboard-list\"></i>
                    </a>
                    <a href='".url("merch/po-costing/$data->po_id")."' class=\"btn btn-xs btn-warning text-white\" data-toggle=\"tooltip\" title=\"Order Costing\">
                    <i class=\"las la-clipboard-list\"></i>
                    </a>
                    
                    </div>";
                return $action_buttons;
            })
            ->rawColumns(['order_code', 'order_ref_no', 'hr_unit_name', 'b_name', 'se_name', 'stl_no', 'order_qty', 'order_delivery_date', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('merch.po.create');
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
        // return $input;
        DB::beginTransaction();
        try {
            // PO create
            $getPo = PurchaseOrder::getPOCheckUniqueWiseExists($input);
            if($getPo == true){
                $data['message'] = "PO Already Exists";
                return $data;
            }
            // Order BOM other costing
            $orderOtherCosting = OrderBomOtherCosting::getOrderIdWiseOrderOtherCosting($input['order_id']);
            // PO create
            $input['country_fob'] = $orderOtherCosting->agent_fob;
            $input['mr_order_entry_order_id'] = $input['order_id'];
            $poId = PurchaseOrder::create($input)->po_id;
            // mr_po_size_qty create & check
            foreach ($input['size_group'] as $key => $value) {
                if($value != "0" || $value != 0){
                    DB::table('mr_po_size_qty')->insert([
                        'po_id' => $poId,
                        'mr_product_size_id' => $key,
                        'qty' => $value
                    ]);
                }
            }
            // Order BOM & Costing info
            $getOrderBOMCosting = OrderBOM::getOrderWiseItem($input['order_id'], 'all')->toArray();

            // PO BOM create
            $poBOMCosting = collect($getOrderBOMCosting)->map(function($q) use ($poId) {
                $data = collect($q)->toArray();
                $data['ord_bom_id'] = $data['id'];
                $data['po_id'] = $poId;
                $data['created_by'] = auth()->user()->id;
                unset($data['id'], $data['stl_bom_id']);
                return $data;
            });
            PoBOM::insert($poBOMCosting->toArray());

            // Order Special operation 
            $getOrderSP = OrderOperationNCost::getOrderIdWiseOperation($input['order_id'], 2)->toArray();
            // PO Special operation create
            $poSP = collect($getOrderSP)->map(function($q) use ($input, $poId){
                $data = collect($q)->toArray();
                $data['po_id'] = $poId;
                $data['clr_id'] = $input['clr_id'];
                $data['created_by'] = auth()->user()->id;
                unset($data['order_op_id']);
                return $data;
            });
            MrPoOperationNCost::insert($poSP->toArray());

            // PO BOM other costing
            $poOtherCosting = collect($orderOtherCosting)->toArray();
            $poOtherCosting['po_id'] = $poId;
            $poOtherCosting['clr_id'] = $input['clr_id'];
            unset($poOtherCosting['id']);

            MrPoBomOtherCosting::insert($poOtherCosting);
            // total PO qty
            $data['poqty'] = PurchaseOrder::getPoOrderSumQtyOrderIdWise($input['order_id']);
            $data['type'] = 'success';
            $data['message'] = "PO Successfully Save.";
            DB::commit();
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

        try {
            $po = PurchaseOrder::findOrFail($id);
            $getPort = DB::table('cm_port')
            ->where('cnt_id', $po->po_delivery_country)
            ->pluck('port_name', 'id');

            $order = OrderEntry::orderInfoWithStyle($po->mr_order_entry_order_id);
            if($order == null || $order->style == null){
                toastr()->error("Order Not Found!");
                return back();
            }
            $sizeGroup= StyleSizeGroup::getSizeGroupIdStyleWise($order->style->stl_id);
            
            $getSizeGroup = [];
            if($sizeGroup != null){
                $getSizeGroup= ProductSize::getProductSizeGroupIdWiseInfo($sizeGroup);
            }

            $poSizeQty = PoSizeQty::getPoSizeQtyPoIdWise($id);
            $poSizeKey = collect($poSizeQty)->keyBy('mr_product_size_id');
            $getSizeGroup = collect($getSizeGroup)->map(function($size) use ($poSizeKey){
                if(isset($poSizeKey[$size->id])){
                    // return ($size);
                    $size->value = $poSizeKey[$size->id]->qty;
                }

                return $size;
            });
            $totalPoValue = collect($getSizeGroup)->sum('value');

            $sizeValue = collect($getSizeGroup)->pluck('value','mr_product_pallete_name');
            $totalPoQty = PurchaseOrder::getPoOrderSumQtyOrderIdWise($order->order_id);
            $totalPoQty = $totalPoQty??0;
            return view('merch.po.edit', compact('po', 'order', 'getSizeGroup', 'sizeValue', 'totalPoQty', 'totalPoValue', 'getPort'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
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
        $input = $request->all();
        $data['type'] = 'error';
        $data['value'] = [];
        DB::beginTransaction();
        try {
            // PO create
            $checkPo = PurchaseOrder::getPoInfoConditionWise($input);
            if($checkPo != null){
                if($checkPo->po_id != $id){
                    $data['message'] = "PO Already Exists";
                    return $data;
                }
            }
            // Order BOM other costing
            $orderOtherCosting = OrderBomOtherCosting::getOrderIdWiseOrderOtherCosting($input['order_id']);
            // PO create
            $getPo = PurchaseOrder::findOrFail($id);
            $getPo->update($input);
            // mr_po_size_qty update, delete & check
            
            foreach ($input['size_group'] as $key => $value) {
                if($value != "0" && $value != 0){
                    PoSizeQty::updateOrCreate(
                        [
                            'po_id' => $id,
                            'mr_product_size_id' => $key
                        ],
                        [
                            'qty' => $value
                        ]
                    );
                }else{
                    DB::table('mr_po_size_qty')
                    ->where('po_id', $id)
                    ->where('mr_product_size_id', $key)
                    ->delete();
                }
            }
            $data['type'] = 'success';
            $data['message'] = "PO Successfully Update.";
            $data['url'] = url()->previous();
            DB::commit();
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
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

    public function orderWise(Request $request)
    {
        $input = $request->all();
        if(!isset($input['order_id']) || $input['order_id'] == null){
            toastr()->error("Order Not Found!");
            return back();
        }
        try {
            $order = OrderEntry::orderInfoWithStyle($input['order_id']);
            if($order == null || $order->style == null){
                toastr()->error("Order Not Found!");
                return back();
            }

            $sizeGroup= StyleSizeGroup::getSizeGroupIdStyleWise($order->style->stl_id);
            $getSizeGroup = [];
            if($sizeGroup != null){
                $getSizeGroup= ProductSize::getProductSizeGroupIdWiseInfo($sizeGroup);
            }
            $sizeValue = collect($getSizeGroup)->pluck('value','mr_product_pallete_name');
            $totalPoQty = PurchaseOrder::getPoOrderSumQtyOrderIdWise($order->order_id);
            $totalPoQty = $totalPoQty??0;
            return view('merch.po.create', compact('input', 'order', 'getSizeGroup', 'sizeValue', 'totalPoQty'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }

    public function process(Request $request)
    {
        $input = $request->all();
        $result['type'] = 'error';
        // return $input;
        try {
            if($request->file('file')){
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($request->file('file'));
                 
                $data = [];
                $pages  = $pdf->getPages();
                $flag = 0;
                foreach ($pages as $page) {
                    if(strpos($page->getText(), 'Comp Sizes Comp Qty') !== false){
                        $variable = $page->getText();
                        $b = explode('size level', $variable);
                        $d = $b[1];
                        if(strpos($d, 'This Purchase Order') !== false){
                            $l = substr($d, 0, strpos($d, "This Purchase Order"));
                        }else{
                            $l = $d;
                        }
                        if($l != ' '){
                            $dataReplace = preg_replace('~-{2,}~', '--', $l);
                            $dataExlode = explode('--', $dataReplace);
                            $dataFilter = array_filter(array_map('trim', $dataExlode));
                            if($flag == 0){
                                $flag = 1;
                                $firstIndex = explode(' ', $dataFilter[0]);
                                $dataFilter[0] = end($firstIndex);
                                if(count($dataFilter) > 4){
                                    $newArray = [];
                                    $firstReInd = $this->replaceVal(count($dataFilter), 5, 1);
                                    $otherReInd = $firstReInd * 2;
                                    for ($i=0; $i < count($dataFilter); $i++) { 
                                        if($i == $firstReInd){
                                            $firstExlop = explode(' ', $dataFilter[$i]);
                                            if(count($firstExlop) > 0){
                                                $newArray[] =($firstExlop[0]);
                                                array_shift($firstExlop);
                                                $newArray[] = implode(' ',$firstExlop);
                                            }else{
                                                $newArray[] = $dataFilter[$i];
                                            }
                                        }elseif($i == $otherReInd){
                                            $otherReInd = $otherReInd + $firstReInd;
                                            $otherExlop = explode(' ', $dataFilter[$i]);
                                            if(count($otherExlop) > 0){
                                                $lastIn = end($otherExlop);
                                                array_pop($otherExlop);
                                                $newArray[] = implode(' ',$otherExlop);
                                                $newArray[] = $lastIn;
                                            }else{
                                                $newArray[] = $dataFilter[$i];
                                            }
                                        }else{
                                            $newArray[] = $dataFilter[$i];
                                        }
                                    }

                                    $dataFilter = array_filter($newArray);
                                }
                                $fidata = $dataFilter;
                            }else{
                                $fidata = $dataFilter;
                            }
                            $datasi = preg_replace('/-+/', '', $fidata);
                            $datarspace = array_map('rtrim', $datasi);
                            $data[] = preg_replace('/\s\s+/', 'x', $datarspace);
                            
                        }
                    }
                }
                $data = array_filter($data); // remove empty array
                // return $data;
                $result = [];
                foreach ($data as $key => $value) {
                    $arrayDivision = count($value)/4;
                    $arraySeperate = array_chunk($value, $arrayDivision);
                    $arrayCombine = array_combine($arraySeperate[1], $arraySeperate[2]);
                    $result = array_replace_recursive($result, $arrayCombine);
                }
                // return $result;
                $result['value'] = $result;

            }else{
                $data = $input['pdf_data'];
                $checkSpace = str_replace(' ', 'x', $data);
                $checkNewLine = trim(preg_replace('/\s\s+/', '-', $checkSpace));
                $checkCharacterRemove = preg_replace('~-{2,}~', '-', $checkNewLine);
                $checkCharacterRemove = str_replace(array("\n", "\r"), '-', $checkCharacterRemove);

                $textExplode = array_filter(explode('-', $checkCharacterRemove));
                $arrayDivision = count($textExplode)/2;
                $arraySeperate = array_chunk($textExplode, $arrayDivision);
                $arrayCombine = array_combine($arraySeperate[0], $arraySeperate[1]);
                $result['value'] = $arrayCombine;
            }
            
            $result['type'] = 'success';
            return $result;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    public function replaceVal($countValue, $inc, $num)
    {
        if($countValue == $inc){
            return $num;
        }else{
            $inc = $inc + 4;
            $num = $num + 1;
            return $this->replaceVal($countValue, $inc, $num);
        }
    }

    public function sizeBreakdown(Request $request)
    {
        $input = $request->all();
        try {
            $getPoList = PurchaseOrder::getPOListOrderIdWise($input['order_id']);
            $order = OrderEntry::orderInfoWithStyle($input['order_id']);
            $pagesize = 'size-breakdown';
            $sizeGroup= StyleSizeGroup::getSizeGroupIdStyleWise($order->style->stl_id);
            // $getSizeGroup = [];
            // if($sizeGroup != null){
            //     $getSizeGroup= ProductSize::getProductSizeGroupIdWiseInfo($sizeGroup);
            // }
            // $getSizeGroup = array_chunk($getSizeGroup->toArray(), 5);
            $getPoId = collect($getPoList)->pluck('po_id');
            // PO id wise size qty
            // $getPOSize = DB::table('mr_po_size_qty')
            // ->whereIn('po_id', $getPoId)
            // ->get()
            // ->groupBy('po_id', true)
            // ->map(function($q) {
            //     return collect($q)->pluck('qty','mr_product_size_id');
            // });
            $getPoSizeQty = DB::table('mr_po_size_qty')
            ->whereIn('po_id', $getPoId)
            ->get();
            $getPOSize = collect($getPoSizeQty)->groupBy('po_id', true)->map(function($q){
                return collect($q)->pluck('qty','mr_product_size_id');
            });
            $uniqueSizeQty = collect($getPoSizeQty)->groupBy('mr_product_size_id', true)->map(function($q){
                return collect($q)->sum('qty');
            });
            return view('merch.po.order_wise_list', compact('getPoList', 'order', 'pagesize', 'getPOSize', 'uniqueSizeQty'));
        } catch (\Exception $e) {

            $data['type'] = 'error';
            $data['message'] = $e->getMessage();
            return 'error';
        }
    }
}
