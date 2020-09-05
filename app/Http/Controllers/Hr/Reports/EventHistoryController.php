<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Line;
use App\Models\Hr\Shift;
use App\Models\Hr\Station;
use App\Models\Hr\Unit;
use DB, DataTables, Validator, Response;

class EventHistoryController extends Controller
{
    public function showList()
    {
    	$typeList= DB::table('event_history')
                    // ->pluck('type')
                    ->groupBy('type')
                    ->get();
        foreach ($typeList as $types) {
            if($types->type==1)
                $type[]="Intime/OutTime Modify";
            else if($types->type==2)
                $type[]="Absent to Present";
            else if($types->type==3)
                $type[]="Present to Absent";
            else
                $type[]="Made Halfday";
        }
        //dd($type);
    	return view('hr/reports/event_history', compact('type'));
    }

    //get list data
    public function listData()
    {
    	$data = DB::table('event_history AS e')
    			->select([
    				"e.*"
    			])
                ->latest()
    			->get();

        $units = Unit::get()->keyBy('hr_unit_id');


    	return DataTables::of($data)->addIndexColumn()
                ->addColumn('modified_event', function($data) use ($units){
                    $modified_events=json_decode($data->modified_event);
                    $view='';
                    if(isset($modified_events->unit) || isset($modified_events->hr_unit)) {
                        $unit_id = isset($modified_events->unit)?isset($modified_events->unit):isset($modified_events->hr_unit);
                        
                        $unit_name = $units[$unit_id]; 
                        if($data->type==1)
                            {$view= "<div>  
                                    Unit: $unit_name->hr_unit_name <br> 
                                    ID: $modified_events->associate_id <br>
                                    Date: $modified_events->date<br>
                                    In Punch: $modified_events->in_punch_new<br>
                                    Out Punch: $modified_events->out_punch_new";
                            $view.= "</div>";}
                        if($data->type==2)
                            {$view= "<div>  
                                    Unit: $unit_name->hr_unit_name <br> 
                                    ID: $modified_events->associate_id <br>
                                    Date: $modified_events->date<br>
                                    In Punch: $modified_events->in_punch_new<br>
                                    Out Punch: $modified_events->out_punch_new";
                            $view.= "</div>";}
                        if($data->type==3)
                            {$view= "<div>  
                                    Unit: $unit_name->hr_unit_name <br> 
                                    ID: $modified_events->associate_id <br>
                                    Date: $modified_events->date";
                            $view.= "</div>";}
                        if($data->type==4)
                            {$view= "<div>  
                                    Unit: $unit_name->hr_unit_name <br> 
                                    ID: $modified_events->associate_id <br>
                                    Date: $modified_events->date";
                        $view.= "</div>";}
                    }
                    return $view;
                })
                ->addColumn('type', function($data){
                    if($data->type==1)
                        $type="<div>In-time/Out-time Modify</div>";
                    else if($data->type==2)
                        $type="<div>Absent to Present</div>";
                    else if($data->type==3)
                        $type="<div>Present to Absent</div>";
                    else
                        $type="<div>Made Halfday</div>";
                    return $type;
                })
                ->addColumn('action', function($data){
                    $action_button= "<div class=\"btn-group\">  
                        <button data-book-id=".$data->id." class=\"btn btn-sm btn-success log-details\">
                            <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                        </button> ";
                    $action_button.= "</div>";
                    return $action_button;
                })
                ->addColumn('user_id', function($data){
                    $return = "<a href=".url('hr/recruitment/employee/show/'.$data->user_id).">$data->user_id</a>";
                    return $return;
                })
                ->addColumn('created_by', function($data){

                    $return = "<a href=".url('hr/recruitment/employee/show/'.$data->created_by).">$data->created_by</a>";
                    return $return;
                })

                ->rawColumns(["type","modified_event","action","user_id","created_by"])
				->toJson();
    }

    public function getDetail(Request $request)
    {
        $row_data= DB::table('event_history AS e')
                ->select([
                    "e.*"
                ])
                ->where('id',$request->id)
                ->first();

        $pre=json_decode($row_data->previous_event);
        $post=json_decode($row_data->modified_event);
        // dd($post); exit;
        // if($row_data->type==1)
        // {
        //     $data['id']=$row_data->user_id;
        //     $data['b_in_time']=$pre->in_time;
        //     $data['b_out_time']=$pre->
        // }
        return Response::json($row_data);
    }

}
