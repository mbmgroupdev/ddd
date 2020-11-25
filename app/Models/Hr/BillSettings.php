<?php

namespace App\Models\Hr;

use App\Models\Hr\BillSpecialSettings;
use Illuminate\Database\Eloquent\Model;

class BillSettings extends Model
{
	protected $table = 'hr_bill_settings';
	protected $primaryKey = 'id';
    protected $guarded = [];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function special()
    {
    	return $this->hasMany(BillSpecialSettings::class, 'bill_id', 'id');
    }

     public function available_special() {
        return $this->special()->whereNull('end_date');
    }
}
