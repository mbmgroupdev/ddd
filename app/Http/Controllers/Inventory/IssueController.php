<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IssueController extends Controller
{
    public function index(){
    	return view('inventory.sections.issue');
    }
}
