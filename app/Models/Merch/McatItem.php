<?php

namespace App\Models\Merch;

use App\Models\Merch\MainCategory;
use App\Models\Merch\McatItem;
use App\Models\Merch\OrderBomCostingBooking;
use Illuminate\Database\Eloquent\Model;
use DB;

class McatItem extends Model
{
	// public $with = ['item_placement', 'mr_material_category'];
    protected $table= 'mr_cat_item';
    public $timestamps= false;

    public function item_placement()
	{
		return $this->belongsTo('App\Models\Merch\ItemPlacement', 'item_id', 'id');
	}

	public function OrderBomCostingBooking()
	{
		return $this->hasMany(OrderBomCostingBooking::class);
	}

	public function mr_material_category()
	{
		return $this->hasOne(MainCategory::class, 'mcat_id', 'mcat_id');
	}

	public static function checkExistItem($data)
	{
		return McatItem::
		where('mcat_id', $data['mcat_id'])
		->where('item_name', $data['item_name'])
		->where('item_code', $data['item_code'])
		->first();
	}

	public static function getItemListItemIdsWise($ids)
	{
		return DB::table('mr_cat_item AS i')
            ->select('i.id','i.item_name','i.item_code', 'i.dependent_on')
            ->whereIn('i.id', $ids)
            ->get();
	}
}
