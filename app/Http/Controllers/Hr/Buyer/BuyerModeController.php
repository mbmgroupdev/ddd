<?php

namespace App\Http\Controllers\Hr\Buyer;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBuyerSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

use App\User;
use DB,Hash,Validator,Auth;


class BuyerModeController extends Controller
{

	protected $atttable = [
		['name' => 'as_id', 'type' => 'bigInteger'],
        ['name' => 'in_date', 'type' => 'date'],
        ['name' => 'in_time', 'type' => 'timestamp', 'null' => 1, 'deafult' => null],
        ['name' => 'out_time', 'type' => 'timestamp', 'null' => 1, 'deafult' => null],
        ['name' => 'att_status', 'type' => 'string', 'length' => [2]],
        ['name' => 'flag', 'type' => 'string', 'length' => [2], 'null' => 1, 'deafult' => null],
        ['name' => 'remarks', 'type' => 'text','null' => 1, 'deafult' => null],
        ['name' => 'hr_shift_code', 'type' => 'string','length' => [20], 'null' => 1, 'deafult' => null],
        ['name' => 'ot_hour', 'type' => 'float', 'length' => [8, 3], 'null' => 1, 'deafult' => null],
        ['name' => 'late_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'line_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'created_by', 'type' => 'integer', 'null' => 1, 'deafult' => null]
	];


