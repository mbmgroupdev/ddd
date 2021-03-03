<?php

namespace App\Models\Merch;

use App\Models\Merch\Buyer;
use App\Models\Merch\BuyerContact;
use Illuminate\Database\Eloquent\Model;
use DB;

class Buyer extends Model
{
    protected $table= 'mr_buyer';
    public $timestamps= false;


    // All Buyer list
    public static function getBuyerList()
    {
    	return Buyer::pluck("b_name", "b_id");
    }

    public function buyer_contacts()
    {
    	return $this->hasMany(BuyerContact::class, 'b_id', 'b_id');
    }

    public static function checkExistBuyer($data)
    {
    	return DB::table("mr_buyer")
    	->where('b_name', $data['march_buyer_name'])
    	->where('b_address', $data['march_buyer_address'])
    	->where('b_country', $data['country'])
    	->first();
    }
}
