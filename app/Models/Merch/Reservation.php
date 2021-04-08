<?php

namespace App\Models\Merch;

use App\Models\Merch\Reservation;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table= 'mr_capacity_reservation';
    public $timestamps= false;
    protected $guarded = [];

    public static function getReservationIdWiseReservation($rId)
    {
    	return Reservation::where('id', $rId)->first();
    }

    public static function checkReservationExists($value)
    {
    	return Reservation::where('hr_unit_id', $value['hr_unit_id'])
    	->where('b_id', $value['b_id'])
    	->where('res_month', $value['res_month'])
    	->where('res_year', $value['res_year'])
    	->where('prd_type_id', $value['prd_type_id'])
    	->exists();
    }

    public static function checkReservationIdWise($value)
    {
        return Reservation::where('hr_unit_id', $value['hr_unit_id'])
        ->where('b_id', $value['b_id'])
        ->where('res_month', $value['res_month'])
        ->where('res_year', $value['res_year'])
        ->where('prd_type_id', $value['prd_type_id'])
        ->first();
    }

    public static function checkReservationStyleInfoWise($value)
    {
        return Reservation::where('b_id', $value['mr_buyer_b_id'])
        ->where('prd_type_id', $value['prd_type_id'])
        ->first();
    }
}
