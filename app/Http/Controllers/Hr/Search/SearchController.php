<?php

namespace App\Http\Controllers\Hr\Search;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables;

class SearchController extends Controller
{

    public function hrSearch(Request $request)
    {
        try{
        	//return $request;
            $resultData = '';
            return view('hr.search.hr_search', compact('resultData'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

}
