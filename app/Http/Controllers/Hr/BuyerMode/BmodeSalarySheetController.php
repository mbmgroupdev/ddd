<?php

namespace App\Http\Controllers\Hr\BuyerMode;

use Illuminate\Http\Request;
use App\Helpers\Custom;
use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\Floor;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\Unit;
use App\Models\Hr\YearlyHolyDay;
use Attendance2;
use Attendance;
use Carbon\Carbon;
use DB;

class BmodeSalarySheetController extends Controller
{
  public function convertMonthNameToNumber($month)
	{
		return Carbon::parse("1 $month")->month;
	}
  public function index()
  {
      try {
        $data['getEmployees']  = Employee::getSelectIdNameEmployee();
          $data['unitList']      = Unit::where('hr_unit_status', '1')
              ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
              ->pluck('hr_unit_name', 'hr_unit_id');
          $data['areaList']      = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
          $data['floorList']     = Floor::getFloorList();
          $data['deptList']      = Department::getDeptList();
          $data['sectionList']   = Section::getSectionList();
          $data['subSectionList'] = Subsection::getSubSectionList();
          $data['salaryMin']      = Benefits::getSalaryRangeMin();
          $data['salaryMax']      = Benefits::getSalaryRangeMax();
          $data['getYear']       = HrMonthlySalary::select('year')->distinct('year')->orderBy('year', 'desc')->pluck('year');
        return view('hr.buyermode.salary_sheet_custom', $data);
      } catch(\Exception $e) {
          return $e->getMessage();
      }
  }
  public function ajaxGetEmployee(Request $request)
  {

      $data = $request->all();
      $getUnit = Unit::getUnitNameBangla($data['unit']);
      $month = date("F", mktime(0, 0, 0, $data['month'], 10));
      // return $getUnit;
      try {
          // $query = Employee::getEmployeeWiseSalarySheet($data);
          // $getSalaryList = $query->get();
          //dd($month);exit;
          $getSalaryList = DB::table('hr_as_basic_info')
                              // ->select(
                              //   'hr_as_basic_info.*',
                              //   'hr_buyer_salary_template.*',
                              //   'hr_salary_add_deduct.*',
                              //   'hr_monthly_salary.*',
                              //   'hr_employee_bengali.*'
                              //   )
                              ->where('as_unit_id',$data['unit'])
                              ->where('as_status', 1)
                              ->leftJoin('hr_employee_bengali','hr_as_basic_info.associate_id','hr_employee_bengali.hr_bn_associate_id')
                              ->leftJoin('hr_monthly_salary','hr_as_basic_info.associate_id','hr_monthly_salary.as_id')
                              ->leftJoin('hr_buyer_salary_template','hr_monthly_salary.id','hr_buyer_salary_template.hr_monthly_salary_id')
                              ->leftJoin('hr_salary_add_deduct','hr_monthly_salary.salary_add_deduct_id','hr_salary_add_deduct.id')
                              ->where('hr_monthly_salary.month',$this->convertMonthNameToNumber($month))
                              ->where('hr_monthly_salary.year',$data['year'])

                              ->when(!empty($data['min_sal']), function ($query) use($data){
                                     return $query->where('hr_monthly_salary.gross', '>=', $data['min_sal']);
                                   })
                               ->when(!empty($data['max_sal']), function ($query) use($data){
                                      return $query->where('hr_monthly_salary.gross', '<=', $data['max_sal']);
                                    })
                              ->when(!empty($data['floor']), function ($query) use($data){
                                     return $query->where('hr_as_basic_info.as_floor_id', $data['floor']);
                                   })
                             ->when(!empty($data['area']), function ($query) use($data){
                                    return $query->where('hr_as_basic_info.as_area_id', $data['area']);

                                  })
                              ->when(!empty($data['department']), function ($query) use($data){
                                      return $query->where('hr_as_basic_info.as_department_id', $data['department']);

                                   })
                               ->when(!empty($data['section']), function ($query) use($data){
                                       return $query->where('hr_as_basic_info.as_section_id', $data['section']);
                                    })
                                ->when(!empty($data['sub_section']), function ($query) use($data){
                                        return $query->where('hr_as_basic_info.as_subsection_id', $data['sub_section']);

                                     })
                              ->where('hr_buyer_salary_template.hr_buyer_template_id',auth()->user()->buyer_template_permission())
                              ->get();
          $locationDataSet = $getSalaryList->toArray();
          $locationList = array_column($locationDataSet, 'as_location');
          $uniqueLocation = array_unique($locationList);
          // $title = $getUnit->hr_unit_name_bn;
          $pageHead['current_date']   = date('Y-m-d');
          $pageHead['current_time']   = date('H:i');
          $pageHead['pay_date']       = $data['disbursed_date'];
          $pageHead['unit_name']      = $getUnit->hr_unit_name_bn;
          $pageHead['for_date']       = Custom::engToBnConvert($month.' - '.$data['year']);
          $pageHead['floor_name']     = $data['floor'];
          $pageHead = (object)$pageHead;
          return view('hr.common.employee_salary_sheet_buyer', compact('uniqueLocation', 'getSalaryList', 'pageHead'));
      } catch (\Exception $e) {
          return 'error';

      }
  }

