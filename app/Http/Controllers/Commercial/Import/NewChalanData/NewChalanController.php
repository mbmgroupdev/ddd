<?php

namespace App\Http\Controllers\Commercial\Import\NewChalanData;

use App\Models\Commercial\ChalaDataEntry;
use App\Models\Commercial\CmPiBom;
use App\Models\Commercial\CmPiMaster;
use App\Models\Commercial\CommBank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Response;
use Yajra\DataTables\DataTables;

class NewChalanController extends Controller
{
    public function index()
    {
        $bank = CommBank::pluck('bank_name','id');

        $uoms = CmPiBom::all();

        $cm_pi = CmPiMaster::all();

        $mr_category = DB::table('mr_material_category')->get();

        $mr_color = DB::table('mr_material_color')->get();

        $mr_art = DB::table('mr_article')->get();

        $mr_comp = DB::table('mr_composition')->get();

        $mr_const = DB::table('mr_construction')->get();

        $cm_file = DB::table('cm_pi_master')
            ->join('cm_file','cm_pi_master.cm_file_id','=','cm_file.id')
            ->pluck('cm_file.file_no','cm_file.id');

        $cm_supplier = DB::table('cm_pi_master')
            ->join('mr_supplier','cm_pi_master.mr_supplier_sup_id','=','mr_supplier.sup_id')
            ->pluck('mr_supplier.sup_name','mr_supplier.sup_id');

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

        return view('commercial.import.NewChalanData.new_chalan_data_entry',
            compact('bank','cm_file','cm_supplier','uoms','pi',
                'cm_pi','mr_category','mr_color','mr_art','mr_comp','mr_const'));
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
            ->select('cm_btb.lc_no as lc_no','cm_btb.id as btb_id')
            ->where('cm_btb.mr_supplier_sup_id',$request->supplier_no)
            ->first();

        //echo $supplier_no->lc_no;exit;
        return Response::json($supplier_no);
    }

    public function piNo($invoiceId)
    {
        $cm_pi = DB::table('cm_pi_master')->get();

        return view('commercial.import.NewChalanData.chalan_pi_no',compact('cm_pi','invoiceId'));
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

    public function piBom(Request $request, $piId)
    {
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

        return view('commercial.import.NewChalanData.chalan_pi_bom',
            compact('pi','mr_category','mr_color','mr_art','mr_comp','mr_const','piId','cat_item'));
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

    public function suppFilePi(Request $request)
    {
        $cm_pi = DB::table('cm_pi_master')
            ->where('cm_file_id',$request->file_no)
            ->where('mr_supplier_sup_id',$request->supplier_no)
            ->first();
        //dd($cm_pi);
        //return view('commercial.import.NewChalanData.chalan_pi_no',compact('cm_pi','invoiceId'));
        return Response::json($cm_pi);
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $data = $request->all();

            $chalan = new ChalaDataEntry();

            $chalan->cm_bank_id = $data['bank'];
            $chalan->cm_file_id = $data['file_no'];
            $chalan->mr_supplier_sup_id = $data['supplier'];
            $chalan->imp_lc_type = $data['impdatatype'];
            $chalan->transp_doc_no1 = $data['tr_doc1'];
            $chalan->transp_doc_date = $data['tr_doc_date'];
            $chalan->value = $data['value'];
            $chalan->carrier = $data['carrier'];
            $chalan->doc_type = $data['doc_type'];
            $chalan->hr_unit = $data['unit_id'];
            $chalan->package = $data['package'];
            $chalan->doc_recv_date = $data['doc_date'];
            $chalan->qty = $data['quantity'];
            $chalan->cm_btb_id = $data['btb_id'];
//            $chalan->uom = '';

            $chalan->save();

            $import = [];
            $new_import = [];
            $input = $request->all();
            $last_id = $chalan->id;

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

            $this->logFileWrite("Commercial-> Import Chalan Data Entry Saved", $last_id );

            return back()->with('success', "Chalan Data Information Successfully Added!!!");
        }
    }

    public function view()
    {
        return view('commercial.import.NewChalanData.new_chalan_data_list');
    }

    public function getData()
    {
        $chalans = DB::table('cm_imp_data_entry as cmpde')
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
            ->where('cmpde.imp_lc_type','Local')
            ->groupBy('cmpde.id')
            ->orderBy('cmpde.id', 'DESC')
            ->get();
        //dd($chalans);exit;

        return DataTables::of($chalans)
            ->addIndexColumn()

            ->editColumn('action', function ($chalans) {
                $return = "<div class=\"btn-group\">";
                if (!empty($chalans->imp_lc_type))
                {
                    $return .= "<a href=".url('comm/import/chalan/chalanedit/'.$chalans->id)." class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" title=\"Edit\">
                                 <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                                  </a>

                                  <a onclick=\"deleteData($chalans->id)\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\">
                                        <i class=\"ace-icon fa fa-remove bigger-120\"></i>
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

        $chalan = ChalaDataEntry::findOrFail($input['id']);
        $id = $input['id'];


        $ci = DB::table('cm_imp_invoice')->where('cm_imp_data_entry_id',$chalan->id)->get();


        foreach ($ci as $c)
        {

            $cis = DB::table('cm_invoice_pi_bom')->where('cm_imp_invoice_id',$input['id'])->get();

            foreach ($cis as $cs)
            {
                $cs = DB::table('cm_invoice_pi_bom')->where('cm_imp_invoice_id',$cs->id)->delete();
            }
            $c = DB::table('cm_imp_invoice')->where('cm_imp_data_entry_id',$chalan->id)->delete();
        }

        $chalan->delete();

        $this->logFileWrite("Commercial-> Import Chalan Data Entry Deleted", $id );

        return back()->with('success', "Chalan Data Information Successfully Deleted!!!");
    }

}
