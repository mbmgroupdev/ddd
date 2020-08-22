<?php

namespace App\Http\Controllers\Hr\Search;

use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables;

class SalaryRatioSearchController extends Controller
{
    public function hrSalRatioSearch(Request $request)
    {
        try{
            return $this->searchGetSalRatioGlobal($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pageTitle($request)
    {
        // $showTitle = ucwords($request['category']).' - '.ucwords($request['type']) ;
        $showTitle = 'Salary Ratio - '.ucwords($request['type']) ;
        if(isset($request['month'])){
            $showTitle =$showTitle.': '.$request['month'];
        }
        if(isset($request['year'])){
            $showTitle =$showTitle.'-'.$request['year'];
        }
        if($request['type']=='date'){
            $showTitle =$showTitle.': '.$request['date'];
        }
        return $showTitle;
    }

    public function getSearchType($request)
    {
        if($request['type'] == 'date') {
            $date = date('Y-m-d', strtotime($request['date']));
        }
        else if($request['type'] == 'month') {
            $date = date('Y-m-%', strtotime($request['month']));
        }else if($request['type'] == 'year') {
            $date = date('Y-%-%', strtotime($request['year']));
        }else{
            $date = date('Y-m-d');
        }
        return $date;

    }

    public function getSalRatioFromDate($date)
    {
        /*$query = DB::table('hr_monthly_salary')
                ->select(
                    DB::raw('*,max(id) as sal_id'),
                    'total_payable as pay',
                    'as_id as asid' 
                );
        $salaryData = $query->groupBy('as_id');
        $salaryData_sql = $salaryData->toSql();*/


         $status = DB::table('hr_as_basic_info AS e')
                    ->select(
                        DB::raw('sum(b.ben_current_salary) AS total_payable'),
                        DB::raw('count(e.associate_id) AS employee'),
                        'e.as_status'
                        )
                    ->Join('hr_benefits AS b','b.ben_as_id','e.associate_id')
                    ->where('e.as_status_date','like',$date)
                    ->whereNotIn('e.as_status',[0,1,6])
                    ->groupBy('e.as_status')
                    ->get();
        return $status;

    }

    public function getJoinSalRatioFromDate($date){
        $status = DB::table('hr_as_basic_info AS e')
                    ->select(
                        DB::raw('sum(b.ben_current_salary) AS total_payable'),
                        DB::raw('count(e.associate_id) AS employee')
                        )
                    ->Join('hr_benefits AS b','b.ben_as_id','e.associate_id')
                    ->where('e.as_doj','like',$date)
                    ->where('e.as_status',1)
                    ->groupBy('e.as_status')
                    ->first();
        return $status;

    }

   

    public function searchGetSalRatioGlobal($request)
    {
        try {

            $date = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);

            $statusSalary = $this->getSalRatioFromDate($date);
            $joinedSalary = $this->getJoinSalRatioFromDate($date);
            //dd($joinedSalary);
            $result['page'] = view('hr.search.salratio.allempstatus',compact('showTitle', 'request', 'statusSalary','joinedSalary'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    // print section =======
    public function hrSalRatioSearchPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        $joined = $request->joined;
        return view('hr.search.salratio.allempstatusPrint',compact('joined','data','title'))->render();
    }

}
