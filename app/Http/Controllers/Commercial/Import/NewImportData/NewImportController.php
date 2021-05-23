<?php

namespace App\Http\Controllers\Commercial\Import\NewImportData;

use App\Models\Commercial\CmFile;
use App\Models\Commercial\CmPiBom;
use App\Models\Commercial\CmPiMaster;
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

use Validator, DB, ACL, Auth, DataTables,Response;

class NewImportController extends Controller
{
    public function index(Request $request)
    {
        $port = Port::pluck('port_name','cnt_id'); //change port_id to cnt_id

        $bank  = CommBank::pluck('bank_name','id'); //change bank_id to short_code

        $country= Country::pluck('cnt_name','cnt_id');

        $vessel= Vessel::pluck('vessel_name','id'); //vessel table is not nay vaessel_id

        $voyage= VesselVoyage::pluck('voyage_name','cm_vessel_id'); //chnage voyage_id to cm_vessel_id

        $colorcode  = DB::table('mr_material_color')->pluck('clr_code','clr_id');

        $colorname  = DB::table('mr_material_color')->pluck('clr_name','clr_id');


        $cm_pi = CmPiMaster::all();

        $cm_file = DB::table('cm_pi_master')
            ->join('cm_file','cm_pi_master.cm_file_id','=','cm_file.id')
            ->pluck('cm_file.file_no','cm_file.id');

        $cm_supplier = DB::table('cm_pi_master')
            ->join('mr_supplier','cm_pi_master.mr_supplier_sup_id','=','mr_supplier.sup_id')
            ->pluck('mr_supplier.sup_name','mr_supplier.sup_id');

        $mr_category = DB::table('mr_material_category')->get();

        $mr_color = DB::table('mr_material_color')->get();

        $mr_art = DB::table('mr_article')->get();

        $mr_comp = DB::table('mr_composition')->get();

        $mr_const = DB::table('mr_construction')->get();

        $pi = DB::table('cm_pi_bom')
            ->select(
                 'cm_pi_bom.cm_pi_master_id',
                'mr_cat_item.id',
                'mr_cat_item.item_name'


            )
            ->leftJoin('mr_order_bom_costing_booking','cm_pi_bom.mr_order_bom_costing_booking_id','=','mr_order_bom_costing_booking.id')
            ->leftJoin('mr_cat_item','mr_order_bom_costing_booking.mr_cat_item_id','=','mr_cat_item.id')
            ->leftJoin('mr_material_category','mr_order_bom_costing_booking.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
            //->where('cm_pi_master_id',$request->pi_master_id)
            ->get();


        return view('commercial.import.NewImportData.new_import_data_entry',
            compact('port','vessel','bank','country','pi','cm_supplier','cm_pi','cm_file','voyage','colorcode','colorname','mr_category','mr_color','mr_art','mr_comp','mr_const'));
    }

    public function autocode(Request $request)
    {
        $length=8;
        $randstr = "";
        srand((double)microtime() * 1000000);
        //our array add all letters and numbers if you wish
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        for ($rand = 1; $rand <= $length; $rand++) {
            $random = rand(0, count($chars) - 1);
            $randstr .= $chars[$random];
        }
        echo $randstr.'/'.date('y');exit;
    }

    public function vesselVoyage(Request $request)
    {
        $select = $request->get('select');

        $value = $request->get('value');

        $dependent = $request->get('dependent');

        $data = DB::table('cm_voyage_vessel')->where($select, $value)->groupBy($dependent)->get();

        $output = '<option value="">Select '.ucfirst($dependent).'</option>';

        foreach ($data as $row) {
            $output .='<option value="'.$row->cm_vessel_id.'">'.$row->$dependent.'</option>';
        }
        echo $output;
    }

    public function fileUnit(Request $request)
    {
        $data = array();
        $data['file_no'] = DB::table('cm_pi_master')
            ->select('hu.hr_unit_name','cf.hr_unit')
            ->leftJoin('cm_file as cf','cf.id','=','cm_pi_master.cm_file_id')
            ->leftJoin('hr_unit as hu','hu.hr_unit_id','=','cf.hr_unit')
            ->where('cf.id',$request->file_no)
            ->first();

        $data['lc_no'] = DB::table('cm_btb')
            ->where('cm_file_id', $request->file_no)
            ->select('id', 'lc_no')
            ->get();
        //echo $file_no->hr_unit_name;exit;
        return Response::json($data);
    }

    public function supplierIlcNo(Request $request)
    {
        $supplier_no = DB::table('cm_pi_master')
            ->join('cm_btb','cm_pi_master.mr_supplier_sup_id','=','cm_btb.mr_supplier_sup_id')
            ->select('cm_btb.lc_no as lc_no')
            ->where('cm_btb.mr_supplier_sup_id',$request->supplier_no)
            ->first();

        //echo $supplier_no->lc_no;exit;
        return Response::json($supplier_no);
    }

    public function piNo($invoiceId)
    {

        $cm_pi = DB::table('cm_pi_master')->get();

        return view('commercial.import.NewImportData.pi_no',compact('cm_pi','invoiceId'));
    }

    public function suppFilePi(Request $request)
    {
        $cm_pi = DB::table('cm_pi_master')
            ->where('cm_file_id',$request->file_no)
            ->where('mr_supplier_sup_id',$request->supplier_no)
            ->first();
        //dd($cm_pi);
        //return view('commercial.import.NewImportData.pi_no',compact('cm_pi','invoiceId'));
        return Response::json($cm_pi);
    }

    public function piBom(Request $request, $piId)
    {
        //return $piId;

        $cat_item = DB::table('mr_cat_item')->get();

        $mr_category = DB::table('mr_material_category')->get();

        $mr_color = DB::table('mr_material_color')->get();

        $mr_art = DB::table('mr_article')->get();

        $mr_comp = DB::table('mr_composition')->get();

        $mr_const = DB::table('mr_construction')->get();

        $pi = DB::table('cm_pi_bom')
            ->select(
                'cm_pi_bom.id',
                'cm_pi_bom.cm_pi_master_id',
                'cm_pi_bom.extra',
                'cm_pi_bom.total_qty',
                'cm_pi_bom.unit_price',
                'cm_pi_bom.currency',
                'cm_pi_bom.consumption',
                'cm_pi_bom.pi_qty',
                'mr_material_category.mcat_name',
                'mr_cat_item.id',
                'mr_cat_item.item_name',
                'mr_order_bom_costing_booking.item_description',
                'mr_order_bom_costing_booking.uom',
                'mr_material_color.clr_name',
                'mr_article.art_name',
                'mr_composition.comp_name',
                'mr_construction.construction_name'

            )
            ->leftJoin('mr_order_bom_costing_booking','cm_pi_bom.mr_order_bom_costing_booking_id','=','mr_order_bom_costing_booking.id')
            ->leftJoin('mr_cat_item','mr_order_bom_costing_booking.mr_cat_item_id','=','mr_cat_item.id')
            ->leftJoin('mr_material_category','mr_order_bom_costing_booking.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
            ->leftJoin('mr_composition','mr_order_bom_costing_booking.mr_composition_id','=','mr_composition.id')
            ->leftJoin('mr_construction','mr_order_bom_costing_booking.mr_construction_id','=','mr_construction.id')
            ->leftJoin('mr_article','mr_order_bom_costing_booking.mr_article_id','=','mr_article.id')
            ->leftJoin('mr_material_color','mr_order_bom_costing_booking.clr_id','=','mr_material_color.clr_id')
            ->where('cm_pi_master_id',$request->pi_master_id)->get();
        //dd($pi);exit;

        return view('commercial.import.NewImportData.pi_bom',
            compact('pi','mr_category','mr_color','mr_art','mr_comp','mr_const','piId','cat_item'));
    }

    public function piDate(Request $request)
    {
        $pi_no = DB::table('cm_pi_master AS mpi')
            ->select([
                'mpi.pi_date'
            ])

            ->where('mpi.id',$request->pi_no)
            ->first();
        echo $pi_no->pi_date;exit;
    }

    public function bomData(Request $request)
    {

        $pi_bom = DB::table('cm_pi_bom')
            ->select(
                'cm_pi_bom.id',
                'cm_pi_bom.cm_pi_master_id',
                'cm_pi_bom.extra',
                'cm_pi_bom.total_qty',
                'cm_pi_bom.unit_price',
                'cm_pi_bom.currency',
                'cm_pi_bom.consumption',
                'cm_pi_bom.pi_qty',
                'cm_pi_bom.mr_material_category_mcat_id',
                'cm_pi_bom.mr_order_bom_costing_booking_id',
                'mr_material_category.mcat_name',
                'mr_cat_item.mcat_id',
                'mr_order_bom_costing_booking.item_description',
                'mr_order_bom_costing_booking.uom',
                'mr_order_bom_costing_booking.mr_cat_item_id',
                'mr_material_color.clr_name',
                'mr_article.art_name',
                'mr_composition.comp_name',
                'mr_construction.construction_name'
            )
            ->leftJoin('mr_order_bom_costing_booking','cm_pi_bom.mr_order_bom_costing_booking_id','=','mr_order_bom_costing_booking.id')
            ->leftJoin('mr_cat_item','mr_order_bom_costing_booking.mr_cat_item_id','=','mr_cat_item.id')
            ->leftJoin('mr_material_category','mr_order_bom_costing_booking.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
            ->leftJoin('mr_composition','mr_order_bom_costing_booking.mr_composition_id','=','mr_composition.id')
            ->leftJoin('mr_construction','mr_order_bom_costing_booking.mr_construction_id','=','mr_construction.id')
            ->leftJoin('mr_article','mr_order_bom_costing_booking.mr_article_id','=','mr_article.id')
            ->leftJoin('mr_material_color','mr_order_bom_costing_booking.clr_id','=','mr_material_color.clr_id')
            ->where('mr_order_bom_costing_booking.mr_cat_item_id',$request->item)
            ->first();
        //dd($pi_bom);exit;
        return Response::json($pi_bom);
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post'))
        {     DB::beginTransaction();
            try {
              $data = $request->all();
              //dd($data);exit;
              $newimp= new ImportDataEntry();

              $newimp->imp_code = $data['importcode'];
              $newimp->cm_file_id = $data['file_no'];
              $newimp->mr_supplier_sup_id = $data['supplier'];
              $newimp->cm_btb_id = $data['btb_id'];
              $newimp->hr_unit = $data['unit_id'];
              $newimp->cm_bank_id = $data['bank'];
              $newimp->imp_lc_type = $data['impdatatype'];
              $newimp->transp_doc_no1 = $data['tr_doc1'];
              $newimp->transp_doc_date = $data['tr_doc_date'];
              $newimp->transp_doc_no2  = $data['tr_doc2'];
              $newimp->ship_mode  = $data['ship'];
              $newimp->weight   = $data['weight'];
              $newimp->imp_lc_type  = $data['impdatatype'];
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


              $import = [];
              $new_import = [];
              $input = $request->all();
              $last_id = $newimp->id;

              // Store Invoice Data Import Invoice Table
              for($i=0; $i < sizeof($request->rowno); $i++)
              {
                  $import['cm_imp_data_entry_id'] = $last_id;
                  $import['invoice_no'] = $request->invoiceno[$i];
                  $import['invoice_date'] = $request->invoicedate[$i];
                  $datas [] = $import;
                  $last_invoice = DB::table('cm_imp_invoice')->insertGetId($import);

                  for ($j=0; $j < sizeof($request['shipped_qty-'.$i]); $j++)
                  {

                      $new_import['cm_imp_invoice_id'] = $last_invoice;
                      $new_import['cm_pi_master_id'] = $request['cm_pi_master_id-'.$i][$j];
                      $new_import['cm_pi_bom_id'] = $request['id-'.$i][$j];
                      $new_import['shipped_qty'] = $request['shipped_qty-'.$i][$j];

                      $datass[] = $new_import;
                      //dd($new_import);exit;

                      DB::table('cm_invoice_pi_bom')->insert($new_import);
                  }

              }

              $this->logFileWrite("Commercial-> Import Data Entry Saved", $last_id );
               DB::commit();
              return back()->with('success', "Import Data Information Successfully Added!!!");

            } catch (\Exception $e) {
               DB::rollback();
              return back()->with('error', $e->getMessage());
            }


        }
    }

    public function view()
    {
        return view('commercial.import.NewImportData.new_import_data_list');
    }

    public function getData()
    {
        $imports = DB::table('cm_imp_data_entry as cmpde')
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
        //dd($imports);exit;


        return DataTables::of($imports)
            ->addIndexColumn()

            ->editColumn('action', function ($imports) {
                $return = "<div class=\"btn-group\">";
                if (!empty($imports->imp_lc_type))
                {
                    $return .= "<a href=".url('commercial/import/importdata/edit/'.$imports->id)." class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" title=\"Edit\">
                                 <i class=\"ace-icon fa fa-pencil \"></i>
                                  </a>

                                  <a onclick=\"deleteData($imports->id)\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\">
                                        <i class=\"ace-icon fa fa-remove \"></i>
                                    </a>
                                  ";
                }
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'action'
            ])

            ->make(true);
    }

    public function destroy(Request $request)
    {
        $input = $request->all();

        $import = ImportDataEntry::findOrFail($input['id']);
        $last_id = $input['id'];

        $ci = DB::table('cm_imp_invoice')->where('cm_imp_data_entry_id',$import->id)->get();


        foreach ($ci as $c)
        {

            $cis = DB::table('cm_invoice_pi_bom')->where('cm_imp_invoice_id',$input['id'])->get();

            foreach ($cis as $cs)
            {
                $cs = DB::table('cm_invoice_pi_bom')->where('cm_imp_invoice_id',$cs->id)->delete();
            }
            $c = DB::table('cm_imp_invoice')->where('cm_imp_data_entry_id',$import->id)->delete();
        }

        $import->delete();

        $this->logFileWrite("Commercial-> Import Data Entry Deleted", $last_id );

        return back()->with('success', "Import Data Information Successfully Deleted!!!");
    }

    public function edit($id){
        
        $import = ImportDataEntry::findOrFail($id);

        $port = Port::all(); //change port_id to cnt_id

        $bank = CommBank::all(); //change bank_id to short_code

        $country= Country::all();

        $vessel= Vessel::all(); //vessel table is not nay vaessel_id

        $voyage= VesselVoyage::all(); //chnage voyage_id to cm_vessel_id

        $colorcode  = DB::table('mr_material_color')->pluck('clr_code','clr_id');

        $colorname  = DB::table('mr_material_color')->pluck('clr_name','clr_id');


        $cm_pi = CmPiMaster::all();

        $cm_file = DB::table('cm_pi_master')
            ->join('cm_file','cm_pi_master.cm_file_id','=','cm_file.id')
            ->pluck('cm_file.file_no','cm_file.id');

        $cm_supplier = DB::table('cm_pi_master')
            ->join('mr_supplier','cm_pi_master.mr_supplier_sup_id','=','mr_supplier.sup_id')
            ->pluck('mr_supplier.sup_name','mr_supplier.sup_id');

        $cm_invoice = DB::table('cm_imp_data_entry')
            ->join('cm_imp_invoice','cm_imp_data_entry.id','=','cm_imp_invoice.cm_imp_data_entry_id')
            ->select('cm_imp_invoice.invoice_no as invoice_no','cm_imp_invoice.invoice_date as invoice_date')
            ->where('cm_imp_invoice.cm_imp_data_entry_id',$id)
            ->get();

        $cm_pi_invoice = DB::table('cm_imp_data_entry')
            ->join('cm_imp_invoice','cm_imp_data_entry.id','=','cm_imp_invoice.cm_imp_data_entry_id')
            ->join('cm_invoice_pi_bom','cm_imp_invoice.id','=','cm_invoice_pi_bom.cm_imp_invoice_id')
            ->join('cm_pi_master','cm_invoice_pi_bom.cm_pi_master_id','=','cm_pi_master.id')
            ->select('cm_pi_master.pi_no','cm_pi_master.pi_date')
            ->where('cm_imp_invoice.cm_imp_data_entry_id',$id)
            ->get();

        $cm_pi_master = DB::table('cm_imp_data_entry')
            ->join('cm_imp_invoice','cm_imp_data_entry.id','=','cm_imp_invoice.cm_imp_data_entry_id')
            ->join('cm_invoice_pi_bom','cm_imp_invoice.id','=','cm_invoice_pi_bom.cm_imp_invoice_id')
            ->join('cm_pi_master','cm_invoice_pi_bom.cm_pi_master_id','=','cm_pi_master.pi_no')
            ->join('cm_pi_bom','cm_pi_master.id','=','cm_pi_bom.cm_pi_master_id')
            ->where('cm_imp_invoice.cm_imp_data_entry_id',$id)
            ->get();

        $mr_category = DB::table('mr_material_category')->get();

        $mr_color = DB::table('mr_material_color')->get();

        $mr_art = DB::table('mr_article')->get();

        $mr_comp = DB::table('mr_composition')->get();

        $mr_const = DB::table('mr_construction')->get();

        // dd($cm_invoice);

        $pi = DB::table('cm_pi_bom')
            ->select(
                 'cm_pi_bom.cm_pi_master_id',
                'mr_cat_item.id',
                'mr_cat_item.item_name'


            )
            ->leftJoin('mr_order_bom_costing_booking','cm_pi_bom.mr_order_bom_costing_booking_id','=','mr_order_bom_costing_booking.id')
            ->leftJoin('mr_cat_item','mr_order_bom_costing_booking.mr_cat_item_id','=','mr_cat_item.id')
            ->leftJoin('mr_material_category','mr_order_bom_costing_booking.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
            ->get();

            $unit_name= Unit::where('hr_unit_id', $import->hr_unit)->pluck('hr_unit_name')->first();


          $ilc_list = DB::table('cm_btb')
            ->where('cm_file_id', $import->cm_file_id)
            ->pluck('lc_no', 'id');


        return view('commercial.import.NewImportData.import_data_edit', compact('import','port','vessel','bank','country','cm_supplier','cm_pi','cm_file','voyage','colorcode','colorname','mr_category','cm_invoice','cm_pi_invoice','cm_pi_master','mr_color','mr_art','mr_comp','mr_const', 'pi', 'unit_name', 'ilc_list'));
    }
}