  public function individualSearch(Request $request)
  {
    $input = $request->all();
    try {
          // form explode
          $formExplode = explode('-', $input['form_date']);
          $input['formMonth'] = $this->convertMonthNameToNumber($formExplode[0]);
          $input['formYear']  = $formExplode[1];
          // to explode
          $toExplode          = explode('-', $input['to_date']);
          $input['toMonth']   = $this->convertMonthNameToNumber($toExplode[0]);
          $input['toYear']    = $toExplode[1];

          // $query              = Employee::getSingleEmployeeWiseSalarySheet($input);
          // $getSalaryList      = $query->get();

          $getSalaryList = DB::table('hr_as_basic_info')
                              // ->select(
                              //   'hr_as_basic_info.*',
                              //   'hr_buyer_salary_template.*',
                              //   'hr_salary_add_deduct.*',
                              //   'hr_monthly_salary.*',
                              //   'hr_employee_bengali.*'
                              //   )
                              ->where('hr_as_basic_info.associate_id',$input['as_id'])
                              ->where('hr_as_basic_info.as_status',1)
                              ->leftJoin('hr_monthly_salary','hr_as_basic_info.associate_id','hr_monthly_salary.as_id')
                              ->leftJoin('hr_employee_bengali','hr_as_basic_info.associate_id','hr_employee_bengali.hr_bn_associate_id')
                              ->leftJoin('hr_buyer_salary_template','hr_monthly_salary.id','hr_buyer_salary_template.hr_monthly_salary_id')
                              ->leftJoin('hr_salary_add_deduct','hr_monthly_salary.salary_add_deduct_id','hr_salary_add_deduct.id')
                              ->when(!empty($input['form_date']), function ($query) use($input){
                                     return $query->whereBetween('hr_monthly_salary.month', array($input['formMonth'], $input['toMonth']));
                                })
                              ->when(!empty($input['form_date']), function ($query) use($input){
                                     return $query->whereBetween('hr_monthly_salary.year', array($input['formYear'], $input['toYear']));
                                })
                              ->where('hr_buyer_salary_template.hr_buyer_template_id',auth()->user()->buyer_template_permission())
                              ->get();
                              //dd($getSalaryList);exit;

          $locationDataSet    = $getSalaryList->toArray();
          $locationList       = array_column($locationDataSet, 'as_location');
          $uniqueLocation     = array_unique($locationList);
          $getEmployee        = Employee::getEmployeeAssociateIdWise($input['as_id']);
          $pageHead['current_date']   = date('Y-m-d');
          $pageHead['current_time']   = date('H:i');
          $pageHead['pay_date']       = '';
          $pageHead['unit_name']      = $getEmployee->unit['hr_unit_name_bn'];
          $pageHead['for_date']       = $input['form_date'].' - '.$input['to_date'];
          //$pageHead['total_work_day'] = $input['disbursed_date'];
          $pageHead['floor_name']     = $getEmployee->floor['hr_floor_name_bn'];

          $pageHead = (object) $pageHead;
          return view('hr.common.employee_salary_sheet_buyer', compact('uniqueLocation', 'getSalaryList', 'pageHead'));
      } catch (\Exception $e) {
         return $e;
      }
  }

