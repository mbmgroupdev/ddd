@section('nav')
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
         <li class="@if($segment1 == '') active @endif">
            <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
         </li>
         @if(auth()->user()->module_permission('HR'))
         <li>
            <a href="{{ url('/hr') }}" class="iq-waves-effect"><i class="las la-users"></i><span>HR</span></a>
         </li> 
         @endif  
         @if(auth()->user()->module_permission('Merchandising'))
         <li>
            <a href="{{ url('/merchandising') }}" class="iq-waves-effect"><i class="lab la-opencart"></i></i><span>Merchandising</span></a>
         </li> 
         @endif
         @if(auth()->user()->hasRole('Super Admin'))
         {{-- <li class="@if($segment1 == 'mmr-report') active @endif">
            <a href="{{ url('/mmr-report') }}" class="iq-waves-effect"><i class="las la-file"></i><span>MMR Report</span></a>
         </li>  --}}
         @endif                   
         {{-- <li>
            <a href="#" class="iq-waves-effect"><i class="las la-user-secret"></i><span>Merchandising </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-landmark"></i><span>Commercial</span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-store"></i><span>Inventory</span></a>
         </li> --}}
         {{-- <li>
            <a href="#" class="iq-waves-effect"><i class="las la-user-tie"></i><span>Attendance Summery </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-file-invoice-dollar"></i><span>Budget Estimation </span></a>
         </li>
         <li>
            <a href="#" class="iq-waves-effect"><i class="las la-school"></i><span>Training Overview </span></a>
         </li> --}}
         <li class="@if($segment1 == 'ess') active @endif">
            <a href="#recruitment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-user-tie"></i><span>ESS</span><i class="las la-angle-right iq-arrow-right"></i></a>
            <ul id="recruitment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
               <li class="@if( $segment2=='leave_application') active @endif"><a href="{{ url('ess/leave_application') }}"><i class="las la-file-alt"></i>Leave Application</a></li>
               <li class="@if( $segment2=='out_side_request') active @endif"><a href="{{ url('ess/out_side_request/entry') }}"><i class="las la-file-alt"></i>Outside Request</a></li>
               <li class="@if( $segment2=='loan_application') active @endif"><a href="{{ url('ess/loan_application') }}"><i class="las la-file-alt"></i>Loan Application</a></li>
               {{-- <li class="@if( $segment2=='grievance') active @endif"><a href="{{ url('ess/grievance/appeal')}}"><i class="las la-file-alt"></i>Greivence</a></li> --}}
            </ul>
         </li>

         
         
      </ul>
   </nav>
@endsection