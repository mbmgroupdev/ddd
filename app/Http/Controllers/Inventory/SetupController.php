<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\StWareHouseEntry;

class SetupController extends Controller
{
    public function index(){
        $datas = StWareHouseEntry::all();
    	return view('inventory.sections.setup', compact('datas'));
    }
}
