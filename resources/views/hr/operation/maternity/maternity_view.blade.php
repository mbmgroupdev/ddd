@extends('hr.layout')
@section('title', 'Maternity Leave Application')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Operation </a>
                </li>
                <li>
                    <a href="#"> Maternity Leave </a>
                </li>
                <li class="active">Application </li>
            </ul>
        </div>

        
        @include('inc/message')
        <div class="panel panel-success" style="">
            <div class="panel-heading page-headline-bar">
                <h6>
                    Leave Application Tracking
                    <a href="{{url('hr/operation/maternity-leave/list')}}" target="_blank" class="btn btn-primary pull-right" >List <i class="fa fa-list bigger-120"></i></a>
                </h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    
                    <div class="col-sm-3">        
                        <div class="user-details-block" style="padding-top: 0.5rem;">
                            <div class="user-profile text-center mt-0">
                                <img id="avatar" class="avatar-130 img-fluid" src="{{ $employee->as_pic }} " >
                            </div>
                            <div class="text-center mt-3">
                                <h4><b id="name">{{ $employee->as_name }}</b></h4>
                                <p class="mb-0" id="designation">
                                {{ $employee->hr_designation_name }}, {{$employee->hr_department_name}}</p>
                                <p class="mb-0" id="designation">
                                {{$employee->hr_unit_name}}</p>
                            </div>
                             <table style="width: 100%;" border="0">
                                 <tr>
                                     <td><i class="field-title">Oracle ID</i></td>
                                     <td class="field-data">: {{ $employee->as_oracle_code }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Associate ID</i></td>
                                     <td class="field-data">: {{ $employee->associate_id }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Name</i></td>
                                     <td class="field-data">: {{ $leave->husband_name }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Occupation</i></td>
                                     <td class="field-data">: {{ $leave->husband_occupasion }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Age</i></td>
                                     <td class="field-data">: {{ $leave->husband_age }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Total Child</i> <span class="field-data">: {{ ($leave->no_of_son + $leave->no_of_daughter) }}</span></td>
                                     <td><i class="field-title">Last Child Age</i> <span class="field-data">: {{ $leave->last_child_age }} </span></td>
                                 </tr>
                             </table>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="iq-accordion career-style track-style">
                            <div class="iq-accordion-block accordion-active">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div @if($tabs['initial_checkup']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-stethoscope f-18"></i></div>  
                                        <div class="media-support-info ml-3">
                                          <h6>Initial Checkup </h6>
                                          <p id="line" class="mb-0">
                                              @if($tabs['initial_checkup']) {{$leave->medical->created_at->format('Y-m-d')}} @else No action yet! @endif
                                          </p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    
                               </div>
                            </div>

                            <div class="iq-accordion-block ">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div @if($tabs['routine_checkup']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-diagnoses f-18"></i></div> 
                                        <div class="media-support-info ml-3">
                                          <h6>Routine Checkup </h6>
                                          <p id="line" class="mb-0">
                                              @if($tabs['routine_checkup']) 
                                                {{$leave->medical->record->last()->checkup_date}}
                                              @else
                                              @endif
                                          </p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    <table style="width:100%; text-align: center;" border="1">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">
                                                    তারিখ
                                                </th>
                                                <th rowspan="2">
                                                    ওজন
                                                </th>
                                                <th rowspan="2">
                                                    বিপি
                                                </th>
                                                <th rowspan="2">
                                                    ইডিমা
                                                </th>
                                                <th rowspan="2">
                                                    হিমোগ্লোবিন <br>
                                                    % রক্তস্বল্পতা
                                                </th>
                                                <th rowspan="2">
                                                    জন্ডিস
                                                </th>
                                                <th rowspan="2">
                                                    জরায়ুর উচ্চতা
                                                </th>
                                                <th colspan="3">
                                                    গর্ভস্থ শিশুর
                                                </th>
                                                <th colspan="2">
                                                    প্রস্রাব পরীক্ষা
                                                </th>
                                                <th rowspan="2">
                                                    অন্যান্য
                                                </th>
                                                <th rowspan="2">
                                                    মন্ত্যব্য
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>অবস্থান</th>
                                                <th>নড়াচরা</th>
                                                <th>হৃদ-স্পন্দন</th>
                                                <th>আল্বুমিন</th>
                                                <th>সুগার</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if($tabs['routine_checkup']) 
                                        
                                            @foreach($leave->medical->record as $key => $record)
                                                <tr>
                                                    <td>{{$record->checkup_date}}</td>
                                                    <td>{{$record->weight}}</td>
                                                    <td>{{$record->bp}}</td>
                                                    <td>{{$record->edema}}</td>
                                                    <td></td>
                                                    <td>{{$record->jaundice}}</td>
                                                    <td>{{$record->uterus_height}}</td>
                                                    <td>{{$record->baby_position}}</td>
                                                    <td>{{$record->baby_movement}}</td>
                                                    <td>{{$record->albumine}}</td>
                                                    <td>{{$record->sugar}}</td>
                                                    <td>{{$record->others}}</td>
                                                    <td>{{$record->comment}}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="14">No medical record found!</td></tr>
                                        @endif
                                        </tbody>
                                    </table>
                               </div>
                            </div>

                            <div class="iq-accordion-block ">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div @if($tabs['doctors_clearence']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-notes-medical f-18"></i></div> 
                                        <div class="media-support-info ml-3">
                                          <h6>Doctor Clearence </h6>
                                          <p id="line" class="mb-0"></p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    @if($tabs['doctors_clearence'])
                                        @include('hr.operation.maternity.doctor_leave_suggestion')
                                    @else
                                        No information
                                    @endif
                               </div>
                            </div>
                            <div class="iq-accordion-block ">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div @if($tabs['leave_approval']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-user-check f-18"></i></div> 
                                        <div class="media-support-info ml-3">
                                          <h6>Leave Approval </h6>
                                          <p id="line" class="mb-0"></p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    
                               </div>
                            </div>
                            <div class="iq-accordion-block ">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div @if($tabs['reports']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-file-invoice f-18"></i></div> 
                                        <div class="media-support-info ml-3">
                                          <h6>Reports </h6>
                                          <p id="line" class="mb-0"></p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    hi
                               </div>
                            </div>
                            <div class="iq-accordion-block ">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div @if($tabs['verification']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-money-check-alt f-18"></i></div> 
                                        <div class="media-support-info ml-3">
                                          <h6>Verification </h6>
                                          <p id="line" class="mb-0"></p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    hi
                               </div>
                            </div>
                            <div class="iq-accordion-block ">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div @if($tabs['payment']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-file-invoice-dollar f-18"></i></div> 
                                        <div class="media-support-info ml-3">
                                          <h6>Payment </h6>
                                          <p id="line" class="mb-0"></p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    
                               </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection