<?php

namespace App\Http\Controllers\Hr\TimeAttendance;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Line;
use App\Models\Hr\Shift;
use App\Models\Hr\Station;
use App\Models\Hr\Unit;
use DB, DataTables, Validator, Response;
use Illuminate\Http\Request;

class StationController extends Controller

{
	public function showForm(){
		
		$data['unitList'] = Unit::where('hr_unit_status', '1')
                ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
                ->pluck('hr_unit_name', 'hr_unit_id');

		return view('hr/timeattendance/station_card',$data);

	}

	//save form

	public function saveForm(Request $request){

		$validator= Validator::make($request->all(),[

			"associate_id" => "required|max:10",
  			"floor_id" => "required|max:10",
  			"line_id" => "required|max:10",
  			"start_date" => "required",
  			"end_date" => "required"
		]);

		if($validator->fails()){
			return back()
					->withInput()
					->with("error", "Incorrect Input!");
		}
		else{

			$unit_id= Employee::where('associate_id', $request->associate_id)
							->pluck('as_unit_id')->first();

			$getStation = Station::checkDateRangeWiseStartDateExists($request->associate_id, $request->start_date, $request->end_date);
			if($getStation != null){
				return redirect()->back()->with('error', $request->associate_id.' is already assigned.');
			}
			$getStation = Station::checkDateRangeWiseEndDateExists($request->associate_id, $request->start_date, $request->end_date);
			if($getStation != null){
				return redirect()->back()->with('error', $as_id.' is already assigned.');
			}


			$station= new Station();

			$station->associate_id = $request->associate_id;

			$station->unit_id = $unit_id;

			$station->changed_floor = $request->floor_id;

			$station->changed_line = $request->line_id;

			$station->start_date = $request->start_date;

			$station->end_date = $request->end_date;

			$station->updated_at = date("Y-m-d H:i:s");

			$station->updated_by = Auth()->user()->associate_id;

			$station->save();
			//log file
			$this->logFileWrite("Station Card Created", $station->id);
			return back()
				->with('success', 'Station Card saved successfully!');
		}

	}

	//get associate information of selected associate id

	public function stationAssInfo(Request $request)
	{
		$data= DB::table('hr_as_basic_info AS b')
					->where('b.associate_id', $request->associate_id)
					->select([
						'b.*',
						'b.as_shift_id',
						"u.hr_unit_name",
						"f.hr_floor_name",
						"l.hr_line_name",
						"dp.hr_department_name",
						"dg.hr_designation_name"
					])

					->leftJoin('hr_floor AS f', 'f.hr_floor_id', 'b.as_floor_id')
    				->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
    				->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'b.as_unit_id')
    				->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            		->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
    				->first();

    	//get floor list
    	$floors = Floor::where('hr_floor_unit_id', $data->as_unit_id)
    					->select([
    						"hr_floor_name",
    						"hr_floor_id"
    					])
    					->get();
    	//generate floor list dropdown
    	$floorList= '<option value="">Select Floor</option>';

    	foreach($floors AS $floor){

    		$floorList.= '<option value="'.$floor->hr_floor_id.'">'.$floor->hr_floor_name.'</option>';
    	}
    	$return["associate_id"]= $data->associate_id;
    	$return["unit"]= $data->hr_unit_name;
    	$return["floor"]= $data->hr_floor_name;
    	$return["line"]= $data->hr_line_name;
    	$return["shift"]= $data->as_shift_id;
    	$return["floorList"]= $floorList;
    	$return["as_pic"]= emp_profile_picture($data);
    	$return["as_name"]= $data->as_name;
    	$return["as_oracle_code"]= $data->as_oracle_code;
    	$return["hr_department_name"]= $data->hr_department_name;
    	$return["hr_designation_name"]= $data->hr_designation_name;

