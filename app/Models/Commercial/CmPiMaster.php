<?php

namespace App\Models\Commercial;

use Illuminate\Database\Eloquent\Model;
use App\Models\Commercial\CmPiBom;

class CmPiMaster extends Model
{
	public $with = ['pi_master_bom'];
    protected $table= "cm_pi_master";
    public $timestamps= false;

    public function pi_master_bom(){
    	return $this->hasMany(CmPiBom::class, 'cm_pi_master_id','id');
    }
}
