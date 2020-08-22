<?php

namespace App\Http\Controllers\Hr\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Hr\Area;
use App\Models\Hr\Department;
use App\Models\Hr\Floor;
use App\Models\Hr\Section;
use App\Models\Hr\Subsection;
use App\Models\Hr\AttendanceBonus;
use App\Models\Hr\HrMonthlySalary;
use App\Jobs\SalaryDayWiseSaveProcess;

use Carbon\Carbon;
use DB, PDF;
use Attendance;

class SalarySheetController extends Controller
{
    public function showForm(Request $request)
    {
        try {
            $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
            $areaList  = Area::where('hr_area_status', '1')->pluck('hr_area_name', 'hr_area_id');
            #--------------------------------------------------
            $dataList = $this->getSalaryData($request, true);
            if ($request->get('pdf') == true)
            {
                $pdf = PDF::loadView('hr/reports/salary_sheet_pdf', ['info'=>$info]);
                return $pdf->download('Salary_Sheet_Report_'.date('d_F_Y').'.pdf');
            }

            $floorList= DB::table('hr_floor')
                            ->where('hr_floor_unit_id', $request->unit)
                            ->pluck('hr_floor_name', 'hr_floor_id');

            $deptList= DB::table('hr_department')
                            ->where('hr_department_area_id', $request->area)
                            ->pluck('hr_department_name', 'hr_department_id');

            $sectionList= DB::table('hr_section')
                            ->where('hr_section_department_id', $request->department)
                            ->pluck('hr_section_name', 'hr_section_id');


            $subSectionList= DB::table('hr_subsection')
                            ->where('hr_subsec_section_id', $request->section)
                            ->pluck('hr_subsec_name', 'hr_subsec_id');

            return view("hr/reports/salary_sheet", compact(
                "dataList",
                "unitList",
                "areaList",
                "floorList",
                "deptList",
                "sectionList",
                "subSectionList",
                "unit_id"
            ));

        } catch (\Exception $e) {
            $bug1 = $e->getMessage();
            return redirect()->back()->with('error', $bug1);
        }
    }

