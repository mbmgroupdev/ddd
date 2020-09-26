<?php

namespace App\Http\Controllers\Hr\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\BuyerTemplate;
use App\Models\Hr\BuyerTemplateDetails;
use App\Models\Hr\BuyerHoliday;
use App\Models\Hr\YearlyHolyDay;


use DB, Validator, DataTables, ACL;

class BuyerModeSetupController extends Controller
{
   # Show Add buyer mode Form
    public function buyermode()
    {
        ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
        $unitList = Unit::unitList();
        // $templateList = BuyerTemplate::get();
        $templateList= DB::table('hr_buyer_template as bt')
                    ->Select(
                        'bt.*',
                        'u.hr_unit_name'
                    )

                  ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'bt.hr_unit_id')
                  ->get();

        return view('hr/setup/buyer_mode_setup', compact('unitList','templateList'));
    }

   # Show holiday List
    public function getHolidayList(Request $request)

    { //dd($request->all());
           $month = $request->month;
           $date = date_parse($month);
           $monthNumber = str_pad($date['month'], 2, '0', STR_PAD_LEFT);


        if (!empty($request->unit_id))
        {
            $data = DB::table('hr_yearly_holiday_planner AS h')
                    ->select(
                        'h.*'
                    )

                     ->where('hr_yhp_unit', $request->unit_id)
                     ->whereYear('hr_yhp_dates_of_holidays', '=', $request->year)
                     ->whereMonth('hr_yhp_dates_of_holidays', '=', $monthNumber )
                    // ->orderBy('h.hr_yhp_dates_of_holidays', 'desc')
                    ->get();  //dd($data);
            $rowsCount=COUNT($data);  //dd($rows);

            if($rowsCount!=0){

                $list = '<table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Comment</th>
                                    <th>Open Status</th>
                                </tr>
                            </thead>
                        <tbody>';



                foreach ($data as  $value)
                {
                    //$statusname="open_status".$value->hr_yhp_id;//dd($statusname);

                    $statusname="open_status[".$value->hr_yhp_id."]";
                    $list .= '<tr>
                                  <td style="padding:0px;">
                                    <input type="hidden" name="hp_id[]" value="'. $value->hr_yhp_id.'" readonly="readonly">
                                    <input type="text"  value="'. $value->hr_yhp_dates_of_holidays.'" readonly="readonly"></td>
                                  <td style="padding:0px;"><input type="text" value="'. $value->hr_yhp_comments.'" readonly="readonly"></td>
                                  <td style="padding:0;" width="40%">
                                    <label class="radio-inline" style="font-size:11px;padding:0 0 0 16px;">
                                      <input type="radio" name="'.$statusname.'" class="open_status" value="0" style="margin-left:-15px" '.($value->hr_yhp_open_status==0?'checked':'').'> Holiday

                                    </label>
                                    <label class="radio-inline" style="font-size:11px;padding:0 0 0 10px;">
                                      <input type="radio" name="'.$statusname.'" class="open_status" value="1"'.($value->hr_yhp_open_status==1?'checked':'').'> General

                                    </label>
                                    <label class="radio-inline" style="font-size:11px;">
                                      <input type="radio" name="'.$statusname.'" class="open_status" value="2" '.($value->hr_yhp_open_status==2?'checked':'').'> OT

                                    </label>

                                  </td>
                             </tr>';
                }

                $list .= '</tbody></table>';
            }

            else{ $list="No Holiday found."; } //dd($list);

       }
        return $list;

    }

    # Store Buyer mode
    public function buyerModeStore(Request $request)
    {
        ACL::check(["permission" => "hr_training_add"]);
        #-----------------------------------------------------------#

        $validator = Validator::make($request->all(), [
            'as_unit_id'     => 'required|max:11',
            'template_name' => 'required|max:128'
        ]);


        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            //-----------Store Data---------------------

            // Store in Buyer Template
            $store = new BuyerTemplate;
            $store->template_name = $request->template_name;
            $store->hr_unit_id = $request->as_unit_id;
            $store->created_at = NOW();
            $store->save();
            $last_id = $store->id;

            // Date Formate
            $month = $request->month;
            $date = date_parse($month);
            $monthNumber = str_pad($date['month'], 2, '0', STR_PAD_LEFT);
            $month_year=$request->year.'-'.$monthNumber.'-01';

            // Store in Buyer Template details

            $storeDetails = new BuyerTemplateDetails;
            $storeDetails->buyer_template_id = $last_id;
            $storeDetails->month_year = $month_year;
            $storeDetails->ot_hour = $request->ot_hour;
            $storeDetails->in_time_start_range = $request->intime_slot_1;
            $storeDetails->in_time_end_range = $request->intime_slot_2;
            $storeDetails->out_time_start_range = $request->outtime_slot_1;
            $storeDetails->out_time_end_range = $request->outtime_slot_2;
            $storeDetails->created_at = NOW();


            //Update  Holiday Planner

            // dd($request->open_status[59]);

            if(!empty($request->hp_id)){

                // foreach ($request->hp_id as $value) {$request->open_status
                //     $hd_exists= YearlyHolyDay::where('hr_yhp_id', $value)->first();

                    foreach ($request->hp_id as $value) {
                           $hd_exists= YearlyHolyDay::where('hr_yhp_id', $value)->first();
                           $id = $hd_exists->hr_yhp_id;

                        if(!empty($hd_exists))  {
                            if(isset($request->open_status[$hd_exists->hr_yhp_id])){
                                if($hd_exists->hr_yhp_open_status==$request->open_status[$id] ) {

                                }
                                else{

                                    $prev_status=$hd_exists->hr_buyer_mode_open_status;
                                    if(!empty($prev_status)){
                                        $new_status=$prev_status.','.$last_id.'-'.$request->open_status[$id];
                                    }
                                    else{
                                        $new_status=$last_id.'-'.$request->open_status[$id];
                                    }
                                    YearlyHolyDay::where('hr_yhp_id', $value)->update([
                                        'hr_buyer_mode_open_status' => $new_status
                                    ]);
                                }
                            }
                        }

                     }

                // }
            }

              // dd($aa);


            if ($storeDetails->save())
            {
                $this->logFileWrite("Buyer Mode Entry Saved", $store->id);
                return back()
                    ->withInput()
                    ->with('success', 'Save Successful.');
            }
            else
            {
                return back()
                    ->withInput()->with('error', 'Please try again.');
            }
        }
    }

    # Show Add buyer mode Update  Form
    public function editBuyerModeTemplate($id)
    {
        ACL::check(["permission" => "hr_setup"]);
        #-----------------------------------------------------------#
        //$template = BuyerTemplate::where('id',$id)->first();
        $unitList = Unit::unitList();

        $template= DB::table('hr_buyer_template as bt')
                    ->Select(
                        'bt.*',
                        'bt.id as temp_id',
                        'u.hr_unit_name',
                        'u.hr_unit_id',
                        'd.*'
                    )

                  ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'bt.hr_unit_id')
                  ->leftJoin('hr_buyer_template_detail AS d', 'd.buyer_template_id', '=', 'bt.id')
                  ->where('bt.id',$id)
                  ->first();
        $tem_date=$template->month_year;
        $month = date("m",strtotime($tem_date));
        $year = date("Y",strtotime($tem_date));

        $holiday= DB::table('hr_yearly_holiday_planner as h')
                   ->Select(
                        'h.hr_yhp_id',
                        'h.hr_yhp_dates_of_holidays',
                        'h.hr_yhp_comments',
                        'h.hr_yhp_open_status',
                        'h.hr_buyer_mode_open_status'
                    )
                  ->where('h.hr_yhp_unit',$template->hr_unit_id)
                  ->whereYear('hr_yhp_dates_of_holidays', '=', $year)
                  ->whereMonth('hr_yhp_dates_of_holidays', '=', $month )
                  ->get();  //dd($holiday);

        // foreach($holiday as  $value) {

        //     if(!empty($value->hr_buyer_mode_open_status)){
        //       $h_day[]=$value->hr_buyer_mode_open_status;
        //     }
        //     else{
        //          $h_day[]='';

        //     }
        // }

                    //dd($h_day);
        return view('hr/setup/buyer_mode_setup_edit', compact('unitList','template','holiday'));
    }

    # Update  Buyer mode
    public function editActionBuyerModeTemplate(Request $request)
    {   //dd( $request->all());
        ACL::check(["permission" => "hr_training_add"]);
        #-----------------------------------------------------------#

        $validator = Validator::make($request->all(), [
            'as_unit_id'     => 'required|max:11',
            'template_name' => 'required|max:128'
        ]);


        if ($validator->fails())
        {
            return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please fillup all required fields!');
        }
        else
        {
            //-----------Update Data---------------------

            // Update Buyer Template

            $temp=BuyerTemplate::where('id', $request->templateId)->update([
                'template_name'   => $request->template_name,
                'hr_unit_id'      => $request->as_unit_id,
                'updated_at'      =>  NOW()

            ]);

            // Update Formate
            $month = $request->month;
            $date = date_parse($month);
            $monthNumber = str_pad($date['month'], 2, '0', STR_PAD_LEFT);
            $month_year=$request->year.'-'.$monthNumber.'-01';

            // Update Buyer Template details


            $tempDetails=BuyerTemplateDetails::where('id', $request->templateId)->update([

            'month_year' => $month_year,
            'ot_hour' => $request->ot_hour,
            'in_time_start_range' => $request->intime_slot_1,
            'in_time_end_range' => $request->intime_slot_2,
            'out_time_start_range' => $request->outtime_slot_1,
            'out_time_end_range' => $request->outtime_slot_2,
            'updated_at' => NOW()

            ]);



            //Update  Holiday Planner

            // dd($request->open_status[59]);

            if(!empty($request->hp_id)){


                    foreach ($request->hp_id as $value) {
                       $hd_exists= YearlyHolyDay::where('hr_yhp_id', $value)->first();
                       $id = $hd_exists->hr_yhp_id;
                      // $prev_status=$hd_exists->hr_buyer_mode_open_status; dd($prev_status);



                        if(!empty($hd_exists))  {
                            if(isset($request->open_status[$hd_exists->hr_yhp_id])){
                                if(!empty($hd_exists->hr_buyer_mode_open_status)){


                                    $statusValue1 = [];
                                    $OldstatusValue=$hd_exists->hr_buyer_mode_open_status;
                                    // dd($OldstatusValue);


                                    if(strpos($OldstatusValue, $request->templateId.'-') !== false){
                                        $statusSplit= explode(',',$OldstatusValue); //dd($statusSplit);

                                        // foreach($statusSplit as $key) {
                                        foreach ($statusSplit as $key => $value2) {

                                            $template_id=explode('-',$value2); //dd($template_id);

                                            foreach ($template_id as $key3 => $tmpId) {
                                               // $ddf[]=$value3;
                                               if($tmpId==$request->templateId){
                                               // $statusValue1[$hd_exists->hr_yhp_id]=$hd_exists->hr_yhp_open_status;

                                                $new_status_value=$tmpId.'-'.$request->open_status[$value];

                                                $position=$key;         //dd($new_status_value);

                                               $newValue=str_replace($value2,$new_status_value,$OldstatusValue);

                                               YearlyHolyDay::where('hr_yhp_id', $value)->update([
                                                    'hr_buyer_mode_open_status' => $newValue
                                               ]);


                                               }
                                            }

                                        }
                                    }
                                    else {


                                        $newValue=$OldstatusValue.','.$request->templateId.'-'.$request->open_status[$value]; //dd($newValue);


                                               YearlyHolyDay::where('hr_yhp_id', $value)->update([
                                                    'hr_buyer_mode_open_status' => $newValue
                                               ]);


                                    }

                                }

                            }
                        }

                        // else{   //tttt
                        //     $newValue=$OldstatusValue.','.$request->templateId.'-'.$request->open_status[$value]; //dd($newValue);


                        //                        YearlyHolyDay::where('hr_yhp_id', $value)->update([
                        //                             'hr_buyer_mode_open_status' => $newValue
                        //                        ]);

                        // }

                     }


                        $this->logFileWrite("Buyer Mode Entry Updated", $request->templateId);
                        return back()
                            ->withInput()
                            ->with('success', 'Update Successful.');


            }

              // dd($aa);


            // if ($temp|| $tempDetails)
            // {
            //     $this->logFileWrite("Buyer Mode Entry Updated", $request->templateId);
            //     return back()
            //         ->withInput()
            //         ->with('success', 'Save Successful.');
            // }
            // else
            // {
            //     return back()
            //         ->withInput()->with('error', 'Please try again.');
            // }
        }
    }

}
