<?php

namespace App\Http\Controllers\Commercial\Bank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Commercial\Bank\CmFile;
use App\Models\Commercial\Bank\BankAcceptanceImport;
use App\Models\Commercial\Bank\ImportBillSettle;
use App\Models\Commercial\Bank\AccountType;


use Validator;

use DB;
use Response;

class ImportBillSettlementController extends Controller{

	public function selectdata(){

		//$imp_data_entry=ImportDataEntry::get();
		//return view('Commercial.Bank.import_bill_settlement');

		$fileno=DB::table('cm_file as c')->select('c.file_no', 'd.value','c.id','d.transp_doc_no1')
    					->join('cm_imp_data_entry as d', 'c.id', '=', 'd.cm_file_id')
    					->get();
        // dd($fileno);
		$accountType = DB::table('cm_acc_type')->get();

    	return view('commercial.bank.import_bill_settlement', compact('fileno','accountType'));

	}


	public function showdata(Request $request){

		//dd($request->all());

        $fileno=$request->file_no;
        $value=$request->value;
        $doc=$request->tdoc;
		$join_result=DB::table('cm_imp_data_entry as c')
		                ->select(
		                	'c.transp_doc_no1',
		                	'c.value',
		                	'e.id as bnk_acceptance_imp_id',		                	
                            'e.discre_acp_date',                            
                            // 'ff.due_date',                          

		                	'd.lc_no',
		                	'c.id',
                            'f.file_no'
		                	)
						->leftJoin('cm_file as f', 'f.id', '=', 'c.cm_file_id')
    					->leftJoin('cm_btb as d', 'c.cm_btb_id', '=', 'd.id')
    					->leftJoin('cm_bank_acceptance_imp as e','c.id', '=','e.cm_imp_data_entry_id' )
                        // ->leftJoin('cm_bank_acceptance_imp_duedate as ff','e.id', '=','ff.cm_bank_acceptance_imp_id' )
    					/*->where('f.id','=',$request->file_no)
    					->orWhere('c.id','=',$request->value)
    					->orWhere('c.id','=',$request->tdoc)*/
                        ->where(function($condition) use ($fileno,$value,$doc){
                            if(!empty($fileno)){
                                $condition->where('c.cm_file_id',$fileno);
                            }
                            if(!empty($value)){
                                $condition->where( 'c.value' , $value);
                            }
                            if(!empty($doc)){
                                $condition->where('c.transp_doc_no1', $doc);
                            }

                        })
    					->get();

    					// dd($join_result);
      // $days = 200;
      // $date= BankAcceptanceImport::whereRaw('DATEDIFF(discre_acp_date,due_date) < ?')
           // ->setBindings([$days])
            //->get();

        foreach ($join_result as $jr) {
            $jr->due_dates = DB::table('cm_bank_acceptance_imp_duedate')
                                    ->select('due_date')
                                    ->where('cm_bank_acceptance_imp_id', $jr->bnk_acceptance_imp_id)
                                    ->get();
        }

        // dd($join_result[0]->due_dates[0]->due_date);exit;


    	$data = "";
    	for($i=0; $i<sizeof($join_result); $i++){
        //foreach ($join_result as $j_result) {




            $pre_data = DB::table('cm_imp_bill_settle as d')
                                        ->select('d.cm_imp_data_entry_id',
                                                'd.payment_date',
                                                'd.days_interest',
                                                'd.interest_rate',
                                                'd.amount',
                                                'd.cm_acc_type_id'
                                                )
                                        ->where('d.cm_imp_data_entry_id',$join_result[$i]->id);

                  $pre_data_exist=$pre_data->exists();
                  $pre_data_first=$pre_data->first();

            // dd($pre_data_first);
            
            //Differnce calculation for due dates from discre acceptance date
            $diff[] = "";      
            $acp_date = strtotime($join_result[$i]->discre_acp_date);      
            for ($j=0; $j <sizeof($join_result[$i]->due_dates) ; $j++) { 
                    $diff[$j]=(strtotime($join_result[$i]->due_dates[$j]->due_date))-$acp_date;
                    
                    if($diff[$j]<0) $diff[$j] = $diff[$j]*(-1);

                    $diff[$j]= (($diff[$j]/60)/60)/24;                      
                  }      
            // dd($diff);

			// $diff=(strtotime($join_result[$i]->due_date))-strtotime(($join_result[$i]->discre_acp_date));
			// if($diff<0) $diff= $diff*(-1);
			// $diff= (($diff/60)/60)/24;

            if($pre_data_exist){


    	  	   $data .= '<tr>  <td>'.$join_result[$i]->file_no.'</td>
                        <td>'.$join_result[$i]->transp_doc_no1.'</td>
                        <td>'.$join_result[$i]->lc_no.'</td>
                        <td>'.$join_result[$i]->value.'</td>
                        <td>'.$join_result[$i]->discre_acp_date.'</td>
                        <td>';
                        foreach ($join_result[$i]->due_dates as $ddd) { 
                           $data .= $ddd->due_date.'<br>';
                        }
               $data .= '</td>
                        <td>';
                        for($nm=0; $nm<sizeof($diff); $nm++ ) {
                            $data .= $diff[$nm].'<br>';
                        }
               $data .= '</td>
                        <td> <input type="radio" name="check[]" id="check" value="'.$join_result[$i]->id.'" class="ck" />
                        <input class="settle_id" type="hidden" value="'.$pre_data_first->cm_imp_data_entry_id.'"/>
                        <input class="payment_date" type="hidden" value="'.$pre_data_first->payment_date.'"/>
                        <input class="days_interest" type="hidden" value="'.$pre_data_first->days_interest.'"/>
                        <input class="interest_rate" type="hidden" value="'.$pre_data_first->interest_rate.'"/>
                        <input class="amount" type="hidden" value="'.$pre_data_first->amount.'"/>
                        <input class="accounttype" type="hidden" value="'.$pre_data_first->cm_acc_type_id.'"/>
                        <input class="status" type="hidden" value="1"/>
                        </td>
                        </tr>';
                    }

                else{

                    $data .= '<tr>  <td>'.$join_result[$i]->file_no.'</td>
                            <td>'.$join_result[$i]->transp_doc_no1.'</td>
                            <td>'.$join_result[$i]->lc_no.'</td>
                            <td>'.$join_result[$i]->value.'</td>
                            <td>'.$join_result[$i]->discre_acp_date.'</td>
                            <td>';
                            foreach ($join_result[$i]->due_dates as $ddd) { 
                               $data .= $ddd->due_date.'<br>';
                            }
                   $data .= '</td>
                            <td>';
                            for($nm=0; $nm<sizeof($diff); $nm++ ) {
                                $data .= $diff[$nm].'<br>';
                            }
                   $data .= '</td>
                            <td> <input type="radio" name="check[]" id="check" value="'.$join_result[$i]->id.'" class="ck" />
                            <input class="settle_id" type="hidden" value="0"/>
                            <input class="payment_date" type="hidden" value="0"/>
                            <input class="days_interest" type="hidden" value="0"/>
                            <input class="interest_rate" type="hidden" value="0"/>
                            <input class="amount" type="hidden" value="0"/>
                            <input class="accounttype" type="hidden" value="0"/>
                            <input class="status" type="hidden" value="0"/>
                            </td>
                            </tr>';

                }
            }
                 //dd($data);


    	return Response::json($data);
    }




