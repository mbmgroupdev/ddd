<?php

namespace App\Http\Controllers\Inventory\Inspection;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Buyer;
use App\Models\Merch\Supplier;
use App\Models\Commercial\Item;
use App\Models\Inventory\StInspectionMaster;
use App\Models\Inventory\AccessoriesCheckPoints;
use App\Models\Inventory\AccessoriesCheckPointsDetails;
use App\Models\Inventory\InspectionDetailsAccessories;
use App\Models\Inventory\StRawMaterialItemReceive;
use App\Models\Inventory\StRawMaterialReceive;
Use DB, ACL, Validator, DataTables,DateTime;


class AccessoriesInspectionController extends Controller
{
  # Accessories Inspection form 
    public function accessoriesInspection($grnNo, $itemId)
    {

    	try {

          $stMaterial = StRawMaterialReceive::where('grn_no',$grnNo)->first();
            if(isset($stMaterial->id)) {
            $stMaterialItem = StRawMaterialItemReceive::where(['st_raw_material_receive_id' => $stMaterial->id, 'mr_cat_item_id' => $itemId])->first();

                if(isset($stMaterialItem->id)) {
                  $data['getCheckPoints'] = AccessoriesCheckPoints::getCheckPointsList();
                  //dd($data);
                  $data['item'] =DB::table('st_raw_material_item_receive AS ri') 
                        ->select(

                            "ri.receive_qty",
                            "ri.id AS raw_mat_item_rcv_id",
                            "r.cm_imp_invoice_id",
                            "r.id AS raw_material_id",
                            "ci.*",
                            "mc.item_name",
                            "d.qty AS invoice_qty",                                              
                            "f.file_no",
                            "o.order_qty",                  
                            "o.order_code",
                            "b.b_name",
                            "sup.sup_name" 

                        )
                       ->leftJoin('st_raw_material_receive AS r', 'r.id', '=', 'ri.st_raw_material_receive_id')

                       ->leftJoin('mr_cat_item AS mc', 'mc.id', '=', 'ri.mr_cat_item_id')

                       ->leftJoin('cm_imp_invoice AS ci', 'ci.id', '=', 'r.cm_imp_invoice_id')

                       ->leftJoin('cm_imp_data_entry AS d', 'd.id', '=', 'ci.cm_imp_data_entry_id')

                       ->leftJoin('cm_file AS f', 'f.id', '=', 'd.cm_file_id')

                       ->leftJoin('cm_exp_lc_entry AS lc', 'lc.cm_file_id', '=', 'd.cm_file_id')
                       ->leftJoin('cm_sales_contract AS sc', 'sc.id', '=', 'lc.cm_sales_contract_id')

                       ->leftJoin('cm_sales_contract_order AS sco', 'sco.cm_sales_contract_id', '=', 'sc.id')

                       ->leftJoin('mr_order_entry AS o', 'o.order_id', '=', 'sco.mr_order_entry_order_id')

                       ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 'o.mr_buyer_b_id')

                       ->leftJoin('mr_supplier AS sup', 'sup.sup_id', '=', 'd.mr_supplier_sup_id')

                       ->where('r.grn_no',$grnNo)
                       ->where('ri.mr_cat_item_id',$itemId)

                       ->first(); //dd($data);

                    $data['item_id']=$itemId;

                  return view('inventory.inspection.accessories_inspection',$data);
                } else {
                     return redirect('inventory/rm_receive_list')->with('error','No item found');
                }
             } else {
                    return redirect('inventory/rm_receive_list')->with('error','No data found');
            }


    	} catch(\Exception $e) {
    		return $e->getMessage();
    	}
    }


  # Accessories Inspection Store  
    public function accessoriesInspectionStore(Request $request)
    {
     //  dd($request->all());

        #-----------------------------------------------------------# 
    	$validator= Validator::make($request->all(),[

            /*'inspection_date'=>'required',
    		'file_no'=>'required',
            'buyer'=>'required',
            'order_no'=>'required',
            'description'=>'required',
    		'item'=>'required',
            'supplier' =>'required',
            'inv_qty' =>'required'*/
            
    	]);

    

    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput()
    			->with('error', 'Please fillup all required fields!');
    	}
    	else
        {
         DB::beginTransaction();
         try {   

            // Insert into st_inspection_master
            
            $master= new StInspectionMaster();
            $master->st_raw_material_receive_id       = $request->raw_mat_id;
            $master->st_raw_material_item_receive_id  = $request->raw_mat_item_rcv_id;
            $master->mr_cat_item_id                   = $request->item_id;
            $master->inspection_date                  = $request->inspection_date;
            $master->ship_description                 = $request->ship_description;
            $master->inspected_by                     = 1;
            $master->save();
            $last_master_id=$master->id;



            // Insert into st_inspection_details_accessories

    		$points= new InspectionDetailsAccessories();
            $points->metal_detection_scan       = $request->metal_detection_result;
    		$points->inspection_result          = $request->inspection_result;
            $points->st_inspection_master_id    = $last_master_id;
    	    $points->save();
    	    $last_id=$points->id;

             // Insert into 
    	      for($i=0; $i<sizeof($request->checkpoint); $i++)
               {
	              AccessoriesCheckPointsDetails::insert([
	                'check_points_id'                       => $request->checkpoint[$i],
	                'defect_desc'                           => $request->defect_des[$i],
	                'critical'                              => $request->critical[$i],
	                'major'                                 => $request->major[$i],
	                'minor'                                 => $request->minor[$i],
	                'st_inspection_details_accessories_id'  => $last_id
	                ]);
                }
            
            // $this->logFileWrite("Location Entry Saved", $loc->hr_location_id);

    		
            DB::commit();
            
            return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
            }
            catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            //$bug = $e->errorInfo[1];;

            return redirect()->back()->with('error',$bug);
          }

    	}
    }

   # Accessories Inspection List
    public function accessoriesInspectionList()
    {
        try {
            $buyerList=Buyer::pluck('b_name','b_id' );    
            $supplierList=Supplier::pluck('sup_name','sup_id' );  

		    return view('inventory.inspection.accessories_inspection_list',compact('buyerList','supplierList'));
	    } 
	    catch(\Exception $e) {
    		return $e->getMessage();
    	} 
    }
  
  # get List data
    public function inspectionListData()
    { 
       /****
        $article = StRawMaterialReceive::with(['InspectionMaster'])->first(['grn_no']); 
        $master = StInspectionMaster::with('st_raw_material_receive')->get(); dd($master);
       /****/ 


        DB::statement(DB::raw('set @serial_no=0')); 
        $data = DB::table('st_inspection_master AS m') 
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                "a.*",
                "m.mr_cat_item_id",  
                "r.grn_no",
                "ri.receive_qty",
                "mc.item_name AS item",             
                "sup.sup_name",
                "b.b_name",              
                "f.file_no"
              
            )
            ->leftJoin('st_raw_material_receive AS r', 'r.id', '=', 'm.st_raw_material_receive_id')   

            ->leftJoin('st_inspection_details_accessories AS a', 'a.st_inspection_master_id', '=', 'm.id')
            ->leftJoin('st_raw_material_item_receive AS ri', 'ri.id', '=', 'm.st_raw_material_item_receive_id')
            ->leftJoin('mr_cat_item AS mc', 'mc.id', '=', 'ri.mr_cat_item_id') 

            ->leftJoin('cm_imp_invoice AS ci', 'ci.id', '=', 'r.cm_imp_invoice_id')

            ->leftJoin('cm_imp_data_entry AS d', 'd.id', '=', 'ci.cm_imp_data_entry_id')

            ->leftJoin('cm_file AS f', 'f.id', '=', 'd.cm_file_id')

            ->leftJoin('cm_exp_lc_entry AS lc', 'lc.cm_file_id', '=', 'd.cm_file_id')

            ->leftJoin('cm_sales_contract AS sc', 'sc.id', '=', 'lc.cm_sales_contract_id')

            //->leftJoin('cm_sales_contract_order AS sco', 'sco.cm_sales_contract_id', '=', 'sc.id') // order 

            //->leftJoin('mr_order_entry AS o', 'o.order_id', '=', 'sco.mr_order_entry_order_id')

            ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 'sc.mr_buyer_b_id')

            ->leftJoin('cm_pi_master AS cpm', 'cpm.id', '=', 'ri.cm_pi_master_id')

            ->leftJoin('mr_supplier AS sup', 'sup.sup_id', '=', 'cpm.mr_supplier_sup_id')


            //->groupby('o.mr_buyer_b_id')  
            //->groupby('sco.mr_order_entry_order_id')    

                    
            ->get();

  // dd($data);
        return DataTables::of($data)
            ->editColumn('inspection_result', function ($data) {

            	//dd($data);

            	if($data->inspection_result==1 && $data->metal_detection_scan==1)

                  $return = "Pass";             
                 
                else
                  $return = "Fail";

                return $return;
            })
        
            ->editColumn('action', function ($data) {

                $return = "<div class=\"btn-group\">";

                  if($data->inspection_result==1 && $data->metal_detection_scan==1){
                    $return .= "<a href=".url('inventory/inspection_fabric/'.$data->grn_no.'/'.$data->mr_cat_item_id)." class=\"btn btn-xs  btn-success\"> <i class=\"ace-icon fa fa-pencil align-top bigger-125\"></i>Inventory Entry</a>";
                    }
                  else  {
                    $return .= "<a href=\"\" class=\"btn btn-xs  btn-success\" style=\"width:112px;\"> <i class=\"ace-icon fa fa-reply icon-only\"></i> Return </a>";
                    }
             
                    $return .= "<a href=".url('inventory/accessories_inspection_edit/'.$data->id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit Bulk\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>";

                    $return .= "<a href=".url('inventory/accessories_inspection_delete/'.$data->id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" onClick=\"return window.confirm('Are you sure?')\" title=\"Delete\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";

                $return .= "</div>";

                return $return;
            })  
            ->rawColumns([
                'serial_no',
                'action'
            ])
            ->toJson();
    }
  # Accessories Inspection Edit
    public function inspectionEdit($id)
    {
        try {
            $buyerList=Buyer::pluck('b_name','b_id' );    
            $supplierList=Supplier::pluck('sup_name','sup_id' );  

		    return view('inventory.inspection.accessories_inspection_edit',compact('buyerList','supplierList'));
	    } 
	    catch(\Exception $e) {
    		return $e->getMessage();
    	} 
    }


}