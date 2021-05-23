<?php
namespace App\Http\Controllers\Commercial\Export\ExportLc\EntryUpdateScreen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Buyer;
use App\Models\Commercial\Bank;
use App\Models\Commercial\BankAccNo;
use App\Models\Commercial\SalesContract;
use App\Models\Commercial\SalesContractOrder;
use App\Models\Merch\Country;
use App\Models\Merch\OrderEntry;
use App\Models\Commercial\ExpDataEntry;
use App\Models\Hr\Unit;
use Validator, DB, ACL, Auth, DataTables, Response;



class ExportLcEntryUpdateScreenController extends Controller
{

 //get entry update screen2
  public function getEntryUpdateScreen(Request $request){
    $hr_unit = DB::table('hr_unit')
                 ->pluck('hr_unit_name','hr_unit_id');
    $uom = DB::table('uom')
            ->pluck('measurement_name','measurement_name');
   return view("commercial/export/export_lc/entry_update_screen/export_lc_entry_update_screen2", compact("hr_unit","uom"));
 }

 public function getEntryUpdateScreenEdit(Request $request,$id){
   if($request->isMethod('get')){
         $hr_unit = DB::table('hr_unit')
                     ->pluck('hr_unit_name','hr_unit_id');
         $update2= DB::table('cm_exp_update2')->where('id',$id)
                     ->first();

          $check= DB::table('cm_exp_update3')->where('invoice_no',$update2->invoice_no)
                     ->first();
         $update2_container= DB::table('cm_exp_update2_container')->where('cm_exp_update2_id',$id)
                       ->get();
         $uom = DB::table('uom')
            ->pluck('measurement_name','measurement_name');
         return view("commercial/export/export_lc/entry_update_screen/export_lc_entry_update_screen2_edit", compact("hr_unit","update2","update2_container", "uom", "check"));
   }elseif ($request->isMethod('post')) {
     try {
       $update2 = DB::table('cm_exp_update2')->where('id',$id)->update([
             'cnf_doc_dispatch_date'   => $request->cf_doc_dispatch_date,
             'ex_fty_date'             => $request->ex_fty_date,
             'agent_inv_no'            => $request->agent_invoice_no,
             'ship_company'            =>$request->shipping_company,
             'staffing_date'           => $request->staffing_date,
             'feeder_vessel_details'   =>$request->feeder_vessel_name_no,
             'vessel_berth'            => $request->vessel_berth,
             'transp_doc_no'           => $request->transport_doc_no_of_ship_co,
             'transp_doc_date'         => $request->transport_doc_date_of_ship_co,
             'ship_bill_no'            => $request->shipping_bill_no,
             'ship_bill_date'          => $request->shipping_bill_date,
             'examine_date'            => $request->examine_date,
             'exp_release_date'        => $request->exp_release_date,
             'final_date_status'       =>isset($request->final_date)?1:0
           ]);

       //if($update2){
       if(isset($request->data)){
          $containerDatas = $request->data;
          DB::table('cm_exp_update2_container')->where('cm_exp_update2_id',$id)->delete();

            foreach ($containerDatas as $containerData) {
              DB::table('cm_exp_update2_container')->insert([
                    'cm_exp_update2_id'  => $id,
                    'container_no'       => $containerData['containerno'],
                    'size'               => $containerData['size'],
                    'container_sl'       => $containerData['containersi'],
                    'qty'                => $containerData['qty'],
                    'uom'                => $containerData['uom'],
                    'pkg'                =>$containerData['pkg']

              ]);
            }
        // }else{
        //   return back()->with("error", "Something went wrong.Please try again");
        // }
      }
        return back()->with("success", "Save Successful.");
     } catch (\Exception $e) {
        return back()->with("error",$e->getMessage());
     }
   }

 }
 public function getEntryUpdateScreen3Edit(Request $request,$id){
   if($request->isMethod('get')){
         $hr_unit = DB::table('hr_unit')
                     ->pluck('hr_unit_name','hr_unit_id');
         $hub = DB::table('cm_hub')
                    ->pluck('hub_name','id');

         $passBookVol = DB::table('cm_passbook_volume')
                            ->pluck('volume_no','volume_no');
         $passBookPage = DB::table('cm_passbook_volume')
                             ->pluck('page_no','page_no');
         $update3= DB::table('cm_exp_update3')
                       ->leftJoin('cm_passbook_volume','cm_exp_update3.cm_passbook_volume_id','cm_passbook_volume.id')
                       ->where('cm_exp_update3.id',$id)
                       ->first();
          $check = DB::table('cm_exp_update5')->where('invoice_no', $update3->invoice_no)->first();
                       //dd($update3);exit;
         // $update2_container= DB::table('cm_exp_update2_container')->where('cm_exp_update2_id',$id)
         //               ->get();
       return view("commercial/export/export_lc/entry_update_screen/export_lc_entry_update_screen3_edit", compact(
         "hr_unit",
         "update3",
         "hub",
         "passBookVol",
         "passBookPage",
         "check"
       ));
   }elseif ($request->isMethod('post')) {
     try {
      // dd($request-all());
       $cm_passbook_volume = DB::table('cm_passbook_volume')
                                   ->where('volume_no',$request->pass_book_vol_no)
                                   ->where('page_no',$request->pass_book_page_no)
                                   ->first();
  //dd($request->pass_book_page_no);exit;
       $update3 = DB::table('cm_exp_update3')->where('id',$id)->update([
             'hr_unit'                   => $request->unit,
             'invoice_no'                =>$request->invoiceno,
             'vessel_sail'               => $request->vessel_sail,
             'forwarder_name'            => $request->forwarder_name,
             'fcr_no'                    => $request->fcr_no,
             'fcr_date'                  =>$request->fcr_date,
             'ic_rec_date'               =>$request->ic_rec_date,
             'cm_hub_id'                 =>$request->hub,
             'mother_vessel'             => $request->mother_vessel,
             'voyage'                    =>$request->voyage,
             'etd_hub'                   => $request->etd_hub,
             'eta_destination'           => $request->eta_destination,
             'doc_sub_buyer'             => $request->doc_sub_to_buyer,
             'payment_inv_sd'            => $request->payment_invoice_sd,
             'bl_surrender_date'         => $request->bi_surrender_date,
             'f_amount'                  => $request->f_amount,
             'amount_currency'           => $request->f_amount_currancy,
             'marine_ins_value'          => $request->marine_insurance_value,
             'marine_ins_currency'       => $request->marine_insurance_value_currancy,
             'insurance_charge'          => $request->insurance_charge,
             'insurance_currency'        => $request->insurance_charge_currancy,
             'shipping_doc_courier_name' => $request->shipping_doc_courier_name,
             'shipping_doc_courier_no'   => $request->shipping_doc_courier_number,
             'shipping_doc_courier_date' => $request->shipping_doc_courier_date,
             'carrier_name'              => $request->carrier_name,
             'actual_fright_amt'         => $request->actual_freight_amt,
             'actual_fright_currency'    => $request->actual_freight_amt_currancy,
             'actual_fright_date'        => $request->actual_freight_date,
             'import_from_epz'           => $request->import_fabric_from_epz,
             'cm_passbook_volume_id'     => $cm_passbook_volume->id,

       ]);
        // }else{
        //   return back()->with("error", "Something went wrong.Please try again");
        // }
        return back()->with("success", "Save Successful.");
     } catch (\Exception $e) {
        return back()->with("error",$e->getMessage());
     }
   }

 }
 public function getEntryUpdateScreenList(){
   $unit = DB::table('hr_unit')
                 ->pluck('hr_unit_short_name','hr_unit_id');
    $buyer = [];
    $bank =[];
   return view("commercial/export/export_lc/entry_update_screen/export_lc_entry_update_screen2_list", compact(
     "unit",
     "buyer",
     "bank"
   ));

 }
 public function getEntryUpdateScreenList3(){
   $unit = DB::table('hr_unit')
                 ->pluck('hr_unit_short_name','hr_unit_id');
    $buyer = [];
    $bank =[];
   return view("commercial/export/export_lc/entry_update_screen/export_lc_entry_update_screen3_list", compact(
     "unit",
     "buyer",
     "bank"
   ));

 }
 public function getEntryUpdateScreenListData(){
   $data=  DB::table('cm_exp_update2')
              ->orderBy('id','DESC')
              ->get();


    //dd($data);exit;
        return DataTables::of($data)

      ->editColumn('action', function ($data) {
          $btn = "
              <a href=".url('commercial/export/export-lc-update-screen-edit/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  <i class=\"fa fa-pencil\"></i>
                  </a>
              </div>

              <button type=\"button\" class=\"btn btn-xs btn-danger delete\" data-id=\"$data->id\" data-toggle=\"tooltip\" title=\"delete\" >
                  <i class=\"fa fa-trash\"></i>
                  </button>  ";


          return $btn;
        })
      ->rawColumns(['action'])
      ->toJson();


 }

