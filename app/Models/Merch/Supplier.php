<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class Supplier extends Model
{
    protected $table= 'mr_supplier';
    public $timestamps= false;

    public static function checkExistSupplier($data)
    {
    	return DB::table('mr_supplier')
    	->where('cnt_id', $data['cnt_id'])
    	->where('sup_name', $data['sup_name'])
    	->where('sup_address', $data['sup_address'])
    	->first();
    }

}
