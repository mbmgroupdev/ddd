<?php

namespace App\Http\Controllers\Wash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderBookingController extends Controller
{
    public function index(){
    	return view('wash.sections.order_booking');
    }
}