 public function getEntryUpdateScreenListData3(){
   $data=  DB::table('cm_exp_update3')
              ->orderBy('id','DESC')
              ->get();


    //dd($data);exit;
        return DataTables::of($data)

  /// Query for Action
      ->editColumn('action', function ($data) {
          $btn = "
              <a href=".url('commercial/export/export-lc-update-screen3-edit/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  <i class=\"fa fa-pencil\"></i>
                  </a>
              </div>

              <button type=\"button\" class=\"btn btn-xs btn-danger delete\" data-id=\"$data->id\" data-toggle=\"tooltip\" title=\"delete\" >
                  <i class=\"fa fa-trash\"></i>
                  </button>  ";


          return $btn;
        })
      ->rawColumns(['action'])
      ->toJson();


 }
 public function getEntryUpdateScreen3(){
   $hr_unit = DB::table('hr_unit')
                 ->pluck('hr_unit_name','hr_unit_id');
   $hub = DB::table('cm_hub')
              ->pluck('hub_name','id');

   $passBookVol = DB::table('cm_passbook_volume')
                      ->pluck('volume_no','volume_no');
   $passBookPage = DB::table('cm_passbook_volume')
                       ->pluck('page_no','page_no');

   return view("commercial/export/export_lc/entry_update_screen/export_lc_entry_update_screen3", compact(
     "hr_unit",
     "hub",
     "passBookVol",
     "passBookPage"
   ));

 }
 //entry update screen2 save
 public function entryUpdateScreenSave(Request $request){

     $validator = Validator::make($request->all(), [
         "unit"                                    => "required",
         "invoiceno"                               => "required",
         "ex_fty_date"                             => "required"/*,
         "shipping_company"                        => "required",
         "staffing_date"                           => "required",
         "feeder_vessel_name_no"                   => "required",
         "vessel_berth"                            => "required",
         "transport_doc_no_of_ship_co"             => "required",
         "transport_doc_date_of_ship_co"           => "required",
         "shipping_bill_no"                        => "required",
         "shipping_bill_date"                      => "required",
         "examine_date"                            => "required",
         "exp_release_date"                        => "required"*/
     ]);

   if ($validator->fails())
   {
       return back()
           ->withErrors($validator)
           ->withInput()
           ->with('error', "Incorrect Input!!");
   }
   else
   {
     $update2 = DB::table('cm_exp_update2')->insert([
           'unit_id'                 => $request->unit,
           'invoice_no'              => $request->invoiceno,
           'cnf_doc_dispatch_date'   => $request->cf_doc_dispatch_date,
           'ex_fty_date'             => $request->ex_fty_date,
           'agent_inv_no'            => $request->agent_invoice_no,
           'ship_company'            => $request->shipping_company,
           'staffing_date'           => $request->staffing_date,
           'feeder_vessel_details'   => $request->feeder_vessel_name_no,
           'vessel_berth'            => $request->vessel_berth,
           'transp_doc_no'           => $request->transport_doc_no_of_ship_co,
           'transp_doc_date'         => $request->transport_doc_date_of_ship_co,
           'ship_bill_no'            => $request->shipping_bill_no,
           'ship_bill_date'          => $request->shipping_bill_date,
           'examine_date'            => $request->examine_date,
           'exp_release_date'        => $request->exp_release_date,
           'final_date_status'       => isset($request->final_date)?1:0
         ]);

       if($update2){
         if(isset($request->data)){
          $containerDatas = $request->data;
          $update2Id = DB::getPdo()->lastInsertId('cm_exp_update2');

            foreach ($containerDatas as $containerData) {
              DB::table('cm_exp_update2_container')->insert([
                    'cm_exp_update2_id'  => $update2Id,
                    'container_no'       => $containerData['containerno'],
                    'size'               => $containerData['size'],
                    'container_sl'       => $containerData['containersi'],
                    'qty'                => $containerData['qty'],
                    'uom'                => $containerData['uom'],
                    'pkg'                =>$containerData['pkg']

              ]);
            }
          }
        }else{
          return back()->with("error", "Something went wrong.Please try again");
        }
      return back()->with("success", "Save Successful.");
   }
 }

