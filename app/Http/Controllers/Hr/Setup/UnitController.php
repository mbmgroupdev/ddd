<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;

Use Validator, Image,ACL,DB;


class UnitController extends Controller
{
    

    public function unit()
    {
        $units= Unit::get();
        $trashed= Unit::onlyTrashed();

        return view('hr/settings/unit', compact('units','trashed'));
    }

    public function unitStore(Request $request)
    {
        //ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
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

        //-----------Logo UPLOAD---------------------
        $hr_unit_logo = null;
        $hr_unit_authorized_signature = null;

        if($request->hasFile('hr_unit_logo'))
        {
            $file = $request->file('hr_unit_logo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $hr_unit_logo = '/assets/idcard/' . $filename;
            Image::make($file)->resize(248, 148)->save(public_path( $hr_unit_logo ) );
        }

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
    		$unit->hr_unit_logo		  = $hr_unit_logo;
            $unit->hr_unit_authorized_signature = $hr_unit_authorized_signature;

    		if ($unit->save())
            {
                $this->logFileWrite("Unit Saved", $unit->hr_unit_id );
                return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
    	}
    }
    public function unitDelete($id){
        //dd($id);
        Unit::where('hr_unit_id','=',$id)->delete();
        $this->logFileWrite("Unit Deleted", $id );
        return redirect('/hr/setup/unit')->with('success', "Successfuly deleted Unit");

    }
    public function unitUpdate(Request $request){
        $unit= DB::table('hr_unit')->where('hr_unit_id','=',$request->hr_unit_id)->first();
        return view('/hr/setup/unit_update',compact('unit'));
    }
    public function unitUpdateStore(Request $request){
        // dd($request->all());
        $validator= Validator::make($request->all(),[
            'hr_unit_name'=>'required|max:128',
            'hr_unit_short_name'=>'required|max:64',
            'hr_unit_name_bn'=>'max:255',
            'hr_unit_code'=>'max:10',
            'hr_unit_address'=>'max:255',
            'hr_unit_address_bn'=>'max:512',
            'hr_unit_logo' => 'image|mimes:jpeg,png,jpg|max:200|dimensions:min_width=248,min_height=148',
            'hr_unit_authorized_signature' => 'image|mimes:jpeg,png,jpg|max:80|dimensions:min_width=120,min_height=80'
        ]);
        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }
        else{
            if($request->hasFile('hr_unit_logo')){
                $file = $request->file('hr_unit_logo');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $hr_unit_logo = '/assets/idcard/' . $filename;
                Image::make($file)->resize(248, 148)->save(public_path( $hr_unit_logo ) );
            }
            else{
                $hr_unit_logo= $request->old_pic;
                // $file = $request->file('old_pic');
                // $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                // $hr_unit_logo = '/assets/idcard/' . $filename;
                // Image::make($file)->save(public_path( $hr_unit_logo ) );
            }

            //Siganture Path-with file
            if($request->hasFile('hr_unit_authorized_signature'))
            {
                $file = $request->file('hr_unit_authorized_signature');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $hr_unit_authorized_signature = '/assets/idcard/' . $filename;
                Image::make($file)->resize(120, 80)->save(public_path( $hr_unit_authorized_signature ) );
            }
            else{
                $hr_unit_authorized_signature = $request->old_signature;
            }


          DB::table('hr_unit AS u')->where('u.hr_unit_id', '=', $request->hr_unit_id)
          ->update([
            'hr_unit_name' => $request->hr_unit_name,
            'hr_unit_short_name' => $request->hr_unit_short_name,
            'hr_unit_name_bn' => $request->hr_unit_name_bn,
            'hr_unit_address' => $request->hr_unit_address,
            'hr_unit_address_bn' => $request->hr_unit_address_bn,
            'hr_unit_code' => $request->hr_unit_code,
            'hr_unit_logo' => $hr_unit_logo,
            'hr_unit_authorized_signature' => $hr_unit_authorized_signature
          ]);

          $this->logFileWrite("Unit Updated", $request->hr_unit_id );

          return redirect('/hr/setup/unit')->with('success', "Successfuly updated Unit");
        }

    }

}
