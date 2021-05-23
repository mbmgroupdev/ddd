<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use DB, ACL;

class InspectionController extends Controller
{
    public function index()
    {
    	try {
    		return view('inventory.sections.inspection');
    	} catch(\Exception $e) {
    		return $e->getMessage();
    	}
    }
}
