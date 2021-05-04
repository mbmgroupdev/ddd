<?php

namespace App\Models\Hr;

use App\Models\Hr\BillSpecialSettings;
use App\Models\Hr\BillType;
use DB;
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
    	return $this->hasMany(BillSpecialSettings::class, 'bill_setup_id', 'id');
    }

    public function bill_type()
    {
        return $this->belongsTo(BillType::class, 'bill_type_id', 'id');
    }

     public function available_special() {
        return $this->special()->whereNull('end_date');
    }

    public static function checkExistsCode($code)
    {
        return DB::table('hr_bill_settings')
        ->where('code', $code)
        ->pluck('code')
        ->first();
    }
}
