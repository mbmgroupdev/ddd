<?php

namespace App\Http\Controllers\Hr\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use DB;

class BuyerJobCardController extends Controller
{
    public function index(Request $request)
    {
        if(isset($request->associate)){

        	$unit = unit_by_id();
        	$designation = designation_by_id();
        	$department = department_by_id();
        	$section = section_by_id();
            $subsection = subSection_by_id();
            $line = line_by_id();
            $floor = floor_by_id();

        	$associate = $request->associate;
        	$month = $request->month_year??date('Y-m');

        	$employee = DB::table('hr_as_basic_info')
        				->where('associate_id', $associate)->first();


        	// get job card start and end date
        	$instance = Carbon::parse($month.'-01');
            $start_date = $instance->copy()->startOfMonth()->toDateString();
            $end_date = $instance->copy()->endOfMonth()->toDateString();

        	$empdojMonth = date('Y-m', strtotime($employee->as_doj));

            if($employee->as_status_date){
                $statusMonth = date('Y-m', strtotime($employee->as_status_date));
                if($statusMonth == $month){
                	if(in_array($employee->as_status, [2,3,4,5,7] )){
                        $end_date = $employee->as_status_date;
                    }else if($employee->as_status == 1){
                        $start_date = $employee->as_status_date;
                    }

                }
            }

            if($empdojMonth == $month){
            	$start_date = $employee->as_doj;
            }

        	$buyer = DB::table('hr_buyer_template')->where('table_alias', auth()->user()->name)->first();



        	// get job card history

        	$att = DB::table('hr_buyer_att_'.$buyer->table_alias)
        			->whereBetween('in_date', [$start_date, $end_date])
        			->where('as_id', $employee->as_id)
        			->orderBy('in_date')
        			->get()->keyBy('in_date');

            /*$sum = DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->select(
                        DB::raw("
                            COUNT(CASE WHEN att_status = 'p' THEN as_id END) AS males,
                            COUNT(CASE WHEN att_status = 'l' THEN as_id END) AS females,
                            COUNT(CASE WHEN att_status = 'h' THEN as_id END) AS females,
                            COUNT(CASE WHEN as_ot = '0' THEN as_id END) AS non_ot")
                    )
                    ->whereBetween('in_date', [$start_date, $end_date])
                    ->where('as_id', $employee->as_id)
                    ->orderBy('in_date')
                    ->get()*/

        	$attdata = [];

        	$plusend = Carbon::parse($end_date)->addDay()->toDateString();
        	$period = new \DatePeriod(
    		     new \DateTime($start_date),
    		     new \DateInterval('P1D'),
    		     new \DateTime($plusend)
    		);

            $sum['p'] = 0;
            $sum['a'] = 0;
            $sum['ot'] = 0;
    		foreach ($period as $key => $dt) {
                $at = $att[$dt->format('Y-m-d')]??'';
    			if($at){
    				$attdata[$dt->format('Y-m-d')] = $at;
                    if($at->att_status == 'p') $sum['p']++;
                    else if($at->att_status == 'a') $sum['a']++;

                    $sum['ot'] += $at->ot_hour;

    			}else{
    				$attdata[$dt->format('Y-m-d')] = '';
                    $sum['a']++;
    			}
    		}

        	$jobcardview =  view('hr.buyer.front.buyer_job_card', 
        		compact('attdata', 'employee', 'month', 'att', 'unit','department','designation','section','subsection','associate','sum','line','floor')
        	)->render();
        }else{
            $jobcardview = '';
        }


    	return view('hr.buyer.front.jobcard', compact('jobcardview'));

    }

    public function edit(Request $request)
    {
        if(isset($request->associate)){

            $unit = unit_by_id();
            $designation = designation_by_id();
            $department = department_by_id();
            $section = section_by_id();
            $subsection = subSection_by_id();
            $shift = shift_by_code();

            $associate = $request->associate;
            $month = $request->month_year??date('Y-m');

            $employee = DB::table('hr_as_basic_info')
                        ->where('associate_id', $associate)->first();


            // get job card start and end date
            $instance = Carbon::parse($month.'-01');
            $start_date = $instance->copy()->startOfMonth()->toDateString();
            $end_date = $instance->copy()->endOfMonth()->toDateString();

            $shifts = $this->getShiftByCode($employee->as_unit_id, $end_date);
            $empdojMonth = date('Y-m', strtotime($employee->as_doj));

            if($employee->as_status_date){
                $statusMonth = date('Y-m', strtotime($employee->as_status_date));
                if($statusMonth == $month){
                    if(in_array($employee->as_status, [2,3,4,5,7] )){
                        $end_date = $employee->as_status_date;
                    }else if($employee->as_status == 1){
                        $start_date = $employee->as_status_date;
                    }

                }
            }

            $shift_history = DB::table('hr_shift_roaster')
                             ->where('shift_roaster_user_id', $employee->as_id)
                             ->where('shift_roaster_month', $instance->copy()->format('m'))
                             ->where('shift_roaster_year', $instance->copy()->format('Y'))
                             ->first();

            $default_shift = $shifts[$employee->as_shift_id];


            if($empdojMonth == $month){
                $start_date = $employee->as_doj;
            }

            $buyer = DB::table('hr_buyer_template')->where('id', 1)->first();

            // get job card history

            $att = DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->whereBetween('in_date', [$start_date, $end_date])
                    ->where('as_id', $employee->as_id)
                    ->orderBy('in_date')
                    ->get()->keyBy('in_date');

            $attdata = [];

            $plusend = Carbon::parse($end_date)->addDay()->toDateString();
            $period = new \DatePeriod(
                 new \DateTime($start_date),
                 new \DateInterval('P1D'),
                 new \DateTime($plusend)
            );


            foreach ($period as $key => $dt) {
                if(isset($att[$dt->format('Y-m-d')])){
                    $attdata[$dt->format('Y-m-d')] = $att[$dt->format('Y-m-d')];
                }else{
                    $attdata[$dt->format('Y-m-d')] = '';
                }
            }

            $jobcardview =  view('hr.buyer.front.buyer_job_card_edit', 
                compact('attdata', 'employee', 'month', 'att', 'unit','department','designation','section','subsection','associate','shift','default_shift')
            )->render();
        }else{
            $jobcardview = '';
        }


        return view('hr.buyer.front.jobcard_edit', compact('jobcardview'));

    }

    public function getShiftByCode($unit, $date)
    {
        return DB::table('hr_shift')
            ->where('hr_shift_unit_id', $unit)
            ->orderBy('hr_shift_id','ASC')
            ->where('created_at','<=',$date)
            ->pluck('hr_shift_code', 'hr_shift_name');
    }


    
}
