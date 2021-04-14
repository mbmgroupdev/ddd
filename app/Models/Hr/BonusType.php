<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class BonusType extends Model
{
    protected $table= "hr_bonus_type";
    protected $fillable = ['bonus_type_name'];

   //  public static function storeData($data){
   //  	// dd($data);
   //  	$data['month'] = date('m', strtotime($data['month']));
   //  	// dd($data['month']);
 		// $ob =  new BonusType();
 		// $ob->bonus_type_name  	= $data['bonus_type_name'];   	
 		// $ob->month 				= $data['month'];   	
 		// $ob->year 				= $data['year'];   	
 		// $ob->amount 			= $data['bonus_amount'];   	
 		// $ob->percent_of_basic 	= $data['bonus_percent'];
 		// $ob->save();
 		
 		// return $ob->id;
   //  }

   //  public static function updateData($edit_data){
   //  	// dd($edit_data);
   //  	$edit_data['edit_month'] = date('m', strtotime($edit_data['edit_month']));
    	
   //  	BonusType::where('id', $edit_data['edit_id'])->update([
   //  		'bonus_type_name'   => $edit_data['edit_bonus_type_name'],
			// 'month'				=> $edit_data['edit_month'],
			// 'year'				=> $edit_data['edit_year'],
			// 'amount'			=> $edit_data['edit_bonus_amount'],
			// 'percent_of_basic'  => $edit_data['edit_bonus_percent']
   //  	]);	

   //  }
}


