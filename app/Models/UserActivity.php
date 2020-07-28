<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserActivity extends Model
{

	protected $table = 'users_login_activities';
    protected $guarded = ['id'];

    protected $dates = [
        'login_at'
    ];

}
