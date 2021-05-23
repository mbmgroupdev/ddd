<?php

namespace App\Http\Controllers\Commercial\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ReportController extends Controller
{
    public function index()
    {

      $form_name= DB::table('cm_form_dimension as a')
                    ->select('a.cm_bank_id','b.bank_name')
                    ->leftJoin('cm_bank as b','b.id','=','a.cm_bank_id')
                    ->distinct()
                    ->get();

      $form_dimension = DB::table('cm_form_dimension as a')
                        ->select(['a.id as aid','a.cm_bank_id','a.form_id','a.form_height',    
                                  'a.form_width','b.bank_name','b.id'
                                ])
                        ->leftJoin('cm_bank as b','b.id','=','a.cm_bank_id')
                        ->get();
                        //dd($form_dimension);
        foreach ($form_dimension as $fdm) {
                if($fdm->form_id == 1){
                $fdm->form_type = "EXP Form";
                }
                else{
                    $fdm->form_type = "L/C Application";
                }
        }
        //dd($form_dimension);
      return view('commercial.report.bankform', compact('form_dimension','form_name'));
    }
    public function CreatePdf(Request $request)
    {

      $mpdf = new \Mpdf\Mpdf();


      // $height = $request->height;
      $width = $request->width;
      $stylesheet = file_get_contents('https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css');
      // $view = view('commercial.report.hsbc_form', compact('width','height'))->render();
      // $view = '<p>Hello</p>';
      // dd($width);
      // exit;
      $mpdf->WriteHTML($stylesheet, 1);
      $mpdf->WriteHTML($width);
    return  $mpdf->Output('BankForm/my_filename.pdf','F');
      //$mpdf = PDF::loadView('pdf.document', $data);
      //dd($width);exit;
	// $mpdf->SetProtection(['copy', 'print'], '', 'pass');
	// return $mpdf->stream('document.pdf');
    //return $mpdf->Output('my_filename.pdf','F');
      //return "Download complete";
      // return "pdf";
    }
}
