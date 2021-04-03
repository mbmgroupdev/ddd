<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Unit;
use App\Models\Hr\Line;
use App\Models\Hr\Area;
use App\Models\Hr\Station;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, DB, DataTables, stdClass, Cache;

class ReportController extends Controller
{

    public function machineModuleRedirect()
    {
        return redirect('http://123.200.11.74/apex/f?p=128:LOGIN_DESKTOP:451327097048:::::');
    }
    
    public function line()
    {
    	$lines = Employee::where('as_line_id', '!=', null)
    	->get()
    	->groupBy('as_line_id');


    	return view('hr.line_report', compact('lines'));
    }


    public function mmr(Request $request)
    {
        
    	
    	$date = $request->date??date('Y-m-d');
        $all_unit = unit_by_id();
    	$units  = auth()->user()->unit_permissions();
    	$present = array();

        foreach ($units as $key => $u) {
            $table = get_att_table($u);

            $present[$u] = DB::table($table.' AS a')
                    ->where('a.in_date', $date)
                    ->select(
                        DB::raw('count(*) AS count'),
                        'b.as_subsection_id'
                    )
                    ->leftJoin('hr_as_basic_info AS b', 'a.as_id', 'b.as_id')
                    ->when(in_array($u, [1,4,5]), function ($q) {
                        // ignore head office and washing department
                        return $q->where('b.as_location', '!=', 12)->where('b.as_department_id', '!=', 67);
                    })
                    ->where('b.as_unit_id',$u)
                    ->groupBy('b.as_subsection_id')
                    ->pluck('count', 'b.as_subsection_id')->toArray();

            $unit[$u]['present'] = array_sum($present[$u]);
            $unit[$u]['name'] = $all_unit[$u]['hr_unit_name'];
            $op = ($present[$u][138]??0)+($present[$u][219]??0)+($present[$u][214]??0)+($present[$u][306]??0);
            $unit[$u]['operator'] = $op;

            $mmr = round(($unit[$u]['present']/($op < 1?1:$op)),2);
            
            $unit[$u]['mmr'] = $mmr;

            $chart_data[] = array(
                'Unit' => $all_unit[$u]['hr_unit_short_name'],
                'MMR'  => $mmr
            );
        }

    	return view('common.daily_mmr_report', compact('chart_data','unit'));
    }

    public function monthlyOT(Request $request)
    {
        $unitList = Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->orderBy('hr_unit_name', 'desc')->pluck('hr_unit_short_name','hr_unit_id');
        unset($unitList[4], $unitList[5]);

        $unit = [];
        if($request->unit != null){
            $unit[] = $request->unit;
        }else{
            if(in_array(1, auth()->user()->unit_permissions())){
                $unit[] = 1;
            }elseif(in_array(2, auth()->user()->unit_permissions())){
                $unit[] = 2;
            }elseif(in_array(3, auth()->user()->unit_permissions())){
                $unit[] = 3;
            }elseif(in_array(8, auth()->user()->unit_permissions())){
                $unit[] = 8;
            }
        }
        
        ini_set('zlib.output_compression', 1);
        $month = $request->month??date('Y-m');
        $monthFormat = Carbon::createFromFormat("Y-m", $month);
        $start_date = $monthFormat->copy()->firstOfMonth();
        $end_date = $monthFormat->copy()->lastOfMonth()->format('Y-m-d');
        $empAs = auth()->user()->permitted_asid();
        // $att_table = $request->unit??[1,2,3,8];
        $att_table = $unit;

        $date = Carbon::parse($month);
        $now = Carbon::now();
        if($date->diffInMonths($now) <= 6 ){
            $max = Carbon::now();
        }else{
            $max = $date->addMonths(6);
        }

        $months = [];
        $data = [];
        $months[date('Y-m')] = 'Current';
        for ($i=1; $i <= 6 ; $i++) { 
            $months[$max->format('Y-m')] = $max->format('M, y');
            $max = $max->subMonth(1);
        }

        foreach ( $att_table as $key => $u) {
            if(in_array($u, auth()->user()->unit_permissions())){

                $table = get_att_table($u);

                $data[$u] = DB::table($table)
                    ->select(
                        DB::raw('sum(ot_hour) as total_ot'),
                        DB::raw('max(ot_hour) as maximum'),
                        DB::raw('count(*) as emp'),
                        'in_date'
                    )
                    ->where('in_date','>=',$start_date->format('Y-m-d'))
                    ->where('in_date','<=',$end_date)
                    ->whereIn('as_id',$empAs)
                    ->where('ot_hour','>',0)
                    ->groupBy('in_date')
                    ->get()
                    ->keyBy('in_date');
            }
        }


        $totalday = date('d', strtotime($end_date));
        $chart_data = [];
        $chart_ot = [];
        $otdata = [];
        
        for ($i=0; $i < $totalday; $i++) { 
            $otday = $start_date->copy()->addDays($i)->format('Y-m-d');
            $thisday = $start_date->copy()->addDays($i)->format('d');
            $maxOT = 0;
            $employee = 0;
            $ot = 0;
            $avg = 0;
            foreach ($data as $key => $u) {
                if(isset($u[$otday])){
                    $employee += $u[$otday]->emp??0; 
                    $ot += $u[$otday]->total_ot??0; 
                    if($u[$otday]->maximum > $maxOT){
                        $maxOT = $u[$otday]->maximum??0;  
                    }
                }
            }

            $otdata[$i]['emp'] = $employee;
            $otdata[$i]['date'] = $otday;
            $otdata[$i]['ot_hour'] = $ot;
            $otdata[$i]['max'] = $maxOT;
            $otdata[$i]['avg'] = round($ot/($employee == 0?1:$employee),2);
            
            $chart_data[] = array(
                'Date' => $thisday,
                'Avg'  => $maxOT
            );

            $chart_ot[] = array(
                'Date' => $thisday,
                'totalOt' => $ot
            );
        }

        $selectUnit = $unit[0];
        return view('common.monthly_ot', compact('chart_data', 'chart_ot','otdata', 'month', 'months', 'unitList', 'selectUnit'));

    }