  public function multiSearch(Request $request)
  {
      $input = $request->all();
      $input['unit'] = intval($input['unit']);

      try {
          //getEmployee Unit, Floor, Area, Deparment, Section, SubScetion with
          $getEmployees = Employee::getEmployeeFilterWise($input);
          $getUnit = Unit::getUnitNameBangla($input['unit']);

          if($getUnit != null){
              $unitName = $getUnit->hr_unit_name_bn;
          } else {
              $unitName = '';
          }
          $getFloor = Floor::getFloorNameBangla($input['floor']);

          if($getFloor != null){
              $floorName = $getFloor->hr_floor_name_bn;
          } else {
              $floorName = '';
          }

          $pageHead['current_date']   = date('d-m-Y');
          $pageHead['current_time']   = date('H:i');
          $pageHead['pay_date']       = $input['disbursed_date'];
          $pageHead['unit_name']      = $unitName;
          $pageHead['for_date']       = $input['month'].' - '.$input['year'];
          //$pageHead['total_work_day'] = $input['disbursed_date'];
          $pageHead['floor_name']     = $floorName;
          $result['pageHead']         = $pageHead;
          //
          $getSalaryList    = array();
          $result['group1'] = array();
          $result['group2'] = array();
          $result['group3'] = array();
          foreach ($getEmployees as $employee) {
              if((intval($input['unit']) == $employee->as_unit_id) && (intval($input['unit']) == $employee->as_location)){

                  $group1 = $this->groupWiseSalarySheet($employee->associate_id, $input);
                  if($group1 != null){
                      $result['group1'][] = $group1;
                  }

              } elseif ((intval($input['unit']) == $employee->as_unit_id) && (intval($input['unit']) != $employee->as_location)){

                  $group2 = $this->groupWiseSalarySheet($employee->associate_id, $input);
                  if($group2 != null){
                      $result['group2'][] = $group2;
                  }

              } elseif ((intval($input['unit']) != $employee->as_unit_id) && (intval($input['unit']) == $employee->as_location)){
                  $group3 = $this->groupWiseSalarySheet($employee->associate_id, $input);
                  if($group3 != null){
                      $result['group3'][] = $group3;
                  }
              } else {
                  return "error";
              }
          }
          //return $result['group3'];
          return view('hr.common.group_wise_salary_sheet_list', $result);
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }

  public function hoursToseconds($inHour)
  {
      try {
          list($hours,$minutes,$seconds) = array_pad(explode(':',$inHour),3,'00');
          sscanf($inHour, "%d:%d:%d", $hours, $minutes, $seconds);
          return isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
      } catch(\Exception $e) {
          return $inHour;
      }
  }

  public function groupWiseSalarySheet($associate_id, $input)
  {
      try {
          $getSalary = HrMonthlySalary::getSalaryListFilterWise($associate_id, $input['month'], $input['year'], $input['min_sal'], $input['max_sal']);
          $holiday_ot_day = [];
          if($getSalary != null){
              $total_ot_minutes = 0;
              if($input['ot_range'] > 0){
                  $year   = $input['year'];
                  $month  = $input['month'];
                  $date   = ($year."-".$month."-"."01");
                  $startDay   = date('Y-m-d', strtotime($date));
                  $endDay     = date('Y-m-t', strtotime($date));
                  $totalDays  = (date('d', strtotime($endDay))-date('d', strtotime($startDay)));
                  $x  =   1;
                  $get_salary_ot_minute = 0;
                  $ot_hours = 0;
                  for($i=0; $i<=$totalDays; $i++) {
                      $date       = ($year."-".$month."-".$x++);
                      // check holiday ot (if holiday+ot status found) than skip ot hour calculation
                      $holiday_ot = YearlyHolyDay::where(['hr_yhp_dates_of_holidays' => $date, 'hr_yhp_unit' => $input['unit'], 'hr_yhp_open_status' => 2])->first();
                      if(empty($holiday_ot)) {
                          $startDay   = date('Y-m-d', strtotime($date));
                          $att        = Attendance2::track($associate_id, $input['unit'], $startDay, $startDay);
                          $ot_minutes    = $att->overtime_minutes;
                          if($ot_minutes > ($input['ot_range'] * 60)) {
                              $ot_minutes = ($input['ot_range'] * 60);
                          }
                          $total_ot_minutes += $ot_minutes;
                      }
                  }
                  // convert minute from hours
                  if($getSalary->ot_hour) {
                      $get_salary_ot_minute = ($this->hoursToseconds($getSalary->ot_hour)/60);
                  }
                 // change value if geter than salary ot_hour
                  if($get_salary_ot_minute > $total_ot_minutes){
                      $ot_hours = number_format((float)($total_ot_minutes/60), 2, '.', ''); // minute to float hours
                      $ot_hours = sprintf('%02d:%02d', (int) $ot_hours, fmod($ot_hours, 1) * 60); // convert float hours to hour:minute
                      $getSalary->ot_hour = $ot_hours;
                  }
              }
          }
          return $getSalary;
      } catch(\Exception $e) {
          return $e->getMessage();
      }
  }

  }