    public function salarySheetUnitWise()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        return view("hr/reports/salary_sheet_unit_wise", compact(
            "unitList"
        ));
    }

    public function salarySheetUnitWiseday()
    {
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        return view("hr/reports/salary_sheet_unit_wise_days_15", compact(
            "unitList"
        ));
    }

    // save salary report
    public function saveSalarySheetUnit(Request $request)
    {
        $data['unit']       = $request->unit;
        $data['floor']      = null;
        $data['department'] = null;
        $data['section']    = null;
        $data['subSection'] = null;
        $data['start_date'] = $request->start_date;
        $data['end_date']   = $request->end_date;
        $data['disbursed_date'] = $request->disbursed_date;
        // $objectData     = (object) $data;
        $data['employee_list_main']  = $this->employeeInfo($request->unit, $request->floor, $request->department, $request->section, $request->subSection, $request->start_date);
        $data['array_count']    = 0;
        $data2 = [];
        if(!empty($data['employee_list_main'])) {
            if(count($data['employee_list_main']) > 100) {
                $data2['employee_list']  = array_chunk($data['employee_list_main'],5);
                $data2['employee_count'] = count($data['employee_list_main']);
                $data2['array_count']    = count($data2['employee_list']);
            } else {
                $data2['employee_list']  = $data['employee_list_main'];
                $data2['employee_count'] = count($data['employee_list_main']);
            }
        }
        return $data2;
    }

    // get salary data
    public function saveSalarySheetUnitData(Request $request)
    {
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',',');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',',');
        $data       = [];
        $user_id    = auth()->user()->id;
        $work_days  = $this->workDays(
            $request->start_date,
            $request->end_date,
            $request->unit
        );
        try {
            if(!empty($request->employeelist)) {
                $msg = '';
                foreach($request->employeelist as $k=>$employee) {
                    try {
                        /*
                        *--------------------------------------------------------------
                        * ATTENDANCE
                        *--------------------------------------------------------------
                        */
                        $startDate          = date("Y-m-d", strtotime($request->start_date));
                        $endDate            = date("Y-m-d", strtotime($request->end_date));
                        $track              = Attendance::track($employee['associate'], $employee['as_id'], $employee['unit'], $startDate, $endDate);
                        $salary_add_deduct  = Attendance::salaryAddDeduct($employee['associate'], $startDate);
                        #---------------------------------------------------------------
                        $totalDays = 30;
                        $attends   = $track->attends;
                        $leaves    = $track->leaves;
                        $absents   = $track->absents;
                        $lates     = $track->lates;
                        $holidays  = $track->holidays;
                        if($employee['as_ot'] == 1){
                            $overtimes      = $track->overtime_minutes;
                            $overtime_time  = $track->overtime_time;
                        } else {
                            $overtimes      = 0;
                            $overtime_time  = null;
                        }
                        /*
                        *--------------------------------------------------------------
                        * Attendance Bonus
                        *--------------------------------------------------------------
                        */
                        $bonusamount = DB::table('hr_attendance_bonus as a')->Select('a.*')
                                        ->where('a.unit',$request->unit)
                                        ->where('a.status',1)
                                        ->first();
                        $present_bonous = 0;
                        $date1 = strtotime(date("Y-m", strtotime($employee['doj'])));
                        $date2 = strtotime(date("Y-m", strtotime($startDate)));
                        if ($lates <= 3 && $leaves <= 1 && $employee['type'] == 3 && ($date1 <= $date2)) {
                            if ($date1 == $date2) {
                                if(isset($bonusamount->first_month)) {
                                    $present_bonous = $bonusamount->first_month;
                                }
                            } else {
                                if(isset($bonusamount->first_month)) {
                                    $present_bonous = $bonusamount->first_month;
                                }
                            }
                        }
                        /*
                        *--------------------------------------------------------------
                        * EXPENSE & PAYMENT
                        *--------------------------------------------------------------
                        */
                        $basic              = $employee['basic']?$employee['basic']:"0.00";
                        $salary_absent      = $basic?number_format(($basic/$totalDays)*$absents, 2, ".", ""):"0.00";
                        $salary_half_day    = "0.00";
                        $salary_advance     = $salary_add_deduct["advp_deduct"];
                        $salary_product     = $salary_add_deduct["cg_deduct"];
                        $salary_food        = $salary_add_deduct["food_deduct"];
                        $salary_others      = $salary_add_deduct["others_deduct"];
                        $salary_stamp       = "10.00";
                        /* TOTAL & NET PAY*/
                        $gross_salary   = number_format(($employee['salary']?$employee['salary']:0), 2, ".", "");
                        $salary_net     = number_format(($gross_salary-($salary_absent+$salary_half_day+$salary_product+$salary_advance+$salary_others+$salary_food)), 2, ".", "");
                        if($employee['as_ot'] == 1){
                            $overtime_rate   = number_format((($basic/208)*2), 2, ".", "");
                        } else {
                            $overtime_rate   = 0;
                        }
                        $overtime_salary        = number_format($overtime_rate*($overtimes/60), 2, ".", "");
                        $salary_advance_adjust  = $salary_add_deduct["salary_add"];
                        $total_pay              = number_format((($salary_net+$overtime_salary+$present_bonous+$salary_advance_adjust)-($salary_stamp)), 2, ".", "");

                        $data['as_id']                  = $employee['associate'];
                        $data['month']                  = date("n", strtotime($request->start_date)); // without 0 EX: 03 -> 3
                        $data['year']                   = date("Y", strtotime($request->end_date));
                        $data['gross']                  = $this->nullCheck($gross_salary);
                        $data['basic']                  = $this->nullCheck($employee['basic']);
                        $data['house']                  = $this->nullCheck($employee['house']);
                        $data['medical']                = $this->nullCheck($employee['medical']);
                        $data['transport']              = $this->nullCheck($employee['transport']);
                        $data['food']                   = $this->nullCheck($employee['food']);
                        $data['late_count']             = $this->nullCheck($lates);
                        $data['present']                = $this->nullCheck($attends);
                        $data['holiday']                = $this->nullCheck($holidays);
                        $data['absent']                 = $this->nullCheck($absents);
                        $data['leave']                  = $this->nullCheck($leaves);
                        $data['ot_rate']                = $this->nullCheck($overtime_rate);
                        $data['ot_hour']                = $this->nullCheck($overtime_time);
                        $data['absent_deduct']          = $this->nullCheck($salary_absent);
                        $data['half_day_deduct']        = $this->nullCheck($salary_half_day);
                        $data['salary_add_deduct_id']   = $salary_add_deduct["add_deduct_id"];
                        $data['salary_payable']         = $this->nullCheck($salary_net);
                        $data['attendance_bonus']       = $this->nullCheck($present_bonous);
                        $exist  = HrMonthlySalary::where(['as_id' => $data['as_id'], 'month' => $data['month'], 'year' => $data['year']])->first();
                        if(isset($exist->id)) {
                            // update data if exist
                            $data['updated_by'] = $user_id;
                            $data['updated_at'] = date('Y-m-d');
                            HrMonthlySalary::where('id', $exist->id)->update($data);
                        } else {
                            // insert data if not exist
                            $data['created_by'] = $user_id;
                            $data['created_at'] = date('Y-m-d');
                            HrMonthlySalary::insert($data);
                        }
                    } catch(\Exception $e) {
                        $msg = $e->getMessage();
                    }
                }
                return json_encode(['msg' => $msg, 'count' => count($request->employeelist)]);
            }
        } catch(\Exception $e) {
            return json_encode(['msg' => $e->getMessage()]);
        }
    }

    // save salary report
    public function saveSalarySheet(Request $request)
    {
        //dd($request->all());exit;

          //$paginate = isset($request->save)?false:true;
          if(isset($request->save)){

              $queue = (new SalaryDayWiseSaveProcess($request->all()))
                      //->onQueue('salarygenerate')
                      ->delay(Carbon::now()->addSeconds(2));
                      dispatch($queue);
          }
          $paginate = false;
          $resultList = $this->getSalaryDataDayWise($request,$paginate);

        //return $resultList;
        $unitList  = Unit::where('hr_unit_status', '1')
            ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
            ->pluck('hr_unit_name', 'hr_unit_id');
        return view("hr/reports/salary_sheet_unit_wise_days_15", compact(
            "unitList",
            "resultList"
        ));
        //return $resultList;
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
     public function printPage(Request $request){
         $month = date("m", strtotime($request->day_from));
         $year = date("Y", strtotime($request->day_to));
         $results = DB::table('hr_as_basic_info')
                           ->where('as_unit_id',$request->unit)
                           ->where('as_status',1)
                           ->where('partial_salary.month',$month)
                           ->where('partial_salary.year',$year)
                           ->where('partial_salary.date_range',$request->day_from.'/'.$request->day_to)
                           ->where('partial_salary.ot_range',$request->ot_from.'/'.$request->ot_to)
                           ->leftJoin('partial_salary','hr_as_basic_info.associate_id','partial_salary.as_id')
                           ->leftJoin('hr_employee_bengali','hr_as_basic_info.associate_id','hr_employee_bengali.hr_bn_associate_id')
                           ->leftJoin('hr_designation','hr_as_basic_info.as_designation_id','hr_designation.hr_designation_id')
                           ->get();
                           //dd(date("m", strtotime($request->day_from)));exit;
          $resultList = [];
          $work_days  = $this->workDays(
              $request->day_from,
              $request->day_to,
              $request->unit
          );
        foreach ($results as $key => $result) {
          $resultList['local'][$key]['as_id']          = $result->as_id;
          $resultList['local'][$key]['month']          = date("m", strtotime($request->day_from)); // without 0 EX: 03 -> 3
          $resultList['local'][$key]['year']           = date("Y", strtotime($request->day_to));
          $resultList['local'][$key]['designation']  = $result->hr_designation_name_bn;
          $resultList['local'][$key]['name']         = $result->hr_bn_associate_name;
          $resultList['local'][$key]['designation_grade']  = $result->hr_designation_grade;
          $resultList['local'][$key]['doj']  = $result->as_doj;
          $resultList['local'][$key]['gross']          = $this->nullCheck($result->gross);
          $resultList['local'][$key]['basic']          = $this->nullCheck($result->basic);
          $resultList['local'][$key]['house']          = $this->nullCheck($result->house);
          $resultList['local'][$key]['medical']        = $this->nullCheck($result->medical);
          $resultList['local'][$key]['transport']      = $this->nullCheck($result->transport);
          $resultList['local'][$key]['food']           = $this->nullCheck($result->food);
          $resultList['local'][$key]['late_count']     = $this->nullCheck($result->late_count);
          $resultList['local'][$key]['present']        = $this->nullCheck($result->present);
          $resultList['local'][$key]['holiday']        = $this->nullCheck($result->holiday);
          $resultList['local'][$key]['absent']         = $this->nullCheck($result->absent);
          $resultList['local'][$key]['leave']          = $this->nullCheck($result->leave);
          $resultList['local'][$key]['ot_rate']        = $this->nullCheck($result->ot_rate);
          $resultList['local'][$key]['ot_hour']        = $this->nullCheck($result->ot_hour);
          $resultList['local'][$key]['absent_deduct']  = $this->nullCheck($result->absent_deduct);
          $resultList['local'][$key]['half_day_deduct']        = $this->nullCheck($result->half_day_deduct);
          $resultList['local'][$key]['salary_add_deduct_id']   = $result->salary_add_deduct_id;
          $resultList['local'][$key]['salary_payable']         = $this->nullCheck($result->salary_payable);
          $resultList['local'][$key]['attendance_bonus']       = (string)number_format($this->nullCheck($result->attendance_bonus),2, '.', ',');
          $resultList['local'][$key]['overtime_salary']  = (string)number_format(($result->ot_rate*$result->ot_hour),2, '.', ',');
          $resultList['local'][$key]['salary_advance_adjust'] = (string)number_format(0,2, '.', ',');
          $resultList['local'][$key]['salary_product']   = (string)number_format(0,2, '.', ',');
          $resultList['local'][$key]['salary_others']    = (string)number_format(0,2, '.', ',');
        }
        $resultList['global']['dateDate'] = date("d-F-Y");
        $resultList['global']['dateTime'] = date("H:i");
        $resultList['global']['start_date']     = date("d-F-Y", strtotime($request->day_from));
        $resultList['global']['end_date']       = date("d-F-Y", strtotime($request->day_to));
        $resultList['global']['work_days']      = $work_days;
        $resultList['global']['disbursed_date'] = date("d-F-y", strtotime($request->disbursed_date));
        $resultList['global']['unit']           = Unit::where("hr_unit_id", $request->unit)->value("hr_unit_name_bn");
        // $data['global']['department']     = Department::where("hr_department_id", $request->department)->value("hr_department_name_bn");
        // $data['global']['floor']          = Floor::where("hr_floor_id", $request->floor)->value("hr_floor_name_bn");
        // $data['global']['sec_name']       = Section::where("hr_section_id", $request->section)->value("hr_section_name_bn");
        // $data['global']['sub_sec_name']   = Subsection::where("hr_subsec_id", $request->subSection)->value("hr_subsec_name_bn");

       $unitList  = Unit::where('hr_unit_status', '1')
           ->whereIn('hr_unit_id', auth()->user()->unit_permissions())
           ->pluck('hr_unit_name', 'hr_unit_id');
       return view("hr/reports/salary_sheet_unit_wise_days_15_print", compact(
           "unitList",
           "resultList"
       ));


     }
    public function getSalaryDataDayWise($request, $paginate){
      // if($request->unit){
      //   dd("request");
      // }else{
      //   dd("Hi");
      // }
      // exit;
    //  dd($request->all());exit;
         // $cdate = date('Y-m');
         //  if($request->day > 0){
         //    $start_date = $cdate.'-1';
         //    $end_date = $cdate.'-'.($request->day+1);
         //  }else{
         //    $start_date = $cdate.'-'.$request->day;
         //    $end_date = $cdate.'-'.$request->day;
         //  }
          $start_date = $request->day_from;
          $end_date = $request->day_to;
          $ot_start = $request->ot_from;
          $ot_end = $request->ot_to;

          // $start_date = '2019-04-01';
          // $end_date = '2019-04-15';
          // $ot_start = '2019-04-01';
          // $ot_end = '2019-04-10';
         //dd($end_date);exit;
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',',');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',',');
        $data       = [];
        $user_id    = auth()->user()->id;
        $work_days  = $this->workDays(
            $start_date,
            $end_date,
            $request->unit
        );

            $employee_list   = $this->employeeInfo($request->unit, $request->floor, $request->department, $request->section, $request->subSection, $start_date,$paginate);
            // return $employee_list;

          // dd($employee_list);exit;
        foreach($employee_list as $key=>$employee) {
            /*
            *--------------------------------------------------------------
            * ATTENDANCE
            *--------------------------------------------------------------
            */
            $startDate          = date("Y-m-d", strtotime($start_date));
            $endDate            = date("Y-m-d", strtotime($end_date));
            //$track              = Attendance::track($employee->associate, $employee->as_id, $employee->unit, $startDate, $endDate,$ot_start,$ot_end);
           //dd($employee);exit;
           ///////////////////////////////////////////////////////////////////
           // $expdate = explode('-',$startDate);
           // $yearMonth = $expdate[0].'-'.$expdate[1]
           $tableName = $this->getTableName($employee->unit);
           $totalDays = ((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24))+1;
           $getPresentOT = DB::table($tableName)
               ->select([
                   \DB::raw('count(as_id) as present'),
                   \DB::raw('SUM(ot_hour) as ot')

               ])
               ->where('as_id', $employee->as_id)
               ->whereDate('in_time','>=',$startDate)
               ->whereDate('in_time','<=',$endDate)
               ->first();

               $getOT = DB::table($tableName)
                   ->select([
                       \DB::raw('count(as_id) as present'),
                       \DB::raw('SUM(ot_hour) as ot')

                   ])
                   ->where('as_id', $employee->as_id)
                   ->whereDate('in_time','>=',$ot_start)
                   ->whereDate('in_time','<=',$ot_end)
                   ->first();

           if(!isset($getPresentOT->present)){
               $getPresentOT->present = 0;
           }

           if(!isset($getOT->ot)){
               $getOT->ot = 0;
           }

           $lateCount = DB::table($tableName)
               ->where('as_id', $employee->as_id)
               ->whereDate('in_time','>=',$startDate)
               ->whereDate('in_time','<=',$endDate)
               ->where('late_status', 1)
               ->count();

           $halfCount = DB::table($tableName)
               ->where('as_id', $employee->as_id)
               ->whereDate('in_time','>=',$startDate)
               ->whereDate('in_time','<=',$endDate)
               ->where('remarks', 'HD')
               ->count();

           // get holiday employee wise
           $holidayCount = DB::table("hr_yearly_holiday_planner")
                   ->where('hr_yhp_unit', $employee->unit)
                   // ->whereYear('hr_yhp_dates_of_holidays', $year)
                   // ->whereMonth('hr_yhp_dates_of_holidays', $month)
                   ->where('hr_yhp_dates_of_holidays','>=',$startDate)
                   ->where('hr_yhp_dates_of_holidays','<=',$endDate)
                   ->where('hr_yhp_open_status', 0)
                   ->count();
           // get absent employee wise
           $getAbsent = DB::table('hr_absent')
           ->where('associate_id', $employee->associate)
           ->where('date','>=',$startDate)
           ->where('date','<=',$endDate)
           ->count();

           // get leave employee wise
           $getLeave = DB::table('hr_leave')
           ->where('leave_ass_id', $employee->associate)
           ->where('leave_from', '<=',$startDate)
           ->where('leave_to', '>=',$endDate)
           ->where('leave_status', 1)
           ->get();
           $leaveCount = 0;
           if(count($getLeave) > 0){
               foreach ($getLeave as $leave) {
                   $day = 1;
                   $date = Carbon::parse($leave->leave_from);
                   $now = Carbon::parse($leave->leave_to);
                   $diff = $date->diffInDays($now);
                   $leaveCount += $diff + $day;
               }
           }

           $getHoliday = $totalDays - ($getPresentOT->present + $getAbsent + $leaveCount);
           if($getHoliday > $holidayCount){
               $getHoliday = $holidayCount;
           }
           /////////////////////////////////////////////////////////////////////

            $salary_add_deduct  = Attendance::salaryAddDeduct($employee->associate, $startDate);
            #---------------------------------------------------------------
            //dd($track);exit;
            $totalDays = $totalDays;
            $attends   = $getPresentOT->present;
            $leaves    = $leaveCount;
            $absents   = (($getPresentOT->present == 0) && ($getAbsent == 0))?$totalDays-($leaveCount+$holidayCount):$getAbsent;
            $lates     = $lateCount;
            $holidays  = $holidayCount;

            // dd($absents);exit;
            if($employee->as_ot == 1){
                $overtimes      = $getOT->ot;
                $overtime_time  = $getOT->ot;
            } else {
                $overtimes      = 0;
                $overtime_time  = null;
            }
            /*
            *--------------------------------------------------------------
            * Attendance Bonus
            *--------------------------------------------------------------
            */
            $bonusamount = DB::table('hr_attendance_bonus as a')->Select('a.*')
                            ->where('a.unit',$request->unit)
                            ->where('a.status',1)
                            ->first();
            $present_bonous = 0;
            $date1 = strtotime(date("Y-m", strtotime($employee->doj)));
            $date2 = strtotime(date("Y-m", strtotime($startDate)));
            $lateAllow = 0;
            $leaveAllow = 0;
            $absentAllow = 0;
            //get unit wise bonus rules
            $getBonusRule = DB::table('hr_attendance_bonus_dynamic')
            ->where('unit_id', $request->unit)
            ->first();

            //dd($getBonusRule);exit;
            $getAbsent = DB::table('hr_absent')
            ->where('associate_id', $employee->associate)
            ->whereMonth('date', '=', $startDate)
            ->whereYear('date', '=', $endDate)
            ->count();
            if($getBonusRule != null){
                $lateAllow = $getBonusRule->late_count;
                $leaveAllow = $getBonusRule->leave_count;
                $absentAllow = $getBonusRule->absent_count;
            }else{
                $lateAllow = 3;
                $leaveAllow = 1;
                $absentAllow = 1;
            }
            // $lastMonth = Carbon::now();
            // $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('n');
            // dd($lastMonth);exit;
            $expd = explode('-',$startDate);
            //dd((int)$expd[2]);exit;
            if (((int)$lates <= (int)$lateAllow) && ((int)$leaves <= (int)$leaveAllow) && ((int)$absents <= (int)$absentAllow) && ($employee->type == 3 ) && ((int)$expd[2] >= 16 )) {
                // if ($date1 == $date2) {
                //     if(isset($bonusamount->first_month)) {
                //         $present_bonous = ($bonusamount->first_month/30)*$totalDays;
                //     }
                // } else {
                //     if(isset($bonusamount->first_month)) {
                //         $present_bonous = ($bonusamount->first_month/30)*$totalDays;
                //     }
                // }
                $lastMonth = Carbon::now();
                $lastMonth = $lastMonth->startOfMonth()->subMonth()->format('n');
                //dd($lastMonth);exit;
                if($lastMonth == '12'){
                    $year = $year - 1;
                }
                $getLastMonthSalary = HrMonthlySalary::
                    where('as_id', $employee->associate)
                    ->where('month', $lastMonth)
                    ->where('year', $year)
                    ->first();
                if (($getLastMonthSalary != null) && ($getLastMonthSalary->attendance_bonus > 0)) {
                    if(isset($bonusamount->from_2nd)) {
                        $present_bonous = $bonusamount->from_2nd;
                    }
                } else {
                    if(isset($bonusamount->first_month)) {
                        $present_bonous = $bonusamount->first_month;
                    }
                }
            }
            /*
            *--------------------------------------------------------------
            * EXPENSE & PAYMENT
            *--------------------------------------------------------------
            */
            $basic              = $employee->basic?$employee->basic:"0.00";
            $salary_absent      = $basic?number_format(($basic/30)*$absents, 2, ".", ""):"0.00";
            $salary_half_day    = "0.00";
            $salary_advance     = ($salary_add_deduct["advp_deduct"]/30)*$totalDays;
            $salary_product     = ($salary_add_deduct["cg_deduct"]/30)*$totalDays;
            $salary_food        = ($salary_add_deduct["food_deduct"]/30)*$totalDays;
            $salary_others      = ($salary_add_deduct["others_deduct"]/30)*$totalDays;
            $salary_stamp       = "10.00";

            /* TOTAL & NET PAY*/
            $gross_salary   = number_format(($employee->salary?$employee->salary:0), 2, ".", "");
            $getHalfDeduct = $halfCount * (($gross_salary/30) / 2);
            $salary_net     = number_format((($gross_salary/30)*($totalDays)-($salary_absent+$getHalfDeduct+$salary_half_day+$salary_product+$salary_advance+$salary_others+$salary_food)), 2, ".", "");
            if($employee->as_ot == 1){
                $overtime_rate   = number_format((($basic/208)*2), 2, ".", "");
            } else {
                $overtime_rate   = 0;
            }
             $e = explode('.',$overtimes);
             $eh = $e[0];
             $em = (isset($e[1]) && !empty($e[1]))?'30':'00';
             $overtimes = $eh+$em;

             //dd($overtimes);exit;
            $overtime_salary        = number_format($overtime_rate*($overtimes/60), 2, ".", "");
            $salary_advance_adjust  = ($salary_add_deduct["salary_add"]/30)*$totalDays;
            $total_pay              = number_format((($salary_net)-($salary_stamp)), 2, ".", "");

            if($paginate) {
              $data['local'][$key]['no']           = ($employee_list->perPage() * ($employee_list->currentPage()-1)) + ($key + 1);
              $data['local'][$key]['as_id']          = $employee->associate;
              $data['local'][$key]['month']          = date("m", strtotime($request->start_date)); // without 0 EX: 03 -> 3
              $data['local'][$key]['year']           = date("Y", strtotime($request->end_date));
              $data['local'][$key]['designation']  = $employee->designation;
              $data['local'][$key]['name']         = $employee->name;
              $data['local'][$key]['designation_grade']  = $employee->grade;
              $data['local'][$key]['doj']  = $employee->doj;
              $data['local'][$key]['gross']          = $this->nullCheck($gross_salary);
              $data['local'][$key]['basic']          = $this->nullCheck($employee->basic);
              $data['local'][$key]['house']          = $this->nullCheck($employee->house);
              $data['local'][$key]['medical']        = $this->nullCheck($employee->medical);
              $data['local'][$key]['transport']      = $this->nullCheck($employee->transport);
              $data['local'][$key]['food']           = $this->nullCheck($employee->food);
              $data['local'][$key]['late_count']     = $this->nullCheck($lates);
              $data['local'][$key]['present']        = $this->nullCheck($attends);
              $data['local'][$key]['holiday']        = $this->nullCheck($holidays);
              $data['local'][$key]['absent']         = $this->nullCheck($absents);
              $data['local'][$key]['leave']          = $this->nullCheck($leaves);
              $data['local'][$key]['ot_rate']        = $this->nullCheck($overtime_rate);
              $data['local'][$key]['ot_hour']        = $this->nullCheck($overtime_time);
              $data['local'][$key]['absent_deduct']  = $this->nullCheck($salary_absent);
              $data['local'][$key]['half_day_deduct']        = $this->nullCheck($salary_half_day);
              $data['local'][$key]['salary_add_deduct_id']   = $salary_add_deduct["add_deduct_id"];
              $data['local'][$key]['salary_payable']         = $this->nullCheck($total_pay);
              $data['local'][$key]['attendance_bonus']       = (string)number_format($this->nullCheck($present_bonous),2, '.', ',');
              $data['local'][$key]['overtime_salary']  = (string)number_format($overtime_salary,2, '.', ',');
              $data['local'][$key]['salary_advance_adjust'] = (string)number_format($salary_advance_adjust,2, '.', ',');
              $data['local'][$key]['salary_product']   = (string)number_format($salary_product,2, '.', ',');
              $data['local'][$key]['salary_others']    = (string)number_format($salary_others,2, '.', ',');



            }else{
              // $data['local'][$key]['no']           = ($employee_list->perPage() * ($employee_list->currentPage()-1)) + ($key + 1);
              $data['local'][$key]['as_id']          = $employee->associate;
              $data['local'][$key]['month']          = date("m", strtotime($request->start_date)); // without 0 EX: 03 -> 3
              $data['local'][$key]['year']           = date("Y", strtotime($request->end_date));
              $data['local'][$key]['designation']  = $employee->designation;
              $data['local'][$key]['name']         = $employee->name;
              $data['local'][$key]['designation_grade']  = $employee->grade;
              $data['local'][$key]['doj']  = $employee->doj;
              $data['local'][$key]['gross']          = $this->nullCheck($gross_salary);
              $data['local'][$key]['basic']          = $this->nullCheck($employee->basic);
              $data['local'][$key]['house']          = $this->nullCheck($employee->house);
              $data['local'][$key]['medical']        = $this->nullCheck($employee->medical);
              $data['local'][$key]['transport']      = $this->nullCheck($employee->transport);
              $data['local'][$key]['food']           = $this->nullCheck($employee->food);
              $data['local'][$key]['late_count']     = $this->nullCheck($lates);
              $data['local'][$key]['present']        = $this->nullCheck($attends);
              $data['local'][$key]['holiday']        = $this->nullCheck($holidays);
              $data['local'][$key]['absent']         = $this->nullCheck($absents);
              $data['local'][$key]['leave']          = $this->nullCheck($leaves);
              $data['local'][$key]['ot_rate']        = $this->nullCheck($overtime_rate);
              $data['local'][$key]['ot_hour']        = $this->nullCheck($overtime_time);
              $data['local'][$key]['absent_deduct']  = $this->nullCheck($salary_absent);
              $data['local'][$key]['half_day_deduct']        = $this->nullCheck($salary_half_day);
              $data['local'][$key]['salary_add_deduct_id']   = $salary_add_deduct["add_deduct_id"];
              $data['local'][$key]['salary_payable']         = $this->nullCheck($total_pay);
              $data['local'][$key]['attendance_bonus']       = (string)number_format($this->nullCheck($present_bonous),2, '.', ',');
              $data['local'][$key]['overtime_salary']  = (string)number_format($overtime_salary,2, '.', ',');
              $data['local'][$key]['salary_advance_adjust'] = (string)number_format($salary_advance_adjust,2, '.', ',');
              $data['local'][$key]['salary_product']   = (string)number_format($salary_product,2, '.', ',');
              $data['local'][$key]['salary_others']    = (string)number_format($salary_others,2, '.', ',');


            }

                // $exist  = HrMonthlySalary::where(['as_id' => $data['as_id'], 'month' => $data['month'], 'year' => $data['year']])->first();
                // if(isset($exist->id)) {
                //     // update data if exist
                //     $data['updated_by'] = $user_id;
                //     $data['updated_at'] = date('Y-m-d');
                //     HrMonthlySalary::where('id', $exist->id)->update($data);
                // } else {
                //     // insert data if not exist
                //     $data['created_by'] = $user_id;
                //     $data['created_at'] = date('Y-m-d');
                //     HrMonthlySalary::insert($data);
                // }

        }
         if($paginate){
          $data['global']['links'] = !empty($employee_list->links())?$employee_list->appends(request()->query())->links():null;
          $data['global']['dateDate'] = date("d-F-Y");
          $data['global']['dateTime'] = date("H:i");
          $data['global']['start_date']     = date("d-F-Y", strtotime($start_date));
          $data['global']['end_date']       = date("d-F-Y", strtotime($end_date));
          $data['global']['work_days']      = $work_days;
          $data['global']['disbursed_date'] = date("d-F-y", strtotime($request->disbursed_date));
          $data['global']['unit']           = Unit::where("hr_unit_id", $request->unit)->value("hr_unit_name_bn");
          $data['global']['department']     = Department::where("hr_department_id", $request->department)->value("hr_department_name_bn");
          $data['global']['floor']          = Floor::where("hr_floor_id", $request->floor)->value("hr_floor_name_bn");
          $data['global']['sec_name']       = Section::where("hr_section_id", $request->section)->value("hr_section_name_bn");
          $data['global']['sub_sec_name']   = Subsection::where("hr_subsec_id", $request->subSection)->value("hr_subsec_name_bn");
       }else{
         $data['global']['dateDate'] = date("d-F-Y");
         $data['global']['dateTime'] = date("H:i");
         $data['global']['start_date']     = date("d-F-Y", strtotime($start_date));
         $data['global']['end_date']       = date("d-F-Y", strtotime($end_date));
         $data['global']['work_days']      = $work_days;
         $data['global']['disbursed_date'] = date("d-F-y", strtotime($request->disbursed_date));
         $data['global']['unit']           = Unit::where("hr_unit_id", $request->unit)->value("hr_unit_name_bn");
         $data['global']['department']     = Department::where("hr_department_id", $request->department)->value("hr_department_name_bn");
         $data['global']['floor']          = Floor::where("hr_floor_id", $request->floor)->value("hr_floor_name_bn");
         $data['global']['sec_name']       = Section::where("hr_section_id", $request->section)->value("hr_section_name_bn");
         $data['global']['sub_sec_name']   = Subsection::where("hr_subsec_id", $request->subSection)->value("hr_subsec_name_bn");

       }
        //dd($data);exit;
        return $data;

    }
    // get salary data
    public function getSalaryData($request, $paginate)
    {

      //dd($request->all());exit;
         $cdate = date('Y-m');
          if($request->day > 0){
            $start_date = $cdate.'-1';
            $end_date = $cdate.'-'.($request->day+1);
          }else{
            $start_date = $cdate.'-'.$request->day;
            $end_date = $cdate.'-'.$request->day;
          }
          // $start_date = '2019-04-01';
          // $end_date = '2019-04-15';
         //dd($end_date);exit;
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',',');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',',');
        $data       = [];
        $user_id    = auth()->user()->id;
        $work_days  = $this->workDays(
            $start_date,
            $end_date,
            $request->unit
        );
        if($paginate) {
            $employee_list   = $this->employeeInfo($request->unit, $request->floor, $request->department, $request->section, $request->subSection, $start_date, 'paginate');
        } else {
            $employee_list   = $this->employeeInfo($request->unit, $request->floor, $request->department, $request->section, $request->subSection, $start_date,'paginate');
        }

          // dd($employee_list);exit;
        foreach($employee_list as $key=>$employee) {
            /*
            *--------------------------------------------------------------
            * ATTENDANCE
            *--------------------------------------------------------------
            */
            $startDate          = date("Y-m-d", strtotime($start_date));
            $endDate            = date("Y-m-d", strtotime($end_date));
            $track              = Attendance::track($employee->associate, $employee->as_id, $employee->unit, $startDate, $endDate);
            $salary_add_deduct  = Attendance::salaryAddDeduct($employee->associate, $startDate);
            #---------------------------------------------------------------
            //dd($track);exit;
            $totalDays = $track->total_days;
            $attends   = $track->attends;
            $leaves    = $track->leaves;
            $absents   = $track->absents;
            $lates     = $track->lates;
            $holidays  = $track->holidays;
            if($employee->as_ot == 1){
                $overtimes      = $track->overtime_minutes;
                $overtime_time  = $track->overtime_time;
            } else {
                $overtimes      = 0;
                $overtime_time  = null;
            }
            /*
            *--------------------------------------------------------------
            * Attendance Bonus
            *--------------------------------------------------------------
            */
            $bonusamount = DB::table('hr_attendance_bonus as a')->Select('a.*')
                            ->where('a.unit',$request->unit)
                            ->where('a.status',1)
                            ->first();
            $present_bonous = 0;
            $date1 = strtotime(date("Y-m", strtotime($employee->doj)));
            $date2 = strtotime(date("Y-m", strtotime($startDate)));
            if ($lates <= 3 && $leaves <= 1 && $employee->type == 3 && ($date1 <= $date2)) {
                if ($date1 == $date2) {
                    if(isset($bonusamount->first_month)) {
                        $present_bonous = $bonusamount->first_month;
                    }
                } else {
                    if(isset($bonusamount->first_month)) {
                        $present_bonous = $bonusamount->first_month;
                    }
                }
            }
            /*
            *--------------------------------------------------------------
            * EXPENSE & PAYMENT
            *--------------------------------------------------------------
            */
            $basic              = $employee->basic?$employee->basic:"0.00";
            $salary_absent      = $basic?number_format(($basic/30)*$absents, 2, ".", ""):"0.00";
            $salary_half_day    = "0.00";
            $salary_advance     = $salary_add_deduct["advp_deduct"];
            $salary_product     = $salary_add_deduct["cg_deduct"];
            $salary_food        = $salary_add_deduct["food_deduct"];
            $salary_others      = $salary_add_deduct["others_deduct"];
            $salary_stamp       = "10.00";
            /* TOTAL & NET PAY*/
            $gross_salary   = number_format(($employee->salary?$employee->salary:0), 2, ".", "");
            $salary_net     = number_format((($gross_salary/30)*($attends+$leaves+$holidays+$lates)-($salary_half_day+$salary_product+$salary_advance+$salary_others+$salary_food)), 2, ".", "");
            if($employee->as_ot == 1){
                $overtime_rate   = number_format((($basic/208)*2), 2, ".", "");
            } else {
                $overtime_rate   = 0;
            }
             $e = explode(':',$overtimes);
             $eh = $e[0]*60;
             $em = isset($e[1])?$e[1]*60:0;
             $overtimes = $eh+$em;

             //dd($overtimes);exit;
            $overtime_salary        = number_format($overtime_rate*($overtimes/60), 2, ".", "");
            $salary_advance_adjust  = $salary_add_deduct["salary_add"];
            $total_pay              = number_format((($salary_net+$overtime_salary+$present_bonous+$salary_advance_adjust)-($salary_stamp)), 2, ".", "");
            if($paginate) {
                // $data['local'][$key]['no']           = str_replace($en, $bn, ($employee_list->perPage() * ($employee_list->currentPage()-1)) + ($key + 1));
                $data['local'][$key]['name']         = $employee->name;
                $data['local'][$key]['doj']          = str_replace($en, $bn, date("d-m-Y", strtotime($employee->doj)));
                $data['local'][$key]['designation']  = $employee->designation;
                $data['local'][$key]['basic']        = str_replace($en, $bn,(string)number_format($employee->basic,2, '.', ','));
                $data['local'][$key]['house']        = str_replace($en, $bn,(string)number_format($employee->house,2, '.', ','));
                $data['local'][$key]['medical']      = str_replace($en, $bn,(string)number_format($employee->medical,2, '.', ','));
                $data['local'][$key]['transport']    = str_replace($en, $bn,(string)number_format($employee->transport,2, '.', ','));
                $data['local'][$key]['food']         = str_replace($en, $bn,(string)number_format($employee->food,2, '.', ','));
                $data['local'][$key]['as_id']    = $employee->associate;
                $data['local'][$key]['late_count']        = str_replace($en, $bn, $lates);
                $data['local'][$key]['designation_grade']        = str_replace($en, $bn, $employee->grade);
                $data['local'][$key]['gross'] = str_replace($en, $bn,(string)number_format($gross_salary,2, '.', ','));
                $data['local'][$key]['present']      = str_replace($en, $bn, $attends);
                $data['local'][$key]['holiday']     = str_replace($en, $bn, $holidays);
                $data['local'][$key]['absent']      = str_replace($en, $bn, $absents);
                $data['local'][$key]['leave']       = str_replace($en, $bn, $leaves);
                $data['local'][$key]['total_day']    = str_replace($en, $bn, ($attends+$holidays+$leaves));
                $data['local'][$key]['salary_absent']    = str_replace($en, $bn,(string)number_format($salary_absent,2, '.', ','));
                $data['local'][$key]['salary_half_day']  = str_replace($en, $bn,(string)number_format($salary_half_day,2, '.', ','));
                $data['local'][$key]['salary_advance']   = str_replace($en, $bn,(string)number_format($salary_advance,2, '.', ','));
                $data['local'][$key]['salary_stamp']     = str_replace($en, $bn,(string)number_format($salary_stamp,2, '.', ','));
                $data['local'][$key]['salary_product']   = str_replace($en, $bn,(string)number_format($salary_product,2, '.', ','));
                $data['local'][$key]['salary_food']      = str_replace($en, $bn,(string)number_format($salary_food,2, '.', ','));
                $data['local'][$key]['salary_others']    = str_replace($en, $bn,(string)number_format($salary_others,2, '.', ','));
                $data['local'][$key]['salary_net']       = str_replace($en, $bn,(string)number_format($salary_net,2, '.', ','));
                $data['local'][$key]['overtime_salary']  = str_replace($en, $bn,(string)number_format($overtime_salary,2, '.', ','));
                $data['local'][$key]['ot_rate']    = str_replace($en, $bn, $overtime_rate);
                $data['local'][$key]['ot_hour']    = str_replace($en, $bn, $this->nullCheck($overtime_time));
                $data['local'][$key]['present_bonous']   = str_replace($en, $bn,(string)number_format($present_bonous,2, '.', ','));
                $data['local'][$key]['total_pay']        = str_replace($en, $bn,(string)number_format($total_pay,2, '.', ','));

                $data['local'][$key]['salary_advance_adjust'] = str_replace($en, $bn,(string)number_format($salary_advance_adjust,2, '.', ','));
            } else {
                $data[$key]['as_id']          = $employee->associate;
                $data[$key]['month']          = date("n", strtotime($request->start_date)); // without 0 EX: 03 -> 3
                $data[$key]['year']           = date("Y", strtotime($request->end_date));
                $data[$key]['designation']  = $employee->designation;
                $data[$key]['designation_grade']  = $employee->grade;
                $data[$key]['doj']  = $employee->doj;
                $data[$key]['gross']          = $this->nullCheck($gross_salary);
                $data[$key]['basic']          = $this->nullCheck($employee->basic);
                $data[$key]['house']          = $this->nullCheck($employee->house);
                $data[$key]['medical']        = $this->nullCheck($employee->medical);
                $data[$key]['transport']      = $this->nullCheck($employee->transport);
                $data[$key]['food']           = $this->nullCheck($employee->food);
                $data[$key]['late_count']     = $this->nullCheck($lates);
                $data[$key]['present']        = $this->nullCheck($attends);
                $data[$key]['holiday']        = $this->nullCheck($holidays);
                $data[$key]['absent']         = $this->nullCheck($absents);
                $data[$key]['leave']          = $this->nullCheck($leaves);
                $data[$key]['ot_rate']        = $this->nullCheck($overtime_rate);
                $data[$key]['ot_hour']        = $this->nullCheck($overtime_time);
                $data[$key]['absent_deduct']  = $this->nullCheck($salary_absent);
                $data[$key]['half_day_deduct']        = $this->nullCheck($salary_half_day);
                $data[$key]['salary_add_deduct_id']   = $salary_add_deduct["add_deduct_id"];
                $data[$key]['salary_payable']         = $this->nullCheck($salary_net);
                $data[$key]['attendance_bonus']       = $this->nullCheck($present_bonous);


                // $exist  = HrMonthlySalary::where(['as_id' => $data['as_id'], 'month' => $data['month'], 'year' => $data['year']])->first();
                // if(isset($exist->id)) {
                //     // update data if exist
                //     $data['updated_by'] = $user_id;
                //     $data['updated_at'] = date('Y-m-d');
                //     HrMonthlySalary::where('id', $exist->id)->update($data);
                // } else {
                //     // insert data if not exist
                //     $data['created_by'] = $user_id;
                //     $data['created_at'] = date('Y-m-d');
                //     HrMonthlySalary::insert($data);
                // }
            }
        }
        if($paginate){
            // $data['global']['links'] = !empty($employee_list->links())?$employee_list->appends(request()->query())->links():null;
            $data['global']['dateDate'] = str_replace($en, $bn, date("d-m-Y"));
            $data['global']['dateTime'] = str_replace($en, $bn, date("H:i"));
            $data['global']['start_date']     = str_replace($en, $bn, date("d-F-Y", strtotime($request->start_date)));
            $data['global']['end_date']       = str_replace($en, $bn, date("d-F-Y", strtotime($request->end_date)));
            $data['global']['work_days']      = str_replace($en, $bn, $work_days);
            $data['global']['disbursed_date'] = str_replace($en, $bn, date("d-F-y", strtotime($request->disbursed_date)));
            $data['global']['unit']           = Unit::where("hr_unit_id", $request->unit)->value("hr_unit_name_bn");
            $data['global']['department']     = Department::where("hr_department_id", $request->department)->value("hr_department_name_bn");
            $data['global']['floor']          = Floor::where("hr_floor_id", $request->floor)->value("hr_floor_name_bn");
            $data['global']['sec_name']       = Section::where("hr_section_id", $request->section)->value("hr_section_name_bn");
            $data['global']['sub_sec_name']   = Subsection::where("hr_subsec_id", $request->subSection)->value("hr_subsec_name_bn");
        }
        // dd($data);exit;
        return $data;
    }

    //  null check function
    public function nullCheck($value) {
        if($value == NULL) {
            $value = 0;
        }
        return $value;
    }

    // get total working days
    public function workDays($startDate = null, $endDate = null, $unit = null)
    {
        $startDate = date("Y-m-d", strtotime($startDate));
        $endDate   = date("Y-m-d", strtotime($endDate));
        $totalDays = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        $work_days = 0;
        #----------------------------------------------
        # Check Holiday with unit & month_year
        $total_holidays = DB::table("hr_yearly_holiday_planner")
            ->where("hr_yhp_unit", $unit)
            ->whereBetween("hr_yhp_dates_of_holidays", [$startDate, $endDate])
            ->count();

        $work_days = (($totalDays+1)-$total_holidays);
        return $work_days;
    }

    //get employee info
    public function employeeInfo($unit = null, $floor=null, $department=null, $section=null, $subSection=null, $salaryMonth=null, $paginate=null)
    {
        //dd('opps');exit;
        if(auth()->user()->hasRole('power user 3')){
          $cantacces = ['power user 2','advance user 2'];
        }elseif (auth()->user()->hasRole('power user 2')) {
          $cantacces = ['power user 3','advance user 2'];
        }elseif (auth()->user()->hasRole('advance user 2')) {
          $cantacces = ['power user 3','power user 2'];
        }else{
          $cantacces = [];
        }
        $userIdNotAccessible = DB::table('roles')
                  ->whereIn('name',$cantacces)
                  ->leftJoin('model_has_roles','roles.id','model_has_roles.role_id')
                  ->pluck('model_has_roles.model_id');

            $asIds = DB::table('users')
                     ->whereIn('id',$userIdNotAccessible)
                     ->pluck('associate_id');

        $salaryMonth = date("Y-m", strtotime($salaryMonth));

        $result = DB::table("hr_as_basic_info AS b")
            ->select(
                "bd.hr_bn_associate_name AS name",
                "b.as_doj AS doj",
                'dg.hr_designation_name_bn AS designation',
                'dg.hr_designation_grade AS grade',
                "b.as_id",
                "b.as_ot",
                "b.temp_id",
                "b.associate_id AS associate",
                "b.as_emp_type_id AS type",
                "b.as_name",
                "b.as_unit_id AS unit",
                "ben.ben_current_salary AS salary",
                "ben.ben_basic AS basic",
                "ben.ben_house_rent AS house",
                "ben.ben_medical AS medical",
                "ben.ben_transport AS transport",
                "ben.ben_food AS food"
            )
            ->leftJoin("hr_employee_bengali AS bd", "bd.hr_bn_associate_id", "=", "b.associate_id")
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin('hr_benefits AS ben', function($join){
                $join->on('ben.ben_as_id', '=', 'b.associate_id');
                $join->where('ben.ben_status', '=', 1);
            })
            ->where(function($c) use($unit, $floor, $department, $section, $subSection){
                $c->where("b.as_unit_id", $unit);
                if (!empty($department)) {
                    $c->where("b.as_department_id", $department);
                }
                if (!empty($floor)) {
                    $c->where("b.as_floor_id", $floor);
                }
                if (!empty($section)) {
                    $c->where("b.as_section_id", $section);
                }
                if (!empty($subSection)) {
                    $c->where("b.as_subsection_id", $subSection);
                }
            })
            ->where(DB::raw("DATE_FORMAT(b.as_doj, '%Y-%m')"), "<=", $salaryMonth)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->whereNotIn('b.associate_id',$asIds)
            ->whereIn('b.as_status',[1,5]); // checking status
            if($paginate != null) {
              return $result->paginate(25);
            } else {
              return $result->get()->toArray();
            }
            // return $result->get()->toArray();
    }

}
