<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use DB;

class BonusRule extends Model
{
    protected $table= "hr_bonus_rule";

    protected $guarded = ['id'];

    public static function getNonApprovalBonusList()
    {
    	return DB::table('hr_bonus_rule')
    	->whereNull('approved_at')
    	->orderBy('id', 'desc')
    	->get();
    }

    
}