 //entry update screen3 save
 public function entryUpdateScreen3Save(Request $request){
   // dd($request->all());exit;
     $validator = Validator::make($request->all(), [
         "unit"                            => "required",
         "invoiceno"                       => "required",
         "vessel_sail"                     => "required",
         "forwarder_name"                  => "required",
         "fcr_no"                          => "required",
         "fcr_date"                        => "required",
         "hub"                             => "required",
         "mother_vessel"                   => "required",
         "voyage"                          => "required",
         "etd_hub"                         => "required",
         "eta_destination"                 => "required",
         "doc_sub_to_buyer"                => "required",
         "payment_invoice_sd"              => "required",
         "bi_surrender_date"               => "required",
         "ic_rec_date"                     => "required",
         "f_amount"                        => "required",
         "f_amount_currancy"               => "required",
         "marine_insurance_value"          => "required",
         "marine_insurance_value_currancy" => "required",
         "insurance_charge"                => "required",
         "insurance_charge_currancy"       => "required",
         "shipping_doc_courier_name"       => "required",
         "shipping_doc_courier_number"     => "required",
         "shipping_doc_courier_date"       => "required",
         "carrier_name"                    => "required",
         "actual_freight_amt"              => "required",
         "actual_freight_amt_currancy"     => "required",
         "actual_freight_date"             => "required",
         "pass_book_vol_no"                => "required",
         "pass_book_page_no"               => "required",
     ]);

   if ($validator->fails())
   {
       return back()
           ->withErrors($validator)
           ->withInput()
           ->with('error', "Incorrect Input!!");
   }
   else
   {
     $cm_passbook_volume = DB::table('cm_passbook_volume')
                                 ->where('volume_no',$request->pass_book_vol_no)
                                 ->where('page_no',$request->pass_book_page_no)
                                 ->first();
//dd($request->pass_book_page_no);exit;
     $update3 = DB::table('cm_exp_update3')->insert([
           'hr_unit'                   => $request->unit,
           'invoice_no'                =>$request->invoiceno,
           'vessel_sail'               => $request->vessel_sail,
           'forwarder_name'            => $request->forwarder_name,
           'fcr_no'                    => $request->fcr_no,
           'fcr_date'                  =>$request->fcr_date,
           'ic_rec_date'               =>$request->ic_rec_date,
           'cm_hub_id'                 =>$request->hub,
           'mother_vessel'             => $request->mother_vessel,
           'voyage'                    =>$request->voyage,
           'etd_hub'                   => $request->etd_hub,
           'eta_destination'           => $request->eta_destination,
           'doc_sub_buyer'             => $request->doc_sub_to_buyer,
           'payment_inv_sd'            => $request->payment_invoice_sd,
           'bl_surrender_date'         => $request->bi_surrender_date,
           'f_amount'                  => $request->f_amount,
           'amount_currency'           => $request->f_amount_currancy,
           'marine_ins_value'          => $request->marine_insurance_value,
           'marine_ins_currency'       => $request->marine_insurance_value_currancy,
           'insurance_charge'          => $request->insurance_charge,
           'insurance_currency'        => $request->insurance_charge_currancy,
           'shipping_doc_courier_name' => $request->shipping_doc_courier_name,
           'shipping_doc_courier_no'   => $request->shipping_doc_courier_number,
           'shipping_doc_courier_date' => $request->shipping_doc_courier_date,
           'carrier_name'              => $request->carrier_name,
           'actual_fright_amt'         => $request->actual_freight_amt,
           'actual_fright_currency'    => $request->actual_freight_amt_currancy,
           'actual_fright_date'        => $request->actual_freight_date,
           'import_from_epz'           => $request->import_fabric_from_epz,
           'cm_passbook_volume_id'     => $cm_passbook_volume->id,

     ]);
     if($update3){
      return back()->with("success", "Save Successful.");
    }else{
      return back()->with("error", "Something Went Wrong Please Try Again");
    }
   }
 }

//get invoice no by unit id
 public function ajaxgetInvoiceNoByUnitId(Request $request){

   $result = DB::table('cm_exp_data_entry_1')
                ->where('unit_id',$request->unit_id )
                ->pluck('inv_no','inv_no');
   echo $result;exit;
   // return Response::json($result);

 }

