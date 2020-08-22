<?php

namespace App\Http\Controllers\Hr\Recruitment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\InterviewNote;
use Yajra\Datatables\Datatables;
use Validator, ACL, DB;

class InterviewNoteController extends Controller
{
    public function Interview()
    {
        try {
           // ACL::check(["permission" => "hr_recruitment_job_interview"]);
           #-----------------------------------------------------------#
	       return view('hr/recruitment/interview_notes');
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }

    public function InterviewList()
    {
        try {
            // ACL::check(["permission" => "hr_recruitment_job_interview"]);

            $interviewNotesList= InterviewNote::all();
            return view('hr/recruitment/interview_notes_list',compact('interviewNotesList'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function InterviewDelete($id)
    {
        try {
            // ACL::check(["permission" => "hr_recruitment_job_interview"]);

            $exist = InterviewNote::where('hr_interview_id',$id)->first();
            if($exist != NULL) {
                InterviewNote::where('hr_interview_id',$id)->delete();
                return redirect('hr/recruitment/job_portal/interview_notes_list')->with('success', 'Delete success.');
            } else {
                return redirect('hr/recruitment/job_portal/interview_notes_list')->with('error', 'No data found.');
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function InterviewListData()
    {
        // ACL::check(["permission" => "hr_recruitment_job_interview"]);

        $data= InterviewNote::
                select(
                    'hr_interview_id as serial_no',
                    'hr_interview_date',
                    'hr_interview_name',
                    'hr_interview_contact',
                    'hr_interview_exp_salary',
                    'hr_interview_board_member',
                    'hr_interview_note'
                )
                ->get();
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                    return "<div class=\"btn-group\">
                        <a onclick=\"confirm('Are you sure?')\" href=".url('hr/recruitment/job_portal/interview_notes_delete/'.$data->serial_no)." class=\"btn btn-xs btn-danger\">
                            <i class=\"ace-icon fa fa-times bigger-120\"></i>
                        </a></div>";
            })
            ->rawColumns([
                'serial_no',
                'action'
            ])->toJson();
    }

    public function InterviewNoteStore(Request $request)
    {
        try {
            // ACL::check(["permission" => "hr_recruitment_job_interview"]);
            #-----------------------------------------------------------#

        	$validator= Validator::make($request->all(),[
        		'hr_interview_date'=>'required',
        		'hr_interview_name'=>'required|max:64|min:3',
        		'hr_interview_contact'=>'required|max:11',
        		'hr_interview_exp_salary'=>'required|max:11',
        		'hr_interview_board_member'=>'required|max:255',
        		'hr_interview_note'=>'max:255'
        	]);
        	if($validator->fails())
        	{
        		return back()
        		->withErrors($validator)
        		->withInput()
        		->with('error', 'Please fillup all required fileds!.');
        	}
        	else
            {
        		$interview= new InterviewNote();
        		$interview->hr_interview_date=$request->hr_interview_date;
        		$interview->hr_interview_name=$request->hr_interview_name;
        		$interview->hr_interview_contact=$request->hr_interview_contact;
        		$interview->hr_interview_exp_salary=$request->hr_interview_exp_salary;
        		$interview->hr_interview_board_member=$request->hr_interview_board_member;
        		$interview->hr_interview_note=$request->hr_interview_note;

        		if($interview->save()){
                    $this->logFileWrite("Interview Entry Saved", $interview->hr_interview_id);
        			return back()
        			->with('success', 'Save Successfull.');
        		}
        		else{
        			return back()
        			->withInput()
        			->with('error', 'Please try again.');
        		}
        	}
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
