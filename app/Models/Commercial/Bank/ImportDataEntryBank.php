<?php

namespace App\Models\Commercial\Bank;

use App\Models\Commercial\Bank\ImportDataEntryBank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ImportDataEntryBankBank extends Model
{
	//public $with = ['file'];
	protected $table = "cm_imp_data_entry";

    public $timestamps = false;

    
}
