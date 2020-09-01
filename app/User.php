<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Access\Gate;
use App\Models\Employee;
use App\Models\UserActivity;
use App\Models\UserLog;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

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
        'created_at', 'updated_at', 'deleted_at'
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
        return UserActivity::where('associate_id',$this->id)->orderBy('id','DESC')->first();
    }

    public function unit_permissions()
    {
        $units = explode(",", $this->unit_permissions);
        return (!empty($units[0])?$units:[]);
    }

    public function buyer_permissions()
    {
        $buyers = explode(",", $this->buyer_permissions);
        return (!empty($buyers[0])?$buyers:[]);
    }

    public function management_permissions()
    {
        $managements = explode(",", $this->management_restriction);
        return (!empty($managements[0])?$managements:[]);
    }

    public function module_permission($module)
    {
        if(auth()->user()->hasRole('Super Admin')){
            $status = true;
            return $status;
        }
        $permissions = auth()->user()->getAllPermissions();
        $modules =  $permissions->map(function ($permissions) {
            return $permissions->module;
        })->toArray();

        return in_array($module, $modules);

    }

    public function canany(array $abilities, $arguments = []) {
        return collect($abilities)->reduce(function($canAccess, $ability) use ($arguments) {
          // if this user has access to any of the previously checked abilities, or the current ability, return true
          return $canAccess || app(Gate::class)->forUser($this)->check($ability, $arguments);
        }, false);
    }
}
