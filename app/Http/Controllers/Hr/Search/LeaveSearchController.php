<?php

namespace App\Http\Controllers\Hr\Search;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\Department;
use App\Models\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\Leave;
use App\Models\Hr\Section;
use App\Models\Hr\Shift;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use Carbon\Carbon;
use Collective\Html\HtmlFacade;
use Illuminate\Http\Request;
use Validator, Auth, ACL, DB, DataTables;

class LeaveSearchController extends Controller
{ 
    public function hrLeaveSearch(Request $request)
    {
        try{
            return $this->searchGetAttendancedReport_global($request->all());
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pageTitle($request)
    {
        $showTitle = ucwords($request['category']).' - '.ucwords($request['type']) ;
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
            $date['from'] = $request['date'];
            $date['to']   = $request['date'];
        }else if($request['type'] == 'month') {
            $year = date('Y', strtotime($request['month']));
            $monthNumber = date('n', strtotime($request['month']));
            $date = [
                'month' => $monthNumber,
                'year' => $year
           ];
        }else if ($request['type'] == 'range') {
            $date=[
                'from'=>$request['rangeFrom'],
                'to' => $request['rangeTo']
            ];
        }else if($request['type'] == 'year') {
            $year = $request['year'];
            $date=[
                'year'=>$year
            ];
        }else{
            $date=[
                'year' => date('Y')
            ];
        }
        return $date;
    }

    public function getLeaveFromDate1($date,$type,$where)
    {
        if($type == 'date') {
            $leave_type_list = DB::table('hr_leave AS a')
            ->where('a.leave_from', '<=', $date['from'])
            ->where('a.leave_to', '>=', $date['to'])
            ->where('a.leave_status', '=', 1)
            ->join('hr_as_basic_info AS b', function($join) use ($where){
                $join->on('b.associate_id', '=', 'a.leave_ass_id');
                $join->where('b.as_status', '1');
                if(!empty($where['unit'])) {
                    $join->where('b.as_unit_id', $where['unit']);
                }
                if(!empty($where['areaid'])) {
                    $join->where('b.as_area_id', $where['areaid']);
                }
                if(!empty($where['departmentid'])) {
                    $join->where('b.as_department_id', $where['departmentid']);
                }
                if(!empty($where['floorid'])) {
                    $join->where('b.as_floor_id', $where['floorid']);
                }
                if(!empty($where['lineid'])) {
                    $join->where('b.as_line_id', $where['lineid']);
                }
                if(!empty($where['section'])) {
                    $join->where('b.as_section_id', $where['section']);
                }
                if(!empty($where['subsection'])) {
                    $join->where('b.as_subsection_id', $where['subsection']);
                }
            })
            ->get();
            $leave_type_list = $leave_type_list->groupBy('leave_type')->toArray();
        } else if($type == 'month') {
            $leave_type_list = DB::table('hr_leave AS a')
            ->whereYear('leave_from', '>=', $date['year'])
            ->whereMonth('leave_from', '>=', $date['month'])
            ->whereYear('leave_to', '<=', $date['year'])
            ->whereMonth('leave_to', '<=', $date['month'])
            ->where('leave_status', '=', 1)
            ->join('hr_as_basic_info AS b', function($join) use ($where){
                $join->on('b.associate_id', '=', 'a.leave_ass_id');
                $join->where('b.as_status', '1');
                if(!empty($where['unit'])) {
                    $join->where('b.as_unit_id', $where['unit']);
                }
                if(!empty($where['areaid'])) {
                    $join->where('b.as_area_id', $where['areaid']);
                }
                if(!empty($where['departmentid'])) {
                    $join->where('b.as_department_id', $where['departmentid']);
                }
                if(!empty($where['floorid'])) {
                    $join->where('b.as_floor_id', $where['floorid']);
                }
                if(!empty($where['lineid'])) {
                    $join->where('b.as_line_id', $where['lineid']);
                }
                if(!empty($where['section'])) {
                    $join->where('b.as_section_id', $where['section']);
                }
                if(!empty($where['subsection'])) {
                    $join->where('b.as_subsection_id', $where['subsection']);
                }
            })
            // ->groupBy('a.leave_ass_id')
            ->get();
            $leave_type_list = $leave_type_list->groupBy('leave_type')->toArray();
        } else if($type == 'range') {

        } else if($type == 'year') {
            for($month = 1; $month<=12; $month++){
                $monthName = date("F", mktime(0, 0, 0, $month, 10));
                $leave_type_list1 = DB::table('hr_leave AS a')
                    ->whereYear('leave_from', '>=', $date['year'])
                    ->whereMonth('leave_from', '>=', $month)
                    ->whereYear('leave_to', '<=', $date['year'])
                    ->whereMonth('leave_to', '<=', $month)
                    ->where('leave_status', '=', 1)
                    ->join('hr_as_basic_info AS b', function($join) use ($where){
                        $join->on('b.associate_id', '=', 'a.leave_ass_id');
                        $join->where('b.as_status', '1');
                        if(!empty($where['unit'])) {
                            $join->where('b.as_unit_id', $where['unit']);
                        }
                        if(!empty($where['areaid'])) {
                            $join->where('b.as_area_id', $where['areaid']);
                        }
                        if(!empty($where['departmentid'])) {
                            $join->where('b.as_department_id', $where['departmentid']);
                        }
                        if(!empty($where['floorid'])) {
                            $join->where('b.as_floor_id', $where['floorid']);
                        }
                        if(!empty($where['lineid'])) {
                            $join->where('b.as_line_id', $where['lineid']);
                        }
                        if(!empty($where['section'])) {
                            $join->where('b.as_section_id', $where['section']);
                        }
                        if(!empty($where['subsection'])) {
                            $join->where('b.as_subsection_id', $where['subsection']);
                        }
                    })
                    ->groupBy('a.leave_ass_id')
                    ->get();
                $leave_type_list[$date['year']][$monthName] = $leave_type_list1->groupBy('leave_type');
            }
        } else {
            $leave_type_list  = Leave::where(function ($query) use ($date) {
                $query->where('leave_from', '<=', date('Y-m-d'));
                $query->where('leave_to', '>=', date('Y-m-d'));
                $query->where('leave_status', '=', 1);
            })
            ->groupBy('a.leave_ass_id')
            ->get();
            $leave_type_list = $leave_type_list->groupBy('leave_type');
        }
        return $leave_type_list;
    }

    public function getLeaveFromDate($date,$type)
    {
        if($type == 'date') {
            $leave_type_list  = Leave::where(function ($query) use ($date) {
                $query->where('leave_from', '<=', $date['from']);
                $query->where('leave_to', '>=', $date['to']);
                $query->where('leave_status', '=', 1);
            })
            ->groupBy('leave_ass_id')
            ->get();
            $leave_type_list = $leave_type_list->groupBy('leave_type');
        } else if($type == 'month') {
            $leave_type_list  = Leave::where(function ($query) use ($date) {
                $query->whereYear('leave_from', '>=', $date['year']);
                $query->whereMonth('leave_from', '>=', $date['month']);
                $query->whereYear('leave_to', '<=', $date['year']);
                $query->whereMonth('leave_to', '<=', $date['month']);
                $query->where('leave_status', '=', 1);
            })
            ->groupBy('leave_ass_id')
            ->get();
            $leave_type_list = $leave_type_list->groupBy('leave_type');
        } else if($type == 'range') {
            
        } else if($type == 'year') {
            for($month = 1; $month<=12; $month++){
                $monthName = date("F", mktime(0, 0, 0, $month, 10));
                $leave_type_list1  = Leave::where(function ($query) use ($date,$month) {
                    $query->whereYear('leave_from', '>=', $date['year']);
                    $query->whereMonth('leave_from', '>=', $month);
                    $query->whereYear('leave_to', '<=', $date['year']);
                    $query->whereMonth('leave_to', '<=', $month);
                    $query->where('leave_status', '=', 1);
                })
                ->groupBy('leave_ass_id')
                ->get();
                $leave_type_list[$date['year']][$monthName] = $leave_type_list1->groupBy('leave_type');
            }
        } else {
            $leave_type_list  = Leave::where(function ($query) use ($date) {
                $query->where('leave_from', '<=', date('Y-m-d'));
                $query->where('leave_to', '>=', date('Y-m-d'));
                $query->where('leave_status', '=', 1);
            })
            ->get();
            $leave_type_list = $leave_type_list->groupBy('leave_type');
        }
        return $leave_type_list;
    }

    public function searchGetAttendancedReport_global($request)
    {
        try {
            $showTitle = $this->pageTitle($request);
            $date = $this->getSearchType($request);
            // get previous url params
            $parts = parse_url(url()->previous());
            if(isset($parts['query'])){
                parse_str($parts['query'], $request1);
                // $request1['date'] = null;
                if($request1['category'] != $request['category']) {
                    $request['category'] = $request['category'];
                }
                if($request1['type'] != $request['type']) {
                    $request['type'] = $request['type'];
                }
                if(isset($request['date'])){
                    $request2['date'] = $request['date'];
                }
                if(isset($request['month'])){
                    $request2['month'] = $request['month'];
                }
                if(isset($request['year'])){
                    $request2['year'] = $request['year'];
                }
                $request = ['category'=> $request['category'], 'type' => $request['type']];
                $request = array_merge($request2, $request);
            }

            $unit_list      = Unit::where('hr_unit_status',1)->count();
            $employee_list  = Employee::whereIn('as_status',[1,6])->count();
            $leave = $this->getLeaveFromDate1($date, $request['type'],[]);
            $groups = collect($leave);//->groupBy('leave_type',true);
          
            $result = [];
            $result['page'] = view('hr.search.leave.allleave',
                compact('unit_list','employee_list','showTitle','leave', 'groups','request'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLeaveSearchUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $array_slice = 3;
            $date = $this->getSearchType($request);
            if(isset($request['attstatus'])) {
                unset($request['attstatus']);
            }
            if(isset($request['unit'])) {
                unset($request['unit']);
            }
            if($request['type'] == 'date') {
                unset($request['month']);
                unset($request['year']);
            } else if($request['type'] == 'month') {
                unset($request['date']);
                unset($request['year']);
            } else if($request['type'] == 'year') {
                unset($request['date']);
                unset($request['month']);
            }
            if(isset($request['view'])) {
                $array_slice = 4;
            }
            $request    = array_slice($request, 0, $array_slice);
            // return $request;
            $showTitle  = $this->pageTitle($request);
            $unit_list  = Unit::where('hr_unit_status',1)->get();
            $area_count = Area::where('hr_area_status',1)->count();
            foreach($unit_list as $k=>$unit) {
                $where = ['unit'=>$unit->hr_unit_id];
                $unit_leave_wise[$unit->hr_unit_id] = $this->getLeaveFromDate1($date,$request['type'],$where);
            }
            $result = [];
            $result['page'] = view('hr.search.leave.allunit',
                compact('unit_list', 'request', 'unit_leave_wise', 'area_count', 'showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLeaveSearchArea(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $date = $this->getSearchType($request1);
            $request1['unit'] = isset($request->unit)?$request->unit:$request1['unit'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }

            $request1 = array_slice($request1, 0, 4);
            $showTitle = $this->pageTitle($request1);

            // return $request1;
            $unit=Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area_list = Area::where('hr_area_status',1)->get();
            foreach($area_list as $k=>$area) {
                $where = ['unit' => $request1['unit'],'areaid' => $area->hr_area_id];
                $area_leave_wise[$area->hr_area_id] = $this->getLeaveFromDate1($date,$request1['type'],$where);
            }
            $result = [];
            $result['page'] = view('hr.search.leave.allarea',
                compact('area_list','area_leave_wise','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLeaveSearchDepartment(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $date = $this->getSearchType($request1);
            $request1['area'] = isset($request->area)?$request->area:$request1['area'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }

            $request1 = array_slice($request1, 0, 5);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department_list = Department::where(['hr_department_area_id' => $request1['area'], 'hr_department_status'=>1])->get();
            foreach($department_list as $k=>$department) {
                $where = ['unit' => $request1['unit'],'areaid' => $request1['area'], 'departmentid' => $department->hr_department_id];
                $department_leave_wise[$department->hr_department_id] = $this->getLeaveFromDate1($date,$request1['type'],$where);
            }
            $result = [];
            $result['page'] = view('hr.search.leave.alldepartment',
                compact('department_list','department_leave_wise','area','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLeaveSearchFloor(Request $request)
    {
        try {
            // get previous url params
            $result = [];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $date = $this->getSearchType($request1);
            $request1['department'] = isset($request->department)?$request->department:$request1['department'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }

            $request1 = array_slice($request1, 0, 6);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department = Department::where(['hr_department_id' => $request1['department'], 'hr_department_status' => 1])->first();
            $floor_list = Floor::where(['hr_floor_unit_id' => $request1['unit'], 'hr_floor_status'=>1])->get();
            foreach($floor_list as $k=>$floor) {
                $where = ['unit' => $request1['unit'],'areaid' => $request1['area'], 'departmentid' => $request1['department'], 'floorid' => $floor->hr_floor_id];
                $floor_leave_wise[$floor->hr_floor_id] = $this->getLeaveFromDate1($date,$request1['type'],$where);
            }
            // return $floor_leave_wise;
            $result['page'] = view('hr.search.leave.allfloor',
                compact('floor_list','area','department','floor_leave_wise','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLeaveSearchSection(Request $request)
    {
        try {
            // get previous url params
            $result = [];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $date = $this->getSearchType($request1);
            $request1['floor'] = isset($request->floor)?$request->floor:$request1['floor'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }
            $request1 = array_slice($request1, 0, 7);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department = Department::where(['hr_department_id' => $request1['department'], 'hr_department_status' => 1])->first();
            $floor = Floor::where(['hr_floor_id' => $request1['floor'], 'hr_floor_status' => 1])->first();
            $section_list = Section::where(['hr_section_area_id' => $request1['area'], 'hr_section_department_id' => $request1['department'], 'hr_section_status' => 1])->get();
            foreach($section_list as $k=>$section) {
                $where = ['unit' => $request1['unit'],'areaid' => $request1['area'], 'departmentid' => $request1['department'], 'floorid' => $request1['floor'], 'section' => $section->hr_section_id];
                $section_leave_wise[$section->hr_section_id] = $this->getLeaveFromDate1($date,$request1['type'],$where);
            }
            $result['page'] = view('hr.search.leave.allsection',
                compact('section_list','area','floor','department','section_leave_wise','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLeaveSearchSubSection(Request $request)
    {
        try {
            // get previous url params
            $result = [];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $date = $this->getSearchType($request1);
            $request1['section'] = isset($request->section)?$request->section:$request1['section'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }

            $request1 = array_slice($request1, 0, 8);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department = Department::where(['hr_department_id' => $request1['department'], 'hr_department_status' => 1])->first();
            $floor = Floor::where(['hr_floor_id' => $request1['floor'], 'hr_floor_status' => 1])->first();
            $section = Section::where(['hr_section_id' => $request1['section'], 'hr_section_status' => 1])->first();
            $subsection_list = Subsection::where(['hr_subsec_area_id' => $request1['area'], 'hr_subsec_department_id' => $request1['department'], 'hr_subsec_section_id' => $request1['section'], 'hr_subsec_status' => 1])->get();
            foreach($subsection_list as $k=>$subsection) {
                $where = ['unit' => $request1['unit'],'areaid' => $request1['area'], 'departmentid' => $request1['department'], 'floorid' => $request1['floor'], 'section' => $request1['section'], 'subsection' => $subsection->hr_subsec_id];
                $subsection_leave_wise[$subsection->hr_subsec_id] = $this->getLeaveFromDate1($date,$request1['type'],$where);
            }
            // return $subsection_leave_wise;
            $result['page'] = view('hr.search.leave.allsubsection',
                compact('section','area','floor','department','subsection_leave_wise','request1','unit','showTitle','subsection_list'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrLeaveSearchEmployee(Request $request)
    {
        try {
            // get previous url params
            $request2 = [];
            $data = [];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            if(isset($request->attstatus)) {
                $request1['attstatus'] = $request->attstatus;
            }
            $showTitle = $this->pageTitle($request1);

            if(isset($request->subsection) || isset($request1['subsection'])) {
                $request2['as_subsection_id'] = isset($request->subsection)?$request->subsection:$request1['subsection'];
                $data['subsection'] = Subsection::where(['hr_subsec_id' => $request2['as_subsection_id'], 'hr_subsec_status' => 1])->first();
            }

            if(isset($request->section) || isset($request1['section'])) {
                $request2['as_section_id'] = isset($request->section)?$request->section:$request1['section'];
                $data['section'] = Section::where(['hr_section_id' => $request2['as_section_id'], 'hr_section_status' => 1])->first();
            }

            if(isset($request->floor) || isset($request1['floor'])) {
                $request2['as_floor_id'] = isset($request->floor)?$request->floor:$request1['floor'];
                $data['floor'] = Floor::where(['hr_floor_id' => $request2['as_floor_id'], 'hr_floor_status' => 1])->first();
            }

            if(isset($request->area) || isset($request1['area'])) {
                $request2['as_area_id'] = isset($request->area)?$request->area:$request1['area'];
                $data['area'] = Area::where(['hr_area_id' => $request2['as_area_id'], 'hr_area_status' => 1])->first();
            }

            if(isset($request->department) || isset($request1['department'])) {
                $request2['as_department_id'] = isset($request->department)?$request->department:$request1['department'];
                $data['department'] = Department::where(['hr_department_id' => $request2['as_department_id'], 'hr_department_status' => 1])->first();
            }

            if(isset($request->unit) || isset($request1['unit'])) {
                $request2['as_unit_id'] = isset($request->unit)?$request->unit:$request1['unit'];
                $request1['unit']       = isset($request->unit)?$request->unit:$request1['unit'];
                $data['unit'] = Unit::where(['hr_unit_id' => $request2['as_unit_id'], 'hr_unit_status' => 1])->first();
            }
            $request1['leavetype'] = isset($request1['leavetype'])?$request1['leavetype']:$request->leavetype;
            $request2['as_status'] = 1;
            $employee_list = Employee::where($request2)->get();
            $result['page'] = view('hr.search.leave.allemployee',
                compact('employee_list','data','request1','request2','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getEmpAttGetData($request)
    {
        // 2019-04-04
        $unit         = $request['unit'] != 'NaN'?$request['unit']:'';
        $associate_id = !empty($request['associate_id'])?$request['associate_id']:'';
        $rangeFrom    = !empty($request['rangeFrom'])?$request['rangeFrom']:'';
        $rangeTo      = !empty($request['rangeTo'])?$request['rangeTo']:'';
        $lineid       = !empty($request['line'])?$request['line']:'';
        $floorid      = $request['floor'] != 'NaN'?$request['floor']:'';
        $departmentid = $request['department'] != 'NaN'?$request['department']:'';
        $areaid       = $request['area'] != 'NaN'?$request['area']:'';
        $section      = $request['section'] != 'NaN'?$request['section']:'';
        $subSection   = $request['subsection'] != 'NaN'?$request['subsection']:'';
        $leavetype    = !empty($request['leavetype'])?$request['leavetype']:'';
        $where = [];
        if(!empty($unit)) {
            $where['as_unit_id'] = $unit;
        }
        if(!empty($areaid)) {
            $where['as_area_id'] = $areaid;
        }
        if(!empty($departmentid)) {
            $where['as_department_id'] = $departmentid;
        }
        if(!empty($floorid)) {
            $where['as_floor_id'] = $floorid;
        }
        if(!empty($lineid)) {
            $where['as_line_id'] = $lineid;
        }
        if(!empty($section)) {
            $where['as_section_id'] = $section;
        }
        if(!empty($subSection)) {
            $where['as_subsection_id'] = $subSection;
        }
        $select = [
            "hr_as_basic_info.associate_id",
            "hr_as_basic_info.as_shift_id",
            "u.hr_unit_name",
            "hr_as_basic_info.as_name",
            "hr_as_basic_info.as_pic",
            "hr_as_basic_info.as_gender",
            "dsg.hr_designation_name"
        ];
        $leave_emp_list = Employee::where('as_status',1);
        if(!empty($leavetype) && $request['type'] == 'date') {
            $leave_emp_list->join('hr_leave AS b', function($join) use ($leavetype, $rangeFrom, $rangeTo){
                $join->on('hr_as_basic_info.associate_id', '=', 'b.leave_ass_id');
                $join->where('b.leave_from', '<=', $rangeFrom);
                $join->where('b.leave_to', '>=', $rangeTo);
                $join->where('b.leave_status', '=', 1);
                if($leavetype != 'all') {
                    $join->where('b.leave_type', $leavetype);
                }
            });
            array_push($select, 'b.leave_type');
        }
        $leave_emp_list->where($where);
        $leave_emp_list->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'hr_as_basic_info.as_designation_id');
        $leave_emp_list->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "hr_as_basic_info.as_unit_id");
        /*if(!empty($leavetype) && $request['type'] == 'date') {
            $leave_emp_list->groupBy('b.leave_ass_id');
        }*/
        $leave_emp_list->orderBy('dsg.hr_designation_name');
        $leave_emp_list->select($select);
        $leave_emp_list->get();
        // $leave_emp_list->get()->toArray();
        return $leave_emp_list;
    }

    public function monthLeaveCheck($request) 
    {
        $unit         = $request['unit'] != 'NaN'?$request['unit']:'';
        $associate_id = !empty($request['associate_id'])?$request['associate_id']:'';
        $month    = !empty($request['month'])?$request['month']:'';
        $lineid       = !empty($request['line'])?$request['line']:'';
        $floorid      = $request['floor'] != 'NaN'?$request['floor']:'';
        $departmentid = $request['department'] != 'NaN'?$request['department']:'';
        $areaid       = $request['area'] != 'NaN'?$request['area']:'';
        $section      = $request['section'] != 'NaN'?$request['section']:'';
        $subSection   = $request['subsection'] != 'NaN'?$request['subsection']:'';
        $leavetype    = !empty($request['leavetype'])?$request['leavetype']:'';

        if(!empty($unit)) {
            $where['as_unit_id'] = $unit;
        }
        if(!empty($areaid)) {
            $where['as_area_id'] = $areaid;
        }
        if(!empty($departmentid)) {
            $where['as_department_id'] = $departmentid;
        }
        if(!empty($floorid)) {
            $where['as_floor_id'] = $floorid;
        }
        if(!empty($lineid)) {
            $where['as_line_id'] = $lineid;
        }
        if(!empty($section)) {
            $where['as_section_id'] = $section;
        }
        if(!empty($subSection)) {
            $where['as_subsection_id'] = $subSection;
        }

        $monthStart = $month'-01';
        $monthEnd = \Carbon\Carbon::parse($monthStart)->endOfMonth()->format('Y-m-d');

        $where['as_status'] = 1;
        $basic_emp_list = DB::table('hr_as_basic_info')->where($where)->groupBy('as_id');
        $basic_emp_sql    = $basic_emp_list->toSql();
        $select = [
            'a.associate_id',
            'a.as_name',
            'a.as_gender',
            'a.as_dob',
            'b.leave_type',
            "dsg.hr_designation_name",
            "a.as_shift_id",
            'b.id'
        ];
        $empLeaveMonth = DB::table('hr_leave AS b')->select($select)->whereYear('leave_from', '>=', $rangeFrom);
        $empLeaveMonth->where('b.leave_from', '>=', $monthStart);
        $empLeaveMonth->where('b.leave_from', '<=', $monthEnd);
        $empLeaveMonth->where('b.leave_to', '>=', $monthStart);
        $empLeaveMonth->where('b.leave_to', '<=', $monthEnd);
        // $empLeaveMonth->where('leave_ass_id',$employee->associate_id);
        $empLeaveMonth->where('b.leave_status', '=', 1);
        if($leavetype != 'all') {
            $empLeaveMonth->where('b.leave_type', $leavetype);
        }
        $empLeaveMonth->join(DB::raw('(' . $basic_emp_sql. ') AS a'), function($join) use ($basic_emp_list) {
            $join->on('a.associate_id', '=', 'b.leave_ass_id')->addBinding($basic_emp_list->getBindings());
        });
        $empLeaveMonth->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'a.as_designation_id');
        /*$empLeaveMonth->groupBy('b.leave_ass_id');*/
        $empLeaveMonth->orderBy('dsg.hr_designation_name');
        //$empLeaveMonth->groupBy('leave_ass_id',true);
        $empLeaveMonth->get();
        return $empLeaveMonth;
    }

    public function hrLeaveSearchEmpData(Request $request)
    {
        if($request->type == 'date') {
            $data = $this->getEmpAttGetData($request->all());
            $date = $request->query('rangeFrom');
            $month = date("m",strtotime($date)); // convert date to month number
        } else if($request->type == 'month') {
            $data = $this->monthLeaveCheck($request->all())->groupBy('a.associate_id');
            $month = $request->month;
        }
        $allRequest = $request->all();

        if(isset($request->leavetype) ){
            $leaves = DB::table('hr_leave')
                ->select(
                    DB::raw("
                        YEAR(leave_from) AS year,
                        SUM(CASE WHEN leave_type = '".$request->leavetype."' THEN DATEDIFF(leave_to, leave_from)+1 END) AS leave
                    ")
                )
                ->where('leave_status', '1')
                ->whereMonth('leave_from', '>=', $allRequest['rangeFrom'])
                ->whereYear('leave_from', '>=', $allRequest['rangeFrom'])
                ->whereMonth('leave_to', '<=', $allRequest['rangeFrom'])
                ->whereYear('leave_to', '<=', $allRequest['rangeFrom'])
                ->groupBy("leave_ass_id")
                ->get();
        }else{
            $leaves = DB::table('hr_leave')
                ->select(
                    DB::raw("
                        SUM(DATEDIFF(leave_to, leave_from)+1) AS leave
                    ")
                )
                ->where('leave_status', '1')
                ->whereMonth('leave_from', '>=', $allRequest['rangeTo'])
                ->whereYear('leave_from', '>=', $allRequest['rangeTo'])
                ->whereMonth('leave_to', '<=', $allRequest['rangeFrom'])
                ->whereYear('leave_to', '<=', $allRequest['rangeFrom'])
                ->groupBy("leave_ass_id")
                ->get();
        }
        

        // $data = (array) $data;
        return DataTables::of($data)->addIndexColumn()
            ->editColumn('associate_id', function ($data) use ($month) {
                return HtmlFacade::link("hr/recruitment/employee/show/{$data->associate_id}",$data->associate_id,['class' => 'employee-att-details']);
            })
            ->editColumn('hr_shift_name', function ($data) use ($allRequest) {
                return $data->as_shift_id;
            })
            ->addColumn('status', function ($data) use ($allRequest) {
                if($allRequest['type'] == 'date') {
                    return $data->leave_type;
                } else if($allRequest['type'] == 'month') {
                    $empLeaveCount = 0;
                    $empLeaveDays = [];
                    if($data->associate_id != null) {
                        $empLeaveDays = Leave::whereYear('leave_from', '>=', $allRequest['rangeFrom'])
                        ->whereMonth('leave_from', '>=', $allRequest['rangeTo'])
                        ->whereYear('leave_to', '<=', $allRequest['rangeFrom'])
                        ->whereMonth('leave_to', '<=', $allRequest['rangeTo'])
                        ->where('leave_ass_id',$data->associate_id)
                        ->where('leave_status', '=', 1)
                        ->get()->toArray();
                        foreach($empLeaveDays as $k=>$empLeave) {
                            $empLeaveCount += Carbon::parse($empLeave['leave_to'])->diffInDays($empLeave['leave_from']);
                        }
                        if($empLeaveCount == 0) {
                            $empLeaveCount = 1;
                        }
                    }
                    return $empLeaveCount.' day\'s';
                }
            })
            ->rawColumns(['status'])
            ->make(true);
            // ->toJson();
    }

    public function hrLeaveSearchSingleEmpData(Request $request)
    {
        try {
            $associate_id = $request->segment(4);
            $monthNumber = $request->segment(5);
            $single_emp = Employee::where('associate_id',$associate_id)->first();
            if(!empty($single_emp) && !empty($monthNumber)) {
                $year = date('Y');
                $firstDayOfMonth = date($year."-m-d", mktime(0, 0, 0, $monthNumber, 1));
                $lastDayOfMonth = date($year."-m-d", mktime(0, 0, 0, $monthNumber+1, 0));
                // dd($lastDayOfMonth);
                $result    = [];
                $data_list = [];
                $events    = [];
                $empLeaveData = Leave::whereYear('leave_from', '>=', $year)
                ->whereMonth('leave_from', '>=', $monthNumber)
                ->whereYear('leave_to', '<=', $year)
                ->whereMonth('leave_to', '<=', $monthNumber)
                ->where('leave_ass_id',$associate_id)
                ->where('leave_status', '=', 1)
                ->get()->toArray();

                if(!empty($empLeaveData)) {
                    foreach($empLeaveData as $k=>$empLeave) {
                        $leaveTo = date('Y-m-d', strtotime($empLeave['leave_to'] . ' +1 day'));
                        $events[] = $this->getEvents(['start' => $empLeave['leave_from'], 'end' => $leaveTo] ,$empLeave['leave_type'],'#00BA1D','#fff');
                    }
                }


                $calendar = \Calendar::addEvents($events) //add an array with addEvents
                    ->setOptions([ //set fullcalendar options
                        'firstDay' => 1,
                        'defaultDate'=> $firstDayOfMonth
                    ]);
                return view('hr.search.leave.leavesingleemp', compact('result','calendar'))->render();
            } else {
                return 'No Employee Found';
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getEvents($date,$title,$bgColor,$txtColor)
    {
        $event = [];
        $event = \Calendar::event(
            $title, //event title
            true, //full day event?
            new \DateTime($date['start']), //start time (you can also use Carbon instead of DateTime)
            new \DateTime($date['end']), //end time (you can also use Carbon instead of DateTime)
            null,
            [
                'color' => $bgColor,
                'textColor' => $txtColor
            ] //optionally, you can specify an event ID
        );
        return $event;
    }

    public function hrLeaveSearchPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        return view('hr.search.leave.print.allleavePrint',compact('data','title'))->render();
    }

    public function hrLeaveSearchUnitPrint(Request $request)
    {
        $data = $request->data;
        $data1 = json_decode($request->data1, true);
        $title = $request->title;
        return view('hr.search.leave.print.allleaveunitPrint',compact('data','data1','title'))->render();
    }

    public function hrLeaveSearchAreaPrint(Request $request)
    {
        $data = $request->data;
        $unitName = $request->unitName;
        $data1 = json_decode($request->data1, true);
        $title = $request->title;
        return view('hr.search.leave.print.allleaveareaPrint',compact('data','data1','title','unitName'))->render();
    }

    public function hrLeaveSearchDepartmentPrint(Request $request)
    {
        $data = $request->data;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $data1 = json_decode($request->data1, true);
        $title = $request->title;
        return view('hr.search.leave.print.allleavedepartmentPrint',compact('data','data1','title','unitName','areaName'))->render();
    }

    public function hrLeaveSearchFloorPrint(Request $request)
    {
        $data = $request->data;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $departmentName = $request->departmentName;
        $data1 = json_decode($request->data1, true);
        $title = $request->title;
        return view('hr.search.leave.print.allleavefloorPrint',compact('data','data1','title','unitName','areaName','departmentName'))->render();
    }

    public function hrLeaveSearchSectionPrint(Request $request)
    {
        $data = $request->data;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $departmentName = $request->departmentName;
        $floorName = $request->floorName;
        $data1 = json_decode($request->data1, true);
        $title = $request->title;
        return view('hr.search.leave.print.allleavesectionPrint',compact('data','data1','title','unitName','areaName','departmentName','floorName'))->render();
    }

    public function hrLeaveSearchSubSectionPrint(Request $request)
    {
        $data = $request->data;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $departmentName = $request->departmentName;
        $floorName = $request->floorName;
        $sectionName = $request->sectionName;
        $data1 = json_decode($request->data1, true);
        $title = $request->title;
        return view('hr.search.leave.print.allleavesubsectionPrint',compact('data','data1','title','unitName','areaName','departmentName','floorName','sectionName'))->render();
    }


}
