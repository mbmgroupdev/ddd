<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use App\Models\Merch\OrderEntry;

class OrderTNA extends Model
{
    protected $table= 'mr_order_tna';
    public $timestamps= false;

    public function tnaWithOrder(){
    	return $this->belongsTo(OrderEntry::class,'order_id','order_id');
    }
}
