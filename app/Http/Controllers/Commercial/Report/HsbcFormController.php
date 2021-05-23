<?php

namespace App\Http\Controllers\Commercial\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HsbcFormController extends Controller
{
    public function index(Request $request)
    {
      $height = $request->height;
      $width = $request->width;
      $formtype = $request->formtype;
      $bankname = $request->bankname;
      if($formtype == "Lc Application"){
        switch ($bankname) {
          case 'SCB Bank':
            return view('commercial.report.scb_form',compact('height','width'));
            break;
          case 'HSBC Bank':
            return view('commercial.report.hsbc_form',compact('height','width'));
            break;
          case 'AB Bank':
            return view('commercial.report.ab_form',compact('height','width'));
            break;
        }
      }
      else {
        return view('commercial.report.hsbc_form',compact('height','width'));
      }
    }
}
