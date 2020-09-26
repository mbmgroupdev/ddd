<?php

namespace App\Http\Controllers\Hr\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hr\Unit;

Use Validator,Image,DB;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units= Unit::get();
        $trashed= Unit::onlyTrashed();

        return view('hr/settings/unit', compact('units','trashed'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'hr_unit_name'=>'required|max:128|unique:hr_unit',
            'hr_unit_short_name'=>'required|max:64|unique:hr_unit',
            'hr_unit_name_bn'=>'max:255',
            'hr_unit_code'=>'max:10',
            'hr_unit_address'=>'max:255',
            'hr_unit_address_bn'=>'max:512',
            'hr_unit_logo' => 'image|mimes:jpeg,png,jpg|max:200|dimensions:min_width=248,min_height=148',
            'hr_unit_authorized_signature' => 'image|mimes:jpeg,png,jpg|max:80|dimensions:min_width=120,min_height=80'
        ]);

        #-----------Logo---------------------
        $hr_unit_logo = null;
        $hr_unit_authorized_signature = null;

        if($request->hasFile('hr_unit_logo'))
        {
            $file = $request->file('hr_unit_logo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $hr_unit_logo = '/assets/idcard/' . $filename;
            Image::make($file)->resize(248, 148)->save(public_path( $hr_unit_logo ) );
        }
        #---------- signature---------------------------
        if($request->hasFile('hr_unit_authorized_signature'))
        {
            $file = $request->file('hr_unit_authorized_signature');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $hr_unit_authorized_signature = '/assets/idcard/' . $filename;
            Image::make($file)->resize(120, 80)->save(public_path( $hr_unit_authorized_signature ) );
        }

        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            $unit= new Unit();
            $unit->hr_unit_name       = $request->hr_unit_name;
            $unit->hr_unit_short_name = $request->hr_unit_short_name;
            $unit->hr_unit_name_bn    = $request->hr_unit_name_bn;
            $unit->hr_unit_address    = $request->hr_unit_address;
            $unit->hr_unit_address_bn = $request->hr_unit_address_bn;
            $unit->hr_unit_code       = $request->hr_unit_code;
            $unit->hr_unit_logo       = $hr_unit_logo;
            $unit->hr_unit_authorized_signature = $hr_unit_authorized_signature;

            if ($unit->save()){
                $this->logFileWrite("Unit Saved", $unit->hr_unit_id );
                
                return back()->withInput()
                    ->with('success', 'Save Successful.');
            }else{
                return back()->withInput()
                ->with('error', 'Please try again.');
            }
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
