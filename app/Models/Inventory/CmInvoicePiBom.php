<?php

namespace App\Models\Inventory;

use App\Models\Inventory\CmImpInvoice;
use Illuminate\Database\Eloquent\Model;

class CmInvoicePiBom extends Model
{
    protected $table = "cm_invoice_pi_bom";
    public function CmImpInvoice()
    {
        return $this->belongsTo(CmImpInvoice::class);
    }
}
