<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Http\Controllers\Controller;
use App\Models\Hr\BillSettings;
use App\Models\Hr\BillSpecialSettings;
use App\Models\Hr\BillType;
use App\Models\Hr\Designation;
use App\Models\Hr\Unit;
use Illuminate\Http\Request;
use Validator, DB;

class BillAnnounceSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['billType']  = BillType::get()->keyBy('id');
        $data['billTypeList']  = collect($data['billType'])->pluck('name', 'id');
        $data['unit'] = unit_by_id();
        $data['unitList']  = collect($data['unit'])->pluck('hr_unit_name', 'hr_unit_id');
        $data['billList'] = BillSettings::with('available_special')->whereIn('unit_id', auth()->user()->unit_permissions())->whereNull('end_date')->get();
        return view('hr.setup.bill.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $data['type'] = 'error';
        $validator = Validator::make($request->all(), [
            'unit' => 'required',
            'bill_type_id' => 'required'
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                $data['message'][] = $message;
            }
            return $data;
        }
        $input = $request->all();

        DB::beginTransaction();
        try {
            $specialData = [];
            $totalUnit = count($input['unit']);
            for ($i=0; $i < $totalUnit; $i++) { 
                // updated another bill end date
                $input['unit_id'] = $input['unit'][$i];
                $lastCode = BillSettings::checkUnitTypeWiseExistsCode($input);
                $unitTypeMix = $input['unit_id'].$input['bill_type_id'];
                if($lastCode != null){
                    $billCode = explode($unitTypeMix, $lastCode);
                    $adjustNo = ((int)$billCode[1]+1)??1;
                }else{
                    $adjustNo = 1;
                }
                $code = $this->getCheckUniqueCode($unitTypeMix, $adjustNo);
                // update previous bill 
                BillSettings::updatePreviousBillUnitWiseStatus($input);
                
                // create bill setup
                $bill = [
                    'unit_id' => $input['unit_id'],
                    'code' => $code,
                    'bill_type_id' => $input['bill_type_id'],
                    'amount' => $input['amount'],
                    'start_date' => $input['start_date'],
                    'end_date'  => $input['end_date'],
                    'pay_type'  => $input['pay_type'],
                    'duration'  => $input['duration'],
                    'as_ot'       => $input['as_ot'],
                    'created_by'  => auth()->user()->id
                ];
                
                $special['bill_setup_id'] = BillSettings::create($bill)->id;
                foreach ($input['special_rule'] as $key => $value) {
                    $special['adv_type'] = $key;
                    $totalKey = count($value);
                    for ($s=0; $s < $totalKey; $s++) { 
                        $keyValue = $value[$s];
                        if($key == 'outtime'){
                            $keyValue['pay_type'] = 0;
                            $keyValue['duration'] = 0;
                        }
                        $special['parameter'] = $keyValue['id'];
                        $special['amount'] = $keyValue['amount'];
                        $special['pay_type'] = $keyValue['pay_type'];
                        $special['duration'] = $keyValue['duration'];
                        $special['start_date'] = $input['start_date'];
                        $special['end_date']  = $input['end_date'];
                        $special['created_by']  = auth()->user()->id;
                        $specialData[] = $special;
                    }
                }
            }

            if(count($specialData) > 0){
                BillSpecialSettings::insert($specialData);
            }
            
            $data['url'] = url()->current();
            DB::commit();
            $data['type'] = 'success';
            $data['message'][] = 'Successfully Created';
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            $data['message'][] = $e->getMessage();
            return $data;
        }
    }
    public function getCheckUniqueCode($value, $no)
    {
        $code = $value.$no;
        $checkCode = BillSettings::checkExistsCode($code);
        if(empty($checkCode)){
            return $code;
        }else{
            $no = ($no+1);
            return $this->getCheckUniqueCode($value, $no);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = BillSettings::findOrFail($id);
        $billGroup = [];
        if(count($bill->available_special) > 0){
            $billGroup = collect($bill->available_special)->sortBy('pay_type')->groupBy('adv_type', true);
        }
        $data['billGroup'] = $billGroup;
        $data['bill'] = $bill;
        $data['unit'] = unit_by_id();
        $data['location']    = location_by_id();
        $data['department']  = department_by_id();
        $data['designation'] = designation_by_id();
        $data['section']     = section_by_id();
        $data['subSection']  = subSection_by_id();
        return view('hr.setup.bill.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
