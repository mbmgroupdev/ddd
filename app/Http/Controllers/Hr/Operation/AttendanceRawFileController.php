<?php

namespace App\Http\Controllers\Hr\Operation;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator, DB, Response;

class AttendanceRawFileController extends Controller
{
    public function index()
    {
    	$unitList  = Unit::where('hr_unit_status', '1')->whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
    	unset($unitList[4]);
    	unset($unitList[5]);
        $unitList->put(1001,  "Common Unit");
    	return view('hr.operation.att_raw_file.index', compact('unitList'));
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'unit'      => 'required',
            'from_date' => 'required|date',
            'to_date'   => 'required|date',
            'file_name' => 'required'
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

    	$input = $request->all();
    	if(!in_array($input['unit'], [1,4,5])){
    		toastr()->error('Coming Soon!');
    		return back();
    	}

    	if(strtotime($input['to_date']) < strtotime($input['from_date'])){
    		toastr()->error('To Date Always Large!');
    		return back();
    	}


    	try {
    		$tableName = get_att_table($input['unit']);

    		$fromDate = date('Y-m-d', strtotime($input['from_date']));
    		$toDate = date('Y-m-d', strtotime($input['to_date']));
    		$diffDate = displayBetweenTwoDates($fromDate, $toDate);

    		$date = Carbon::parse($input['from_date']);
	    	$now = Carbon::parse($input['to_date']);
			$diff = $date->diffInDays($now);

			$attData = [];
    		if($diff == 0){
    			// single date in time get
    			if($this->checkDateWiseAttendanceExist($tableName, $diffDate[0]) > 0){
    				$attData[] = $this->getAttendanceDataDateWise($tableName, $diffDate[0], 'in_time');
    			}
    		}elseif($diff == 1){
    			// first date out time get
    			if($this->checkDateWiseAttendanceExist($tableName, $diffDate[0]) > 0){
    				$attData[] = $this->getAttendanceDataDateWise($tableName, $diffDate[0], 'out_time');
    			}
    			// last date in time get
    			if($this->checkDateWiseAttendanceExist($tableName, $diffDate[1]) > 0){
    				$attData[] = $this->getAttendanceDataDateWise($tableName, $diffDate[1], 'in_time');
    			}
    		}elseif($diff > 1){
    			// first date out time get
    			if($this->checkDateWiseAttendanceExist($tableName, $diffDate[0]) > 0){
	    			$attData[] = $this->getAttendanceDataDateWise($tableName, $diffDate[0], 'out_time');
	    		}

    			for ($i=1; $i < $diff; $i++) { 
	    			// middle date in time and out time  get
	    			if($this->checkDateWiseAttendanceExist($tableName, $diffDate[$i]) > 0){
		    			// first date in time get
	    				$attData[] = $this->getAttendanceDataDateWise($tableName, $diffDate[$i], 'in_time');
	    				// last date out time get
	    				$attData[] = $this->getAttendanceDataDateWise($tableName, $diffDate[$i], 'out_time');
	    			}
	    			
    			}
    			// last date in time get
    			if($this->checkDateWiseAttendanceExist($tableName, $diffDate[$diff]) > 0){
	    			$attData[] = $this->getAttendanceDataDateWise($tableName, $diffDate[$diff], 'in_time');
	    		}
    		}

		    $content = "";
		    foreach ($attData as $log) {
		      foreach ($log as $value) {
		      	$content .= $value;
		      	$content .= "\n";
		      }
		    }

		    // file name that will be used in the download
		    $fileName = $input['file_name'].".txt";

		    // use headers in order to generate the download
		    $headers = [
		      'Content-type' => 'text/plain', 
		      'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
		      'Content-Length' => strlen($content)
		    ];

		    return Response::make($content, 200, $headers);
    	} catch (\Exception $e) {
    		$bug = $e->getMessage();
    		toastr()->error($bug);
    		return back();
    	}
    }

    public function checkDateWiseAttendanceExist($tableName, $date)
    {
    	return DB::table($tableName)
    	->where('in_date', $date)
    	->count();

    }

    public function getAttendanceDataDateWise($tableName, $date, $parameter)
    {
    	// employee info
		$employeeData = DB::table('hr_as_basic_info');
        $employeeDataSql = $employeeData->toSql();

    	$queue = DB::table($tableName.' AS s')
    	->select('s.as_id', 's.in_date', 's.'.$parameter.' AS parameter', 'emp.as_rfid_code')
    	->where('s.in_date', $date)
    	->whereNotNull($parameter);
    	$queue->leftjoin(DB::raw('(' . $employeeDataSql. ') AS emp'), function($join) use ($employeeData) {
            $join->on('emp.as_id','s.as_id')->addBinding($employeeData->getBindings());
        });
    	if($parameter == 'in_time'){
    		$queue->where('s.remarks', '!=', 'DSI');
    	}
    	$data = $queue->get();

    	$data = collect($data)->map(function($row) {
            return "12".date('Ymd', strtotime($row->in_date)).''.date('His', strtotime($row->parameter)).''.$row->as_rfid_code.':';
        });
    	return $data;

    }

}
