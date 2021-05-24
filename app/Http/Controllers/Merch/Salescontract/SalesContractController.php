<?php
namespace App\Http\Controllers\Merch\Salescontract;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Buyer;
use App\Models\Commercial\Bank;
use App\Models\Commercial\BankAccNo;
use App\Models\Commercial\SalesContract;
use App\Models\Commercial\SalesContractOrder;
use App\Models\Commercial\SalesContractAmend;
use App\Models\Merch\Country;
use App\Models\Hr\Unit;
use App\Models\Merch\OrderEntry;

use Validator, DB, ACL, Auth, DataTables;
class SalesContractController extends Controller
{
	# show form
    public function entryForm(){

      $buyer=Buyer::pluck('b_name','b_id');
      $bank=Bank::pluck('bank_name','id');
      //$bank=null;
      $country=Country::pluck('cnt_name','cnt_id');
      $unit=Unit::pluck('hr_unit_name','hr_unit_id');

      return view('merch/salescontract/salescontract_entry', compact('buyer','bank','unit'));
    }

	# Contract list
    public function getContractList(Request $request){

        $list =1;
        $orderList  = SalesContract::where('hr_unit_id', $request->unit_id)
                     ->where('mr_buyer_b_id', $request->b_id)
                     ->get();
        $order = $orderList->count();
        $list=$order+$list;
        return $list;
    }

