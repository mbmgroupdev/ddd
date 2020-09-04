<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
    
    
    public function reset(Request $request)
    {
        
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        cache_daily_operation();
        
        return redirect()->back();
    }






}
