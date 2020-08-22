<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;

Use Validator, Image,ACL,DB;


class LocationController extends Controller
{
    public function location()
    {
        //ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
        $locations= Location::all();
        $unitList= Unit::pluck('hr_unit_name', 'hr_unit_id');
    	return view('hr/setup/location', compact('locations','unitList'));
    }

    public function locationStore(Request $request)
    {
        //ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
    	$validator= Validator::make($request->all(),[
            'hr_location_name'=>'required|max:128|unique:hr_location',
    		'hr_location_short_name'=>'required|max:64|unique:hr_location',
            'hr_location_unit_id'=>'required|max:11',
            'hr_location_name_bn'=>'max:255',
            'hr_location_code'=>'max:10',
            'hr_location_address'=>'max:255',
    		'hr_location_address_bn'=>'max:512',
            'hr_location_logo' => 'image|mimes:jpeg,png,jpg|max:200|dimensions:min_width=248,min_height=148',
    	]);



    	if($validator->fails()){
    		return back()
    			->withErrors($validator)
    			->withInput()
    			->with('error', 'Please fillup all required fields!');
    	}
    	else
        {
         DB::beginTransaction();
         try {
    		$loc= new Location();
            $loc->hr_location_name       = $request->hr_location_name;
    		$loc->hr_location_short_name = $request->hr_location_short_name;
            $loc->hr_location_unit_id = $request->hr_location_unit_id;
            $loc->hr_location_name_bn    = $request->hr_location_name_bn;
            $loc->hr_location_address    = $request->hr_location_address;
            $loc->hr_location_address_bn = $request->hr_location_address_bn;
            $loc->hr_location_code     = $request->hr_location_code;
    	    $loc->save();

            $this->logFileWrite("Location Entry Saved", $loc->hr_location_id);


            DB::commit();


            return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
            }
            catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            //$bug = $e->errorInfo[1];;

            return redirect()->back()->with('error',$bug);
          }

    	}
    }
    public function locationDelete($id){
        //dd($id);
        Location::where('hr_location_id','=',$id)->delete();
        $this->logFileWrite("Location Entry Deleted", $id);
        return redirect('/hr/setup/location')->with('success', "Successfuly deleted Location");

    }
    public function locationUpdate($id){
        $location= DB::table('hr_location')->where('hr_location_id','=',$id)->first(); //dd($location);
        $unitList= Unit::pluck('hr_unit_name', 'hr_unit_id');
        return view('/hr/setup/location_update',compact('location','unitList'));
    }
    public function locationUpdateStore(Request $request){
        // dd($request->all());
        $validator= Validator::make($request->all(),[
            'hr_location_name'=>'required|max:128',
            'hr_location_short_name'=>'required|max:64',
            'hr_location_unit_id'=>'required|max:11',
            'hr_location_name_bn'=>'max:255',
            'hr_location_code'=>'max:10',
            'hr_location_address'=>'max:255',
            'hr_location_address_bn'=>'max:512',
            'hr_location_logo' => 'image|mimes:jpeg,png,jpg|max:200|dimensions:min_width=248,min_height=148',
        ]);
        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }
        else{

            DB::beginTransaction();
            try {
              DB::table('hr_location AS u')->where('u.hr_location_id', '=', $request->hr_location_id)
              ->update([
                'hr_location_name' => $request->hr_location_name,
                'hr_location_short_name' => $request->hr_location_short_name,
                'hr_location_unit_id' => $request->hr_location_unit_id,
                'hr_location_name_bn' => $request->hr_location_name_bn,
                'hr_location_address' => $request->hr_location_address,
                'hr_location_address_bn' => $request->hr_location_address_bn,
                'hr_location_code' => $request->hr_location_code
              ]);
               $this->logFileWrite("Location Entry Updated", $request->hr_location_id);
              DB::commit();
              return redirect('/hr/setup/location')->with('success', "Successfuly updated Location");
            }
            catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            //$bug = $e->errorInfo[1];;

            return redirect()->back()->with('error',$bug);
          }
        }

    }

}
