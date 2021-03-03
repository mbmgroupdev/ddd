<?php

namespace App\Http\Controllers\Merch\TimeAction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\TnaLibrary;
use App\Models\Merch\TnaTemplate;
use App\Models\Merch\OrderTNA;
use App\Models\Merch\TnaTemplatetoLibrary;
Use DB, ACL, Validator, DataTables, DateTime, DatePeriod, DateInterval, stdClass;

class TnaStatusController  extends Controller
{
	public function tnaShowStatus()
    {

    #-----------------------------------------------------------#
	    $tnainfo=[]; $pp=[]; $tnatable=[];
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
					 $team = $executive_associateId;
			}else{
			 $team =[];
			}

			if(!empty($team)){
				$order_tna=OrderTNA::join('mr_order_entry AS a', 'mr_order_tna.order_id', '=', 'a.order_id')
									    		->select(
														'a.order_delivery_date',
														DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d') as start_date"),
														'a.order_code',
														'mr_order_tna.*'
													)
													->whereIn('a.created_by',$team)
													->get();
			}else{
				$order_tna=OrderTNA::join('mr_order_entry AS a', 'mr_order_tna.order_id', '=', 'a.order_id')
									    		->select(
														'a.order_delivery_date',
														DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d') as start_date"),
														'a.order_code',
														'mr_order_tna.*'
													)
													->get();
			}