    	return $return;
	}

	public function multipleAsInfo(Request $request)
	{
		$input = $request->all();
		$data = array();
		foreach ($input['associate_id'] as $key => $value) {
			$getEmployee = Employee::getEmployeeAssociateIdWise($value);
			$date = date('Y-m-d 00:00:00');
			$getStation = Station::checkDateWiseExists($date, $getEmployee->associate_id);
			if($getStation != null){
				$getEmployee->floor['hr_floor_name'] = $getStation->floor['hr_floor_name'];
				$getEmployee->line['hr_line_name'] = $getStation->line['hr_line_name'];
			}
			$data[] = $getEmployee;
		}
		//$name=$data['as_name'];
		return view('hr.timeattendance.station_multiple_as_info', compact('data'));
		return $data;
	}


	//get line list of selected floor
	public function stationLineInfo(Request $request){

		$lines= Line::where('hr_line_floor_id', $request->floor_id)

						->select([

							"hr_line_id",

							"hr_line_name"

						])

						->get();

		$data= '<option value="">Select Line</option>';

		foreach ($lines as $line) {

			$data.= '<option value="'.$line->hr_line_id.'">'.$line->hr_line_name.'</option>';

		}

		return $data;

	}


	# select unit and send the units employee data for multiple

    public function unitEmployees(Request $request){

    		$data=Employee::where('as_unit_id',$request->unit_id)
    							->select('as_name', 'associate_id', 'as_pic')
    							->get()
    							->toArray();
    		// dd($data);exit;
    	return Response::json($data);
    }

    #select multiple section unit and get floor

    public function getFloor(Request $request){
    	//get floor list
    	$floors = Floor::where('hr_floor_unit_id', $request->unit_id)
    					->select([
    						"hr_floor_name",
    						"hr_floor_id"
    					])
    					->get();
    	//generate floor list dropdown

    	$floorList= '<option value="">Select Floor</option>';

    	foreach($floors AS $floor){

    		$floorList.= '<option value="'.$floor->hr_floor_id.'">'.$floor->hr_floor_name.'</option>';
    	}

    	//dd($floorList);exit;

    	return Response::json($floorList);
    }

    # multiple save

    public function saveFormMultiple(Request $request){

    	//dd($request->all());
		$validator= Validator::make($request->all(),[

			"multiple_associate_id" => "required|max:10",
  			"floor_id_multiple" => "required|max:10",
  			"line_id_multiple" => "required|max:10",
  			"start_date_multiple" => "required",
  			"end_date_multiple" => "required",
  			"unit" => "required"
		]);

		if($validator->fails()){
			return back()
					->withInput()
					->with("error", "Incorrect Input!");
		}
		else{

			$as_ids=$request->multiple_associate_id;
			//dd($as_ids);
			foreach($as_ids as $as_id){
				$getStation = Station::checkDateRangeWiseStartDateExists($as_id, $request->start_date_multiple, $request->end_date_multiple);
				if($getStation != null){
					return redirect()->back()->with('error', $as_id.' is already assigned.');
				}
				$getStation = Station::checkDateRangeWiseEndDateExists($as_id, $request->start_date_multiple, $request->end_date_multiple);
				if($getStation != null){
					return redirect()->back()->with('error', $as_id.' is already assigned.');
				}

			 	$station= new Station();
			 	$station->associate_id = $as_id;
			 	$station->unit_id = $request->unit;
			 	$station->changed_floor = $request->floor_id_multiple;
			 	$station->changed_line = $request->line_id_multiple;
			 	$station->start_date = $request->start_date_multiple;
			 	$station->end_date = $request->end_date_multiple;
			 	$station->updated_at = date("Y-m-d H:i:s");
			 	$station->updated_by = Auth()->user()->associate_id;
			 	$station->save();
			}
			//log file
			$this->logFileWrite("Station Card Created", $station->id);
			return back()
				->with('success', 'Station Card saved successfully!');
		}

	}



    public function showList(){

    	$unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())

    					->pluck('hr_unit_name');

    	$floorList= Floor::pluck('hr_floor_name');

    	$lineList= Line::pluck('hr_line_name');

    	return view('hr/timeattendance/station_card_list', compact('unitList', 'floorList', 'lineList'));

    }

    //get list data

    public function listData(){



    	$data= DB::table('hr_station AS s')

    			->select([

    				"s.*",

    				"b.as_name",

    				"ff.hr_floor_name",

    				"ll.hr_line_name",

    				"f.hr_floor_name AS changed_floor",

    				"l.hr_line_name AS changed_line",

    				"u.hr_unit_name"

    			])

    			->leftJoin('hr_as_basic_info As b', 'b.associate_id', 's.associate_id')

    			->leftJoin('hr_floor AS f', 'f.hr_floor_id', 's.changed_floor')

    			->leftJoin('hr_line AS l', 'l.hr_line_id', 's.changed_line')

    			->leftJoin('hr_floor AS ff', 'ff.hr_floor_id', 'b.as_floor_id')

    			->leftJoin('hr_line AS ll', 'll.hr_line_id', 'b.as_line_id')

    			->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'b.as_unit_id')

    			->get();



    	return DataTables::of($data)->addIndexColumn()

    						->addColumn('action', function($data){

    							$action_buttons= "<div class=\"btn-group\">  
		                            <a href=".url('hr/timeattendance/station_card/'.$data->station_id.'/edit')." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"Edit\" style=\"height:25px; width:26px;\">
		                                <i class=\"ace-icon fa fa-pencil bigger-120\"></i>

		                            </a> 

		                            <a href=".url('hr/timeattendance/station_card/'.$data->station_id.'/delete')." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\" style=\"height:25px; width:26px;\">

		                                <i class=\"ace-icon fa fa-trash bigger-120\"></i>

		                            </a> ";

		                        $action_buttons.= "</div>";
		                        return $action_buttons;
    						})
    						->rawColumns(["action"])
    						->toJson();

    }



    //station card delete



    public function stationDelete($id){

    	Station::where('station_id', $id)->delete();

    	//log file

		$this->logFileWrite("Station Card Deleted", $id);

    	return redirect("hr/timeattendance/station_card")->with('success', 'Station Card deleted successfully!');

    }

    //station cadr edit

    public function stationEdit($id){

    	$station= DB::table('hr_station AS st')
    				->where('station_id', $id)
    				->select([
    					"st.*",
    					'b.as_unit_id',
    					'b.as_shift_id',
						"u.hr_unit_name",
						"f.hr_floor_name",
						"l.hr_line_name"
					])
					->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 'st.associate_id')
					->leftJoin('hr_floor AS f', 'f.hr_floor_id', 'b.as_floor_id')
    				->leftJoin('hr_line AS l', 'l.hr_line_id', 'b.as_line_id')
    				->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'b.as_unit_id')
    				->first();

    	$floorList= Floor::where('hr_floor_unit_id', $station->unit_id)
    						->pluck('hr_floor_name', 'hr_floor_id');
    	$lineList= Line::where('hr_line_floor_id', $station->changed_floor)
    					->pluck('hr_line_name', 'hr_line_id');
    	return view('hr/timeattendance/station_card_edit', compact('station', 'floorList', 'lineList'));

    }

    //update station card
    public function stationUpdate(Request $request){



    	$validator= Validator::make($request->all(),[

			"associate_id" => "required|max:10",

  			"floor_id" => "required|max:10",

  			"line_id" => "required|max:10",

  			"start_date" => "required",

  			"end_date" => "required"

		]);

		if($validator->fails()){

			return back()

					->withInput()

					->with("error", "Incorrect Input!");

		}

		else{

			Station::where('station_id', $request->station_id)

			->update([

				"associate_id" => $request->associate_id,

				"unit_id" => $request->unit_id,

				"changed_floor" => $request->floor_id,

				"changed_line" => $request->line_id,

				"start_date" => $request->start_date,

				"end_date" => $request->end_date,

				"updated_by" => Auth()->user()->associate_id

			]);

			//log file

			$this->logFileWrite("Station Card updated", $request->station_id);



			return redirect("hr/timeattendance/station_card")

				->with('success', 'Station Card updated successfully!');

		}

    }



    //Write Every Events in Log File

    public function logFileWrite($message, $event_id){

        $log_message = date("Y-m-d H:i:s")." ".Auth()->user()->associate_id." \"".$message."\" ".$event_id.PHP_EOL;

        $log_message .= file_get_contents("assets/log.txt");

        file_put_contents("assets/log.txt", $log_message);

    }

}

