<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use App\Models\Merch\Composition;

class Article extends Model
{
    protected $table= 'mr_article';
    public $timestamps= false;
    // public $with = ['composition'];

    public function composition()
    {
        return $this->hasOne(Composition::class, 'id', 'mr_article_id');
    }
}
