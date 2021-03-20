<?php

namespace App\Models\Merch;

use Illuminate\Database\Eloquent\Model;
use DB;

class OrderOperationNCost extends Model
{
    protected $table= 'mr_order_operation_n_cost';
    protected $primaryKey = 'order_op_id';
    public $timestamps= false;
    protected $guarded = [];

    public static function getOrderIdWiseOperationInfo($ordId, $oprType)
    {
    	$operationData = DB::table('mr_operation');
		$operationSql = $operationData->toSql();
    	return DB::table("mr_order_operation_n_cost AS oc")
			->select("oc.*", 'oc.order_op_id AS op_id',"o.opr_name")
			->leftjoin(DB::raw('(' . $operationSql. ') AS o'), function($join) use ($operationData) {
                $join->on("oc.mr_operation_opr_id", "o.opr_id")->addBinding($operationData->getBindings());
            })
			->where("oc.mr_order_entry_order_id", $ordId)
			->where("oc.opr_type", $oprType)
			->get();
    }
}
