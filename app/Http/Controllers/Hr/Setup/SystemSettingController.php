<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Validator,DB,ACL;




class SystemSettingController extends Controller
{
    #show subsection
    public function showForm()
    {
        $system_setting=DB::table('hr_system_setting')->latest()->get();
        // dd($system_setting);
    	$id=DB::table('hr_system_setting')->first();

        // dd($id);

    	return view('hr.setup.default_system_setting', compact('system_setting','id'));
    }

    public function saveForm(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'salary_lock' =>'required'
        ]);


        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        else
        {
            DB::table('hr_system_setting')->where('id',$request->lock_id)
            ->update([
                'salary_lock' => $request->salary_lock
                
            ]);

            $this->logFileWrite("Salary Lock Saved", $request->salary_lock );
            return redirect('/hr/setup/default_system_setting')->with('success', "Successfuly updated Salary Lock");
        }
    }

    public function updateForm($id)
    {
        $system_setting=DB::table('hr_system_setting')->where('id',$id)->first();
        // dd($system_setting);
        
        return view('hr.setup.default_system_setting_update', compact('system_setting','id'));
    }

    public function updateStore(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'salary_lock' =>'required'
        ]);


        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        else
        {
            DB::table('hr_system_setting')->where('id',$request->salary_lock_id)
            ->update([
                'salary_lock' => $request->salary_lock
                
            ]);

            return redirect('/hr/setup/default_system_setting')->with('success', "Successfuly updated Salary Lock");
        }
    }

    public function deleteData($id){
        DB::table('hr_system_setting')->where('id', $id)->delete();
        return redirect('/hr/setup/default_system_setting')->with('success', "Successfuly deleted Data");
    }

   
}
