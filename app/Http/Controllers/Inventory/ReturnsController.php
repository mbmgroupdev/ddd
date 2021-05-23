<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Models\Commercial\CmImpInvoice;
use App\Models\Commercial\CmInvoicePiBom;
use App\Models\Inventory\RmReturn;
use App\Models\Inventory\RmReturnItem;
use App\Models\Inventory\MrCatItem;
use App\Models\Inventory\StRawMaterialReceive;
Use DB, ACL, Validator, DataTables, DateTime;

class ReturnsController extends Controller
{
    public function index(){
    	
        
        return view('inventory.sections.returns');
    }


    public function returnItem($grnNo, $masterId, $itemId){	
        $invoice_id= StRawMaterialReceive::where('grn_no', '=', $grnNo)->first()->cm_imp_invoice_id;
        //private $masterId;
    	try{
            $invoice_data = CmImpInvoice::find($invoice_id);
            $invoice_n=$invoice_data->invoice_no;
            $supplier=$invoice_data->invoice_supplier->supplier->sup_name;
            $item_model=CmInvoicePiBom::where([
                               'cm_imp_invoice_id' => $invoice_id,
                               'cm_pi_master_id' => $masterId
                        ])
                        ->with(['pi_master.pi_master_bom.costing_booking' => function ($query) use ($itemId) {
                            $query->where('mr_cat_item_id', '=', $itemId);
                        }])
                        ->first();
            $item_data= $item_model->pi_master->pi_master_bom;
            //dd($item_data);

            return view('inventory.sections.returns', compact('invoice_data','item_data'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }


      # Return Store  
    public function returnStore(Request $request)
    {
     //  dd($request->all());
        #-----------------------------------------------------------# 

    	///$regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
    	$validator= Validator::make($request->all(),[
        //'ispection_report' => 'regex:' . $regex,
    	 'return_date'      => 'required',
    	 'reason'           => 'required',
    	 'ispection_report' => 'required',
    
            
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
    		$returns= new RmReturn();
            $returns->material_type          = 1;
    		$returns->st_rm_issue_id         = 1;
            $returns->return_qty             = 0;
            $returns->reason                 = $request->description;
            $returns->inspection_report      = $request->ispection_report;
            $returns->date                   = $request->return_date;
    	    $returns->save();
    	    $last_id=$returns->id;


    	      for($i=0; $i<sizeof($request->item); $i++)
               {
	              RmReturnItem::insert([
	              	'st_rm_return_id'                   => $last_id,
	                'st_raw_material_item_receive_id'   => $request->item[$i],
	                'return_qty'                        => $request->return_qty[$i],
	                'reason'  							=> $request->reason[$i],
	               
	                
	                ]);
                }
            
       

    		
            DB::commit();
            
            return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
            }
            catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();

            return redirect()->back()->with('error',$bug);
          }

    	}
    }



       # Aseet  List
    public function returnList()
    {
        try {          
      	    
		  return view('inventory.return_list');
	    } 
	    catch(\Exception $e) {
    		return $e->getMessage();
    	} 
    }
  
  # get List data
    public function returnListData()
    { 
        DB::statement(DB::raw('set @serial_no=0'));  
        $data = DB::table('st_rm_return_item AS a')
              ->join("st_rm_return As b","b.id","=","a.st_rm_return_id") 
              ->select(
                DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                "a.st_raw_material_item_receive_id","a.return_qty","a.reason" ,"b.inspection_report","b.date"
              
            );
        /*$data= RmReturnItem::with(['st_rm_return' => function ($query) {
                            $query->select('material_type','inspection_report','date');
                        }])
                ->select('id','return_qty','reason')
                ->get();*/
        //dd($data);
        	return DataTables::of($data)

           ->editColumn('st_raw_material_item_receive_id', function ($data) {
                    $id=$data->st_raw_material_item_receive_id;
                    return MrCatItem::where('id', '=', $id)->first()->item_name;
                     
                     
            })

        
            ->editColumn('action', function ($data) {

                $return = "<div class=\"btn-group\">";
             
                    $return .= "<a href=edit\"\" class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit Bulk\">
                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                    </a>";

                    $return .= "<a href=\"\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" onClick=\"return window.confirm('Are you sure?')\" title=\"Delete\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";

                $return .= "</div>";

                return $return;
            })  



            ->rawColumns([
                
            ])
            ->toJson();
    }
}
