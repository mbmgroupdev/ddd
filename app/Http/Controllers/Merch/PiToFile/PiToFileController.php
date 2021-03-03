<?php

namespace App\Http\Controllers\Merch\PiToFile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Supplier;
use App\Models\Commercial\CmPiMaster;
use DB, ACL, DataTables, Auth, Validator;

class PiToFileController extends Controller
{
    public function showForm()
    {     

      $fileList= DB::table('cm_file')->where('file_type', 1)->where('status', 1)->pluck('file_no', "id");

      $tableData=DB::table('cm_pi_master as a')
              ->select('a.pi_no', 'a.cm_file_id','b.file_no','a.total_pi_qty','c.sup_name')
              ->leftJoin('cm_file as b','b.id','=','a.cm_file_id')
              ->leftJoin('mr_supplier as c','c.sup_id','=','a.mr_supplier_sup_id')
              ->get();
      //dd($tableData);
        

      return view('merch/pi_to_file/pi_to_file_preview', compact('fileList','tableData'));

      
    } 

    //get PI BOM information
    public function piBomInfo(Request $request){

      //dd($request->all());exit;

        $masterPis= DB::table('cm_exp_lc_entry as a')
                        ->select([
                            "a.cm_file_id as aid",
                            "a.cm_sales_contract_id",
                            "b.mr_order_entry_order_id",
                            "c.cm_pi_master_id",
                            "d.pi_no",
                            "d.id",
                            "d.cm_file_id as did"
                        ])
                        ->leftJoin('cm_sales_contract_order as b', 'a.cm_sales_contract_id','=','b.cm_sales_contract_id')
                        ->leftJoin('cm_pi_bom as c','c.mr_order_entry_order_id','=','b.mr_order_entry_order_id')
                        ->leftJoin('cm_pi_master as d','d.id','=','c.cm_pi_master_id')
                        ->where('a.cm_file_id', $request->file)
                        ->get()->unique('cm_pi_master_id');
        // dd($masterPis);exit;

        $pi_raws= "";
        foreach ($masterPis as $master) {
            if($master->pi_no != '' || $master->pi_no != null){
              if($master->aid == $master->did){
                $pi_raws.='<tr> 
                <td>'.$master->pi_no.'</td>
                <td><input type="checkbox" name="pi_master_id[]" value="'.$master->id.'" checked="checked" class="check_val"/> 
                </td>
                </tr>';
              }
              else{
                $pi_raws.='<tr> 
                <td>'.$master->pi_no.'</td>
                <td><input type="checkbox" name="pi_master_id[]" value="'.$master->id.'" class="check_val"/></td>
                </tr>';
              }
            }
        }
        $data = array();
        
        $data = $pi_raws;

        return $data;
    }

    public function updatePiToFile(Request $request){
        
      $previous = DB::table('cm_pi_master')
                  ->where('cm_file_id','=',$request->cm_file_id)
                  ->pluck('id')->toArray();

      if($request->pi_master_id == ""){
        $new = [];
      }
      else{
        $new = $request->pi_master_id;
      }
      $vanish = array_diff($previous,$new);
  
      // dd($previous, $new, $vanish);
      // dd($request->all());exit;

      if($vanish != ""){
        foreach ($vanish as $van) {
              DB::table('cm_pi_master')->where('id', $van)->update([
                      'cm_file_id' => null
              ]);
        }  
      }
      if($request->pi_master_id != ''){
        foreach ($request->pi_master_id as $pi_masters) {
              DB::table('cm_pi_master')->where('id', $pi_masters)->update([
                      'cm_file_id' => $request->cm_file_id
              ]);
        }
      }
      return back()->with('success', 'PI Master Updated with File');
    }
}
