<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Hr\Absent;
use App\Models\Hr\Benefits;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use Box\Spout\Writer\Style\StyleBuilder;
use DB;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Contracts\Hr\EmployeeInterface;
use App\Repository\Hr\EmployeeRepository;

class DailyActivityReportController extends Controller
{

    protected $employee;
  
    public function __construct(EmployeeRepository $employee)
    {
        $this->employee = $employee;
    }

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
        $locationList  = Location::where('hr_location_status', '1')
        ->whereIn('hr_location_id', auth()->user()->location_permissions())
        ->orderBy('hr_location_name', 'desc')
        ->pluck('hr_location_name', 'hr_location_id');
        $salaryMin = Benefits::getSalaryRangeMin();
        $salaryMax = Benefits::getSalaryRangeMax();
        $areaList  = DB::table('hr_area')->where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
        return view('hr/reports/daily_activity/attendance/index', compact('unitList','areaList', 'locationList', 'salaryMin', 'salaryMax'));
    }

    public function attendanceReport(Request $request)
    {
        $input = $request->all();
        // return $input;
        try {
            ini_set('zlib.output_compression', 1);

            $input['area']       = isset($request['area'])?$request['area']:'';
            $input['otnonot']    = isset($request['otnonot'])?$request['otnonot']:'';
            $input['department'] = isset($request['department'])?$request['department']:'';
            $input['line_id']    = isset($request['line_id'])?$request['line_id']:'';
            $input['floor_id']   = isset($request['floor_id'])?$request['floor_id']:'';
            $input['section']    = isset($request['section'])?$request['section']:'';
            $input['subSection'] = isset($request['subSection'])?$request['subSection']:'';
            $input['location'] = isset($request['location'])?$request['location']:'';


            if($input['report_type'] == 'missing_token'){
                return $this->getatttoken($input, $request);
            }

            if($input['report_type'] == 'two_day_att' || $input['report_type'] == 'executive_attendance'){
                return $this->twoDayAtt($input,$request);
            }

            if($input['report_type'] == 'att_statistics'){
                return $this->attStatistics($input,$request);
            }

            if($input['report_type'] == 'shift'){
                return $this->getShiftEmployee($input,$request);
            }

            $unit = unit_by_id();
            $location = location_by_id();
            $line = line_by_id();
            $floor = floor_by_id();
            $department = department_by_id();
            $designation = designation_by_id();
            $section = section_by_id();
            $subSection = subSection_by_id();
            $area = area_by_id();
            $uniqueGroupEmp = [];

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

            $tableName = get_att_table($request['unit']).' AS a';

            if($input['report_type'] == 'ot' || $input['report_type'] == 'present' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'in_out_missing'){
                
                $attData = DB::table($tableName)
                ->where('a.in_date', $request['date']);
                if($input['report_type'] == 'late'){
                    $attData->where('a.late_status', 1);
                }
                if($input['report_type'] == 'in_out_missing'){
                    $attData->where( function($q) use ($request){
                        $q->whereNull('a.in_time');
                        if($request['date'] != date('Y-m-d')){
                            $q->orWhereNull('a.out_time');
                        }
                        $q->orWhere('a.remarks','DSI');
                    });
                }
            }elseif($input['report_type'] == 'absent'){
                $attData = DB::table('hr_absent AS a')
                ->where('a.date', $request['date']);
            }elseif($input['report_type'] == 'leave'){
                $attData = DB::table('hr_leave AS l')
                ->whereRaw('? between leave_from and leave_to', [$request['date']])
                ->where('l.leave_status',1);
            }elseif($input['report_type'] == 'before_absent_after_present'){
                $absentData = $this->getAbsentEmployeeFromDate($input);
                $attData = DB::table($tableName)
                ->where('a.in_date', $request['present_date'])
                ->whereIn('a.as_id', $absentData);
            }elseif($input['report_type'] == 'employee'){
                $emp_day = $request['date'];
                $attData = DB::table('hr_as_basic_info as emp');
                if($emp_day == date('Y-m-d')){
                    $attData->where('emp.as_status',1);
                }else{
                    $attData->where('emp.as_doj','<=',$emp_day)
                        ->where(function($p) use($emp_day){
                        $p->where(function($q) use($emp_day){
                            $q->whereIn('emp.as_status',[2,3,4,5,6,7,8]);
                            $q->where('emp.as_status_date','>=',$emp_day);
                        });
                        $p->orWhere(function($q) use($emp_day){
                            $q->where('emp.as_status',1);
                            $q->where(function($j) use($emp_day){
                                $j->where('emp.as_status_date','<=',$emp_day);
                                $j->orWhereNull('emp.as_status_date');
                            });
                        });
                    });
                }
            }
            // employee check
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $attData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $attData->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('emp.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('emp.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('emp.as_location',$input['location']);
            })
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
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['selected'] == 'null'){
                    return $query->whereNull($input['report_group']);
                }else{
                    if($input['report_group'] == 'ot_hour'){
                        return $query->where($input['report_group'], 'LIKE', $input['selected']);
                    }else{
                        return $query->where($input['report_group'], $input['selected']);
                    }
                }
            });
            
            if($input['report_type'] == 'ot' || $input['report_type'] == 'present' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'before_absent_after_present' || $input['report_type'] == 'in_out_missing'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                });
            }elseif($input['report_type'] == 'absent'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('a.associate_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
                $getEmployee = $attData->get();
                $absentEmpA = collect($getEmployee)->pluck('associate_id');
                $day= date('j', strtotime($request['date']));
                $month= date('m', strtotime($request['date']));
                $year= date('Y', strtotime($request['date']));

                $absentShift = DB::table('hr_shift_roaster')
                ->where('shift_roaster_month', $month)
                ->where('shift_roaster_year', $year)
                ->whereIn('shift_roaster_associate_id', $absentEmpA)
                ->pluck('day_'.$day, 'shift_roaster_associate_id');

                // map with current shift
                $getEmployee = collect($getEmployee)->map(function($q) use ($absentShift){
                    if(isset($absentShift[$q->associate_id])){
                        if($absentShift[$q->associate_id] != null){
                            $q->as_shift_id = $absentShift[$q->associate_id];
                        }
                    }
                    return $q;
                });

                $selshift = collect($getEmployee)
                            ->unique('as_shift_id')
                            ->pluck('as_shift_id')->toArray();

                $input['filter_shift'] = $selshift;

                // if selected shift
                if(isset($request->filter_shift)){
                    $input['filter_shift'] = $request->filter_shift;
                    $getEmployee = collect($getEmployee)->filter(function($q) use ($input){
                        return in_array($q->as_shift_id, $input['filter_shift']);
                    })->values();

                }

                $totalEmployees = collect($getEmployee)->count();
                //dd($input['report_format'], $format);
                if($format != null && count($getEmployee) > 0){
                    $uniqueGroupEmp = collect($getEmployee)->groupBy($request['report_group'],true);

                }



                 return view('hr.reports.daily_activity.attendance.absent_report', compact('format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'absentShift', 'uniqueGroupEmp','selshift'));




                // return absent data
            }elseif($input['report_type'] == 'leave'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('l.leave_ass_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            }

            // $countEmployee = $attData->select('emp.as_id', DB::raw('count(*) as countEmp'))->pluck('countEmp')->first();
            if($input['report_group'] == 'ot_hour'){
                $groupBy = 'a.'.$input['report_group'];
                $attData->orderBy('a.ot_hour','desc');
            }else{
                $groupBy = 'emp.'.$input['report_group'];
            }
            if($input['report_type'] == 'ot'){
                
                $attData->where('a.ot_hour', '>', 0);
                if($input['report_format'] == 1 && $input['report_group'] != null){

                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum(ot_hour) as groupOt'))->groupBy($groupBy);
                    $totalOtHour =  array_sum(array_column($attData->get()->toArray(),'groupOt'));
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', 'a.in_time', 'a.remarks', 'a.out_time', 'a.ot_hour')->orderBy('a.ot_hour','desc');
                    $totalOtHour = $attData->sum("a.ot_hour");
                    
                }
                
                $totalValue = numberToTimeClockFormat($totalOtHour);
            }elseif($input['report_type'] == 'working_hour'){
                $attData->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function($join) use ($shiftData) {
                    $join->on('a.hr_shift_code', '=', 's.hr_shift_code')->addBinding($shiftData->getBindings());
                });
                // $attData->whereNotNull('a.in_time');
                // $attData->whereNotNull('a.out_time');
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    
                    
                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum((TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time)) as groupHourDuration'))->groupBy($groupBy);
                    
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(),'groupHourDuration'));
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', 'a.in_time', 'a.remarks', 'a.out_time', 's.hr_shift_break_time', 'a.ot_hour');
                    $attData->addSelect(DB::raw('(TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time) as hourDuration'));
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(),'hourDuration'));
                    
                }

                $hours = $totalWorkingMinute == 0?0:floor($totalWorkingMinute / 60);
                $minutes = $totalWorkingMinute == 0?0:($totalWorkingMinute % 60);
                $totalValue = sprintf('%02d Hours, %02d Minutes', $hours, $minutes);
            }elseif($input['report_type'] == 'employee'){
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    
                    $attData->select('emp.'.$input['report_group'], 
                        DB::raw("COUNT(CASE WHEN as_gender = 'Male' THEN 1 END) AS male"),
                        DB::raw("COUNT(CASE WHEN as_gender = 'Female' THEN 1 END) AS female"),
                        DB::raw("COUNT(*) AS total")
                    )->groupBy('emp.'.$input['report_group']);
                    
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code','emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id','emp.as_doj');
                }
            }else{
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    
                    $attData->select('emp.'.$input['report_group'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_group']);
                    
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_shift_id', 'emp.as_oracle_code','emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id');
                    if($input['report_type'] == 'leave'){
                        $attData->addSelect('l.leave_type');
                    }

                    if($input['report_type'] == 'in_out_missing' || $input['report_type'] == 'present' || $input['report_type'] == 'late'){
                        $attData->addSelect('a.in_time', 'a.out_time', 'a.remarks');
                    }
                }
            }
            if($input['report_group'] == 'as_section_id' || $input['report_group'] == 'as_subsection_id'){
                $attData->orderBy('emp.as_department_id', 'asc');
            }else{
                $attData->orderBy($groupBy, 'asc');
            }
            if($input['report_group'] == 'as_subsection_id'){
                $attData->orderBy('emp.as_section_id', 'asc');
            } 
            $getEmployee = $attData->get();
            
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

                if($request['report_group'] == 'ot_hour'){
                    $uniqueGroupEmp = collect($getEmployee)->groupBy(function ($item) {
                            return (string) $item->ot_hour;
                    },true);
                }else{
                    $uniqueGroupEmp = collect($getEmployee)->groupBy($request['report_group'],true);
                }

            }

            if($input['report_type'] == 'ot'){
                return view('hr.reports.daily_activity.attendance.ot_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees','totalValue', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }elseif($input['report_type'] == 'in_out_missing'){
                return view('hr.reports.daily_activity.attendance.in_out_mis_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }elseif($input['report_type'] == 'employee'){
                return view('hr.reports.daily_activity.attendance.employee', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }elseif($input['report_type'] == 'leave'){
                return view('hr.reports.daily_activity.attendance.leave_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }elseif($input['report_type'] == 'working_hour'){
                return view('hr.reports.daily_activity.attendance.working_hour_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'totalValue', 'totalAvgHour', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }elseif($input['report_type'] == 'late' || $input['report_type'] == 'present'){
                return view('hr.reports.daily_activity.attendance.late_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }elseif($input['report_type'] == 'before_absent_after_present'){
                return view('hr.reports.daily_activity.attendance.before_after_report', compact('uniqueGroups', 'format', 'getEmployee', 'input', 'totalEmployees', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area', 'uniqueGroupEmp'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
            return 'error';
        }
    }

    public function twoDayAtt($input,$request)
    {

        $date[0] = $request['date'];
        $date[1] = \Carbon\Carbon::parse($request['date'])->subDays(1)->toDateString();
        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();
        $short_designation = shortdesignation_by_id();

        
        $queryData = DB::table('hr_as_basic_info AS b')
            ->select('b.as_unit_id', 'b.as_area_id','b.as_location', 'b.as_emp_type_id','b.as_id', 'b.as_gender', 'b.associate_id', 'b.as_line_id', 'b.as_designation_id','b.as_oracle_code', 'b.as_department_id', 'b.as_floor_id', 'b.as_pic', 'b.as_name', 'b.as_contact', 'b.as_section_id','b.as_subsection_id','b.as_oracle_sl','b.temp_id')
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereNotIn('b.associate_id', config('base.ignore_salary'))
            ->whereIn('b.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
                if($input['unit'] == 145){
                    return $query->whereIn('b.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('b.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('b.as_location',$input['location']);
            })
            ->when(!empty($input['area']), function ($query) use($input){
               return $query->where('b.as_area_id',$input['area']);
            })
            ->when(!empty($input['department']), function ($query) use($input){
               return $query->where('b.as_department_id',$input['department']);
            })
            ->when(!empty($input['line_id']), function ($query) use($input){
               return $query->where('b.as_line_id', $input['line_id']);
            })
            ->when(!empty($input['floor_id']), function ($query) use($input){
               return $query->where('b.as_floor_id',$input['floor_id']);
            })
            ->when($request['otnonot']!=null, function ($query) use($input){
               return $query->where('b.as_ot',$input['otnonot']);
            })
            ->when(!empty($input['section']), function ($query) use($input){
               return $query->where('b.as_section_id', $input['section']);
            })
            ->when(!empty($input['subSection']), function ($query) use($input){
               return $query->where('b.as_subsection_id', $input['subSection']);
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['report_group'] != 'as_line_id' && $input['report_group'] != 'as_floor_id'){
                    if($input['selected'] == 'null' ){
                        return $query->whereNull($input['report_group']);
                    }else{
                        return $query->where('b.'.$input['report_group'], $input['selected']);
                    }
                }
            });

            if($input['report_type'] == 'executive_attendance'){
                // benefit
                $benefitData = DB::table('hr_benefits');
                $benefitData_sql = $benefitData->toSql();
                $queryData->leftjoin(DB::raw('(' . $benefitData_sql. ') AS ben'), function($join) use ($benefitData) {
                    $join->on('ben.ben_as_id','b.associate_id')->addBinding($benefitData->getBindings());
                })->whereBetween('ben.ben_current_salary', [$input['min_sal'], $input['max_sal']]);
            }
            $queryData->where('b.as_status', 1);
            if($input['report_type'] == 'executive_attendance'){
                $queryData->orderBy('b.as_unit_id', 'ASC')->orderBy('b.as_area_id', 'ASC');
            }else{

                $queryData->orderBy('b.as_oracle_sl', 'ASC')->orderBy('b.temp_id', 'ASC');

            }
            $getEmployee = $queryData->get();



        $avail = $getEmployee->pluck('associate_id');



        // modify data with current line & floor
        $lineInfo = DB::table('hr_station')
                    ->select('associate_id','changed_floor','changed_line')
                    ->whereIn('associate_id',$avail)
                    ->whereDate('start_date','<=',$date[0])
                    ->where(function ($q) use($date) {
                      $q->whereDate('end_date', '>=', $date[0]);
                      $q->orWhereNull('end_date');
                    })
                    ->get()->keyBy('associate_id');

        
        if(count($lineInfo) > 0){
            $getEmployee = $getEmployee->map(function ($arr) use ($lineInfo, $line) {
                $as_id = $arr->associate_id;
                if(isset($lineInfo[$as_id])){
                    $arr->df_line_id = $arr->as_line_id;
                    $arr->df_floor_id = $arr->as_floor_id;
                    $arr->as_line_id = $lineInfo[$as_id]->changed_line;
                    $arr->as_floor_id = $lineInfo[$as_id]->changed_floor;

                }
                if(isset($line[$arr->as_line_id])){
                    $arr->ordby = $line[$arr->as_line_id]['hr_line_name'];
                }else{
                    $arr->ordby = '';
                }
                return $arr;
            });
        }
        

            //dd($input['selected']);
        if(!empty($input['selected']) && ($input['report_group'] == 'as_line_id' || $input['report_group'] == 'as_floor_id')){
            $input['sel_type'] = $input['report_group'] == 'as_line_id'? 'ordby':$input['report_group'];
            $getEmployee = $getEmployee->filter(function($arr) use ($input){
                if($input['selected'] == 'null'){

                    return $arr->{$input['sel_type']} == '';
                }else{
                    return $arr->{$input['sel_type']} == $input['selected'];
                }
            })->values();
        }

        $avail_as = $getEmployee->pluck('as_id');


        if($input['report_group'] == 'as_line_id'){
            $uniqueGroups = collect($getEmployee)
                            //->sortBy('ordby')
                            ->groupBy('ordby', true)->toArray();
            ksort($uniqueGroups);
        }else{

            $uniqueGroups = collect($getEmployee)
                            //->sortBy('ordby')
                            ->groupBy($request['report_group'], true);
        }

        $format = $request['report_group'];
        
        $gUnit = [];
        $unitWiseEId = [];
        $attPr = [];
        if($input['report_type'] == 'executive_attendance'){
            $gUnit = array_column($getEmployee->toArray(), 'as_unit_id');
            $gUnit = array_unique($gUnit);
            // $unitWiseEId = $getEmployee->groupBy('as_unit_id', true);
            $unitWiseEId = collect($getEmployee)->groupBy('as_unit_id',true)->map(function($row) {
                return collect($row)->pluck('as_id');
            });

            foreach ($unitWiseEId as $key => $value) {
                $table = get_att_table($key);
                $attp = DB::table($table)
                        ->whereIn('in_date', $date)
                        ->whereIn('as_id', $value)
                        ->get();
                if(count($attp) > 0){
                    $attp = $attp->groupBy('as_id', true)
                    ->map(function($row) {
                        return collect($row)->keyBy('in_date');
                    })->toArray();

                    foreach ($attp as $key => $value) {
                        $attPr[$key] = $value;
                    }
                }
            }

            // $pr = array_reduce($attPr, 'array_merge', array());
            $pr = $attPr;


        }else{
            $table = get_att_table($input['unit']);

            $pr = DB::table($table)
                    ->whereIn('in_date', $date)
                    ->whereIn('as_id', $avail_as)
                    ->get();
            if(count($pr) > 0){
                $pr = $pr->groupBy('as_id', true)
                    ->map(function($row) {
                        return collect($row)->keyBy('in_date');
                    });
            }
        }

        $ab = DB::table('hr_absent')
                ->whereIn('date', $date)
                ->whereIn('associate_id', $avail)
                ->get();
        if(count($ab) > 0){
            $ab = $ab->groupBy('associate_id', true)
                ->map(function($row) {
                    return collect($row)->keyBy('date');
                });
        }

        $lv = DB::table('hr_leave')
                ->selectRaw("
                    leave_ass_id,
                    leave_type,
                    (CASE 
                        WHEN leave_from <= '".$date[0]."' AND leave_to >= '".$date[0]."' AND leave_from <= '".$date[1]."' AND leave_to >= '".$date[1]."' THEN 2 
                        WHEN leave_from <= '".$date[0]."' AND leave_to >= '".$date[0]."' THEN '".$date[0]."' 
                        WHEN leave_from <= '".$date[1]."' AND leave_to >= '".$date[1]."' THEN '".$date[1]."'
                    END) AS lv
                ")
                ->whereIn('leave_ass_id', $avail)
                ->where('leave_status', 1)
                ->where(function($q) use ($date){
                    $q->where('leave_from', "<=", $date[0]);
                    $q->where('leave_to', ">=", $date[0]);
                })
                ->orWhere(function($q) use ($date){
                    $q->where('leave_from', "<=", $date[1]);
                    $q->where('leave_to', ">=", $date[1]);
                })
                ->get();

        if(count($lv) > 0){
            $lv = $lv->groupBy('leave_ass_id', true)
                ->map(function($row) {
                    return collect($row)->keyBy('lv');
                });
        }

        $do = DB::table('holiday_roaster')
                ->whereIn('date', $date)
                ->whereIn('as_id', $avail)
                ->where('remarks', 'Holiday')
                ->get();

        if(count($do) > 0){
            $do = $do->groupBy('as_id', true)
                ->map(function($row) {
                    return collect($row)->keyBy('date');
                });
        }

        ini_set('zlib.output_compression', 1);
        if($input['report_type'] == 'executive_attendance'){
            return view('hr.reports.daily_activity.attendance.unit_wise_att', compact('uniqueGroups', 'getEmployee', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','pr','ab','lv','do','format','date','short_designation','avail_as'))->render();
        }
        return view('hr.reports.daily_activity.attendance.two_day_att', compact('uniqueGroups', 'getEmployee', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','pr','ab','lv','do','format','date','short_designation','avail_as'))->render();
    

    }

    public function attStatistics($input,$request)
    {
        $date = $request['date'];
        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();
        
        $getEmployee = $this->employee->getEmployees($input,$date);



        $avail = $getEmployee->pluck('associate_id');
        


        // modify data with current line & floor
        $lineInfo = DB::table('hr_station')
                    ->select('associate_id','changed_floor','changed_line')
                    ->whereIn('associate_id',$avail)
                    ->whereDate('start_date','<=',$date)
                    ->where(function ($q) use($date) {
                      $q->whereDate('end_date', '>=', $date);
                      $q->orWhereNull('end_date');
                    })
                    ->get()->keyBy('associate_id');

        if(count($lineInfo) > 0){
            $getEmployee = $getEmployee->map(function ($arr) use ($lineInfo) {
                $as_id = $arr->associate_id;
                if(isset($lineInfo[$as_id])){
                    $arr->df_line_id = $arr->as_line_id;
                    $arr->df_floor_id = $arr->as_floor_id;
                    $arr->as_line_id = $lineInfo[$as_id]->changed_line;
                    $arr->as_floor_id = $lineInfo[$as_id]->changed_floor;

                }
                return $arr;
            });
        }

        if(!empty($input['selected']) && ($input['report_group'] == 'as_line_id' || $input['report_group'] == 'as_floor_id')){
            $getEmployee = $getEmployee->filter(function($arr) use ($input){
                return $arr->{$input['report_group']} == $input['selected'];
            })->values();
        }

        $avail_as = $getEmployee->pluck('as_id');
        $avail = $getEmployee->pluck('associate_id');

        $uniqueGroups = $getEmployee->groupBy($request['report_group'], true);

        $format = $request['report_group'];

        $table = get_att_table($input['unit']);

        $pr = DB::table($table.' AS a')
                ->select('a.*','b.associate_id')
                ->leftjoin('hr_as_basic_info as b','b.as_id','a.as_id')
                ->where('a.in_date', $date)
                ->whereIn('a.as_id', $avail_as)
                ->orderBy('temp_id', 'ASC')
                ->get()->keyBy('associate_id');

        $pr_emps = collect($pr)->pluck('associate_id');

        $ab = DB::table('hr_absent AS a')
                ->where('date', $date)
                ->whereIn('associate_id', $avail)
                ->get()->keyBy('associate_id');

        $ab_emps = collect($ab)->pluck('associate_id');

        $lv = DB::table('hr_leave')
                ->whereIn('leave_ass_id', $avail)
                ->where('leave_from', "<=", $date)
                ->where('leave_to', ">=", $date)
                ->get()->keyBy('leave_ass_id');

        $lv_emps = collect($lv)->pluck('leave_ass_id');

        $do = DB::table('holiday_roaster')
                ->where('date', $date)
                ->whereIn('as_id', $avail)
                ->where('remarks', 'Holiday')
                ->get()->keyBy('as_id');

        $do_emps = collect($do)->pluck('as_id');

        $count = [];
        if($input['report_format'] == 1 && $input['report_group'] != null){
            foreach($uniqueGroups as $key => $item) {
                $emp = collect($item)->pluck('associate_id');
                $count[$key]['total'] = count($emp);
                $count[$key]['present'] = count($emp->intersect($pr_emps));
                $count[$key]['leave'] = count($emp->intersect($lv_emps));
                $count[$key]['holiday'] = count($emp->intersect($do_emps));
                $count[$key]['absent'] = count($emp) - $count[$key]['present'] - $count[$key]['leave'] - $count[$key]['holiday'];
                
            }
        }

        return view('hr.reports.daily_activity.attendance.att_statistics', compact('avail','uniqueGroups', 'getEmployee', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','pr','ab','lv','do','format','date','avail_as','count'));
    }


    public function getShiftEmployee($input, $request)
    {
        $date = $request['date']??date('Y-m-d');
        $day = 'day_'.((int)date('d',strtotime($date)));
        $format = $request['report_group']??'';
        $shift_id = isset($request['shift_id'])?$request['shift_id']:null;

        $unit = unit_by_id();
        $location = location_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $area = area_by_id();


        $employees = $this->employee->getEmployees($input, $date);
        $associates = collect($employees)->pluck('associate_id'); 

        $changed = DB::table('hr_shift_roaster')
            ->where('shift_roaster_month', date('n',strtotime($date)))
            ->where('shift_roaster_year', date('Y',strtotime($date)))
            ->whereIn('shift_roaster_associate_id', $associates)
            ->pluck($day, 'shift_roaster_associate_id');
        
        $employees = collect($employees)
            ->map(function($q) use($changed){
                if(isset($changed[$q->associate_id])){
                    $q->as_shift_id = $changed[$q->associate_id] != null?$changed[$q->associate_id]:$q->as_shift_id;
                }
                return $q;
                
            });
        if($shift_id != ''){
            $employees = collect($employees)
                ->where('as_shift_id', $shift_id)
                ->all();
        }

        $totalEmployees = count($employees);
            
        $employees = collect($employees)   
            ->sortBy('as_shift_id')
            ->groupBy('as_shift_id')
            ->all();

        if($input['report_format'] == 1){
            $employees = collect($employees)
                ->map(function($q){
                    $p = (object)[];
                    $p->male = collect($q)->where('as_gender','Male')->count();
                    $p->female = collect($q)->where('as_gender','Female')->count();
                    return $p;
                });


        }
        ini_set('zlib.output_compression', 1);

        return view('hr.reports.daily_activity.attendance.assigned_shift', compact('employees', 'totalEmployees', 'input', 'unit', 'location', 'line', 'floor', 'department', 'designation', 'section', 'subSection', 'area','date','format'))->render();
    }

   


    public function getatttoken($input, $request)
    {

        $associates = $this->employee->getEmployees($input, $request['date'])
                            ->pluck('associate_id');

        //dd($associates);
        $tableName = get_att_table($request['unit']).' AS a';

        $unit = unit_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();

        $attData = DB::table($tableName)
                    ->select('b.as_name','b.as_designation_id','b.as_department_id','b.as_section_id','a.in_time','a.out_time','a.remarks','b.as_oracle_code','b.as_unit_id','b.associate_id')
                    ->leftJoin('hr_as_basic_info AS b','b.as_id','a.as_id')
                    ->where('a.in_date', $request['date'])
                    ->whereIn('b.associate_id', $associates)
                    ->where( function($q) use ($request){
                        $q->whereNull('a.in_time');
                        if($request['date'] != date('Y-m-d')){
                            $q->orWhereNull('a.out_time');
                        }
                        $q->orWhere('a.remarks','DSI');
                    })
                    ->orderBy('b.as_unit_id', 'ASC')
                    ->get();

        /*$absData = DB::table('hr_absent AS a')
                    ->select('b.as_name','b.as_designation_id','b.as_department_id','b.as_section_id','b.as_oracle_code','b.as_unit_id','b.associate_id')
                    ->leftJoin('hr_as_basic_info AS b','b.associate_id','a.associate_id')
                    ->where('a.date', $request['date'])
                    ->whereIn('b.associate_id', $associates)
                    ->orderBy('b.as_unit_id', 'ASC')
                    ->get();*/

        return view('hr.common.in_out_token', compact('attData','unit','department','designation','section','request'));
    }



    public function activityProcess($input)
    {
        $data['type'] = 'success';
        try {
            $input['area']       = isset($input['area'])?$input['area']:'';
            $input['otnonot']    = isset($input['otnonot'])?$input['otnonot']:'';
            $input['department'] = isset($input['department'])?$input['department']:'';
            $input['line_id']    = isset($input['line_id'])?$input['line_id']:'';
            $input['floor_id']   = isset($input['floor_id'])?$input['floor_id']:'';
            $input['section']    = isset($input['section'])?$input['section']:'';
            $input['subSection'] = isset($input['subSection'])?$input['subSection']:'';

            $getEmployee = array();
            $format = $input['report_group'];
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
            $tableName = get_att_table($input['unit']).' AS a';
            if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'present'){
                
                $attData = DB::table($tableName)
                ->where('a.in_date', $input['date']);
                if($input['report_type'] == 'late'){
                    $attData->where('a.late_status', 1);
                }
            }elseif($input['report_type'] == 'absent'){
                $attData = DB::table('hr_absent AS a')
                ->where('a.date', $input['date']);
            }elseif($input['report_type'] == 'leave'){
                $attData = DB::table('hr_leave AS l')
                ->whereRaw('? between leave_from and leave_to', [$input['date']])
                ->where('l.leave_status',1);
            }elseif($input['report_type'] == 'before_absent_after_present'){
                $absentData = $this->getAbsentEmployeeFromDate($input);
                $attData = DB::table($tableName)
                ->where('a.in_date', $input['present_date'])
                ->whereIn('a.as_id', $absentData);
            }
            // employee check
            if($input['report_format'] == 0 && !empty($input['employee'])){
                $attData->where('emp.associate_id', 'LIKE', '%'.$input['employee'] .'%');
            }
            $attData->whereIn('emp.as_unit_id', auth()->user()->unit_permissions())
            ->whereIn('emp.as_location', auth()->user()->location_permissions())
            ->when(!empty($input['unit']), function ($query) use($input){
               if($input['unit'] == 145){
                    return $query->whereIn('emp.as_unit_id',[1, 4, 5]);
                }else{
                    return $query->where('emp.as_unit_id',$input['unit']);
                }
            })
            ->when(!empty($input['location']), function ($query) use($input){
               return $query->where('emp.as_location',$input['location']);
            })
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
            })
            ->when(!empty($input['selected']), function ($query) use($input){
                if($input['selected'] == 'null'){
                    return $query->whereNull($input['report_group']);
                }else{
                    if($input['report_group'] == 'ot_hour'){
                        return $query->where($input['report_group'], 'LIKE', $input['selected']);
                    }else{
                        return $query->where($input['report_group'], $input['selected']);
                    }
                }
            });

            if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'present' || $input['report_type'] == 'late' || $input['report_type'] == 'before_absent_after_present'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('a.as_id', '=', 'emp.as_id')->addBinding($employeeData->getBindings());
                });
            }elseif($input['report_type'] == 'absent'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('a.associate_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            }elseif($input['report_type'] == 'leave'){
                $attData->leftjoin(DB::raw('(' . $employeeData_sql. ') AS emp'), function($join) use ($employeeData) {
                    $join->on('l.leave_ass_id', '=', 'emp.associate_id')->addBinding($employeeData->getBindings());
                });
            }

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
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', 'a.in_time', 'a.out_time', 'a.ot_hour')->orderBy('a.ot_hour','desc');
                    $totalOtHour = $attData->sum("a.ot_hour");
                    
                }
                
                $totalValue = numberToTimeClockFormat($totalOtHour);
            }elseif($input['report_type'] == 'working_hour'){
                $attData->leftjoin(DB::raw('(' . $shiftDataSql. ') AS s'), function($join) use ($shiftData) {
                    $join->on('a.hr_shift_code', '=', 's.hr_shift_code')->addBinding($shiftData->getBindings());
                });
                // $attData->whereNotNull('a.in_time');
                // $attData->whereNotNull('a.out_time');
                if($input['report_format'] == 1 && $input['report_group'] != null){
                    $groupBy = 'emp.'.$input['report_group'];
                    
                    $attData->select($groupBy, DB::raw('count(*) as total'), DB::raw('sum((TIMESTAMPDIFF(minute, in_time, out_time) - s.hr_shift_break_time)) as groupHourDuration'))->groupBy($groupBy)->orderBy('groupHourDuration','desc')->orderBy('emp.as_section_id', 'desc');
                    $totalWorkingMinute =  array_sum(array_column($attData->get()->toArray(),'groupHourDuration'));
                }else{
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_oracle_code', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id','emp.as_subsection_id', 'a.in_time', 'a.out_time', 's.hr_shift_break_time', 'a.ot_hour')->orderBy('emp.as_subsection_id', 'asc');
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
                    $attData->select('emp.as_id', 'emp.as_gender', 'emp.as_oracle_code','emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_subsection_id','emp.as_section_id');
                    if($input['report_type'] == 'leave'){
                        $attData->addSelect('l.leave_type');
                    }
                    if($input['report_type'] == 'in_out_missing' || $input['report_type'] == 'present' || $input['report_type'] == 'late'){
                        $attData->addSelect('a.in_time', 'a.out_time', 'a.remarks');
                    }
                }
            }
                
            $getEmployee = $attData->get();
            $data['value'] = $getEmployee;
            return $data;
        } catch (\Exception $e) {
            $data['type'] = 'error';
            $data['msg'] = $e->getMessage();
            return $data;
        }
    }
    public function activityExcle(Request $request)
    {
        $input = $request->all();
        $unit = unit_by_id();
        $line = line_by_id();
        $floor = floor_by_id();
        $department = department_by_id();
        $designation = designation_by_id();
        $section = section_by_id();
        $subSection = subSection_by_id();
        $result = $this->activityProcess($input);
        if($result['type'] == 'success'){
            $excel = [];

            foreach ($result['value'] as $key => $value) {
                $dataValue = array(
                    'Name' => $value->as_name,
                    'Associate ID' => $value->associate_id,
                    'Oracle ID' => $value->as_oracle_code,
                    'Designation' => $designation[$value->as_designation_id]['hr_designation_name']??'',
                    'Department' => $department[$value->as_department_id]['hr_department_name']??'',
                    'Section' => $section[$value->as_section_id]['hr_section_name']??'',
                    'Sub Section' => $subSection[$value->as_subsection_id]['hr_subsec_name']??'',
                    'Floor' => $floor[$value->as_floor_id]['hr_floor_name']??'',
                    'Line' => $line[$value->as_line_id]['hr_line_name']??''

                );
                if($input['report_type'] == 'ot' || $input['report_type'] == 'working_hour' || $input['report_type'] == 'late' || $input['report_type'] == 'present'){
                    $dataValue['In Time'] = ($value->in_time != null?date('H:i:s', strtotime($value->in_time)):'');
                    $dataValue['Out Time'] = ($value->out_time != null?date('H:i:s', strtotime($value->out_time)):'');
                }
                if($input['report_type'] == 'working_hour'){
                    $dataValue['Break Time'] = $value->hr_shift_break_time;
                    $dataValue['Working Hour'] = round($value->hourDuration/60,2);
                }
                if($input['report_type'] == 'ot'){
                    $dataValue['OT Hour'] = $value->ot_hour;
                }

                $excel[$value->associate_id] = $dataValue;
            }
            $fileName = ($input['unit'] == 145?'MBM + MBF+MBM2':$unit[$input['unit']]['hr_unit_short_name']).' - '.$input['report_type'].' - '.$input['date'].'.xlsx';
            $header_style = (new StyleBuilder())->setFontBold()->build();

            return (new FastExcel(collect($excel)))->headerStyle($header_style)->download($fileName);
        }else{
            return 'Something Wrong, Please try again';
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

        $queryData = Absent::select('emp.as_id');
        if($input['unit'] == 145){
            $queryData->whereIn('hr_unit',[1,4,5]);
        }else{
            $queryData->where('hr_unit',$input['unit']);
        }
        $queryData->where('date', $input['absent_date'])
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
