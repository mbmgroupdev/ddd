<?php

namespace App\Http\Controllers\Hr\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

use DB;

class BuyerModeController extends Controller
{

	protected $atttable = [
		['name' => 'as_id', 'type' => 'bigInteger'],
        ['name' => 'in_date', 'type' => 'date'],
        ['name' => 'in_time', 'type' => 'timestamp', 'null' => 1, 'deafult' => null],
        ['name' => 'out_time', 'type' => 'timestamp', 'null' => 1, 'deafult' => null],
        ['name' => 'att_status', 'type' => 'string', 'length' => [2]],
        ['name' => 'remarks', 'type' => 'text','null' => 1, 'deafult' => null],
        ['name' => 'hr_shift_code', 'type' => 'string','length' => [20], 'null' => 1, 'deafult' => null],
        ['name' => 'ot_hour', 'type' => 'float', 'length' => [8, 3], 'null' => 1, 'deafult' => null],
        ['name' => 'late_status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'line_id', 'type' => 'integer', 'null' => 1, 'deafult' => null],
        ['name' => 'created_by', 'type' => 'integer', 'null' => 1, 'deafult' => null]
	];


    protected $buyerhistory = [
        ['name' => 'buyer_template_id', 'type' => 'integer'],
        ['name' => 'year', 'type' => 'tinyInteger'],
        ['name' => 'month', 'type' => 'string', 'length' => [2]],
        ['name' => 'status', 'type' => 'tinyInteger', 'null' => 1, 'deafult' => null],
        ['name' => 'holidays', 'type' => 'json','null' => 1,'deafult' => null],
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
                $table->timestamps();
            });
 
            return 'success';
        }
 
        return 'failed';
    }

    public function index(Request $request)
    {
        $templates = DB::table('hr_buyer_template')->get();
        $unit = unit_list();
    	return view('hr.buyer.index', compact('unit','templates'));
    }

    public function generate(Request $request)
    {
        $alias = $request->table_alias;
        $buyer = DB::table('hr_buyer_template')->insert($request->except('_token'));

        $atttable = $this->createTable('hr_buyer_att_'.$alias, $this->atttable);
    	$buyerhistory = $this->createTable('hr_buyer_template_history_'.$alias, $this->buyerhistory);

        return redirect('hr/buyer');
    }

    public function syncIndex(Request $request, $id)
    {
        $month = $request->month??date('Y-m');
        $instance = Carbon::parse($month.'-01');
        $start_date = $instance->copy()->startOfMonth()->toDateString();
        $end_date = $instance->copy()->endOfMonth()->toDateString();

        $count = $instance->copy()->daysInMonth;

        $date_array = [];
        for ($i=0; $i < $count; $i++) {
            $date_array[] = $instance->toDateString();
            $instance = $instance->addDay(); 
        }


        $date_array = collect($date_array)->chunk( ceil($count/2));

        $buyer = DB::table('hr_buyer_template')->where('id', $id)->first();
        if(Schema::hasTable('hr_buyer_template_history_'.$buyer->table_alias)){

            $holidays = DB::table('hr_buyer_template_history_'.$buyer->table_alias)
                         ->where('month', $instance->format('m'))
                         ->where('year', $instance->format('Y'))
                         ->where('buyer_template_id', $id)
                         ->first();
            }else{
               $holidays = []; 
            }

        $getSynced = DB::table('hr_buyer_att_'.$buyer->table_alias)
                     ->select(DB::raw('count(*) as count'), 'in_date')
                     ->whereBetween('in_date', [$start_date, $end_date])
                     ->groupBy('in_date')
                     ->get()->keyBy('in_date');

        $unit = unit_list();

        return view('hr.buyer.sync', compact('buyer','unit','getSynced','date_array'));
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



    public function syncAtt($date, $buyer)
    {
        $shift = shift_by_code();

        $mappedshift = collect($shift)->map(function($item) use ($date, $buyer){
            $limits = $this->getMaxMin($date, $item, $buyer->base_ot);
            $item['in_limit'] =  $limits['in_limit'];
            $item['out_limit'] =  $limits['out_limit'];
            return $item;
        });

        $toDayEmps = DB::table('hr_as_basic_info')
                    ->select('as_id','associate_id','shift_roaster_status')
                    ->where('as_unit_id', $buyer->hr_unit_id)
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


        // get planner data


        // get synced data
        $synced = DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->where('in_date', $date)
                    ->get()
                    ->keyBy('as_id');


        // get att data 
        $table = get_att_table($buyer->hr_unit_id);
        $att = DB::table($table)
                ->whereIn('as_id', $toDayEmps)
                ->where('in_date', $date)
                ->get();

        $ins = []; $newInsert = [];



        foreach ($att as $key => $a) {
            $shiftData =  $mappedshift[$a->hr_shift_code];
            $ins[$a->as_id] = [
                'as_id' => $a->as_id,
                'in_date' => $a->in_date,
                'att_status' => 'p',
                'hr_shift_code' => $a->hr_shift_code,
                'late_status' => $a->late_status,
                'line_id' => $a->line_id,
                'line_id' => $a->hr_shift_code,
                'created_by' => auth()->id()
            ];
            if(($a->in_time >= $shiftData['in_limit'] || $a->in_time == null)  && ($a->out_time <= $shiftData['out_limit'] || $a->out_time == null) && $a->ot_hour <= $buyer->base_ot){
                // no changes needed

                    $ins[$a->as_id]['in_time'] = $a->in_time;
                    $ins[$a->as_id]['out_time'] = $a->out_time;
                    $ins[$a->as_id]['ot_hour'] = $a->ot_hour;

            }else if($a->out_time > $shiftData['out_limit'] || $a->ot_hour > $buyer->base_ot){
                // only out time modify
                $ins[$a->as_id]['out_time'] = Carbon::parse($shiftData['out_limit'])->subSeconds(rand(0,839))->format('Y-m-d H:i:s');
                $ins[$a->as_id]['in_time'] = $a->in_time;

                if($a->in_time != null){
                    $ins[$a->as_id]['ot_hour'] = $buyer->base_ot;
                }else{
                    $ins[$a->as_id]['ot_hour'] = 0;
                }

            }else if($a->in_time < $shiftData['in_limit']  ){
                // only intime modify
                $ins[$a->as_id]['in_time'] = Carbon::parse($shiftData['in_limit'])->addSeconds(rand(0,419))->format('Y-m-d H:i:s');
                $ins[$a->as_id]['out_time'] = $a->out_time;
                $ins[$a->as_id]['ot_hour'] = $a->ot_hour;

            }


            if(isset($synced[$a->as_id])){
                $atn = $synced[$a->as_id];
                if($atn->att_status == 'p' && $atn->ot_hour == $ins[$a->as_id]['ot_hour'] && $ins[$a->as_id]['hr_shift_code'] == $atn->hr_shift_code){

                }else{
                    DB::table('hr_buyer_att_'.$buyer->table_alias)
                    ->where([
                        'as_id' => $a->as_id,
                        'in_date' => $date
                    ])->update($ins[$a->as_id]);

                }
            }else{
                $newInsert[$a->as_id] = $ins[$a->as_id];
            }
        }        

        // get dayoff data

        // get leave data

        // get absent data

        DB::table('hr_buyer_att_'.$buyer->table_alias)->insertOrIgnore($newInsert);

        return count($ins);

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
