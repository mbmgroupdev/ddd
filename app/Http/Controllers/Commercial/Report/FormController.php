<?php

namespace App\Http\Controllers\Commercial\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class FormController extends Controller
{
    public function index(Request $request)
    {
      // $height = $request->height;
      // $width = $request->width;
      $formtype = $request->formtype;
      $bankname = $request->bankname;
      $result = DB::table('cm_form_dimension')->where('form_id',$formtype)->where('cm_bank_id',$bankname)->first();
        $height = isset($result->form_height)?$result->form_height:0;
        $width = isset($result->form_width)?$result->form_width:0;
      if($formtype == "1"){
        
        $bank = DB::table('cm_bank')->where('id',$bankname)->first();
        $bankname = $bank->bank_name;

        
        switch ($bankname) {
          case 'SCB Bank':
            return view('commercial.report.scb_form_exp',compact('height','width'));
            break;
          case 'HSBC Bank':
            return view('commercial.report.hsbc_form_exp',compact('height','width'));
            break;
          case 'AB Bank':
            return view('commercial.report.ab_form_exp',compact('height','width'));
            break;
          default:
            return redirect()->back()->withErrors(['Form does not exist']);

        }
      }

      else if ($formtype == "2") {
        
        $bank = DB::table('cm_bank')->where('id',$bankname)->first();
        $bankname = $bank->bank_name;

        switch ($bankname) {
          case 'AB Bank':
            return view('commercial.report.ab_form_lc',compact('height','width'));
            break;
          case 'HSBC Bank':
            return view('commercial.report.hsbc_form_lc',compact('height','width'));
            break;
          case 'SCB Bank':
            return view('commercial.report.scb_form_lc',compact('height','width'));
            break;
          default:
            return redirect()->back()->withErrors(['Form does not exist']);
      }
    }

      else {
        return view('commercial.report.hsbc_form',compact('height','width'));
      }
    }
}
