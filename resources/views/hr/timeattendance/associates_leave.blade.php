<style>.text-bold{font-weight:bold;font-size:14px;}</style>
@php $unit = unit_by_id() @endphp
{{--  --}}
<div class="col-sm-5">
    <div class="user-details-block benefit-employee">
        <div class="user-profile text-center mt-0">
            <img id="avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($info) }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
        </div>
        <div class="text-center mt-3">
            <h4><b id="user-name">{{$info->as_name}} </b></h4>
            <p class="mb-0" id="designation">
                Associate ID: {{$info->associate_id}} </p>
            <p class="mb-0" >
                Oracle ID: {{$info->as_oracle_code}} </p>
            <p class="mb-0" >
                  Unit: {{$unit[$info->as_unit_id]['hr_unit_name']}} </p>
            <p class="mb-0" >
                  Date of Join: {{$info->as_doj->format('d-m-Y')}} </p>
          </div>
    </div>
<center>
    <div style="margin-top: 20px">
        <button class="btn btn-sm btn-success" onclick="printDiv('DivIdToPrint')">Print</button>
    </div>
</center>
</div>

@php
        $member_join_year = $info->as_doj->format('Y');
        $member_join_month = $info->as_doj->format('m');
        $this_year = \Carbon\Carbon::now()->year;
@endphp

<div class="col-sm-7">
    <ul class="speciality-list m-0 p-0">
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-calendar-day"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Casual Leave</h6>
              <p class="mb-0">
                <span class="text-danger">Total:</span>  <span class="text-bold" id="total_earn_leave">{{$member_join_year == $this_year ? ceil((10/12)*(12-($member_join_month-1))) : '10'}}</span>
                <span class="text-danger">Enjoyed:</span> <span class="text-bold" id="enjoyed_earn_leave">{{ (!empty($leaves->casual)?$leaves->casual:0) }}</span >
                <span class="text-danger">Remained:</span> <span class="text-bold" id="remained_earn_leave">{{ $member_join_year == $this_year ? ceil((10/12)*(12-($member_join_month-1)))-$leaves->casual : (10-$leaves->casual) }}</span></p>
           </div>
        </li>
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-stethoscope"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Sick Leave</h6>
              <p class="mb-0">
                <span class="text-danger">Total: </span>  <span class="text-bold" id="total_earn_leave">{{$member_join_year == $this_year ? ceil((14/12)*(12-($member_join_month-1))) : '14'}}</span class="text-danger">
                <span class="text-danger">Enjoyed: </span> <span class="text-bold" id="enjoyed_earn_leave">{{ (!empty($leaves->sick)?$leaves->sick:0) }}</span >
                <span class="text-danger">Remained: </span> <span class="text-bold" id="remained_earn_leave">{{ $member_join_year == $this_year ? ceil((14/12)*(12-($member_join_month-1)))-$leaves->sick : (14-$leaves->sick) }}</span></p>
           </div>
        </li>

        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-info"><i class="las f-18 la-dollar-sign"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Earned Leave</h6>
              <p class="mb-0">
                <span class="text-danger">Total: </span>  <span class="text-bold" id="total_earn_leave">{{($earnedLeaves[date('Y')]['remain']+ $earnedLeaves[date('Y')]['enjoyed'])}}</span class="text-danger">
                <span class="text-danger">Enjoyed: </span> <span class="text-bold" id="enjoyed_earn_leave">{{$earnedLeaves[date('Y')]['enjoyed']??0}}</span >
                <span class="text-danger">Remained: </span> <span class="text-bold" id="remained_earn_leave">{{$earnedLeaves[date('Y')]['remain']}}</span></p>
           </div>
        </li>
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-gift"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Special Leave</h6>
              <p class="mb-0">{{ (!empty($leaves->special)?$leaves->special:0) }}</p>
           </div>
        </li>
     </ul>
</div>

@include('hr/timeattendance/leave_application_form')
