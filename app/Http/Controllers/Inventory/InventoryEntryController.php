<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB, DataTables, Response, Validator;

class InventoryEntryController extends Controller
{
    public function index(){
    	return view('inventory.sections.inventory_entry_list');
    }
    public function itemEntry(){
    	return view('inventory.sections.inventory_item_entry');
    }
    public function getData(){


      	DB::statement(DB::raw('set @serial_no=0'));
        $data = DB::table('st_inventory_entry as a')
            		->select(
                		DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
		                'a.type',
		                'a.mr_cat_item_id',
		                'a.receive_qty',
		                'a.receive_date',
		                'a.st_raw_material_receive_id',
		                'f.mcat_id',
		                'f.item_name',
		                'b.id',
		                'b.grn_no'

		            )
		            ->leftJoin('st_raw_material_receive as b', 'b.id', '=', 'a.st_raw_material_receive_id')
		            ->leftJoin('mr_cat_item as f','f.id','=','a.mr_cat_item_id')
		            ->get();

		            //dd($data);

        return DataTables::of($data)
		            ->editColumn('action', function ($data) {
		                $return = "<div class=\"btn-group\">";

		                    $return .= "<a href=\"#\" class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Inventory Entry\">Action
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


    public function itemEntryEdit(Request $request,$grn_no,$itemid){

    	// $data = DB::table('st_inspection_master as a')
     //        		->select(
		   //              'a.st_raw_material_receive_id',
		   //              'a.st_raw_material_item_receive_id',
		   //              'a.mr_cat_item_id',
		   //              'a.id',
		   //              'b.id as bid',
		   //              'b.grn_no',
		   //              'd.invoice_no',
		   //              'd.id as did',
		   //              'c.receive_qty',
		   //              'c.receive_date',
		   //              'c.id as cid',
		   //              'e.pi_no',
		   //              'e.id as eid',
		   //              'f.mcat_id',
		   //              'f.item_name',
		   //              'f.id as itemid'


		   //          )
		   //          ->leftJoin('st_raw_material_receive as b', 'b.id', '=', 'a.st_raw_material_receive_id')
		   //          ->leftJoin('st_raw_material_item_receive as c', 'c.id', '=', 'a.st_raw_material_item_receive_id')
		   //          ->leftJoin('cm_imp_invoice as d','d.id','=','b.cm_imp_invoice_id')
		   //          ->leftJoin('cm_pi_master as e','e.id','=','c.cm_pi_master_id')
		   //          ->leftJoin('mr_cat_item as f','f.mcat_id','=','a.mr_cat_item_id')
		   //          //->where('a.id',$id)
		   //          ->where('f.id',$itemid)
		   //          ->first();

	    	$data=DB::table('st_raw_material_item_receive AS a')
			        ->select([
			            'a.id AS st_raw_mir_id',
			            'b.id AS st_raw_mr_id',
			            'a.cm_pi_master_id',
			            'a.receive_date',
			            'a.receive_qty',
			            'a.mr_cat_item_id',
			            'b.grn_no',
			            'b.id as st_raw_mat_rcv_id',
			            'b.cm_imp_invoice_id',
			            'c.item_name',
			            'c.mcat_id',
			            'f.sup_name',
			            'd.invoice_no'
			        ])
			        ->leftJoin('st_raw_material_receive AS b', 'b.id', 'a.st_raw_material_receive_id')
			        ->leftJoin('mr_cat_item AS c', 'c.id', 'a.mr_cat_item_id')
			        ->leftJoin('cm_imp_invoice AS d', 'd.id', 'b.cm_imp_invoice_id')
			        ->leftJoin('cm_imp_data_entry AS e', 'e.id', 'd.cm_imp_data_entry_id')
			        ->leftJoin('mr_supplier AS f', 'f.sup_id', 'e.mr_supplier_sup_id')
			        ->where('b.grn_no',$grn_no)
			        ->where('a.mr_cat_item_id',$itemid)
			        ->first();

					//dd($data);
		$warehouse = DB::table('st_warehouse')
					->select(['id', 'name','fk_unit_id'])
					->get();

					//dd($warehouse);
		$material_type=DB::table('mr_material_category') 
					->where('mcat_id',$data->mcat_id)
					->select('mcat_name')
					->first();
		//dd($material_type);

		

    	return view('inventory.sections.inventory_item_entry', compact('data','warehouse','material_type'));
    }

	public function getRack(Request $request){
			// dd($request->all());exit;
			$rack_no= DB::table('st_warehouse_rack as a')
				->select('a.id as aid','a.name','a.fk_ware_house_id','b.id as bid')
				->leftJoin('st_warehouse as b', 'b.id','=','a.fk_ware_house_id')
				->where('a.fk_ware_house_id', $request->wid)
				->get();
				// dd($request->warehouse_name);
				//$data['?'][0]=dgd
	    return Response::json($rack_no);

	}

	public function getColumn(Request $request){
			// dd($request->all());exit;
				// dd($request->warehouse_name);
				//$data['?'][0]=dgd
			
			$row_no= DB::table('st_warehouse_rack_row')
					->where('fk_rack_id', $request->rack_no)
					->count('fk_rack_id');
					
			//dd($row_no);
			$row_id=DB::table('st_warehouse_rack_row')
					->where('fk_rack_id', $request->rack_no)
					->pluck('id');
					//dd($row_id);
			$v=0;
			foreach ($row_id as $key => $rid) {
				$col[$v++]=DB::table('st_warehouse_rack_row_colmn')
						->where('fk_row_id',$rid)
						->count('fk_row_id');
			}
			//dd($col);
			$t=0;
			foreach ($row_id as $key => $rid) {
				$col_name[$t++]=DB::table('st_warehouse_rack_row_colmn')
						->select('name','id')
						->where('fk_row_id',$rid)
						->get()
						->toArray();
			}
			$col[$v]=$col_name;

			//dd('Data:',$col);exit;


	    return Response::json($col);

	}
    
	public function storeData(Request $request){
    	//dd($request->all());exit;
    	

     	DB::beginTransaction();
     	try{
     		
     		$id = DB::table('st_inventory_entry')->insertGetId(
				    ['type' => $request->type,
				     'mr_cat_item_id'=>$request->mcat_id,
				     'st_raw_material_receive_id'=> $request->raw_mat_id,
				     'receive_date'=>$request->receive_date,
				     'receive_qty'=>$request->receive_qty

					]
					);
     		foreach ($request->rack_row_col as $key => $value) {
     			$save_cell_value= DB::table('st_inventory_item_cell')->insert(
     							[ 'st_inventory_entry_id'=>$id,
     							  'st_warehouse_rack_row_column_id'=>$value,
     							  'qty'=>$request->qty[$key],
     							]
     		);
     		}
     		
     		//$this->logFileWrite("Inventory>Setup> Warehouse Rack Row Colmn Saved", $last_id);
     		DB::commit();

     		// return back()->with('success', 'Entry Saved');
             return 'success';

     	}catch(\Exception $e){
     		DB::rollback();
     		$msg = $e->getMessage();
     		// return back()->withInput()->with('error',$msg);
     		return $msg;
     	}
    }



}