	    if(count($order_tna)){
	    	$tnatable=$this->tnaTableHead();

	    	//dd($order_tna);
	    	foreach ($order_tna as $key => $tna) {



	        	$library=DB::table('mr_tna_template_to_library AS l')
	                    ->select([
	                              'l.*',
	                              'tm.tna_temp_name',
	                              'tl.tna_lib_action',
	                              'tl.tna_lib_code',
	                              'tl.id as tlid',
	                              'ta.actual_date'

	                          ])
	                    ->leftJoin('mr_tna_template AS tm', 'tm.id', '=', 'l.mr_tna_template_id')
	                    ->leftJoin('mr_tna_library AS tl', 'tl.id', '=', 'l.mr_tna_library_id')
	                    ->join('mr_order_tna_action as ta', function($join)
	                     {
	                       $join->on('ta.mr_tna_library_id', '=', 'l.mr_tna_library_id');
	                       $join->on('ta.mr_tna_template_id', '=', 'l.mr_tna_template_id');

	                     })
	                    ->where('ta.mr_order_entry_order_id', $tna->id)
	                    ->orderBy('tl.id','ASC')
	                    ->get();

	        if(!empty($library) && count($library)>0){


	            $GDD=date('Y-m-d', strtotime($tna->order_delivery_date));
	            $deldate=date_create($GDD);

	            $totaldays =$library->sum('offset_day');
	            $dcdbegin = date('Y-m-d', strtotime('-'.$totaldays.' day', strtotime($GDD)));

	            //dd($GDD);
	            //dd($otbbegin);



	            $lead_tole= $tna->lead_days+$tna->tolerance_days ;
	            $yy=date('Y-m-d', strtotime('-'.$lead_tole.' day', strtotime($GDD)));

	            //dd($totaldays);
	            $tnaitems= new stdClass;
	            $tnaitems->delivery_date=$GDD;


		        $offset=0;$offset2=0;
		        if($dcdbegin>=$tnatable['start_date']){
		            $start_tna= date('Y-m-d', strtotime('-'.($library->first()->offset_day-1).' day', strtotime($dcdbegin)));
		            $tnaitems->start_period=(int)date_diff(date_create($tnatable['start_date']),date_create($start_tna))->format("%a");
		        }else{
		        	$tnaitems->start_period=0;
		        }

		            $tnaitems->end_period=(int)date_diff(date_create($GDD),$tnatable['end_date'])->format("%a");
		            $tnaitems->data=[];
		            $offset_minus=0;
		            $overtime=0; $fastdone=0;$isDue=0;$isFast=0; $sumOffset=0; $offset_prev=$library->first()->offset_day;
		            foreach($library AS $lib){
		                $offset_this=$lib->offset_day;
		                $offset2=$offset2+$lib->offset_day;

		                /*if($lib->tna_temp_logic=="OK to Begin"){
		                    $sg_date=date('Y-m-d', strtotime('-'.$offset2.' day', strtotime($yy)));
		                }*/

				        /*if($lib->tna_temp_logic=="DCD or FOB"){*/
				            $end_date_lib=date('Y-m-d', strtotime('+'.($offset2-$offset_this).' day', strtotime($dcdbegin)));
				       /* }*/
				        $start_date_lib=date('Y-m-d', strtotime("-".($offset_prev-1)." day", strtotime($end_date_lib)));


			     		if($start_date_lib>=$tnatable['start_date']){
					        $libitems= new stdClass;
			            	$libitems->start_date=$start_date_lib;
			            	$libitems->end_date=$end_date_lib;
			            	$libitems->actual_date= $lib->actual_date;
			            	$libitems->offset=$offset_prev;
		            		$libitems->column=$offset_prev;
		            		$libitems->rowaffect ='';
		            		$libitems->status= '';

			            	if($overtime>0){
			            		$libitems->column=$offset_prev-$overtime;
			            		$libitems->prevrowaffect =$overtime;
			            		$overtime=0;
			            	}
			            	if($fastdone>0){
			            		$libitems->column=$offset_prev+$fastdone;
			            		$libitems->nextrowaffect =$fastdone;
			            		$fastdone=0;
			            	}


			            	if($end_date_lib<=date('Y-m-d')  && $isDue==0){

				            	if($lib->actual_date !=null){

					            	if($end_date_lib==$lib->actual_date){
					            		$libitems->status= 'Completed';
					            	}else if($end_date_lib>$lib->actual_date){
					            		$isFast=1;
					            		$libitems->status= 'Fastdone';
					            		$fastdone=(int)date_diff(date_create($lib->actual_date),date_create($end_date_lib))->format("%a");
					            		$libitems->fastdone=$fastdone;
					            		$libitems->column=$offset_prev-$fastdone;
					            		$sumOffset=$fastdone;
					            	}else{
					            		$libitems->status= 'Late';
					            		$overtime=(int)date_diff(date_create($lib->actual_date),date_create($end_date_lib))->format("%a");
					            		$libitems->rowaffect=$overtime;
					            		$sumOffset=-($overtime);
					            	}
				            	}else{
				            		if(date('Y-m-d')>$end_date_lib){
				            			$isDue=1;
					            		$libitems->status= 'Due';
					            		$overtime=(int)date_diff(date_create(date('Y-m-d')),date_create($end_date_lib))->format("%a");
					            		$libitems->rowaffect=$overtime;
					            		$sumOffset=-($overtime);
					            	}else{
					            		$libitems->status= 'Active';
					            	}
				            	}
			            	}else if($start_date_lib <= date('Y-m-d') && $isDue==0){
			            		if($isFast==1){
				            		$libitems->status= 'Active';
				            		$libitems->isfast=$isFast;
				            		$isFast=0;
			            		}else{
			            			$libitems->status= 'Active';
			            		}
			            	}else{
			            		$libitems->status= 'Next';
			            		if($isDue==1){ $libitems->column= $libitems->column+$offset_minus; }
			            	}

			            	$tnaitems->data[$lib->tna_lib_code]=$libitems;
			            	$offset_prev=$lib->offset_day;

			            	$offset_minus = $libitems->column<0 ? $libitems->column:$offset_minus;
			            }else if($start_date_lib<$tnatable['start_date'] && $end_date_lib>= $tnatable['start_date']){

			            	$libitems= new stdClass;
			            	$libitems->start_date=$start_date_lib;
			            	$libitems->end_date=$end_date_lib;
		            		$libitems->column=(int)date_diff(date_create($tnatable['start_date']),date_create($end_date_lib))->format("%a")+1;
			            	$libitems->offset=$libitems->column;
			            	$tnaitems->data[$lib->tna_lib_code]=$libitems;



			            	if($end_date_lib<=date('Y-m-d')  && $isDue==0){

				            	if($lib->actual_date !=null){

					            	if($end_date_lib==$lib->actual_date){
					            		$libitems->status= 'Completed';
					            	}else if($end_date_lib>$lib->actual_date){
					            		$isFast=1;
					            		$libitems->status= 'Fastdone';
					            		$fastdone=(int)date_diff(date_create($lib->actual_date),date_create($end_date_lib))->format("%a");
					            		$libitems->fastdone=$fastdone;
					            		$libitems->column=$offset_prev-$fastdone;
					            		$sumOffset=$fastdone;
					            	}else{
					            		$libitems->status= 'Late';
					            		$overtime=(int)date_diff(date_create($lib->actual_date),date_create($end_date_lib))->format("%a");
					            		$libitems->rowaffect=$overtime;
					            		$sumOffset=-($overtime);
					            	}
				            	}else{
				            		if(date('Y-m-d')>$end_date_lib){
				            			$isDue=1;
					            		$libitems->status= 'Due';
					            		$overtime=(int)date_diff(date_create(date('Y-m-d')),date_create($end_date_lib))->format("%a");
					            		$libitems->rowaffect=$overtime;
					            		$sumOffset=-($overtime);
					            	}else{
					            		$libitems->status= 'Active';
					            	}
				            	}
			            	}else if($start_date_lib <= date('Y-m-d') && $isDue==0){
			            		if($isFast==1){
				            		$libitems->status= 'Active';
				            		$libitems->isfast=$isFast;
				            		$isFast=0;
			            		}else{
			            			$libitems->status= 'Active';
			            		}
			            	}else{
			            		$libitems->status= 'Next';
			            		if($isDue==1){ $libitems->column= $libitems->column+$offset_minus; }
			            	}

			            }

		            }
		            $tnaitems->offset_period=$library->last()->offset_day-1;
		            $tnainfo[$tna->order_code]=$tnaitems;

	            }
	            //$tnainfo[$tna->order_code]=$tnaitems;
	        }

        	//dd($tnainfo);
	    }




