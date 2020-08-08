<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
	protected $table = 'mr_buyer';
	protected $primaryKey = 'b_id';
    protected $guarded = [];

    /*protected $dates = [
        'created_at', 'updated_at'
    ];*/
}