    protected $buyersalary = [
        ['name' => 'as_id', 'type' => 'integer'],
        ['name' => 'year', 'type' => 'integer'],
        ['name' => 'month', 'type' => 'string', 'length' => [2]],
        ['name' => 'gross', 'type' => 'float', 'deafult' => null],
        ['name' => 'basic', 'type' => 'float', 'deafult' => null],
        ['name' => 'house', 'type' => 'float', 'deafult' => null],
        ['name' => 'medical', 'type' => 'float', 'deafult' => null],
        ['name' => 'transport', 'type' => 'float', 'deafult' => null],
        ['name' => 'food', 'type' => 'float', 'deafult' => null],
        ['name' => 'late_count', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'present', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'holiday', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'absent', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'leave', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'absent_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'half_day_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'adv_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'cg_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'food_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'others_deduct', 'type' => 'float', 'deafult' => null],
        ['name' => 'salary_add', 'type' => 'float', 'deafult' => null],
        ['name' => 'bonus_add', 'type' => 'float', 'deafult' => null],
        ['name' => 'leave_adjust', 'type' => 'float', 'deafult' => null],
        ['name' => 'ot_rate', 'type' => 'float', 'deafult' => null],
        ['name' => 'ot_hour', 'type' => 'float', 'length' => [8, 3], 'null' => 1, 'deafult' => null],
        ['name' => 'attendance_bonus', 'type' => 'float', 'deafult' => null],
        ['name' => 'production_bonus', 'type' => 'float', 'deafult' => null],
        ['name' => 'stamp', 'type' => 'float', 'deafult' => null],
        ['name' => 'salary_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'total_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'bank_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'cash_payable', 'type' => 'float', 'deafult' => null],
        ['name' => 'tds', 'type' => 'float', 'deafult' => null],
        ['name' => 'pay_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'pay_type', 'type' => 'char', 'length' => [10], 'null' => 1, 'deafult' => null],
        ['name' => 'emp_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'unit_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'designation_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'subsection_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'location_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'ot_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'created_by', 'type' => 'integer', 'null' => 1, 'deafult' => null]
    ];

    /**
     * Create dynamic table along with dynamic fields
     *
     * @param       $table_name
     * @param array $fields
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTable($table_name, $fields = [])
    {
        // check if table is not already exists
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($fields, $table_name) {
                $table->increments('id');
                if (count($fields) > 0) {
                    foreach ($fields as $field) {
                        // check all properties first
                        if(isset($field['null']) && isset($field['length']) && isset($field['default'])){

                            $table->{$field['type']}($field['name'], $field['length'][0], $field['length'][1]??'')->nullable()->default($field['default']);

                        // if nullable and has default value
                    	}else if(isset($field['null']) && isset($field['default'])){

	                        $table->{$field['type']}($field['name'])->nullable()->default($field['default']);

                        // if nullable and has a length	
                    	}else if(isset($field['null']) && isset($field['length'])){

                            $table->{$field['type']}($field['name'], $field['length'][0], $field['length'][1]??'')->nullable();

                        // if  has a length  and default value  
                        }else if(isset($field['default']) && isset($field['length'])){

                            $table->{$field['type']}($field['name'], $field['length'][0], $field['length'][1]??'')->default($field['default']);

                        // if  has default value 
                        }else if(isset($field['default'])){

                            $table->{$field['type']}($field['name'])->default($field['default']);   

                        // if  has length 
                        }else if(isset($field['length'])){

                            $table->{$field['type']}($field['name'], $field['length'][0], $field['length'][1]??'');  

                        // if  nullable 
                        }else if(isset($field['null'])){

                            $table->{$field['type']}($field['name'])->nullable();

                        }else{

                    		$table->{$field['type']}($field['name']);

                    	}
                    }
                }
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });
 
            return 'success';
        }
 
        return 'failed';
    }

    public function buildUpdateQuery($table, $update)
    {
        if(count($update) > 0){
            $chunked = array_chunk($update, 300);

            foreach ($chunked as $key => $part) {
                # code...
                $qr = "update ".$table." set ";
                $cases = []; 
                $ids = [];
                foreach ($part as $key => $val) {
                    $ids[] = $val['id'];
                    foreach ($val['data'] as $k => $v) {
                        if($v){
                            $cases[$k][] =  "when ".$val['id']." then '".$v."'";
                        }
                    }
                }

                foreach ($cases as $k => $vl) {
                    if(collect($cases)->keys()->last() == $k){
                        $qr .= $k.' = case id '.implode(" ",$vl).' end ';
                    }else{
                        $qr .= $k.' = case id '.implode(" ",$vl).' end ,';
                    }
                }
                $qr .= " where id in (".implode(', ',$ids).")";

                DB::statement($qr);
            }
        }

        return 'success';

    }

    public function createUser($unit, $location, $name)
    {
        $user = new User();
        $user->name = $name;
        $user->associate_id = '';
        $user->email = $name.'@erp.com';
        $user->password = Hash::make('123456');
        $user->unit_permissions = $unit;
        $user->location_permission = $location;
        $user->created_by = auth()->user()->id??'';
        $user->save();

        $user->assignRole(['Buyer Mode']);

        return $user;
    }

    public function calculateOt($time_1, $time_2, $break){

        $diff = (strtotime($time_2) - (strtotime($time_1) + ($break*60)))/3600;
        if($diff < 0){
            $diff = 0;
        }
        // $diff = round($diff, 2);
        $diffExplode = explode('.', $diff);
        // return $diff;
        $minutes = (isset($diffExplode[1]) ? $diffExplode[1] : 0);
        $minutes = floatval('0.'.$minutes);
        // return $minutes;
        if($minutes > 0.16667 && $minutes <= 0.75) $minutes = $minutes;
        else if($minutes >= 0.75) $minutes = 1;
        else $minutes = 0;
        
        if($minutes > 0 && $minutes != 1){
            $min = (int)round($minutes*60);
            $minOT = min_to_ot();
            $minutes = $minOT[$min]??0;
        }

        $overtimes = $diffExplode[0]+$minutes;
        $overtimes = number_format((float)$overtimes, 3, '.', '');
        return $overtimes;
    }



    public function index(Request $request)
    {
    
        $templates = DB::table('hr_buyer_template')
                        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                        ->whereIn('hr_location', auth()->user()->location_permissions())
                        ->get();
        $unit = unit_list();
    	return view('hr.buyer.index', compact('unit','templates'));
    }

    public function generate(Request $request)
    {
        $alias = $request->table_alias;
        $input = $request->except('_token');
        $input['hr_location'] = 7;

        $buyer = DB::table('hr_buyer_template')->insert($input);

        $atttable = $this->createTable('hr_buyer_att_'.$alias, $this->atttable);
        $buyerhistory = $this->createTable('hr_buyer_salary_'.$alias, $this->buyersalary);
    	$user = $this->createUser($request->hr_unit_id, $input['hr_location'],$alias);

        return $buyerhistory;
    }

    public function syncIndex(Request $request, $id)
    {
        $month = $request->month??date('Y-m');


        $instance = Carbon::parse($month.'-01');
        $reqMonth = $instance->copy();
        $start_date = $instance->copy()->startOfMonth()->toDateString();
        $end_date = $instance->copy()->endOfMonth()->toDateString();

        $count = $instance->copy()->daysInMonth;


        $date = $instance->copy();
        $now = Carbon::now();
        if($date->diffInMonths($now) <= 6 ){
            $max = Carbon::now();
        }else{
            $max = $date->addMonths(6);
        }

        $months = [];
        $months[date('Y-m')] = 'Current';
        for ($i=1; $i <= 12 ; $i++) { 
            $months[$max->format('Y-m')] = $max->format('M, y');
            $max = $max->subMonth(1);
        }

        $date_array = [];
        for ($i=0; $i < $count; $i++) {
            $date_array[] = $instance->toDateString();
            $instance = $instance->addDay(); 
        }


        $date_array = collect($date_array)->chunk( ceil($count/2));

        $buyer = DB::table('hr_buyer_template')->where('id', $id)->first();

        $temp_info = DB::table('hr_buyer_template_detail')
                     ->where('month', $reqMonth->copy()->format('m'))
                     ->where('year', $reqMonth->copy()->format('Y'))
                     ->where('buyer_template_id', $id)
                     ->first();

        if($temp_info == null){
            $holidays = DB::table('hr_yearly_holiday_planner')
                        ->where('hr_yhp_unit', $buyer->hr_unit_id)
                        ->where('hr_yhp_dates_of_holidays','>=', $start_date)
                        ->where('hr_yhp_dates_of_holidays','<=', $end_date)
                        ->where('hr_yhp_open_status', 0)
                        ->get();
        }else{
            $holidays = json_decode($temp_info->holidays);
        }
       
        $getSynced = DB::table('hr_buyer_att_'.$buyer->table_alias)
                     ->select(DB::raw('count(*) as count'), 'in_date')
                     ->whereBetween('in_date', [$start_date, $end_date])
                     ->groupBy('in_date')
                     ->get()->keyBy('in_date');

        $unit = unit_list();

        return view('hr.buyer.sync', compact('buyer','unit','getSynced','date_array','reqMonth','temp_info','holidays','start_date','end_date','months'));
    }

    public function sync(Request $request, $id)
    {
        $buyer = DB::table('hr_buyer_template')->where('id', $id)->first();

        $count = $this->syncAtt($request->date, $buyer);

        return response([
            'status' => 'success',
            'count'  => $count
        ]);

    }

    public function holidays(Request $request, $id)
    {
        $buyer = DB::table('hr_buyer_template')->where('id', $id)->first();
        if(in_array($buyer->hr_unit_id, auth()->user()->unit_permissions())){
            $holidays = [];
            foreach ($request->holidays as $key => $val) {
                $holidays[$key] = [
                    'date' => $val,
                    'title' => $request->title[$key],
                    'type' => 1
                ];
            }

            $temp_info = DB::table('hr_buyer_template_detail')
                     ->where('month', $request->month)
                     ->where('year',  $request->year)
                     ->where('buyer_template_id', $id)
                     ->first();

            if($temp_info){
                DB::table('hr_buyer_template_detail')
                ->where('id', $temp_info->id)
                ->update([
                    'holidays' => json_encode($holidays)
                ]);
            }else{
                DB::table('hr_buyer_template_detail')
                ->insert([
                    'buyer_template_id' => $id,
                    'month' => $request->month,
                    'year'  => $request->year,
                    'holidays' => json_encode($holidays)
                ]);
            }
            return response([
                'status' => 1,
                'msg' => 'Proceed to sync!'
            ]);
        }

        return response([
                'status' => 0,
                'msg' => 'You are not authorised! '
            ]);
    }

    public function getlineShift($emplist, $date, $unit)
    {
        $instance = Carbon::parse($date);

        $s_field = 'day_'.((int) $instance->copy()->format('d'));

        $shift_history = DB::table('hr_shift_roaster')
                        ->where('shift_roaster_month', $instance->copy()->format('m'))
                        ->where('shift_roaster_year', $instance->copy()->format('Y'))
                        ->pluck($s_field,'shift_roaster_user_id');

        $lineInfo = DB::table('hr_station as h')
                    ->leftJoin('hr_as_basic_info as b', 'b.associate_id','h.associate_id')
                    ->select('b.as_id','h.changed_floor','h.changed_line')
                    ->whereDate('h.start_date','<=',$date)
                    ->where(function ($q) use($date) {
                      $q->whereDate('h.end_date', '>=', $date);
                      $q->orWhereNull('h.end_date');
                    })
                    ->get()->keyBy('as_id');

        $code = DB::table('hr_shift')
                ->where('hr_shift_unit_id', $unit)
                ->orderBy('hr_shift_id','ASC')
                ->where('created_at','<=',$date)
                ->pluck('hr_shift_code', 'hr_shift_name');

        if(count($lineInfo) > 0 || count($shift_history) > 0){
            $emplist = $emplist->map(function ($arr) use ($lineInfo, $shift_history, $code) {
                $as_id = $arr->as_id;
                if(isset($lineInfo[$as_id])){
                    $arr->df_line_id = $arr->as_line_id;
                    $arr->as_line_id = $lineInfo[$as_id]->changed_line;
                }
                
                if(isset($shift_history[$as_id])){
                    $arr->df_shift_id = $arr->as_shift_id;
                    $name = $code[$shift_history[$as_id]]??'';
                    $arr->as_shift_id = $name;
                }else{
                    if(isset($code[$arr->as_shift_id])){
                        $arr->as_shift_id = $code[$arr->as_shift_id];
                    }
                }
                return $arr;
            })->all();
        }


        return $emplist;
    }





    public function syncAtt($date, $buyer)
    {
        $ignore_subsec = explode(',', $buyer->ignore_subsec);
        $shift = shift_by_code();

        $mappedshift = collect($shift)->map(function($item) use ($date, $buyer){
            $limits = $this->getMaxMin($date, $item, $buyer->base_ot);
            $item['in_limit'] =  $limits['in_limit'];
            $item['out_limit'] =  $limits['out_limit'];
            return $item;
        });

        $ignore = DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->where('in_date', $date)
                    ->where('flag', 'm')
                    ->pluck('as_id')->toArray();

        # all active employees on that day
        $location = explode(',',$buyer->hr_location);
        $toDayEmps = DB::table('hr_as_basic_info')
                    ->select('as_id','associate_id','shift_roaster_status','as_shift_id','as_line_id','as_unit_id','as_ot','as_subsection_id')
                    ->where('as_unit_id', $buyer->hr_unit_id)
                    ->whereIn('as_location', $location)
                    ->whereNotIn('as_id', $ignore)
                    ->where(function($q) use ($date){
                        $q->where(function($qa) use ($date){
                            $qa->where('as_status',1);
                            $qa->where('as_doj' , '<=', $date);
                        });
                        $q->orWhere(function($qa) use ($date){
                            $qa->whereIn('as_status',[2,3,4,5,6,7,8]);
                            $qa->where('as_status_date' , '>', $date);
                        });

                    })
                    ->get();

        $emplist = collect($toDayEmps)->keyBy('as_id', true);

        // modify with line and shift of this day
        $emplist = $this->getlineShift($emplist, $date, $buyer->hr_unit_id);

        $toDayAs = collect($toDayEmps)->pluck('as_id')->toArray();

        $roasterAs =  collect($toDayEmps)
                        ->where('shift_roaster_status', 1)
                        ->pluck('as_id')->toArray();

        $shiftAs = collect($toDayEmps)
                        ->where('shift_roaster_status', 0)
                        ->pluck('as_id')->toArray();


        // roaster data
        $roaster = DB::table('holiday_roaster as l')
                    ->select('l.remarks', 'b.as_id')
                    ->leftJoin('hr_as_basic_info as b', 'b.associate_id','l.as_id')
                    ->where('l.date', $date)
                    ->whereIn('b.as_id', $toDayAs)
                    ->get();


        // roaster data
        $absent = DB::table('hr_absent as h')
                    ->leftJoin('hr_as_basic_info as b', 'b.associate_id','h.associate_id')
                    ->where('h.date', $date)
                    ->whereIn('b.as_id', $toDayAs)
                    ->pluck('b.as_id')->toArray();


        $r_general = collect($roaster)->where('remarks', 'General')
                        ->pluck('as_id')->toarray();

        $r_holiday = collect($roaster)->where('remarks', 'Holiday')
                        ->pluck('as_id')->toarray();
        
        $r_ot = collect($roaster)->where('remarks', 'OT')
                    ->pluck('as_id')->toarray();

        // get synced data
        $synced = DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->where('in_date', $date)
                    ->get()->keyBy('as_id');


        // get att data 
        $table = get_att_table($buyer->hr_unit_id);
        $att = DB::table($table)
                ->whereIn('as_id', $toDayAs)
                ->where('in_date', $date)
                ->get()->keyBy('as_id');

        
        // get planner data
        $planner = DB::table('hr_buyer_template_detail')
                    ->where([
                        'buyer_template_id' => $buyer->id,
                        'month' => date('m', strtotime($date)), 
                        'year' => date('Y', strtotime($date)) 
                    ])->first();

        $globalHoliday = collect(json_decode($planner->holidays))
                            ->keyBy('date')->toArray();


        $ins = []; $inserts = []; $updates = [];
        $attprocess = []; 

        // check if global holiday

        $common = [
            'in_date' => $date,
            'in_time' => null,
            'out_time' => null,
            'ot_hour' => 0,
            'remarks' => null,
            'late_status' => 0,
            'created_by' => auth()->id()
        ];

        if(in_array($date, array_keys($globalHoliday))){

            // for shift employees

            $sf_holiday = array_diff($shiftAs, $r_general);
            $sf_holiday = array_diff($sf_holiday, $r_ot);

            foreach ($sf_holiday as $key => $w) {
                $ins[$w]                    = $common;
                $ins[$w]['as_id']           = $w;
                $ins[$w]['hr_shift_code']   = $emplist[$w]->as_shift_id;
                $ins[$w]['line_id']         = $emplist[$w]->as_line_id;
                $ins[$w]['att_status']      = 'h';
                $ins[$w]['remarks']         = $globalHoliday[$date]->title;

                if(isset($synced[$w])){
                    if($synced[$w]->att_status == $ins[$w]['att_status'] && $synced[$w]->remarks == $ins[$w]['remarks']){

                    }else{
                        $updates[$w] = [
                            'data' => $ins[$w],
                            'id'   => $synced[$w]->id
                        ];
                    }
                }else{
                    $inserts[$w] = $ins[$w];
                }
            }

            // for roster employees
            $sf_roaster = array_intersect($shiftAs, $r_holiday);

            $holiday_emp = array_merge($sf_holiday, $sf_roaster);
            $attprocess = array_diff($toDayAs, $holiday_emp);
            
        }else{

            // for non holiday
            $attprocess = array_diff($toDayAs, $r_holiday);

            $sf_roaster = $r_holiday;
        }


        // assign day off for employees
        foreach ($sf_roaster as $key => $w) {
            $ins[$w]                    = $common;
            $ins[$w]['as_id']           = $w;
            $ins[$w]['hr_shift_code']   = $emplist[$w]->as_shift_id;
            $ins[$w]['line_id']         = $emplist[$w]->as_line_id;
            $ins[$w]['att_status']      = 'h';
            $ins[$w]['remarks']         = 'Day Off'; // replace with comment

            if(isset($synced[$w])){
                if($synced[$w]->att_status == $ins[$w]['att_status'] && $synced[$w]->remarks == $ins[$w]['remarks']){

                }else{
                    $updates[$w] = [
                        'data' => $ins[$w],
                        'id'   => $synced[$w]->id
                    ];
                }
            }else{
                $inserts[$w] = $ins[$w];
            }
        }

        // get leave data
        $leave =  DB::table('hr_leave AS l')
            ->leftJoin('hr_as_basic_info as b', 'b.associate_id','l.leave_ass_id')
            ->whereIn('b.as_id', $toDayAs)
            ->where('l.leave_status', 1)
            ->where('l.leave_from', '<=', $date)
            ->where('l.leave_to', '>=', $date)
            ->get()
            ->keyBy('as_id')->toArray();



        foreach ($attprocess as $key => $w) {
            $ins[$w]            = $common;
            $ins[$w]['as_id']   = $w;
            $ins[$w]['remarks'] = '';

            // set remarks
            if(in_array($w, $r_general)){
                $ins[$w]['remarks'] = 'General';
            }else if(in_array($w, $r_ot)){
                $ins[$w]['remarks'] = 'OT';
            }

            // if att exist
            if(isset($att[$w])){
                $a = $att[$w];
                $shiftData =  $mappedshift[$a->hr_shift_code];
                $ins[$w]['att_status']      = 'p';
                $ins[$w]['hr_shift_code']   = $a->hr_shift_code; // att shift
                $ins[$w]['late_status']     = $a->late_status;
                $ins[$w]['line_id']         = $a->line_id; // att line

                // ignore subsection
                if(in_array($emplist[$w]->as_subsection_id, $ignore_subsec)){
                    $ins[$w]['in_time']     = $a->in_time;
                    $ins[$w]['out_time']    = $a->out_time;
                    $ins[$w]['ot_hour']     = $a->ot_hour;
                }else{

                    if(($a->in_time >= $shiftData['in_limit'] || $a->in_time == null)  && ($a->out_time <= $shiftData['out_limit'] || $a->out_time == null) ){
                        // no changes needed
                            $ins[$w]['in_time']     = $a->in_time;
                            $ins[$w]['out_time']    = $a->out_time;
                            $ins[$w]['ot_hour']     = $a->ot_hour;

                    }else if($a->out_time > $shiftData['out_limit'] ){
                        // only out time modify
                        $ins[$w]['out_time'] = Carbon::parse($shiftData['out_limit'])->subSeconds(rand(0,839))->format('Y-m-d H:i:s');
                        $ins[$w]['in_time'] = $a->in_time;

                        if($a->in_time != null && $emplist[$w]->as_ot == 1){
                            $ins[$w]['ot_hour'] = $buyer->base_ot;

                        }else{
                            $ins[$w]['ot_hour'] = 0;
                        }


                    }else if($a->in_time < $shiftData['in_limit']  ){
                        // only intime modify
                        $ins[$w]['in_time'] = Carbon::parse($shiftData['in_limit'])->addSeconds(rand(0,419))->format('Y-m-d H:i:s');
                        $ins[$w]['out_time']    = $a->out_time;
                        $ins[$w]['ot_hour']     = $a->ot_hour;

                    }

                    // if full day ot
                    if($ins[$w]['in_time'] != null && $ins[$w]['out_time'] != null && $ins[$w]['remarks'] == 'OT' && $emplist[$w]->as_ot == 1){
                        $ins[$w]['ot_hour'] = $this->calculateOt(($date.' '.$shiftData['hr_shift_start_time']), $ins[$w]['out_time'], $shiftData['hr_shift_break_time']);
                    }
                }
            }else if(isset($leave[$w])){
                // if leave exist
                $ins[$w]['hr_shift_code']   = $emplist[$w]->as_shift_id;
                $ins[$w]['line_id']         = $emplist[$w]->as_line_id;
                $ins[$w]['att_status']      = 'l';
                $ins[$w]['remarks']         = $leave[$w]->leave_type;

            }else{
                // make absent
                $ins[$w]['hr_shift_code']   = $emplist[$w]->as_shift_id;
                $ins[$w]['line_id']         = $emplist[$w]->as_line_id;
                $ins[$w]['att_status']      = 'a';
            }

            if(isset($synced[$w])){
                $atn = $synced[$w];
                if($atn->att_status != $ins[$w]['att_status']  || $atn->ot_hour != $ins[$w]['ot_hour'] || $ins[$w]['hr_shift_code'] != $atn->hr_shift_code || $ins[$w]['remarks'] != $atn->remarks){

                    $updates[$w] = [
                        'data' => $ins[$w],
                        'id'   => $synced[$w]->id
                    ];
                }
            }else{
                $inserts[$w] = $ins[$w];
            }

        } 

        if(count($inserts) > 0){
            $chunked = array_chunk($inserts, 300);
            foreach ($chunked as $key => $insert) {

                DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->insertOrIgnore($insert);

                $insertIds = collect($insert)->pluck('as_id')->toArray();

                
            }

        }

        // build update query
        $this->buildUpdateQuery('hr_buyer_att_'.$buyer->table_alias, $updates);

        if(isset($request->process)){
            if(count($inserts) > 0){
                $chunked = array_chunk($inserts, 50);
                foreach ($chunked as $key => $insert) {
                    $queue = (new ProcessBuyerSalary($buyer, date('m', strtotime($date)), date('Y', strtotime($date)), $insert))
                                ->onQueue('buyersalary')
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);
                }
            }
            if(count($updates) > 0){
                $chunked = array_chunk($updates, 50);
                foreach ($chunked as $key => $insert) {

                    $queue = (new ProcessBuyerSalary($buyer, date('m', strtotime($date)), date('Y', strtotime($date)), array_keys($insert)))
                                ->onQueue('buyersalary')
                                ->delay(Carbon::now()->addSeconds(2));
                                dispatch($queue);
                }

            }
        }

        return count($ins);

    }

    public function processSalary(Request $request, $id)
    {
        $buyer = DB::table('hr_buyer_template')->where('id', $id)->first();
        if($buyer){

            $month = $request->month??date('m');
            $year = $request->year??date('Y');
            $instance = Carbon::parse($year.'-'.$month.'-01');
            $start_date = $instance->copy()->startOfMonth()->toDateString();
            $end_date = $instance->copy()->endOfMonth()->toDateString();
            $data = DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->where('in_date','>=',$start_date)
                    ->where('in_date','<=',$end_date)
                    ->distinct()
                    ->pluck('as_id')
                    ->toArray();



            $chunked = collect($data)->chunk(50);

            foreach ($chunked as $key => $insert) {
                
                $queue = (new ProcessBuyerSalary($buyer, $month, $year, $insert))
                        ->onQueue('buyersalary')
                        ->delay(Carbon::now()->addSeconds(2));
                        dispatch($queue);
            }

        }

        return $data;
    }


    public function getMaxMin($date, $shift, $max)
    {
        $shift_start = $date." ".$shift['hr_shift_start_time'];
        $shift_end = $date." ".$shift['hr_shift_end_time'];
        $break = $shift['hr_shift_break_time'];
        $shift_in_time = Carbon::createFromFormat('Y-m-d H:i:s', $shift_start);
        $shift_out_time = Carbon::createFromFormat('Y-m-d H:i:s', $shift_end);
    
        if($shift_out_time < $shift_in_time){
            $shift_out_time = $shift_out_time->copy()->addDays(1);
        }

        $sft['in_limit'] = $shift_in_time->copy()->subMinutes(7)->format('Y-m-d H:i:s'); 
        $sft['out_limit'] = $shift_out_time->copy()->addMinutes(($max*60 + $break + 7))->format('Y-m-d H:i:s'); 

        return $sft;

    }


    





}
