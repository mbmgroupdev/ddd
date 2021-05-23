<?php

namespace App\Http\Controllers\Commercial\Query;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Validator, Auth, ACL, DB, DataTables;

class QueryController extends Controller
{
    public function commercialQuery(Request $request)
    {
        try{
        	//return $request;
            $resultData = '';
            return view('commercial.query.commercial_query', compact('resultData'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
