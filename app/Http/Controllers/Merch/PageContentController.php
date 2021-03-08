<?php

namespace App\Http\Controllers\Merch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageContentController extends Controller
{
    public function index(Request $request)
    {
    	$input = $request->all();
    	if($input['type'] != ''){
    		return view('merch.page.'.$input['type'], compact('input'));
    	}else{
    		return 'error';
    	}
    }
}
