<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Outsides extends Model
{
	public $with = ['basic'];
    protected $table= 'hr_outside';
    public $timestamps= false;

    public function basic()
    {
    	return $this->hasOne(Employee::class, 'associate_id', 'as_id');
    }
}
