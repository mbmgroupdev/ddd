<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Http\Controllers\Controller;
use App\Models\Hr\HrLateCount;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use App\Models\Hr\hrLateCountCustomize;
use DB,Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HrLateCountCustomizeController extends Controller
{
    public function showForm($lateCountCustomize_single='')
    {
        try {
            if(empty($lateCountCustomize_single)) {
                $lateCountCustomize_single = (object)[
                    'id'            => '',
                    'hr_unit_id'    => '',
                    'shift_id'      => '',
                    'date_from'     => '',
                    'date_to'       => '',
                    'time'          => '',
                    'comment'       => ''
                ];
            }
            $unit_list               = Unit::pluck('hr_unit_name','hr_unit_id')->all();
            $lateCountCustomize_list = hrLateCountCustomize::all();
        	return view('hr/setup/hr_late_count_customize', compact('unit_list','lateCountCustomize_list','lateCountCustomize_single'));
        } catch(\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function getShiftsByUnit(Request $request){
        $data = DB::table('hr_shift')->where('hr_shift_unit_id','=', $request->unit_id)
                                    ->select('hr_shift_id', 'hr_shift_name', 'hr_shift_code', 'hr_shift_start_time', 'hr_shift_end_time')
                                    ->get()->toArray();

        return Response::json($data);
    }

    public function roles()
    {
        return $role = [
            'hr_unit_id'    => 'required',
            'hr_shift_name' => 'required',
            'date_from'     => 'required',
            'date_to'       => 'required',
            'time'          => 'required|numeric'
        ];
    }
    public function saveLateCountCustomize(Request $request)
    {
        // check validation
        $validator = Validator::make($request->all(), $this->roles());
        if ($validator->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        $input = $request->all();
        unset($input['_token']);
        // return $input;
        try {
            if($input['hr_shift_name'] == 'all'){
                $getShifts = Shift::getShiftsByUnitIdWiseUqiue($input['hr_unit_id']);
                foreach ($getShifts as $shift) {
                    $input['hr_shift_name'] = $shift['hr_shift_name'];
                    $data = $this->lateCountCreateUpdate($input);
                }
                if($data['type']){
                    $getUnitName = Custom::unitIdWiseName($input['hr_unit_id']);
                    $msg = "Unit: ".$getUnitName." & Shift: All Customize Late Count Assign ".$input['time'];
                }
            }else{
                $getLateCustomize = hrLateCountCustomize::checkExistsAlreadyHaving($input);
                $data = $this->lateCountCreateUpdate($input);
                if($data['type']){
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
        DB::beginTransaction();
        try {
            $getLateCustomize = hrLateCountCustomize::checkExistsAlreadyHaving($input);
            if(empty($getLateCustomize)) {
                $id = hrLateCountCustomize::insertGetId($input);
                $getLateCount = HrLateCount::getUnitShiftIdWiseCheckExists($input['hr_unit_id'], $input['hr_shift_name']);
                
                if($getLateCount != null){
                    $defultData = [
                        'date_from' => $input['date_from'],
                        'date_to'   => $input['date_to'],
                        'value'     => $input['time']
                    ];
                    $getUnitLate = HrLateCount::where('id',$getLateCount->id)->update($defultData);
                    
                }else{
                    $defultData = [
                        'hr_unit_id'    => $input['hr_unit_id'],
                        'hr_shift_name' => $input['hr_shift_name'],
                        'default_value' => $input['time'],
                        'date_from'     => $input['date_from'],
                        'date_to'       => $input['date_to'],
                        'value'         => $input['time']
                    ];
                    HrLateCount::create($defultData);
                }
                $msg = 'Late Count Customize Insert Success';
                $result['type'] = 'success';
            } else {
                $id = $getLateCustomize->id;
                $result['type'] = 'warning';
                $msg = 'Time exist in this date range.';
            }

            $this->logFileWrite($msg, $id);
            $result['value'] = $msg;
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $result['type'] = 'error';
            $result['value'] = $bug;
            return $result;
        }
    }

    public function editLateCountCustomize($id)
    {
        try {
            $lateCountCustomize_single = hrLateCountCustomize::where('id',$id)->first();
            if(!empty($lateCountCustomize_single)){
                return $this->showForm($lateCountCustomize_single);
            } else {
                return redirect()->back()->with('error','Data not found.');
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateLateCountCustomize(Request $request, $id)
    {
        // check validation
        $validator = Validator::make($request->all(), $this->roles());
        if ($validator->fails()) {
            return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        // check if exist
        $getLateCountCustomize = hrLateCountCustomize::findOrFail($id);
        if(empty($getLateCountCustomize)) {
            return redirect()->back()->with('error','Data not found.');
        }
        $input = $request->all();
        DB::beginTransaction();
        try {
            $getLateCount = HrLateCount::getUnitShiftWiseCheckExists($input['unit_id'], $input['shift_id']);
            $defultData = [
                'date_from' => $input['date_from'],
                'date_to'   => $input['date_to'],
                'value'     => $input['time']
            ];
            $getUnitLate = HrLateCount::where('id',$getLateCount->id)->update($defultData);
            $getLateCountCustomize->update($input);
            $msg = 'Late Count Customize Update Success';
            $this->logFileWrite($msg, $id);
            DB::commit();
            return redirect()->back()->with('success', $msg);
        } catch(\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function deleteLateCountCustomize($id)
    {
        DB::beginTransaction();
        try {
            // check if exist
            $getLateCountCustomize = hrLateCountCustomize::where('id',$id)->first();
            if(!empty($getLateCountCustomize)) {
                $getLateCount = HrLateCount::getUnitShiftWiseCheckExists($getLateCountCustomize->hr_unit_id, $getLateCountCustomize->hr_shift_id);
                $getCheckLate = HrLateCount::getCheckExistsLateCount($getLateCountCustomize);
                if($getCheckLate != null){
                    $defultData = [
                        'date_from' => '',
                        'date_to'   => '',
                        'value'     => ''
                    ];
                    $getUnitLate = HrLateCount::where('id',$getLateCount->id)->update($defultData);
                }
                
                hrLateCountCustomize::where('id',$id)->delete();
                $msg = 'Late Count Customize Delete Success';
                $this->logFileWrite($msg, $id);
                DB::commit();
                return redirect('hr/setup/late_count_customize')->with('success','Delete Success');
            } else {
                return redirect()->back()->with('error','Data not found.');
            }
        } catch(\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

}