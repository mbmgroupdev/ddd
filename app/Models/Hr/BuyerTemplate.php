<?php

namespace App\Models\Hr;

use App\Models\Hr\BuyerTemplate;

use Illuminate\Database\Eloquent\Model;

class BuyerTemplate extends Model
{
    
    protected $table= 'hr_buyer_template';
    public $timestamps= false;

    public static function getBuyerTemplateList()
    {
    	return BuyerTemplate::pluck('template_name', 'id');
    }

   

}
