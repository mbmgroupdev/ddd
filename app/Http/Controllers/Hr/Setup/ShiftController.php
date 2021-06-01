<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Shift;
use App\Models\Hr\ShiftRoaster;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;
use Validator,DB,ACL, Response, Cache;

class ShiftController extends Controller
{
	#show form
    public function shift()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->orderBy('hr_unit_name', 'desc')
            ->pluck('hr_unit_short_name', 'hr_unit_id');


        $designation = collect(designation_by_id())->pluck('hr_designation_name','hr_designation_id');


        $unitids = implode(",", auth()->user()->unit_permissions());

        $shifts = DB::select("SELECT
            s1.hr_shift_id,
            s1.hr_shift_name,
            s1.hr_shift_code,
            s1.hr_shift_start_time,
            s1.hr_shift_end_time,
            s1.bill_eligible,
            s1.ot_status,
            s1.ot_shift,
            s1.hr_shift_break_time,u.hr_unit_name
            FROM hr_shift s1
            LEFT JOIN hr_shift s2
            ON (s1.hr_shift_unit_id = s2.hr_shift_unit_id AND s1.hr_shift_name = s2.hr_shift_name AND s1.hr_shift_id < s2.hr_shift_id)
            LEFT JOIN hr_unit AS u
            ON u.hr_unit_id = s1.hr_shift_unit_id
            WHERE s2.hr_shift_id IS NULL AND s1.hr_shift_unit_id IN ($unitids)
            ORDER BY s1.hr_shift_id DESC");
        $trashed = [];

        $ot_shift = collect($shifts)->where('ot_status',1)->pluck('hr_shift_name','hr_shift_name');

        $bill  = [
            '1' => 'Ifter',
            '2' => 'Lunch',
        ];

        return view('hr/setup/shift/create', compact('unitList', 'shifts','trashed','ot_shift','designation'));
    	return view('hr/setup/shift', compact('unitList', 'shifts','trashed','ot_shift'));
    }

    public function shiftStore(Request $request)
    {
    	$validator= Validator::make($request->all(),[
    		'hr_shift_unit_id'    => 'required|max:11',
            'hr_shift_name'       => 'required|max:128',
    		'hr_shift_name_bn'    => 'max:255',
    		'hr_shift_start_time' => 'required|max:10',
            'hr_shift_end_time'   => 'required|max:10',
            'hr_shift_break_time' => 'required|max:3'
    	]);

    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput()
    			->with('error', 'Please fill-up all required fields!');
    	}
        $input = $request->all();
        // return $input;
        
