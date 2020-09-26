<?php

namespace App\Http\Controllers\Hr\Timeattendance;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Hr\YearlyHolyDay;
use App\Models\Hr\Unit;
use Validator, DB, ACL, DataTables,Response;

class YearlyHolidayController extends Controller
{
    public function index()
    {
        $unit=Unit::pluck('hr_unit_short_name');
        return view("hr/timeattendance/yearly_holiday_list", compact('unit'));
    }

    public function getAll()
    {
        DB::statement(DB::raw('set @serial_no=0'));

        $data = DB::table('hr_yearly_holiday_planner AS h')
            ->select(
                'h.*',
                'u.hr_unit_name'
            )
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'h.hr_yhp_unit')
            ->whereIn('u.hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('h.hr_yhp_dates_of_holidays', 'desc')
            ->get();

        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($data) {
                    if ($data->hr_yhp_comments !='Weekend')
                    {
                        return "<div class=\"btn-group\">
                            <button class=\"btn btn-xs btn-primary date_edit\" data-toggle=\"tooltip\" title=\"Edit\" value=\"$data->hr_yhp_id\">
                                <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                            </button>
                            </div>";
                    }
                    
                })

            ->addColumn('date', function ($data) {
                    if ($data->hr_yhp_comments !='Weekend')
                    {
                        return "<div>
                            <p class=\"holiday_date\" data-id=\"$data->hr_yhp_id\">$data->hr_yhp_dates_of_holidays</p>
                            </div>";
                    }
                    else{
                        return "<div>
                            <p data-id=\"$data->hr_yhp_id\">$data->hr_yhp_dates_of_holidays</p>
                            </div>";
                    }
                    
                })

            ->addColumn("open_status", function($data) {

                return "<label class=\"radio-inline\">

                      <input type=\"radio\" data-id=\"$data->hr_yhp_id\" name=\"hr_yhp_open_status[$data->hr_yhp_id]\" class=\"open_status\" value=\"0\" style=\"margin-left:-15px\" ".($data->hr_yhp_open_status=="0"?'checked':null)."> Holiday

                    </label>

                    <label class=\"radio-inline\">

                      <input type=\"radio\" data-id=\"$data->hr_yhp_id\" name=\"hr_yhp_open_status[$data->hr_yhp_id]\" class=\"open_status\" value=\"1\" style=\"margin-left:-15px\" ".($data->hr_yhp_open_status=="1"?'checked':null)."> General

                    </label>

                    <label class=\"radio-inline\">

                      <input type=\"radio\" data-id=\"$data->hr_yhp_id\" name=\"hr_yhp_open_status[$data->hr_yhp_id]\" class=\"open_status\" value=\"2\" style=\"margin-left:-15px\" ".($data->hr_yhp_open_status=="2"?'checked':null)."> OT

                    </label>";

            })

            ->rawColumns(['hr_unit_name','open_status', 'action','date'])

