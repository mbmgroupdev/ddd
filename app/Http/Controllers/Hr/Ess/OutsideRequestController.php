<?php

namespace App\Http\Controllers\Hr\Ess;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Location;
use App\Models\Hr\Outsides;
use DB, Validator, ACL, Auth;

class OutsideRequestController extends Controller
{
    //show entry form
    public function showForm(){
    	$locationList= Location::pluck('hr_location_name', 'hr_location_id');
    	$locationList['Outside']= "Outside";

    	// dd(auth()->user()->associate_id);
    	$requestList= Outsides::where('as_id', auth()->user()->associate_id)->orderBy('start_date','desc')->get();
    	foreach ($requestList as $value) {
    		if(is_numeric($value->requested_location)){
    			$value->location_name= Location::where('hr_location_id', $value->requested_location)->pluck('hr_location_name')->first();
    		}
    		else{
    			$value->location_name= $value->requested_location;
    		}
    	}
    	return view('hr/ess/outside_request_entry', compact('locationList', 'requestList'));
    }

    //store data
    public function storeData(Request $request){
    	$validator= Validator::make($request->all(),[
    		'start_date' 			=> 'required|date',
    		'end_date' 				=> 'date',
    		'requested_location' 	=> 'required',
            'type'                  => 'required|max:4',
    		'requested_place' 		=> 'max:64',
    		'comment' 				=> 'max:128',
    	]);

    	if($validator->fails()){
    		return back()
    				->withInput()
    				->withErrors($validator);
    	}
    	else{
    		DB::beginTransaction();
    		try {

    			$out= new Outsides();

    			$out->as_id = auth()->user()->associate_id;
    			$out->start_date = $request->start_date;
    			$out->end_date = $request->end_date;
    			$out->requested_location = $request->requested_location;
                $out->type = $request->type;
    			$out->requested_place = $request->requested_place;
    			$out->comment = $request->comment;
    			$out->status = 0;

    			$out->save();

    			DB::commit();
    			return back()
    					->with('success', 'Outside Request submitted for approval!');

    		} catch (\Exception $e) {
    			DB::rollback();
    			$msg= $e->getMessage();
    			return back()
    					->withInput()
    					->withErrors($msg);
    		}
    	}
    }

    //delete outside request
    public function deleteRequest(Request $request){
    	$id= $request->id;
    	$out= Outsides::where('id', $id)->first();

    	if($out->status==1){
    		return back()
    				->with('error', "You cant not delete approved request!");
    	}
    	else{
    		$out->delete();
    		return back()
    				->with('success', "Request deleted successfully!");
    	}

    }
}