 public function ajaxgetPassBookPageNoByPassBookVolNo(Request $request){

   $result = DB::table('cm_passbook_volume')
                ->where('volume_no',$request->pass_book_vol_no )
                ->pluck('page_no','page_no');
   echo $result;exit;
   // return Response::json($result);

 }

 //get agent code by invoice no
 public function ajaxgetAgentCodeByInvoiceNo(Request $request ){
   $result = DB::table('cm_exp_data_entry_1')
                 ->join('cm_agent','cm_exp_data_entry_1.cm_agent_id' ,'=','cm_agent.id')
                 ->where('cm_exp_data_entry_1.inv_no',$request->invoiceno )
                 ->select('cm_agent.agent_name as agent_name')
                 ->first();

    $agentName = $result->agent_name;
    $agentInvoiceNo = strtoupper(substr($agentName, 0,2)).$this->randomnum(5);

   echo $agentInvoiceNo;exit;
 }

 public function entryUpdateScreen2Delete(Request $request){
    // try {
    //
    // } catch (\Exception $e) {
    //   return json_encode($e->getMessage());
    // }

    if(!empty($request->id)){
      DB::beginTransaction();
      $entry2 = DB::table('cm_exp_update2')->where('id',$request->id)->delete();
      if($entry2){
         $container2 = DB::table('cm_exp_update2_container')->where('cm_exp_update2_id',$request->id)->delete();
          if($container2){
            DB::commit();
            return 1;
          }else{
            DB::rollback();
            return 0;
          }

      }else{
        return 0;
      }
      // return json_encode($entry2);
    }else{
      return 0;
    }


 }

 public function entryUpdateScreen3Delete(Request $request){

    if(!empty($request->id)){
      $entry2 = DB::table('cm_exp_update3')->where('id',$request->id)->delete();
      if($entry2){
        return 1;
      }else{
        return 0;
      }
    }else{
      return 0;
    }


 }

 function randomnum($length)
    {
        $randstr = "";
        srand((double)microtime() * 1000000);
        //our array add all letters and numbers if you wish
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        for ($rand = 0; $rand <= $length; $rand++) {
            $random = rand(0, count($chars) - 1);
            $randstr .= $chars[$random];
        }
        return $randstr;
    }

}
