<?php

namespace App\Http\Controllers\Wash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WashBillEntryController extends Controller
{
    public function index(){
    	return view('wash.sections.wash_bill_entry');
    }
}
