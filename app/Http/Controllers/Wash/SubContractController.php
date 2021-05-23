<?php

namespace App\Http\Controllers\Wash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubContractController extends Controller
{
    public function index(){
    	return view('wash.sections.sub_contract');
    }
}
