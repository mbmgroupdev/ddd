<?php

namespace App\Models\Inventory;

use App\Models\Inventory\CmFile;
use App\Models\Inventory\MrSupplier;
use Illuminate\Database\Eloquent\Model;

class CmPiMaster extends Model
{
    protected $table = 'cm_pi_master';
    public function CmFile()
    {
        return $this->belongsTo(CmFile::class);
    }

    public function MrSupplier()
    {
        return $this->belongsTo(MrSupplier::class,'mr_supplier_sup_id','sup_id');
    }
}
