<?php

namespace App\Models\Commercial\Bank;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
	protected $table = "cm_acc_type";

    public $timestamps = false;  

    public static function getAllDataAccType(){
    	return AccountType::get();
    }

    public static function storeAccType( $form_data ){
    	// dd($form_data);
    	$data = new AccountType();
    	$data->acc_type_name = $form_data['account_type'];
    	$data->save();

    	return $data->id;
    	// if($data->save() ){
    	// 	return $data->id;
    	// }
    	// else{
    	// 	return -1;
    	// }
    }
    public static function getEditRowData( $row_id ){

    	return AccountType::where('id', $row_id)->first();
    }

    public static function updateAccTypeData( $row_data ){

    	// dd($row_data);

    	$ck = AccountType::where('id', $row_data['acc_type_id'] )->update([
    				'acc_type_name' => $row_data['account_type']
    	]);
    	// dd($ck);
    	return $ck;
    }
    
    public static function deleteAccountType( $row_id ){
    	$ck = AccountType::where('id', $row_id)->delete();
    	return $ck;
    }
}
