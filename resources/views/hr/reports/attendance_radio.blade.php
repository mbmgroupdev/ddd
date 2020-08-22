<?php

  $absent_present_list= url('');
  $bonus_summmary_report= url('');
  $salary_summary_report= url('');
  $earn_leave_report= url('');
  $fixed_salary_report= url('');
  /*$employee_report= url("hr/reports/employee_report");*/
  $at_report_link    = url('hr/reports/attendance_report');
  $at_report_2_link  = url('hr/reports/attendance_report_2');
  //$unit_report_link  = url('hr/reports/unitattendance');
  //$line_report_link  = url('hr/reports/line_wise_att');
  //$absent_report_link= url('hr/reports/absent_status');
  /*$worker_report_link= url('hr/reports/worker_register');*/
  //$ot_status= url('hr/reports/daily_ot_report');

  //$leave_report  = url('hr/reports/leave_report');
  //$increment_link           = url('hr/reports/increment_report');
  $m_increment_link         = url('hr/reports/monthy_increment');
  $group_absent_report_link = url('hr/reports/group_attendance');

?>
@push('css')
  <style>
   .radio_section .btn{
        margin-bottom: 20px;
        min-width: 180px;
        float: left;
        text-align: left;

    }
    .radio_section{width: 100%;}

    .radio_section label{width: 100%; padding: 0px !important; margin: 0px; cursor: pointer;}

  </style>
@endpush
<div class="row">
<div class="col-sm-12 hidden-xs radio_section">
        <!-- <div class="col-sm-2">
        <button class="btn btn-sm btn-white btn-success">  <label> <input type="radio" name="attendance" class="ace" value="{{$absent_present_list}}" <?php //if ($type == 'absent_present_list') echo 'checked="checked"'; ?>  onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Absent & Present List</span>
          </label></button>
       </div> -->
       

       

        <div class="col-sm-2">
          <button class="btn btn-sm btn-white btn-danger">
         <label> <input type="radio" name="attendance" class="ace" value="{{$m_increment_link}}" <?php if ($type == 'm_increment') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Monthly Increment</span>
         </label></button>
        </div>

       <div class="col-sm-2">
       <button class="btn btn-sm btn-white btn-default">
         <label> <input type="radio" name="attendance_2" class="ace" value="{{$at_report_2_link}}" <?php if ($type == 'attendance_2') echo 'checked="checked"'; ?>  onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Attendance Summary Report</span>
       </label></button>
       </div>

       
        <div class="col-sm-2">
        <button class="btn btn-sm btn-white btn-default">
          <label> <input type="radio" name="attendance" class="ace" value="{{$group_absent_report_link}}" <?php if ($type == 'group_attendance_report') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;"> Group Attendance</span>
          </label></button>
       </div>

  


</div>
</div>
