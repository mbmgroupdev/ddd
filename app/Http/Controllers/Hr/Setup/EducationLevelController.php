<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\EducationLevel;
use App\Models\Hr\EducationDegree;
use DB, Validator, ACL;
class EducationLevelController extends Controller
{
    public function showForm(){
    	//ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
    	$levelList= DB::table('hr_education_level AS l')->pluck('education_level_title', 'id');
        $degrees=DB::table('hr_education_degree_title AS a')
                    ->select(
                        "a.id",
                        "b.education_level_title",
                        "a.education_degree_title"
                    )
                    ->leftJoin("hr_education_level AS b", "b.id", "=", "a.education_level_id")
                    ->get();


    	return view('hr/setup/education_title', compact('levelList','degrees'));
    }
    public function saveData(Request $request){
    	//ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
        $validator= Validator::make($request->all(),[
        	'id'					=> 'required',
        	'education_degree_title' => 'required| max:128'
        ]);
        if($validator->fails())
        {
        	return back()
        			->withInput()
        			->with('error', "Invalid Input!");
        }
        else{
        	$title= new EducationDegree();

        	$title->education_level_id = $request->id ;
        	$title->education_degree_title = $request->education_degree_title ;

        	if($title->save()){
                $this->logFileWrite("Education Degree Saved", $title->id );
        		return back()
        				->with('success', "Saved Successfully!!");
        	}
        	else{
        		return back()
        			->withInput()
        			->with('error', 'Something wrong!!');
        	}
        }
    }

    public function degreeDelete($id){
        EducationDegree::where('id',$id)->delete();
       
        return redirect('/hr/setup/education_title')->with('success', "Successfuly deleted Degree");
    }

    public function degreeEdit($id){
        $levelList= DB::table('hr_education_level AS l')->pluck('education_level_title', 'id');

        $degree=DB::table('hr_education_degree_title AS a')
                ->select(
                    "a.id",
                    "b.education_level_title",
                    "a.education_degree_title",
                    "b.id AS lvl_id"
                )
                ->leftJoin("hr_education_level AS b", "b.id", "=", "a.education_level_id")->where('a.id', $id)->first();
        $degrees=DB::table('hr_education_degree_title AS a')
                    ->select(
                        "a.id",
                        "b.education_level_title",
                        "a.education_degree_title"
                    )
                    ->leftJoin("hr_education_level AS b", "b.id", "=", "a.education_level_id")
                    ->get();


        return view('hr/setup/education_title_edit', compact('degree','levelList','degrees'));
    }

    public function degreeUpdate(Request $request){
        //dd($request);
        ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------# 
        $validator= Validator::make($request->all(),[
            'id'                    => 'required',
            'education_degree_title' => 'required| max:128'
        ]);
        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }
        else{
            
            
           EducationDegree::where('id',$request->id)
                    ->update([
                        'education_level_id' => $request->lvlid,
                        'education_degree_title' =>$request->education_degree_title
                        ]);
            

            return redirect('/hr/setup/education_title')->with('success', 'Successfuly updated Seb-Section');

            }
    }
}

