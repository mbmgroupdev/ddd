<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, DB, Session,PDF;
use App\Models\Hr\Increment;
use App\Models\Hr\promotion;
use App\Models\Hr\HrMonthlySalary;
use App\Models\Employee;
use Carbon\Carbon;
use Calendar;
use DateTime;
use DatePeriod;
use DateInterval;

class ProfileController extends Controller
{
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

    public function showProfile()
    {
        $associate_id = Auth::user()->associate_id;

      /*  $ii=Employee::with('designation','floor')->where('associate_id',$associate_id)->first();
        dd($ii);*/

        $info = DB::table('hr_as_basic_info AS b')
            ->select(
                'b.*',
                'u.hr_unit_name',
                'u.hr_unit_name_bn',
                'f.hr_floor_name',
                'f.hr_floor_name_bn',
                'l.hr_line_name',
                'l.hr_line_name_bn',
                'dp.hr_department_name',
                'dp.hr_department_name_bn',
                'dg.hr_designation_name',
                'dg.hr_designation_name_bn',
                'a.*',
                'be.*',
                'm.*',
                'e.hr_emp_type_name',
                'ar.hr_area_name',
                'se.hr_section_name',
                'se.hr_section_name_bn',
                'sb.hr_subsec_name',
                'sb.hr_subsec_name_bn',
                'bn.*',
                # unit/floor/line/shif
                DB::raw("
                    CONCAT_WS('. ',
                        CONCAT('Unit: ', u.hr_unit_name),
                        CONCAT('Floor: ', f.hr_floor_name),
                        CONCAT('Line: ', l.hr_line_name)
                    ) AS unit_floor_line
                "),
                # permanent district & upazilla
                "per_dist.dis_name AS permanent_district",
                "per_dist.dis_name_bn AS permanent_district_bn",
                "per_upz.upa_name AS permanent_upazilla",
                "per_upz.upa_name_bn AS permanent_upazilla_bn",
                # present district & upazilla
                "pres_dist.dis_name AS present_district",
                "pres_dist.dis_name_bn AS present_district_bn",
                "pres_upz.upa_name AS present_upazilla",
                "pres_upz.upa_name_bn AS present_upazilla_bn"
            )
            ->leftJoin('hr_area AS ar', 'ar.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_section AS se', 'se.hr_section_id', '=', 'b.as_area_id')
            ->leftJoin('hr_subsection AS sb', 'sb.hr_subsec_id', '=', 'b.as_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "b.associate_id")
            ->leftJoin('hr_benefits AS be',function ($leftJoin) {
                $leftJoin->on('be.ben_as_id', '=' , 'b.associate_id') ;
                $leftJoin->where('be.ben_status', '=', '1') ;
            })
            ->leftJoin('hr_med_info AS m', 'm.med_as_id', '=', 'b.associate_id')

            #permanent district & upazilla
            ->leftJoin('hr_dist AS per_dist', 'per_dist.dis_id', '=', 'a.emp_adv_info_per_dist')
            ->leftJoin('hr_upazilla AS per_upz', 'per_upz.upa_id', '=', 'a.emp_adv_info_per_upz')
            #present district & upazilla
            ->leftJoin('hr_dist AS pres_dist', 'pres_dist.dis_id', '=', 'a.emp_adv_info_pres_dist')
            ->leftJoin('hr_upazilla AS pres_upz', 'pres_upz.upa_id', '=', 'a.emp_adv_info_pres_upz')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
            ->where("b.associate_id", $associate_id)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->first();

            //dd($info);

        if(empty($info)) abort(404, "User not found!");

        $loans = DB::table("hr_loan_application")
            ->select(
            "*",
            DB::raw("
                CASE
                    WHEN hr_la_status = '0' THEN 'Applied'
                    WHEN hr_la_status = '1' THEN 'Approved'
                    WHEN hr_la_status = '2' THEN 'Declined'
                END AS hr_la_status
            ")
        )
        ->where("hr_la_as_id", $associate_id)
        ->get();


        $leaves = DB::table('hr_leave')
            ->select(
                DB::raw("
                    YEAR(leave_from) AS year,
                    SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual,
                    SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned,
                    SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick,
                    SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity,
                    SUM(CASE WHEN leave_type = 'Special' THEN DATEDIFF(leave_to, leave_from)+1 END) AS special,
                    SUM(DATEDIFF(leave_to, leave_from)+1) AS total
                ")
            )
            ->where('leave_status', '1')
            ->where("leave_ass_id", $associate_id)
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->get();

        $earnedLeaves = $this->earnedLeave($leaves,$info->as_id,$info->associate_id,$info->as_unit_id);



        $records = DB::table('hr_dis_rec AS r')
            ->select(
                'r.*',
                DB::raw("CONCAT_WS(' to ', r.dis_re_doe_from, r.dis_re_doe_to) AS date_of_execution"),
                'i.hr_griv_issue_name',
                's.hr_griv_steps_name'
            )
            ->leftJoin('hr_grievance_issue AS i', 'i.hr_griv_issue_id', '=', 'r.dis_re_issue_id')
            ->leftJoin('hr_grievance_steps AS s', 's.hr_griv_steps_id', '=', 'r.dis_re_issue_id')
            ->where('r.dis_re_offender_id', $associate_id)
            ->get();



        $promotions = DB::table("hr_promotion AS p")
            ->select(
                "d1.hr_designation_name AS previous_designation",
                "d2.hr_designation_name AS current_designation",
                "p.eligible_date",
                "p.effective_date"
            )
            ->leftJoin("hr_designation AS d1", "d1.hr_designation_id", "=", "p.previous_designation_id")
            ->leftJoin("hr_designation AS d2", "d2.hr_designation_id", "=", "p.current_designation_id")
            ->where('p.associate_id', $associate_id)
            ->orderBy('p.effective_date', "DESC")
            ->get();

        $increments = Increment::where('associate_id', $associate_id)
            ->orderBy('effective_date', 'DESC')->get();

        $educations = DB::table('hr_education AS e')
            ->select(
                'l.education_level_title',
                'dt.education_degree_title',
                'e.education_level_id',
                'e.education_degree_id_2',
                'e.education_major_group_concentation',
                'e.education_institute_name',
                'r.education_result_title',
                'e.education_result_id',
                'e.education_result_marks',
                'e.education_result_cgpa',
                'e.education_result_scale',
                'e.education_passing_year'
            )
            ->leftJoin('hr_education_level AS l', 'l.id', '=', 'e.education_level_id')
            ->leftJoin('hr_education_degree_title AS dt', 'dt.id', '=', 'e.education_degree_id_1')
            ->leftJoin('hr_education_result AS r', 'r.id', '=', 'e.education_result_id')
            ->where("e.education_as_id", $associate_id)
            ->get();
        //$as_id='18B010023C';
        $year=now()->year;
        $month=now()->month-1;

        $station= DB::table('hr_station AS s')
                    ->where('s.associate_id', $associate_id)
                    ->whereDate('s.start_date', "<=", date('Y-m-d'))
                    ->whereDate('s.end_date', ">=", date("Y-m-d"))
                    ->select([
                        "s.associate_id",
                        "s.changed_floor",
                        "s.changed_line",
                        "s.start_date",
                        "s.updated_by",
                        "s.end_date",
                        "f.hr_floor_name",
                        "l.hr_line_name",
                        "b.as_name"
                    ])
                    ->leftJoin('hr_floor AS f', 'f.hr_floor_id', 's.changed_floor')
                    ->leftJoin('hr_line AS l', 'l.hr_line_id', 's.changed_line')
                    ->leftJoin('hr_as_basic_info AS b', 'b.associate_id', 's.updated_by')
                    ->first();

                $getSalaryList      = HrMonthlySalary::where('as_id', $info->as_id)
                                    ->where('year',2019)
                                    ->where('month',4)
                                    ->get();
                $title              = 'Unit : '.$info->hr_unit_name_bn.' - Location : '.$info->hr_unit_name_bn;
                $pageHead['current_date']   = date('d-m-Y');
                $pageHead['current_time']   = date('H:i');
                $pageHead['pay_date']       = '';
                $pageHead['unit_name']      = $info->hr_unit_name_bn;
                $pageHead['for_date']       = 'Jan, '.date('Y').' - '.date('M, Y');
                $pageHead['floor_name']     = $info->hr_floor_name_bn;

                $pageHead = (object) $pageHead;

                return view('hr.profile', compact('info','loans', 'leaves', 'records','promotions','increments','educations','station','getSalaryList', 'title', 'pageHead','earnedLeaves'));

    }

    public function employeeProfile()
    {
        $associate_id = Auth::user()->associate_id;
        $info = DB::table('hr_as_basic_info AS b')
            ->select(
                'b.*',
                'u.hr_unit_name',
                'u.hr_unit_name_bn',
                'f.hr_floor_name',
                'f.hr_floor_name_bn',
                'l.hr_line_name',
                'l.hr_line_name_bn',
                'dp.hr_department_name',
                'dp.hr_department_name_bn',
                'dg.hr_designation_name',
                'dg.hr_designation_name_bn',
                'a.*',
                'be.*',
                'm.*',
                'e.hr_emp_type_name',
                'ar.hr_area_name',
                'se.hr_section_name',
                'se.hr_section_name_bn',
                'sb.hr_subsec_name',
                'sb.hr_subsec_name_bn',
                'bn.*',
                # unit/floor/line/shif
                DB::raw("
                    CONCAT_WS('. ',
                        CONCAT('Unit: ', u.hr_unit_name),
                        CONCAT('Floor: ', f.hr_floor_name),
                        CONCAT('Line: ', l.hr_line_name)
                    ) AS unit_floor_line
                "),
                # permanent district & upazilla
                "per_dist.dis_name AS permanent_district",
                "per_dist.dis_name_bn AS permanent_district_bn",
                "per_upz.upa_name AS permanent_upazilla",
                "per_upz.upa_name_bn AS permanent_upazilla_bn",
                # present district & upazilla
                "pres_dist.dis_name AS present_district",
                "pres_dist.dis_name_bn AS present_district_bn",
                "pres_upz.upa_name AS present_upazilla",
                "pres_upz.upa_name_bn AS present_upazilla_bn"
            )
            ->leftJoin('hr_area AS ar', 'ar.hr_area_id', '=', 'b.as_area_id')
            ->leftJoin('hr_section AS se', 'se.hr_section_id', '=', 'b.as_area_id')
            ->leftJoin('hr_subsection AS sb', 'sb.hr_subsec_id', '=', 'b.as_area_id')
            ->leftJoin('hr_emp_type AS e', 'e.emp_type_id', '=', 'b.as_emp_type_id')
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_floor AS f', 'f.hr_floor_id', '=', 'b.as_floor_id')
            ->leftJoin('hr_line AS l', 'l.hr_line_id', '=', 'b.as_line_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "b.associate_id")
            ->leftJoin('hr_benefits AS be',function ($leftJoin) {
                $leftJoin->on('be.ben_as_id', '=' , 'b.associate_id') ;
                $leftJoin->where('be.ben_status', '=', '1') ;
            })
            ->leftJoin('hr_med_info AS m', 'm.med_as_id', '=', 'b.associate_id')

            #permanent district & upazilla
            ->leftJoin('hr_dist AS per_dist', 'per_dist.dis_id', '=', 'a.emp_adv_info_per_dist')
            ->leftJoin('hr_upazilla AS per_upz', 'per_upz.upa_id', '=', 'a.emp_adv_info_per_upz')
            #present district & upazilla
            ->leftJoin('hr_dist AS pres_dist', 'pres_dist.dis_id', '=', 'a.emp_adv_info_pres_dist')
            ->leftJoin('hr_upazilla AS pres_upz', 'pres_upz.upa_id', '=', 'a.emp_adv_info_pres_upz')
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')
            ->where("b.associate_id", $associate_id)
            ->whereIn('b.as_unit_id', auth()->user()->unit_permissions())
            ->first();

        if(empty($info)) abort(404, "User not found!");

        $loans = DB::table("hr_loan_application")
            ->select(
            "*",
            DB::raw("
                CASE
                    WHEN hr_la_status = '0' THEN 'Applied'
                    WHEN hr_la_status = '1' THEN 'Approved'
                    WHEN hr_la_status = '2' THEN 'Declined'
                END AS hr_la_status
            ")
        )
        ->where("hr_la_as_id", $associate_id)
        ->get();


        $leaves = DB::table('hr_leave')
            ->select(
                DB::raw("
                    YEAR(leave_from) AS year,
                    SUM(CASE WHEN leave_type = 'Casual' THEN DATEDIFF(leave_to, leave_from)+1 END) AS casual,
                    SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS earned,
                    SUM(CASE WHEN leave_type = 'Sick' THEN DATEDIFF(leave_to, leave_from)+1 END) AS sick,
                    SUM(CASE WHEN leave_type = 'Maternity' THEN DATEDIFF(leave_to, leave_from)+1 END) AS maternity,
                    SUM(CASE WHEN leave_type = 'Special' THEN DATEDIFF(leave_to, leave_from)+1 END) AS special,
                    SUM(DATEDIFF(leave_to, leave_from)+1) AS total
                ")
            )
            ->where('leave_status', '1')
            ->where("leave_ass_id", $associate_id)
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->get();
        $earnedLeaves = $this->earnedLeave($leaves,$info->as_id,$info->associate_id,$info->as_unit_id);



        $records = DB::table('hr_dis_rec AS r')
            ->select(
                'r.*',
                DB::raw("CONCAT_WS(' to ', r.dis_re_doe_from, r.dis_re_doe_to) AS date_of_execution"),
                'i.hr_griv_issue_name',
                's.hr_griv_steps_name'
            )
            ->leftJoin('hr_grievance_issue AS i', 'i.hr_griv_issue_id', '=', 'r.dis_re_issue_id')
            ->leftJoin('hr_grievance_steps AS s', 's.hr_griv_steps_id', '=', 'r.dis_re_issue_id')
            ->where('r.dis_re_offender_id', $associate_id)
            ->get();



        $promotions = DB::table("hr_promotion AS p")
            ->select(
                "d1.hr_designation_name AS previous_designation",
                "d2.hr_designation_name AS current_designation",
                "p.eligible_date",
                "p.effective_date"
            )
            ->leftJoin("hr_designation AS d1", "d1.hr_designation_id", "=", "p.previous_designation_id")
            ->leftJoin("hr_designation AS d2", "d2.hr_designation_id", "=", "p.current_designation_id")
            ->where('p.associate_id', $associate_id)
            ->orderBy('p.effective_date', "DESC")
            ->get();

        $increments = Increment::where('associate_id', $associate_id)
            ->orderBy('effective_date', 'DESC')->get();

        $educations = DB::table('hr_education AS e')
            ->select(
                'l.education_level_title',
                'dt.education_degree_title',
                'e.education_level_id',
                'e.education_degree_id_2',
                'e.education_major_group_concentation',
                'e.education_institute_name',
                'r.education_result_title',
                'e.education_result_id',
                'e.education_result_marks',
                'e.education_result_cgpa',
                'e.education_result_scale',
                'e.education_passing_year'
            )
            ->leftJoin('hr_education_level AS l', 'l.id', '=', 'e.education_level_id')
            ->leftJoin('hr_education_degree_title AS dt', 'dt.id', '=', 'e.education_degree_id_1')
            ->leftJoin('hr_education_result AS r', 'r.id', '=', 'e.education_result_id')
            ->where("e.education_as_id", $associate_id)
            ->get();

        $year=now()->year;
        $month=now()->month-1;




        $getSalaryList      = HrMonthlySalary::where('as_id', $info->as_id)
                            ->get();
        //$getEmployee        = Employee::getEmployeeAssociateIdWise($info->associate_id);
        $title              = 'Unit : '.$info->hr_unit_name_bn.' - Location : '.$info->hr_unit_name_bn;
        $pageHead['current_date']   = date('d-m-Y');
        $pageHead['current_time']   = date('H:i');
        $pageHead['pay_date']       = '';
        $pageHead['unit_name']      = $info->hr_unit_name_bn;
        $pageHead['for_date']       = 'Jan, '.date('Y').' - '.date('M, Y');
        $pageHead['floor_name']     = $info->hr_floor_name_bn;

        $pageHead = (object) $pageHead;

        $pdf = PDF::loadView('hr.pdfprofile', [
                'info'           =>$info,
                'loans'          =>$loans,
                'leaves'         =>$leaves,
                'records'        =>$records,
                'promotions'     =>$promotions,
                'increments'     =>$increments,
                'educations'     =>$educations,
                'getSalaryList'  =>$getSalaryList,
                'title'          =>$title,
                'pageHead'       =>$pageHead,
                'earnedLeaves'   =>$earnedLeaves
              ]);
        return $pdf->download('Employee_Report_'.$info->associate_id.'_'.date('d_F_Y').'.pdf');
    }

    public function earnedLeave($leaves, $as_id, $associate_id, $unit_id)
    {
        $table = $this->getTableName($unit_id);
        $leavesForEarned = collect($leaves)->sortBy('year');
        //dd()
            
        $earnedLeaves = [];
        if(count($leavesForEarned)>0){
            $remainEarned = 0;
            foreach($leavesForEarned AS $yearlyLeave){
                
                $attendance = DB::table($table)
                                ->where('a.as_id',$as_id)
                                ->whereYear('a.in_time', $yearlyLeave->year)
                                ->count();

                $earnedTotal = intval($attendance/18)+$remainEarned;
                

                $enjoyed = DB::table("hr_leave")
                            ->select(
                                DB::raw("
                                    SUM(CASE WHEN leave_type = 'Earned' THEN DATEDIFF(leave_to, leave_from)+1 END) AS enjoyed
                                ")
                            )
                            ->where("leave_ass_id", $associate_id)
                            ->where("leave_status", "1")
                            ->where(DB::raw("YEAR(leave_from)"), '=', $yearlyLeave->year)
                            ->value("enjoyed");

                $remainEarned = $earnedTotal-$enjoyed;

                $earnedLeaves[$yearlyLeave->year]['remain'] = $remainEarned;
                $earnedLeaves[$yearlyLeave->year]['enjoyed'] = $enjoyed;
                $earnedLeaves[$yearlyLeave->year]['earned'] = $earnedTotal;

            }   
        }else{
            $yearAtt = DB::table($table)
                            ->select(DB::raw('count(as_id) as att'))
                            ->where('a.as_id',$as_id)
                            ->groupBy(DB::raw('Year(in_time)'))
                            ->first();
            //dd($yearAtt);
            $earnedTotal = 0;
            if($yearAtt!= null){
                foreach ($yearAtt as $key => $att) {
                    $earnedTotal += intval($att/18);    
                }
                
            }
            $earnedLeaves[date('Y')]['remain'] = $earnedTotal;
            $earnedLeaves[date('Y')]['enjoyed'] = 0;
            $earnedLeaves[date('Y')]['earned'] = $earnedTotal;
        }
        return $earnedLeaves;
       
    }


    public function attendanceCalendar($associate_id){

        //$associate_id = Auth::user()->associate_id;
        $info=Employee::where('associate_id',$associate_id)->first();

        $tableName    = $this->getTableName($info->as_unit_id);

        $firstDay = new \DateTime('first day of this month');
        $lastDay = new \DateTime('last day of this month');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($firstDay, $interval, $lastDay);
        $events=[];

        foreach ($period as $dt) {
            $evstart= $dt->format("Y-m-d");
            $evend= $dt->format("Y-m-d");
            $wholeday= true;
            $color='';
            $textColor='#FFF';

            if($dt->format("Y-m-d")<= date('Y-m-d')){
                $daystart=$dt->format("Y-m-d")." 00:00:00";
                $dayend=$dt->format("Y-m-d")." 23:59:59";



                $attendence  = DB::table($tableName)->where('as_id',$info->as_id)
                               ->whereBetween('in_time',[$daystart,$dayend])
                               ->first();
                if($attendence){
                    $evstart= $attendence->in_time;
                    if($attendence->out_time!=null){
                        $evend= $attendence->out_time;
                    }

                    $status= 'Present';
                    $wholeday= false;
                    $color='#00BA1D';
                }else{
                    $holiday=DB::table('hr_yearly_holiday_planner')
                             ->where([
                                'hr_yhp_dates_of_holidays' => $dt->format("Y-m-d"),
                                'hr_yhp_unit' => $info->as_unit_id
                                ])
                            ->first();
                        if($holiday){
                            $status= $holiday->hr_yhp_comments;
                            $color= '#0984e3';
                        }else{
                            $leave = DB::table('hr_leave')
                                    ->where('leave_ass_id', $info->associate_id )
                                    ->where('leave_from','<=', $dt->format("Y-m-d"))
                                    ->where('leave_to','>=', $dt->format("Y-m-d"))
                                    ->where('leave_status','1')
                                    ->first();

                            if($leave){
                                $status=$leave->leave_type.' Leave';
                                $color= '#e67e22';


                            }else{
                                $status= 'Absent';
                                $color='#e74c3c';
                            }
                        }
                }
            }else{
                $holiday=DB::table('hr_yearly_holiday_planner')
                     ->where([
                        'hr_yhp_dates_of_holidays' => $dt->format("Y-m-d"),
                        'hr_yhp_unit' => $info->as_unit_id
                        ])
                    ->first();
                if($holiday){
                    $status= $holiday->hr_yhp_comments;
                    $color= '#0984e3';
                }else{
                    $status= '';
                    $color='#fff';
                }

            }

            $events[] = Calendar::event(
                $status,
                $wholeday,
                new \DateTime($evstart),
                new \DateTime($evend),
                '',
                [
                    'color' => $color,
                    'textColor' => $textColor
                ]);
        }


      $calendar = Calendar::addEvents($events)
                  ->setOptions([
                        'selectable' => true,
                        'defaultDate'=>date('Y-m-d')
                ]);
      return view('hr.attendance_calendar', compact('calendar'));
    }
}
