<?php

namespace App\Http\Controllers\Hr\Search;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\HrMonthlySalary;
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

class OTSearchController extends Controller
{
    public function hrOTSearch(Request $request)
    {
        try{

            return $this->hrOTSearchGlobal($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

     public function getSearchType($request)
    {
        if($request['type'] == 'date') {
            $date = $request['date'];
        }else if($request['type'] == 'yesterday') {
            $date =  date('Y-m-d', strtotime('-1 day'));
        }else if($request['type'] == 'month') {
            $date = [
            'month' => date('n', strtotime($request['month'])),
            'year' => date('Y', strtotime($request['month']))
           ];        
        }
        return $date;
    }

   
    public function pageTitle($request)
    {

            $showTitle = 'Over Time'.' - '.ucwords($request['type']) ;
            if($request['type']=='date')
            {
                $showTitle =$showTitle.': '.$request['date'];
            }
            if(isset($request['month']))
            {
                $showTitle =$showTitle.': '.$request['month'];
            }

            return $showTitle;
    }




    public function getTableName($unit)
    {
        $tableName = "";
        //CEIL
        if($unit == 2){
            $tableName= "hr_attendance_ceil AS a";
        }
        //AQl
        else if($unit == 3){
            $tableName= "hr_attendance_aql AS a";
        }
        // MBM
        else if($unit == 1 || $unit == 4 || $unit == 5 || $unit == 9){
            $tableName= "hr_attendance_mbm AS a";
        }
        // CEW
        else if($unit == 8){
            $tableName= "hr_attendance_cew AS a";
        }
        else{
            $tableName= "hr_attendance_mbm AS a";
        }
        return $tableName;
    }

    public function calculateOt($unitId,$date,$where = [])
    {

        $tableData_list = [];
        $tableName      = $this->getTableName($unitId);
        $otData = DB::table($tableName)
                    ->select(
                        DB::raw('sum(a.ot_hour) AS ot_hour'),
                        DB::raw('count(distinct a.as_id) AS employee')
                    )
                    ->where(function($query) use ($date,$unitId) {
                        if(isset($date['month'])){
                            $query->whereMonth('a.in_time', $date['month']);
                            $query->whereYear('a.in_time', $date['year']);
                        }else{
                            $query->where('a.in_time', '>=', $date." 00:00:00");
                            $query->where('a.in_time', '<=', $date." 23:59:59");     
                        }
                        $query->where('b.as_unit_id', $unitId);
                    })
                    ->where('a.ot_hour','>',0)
                    ->where('b.as_ot',1)
                    ->Join('hr_as_basic_info AS b','b.as_id','a.as_id')
                    ->where(function($condition) use ($where){
                        if (!empty($where['unit'])) {
                            $condition->where('b.as_unit_id', $where['unit']);
                        }
                        if (!empty($where['areaid'])) {
                            $condition->where('b.as_area_id', $where['areaid']);
                        }
                        if (!empty($where['departmentid'])) {
                            $condition->where('b.as_department_id', $where['departmentid']);
                        }
                        if (!empty($where['floorid'])) {
                            $condition->where('b.as_floor_id', $where['floorid']);
                        }
                        if (!empty($where['section'])) {
                            $condition->where('b.as_section_id', $where['section']);
                        }
                        if (!empty($where['subsection'])) {
                            $condition->where('b.as_subsection_id', $where['subsection']);
                        }
                        if (!empty($where['shift_code'])) {
                            $condition->where('a.hr_shift_code', $where['shift_code']);
                        }
                        if (!empty($where['hour'])) {
                            $condition->where($where['hour']);
                        }
                    })->first();

        return $otData;
    }

    public function getAttOtShift($unitId,$date,$where = [])
    {
        $tableData_list = [];
        $tableName      = $this->getTableName($unitId);
        $tableData_list = DB::table($tableName)
                        ->select("a.hr_shift_code")
                        ->whereNotNull('b.as_shift_id')
                        ->where(function($query) use ($where, $date) {
                            if (!empty($where['associate_id'])) {
                                $query->where('b.associate_id', '=', $where['associate_id']);
                            }
                            $query->where('a.in_time', '>=', $date." 00:00:00");
                            $query->where('a.in_time', '<=', $date." 23:59:59");
                        })
                        ->join("hr_as_basic_info AS b", function($join){
                            $join->on( "b.as_id", "=", "a.as_id");
                        })
                        ->where('b.as_ot',1)
                        ->when(!empty($where['areaid']), function ($query) use($where){
                               return $query->where('b.as_area_id',$where['areaid']);
                               })
                        ->when(!empty($where['departmentid']), function ($query) use($where){
                              return $query->where('b.as_department_id',$where['departmentid']);
                              })
                        ->when(!empty($where['unit']), function ($query) use($where){
                              return $query->where('b.as_unit_id',$where['unit']);
                              })
                        ->where(function($condition) use ($where){
                            if (!empty($where['floorid'])) {
                                $condition->where('b.as_floor_id', $where['floorid']);
                            }
                            if (!empty($where['lineid'])) {
                                $condition->where('b.as_line_id', $where['lineid']);
                            }
                            if (!empty($where['section'])) {
                                $condition->where('b.as_section_id', $where['section']);
                            }
                            if (!empty($where['subsection'])) {
                                $condition->where('b.as_subsection_id', $where['subsection']);
                            }
                        })
                        ->groupBy('a.hr_shift_code')
                        ->get();
        return $tableData_list;
    }

    public function hrOTSearchGlobal($request)
    {
        try {
            $date = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);

            unset($request['view']);
            unset($request['unit'],$request['area'],$request['department'],$request['floor'],$request['section'],$request['subsection']);
            $unit_list      = Unit::get();
            $result = [];

            $employee = 0;
            if(isset($date['month'])){
                $otTotal = 0;
                foreach($unit_list as $k=>$val) {
                    $thisData = $this->calculateOt($val->hr_unit_id,$date,[]);
                    $otTotal +=$thisData->ot_hour;
                    $employee += $thisData->employee;
                }

                $result['page'] = view('hr.search.ot.monthly',
                    compact('employee','unit_list', 'showTitle', 'request','otTotal'))->render();

            }else{
                $tableOt_list=[];
                $tableOt_list1=[];
                $tableOt_list2=[];
                $date1=date('Y-m-d', strtotime($date .' -1 day'));
                $date2=date('Y-m-d', strtotime($date .' -2 day'));
                
                foreach($unit_list as $k=>$val) {
                    $thisData = $this->calculateOt($val->hr_unit_id,$date,[]);
                    $tableOt_list[$val->hr_unit_id] =$thisData->ot_hour;
                    $employee += $thisData->employee;
                    $tableOt_list1[$val->hr_unit_id] =$this->calculateOt($val->hr_unit_id,$date1,[])->ot_hour;
                    $tableOt_list2[$val->hr_unit_id] =$this->calculateOt($val->hr_unit_id,$date2,[])->ot_hour;
                }

                $ot_data =[
                    'dayot' => array_sum($tableOt_list),
                    'day' =>$date,
                    'dayot1' => array_sum($tableOt_list1),
                    'day1' => $date1,
                    'dayot2' => array_sum($tableOt_list2),
                    'day2' => $date2
                ];
                
                
                $result['page'] = view('hr.search.ot.allot',
                    compact('employee','unit_list', 'showTitle', 'request','ot_data'))->render();
            }
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOTSearchShift(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['view'] = 'shift';
            $showTitle = $this->pageTitle($request1);
            $where=[];
            //return $request;
            if(isset($request->subsection) || isset($request1['subsection'])) {
                $request1['subsection'] = isset($request->subsection)?$request->subsection:$request1['subsection'];
                $where['subsection']=$request1['subsection'];
                $data['subsection'] = Subsection::where(['hr_subsec_id' => $request1['subsection']])->first();
            }

            if(isset($request->section) || isset($request1['section'])) {
                $request1['section'] = isset($request->section)?$request->section:$request1['section'];
                $where['section']=$request1['section'];
                $data['section'] = Section::where(['hr_section_id' => $request1['section']])->first();
            }

            if(isset($request->floor) || isset($request1['floor'])) {
                $request1['floor'] = isset($request->floor)?$request->floor:$request1['floor'];
                $where['floorid']=$request1['floor'];
                $data['floor'] = Floor::where(['hr_floor_id' => $request1['floor']])->first();
            }

            if(isset($request->area) || isset($request1['area'])) {
                $request1['area'] = isset($request->area)?$request->area:$request1['area'];
                $where['areaid']=$request1['area'];
                $data['area'] = Area::where(['hr_area_id' => $request1['area']])->first();
            }

            if(isset($request->department) || isset($request1['department'])) {
                $request1['department'] = isset($request->department)?$request->department:$request1['department'];
                $where['departmentid']=$request1['department'];
                $data['department'] = Department::where(['hr_department_id' => $request1['department'] ])->first();
            }
            if(isset($request->unit) || isset($request1['unit'])) {
                $request1['unit'] = isset($request->unit)?$request->unit:$request1['unit'];
                $where['unit']=$request1['unit'];
                $data['unit'] = Unit::where(['hr_unit_id' => $request1['unit']])->first();
            }

            
            $date = $this->getSearchType($request1);
            $date1=date('Y-m-d', strtotime($date .' -1 day'));
            $date2=date('Y-m-d', strtotime($date .' -2 day'));

            $unitShifts  = DB::table('hr_shift')->select('hr_shift_code')->where('hr_shift_unit_id', $request1['unit'])->get();
            $attOtShift =$this->getAttOtShift($request1['unit'],$date,$where);
            $shiftCollection=$attOtShift->toBase()->merge($unitShifts);
            $shifts=$shiftCollection->unique()->values()->all();


            $ot_data=[];
            $ot=0;$ot1=0;$ot2=0;
            foreach($shifts as $k=>$shift) {

                $where['shift_code']=$shift->hr_shift_code;
                $thisData = $this->calculateOt($request1['unit'],$date,$where);
                $ot =$thisData->ot_hour??0;
                $ot1 =$this->calculateOt($request1['unit'],$date1,$where)->ot_hour??0;
                $ot2 =$this->calculateOt($request1['unit'],$date2,$where)->ot_hour??0;

                $shiftinfo=Shift::where('hr_shift_code', $shift->hr_shift_code)->first();

                $ot_data[$shift->hr_shift_code]=[
                               'name' => $shiftinfo->hr_shift_name,
                               'count' => $thisData->employee,
                               'day' => $date,
                               'day1' => $date1,
                               'day2' => $date2,
                               'dayot' => $ot,
                               'dayot1' => $ot1,
                               'dayot2' => $ot2
                            ];
                
            }
            $result = [];
            $result['page'] = view('hr.search.ot.shiftwise',
                compact('showTitle', 'request','ot_data','request1','data'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }
    public function hrOtListData($unitId,$date,$where = []){
        $tableName = $this->getTableName($unitId);
        $tableData = DB::table($tableName)
                    ->select(
                        DB::raw('sum(a.ot_hour) as ot_hour'),
                        'a.as_id',
                        'b.as_name',
                        'b.associate_id',
                        'd.hr_designation_name',
                        'f.hr_floor_name',
                        'u.hr_unit_name'
                    )
                    ->where(function($query) use ($date,$unitId) {
                        if(isset($date['month'])){
                            $query->whereMonth('a.in_time', $date['month']);
                            $query->whereYear('a.in_time', $date['year']);
                        }else{
                            $query->where('a.in_time', '>=', $date." 00:00:00");
                            $query->where('a.in_time', '<=', $date." 23:59:59");     
                        }
                        $query->where('b.as_unit_id', $unitId);
                    })
                    ->where('a.ot_hour','>',0)
                    ->where('b.as_ot',1)
                    ->Join('hr_as_basic_info AS b','b.as_id','a.as_id')
                    ->leftJoin('hr_unit as u','u.hr_unit_id','b.as_unit_id')
                    ->leftJoin('hr_floor as f','f.hr_floor_id','b.as_floor_id')
                    ->leftJoin('hr_designation as d','d.hr_designation_id','b.as_designation_id')
                    ->where(function($condition) use ($where){
                        if (!empty($where['unit'])) {
                            $condition->where('b.as_unit_id', $where['unit']);
                        }
                        if (!empty($where['areaid'])) {
                            $condition->where('b.as_area_id', $where['areaid']);
                        }
                        if (!empty($where['departmentid'])) {
                            $condition->where('b.as_department_id', $where['departmentid']);
                        }
                        if (!empty($where['floorid'])) {
                            $condition->where('b.as_floor_id', $where['floorid']);
                        }
                        if (!empty($where['section'])) {
                            $condition->where('b.as_section_id', $where['section']);
                        }
                        if (!empty($where['subsection'])) {
                            $condition->where('b.as_subsection_id', $where['subsection']);
                        }
                        if (!empty($where['shift_code'])) {
                            $condition->where('a.hr_shift_code', $where['shift_code']);
                        }
                        if (!empty($where['hour'])) {
                            $condition->where(DB::raw('cast(a.ot_hour as int)'), '>=', $where['hour']);
                            $condition->where(DB::raw('cast(a.ot_hour as int)'), '<', ($where['hour']+1));
                        }
                    })
                    ->groupBy('a.as_id')
                    ->get();
        return  $tableData;
    }


    public function hrOTSearchHour(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $showTitle = $this->pageTitle($request1);
            $request1['view'] = 'hour';

            $c1=0;$c2=0;$c3=0;$c4=0;$c5=0;$c6=0;$c7=0;$c8=0;$c9=0;
            $date = $this->getSearchType($request1);
            $data = [];

            if(isset($request1['unit']) || isset($request->shiftcode)){

                $request1['shiftcode'] = $request->shiftcode;
                $unitId=$request1['unit'];
                /*unset($request['unit'],$request['area'],$request['department'],$request['floor'],$request['section'],$request['subsection']);*/
                $where=[];
                $where=[
                    'shift_code' => $request1['shiftcode'],
                    'unit' => $request1['unit']
                ];
                if(isset($request->shiftcode) || isset($request1['shiftcode'])) {
                    $request1['shiftcode'] = isset($request->shiftcode)?$request->shiftcode:$request1['shiftcode'];
                    $where['shift_code']=$request1['shiftcode'];
                    
                }
                if(isset($request->subsection) || isset($request1['subsection'])) {
                    $request1['subsection'] = isset($request->subsection)?$request->subsection:$request1['subsection'];
                    $where['subsection']=$request1['subsection'];
                    $data['subsection'] = Subsection::where('hr_subsec_id',$request1['subsection'])->first();
                }

                if(isset($request->section) || isset($request1['section'])) {
                    $request1['section'] = isset($request->section)?$request->section:$request1['section'];
                    $where['section']=$request1['section'];
                    $data['section'] = Section::where('hr_section_id',$request1['section'])->first();
                }

                if(isset($request->floor) || isset($request1['floor'])) {
                    $request1['floor'] = isset($request->floor)?$request->floor:$request1['floor'];
                    $where['floorid']=$request1['floor'];
                    $data['floor'] = Floor::where('hr_floor_id',$request1['floor'])->first();
                }

                if(isset($request->area) || isset($request1['area'])) {
                    $request1['area'] = isset($request->area)?$request->area:$request1['area'];
                    $where['areaid']=$request1['area'];
                    $data['area'] = Area::where('hr_area_id',$request1['area'])->first();
                }

                if(isset($request->department) || isset($request1['department'])) {
                    $request1['department'] = isset($request->department)?$request->department:$request1['department'];
                    $where['departmentid']=$request1['department'];
                    $data['department'] = Department::where('hr_department_id', $request1['department'])->first();
                }
                


                $data['unit'] = Unit::where('hr_unit_id',$request1['unit'])->first();
                $data['shift'] = Shift::where('hr_shift_code',$request1['shiftcode'])->first();
                $tableData = $this->hrOtListData($request1['unit'],$date,$where);
                

                
                if(!$tableData->isEmpty()) {
                    foreach ($tableData as $key => $ot) {
                        # code...
                        if($ot->ot_hour>=1 && $ot->ot_hour<2){$c1++;}
                        if($ot->ot_hour>=2 && $ot->ot_hour<3){$c2++;}
                        if($ot->ot_hour>=3 && $ot->ot_hour<4){$c3++;}
                        if($ot->ot_hour>=4 && $ot->ot_hour<5){$c4++;}
                        if($ot->ot_hour>=5 && $ot->ot_hour<6){$c5++;}
                        if($ot->ot_hour>=6 && $ot->ot_hour<7){$c6++;}
                        if($ot->ot_hour>=7 && $ot->ot_hour<8){$c7++;}
                        if($ot->ot_hour>=8){$c8++;}
                    }
                }
            }else{
                unset($request1['unit'],$request1['area'],$request1['department'],$request1['floor'],$request1['section'],$request1['subsection'],$request1['hour']);
                //employee count for all ot
                $unit_list = Unit::get();
                foreach($unit_list as $k=>$val) {
                    $unitData = $this->hrOtListData($val->hr_unit_id,$date,[]);
                    if(!$unitData->isEmpty()) {
                        foreach ($unitData as $key => $ot) {
                            # code...
                            if($ot->ot_hour>=1 && $ot->ot_hour<2){$c1++;}
                            if($ot->ot_hour>=2 && $ot->ot_hour<3){$c2++;}
                            if($ot->ot_hour>=3 && $ot->ot_hour<4){$c3++;}
                            if($ot->ot_hour>=4 && $ot->ot_hour<5){$c4++;}
                            if($ot->ot_hour>=5 && $ot->ot_hour<6){$c5++;}
                            if($ot->ot_hour>=6 && $ot->ot_hour<7){$c6++;}
                            if($ot->ot_hour>=7 && $ot->ot_hour<8){$c7++;}
                            if($ot->ot_hour>=8){$c8++;}
                        }
                    }
                }

            }

            $ot_data=[
                '1' => $c1,
                '2' => $c2,
                '3' => $c3,
                '4' => $c4,
                '5' => $c5,
                '6' => $c6,
                '7' => $c7,
                '8' => $c8
            ];
            //return $ot_data;
            $result = [];
            $result['page'] = view('hr.search.ot.hourly', compact( 'showTitle', 'request','ot_data','data','request1'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }

    public function hrOTSearchUnit(Request $request)
    {
        try {
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $request['view'] = 'allunit';
            $date = $this->getSearchType($request);
            $showTitle = $this->pageTitle($request);
            unset($request['unit'],$request['area'],$request['department'],$request['floor'],$request['section'],$request['subsection'],$request['hour']);

            $unit_list = Unit::get();

            $unit_data=[];
            $where=[];
            foreach ($unit_list as $key => $unit) {
                $where=[
                    'as_unit_id'=>$unit->hr_unit_id
                ];
                $thisData = $this->calculateOt($unit->hr_unit_id,$date,['unit' => $unit->hr_unit_id]);
                //$employee = Employee::where($where)->whereNotNull('as_shift_id')->count();
                $unit_data[$key]=[
                    'id'=> $unit->hr_unit_id,
                    'name'=> $unit->hr_unit_name,
                    'employee' => $thisData->employee,
                    'ot_hour'=> $thisData->ot_hour??0
                ];
            }

            $area_count = Area::count();
            $result = [];
            $result['page'] = view('hr.search.ot.allunit',
                compact('unit_data', 'request', 'area_count', 'showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOTSearchArea(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['unit'] = $request->unit;
            unset($request1['area'],$request1['department'],$request1['floor'],$request1['section'],$request1['subsection'],$request1['view'],$request['hour']);

            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where('hr_unit_id',$request1['unit'])->first();
            
            $area_list = Area::get();

            $area_data=[];
            $where=[];
            foreach ($area_list as $key => $area) {
                $where=[
                    'unit' => $request1['unit'],
                    'areaid' => $area->hr_area_id
                ];
                $thisData = $this->calculateOt($unit->hr_unit_id,$date,$where);
                $dept=  Department::where([
                            'hr_department_area_id'=> $area->hr_area_id
                        ])
                        ->count();


                $area_data[$key]=[
                    'id'=> $area->hr_area_id,
                    'dept'=> $dept,
                    'name'=> $area->hr_area_name,
                    'employee' => $thisData->employee,
                    'ot_hour'=> $thisData->ot_hour??0
                ];
            }
             //return $area_data;
            $result = [];
            $result['page'] = view('hr.search.ot.allarea',
                compact('area_data','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOTSearchDepartment(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['area'] = $request->area;

            $date = $this->getSearchType($request1);
            $showTitle = $this->pageTitle($request1);

            unset($request1['department'],$request1['floor'],$request1['section'],$request1['subsection'],$request1['view'],$request['hour']);

            /*-----get names------*/
            $unit = Unit::where('hr_unit_id',$request1['unit'])->first();
            $area = Area::where('hr_area_id', $request1['area'])->first();

            $department_list =  Department::where([
                                    'hr_department_area_id' => $request1['area']
                                    ])
                                ->get();


            $department_data=[];
            $where=[];
            foreach ($department_list as $key => $department) {
                $where = ['unit' => $unit->hr_unit_id, 'areaid' => $area->hr_area_id, 'departmentid' => $department->hr_department_id];
                $thisData = $this->calculateOt($unit->hr_unit_id,$date,$where);
                $department_data[$key]=[
                    'id'=> $department->hr_department_id,
                    'name'=> $department->hr_department_name,
                    'employee' => $thisData->employee,
                    'ot_hour'=> $thisData->ot_hour??0
                ];
            }
            $floor_count = Floor::where([
                            'hr_floor_unit_id' => $request1['unit'],
                            ])
                        ->count();
            $result = [];
            $result['page'] = view('hr.search.ot.alldepartment',
                compact('department_data','area','request1','unit','showTitle','floor_count'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOTSearchFloor(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['department'] = $request->department;

            $showTitle = $this->pageTitle($request1);

            unset($request1['floor'],$request1['section'],$request1['subsection'],$request1['view'],$request['hour']);

            /*-----get names------*/
            $unit = Unit::where('hr_unit_id',$request1['unit'])->first();
            $area = Area::where('hr_area_id', $request1['area'])->first();
            $department =   Department::where('hr_department_id',$request1['department'])
                            ->first();

            $floor_list =   Floor::where([
                                'hr_floor_unit_id' => $request1['unit']
                                ])
                            ->get();

            $date = $this->getSearchType($request1);
            $floor_data=[];
            $where=[];
            foreach ($floor_list as $key => $floor) {

                $where = [
                    'unit' => $unit->hr_unit_id, 
                    'areaid' => $area->hr_area_id, 
                    'departmentid' => $department->hr_department_id, 
                    'floorid' => $floor->hr_floor_id
                  ];
                $thisData = $this->calculateOt($unit->hr_unit_id,$date,$where);
                $floor_data[$key]=[
                    'id'=> $floor->hr_floor_id,
                    'name'=> $floor->hr_floor_name,
                    'employee' => $thisData->employee,
                    'ot_hour'=> $thisData->ot_hour??0
                ];
            }

            $section_count = Section::where([
                        'hr_section_area_id' => $request1['area'],
                        'hr_section_department_id' => $request1['department']
                        ])
                    ->count();

            $result = [];
            $result['page'] = view('hr.search.ot.allfloor',
                compact('floor_data','area','department','request1','unit','showTitle','section_count'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOTSearchSection(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['floor'] = $request->floor;


            //return $request1;

            $showTitle = $this->pageTitle($request1);

            unset($request1['section'],$request1['subsection'],$request1['view'],$request['hour']);

            /*-----get names------*/
            $unit = Unit::where('hr_unit_id',$request1['unit'])->first();
            $area = Area::where('hr_area_id', $request1['area'])->first();
            $department =   Department::where('hr_department_id',$request1['department'])
                            ->first();
            $floor= Floor::where('hr_floor_id',$request1['floor'])->first();
            $section_list = Section::where(['hr_section_area_id' => $request1['area'],'hr_section_department_id' => $request1['department']])->get();

            $date = $this->getSearchType($request1);
            $section_data=[];
            $where=[];
            foreach ($section_list as $key => $section) {

                $subsection = Subsection::where([
                                'hr_subsec_area_id' => $request1['area'],
                                'hr_subsec_department_id' => $request1['department'],
                                'hr_subsec_section_id' =>$section->hr_section_id
                                ])
                            ->count();
                $where = [
                    'unit' => $unit->hr_unit_id, 
                    'areaid' => $area->hr_area_id, 
                    'departmentid' => $department->hr_department_id, 
                    'floorid' => $floor->hr_floor_id, 
                    'section' => $section->hr_section_id
                  ];
                $thisData = $this->calculateOt($unit->hr_unit_id,$date,$where);
                $section_data[$key]=[
                    'id'=> $section->hr_section_id,
                    'subsection'=> $subsection,
                    'name'=> $section->hr_section_name,
                    'employee' => $thisData->employee,
                    'ot_hour'=> $thisData->ot_hour??0
                ];
            }

            $result = [];
            $result['page'] = view('hr.search.ot.allsection',
                compact('section_data','area','department','request1','unit','floor','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOTSearchSubscetion(Request $request)
    {
        try {

            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['section'] = $request->section;

            $showTitle = $this->pageTitle($request1);

            unset($request1['subsection'],$request1['view'],$request['hour']);
            /*-----get names------*/
            $unit = Unit::where('hr_unit_id',$request1['unit'])->first();
            $area = Area::where('hr_area_id', $request1['area'])->first();
            $department =   Department::where('hr_department_id',$request1['department'])
                            ->first();
            $floor= Floor::where('hr_floor_id',$request1['floor'])->first();
            $section =  Section::where('hr_section_id',$request1['section'])->first();

            $subsection_list = Subsection::where([
                                'hr_subsec_area_id' => $request1['area'],
                                'hr_subsec_department_id' => $request1['department'],
                                'hr_subsec_section_id' =>$request1['section']
                                ])
                            ->get();
            //return $subsection_list;
            $date = $this->getSearchType($request1);
            $subsection_data=[];
            $where=[];
            foreach ($subsection_list as $key => $subsection) {

                $where = [
                    'unit' => $unit->hr_unit_id, 
                    'areaid' => $area->hr_area_id, 
                    'departmentid' => $department->hr_department_id, 
                    'floorid' => $floor->hr_floor_id, 
                    'section' => $section->hr_section_id, 
                    'subsection' => $subsection->hr_subsec_id
                ];
                $thisData = $this->calculateOt($unit->hr_unit_id,$date,$where);
                $subsection_data[$key]=[
                    'id'=> $subsection->hr_subsec_id,
                    'name'=> $subsection->hr_subsec_name,
                    'employee' => $thisData->employee,
                    'ot_hour'=> $thisData->ot_hour??0
                ];
            }

            $result = [];
            $result['page'] = view('hr.search.ot.allsubsection',
                compact('subsection_data','area','department','request1','unit','section','floor','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrOTSearchEmployee(Request $request)
    {
        try {
            // get previous url params
            $request2 = [];
            $data=[];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            //return $request;

             $request1['view']='employee';
            //return $request1;
            if(isset($request->shiftcode)){
                $request1['shiftcode']= $request->shiftcode;
            }
            if(isset($request->hour)){
                $request1['hour']= $request->hour;
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
            //return $EmpData;
            $result['page'] = view('hr.search.ot.allemployee',
                compact('data','request1','request2','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function numberToTime($number){
        $number = round($number,1);
        $hour = explode(".", $number);
        if(isset($hour[1])){
            return $hour[0].':'.round($hour[1]*6);   
        }else
            return $hour[0];
    }

    public function  hrOtSearchListEmployee(Request $request){
        try {

            $request2 = [];
            $parts = parse_url(url()->previous());
            $where=[];
            parse_str($parts['query'], $request1);
            //dd($request1);
            if(isset($request->unit)){
                $where['unit'] = $request->unit;
            }
            if(isset($request->area)){
                $where['areaid'] = $request->area;
            }
            if(isset($request->department)){
                $where['departmentid'] = $request->department;
            }
            if(isset($request->floor)){
                $where['floorid'] = $request->floor;
            }
            if(isset($request->section)){
                $where['section'] = $request->section;
            }
            if(isset($request->subsection)){
                $where['subsection'] = $request->subsection;
            }
            if(isset($request->hour)){
                $where['hour'] = $request->hour;
            }
            if(isset($request->shiftcode)){
                $where['shift_code'] = $request->shiftcode;
            }

            $date = $this->getSearchType($request1);

            $empList = [];
            if(isset($request->unit)){
                $empList = $this->hrOtListData($request->unit,$date,$where);
            }else{
                $unit_list = Unit::get();
                foreach($unit_list as $k=>$val) {
                    $toMerge = $this->hrOtListData($val->hr_unit_id,$date,$where)->toArray();
                    $empList = array_merge($empList,$toMerge); 
                }            
            }
            //hr/reports/job_card?associate=13H800024P&month=March&year=2019

            //dd($empList);
            return DataTables::of($empList)->addIndexColumn()
                    ->editColumn('associate_id', function ($empList) use($request1){
                        if(isset($request1['month'])){
                            $yearMonth = date('Y-m', strtotime($request1['month']));
                            
                            $link = HtmlFacade::link(url('hr/operation/job_card?associate=').$empList->associate_id.'&month='.$yearMonth,$empList->associate_id);
                        }else{

                            $link = HtmlFacade::link(url('hr/recruitment/employee/show').'/'.$empList->associate_id,$empList->associate_id);
                        }

                        return $link;
                    })
                    ->editColumn('ot_hour', function($empList){
                        return $this->numberToTime($empList->ot_hour).' Hour';
                    })
                    ->rawColumns(['associate_id'])
                    ->make(true);


            //return dd($orderinfo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }

    }


    public function hrOtSearchPrintPage(Request $request)
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
        if($type == 'Month'){
            $data['emp'] = $request->emp;
            $data['ot'] = $request->ot;
        }else{
            
            $data = $request->data;
        }
     
        $title = $request->title;
        return view('hr.search.ot.printpage',compact('data','title','info','type'))->render();
    }
}