            ->toJson();

    }



    public function create()

    {

        //ACL::check(["permission" => "hr_time_op_holiday"]);

        #-----------------------------------------------------------#

        $unitList  = Unit::where('hr_unit_status', '1')

            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())

            ->pluck('hr_unit_short_name', 'hr_unit_id');

        return view('hr/timeattendance/yearly_holiday', compact('unitList'));

    }


    public function store(Request $request)

    {

    	$validator= Validator::make($request->all(), [
            'hr_yhp_dates_of_holidays' 	=> 'required|max:10',
            'hr_yhp_comments' 			=> 'required|max:64'
        ]);

        if($validator->fails())
        {
        	return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fileds!.');
        }
        // return $request->all();
        try {
            for($i=0; $i<sizeof($request->hr_yhp_dates_of_holidays); $i++)
            {
                if($request->hr_yhp_dates_of_holidays[$i] != null){
                    $date = (date("Y-m-d", strtotime($request->hr_yhp_dates_of_holidays[$i])));
                    if (YearlyHolyDay::where('hr_yhp_unit', $request->as_unit_id)->where('hr_yhp_dates_of_holidays', $date)->exists())
                    {
                        YearlyHolyDay::where('hr_yhp_unit', $request->as_unit_id)->where('hr_yhp_dates_of_holidays', $date)
                        ->update([
                            'hr_yhp_unit'               => $request->as_unit_id,
                            'hr_yhp_dates_of_holidays'  => $date,
                            'hr_yhp_comments' => $request->hr_yhp_comments[$i],
                            'hr_yhp_status' => 1
                        ]);
                        $last_id = YearlyHolyDay::where('hr_yhp_unit', $request->as_unit_id)->where('hr_yhp_dates_of_holidays', $date)
                        ->value('hr_yhp_id');
                        $this->logFileWrite("Yearly Holiday Entry Updated", $last_id);
                    }
                    else
                    {
                        YearlyHolyDay::insert([
                            'hr_yhp_unit'               => $request->as_unit_id,
                            'hr_yhp_dates_of_holidays'  => $date,
                            'hr_yhp_comments'            => $request->hr_yhp_comments[$i],
                            'hr_yhp_status' => 1
                        ]);

                        $last_id = DB::getPdo()->lastInsertId();

                        $this->logFileWrite("Yearly Holiday Entry Saved", $last_id);
                    }
                }
            }

            toastr()->success('Successful Completed');
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }

    }





    public function status(Request $request)

    {

        //ACL::check(["permission" => "hr_time_op_holiday"]);

        #-----------------------------------------------------------#



         DB::table("hr_yearly_holiday_planner")

            ->where("hr_yhp_id", $request->id)

            ->update([

                "hr_yhp_status" => (($request->status =="enable")?1:0)

            ]);

        return back()

            ->with("success", "Update Successful!");

    }



    public function getHolidays(Request $request){
        $input = $request->all();
        $month = date('m', strtotime($input['month_year']));
        $year = date('Y', strtotime($input['month_year']));
        $date = date_parse($request->month);
        $month_id= $date['month'];
        $workdays = array();
        $type = CAL_GREGORIAN;
        $month_id = date_parse($month);
        $day_count = cal_days_in_month($type, $month, $year);
        $weekend_count= count($request->weekdays);
        $weekends= $request->weekdays;
        $data='<legend>Weekend Dates</legend>';

        for ($i = 1; $i <= $day_count; $i++) {
            $date = $year.'/'.$month.'/'.$i;
            $date= date('Y-m-d', strtotime($date));
            $get_name = date('l', strtotime($date));
           if(in_array($get_name, $weekends))
           {
                $data.='<div class="form-group">
                    <div class="row">
                        <input type="date" name="hr_yhp_dates_of_holidays[]" value="'. $date . '" class="form-control col" data-validation="required" readonly/>
                        <input type="text" name="hr_yhp_comments[]" class="form-control col" value="Weekend" placeholder="Holiday Name" data-validation="required" readonly/>
                    </div>
                </div>';
           }
        }
        return $data;
    }



    public function openStatus(Request $request)

    {

        $update = YearlyHolyDay::where("hr_yhp_id", $request->get("id"))

            ->update(['hr_yhp_open_status' => $request->get("status")]);



        if ($update)

        {

            echo "<div class=\"alert alert-success alert-dismissible\" role=\"alert\">

                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>

                Open Status update Successful.

            </div>";

        }

        else

        {

            echo "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\">

                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>

                Please try again...

            </div>";

        }

    }


    public function modalData(Request $request)

    {
        $date=DB::table("hr_yearly_holiday_planner")
                ->where("hr_yhp_id", $request->id)
                ->pluck('hr_yhp_dates_of_holidays');
        return Response::json($date);

    }

    public function modalSave(Request $request)

    {
         $update=DB::table("hr_yearly_holiday_planner")
            ->where("hr_yhp_id", $request->id)
            ->update([
                "hr_yhp_dates_of_holidays" => ($request->date)
            ]);

        if ($update)
        {
            echo "<div class=\"alert alert-success alert-dismissible\" role=\"alert\">
                Date update Successful.
            </div>";
        }
        else
        {
            echo "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\">
                Please try again...
            </div>";
        }

    }
    public function modalDelete(Request $request)

    {
         //dd($request->all());exit;
         $update=DB::table("hr_yearly_holiday_planner")
            ->where("hr_yhp_id", $request->id)
            ->delete();

        if ($update)
        {
            echo "<div class=\"alert alert-success alert-dismissible\" role=\"alert\">
                Date delete Successful.
            </div>";
        }
        else
        {
            echo "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\">
                Please try again...
            </div>";
        }

    }



}
