<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Merch\OrderEntry;
use App\Models\Merch\MainCategory;
use App\Http\Controllers\Controller;
use App\Models\Merch\OrderBomCostingBooking;

class RequisitionController extends Controller
{
    public function index()
    {
        $catagories = MainCategory::catagory();
        $orders = OrderEntry::orders();
        // return $orders;

        return view('inventory.requisitionProduction',compact('catagories','orders'));
    }
    public function itemsData(Request $request)
    {
        $itemId = $request->itemId;
        $orderId = $request->orderId;
        $items = OrderBomCostingBooking::with('McatItem')->where('order_id',$orderId)->where('mr_material_category_mcat_id',$itemId)->get();
        
        return $items;

        return view('Inventory.RequisitionProduction',compact('catagories','orders'));
    }
    public function othersData(Request $request)
    {
        $itemId = $request->otherItemId;
        $orderValue = $request->orderValue;
        $items = OrderBomCostingBooking::where('mr_cat_item_id',$itemId)->where('order_id',$orderValue)->get();
        
        return $items;

        return view('Inventory.RequisitionProduction',compact('catagories','orders'));
    }
}
