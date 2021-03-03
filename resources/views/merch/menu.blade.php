
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
         <a href="{{ url('/') }}" class="iq-waves-effect"><i class="las la-home"></i><span>Dashboard</span></a>
      </li>
      <li class="{{ $segment2 == ''?'active':'' }}">
         <a href="{{ url('/merchandising') }}" class="iq-waves-effect"><i class="lab la-opencart"></i><span>Merchandising Dashboard</span></a>
      </li>
      <!-- Recruitment Sub menu start -->
      @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

      <li class="@if($segment2 == 'style') active @endif">
         <a href="#style" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-tshirt"></i></i><span>Style</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="style" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->can('New Style') || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'style' && $segment3=='style_new') active @endif">
               <a  href="{{ url('merch/style/style_new') }}"><i class="las la-folder-plus"></i> New Style</a>
            </li>
            @endif
            @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'style' && $segment3=='style_list') active @endif">
               <a href="{{ url('merch/style/style_list') }}"><i class="las la-list-ol"></i> Style List</a>
            </li>
            @endif
            @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'style' && $segment3=='create_bulk') active @endif">
               <a href="{{ url('merch/style/create_bulk') }}"><i class="las la-folder-plus"></i></i> Style Bulk</a>
            </li>
            @endif
            @if($user->can('Style List' ) || $user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'style' && $segment3=='style_copy_search') active @endif">
               <a href="{{ url('merch/style/style_copy_search') }}"><i class="las la-folder-plus"></i></i> Style Copy</a>
            </li>
            @endif
            
         </ul>
      </li>
      @endif
      <li class="{{ $segment2 == 'style_bom'?'active':'' }}">
         <a href="{{ url('/merch/style_bom') }}" class="iq-waves-effect"><i class="las la-list-ol"></i><span>Style BOM</span></a>
      </li>
      <li class="{{ $segment2 == 'style_costing'?'active':'' }}">
         <a href="{{ url('/merch/style_costing') }}" class="iq-waves-effect"><i class="las la-list-ol"></i><span>Style Costing</span></a>
      </li>
      <li class="{{ $segment2 == 'reservation'?'active':'' }}">
         <a href="{{ url('/merch/reservation/reservation_list') }}" class="iq-waves-effect"><i class="las la-list-ol"></i><span>Reservation</span></a>
      </li>
      @if(auth()->user()->canany([]) || $user->hasRole('Super Admin'))

      <li class="@if($segment2 == 'setup') active @endif">
         <a href="#merch-setup" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-cog"></i><span>Setup</span><i class="las la-angle-right iq-arrow-right"></i></a>
         <ul id="merch-setup" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='sampletype') active @endif">
               <a  href="{{ url('merch/setup/sampletype') }}"><i class="las la-folder-plus"></i> Sample Type</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='buyer_info') active @endif">
               <a  href="{{ url('merch/setup/buyer_info') }}"><i class="las la-folder-plus"></i> Buyer </a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='productsize') active @endif">
               <a  href="{{ url('merch/setup/productsize') }}"><i class="las la-folder-plus"></i> Size Group</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='product_type') active @endif">
               <a  href="{{ url('merch/setup/product_type') }}"><i class="las la-folder-plus"></i> Product Type</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='garments_type') active @endif">
               <a  href="{{ url('merch/setup/garments_type') }}"><i class="las la-folder-plus"></i> Garments Type</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='season') active @endif">
               <a  href="{{ url('merch/setup/season') }}"><i class="las la-folder-plus"></i> Season</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='supplier') active @endif">
               <a  href="{{ url('merch/setup/supplier') }}"><i class="las la-folder-plus"></i> Supplier</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='item') active @endif">
               <a  href="{{ url('merch/setup/item') }}"><i class="las la-folder-plus"></i> Materials Item</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='article') active @endif">
               <a  href="{{ url('merch/setup/article') }}"><i class="las la-folder-plus"></i> Article </a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='operation') active @endif">
               <a  href="{{ url('merch/setup/operation') }}"><i class="las la-folder-plus"></i> Operation</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='spmachine') active @endif">
               <a  href="{{ url('merch/setup/spmachine') }}"><i class="las la-folder-plus"></i> Special Machine</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='wash_category') active @endif">
               <a  href="{{ url('merch/setup/wash_category') }}"><i class="las la-folder-plus"></i> Wash Category</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='wash_type') active @endif">
               <a  href="{{ url('merch/setup/wash_type') }}"><i class="las la-folder-plus"></i> Wash Type</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='tna_library') active @endif">
               <a  href="{{ url('merch/setup/tna_library') }}"><i class="las la-folder-plus"></i> TNA Library</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='tna_template') active @endif">
               <a  href="{{ url('merch/setup/tna_template') }}"><i class="las la-folder-plus"></i> TNA Template</a>
            </li>
            @endif

            @if($user->hasRole('Super Admin'))
            <li class="@if($segment2 == 'setup' && $segment3=='approval') active @endif">
               <a  href="{{ url('merch/setup/approval') }}"><i class="las la-folder-plus"></i> Approval Hierarchy</a>
            </li>
            @endif
            
            
         </ul>
      </li>
      @endif
   </ul>
</nav>
