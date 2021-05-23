<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\CmInvoicePiBom;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmImpInvoice extends Model
{
    protected $table = "cm_imp_invoice";
    public function cmInvoicePiBoms()
    {
        return $this->hasMany(cmInvoicePiBom::class);
    }
}
