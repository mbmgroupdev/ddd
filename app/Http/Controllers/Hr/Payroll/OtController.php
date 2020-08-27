<?php

namespace App\Http\Controllers\Hr\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Jobs\BuyerManualOtProcess;

use App\Models\Hr\Ot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator, ACL, DataTables, DB;

class OtController extends Controller
{
    public function OT()
    {
        // ACL::check(["permission" => "hr_payroll_ot"]);
        #-----------------------------------------------------------#
    	return view('hr/payroll/ot');
    }

    public function OtStore(Request $request)
    {
        // ACL::check(["permission" => "hr_payroll_ot"]);
        #-----------------------------------------------------------#
    	$validator= Validator::make($request->all(), [
    		'hr_ot_as_id' => 'required|max:10|min:10',
    		'hr_ot_date'  => 'required|date',
            'hr_ot_hour'  => 'required',
    		'hr_ot_remarks' => 'max:128'
    	]);

    	if($validator->fails())
        {
    		return back()
    		->withErrors($validator)
    		->withInput()
    		->with('error', 'Please fillup all required fileds correctly!.');
    	}
    	DB::beginTransaction();
        try {
            $ot = DB::table("hr_ot")->where("hr_ot_as_id", $request->hr_ot_as_id)
                ->where("hr_ot_date", $request->hr_ot_date)
                ->exists();

            if ($ot)
            {
                $ot = DB::table("hr_ot")
                ->where("hr_ot_as_id", $request->hr_ot_as_id)
                ->where("hr_ot_date", $request->hr_ot_date)
                ->update([
                    "hr_ot_as_id"   => $request->hr_ot_as_id,
                    "hr_ot_date"    => $request->hr_ot_date,
                    "hr_ot_hour"    => $request->hr_ot_hour,
                    "hr_ot_remarks" => $request->hr_ot_remarks,
                    "hr_ot_created_by" => auth()->user()->associate_id,
                    "hr_ot_created_at" => date("Y-m-d H:i:s")
                ]);
                    //Log with base table primary key
                    // $id = DB::table("hr_ot")
                    //         ->where("hr_ot_as_id", $request->hr_ot_as_id)
                    //         ->where("hr_ot_date", $request->hr_ot_date)
                    //         ->value('hr_ot_id');
                    // $this->logFileWrite("Over Time Entry Updated",  $id);

                    //Log with associate id
                    $this->logFileWrite("Over Time Entry Updated", $request->hr_ot_as_id );

            }
            else
            {
                $ot= new Ot();
                $ot->hr_ot_as_id = $request->hr_ot_as_id;
                $ot->hr_ot_date = $request->hr_ot_date;
                $ot->hr_ot_hour = $request->hr_ot_hour;
                $ot->hr_ot_remarks = $request->hr_ot_remarks;
                $ot->hr_ot_created_by = auth()->user()->associate_id;
                $ot->hr_ot_created_at = date("Y-m-d H:i:s");
                $ot->save();
                $this->logFileWrite("Over Time Entry Updated", $ot->hr_ot_id );
            }

            $today = date('Y-m-d');
            if($request->hr_ot_date < $today){
                $otHourExpolde = explode('.', $request->hr_ot_hour);
                $hour = $otHourExpolde[0];
                $hour = sprintf("%02d", $hour);
                $minute = '00';
                if($otHourExpolde[1] > 0){
                    $minute = '30';
                }
                $otHour = $hour.":".$minute;

                $queue = (new BuyerManualOtProcess($request->hr_ot_date, $request->hr_ot_as_id, $otHour))
                  ->delay(Carbon::now()->addSeconds(2));
                  dispatch($queue);
                //check exists employee attendace table
                $getEmployee = Employee::getEmployeeAssociateIdWise($request->hr_ot_as_id);
                if($getEmployee->as_unit_id ==1 || $getEmployee->as_unit_id==4 || $getEmployee->as_unit_id==5 || $getEmployee->as_unit_id==9){
                    $tableName="hr_attendance_mbm";
                } else if($getEmployee->as_unit_id ==2){
                    $tableName="hr_attendance_ceil";
                } else if($getEmployee->as_unit_id ==3){
                    $tableName="hr_attendance_aql";
                } else if($getEmployee->as_unit_id ==6){
                    $tableName="hr_attendance_ho";
                } else if($getEmployee->as_unit_id ==8){
                    $tableName="hr_attendance_cew";
                } else{
                    $tableName="hr_attendance_mbm";
                }
                $checkDate = $request->hr_ot_date.'%';
                $yearMonth = Carbon::parse($request->hr_ot_date)->format('Y-m');
                $year = Carbon::parse($request->hr_ot_date)->format('Y');
                $month = Carbon::parse($request->hr_ot_date)->format('n');
                $checkYearMonth = $yearMonth.'-%';

                $getAttEmp = DB::table($tableName)
                ->where('as_id', $getEmployee->as_id)
                ->where('in_time', 'LIKE', $checkDate)
                ->first();
                if($getAttEmp != null){
                    DB::table($tableName)
                    ->where('id', $getAttEmp->id)
                    ->update([
                        'ot_hour' => $otHour
                    ]);

                    //re salary ot calcuation this employee process
                    $getAttMonth = DB::table($tableName)
                    ->where('as_id', $getEmployee->as_id)
                    ->where('in_time', 'LIKE', $checkYearMonth)
                    ->get();
                    $otMinute = 0;
                    foreach ($getAttMonth as $att) {
                        $otHour = $att->ot_hour;
                        if($otHour > 0){
                            $otHourExpolde = explode(':', $otHour);
                            $otMinuteConvert = ($otHourExpolde[0] * 60);
                            $otMinute += $otMinuteConvert + $otHourExpolde[1];
                        }
                    }
                    //update salary ot hour
                    HrMonthlySalary::
                    where('as_id', $request->hr_ot_as_id)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->update([
                        'ot_hour' => $otMinute
                    ]);
                }
            }
            $msg = $request->hr_ot_as_id." Manual ot assing successfully";
            $this->logFileWrite($msg, $request->hr_ot_as_id);
            DB::commit();
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error',$bug);
        }
    }

