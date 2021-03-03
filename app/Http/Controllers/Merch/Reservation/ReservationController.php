<?php

namespace App\Http\Controllers\Merch\Reservation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\ProductType;
use App\Models\Merch\Reservation;
Use DB, ACL, Validator, DataTables,DateTime;

class ReservationController extends Controller
{
    public function showForm(){
    	$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
    	$buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
    	$prdtypList= ProductType::pluck('prd_type_name', 'prd_type_id');
    	return view('merch/reservation/reservation', compact('unitList', 'buyerList', 'prdtypList'));
    }
    //Store Capacity Reservation
    public function storeData(Request $request){

    	$dat= new DateTime();
      $usr = DB::table('hr_as_basic_info')->where('associate_id',Auth()->user()->associate_id)->first();
        //Validation Rules
    	$validator= Validator::make($request->all(), [
    		'hr_unit_id'      => 'required|max:11',
    		'b_id'            => 'required|max:11',
    		'res_month'       => 'required',
    		'res_year'        => 'required',
    		'prd_type_id'     => 'required|max:11',
        'res_quantity'    => 'required|max:11',
    		'res_sewing_smv'  => 'required',
    		'res_sah'         => 'required'
    	]);
        //If Validation fails back to previous page
    	if($validator->fails()){
    		return back()
    			->withInput()
    			->with('error', "Incorrect Input!!");
    	}

    	try {
            //if validation successfull then store data and redirect to the List
            $res_month= date("m", strtotime($request->res_month));
            $resv = Reservation::where([
                    'hr_unit_id'=>$request->hr_unit_id,
                    'b_id'=>$request->b_id,
                    'res_month'=>$res_month,
                    'res_year'=>$request->res_year,
                    'prd_type_id'=>$request->prd_type_id
                ])->first();
            if($resv){
                return back()->withInput()
                             ->with('error', "This reservation already exists");
            }else{
                $data= new Reservation();
                $data->hr_unit_id   = $request->hr_unit_id;
                $data->b_id         = $request->b_id;
                $data->res_month    = $res_month;
                $data->res_year     = $request->res_year;
                $data->prd_type_id  = $request->prd_type_id;
                $data->res_quantity = $request->res_quantity;
                $data->res_sewing_smv = $request->res_sewing_smv;
                $data->res_sah      = $request->res_sah;
                $data->res_created_on       = $dat;
                $data->res_created_by       = $usr->as_id;
                $data->save();
                $this->logFileWrite("Capacity Reservation created", $data->id);
                return redirect('merch/reservation/reservation_edit/'.$data->id)->with('success', "Capacity Reservation saved successfully!!");
            }

        } catch (\Exception $e) {
            $bug=$e->errorInfo[1];
            $bug1=$e->errorInfo[2];
            if($bug == 1062){
                return back()->withInput()->with('error', "This reservation already exists");
            }else{
                return back()->withInput()->with('error', $bug1);
            }
        }
    }
    //Reservation List
    public function getReservationList(){
        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
        $buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
        $prdtypList= ProductType::pluck('prd_type_name', 'prd_type_id');
    	return view('merch/reservation/reservation_list', compact('unitList', 'buyerList', 'prdtypList'));
    }
    //Get capacity reservation List Data
    public function getReservationListData(){

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

      if(!empty($team)){
        $data= DB::table('mr_capacity_reservation AS cr')
      			->select(
      				'cr.*',
      				'u.hr_unit_name',
      				'b.b_name',
      				'pt.prd_type_name'
      			)
      			->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'cr.hr_unit_id')
      			->leftJoin('mr_buyer AS b', 'b.b_id', 'cr.b_id')
            ->whereIn('b.b_id', auth()->user()->buyer_permissions())
            ->whereIn('cr.res_created_by', $team)
      			->leftJoin('mr_product_type AS pt', 'pt.prd_type_id', 'cr.prd_type_id')
            ->orderBy('cr.res_id', 'DESC')
      			->get();
      }else{
        $data= DB::table('mr_capacity_reservation AS cr')
      			->select(
      				'cr.*',
      				'u.hr_unit_name',
      				'b.b_name',
      				'pt.prd_type_name'
      			)
      			->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'cr.hr_unit_id')
      			->leftJoin('mr_buyer AS b', 'b.b_id', 'cr.b_id')
            ->whereIn('b.b_id', auth()->user()->buyer_permissions())
      			->leftJoin('mr_product_type AS pt', 'pt.prd_type_id', 'cr.prd_type_id')
            ->orderBy('cr.res_id', 'DESC')
      			->get();
      }


		return DataTables::of($data)->addIndexColumn()
			->addColumn('month_year', function($data){
				$month_year= date('F', mktime(0, 0, 0, $data->res_month, 10)). "-". $data->res_year;

                return $month_year;
            })
			->addColumn('projection', function($data){
                return $data->res_quantity;
            })
			->addColumn('confirmed', function($data){
                //dd($data);exit;
                $ordered= DB::table('mr_order_entry')
                                ->where('res_id', $data->res_id)
                                ->select(DB::raw("SUM(order_qty) AS sum"))
                                ->first();
                return $ordered->sum;

            })
            ->addColumn('balance', function($data){
                $ordered= DB::table('mr_order_entry')
                                ->where('res_id', $data->res_id)
                                ->select(DB::raw("SUM(order_qty) AS sum"))
                                ->first();
                return $data->res_quantity - $ordered->sum;

            })
            ->addColumn('status', function ($data){

                $mytime = date("Y-m-d");
               $mnth = explode('-',$mytime);
                // dd((int)$mnth[1]+1);
               // dd((int)$data->res_month + 1);exit;

               if(((int)$mnth[0] == (int)$data->res_year) && (((int)$mnth[1]) >= (int)$data->res_month - 1) ){
                  $rdata = '<button class="btn btn-xs btn-danger btn-round" rel="tooltip" data-tooltip="Date Expired" data-tooltip-location="top" >
                            Closed</button>';

               }else{

                $rdata = '';
               }
               return $rdata;

            })
			->addColumn('action', function ($data) {
               $mytime = date("Y-m-d");
               $mnth = explode('-',$mytime);
                // dd((int)$mnth[1]+1);
               // dd((int)$data->res_month + 1);exit;

               if(((int)$mnth[0] == (int)$data->res_year) && (((int)$mnth[1]) >= (int)$data->res_month - 1) ){
                  $rd = 'style="pointer-events: none;"';//1=closed

               }else{

                $rd = '';//2=notclosed
               }
                $action_buttons= "<div>
                        <a href=".url('merch/reservation/reservation_edit/'.$data->res_id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\" style=\"width:26px;\">
                            <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                        </a> ";
                        $ordered= DB::table('mr_order_entry')
                                ->where('res_id', $data->res_id)
                                ->select(DB::raw("SUM(order_qty) AS sum"))
                                ->first();
                        if($data->res_quantity > $ordered->sum) {
                            $action_buttons.= "<a href=".url('merch/orders/order_entry/'.$data->res_id)." class=\"btn btn-xs btn-primary\" rel='tooltip' title=\"Order Entry\"  style=\"width:26px;\">
                                <i class=\" ace-icon fa fa-plus bigger-120\"></i>
                            </a>";
                        }
                    $action_buttons.= "</div>";
                    return $action_buttons;
                })
            ->rawColumns(['action', 'month_year', 'projection', 'confirmed','status'])
            ->toJson();

    }
    //Edit form
    public function resEdit($id){
    	$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
    	$buyerList= Buyer::whereIn('b_id', auth()->user()->buyer_permissions())->pluck('b_name', 'b_id');
    	$prdtypList= ProductType::pluck('prd_type_name', 'prd_type_id');
    	$reserved= DB::table('mr_capacity_reservation')->where('res_id', $id)->first();
        //dd($reserved->res_month);
        $ordered_qty= DB::table('mr_order_entry')
                            ->where('res_id', $id)
                            ->select(
                                DB::raw("SUM(order_qty) AS ordered")
                            )
                            ->first();
        $ordered_qty= $ordered_qty->ordered;


        $reserved->edit_month = date('F', mktime(0, 0, 0, $reserved->res_month, 10));

        //disable function
        $mytime = date("Y-m-d");
        $mnth = explode('-',$mytime);

        if(((int)$mnth[0] == (int)$reserved->res_year) && (((int)$mnth[1]) >= (int)$reserved->res_month - 1) ){
            $rd = 'disabled';//1=closed
        }
        else{
            $rd = '';//2=notclosed
        }

        //disable function end

    	return view('merch/reservation/reservation_edit', compact('unitList','buyerList', 'prdtypList', 'reserved', 'ordered_qty','rd'));
    }
    //Update Capacity Reservation
    public function resUpdate(Request $request){

    	$dat= new DateTime(); //date time for update time and date
    	$usr= Auth()->user()->associate_id; //Updated by the associate(associate_id)
        //Validation Rules
    	$validator= Validator::make($request->all(), [
    		'hr_unit_id'      => 'required|max:11',
            'b_id'            => 'required|max:11',
            'res_month'       => 'required',
            'res_year'        => 'required',
            'prd_type_id'     => 'required|max:11',
            'res_quantity'    => 'required|max:11',
            'res_sewing_smv'  => 'required',
            'res_sah'         => 'required'
    	]);

    	if($validator->fails()){
            //Back to current page with error message
    		return back()
    			->withInput()
    			->with('error', "Incorrect Input!!");
    	}
    	else{
            //create month number from month name
            $res_month= date("m", strtotime($request->res_month));

            //update reservation
            $resv = Reservation::where([
                    'hr_unit_id'=>$request->hr_unit_id,
                    'b_id'=>$request->b_id,
                    'res_month'=>$res_month,
                    'res_year'=>$request->res_year,
                    'prd_type_id'=>$request->prd_type_id
                ])->first();
            // if($resv){
            //     return back()->withInput()
            //                  ->with('error', "This reservation already exists");
            // }else{
        		DB::table('mr_capacity_reservation')->where('res_id', $request->res_id)
        		->update([
        			'hr_unit_id' => $request->hr_unit_id,
            		'b_id' => $request->b_id,
            		'res_month' => $res_month,
            		'res_year' => $request->res_year,
            		'prd_type_id' => $request->prd_type_id,
            		'res_quantity' => $request->res_quantity,
                    'res_sewing_smv' => $request->res_sewing_smv,
            		'res_sah' => $request->res_sah,
            		'res_updated_on'=> $dat,
            		'res_updated_by' => $usr
        		]);
                $this->logFileWrite("Capacity Reservation updated", $request->res_id);
    			return redirect('merch/reservation/reservation_list')
    					->with('success', "Capacity Reservation Updated successfully!!");
            // }
		}
    }

    //Write Every Events in Log File
    public function logFileWrite($message, $event_id){
        $log_message = date("Y-m-d H:i:s")." \"".Auth()->user()->associate_id."\" ".$message." ".$event_id.PHP_EOL;
        $log_message .= file_get_contents("assets/log.txt");
        file_put_contents("assets/log.txt", $log_message);
    }
}
