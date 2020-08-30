@section('nav')
{{-- define segments --}}
@php
   $segment1 = request()->segment(1);
   $segment2 = request()->segment(2);
   $segment3 = request()->segment(3);
   $segment4 = request()->segment(4);
@endphp

   <nav class="iq-sidebar-menu">
      <ul id="iq-sidebar-toggle" class="iq-menu">
         <li>
            <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
         </li>
         <li class="{{ $segment2 == ''?'active':'' }}">
            <a href="{{ url('/hr') }}" class="iq-waves-effect"><i class="las la-users"></i><span>HR Dashboard</span></a>
         </li>
         <li class="@if($segment2 == 'recruitment') active @endif">
            <a href="#recruitment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-user-plus"></i></i><span>Recruitment</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="recruitment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment2 == 'recruitment' && $segment4=='create') active @endif">
                  <a  href="{{ url('/hr/recruitment/recruit/create') }}"><i class="las la-user-plus"></i> New Recruit</a>
               </li>
               <li class="@if($segment3 == 'recruit' && $segment4=='') active @endif">
                  <a href="{{ url('/hr/recruitment/recruit') }}"><i class="las la-list-ol"></i>Recruit List</a>
               </li>
               <li class="@if( $segment3=='job-application') active @endif">
                  <a href="{{url('hr/recruitment/job-application')}}"><i class="las la-file-contract"></i>Job Application</a>
               </li>
               <li class="@if( $segment3=='appointment-letter') active @endif">
                  <a href="{{ url('hr/recruitment/appointment-letter') }}"><i class="las la-file-contract"></i>Appointment Letter</a>
               </li>
               <li class="@if($segment3 == 'nominee') active @endif">
                  <a href="{{ url('hr/recruitment/nominee') }}"><i class="las la-address-card"></i>Nominee</a>
               </li>
               <li class="@if($segment2 == 'recruitment' && $segment3=='background-verification') active @endif">
                  <a href="{{ url('hr/recruitment/background-verification') }}"><i class="las la-user-check"></i>Background Verification</a>
               </li>
            </ul>
         </li>
         
         <li class="@if($segment2 == 'employee') active @endif">
            <a href="#employee" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-user-tie"></i><span>Employee</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="employee" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment2 == 'employee' && $segment3=='list') active @endif">
                  <a href="{{ url('hr/employee/list') }}"><i class="las la-list-ul"></i>All Employee</a>
               </li>
               <li class="@if($segment2 == 'employee' && $segment3=='benefits') active @endif">
                  <a href="{{ url('hr/employee/benefits') }}"><i class="las la-gifts"></i> Benefits</a>
               </li>
               <li class="@if($segment2 == 'employee' && $segment3=='file_tag') active @endif">
                  <a href="{{ url('hr/employee/file_tag') }}"><i class="las la-folder-plus"></i>Print File Tag</a>
               </li>
            </ul>
         </li>
         <li class="@if($segment2 == 'timeattendance') active @endif">
            <a href="#timeattendance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-fingerprint"></i><span>Time & Attendance</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="timeattendance" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment3 == 'attendance-upload') active @endif">
                  <a href="{{ url('hr/timeattendance/attendance-upload') }}"><i class="las la-fingerprint"></i>Attendance Upload</a>
               </li>
               <li class="@if($segment3 == 'leave-entry') active @endif">
                  <a href="{{ url('hr/timeattendance/leave-entry') }}"><i class="las la-file-alt"></i>Employee Leave</a>
               </li>
            </ul>
         </li>
         <li class="@if($segment2 == 'payroll') active @endif">
            <a href="#payroll" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-money-check-alt"></i><span>Payroll</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="payroll" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment3 == 'promotion') active @endif">
                  <a href="{{ url('hr/payroll/promotion') }}"><i class="las la-chart-line"></i>Promotion</a>
               </li>
               <li class="@if($segment3 == 'increment') active @endif">
                  <a href="{{ url('hr/payroll/increment') }}"><i class="las la-chart-area"></i>Increment</a>
               </li>
               <li class="@if($segment3 == 'salary-adjustment') active @endif">
                  <a href="{{ url('hr/payroll/salary-adjustment') }}"><i class="las la-funnel-dollar"></i>Salary Adjustment</a>
               </li>
               <li class="@if($segment3 == 'benefits') active @endif">
                  <a href="{{ url('hr/payroll/benefits') }}"><i class="las la-gift"></i>End Of Job Benefits</a>
               </li>
               <li><a href="{{ url('hr/ess/loan_list') }}"><i class="las la-dollar-sign"></i>Loan</a></li>
            </ul>
         </li>
         <li class="@if($segment2 == 'performance') active @endif">
            <a href="#performance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-award"></i><span>Performance</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="performance" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment3 == 'appraisal') active @endif">
                  <a href="{{ url('hr/performance/appraisal') }}"><i class="las la-award"></i>Performance Appraisal</a>
               </li>
               <li class="@if($segment3 == 'disciplinary-record') active @endif">
                  <a href="{{ url('hr/performance/disciplinary-record') }}"><i class="las la-question-circle"></i>Disciplinary Record</a>
               </li>
            </ul>
         </li>
         <li class="@if($segment2 == 'training') active @endif">
            <a href="#training" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-network-wired"></i><span>Training</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="training" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment3 == 'add_training') active @endif">
                  <a href="{{ url('hr/training/add_training') }}"><i class="las la-network-wired"></i>Add Training</a>
               </li>
               <li class="@if($segment3 == 'assign_training') active @endif">
                  <a href="{{ url('hr/training/assign_training') }}"><i class="las la-users"></i>Assign Training</a>
               </li>
               <li class="@if($segment3 == 'training_list') active @endif">
                  <a href="{{ url('hr/training/training_list') }}"><i class="las la-tasks"></i>Training List</a>
               </li>
            </ul>
         </li>
         <li class="@if($segment2 == 'operation') active @endif">
            <a href="#operation" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-tools"></i><span>Operation</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="operation" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment3 == 'attendance-operation') active @endif">
                  <a href="{{ url('hr/operation/attendance-operation') }}"><i class="las la-fingerprint"></i> Attendance Operation</a>
               </li>
               <li class="@if($segment3 == 'multiple_emp_shift_assign') active @endif">
                  <a href="{{ url('hr/operation/multiple_emp_shift_assign') }}"><i class="las la-tasks"></i> Employee Shift Change</a>
               </li>
               <li class="@if($segment3 == 'shift_roaster_define') active @endif">
                  <a href="{{ url('hr/operation/shift_roaster_define') }}"><i class="las la-tasks"></i> Define Shift Roster</a>
               </li>
               <li class="@if($segment3 == 'shift_assign') active @endif">
                  <a href="{{ url('hr/operation/shift_assign') }}"><i class="las la-tasks"></i>Shift Roster Assign</a>
               </li>
               
               <li class="@if($segment3 == 'holiday-roster') active @endif">
                  <a href="{{ url('hr/operation/holiday-roster') }}"><i class="las la-stream"></i>Holiday Roster</a>
               </li>
               <li class="@if($segment3 == 'yearly-holidays') active @endif">
                  <a href="{{ url('hr/operation/yearly-holidays/create') }}"><i class="las la-calendar-day"></i>Holiday Planner</a>
               </li>
               <li class="@if($segment3 == 'job_card') active @endif">
                  <a href="{{ url('hr/operation/job_card') }}"><i class="las la-id-card"></i>Job Card</a>
               </li>
               <li class="@if($segment3 == 'salary-sheet') active @endif">
                  <a href="{{ url('hr/operation/salary-sheet') }}"><i class="las la-file-invoice-dollar"></i>Salary Sheet</a>
               </li>
               <li class="@if($segment3 == 'payslip') active @endif">
                  <a href="{{ url('hr/operation/payslip') }}"><i class="las la-address-card"></i>Payslip</a>
               </li>
               <li class="@if($segment3 == 'bonus-sheet') active @endif">
                  <a href="{{ url('hr/operation/bonus-sheet') }}"><i class="las la-file-invoice-dollar"></i>Bonus Sheet</a>
               </li>
               {{-- <li class="@if($segment3 == 'earn-leave-payment') active @endif">
                  <a href="{{ url('hr/operation/earn-leave-payment') }}"><i class="las la-file-invoice-dollar"></i>Earn Leave Payment</a>
               </li> --}}
               {{-- <li class="@if($segment3 == 'fixed-salary-sheet') active @endif">
                  <a href="{{ url('hr/operation/fixed-salary-sheet') }}"><i class="las la-file-invoice-dollar"></i>Fixed Salary Sheet</a>
               </li> --}}
               {{-- <li class="@if($segment3 == 'maternity-leave-payment') active @endif">
                  <a href="{{ url('hr/operation/maternity-leave-payment') }}"><i class="las la-file-invoice-dollar"></i>Maternity Payment</a>
               </li> --}}
               <li class="@if($segment3 == 'new_card') active @endif">
                  <a href="{{ url('hr/timeattendance/new_card') }}"><i class="las la-list-ul"></i>Line Change</a>
               </li>
               <li class="@if($segment3 == 'location_change') active @endif">
                  <a href="{{ url('hr/operation/location_change/list') }}"><i class="las la-list-ul"></i>Outside Work</a>
               </li>
               {{-- <li class="@if($segment4 == 'idcard') active @endif">
                  <a href="{{ url('hr/recruitment/employee/idcard') }}"><i class="las la-list-ul"></i>ID Card</a>
               </li> --}}
            </ul>
         </li>
         <li>
            <a href="#report" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-file-invoice"></i><span>Reports</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="report" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="{{ url('hr/timeattendance/shift_roaster') }}"><i class="las la-fingerprint"></i>Shift Roster Summary</a></li>
               <li><a href="{{ url('hr/reports/attendance_summary_report') }}"><i class="las la-fingerprint"></i>Attendance Summary</a></li>
               
               <li><a href="{{ url('hr/reports/daily-attendance-activity') }}"><i class="las la-fingerprint"></i>Daily Attendance</a></li>
               <li><a href="{{ url('hr/operation/absent_present_list') }}"><i class="las la-fingerprint"></i>Attendance Consecutive</a></li>
               {{-- <li><a href="{{ url('hr/reports/group_attendance') }}"><i class="las la-fingerprint"></i>Group Attendance</a></li> --}}
               <li><a href="{{ url('hr/reports/monthly-salary') }}"><i class="las la-fingerprint"></i>Monthly Salary</a></li>
               <li><a href="{{ url('hr/reports/employee-yearly-activity') }}"><i class="las la-fingerprint"></i>Employee Yearly Activity</a></li>
               <li><a href="{{ url('hr/reports/monthy_increment') }}"><i class="las la-chart-area"></i>Increment Report</a></li>
               <li><a href="{{ url('') }}"><i class="las la-chart-area"></i>Promotion Report</a></li>
               
               
               <li><a href="{{ url('hr/reports/leave_log') }}"><i class="las la-calendar-alt"></i>Yearly Leave Log</a></li>
               <li><a href="{{ url('hr/reports/event_history') }}"><i class="las la-calendar-alt"></i>Event History</a></li>
            </ul>
         </li>
         <li class="@if($segment2 == 'search') active @endif">
            <a href="{{ url('/hr/search') }}" class="iq-waves-effect"><i class="las la-search"></i><span>Query</span></a>
         </li>
         <li class="@if($segment2 == 'adminstrator') active @endif">
            <a href="#adminstratior" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-users-cog"></i><span>Adminstrator</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="adminstratior" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment3 == 'user' && $segment4 == 'create') active @endif">
                  <a href="{{ url('/hr/adminstrator/user/create') }}"><i class="las la-user-plus"></i>Add User</a>
               </li>
               <li class="@if($segment3 == 'users') active @endif">
                  <a href="{{ url('/hr/adminstrator/users') }}"><i class="las la-users"></i>All User</a>
               </li>
               <li class="@if($segment4 == 'permission-assign') active @endif">
                  <a href="{{ url('/hr/adminstrator/user/permission-assign') }}"><i class="las la-user-shield"></i>Assign Permission</a>
               </li>
               <li class="@if($segment3 == 'role') active @endif">
                  <a href="{{ url('/hr/adminstrator/role/create') }}"><i class="las la-shield-alt"></i>Add Role</a>
               </li>
               <li class="@if($segment3 == 'roles') active @endif">
                  <a href="{{ url('/hr/adminstrator/roles') }}"><i class="las la-list"></i>All Role</a>
               </li>
            </ul>
         </li>
         
         <li class="@if($segment2 == 'setup') active @endif">
            <a href="#settings" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-cog"></i><span>Settings</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="settings" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if($segment3 == 'unit' || $segment3 == 'location' || $segment3 == 'floor' || $segment3 == 'line' || $segment3 == 'shift' || $segment3 == 'department' || $segment3 == 'section' || $segment3 == 'subsection') active @endif"><a  href="{{ url('hr/setup/unit') }}"><i class="las la-city"></i>Library Setup</a></li>
               {{-- <li><a href="{{ url('') }}"><i class="ri-file-list-fill"></i>Basic Settings</a></li> --}}
               <li class="@if($segment3 == 'salary_structure') active @endif"><a  href="{{ url('hr/setup/salary_structure') }}"><i class="las la-coins"></i>Salary Structure</a></li>
               <li class="@if($segment3 == 'bonus_type') active @endif"><a  href="{{ url('hr/setup/bonus_type') }}"><i class="las la-gifts"></i>Bonus</a></li>
               <li class="@if($segment3 == 'attendancebonus') active @endif"><a  href="{{ url('hr/setup/attendancebonus') }}"><i class="las la-gifts"></i>Attendance Bonus</a></li>
               <li class="@if($segment3 == 'designation') active @endif"><a  href="{{ url('hr/setup/designation') }}"><i class="las la-user-tie"></i>Designation</a></li>
               <li class="@if($segment3 == 'education_title') active @endif"><a  href="{{ url('hr/setup/education_title') }}"><i class="las la-school"></i>Education</a></li>
               <li class="@if($segment3 == 'loan_type') active @endif"><a  href="{{ url('hr/setup/loan_type') }}"><i class="las la-comment-dollar"></i>Loan</a></li>
            </ul>
         </li>
      </ul>
   </nav>
@endsection