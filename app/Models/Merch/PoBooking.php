<?php

namespace App\Models\Merch;

use App\Models\Hr\Unit;
use App\Models\Merch\Buyer;
use App\Models\Merch\MrOrderBooking;
use App\Models\Merch\PoBookingDetail;
use App\Models\Merch\Supplier;
use DB;
use Illuminate\Database\Eloquent\Model;

class PoBooking extends Model
{
	// public $with = ['getSupplierInfo','getUnitInfo','poDetails','orderBooking'];
    protected $table = 'mr_po_booking';
    public $timestamps = false;

    public function getSupplierInfo()
    {
		return $this->belongsTo(Supplier::class, 'mr_supplier_sup_id', 'sup_id');
    }

    public function getUnitInfo()
    {
    	return $this->belongsTo(Unit::class, 'unit_id', 'hr_unit_id');
    }

    public function getOrderList($poBookingId)
    {
        $result = '';
        $detailsDatas = DB::table('mr_po_booking_detail as a')
                        ->where('mr_po_booking_id',$poBookingId)
                        ->leftJoin('mr_order_entry as b','b.order_id','a.mr_order_entry_order_id')
                        ->groupBy('a.mr_order_entry_order_id')
                        ->pluck('b.order_code','b.order_id')
                        ->toArray();
        if(!empty($detailsDatas)) {
            $result = implode(', ', $detailsDatas);
        }
        return $result;

    }

    public function poDetails()
    {
        return $this->hasMany(PoBookingDetail::class, 'mr_po_booking_id', 'id');
    }

    public function orderBooking()
    {
        return $this->hasMany(MrOrderBooking::class, 'mr_po_booking_id', 'id');
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class, 'b_id', 'mr_buyer_b_id');
    }
}
