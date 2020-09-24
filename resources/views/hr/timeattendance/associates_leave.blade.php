<div class="col-sm-5">
    <div class="user-details-block benefit-employee">
        <div class="user-profile text-center mt-0">
            <img id="avatar" class="avatar-130 img-fluid" src="{{ emp_profile_picture($info) }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
        </div>
        <div class="text-center mt-3">
            <h4><b id="user-name">{{$info->as_name}} </b></h4>
            <p class="mb-0" id="designation">
                Associate ID: {{$info->associate_id}} </p>
            <p class="mb-0" id="designation">
                Oracle ID: {{$info->as_oracle_code}} </p>
             
          </div>
    </div>
</div>
        

<div class="col-sm-7">
    <ul class="speciality-list m-0 p-0">
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-calendar-day"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Casual Leave</h6>
              <p class="mb-0">
              	Total:  <span class="text-danger" id="total_earn_leave">10</span class="text-danger"> 
              	Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">{{ (!empty($leaves->casual)?$leaves->casual:0) }}</span > 
              	Remained: <span class="text-success" id="remained_earn_leave">{{ (10-$leaves->casual) }}</span></p>
           </div>
        </li>
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-warning"><i class="las f-18 la-stethoscope"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Sick Leave</h6>
              <p class="mb-0">
              	Total:  <span class="text-danger" id="total_earn_leave">14</span class="text-danger"> 
              	Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">{{ (!empty($leaves->sick)?$leaves->sick:0) }}</span >
              	Remained: <span class="text-success" id="remained_earn_leave">{{ (14-$leaves->sick) }}</span></p>
           </div>
        </li>
        
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-info"><i class="las f-18 la-dollar-sign"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Earned Leave</h6>
              <p class="mb-0">
              	Total:  <span class="text-danger" id="total_earn_leave">{{($earnedLeaves[date('Y')]['remain']+ $earnedLeaves[date('Y')]['enjoyed'])}}</span class="text-danger"> 
              	Enjoyed: <span class="text-warning" id="enjoyed_earn_leave">{{$earnedLeaves[date('Y')]['enjoyed']??0}}</span > 
              	Remained: <span class="text-success" id="remained_earn_leave">{{$earnedLeaves[date('Y')]['remain']}}</span></p>
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

