<?php

namespace App\Models\Commercial;

use Illuminate\Database\Eloquent\Model;

class SalesContractAmend extends Model
{
    protected $table= 'cm_sales_contract_amend';
    public $timestamps= false;

    // public function cm_sales_contract_amend()
    // {
    // 	return $this->hasOne('App\Models\Commercial\SalesContract', 'id', 'cm_sales_contract_id	');
    // }
}
