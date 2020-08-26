<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class HrAllGivenBenefits extends Model
{
	protected $table = "hr_all_given_benefits";
	public $timestamps = false;


    public static function storeBenefits($request)
    {

 		$data = new HrAllGivenBenefits();
 		$data->associate_id 			= $request['associate_id'];  	
 		$data->benefit_on   			= $request['benefit_on'];	
 		$data->suspension_days 			= $request['suspension_days']??0;   	
 		$data->earn_leave_amount    	= $request['earn_amount'];
 		$data->service_benefits 		= $request['service_benefits'];	
 		$data->subsistance_allowance 	= $request['subsistence_allowance']??0;	
 		$data->notice_pay   			= $request['notice_pay'];
 		$data->termination_benefits    	= $request['termination_benefits']??0;	
 		$data->death_reason   			= $request['death_reason']??'';	
 		$data->natural_death_benefits  	= $request['natural_death_benefits'];	
 		$data->on_duty_accidental_death_benefits  =  $request['on_duty_accidental_death_benefits'];
 		$data->save();

 		return 'success';
    }
}
