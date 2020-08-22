<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;

class TopManagement extends Model
{
    protected $table= "top_management";
    protected $fillable = [
        'unit_id', 'group_name', 'top_management_members'
    ];
    public $timestamps = false;
   
}
