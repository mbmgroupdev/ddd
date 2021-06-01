<?php
namespace App\Http\Controllers\Commercial\Import\ImportData;

use App\Models\Commercial\CmFile;
use App\Models\Commercial\CmPiBom;
use App\Models\Commercial\CmPiMaster;
use App\Models\Commercial\CmInvoicePiBom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Merch\ShortCodeLib as ShortCodeLib;
use App\Models\Merch\Supplier;
use App\Models\Commercial\Port;
use App\Models\Commercial\Item;
use App\Models\Commercial\CommBank;
use App\Models\Commercial\ImportDataEntry;
use App\Models\Commercial\Vessel;
use App\Models\Commercial\VesselVoyage;
use App\Models\Commercial\ImportInvoice;
use App\Models\Commercial\fabPocket;
use App\Models\Commercial\ImportInvFabric;
use App\Models\Commercial\ImportDataHistory;
use App\Models\Hr\Unit;
use App\Models\Merch\Country;
use App\Http\Controllers\Commercial\Import\PI\PiMasterFabricController as PiMasterFabricController;

use Validator, DB, ACL, Auth, DataTables;

class ImportDataController extends Controller
{
    //Show Entry Form
    public function showForm(){
        $bankList= CommBank::pluck('bank_name', 'id');
        $countryList= Country::pluck('cnt_name', 'cnt_id');
        $portList= Port::pluck('port_name', 'id');
        $vesselList= Vessel::pluck('vessel_name', 'id');
        $fileList= DB::table('cm_pi_master AS cpm')
                        ->distinct('cpm.cm_file_id')
                        ->leftJoin('cm_file AS cf', 'cf.id', 'cpm.cm_file_id')
                        ->orderBy('cf.id', 'desc')
                        ->pluck('cf.file_no', 'cf.id');
        $new_file_list= [];
        foreach ($fileList as $key => $value) {
            if($key && $value){
                $new_file_list[$key]= $value;
            }
        }
        $fileList= $new_file_list;

        $importCode= $this->autoCode();

        return view('commercial/import/import_data/import_data_entry', compact('bankList', 'countryList', 'portList', 'vesselList', 'fileList', 'importCode'));
    }

    //generate improt data auto code
    public function autoCode(){
        $length=8;
        $randstr = "";
        srand((double)microtime() * 1000000);
        //our array add all letters and numbers if you wish
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        for ($rand = 1; $rand <= $length; $rand++) {
            $random = rand(0, count($chars) - 1);
            $randstr .= $chars[$random];
        }
        return ($randstr.'/'.date('y'));
    }

    //get voyage by Vessel
    public function getVoyageByVessel(Request $request){

        $voyages= DB::table('cm_voyage_vessel')->where("cm_vessel_id", $request->mother_vessel)
                    ->pluck('voyage_name', 'id');
        $voyageList='<option> Select Voyage </option>';
        foreach ($voyages as $key => $value) {
            $voyageList.='<option value"'.$key.'"> '. $value .' </option>';
        }
        return $voyageList;
    }