        //dd($tnainfo);


	    return view('merch.time_action.tna_status',compact('tnatable','tnainfo'));

    }


    public function tnaTableHead(){
    	$tna=OrderTNA::join('mr_order_entry AS a', 'mr_order_tna.order_id', '=', 'a.order_id')
	    		->select('a.order_delivery_date',DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d') as start_date"));
    	$tnatable=[];
    	$tnatable['start_date'] =(clone $tna)->orderBy('a.created_at','ASC')
	    						->first()->start_date;
	    $end_date   =$tna->orderBy('a.order_delivery_date','DESC')
	    						->first()->order_delivery_date;

	    $start_date = new \DateTime($tnatable['start_date']);
	    $end_date = new \DateTime($end_date);
	    $end_date = $end_date->add(new DateInterval('P11D'));
	    //$tnatable['cal_end']=$end_date;
	    $tnatable['column']= $end_date->diff($start_date)->format('%a');
	    $tnatable['end_date'] = $end_date;
	    $interval = DateInterval::createFromDateString('1 day');
	    $period = new DatePeriod($start_date, $interval, $end_date);
	    $row="";
	    foreach ($period as $dt) {
	    	if($dt->format("Y-m-d")==date('Y-m-d')){
	    		$row.="<th class='status-th-head' id='today'> <span class='date-float'>".$dt->format("d-m-y")."</span></th>";
	    	}else{
	    		$row.="<th class='status-th-head'><span class='date-float'>".$dt->format("d-m-y")."</span></th>";
	    	}

	    }

	    $tnatable['head']=$row;

	    return $tnatable;
    }
}
