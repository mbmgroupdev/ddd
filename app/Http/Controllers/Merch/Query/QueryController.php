<?php

namespace App\Http\Controllers\Merch\Query;

use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables;

class QueryController extends Controller
{

    public function merchQuery(Request $request)
    {
        try{
        	//return $request;
            $resultData = '';
            return view('merch.query.merch_query', compact('resultData'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

}