    //get supplier list and unit name by file
    public function getUnitName(Request $request){

        $unit= DB::table('cm_file AS f')
                        ->where('id', $request->file_no)
                        ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'f.hr_unit')
                        ->select(['hr_unit_name', 'hr_unit_id'])
                        ->first();
        $data['name']= $unit->hr_unit_name;
        $data['id']= $unit->hr_unit_id;
        return $data;
    }

    public function getSupplierList(Request $request){

        $suppliers = DB::table('cm_btb')
                    ->where('cm_file_id', $request->file_id)
                    ->leftJoin('mr_supplier','mr_supplier.sup_id','cm_btb.mr_supplier_sup_id')
                    ->pluck('sup_name','sup_id');

        $supList= "<option>Select Supplier</option>";
        foreach ($suppliers as $key => $value) {
            $supList.='<option value="'.$key.'"> '. $value .' </option>';
        }
        return response()->json($supList);
    }

    //get ilc list by supplier
    public function getIlcList(Request $request){

        $ilcList= "<option>Select ILC</option>";
        $ilcs= $this->ilcList($request->file_no, $request->supplier_no);
        foreach ($ilcs as $key => $value) {
            $ilcList.='<option value="'.$key.'"> '. $value .' </option>';
        }
        return $ilcList;
    }

    public static function ilcList($file_no, $supplier_no){

        $ilcs= DB::table('cm_btb')
                    ->where('cm_file_id', $file_no)
                    ->where('mr_supplier_sup_id', $supplier_no)
                    ->pluck('lc_no', 'id');

        return $ilcs;
    }

    //get PI
    public function getPIList(Request $request){
        $cm_pi = DB::table('cm_pi_master AS cpm')
                    ->select('cpm.*')
                    ->Join('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpm.id')
                    ->leftJoin('cm_btb AS b', 'b.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                    ->where('b.id', $request->ilc_no)
                    ->get();
        //$invoiceId= $request->invoice_id;

        return view('commercial/import/import_data/pi_no',compact('cm_pi'));
    } 

    //get selected pi date
    /*public function getPIDate(Request $request){
        $pi_date = DB::table('cm_pi_master')
                    ->where('id',$request->pi_no)
                    ->pluck('pi_date')
                    ->first();
        return $pi_date;
    }*/

    //get PI BOM List
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

    public function getCatItemsConsClrWiseBom($where,$btb){
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
                          "mob.size"/*,
                          "ip.unit_price"*/
                        )
                        ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                        ->leftJoin("mr_material_category AS c", function($join) {
                          $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
                        })
                        ->leftJoin("mr_cat_item AS i", function($join) {
                            $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                            $join->on("i.id", "=", "b.mr_cat_item_id");
                        })
                        /*->leftJoin("mr_pi_item_unit_price AS ip", function($join) {
                            $join->on("ip.cm_pi_master_id", "=", "cpb.cm_pi_master_id");
                            $join->on("ip.mr_po_booking_id", "=", "cpb.mr_po_booking_id");
                            $join->on("ip.mr_cat_item_id", "=", "b.mr_cat_item_id");

                        })*/
                        ->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
                        ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
                        ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
                        ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
                        ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
                        ->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
                        ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                        ->Join('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                        ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                        ->where($where)
                        ->groupBy("mob.mr_material_color_id","mob.size")
                        ->orderBy("mob.id")
                        ->get();

        return view("commercial/import/import_data/pi_bom", compact('booking','btb'))->render();

    }

    public function getPIBomList(Request $request){
        $categories = DB::table('mr_order_booking As mob')
                        ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                        ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                        ->leftJoin("mr_cat_item AS i", function($join) {
                            $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                            $join->on("i.id", "=", "b.mr_cat_item_id");
                        })
                        ->LeftJoin('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                        ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                        ->where('btb.id', $request->ilc_no)
                        ->groupBy('b.mr_material_category_mcat_id')
                        ->pluck('b.mr_material_category_mcat_id');

        $bomList = '';
        $boms = [];
        foreach ($categories as $key => $category) {
            $items = $this->getCatItemsConsClr(['btb.id'=>$request->ilc_no,'b.mr_material_category_mcat_id'=>$category],'b.mr_cat_item_id');
            foreach ($items as $key => $item) {
                $constructions = $this->getCatItemsConsClr(['btb.id'=>$request->ilc_no,'b.mr_material_category_mcat_id'=>$category,'b.mr_cat_item_id'=>$item],'b.mr_construction_id');
                foreach ($constructions as $key => $con) {
                    $depends = $this->getCatItemsConsClr(['btb.id'=>$request->ilc_no,'b.mr_material_category_mcat_id'=>$category,'b.mr_cat_item_id'=>$item,'b.mr_construction_id'=>$con],'b.depends_on');
                    $bomList .= $this->getCatItemsConsClrWiseBom(['btb.id'=>$request->ilc_no,'b.mr_material_category_mcat_id'=>$category,'b.mr_cat_item_id'=>$item,'b.mr_construction_id'=>$con],$request->ilc_no);
                    //$bom[$category][$item][$con]['dep'] = $depend;
                    //$bomList .= '<tr><td>'.$category.'/'.$item.'/'.$con.'<td><tr>';
                    
                }
            }
            
        }
        //dd($bom);
        

        return $bomList;
    }

    public function checkPiQtyByLc($ilc_no){
        $cm_pi = DB::table('cm_pi_master AS cpm')
                    ->select('cpm.*')
                    ->Join('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpm.id')
                    ->leftJoin('cm_btb AS b', 'b.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                    ->where('b.id', $ilc_no)
                    ->get();
        $total_rem = 0;
        foreach ($cm_pi as $key => $pi) {
            $master = DB::table('cm_pi_bom')->where('cm_pi_master_id', $pi->id)->sum('pi_qty');
            $invoice = DB::table('cm_invoice_pi_bom')->where('cm_pi_master_id',$pi->id)->sum('shipped_qty');
            
            $total_rem +=($master-$invoice);
        }

        return $total_rem;

    }

    //store form data
    public function storeForm(Request $request){
        //dd($request->all());
        if ($request->isMethod('post'))
        { 
           DB::beginTransaction();
            try {

                $data = $request->all();
                $remaining = $this->checkPiQtyByLc($data['ilc_no']);
                
                if($data['quantity'] <= $remaining){
                    $newimp= new ImportDataEntry();

                    $newimp->imp_code = $data['importcode'];
                    $newimp->cm_file_id = $data['file_no'];
                    $newimp->mr_supplier_sup_id = $data['supplier_no'];
                    $newimp->cm_btb_id = $data['ilc_no'];
                    $newimp->hr_unit = $data['unit_id'];
                    $newimp->cm_bank_id = $data['bank'];
                    $newimp->imp_lc_type = $data['impdatatype'];
                    $newimp->transp_doc_no1 = $data['tr_doc1'];
                    $newimp->transp_doc_date = $data['tr_doc_date'];
                    $newimp->transp_doc_no2  = $data['tr_doc2'];
                    $newimp->ship_mode  = $data['ship'];
                    $newimp->weight   = $data['weight'];
                    $newimp->cnt_id  = $data['country'];
                    $newimp->carrier = $data['carrier'];
                    $newimp->freight = $data['freight'];
                    $newimp->ship_company = $data['ship_com'];
                    $newimp->container_1   = $data['container1'];
                    $newimp->container_2  = $data['container2'];
                    $newimp->container_3   = $data['container3'];
                    $newimp->package     = $data['package'];
                    $newimp->doc_type    = $data['doc_type'];
                    $newimp->doc_recv_date = $data['docdate'];
                    $newimp->qty           = $data['quantity'];
                    $newimp->value         = $data['value'];
                    $newimp->value_currency = $data['currency'];
                    $newimp->cm_port_id     = $data['port_loading'];
                    $newimp->container_size = $data['container_size'];
                    $newimp->cm_vessel_id   = $data['mother_vessel'];
                    $newimp->cm_voyage_vessel_id = $data['voyage_no'];
                    $newimp->cubic_measurement = $data['cubic_measurement'];
                    $newimp->save();


                    
                    $new_import = [];
                    $input = $request->all();
                    $last_id = $newimp->id;

                    $invoice['cm_imp_data_entry_id']= $last_id;
                    $invoice['invoice_no']= $request->invoice_no;
                    $invoice['invoice_date']= $request->invoice_date;

                    $last_invoice = DB::table('cm_imp_invoice')->insertGetId($invoice);

                    //Store Invoice Data Import Invoice Table
                    if(isset($request->shipped) && sizeof($request->shipped)>0){

                        foreach ($request->shipped as $pi_key => $pi_value) {

                            if(count($pi_value)>0){

                                foreach ($pi_value as $bom_key => $bom_value) {
                                    //if($bom_value['shipped_qty']>0){
                                        $pi['cm_imp_invoice_id'] = $last_invoice;
                                        $pi['cm_pi_master_id'] = $pi_key;
                                        $pi['cm_pi_bom_id'] = $bom_key;
                                        $pi['shipped_qty'] = $bom_value['shipped_qty'];
                                        DB::table('cm_invoice_pi_bom')->insert($pi);
                                    //}
                                }
                            }
                        }
                    }

                    // $this->logFileWrite("Commercial-> Import Data Entry Saved", $last_id );
                   DB::commit();
                    return back()->with('success', "Import Data Information Successfully Added!!!");

                }else{
                    return back()->with('error', 'Remaining quantity is less!');    
                }
                
            } catch (\Exception $e) {
               DB::rollback();
              return back()->with('error', $e->getMessage());
            }
        }
    }

    #----Imort Data List----------/
    public function showList(){

        $bank  = CommBank::pluck('bank_name','id');
        $supplier = Supplier::pluck('sup_name','sup_id');
        $unit = Unit::pluck('hr_unit_name','hr_unit_id');

        return view('commercial/import/import_data/import_data_list', compact('supplier','unit','bank'));
    }

    public function editForm($id){
        $importData= DB::table('cm_imp_data_entry AS de')
                        ->where('de.id', $id)
                        ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'de.hr_unit')
                        ->select([
                            'de.*',
                            'u.hr_unit_name'
                        ])
                        ->first();
        // dd($importData);
        $bankList= CommBank::pluck('bank_name', 'id');
        $countryList= Country::pluck('cnt_name', 'cnt_id');
        $portList= Port::pluck('port_name', 'id');
        $vesselList= Vessel::pluck('vessel_name', 'id');
        $fileList= DB::table('cm_pi_master AS cpm')
                        ->distinct('cpm.cm_file_id')
                        ->leftJoin('cm_file AS cf', 'cf.id', 'cpm.cm_file_id')
                        ->orderBy('cf.id', 'desc')
                        ->pluck('cf.file_no', 'cf.id');
        $new_file_list= [];
        foreach ($fileList as $key => $value) {
            if($key && $value){
                $new_file_list[$key]= $value;
            }
        }
        $fileList= $new_file_list;

        $voyageList= VesselVoyage::where('cm_vessel_id', $importData->cm_vessel_id)
                                    ->pluck('voyage_name', 'id');
        
        $suppliers = DB::table('cm_btb')
                    ->where('cm_file_id', $importData->cm_file_id)
                    ->leftJoin('mr_supplier','mr_supplier.sup_id','cm_btb.mr_supplier_sup_id')
                    ->pluck('sup_name','sup_id');

        //get ILC List
        $ilcs= $this->ilcList($importData->cm_file_id, $importData->mr_supplier_sup_id);

       

        //get invoice information
        $invoiceInfo= DB::table('cm_imp_invoice')
                        ->where('cm_imp_data_entry_id', $id)
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


        
        $piBomList= $this->getPIBomListEdit($importData->cm_btb_id, $invoiceInfo->id);
        $piId=12;
        //dd($piBomList);
        //dd($selected_pi_list);

        return view('commercial/import/import_data/import_data_edit', compact('importData', 'bankList', 'countryList', 'portList', 'vesselList', 'fileList', 'voyageList', 'suppliers', 'ilcs', 'invoiceInfo', 'cm_pi', 'piBomList', 'piId'));
    }

    public  function getPIBomListEdit($ilc_no,$invoice_id){
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

        return view("commercial/import/import_data/pi_bom_edit", compact('booking','btb','invoice_id'))->render();

    }

    

    //Import Data Update
    public function updateData(Request $request){
        //dd($request->all());
        if ($request->isMethod('post'))
        { 
           DB::beginTransaction();
            try {
                $data = $request->all();
                ImportDataEntry::where('id', $data['data_entry_id'])
                    ->update([
                        'cm_bank_id'            => $data['bank'],
                        'imp_lc_type'           => $data['impdatatype'],
                        'transp_doc_no1'        => $data['tr_doc1'],
                        'transp_doc_date'       => $data['tr_doc_date'],
                        'transp_doc_no2'        => $data['tr_doc2'],
                        'ship_mode'             => $data['ship'],
                        'weight'                => $data['weight'],
                        'imp_lc_type'           => $data['impdatatype'],
                        'cnt_id'                => $data['country'],
                        'carrier'               => $data['carrier'],
                        'freight'               => $data['freight'],
                        'ship_company'          => $data['ship_com'],
                        'container_1'           => $data['container1'],
                        'container_2'           => $data['container2'],
                        'container_3'           => $data['container3'],
                        'package'               => $data['package'],
                        'doc_type'              => $data['doc_type'],
                        'doc_recv_date'         => $data['docdate'],
                        'qty'                   => $data['quantity'],
                        'value'                 => $data['value'],
                        'value_currency'        => $data['value_currency'],
                        'cm_port_id'            => $data['port_loading'],
                        'container_size'        => $data['container_size'],
                        'cm_vessel_id'          => $data['mother_vessel'],
                        'cm_voyage_vessel_id'   => $data['voyage_no'],
                        'cubic_measurement'     => $data['cubic_measurement']
                    ]);

                DB::table('cm_imp_invoice')
                    ->where('id', $data['invoice_id'])
                    ->update([
                        'invoice_no'    => $data['invoice_no'],
                        'invoice_date'  => $data['invoice_date']
                    ]);
                        
                $last_invoice = $data['invoice_id'];
                //Store Invoice Data Import Invoice Table
                if(isset($request->shipped) && sizeof($request->shipped)>0){
                    foreach ($request->shipped as $key => $value) {
                        $up = CmInvoicePiBom::find($key);
                        $up->shipped_qty = $value;
                        $up->save();
                    }
                }

                // $this->logFileWrite("Commercial-> Import Data Entry Saved", $last_id );
               DB::commit();
                return back()->with('success', "Import Data Information Successfully Updated!!!");
            } catch (\Exception $e) {
               DB::rollback();
              return back()->with('error', $e->getMessage());
            }
        }
    }

    //get Import Data List Data
    public function getImportListData(){
       
        $data = DB::table('cm_imp_data_entry as cmpde')
            ->select(
                "cmpde.id",
                "cb.bank_name",
                "cmpde.imp_lc_type",
                "cmpde.transp_doc_no1",
                "cmpde.transp_doc_date",
                "cmpde.value",
                "cmpde.qty",
                "cf.file_no",
                "hu.hr_unit_name",
                "cmpde.cm_btb_id",
                "ms.sup_name"
            )
            ->leftJoin("cm_bank AS cb", "cb.id", "=",  "cmpde.cm_bank_id")
            ->leftJoin("cm_file As cf", "cf.id","=","cmpde.cm_file_id")
            ->leftJoin("hr_unit As hu", "hu.hr_unit_id","=", "cmpde.hr_unit")
            ->leftJoin("mr_supplier As ms","ms.sup_id","=","cmpde.mr_supplier_sup_id")
            ->where('imp_lc_type','Foreign')
            ->groupBy('cmpde.id')
            ->orderBy('cmpde.id','DESC')
            ->get();

            // dd($data);

        return DataTables::of($data)->addIndexColumn()

            /// Query for Action

            ->editColumn('action', function ($data) {

                $action_buttons= "<div class=\"btn-group\">
                            <a href=".url('commercial/import/import_data/edit/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit Import Data\" style=\"height:25px; width:26px;\">
                                <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                            </a>
                            <a href=".url('commercial/import/import_data/'.$data->id.'/delete')." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\" style=\"height:25px; width:26px;\"  onclick=\"return confirm('Are you sure?')\" >
                                <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                            </a> ";
                        $action_buttons.= "</div>";

                return $action_buttons;

            })
            ->rawColumns(['action'])
            ->toJson();

    }

    //delete Import Data
    public function deleteEntry($id){
        // dd($id);
        try {
            DB::beginTransaction();
            DB::table('cm_imp_data_entry')->where('id', $id)->delete();
            $invoiceList= DB::table('cm_imp_invoice')->where('cm_imp_data_entry_id', $id)->pluck('id');
            DB::table('cm_imp_invoice')->where('cm_imp_data_entry_id', $id)->delete();
            DB::table('cm_invoice_pi_bom')->whereIn('cm_imp_invoice_id', $invoiceList)->delete();

            DB::commit();
            $this->logFileWrite("Import Data Deleted", $id);
            return redirect('commercial/import/import_data/list')
                    ->with('success', "Import Data Deleted Successfully!");
            
        } catch (\Exception $e) {
            $msg= $e->getMessage();
            return redirect('commercial/import/import_data/list')
                    ->with('error', $msg);
        }
    }

    
}
