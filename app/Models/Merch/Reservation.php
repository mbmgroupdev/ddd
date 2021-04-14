<?php

namespace App\Models\Merch;

use App\Models\Merch\Reservation;
use Illuminate\Database\Eloquent\Model;
use DB;

class Reservation extends Model
{
    protected $table= 'mr_capacity_reservation';
    protected $fillable = ['hr_unit_id', 'b_id', 'res_month', 'res_year', 'prd_type_id', 'res_quantity', 'res_sah', 'res_sewing_smv', 'res_status'];
    public $timestamps= false;

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

    public static function getReservationForOrder($value)
    {
        return Reservation::where('b_id', $value['mr_buyer_b_id'])
        ->where('hr_unit_id', $value['unit_id'])
        ->where('prd_type_id', $value['prd_type_id'])
        ->where('res_year', '>=', date('Y'))
        ->where('res_month', '>', date('m'))
        ->orderBy('id', 'desc')
        ->first();
    }

    public static function getReservationData($team=null)
    {
        $queueData = DB::table('mr_capacity_reservation AS cr')
            ->whereIn('cr.b_id', auth()->user()->buyer_permissions());
            if(!empty($team)){
                $queueData->whereIn('cr.res_created_by', $team);
            }
            $queueData->orderBy('cr.id', 'DESC');
        $data = $queueData->get();
        return $data;
    }
}
