<?php

namespace App\Http\Controllers\Hr\ServiceBook;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\GrievanceIssue;
use App\Models\Hr\GrievanceAppeal;
use App\Models\Hr\ServiceBook;
use Auth, DB, Validator, Image, DataTables, ACL;


class ServiceBookController extends Controller
{
	public function showForm()
    {

    	$issueList = GrievanceIssue::where('hr_griv_issue_status', '1')
    				->pluck('hr_griv_issue_name', 'hr_griv_issue_id');
        $sbooklist=ServiceBook::get();

    	return view('hr/ess/service_book', compact('issueList','sbooklist'));
    }

    public function servicebookPage(Request $request)
    {

        $sbook=ServiceBook::where('hr_associate_id', $request->associate_id)->first();

        return view('hr.recruitment.recruit.service_book_page', compact('sbook'))->render();
    }
    public function servicebookStore(Request $request)
    {
        //ACL::check(["permission" => "hr_ess_grievance_appeal"]);
        #-----------------------------------------------------------#
      $validator= Validator::make($request->all(),[
            'associate_id'       =>'required'

        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!!");
        }
        else{
            if ($request->store){
                // Insert query

            $page1 = null;
            if($request->hasFile('page1')){
                $file = $request->file('page1');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page1 = $dir.$filename;
            }
            $page2 = null;
            if($request->hasFile('page2')){
                $file = $request->file('page2');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page2 = $dir.$filename;
            }
            $page3 = null;
            if($request->hasFile('page3')){
                $file = $request->file('page3');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page3 = $dir.$filename;
            }
           $page4 = null;
            if($request->hasFile('page4')){
                $file = $request->file('page4');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page4 = $dir.$filename;
            }
            $page5 = null;
            if($request->hasFile('page5')){
                $file = $request->file('page5');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page5 = $dir.$filename;
            }

            $page6 = null;
            if($request->hasFile('page6')){
                $file = $request->file('page6');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page6 = $dir.$filename;
            }

            $page7 = null;
            if($request->hasFile('page7')){
                $file = $request->file('page7');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page7 = $dir.$filename;
            }

            ///File Url Store //////////

                  ServiceBook::insert([
                        'hr_associate_id' => $request->associate_id,
                        'page1_url'       => $page1,
                        'page2_url'       => $page2,
                        'page3_url'       => $page3,
                        'page4_url'       => $page4,
                        'page5_url'       => $page5,
                        'page6_url'       => $page6,
                        'page7_url'       => $page7,
                        'created_by'      => auth()->user()->associate_id
                    ]);

                  $id = DB::getPdo()->lastInsertId();
                  $this->logFileWrite("Service Book Entry Saved", $id);

            return back()
            ->with('success', "Page File Saved Successfully!!");
        }
        else { // Update query
          $sbook=ServiceBook::where('hr_associate_id',$request->serviceid)->first();

        //File upload///
              $page1 = $sbook->page1_url;
            if($request->hasFile('page1')){
                $file = $request->file('page1');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page1 = $dir.$filename;
            }
           $page2 = $sbook->page2_url;
            if($request->hasFile('page2')){
                $file = $request->file('page2');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page2 = $dir.$filename;
            }
            $page3 = $sbook->page3_url;
            if($request->hasFile('page3')){
                $file = $request->file('page3');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page3 = $dir.$filename;
            }
           $page4 = $sbook->page4_url;
            if($request->hasFile('page4')){
                $file = $request->file('page4');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page4 = $dir.$filename;
            }
            $page5 = $sbook->page5_url;
            if($request->hasFile('page5')){
                $file = $request->file('page5');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page5 = $dir.$filename;
            }

            $page6 = $sbook->page6_url;
            if($request->hasFile('page6')){
                $file = $request->file('page6');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page6 = $dir.$filename;
            }

            $page7 = $sbook->page7_url;
            if($request->hasFile('page7')){
                $file = $request->file('page7');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page7 = $dir.$filename;
            }

           ///File Update  //////////
              $sbookup = ServiceBook::where('hr_associate_id',$request->serviceid)->update([
                        'hr_associate_id' => $request->associate_id,
                        'page1_url'       => $page1,
                        'page2_url'       => $page2,
                        'page3_url'       => $page3,
                        'page4_url'       => $page4,
                        'page5_url'       => $page5,
                        'page6_url'       => $page6,
                        'page7_url'       => $page7,
                        'updated_by'      => auth()->user()->associate_id
                    ]);

              //log with associative id
              $this->logFileWrite("Service Book Entry Updated", $request->serviceid);

              // log with base table primary key
              // $id = ServiceBook::where('hr_associate_id',$request->serviceid)->value('hr_s_book_id');
              // $this->logFileWrite("Service Book Entry Updated", $id);

        return back()
        ->with('success', "Service Book Successfully Updated!!");
    }
     }
    }

    public function servicebookEdit($id)
    {
        //ACL::check(["permission" => "hr_ess_grievance_appeal"]);
        #-----------------------------------------------------------#

        $issueList = GrievanceIssue::where('hr_griv_issue_status', '1')
                    ->pluck('hr_griv_issue_name', 'hr_griv_issue_id');
        $sbook=ServiceBook::where('hr_s_book_id', $id)->first();

       // dd($sbook);

        return view('hr/ess/service_book_edit', compact('issueList','sbook'));
    }

      public function servicebookUpdate(Request $request){
        //dd($request->all());
        //ACL::check(["permission" => "hr_ess_grievance_appeal"]);
        #-----------------------------------------------------------#

       $validator= Validator::make($request->all(),[
            'associate_id'       =>'required',
            'page1'              =>'required|mimes:docx,doc,pdf,jpg,png,jpeg|max:512'


        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{



            $sbook=ServiceBook::where('hr_s_book_id',$request->serviceid)->first();

        //File upload///
              $page1 = $sbook->page1_url;
            if($request->hasFile('page1')){

                // previous file delete
                   // $url1 = asset($page1);

                   //    if (file::exists($url1)) { // unlink or remove previous image from folder
                   //     unlink($url1);
                   //   }

                $file = $request->file('page1');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page1 = $dir.$filename;
            }
           $page2 = $sbook->page2_url;
            if($request->hasFile('page2')){
                $file = $request->file('page2');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page2 = $dir.$filename;
            }
            $page3 = $sbook->page3_url;
            if($request->hasFile('page3')){
                $file = $request->file('page3');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page3 = $dir.$filename;
            }
           $page4 = $sbook->page4_url;
            if($request->hasFile('page4')){
                $file = $request->file('page4');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page4 = $dir.$filename;
            }
            $page5 = $sbook->page5_url;
            if($request->hasFile('page5')){
                $file = $request->file('page5');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page5 = $dir.$filename;
            }

            $page6 = $sbook->page6_url;
            if($request->hasFile('page6')){
                $file = $request->file('page6');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page6 = $dir.$filename;
            }

            $page7 = $sbook->page7_url;
            if($request->hasFile('page7')){
                $file = $request->file('page7');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $dir  = '/assets/files/servicebook/';
                $file->move( public_path($dir) , $filename );
                $page7 = $dir.$filename;
            }

           ///File Update  //////////
              $sbookup = ServiceBook::where('hr_s_book_id',$request->serviceid)->update([
                        'hr_associate_id' => $request->associate_id,
                        'page1_url'       => $page1,
                        'page2_url'       => $page2,
                        'page3_url'       => $page3,
                        'page4_url'       => $page4,
                        'page5_url'       => $page5,
                        'page6_url'       => $page6,
                        'page7_url'       => $page7,
                        'updated_by'      => auth()->user()->associate_id
                    ]);

              //log with associative id
              $this->logFileWrite("Service Book Entry Updated", $request->serviceid);

              // log with base table primary key
              // $id = ServiceBook::where('hr_associate_id',$request->serviceid)->value('hr_s_book_id');
              // $this->logFileWrite("Service Book Entry Updated", $id);

        return back()
        ->with('success', "Service Book Successfully Updated!!");
     }

    //return redirect('merch/setup/infoBrand');
  }


}
