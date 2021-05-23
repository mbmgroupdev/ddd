<?php

namespace App\Http\Controllers\Wash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryChallanController extends Controller
{
    public function index(){
    	return view('wash.sections.delivery_challan');
    }
}
