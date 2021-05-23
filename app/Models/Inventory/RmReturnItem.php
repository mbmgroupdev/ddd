<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\RmReturn;
use App\Models\Inventory\RmReturnItem;

class RmReturnItem extends Model
{
    protected $table= 'st_rm_return_item';
    public $timestamps = false;

    public static function getReturnWithItem()
    {
        return $this->belongsTo(RmReturn::class);
    }

    public function st_rm_return()
    {
        return $this->belongsTo("App\Models\Inventory\RmReturn");
    }



}