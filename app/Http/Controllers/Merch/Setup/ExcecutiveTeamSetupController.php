<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\MrExcecutiveTeam;
use App\Models\Merch\MrExcecutiveTeamMembers;
use DB,Exception,Validator;

class ExcecutiveTeamSetupController extends Controller
{
    public function index(){
    	 $units = Unit::pluck('hr_unit_name', 'hr_unit_id');
    	 // dd($units);
       if(auth()->user()->hasRole('merchandiser')){
         $lead_asid = DB::table('hr_as_basic_info as b')
            ->where('associate_id',auth()->user()->associate_id)
            ->pluck('as_id');
            $team_member = $lead_asid;
        //dd($team);exit;
       }elseif (auth()->user()->hasRole('merchandising_executive')) {
         $executive_member = DB::table('hr_as_basic_info as b')
            ->where('associate_id',auth()->user()->associate_id)
            ->get();

       }else{
        $team_member ='';
       }
       if(!empty($team_member)){
         if(!empty($executive_member)){
           $exce_team = DB::table('mr_excecutive_team as b')
        	 						->select([
        	 							'b.*',
        	 							'd.hr_unit_name',
        	 							'e.as_name as team_leader'
        	 							])
        	 						->leftJoin('hr_unit as d', 'd.hr_unit_id', 'b.unit_id')
        	 						->leftJoin('hr_as_basic_info as e', 'e.as_id', 'b.team_lead_id')
                      ->where('b.id',$executive_member->mr_excecutive_team_id)
        	 						->get();
         }else{
           $exce_team = DB::table('mr_excecutive_team as b')
        	 						->select([
        	 							'b.*',
        	 							'd.hr_unit_name',
        	 							'e.as_name as team_leader'
        	 							])
        	 						->leftJoin('hr_unit as d', 'd.hr_unit_id', 'b.unit_id')
        	 						->leftJoin('hr_as_basic_info as e', 'e.as_id', 'b.team_lead_id')
                      ->where('b.team_lead_id',$team_member)
        	 						->get();
         }
       }else{
         $exce_team = DB::table('mr_excecutive_team as b')
                    ->select([
                      'b.*',
                      'd.hr_unit_name',
                      'e.as_name as team_leader'
                      ])
                    ->leftJoin('hr_unit as d', 'd.hr_unit_id', 'b.unit_id')
                    ->leftJoin('hr_as_basic_info as e', 'e.as_id', 'b.team_lead_id')
                    ->get();
       }


    	 foreach ($exce_team as $team) {
    	 	$members = DB::table('mr_excecutive_team_members as tm')->where('tm.mr_excecutive_team_id', $team->id)
    	 						->select([
    	 							'tm.*',
    	 							'f.as_name as team_member'
    	 						])
    	 						->leftJoin('hr_as_basic_info as f','f.as_id', 'tm.member_id')
    	 						->get();
    	 	$team->members = $members;
    	 }
    	 //dd($exce_team);

    	 return view('merch.setup.excecutive_team_setup', compact('units','exce_team'));
    }

    # Search Unit wise Management Associate ID returns NAME & ID
    public function memberList(Request $request)
    {
        // dd($request->unit_id);
        $leader = DB::table('mr_excecutive_team')->pluck('team_lead_id')->toArray();
        $members= DB::table('mr_excecutive_team_members')->pluck('member_id')->toArray();
        $already_membered =  array_merge($leader, $members);
        $list = "";
        $data_list = DB::table("hr_as_basic_info as a")
                ->join('users as b', 'b.associate_id', 'a.associate_id')
                ->where("a.as_unit_id",$request->unit_id)
                ->select('a.as_name','a.as_id','a.associate_id')
                ->get();
            foreach ($data_list as $k => $data) {
                $value = $data->as_name.' ('.$data->associate_id.')';
                $key = $data->as_id;
                if(in_array($key, $already_membered) == true ){
                    $list .= "<option value=\"$key\">$value (Already member of a team)</option>";
                }
                else{
                    $list .= "<option value=\"$key\">$value</option>";
                }
            }
        return $list;
    }

    // public function memberListEdit(Request $request)
    // {
    //     // dd($request->unit_id);

    //     $leader = DB::table('mr_excecutive_team')->where('id','!=',$request->team_id)->pluck('team_lead_id')->toArray();
    //     $members= DB::table('mr_excecutive_team_members')->where('mr_excecutive_team_id','!=',$request->team_id)->pluck('member_id')->toArray();
    //     // dd($leader, $members );
    //     $already_membered =  array_merge($leader, $members);
    //     // dd($already_membered);
    //     $list = "";
            
    //     $data = DB::table("hr_as_basic_info")              
    //             ->where("as_unit_id",$request->unit_id)   
    //             // ->where("as_emp_type_id",1)
    //             // ->whereNotIn('as_id', $already_membered)            
    //             ->pluck('as_name','as_id');
              
    //     // dd($already_membered, $data);

