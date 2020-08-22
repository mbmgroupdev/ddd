<?php

namespace App\Http\Controllers\Hr\Search;

use App\Http\Controllers\Controller;
use App\Models\Hr\Area;
use App\Models\Hr\Attendace;
use App\Models\Hr\AttendaceManual;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
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

class AttendaceSearchController extends Controller
{
    public function hrAttSearch(Request $request)
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
            $date = $request['date'];
            $date = Carbon::create($date)->format('Y-m-d%');
        }
        else if($request['type'] == 'yesterday') {
            $date = Carbon::create(date('d')-1)->format('Y-m-d%');
        } else {
            $date = Carbon::create(date('d'))->format('Y-m-d%');
        }
        return $date;
    }

    public function searchGetAttendancedReport_global($request)
    {
        try {
            $showTitle = $this->pageTitle($request);
            // get previous url params
            $parts = parse_url(url()->previous());
            if(isset($parts['query'])){
                parse_str($parts['query'], $request1);
                if($request1['category'] != $request['category']) {
                    $request1['category'] = $request['category'];
                }
                if($request1['type'] != $request['type']) {
                    $request1['type'] = $request['type'];
                }
                if(isset($request['date'])){
                    $request1['date'] = $request['date'];
                }
                $request = ['category'=> $request1['category'], 'type' => $request1['type'], 'date' => $request1['date']];
            }
            // $date = $this->getSearchType($request);
            $unit_list      = Unit::where('hr_unit_status',1)->get();
            $employee_list  = Employee::where('as_status',1)->get();
            $result = [];
            $attResultCount['present']  = 0;
            $attResultCount['absent']   = 0;
            $attResultCount['late']     = 0;
            $attResultCount['leave']    = 0;
            $attResultList         = [];
            foreach($unit_list as $unit) {
                $attResultList = $this->getEmpAttGetPresentAbsentCount(['unit' => $unit->hr_unit_id,'rangeFrom'=>$request['date'],'rangeTo'=>$request['date']]);
                $attResultCount['present']  += $attResultList['total_present'];
                $attResultCount['absent']   += $attResultList['total_absent'];
                $attResultCount['late']     += $attResultList['total_late'];
                $attResultCount['leave']    += $attResultList['total_leave'];
            }
            $result['page'] = view('hr.search.attendance.allattendance',
                compact('unit_list','employee_list','showTitle', 'request', 'attResultCount'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchUnit(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request);
            $array_slice = 3;
            if(isset($request['attstatus'])) {
                unset($request['attstatus']);
            }
            if(isset($request['unit'])) {
                unset($request['unit']);
            }
            if(isset($request['view'])) {
                $array_slice = 4;
            }
            $request = array_slice($request, 0, $array_slice);
            $showTitle = $this->pageTitle($request);
            $attCountUnitWise = [];
            $unit_list = Unit::where('hr_unit_status',1)->get();

            $area_count = Area::where('hr_area_status',1)->count();
            $result = [];
            $result['page'] = view('hr.search.attendance.allunit',
                compact('unit_list', 'request', 'area_count', 'attCountUnitWise', 'showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchArea(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['unit'] = isset($request->unit)?$request->unit:$request1['unit'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }
            unset($request1['view']);

            $request1 = array_slice($request1, 0, 4);
            $showTitle = $this->pageTitle($request1);

            // return $request1;
            $unit=Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area_list = Area::where('hr_area_status',1)->get();
            // return $area_list;
            $result = [];
            $result['page'] = view('hr.search.attendance.allarea',
                compact('area_list','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchDepartment(Request $request)
    {
        try {
            // get previous url params
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['area'] = isset($request->area)?$request->area:$request1['area'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }
            unset($request1['view']);

            $request1 = array_slice($request1, 0, 5);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department_list = Department::where(['hr_department_area_id' => $request1['area'], 'hr_department_status'=>1])->get();
            $result = [];
            $result['page'] = view('hr.search.attendance.alldepartment',
                compact('department_list','area','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchFloor(Request $request)
    {
        try {
            // get previous url params
            $result = [];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['department'] = isset($request->department)?$request->department:$request1['department'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }
            unset($request1['view']);

            $request1 = array_slice($request1, 0, 6);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department = Department::where(['hr_department_id' => $request1['department'], 'hr_department_status' => 1])->first();
            $floor_list = Floor::where(['hr_floor_unit_id' => $request1['unit'], 'hr_floor_status'=>1])->get();
            $result['page'] = view('hr.search.attendance.allfloor',
                compact('floor_list','area','department','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchSection(Request $request)
    {
        try {
            // get previous url params
            $result = [];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['floor'] = isset($request->floor)?$request->floor:$request1['floor'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }
            unset($request1['view']);
            $request1 = array_slice($request1, 0, 7);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department = Department::where(['hr_department_id' => $request1['department'], 'hr_department_status' => 1])->first();
            $floor = Floor::where(['hr_floor_id' => $request1['floor'], 'hr_floor_status' => 1])->first();
            $section_list = Section::where(['hr_section_area_id' => $request1['area'], 'hr_section_department_id' => $request1['department'], 'hr_section_status' => 1])->get();
            $result['page'] = view('hr.search.attendance.allsection',
                compact('section_list','area','floor','department','request1','unit','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchSubSection(Request $request)
    {
        try {
            // get previous url params
            $result = [];
            $parts = parse_url(url()->previous());
            parse_str($parts['query'], $request1);
            $request1['section'] = isset($request->section)?$request->section:$request1['section'];
            if(isset($request1['attstatus'])) {
                unset($request1['attstatus']);
            }
            unset($request1['view']);
            $request1 = array_slice($request1, 0, 8);
            $showTitle = $this->pageTitle($request1);

            $unit = Unit::where(['hr_unit_id' => $request1['unit'], 'hr_unit_status' => 1])->first();
            $area = Area::where(['hr_area_id' => $request1['area'], 'hr_area_status' => 1])->first();
            $department = Department::where(['hr_department_id' => $request1['department'], 'hr_department_status' => 1])->first();
            $floor = Floor::where(['hr_floor_id' => $request1['floor'], 'hr_floor_status' => 1])->first();
            $section = Section::where(['hr_section_id' => $request1['section'], 'hr_section_status' => 1])->first();
            $subsection_list = Subsection::where(['hr_subsec_area_id' => $request1['area'], 'hr_subsec_department_id' => $request1['department'], 'hr_subsec_section_id' => $request1['section'], 'hr_subsec_status' => 1])->get();
            $result['page'] = view('hr.search.attendance.allsubsection',
                compact('section','area','floor','department','request1','unit','showTitle','subsection_list'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchEmployee(Request $request)
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
            $result['page'] = view('hr.search.attendance.allemployee',
                compact('employee_list','data','request1','request2','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
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
        //HO
        else if($unit == 6){
            $tableName= "hr_attendance_ho AS a";
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

    public function hrGetEmpAttCount(Request $request)
    {
        $data_list = $this->getEmpAttGetPresentAbsentCount($request->all());
        $result    = [];
        $result['present']  = $data_list['total_present'];
        $result['absent']   = $data_list['total_absent'];
        $result['late']     = $data_list['total_late'];
        $result['leave']     = $data_list['total_leave'];
        $result['attendance']    = $data_list['total_attendance'];
        return $result;
    }

    public function getEmpAttGetPresentAbsentCount($request)
    {
        $result = [];
        $result['total_absent']   = 0;
        $result['total_present']  = 0;
        $result['total_late']  = 0;
        $result['total_leave']  = 0;
        $result['total_attendance']  = 0;
        $leave = '';

        $unit         = $request['unit'];
        $associate_id = !empty($request['associate_id'])?$request['associate_id']:'';
        $rangeFrom    = !empty($request['rangeFrom'])?$request['rangeFrom']:'';
        $rangeTo      = !empty($request['rangeTo'])?$request['rangeTo']:'';
        $lineid       = !empty($request['line'])?$request['line']:'';
        $floorid      = !empty($request['floor'])?$request['floor']:'';
        $departmentid = !empty($request['department'])?$request['department']:'';
        $areaid       = !empty($request['area'])?$request['area']:'';
        $section      = !empty($request['section'])?$request['section']:'';
        $subSection   = !empty($request['subsection'])?$request['subsection']:'';

        // basic table
        $hr_basic_list = DB::table('hr_as_basic_info')->where('as_status', 1);
        if (!empty($unit)) {
            $hr_basic_list->where('as_unit_id',$unit);
        }
        if (!empty($associate_id)) {
            $hr_basic_list->where('associate_id', $associate_id);
        }
        if(!empty($areaid)) {
            $hr_basic_list->where('as_area_id',$areaid);
        }
        if(!empty($departmentid)) {
            $hr_basic_list->where('as_department_id',$departmentid);
        }
        if(!empty($floorid)) {
            $hr_basic_list->where('as_floor_id',$floorid);
        }
        if (!empty($lineid)) {
            $hr_basic_list->where('as_line_id', $lineid);
        }
        if (!empty($section)) {
            $hr_basic_list->where('as_section_id', $section);
        }
        if (!empty($subSection)) {
            $hr_basic_list->where('as_subsection_id', $subSection);
        }
        $hr_basic_list->orderBy('as_id');
        // return $hr_basic_list->count();
        $hr_basic_sql    = $hr_basic_list->toSql();  // compiles to SQL

        // accendance count
        $select = [
            'b.associate_id',
            'b.as_name',
            'b.as_gender',
            'b.as_dob',
            'a.in_time',
            'a.late_status'
            // 's.hr_shift_start_time',
            // 's.hr_shift_name'
        ];
        $tableName = $this->getTableName($unit);
        $attData = DB::table($tableName)->select($select);
        if (!empty($associate_id)) {
            $single_emp = Employee::where('associate_id',$associate_id)->first();
            $attData->where('a.as_id', $single_emp->as_id);
        }
        $attData->where('a.in_time','>=', $rangeFrom." 00:00:00");
        $attData->where('a.in_time','<=', $rangeTo." 23:59:59");
        $attData->join(DB::raw('(' . $hr_basic_sql. ') AS b'), function($join) use ($hr_basic_list){
            $join->on('a.as_id', '=', 'b.as_id')->addBinding($hr_basic_list->getBindings()); ;
        });
        $attData->leftJoin("hr_shift AS s", "a.hr_shift_code", "=", "s.hr_shift_code");
        $attData->groupBy('a.as_id');


        // leave count
        $leaveData = DB::table('hr_leave AS a');
        if (!empty($associate_id)) {
            $leaveData->where('a.leave_ass_id', $single_emp->as_id);
        }
        $leaveData->where('a.leave_from','<=', $rangeFrom);
        $leaveData->where('a.leave_to','>=', $rangeTo);
        $leaveData->where('a.leave_status','1');
        $leaveData->join(DB::raw('(' . $hr_basic_sql. ') AS b'), function($join) use ($hr_basic_list){
            $join->on('a.leave_ass_id', '=', 'b.associate_id')->addBinding($hr_basic_list->getBindings()); ;
        });
        $leaveData->groupBy('a.leave_ass_id');

        // absent count
        $absentData = DB::table('hr_absent AS a');
        if (!empty($associate_id)) {
            $absentData->where('a.associate_id', $single_emp->associate_id);
        }
        $absentData->where('a.date','=', $rangeFrom);
        $absentData->join(DB::raw('(' . $hr_basic_sql. ') AS b'), function($join) use ($hr_basic_list){
            $join->on('a.associate_id', '=', 'b.associate_id')->addBinding($hr_basic_list->getBindings()); ;
        });
        // $absentData->groupBy('a.associate_id');

        //  calculate total late
        $result['total_leave']  = $leaveData->count();
        $result['total_absent'] = $absentData->count();
        foreach($attData->get() as $k=>$att) {
            if($att->late_status == 1) {
                $result['total_late'] += 1;
            } else {
                $result['total_present'] += 1;
            }
        }
        $result['total_attendance'] = $result['total_present']+$result['total_absent']+$result['total_late']+$result['total_leave'];
        return $result;
    }

    public function getEmpAttGetData($request)
    {
        $result         = [];
        $presentResult  = [];
        $unit         = $request['unit'] != 'NaN'?$request['unit']:'';
        $associate_id = $request['associate_id'] != 'NaN'?$request['associate_id']:'';
        $rangeFrom    = $request['rangeFrom'] != 'NaN'?$request['rangeFrom']:'';
        $rangeTo      = $request['rangeTo'] != 'NaN'?$request['rangeTo']:'';
        $lineid       = $request['line'] != 'NaN'?$request['line']:'';
        $floorid      = $request['floor'] != 'NaN'?$request['floor']:'';
        $departmentid = $request['department'] != 'NaN'?$request['department']:'';
        $areaid       = $request['area'] != 'NaN'?$request['area']:'';
        $section      = $request['section'] != 'NaN'?$request['section']:'';
        $subSection   = $request['subsection'] != 'NaN'?$request['subsection']:'';

        // basic table
        $hr_basic_list = Employee::where('as_status', 1);
        if (!empty($unit)) {
            $hr_basic_list->where('as_unit_id',$unit);
        }
        if (!empty($associate_id)) {
            $hr_basic_list->where('associate_id', $associate_id);
        }
        if(!empty($areaid)) {
            $hr_basic_list->where('as_area_id',$areaid);
        }
        if(!empty($departmentid)) {
            $hr_basic_list->where('as_department_id',$departmentid);
        }
        if(!empty($floorid)) {
            $hr_basic_list->where('as_floor_id',$floorid);
        }
        if (!empty($lineid)) {
            $hr_basic_list->where('as_line_id', $lineid);
        }
        if (!empty($section)) {
            $hr_basic_list->where('as_section_id', $section);
        }
        if (!empty($subSection)) {
            $hr_basic_list->where('as_subsection_id', $subSection);
        }
        $hr_basic_list->orderBy('as_id');
        $hr_basic_sql    = $hr_basic_list->toSql();  // compiles to SQL

        // accendance count
        $attSelect = [
            'b.associate_id',
            'b.as_name',
            'b.as_gender',
            'b.as_dob',
            'a.in_time',
            'a.late_status',
            'u.hr_unit_name',
            's.hr_shift_start_time',
            's.hr_shift_name',
            'dsg.hr_designation_name',
            'f.hr_floor_name'
        ];
        $tableName = $this->getTableName($unit);
        $attData = DB::table($tableName)->select($attSelect);
        if (!empty($associate_id)) {
            $single_emp = Employee::where('associate_id',$associate_id)->first();
            $attData->where('a.as_id', $single_emp->as_id);
        }
        $attData->where('a.in_time','>=', $rangeFrom." 00:00:00");
        $attData->where('a.in_time','<=', $rangeTo." 23:59:59");
        $attData->join(DB::raw('(' . $hr_basic_sql. ') AS b'), function($join) use ($hr_basic_list){
            $join->on('a.as_id', '=', 'b.as_id')->addBinding($hr_basic_list->getBindings()); ;
        });
        $attData->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id');
        $attData->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id");
        $attData->leftJoin("hr_shift AS s", "a.hr_shift_code", "=", "s.hr_shift_code");
        // $attData->leftJoin('hr_shift AS s', function($q) use ($unit) {
        //     $q->on('s.hr_shift_code','a.hr_shift_code')
        //       ->where('s.hr_shift_unit_id',$unit);
        //     //   ->on('s.hr_shift_unit_id', 'b.as_unit_id');
        //       //->on('s.hr_shift_code', DB::raw("(select s.hr_shift_code from s WHERE s.hr_shift_code = a.hr_shift_code AND s.hr_shift_unit_id = b.as_unit_id )"));
        // });
        $attData->leftJoin("hr_floor AS f", 'f.hr_floor_id', '=', 'b.as_floor_id');
        // $attData->leftJoin("hr_section AS sec", 'sec.hr_section_id', '=', 'b.as_section_id');
        // $attData->leftJoin("hr_line AS li", 'li.hr_line_id', '=', 'b.as_line_id');
        $attData->groupBy('a.as_id');

        // return dd($attData->get());
        // leave count
        $leaveSelect = [
            DB::raw('"leave" AS status'),
            'b.associate_id',
            'b.as_name',
            'b.as_gender',
            'b.as_dob',
            'u.hr_unit_name',
            'b.as_shift_id AS hr_shift_name',
            'dsg.hr_designation_name',
            'f.hr_floor_name'
        ];
        $leaveData = DB::table('hr_leave AS a')->select($leaveSelect);
        if (!empty($associate_id)) {
            $leaveData->where('a.leave_ass_id', $single_emp->as_id);
        }
        $leaveData->where('a.leave_from','<=', $rangeFrom);
        $leaveData->where('a.leave_to','>=', $rangeTo);
        $leaveData->where('a.leave_status','1');
        $leaveData->join(DB::raw('(' . $hr_basic_sql. ') AS b'), function($join) use ($hr_basic_list){
            $join->on('a.leave_ass_id', '=', 'b.associate_id')->addBinding($hr_basic_list->getBindings()); ;
        });
        $leaveData->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id');
        $leaveData->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id");
        // $leaveData->leftJoin("hr_shift AS s", "s.hr_shift_id", "=", "b.as_shift_id");
        $leaveData->leftJoin("hr_floor AS f", 'f.hr_floor_id', '=', 'b.as_floor_id');
        // $leaveData->leftJoin("hr_section AS sec", 'sec.hr_section_id', '=', 'b.as_section_id');
        // $leaveData->leftJoin("hr_line AS li", 'li.hr_line_id', '=', 'b.as_line_id');
        $leaveData->groupBy('a.leave_ass_id');

        // absent count
        $absSelect = [
            DB::raw('"absent" AS status'),
            'b.associate_id',
            'b.as_name',
            'b.as_gender',
            'b.as_dob',
            'u.hr_unit_name',
            // 's.hr_shift_start_time',
            'b.as_shift_id AS hr_shift_name',
            'dsg.hr_designation_name',
            'f.hr_floor_name'
        ];
        $absentData = DB::table('hr_absent AS a')->select($absSelect);
        if (!empty($associate_id)) {
            $absentData->where('a.associate_id', $single_emp->as_id);
        }
        $absentData->where('a.date','=', $rangeFrom);
        $absentData->join(DB::raw('(' . $hr_basic_sql. ') AS b'), function($join) use ($hr_basic_list){
            $join->on('a.associate_id', '=', 'b.associate_id')->addBinding($hr_basic_list->getBindings());
        });
        $absentData->leftJoin('hr_designation AS dsg', 'dsg.hr_designation_id', 'b.as_designation_id');
        $absentData->leftJoin("hr_unit AS u", "u.hr_unit_id", "=", "b.as_unit_id");
        // $absentData->leftJoin("hr_shift AS s", "s.hr_shift_id", "=", "b.as_shift_id");
        $absentData->leftJoin("hr_floor AS f", 'f.hr_floor_id', '=', 'b.as_floor_id');
        // $absentData->leftJoin("hr_section AS sec", 'sec.hr_section_id', '=', 'b.as_section_id');
        // $absentData->leftJoin("hr_line AS li", 'li.hr_line_id', '=', 'b.as_line_id');
        // $absentData->groupBy('a.associate_id');

        // leave employee list
        $resultLe = [];
        if($leaveData->count() > 0 && ($request['attstatus'] == 'leave' || $request['attstatus'] == 'all')) {
            $result = array_merge($result,$leaveData->get()->toArray());
        }
        // absent employee list
        if($absentData->count() > 0 && ($request['attstatus'] == 'absent' || $request['attstatus'] == 'all')) {
            $result = array_merge($result,$absentData->get()->toArray());
        }
        // present & late employee list
        if($attData->count() > 0 && ($request['attstatus'] == 'late' || $request['attstatus'] == 'present' || $request['attstatus'] == 'all')) {
            foreach($attData->get() as $k=>$att) {
                // if(isset($att->late_status)) {
                    if($att->late_status == 1) {
                        if($request['attstatus'] == 'late' || $request['attstatus'] == 'all'){
                            $presentResult[$att->associate_id] = $att;
                            $presentResult[$att->associate_id]->status = 'late';
                        }
                    } else {
                        if($request['attstatus'] == 'present' || $request['attstatus'] == 'all') {
                            $presentResult[$att->associate_id] = $att;
                            $presentResult[$att->associate_id]->status = 'present';
                        }
                    }
                // } else {
                //     $time = explode(' ',$att->in_time);
                //     if(strtotime($time[1]) >(strtotime($att->hr_shift_start_time)+180)){
                //         if($request['attstatus'] == 'late' || $request['attstatus'] == 'all'){
                //             $presentResult[$att->associate_id] = $att;
                //             $presentResult[$att->associate_id]->status = 'late';
                //         }
                //     } else {
                //         if($request['attstatus'] == 'present' || $request['attstatus'] == 'all') {
                //             $presentResult[$att->associate_id] = $att;
                //             $presentResult[$att->associate_id]->status = 'present';
                //         }
                //     }
                // }
            }
        }
        $result = array_merge($result,$presentResult);
        return $result;
    }

    public function hrAttSearchEmpData(Request $request)
    {
        $data = $this->getEmpAttGetData($request->all());
        $date = $request->query('rangeFrom');
        $month = date("m",strtotime($date)); // convert date to month number
        // $data = (array) $data;
        return DataTables::of($data)->addIndexColumn()
            ->editColumn('associate_id', function ($data) use ($month) {
                return HtmlFacade::link("hr/search/single_emp/{$data->associate_id}/{$month}",$data->associate_id,['class' => 'employee-att-details']);
            })
            ->addColumn('att_date', function ($data) {
                return '';
            })
            ->addColumn('att_status', function ($data) {
                return $data->status;
            })
            ->rawColumns(['att_date','att_status'])
            ->make(true);
            // ->toJson();
    }

    public function hrAttSearchAllEmployee(Request $request)
    {
        try {
            $showTitle = $this->pageTitle($request->all());
            $request1 = $request->all();
            $request1['view'] = 'employee';
            $unit_list = Unit::where('hr_unit_status',1)->get();
            $result['page'] = view('hr.search.attendance.allemployee_search',
                compact('request1','showTitle'))->render();
            $result['url'] = url('hr/search?').http_build_query($request1);
            return $result;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function hrAttSearchAllEmpData(Request $request)
    {
        $unit_list  = Unit::where('hr_unit_status',1)->get();
        $rangeFrom  = $request->rangeFrom;
        $rangeTo    = $request->rangeTo;
        $dataTotal  = [];
        $data       = [];
        $month = date("m",strtotime($rangeFrom)); // convert date to month number
        foreach($unit_list as $k=>$unit) {
            $params = [
                'unit'          => $unit->hr_unit_id,
                'rangeFrom'     => $rangeFrom,
                'rangeTo'       => $rangeTo,
                'associate_id'  => 'NaN',
                'floor'         => 'NaN',
                'line'          => 'NaN',
                'area'          => 'NaN',
                'section'       => 'NaN',
                'subsection'    => 'NaN',
                'department'    => 'NaN',
                'attstatus'     => $request->attstatus
            ];
            $data = $this->getEmpAttGetData($params);
            // return $data;
            $dataTotal = array_merge($dataTotal, $data);
        }
        return DataTables::of($dataTotal)->addIndexColumn()
            ->editColumn('associate_id', function ($dataTotal) use ($month) {
                // hr/search/single_emp/{$dataTotal->associate_id}
                return HtmlFacade::link("hr/search/single_emp/{$dataTotal->associate_id}/{$month}",$dataTotal->associate_id,['class' => 'employee-att-details']);
            })
            ->addColumn('att_date', function ($dataTotal) {
                return '';
            })
            ->addColumn('att_status', function ($dataTotal) {
                return $dataTotal->status;
            })
            ->rawColumns(['att_date','att_status'])
            ->make(true);
            // ->toJson();
    }

    public function hrAttSearchSingleEmpData(Request $request)
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
                for($i = $firstDayOfMonth; $i <= $lastDayOfMonth; $i++) {
                    $data = [
                        'associate_id' => $associate_id,
                        'unit' => $single_emp->as_unit_id,
                        'rangeFrom' => $i,
                        'rangeTo' => $i
                    ];
                    $data_list = $this->getEmpAttGetPresentAbsentCount($data);
                    if($data_list['total_present'] != 0) {
                        $result[$i] = 'Present';
                        $events[] = $this->getEvents($i,'Present','#00BA1D','#fff');
                    }
                    if($data_list['total_absent'] != 0) {
                        $result[$i] = 'Absent';
                        $events[] = $this->getEvents($i,'Absent','#E74C3C','#fff');
                    }
                    if($data_list['total_late'] != 0) {
                        $result[$i] = 'Late';
                        $events[] = $this->getEvents($i,'Late','#E97E12','$fff');
                    }
                    if($data_list['total_leave'] != 0) {
                        $result[$i] = 'Leave';
                        $events[] = $this->getEvents($i,'Leave','#E67E22','#fff');
                    }
                }
                $calendar = \Calendar::addEvents($events) //add an array with addEvents
                    ->setOptions([ //set fullcalendar options
                        'firstDay' => 1,
                        'defaultDate'=> $firstDayOfMonth
                    ]);
                return view('hr.search.attendance.attsingleemp', compact('result','calendar'))->render();
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
            new \DateTime($date), //start time (you can also use Carbon instead of DateTime)
            new \DateTime($date), //end time (you can also use Carbon instead of DateTime)
            null,
            [
                'color' => $bgColor,
                'textColor' => $txtColor
            ] //optionally, you can specify an event ID
        );
        return $event;
    }


    // print section =======
    public function hrAttSearchPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        return view('hr.search.attendance.print.allattendancePrint',compact('data','title'))->render();
    }

    public function hrAttSearchUnitPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        $response = $request->response;
        return view('hr.search.attendance.print.allunitPrint',compact('data','title','response'))->render();
    }

    public function hrAttSearchAreaPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        $unitName = $request->unitName;
        $response = $request->response;
        return view('hr.search.attendance.print.allareaPrint',compact('data','title','response','unitName'))->render();
    }

    public function hrAttSearchDepartmentPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $response = $request->response;
        return view('hr.search.attendance.print.alldepartmentPrint',compact('data','title','response','unitName','areaName'))->render();
    }

    public function hrAttSearchFloorPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $departmentName = $request->departmentName;
        $response = $request->response;
        return view('hr.search.attendance.print.allfloorPrint',compact('data','title','response','unitName','areaName','departmentName'))->render();
    }

    public function hrAttSearchSectionPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $departmentName = $request->departmentName;
        $floorName = $request->floorName;
        $response = $request->response;
        return view('hr.search.attendance.print.allsectionPrint',compact('data','title','response','unitName','areaName','departmentName','floorName'))->render();
    }

    public function hrAttSearchSubSectionPrint(Request $request)
    {
        $data = $request->data;
        $title = $request->title;
        $unitName = $request->unitName;
        $areaName = $request->areaName;
        $departmentName = $request->departmentName;
        $floorName = $request->floorName;
        $sectionName = $request->sectionName;
        $response = $request->response;
        return view('hr.search.attendance.print.allsubsectionPrint',compact('data','title','response','unitName','areaName','departmentName','floorName','sectionName'))->render();
    }

}
