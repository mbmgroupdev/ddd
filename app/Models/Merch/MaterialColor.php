<?php

namespace App\Models\Merch;

use App\Models\Merch\MaterialColorAttach;
use Illuminate\Database\Eloquent\Model;

class MaterialColor extends Model
{
    protected $table= 'mr_material_color';
    public $timestamps= false;

    public function attached_files()
    {
    	return $this->hasMany(MaterialColorAttach::class, 'clr_id', 'clr_id');
    }
}
