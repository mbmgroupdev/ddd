<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Commercial\CmImpInvoice;
use App\Models\Commercial\CmPiBom;
use App\Models\Commercial\CmPiMaster;
use App\Models\Inventory\StInspectionDetailsFabric;
use App\Models\Inventory\StInspectionMaster;
use App\Models\Inventory\StInspectionPointDetailsFabric;
use App\Models\Inventory\StRawMaterialItemReceive;
use App\Models\Inventory\StRawMaterialReceive;
use App\Models\Merch\MainCategory;
use App\Models\Merch\McatItem;
use DB, ACL;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Response;

class RmReceiveController extends Controller
{
    public function index()
    {
        try {
            $invoice_list = CmImpInvoice::all();
            return view('inventory.sections.rm_receive',compact('invoice_list'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function rmReceiveList()
    {
        try {
            $grnList        = DB::table('st_raw_material_receive')
                             //->select('id','grn_no','cm_imp_invoice_id')
                             ->pluck('grn_no');
            //dd($grnList);
            $invoiceList    = DB::table('cm_imp_invoice')
                                //->select('id','invoice_no')
                                ->pluck('invoice_no');
            $itemList       = DB::table('mr_cat_item')
                              //->select('id','item_name')
                              ->pluck('item_name');
            $supplierList   = DB::table('mr_supplier')
                            ->pluck('sup_name');

            $employeeTypes = [];
            $unitList = [];
            $floorList = [];
            $lineList = [];
            $departmentList = [];
            return view('inventory.sections.rm_receive_list',compact('grnList','invoiceList','itemList','supplierList','employeeTypes','unitList','floorList','lineList','departmentList'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    // public function invoiceNo(Request $request)
    // {
    //     $invoice_no=DB::table('cm_imp_invoice')
    //                 ->select('id','invoice_no')
    //                 ->where('id',$request->invoice_id)
    //                 ->first();
    //     return Response::json($invoice_no);

    // }

    // public function getItem(Request $request)
    // {
    //     $item = DB::table('st_raw_material_receive as a')
    //             ->select('c.item_name','b.id')
    //             ->leftJoin('st_raw_material_item_receive as b','b.st_raw_material_receive_id','=','a.id')
    //             ->leftJoin('mr_cat_item as c','c.id','=','b.mr_cat_item_id')
    //             ->where('a.cm_imp_invoice_id',$request->invoice_no)
    //             ->get();
    //     return Response::json($item);
    // }

    // public function getDateSupplier(Request $request)
    // {
    //     $data= DB::table('st_raw_material_item_receive as a')
    //             ->select('a.receive_date','c.sup_name','c.sup_id')
    //             ->leftJoin('cm_pi_master as b','b.id','=','a.cm_pi_master_id')
    //             ->leftJoin('mr_supplier as c','c.sup_id','=','mr_supplier_sup_id')
    //             ->where('a.id',$request->raw_mat_item_id)
    //             ->get();
    //     return Response::json($data);
    // }

    public function stInspectionMaster_data($st_raw_material_receive_id, $st_raw_material_item_receive_id, $mr_cat_item_id='')
    {
        $where = [
            'st_raw_material_receive_id' => $st_raw_material_receive_id,
            'st_raw_material_item_receive_id' => $st_raw_material_item_receive_id
        ];
        if(!empty($mr_cat_item_id)) {
            $where['mr_cat_item_id'] = $mr_cat_item_id;
        }
        $query = StInspectionMaster::where($where);
        if(!empty($mr_cat_item_id)) {
            return $query->first();
        } else {
            return $query->get()->toArray();
        }
    }

    public function inspectionFabricSingle($grnNo, $itemId)
    {
        try{
            $stMaterial = StRawMaterialReceive::where('grn_no',$grnNo)->first();
            if(isset($stMaterial->id)) {
            $stMaterialItem = StRawMaterialItemReceive::where(['st_raw_material_receive_id' => $stMaterial->id, 'mr_cat_item_id' => $itemId])->first();
                if(isset($stMaterialItem->id)) {
                    $stInspectionMaster = $this->stInspectionMaster_data($stMaterial->id, $stMaterialItem->id);
                    if(empty($stInspectionMaster)) {
                        $stInspectionMaster = [];
                    } else {
                        $stInspectionMaster = $stInspectionMaster[0];
                    }
                    return view('inventory.sections.inspection_fabric_single', compact('stMaterial', 'stMaterialItem','stInspectionMaster'));
                } else {
                    return redirect()->back()->with('error','No item found');
                }
            } else {
                return redirect()->back()->with('error','No data found');
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function inspectionFabricSingleSave(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->st_inspection_details_fabric);
            $st_inspection_master = $request->st_inspection_master;
            $st_inspection_master['created_by'] = auth()->user()->id;
            $st_inspection_master_id = StInspectionMaster::insertGetId($st_inspection_master);
            // $st_inspection_master_id = 1;
            $st_d_f     = $request->st_inspection_details_fabric;
            $st_i_d_f   = $request->st_inspection_point_details_fabric;
            $st_inspection_d_f = [];
            foreach($request->st_inspection_details_fabric['roll_no'] as $k=>$each) {
                $st_inspection_d_f['status']             = isset($st_d_f['status'][$k])?1:0;
                $st_inspection_d_f['roll_no']            = $st_d_f['roll_no'][$k];
                $st_inspection_d_f['y_m']                = $st_d_f['y_m'][$k];
                $st_inspection_d_f['shrinkage_group']    = $st_d_f['shrinkage_group'][$k];
                $st_inspection_d_f['shade']              = $st_d_f['shade'][$k];
                $st_inspection_d_f['width_head']         = $st_d_f['width_head'][$k];
                $st_inspection_d_f['width_med']          = $st_d_f['width_med'][$k];
                $st_inspection_d_f['width_end']          = $st_d_f['width_end'][$k];
                $st_inspection_d_f['actual_yardage']     = $st_d_f['actual_yardage'][$k];
                $st_inspection_d_f['total_points']       = $st_d_f['total_points'][$k];
                $st_inspection_d_f['remarks']            = $st_d_f['remarks'][$k];
                $st_inspection_d_f['created_by']         = auth()->user()->id;
                $st_inspection_d_f['avg_point_per_100sq']     = $st_d_f['avg_point_per_100sq'][$k];
                $st_inspection_d_f['st_inspection_master_id'] = $st_inspection_master_id;
                $st_inspection_details_fabric_id = StInspectionDetailsFabric::insertGetId($st_inspection_d_f);
                // $st_inspection_details_fabric_id = $k;
                $st_inspection_point_d_f = [];
                foreach(range(0,3) as $k1=>$each1) {
                    $st_inspection_point_d_f['defect_point']   = $st_i_d_f['defect_point'][$k][$k1];
                    $st_inspection_point_d_f['broken_end']     = $st_i_d_f['broken_end'][$k][$k1];
                    $st_inspection_point_d_f['broken_pick']    = $st_i_d_f['broken_pick'][$k][$k1];
                    $st_inspection_point_d_f['coarse_end']     = $st_i_d_f['coarse_end'][$k][$k1];
                    $st_inspection_point_d_f['coarse_pick']    = $st_i_d_f['coarse_pick'][$k][$k1];
                    $st_inspection_point_d_f['color_yarn']     = $st_i_d_f['color_yarn'][$k][$k1];
                    $st_inspection_point_d_f['hang_thread']    = $st_i_d_f['hang_thread'][$k][$k1];
                    $st_inspection_point_d_f['knot']           = $st_i_d_f['knot'][$k][$k1];
                    $st_inspection_point_d_f['reed_mark']      = $st_i_d_f['reed_mark'][$k][$k1];
                    $st_inspection_point_d_f['stop_mark']      = $st_i_d_f['stop_mark'][$k][$k1];
                    $st_inspection_point_d_f['burl_mark']      = $st_i_d_f['burl_mark'][$k][$k1];
                    $st_inspection_point_d_f['crease_mark']    = $st_i_d_f['crease_mark'][$k][$k1];
                    $st_inspection_point_d_f['hole']           = $st_i_d_f['hole'][$k][$k1];
                    $st_inspection_point_d_f['stain']          = $st_i_d_f['stain'][$k][$k1];
                    $st_inspection_point_d_f['splice']         = $st_i_d_f['splice'][$k][$k1];
                    $st_inspection_point_d_f['uneven_dyed']    = $st_i_d_f['uneven_dyed'][$k][$k1];
                    $st_inspection_point_d_f['bow']            = $st_i_d_f['bow'][$k][$k1];
                    $st_inspection_point_d_f['color_out']      = $st_i_d_f['color_out'][$k][$k1];
                    $st_inspection_point_d_f['uneven_print']   = $st_i_d_f['uneven_print'][$k][$k1];
                    $st_inspection_point_d_f['other_defect']   = $st_i_d_f['other_defect'][$k][$k1];
                    $st_inspection_point_d_f['st_inspection_details_fabric_id'] = $st_inspection_details_fabric_id;
                    StInspectionPointDetailsFabric::insert($st_inspection_point_d_f);
                }
            }
            DB::commit();
            return redirect('inventory/rm_receive_list')->with('success','Data Insert Success.');
        } catch(\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function ajaxInspectionFabricSingleTable(Request $request)
    {
        $rowid = $request->rowid;
        $stInspectionMaster = $request->stInspectionMaster;
        return view('inventory.sections.ajax_inspection_fabric_table', compact('rowid','stInspectionMaster'))->render();
    }

    public function fabricInspectionList()
    {
        try {
            return view('inventory.sections.fabric_inspection_list');
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function rmReceiveListData(Request $request)
    {
        try {
            $data = StRawMaterialItemReceive::get_rm_receive_items();
            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    $itemInfo = McatItem::where('id',$data->mr_cat_item_id)->first();
                    if(isset($itemInfo->id)){
                        if($itemInfo->mr_material_category->mcat_id == 1) { // Fabric
                            return '<a href="'.url('inventory/inspection_fabric/'.$data->grn_no.'/'.$data->mr_cat_item_id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Inspection</a>';
                        } else if(in_array($itemInfo->mr_material_category->mcat_id, [2,3]) !== FALSE){ // Accessories
                            return '<a href="'.url('inventory/accessories_inspection/'.$data->grn_no.'/'.$data->mr_cat_item_id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Inspection</a>';
                        } else {
                            // no type found
                        }
                    } else {
                        // no data found
                    }
                })
                ->editColumn('status', function($data) {
                    return '<span class="label label-warning">Pending</span>';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function fabricInspectionListData(Request $request)
    {
        try {
            $data = StRawMaterialItemReceive::get_rm_receive_fabric_items();
            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    if($data->totalStatus === 1) {
                        $inventoryButton = '<a href="'.url('inventory/inventory_item_entry/'.$data->grn_no.'/'.$data->item_id).'" class="btn btn-xs btn-success"> Inventory Entry</a>';
                    } else {
                        $inventoryButton = '<a href="'.url('inventory/returns/'.$data->grn_no.'/'.$data->cm_pi_master_id.'/'.$data->item_id).'" class="btn btn-xs btn-warning"> Return</a>';
                    }
                    $editButton = '<a href="'.url('inventory/inspection_fabric/'.$data->grn_no.'/'.$data->item_id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                    $deleteButton = '<a href="'.url('inventory/delete_inspection_fabric/'.$data->imaster_id.'/'.$data->item_id).'" class="btn btn-xs btn-warning" onclick="return confirm(`Are you sure you want to delete?`)"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                    return $inventoryButton.' '.$editButton.' '.$deleteButton;
                })
                ->editColumn('status', function($data) {
                    if($data->totalStatus === 1) {
                        $status = '<span class="label label-success">Pass</span>';
                    } else {
                        $status = '<span class="label label-warning">Fail</span>';
                    }
                    return $status;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function fabricInspectionDelete($inspectionMasterId, $itemId)
    {
        try {
            $where = [
                'id' => $inspectionMasterId,
                'mr_cat_item_id' => $itemId
            ];
            $inspectionMaster = StInspectionMaster::where($where)->first();
            if(isset($inspectionMaster->id)) {
                StInspectionMaster::where($where)->delete();
                return redirect()->back()->with('success','Successfully deleted.');
            } else {
                return redirect()->back()->with('error','Data not found.');
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function rmReceiveSave(Request $request)
    {
        try {
            if(!empty($request->cm_pi_master_id)) {
                // check invoice exist
                $stMat = StRawMaterialReceive::where('cm_imp_invoice_id',(int)$request->invoice_no)->first();
                if(isset($stMat->id)) {
                    $stMatItem = StRawMaterialItemReceive::where('st_raw_material_receive_id',$stMat->id)->get()->toArray();
                    if(!empty($stMatItem)) {
                        return redirect()->back()->with('error','Invoice already exist.');
                    }
                }
                // dd($request->all(),'Receiver ID: '.auth()->user()->id, 'GRN NO: '.strtotime(now()));
                $stReceive['receiver_id']       = auth()->user()->id;
                $stReceive['grn_no']            = strtotime(now()); // auto generate number
                $stReceive['cm_imp_invoice_id'] = (int)$request->invoice_no;
                // dd($stReceive);
                $stReceiveId = StRawMaterialReceive::insertGetId($stReceive);
                foreach($request->cm_pi_master_id as $k=>$each_pi_id) {
                    foreach($request->mr_cat_item_id[$each_pi_id] as $item_id=>$each_item_id) {
                        $stReceiveItem['st_raw_material_receive_id'] = $stReceiveId;
                        $stReceiveItem['mr_cat_item_id']    = $each_item_id;
                        $stReceiveItem['cm_pi_master_id']   = $each_pi_id;
                        $stReceiveItem['receive_qty']   = $request->receive_qty[$each_pi_id][$each_item_id];
                        $stReceiveItem['receive_date']  = $request->receive_date[$each_pi_id][$each_item_id];
                        $stReceiveItem['uom']           = $request->uom[$each_pi_id][$each_item_id];
                        StRawMaterialItemReceive::insert($stReceiveItem);
                    }
                }
                // dd($stReceiveItem);
                return redirect()->back()->with('success','Data Insert Success.');
            } else {
                return 'PI not found.';
            }
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }
    // public function getBomItem($where){
    //         $query= DB::table('cm_invoice_pi_bom')
    //                 ->select(
    //                     DB::raw('sum(shipped_qty) AS shipped_qty'),
    //                     'cm_pi_bom_id as cipb_id'
    //                 );
    //         $shippedData = $query->groupBy('cm_pi_bom_id');
    //         $shippedData_sql = $shippedData->toSql();
    //
    //         $query1 = DB::table('mr_order_booking As mob')
    //                 ->select(
    //                   "mc.clr_name",
    //                   "mob.*",
    //                   "c.mcat_name",
    //                   "c.mcat_id",
    //                   "sz.mr_product_pallete_name",
    //                   "i.item_name",
    //                   "i.item_code",
    //                   "i.id as item_id",
    //                   "i.dependent_on",
    //                   "s.sup_name",
    //                   "a.art_name",
    //                   "com.comp_name",
    //                   "con.construction_name",
    //                   "b.item_description",
    //                   "b.uom",
    //                   "b.consumption",
    //                   "b.extra_percent",
    //                   "b.precost_unit_price",
    //                   "b.order_id",
    //                   "o.order_code",
    //                   "cpb.pi_qty",
    //                   "cpb.currency",
    //                   "cipb.shipped_qty"
    //                 )
    //                 ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
    //                 ->leftJoin("mr_material_category AS c", function($join) {
    //                   $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
    //                 })
    //                 ->leftJoin("mr_cat_item AS i", function($join) {
    //                     $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
    //                     $join->on("i.id", "=", "b.mr_cat_item_id");
    //                 })
    //                 ->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
    //                 ->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
    //                 ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
    //                 ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
    //                 ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
    //                 ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
    //                 ->leftJoin("mr_order_entry AS o","o.order_id","b.order_id")
    //                 ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
    //                 ->where(function($condition) use ($where){
    //                         if (!empty($where['order_id'])) {
    //                             $condition->where('o.order_id', $where['order_id']);
    //                         }
    //                         if (!empty($where['pi_id'])) {
    //                             $condition->where('cpb.cm_pi_master_id', $where['pi_id']);
    //                         }
    //                         if (!empty($where['pi_group'])) {
    //                             $condition->whereIn('cpb.cm_pi_master_id', $where['pi_group']);
    //                         }
    //                 })
    //                 ->orderBy("mob.id");
    //         $query1->leftJoin(DB::raw('(' . $shippedData_sql. ') AS cipb'), function($join) use ($shippedData) {
    //             $join->on('cipb.cipb_id', '=', 'cpb.id')->addBinding($shippedData->getBindings());
    //         });
    //         $booking = $query1->get();
    //         $bookColl = collect($booking)->groupBy('mr_order_bom_costing_booking_id', true);
    //         $bom = collect($bookColl)->groupBy('mr_cat_item_mcat_id', true);
    //
    //         return view("commercial.shipment.get_order_bom",compact('bom'))->render();
    //     }
    public function piData(Request $request)
    {
        try{
            $data_list = [];
            $data_list = CmImpInvoice::find($request->dataEntryId);
            // return $data_list->invoice_supplier->supplier->sup_name;
            // echo '<pre>';
            // print_r($data_list);
            // echo '</pre>';
            // exit;
            // dd($data_list);
            $importData= DB::table('cm_imp_data_entry AS de')
                            ->where('de.id', $request->dataEntryId)
                            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'de.hr_unit')
                            ->select([
                                'de.*',
                                'u.hr_unit_name'
                            ])
                            ->first();
            $invoiceInfo= DB::table('cm_imp_invoice')
                            ->where('cm_imp_data_entry_id', $request->dataEntryId)
                            ->first();

            //get Selected PI List
            $cm_pi=DB::table('cm_invoice_pi_bom as cipb')
                                    ->select(
                                        'cpm.id',
                                        'cpm.pi_no',
                                        'cpm.pi_date',
                                        DB::raw('sum(cipb.shipped_qty) as shipped_qty')
                                        )
                                    ->where('cipb.cm_imp_invoice_id', $invoiceInfo->id)
                                    ->Join('cm_pi_master as cpm', 'cpm.id', 'cipb.cm_pi_master_id')
                                    ->groupBy('cipb.cm_pi_master_id')
                                    ->get();

                //dd($cm_pi);

            $piBomList= $this->getPIBomListEdit($importData->cm_btb_id, $invoiceInfo->id);

            //dd($piBomList);
            return view('inventory.rmreceivepush', compact('piBomList','data_list','cm_pi'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function getPIBomListEdit($ilc_no,$invoice_id){
        $categories = DB::table('mr_order_booking As mob')
                        ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                        ->Join("cm_invoice_pi_bom as cipb", "cipb.cm_pi_bom_id" ,"cpb.id")
                        ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                        ->leftJoin("mr_cat_item AS i", function($join) {
                            $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                            $join->on("i.id", "=", "b.mr_cat_item_id");
                        })
                        ->LeftJoin('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                        ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                        ->where('btb.id', $ilc_no)
                        ->where('cipb.cm_imp_invoice_id', $invoice_id)
                        ->groupBy('b.mr_material_category_mcat_id')
                        ->pluck('b.mr_material_category_mcat_id');

        $bomList = '';
        $bom = [];
        foreach ($categories as $key => $category) {
            $items = $this->getCatItemsConsClr(['btb.id'=>$ilc_no,'b.mr_material_category_mcat_id'=>$category],'b.mr_cat_item_id');
            foreach ($items as $key => $item) {
                $constructions = $this->getCatItemsConsClr(['btb.id'=>$ilc_no,'b.mr_material_category_mcat_id'=>$category,'b.mr_cat_item_id'=>$item],'b.mr_construction_id');
                foreach ($constructions as $key => $con) {
                    $depends = $this->getCatItemsConsClr(['btb.id'=>$ilc_no,'b.mr_material_category_mcat_id'=>$category,'b.mr_cat_item_id'=>$item,'b.mr_construction_id'=>$con],'b.depends_on');
                    $bomList .= $this->getCatItemsConsClrWiseBomEdit(['btb.id'=>$ilc_no,'b.mr_material_category_mcat_id'=>$category,'b.mr_cat_item_id'=>$item,'b.mr_construction_id'=>$con,'cipb.cm_imp_invoice_id'=>$invoice_id],$ilc_no,$invoice_id);
                    //$bom[$category][$item][$con]= 'hi';

                }
            }
        }

        return $bomList;
    }
    public function getCatItemsConsClrWiseBomEdit($where,$btb,$invoice_id){
        $booking = DB::table('mr_order_booking As mob')
                        ->select(
                          "mc.clr_name",
                          DB::raw('SUM(mob.booking_qty) AS booking_qty'),
                          "mob.size",
                          "sz.mr_product_pallete_name",
                          "mob.id",
                          "c.mcat_name",
                          "c.mcat_id",
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
                          "cpb.currency",
                          DB::raw('SUM(cpb.pi_qty) AS pi_qty'),
                          "cpb.shipped_date",
                          "cpb.mr_po_booking_id",
                          "cpb.id AS cm_pi_bom_id",
                          "mob.mr_order_bom_costing_booking_id",
                          "mob.mr_cat_item_id",
                          "b.order_id",
                          "b.depends_on",
                          "b.mr_construction_id",
                          "mc.clr_id",
                          "mob.size",
                          "cipb.shipped_qty",
                          "cipb.id as cipb_id"
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
                        ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
                        ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
                        ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
                        ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
                        ->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
                        ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                        ->Join("cm_invoice_pi_bom as cipb", "cipb.cm_pi_bom_id" ,"cpb.id")
                        ->Join('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                        ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                        ->where($where)
                        ->groupBy("mob.mr_material_color_id","mob.size")
                        ->orderBy("mob.id")
                        ->get();

        return view("commercial/import/import_data/pi_bom_edit_rm", compact('booking','btb','invoice_id'))->render();

    }
    public function getCatItemsConsClr($where, $groupBy){
        $categories = DB::table('mr_order_booking As mob')
                        ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                        ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                        ->leftJoin("mr_cat_item AS i", function($join) {
                            $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                            $join->on("i.id", "=", "b.mr_cat_item_id");
                        })
                        ->LeftJoin('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                        ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                        ->where($where)
                        ->groupBy($groupBy)
                        ->pluck($groupBy);
        return $categories;

    }
    // for testing perpus
    public function testTest()
    {
        dd(StRawMaterialItemReceive::get_rm_receive_fabric_items());
    }
}