    public function monthlyMMR(Request $request)
    {
        $month = $request->month??date('Y-m');
        $monthFormat = Carbon::createFromFormat("Y-m", $month);
        $start_date = $monthFormat->copy()->firstOfMonth();
        $end_date = $monthFormat->copy()->lastOfMonth()->format('Y-m-d');

 
        $units  = unit_by_id();
        $operator = DB::table('hr_as_basic_info')
                    ->where('as_status', 1)
                    ->where('as_subsection_id', 138)
                    ->orWhere('as_subsection_id', 54)
                    ->count();

        $present = array();

        $mbm = DB::table('hr_attendance_mbm')
                ->select(
                    DB::raw('count(*) AS count'),
                    'in_date'
                )
                ->where('in_date','>=',$start_date->format('Y-m-d'))
                ->where('in_date','<=',$end_date)
                ->groupBy('in_date')
                ->pluck('count', 'in_date');

        $ceil = DB::table('hr_attendance_ceil')
                ->select(
                    DB::raw('count(*) AS count'),
                    'in_date'
                )
                ->where('in_date','>=',$start_date->format('Y-m-d'))
                ->where('in_date','<=',$end_date)
                ->groupBy('in_date')
                ->pluck('count', 'in_date');

        $aql = DB::table('hr_attendance_aql')
                ->select(
                    DB::raw('count(*) AS count'),
                    'in_date'
                )
                ->where('in_date','>=',$start_date->format('Y-m-d'))
                ->where('in_date','<=',$end_date)
                ->groupBy('in_date')
                ->pluck('count', 'in_date');

        $cew = DB::table('hr_attendance_cew')
                ->select(
                    DB::raw('count(*) AS count'),
                    'in_date'
                )
                ->where('in_date','>=',$start_date->format('Y-m-d'))
                ->where('in_date','<=',$end_date)
                ->groupBy('in_date')
                ->pluck('count', 'in_date');

            
        
        $totalday = date('d', strtotime($end_date));
        $chart_data = [];
        for ($i=0; $i < $totalday; $i++) { 
            $otday = $start_date->copy()->addDays($i)->format('Y-m-d');
            $thisday = $start_date->copy()->addDays($i)->format('d M');

            $att = 0 ;
            if(isset($mbm[$otday])){
                $att += $mbm[$otday];  
            }
            if(isset($ceil[$otday])){
                $att += $ceil[$otday];  
            }
            if(isset($aql[$otday])){
                $att += $aql[$otday];  
            }
            if(isset($cew[$otday])){
                $att += $cew[$otday];  
            }

            $mmr = round(($att/$operator),2);

            $chart_data[] = array(
                'Date' => $thisday,
                'MMR'  => $mmr
            );
        }

        return view('common.monthly_mmr_report', compact('chart_data'));

    }

    


    public function otEmpAttendance($unit = null, $date = null, $ot)
    {
		$tablename = get_att_table($unit).' AS a';
		
		$data = array();

		$data['total'] = DB::table('hr_as_basic_info')
				 ->select([
					DB::raw('count(*) AS count'),
					'as_subsection_id'
				])
				//->whereDate('as_doj','<=', $date)
                ->where('as_unit_id', $unit)
				->whereIn('associate_id', auth()->user()->permitted_associate())
				->where('as_status',1) 
				->where('as_ot', $ot)
				->groupBy('as_subsection_id')
				->get()
				->filter(function ($item)
		        {
		            return $item->count > 0;
		        })
				->pluck('count','as_subsection_id')->toArray();


    	$data['present'] = DB::table($tablename)
    				->where('a.in_date', $date)
    				->select([
    					DB::raw('count(*) AS count'),
    					'b.as_subsection_id'
    				])
    				->leftJoin('hr_as_basic_info AS b', 'a.as_id', 'b.as_id')
    				->where('b.as_unit_id', $unit)
                    ->whereIn('b.associate_id', auth()->user()->permitted_associate())
    				->where('b.as_status',1) 
    				->where('b.as_ot', $ot)
    				->groupBy('b.as_subsection_id')
    				->get()
    				->pluck('count','as_subsection_id')->toArray();

        
		return $data;
    }
}