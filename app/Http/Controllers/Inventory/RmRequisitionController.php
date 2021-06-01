<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RmRequisitionController extends Controller
{
    public function index(){
    	return view('inventory.sections.rm_requisition');
    }
}
