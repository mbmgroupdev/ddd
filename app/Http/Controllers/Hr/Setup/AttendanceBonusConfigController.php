<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\AttendanceBonusConfig;
Use Validator,DB, ACL, Exception, Response;

class AttendanceBonusConfigController extends Controller
{
    public function index(){
        $unitList  = Unit::where('hr_unit_status', '1')->whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
        $attBonusData = DB::table('hr_attendance_bonus_dynamic as b')
        ->select([
            'b.*',
            'c.hr_unit_name'
        ])
        ->leftJoin('hr_unit as c', 'c.hr_unit_id', 'b.unit_id')
        ->orderBy('b.id', 'DESC')
        ->get();
        return view('hr.setup.attendance_bonus', compact('unitList','attBonusData'));
    }

    public function saveData(Request $request){
        $validator= Validator::make($request->all(),[
            'unit_id'      => 'required|max:11',
            'late_count'   => 'required|max:11',
            'leave_count'  => 'required|max:11',
            'absent_count' => 'required|max:11',
            'first_month'  => 'required',
            'second_month' => 'required'
        ]);

        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }

        try{
            // dd($request->all());
            $units = AttendanceBonusConfig::pluck('unit_id')->toArray();
            // dd($units);
            if(in_array($request->unit_id, $units)){
                // dd('Yes');
                DB::table('hr_attendance_bonus_dynamic')
                ->where('unit_id',$request->unit_id)
                ->update([
                    'late_count'   => $request->late_count,
                    'leave_count'  => $request->leave_count,
                    'absent_count' => $request->absent_count,
                    'first_month'  => $request->first_month,
                    'second_month' => $request->second_month
                ]);
                $l_id = DB::table('hr_attendance_bonus_dynamic')->where('unit_id',$request->unit_id)->value('id');
                $this->logFileWrite("Attendance Bonus Updated", $l_id);
                return back()->with('success', 'Attendance Bonus Updated');
            }
            else{
                $data = new AttendanceBonusConfig();
                $data->unit_id      = $request->unit_id;
                $data->late_count   = $request->late_count;
                $data->leave_count  = $request->leave_count;
                $data->absent_count = $request->absent_count;
                $data->first_month  = $request->first_month;
                $data->second_month = $request->second_month;
                $data->save();
                $l_id=$data->id;
                $this->logFileWrite("Attendance Bonus Saved", $l_id);
                return back()->with('success', 'Attendance Bonus Saved');
            }

        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function getData(Request $req){
        $data = AttendanceBonusConfig::where('unit_id', $req->unit_id)->first();
        return Response::json($data);
    }
}
