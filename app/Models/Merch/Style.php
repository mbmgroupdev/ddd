<?php

namespace App\Models\Merch;

use App\Models\Merch\Style;
use App\Models\Merch\StyleImage;
use Illuminate\Database\Eloquent\Model;
use DB;

class Style extends Model
{
    protected $table= 'mr_style';
    public $timestamps= false;
    // public $rules = [
    //
    //         "stl_no" => "required|max:30|unique:mr_style,mr_buyer_b_id,prd_type_id,mr_season_se_id",
    //      ];

    public static function checkStyleNoTextWise($stl_no)
    {
    	return Style::select('stl_no')->where('stl_no', $stl_no)->first();
    }

    public function style_image()
    {
    	return $this->hasMany(StyleImage::class, 'mr_stl_id', 'stl_id');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Merch\OrderEntry', 'stl_id', 'mr_style_stl_id');
    }

    public static function getStyleInfo($selectedField)
    {
        $query = DB::table("mr_style")
            ->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions());
        if($selectedField != 'all'){
            $query->select($selectedField);
        }
        return $query->orderBy('stl_id', 'desc')->get();
    }

    public static function getStyleIdWiseStyleInfo($id, $selectedField)
    {
        $query = DB::table("mr_style")
            ->whereIn('mr_buyer_b_id', auth()->user()->buyer_permissions())
            ->where("stl_id", $id);
        if($selectedField != 'all'){
            $query->select($selectedField);
        }
        return $query->first();
    }

    public static function getSeasonStyleBuyerIdWise($buyerid)
    {
        $season = DB::table('mr_season');
        $season_sql = $season->toSql();

        $getSeason = DB::table('mr_style AS stl')
        ->select(DB::raw('CONCAT_WS("-", se_id, stl_year) AS id'), DB::raw('CONCAT_WS("-", se_name, stl_year) AS text'))
        ->leftjoin(DB::raw('(' . $season_sql. ') AS s'), function($join) use ($season) {
            $join->on('stl.mr_season_se_id','s.se_id')->addBinding($season->getBindings());
        })
        ->where('s.b_id', $buyerid)
        ->orderBy('text', 'desc')
        ->groupBy('text')
        ->get();

        return $getSeason;
    }
}
