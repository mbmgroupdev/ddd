<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class WashCategory extends Model
{
    protected $table= 'mr_wash_category';
    public $timestamps= false;

    public function mr_wash_type()
    {
    	return $this->hasMany('App\Models\Merch\WashType','mr_wash_category_id','id');
    }
}
