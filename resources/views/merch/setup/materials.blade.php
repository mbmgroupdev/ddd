@php $materials = request()->segment(3); @endphp
<div class="iq-card">
     <div class="iq-card-body">
       <div class="iq-email-list">
          <button class="btn btn-primary btn-lg btn-block mb-3 font-size-16 p-2" data-target="#compose-email-popup" data-toggle="modal"><i class="ri-send-plane-line mr-2"></i>Materials</button>
            <div class="iq-email-ui nav flex-column nav-pills">
                <li class="nav-link {{ $materials == 'item'?'active':'' }} {{ $materials == 'item_edit.*'?'active':'' }}"  >
                    <a href="{{ url('merch/setup/item') }}"><i class="las la-city"></i>Item</a>
                </li>
                {{-- <li class="nav-link {{ $materials == 'location'?'active':'' }} {{ $materials == 'location_update'?'active':'' }}"  >
                    <a href="{{ url('hr/setup/location') }}"><i class="las la-city"></i>Item Index</a>
                </li> --}}
                <li class="nav-link {{ $materials == 'color'?'active':'' }}"  >
                    <a href="{{ url('merch/setup/color') }}"><i class="las la-city"></i>Color</a>
                </li>
                
            </div>
       </div>
     </div>
  </div>