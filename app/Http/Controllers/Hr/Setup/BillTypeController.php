<?php

namespace App\Http\Controllers\Hr\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\BillTypeRequest;
use App\Models\Hr\BillType;
use Illuminate\Http\Request;

class BillTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getType = BillType::get();
        return view('hr/setup/bill/type', compact('getType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillTypeRequest $request)
    {
        try {
            $request->store();
            toastr()->success('Successfully Created Bill Type');
            return back();
        } catch (\Exception $e) {
            toastr()->error($e->getMessage());
            return back();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        try {
            $type = BillType::findOrFail($id);
            $type->delete();
            toastr()->success('Successfully Deleted Bill Type');
            return back();
        } catch (\Exception $e) {
            toastr()->error($e->getMessage());
            return back();
        }
    }
}
