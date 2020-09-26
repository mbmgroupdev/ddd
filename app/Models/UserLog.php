<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserLog extends Model
{

	protected $table = 'user_logs';
    protected $guarded = ['id'];

    protected $dates = [
        'created_at','updated_at'
    ];

}
