<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class WashType extends Model
{
    protected $table= 'mr_wash_type';
    public $timestamps= false;

    public function mr_wash_category()
    {
    	return $this->hasOne('App\Models\Merch\WashCategory', 'id', 'mr_wash_category_id');
    }
}
