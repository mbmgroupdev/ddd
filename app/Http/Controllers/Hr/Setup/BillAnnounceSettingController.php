<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Http\Controllers\Controller;
use App\Models\Hr\BillSettings;
use App\Models\Hr\BillSpecialSettings;
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
        $data['unitList']  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('hr_unit_name', 'desc')
        ->pluck('hr_unit_name', 'hr_unit_id');
        $data['billList'] = BillSettings::with('available_special')->whereNull('end_date')->get();
        return view('hr.setup.bill.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit' => 'required'
            
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->except('_token');
        // return $input;
        DB::beginTransaction();
        try {
            $allUnit = count($input['unit']);
            for ($i=0; $i < $allUnit; $i++) { 
                // updated another bill end date
                $unitId = $input['unit'][$i];
                $getBill = BillSettings::
                where('unit_id', $unitId)
                ->whereNull('end_date')
                ->update([
                    'end_date' => date('Y-m-d'),
                    'status' => 0,
                    'updated_by' => auth()->user()->id
                ]);
                // create bill
                $bill = [
                    'unit_id' => $unitId,
                    'tiffin_bill' => $input['tiffin'],
                    'dinner_bill' => $input['dinner'],
                    'start_date'  => date('Y-m-d'),
                    'as_ot'       => $input['as_ot'],
                    'created_by'  => auth()->user()->id
                ];
                $billId = BillSettings::create($bill)->id;

                $this->logFileWrite("Tiffin/Dinner Bill Setup Create", $billId);
                // bill special 
                
                $allUnitSpecial = count($input['designation']);
                for ($j=0; $j < $allUnitSpecial; $j++) { 
                    $designation = $input['designation'][$j];
                    if($designation != null){
                        $getDesignation = Designation::getDesignationCheckExists($designation);
                        if($getDesignation != null){
                            // updated another special bill end date
                            // $getBillSpecial = BillSpecialSettings::
                            // where('bill_id', $billId)
                            // ->where('designation_id', $designation)
                            // ->whereNull('end_date')
                            // ->update([
                            //     'end_date' => date('Y-m-d')
                            // ]);
                            // create special bill
                            $billSpecial = [
                                'bill_id' => $billId,
                                'designation_id' => $getDesignation->hr_designation_id,
                                'tiffin_bill' => $input['special_tiffin'][$j],
                                'dinner_bill' => $input['special_dinner'][$j],
                                'start_date' => date('Y-m-d')
                            ];
                            BillSpecialSettings::create($billSpecial);
                        }
                    }
                    
                }
            }
            DB::commit();
            toastr()->success('Successfully Created Setup');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
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
        //
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
