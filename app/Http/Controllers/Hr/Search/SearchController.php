<?php

namespace App\Http\Controllers\Hr\Search;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\Department;
use App\Models\Employee;
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

    public function type(Request $request)
    {
        try {
            $input = $request->all();
            // return $input;
            $data['type'] = 'error';
            
            if($input['type'] == 'as_designation_id'){
                $query = DB::table('hr_designation')
                ->select('hr_designation_id AS id', 'hr_designation_name AS text')
                ->where('hr_designation_name', 'LIKE', '%'.$request->keyvalue.'%');
            }elseif($input['type'] == 'as_department_id'){
                $query = DB::table('hr_department')
                ->select('hr_department_id AS id', 'hr_department_name AS text')
                ->where('hr_department_name', 'LIKE', '%'.$request->keyvalue.'%');
            }elseif($input['type'] == 'as_section_id'){
                $query = DB::table('hr_section')
                ->select('hr_section_id AS id', 'hr_section_name AS text')
                ->where('hr_section_name', 'LIKE', '%'.$request->keyvalue.'%');
            }elseif($input['type'] == 'as_subsection_id'){
                $query = DB::table('hr_subsection')
                ->select('hr_subsec_id AS id', 'hr_subsec_name AS text')
                ->where('hr_subsec_name', 'LIKE', '%'.$request->keyvalue.'%');
            }elseif($input['type'] == 'as_id'){
                $query = DB::table('hr_as_basic_info')
                ->select('as_id AS id', DB::raw('CONCAT_WS(" - ", associate_id, as_name) AS text'))
                ->where('as_status', [1,6])
                ->where('associate_id', 'LIKE', '%'. $request->keyvalue .'%')
                ->whereIn('as_unit_id', auth()->user()->unit_permissions())
                ->whereIn('as_location', auth()->user()->location_permissions())
                ->orWhere('as_name', 'LIKE', '%'. $request->keyvalue . '%')
                ->orWhere('as_oracle_code', 'LIKE', '%'. $request->keyvalue . '%');
            }else{
                $data['message'] = 'Something Wrong, Please reload the page';
                return $data;
            }
            $data['type'] = 'success';
            $data['value'] = $query->limit(10)->get();
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

}
