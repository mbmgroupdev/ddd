<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use DB;

class SearchController extends Controller
{
    public function searchEmp(Request $request)
    {
        $input = $request->all();
        $getEmployee = Employee::getSearchGlobalKeyWise($input['name_startsWith']);

        $data = array();

        if(count($getEmployee) > 0){
            foreach ($getEmployee as $emp) {
                $data[] = $emp->associate_id.' - '.$emp->as_name;
            }
        }
        return $data;
    }

    public function search(Request $request)
    {
        $input = $request->all();
        $inputExp = explode('-', $input['search']);
        $input['search'] = $inputExp[0];
        $getEmployee = Employee::getSearchKeyWise($input['search']);
        return view('common.search_result', compact('getEmployee'));
    }

    public function suggestion(Request $request)
    {
        $keyword = $request->keyword;

        $employees = DB::table('hr_as_basic_info')
        ->select('as_name', 'associate_id', 'as_pic', 'as_gender','as_status')
        ->where(function ($q) use ($keyword) {
            $q->where('associate_id', 'LIKE', '%'. $keyword .'%');
            $q->orWhere('as_name', 'LIKE', '%'. $keyword . '%');
            $q->orWhere('as_oracle_code', 'LIKE', '%'. $keyword . '%');
        })
        ->whereIn('as_unit_id', auth()->user()->unit_permissions())
        ->where('as_status', 1)
        ->limit(5)
        ->get();


        return view('common.search_suggestion', compact('employees','keyword'))->render();
    }
    
    
    public function reset(Request $request)
    {
        
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        cache_daily_operation();
        
        return redirect()->back();
    }






}
