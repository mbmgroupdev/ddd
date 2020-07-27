@section('nav')
   <nav class="iq-sidebar-menu">
      <ul id="iq-sidebar-toggle" class="iq-menu">
         {{-- <li class="iq-menu-title"><i class="ri-subtract-line"></i><span>Dashboard</span></li> --}}
         <li class="active">
            <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
         </li>
         <li>
            <a href="{{ url('/hr') }}" class="iq-waves-effect"><i class="las la-users"></i><span>HR</span></a>
         </li>                     
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-user-secret"></i><span>Merchandising </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-landmark"></i><span>Commercial</span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-store"></i><span>Inventory</span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-user-tie"></i><span>Attendance Summery </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-file-invoice-dollar"></i><span>Budget Estimation </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-school"></i><span>Training Overview </span></a>
         </li>
         <li>
            <a href="#recruitment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-mail-open-fill"></i><span>ESS</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
            <ul id="recruitment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li><a href="#"><i class="las la-file-alt"></i>Leave Application</a></li>
               <li><a href="#"><i class="las la-file-alt"></i>Outside Request</a></li>
               <li><a href="#"><i class="las la-file-alt"></i>Loan Application</a></li>
               <li><a href="#"><i class="las la-file-alt"></i>Greivence</a></li>
            </ul>
         </li>

         
         
      </ul>
   </nav>
@endsection