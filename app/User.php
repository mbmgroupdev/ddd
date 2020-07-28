<?php

namespace App;

use App\Models\Employee;
use App\Models\UserActivity;
use App\Models\UserLog;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'associate_id', 'email', 'password','unit_id', 'unit_permissions', 'buyer_permissions','buyer_template_permission','management_restriction'
    ];

    protected $with = ['employee','logs'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $date = [
        'created_at', 'updated_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'associate_id', 'associate_id');
    }

    public function logins()
    {
        return $this->hasMany(UserActivity::class, 'associate_id', 'associate_id')->orderBy('id','DESC');
    }

    public function logs()
    {
        return $this->hasMany(UserLog::class, 'log_as_id', 'associate_id')->orderBy('id','DESC');
    }


    public function lastlogin()
    {
        return UserActivity::where('associate_id',$this->associate_id)->orderBy('id','DESC')->first();
    }
}
