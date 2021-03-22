@extends('hr.layout')
@section('title', 'Warning Notice')
@section('main-content')
@push('css')
<style type="text/css">
    .user-action-content{
                                    padding: 10px 20px;
                                    border: 1px solid #000;
                                    border-radius: 5px;
                                    margin-top: 10px;
                                }
</style>
@endpush
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="ace-icon fa fa-home home-icon"></i>
          <a href="#">Human Resource</a>
        </li>
        <li>
          <a href="#">Operation</a>
        </li>
        <li class="active"> Warning Notice</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">   
            <div class="col">
                <form role="form" method="get" action="{{ url('hr/operation/warning-notice') }}" class="noticeReport" id="noticeReport">
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                        <label  for="associate"> Associate's ID </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="month" class="form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                        <label  for="year"> Month </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    <a href="{{url('hr/reports/warning-notices')}}" class="btn btn-success pull-right" >Warning Notice List <i class="fa fa-list bigger-120"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
        </div>
        <div class="">
            @if(isset($info))
            <div class="panel panel-success" style=""> 
                <div class="panel-body">
                    <div class="row">
                        <div class="col-6">
                            {{Form::open(['url'=>'#', 'class'=>'form-horizontal'])}}
                                <div class="iq-card-body" style="border-right: 1px solid #d1d1d1;">
                                    @php
                                        $thirdFlag = 0;
                                        $thirdDone = 0;
                                      
                                        if(isset($notice->start_date)){
                                            $startDate = $notice->start_date??date('Y-m-d', strtotime(Request::get('start_date')));
                                        }elseif(Request::get('start_date')){
                                            $startDate = date('Y-m-d', strtotime(Request::get('start_date')));
                                        }else{
                                            $startDate = date('Y-m-d');
                                        }

                                        if(isset($notice->first_step_date)){
                                            $firstStep = $notice->first_step_date;
                                            $firstResponse = $notice->first_response;
                                            $firstManager = $notice->first_manager;
                                        }else{
                                            $firstStep = date('Y-m-d');
                                            $firstResponse = 10;
                                            $firstManager = null;
                                        }

                                        if(isset($notice->second_step_date) && $notice->second_manager != null){
                                            $thirdFlag = 1;
                                            $secondStep = $notice->second_step_date;
                                            $secondResponse = $notice->second_response;
                                            $secondManager = $notice->second_manager;
                                        }else{
                                            $secondStep = date('Y-m-d');
                                            $secondResponse = 7;
                                            $secondManager = null;
                                        }

                                        if(isset($notice->third_step_date) && $notice->third_manager != null){
                                            $thirdDone = 1;
                                            $thirdStep = $notice->third_step_date;
                                            $thirdManager = $notice->third_manager;
                                        }else{
                                            $thirdStep = date('Y-m-d');
                                            $thirdManager = null;
                                        }
                                    @endphp
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-float-label has-required">
                                                <input type="text" name="reason" id="reason" class="form-control" placeholder="Notice Reason" required="required" value="@if(isset($notice->reason)) {{ $notice->reason }} @else {{ Request::get('days') }} days absent @endif" @if($thirdFlag == 1) readonly @endif>
                                                <label  for="reason"> Notice Reason  </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-float-label has-required ">
                                                <input type="date" name="absent_start_date" id="absent_start_date" class="form-control" placeholder="dd-mm-yyyy" required="required" value="{{ $startDate }}" @if($thirdFlag == 1) readonly @endif />
                                                <label for="absent_start_date"> Absent Start Date </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                   <ul class="iq-timeline">
                                      <li>
                                         <div class="timeline-dots"></div>
                                         <h4 class="float-left mb-1">First Step</h4>
                                         <div class="d-inline-block mt-3 w-100">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group has-float-label has-required ">
                                                        <input type="date" name="first_step_date" id="first_step_date" class="form-control" required="required" placeholder="dd-mm-yyyy" value="{{ $firstStep }}" @if($thirdFlag == 1) readonly @endif />
                                                        <label for="first_step_date"> First issue Date </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group has-float-label has-required ">
                                                        <input type="number" name="first_response" id="first_response" class="form-control" min="0" required="required" value="{{ $firstResponse}}" @if($thirdFlag == 1) readonly @endif />
                                                        <label for="first_response"> Response Day </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group has-float-label has-required select-search-group">
                                                        @if($thirdFlag == 0)
                                                        {{ Form::select('manager', [$firstManager => $firstManager], $firstManager, ['placeholder'=>'Select Manager Associate\'s ID', 'id'=>'manager', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                                        <label  for="manager"> Manager ID </label>
                                                        @else
                                                        <input type="text" id="f_manager" class="form-control" value="{{ $firstManager}}" readonly />
                                                        <label for="f_manager"> Manager ID </label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group ">
                                                        @if($thirdFlag == 1) 
                                                        <button type="button" onClick="stepOnePrint()" class="btn btn-success"><i class="fa fa-save"></i> Generate</button>
                                                        @else
                                                        <button type="button" onClick="stepOne()" class="btn btn-success"><i class="fa fa-save"></i> Generate</button>
                                                        @endif
                                                     </div>
                                                </div>
                                            </div>
                                         </div>
                                         
                                      </li>
                                      <li>
                                         <div class="timeline-dots border-warning"></div>
                                         <h4 class="float-left mb-1">Second Step</h4>
                                         <div class="d-inline-block mt-3 w-100">
                                            <div class="second-step-content @if($notice == null) hide @endif">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group has-float-label has-required ">
                                                            <input type="date" name="second_step_date" id="second_step_date" class="form-control" required="required" placeholder="dd-mm-yyyy" value="{{ $secondStep }}" @if($thirdDone == 1) readonly @endif />
                                                            <label for="second_step_date"> Second issue Date </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group has-float-label has-required ">
                                                            <input type="number" name="second_response" id="second_response" class="form-control" min="0" required="required" value="{{ $secondResponse }}" @if($thirdDone == 1) readonly @endif />
                                                            <label for="second_response"> Response Day </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    
                                                    <div class="col-sm-6">
                                                        <div class="form-group has-float-label has-required select-search-group">
                                                            @if($thirdDone == 0)
                                                            {{ Form::select('second_manager', [$secondManager => $secondManager], $secondManager, ['placeholder'=>'Select Manager Associate\'s ID', 'id'=>'second_manager', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                                            <label  for="second_manager"> Manager ID </label>
                                                            @else
                                                            <input type="text" id="s_manager" class="form-control" value="{{ $secondManager}}" readonly />
                                                            <label for="s_manager"> Manager ID </label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group ">
                                                            @if($thirdDone == 1)
                                                            <button type="button" onClick="stepTwoPrint()" class="btn btn-success"><i class="fa fa-save"></i> Generate</button>
                                                            @else
                                                            <button type="button" onClick="stepTwo()" class="btn btn-success"><i class="fa fa-save"></i> Generate</button>
                                                            @endif
                                                         </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                         </div>
                                      </li>
                                      
                                      <li>
                                         <div class="timeline-dots border-danger"></div>
                                         <h4 class="float-left mb-1">Third Step</h4>
                                         <div class="d-inline-block mt-3 w-100">
                                            <div class="third-step-content @if($thirdFlag == 0) hide @endif">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group has-float-label has-required ">
                                                            <input type="date" name="third_step_date" id="third_step_date" class="form-control" required="required" placeholder="dd-mm-yyyy" value="{{ $thirdStep }}" />
                                                            <label for="third_step_date"> Third issue Date </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    
                                                    <div class="col-sm-6">
                                                        <div class="form-group has-float-label has-required select-search-group">
                                                            {{ Form::select('third_manager', [$thirdManager => $thirdManager], $thirdManager, ['placeholder'=>'Select Manager Associate\'s ID', 'id'=>'third_manager', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                                            <label  for="third_manager"> Manager ID </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group ">
                                                            <button type="button" onClick="stepThree()" class="btn btn-success"><i class="fa fa-save"></i> Generate</button>
                                                         </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                         </div>
                                      </li>
                                      
                                   </ul>
                                   
                                </div>
                                
                            {{Form::close()}}
                        </div>
                        <div class="col-6">
                            <div class=" panel-info" id="basic_info_div">
                                <div class="panel-body">
                                    <div class="row">
                                        
                                        <div class="col">
                                            
                                            <div class="user-details-block" style="padding-top: 0rem;">
                                                <div class="user-profile text-center mt-0">
                                                    <img id="avatar" class="avatar-130 avatar-radius-4 img-fluid" src="{{ emp_profile_picture($info) }}">
                                                </div>
                                                <div class="text-center mt-3">
                                                 <h4><b id="name">{{ $info->as_name }}</b></h4>
                                                 <p class="mb-0" id="joined">Joined {{ $info->as_doj }}</p>
                                                 <p class="mb-0" id="designation">{{ $info->designation['hr_designation_name'] }}</p>
                                                 <p class="mb-0" >
                                                    Oracle ID: <span id="oracle_id" class="text-success">{{ $info->as_oracle_code }}</span>
                                                 </p>
                                                 <p class="mb-0" >
                                                    Associate ID: <span id="associate_id" class="text-success">{{ $info->associate_id }}</span>
                                                 </p>
                                                 <p  class="mb-0">Department: <span id="department" class="text-success">{{ $info->department['hr_department_name'] }}</span> </p>
                                                 
                                                </div>
                                            </div>
                                            <div class="user-action-content1">
                                                <a class="btn btn-outline-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="If you can Left this employee then click button" href='{{ url("hr/payroll/benefits?associate=$info->associate_id")}}' target="_blank"><i class="las la-user-times"></i> Left</a>
                                            </div>  
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="report-section">
            <div class="panel">
                <div class="panel-heading report-print-section hide">
                    <h6 class="">
                        <button type="button" onClick="printDiv('result-data')" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="fa fa-print"></i>
                        </button>
                        <button type="button" onClick="printDiv('result-data-cover',11)" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Envelope"><i class="las la-envelope"></i>
                        </button>
                    </h6>
                </div>
                <div class="panel-body row">
                    <div class=" offset-2 col-sm-8">
                        <div id="result-data">
                            <style type="text/css" media="print">
                                .stepLetter{padding:60pt 36pt }

                            </style>
                            <div class="step_one" id="step_one" style="display: none">
                                <div class="stepLetter" id="letter" style="font-size: 12px;">
                                    <style type="text/css">p{margin: 0;}</style>
                                    <p>
                                    <center><h3 class="underline" style="font-size:16px;">“রেজিস্ট্রি ডাক যোগে প্রেরিত”</h3></center>
                                    <p>তারিখ :&nbsp; <b id="firstIssueDate">@if($notice != null){{ eng_to_bn(date('d-m-Y', strtotime($notice->first_step_date))) }} @endif</b> ইং</p>

                                    <p>বরাবর,</p>
                                    <p>নামঃ {{ $info->hr_bn_associate_name??'' }}</p>
                                    <p>পিতাঃ {{ $info->hr_bn_father_name??'' }} </p>
                                    <p>পদবীঃ {{ $info->designation['hr_designation_name_bn']?$info->designation['hr_designation_name_bn']:'' }}</p>
                                    <p>আইডিঃ {{ $info->associate_id??'' }}</p>
                                    <br>
                                    <div class="address_section" style="display: flex;">
                                        <div class="address_left" style="width:250px;">
                                            <p><b class="underline">বর্তমান ঠিকানাঃ </b></p>
                                            <p>বাসা নংঃ {{ $info->hr_bn_present_house??'' }}</p>
                                            <p>পোষ্টঃ {{ $info->hr_bn_present_po??'' }}</p>
                                            <p>থানাঃ {{ (!empty($info->present_upazilla_bn)?$info->present_upazilla_bn:null) }}</p>
                                            <p>জেলাঃ {{ $info->present_district_bn??'' }}</p>
                                        </div>
                                        <div class="address_right">
                                            <p><b class="underline">স্থায়ী ঠিকানাঃ </b></p>
                                            <p>গ্রামঃ {{ $info->hr_bn_permanent_village??'' }}</p>
                                            <p>পোষ্টঃ {{ $info->hr_bn_permanent_po??'' }}</p>
                                            <p>থানাঃ {{ $info->permanent_upazilla_bn??'' }} </p>
                                            <p>জেলাঃ {{ $info->permanent_district_bn??'' }} </p>
                                        </div>
                                    </div>
                                    <br>
                                    <p><span style="text-decoration: underline;"><strong>বিষয়ঃ বাংলাদেশ শ্রম আইন ২০০৬ এর ২৭(৩ক) ধারা মোতাবেক ব্যাখ্যা প্রদান সহ চাকুরীতে যোগদানের জন্য নোটিশ। </strong></span></p>
                                    <br>
                                    <p>জনাব/জনাবা,</p>
                                    <br>
                                    <p style="text-align: justify;">আপনি গত <b id="firstAbsentDate">@if($notice != null){{ eng_to_bn(date('d-m-Y', strtotime($notice->start_date))) }} @endif</b> ইং তারিখ থেকে কারখানা কর্তৃপক্ষের বিনা অনুমতিতে কর্মস্থলে অনুপস্থিত রয়েছেন। আপনার এরুপ অনুপস্থিতি বাংলাদেশ শ্রম আইন ২০০৬ এর ২৭(৩ক) ধারার আওতায় পড়ে।</p>
                                    <br>
                                    <p>সুতরাং অত্র পত্র প্রাপ্তির <b id="firstResDay">@if($notice != null){{ eng_to_bn($notice->first_response) }} @endif</b> দিনের মধ্যে আপনার অনুপস্থিতির কারন ব্যাখ্যা সহ কাজে যোগদানের জন্য আপনাকে নির্দেশ দেয়া হল।</p>
                                    <p>আপনার লিখিত জবাব উক্ত সময়ের মধ্যে নিম্ন স্বাক্ষরকারীর নিকট অবশ্যই পৌঁছাতে হবে। অন্যথায় কর্তৃপক্ষ আপনার বিরুদ্ধে প্রয়োজনীয় আইনানুগ ব্যবস্থা নিতে বাধ্য হবে।</p>
                                    <br>
                                    <br>
                                    <p>ধন্যবাদান্তে,</p>
                                    <br>
                                    <br>
                                    <br>
                                    <b id="firsrtManagerName"> </b>
                                    <p id="firsrtManagerDeg"></p>
                                    <br>
                                    <p>অনুলিপিঃ </p>
                                    <p><small>১। মাননীয় ব্যবস্থাপনা পরিচালক মহোদয় (সদয় অবগণ)</small></p>
                                    <p><small>২। সহকারী ব্যবস্থাপনা পরিচালক (সদয় অবগতণ)</small></p>
                                    <p><small>৩। উৎপাদক ব্যবস্থাপকগন)</small></p>
                                    <p><small>৪। কারখানার নোটিশ বোর্ড</small></p>
                                    <p><small>৫। ব্যাক্তিগত নথি</small></p>
                                    <p><small>৬। মাস্টার ফাইল</small></p>
                                    <p><small>৭। সিকিউরিটি সুপারভাইজার।</small></p>
                                    </p>
                                </div>
                            </div>
                            <div class="step_two" id="step_two" style="display: none">
                                <div class="stepLetter" id="letter" style="font-size: 12px;">
                                    <p>
                                    <center><h3 class="underline" style="font-size:16px;">“রেজিস্ট্রি ডাক যোগে প্রেরিত”</h3></center>
                                    <p>তারিখ :&nbsp; <b id="secondIssueDate">@if($notice != null){{ eng_to_bn(date('d-m-Y', strtotime($notice->second_step_date))) }} @endif</b> ইং</p>
                                    <p>বরাবর,</p>
                                    <p>নামঃ {{ $info->hr_bn_associate_name??'' }}</p>
                                    <p>পিতাঃ {{ $info->hr_bn_father_name??'' }} </p>
                                    <p>পদবীঃ {{ $info->designation['hr_designation_name_bn']?$info->designation['hr_designation_name_bn']:'' }}</p>
                                    <p>আইডিঃ {{ $info->associate_id??'' }}</p>
                                    <br>
                                    <div class="address_section" style="display: flex;">
                                        <div class="address_left" style="width:250px;">
                                            <p><b class="underline">বর্তমান ঠিকানাঃ </b></p>
                                            <p>বাসা নংঃ {{ $info->hr_bn_present_house??'' }}</p>
                                            <p>পোষ্টঃ {{ $info->hr_bn_present_po??'' }}</p>
                                            <p>থানাঃ {{ (!empty($info->present_upazilla_bn)?$info->present_upazilla_bn:null) }}</p>
                                            <p>জেলাঃ {{ $info->present_district_bn??'' }}</p>
                                        </div>
                                        <div class="address_right">
                                            <p><b class="underline">স্থায়ী ঠিকানাঃ </b></p>
                                            <p>গ্রামঃ {{ $info->hr_bn_permanent_village??'' }}</p>
                                            <p>পোষ্টঃ {{ $info->hr_bn_permanent_po??'' }}</p>
                                            <p>থানাঃ {{ $info->permanent_upazilla_bn??'' }} </p>
                                            <p>জেলাঃ {{ $info->permanent_district_bn??'' }} </p>
                                        </div>
                                    </div>
                                    <br>
                                    <p><span style="text-decoration: underline;"><strong>বিষয়ঃ বাংলাদেশ শ্রম আইন ২০০৬ এর ২৭(৩ক) ধারা মোতাবেক আত্নপক্ষ সমর্থনের সুযোগ প্রদান প্রসঙ্গে। </strong></span></p>
                                    <br>
                                    <p>জনাব/জনাবা,</p>
                                    <br>
                                    
                                    <p style="text-align: justify;">আপনি গত <b id="sfirstAbsentDate">@if($notice != null){{ eng_to_bn(date('d-m-Y', strtotime($notice->start_date))) }} @endif</b> ইং তারিখ থেকে কারখানা কর্তৃপক্ষের বিনা অনুমতিতে কর্মস্থলে অনুপস্থিত রয়েছেন। এ প্রেক্ষিতে কারখানার কর্তৃপক্ষ আপনার স্থায়ী ও বর্তমান ঠিকানায় রেজিস্ট্রি ডাকযোগে গত <b id="sfirstIssueDate">@if($notice != null){{ eng_to_bn(date('d-m-Y', strtotime($notice->first_step_date))) }} @endif</b> ইং তারিখে বিনাঅনুমতিতে চাকুরীতে অনুপস্থিতির কারন ব্যাখ্যা সহ কাজে যোগদানের জন্য পত্র প্রেরন করেছে। কিন্তু অদ্যবধি আপনি উপরোক্ত বিষয়ে কোন ধরনের লিখিত ব্যাখ্যা প্রদান করেন নাই অথবা চাকুরিতেও যোগদান করেন নাই। </p>
                                    <br>
                                    <p>অতএব, অত্র পত্র প্রাপ্তির <b id="secondResDay">@if($notice != null){{ eng_to_bn($notice->second_response) }} @endif</b> দিনের মধ্যে আত্নপক্ষ সমর্থন সহ কাজে যোগদান করিতে আপনাকে নির্দেশ দেয়া গেল।</p>
                                    <p>উক্ত সময়ের মধ্যে আপনি আত্নপক্ষ সমর্থনের জবাব সহ কাজে যোগদান করতে ব্যর্থ হলে বাংলাদেশ শ্রম আইন ২০০৬ এর ২৭(৩ক) ধারা অনুযায়ী আপনি স্বেচ্ছায় চাকুরী থেকে অব্যাহতি গ্রহন করেছেন বলে গণ্য হবে।</p>
                                    <br>
                                    <br>
                                    <p>ধন্যবাদান্তে,</p>
                                    <br>
                                    <br>
                                    <br>
                                    <b id="secondManagerName"> </b>
                                    <p id="secondManagerDeg"></p>
                                    <br>
                                    <p>অনুলিপিঃ </p>
                                    <p><small>১। মাননীয় ব্যবস্থাপনা পরিচালক মহোদয় (সদয় অবগণ),</small></p>
                                    <p><small>২। সহকারী ব্যবস্থাপনা পরিচালক (সদয় অবগতণ),</small></p>
                                    <p><small>৩। উৎপাদক ব্যবস্থাপকগন),</small></p>
                                    <p><small>৪। কারখানার নোটিশ বোর্ড,</small></p>
                                    <p><small>৫। ব্যাক্তিগত নথি,</small></p>
                                    <p><small>৬। মাস্টার ফাইল,</small></p>
                                    <p><small>৭। সিকিউরিটি সুপারভাইজার।</small></p>
                                    </p>
                                </div>
                            </div>
                            <div class="step_three" id="step_three" style="display: none">
                                <div class="stepLetter" id="letter" style="font-size: 12px;">
                                    <p>
                                    <center><h3 class="underline" style="font-size:16px;">“রেজিস্ট্রি ডাক যোগে প্রেরিত”</h3></center>
                                    <p>তারিখ :&nbsp; <b id="thirdIssueDate"></b> ইং</p>
                                    <p>বরাবর,</p>
                                    <p>নামঃ {{ $info->hr_bn_associate_name??'' }}</p>
                                    <p>পিতাঃ {{ $info->hr_bn_father_name??'' }} </p>
                                    <p>পদবীঃ {{ $info->designation['hr_designation_name_bn']?$info->designation['hr_designation_name_bn']:'' }}</p>
                                    <p>আইডিঃ {{ $info->associate_id??'' }}</p>
                                    <br>
                                    <div class="address_section" style="display: flex;">
                                        <div class="address_left" style="width:250px;">
                                            <p><b class="underline">বর্তমান ঠিকানাঃ </b></p>
                                            <p>বাসা নংঃ {{ $info->hr_bn_present_house??'' }}</p>
                                            <p>পোষ্টঃ {{ $info->hr_bn_present_po??'' }}</p>
                                            <p>থানাঃ {{ (!empty($info->present_upazilla_bn)?$info->present_upazilla_bn:null) }}</p>
                                            <p>জেলাঃ {{ $info->present_district_bn??'' }}</p>
                                        </div>
                                        <div class="address_right">
                                            <p><b class="underline">স্থায়ী ঠিকানাঃ </b></p>
                                            <p>গ্রামঃ {{ $info->hr_bn_permanent_village??'' }}</p>
                                            <p>পোষ্টঃ {{ $info->hr_bn_permanent_po??'' }}</p>
                                            <p>থানাঃ {{ $info->permanent_upazilla_bn??'' }} </p>
                                            <p>জেলাঃ {{ $info->permanent_district_bn??'' }} </p>
                                        </div>
                                    </div>
                                    <br>
                                    <p><span style="text-decoration: underline;"><strong>বিষয়ঃ বাংলাদেশ শ্রম আইন ২০০৬ এর ২৭(৩ক) ধারা মোতাবেক শ্রমিক কর্তৃক স্বেচ্ছায় চাকুরী হইতে অব্যাহতি প্রসঙ্গে। </strong></span></p>
                                    <br>
                                    <p>জনাব/জনাবা,</p>
                                    <br>
                                    
                                    
                                    <p style="text-align: justify;">আপনি গত <b id="tfirstAbsentDate"></b> ইং তারিখ হতে অদ্যবদি পর্যন্ত কর্তৃপক্ষের বিনা অনুমতিতে কর্মস্থলে অনুপস্থিত থাকার কারনে আপনাকে গত <b id="tfirstIssueDate"></b> ইং তারিখে পত্রের মাধ্যমে <b id="tfirstResDay"></b> দিনের সময় দিয়ে চাকুরীতে যোগদান সহ ব্যাখ্যা প্রদান করতে বলা হয়েছিল। কিন্তু আপনি নির্ধারিত সময়ের মধ্যে কর্মস্থলে উপস্থিত হননি এবং কোন ব্যাখ্যা প্রদান করেননি। তথাপি কর্তৃপক্ষ গত  <b id="tsecondIssueDate"></b> ইং তারিখে আর একটি পত্রের মাধ্যমে আপনাকে আরো <b id="tsecondResDay"></b> দিনের সময় দিয়ে আত্নপক্ষ সমর্থন সহ চাকুরীতে যোগদানের জন্য পুনরায় নির্দেশ প্রদান করেন।  তৎসত্ত্বেও আপনি নির্ধারিত সময়ের মধ্যে আত্নপক্ষ সমর্থন করেননি এবং চাকুরিতে যোগদান করেননি।</p>
                                    <br>
                                    <p>সুতরাং বাংলাদেশ শ্রম আইন ২০০৬ এর ২৭(৩ক) ধারা অনুযায়ী অনুপস্তিত দিন থেকে আপনি চাকুরী থেকে স্বেচ্ছায় অব্যাহতি গ্রহন করেছেন বলে গণ্য হলো।</p>
                                    <p>অতএব, আপনার বকেয়া মজুরী ও আইনানুগ পাওনা (যদি থাকে) যে কোন কর্মদিবসে অফিস চলাকালীন সময়ে কারখানা হিসাব শাখা থেকে গ্রহন করার নির্দেশ দেয়া গেল।</p>
                                    <br>
                                    <br>
                                    <p>ধন্যবাদান্তে,</p>
                                    <br>
                                    <br>
                                    <br>
                                    <b id="thirdManagerName"></b>
                                    <p id="thirdManagerDeg"></p>
                                    <br>
                                    <p>অনুলিপিঃ </p>
                                    <p><small>১। মাননীয় ব্যবস্থাপনা পরিচালক মহোদয় (সদয় অবগণ),</small></p>
                                    <p><small>২। সহকারী ব্যবস্থাপনা পরিচালক (সদয় অবগতণ),</small></p>
                                    <p><small>৩। উৎপাদক ব্যবস্থাপকগন),</small></p>
                                    <p><small>৪। কারখানার নোটিশ বোর্ড,</small></p>
                                    <p><small>৫। ব্যাক্তিগত নথি,</small></p>
                                    <p><small>৬। মাস্টার ফাইল,</small></p>
                                    <p><small>৭। সিকিউরিটি সুপারভাইজার।</small></p>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="hide">
                            <div class="envelope-content" id="result-data-cover">
                                <div class="address_section" style="display: flex;width:800px;margin:20pt auto;">
                                    <div class="address_left" style="width:340px;padding:30pt;">
                                        <p><b class="underline">প্রেরক, </b><br><br></p>
                                        <p>{{ $unitAddress['hr_unit_name_bn']??'' }} </p>
                                        <p>{{ $unitAddress['hr_unit_address_bn']??'' }} </p>
                                    </div>
                                    <div class="address_right pl-5" style="padding:30pt;border-left: 1px solid #d1d1d1;">
                                        <p><b class="underline">প্রাপক, </b><br><br></p>
                                        <p>নামঃ {{$info->hr_bn_associate_name??'' }}</p>
                                        <p>পিতাঃ {{ $info->hr_bn_father_name??'' }} </p>
                                        <p><b>বর্তমান ঠিকানাঃ</b></p>
                                        <p>বাসা নংঃ {{ $info->hr_bn_present_house??'' }}, {{ $info->hr_bn_present_house??'' }} {{$info->hr_bn_present_po??''}},</p>
                                        <p> ডাকঘরঃ {{ $info->hr_bn_present_po??'' }}</p>
                                        <p>থানাঃ {{ $info->present_upzilla_bn??'' }}, জেলাঃ {{ $info->present_district_bn??'' }} </p>
                                    </div>
                                </div>
                                <div style="page-break-after: always;"></div>
                                <div class="address_section" style="display: flex;width:800px;margin:20pt auto;">
                                    <div class="address_left" style="width:340px;padding:30pt;">
                                        <p><b class="underline">প্রেরক, </b><br><br></p>
                                        <p>{{ $unitAddress['hr_unit_name_bn']??'' }} </p>
                                        <p>{{ $unitAddress['hr_unit_address_bn']??'' }} </p>
                                    </div>
                                    <div class="address_right pl-5" style="padding:30pt;border-left: 1px solid #d1d1d1;">
                                        <p><b class="underline">প্রাপক, </b><br><br></p>
                                        <p>নামঃ {{$info->hr_bn_associate_name??'' }}</p>
                                        <p>পিতাঃ {{ $info->hr_bn_father_name??'' }} </p>
                                        <p><b>স্থায়ী ঠিকানাঃ</b></p>
                                        <p>গ্রামঃ {{ $info->hr_bn_permanent_village??'' }}, {{ $info->hr_bn_permanent_village??'' }},</p>
                                        <p> ডাকঘরঃ {{ $info->hr_bn_permanent_po??'' }}</p>
                                        <p>থানাঃ {{ $info->permanent_upzilla_bn??'' }}, জেলাঃ {{ $info->permanent_district_bn??'' }} </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script type="text/javascript">
    function printDiv(divName, font = 9)
    { 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head><title></title>');
        myWindow.document.write('<style>div,p,td,span,strong,th,b{font-size:'+font+'pt;padding: 0;margin: 0;}p{padding: 0;margin: 0;}@import url(https://fonts.googleapis.com/css?family=Poppins:200,200i,300,400,500,600,700,800,900&amp;display=swap);body {font-family: Poppins,sans-serif;}.table{width: 100%;}a{text-decoration: none;}.table-bordered {border-collapse: collapse;}.table-bordered th,.table-bordered td {border: 1px solid #777 !important;padding:5px;}.no-border td, .no-border th{border:0 !important;vertical-align: top;}.f-16 th,.f-16 td, .f-16 td b{font-size: 16px !important;}.text-center{text-align:center!important;}.text-justy{text-align: justify!important;}.page-break{page-break-after: always;}.mb-2{margin-bottom:10px!important;}</style>');
        myWindow.document.write('</head><body>');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    function stepOne() {
        let issueDate = $('#first_step_date').val();
        let startDate = $('#absent_start_date').val();
        let day = $("#first_response").val();
        let manager = $("#manager").val();
        let reason = $("#reason").val();
        let flag = 0;
        let msg = '';
        if(issueDate === '' || issueDate === undefined ){
            flag = 1;
            msg = "Issue Date is Required!";
        }
        if(startDate === '' || startDate === undefined ){
            flag = 1;
            msg = "Absent Start Date is Required!";
        }
        if(day === '' || day === undefined ){
            flag = 1;
            msg = "Response Day is Required!";
        }
        if(manager === '' || manager === undefined ){
            flag = 1;
            msg = "Please Choose Manager Id!";
        }
        if(reason === '' || reason === undefined ){
            flag = 1;
            msg = "Notice Reason Field Required!";
        }
        if(flag === 0){
            $(".app-loader").show();
            $.ajax({
                type: "POST",
                url: '{{ url("/hr/operation/warning-notice-first") }}',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: {
                    month_year: '{{ Request::get("month_year") }}',
                    associate_id: '{{ $info->associate_id}}',
                    reason: reason,
                    start_date: startDate,
                    first_step_date: issueDate,
                    first_response: day,
                    first_manager: manager
                },
                success: function(response)
                {
                    console.log(response);
                    if(response.type === 'success'){
                        $(".report-print-section").removeClass('hide');
                        $(".second-step-content").removeClass('hide');
                        $("#step_two").hide();
                        $("#step_three").hide();
                        $("#step_one").show();
                        $("#firsrtManagerName").html(response.first_manager.name);
                        $("#firsrtManagerDeg").html(response.first_manager.designation+', '+response.first_manager.department);
                        $("#firstResDay").html(response.first_response);
                        $("#firstAbsentDate").html(response.start_date);
                        $("#firstIssueDate").html(response.issue_date);
                        $('html, body').animate({
                            scrollTop: $(".report-section").offset().top
                        }, 2000);
                    }
                    setTimeout(function(){
                        $(".app-loader").hide();
                    }, 2000);

                    $.notify(response.msg, response.type);
                },
                error: function (reject) {
                    $(".app-loader").hide();
                }
            });
        }else{
            $.notify(msg,'error');
        }
    }
    function stepTwo() {
        let issueDate = $('#second_step_date').val();
        let day = $("#second_response").val();
        let manager = $("#second_manager").val();
        let flag = 0;
        let msg = '';
        if(issueDate === '' || issueDate === undefined ){
            flag = 1;
            msg = "Issue Date is Required!";
        }
        
        if(day === '' || day === undefined ){
            flag = 1;
            msg = "Response Day is Required!";
        }
        if(manager === '' || manager === undefined ){
            flag = 1;
            msg = "Please Choose Manager Id!";
        }
        
        if(flag === 0){
            $(".app-loader").show();
            $.ajax({
                type: "POST",
                url: '{{ url("/hr/operation/warning-notice-second") }}',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: {
                    month_year: '{{ Request::get("month_year") }}',
                    associate_id: '{{ $info->associate_id}}',
                    second_step_date: issueDate,
                    second_response: day,
                    second_manager: manager
                },
                success: function(response)
                {
                    console.log(response);
                    if(response.type === 'success'){
                        $(".report-print-section").removeClass('hide');
                        $(".third-step-content").removeClass('hide');
                        $("#step_two").show();
                        $("#step_three").hide();
                        $("#step_one").hide();
                        $("#secondManagerName").html(response.second_manager.name);
                        $("#secondManagerDeg").html(response.second_manager.designation+', '+response.second_manager.department);
                        $("#secondResDay").html(response.second_response);
                        $("#sfirstAbsentDate").html(response.start_date);
                        $("#sfirstIssueDate").html(response.issue_date);
                        $("#secondIssueDate").html(response.second_issue_date);
                        $('html, body').animate({
                            scrollTop: $(".report-section").offset().top
                        }, 2000);
                    }
                    setTimeout(function(){
                        $(".app-loader").hide();
                    }, 2000);

                    $.notify(response.msg, response.type);
                },
                error: function (reject) {
                    $(".app-loader").hide();
                }
            });
        }else{
            $.notify(msg,'error');
        }
    }
    function stepThree() {
        let issueDate = $('#third_step_date').val();
        let day = $("#third_response").val();
        let manager = $("#third_manager").val();
        let flag = 0;
        let msg = '';
        if(issueDate === '' || issueDate === undefined ){
            flag = 1;
            msg = "Issue Date is Required!";
        }
        
        if(manager === '' || manager === undefined ){
            flag = 1;
            msg = "Please Choose Manager Id!";
        }
        
        if(flag === 0){
            $(".app-loader").show();
            $.ajax({
                type: "POST",
                url: '{{ url("/hr/operation/warning-notice-third") }}',
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: {
                    month_year: '{{ Request::get("month_year") }}',
                    associate_id: '{{ $info->associate_id}}',
                    third_step_date: issueDate,
                    third_manager: manager
                },
                success: function(response)
                {
                    // console.log(response);
                    if(response.type === 'success'){
                        $(".report-print-section").removeClass('hide');
                        $("#step_two").hide();
                        $("#step_three").show();
                        $("#step_one").hide();
                        
                        $("#tfirstAbsentDate").html(response.start_date);
                        $("#tfirstIssueDate").html(response.issue_date);
                        $("#tfirstResDay").html(response.first_response);
                        $("#tsecondIssueDate").html(response.second_issue_date);
                        $("#tsecondResDay").html(response.second_response);
                        $("#thirdIssueDate").html(response.third_issue_date);
                        $("#thirdManagerName").html(response.third_manager.name);
                        $("#thirdManagerDeg").html(response.third_manager.designation+', '+response.third_manager.department);
                        $('html, body').animate({
                            scrollTop: $(".report-section").offset().top
                        }, 2000);
                    }
                    setTimeout(function(){
                        $(".app-loader").hide();
                    }, 2000);

                    $.notify(response.msg, response.type);
                },
                error: function (reject) {
                    $(".app-loader").hide();
                }
            });
        }else{
            $.notify(msg,'error');
        }
    }
    function stepOnePrint() {
        $(".report-print-section").removeClass('hide');
        $("#step_two").hide();
        $("#step_three").hide();
        $("#step_one").show();
        $('html, body').animate({
            scrollTop: $(".report-section").offset().top
        }, 2000);
    }
    function stepTwoPrint() {
        $(".report-print-section").removeClass('hide');
        $("#step_two").show();
        $("#step_three").hide();
        $("#step_one").hide();
        $('html, body').animate({
            scrollTop: $(".report-section").offset().top
        }, 2000);
    }
    $(document).ready(function(){
        //select 2 check
        function formatState (state) {
         //console.log(state.element);
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span><img /> <span></span></span>'
            );

            var targetName = state.text;
            $state.find("span").text(targetName);
            return $state;
        };
        $('select.associates').select2({
            templateSelection:formatState,
            placeholder: 'Select Associate\'s ID',
            ajax: {
                url: '{{ url("hr/associate-search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            var oCode = '';
                            if(item.as_oracle_code !== null){
                                oCode = item.as_oracle_code + ' - ';
                            }
                            return {
                                text: oCode + item.associate_name,
                                id: item.associate_id,
                                name: item.associate_name
                            }
                        })
                    };
              },
              cache: true
            }
        });

    });
</script>
@endpush
@endsection