    public function otList()
    {
        // ACL::check(["permission" => "hr_payroll_ot"]);
        #-----------------------------------------------------------#
        return view('hr/payroll/ot_list');
    }

    public function otListData(Request $request)
    {
        DB::statement(DB::raw("SET @s:=0 "));
        $data = DB::table("hr_ot AS o")
            ->select(
                DB::raw("@s:=@s+1 AS serial"),
                "o.*"
            )
            ->leftJoin('hr_as_basic_info AS b', 'b.associate_id',  '=', 'o.hr_ot_as_id')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->orderBy('o.hr_ot_date', 'desc')
            ->get();

        return DataTables::of($data)
            // ->addColumn('action', function ($data) {
            //     return "<div class=\"btn-group\">
            //         <a href=".url('hr/payroll/ot/'.$data->hr_ot_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
            //             <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
            //         </a>
            //     </div>";
            // })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();

    }

    public function otEdit(Request $request)
    {
        // ACL::check(["permission" => "hr_payroll_ot"]);
        #-----------------------------------------------------------#
        $ot = Ot::where("hr_ot_id", $request->id)->first();
        return view('hr/payroll/ot_edit', compact('ot'));
    }

    public function otUpdate(Request $request)
    {
        // ACL::check(["permission" => "hr_payroll_ot"]);
        #-----------------------------------------------------------#
        $validator= Validator::make($request->all(), [
            'hr_ot_id'    => 'required|max:11|min:1',
            'hr_ot_as_id' => 'required|max:10|min:10',
            'hr_ot_date'  => 'required|date',
            'hr_ot_hour'  => 'required|max:1:min:1',
            'hr_ot_remarks' => 'max:128'
        ]);

        if($validator->fails())
        {
            return back()
            ->withErrors($validator)
            ->withInput()
            ->with('error', 'Please fillup all required fileds correctly!.');
        }
        else
        {
            $ot = DB::table("hr_ot")
            ->where("hr_ot_id", $request->hr_ot_id)
            ->update([
                "hr_ot_as_id"   => $request->hr_ot_as_id,
                "hr_ot_date"    => $request->hr_ot_date,
                "hr_ot_hour"    => $request->hr_ot_hour,
                "hr_ot_remarks" => $request->hr_ot_remarks,
                "hr_ot_created_by" => auth()->user()->associate_id,
                "hr_ot_created_at" => date("Y-m-d H:i:s")
            ]);

            if($ot)
            {
                $today = date('Y-m-d');
                if($request->hr_ot_date < $today){
                    $otHourExpolde = explode('.', $request->hr_ot_hour);
                    $hour = $otHourExpolde[0];
                    $hour = sprintf("%02d", $hour);
                    $minute = '00';
                    if($otHourExpolde[1] > 0){
                        $minute = '30';
                    }
                    $otHour = $hour.":".$minute;
                    //check exists employee attendace table
                    $getEmployee = Employee::getEmployeeAssociateIdWise($request->hr_ot_as_id);
                    if($getEmployee->as_unit_id ==1 || $getEmployee->as_unit_id==4 || $getEmployee->as_unit_id==5 || $getEmployee->as_unit_id==9){
                        $tableName="hr_attendance_mbm";
                    } else if($getEmployee->as_unit_id ==2){
                        $tableName="hr_attendance_ceil";
                    } else if($getEmployee->as_unit_id ==3){
                        $tableName="hr_attendance_aql";
                    } else if($getEmployee->as_unit_id ==6){
                        $tableName="hr_attendance_ho";
                    } else if($getEmployee->as_unit_id ==8){
                        $tableName="hr_attendance_cew";
                    } else{
                        $tableName="hr_attendance_mbm";
                    }
                    $checkDate = $request->hr_ot_date.'%';
                    $yearMonth = Carbon::parse($request->hr_ot_date)->format('Y-m');
                    $year = Carbon::parse($request->hr_ot_date)->format('Y');
                    $month = Carbon::parse($request->hr_ot_date)->format('n');
                    $checkYearMonth = $yearMonth.'-%';

                    $getAttEmp = DB::table($tableName)
                    ->where('as_id', $getEmployee->as_id)
                    ->where('in_time', 'LIKE', $checkDate)
                    ->first();
                    if($getAttEmp != null){
                        DB::table($tableName)
                        ->where('id', $getAttEmp->id)
                        ->update([
                            'ot_hour' => $otHour
                        ]);

                        //re salary ot calcuation this employee process
                        $getAttMonth = DB::table($tableName)
                        ->where('as_id', $getEmployee->as_id)
                        ->where('in_time', 'LIKE', $checkYearMonth)
                        ->get();
                        $otMinute = 0;
                        foreach ($getAttMonth as $att) {
                            $otHour = $att->ot_hour;
                            if($otHour > 0){
                                $otHourExpolde = explode(':', $otHour);
                                $otMinuteConvert = ($otHourExpolde[0] * 60);
                                $otMinute += $otMinuteConvert + $otHourExpolde[1];
                            }
                        }
                        //update salary ot hour
                        HrMonthlySalary::
                        where('as_id', $request->hr_ot_as_id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->update([
                            'ot_hour' => $otMinute
                        ]);
                    }
                }

                $this->logFileWrite("Over Time Entry Updated", $request->hr_ot_id);
                return back()
                        ->with('success', 'Update Successful.');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
        }
    }

}
