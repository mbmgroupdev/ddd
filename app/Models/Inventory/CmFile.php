<?php

namespace App\Models\Inventory;

use App\Models\Inventory\CmPiMaster;
use Illuminate\Database\Eloquent\Model;

class CmFile extends Model
{
    protected $table = 'cm_file';
    public function CmPiMaster()
    {
        return $this->hasMany(CmPiMaster::class);
    }
}
