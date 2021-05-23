<?php

namespace App\Http\Controllers\Commercial\Import\AssetData;

use App\Models\Commercial\CmImpDataEntryAsset;
use App\Models\Commercial\CmPiMaster;
use App\Models\Commercial\CommBank;
use App\Models\Commercial\Country;
use App\Models\Commercial\Port;
use App\Models\Commercial\Vessel;
use App\Models\Commercial\VesselVoyage;
use DB, Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class AssetController extends Controller
{

    //Show Entry Form
    public function showForm(){
        $bankList= CommBank::pluck('bank_name', 'id');
        $countryList= Country::pluck('cnt_name', 'cnt_id');
        $portList= Port::pluck('port_name', 'id');
        $vesselList= Vessel::pluck('vessel_name', 'id');
        $fileList= DB::table('cm_pi_asset AS cpm')
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

        $supplierList = DB::table('cm_pi_asset')
            ->join('mr_supplier','cm_pi_asset.mr_supplier_sup_id','=','mr_supplier.sup_id')
            ->pluck('mr_supplier.sup_name','mr_supplier.sup_id');


        $importCode= $this->autoCode();

        return view('commercial/import/asset_data/asset_data_entry', compact('bankList', 'countryList', 'portList', 'vesselList', 'fileList', 'importCode', 'supplierList'));
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

        $ilcs= DB::table('cm_btb_asset')
                    ->where('cm_file_id', $file_no)
                    ->where('mr_supplier_sup_id', $supplier_no)
                    ->pluck('lc_no', 'id');

        return $ilcs;
    }


    //get PI
    public function getPIList(Request $request){
        $cm_pi = DB::table('cm_pi_asset')
                    ->where('cm_file_id', $request->file_no)
                    ->where('mr_supplier_sup_id', $request->supplier_no)
                    ->get();
        $invoiceId= $request->invoice_id;

        return view('commercial/import/asset_data/pi_no',compact('cm_pi','invoiceId'));
    } 


    //get selected pi date
    public function getPIDate(Request $request){
        $pi_date = DB::table('cm_pi_asset')
                    ->where('id',$request->pi_no)
                    ->pluck('pi_date')
                    ->first();
        return $pi_date;
    }

    //get PI BOM List
    public function getPIBomList(Request $request){

        $pi_list = DB::table('cm_pi_asset_description as pi')
                    ->leftJoin('cm_machine_type','pi.cm_machine_type_id','=','cm_machine_type.id')
                    ->leftJoin('cm_section','pi.cm_section_id','=','cm_section.id')
                    ->where('pi.cm_pi_asset_id',$request->pi_master_id)
                    ->select([
                        "pi.*",
                        "cm_machine_type.type_name",
                        "cm_machine_type.manufacturer",
                        "cm_machine_type.id AS type_id",
                        'cm_section.section_name',
                        'cm_section.id AS section_id'
                    ])
                    ->get();

                $piId= $request->pi_master_id;

        return view('commercial/import/asset_data/pi_bom',
            compact('pi_list', 'piId'));
    }


    // get pi bom list after plus button of bom list
    public function getPIBomAfterPlusButton(Request $request){

        $piId= $request->piId;

        $pi_list = DB::table('cm_pi_asset_description as pi')
                    ->leftJoin('cm_machine_type','pi.cm_machine_type_id','=','cm_machine_type.id')
                    ->leftJoin('cm_section','pi.cm_section_id','=','cm_section.id')
                    ->where('pi.cm_pi_asset_id',$piId)
                    ->pluck('cm_machine_type.type_name', 'pi.id');
        
        $bomList= '';
        foreach ($pi_list as $key => $value) {
            $bomList.= '<option value="'.$key.'">'.$value.'</option>';
        }
        return $bomList;
    }

    //get bom information after selecting item
    public function getBomItemData(Request $request)
    {

        $pi_bom = DB::table('cm_pi_asset_description AS cpad')
            ->leftJoin('cm_machine_type','cpad.cm_machine_type_id','=','cm_machine_type.id')
            ->leftJoin('cm_section','cpad.cm_section_id','=','cm_section.id')
            ->where('cpad.id',$request->machine)
            ->select([
                'cpad.*',
                'cm_machine_type.type_name',
                'cm_machine_type.manufacturer',
                'cm_machine_type.id AS type_id',
                'cm_section.section_name',
                'cm_section.id AS section_id'
            ])
            ->first();
        return Response::json($pi_bom);
    }

    //store data
    public function storeForm(Request $request){

        DB::beginTransaction();
        try {
            $data = $request->all();

            $asset = new CmImpDataEntryAsset();

            $asset->imp_code = $data['importcode'];
            $asset->cm_file_id = $data['file_no'];
            $asset->mr_supplier_sup_id = $data['supplier_no'];
            $asset->cm_btb_id = $data['ilc_no'];
            $asset->hr_unit = $data['unit_id'];
            $asset->cm_bank_id = $data['bank'];
            $asset->imp_lc_type = $data['impdatatype'];
            $asset->transp_doc_no1 = $data['tr_doc1'];
            $asset->transp_doc_date = $data['tr_doc_date'];
            $asset->transp_doc_no2  = $data['tr_doc2'];
            $asset->ship_mode  = $data['ship'];
            $asset->weight   = $data['weight'];
            $asset->cubic_measurement   = $data['cubic_measurement'];
            $asset->imp_lc_type  = $request->impdatatype;
            $asset->cnt_id  = $data['country'];
            $asset->carrier = $data['carrier'];
            $asset->ship_company = $data['ship_com'];
            $asset->container_1   = $data['container1'];
            $asset->container_2  = $data['container2'];
            $asset->container_3   = $data['container3'];
            $asset->package     = $data['package'];
            $asset->doc_type    = $data['doc_type'];
            $asset->doc_recv_date = $data['docdate'];
            $asset->qty           = $data['quantity'];
            $asset->value         = $data['value'];
            $asset->value_currency = $data['currency'];
            $asset->cm_port_id     = $data['port_loading'];
            $asset->container_size = $data['container_size'];
            $asset->cm_vessel_id   = $data['mother_vessel'];
            $asset->cm_voyage_vessel_id = $data['voyage_no'];
            $asset->save();

            $last_id = $asset->id;

            //Store Invoice Data Asset Invoice Table(cm_imp_invoice_asset)
            $newIn = DB::table('cm_imp_invoice_asset')->insertGetId([
                                'cm_imp_data_entry_asset_id' => $last_id,
                                'invoice_no' => $request->invoice_no,
                                'invoice_date' => $request->invoice_date
                            ]);
            $last_inv_id = $newIn;
            
            // store pi bom data to cm_invoice_pi_asset table
            if(isset($request->cm_pi_asset_description_id) && !empty($request->cm_pi_asset_description_id) && sizeof($request->cm_pi_asset_description_id)>0){

                for ($i = 0; $i < sizeof($request->cm_pi_asset_description_id); $i++) {
                    DB::table('cm_invoice_pi_asset')->insert([
                        'cm_pi_asset_id' => $last_id,
                        'cm_imp_invoice_asset_id' => $last_inv_id,
                        'cm_pi_asset_description_id' => $request->cm_pi_asset_description_id[$i],
                        'shipped_qty' => $request->shipped_qty[$i]
                    ]);
               }
            }

            //log Entry
            $this->logFileWrite("Commercial-> Import Data Entry Asset Saved", $last_id); 

            DB::commit();
            return back()
                    ->with('success', "Asset Data saved successfully!");

        } catch (\Exception $e) {
            DB::rollback();
            $msg= $e->getMessage();
            return back()
                    ->withInput()
                    ->with('error', $msg);

        }
    }

    //edit asset form
    public function editForm($id){
        $asset= CmImpDataEntryAsset::where('id', $id)
                                    ->leftJoin('hr_unit', 'hr_unit.hr_unit_id', 'cm_imp_data_entry_asset.hr_unit')
                                    ->first();

        // dd($asset);

        $voyageList= VesselVoyage::where('cm_vessel_id', $asset->cm_vessel_id)
                                    ->pluck('voyage_name', 'id');


        $invoice= DB::table('cm_imp_invoice_asset')
                    ->where('cm_imp_data_entry_asset_id', $id)
                    ->first();
        $piList= DB::table('cm_pi_asset')
                    ->where('cm_file_id', $asset->cm_file_id)
                    ->where('mr_supplier_sup_id', $asset->mr_supplier_sup_id)
                    ->get();


        $piBomList= DB::table('cm_invoice_pi_asset AS cpib')
                        ->where('cpib.cm_imp_invoice_asset_id', $invoice->id)
                        ->leftJoin('cm_pi_asset AS pib', 'pib.id', 'cpib.cm_pi_asset_description_id')
                        ->select([
                            'cpib.id AS bom_id',
                            'cpib.cm_pi_asset_id',
                            'cpib.cm_imp_invoice_asset_id',
                            'cpib.cm_pi_asset_description_id',
                            'cpib.shipped_qty',
                            'pib.*',
                            'cpad.*',
                            'cm_machine_type.type_name',
                            'cm_machine_type.manufacturer',
                            'cm_machine_type.id AS type_id',
                            'cm_section.section_name',
                            'cm_section.id AS section_id'
                        ])
                        ->leftJoin('cm_pi_asset_description AS cpad', 'cpad.cm_pi_asset_id', 'pib.id')

                        ->leftJoin('cm_machine_type','cpad.cm_machine_type_id','=','cm_machine_type.id')
                        ->leftJoin('cm_section','cpad.cm_section_id','=','cm_section.id')
                        ->get();
            // dd($piBomList);
            $ilcs= $this->ilcList($asset->cm_file_id, $asset->mr_supplier_sup_id);
            // dd($ilcs);
            $ilcList= "<option>Select ILC</option>";
            foreach ($ilcs as $key => $value) {
                $ilcList.='<option value="'.$key.'" selected> '. $value .' </option>';
            }

        // dd($piBomList);

        $bankList= CommBank::pluck('bank_name', 'id');
        $countryList= Country::pluck('cnt_name', 'cnt_id');
        $portList= Port::pluck('port_name', 'id');
        $vesselList= Vessel::pluck('vessel_name', 'id');
        $fileList= DB::table('cm_pi_asset AS cpm')
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

        $supplierList = DB::table('cm_pi_asset')
            ->join('mr_supplier','cm_pi_asset.mr_supplier_sup_id','=','mr_supplier.sup_id')
            ->pluck('mr_supplier.sup_name','mr_supplier.sup_id');


        // $importCode= $this->autoCode();


        // dd($asset);
        return view('commercial/import/asset_data/asset_data_edit', compact('asset', 'bankList', 'countryList', 'portList', 'vesselList', 'fileList', 'supplierList', 'invoice', 'piList', 'piBomList', 'ilcList', 'voyageList'));
    }
 

    //update asset data form
    public function updateData(Request $request){

        DB::beginTransaction();
        try {
            CmImpDataEntryAsset::where('id', $request->asset_id)
                ->update([
                    'imp_code'            => $request->importcode,
                    'cm_file_id'          => $request->file_no,
                    'mr_supplier_sup_id'  => $request->supplier_no,
                    'cm_btb_id'           => $request->ilc_no,
                    'hr_unit'             => $request->unit_id,
                    'cm_bank_id'          => $request->bank,
                    'imp_lc_type'         => $request->impdatatype,
                    'transp_doc_no1'      => $request->tr_doc1,
                    'transp_doc_date'     => $request->tr_doc_date,
                    'transp_doc_no2'      => $request->tr_doc2,
                    'ship_mode'           => $request->ship,
                    'weight'              => $request->weight,
                    'cubic_measurement'   => $request->cubic_measurement,
                    'imp_lc_type'         => $request->impdatatype,
                    'cnt_id'              => $request->country,
                    'carrier'             => $request->carrier,
                    'ship_company'        => $request->ship_com,
                    'container_1'         => $request->container1,
                    'container_2'         => $request->container2,
                    'container_3'         => $request->container3,
                    'package'             => $request->package,
                    'doc_type'            => $request->doc_type,
                    'doc_recv_date'       => $request->docdate,
                    'qty'                 => $request->quantity,
                    'value'               => $request->value,
                    'value_currency'      => $request->currency,
                    'cm_port_id'          => $request->port_loading,
                    'container_size'      => $request->container_size,
                    'cm_vessel_id'        => $request->mother_vessel,
                    'cm_voyage_vessel_id' => $request->voyage_no
                ]);
            $last_id= $request->asset_id;
            //delete existing
            DB::table('cm_imp_invoice_asset')
                ->where('cm_imp_data_entry_asset_id', $request->asset_id)
                ->delete();

            //insert new invoice
            $newIn = DB::table('cm_imp_invoice_asset')->insertGetId([
                                'cm_imp_data_entry_asset_id' => $last_id,
                                'invoice_no' => $request->invoice_no,
                                'invoice_date' => $request->invoice_date
                            ]);
            $last_inv_id = $newIn;

            //delete existing pi's
            DB::table('cm_invoice_pi_asset')
                ->where('cm_pi_asset_id', $request->asset_id)
                ->delete();

            // store pi bom data to cm_invoice_pi_asset table
            if(isset($request->cm_pi_asset_description_id) && !empty($request->cm_pi_asset_description_id) && sizeof($request->cm_pi_asset_description_id)>0){

                for ($i = 0; $i < sizeof($request->cm_pi_asset_description_id); $i++) {
                    DB::table('cm_invoice_pi_asset')->insert([
                        'cm_pi_asset_id' => $last_id,
                        'cm_imp_invoice_asset_id' => $last_inv_id,
                        'cm_pi_asset_description_id' => $request->cm_pi_asset_description_id[$i],
                        'shipped_qty' => $request->shipped_qty[$i]
                    ]);
               }
            }
            //log Entry
            $this->logFileWrite("Commercial-> Import Data Entry Asset Update", $last_id); 

            DB::commit();
            return redirect('commercial/import/asset_data/edit/'.$request->asset_id)
                    ->with('success', "Asset Data updated successfully!");
        } catch (\Exception $e) {
            DB::rollback();
            $msg= $e->getMessage();
            return redirect('commercial/import/asset_data/edit/'.$request->asset_id)
                    ->withInput()
                    ->with('error', $msg);
        }
    }

    //show list
    public function showList()
    {
        return view('commercial/import/asset_data/asset_data_list');
    }

    //get list data
    public function getData()
    {
        $assets = DB::table('cm_imp_data_entry_asset as cmpde')
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
            ->orderBy('cmpde.id')
            ->get();

        return DataTables::of($assets)
            ->addIndexColumn()

            ->editColumn('action', function ($assets) {
                $return = "<div class=\"btn-group\">";
                if (!empty($assets->imp_lc_type))
                {
                    $return .= "<a href=".url('commercial/import/asset_data/edit/'.$assets->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\">
                                 <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                                  </a>

                                  <a onclick=\"deleteData($assets->id)\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\">
                                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
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
        $id= $request->id;
        $asset = CmImpDataEntryAsset::findOrFail($id);



        $ci = DB::table('cm_imp_invoice_asset')->where('cm_imp_data_entry_asset_id',$asset->id)->get();


        foreach ($ci as $c)
        {

            $cis = DB::table('cm_invoice_pi_asset')->where('cm_imp_invoice_asset_id',$c->id)->get();

            foreach ($cis as $cs)
            {
                $cs = DB::table('cm_invoice_pi_asset')->where('cm_imp_invoice_asset_id',$cs->id)->delete();
            }
            $c = DB::table('cm_imp_invoice_asset')->where('cm_imp_data_entry_asset_id',$asset->id)->delete();
        }

        $asset->delete();

        $this->logFileWrite("Commercial-> Import Data Entry Asset Deleted", $id); //log Entry

        return back()->with('success', "Asset Data Information Successfully Deleted!!!");
    }
}
