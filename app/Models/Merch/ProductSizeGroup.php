<?php

namespace App\Models\Merch;

use App\Models\Merch\Buyer;
use Illuminate\Database\Eloquent\Model;

class ProductSizeGroup extends Model
{
	// public $with = ['buyer'];
    protected $table= 'mr_product_size_group';
    public $timestamps= false;

    public function buyer(){
    	return $this->belongsTo(Buyer::class, 'b_id', 'b_id');
    }
}
