<?php

namespace App\Http\Controllers\Commercial\Import\PI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Supplier;
use App\Models\Commercial\BTBEntry;
use App\Models\Commercial\BTBAmend;
use App\Models\Commercial\CmPiMaster;
use App\Models\Commercial\PiForwardMaster;
use App\Models\Commercial\PiForwardDetails;
use DB, ACL, DataTables, Auth, Validator;

class PiForwardingController extends Controller
{    

  public function showForm()
    {     

      $fileList= DB::table('cm_file')->where('file_type', 1)->where('status', 1)->pluck('file_no', "id");
        $paymentTypeList= DB::table('cm_payment_type')->pluck('type_name', 'id');
        // $supplierList= Supplier::pluck('sup_name', 'sup_id');
        $supplierList= [];
        $termList= DB::table('cm_inco_term')->pluck('name', 'id');
      $periodList= DB::table('cm_period')->pluck('period_name', 'id');
        $dateList= DB::table('cm_from_date')->pluck('from_date', 'id');
        $lcTypeList= DB::table('cm_lc_type')->pluck('lc_type_name', 'id');
      return view('commercial/import/pi/pi_forwarding', compact('fileList', 'paymentTypeList', 'supplierList', 'termList', 'periodList', 'dateList', 'lcTypeList'));

      
    }

