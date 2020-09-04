@extends('user.layout')
@section('title', 'User Dashboard')
@section('main-content')

   @php $user = auth()->user(); @endphp
   <div class="row">
      <div class="col-lg-4 row m-0 p-0">
         <div class="col-sm-12 mb-3">
            <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-user-profile-block" style="height: 75%;">
               <div class="iq-card-body">
                  <div class="user-details-block">
                     <div class="user-profile text-center">
                        @if($user->employee)
                        <img src='{{ $user->employee['as_pic'] != null?asset($user->employee['as_pic'] ):($user->employee['as_gender'] == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}' class="avatar-130 img-fluid" alt="{{ $user->name }}" onError='this.onerror=null;this.src="{{ ($user->employee['as_gender'] == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}";'>
                        @else
                           <img class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} ">
                         @endif
                     </div>
                     <div class="text-center mt-3">
                        <h4><b>{{ $user->name }}</b></h4>
                        @if($user->employee)
                        <p class="mb-0">
                           {{ $user->employee->designation['hr_designation_name']??''}}</p>
                        <p class="mb-0">Joined {{ $user->employee['as_doj']->diffForHumans() }}</p>
                        @else
                           <p class="mb-0">Joined in ERP {{ $user->created_at->diffForHumans() }}</p>
                        @endif
                     </div>
                     @php $last_login = $user->lastlogin(); @endphp
                     @if($last_login)
                     <ul class="doctoe-sedual d-flex align-items-center justify-content-between p-0 mt-4 mb-0">
                        <li class="text-center">
                           <h6 class="text-primary">Last Logged In </h6>
                           <span>{{$last_login->login_at->diffForHumans() }}</span>
                        </li>
                        <li class="text-center">
                           <h6 class="text-primary">IP Address</h6>
                           <span>{{$last_login->ip_address}}</span>
                        </li>
                     </ul>
                     @endif
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-12">
            <div class="panel iq-card-block iq-card-stretch iq-card-height" style="height: calc(100% - 10px);">
               <div class="panel-heading d-flex justify-content-between">
                     <h6 >My Logs</h6>
               </div>
               <div class="panel-body">
                  <ul class="iq-timeline">
                     @if(count($user->logs) > 0)
                        @foreach($user->logs as $log)
                        <li>
                           <div class="timeline-dots"></div>
                           <h6 class="float-left mb-1">{{$log->log_message??''}}</h6>
                           <small class="float-right mt-1">{{date('d F, Y',strtotime($log->created_at))}}</small>
                           @if($log->log_row_no != 0)
                           <div class="d-inline-block w-100">
                              <p>at row no.  {{$log->log_row_no}} </p>
                           </div>
                           @endif
                        </li>
                        @endforeach
                    @else
                    <center>No logs found </center>
                    @endif
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-8 pl-0">
         <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
            <div class="iq-card-body pb-0">
               <div class="row"> 
                   <div class="col-sm-12">
                    <div class="iq-card">
                        <div class="iq-card-body bg-primary rounded pt-2 pb-2 pr-2">
                           <div class="d-flex align-items-center justify-content-between">
                              <p class="mb-0">MBM Group will release HRM module very soon! Stay connected!</p>
                              <div class="rounded iq-card-icon bg-white">
                                 <img src="{{ asset('assets/images/page-img/37.png') }}" class="img-fluid" alt="icon">
                              </div>
                           </div>
                        </div>
                    </div>
                </div>   
                  <div class="col-lg-7">
                     <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                           <div class="iq-header-title">
                              <h4 class="card-title text-primary border-left-heading">Attendance History</h4>
                           </div>
                        </div>
                        <div class="iq-card-body p-0">
                           <div id="patient-chart-2"></div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-5">

                     <div class="iq-card mb-0">
                        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                           <div class="iq-header-title">
                              <h4 class="card-title text-primary border-left-heading">My Leave</h4>
                           </div>
                        </div>
                        <div class="iq-card-body p-0">
                           <ul class="speciality-list m-0 p-0">
                              @if(count($leaves) >0 )
                                 @foreach($leaves as $key => $lv)
                                <li class="d-flex mb-4 align-items-center">
                                   <div class="user-img img-fluid">
                                    @if($lv->leave_status==1)
                                         <a href="#" class="iq-bg-success">
                                          <i class="las f-18 la-check-circle"></i>
                                       </a>
                                     @else
                                         <a href="#" class="iq-bg-danger">
                                          <i class="las f-18 la-times-circle"></i>
                                       </a>
                                     @endif
                                    
                                    </div>
                                   <div class="media-support-info ml-3">
                                      <h6>{{$lv->leave_type}} Leave</h6>
                                      <p class="mb-0">
                                      @if($lv->leave_from != $lv->leave_to)
                                          {{$lv->leave_from->format('d M, Y')}} - {{$lv->leave_to->format('d M, Y')}}
                                       @else
                                          {{$lv->leave_from->format('d M, Y')}}
                                       @endif
                                       </p>
                                   </div>
                                </li>
                                @endforeach
                              @else
                                 <li class="d-flex mb-4 align-items-center">
                                   <div class="user-img img-fluid">
                                       <a href="#" class="iq-bg-danger">
                                          <i class="las f-18 la-times-circle"></i>
                                       </a>
                                    </div>
                                   <div class="media-support-info ml-3">
                                      <h6>No leave record!</h6>
                                      <p class="mb-0">
                                       ------------
                                       </p>
                                   </div>
                                </li>
                              @endif
                           </ul>
                        </div>
                     </div>
                  </div>
                  <!-- <div class="col-md-6">
                     <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                           <div class="iq-header-title">
                              <h4 class="card-title text-primary">Heart Rate</h4>
                           </div>
                        </div>
                        <div class="iq-card-body p-0">
                           <div class="d-flex align-items-center">
                              <div class="mr-3">
                                 <h4 class="">75 bpm</h4>
                                 <p class="mb-0 text-primary">Health Zone</p>
                              </div>
                              <div class="rounded-circle iq-card-icon iq-bg-primary"><i class="ri-windy-fill"></i></div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between p-0 bg-white">
                           <div class="iq-header-title">
                              <h4 class="card-title text-primary">Water Balance</h4>
                           </div>
                        </div>
                        <div class="iq-card-body p-0">
                           <div class="d-flex align-items-center">
                              <div class="mr-3 text-left">
                                 <p class="mb-0">Drunk</p>
                                 <h4 class="">1250 ml/ 2000 ml</h4>
                              </div>
                              <div class="rounded-circle iq-card-icon iq-bg-primary"><i class="ri-add-fill"></i></div>
                           </div>
                        </div>
                     </div>
                  </div> -->
               </div>
            </div>
         </div>
      </div>
   </div>
   @push('js')
      <script src="{{ asset('assets/js/apexcharts.js') }}"></script>
      <script src="{{ asset('assets/js/core.js') }}"></script>
      
      <script src="{{ asset('assets/js/animated.js') }}"></script>
      
      <!-- Chart Custom JavaScript -->
      <script src="{{ asset('assets/js/chart-custom.js') }}"></script>
   @endpush  
@endsection