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

    public static function getApprovalGroupBonusList()
    {
    	return DB::table('hr_bonus_rule AS r')
		->select('r.id', DB::raw('CONCAT_WS(" - ", bonus_type_name, bonus_year) AS text'))
		->join('hr_bonus_type AS b', 'r.bonus_type_id', 'b.id')
		->whereIn('r.unit_id', auth()->user()->unit_permissions())
    	->whereNotNull('r.approved_at')
    	->orderBy('r.id', 'desc')
    	->groupBy('text')
    	->get();
    }

    public static function getUnitGroupBonusList()
    {
    	return DB::table('hr_bonus_rule AS r')
		->select('r.id', DB::raw('CONCAT_WS(" - ", bonus_type_name, bonus_year) AS text'))
		->join('hr_bonus_type AS b', 'r.bonus_type_id', 'b.id')
		->whereIn('r.unit_id', auth()->user()->unit_permissions())
    	->orderBy('r.id', 'desc')
    	->groupBy('text')
    	->get();
    }

    
}