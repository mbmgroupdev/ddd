<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class HrLateCountController extends Controller
{
    public function showForm()
    {
        try {
            $unit_list = Unit::pluck('hr_unit_name','hr_unit_id')->all();
            $lateCountDefault_list = HrLateCount::all();
        	return view('hr/setup/hr_late_count_default', compact('unit_list','lateCountDefault_list'));
        } catch(\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getShiftsByUnit($unit_id){
        $data = DB::table('hr_shift')
        ->where('hr_shift_unit_id','=',$unit_id)
        ->select('hr_shift_id', 'hr_shift_name', 'hr_shift_code', 'hr_shift_start_time', 'hr_shift_end_time')
        ->get()
        ->toArray();
        return $data;
    }

    public function saveLateCountDefault(Request $request)
    {
        // check form validation
        $validator = Validator::make($request->all(), [
            'hr_unit_id'       => 'required',
            'default_value'    => 'required|numeric',
            'hr_shift_name'    => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $input = $request->all();
        try {
            if($input['hr_shift_name'] == 'all'){
                $getShifts = Shift::getShiftsByUnitIdWiseUqiue($input['hr_unit_id']);
                foreach ($getShifts as $shift) {
                    $input['hr_shift_name'] = $shift['hr_shift_name'];
                    $data = $this->lateCountCreateUpdate($input);
                }
                if($data['type'] == 'success'){
                    $getUnitName = Custom::unitIdWiseName($input['hr_unit_id']);
                    $msg = "Unit: ".$getUnitName." & Shift: All Default Late Count Assign ".$input['default_value'];
                }
            }else{
                $data = $this->lateCountCreateUpdate($input);
                if($data['type'] == 'success'){
                    $msg = $data['value'];
                }
            }
            
            return redirect()->back()->with('success', $msg);
        } catch(\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function lateCountCreateUpdate($input)
    {
        try {
            $getLateCount = HrLateCount::getUnitShiftNameWiseCheckExists($input);
            $getUnitName = Custom::unitIdWiseName($input['hr_unit_id']);
            if(empty($getLateCount)) {
                $getLateCountId = HrLateCount::create($input)->id;
                $msg = "Unit: ".$getUnitName." & Shift: ".$input['hr_shift_name']." Default Late Count Assign ".$input['default_value'];
            } else {
                $getLateCountId = $getLateCount->id;
                $getLateCount = HrLateCount::findOrFail($getLateCountId);
                $msg = "Unit: ".$getUnitName." & Shift: ".$input['hr_shift_name']." Default Late Count Updated ".$getLateCount->default_value." to ".$input['default_value'];
                $getLateCount->update($input);
            } 
            $this->logFileWrite($msg, $getLateCountId);
            $result['type'] = 'success';
            $result['value'] = $msg;
            return $result;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            $result['type'] = 'error';
            $result['value'] = $bug;
            return $result;
        }
    }

    public function ajaxGetLateCountUnitValue(Request $request)
    {
        $result = array();
        try {
            $result['value'] = Shift::getShiftsByUnitIdWiseUqiue($request->unit_id);
            $result['status'] = 'success';
            return $result;
        } catch(\Exception $e) {
            $result['status'] = 'error';
            $result['value'] = $e->getMessage();
            return $result;
        }
    }

    public function ajaxGetLateCountDefaultValue(Request $request)
    {
        try {
            $input = $request->all();
            $HrLateCount = HrLateCount::getUnitShiftNameWiseCheckExists($input);
            return $HrLateCount;
        } catch(\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }



}