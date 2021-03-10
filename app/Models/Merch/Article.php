<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use App\Models\Merch\Composition;
use DB;

class Article extends Model
{
    protected $table= 'mr_article';
    public $timestamps= false;
    // public $with = ['composition'];

    public function composition()
    {
        return $this->hasOne(Composition::class, 'id', 'mr_article_id');
    }

    public static function checkExistSupplierWiseArticle($value)
    {
    	return DB::table('mr_article')
    	->where('mr_supplier_sup_id', $value['supplier'])
    	->where('art_name', $value['art_name'])
    	->first();
    }
}