        DB::beginTransaction();
    	try {
            unset($request['_token']);
            for ($i=0; $i < count($input['hr_shift_unit_id']); $i++) { 
                $unitId = $input['hr_shift_unit_id'][$i];
                $unitIdWord = Custom::convertNumberToWord($unitId);
                $unitIdWord = preg_replace('/\s+/', '', $unitIdWord);
                
                $getUnitName = Custom::unitIdWiseName($unitId);
                // unit id and shift name unique check
                $checkUnique = Shift::getCheckUniqueUnitIdShiftName($unitId, $input['hr_shift_name']);
                if($checkUnique != null){
                    $msg = "Unit: ".$getUnitName." Shift: ".$input['hr_shift_name']." Already Exists.";
                    toastr()->error($msg);
                    DB::rollback();
                    return back()->withInput();
                }
                // unit id and time check
                $checkTime = Shift::getCheckUniqueUnitIdTime($unitId, $input);
                if($checkTime != null){
                    $msg = "Unit: ".$getUnitName." This Time Shift Already Exists.";
                    toastr()->error($msg);
                    DB::rollback();
                    return back()->withInput();
                }
                $shiftNames = explode(' ', $input['hr_shift_name']);
                $shiftCode = "";
                foreach($shiftNames as $words)
                {
                    $shiftCode = $shiftCode . $words[0];
                }
                $shiftCodeUnit = $unitIdWord.$shiftCode;
                $shiftCodeUpper = strtoupper($shiftCodeUnit);
                $shiftCodeFormat = preg_replace('/[^a-zA-Z]/', '', $shiftCodeUpper); // spare number
                $newShiftCode = $this->uniqueShiftCodeUnitWise($unitId, $shiftCodeFormat, $shiftCodeFormat);
                $input['hr_shift_code'] = $newShiftCode;

                if($input['hr_shift_start_time'] > $input['hr_shift_end_time']){
                    $input['hr_shift_night_flag'] = 1;
                }else{
                    $input['hr_shift_night_flag'] = 0;
                }
                
                if($request->has('hr_shift_default'))
                {
                    DB::table('hr_shift')
                    ->where('hr_shift_unit_id', $unitId)
                    ->where('hr_shift_default', 1)
                    ->update([
                        'hr_shift_default'=> 0
                        ]);
                }else{
                    $input['hr_shift_default'] = 0;
                }



                $data = [
                    'hr_shift_unit_id'    => $unitId,
                    'hr_shift_name'       => $input['hr_shift_name'],
                    'hr_shift_name_bn'    => $input['hr_shift_name_bn'],
                    'hr_shift_start_time' => $input['hr_shift_start_time'],
                    'hr_shift_end_time'   => $input['hr_shift_end_time'],
                    'hr_shift_break_time' => $input['hr_shift_break_time'],
                    'hr_shift_default'    => $input['hr_shift_default'],
                    'hr_shift_night_flag' => $input['hr_shift_night_flag'],
                    'hr_shift_code'       => $input['hr_shift_code'],
                    'ot_shift'       => $input['ot_shift'],
                    'bill_eligible'       => ($input['bill_eligible'] == '00:00:00'?null:$input['bill_eligible']),
                    'created_by'          => auth()->user()->id
                ];
                if($request->has('ot_status')){
                    $data['ot_status'] = 1;
                }
                $shiftId = Shift::insertGetId($data);
                
                $msg = "Unit: ".$getUnitName." New Shift: ".$input['hr_shift_name']." Created Successfully.";
                $this->logFileWrite($msg, $shiftId);
            }
            
            DB::commit();
            Cache::forget('shift_code');
            toastr()->success("New Shift: ".$input['hr_shift_name']." Created Successfully.");
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back()->withInput();
        }
    }

    public function uniqueShiftCodeUnitWise($unit, $code, $value)
    {
        $checkCode = Shift::checkExistsShiftCode($unit, $code);
        if(empty($checkCode)){
            return $code;
        }else{
            $code = $code.$value;
            return $this->uniqueShiftCodeUnitWise($unit, $code, $value);
        }
    }

    # Return Shift List by Line ID with Select Option
    public function getShiftListByLineID(Request $request)
    {
        $list = "<option value=\"\">Select Shift Name </option>";
        if (!empty($request->unit_id))
        {
            $shiftList  = Shift::where('hr_shift_unit_id', $request->unit_id)
                    ->where('hr_shift_status', '1')
                    ->pluck('hr_shift_name', 'hr_shift_name');

            foreach ($shiftList as $key => $value)
            {
                $list .= "<option value=\"$key\">$value</option>";
            }
        }
        return $list;
    }

    public function shiftDelete($id)
    {
        DB::table('hr_shift')->where('hr_shift_id', $id)->delete();
        $this->logFileWrite("Shift Deleted", $id);
        Cache::forget('shift_code');
        return redirect('/hr/setup/shift')->with('success', "Successfuly deleted Shift");
    }


    public function shiftUpdate($id)
    {
        $shift= DB::table('hr_shift AS s')
          ->select("s.*", "u.hr_unit_name")
          ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 's.hr_shift_unit_id')
          ->where('hr_shift_id',$id)
          ->first();
        $unitids = implode(",", auth()->user()->unit_permissions());
        $shifts = DB::select("SELECT
            s1.hr_shift_id,
            s1.hr_shift_name,
            s1.hr_shift_code,
            s1.hr_shift_start_time,
            s1.hr_shift_end_time,
            s1.bill_eligible,
            s1.ot_status,
            s1.ot_shift,
            s1.hr_shift_break_time,u.hr_unit_name
            FROM hr_shift s1
            LEFT JOIN hr_shift s2
            ON (s1.hr_shift_unit_id = s2.hr_shift_unit_id AND s1.hr_shift_name = s2.hr_shift_name AND s1.hr_shift_id < s2.hr_shift_id)
            LEFT JOIN hr_unit AS u
            ON u.hr_unit_id = s1.hr_shift_unit_id
            WHERE s2.hr_shift_id IS NULL AND s1.hr_shift_unit_id IN ($unitids)
            ORDER BY s1.hr_shift_id DESC");

        $ot_shift = collect($shifts)->where('ot_status',1)->pluck('hr_shift_name','hr_shift_name');
        $trashed = [];
        return view('/hr/setup/shift_update', compact('shift','shifts','trashed','ot_shift'));
    }

    public function shiftUpdateStore(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'hr_shift_name_bn'       => 'max:255',
            'hr_shift_start_time'    => 'required|max:10',
            'hr_shift_end_time'      => 'required|max:10',
            'hr_shift_break_time'    => 'required|max:3'
        ]);

        if($validator->fails())
        {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        $input = $request->all();
        $getShift = Shift::getShiftIdWise($input['hr_shift_id']);
        $shift = Shift::checkExistsTimeWiseShift($input);

        DB::beginTransaction();
        try {
            $default_shift = 0;
            $night_shift = 0;
            //check default flag in shift table.
            if($request->has('hr_shift_default'))
            {
                DB::table('hr_shift')
                ->where('hr_shift_unit_id', $request->hr_shift_unit_id)
                ->where('hr_shift_name', $request->hr_shift_name)
                ->where('hr_shift_default', 1)
                ->update([
                    'hr_shift_default'=> 0
                    ]);
                $default_shift = 1;
            }

            if($input['hr_shift_start_time'] > $input['hr_shift_end_time']){
                DB::table('hr_shift')
                ->where('hr_shift_unit_id', $request->hr_shift_unit_id)
                ->where('hr_shift_name', $request->hr_shift_name)
                ->where('hr_shift_night_flag', 1)
                ->update([
                    'hr_shift_night_flag'=> 0
                    ]);
                $night_shift=1;
            }

            if($shift != null){
                // update record in shift table
                DB::table('hr_shift')
                ->where('hr_shift_id', $request->hr_shift_id)
                ->update([
                    'hr_shift_name'       => $request->hr_shift_name,
                    'hr_shift_name_bn'    => $request->hr_shift_name_bn,
                    'hr_shift_default'    => $default_shift,
                    'hr_shift_night_flag' => $night_shift,
                    'ot_shift'            => $request->ot_shift,
                    'bill_eligible'       => ($input['bill_eligible'] == '00:00:00'?null:$input['bill_eligible']),
                    'updated_by'          => auth()->user()->id
                    ]);
            }else{
                //new Shift code value
                $str = $getShift->hr_shift_code;
                $shiftcode = strrev( (int)strrev( $str ) );//separate Numbers
                $shiftcode_i = $shiftcode+1;
                $shift_ltr = preg_replace('/[^a-zA-Z]/', '', $str); //separate letter
                $newshiftCode = $shift_ltr.$shiftcode_i;
                
                // insert new record in shift table
                $data = [
                    'hr_shift_unit_id'    => $request->hr_shift_unit_id,
                    'hr_shift_name'       => $request->hr_shift_name,
                    'hr_shift_name_bn'    => $request->hr_shift_name_bn,
                    'hr_shift_start_time' => date("H:i:s", strtotime($request->hr_shift_start_time)),
                    'hr_shift_end_time'   => date("H:i:s", strtotime($request->hr_shift_end_time)),
                    'hr_shift_break_time' => $request->hr_shift_break_time,
                    'hr_shift_code'       => $newshiftCode,
                    'hr_shift_night_flag' => $night_shift,
                    'hr_shift_default'    => $default_shift,
                    'bill_eligible'       => ($input['bill_eligible'] == '00:00:00'?null:$input['bill_eligible']),
                    'created_by'          => auth()->user()->id
                ]; 
                $shiftId = Shift::insertGetId($data);
            }
            DB::commit();
            //get shift name
            $getUnitName = Custom::unitIdWiseName($request->hr_shift_unit_id);
            $msg = $getUnitName." Shift Successfully updated";
            $this->logFileWrite($msg, $request->hr_shift_id);
            Cache::forget('shift_code');
            toastr()->success($msg);
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            toastr()->error($bug);
            return redirect()->back()->with('error', $bug);
        }
    }



    public function getPreShiftTimes(Request $request){
            $sft_code = $request->shift_code;

            $data = Shift::select([
               'hr_shift_name',
               'hr_shift_code',
               'hr_shift_start_time',
               'hr_shift_end_time',
               'hr_shift_break_time',
               'created_at',
               'updated_at',
              ])
             ->where('hr_shift_code', 'LIKE', "{$sft_code}%")
             ->get()->toArray();
            // dd($data);exit;
        return Response::json($data);
    }

    public function getShiftTimes(Request $request){
            $input = $request->all();
            // return $input;
            $data = Shift::select([
                   'hr_shift_name',
                   'hr_shift_code',
                   'hr_shift_start_time',
                   'hr_shift_end_time',
                   'hr_shift_break_time'
                  ])
                 ->where('hr_shift_name', $input['shift_code'])
                 ->where('hr_shift_unit_id', $input['unit_id'])
                 ->latest()
                 ->first();
            // dd($data);exit;
        return Response::json($data);
    }

    public function shiftUpdateEmployee(Request $request)
    {
        $input = $request->all();
        try {
            foreach ($request->getEmpData as $key => $emp) {
                $getEmployee = Employee::where('as_id', $emp['as_id'])
                ->update([
                    'as_shift_id' => $input['shiftId']
                ]);
                $this->logFileWrite("Shift time is change then employee shift Updated", $emp['as_id']);
            }
            return 'success';
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }

    public function shiftUpdateRoasterEmployee(Request $request)
    {
        $input = $request->all();
        $day = date('j');
        $month = date('n');
        $year = date('Y');
        try {
            foreach ($request->getRoasterdata as $key => $roaster) {
                if(($month > $roaster['shift_roaster_month']) || ($year > $roaster['shift_roaster_year'])){
                    $day = 1;
                }

                for($j=$day; $j<=31; $j++)
                {
                    $dayNum= "day_".$j;
                    if(isset($roaster['day_'.$j]) && $roaster['day_'.$j] != null && $roaster['day_'.$j] == $input['oldShiftCode']){

                        ShiftRoaster::where('shift_roaster_id', $roaster['shift_roaster_id'])
                        ->update([$dayNum => $input['newshiftCode']]);

                        $this->logFileWrite("Shift time is change then Shift Roaster Day Wise Updated", $roaster['shift_roaster_user_id']);
                    } 
                }
            }
            return 'success';
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
        }
    }


}
