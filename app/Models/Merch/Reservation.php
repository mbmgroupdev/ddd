<?php

namespace App\Models\Merch;

use App\Models\Merch\Reservation;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table= 'mr_capacity_reservation';
    public $timestamps= false;

    public static function getReservationIdWiseReservation($rId)
    {
    	return Reservation::where('res_id', $rId)->first();
    }
}
