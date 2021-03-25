<?php

namespace App\Repository\Merch;

use App\Contracts\Merch\PoBomInterface;
use Illuminate\Support\Collection;

use DB;

class PoBomRepository implements PoBomInterface
{

   /**
    * UserRepository constructor.
    *
    * @param User $model
    */
   public function __construct()
   {
       //parent::__construct($model);
   }

   /**
    * @return Collection
    */
   public function bom($orderId, $supplierId = null): Collection
   {
       return DB::table("mr_po_bom_costing_booking AS b")
        ->select(
            "b.*",
            "c.mcat_name",
            "c.mcat_id",
            "i.item_name",
            "i.item_code",
            "i.id as item_id",
            "i.dependent_on",
            "mc.clr_code",
            "s.sup_name",
            "s.sup_id",
            "a.art_name",
            "com.comp_name",
            "con.construction_name",
            "OE.order_qty",
            'PO.po_no as po_po_no',
            'PO.po_id as po_po_id',
            'PO.po_qty as po_po_qty',
            'POS.po_sub_style_id as po_pos_id',
            'POS.clr_id as po_pos_cid',
            'POS.po_sub_style_qty as po_pos_sqty'
        )
        ->leftJoin("mr_material_category AS c", function($join) {
            $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
        })
        ->leftJoin("mr_cat_item AS i", function($join) {
            $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
            $join->on("i.id", "=", "b.mr_cat_item_id");
        })
        ->leftJoin("mr_material_color AS mc", "mc.clr_id", "b.clr_id")
        ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
        ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
        ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
        ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
        ->where("b.order_id", $orderId)
        ->when($supplierId, function($query, $supplierId) {
            return $query->where('b.mr_supplier_sup_id',$supplierId);
        })
        ->leftJoin('mr_order_entry AS OE', 'OE.order_id', 'b.order_id')
        ->leftJoin('mr_purchase_order AS PO', 'PO.po_id', 'b.po_id')
        ->leftJoin('mr_po_sub_style AS POS', 'POS.po_id', 'b.po_id')
        ->orderBy("b.mr_material_category_mcat_id",'ASC')
        ->groupBy("i.id")
        ->get()
        ->groupBy('po_id')
        ->map(function($po){
            // group by category
            return collect($po)
                ->groupBy('mr_material_category_mcat_id');
        });  
   }

   /**
    * @return Collection
    */

   public function subStyle($order_id): Collection
   {

   }

   /**
    * @return Collection
    */

   public function breakdown($bom, $subStyle): Collection
   {
        $bom = $bom->map(function($item){
            if($item->depends_on == 1){
                // color depend


            }else if($item->depends_on == 2){
                // size depend
            }else if($item->depends_on == 3){
                // color & size
            }else{
                // no dependency
            }
        });
   }
}