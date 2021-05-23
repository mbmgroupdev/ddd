<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\RmReturn;
use App\Models\Inventory\RmReturnItem;

class RmReturn extends Model
{
    protected $table= 'st_rm_return';
    public $timestamps = false;
    public static function getReturnList()
    {
    	return RmReturn::get();
    }
    
    public function st_rm_return_item()
    {
        return $this->hasMany("App\Models\Inventory\RmReturnItem","st_rm_return_id","id");
    }
    public function getReturnWithItem()
    {
        return $this;
    }

}