	public function enterdata(Request $request){
        // dd($request->all());

				$acc_id=AccountType::where('acc_type_name','=',$request->acc_radio)
						  		->first();
//dd($acc_id->id);exit;
                $imp_bill_settle_id=DB::table('cm_imp_bill_settle as a')
                                    ->select('a.cm_imp_data_entry_id')
                                    ->where('a.cm_imp_data_entry_id','=',$request->import_id)
                                    ->get();
//dd($import_id);


				$validator = Validator::make($request->all(),[
    				'doi'   => 'required',
					 	'ir'	 => 'required',
						'amount'	 =>	'required',
						'acc_type_id' =>'required'

    			]);
    			 if($validator->fails()){
            		return back()
            			->withInput()
            			->with('error', "Please fill all required Input!!");
         		}

                elseif(count($imp_bill_settle_id)>0){

                ImportBillSettle::where('cm_imp_data_entry_id',$request->import_id)->update([
                'payment_date' => $request->paydate,
                'days_interest'=> $request->doi,
                'interest_rate' => $request->ir,
                'amount'=> $request->amount,
                'cm_acc_type_id'=>$request->acc_type_id

                ]);
                //log entry
                $id = ImportBillSettle::where('cm_imp_data_entry_id',$request->import_id)->value('id');
                $this->logFileWrite("Commercial->Bank Import Bill Settlement Updated ", $id);

                $this->saveToFundTranferEntry($request->file_id, $request->acc_type_id, $request->amount);
                return back()->with('success', 'Entry Updated');

                }

                else{


         			$data = new ImportBillSettle();
         			$data->cm_imp_data_entry_id  = $request->import_id;
         			$data->payment_date = $request->paydate;
         			$data->days_interest= $request->doi;
         			$data->interest_rate= $request->ir;
         			$data->amount= $request->amount;
         			$data->cm_acc_type_id=$request->acc_type_id;


         			$data->save();


         			//dd('saved');
                    $this->logFileWrite("Commercial->Bank Import Bill Settlement Saved ", $data->id);

                    $this->saveToFundTranferEntry($request->file_id, $request->acc_type_id, $request->amount);
         			return back()->with('success', 'Entry Saved');
         		}

					//return view('commercial.enterdata');


			}
    //saving or updating the fund transfer table data.. 
    public function saveToFundTranferEntry($file_id, $acc_type_id, $settalement_amount){
            //Update

            $exist_id = DB::table('cm_file_transfer')
                                ->where(['cm_file_id'=>$file_id, 'cm_acc_type_id'=>$acc_type_id, 'narration'=>'Settlement'])
                                ->value('id');
            if(!is_null($exist_id)){
                // if($settalement_amount == 0){
                //     DB::table('cm_file_transfer')->where('id', $exist_id)->delete();
                // }else{
                    DB::table('cm_file_transfer')
                        ->where(['cm_file_id'=>$file_id, 'cm_acc_type_id'=>$acc_type_id, 'narration'=>'Settlement'])
                        ->update([
                                    'date'      => date('Y-m-d'),
                                    'amount'    => $settalement_amount*(-1)     
                                ]); 
                // }
            }
            else{
            //Insert
                // if($settalement_amount != 0 ){
                    DB::table('cm_file_transfer')->insert([
                        'cm_file_id'    => $file_id, 
                        'cm_acc_type_id'=> $acc_type_id,
                        'date'          => date('Y-m-d'),
                        'amount'        => $settalement_amount*(-1), 
                        'currency'      => 'USD', 
                        'narration'     => 'Settlement'
                    ]);
                // }
            }                                
    }


    public function dataList(){
    	$imp_bill_settle_data = DB::table('cm_imp_bill_settle')->leftJoin('cm_acc_type','cm_acc_type.id','cm_imp_bill_settle.cm_acc_type_id')->orderBy('cm_imp_bill_settle.id', 'DESC')->get();
    	      // //dd($bank_acceptance_imp_data);

    	      // foreach ($imp_bill_settle_data as $ibs) {
    	      //  	    $tmp = ImportBillSettle::where('id', $ibs->cm_imp_data_entry_id)->select('transp_doc_no1','transp_doc_date' ,'value')->get();
    	      //  		$add->bill_entry = $tmp;
    	      //   }

    	     //dd($imp_bill_settle_data);
		return view( 'commercial.bank.import_bill_settlement_view', compact('imp_bill_settle_data')); //compact('bank_acceptance_imp_data') );
    		}




}
