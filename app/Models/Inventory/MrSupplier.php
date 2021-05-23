<?php

namespace App\Models\Inventory;

use App\Models\Inventory\CmPiMaster;
use Illuminate\Database\Eloquent\Model;

class MrSupplier extends Model
{
    protected $table = 'mr_supplier';
    public function CmPiMaster()
    {
        return $this->hasMany(CmPiMaster::class);
    }
}
