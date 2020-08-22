<?php

namespace App\Http\Controllers\Hr\Reports;

use App\Http\Controllers\Controller;
use App\Models\Hr\Absent;
use App\Models\Hr\Employee;
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
            ->whereDate('date', $request['absent_date'])
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
            $format = $request['report_format'];
            $uniqueGroups = ['all'];
            if(count($absentData) > 0){
            	$tableName = get_att_table($request['unit']).' AS a';
            	$attData = DB::table($tableName)
    				->where('emp.as_unit_id',$request['unit'])
		            ->whereDate('a.in_date', $request['present_date'])
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
                    if($input['report_type'] == 1 && $input['report_format'] != null){
                        $attData->select('emp.'.$input['report_format'], DB::raw('count(*) as total'))->groupBy('emp.'.$input['report_format']);
                    }else{
                        $attData->select('emp.as_id', 'emp.associate_id', 'emp.as_line_id', 'emp.as_designation_id', 'emp.as_department_id', 'emp.as_floor_id', 'emp.as_pic', 'emp.as_name', 'emp.as_contact', 'emp.as_section_id');
                    }
    			$getEmployee = $attData->get();
            	if($format != null && count($getEmployee) > 0 && $input['report_type'] == 0){
            		$getEmployeeArray = $getEmployee->toArray();
            		$formatBy = array_column($getEmployeeArray, $request['report_format']);
            		$uniqueGroups = array_unique($formatBy);
            	}
            }
            
            return view('hr.reports.daily_activity.before_after_report', compact('uniqueGroups', 'format', 'getEmployee', 'input'));
    	} catch (\Exception $e) {
            $bug = $e->getMessage();
            return $bug;
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
                    $activity.= '<tr>';
                    $activity.='<td>'.date("F", mktime(0, 0, 0, $el->month, 1)).'</td>';
                    $activity.='<td>'.$el->absent.'</td>';
                    $activity.='<td>'.$el->late_count.'</td>';
                    $activity.='<td>'.$el->leave.'</td>';
                    $activity.='<td>'.$el->holiday.'</td>';
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
}
