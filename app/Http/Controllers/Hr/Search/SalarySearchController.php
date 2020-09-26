<?php

namespace App\Http\Controllers\Hr\Search;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\SalaryAddDeduct;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Shift;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Collective\Html\HtmlFacade;
use Validator, Auth, ACL, DB, DataTables, stdClass;

class SalarySearchController extends Controller
{
    public function hrSalarySearch(Request $request)
    {
        try{

            return $this->hrSalarySearchGlobal($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSearchType($request)
    {
        if($request['type'] == 'month') {
            $date = [
            'month' => date('m', strtotime($request['month'])),
            'year' => date('Y', strtotime($request['month']))
           ];        
        }else if ($request['type'] == 'range') {          
            $date=[
            'from'=>date('m-Y', strtotime($request['rangeFrom'])),
            'to' => date('m-Y', strtotime($request['rangeTo']))
            ];
        }else if($request['type'] == 'year') {
            $date = ['year'=> $request['year'] ];
        }else{
            $date=[
                'year' => date('Y')
            ];
        } 
        return $date;
    }

    public function pageTitle($request){

            $showTitle = ucwords($request['category']).' - '.ucwords($request['type']) ;
            if(isset($request['month']))
            {
                $showTitle =$showTitle.': '.$request['month'];
            }
            if(isset($request['year']))
            {
                $showTitle =$showTitle.': '.$request['year'];
            }
            if($request['type']=='range'){
                $showTitle =$showTitle.': '.$request['rangeFrom'].' to '.$request['rangeTo'];
            }

            return $showTitle;
    }

    public function hrSalarySearchResWise(Request $request){        
        $request2 = [];
        $parts = parse_url(url()->previous());

        parse_str($parts['query'], $request1);
        //return $request1;
        
        if(isset($request->unit)){
            $infocon['e.as_unit_id'] = $request->unit;
            $salary['floor'] = Floor::where([
                                'hr_floor_unit_id' => $request->unit
                                ])->count();
        }
        if(isset($request->area)){
            $infocon['e.as_area_id'] = $request->area;

            $salary['dept'] = Department::where([
                                'hr_department_area_id' => $request->area
                                ])->count();
        }
        if(isset($request->department)){
            $infocon['e.as_department_id'] = $request->department;

            $salary['sec'] = Section::where([
                                'hr_section_area_id' => $request->area,
                                'hr_section_department_id' => $request->department
                                ])->count(); 
        }
        if(isset($request->floor)){
            $infocon['e.as_floor_id'] = $request->floor;
        }
        if(isset($request->section)){
            $infocon['e.as_section_id'] = $request->section;

            $salary['subsec'] = Subsection::where([
                                    'hr_subsec_area_id' => $request->area,
                                    'hr_subsec_department_id' => $request->department,
                                    'hr_subsec_section_id' => $request->section
                                    ])->count(); 
        }
        if(isset($request->subsection)){
            $infocon['e.as_subsection_id'] = $request->subsection;
        }

        $date = $this->getSearchType($request1);

        $query= DB::table('hr_monthly_salary')
                ->select(
                    DB::raw('sum(total_payable) AS total_payable'),
                    DB::raw('sum(ot_hour*ot_rate) AS ot_payable'),
                    DB::raw('count(DISTINCT month) as month'),
                    'as_id'
                );
                if(isset($date['from'])){
                    $query->whereBetween(DB::raw("CONCAT(month, '-', year)"),[$date['from'],$date['to']]);
                }else{
                    $query->where($date);
                }
        $salaryData = $query->groupBy('as_id');
        $salaryData_sql = $salaryData->toSql();


         $query1 = DB::table('hr_as_basic_info AS e')
                        ->select(
                            DB::raw('sum(a.total_payable) AS total_payable'),
                            DB::raw('sum(a.ot_payable) AS ot_payable'),
                            DB::raw('count(a.as_id) AS emp')
                        )
                        ->where($infocon);
            $query1->Join(DB::raw('(' . $salaryData_sql. ') AS a'), function($join) use ($salaryData) {
                        $join->on('a.as_id', '=', 'e.associate_id')->addBinding($salaryData->getBindings()); ;
                    });
            
            $salInfo = $query1->first();


        
        
        $salary['total'] =  round($salInfo->total_payable,2);
        $salary['ot'] = round($salInfo->ot_payable,2);
        $salary['salary'] = round(($salary['total']- $salary['ot']),2);      
        $salary['emp']= $salInfo->emp;
        $salary['name']= $request->name;
        return $salary;

    }


    public function hrSalarySearchGlobal($request)
    {
        try {
            //return $request;
            $date = $this->getSearchType($request);

            
            $showTitle = $this->pageTitle($request);
            unset($request['unit'],$request['area'],$request['department'],$request['floor'],$request['section'],$request['subsection'],$request['view'],$request['salstatus']); 
            if($request['type']=='range'){
                $salaryInfo = HrMonthlySalary::select(
                    DB::raw('sum(ot_hour*ot_rate) AS ot_payable'),
                    DB::raw('sum(total_payable) AS total_payable'),
                    DB::raw('count(distinct as_id) as emp')
                )->whereBetween(DB::raw("CONCAT(month, '-', year)"),[$date['from'],$date['to']])->first();
            }else{
                $salaryInfo = HrMonthlySalary::select(
                    DB::raw('sum(ot_hour*ot_rate) AS ot_payable'),
                    DB::raw('sum(total_payable) AS total_payable'),
                    DB::raw('count(distinct as_id) as emp')
                )->where($date)->first();
            }
            $salary = new stdClass();
            $salary->total_payable = round($salaryInfo->total_payable,2);
            $salary->ot_payable = round($salaryInfo->ot_payable,2);
            $salary->salary_payable = round(($salaryInfo->total_payable-$salaryInfo->ot_payable),2);
            $salary->employee  = $salaryInfo->emp;
            $unit_list      = Unit::get();
            $result = [];
            $result['page'] = view('hr.search.salary.allsalary',
                compact('unit_list','salary','showTitle', 'request'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrSalarySearchUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $request['view'] = 'allunit';
            $date = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            
            unset($request['unit'],$request['area'],$request['department'],$request['floor'],$request['section'],$request['subsection'],$request['salstatus']);

            $unit_list = Unit::get();
            $area_count = Area::count();
            $result = [];
            $result['page'] = view('hr.search.salary.allunit', 
                compact('unit_list', 'request', 'area_count', 'showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrSalarySearchArea(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['unit'] = $request->unit;
            unset($request1['area'],$request1['department'],$request1['floor'],$request1['section'],$request1['subsection'],$request1['view'],$request1['salstatus']);

            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            // return $request1;
            $unit=Unit::where(['hr_unit_id' => $request1['unit']])->first();
            $area_list = Area::get();

            $area_data=[];
            $where=[];
             //return $area_data;
            $result = [];
            $result['page'] = view('hr.search.salary.allarea',
                compact('area_list','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrSalarySearchDepartment(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['area'] = $request->area;
            
            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            unset($request1['department'],$request1['floor'],$request1['section'],$request1['subsection'],$request1['view'],$request1['salstatus']);

            $unit = Unit::where(['hr_unit_id' => $request1['unit']])->first();
            $area = Area::where([
                        'hr_area_id' => $request1['area']
                        ])
                    ->first();

            $department_list =  Department::where([
                                    'hr_department_area_id' => $request1['area']
                                    ])
                                ->get();

            
            $result = [];
            $result['page'] = view('hr.search.salary.alldepartment',
                compact('department_list','area','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function hrSalarySearchFloor(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['department'] = $request->department;

            $showTitle = $this->pageTitle($request1);

            unset($request1['floor'],$request1['section'],$request1['subsection'],$request1['salstatus']);

            $unit = Unit::where([
                        'hr_unit_id' => $request1['unit']
                        ])
                    ->first();

            $area = Area::where([
                        'hr_area_id' => $request1['area']
                        ])
                    ->first();

            $department = Department::where([
                            'hr_department_id' => $request1['department']
                            ])
                        ->first();

            $floor_list =   Floor::where([
                                'hr_floor_unit_id' => $request1['unit']
                                ])
                            ->get();            
            $result = [];
            $result['page'] = view('hr.search.salary.allfloor',
                compact('floor_list','area','department','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrSalarySearchSection(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['floor'] = $request->floor;
            $showTitle = $this->pageTitle($request1);

            unset($request1['section'],$request1['subsection'],$request1['salstatus']);

            $unit = Unit::where([
                        'hr_unit_id' => $request1['unit']
                        ])
                    ->first();

            $area = Area::where([
                        'hr_area_id' => $request1['area']
                        ])
                    ->first();

            $department = Department::where([
                                'hr_department_id' => $request1['department']
                                ])
                          ->first();

            $floor= Floor::where(['hr_floor_id' => $request1['floor'] ])->first();
            $section_list = Section::where(['hr_section_area_id' => $request1['area'],'hr_section_department_id' => $request1['department']])->get();

            
            $result = [];
            $result['page'] = view('hr.search.salary.allsection',
                compact('section_list','area','department','request1','unit','floor','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrSalarySearchSubscetion(Request $request)
    {
        try {
            
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['section'] = $request->section;
            
            $showTitle = $this->pageTitle($request1);

            unset($request1['subsection'],$request1['view'],$request1['salstatus']);

            $unit = Unit::where([
                        'hr_unit_id' => $request1['unit']
                        ])
                    ->first();

            $area = Area::where([
                        'hr_area_id' => $request1['area']
                        ])
                    ->first();
            $department =   Department::where([
                                'hr_department_id' => $request1['department']
                                ])
                            ->first();
            $floor= Floor::where([
                        'hr_floor_id' => $request1['floor']
                        ])
                    ->first();
            $section =  Section::where([
                            'hr_section_id' =>$request1['section']
                            ])
                        ->first();

            $subsection_list = Subsection::where([
                                'hr_subsec_area_id' => $request1['area'],
                                'hr_subsec_department_id' => $request1['department'],
                                'hr_subsec_section_id' =>$request1['section']
                                ])
                            ->get();
            
            $result = [];
            $result['page'] = view('hr.search.salary.allsubsection',
                compact('subsection_list','area','department','request1','unit','section','floor','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrSalarySearchEmployee(Request $request)
    {
        try {
            // get previous url params
            
            $request2 = [];
            $data=[];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view']='employee';
            //return $request1;
            if(isset($request->salstatus)){
                $request1['salstatus']= $request->salstatus;
            }
            if(isset($request->unit)){
                $request1['unit']= $request->unit;
            }
            if(isset($request->area)){
                $request1['area']= $request->area;
            }
            if(isset($request->department)){
                $request1['department']= $request->department;
            }
            if(isset($request->floor)){
                $request1['floor']= $request->floor;
            }
            if(isset($request->section)){
                $request1['section']= $request->section;
            }
            if(isset($request->subsection)){
                $request1['subsection']= $request->subsection;
            }
            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            if(isset($request->subsection) || isset($request1['subsection'])) {
                $request2['as_subsection_id'] = isset($request->subsection)?$request->subsection:$request1['subsection'];
                $data['subsection'] = Subsection::where('hr_subsec_id',$request2['as_subsection_id'])->first();
            }

            if(isset($request->section) || isset($request1['section'])) {
                $request2['as_section_id'] = isset($request->section)?$request->section:$request1['section'];
                $data['section'] = Section::where('hr_section_id',$request2['as_section_id'])->first();
            }

            if(isset($request->floor) || isset($request1['floor'])) {
                $request2['as_floor_id'] = isset($request->floor)?$request->floor:$request1['floor'];
                $data['floor'] = Floor::where('hr_floor_id',$request2['as_floor_id'])->first();
            }

            if(isset($request->area) || isset($request1['area'])) {
                $request2['as_area_id'] = isset($request->area)?$request->area:$request1['area'];
                $data['area'] = Area::where('hr_area_id',$request2['as_area_id'])->first();
            }

            if(isset($request->department) || isset($request1['department'])) {
                $request2['as_department_id'] = isset($request->department)?$request->department:$request1['department'];
                $data['department'] = Department::where('hr_department_id',$request2['as_department_id'])->first();
            }

            if(isset($request->unit) || isset($request1['unit'])) {
                $request2['as_unit_id'] = isset($request->unit)?$request->unit:$request1['unit'];
                $data['unit'] = Unit::where('hr_unit_id',$request2['as_unit_id'])->first();
            }

            

            


            $result['page'] = view('hr.search.salary.allemployee',
                compact('data','request1','request2','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function  hrSalarySearchListEmployee(Request $request){
        try {

            $request2 = [];
            $parts = parse_url(url()->previous());
            $infocon=[];
            parse_str($parts['query'], $request1);
            //return $request1;
            if(isset($request->unit)){
                $infocon['e.as_unit_id'] = $request->unit;
            }
            if(isset($request->area)){
                $infocon['e.as_area_id'] = $request->area;
            }
            if(isset($request->department)){
                $infocon['e.as_department_id'] = $request->department;
            }
            if(isset($request->floor)){
                $infocon['e.as_floor_id'] = $request->floor;
            }
            if(isset($request->section)){
                $infocon['e.as_section_id'] = $request->section;
            }
            if(isset($request->subsection)){
                $infocon['e.as_subsection_id'] = $request->subsection;
            }

            $date = $this->getSearchType($request1);

            $query= DB::table('hr_monthly_salary')
                ->select(
                    DB::raw('sum(total_payable) AS total_payable'),
                    DB::raw('sum(ot_hour*ot_rate) AS ot_payable'),
                    DB::raw('group_concat(month) as month'),
                    'as_id'
                );
                if(isset($date['from'])){
                    $query->whereBetween(DB::raw("CONCAT(month, '-', year)"),[$date['from'],$date['to']]);
                }else{
                    $query->where($date);
                }
            $salaryData = $query->groupBy('as_id');
            $salaryData_sql = $salaryData->toSql();


            $query1 = DB::table('hr_as_basic_info AS e')
                        ->select(
                            'a.total_payable',
                            'a.ot_payable',
                            'a.month',
                            'e.as_name',
                            'e.as_status',
                            'e.associate_id',
                            'd.hr_designation_name',
                            'f.hr_floor_name'
                        )
                        ->leftJoin('hr_floor as f','f.hr_floor_id','e.as_floor_id')
                        ->leftJoin('hr_designation as d','d.hr_designation_id','e.as_designation_id')
                        ->where($infocon);
            $query1->Join(DB::raw('(' . $salaryData_sql. ') AS a'), function($join) use ($salaryData) {
                        $join->on('a.as_id', '=', 'e.associate_id')->addBinding($salaryData->getBindings()); ;
                    });
            
            $empList = $query1->orderBy('a.total_payable','DESC')
                            ->orderBy('a.ot_payable','DESC')
                            ->get();

            //dd($empList);
            return DataTables::of($empList)->addIndexColumn()
                
                ->editColumn('salary', function ($empList) {
                    return round(($empList->total_payable-$empList->ot_payable),2);
                })
                ->editColumn('month', function ($empList) {
                    $months = explode(',', $empList->month);
                    $month = '';
                    foreach ($months as $key => $value) {
                        $month .= '<span class="label label-green arrowed-right">'.date("M", mktime(0, 0, 0, $value, 10)).'</span>';
                    }
                    return $month;
                })
                ->editColumn('total_payable', function ($empList) {
                    return round(($empList->total_payable),2);
                })
                ->editColumn('ot_payable', function ($empList) {
                    return round(($empList->ot_payable),2);
                })
                ->editColumn('associate_id', function ($empList){
                            return HtmlFacade::link("#",$empList->associate_id,['class' => 'employee_info','data-emp'=>$empList->associate_id]);
                        })
                ->rawColumns(['salary','total_payable','month','ot_payable'])
                ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }

    public function getEmployeeSalaryMonth($date,$id){
        if(isset($date['from'])){
            $from=$date['from'];
            $to=$date['to'];
            $salary=HrMonthlySalary::whereBetween(DB::raw("CONCAT(month, '-', year)"),[$from,$to])->where('as_id',$id)->pluck('month');
        }else{
            $salary=HrMonthlySalary::where($date)->where('as_id',$id)->pluck('month');
        }
        $monthName = '';
        foreach ($salary as $key => $value) {
            $monthName .= date("F", mktime(0, 0, 0, $value, 1)).' ';
        }
        return $monthName;
    }


    public function hrSearchEmpInfo(Request $request){
        try {

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);


            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);
            $data=[];
            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            if(isset($request->subsection) || isset($request1['subsection'])) {
                $request2['as_subsection_id'] = isset($request->subsection)?$request->subsection:$request1['subsection'];
                $data['subsection'] = Subsection::where('hr_subsec_id',$request2['as_subsection_id'])->first();
            }

            if(isset($request->section) || isset($request1['section'])) {
                $request2['as_section_id'] = isset($request->section)?$request->section:$request1['section'];
                $data['section'] = Section::where('hr_section_id',$request2['as_section_id'])->first();
            }

            if(isset($request->floor) || isset($request1['floor'])) {
                $request2['as_floor_id'] = isset($request->floor)?$request->floor:$request1['floor'];
                $data['floor'] = Floor::where('hr_floor_id',$request2['as_floor_id'])->first();
            }

            if(isset($request->area) || isset($request1['area'])) {
                $request2['as_area_id'] = isset($request->area)?$request->area:$request1['area'];
                $data['area'] = Area::where('hr_area_id',$request2['as_area_id'])->first();
            }

            if(isset($request->department) || isset($request1['department'])) {
                $request2['as_department_id'] = isset($request->department)?$request->department:$request1['department'];
                $data['department'] = Department::where('hr_department_id',$request2['as_department_id'])->first();
            }

            if(isset($request->unit) || isset($request1['unit'])) {
                $request2['as_unit_id'] = isset($request->unit)?$request->unit:$request1['unit'];
                $data['unit'] = Unit::where('hr_unit_id',$request2['as_unit_id'])->first();
            }
            
            $id=$request['emp'];
            $info=Employee::where('associate_id',$id)->first();
            $department = Department::where('hr_department_id',$info->as_department_id)->first()->hr_department_name??'';

            if(isset($date['from'])){
                $from=$date['from'];
                $to=$date['to'];
                $salary=HrMonthlySalary::whereBetween(DB::raw("CONCAT(month, '-', year)"),[$from,$to])->where('as_id',$id)->get();;
            }else{
                $salary=HrMonthlySalary::where($date)->where('as_id',$id)->get();
            }
               
            
            


            $result['page'] = view('hr.search.salary.employee',
                compact('department','info','salary','data','request1','request2','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrSalarySearchPrintPage(Request $request)
    {
        //dd($request->all());
        $info = [];
        if(isset($request->subsection)) {
            $info['subsection'] = Subsection::where('hr_subsec_id',$request->subsection)->first()->hr_subsection_name??'';
        }

        if(isset($request->section)) {
            $info['section'] = Section::where('hr_section_id',$request->section)->first()->hr_section_name??'';
        }

        if(isset($request->floor)) {
            $info['floor'] = Floor::where('hr_floor_id',$request->floor)->first()->hr_floor_name??'';
        }

        if(isset($request->area)) {
            $info['area'] = Area::where('hr_area_id',$request->area)->first()->hr_area_name??'';
        }

        if(isset($request->department)) {
            $info['department'] = Department::where('hr_department_id',$request->department)->first()->hr_department_name??'';
        }

        if(isset($request->unit)) {
            $info['unit'] = Unit::where('hr_unit_id',$request->unit)->first()->hr_unit_name??'';
        }
        $type = $request->type;
        if($type == 'Total'){
            $data = new stdClass();
            $data->emp = $request->emp;
            $data->ot = $request->ot;
            $data->salary = $request->salary;
            $data->total = $request->total;
        }else{
            $data = $request->data;
        }

        $title = $request->title;
        return view('hr.search.salary.printpages',compact('data','title','info','type'))->render();
    }
}
