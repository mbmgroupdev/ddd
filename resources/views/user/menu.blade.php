@section('nav')
   <nav class="iq-sidebar-menu">
      <ul id="iq-sidebar-toggle" class="iq-menu">
         {{-- <li class="iq-menu-title"><i class="ri-subtract-line"></i><span>Dashboard</span></li> --}}
         <li class="active">
            <a href="{{ url('/') }}" class="iq-waves-effect"><i class="ri-home-8-fill"></i><span>Dashboard</span></a>
         </li>
         <li>
            <a href="{{ url('/hr') }}" class="iq-waves-effect"><i class="ri-hospital-fill"></i><span>HR</span></a>
         </li>                     
         <li>
            <a href="#" class="iq-waves-effect"><i class="ri-home-8-fill"></i><span>Merchandising </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="ri-briefcase-4-fill"></i><span>Commercial</span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="ri-briefcase-4-fill"></i><span>Inventory</span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="ri-briefcase-4-fill"></i><span>Attendance Summery </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="ri-briefcase-4-fill"></i><span>Budget Estimation </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="ri-briefcase-4-fill"></i><span>Training Overview </span></a>
         </li>
         
         
      </ul>
   </nav>
@endsection