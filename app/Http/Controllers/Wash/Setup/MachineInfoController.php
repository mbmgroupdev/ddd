<?php

namespace App\Http\Controllers\Wash\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MachineInfoController extends Controller
{
    public function index(){
    	return view('wash.setup.machine_info');
    }
}
