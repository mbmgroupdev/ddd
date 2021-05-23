<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Supplier;

class Supplier extends Model
{
    protected $table= 'st_supplier';
    public $timestamps = false;

   // All Points  list
    public static function getSupplier()
    {
    	return Supplier::pluck('sup_name','sup_id');
    }
}
