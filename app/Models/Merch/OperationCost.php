<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class OperationCost extends Model
{
    protected $table= 'mr_style_operation_n_cost';
    public $timestamps= false;

    public static function getStyleIdWiseOperationCostName($stlId)
    {
    	return DB::table("mr_style_operation_n_cost AS oc")
		    	->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
		    	->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
		    	->where("oc.mr_style_stl_id", $stlId)
		    	->first();
    }

    public static function getStyleIdWiseOperationInfo($stlId, $oprType)
    {
    	$operationData = DB::table('mr_operation');
		$operationSql = $operationData->toSql();
    	return DB::table("mr_style_operation_n_cost AS oc")
			->select("oc.*", 'oc.style_op_id AS op_id',"o.opr_name")
			->leftjoin(DB::raw('(' . $operationSql. ') AS o'), function($join) use ($operationData) {
                $join->on("oc.mr_operation_opr_id", "o.opr_id")->addBinding($operationData->getBindings());
            })
			->where("oc.mr_style_stl_id", $stlId)
			->where("oc.opr_type", $oprType)
			->get();
    }

    public static function getStyleIdWiseSpOperationInfo($stlId, $oprType)
    {
        return DB::table("mr_style_operation_n_cost AS oc")
            ->where("oc.mr_style_stl_id", $stlId)
            ->where("oc.opr_type", $oprType)
            ->get();
    }
}