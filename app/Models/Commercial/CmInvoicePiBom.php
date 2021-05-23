<?php

namespace App\Models\Commercial;

use App\Models\Commercial\CmPiMaster;
use Illuminate\Database\Eloquent\Model;

class CmInvoicePiBom extends Model
{
    public $with = ['pi_master'];
    protected $table = "cm_invoice_pi_bom";
    public $timestamps = false;

    public function pi_master()
    {
    	return $this->belongsTo(CmPiMaster::class, 'cm_pi_master_id','id');
    }
}
