<?php

namespace App\Models\Merch;

use App\Models\Merch\PoBooking;
use Illuminate\Database\Eloquent\Model;

class PoBookingPi extends Model
{
    protected $table= 'mr_po_booking_pi';
    public $timestamps= false;

    public function po_booking()
    {
    	return $this->hasMany(PoBooking::class, 'id', 'mr_po_booking_id');
    }
}