    public function showList()
    {
        try{
            return view("commercial/import/pi/pi_forwarding_list");
        }catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getListData(){

    $data = PiForwardMaster::orderBy('id','DESC')->get();


    return DataTables::of($data)->addIndexColumn()
      ->addColumn('file_no', function($data){
          return DB::table('cm_file')->where('id',$data->cm_file_id)->first()->file_no??'';
      })
      ->addColumn('supplier', function($data){
          return Supplier::where('sup_id',$data->mr_supplier_sup_id)->first()->sup_name??'';
      })
      ->addColumn('pi', function($data){
          $poDetails = DB::table('cm_pi_forwarding_details AS pfd')
                        ->where('pfd.cm_pi_forwarding_master_id',$data->id)
                        ->leftJoin('cm_pi_master As pm','pm.id','pfd.cm_pi_master_id')
                        ->pluck('pi_no');

            $poText = '';
            foreach($poDetails as $po) {
              $poText .= '<span class="label label-info arrowed-right arrowed-in">'.$po.'</span> <br>';
            }
        return $poText;
      })
      ->addColumn('action', function($data){
          $action_buttons= "<div class=\"btn-group\">
              <a href=".url('commercial/import/pi/pi_forwarding/edit/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\">
                  <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
              </a>
              <a href=".url('commercial/import/pi/pi_forwarding/delete/'.$data->id)." onclick=\"return confirm('Are you sure you want to delete?')\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"View\">
                  <i class=\"ace-icon fa fa-trash bigger-120\"></i>
              </a> ";
          $action_buttons.= "</div>";
          return $action_buttons;
      })
      ->rawColumns(['action','file_no','supplier','pi'])
      ->toJson();
        

  }


    public function saveForm(Request $request)
    {
        $validator= Validator::make($request->all(), [
          'ref_no'         => 'required',
          'cm_file_id'       => 'required',
          'mr_supplier_sup_id'   => 'required',
          'cm_payment_type_id'  => 'required',
          'lc_status'     => 'required',
          'cm_inco_term_id'     => 'required',
          'lc_amount'         => 'required',
          'lc_currency'       => 'required',
          'insurance_no'   => 'required',
          'cover_date'  => 'required',
          'lca_no'     => 'required',
          'shipment_date'     => 'required',
          'expiry_date'         => 'required',
          'cm_period_id'       => 'required',
          'cm_from_date'   => 'required',
          'cm_lc_type_id'  => 'required',
          'interest'     => 'required'
        ]);
          //If Validation fails back to previous psage
        if($validator->fails()){
          return back()
            ->withInput()
            ->with('error', "Please fill the required fields!!");
        }
        try {
          $piForwardMaster = new PiForwardMaster();;
          $piForwardMaster->ref_no = $request->ref_no;
          $piForwardMaster->cm_file_id = $request->cm_file_id;
          $piForwardMaster->mr_supplier_sup_id = $request->mr_supplier_sup_id;
          $piForwardMaster->cm_payment_type_id = $request->cm_payment_type_id;
          $piForwardMaster->lc_status = $request->lc_status;
          $piForwardMaster->cm_inco_term_id = $request->cm_inco_term_id;
          $piForwardMaster->lc_amount = $request->lc_amount;
          $piForwardMaster->lc_currency = $request->lc_currency;
          $piForwardMaster->remarks = $request->remarks;
          $piForwardMaster->insurance_no = $request->insurance_no;
          $piForwardMaster->cover_date = $request->cover_date;
          $piForwardMaster->lca_no = $request->lca_no;
          $piForwardMaster->shipment_date = $request->shipment_date;
          $piForwardMaster->expiry_date = $request->expiry_date;
          $piForwardMaster->cm_period_id = $request->cm_period_id;
          $piForwardMaster->cm_from_date = $request->cm_from_date;
          $piForwardMaster->cm_lc_type_id = $request->cm_lc_type_id;
          $piForwardMaster->interest = $request->interest;
          $piForwardMaster->save();
          //dd( $piForwardMaster->id);
          if($piForwardMaster->id){
            foreach ($request->forwarding_pi as $key => $fpi) {
                $piForwardDetails = new PiForwardDetails();
                $piForwardDetails->cm_pi_master_id = $fpi;
                $piForwardDetails->cm_pi_forwarding_master_id = $piForwardMaster->id;
                $piForwardDetails->save();
                
                $cmPiMaster = CmPiMaster::find($fpi);
                $cmPiMaster->cm_file_id = $request->cm_file_id;
                $cmPiMaster->save();
            }
            $this->logFileWrite("Proforma Invoice created", $piForwardMaster->id);
            return redirect('commercial/import/pi/pi_forwarding/edit/'.$piForwardMaster->id)->with('success', "PI Forwarding Created successfully!!");

          }else{
            return back()->withInput()->with('error', 'PI Forwarding not saved');
          }

        }
        catch(\Exception $e) {
          return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        //dd($request->all());
        $validator= Validator::make($request->all(), [
          'cm_payment_type_id'  => 'required',
          'lc_status'     => 'required',
          'cm_inco_term_id'     => 'required',
          'lc_amount'         => 'required',
          'lc_currency'       => 'required',
          'insurance_no'   => 'required',
          'cover_date'  => 'required',
          'lca_no'     => 'required',
          'shipment_date'     => 'required',
          'expiry_date'         => 'required',
          'cm_period_id'       => 'required',
          'cm_from_date'   => 'required',
          'cm_lc_type_id'  => 'required',
          'interest'     => 'required'
        ]);
          //If Validation fails back to previous psage
        if($validator->fails()){
          return back()
            ->withInput()
            ->with('error', "Please fill the required fields!!");
        }
        try {
          $piForwardMaster = PiForwardMaster::find($request->pif_id);
          $piForwardMaster->cm_payment_type_id = $request->cm_payment_type_id;
          $piForwardMaster->lc_status = $request->lc_status;
          $piForwardMaster->cm_inco_term_id = $request->cm_inco_term_id;
          $piForwardMaster->lc_amount = $request->lc_amount;
          $piForwardMaster->lc_currency = $request->lc_currency;
          $piForwardMaster->remarks = $request->remarks;
          $piForwardMaster->insurance_no = $request->insurance_no;
          $piForwardMaster->cover_date = $request->cover_date;
          $piForwardMaster->lca_no = $request->lca_no;
          $piForwardMaster->shipment_date = $request->shipment_date;
          $piForwardMaster->expiry_date = $request->expiry_date;
          $piForwardMaster->cm_period_id = $request->cm_period_id;
          $piForwardMaster->cm_from_date = $request->cm_from_date;
          $piForwardMaster->cm_lc_type_id = $request->cm_lc_type_id;
          $piForwardMaster->interest = $request->interest;
          $piForwardMaster->save();

          $forwardPis = PiForwardDetails::where('cm_pi_forwarding_master_id', $request->pif_id)
                        ->pluck('cm_pi_master_id')
                        ->toArray();
          $to_insert = array_diff($request->forwarding_pi, $forwardPis);
          $to_delete = array_diff($forwardPis, $request->forwarding_pi);

          //dd($forwardPis,$to_delete, $to_insert);

          //insert new
           if(count($to_insert)>0){
            foreach ($to_insert as $key => $fpi) {
                $piForwardDetails = new PiForwardDetails();
                $piForwardDetails->cm_pi_master_id = $fpi;
                $piForwardDetails->cm_pi_forwarding_master_id = $request->pif_id;
                $piForwardDetails->save();
                
                $cmPiMaster = CmPiMaster::find($fpi);
                $cmPiMaster->cm_file_id = $piForwardMaster->cm_file_id;
                $cmPiMaster->save();
            }
           }
           if(count($to_delete)>0){
            foreach ($to_delete as $key => $fpi) {
                PiForwardDetails::where('cm_pi_forwarding_master_id', $request->pif_id)
                    ->where('cm_pi_master_id',$fpi)
                    ->delete();
                    
                $cmPiMaster = CmPiMaster::find($fpi);
                $cmPiMaster->cm_file_id = null;
                $cmPiMaster->save();
            }

           }

            if(isset($request->btb_id)){
              $amendment= new BTBAmend();
              $amendment->amend_no  = $request->am_amend_id;
              $amendment->amend_date  = $request->am_amend_date;
              $amendment->amend_reason  = $request->am_reason;
              $amendment->amend_value  = $request->am_amend_value;
              $amendment->last_ship_date  = $request->am_ship_date;
              $amendment->expiry_date  = $request->am_expiry_date;
              $amendment->lca_no  = $request->am_lca_no;
              $amendment->amend_remark  = $request->am_remark;
              $amendment->amend_total_lc_amount  = $request->am_total_amount;
              $amendment->marine_ins_no  = $request->marine_ins_no;
              $amendment->marine_ins_cover_date  = $request->marine_ins_cover_date;
              $amendment->cm_btb_id  = $request->btb_id;
              $amendment->cm_pi_forwarding_master_id  = $request->pif_id;
              $amendment->last_pi_lc_amount = $request->lc_amount;
              $amendment->save();
            }

            $this->logFileWrite("PI Forwarding updated", $piForwardMaster->id);
            return redirect('commercial/import/pi/pi_forwarding/edit/'.$piForwardMaster->id)->with('success', "PI Forwarding Updated successfully!!");
        }
        catch(\Exception $e) {
          return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {  

        $piForwardMaster = PiForwardMaster::where('id',$id)->first();
        $forwardPis = PiForwardDetails::where('cm_pi_forwarding_master_id', $id)
                        ->pluck('cm_pi_master_id')
                        ->toArray();
        $allPis = PiForwardDetails::pluck('cm_pi_master_id')->toArray();
        $existPis = array_diff($allPis, $forwardPis);
        
        $masterPis= DB::table('cm_exp_lc_entry as a')
                            ->select([
                                "a.cm_file_id as aid",
                                "a.cm_sales_contract_id",
                                "b.mr_order_entry_order_id",
                                "c.cm_pi_master_id",
                                "d.pi_no",
                                "d.mr_supplier_sup_id as sup_id",
                                "d.id",
                                "d.total_pi_qty",
                                "d.total_pi_value",
                                "d.cm_file_id as did",
                                "e.sup_name"
                            ])
                            ->leftJoin('cm_sales_contract_order as b', 'a.cm_sales_contract_id','=','b.cm_sales_contract_id')
                            ->leftJoin('cm_pi_bom as c','c.mr_order_entry_order_id','=','b.mr_order_entry_order_id')
                            ->leftJoin('cm_pi_master as d','d.id','=','c.cm_pi_master_id')
                            ->leftJoin('mr_supplier as e','e.sup_id','=','d.mr_supplier_sup_id')
                            ->where('a.cm_file_id', $piForwardMaster->cm_file_id)
                            ->get()->where('sup_id',$piForwardMaster->mr_supplier_sup_id)->whereNotIn('id',$existPis)->unique('cm_pi_master_id');

        $btbExist = BTBEntry::where('cm_pi_forwarding_master_id',$id)->first();
        $btbInfo = [];
        if($btbExist){
          $btbInfo['btb'] = $btbExist;
          $btbInfo['lastAmend'] = BTBAmend::where('cm_btb_id',$btbExist->id)
                                    ->orderBy('id','DESC')
                                    ->first();

          $btbInfo['amendments']= BTBAmend::where('cm_btb_id', $btbExist->id)
                              ->where('cm_pi_forwarding_master_id',$id)
                              ->get();

          $btbInfo['last']= BTBAmend::where('cm_btb_id', $btbExist->id)
                              ->where('cm_pi_forwarding_master_id',$id)
                              ->orderBy('id','DESC')
                              ->first()->last_pi_lc_amount??0;
          
        }

        //dd($btbInfo);


        $piList = $this->getPiList($masterPis,$forwardPis, $btbExist);


        $fileList= DB::table('cm_file')->where('file_type', 1)->where('status', 1)->pluck('file_no', "id");
        $paymentTypeList= DB::table('cm_payment_type')->pluck('type_name', 'id');
        $supplierList= Supplier::pluck('sup_name', 'sup_id');
        $termList= DB::table('cm_inco_term')->pluck('name', 'id');
        $periodList= DB::table('cm_period')->pluck('period_name', 'id');
        $dateList= DB::table('cm_from_date')->pluck('from_date', 'id');
        $lcTypeList= DB::table('cm_lc_type')->pluck('lc_type_name', 'id');
        return view('commercial/import/pi/pi_forwarding_edit', compact('piForwardMaster','fileList', 'paymentTypeList', 'supplierList', 'termList', 'periodList', 'dateList', 'lcTypeList','piList','forwardPis','btbInfo'));
    }


    public function cancelCloseInfo(Request $request){

        $lc_sum=0;
        $last_cancel_date= null;
        $is_file_closed= null;
        $file_close_date= null;


        //chech whether file is closed or not
        $is_file_closed= DB::table('cm_file')
                            ->where('id', $request->file_no)
                            ->pluck('status')
                            ->first();

        //if file is not closed check for cancelled date and amount
        if($is_file_closed==1){

            //get the "cancelled id's" from "cm_btb" table
            //this is the primary key against a file number
            $cancelled_btbs= DB::table('cm_btb')
                        ->where('cm_file_id', $request->file_no)
                        ->where('lc_active_status', 0)
                        ->pluck('id AS btb_id')
                        ->toArray();

            //get cancelled amount
            for($i=0; $i<sizeof($cancelled_btbs); $i++){
                $lc_sum+= DB::table('cm_btb_amend')
                                    ->where('cm_btb_id', $cancelled_btbs[$i])
                                    ->orderBy('id', "DESC")
                                    ->pluck('amend_total_lc_amount')
                                    ->first();

                //last Cancelled date
                if($i== (sizeof($cancelled_btbs)-1)){
                    $last_cancel_date = DB::table('cm_btb')
                                        ->where('id', $cancelled_btbs[$i])
                                        ->pluck('cancel_date')
                                        ->first();
                }
            }
        }

           

             $allPis = PiForwardDetails::pluck('cm_pi_master_id')->toArray();
             $masterPis= DB::table('cm_exp_lc_entry as a')
                            ->select([
                                "a.cm_file_id as aid",
                                "a.cm_sales_contract_id",
                                "b.mr_order_entry_order_id",
                                "c.cm_pi_master_id",
                                "d.pi_no",
                                "d.mr_supplier_sup_id as sup_id",
                                "d.id",
                                "d.total_pi_qty",
                                "d.total_pi_value",
                                "d.cm_file_id as did",
                                "e.sup_name"
                            ])
                            ->leftJoin('cm_sales_contract_order as b', 'a.cm_sales_contract_id','=','b.cm_sales_contract_id')
                            ->leftJoin('cm_pi_bom as c','c.mr_order_entry_order_id','=','b.mr_order_entry_order_id')
                            ->leftJoin('cm_pi_master as d','d.id','=','c.cm_pi_master_id')
                            ->leftJoin('mr_supplier as e','e.sup_id','=','d.mr_supplier_sup_id')
                            ->where('a.cm_file_id', $request->file_no)
                            ->get()->unique('cm_pi_master_id');
            $pi_raws = "";
            $pi_sup = [];
            foreach ($masterPis as $master) {
                if($master->pi_no != '' || $master->pi_no != null){
                    if(in_array($master->id, $allPis)){
                      $style = "style =color:#e83838;";
                    }else{
                      $style ='';
                    }
                    $pi_raws.='<tr '.$style.'> 
                    <td>'.$master->pi_no.'</td>
                    <td>'.$master->total_pi_qty.'</td>
                    <td>'.$master->total_pi_value.'</td>
                    <td></td>
                    </tr>';
                    $pi_sup[$master->sup_id] = $master->sup_name;
                } else {
                  $pi_raws .= '<tr><td rowspan="4">No Pi Found</td></tr>';
                }
            }
            $data = array();

        $data[0]= $lc_sum;
        $data[1]= $last_cancel_date;
        $data[2]= $is_file_closed;
        $data[3]= $last_cancel_date;
        // $data[4]= $supplierList;
        $data['piData'] = $pi_raws;

        $data['supData'] = $pi_sup;
        return json_encode($data);
    }

    public function piMasterInfo(Request $request){

      //dd($request->all());exit;
        $existPis = PiForwardDetails::pluck('cm_pi_master_id')->toArray();
        $masterPis= DB::table('cm_exp_lc_entry as a')
                            ->select([
                                "a.cm_file_id as aid",
                                "a.cm_sales_contract_id",
                                "b.mr_order_entry_order_id",
                                "c.cm_pi_master_id",
                                "d.pi_no",
                                "d.mr_supplier_sup_id as sup_id",
                                "d.id",
                                "d.total_pi_qty",
                                "d.total_pi_value",
                                "d.cm_file_id as did",
                                "e.sup_name"
                            ])
                            ->leftJoin('cm_sales_contract_order as b', 'a.cm_sales_contract_id','=','b.cm_sales_contract_id')
                            ->leftJoin('cm_pi_bom as c','c.mr_order_entry_order_id','=','b.mr_order_entry_order_id')
                            ->leftJoin('cm_pi_master as d','d.id','=','c.cm_pi_master_id')
                            ->leftJoin('mr_supplier as e','e.sup_id','=','d.mr_supplier_sup_id')
                            ->where('a.cm_file_id', $request->file)
                            ->get()->where('sup_id',$request->supplier)->whereNotIn('id',$existPis)->unique('cm_pi_master_id');

        $forwardPis = [];
        $pi_raws= "";
        $pi_raws .= $this->getPiList($masterPis,$forwardPis,0);
        return $pi_raws;
    }
    public function delete($id){
        $dM = PiForwardMaster::find($id)->delete();
        if($dM){
            PiForwardDetails::where('cm_pi_forwarding_master_id',$id)->delete();
            $this->logFileWrite("Proforma Invoice deleted", $id);
            return redirect('commercial/import/pi/pi-forwarding-list/')->with('success', "PI Forwarding Deleted successfully!!");
        }else{
            return redirect('commercial/import/pi/pi-forwarding-list/')->with('error', "PI Forwarding Not deleted!");            
        }

    }

    public function getPiList($masterPis,$forwardPis,$btbExist)
    {


      return view("commercial.import.pi.ajax_get_pi_items", compact('masterPis','forwardPis','btbExist'))->render();
    }
    
    public function CheckForwardPi(Request $request)
    {
        $status = 'no';
        try {
          $getPi = PiForwardMaster::where('ref_no',$request->ref_no)->first();
          if($getPi){
            $status = 'yes';
          }
          return $status;
        } catch (\Exception $e) {
          return $status;
        }
    }

     public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." ".Auth()->user()->associate_id." \"".$message."\" ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);
    }
}
