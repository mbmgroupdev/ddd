
@php
   $user = auth()->user();
   $segment1 = request()->segment(1);
   $segment2 = request()->segment(2);
   $segment3 = request()->segment(3);
   $segment4 = request()->segment(4);
@endphp

<nav class="iq-sidebar-menu">
   <ul id="iq-sidebar-toggle" class="iq-menu">
       <li>
         <a style="background: #daf0f3;color: #000;">
            <i class="las la-city "></i>  <span> {!!permitted_units()!!} </span>
            
         </a>
      </li>
      <li>
         <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
      </li>
      <li class="{{ $segment2 == ''?'active':'' }}">
         <a href="{{ url('/hr') }}" class="iq-waves-effect"><i class="las la-users"></i><span>HR Dashboard</span></a>
      </li>
      <!-- Recruitment Sub menu start -->
      @if(auth()->user()->canany(['New Recruit','Recruit List','Medical List','IE List','Nominee','Background Verification','Job Application','Appointment Letter','Job Posting','Job Posting List','Interview Notes','Print File Tag','Interview Notes List','ID Card']) || $user->hasRole('Super Admin'))

      <li class="@if($segment2 == 'recruitment') active @endif">
         <a href="#recruitment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-user-plus"></i></i><span>Recruitment</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="recruitment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('New Recruit') || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'recruitment' && $segment4=='create') active @endif">
               <a  href="{{ url('/hr/recruitment/recruit/create') }}"><i class="las la-user-plus"></i> New Recruit</a>
            </li>
            @endif
            @if($user->can('Recruit List' ) || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'recruit' && $segment4=='') active @endif">
               <a href="{{ url('/hr/recruitment/recruit') }}"><i class="las la-list-ol"></i>Recruit List</a>
            </li>
            @endif
            @if($user->can('Job Application') || $user->hasRole('Super Admin'))
            <li class="@if( $segment3=='job-application') active @endif">
               <a href="{{url('hr/recruitment/job-application')}}"><i class="las la-file-contract"></i>Job Application</a>
            </li>
            @endif
            @if($user->can('Appointment Letter') || $user->hasRole('Super Admin'))
            <li class="@if( $segment3=='appointment-letter') active @endif">
               <a href="{{ url('hr/recruitment/appointment-letter') }}"><i class="las la-file-contract"></i>Appointment Letter</a>
            </li>
            @endif
            @if($user->can('Nominee') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'nominee') active @endif">
               <a href="{{ url('hr/recruitment/nominee') }}"><i class="las la-address-card"></i>Nominee</a>
            </li>
            @endif
            @if($user->can('Background Verification') || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'recruitment' && $segment3=='background-verification') active @endif">
               <a href="{{ url('hr/recruitment/background-verification') }}"><i class="las la-user-check"></i>Background Verification</a>
            </li>
            @endif
            @if($user->can('ID Card') || $user->hasRole('Super Admin'))
            <li class="@if($segment4 == 'idcard') active @endif">
               <a href="{{ url('hr/recruitment/employee/idcard') }}"><i class="las la-list-ul"></i>ID Card</a>
            </li>
            @endif
            @if($user->can('Print File Tag') || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'recruitment' && $segment3=='file_tag') active @endif">
               <a href="{{ url('hr/recruitment/file_tag') }}"><i class="las la-folder-plus"></i>Print File Tag</a>
            </li>
            @endif
         </ul>
      </li>
      @endif
      
      @if(auth()->user()->canany(['Employee List','Advance Info List','Medical Entry','Cost Distribution','Cost Distribution List','Manage Employee']) || $user->hasRole('Super Admin'))

      <li class="@if($segment2 == 'employee') active @endif">
         <a href="#employee" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-user-tie"></i><span>Employee</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="employee" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->canany(['Employee List','Employee Hierarchy','
            Manage Employee']) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'employee' && $segment3=='list') active @endif">
               <a href="{{ url('hr/employee/list') }}"><i class="las la-list-ul"></i>All Employee</a>
            </li>
            @endif
            @if($user->canany(['Employee List','Employee Hierarchy','
            Manage Employee']) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'employee' && $segment3=='new-employee') active @endif">
               <a href="{{ url('hr/employee/new-employee') }}"><i class="las la-list-ul"></i>New Employee</a>
            </li>
            @endif
            @if($user->can('Manage Employee') || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'employee' && $segment3=='incomplete-list') active @endif">
               <a href="{{ url('hr/employee/incomplete-list') }}"><i class="las la-list-ul"></i>Missing Info</a>
            </li>
            @endif
            @if($user->can('Medical Incident') || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'employee' && $segment3=='medical_incident') active @endif">
               <a href="{{ url('hr/employee/medical_incident') }}"><i class="las la-user-md"></i>Medical Incident</a>
            </li>
            @endif
            @if($user->can('Service Book') || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'employee' && $segment3=='servicebook') active @endif">
               <a href="{{ url('hr/employee/servicebook') }}"><i class="las la-book"></i>Service Book</a>
            </li>
            @endif
            @if($user->canany(['Cost Distribution']) || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'cost-mapping') active @endif">
               <a href="{{ url('hr/employee/cost-mapping') }}"><i class="las la-gifts"></i> Cost Distribution</a>
            </li>
            @endif
            
         </ul>
      </li>
      @endif

      @if(auth()->user()->canany(['Manual Attendance','Manage Leave','Leave List','Attendance Upload']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'timeattendance') active @endif">
         <a href="#timeattendance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-fingerprint"></i><span>Time & Attendance</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="timeattendance" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('Attendance Upload') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'attendance-upload') active @endif">
               <a href="{{ url('hr/timeattendance/attendance-upload') }}"><i class="las la-fingerprint"></i>Attendance Upload</a>
            </li>
            @endif
            @if($user->can('Manage Leave') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'leave-entry') active @endif">
               <a href="{{ url('hr/timeattendance/leave-entry') }}"><i class="las la-file-alt"></i>Employee Leave</a>
            </li>
            @endif
            @if($user->canany(['Manage Leave','Leave List']) || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'all_leaves') active @endif">
               <a href="{{ url('hr/timeattendance/all_leaves') }}"><i class="las la-file-alt"></i>Leave List</a>
            </li>
            @endif
         </ul>
      </li>
      @endif

      @if(auth()->user()->canany(['Manage Promotion','Manage Increment','Salary Adjustment','End of Job Benefits','Loan List','Assign Benefit','Bank Sheet']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'payroll') active @endif">
         <a href="#payroll" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-money-check-alt"></i><span>Payroll</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="payroll" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

            @if($user->can('Bank Sheet') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'bank-sheet') active @endif">
               <a href="{{ url('hr/payroll/bank-sheet') }}"><i class="las la-chart-line"></i>Bank Sheet</a>
            </li>
            @endif
            @if($user->canany(['Assign Benefit','Benefit List']) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'payroll' && $segment3=='employee-benefit') active @endif">
               <a href="{{ url('hr/payroll/employee-benefit') }}"><i class="las la-gifts"></i> Benefits</a>

            </li>
            @endif
            @if($user->can('Manage Promotion') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'promotion') active @endif">
               <a href="{{ url('hr/payroll/promotion') }}"><i class="las la-chart-line"></i>Promotion</a>
            </li>
            @endif
            @if($user->can('Manage Increment') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'increment') active @endif">
               <a href="{{ url('hr/payroll/increment') }}"><i class="las la-chart-area"></i>Increment</a>
            </li>
            @endif
            @if($user->can('Salary Adjustment') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'monthly-salary-adjustment-list') active @endif">
               <a href="{{ url('hr/payroll/monthly-salary-adjustment-list') }}"><i class="las la-funnel-dollar"></i>Salary Adjustment</a>
            </li>
            @endif
            @if($user->can('End of Job Benefits') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'benefits') active @endif">
               <a href="{{ url('hr/payroll/benefits') }}"><i class="las la-gift"></i>End Of Job Benefits</a>
            </li>
            @endif
            @if($user->can('Loan List') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'loan') active @endif"><a href="{{ url('hr/payroll/loan') }}"><i class="las la-dollar-sign"></i>Loan</a></li>
            @endif
         </ul>
      </li>
      @endif

      @if(auth()->user()->canany(['Performance Appraisal','Performance List','Disciplinary Record','Disciplinary List']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'performance') active @endif">
         <a href="#performance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-award"></i><span>Performance</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="performance" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('Performance Appraisal') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'appraisal') active @endif">
               <a href="{{ url('hr/performance/appraisal') }}"><i class="las la-award"></i>Performance Appraisal</a>
            </li>
            @endif
            @if($user->can('Disciplinary Record') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'disciplinary-record') active @endif">
               <a href="{{ url('hr/performance/disciplinary-record') }}"><i class="las la-question-circle"></i>Disciplinary Record</a>
            </li>
            @endif
         </ul>
      </li>
      @endif

      @if(auth()->user()->canany(['Add Training','Training List','Assign Training','Assigned Employee List']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'training') active @endif">
         <a href="#training" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-network-wired"></i><span>Training</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="training" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('Add Training') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'add_training') active @endif">
               <a href="{{ url('hr/training/add_training') }}"><i class="las la-network-wired"></i>Add Training</a>
            </li>
            @endif
            @if($user->can('Assign Training') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'assign_training') active @endif">
               <a href="{{ url('hr/training/assign_training') }}"><i class="las la-users"></i>Assign Training</a>
            </li>
            @endif
            @if($user->can('Training List') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'training_list') active @endif">
               <a href="{{ url('hr/training/training_list') }}"><i class="las la-tasks"></i>Training List</a>
            </li>
            @endif
         </ul>
      </li>
      @endif


      @if(auth()->user()->canany(['Define Shift Roster','Job Card','Attendance Operation','Employee Shift Assign','Shift Assign','Holiday Roster','Yearly Holiday','Payslip','Salary Sheet','Bonus Sheet','Retirement','Earn Leave Payment','Maternity Payment','Station Card','Salary Generate - HR','Salary Audit - Audit','Salary Verify - Accounts','Salary Confirmation - Management']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'operation') active @endif">
         <a href="#operation" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-tools"></i><span>Operation</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="operation" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'attendance-operation') active @endif">
               <a href="{{ url('hr/operation/attendance-operation') }}"><i class="las la-fingerprint"></i> Attendance Operation</a>
            </li>
            @endif
             @if($user->can('Shift Assign') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'shift' || $segment3 == 'shift_update') active @endif">
               <a href="{{ url('hr/operation/shift') }}"><i class="las la-tasks"></i>Add Shift</a>
            </li>
            @endif
            @if($user->can('Employee Shift Assign') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'multiple_emp_shift_assign') active @endif">
               <a href="{{ url('hr/operation/multiple_emp_shift_assign') }}"><i class="las la-tasks"></i> Employee Shift Change</a>
            </li>
            @endif
            @if($user->can('Define Shift Roster') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'shift_roaster_define') active @endif">
               <a href="{{ url('hr/operation/shift_roaster_define') }}"><i class="las la-tasks"></i> Define Shift Roster</a>
            </li>
            @endif
            @if($user->can('Shift Assign') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'shift_assign') active @endif">
               <a href="{{ url('hr/operation/shift_assign') }}"><i class="las la-tasks"></i>Shift Assign</a>
            </li>
            @endif
            
            @if($user->can('Holiday Roster') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'holiday-roster') active @endif">
               <a href="{{ url('hr/operation/holiday-roster') }}"><i class="las la-stream"></i>Holiday Roster</a>
            </li>
            @endif

            @if($user->can('Yearly Holiday') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'yearly-holidays') active @endif">
               <a href="{{ url('hr/operation/yearly-holidays/create') }}"><i class="las la-calendar-day"></i>Holiday Planner</a>
            </li>
            @endif
            @if($user->can('Job Card') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'job_card') active @endif">
               <a href="{{ url('hr/operation/job_card') }}"><i class="las la-id-card"></i>Job Card</a>
            </li>
            @endif
            @if($user->canany(['Salary Generate - HR','Salary Audit - Audit','Salary Verify - Accounts','Salary Confirmation - Management']) || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'salary-generate') active @endif">
               <a href="{{ url('hr/operation/salary-generate') }}"><i class="las la-file-invoice-dollar"></i>Salary Process</a>
            </li>
            @endif
            @if($user->canany(['Salary Generate - HR','Salary Sheet']) || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'salary-sheet') active @endif">
               <a href="{{ url('hr/operation/salary-sheet') }}"><i class="las la-file-invoice-dollar"></i>Salary Disburse</a>
            </li>
            @endif
            @if($user->can('Payslip') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'payslip') active @endif">
               <a href="{{ url('hr/operation/payslip') }}"><i class="las la-address-card"></i>Payslip</a>
            </li>
            @endif

            @if($user->can('Tiffin/Dinner') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'tiffin-dinner') active @endif">
               <a href="{{ url('hr/operation/tiffin-dinner') }}"><i class="las la-address-card"></i>Tiffin/Dinner Bill</a>
            </li>
            @endif
           {{-- @if($user->can('Bonus Sheet') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'bonus-sheet') active @endif">
               <a href="{{ url('hr/operation/bonus-sheet') }}"><i class="las la-file-invoice-dollar"></i>Bonus Sheet</a>
            </li>
            @endif --}}
            {{-- <li class="@if($segment3 == 'earn-leave-payment') active @endif">
               <a href="{{ url('hr/operation/earn-leave-payment') }}"><i class="las la-file-invoice-dollar"></i>Earn Leave Payment</a>
            </li> --}}
            {{-- <li class="@if($segment3 == 'fixed-salary-sheet') active @endif">
               <a href="{{ url('hr/operation/fixed-salary-sheet') }}"><i class="las la-file-invoice-dollar"></i>Fixed Salary Sheet</a>
            </li> --}}
            @if($user->can('Maternity Payment') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'maternity-leave') active @endif">
               <a href="{{ url('hr/operation/maternity-leave') }}"><i class="las la-file-invoice-dollar"></i>Maternity Leave</a>
            </li>
            @endif
            @if($user->can('Partial Salary') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'partial-salary') active @endif">
               <a href="{{ url('hr/operation/partial-salary') }}"><i class="las la-file-invoice-dollar"></i>Partial Salary</a>
            </li>
            @endif
            @if($user->can('Station Card') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'line-change') active @endif">
               <a href="{{ url('hr/operation/line-change') }}"><i class="las la-list-ul"></i>Line Change</a>
            </li>
            @endif
            @if($user->can('Manage Outside') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'location_change') active @endif">
               <a href="{{ url('hr/operation/location_change/entry') }}"><i class="las la-list-ul"></i>Outside Work</a>
            </li>
            @endif
            @if($user->can('Production Bonus') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'production-bonus') active @endif">
               <a href="{{ url('hr/operation/production-bonus') }}"><i class="las la-list-ul"></i>Production Bonus</a>
            </li>
            @endif
         </ul>
      </li>
      @endif


      @if(auth()->user()->canany(['Monthly OT','Monthly MMR','Shift Roster','Monthly Increment','Attendance Summary Report','Fixed Salary Sheet','Manual Attendance Report','Outside List','Attendance Consecutive Report','Event History','Group Attendance','Leave Log', 'Salary Report']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'reports') active @endif">
         <a href="#report" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-file-invoice"></i><span>Reports</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="report" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if(auth()->user()->hasRole('Super Admin'))
            <li class="@if($segment1 == 'mmr-report') active @endif">
               <a href="{{ url('/mmr-report') }}" ><i class="las la-file"></i> MMR Report</a>
            </li> 
            @endif 
            @if($user->can('Attendance Summary Report') || $user->hasRole('Super Admin'))
                  <li class="@if($segment3 == 'attendance_summary_report') active @endif"><a href="{{ url('hr/reports/attendance_summary_report') }}"><i class="las la-fingerprint"></i>Attendance Summary</a></li>
            @endif

            @if($user->can('Attendance Report'))
            <li class="@if($segment3 == 'daily-attendance-activity') active @endif"><a href="{{ url('hr/reports/daily-attendance-activity') }}"><i class="las la-fingerprint"></i>Daily Attendance</a></li>
            <li class="@if($segment3 == 'summary') active @endif"><a href="{{ url('hr/reports/summary') }}"><i class="las la-file-alt"></i>Summary Report</a></li>
            @endif
            @if($user->can('Attendance Consecutive Report') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'attendance-consecutive') active @endif"><a href="{{ url('hr/reports/attendance-consecutive') }}"><i class="las la-fingerprint"></i>Attendance Consecutive</a></li>
            <li class="@if($segment3 == 'warning-notices') active @endif"><a href="{{ url('hr/reports/warning-notices') }}"><i class="las la-fingerprint"></i>Warning Notices</a></li>
            @endif
            @if($user->can('Attendance Summary Report'))
            <li class="@if($segment3 == 'monthly-attendance-activity') active @endif"><a href="{{ url('hr/reports/monthly-attendance-activity') }}"><i class="las la-fingerprint"></i>Monthly Attendance</a></li>
            @endif
            {{-- <li class="@if($segment3 == 'monthly-reports') active @endif"><a href="{{ url('hr/reports/monthly-reports') }}"><i class="las la-chart-area"></i>Monthly Report</a></li> --}}
            {{-- <li><a href="{{ url('hr/reports/group_attendance') }}"><i class="las la-fingerprint"></i>Group Attendance</a></li> --}}
            @if($user->can('Salary Report') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'monthly-salary') active @endif"><a href="{{ url('hr/reports/monthly-salary') }}"><i class="las la-fingerprint"></i>Monthly Salary</a></li>
            @endif
            @if($user->can('Monthly OT') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'monthly-ot-report') active @endif"><a href="{{ url('hr/reports/monthly-ot-report') }}"><i class="las la-fingerprint"></i>Monthly OT</a></li>
            @endif
            @if($user->can('Monthly MMR') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'monthly-mmr-report') active @endif"><a href="{{ url('hr/reports/monthly-mmr-report') }}"><i class="las la-fingerprint"></i>Monthly MMR</a></li>
            @endif
             @if($user->can('Shift Roster') || $user->hasRole('Super Admin'))
               <li class="@if($segment3 == 'shift_roaster') active @endif">
                  <a href="{{ url('hr/reports/shift_roaster') }}"><i class="las la-fingerprint"></i>Shift Roster Summary</a>
               </li>
               <li class="@if($segment3 == 'holiday-roster') active @endif">
                  <a href="{{ url('hr/reports/holiday-roster') }}"><i class="las la-fingerprint"></i>Holiday Roster</a>
               </li>
            @endif
            @if($user->can('Maternity Leave') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'maternity') active @endif"><a href="{{ url('hr/reports/maternity') }}"><i class="las la-chart-area"></i>Maternity Leave</a></li>
            @endif
            @if($user->can('Shift Assign') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'unit-wise-shift') active @endif"><a href="{{ url('hr/reports/unit-wise-shift') }}"><i class="las la-fingerprint"></i>Unit Wise Shift</a></li>
            @endif
            @if($user->can('Manage Employee') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'employee-yearly-activity') active @endif"><a href="{{ url('hr/reports/employee-yearly-activity') }}"><i class="las la-fingerprint"></i>Employee Yearly Activity</a></li>
            @endif
            @if($user->can('Increment Report') || $user->hasRole('Super Admin'))
            {{-- <li><a href="{{ url('') }}"><i class="las la-chart-area"></i>Promotion Report</a></li> --}}
            @endif
            
            @if($user->can('Leave Log') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'leave_log') active @endif"><a href="{{ url('hr/reports/leave_log') }}"><i class="las la-calendar-alt"></i>Yearly Leave Log</a></li>
            @endif
            @if($user->can('Event History') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'event_history') active @endif"><a href="{{ url('hr/reports/event_history') }}"><i class="las la-calendar-alt"></i>Event History</a></li>
            @endif
            @if($user->can('Employee Attendance Excel') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'employee-daily-attendance') active @endif"><a href="{{ url('hr/reports/employee-daily-attendance') }}"><i class="las la-calendar-alt"></i>Employee  Excel</a></li>
            @endif
            @if($user->id == 17)
            <li class="@if($segment3 == 'monthly-salary-sheet') active @endif"><a href="{{ url('hr/reports/monthly-salary-sheet') }}"><i class="las la-calendar-alt"></i>Salary  Excel</a></li>
            @endif
         </ul>
      </li>
      @endif

      @if($user->can('Query') || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'search') active @endif">
         <a href="{{ url('/hr/search') }}" class="iq-waves-effect"><i class="las la-search"></i><span>Query</span></a>
      </li>
      @endif

      @if(auth()->user()->canany(['Manage Role','Manage User','Add User','View User','Role Hierarchy','Assign Permission']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'adminstrator') active @endif">
         <a href="#adminstratior" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-users-cog"></i><span>Adminstrator</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="adminstratior" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('Add User') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'user' && $segment4 == 'create') active @endif">
               <a href="{{ url('/hr/adminstrator/user/create') }}"><i class="las la-user-plus"></i>Add User</a>
            </li>
            @endif
            @if($user->can('View User') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'users') active @endif">
               <a href="{{ url('/hr/adminstrator/users') }}"><i class="las la-users"></i>All User</a>
            </li>
            @endif
            @if($user->can('Assign Permission') || $user->hasRole('Super Admin'))
            <li class="@if($segment4 == 'permission-assign') active @endif">
               <a href="{{ url('/hr/adminstrator/user/permission-assign') }}"><i class="las la-user-shield"></i>Assign Permission</a>
            </li>
            @endif
             @if($user->can('Manage Role') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'role') active @endif">
               <a href="{{ url('/hr/adminstrator/role/create') }}"><i class="las la-shield-alt"></i>Add Role</a>
            </li>
            <li class="@if($segment3 == 'roles') active @endif">
               <a href="{{ url('/hr/adminstrator/roles') }}"><i class="las la-list"></i>All Role</a>
            </li>
            @endif
         </ul>
      </li>
      @endif
      
      @if(auth()->user()->canany(['Library Setup','Salary Structure Setup','Designation Setup','Bonus Type Setup','Attendance Bonus Config','Late Count Setup','Library Setup','Buyer Mode']) || $user->hasRole('Super Admin'))
      <li class="@if($segment2 == 'setup') active @endif">
         <a href="#settings" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-cog"></i><span>Settings</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="settings" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('Library Setup') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'unit' || $segment3 == 'location' || $segment3 == 'floor' || $segment3 == 'line' || $segment3 == 'shift' || $segment3 == 'department' || $segment3 == 'section' || $segment3 == 'subsection') active @endif"><a  href="{{ url('hr/setup/unit') }}"><i class="las la-city"></i>Library Setup</a></li>
            @endif
            {{-- <li><a href="{{ url('') }}"><i class="ri-file-list-fill"></i>Basic Settings</a></li> --}}
            @if($user->can('Salary Structure Setup') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'salary_structure') active @endif"><a  href="{{ url('hr/setup/salary_structure') }}"><i class="las la-coins"></i>Salary Structure</a></li>
            @endif
            <li class="@if($segment3 == 'bonus_type') active @endif"><a  href="{{ url('hr/setup/bonus_type') }}"><i class="las la-gifts"></i>Bonus</a></li>
            <li class="@if($segment3 == 'attendancebonus') active @endif"><a  href="{{ url('hr/setup/attendancebonus') }}"><i class="las la-gifts"></i>Attendance Bonus</a></li>
            @if($user->can('Designation Setup') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'designation') active @endif"><a  href="{{ url('hr/setup/designation') }}"><i class="las la-user-tie"></i>Designation</a></li>
            @endif
            @if($user->can('Education Setup') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'education_title') active @endif"><a  href="{{ url('hr/setup/education_title') }}"><i class="las la-school"></i>Education</a></li>
            @endif
            @if($user->can('Late Count Setup') || $user->hasRole('Super Admin'))
            
            <li class="@if($segment3 == 'late_count_default') active @endif"><a  href="{{ url('hr/setup/late_count_default') }}"><i class="las la-comment-dollar"></i>Late Count Default</a></li>
            <li class="@if($segment3 == 'late_count_customize') active @endif"><a  href="{{ url('hr/setup/late_count_customize') }}"><i class="las la-comment-dollar"></i>Late Count Customize</a></li>
            @endif
            @if($user->can('Bill Setup') || $user->hasRole('Super Admin'))
            
            <li class="@if($segment3 == 'bill-setting') active @endif"><a  href="{{ url('hr/setup/bill-setting') }}"><i class="las la-comment-dollar"></i>Tiffin/Dinner Bill</a></li>
            @endif
            @if($user->can('Loan Setup') || $user->hasRole('Super Admin'))
            <li class="@if($segment3 == 'loan_type') active @endif"><a  href="{{ url('hr/setup/loan_type') }}"><i class="las la-comment-dollar"></i>Loan</a></li>
            @endif
         </ul>
      </li>
      @endif
   </ul>
</nav>
