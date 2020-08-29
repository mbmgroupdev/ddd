@php $library = request()->segment(3); @endphp
<div class="iq-card">
     <div class="iq-card-body">
       <div class="iq-email-list">
          <button class="btn btn-primary btn-lg btn-block mb-3 font-size-16 p-2" data-target="#compose-email-popup" data-toggle="modal"><i class="ri-send-plane-line mr-2"></i>Library</button>
            <div class="iq-email-ui nav flex-column nav-pills">
                <li class="nav-link {{ $library == 'unit'?'active':'' }}"  >
                    <a href="{{ url('hr/settings/unit') }}"><i class="las la-city"></i>Unit</a>
                </li>
                <li class="nav-link {{ $library == 'location'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/location') }}"><i class="las la-city"></i>Location</a>
                </li>
                <li class="nav-link {{ $library == 'floor'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/floor') }}"><i class="las la-city"></i>Floor</a>
                </li>
                <li class="nav-link {{ $library == 'line'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/line') }}"><i class="las la-city"></i>Line</a>
                </li>
                <li class="nav-link {{ $library == 'shift'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/shift') }}"><i class="las la-city"></i>Shift</a>
                </li>
                <li class="nav-link {{ $library == 'department'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/department') }}"><i class="las la-city"></i>Department</a>
                </li>
                <li class="nav-link {{ $library == 'section'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/section') }}"><i class="las la-city"></i>Section</a>
                </li>
                <li class="nav-link {{ $library == 'subsection'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/subsection') }}"><i class="las la-city"></i>Sub-section</a>
                </li>
            </div>
       </div>
     </div>
  </div>