<?php
namespace App\Http\Controllers\Commercial\Export\ExportLc;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Merch\Buyer;
use App\Models\Commercial\Bank;
use App\Models\Commercial\BankAccNo;
use App\Models\Commercial\SalesContract;
use App\Models\Commercial\SalesContractAmend;
use App\Models\Commercial\SalesContractOrder;
use App\Models\Merch\Country;
use App\Models\Merch\OrderEntry;
use App\Models\Hr\Unit;

use App\Models\Commercial\ExpLcEntry;
use App\Models\Commercial\ExpLcAmmendment;
use App\Models\Commercial\ExpLcAddress;
use App\Models\Commercial\CFile;

use Validator, DB, ACL, Auth, DataTables;

class FileProfileController extends Controller
{
  public function fileProfileView($file_id, $sc_id)
  {
    $file=DB::table('cm_file')->where('id',$file_id)->first();
    $sales_contract=DB::table('cm_sales_contract')->where('id',$sc_id)->first();
    $pi_info=DB::table('cm_pi_master')->where('cm_file_id',$file_id)->get();

    $sales_order = DB::table('cm_sales_contract_order AS co')
                      ->select(
                          "co.*",
                          "m.order_code",
                          "m.order_id",
                          "m.order_qty",
                          "b.agent_fob",
                          "a.amend_no"
                      )
                      ->leftJoin('mr_order_entry AS m', 'm.order_id', '=', 'co.mr_order_entry_order_id')
                      ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'm.order_id')
                      ->leftJoin('cm_sales_contract_amend as a', 'a.id', 'co.cm_sales_contract_amend_id')
                      ->where('co.cm_sales_contract_id', $sc_id)
                      ->get();
     // dd($sales_order);
  return view('commercial/export/export_lc/file_profile/file_profile_view', compact('file','sc_id','sales_contract','pi_info','sales_order'));
  }

  public function btbData(Request $request){

        //get list data from cm_btb table
        $data= DB::table('cm_btb AS b')
                ->select([
                    "b.id",
                    "b.cm_file_id",
                    "b.mr_supplier_sup_id",
                    "b.lc_no",
                    "b.lc_status",
                    "b.lc_date",
                    "b.cm_lc_type_id",
                    "b.lc_active_status",
                    "f.file_no",
                    "s.sup_name",
                    "l.lc_type_name",
                ])
                ->leftJoin('cm_file AS f', 'f.id', 'b.cm_file_id')
                ->leftJoin('mr_supplier AS s', 's.sup_id', 'b.mr_supplier_sup_id')
                ->leftJoin('cm_lc_type AS l', 'l.id', 'b.cm_lc_type_id')
                ->where('b.cm_file_id',$request->file)
                ->orderBy('b.id','DESC')
                ->get();
        // dd($request->file);

        return DataTables::of($data)->addIndexColumn()
                ->editColumn('lc_active_status', function($data){
                    if($data->lc_active_status == 1)
                        return "Active";
                    else
                        return "Cancel";
                })
                
                ->toJson();
    }
}
