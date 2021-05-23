<?php

namespace App\Models\Commercial;

use App\Models\Commercial\CmInvoicePiBom;
use App\Models\Commercial\ImportDataEntry;
use Illuminate\Database\Eloquent\Model;

class CmImpInvoice extends Model
{
    public $with = ['invoice_pi', 'invoice_supplier'];
    protected $table = "cm_imp_invoice";
    public function invoice_pi()
    {
        return $this->hasMany(CmInvoicePiBom::class, 'cm_imp_invoice_id', 'id');
    }

    public function invoice_supplier()
    {
    	return $this->belongsTo(ImportDataEntry::class, 'cm_imp_data_entry_id', 'id');
    }
}
 