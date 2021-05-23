<?php
namespace App\Http\Controllers\Commercial\Export;

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
use App\Models\Commercial\ExpDataPO;
use App\Models\Hr\Unit;
use Validator, DB, ACL, Auth, DataTables, Response;



class ExportTrackingController extends Controller
{
	public function index(){
		return view("commercial/export/export_tracking");
	}

	public function invoiceProgress(){
		$invoice = ExpDataEntry::orderBy('id','DESC')->get();
		/*<a target='_blank' href=".url('commercial/export/export-lc-update-screen?id=').$base_data->id."  data-toggle=\"tooltip\" title=\"Export Data Entry-1A\" class=\"btn btn-info btn-sm\">
                          <i class=\"fa fa-file-text-o\"></i>
                    </a>

        <a target='_blank' href=".url('commercial/export/export_data_entry_1a_edit/'.$data->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  <i class=\"fa fa-pencil\"></i>
                  </a>
              </div>
*/

		return DataTables::of($invoice)
				->editColumn('inv_no', function ($invoice) {
                    $screen1 = "<a target='_blank' href=".url('commercial/export/export_data_entry_1a_edit/'.$invoice->id )."  data-toggle=\"tooltip\" title=\"Value: ".$invoice->inv_value."\">".$invoice->inv_no."</a>";
                    return $screen1;
                })
                ->addColumn('order_code', function ($invoice) {
                	$order = ExpDataPO::where('cm_exp_data_entry_1_id',$invoice->id)
                			 ->leftJoin('mr_order_entry as o','o.order_id','cm_exp_entry1_po.mr_order_entry_order_id')
                			 ->pluck('order_code');

                    $code = '';
                    foreach($order as $o) {
                      $code .= $o.'<br>';
                    }
                    return $code;
                })
                ->addColumn('screen1', function ($invoice) {
                    $screen1 = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/export_data_entry_1a_edit/'.$invoice->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";
                    return $screen1;
                })
                ->addColumn('screen2', function ($invoice) {
                	$screen2 = DB::table('cm_exp_update2')->where('invoice_no',$invoice->inv_no)->first();
                	if($screen2){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/export-lc-update-screen-edit/'.$screen2->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/export-lc-update-screen?invoice=').$invoice->inv_no.'&&unit='.$invoice->unit_id."  data-toggle=\"tooltip\" title=\"Export Data Entry Update  2\" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('screen3', function ($invoice) {
                	$screen3 = DB::table('cm_exp_update3')->where('invoice_no',$invoice->inv_no)->first();
                	if($screen3){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/export-lc-update-screen3-edit/'.$screen3->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/export-lc-update-screen3?invoice=').$invoice->inv_no.'&&unit='.$invoice->unit_id."  data-toggle=\"tooltip\" title=\"Export Data Entry Update  3\" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('screen5', function ($invoice) {
                	$screen5 = DB::table('cm_exp_update5')->where('invoice_no',$invoice->inv_no)->first();
                	if($screen5){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/exportLcEntry_edit/'.$screen5->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/exportLcEntry?invoice=').$invoice->inv_no.'&&unit='.$invoice->unit_id."  data-toggle=\"tooltip\" title=\"Export Data Entry Update  5\" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('fabric', function ($invoice) {
                	$fabric = DB::table('cm_exp_fabric_consumption')->where('invoice_no',$invoice->inv_no)->first();
                	$view ='Edit Page nai';
                	if($fabric){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/expFabricConsEntry_list?invoice='.$invoice->inv_no)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/expFabricConsEntry?invoice=').$invoice->inv_no.'&&file='.$invoice->cm_file_id.'&&unit='.$invoice->unit_id."  data-toggle=\"tooltip\" title=\"Export Fabric Consumption\" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('freight', function ($invoice) {
                	$freight = DB::table('cm_freight_charge_child')->where('invoice_no',$invoice->inv_no)->first();

                	if($freight){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/freight_charge_list?invoice='.$invoice->inv_no )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/freight_charge?invoice=').$invoice->inv_no.'&&file='.$invoice->cm_file_id."   data-toggle=\"tooltip\" title=\"Freight Charge\" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('bill_air', function ($invoice) {
                	$bill_air = DB::table('cm_export_bill_air')->where('inv_no',$invoice->inv_no)->first();
                	if($bill_air){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/export_bill_air_edit/'.$bill_air->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/export_bill_air?invoice=').$invoice->inv_no.'&&file='.$invoice->cm_file_id."  data-toggle=\"tooltip\" title=\"Export Bill(Air) \" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('bill_sea', function ($invoice) {
                	$bill_sea = DB::table('cm_export_bill_sea')->where('inv_no',$invoice->inv_no)->first();
                	if($bill_sea){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/export_bill_sea_edit/'.$bill_sea->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/export_bill_sea?invoice=').$invoice->inv_no.'&&file='.$invoice->cm_file_id."  data-toggle=\"tooltip\" title=\"Export Bill(Sea) \" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('cash', function ($invoice) {
                	$cash = DB::table('cm_cash_incentive_child')->where('invoice_no',$invoice->inv_no)->first();
                	if($cash){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/cash_incentivelist?invoice='.$invoice->inv_no )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                  		<i class=\"fa fa-pencil\"></i>
                  		</a>";

                	}else{

	                    $view = "<a target='_blank' href=".url('commercial/export/cash_incentive?invoice=').$invoice->inv_no.'&&file='.$invoice->cm_file_id."  data-toggle=\"tooltip\" title=\"Cash Incentive\" class=\"btn btn-danger btn-sm\">
	                          <i class=\"fa fa-file-text-o\"></i>
	                    	</a>";
                	}
                    return $view;
                })
                ->addColumn('exp_prc', function ($invoice) {
                	$prc = DB::table('cm_prc_correction')->where('invoice_no',$invoice->inv_no)->first();
                	if($prc){
                		$view = "<i class='ace-icon fa fa-check'></i> | <a target='_blank' href=".url('commercial/export/export_prc_correction?invoice='.$invoice->inv_no )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Export PRC Correction\">
		              		<i class=\"fa fa-pencil\"></i>
		              		</a>";
                	}else{
	            		$view = " <a target='_blank' href=".url('commercial/export/export_prc_correction?invoice='.$invoice->inv_no )." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Export PRC Correction\">
	              		<i class=\"fa fa-file-text-o\"></i>
	              		</a>";
                	}

                	
                    return $view;
                })
                ->rawColumns(['inv_no','order_code','screen1','screen2','screen3','screen5','fabric','freight','bill_air','bill_sea','cash','exp_prc'])
                ->make(true);

	}
}