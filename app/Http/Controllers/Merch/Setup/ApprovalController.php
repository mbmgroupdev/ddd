<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Approval;
use App\Models\Employee;
use App\Models\Hr\Unit;
Use Validator,DB;

class ApprovalController extends Controller
{

    public function showForm()
    {
        $approval= DB::table('mr_approval_hirarchy AS a')
                    ->select([
                        'a.*',
                        'u.hr_unit_name AS unit_name',
                        DB::raw("CONCAT(a.level_1,' ', a.level_2,' ', a.level_3) AS associateid")
                    ])
                    ->leftJoin('hr_unit AS u', 'u.hr_unit_id', 'a.unit')
                    ->get();
        $approvalID = array_column($approval->toArray(), 'associateid');

        $getApprovalId = collect($approvalID)->map( function($row){
            return explode(' ', $row);
        });

        $arr= array_reduce($getApprovalId->toArray(), 'array_merge', array());
        $getAsId = array_unique($arr);
        $getAsName = Employee::whereIn('associate_id', $getAsId)->pluck('as_name', 'associate_id');

        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');

    	return view('merch.setup.approval',compact('approval', 'unitList', 'getAsName'));
    }
 
    public function approvalStore(Request $request){

        $validator= Validator::make($request->all(),[
            'type'   => 'required|max:45',
            'level1' => 'required|max:45',
            'level2' => 'required|max:45',
            'level3' => 'required|max:45',
            'unit' => 'required|max:11'
        ]);
        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
                toastr()->error($message);
            }
            return back();
        }
        $input = $request->all();

        try {
            $data= new Approval();
            $data->mr_approval_type = $request->type;
            $data->level_1 = $request->level1;
            $data->level_2 = $request->level2;
            $data->level_3 = $request->level3;
            $data->unit = $request->unit;
            $data->created_by = auth()->user()->id;

            if ($data->save()) {
                $this->logFileWrite("Approval Entry Saved", $data->id );
                toastr()->success('Approval Entry Save Successfully');
                return back();
            } else {
                toastr()->error('Something Wrong!, Please try again');
                return back();
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
    }
    
    public function approvalEdit($id){
   
        $approval= Approval::where('id', $id)->first();

        $approval1=DB::table('mr_approval_hirarchy AS a')
                 ->select(
                          'a.*',
                          'b.as_name',
                          'b.associate_id'
                        )
                    ->leftJoin('hr_as_basic_info AS b','b.associate_id', '=', 'a.level_1')
                    ->where('id', $id)     
                    ->first();

        $approval2=DB::table('mr_approval_hirarchy AS a')
                 ->select(
                          'a.*',
                          'b.as_name',
                          'b.associate_id'
                        )
                    ->leftJoin('hr_as_basic_info AS b','b.associate_id', '=', 'a.level_2')
                    ->where('id', $id)     
                    ->first();   

        $approval3=DB::table('mr_approval_hirarchy AS a')
                 ->select(
                          'a.*',
                          'b.as_name',
                          'b.associate_id'
                        )
                    ->leftJoin('hr_as_basic_info AS b','b.associate_id', '=', 'a.level_3')
                    ->where('id', $id)     
                    ->first();  
        $employee = Employee::pluck('as_name','associate_id');
                 
        $unitList= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name', 'hr_unit_id');
         
        return view('merch.setup.approval_edit',compact('approval','approval1','approval2','approval3','employee','unitList'));

    } 
    public function approvalUpdate(Request $request){

        $validator= Validator::make($request->all(),[
            
            'level1' => 'required|max:45',
            'level2' => 'required|max:45',
            'level3' => 'required|max:45',
            'unit' => 'required|max:11'
        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{
            Approval::where('id', $request->approv_id)->update([
               
               'level_1' => $request->level1,
               'level_2' => $request->level2,
               'level_3' => $request->level3,
               'unit' => $request->unit
        ]);

            $this->logFileWrite("Approval Entry Updated", $request->approv_id);

        return back()
            ->with('success', "Updated Successfully!!");
            
         
        }
    }

    public function deleteApprov($id){
        
        Approval::where('id', $id)->delete();
        
        $this->logFileWrite("Approval Entry Deleted",$id );

        return back()
        ->with('success', "Successfully Deleted!!");
    }

     
}
