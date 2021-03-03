<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;

class StyleImage extends Model
{
    protected $table= 'mr_style_image';
    protected $fillable = ['mr_stl_id', 'image', 'sequence'];
}