	# Order list
    public function getOrderList(Request $request){

      if(auth()->user()->hasRole('merchandiser')){
        $lead_asid = DB::table('hr_as_basic_info as b')
           ->where('associate_id',auth()->user()->associate_id)
           ->pluck('as_id');
       $team_members = DB::table('hr_as_basic_info as b')
          ->where('associate_id',auth()->user()->associate_id)
          ->leftJoin('mr_excecutive_team','b.as_id','mr_excecutive_team.team_lead_id')
          ->leftJoin('mr_excecutive_team_members','mr_excecutive_team.id','mr_excecutive_team_members.mr_excecutive_team_id')
          ->pluck('member_id');
       $team = array_merge($team_members->toArray(),$lead_asid->toArray());
     }elseif (auth()->user()->hasRole('merchandising_executive')) {
       $executive_associateId[] = auth()->user()->associate_id;

       $teamid = DB::table('hr_as_basic_info as b')
          ->where('associate_id',auth()->user()->associate_id)
          ->leftJoin('mr_excecutive_team_members','b.as_id','mr_excecutive_team_members.member_id')
          ->pluck('mr_excecutive_team_id');
      $team_lead = DB::table('mr_excecutive_team')
             ->whereIn('id',$teamid)
             ->leftJoin('hr_as_basic_info as b','mr_excecutive_team.team_lead_id','b.as_id')
             ->pluck('associate_id');
      $team_members_associateId = DB::table('mr_excecutive_team_members')
                                        ->whereIn('mr_excecutive_team_id',$teamid)
                                        ->leftJoin('hr_as_basic_info as b','mr_excecutive_team_members.member_id','b.as_id')
                                       ->pluck('associate_id');
                                       //dd($team_members_associateId);exit;
    $team = array_merge($team_members_associateId->toArray(),$team_lead->toArray());
      }else{
       $team =[];
      }

        $list = "";
       if(!empty($team)){
         $orderList = DB::table('mr_order_entry AS m')
             ->select(
                 "m.order_code",
                 "m.order_id",
                 "m.order_qty",
                 "m.mr_style_stl_id",
                 "m.order_delivery_date",
                 "st.stl_no as style_no",
                 "b.agent_fob"
             )
             ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'm.order_id')
             ->leftJoin('mr_style AS st', 'st.stl_id', '=', 'm.mr_style_stl_id')
             ->whereIn('m.created_by', $team)
             ->where('m.mr_buyer_b_id', $request->buyer_id)
             ->where('m.unit_id', $request->unit_id)
             ->get();
       }else{
         $orderList = DB::table('mr_order_entry AS m')
             ->select(
                 "m.order_code",
                 "m.order_id",
                 "m.order_qty",
                 "m.mr_style_stl_id",
                 "m.order_delivery_date",
                 "st.stl_no as style_no",
                 "b.agent_fob"
             )
             ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'm.order_id')
             ->leftJoin('mr_style AS st', 'st.stl_id', '=', 'm.mr_style_stl_id')
             ->where('m.mr_buyer_b_id', $request->buyer_id)
             ->where('m.unit_id', $request->unit_id)
             ->get();
       }

            $list ='<table class="table table-responsive table-bordered table-striped" style= "width:100%;">
                      <thead>
                        <th width="30%" class="">
                         Order No
                        </th>
                        <th width="30%" class="">
                          Style
                        </th>
                        <th width="30%" class="">
                          Order Qty
                        </th>
                        <th width="30%" class="">
                        Order Delivary Date
                        </th>
                      </thead>
                      <tbody>
            ';
            foreach ($orderList as  $value)
            {
            	$po_last_date= DB::table("mr_purchase_order")
            					->where('mr_order_entry_order_id', $value->order_id)
            					->orderBy('po_ex_fty', "DESC")
            					->pluck('po_ex_fty')
            					->first();

                $list.='<tr>
                          <td>
                            <input name="selected_item[]" type="checkbox" value="'.$value->order_id.'" class="ace col-sm-2 checkbox-input">
                            <span class="lbl">&nbsp;&nbsp;'. $value->order_code.'</span>
                            <input type="hidden" class="qty" value="'.$value->order_qty.'">
                            <input type="hidden" class="fob" value="'.$value->agent_fob.'">
                            <input type="hidden" class="ord_del_date" value="'.$value->order_delivery_date.'">
                            <input type="hidden" class="po_del_date" value="'.$po_last_date.'">
                          </td>
                          <td>
                            <span class="lbl">'.$value->style_no.'</span>
                          </td>
                          <td>
                            <span class="lbl">'.$value->order_qty.'</span>
                          </td>
                          <td>
                             <span class="lbl">'.$value->order_delivery_date.'</span>
                          </td>

                        </tr>';

            }
            $list.='</tbody>
                     </table>';
        return $list;
    }

    # Order list
    public function getOrderListForAmendment(Request $request){

    	$pre_amended_order = DB::table('cm_sales_contract AS sc')
    								->leftJoin('cm_sales_contract_order AS sco', 'sco.cm_sales_contract_id', 'sc.id')
    								->where('sco.cm_sales_contract_id', '=', $request->sales_contract_id )
    								->pluck('sco.mr_order_entry_order_id')
    								->toArray();
        // dd($pre_amended_order);

        $list = "";

        $orderList = DB::table('mr_order_entry AS m')
            ->select(
                "m.order_code",
                "m.order_id",
                "m.order_qty",
                "m.mr_style_stl_id",
                "m.order_delivery_date",
                "st.stl_no as style_no",
                "b.agent_fob"
            )
            ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'm.order_id')
            ->leftJoin('mr_style AS st', 'st.stl_id', '=', 'm.mr_style_stl_id')
            ->where('m.mr_buyer_b_id', $request->buyer_id)
            ->where('m.unit_id', $request->unit_id)
            ->whereNotIn('m.order_id', $pre_amended_order)
            ->get();

           // dd($orderList);

            $list ='<table class="table table-responsive table-bordered table-striped">
                      <thead>
                        <th class="">
                         Order No
                        </th>
                        <th class="">
                          Style
                        </th>
                        <th class="">
                          Order Qty
                        </th>
                        <th class="">
                        Order Delivary Date
                        </th>
                      </thead>
                      <tbody>
            ';
            foreach ($orderList as  $value)
            {
            	$po_last_date= DB::table("mr_purchase_order")
            					->where('mr_order_entry_order_id', $value->order_id)
            					->orderBy('po_ex_fty', "DESC")
            					->pluck('po_ex_fty')
            					->first();

                $list.='<tr>
                          <td>
                            <input name="selected_item[]" type="checkbox" value="'.$value->order_id.'" class="ace col-sm-2 checkbox-input">
                            <span class="lbl">&nbsp;&nbsp;'. $value->order_code.'</span>
                            <input type="hidden" class="qty" value="'.$value->order_qty.'">
                            <input type="hidden" class="fob" value="'.$value->agent_fob.'">
                            <input type="hidden" class="ord_del_date" value="'.$value->order_delivery_date.'">
                            <input type="hidden" class="po_del_date" value="'.$po_last_date.'">
                          </td>
                          <td>
                            <span class="lbl">'.$value->style_no.'</span>
                          </td>
                          <td>
                            <span class="lbl">'.$value->order_qty.'</span>
                          </td>
                          <td>
                             <span class="lbl">'.$value->order_delivery_date.'</span>
                          </td>

                        </tr>';

            }
            $list.='</tbody>
                     </table>';

            // dd($list);
        return $list;
    }

	# Sales Store
  	public function salesStore(Request $request){
  		// dd($request->all());

       	$validator= Validator::make($request->all(),[
            'buyer'               => 'required|max:11',
            'unit'                => 'required|max:11',
            'contract_no'         => 'required|max:45',
            'exlc_contract_no'    => 'required|max:45',
            'contract_value'      => 'required|max:45'
        ]);

        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{
        	$checkExisting= SalesContract::where('mr_buyer_b_id', $request->buyer)
        					->where('hr_unit_id', $request->unit)
        					->where('contract_no_by', $request->contract_no)
        					->where('lc_contract_no', $request->exlc_contract_no)
        					->where('lc_contract_type', $request->lctype)
        					->where('initial_value', $request->initial_value)
        					->where('currency_type', $request->currency)
        					->where('btb_bank_id', $request->btb_bank_id)
        					->where('remarks', $request->remarks)
        					->where('lc_open_bank_id', $request->lc_open_bank_id)
        					->exists();
        	if($checkExisting == false){
	        	DB::beginTransaction();
	        	try {
	        		$newsales= new SalesContract();
					$newsales->mr_buyer_b_id   = $request->buyer;
					$newsales->hr_unit_id      = $request->unit;
					$newsales->contract_no_by  = $request->contract_no;
					$newsales->lc_contract_no  = $request->exlc_contract_no;
					$newsales->lc_contract_type= $request->lctype;
					$newsales->initial_value   = $request->initial_value;
					$newsales->currency_type   = $request->currency;
					$newsales->btb_bank_id     = $request->btb_bank;
					$newsales->remarks         = $request->remark;
					$newsales->lc_open_bank_id = $request->lc_bank;

					$newsales->save();
					$last_id = $newsales->id;
					$last_sales_id= $last_id;
					$last_id= SalesContractAmend::insertGetId([
						'amend_no' => 0,
						'elc_amend_date' => $request->elc_date,
						'contract_qty' => $request->contract_qty,
						'contract_value' => $request->contract_value,
		        'expire_date'     => $request->exp_date,
						'cm_sales_contract_id' => $last_id
					]);
					if(!empty($request->order_id)){

		             	for($i=0; $i<sizeof($request->order_id); $i++){
		                  	SalesContractOrder::insert([
		                      	'cm_sales_contract_id'     => $last_sales_id,
		                      	'mr_order_entry_order_id'  => $request->order_id[$i],
		                      	'contract_fob'     => ($request->new_fob[$i] != null)? $request->new_fob[$i]: 0,
		                      	'contract_value'     => ($request->new_order_value[$i] != null)? $request->new_order_value[$i]:0,
		                      	'cm_sales_contract_amend_id'  => $last_id
		                  	]);
		              	}
		            }

		            DB::commit();
		            $this->logFileWrite("Commercial-> Sales Contract Saved", $last_sales_id); //log entry

		          	return back()
		                ->with('success', "Contract Sales Saved Successfully!!");

	        	} catch (\Exception $e) {
	        		DB::rollback();
	        		$msg= $e->getMessage();
	        		return back()
		                ->with('error', $msg);

	        	}
	        }
	        else{
	        	return back()
		                ->with('error', "Same Sales Contract already exists!!");
	        }
       	}
    }

	# Contract Sales list
    public function salesContractList(){

		$buyer=Buyer::pluck('b_name','b_id');
		$bank=Bank::pluck('bank_name','id');
		$country=Country::pluck('cnt_name','cnt_id');
		$unit=Unit::pluck('hr_unit_name','hr_unit_id');

		return view('merch/salescontract/salescontract_list', compact('buyer','bank','unit'));
    }

	# Contract Sales Data
    public function getData(){
      if(auth()->user()->hasRole('merchandiser')){
  			$lead_associateId[] = auth()->user()->associate_id;
  		 $team_members = DB::table('hr_as_basic_info as b')
  				->where('associate_id',auth()->user()->associate_id)
  				->leftJoin('mr_excecutive_team','b.as_id','mr_excecutive_team.team_lead_id')
  				->leftJoin('mr_excecutive_team_members','mr_excecutive_team.id','mr_excecutive_team_members.mr_excecutive_team_id')
  				->pluck('member_id');
  		$team_members_associateId = DB::table('hr_as_basic_info as b')
  	 				                       ->whereIn('as_id',$team_members)
  																 ->pluck('associate_id');
  		 $team = array_merge($team_members_associateId->toArray(),$lead_associateId);
  		 //dd($team);exit;
  	 }elseif (auth()->user()->hasRole('merchandising_executive')) {
  			   $executive_associateId[] = auth()->user()->associate_id;

  				 $teamid = DB::table('hr_as_basic_info as b')
  						->where('associate_id',auth()->user()->associate_id)
  						->leftJoin('mr_excecutive_team_members','b.as_id','mr_excecutive_team_members.member_id')
  						->pluck('mr_excecutive_team_id');
  				$team_lead = DB::table('mr_excecutive_team')
  							 ->whereIn('id',$teamid)
  							 ->leftJoin('hr_as_basic_info as b','mr_excecutive_team.team_lead_id','b.as_id')
  							 ->pluck('associate_id');
  				$team_members_associateId = DB::table('mr_excecutive_team_members')
  						                              ->whereIn('mr_excecutive_team_id',$teamid)
  																					->leftJoin('hr_as_basic_info as b','mr_excecutive_team_members.member_id','b.as_id')
  																				 ->pluck('associate_id');
  																				 //dd($team_members_associateId);exit;
  			$team = array_merge($team_members_associateId->toArray(),$team_lead->toArray());
  			//dd($team);exit;

  		}else{
  		 $team =[];
  		}

       if(!empty($team)){
         DB::statement(DB::raw('set @serial_no=0'));
         $data = DB::table('cm_sales_contract AS co')
             		->select(
                 		DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
 		                "co.*",
 		                "b.b_name",
 		                "u.hr_unit_name"
 		            )
 		            ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 'co.mr_buyer_b_id')
 		            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'co.hr_unit_id')
                ->leftJoin('cm_sales_contract_order AS sordr', 'co.id', '=', 'sordr.cm_sales_contract_id')
                ->leftJoin('mr_order_entry','sordr.mr_order_entry_order_id','mr_order_entry.order_id')
                ->whereIn('mr_order_entry.created_by', $team)
 		            ->orderBy('co.id', 'desc')
                ->groupBy('co.id')
 		            ->get();

       }else{
         DB::statement(DB::raw('set @serial_no=0'));
         $data = DB::table('cm_sales_contract AS co')
                 ->select(
                     DB::raw('@serial_no := @serial_no + 1 AS serial_no'),
                     "co.*",
                     "b.b_name",
                     "u.hr_unit_name"
                 )
                 ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 'co.mr_buyer_b_id')
                 ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'co.hr_unit_id')
                 ->leftJoin('cm_sales_contract_order AS sordr', 'co.id', '=', 'sordr.cm_sales_contract_id')
                 ->leftJoin('mr_order_entry','sordr.mr_order_entry_order_id','mr_order_entry.order_id')

                 ->orderBy('co.id', 'desc')
                 ->groupBy('co.id')
                 ->get();

       }


        return DataTables::of($data)
		            ->editColumn('action', function ($data) {
		                $return = "<div class=\"btn-group\">";

		                    // $return .= "<a href=".url('merch/sales_contract/amendment/'.$data->id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Add Amendment\">
		                    //     <i class=\"ace-icon fa fa-plus bigger-120\"></i>
		                    // </a>";

		                    $return .= "<a href=".url('merch/sales_contract/sales_contract_edit/'.$data->id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit Sales Contract\">
		                        <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
		                    </a>";

		                    $return .= "<a href=".url('merch/sales_contract/sales_contract_delete/'.$data->id)." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete Sales Contract\" onclick=\"return confirm('Are you sure?')\" style=\"width:26px;\">
		                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
		                    </a>";

		                $return .= "</div>";

		              return $return;
		            })
		            ->addColumn('contract_qty', function ($data){

		            	return SalesContractAmend::where('cm_sales_contract_id', $data->id)
		            			->value(DB::raw("SUM(contract_qty)"));

		            })
		            ->addColumn('elc_date', function ($data){
		            	return SalesContractAmend::where('cm_sales_contract_id', $data->id)
		            			->value(DB::raw("max(elc_amend_date)"));
		            })
		            ->rawColumns([
		                'serial_no',
		                'action',
		                'contract_qty',
		                'elc_date'
		            ])
		            ->toJson();
    }

	# Edit form
    public function edit($id){

		$buyer=Buyer::pluck('b_name','b_id');
		$bank=Bank::pluck('bank_name','id');
		$country=Country::pluck('cnt_name','cnt_id');
		$unit=Unit::pluck('hr_unit_name','hr_unit_id');
		$sales=SalesContract::where('id',$id)->first();
		// dd($sales);

		$amend= SalesContractAmend::where('cm_sales_contract_id', $id)
												->where('amend_no', 0)
												->first();
		// dd($amend);
      	$sales_order = DB::table('cm_sales_contract_order AS co')
			            ->select([
			                "co.*",
			                "m.order_code",
			                "m.order_id",
			                "m.order_qty",
			                "m.order_delivery_date",
			                "b.agent_fob"
			            ])
			            ->leftJoin('mr_order_entry AS m', 'm.order_id', '=', 'co.mr_order_entry_order_id')
			            ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'm.order_id')
			            ->where('co.cm_sales_contract_amend_id', $amend->id)
			            ->get();
			foreach ($sales_order as $sales_con) {
				$sales_con->po_delivery_date= DB::table("mr_purchase_order")
				            					->where('mr_order_entry_order_id', $sales_con->mr_order_entry_order_id)
				            					->orderBy('po_ex_fty', "DESC")
				            					->pluck('po_ex_fty')
				            					->first();

			}


      	$orderLists = DB::table('cm_sales_contract_order AS sco')
			            ->select(
			            	"sco.*",
			                "o.order_code",
			                "o.order_id",
			                "o.order_qty",
			                "b.agent_fob",
                      "st.stl_no as style_no",
			                "o.order_delivery_date"
			            )
			            ->leftJoin('mr_order_entry AS o', 'o.order_id', 'sco.mr_order_entry_order_id')
			            ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'sco.mr_order_entry_order_id')
                  ->leftJoin('mr_style AS st', 'st.stl_id', '=', 'o.mr_style_stl_id')
			            ->where('sco.cm_sales_contract_amend_id', $amend->id)
			            ->get();
		foreach ($orderLists as $eachOrder) {
				$eachOrder->po_delivery_date= DB::table("mr_purchase_order")
				            					->where('mr_order_entry_order_id', $eachOrder->order_id)
				            					->orderBy('po_ex_fty', "DESC")
				            					->pluck('po_ex_fty')
				            					->first();

			}


		// dd($orderLists);

		return view('merch/salescontract/salescontract_edit', compact('buyer','bank','unit','sales', 'amend', 'sales_order','orderLists'));
    }

	# Sales Update action
    public function salesUpdate(Request $request){
    	// dd($request->all());
       	$validator= Validator::make($request->all(),[
            'exlc_contract_no'    => 'required|max:45',
            'contract_qty'        => 'required|max:45',
            'contract_value'      => 'required|max:45'
        ]);

        if($validator->fails()){
            return back()
            		->withInput()
            		->with('error', "Incorrect Input!");
        }
        else{
        	DB::beginTransaction();
	        try {

	        	$last_id= $request->con_id;
	        	$last_sales_id= $request->con_id;
	        	SalesContract::where('id', $request->con_id)->update([
					'lc_contract_type'=> $request->lctype,
					'initial_value'   => $request->initial_value,
					'currency_type'   => $request->currency,
					'btb_bank_id'     => $request->btb_bank,
					'remarks'         => $request->remark,
					'lc_open_bank_id' => $request->lc_bank
	          	]);

	          	SalesContractAmend::where('cm_sales_contract_id', $last_id)
	          								->where('amend_no', 0)
	          								->update([
												'elc_amend_date' => $request->elc_date,
												'contract_qty' => $request->contract_qty,
												'contract_value' => $request->contract_value,
								                'expire_date'     => $request->exp_date
											]);
	          	$last_id= SalesContractAmend::where('cm_sales_contract_id', $last_id)
	          								->where('amend_no', 0)
	          								->pluck('id')
	          								->first();

	         	SalesContractOrder::where('cm_sales_contract_amend_id', $last_id)->delete();

	          	if(!empty($request->order_id)){

	             	for($i=0; $i<sizeof($request->order_id); $i++){
	                  	SalesContractOrder::insert([
	                      	'cm_sales_contract_id'     => $last_sales_id,
	                      	'mr_order_entry_order_id'     => $request->order_id[$i],
	                      	'contract_fob'     => ($request->new_fob[$i] != null)? $request->new_fob[$i]: 0,
	                      	'contract_value'     => ($request->new_order_value[$i] != null)? $request->new_order_value[$i]:0,
	                      	'cm_sales_contract_amend_id'  => $last_id
	                  	]);
	              	}
		        }

		         DB::commit();

	          	$this->logFileWrite("Commercial-> Sales Contract Updated", $request->con_id);
	          	return back()
	                ->with('success', "Contract Sales Updated Successfully !!!");

	        } catch (\Exception $e) {
	        	DB::rollback();
	        	$msg= $e->getMessage();
	        	return back()
	        		->withInput()
	                ->with('error', $msg);
	        }
       }
    }

    public function salesDelete($id){

        SalesContract::where('id', $id)->delete();
        SalesContractAmend::where('cm_sales_contract_id', $id)->delete();
        SalesContractOrder::where('cm_sales_contract_id', $id)->delete();

        $this->logFileWrite("Commercial-> Sales Contract Deleted", $id);

        return back()->with('success', 'Deleted Successfully');
    }

    //Amendment Form
    public function amendmentForm($id){
    	$list = "";
    	$buyer=Buyer::pluck('b_name','b_id');
		$bank=Bank::pluck('bank_name','id');
		$country=Country::pluck('cnt_name','cnt_id');
		$unit=Unit::pluck('hr_unit_name','hr_unit_id');
		$sales=SalesContract::where('id',$id)->first();

		$amend= SalesContractAmend::where('cm_sales_contract_id', $id)
												->where('amend_no', 0)
												->first();
        // dd($amend);

      	$sales_order = DB::table('cm_sales_contract_order AS co')
			            ->select([
			                "co.*",
			                "m.order_code",
			                "m.order_id",
			                "m.order_qty",
			                "m.order_delivery_date",
			                "b.agent_fob"
			            ])
			            ->leftJoin('mr_order_entry AS m', 'm.order_id', '=', 'co.mr_order_entry_order_id')
			            ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'm.order_id')
			            ->where('co.cm_sales_contract_amend_id', $amend->id)
			            ->get();
			foreach ($sales_order as $sales_con) {
				$sales_con->po_delivery_date= DB::table("mr_purchase_order")
				            					->where('mr_order_entry_order_id', $sales_con->mr_order_entry_order_id)
				            					->orderBy('po_ex_fty', "DESC")
				            					->pluck('po_ex_fty')
				            					->first();

			}
		$existingOrders= SalesContractOrder::where('cm_sales_contract_id', $id)
											->pluck('mr_order_entry_order_id')
											->toArray();



		$orderList = DB::table('mr_order_entry AS m')
            ->select(
                "m.order_code",
                "m.order_id",
                "m.order_qty",
                "m.order_delivery_date",
                "b.agent_fob"
            )
            ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'm.order_id')
            ->where('m.mr_buyer_b_id', $sales->mr_buyer_b_id)
            ->where('m.unit_id', $sales->hr_unit_id)
            ->whereNotIn('order_id', $existingOrders)
            ->get();

            foreach ($orderList as  $value)
            {
            	$po_last_date= DB::table("mr_purchase_order")
            					->where('mr_order_entry_order_id', $value->order_id)
            					->orderBy('po_ex_fty', "DESC")
            					->pluck('po_ex_fty')
            					->first();

                $list.='<label>
                          <input name="selected_item[]" type="checkbox" value="'.$value->order_id.'" class="ace checkbox-input">
                          <span class="lbl">'. $value->order_code.'</span>
                           <input type="hidden" class="qty" value="'.$value->order_qty.'">
                           <input type="hidden" class="fob" value="'.$value->agent_fob.'">
                           <input type="hidden" class="ord_del_date" value="'.$value->order_delivery_date.'">
                           <input type="hidden" class="po_del_date" value="'.$po_last_date.'">
                        </label>';
            }

		return view('merch/salescontract/salescontract_amend', compact('buyer','bank','unit','sales', 'amend', 'sales_order','list'));
    }

    //amendment store
    public function amendmentStore(Request $request){
    	// dd($request->all());
    	$validator= Validator::make($request->all(), [
    		'contract_qty'      => 'required|max:45',
            'contract_value'    => 'required|max:45',
            'elc_date'      	=> 'required|max:45',
            'exp_date'      	=> 'required|max:45',
            'con_id'    => 'required|max:11'
    	]);

    	if($validator->fails()){
    		dd("oh no");
    		return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
    	}
    	else{
    		// dd($request->all());
    		DB::beginTransaction();
    		try {

	    		$previous_amend_no= SalesContractAmend::where('cm_sales_contract_id', $request->con_id)
	    								->value(DB::raw('max(amend_no)'));

    			$amend= new SalesContractAmend();

	    		$amend->amend_no = $previous_amend_no+1;
	    		$amend->elc_amend_date = $request->elc_date;
	    		$amend->contract_qty = $request->contract_qty;
	    		$amend->contract_value = $request->contract_value;
	    		$amend->cm_sales_contract_id = $request->con_id;
	    		$amend->expire_date = $request->exp_date;

	    		$amend->save();
				$last_id = $amend->id;
				if(!empty($request->order_id)){

	             	for($i=0; $i<sizeof($request->order_id); $i++){
	                  	SalesContractOrder::insert([
	                      	'cm_sales_contract_id'     => $request->con_id,
	                      	'mr_order_entry_order_id'     => $request->order_id[$i],
	                      	'contract_fob'     => ($request->new_fob[$i] != null)? $request->new_fob[$i]: 0,
	                      	'contract_value'     => ($request->new_order_value[$i] != null)? $request->new_order_value[$i]:0,
	                      	'cm_sales_contract_amend_id'  => $last_id
	                  	]);
	              	}
	            }

	            DB::commit();
	            $this->logFileWrite("Sales Contract Amendment Saved", $request->con_id); //log entry

			    return redirect("merch/sales_contract/sales_contract_list")
			        	->with('success', "Contract Sales Amendment Saved Successfully!!");

    		} catch (\Exception $e) {
    			DB::rollback();
	        		$msg= $e->getMessage();
	        		return back()
		                ->with('error', $msg);
    		}

    	}
    }
}
