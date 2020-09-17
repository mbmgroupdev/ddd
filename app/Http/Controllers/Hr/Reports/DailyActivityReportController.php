<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use App\Models\Employee;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Unit;
use DB;
use Illuminate\Http\Request;

class DailyActivityReportController extends Controller
{
    public function beforeAfterStatus()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->pluck('hr_unit_name', 'hr_unit_id');
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        return view('hr/reports/daily_activity/before_after_status', compact('unitList','areaList'));
    }

    public function beforeAfterReport(Request $request)
    {
        $input = $request->all();
       
        try {
            $areaid       = isset($request['area'])?$request['area']:'';
            $otnonot      = isset($request['otnonot'])?$request['otnonot']:'';
            $departmentid = isset($request['department'])?$request['department']:'';
            $lineid       = isset($request['line_id'])?$request['line_id']:'';
            $florid       = isset($request['floor_id'])?$request['floor_id']:'';
            $section      = isset($request['section'])?$request['section']:'';
            $subSection   = isset($request['subSection'])?$request['subSection']:'';
            $absentDate   = $request['absent_date'];
            $presentDate   = $request['present_date'];

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();

            $queryData = Absent::select('emp.as_id')
            ->where('hr_unit',$request['unit'])
            ->where('date', $request['absent_date'])
            ->when(!empty($areaid), function ($query) use($areaid){
               return $query->where('emp.as_area_id',$areaid);
            })
            ->when(!empty($departmentid), function ($query) use($departmentid){
               return $query->where('emp.as_department_id',$departmentid);
            })
            ->when(!empty($lineid), function ($query) use($lineid){
               return $query->where('emp.as_line_id', $lineid);
            })
            ->when(!empty($florid), function ($query) use($florid){
               return $query->where('emp.as_floor_id',$florid);
            })
            ->when($request['otnonot']!=null, function ($query) use($otnonot){
               return $query->where('emp.as_ot',$otnonot);
            })
            ->when(!empty($section), function ($query) use($section){
               return $query->where('emp.as_section_id', $section);
            })
            ->when(!empty($subSection), function ($query) use($subSection){
               return $query->where('emp.as_subsection_id', $subSection);
            });
            $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                $join->on('emp.associate_id','hr_absent.associate_id')->addBinding($employeeData->getBindings());
            });
            
            $absentData = $queryData->pluck('emp.as_id')->toArray();
            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];
            if(count($absentData) > 0){
                $tableName = get_att_table($request['unit']).' AS a';
                $attData = DB::table($tableName)
                    ->where('emp.as_unit_id',$request['unit'])
                    ->where('a.in_date', $request['present_date'])
                    ->whereIn('a.as_id', $absentData)
                    ->when(!empty($areaid), function ($query) use($areaid){
                       return $query->where('emp.as_area_id',$areaid);
                    })
                    ->when(!empty($departmentid), function ($query) use($departmentid){
                       return $query->where('emp.as_department_id',$departmentid);
                    })
                    ->when(!empty($lineid), function ($query) use($lineid){
                       return $query->where('emp.as_line_id', $lineid);
                    })
                    ->when(!empty($florid), function ($query) use($florid){
                       return $query->where('emp.as_floor_id',$florid);
                    })
                    ->when($request['otnonot']!=null, function ($query) use($otnonot){
                       return $query->where('emp.as_ot',$otnonot);
                    })
                    ->when(!empty($section), function ($query) use($section){
                       return $query->where('emp.as_section_id', $section);
                    })
                    ->when(!empty($subSection), function ($query) use($subSection){
                       return $query->where('emp.as_subsection_id', $subSection);
                    });
                    $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                        $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                    });
                    if($input['report_format'] == 1 && $input['report_group'] != null){
                        $attData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                    }else{
                        $attData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id');
                    }
                $getEmployee = $attData->get();
                if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
                    $getEmployeeArray = $getEmployee->toArray();
                    $formatBy = array_column($getEmployeeArray, $request['report_group']);
                    $uniqueGroups = array_unique($formatBy);
                }
            }
            
            return view('hr.reports.daily_activity.before_after_report', compact('uniqueGroups', 'format', 'getEmployee', 'input'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            // return $bug;
            return 'error';
        }
    }
    public function employeeActivity()
    {
        return view('hr.reports.yearly_activity.employee_wise_activity');
    }
    public function employeeActivityReport(Request $request)
    {
        $input = $request->all();
        try {
            if($input['as_id'] == null){
                return 'error';
            }
            if(isset($input['year']) && $input['year'] != null){
                $year = $input['year'];
            }else{
                $year = date('Y');
            }
            $employee = Employee::getEmployeeAssociateIdWise($input['as_id']);
            // get yearly report
            $getData = HrMonthlySalary::getYearlyActivityMonthWise($input['as_id'], $year);
            return view('hr.reports.yearly_activity.employee_activity_result', compact('getData','employee', 'year'));
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function employeeActivityReportModal(Request $request)
    {
        $data['type'] = 'error';
        $input = $request->all();
        try {
            if($input['as_id'] == null){
                $data['message'] = 'Employee Id Not Found!';
                return $data;
            }
            if(isset($input['year']) && $input['year'] != null){
                $year = $input['year'];
            }else{
                $year = date('Y');
            }
            // get yearly report
            $getData = HrMonthlySalary::getYearlyActivityMonthWise($input['as_id'], $year);
            $activity = '';
            if(count($getData) == 0){
                $activity.= '<tr>';
                $activity.= '<td colspan="5" class="text-center"> No Data Found! </td>';
                $activity.= '</tr>';
            }else{
                foreach ($getData as $el) {
                    $otHourEx = explode('.', $el->ot_hour);
                    $minute = '00';
                    if(isset($otHourEx[1])){
                        $minute = $otHourEx[1];
                        if($minute == 5){
                            $minute = 30;
                        }
                    }
                    $otHour = $otHourEx[0].'.'.$minute;
                    $activity.= '<tr>';
                    $activity.='<td>'.date("F", mktime(0, 0, 0, $el->month, 1)).'</td>';
                    $activity.='<td>'.$el->absent.'</td>';
                    $activity.='<td>'.$el->late_count.'</td>';
                    $activity.='<td>'.$el->leave.'</td>';
                    $activity.='<td>'.$el->holiday.'</td>';
                    $activity.='<td>'.$otHour.'</td>';
                    $activity.= '</tr>';
                }
            }
            $data['value'] = $activity;
            $data['type'] = 'success';
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return $data;
        }
    }

    public function attendance()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
        ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
        ->orderBy('hr_unit_name', 'desc')
        ->pluck('hr_unit_name', 'hr_unit_id');
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        return view('hr/reports/daily_activity/attendance/index', compact('unitList','areaList'));
    }

    public function attendanceReport(Request $request)
    {
        $input = $request->all();
        // dd($input);
        // return $input;
        try {
            $input['area']       = isset($request['area'])?$request['area']:'';
            $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';

            $getEmployee = array();
            $data = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];
            $totalValue = 0;

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info');
            $employeeData_sql = $employeeData->toSql();
            // shift
            if($input['report_type'] == 'working_hour'){
                $shiftData = DB::table('hr_shift');
                $shiftDataSql = $shiftData->toSql();
            }

            if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late'){
                $tableName = get_att_table($request['unit']).' AS a';
                $attData = DB::table($tableName)
                ->where('a.in_date', $request['date']);
                if($input['report_type'] == 'late'){
                    $attData->where('a.late_status', 1);
                }
            }elseif($input['report_type'] == 'absent'){
                $attData = DB::table('hr_absent AS a')
                ->where('a.date', $request['date']);
            }elseif($input['report_type'] == 'leave'){
                $attData = DB::table('hr_leave AS a')
                ->whereRaw('? between leave_from and leave_to', [$request['date']])
                ->where('a.leave_status',1);
            }elseif($input['report_type'] == 'before_absent_after_present'){
                $absentData = $this->getAbsentEmployeeFromDate($input);
                $tableName = get_att_table($request['unit']).' AS a';
                $attData = DB::table($tableName)
                ->where('a.in_date', $request['present_date'])
                ->whereIn('a.as_id', $absentData);
            }
            // employee check
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $attData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $attData->where('emp.as_unit_id',$request['unit'])
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            });

            if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'before_absent_after_present'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                });
            }elseif($input['report_type'] == 'absent'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('a.associate_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            }elseif($input['report_type'] == 'leave'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('a.leave_ass_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            }

            // $countEmployee = $attData->select('emp.as_id', DB::raw('count(*) as countEmp'))->pluck('countEmp')->first();

            if($input['report_type'] == 'ot'){
                
                $attData->where('a.ot_hour', '>', 0);
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    if($input['report_group'] == 'ot_hour'){
                        $groupBy = 'a.'.$input['report_group'];
                    }else{
                        $groupBy = 'emp.'.$input['report_group'];
                    }
                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum(ot_hour) as groupOt'))->groupBy($groupBy)->orderBy('a.ot_hour','desc');
                    $totalOtHour =  array_sum(array_column($attData->get()->toArray(),'groupOt'));
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'a.in_time', 'a.out_time', 'a.ot_hour')->orderBy('a.ot_hour','desc');
                    $totalOtHour = $attData->sum("a.ot_hour");
                    
                }
                $otHourEx = explode('.', $totalOtHour);
                $minute = '00';
                if(isset($otHourEx[1])){
                    $minute = $otHourEx[1];
                    if($minute == 50){
                        $minute = 30;
                    }
                }
                $totalValue = $otHourEx[0].'.'.$minute;
            }elseif($input['report_type'] == 'working_hour'){
                $attData->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function($join) use ($shiftData) {
                    $join->on('a.hr_shift_code', '=', 's.hr_shift_code')->addBinding($shiftData->getBindings());
                });
                // $attData->whereNotNull('a.in_time');
                // $attData->whereNotNull('a.out_time');
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    $groupBy = 'emp.'.$input['report_group'];
                    
                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum((TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time)) as groupHourDuration'))->groupBy($groupBy)->orderBy('groupHourDuration','desc');
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(),'groupHourDuration'));
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id', 'a.in_time', 'a.out_time', 's.hr_shift_break_time', 'a.ot_hour')->orderBy('a.ot_hour','desc');
                    $attData->addSelect(DB::raw('(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'));
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(),'hourDuration'));
                    
                }

                $hours = $totalWorkingMinute == 0?0:floor($totalWorkingMinute / 60);
                $minutes = $totalWorkingMinute == 0?0:($totalWorkingMinute % 60);
                $totalValue = sprintf('%02d Hours, %02d Minutes', $hours, $minutes);
            }else{
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    
                    $attData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                    
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id');
                }
            }
                
            $getEmployee = $attData->get();
            // dd($getEmployee);
            if($input['report_format'] == 1 && $input['report_group'] != null){
                $totalEmployees = array_sum(array_column($getEmployee->toArray(),'total'));
            }else{
                $totalEmployees = count($getEmployee);
            }
            if($input['report_type'] == 'working_hour'){
                $avgMin = $totalWorkingMinute == 0?0:$totalWorkingMinute / $totalEmployees;
                $aHours = $avgMin == 0?0:floor($avgMin / 60);
                $aMinutes = $avgMin == 0?0:($avgMin % 60);
                $totalAvgHour = sprintf('%02d Hours, %02d Minutes', $aHours, $aMinutes);
            }
            if($format != null && count($getEmployee) > 0 && $input['report_format'] == 0){
                $getEmployeeArray = $getEmployee->toArray();
                $formatBy = array_column($getEmployeeArray, $request['report_group']);
                $uniqueGroups = array_unique($formatBy);
                if (!array_filter($uniqueGroups)) {
                    $uniqueGroups = ['all'];
                    // $format = '';
                }
            }
            // dd($uniqueGroups);
            if($input['report_type'] == 'ot'){
                return view('hr.reports.daily_activity.attendance.ot_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees','totalValue'));
            }elseif($input['report_type'] == 'absent'){
                return view('hr.reports.daily_activity.attendance.absent_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees'));
            }elseif($input['report_type'] == 'leave'){
                return view('hr.reports.daily_activity.attendance.leave_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees'));
            }elseif($input['report_type'] == 'working_hour'){
                return view('hr.reports.daily_activity.attendance.working_hour_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'totalValue', 'totalAvgHour'));
            }elseif($input['report_type'] == 'late'){
                return view('hr.reports.daily_activity.attendance.late_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees'));
            }elseif($input['report_type'] == 'before_absent_after_present'){
                return view('hr.reports.daily_activity.attendance.before_after_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }

    public function presentAbsentReport(Request $request)
    {
        $input = $request->all();
        // dd($input);
        // return $input;
        try {
            $input['area']       = isset($request['area'])?$request['area']:'';
            $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';

            $getEmployee = array();
            $format = $request['report_group'];
            $uniqueGroups = ['all'];
            $totalValue = 0;

            // employee basic sql binding
            $employeeData = DB::table('hr_as_basic_info AS emp')
            ->where('emp.as_status', 1);
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $employeeData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $employeeData->where('emp.as_unit_id',$request['unit'])
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('emp.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('emp.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('emp.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('emp.as_floor_id',$input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('emp.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('emp.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('emp.as_subsection_id', $input['subSection']);
            });
            if($input['report_format'] == 1 && $input['report_group'] != null){
                
                $employeeData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                
            }else{
                $employeeData->select('emp.as_id', 'emp.as_gender', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id');
            }

            $getEmployee = $employeeData->get();
            $employeeAsIdData = $employeeData->pluck('emp.as_id')->toArray();
            $employeeAssIdData = $employeeData->pluck('emp.associate_id')->toArray();

            if($input['report_format'] == 1 && $input['report_group'] != null){
                $totalEmployees = array_sum(array_column($getEmployee->toArray(),'total'));
            }else{
                $totalEmployees = count($getEmployee);
            }
            return $totalEmployees;
            
            // prsent 
            $tableName = get_att_table($request['unit']).' AS a';
            $presentData = DB::table($tableName)
            ->where('a.in_date', $request['date']);

            
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }
    public function getAbsentEmployeeFromDate($input)
    {
        // employee basic sql binding
        $employeeData = DB::table('hr_as_basic_info');
        $employeeData_sql = $employeeData->toSql();

        $queryData = Absent::select('emp.as_id')
        ->where('hr_unit',$input['unit'])
        ->where('date', $input['absent_date'])

        ->when(!empty($input['area']), function ($query) use($input){
           return $query->where('emp.as_area_id',$input['area']);
        })
        ->when(!empty($input['department']), function ($query) use($input){
           return $query->where('emp.as_department_id',$input['department']);
        })
        ->when(!empty($input['line_id']), function ($query) use($input){
           return $query->where('emp.as_line_id', $input['line_id']);
        })
        ->when(!empty($input['floor_id']), function ($query) use($input){
           return $query->where('emp.as_floor_id',$input['floor_id']);
        })
        ->when($input['otnonot']!=null, function ($query) use($input){
           return $query->where('emp.as_ot',$input['otnonot']);
        })
        ->when(!empty($input['section']), function ($query) use($input){
           return $query->where('emp.as_section_id', $input['section']);
        })
        ->when(!empty($input['subSection']), function ($query) use($input){
           return $query->where('emp.as_subsection_id', $input['subSection']);
        });
        $queryData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
            $join->on('emp.associate_id','hr_absent.associate_id')->addBinding($employeeData->getBindings());
        });
        
        $absentData = $queryData->pluck('emp.as_id')->toArray();
        return $absentData;
    }

    public function attendanceAudit(Request $request)
    {
        $input = $request->all();
        if($input['date'] != null && $input['unit'] != null){
            $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
            $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        
            return view('hr/reports/daily_activity/attendance/audit', compact('unitList','areaList','input'));
        }else{
            toastr()->error('Something Wrong!');
            return back();
        }
    }
}
