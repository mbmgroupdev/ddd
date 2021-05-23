<?php

namespace App\Http\Controllers\Wash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WashDeliveryController extends Controller
{
    public function index(){
    	return view('wash.sections.wash_delivery');
    }
}
