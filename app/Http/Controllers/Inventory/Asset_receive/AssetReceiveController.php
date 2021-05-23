<?php

namespace App\Http\Controllers\Inventory\Asset_receive;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\AssetItem;
use App\Models\Inventory\AssetRequisition;
use App\Models\Inventory\AssetRequisitionItem;
use App\Models\Inventory\AssetReceiveItem;
use App\Models\Inventory\AssetReceiveFromSupplier;

Use DB, ACL, Validator, DataTables,DateTime;


class AssetReceiveController extends Controller
{
   // Old form 
    public function assetReceiveform_b(){
      try {

      	$data['supplier'] = Supplier::getSupplier();
      	$data['assetitem'] = AssetItem::getAssetList();

    	return view('inventory.asset_receive.asset_receive', $data);
        } catch(\Exception $e) {
    		return $e->getMessage();
      }	
    }

    // New form 
    public function assetReceiveform($id){
      try { 

        $data['supplier'] = Supplier::getSupplier();
        $data['assetitem'] = AssetItem::getAssetList();
        $data['requisition']= AssetRequisition::where('id',$id)->first();
        $data['requisitionItem']= AssetRequisitionItem::where('st_asset_requisition_id',$id)->get();
        $data['uom']=DB::table('uom')->get();

        // dd($data);

        return view('inventory.asset_receive.asset_receive', $data);
        } catch(\Exception $e) {
            return $e->getMessage();
      } 
    }

  # Return Item List by Action Type ID
    public function getAssetItems(Request $request)
    {
       
        if (!empty($request->item_id))
        { 

            $desList  = AssetItem::where('id', $request->item_id)
                       ->first(['item_description']);

        }

       
        return $desList->item_description;
    } 

  # Asset Store  
    public function assetReceiveStore(Request $request)
    {
     //  dd($request->all());
        #-----------------------------------------------------------# 
    	$validator= Validator::make($request->all(),[

            /*'chalan_no'=>'required',
    		'supplier'=>'required'
            '*/
            
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
                // dd($request->all());

                $material_type_str = strtolower($request->material_type);
                if($material_type_str == 'supply'){
                    $material_type_id = 1;
                }
                if($material_type_str == 'csr'){
                    $material_type_id = 2;
                }
                if($material_type_str == 'machine'){
                    $material_type_id = 3;
                }
                // dd($material_type_id);

    		    $asset= new AssetReceiveFromSupplier();
                $asset->st_material_type_id       = $material_type_id;
                $asset->st_asset_requisition_id   = $request->asset_requisition_id;
    		    $asset->invoice_no                = $request->chalan_no;
                $asset->po_no                     = $request->po_no;
                $asset->po_qty                    = $request->po_qty;
                $asset->st_supplier_id            = $request->supplier;
                $asset->receiver_name             = auth()->user()->associate_id;
                $asset->receive_date              = NOW();
    	        $asset->save();

    	      $last_id=$asset->id;


    	      for($i=0; $i<sizeof($request->item); $i++)
               {
	              AssetReceiveItem::insert([
	              	'st_asset_receive_from_suppler_id'      => $last_id,
	                'st_asset_item_id'                      => $request->item[$i],
	                'receive_qty'                           => $request->rcvQty[$i],	               
                    'uom_id'                                => $request->uom_id[$i]                
	                
	                ]);
                }
            
            $this->logFileWrite("Asset Receive From Supplier Saved" , $last_id);

    		
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

   # Aseet  List
    public function assetReceiveList()
    {
        try {
             
            $supplierList=Supplier::pluck('sup_name','sup_id' );  
           
      	    $assetitem= AssetItem::getAssetList();


		  return view('inventory.asset_receive.asset_receive_list',compact('supplierList',$assetitem));
	    } 
	    catch(\Exception $e) {
    		return $e->getMessage();
    	} 
    }
  
  # get List data
    public function assetReceiveListData()
    { 
        DB::statement(DB::raw('set @serial_no=0')); 
        $data = DB::table('st_asset_receive_from_suppler AS a') 
            ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                "a.*", 
                "sup.sup_name"
              
            )
            
            ->leftJoin('st_supplier AS sup', 'sup.sup_id', '=', 'a.st_supplier_id')
            ->get();

            // dd($data);

      
        return DataTables::of($data)

           ->editColumn('st_material_type_id', function ($data) {

            	// dd($data);

               if($data->st_material_type_id==1)
                  $return = "Supply";             
               else if($data->st_material_type_id==2)
                  $return = "CSR";
               else if($data->st_material_type_id==3)
                  $return = "Machine";
               else
               	 $return = " ";

                return $return;
            })
       

        
            ->editColumn('action', function ($data) {

                $return = "<div class=\"btn-group\">";
             
                    // $return .= "<a href=\"\" class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit Bulk\">
                    //     <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    // </a>";

                    $return .= "<a href=".url('inventory/asset_receive_delete/'.$data->id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" onClick=\"return window.confirm('Are you sure?')\" title=\"Delete\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";

                $return .= "</div>";

                return $return;
            })  



            ->rawColumns([
                'serial_no',
                'st_material_type_id',
                'action'
            ])
            ->toJson();
    }

    public function assetReceiveDeleteData($id){
        AssetReceiveFromSupplier::where('id',$id)->delete();
        AssetReceiveItem::where('st_asset_receive_from_suppler_id',$id)->delete();

        $this->logFileWrite("Asset Receive From Supplier Deleted" , $id);
        return back()->with('success', "Asset Receive From Supplier Deleted");    
    }

}
