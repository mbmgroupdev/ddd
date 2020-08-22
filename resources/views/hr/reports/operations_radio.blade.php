<?php
  $Job_report_link   = url('hr/reports/job_application');
  $file_tag_link     = url('hr/reports/file_tag');
  $job_card_link     = url('hr/reports/job_card');
  $nominee_link      = url('hr/reports/nominee');
  $bg_verify_link    = url('hr/reports/back_verf');
  $leave_report_link = url('hr/reports/leave_log');
  $fixed_salary_link= url('hr/reports/fixedsalarysheet');
  $bonus_slip_link          = url('hr/reports/bonus_slip');
  $new_bonus_slip_link          = url('hr/reports/new_bonus_slip');
  $salary_sheet_link        = url('hr/reports/salary-sheet-custom');
  $pay_slip_link            = url('hr/reports/payslip');
  $earn_leave_link          = url('hr/reports/earnleavepayment');
  //$extra_ot_link            = url('hr/reports/extraotsheet');
  //$extra_ot_link            = url('hr/reports/salary-sheet-custom-extra-ot');
  $manual_attendance_link   = url('hr/reports/manual_attendance');
  //$employee_bonus_link   = url('hr/operation/employee_bonus');
  $late_count_default_link   = url('hr/setup/late_count_default');
  $late_count_customize_link   = url('hr/setup/late_count_customize');
  $joining_letter= url('hr/recruitment/job_portal/joining_letter');
  $salary_sheet_link_day_wise = url('hr/reports/salary_sheet_unit_wise_day');
  $substitute_holiday_link = url('hr/opration/substitute_holiday');

?>

@push('css')
  <style>
    
    .col-sm-2 {padding: 0;}
   .radio_section .btn{
        margin-bottom: 20px;
        min-width: 173px;
        float: left;
        text-align: left;
        border-radius: 2px;

    }
    .btn-group-sm>.btn, .btn-sm { padding: 4px 4px;}
    .radio_section{width: 100%; padding: 0;}

    .radio_section label{width: 100%; padding: 0px !important; margin: 0px; cursor: pointer;}

  </style>
@endpush

<div class="row">
  <div class="col-sm-12 hidden-xs">
    <div id="accordion-operation" class="accordion-style panel-group">
      <div class="panel panel-info">
          <div class="panel-heading salary-sheet-content">
              <h2 class="panel-title">
                  <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-operation" href="#operations" aria-expanded="false" style="font-size: 15px;">
                      <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                      &nbsp;Operation tabs
                  </a>
              </h2>
          </div>

          <div class="panel-collapse collapse" id="operations" aria-expanded="false" style="height: 0px;">
              <div class="panel-body">
                <div class="col-sm-12 hidden-xs radio_section">
                      <div class="col-sm-2">
                        <button class="btn btn-sm btn-white btn-warning">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$job_card_link}}" <?php if ($type == 'job_card') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Job Card</span>
                          </label>
                        </button>
                      </div>

                      <div class="col-sm-2">
                        <button class="btn btn-sm btn-white btn-primary">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$Job_report_link}}" <?php if ($type == 'job_application') echo 'checked="checked"'; ?>  onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Job Application / Biodata</span>
                          </label>
                        </button>
                      </div>

                      <div class="col-sm-2">
                        <button class="btn btn-sm btn-white btn-danger">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$file_tag_link}}" <?php if ($type == 'file_tag') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  File Tag</span>
                        </label>
                        </button>
                      </div>

                      <div class="col-sm-2">
                        <button class="btn btn-sm btn-white btn-default">
                         <label> <input type="radio" name="attendance" class="ace" value="{{$nominee_link}}" <?php if ($type == 'nominee') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">   Nominee</span>
                        </label>
                        </button>
                      </div>


                      <div class="col-sm-2">
                        <button class="btn btn-sm btn-white btn-success">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$bg_verify_link}}" <?php if ($type == 'bg_verification') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Background Verification</span>
                            </label>
                        </button>
                      </div>

                        <!-- <td>
                          <label> <input type="radio" name="attendance" class="ace" value="{{$bonus_slip_link}}" <?php if ($type == 'bonus_slip') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Bonus Sheet</span>
                         </label>
                        </td> -->
                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-default">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$new_bonus_slip_link}}" <?php if ($type == 'new_bonus_slip') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">Bonus Sheet</span>

                         </label>
                        </button>
                       </div>


                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-primary">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$salary_sheet_link}}" <?php if ($type == 'salary_sheet_link') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">   Salary Sheet</span>
                          </label>
                        </button>
                        </div>

                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-danger">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$salary_sheet_link_day_wise}}" <?php if ($type == 'salary_sheet_link_day_wise') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">   Salary Sheet Day Wise</span>
                          </label>
                        </button>
                        </div>

                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-success">
                           <label> <input type="radio" name="attendance" class="ace" value="{{$pay_slip_link}}" <?php if ($type == 'pay_slip') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">   Pay Slip</span>
                           </label>
                        </button>
                        </div>

                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-default">
                          <label> <input type="radio" name="attendance" class="ace" value="{{$earn_leave_link}}" <?php if ($type == 'earn_leave') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Earned Leave Payment</span>
                            </label>
                        </button>
                        </div>

                       <!-- <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-success">
                          <label> <input type="radio" name="attendance" class="ace" value="{{-- {{$extra_ot_link}} --}}" {{-- <?php if ($type == 'extra_ot_link_radio') echo 'checked="checked"'; ?> --}} onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Extra OT Sheet</span>
                            </label>
                        </button>
                        </div> -->

                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-warning">
                          <label> <input type="radio" name="extra_ot" class="ace" value="{{$fixed_salary_link}}" <?php if ($type == 'fixed_salary') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Fixed Salary Sheet</span>
                            </label>
                        </button>
                      </div>

                      <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-primary">
                        <label> <input type="radio" name="attendance" class="ace" value="{{$leave_report_link}}" <?php if ($type == 'leave_log') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Leave Log</span>
                        </label>
                        </button>
                        </div>

                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-warning">
                         <label> <input type="radio" name="attendance" class="ace" value="{{$joining_letter}}" <?php if ($type == 'joining_letter') echo 'checked="checked"'; ?> onclick="attLocation(this.value)">
                          <span class="lbl" style="font-size:12px;"> Appointment Letter</span>
                         </label>
                        </button>
                      </div>

                        <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-danger">
                         <label> <input type="radio" name="attendance" class="ace" value="{{$manual_attendance_link}}" <?php if ($type == 'manual_attendance') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Manual Attendance</span>
                         </label>
                        </button>
                      </div>

                      <div class="col-sm-2">
                         <button class="btn btn-sm btn-white btn-danger">
                         <label> <input type="radio" name="attendance" class="ace" value="{{$substitute_holiday_link}}" <?php if ($type == 'substitute_holiday') echo 'checked="checked"'; ?> onclick="attLocation(this.value)"> <span class="lbl" style="font-size:12px;">  Substitute Holidays</span>
                         </label>
                        </button>
                      </div>

                      


                </div>
              </div>
          </div>
      </div>
      
    </div>
  </div>
</div>
