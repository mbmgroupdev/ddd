@section('nav')
   <nav class="iq-sidebar-menu">
      <ul id="iq-sidebar-toggle" class="iq-menu">
         <li>
            <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
         </li>
         <li class="active">
            <a href="{{ url('/hr') }}" class="iq-waves-effect"><i class="las la-users"></i><span>HR Dashboard</span></a>
         </li>
         <li>
            <a href="#recruitment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-user-tie"></i><span>Recruitment</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="recruitment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="{{ url('/hr/recruitment/recruit/create') }}"><i class="ri-inbox-fill"></i>New Recruit</a></li>
               <li><a href="#"><i class="ri-edit-2-fill"></i>Recruit List</a></li>
               <li><a href="#"><i class="ri-edit-2-fill"></i>Job Application</a></li>
               <li><a href="#"><i class="ri-edit-2-fill"></i>Appointment Letter</a></li>
               <li><a href="#"><i class="ri-edit-2-fill"></i>Nominee</a></li>
               <li><a href="#"><i class="ri-edit-2-fill"></i>Background Verification</a></li>
            </ul>
         </li>
         
         <li>
            <a href="#employee" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Employee</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="employee" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>All Employee</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i> Benefits</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Print File Tag</a></li>
            </ul>
         </li>
         <li>
            <a href="#timeattendance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Time & Attendance</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="timeattendance" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Attendance Upload</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i> Define Shift Roster</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Shift Assign</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Roster Assign</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Holiday Roster</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Holiday Planner</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Leave</a></li>
            </ul>
         </li>
         <li>
            <a href="#payrroll" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Payroll</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="payrroll" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Promotion</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Increment</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Salary Adjustment</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>End Of Job Benefits</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Loan</a></li>
            </ul>
         </li>
         <li>
            <a href="#performance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Performance</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="performance" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Performance Appraisal</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Disciplinary Record</a></li>
            </ul>
         </li>
         <li>
            <a href="#training" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Training</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="training" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Add Training</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Assign Training</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Training List</a></li>
            </ul>
         </li>
         <li>
            <a href="#operation" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Operation</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="operation" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Job Card</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Salary Sheet</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Payslip</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Bonus Sheet</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Earn Leave Payment</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Fixed Salary Sheet</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Maternity Payment</a></li>
            </ul>
         </li>
         <li>
            <a href="#report" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Report</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="report" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Attendance Report</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Increment Report</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Promotion Report</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Outside List</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Attendance Summery</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Event History</a></li>
            </ul>
         </li>
         <li>
            <a href="#adminstration" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Adminstration</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="adminstration" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Add User</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>All User</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Add Role</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>All Role</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Assign Permission</a></li>
            </ul>
         </li>
         <li>
            <a href="{{ url('/hr') }}" class="iq-waves-effect"><i class="las la-search"></i><span>Query</span></a>
         </li>
         <li>
            <a href="#settings" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-user-3-fill"></i><span>Settings</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="settings" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="ri-file-list-fill"></i>Basic Settings</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Salary Structure</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Bonus</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Attendance Bonus</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Unit</a></li>
               <li><a href="#"><i class="ri-user-add-fill"></i>Location</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Floor</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Line</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Shift</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Department</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Section</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Sub Section</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Designation</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Education</a></li>
               <li><a href="#"><i class="ri-profile-fill"></i>Loan</a></li>
            </ul>
         </li>
      </ul>
   </nav>
@endsection