    //         foreach ($data as $key => $value) 
    //         {
    //             if(in_array($key, $already_membered) == true ){
    //                 $list .= "<option value=\"$key\" disabled >$value (Already member of a team)</option>";
    //             }
    //             else{
    //                 $list .= "<option value=\"$key\">$value</option>";
    //             }
    //         }

    //     return $list;
    // }

    public function StoreExcecutiveTeam(Request $request)
    {
        #-----------------------------------------------------------#
        $validator= Validator::make($request->all(),[
            'unit_id'=>'required|max:11',
            'team_name'=> 'required|max:128|unique:mr_excecutive_team,team_name'

        ]);

        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }
        else
        {
    		// dd($request->all());
        	DB::beginTransaction();
        	try{
	        	$data = new MrExcecutiveTeam();
	        	$data->unit_id 		= $request->unit_id;
	        	$data->team_name 	= $request->team_name;
	        	$data->team_lead_id = $request->team_lead;
	        	$data->save();
	        	$last_id = $data->id;

	        	for($i=0; $i<sizeof($request->members); $i++) {
	        		DB::table('mr_excecutive_team_members')->insert([
	        				'mr_excecutive_team_id' => $last_id,
	        				'member_id'	=>	$request->members[$i]
	        		]);
	        	}
        	}catch(\Exception $e){
        		DB::rollback();
        		return back()->with('error', $e->getMessage());
        	}

        	DB::commit();
        	$this->logFileWrite("Merch>Setup>Excecutive Team Saved",$last_id );
            return redirect('merch/setup/excecutive_team_setup')
                ->withInput()
                ->with('success', 'Save Successful.');
        }
    }
//edit..
    public function editTeam($id){
        	// dd($id);
            $leader = DB::table('mr_excecutive_team')->where('id','!=',$id)->pluck('team_lead_id')->toArray();
            $members= DB::table('mr_excecutive_team_members')->where('mr_excecutive_team_id','!=',$id)->pluck('member_id')->toArray();
            // dd($leader, $members );
            $already_membered =  array_merge($leader, $members);
            // dd($already_membered);
    	    $exce_team = DB::table('mr_excecutive_team as b')->where('b.id', $id)
    	 						->select([
    	 							'b.*',
    	 							'd.hr_unit_name',
    	 							'e.as_name as team_leader'
    	 							])
    	 						->leftJoin('hr_unit as d', 'd.hr_unit_id', 'b.unit_id')
    	 						->leftJoin('hr_as_basic_info as e', 'e.as_id', 'b.team_lead_id')
                                ->first();

    	 	$members_arr = DB::table('mr_excecutive_team_members as tm')
    	 						->where('tm.mr_excecutive_team_id', $exce_team->id)
                                ->whereNotIn('tm.mr_excecutive_team_id', $already_membered)
    	 						->pluck('tm.member_id')->toArray();
    	 						// ->get()->toArray();

    	 	$exce_team->members = $members_arr;
    	 // dd($exce_team);



    	 	$units 	  = Unit::pluck('hr_unit_name', 'hr_unit_id');
    	 	$employee = DB::table("hr_as_basic_info")
		                ->where("as_unit_id", $exce_team->unit_id)
                        ->whereNotIn('as_id', $already_membered)
		                ->pluck('as_name','as_id');

    	return view('merch.setup.excecutive_team_setup_edit', compact('exce_team', 'units', 'employee'));
    }

    //update
    public function updateTeam(Request $request){
    	// dd($request->all());

    	$validator= Validator::make($request->all(),[
            'unit_id'=>'required|max:11',
            'team_name'=> 'required|max:128'

        ]);

        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fillup all required fields!');
        }
        else
        {
    		// dd($request->all());
        	DB::beginTransaction();
        	try{
	        	MrExcecutiveTeam::where('id', $request->team_id)->update([
	        		'unit_id'		=>	$request->unit_id,
					'team_name'		=>	$request->team_name,
					'team_lead_id'	=>	$request->team_lead
	        	]);
	        	MrExcecutiveTeamMembers::where('mr_excecutive_team_id', $request->team_id)->delete();

	        	for($i=0; $i<sizeof($request->members); $i++) {
	        		DB::table('mr_excecutive_team_members')->insert([
	        				'mr_excecutive_team_id' => $request->team_id,
	        				'member_id'	=>	$request->members[$i]
	        		]);
	        	}
        	}catch(\Exception $e){
        		DB::rollback();
        		return back()->with('error', $e->getMessage());
        	}

        	DB::commit();
            $this->logFileWrite("Merch>Setup>Excecutive Team Updated",$request->team_id );
            return redirect('merch/setup/excecutive_team_setup')
                ->withInput()
                ->with('success', 'Update Successful.');
        }
    }

//delete
    public function deleteTeam($id){

    	MrExcecutiveTeam::where('id', $id)->delete();
    	MrExcecutiveTeamMembers::where('mr_excecutive_team_id', $id)->delete();

    	return back()->with('success', "Excecutive Team Deleted");

    }
}
