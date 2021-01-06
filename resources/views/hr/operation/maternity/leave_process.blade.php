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
                <li class="active">Process </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/operation/maternity-leave/list')}}" target="_blank" class="btn btn-primary pull-right" >List <i class="fa fa-list bigger-120"></i></a>
                </li>
            </ul>
        </div>

        @include('inc/message')
        <div class="panel panel-success" style="">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3" style="border-right:1px solid #d1d1d1;">        
                        @include('hr.common.maternity-leave-card')
                    </div>
                    <div class="col-sm-9" >
                        <div class="leave- d-flex justify-content-center">
                            <div class="step d-flex mr-3">
                                <div class="rounded-div @if($tabs['initial_checkup']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-stethoscope f-18"></i></div> 
                                <div class="media-support-info ml-3">
                                  <h6><a href="{{url('hr/operation/maternity-medical-process/'.$leave->id)}}">Initial Checkup </a></h6>
                                  <p id="line" class="mb-0">
                                      @if($tabs['initial_checkup']) 
                                        @if($leave->medical->checkup_date)
                                        {{date('d-m-Y',strtotime($leave->medical->checkup_date))}}
                                        @endif
                                      @else
                                        ----------
                                      @endif
                                  </p>
                               </div>
                                
                            </div>
                            <div class="step d-flex mr-3">
                                <div class="rounded-div @if($tabs['routine_checkup']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-notes-medical f-18"></i></div> 
                                <div class="media-support-info ml-3">
                                  <h6><a href="{{url('hr/operation/maternity-medical-process/'.$leave->id)}}">Routine Checkup </a></h6>
                                  <p id="line" class="mb-0">
                                      @if($tabs['routine_checkup']) 
                                        @if($leave->medical->record->last()->checkup_date)
                                        {{date('d-m-Y',strtotime($leave->medical->record->last()->checkup_date))}}
                                        @endif
                                      @else
                                        ----------
                                      @endif
                                  </p>
                               </div>
                                
                            </div>
                            <div class="step d-flex mr-3">
                                <div class="rounded-div @if($tabs['doctors_clearence']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-file-prescription f-18"></i></div> 
                                <div class="media-support-info ml-3">
                                  <h6><a href="{{url('hr/operation/maternity-leave/doctors-clearence/'.$leave->id)}}">Doctor's Clearence</a> </h6>
                                  <p id="line" class="mb-0">
                                      @if($tabs['doctors_clearence']) 
                                        {{$leave->medical->record->last()->checkup_date}}
                                      @else
                                        ----------
                                      @endif
                                  </p>
                               </div>
                                
                            </div>
                            <div class="step d-flex ">
                                <div class="rounded-div @if($tabs['leave_approval']) iq-bg-primary @else iq-bg-danger @endif"><i class="las la-user-check f-18"></i></div> 
                                <div class="media-support-info ml-3">
                                  <h6>Leave Approval </h6>
                                  <p id="line" class="mb-0">
                                      @if($tabs['leave_approval']) 
                                        {{$leave->medical->record->last()->checkup_date}}
                                      @else
                                        ----------
                                      @endif
                                  </p>
                               </div>
                                
                            </div>
                        </div>
                        <div id="leave-process" class="mt-5">
                            {{-- link for initial checkup --}}
                            @if(!$tabs['initial_checkup'])
                                <div class="text-center mt-5">
                                    
                                    <i class="las la-stethoscope f-100 text-danger"></i>
                                    <br>
                                    <h5 class="text-primary">Initial checkup is not completed yet!</h5>
                                    <br>
                                    <a href="{{url('hr/operation/maternity-medical-process/'.$leave->id)}}" class="btn btn-primary btn-200">Initial Checkup</a>
                                </div>
                            @endif 
                            @if($tabs['initial_checkup'] == true && $tabs['routine_checkup'] == false)
                                <div class="text-center mt-5">
                                    
                                    <i class="las la-notes-medical f-100 text-danger"></i>
                                    <br>
                                    <h5 class="text-primary">Routine checkup is not completed yet!</h5>
                                    <br>
                                    <a href="{{url('hr/operation/maternity-medical-process/'.$leave->id)}}" class="btn btn-primary btn-200">Routine Checkup</a>
                                </div>
                            @endif 

                            @if($tabs['initial_checkup'] == true && $tabs['routine_checkup'] == true && $tabs['doctors_clearence'] == false)
                                <div class="text-center mt-5">
                                    
                                    <i class="las la-file-prescription f-100 text-danger"></i>
                                    <br>
                                    <h5 class="text-primary">Waiting for doctors clearence!</h5>
                                    <br>
                                    <a href="{{url('hr/operation/maternity-leave/doctors-clearence/'.$leave->id)}}" class="btn btn-primary btn-200">Doctors Clearence</a>
                                </div>
                            @endif 
                            <!-- leave approval form start -->
                            @if($tabs['doctors_clearence'] == true && $tabs['leave_approval'] == false)
                            <form id="approval-form" action=""  class="needs-validation " novalidate>
                                
                                <div class="row">
                                    <div class="col-sm-4">
                                        <input type="hidden" name="hr_maternity_leave_id" value="{{$leave->id}}">
                                        <legend class="block-title ">Leave Information</legend>
                                        <div class="form-group  has-float-label has-required mt-2">
                                            <input type="date" id="leave_from" type="leave_from" name="leave_from" class="form-control"    value="{{$leave->leave_from_suggestion}}" required > 
                                            <label for="leave_from">Leave From</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="date" id="leave_to" type="leave_to" name="leave_to" class="form-control"   value="{{\Carbon\Carbon::create($leave->leave_from_suggestion)->addDays(111)->format('Y-m-d')}}" required readonly> 
                                            <label for="leave_to">Leave To</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="text" id="nominee"  name="nominee" class="form-control"  value="{{$leave->husband_name??''}}" placeholder="Enter nominee name" required>
                                            <label for="nominee">Nominee</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="text" id="relation"  name="relation" class="form-control"  value="Husband" placeholder="Enter relation with nominee" required>
                                            <label for="relation">Relation</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="text" id="fathers_name"  name="fathers_name" class="form-control" placeholder="Enter nominee father's name" required>
                                            <label for="fathers_name">Nominee Father's Name</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="text" id="mobile_no"  name="mobile_no" class="form-control" placeholder="Nominee mobile no" required>
                                            <label for="mobile_no">Mobile No</label>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="col-sm-4">
                                        <legend class="block-title ">Nominee Present Address</legend>
                                       

                                        <div class="form-group has-required has-float-label select-search-group mt-2">
                                            {{ Form::select('pr_district', district_by_id(), null, ['placeholder'=>'Select District', 'id'=>'pr_district', 'class'=> 'form-control', 'required']) }}  
                                            <label  for="pr_district"> District </label>
                                        </div>

                                        <div class="form-group has-required has-float-label select-search-group">
                                            {{ Form::select('pr_upzila', upzila_by_id(),null, [ 'placeholder' =>'Select Upazilla', 'id'=>'pr_upzila', 'class'=> 'no-select form-control', 'required']) }}
                                            <label  for="pr_upzila"> Upazilla </label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                            <input name="pr_post" type="text" id="pr_post" placeholder="Present PO" class="form-control" required/>
                                            <label  for="pr_post"> PO </label>
                                        </div>
                                        <div class="form-group has-float-label">
                                            <input name="pr_road" type="text" id="pr_road" placeholder="Present Road" class="form-control"  />
                                            <label  for="pr_road"> Road </label>
                                        </div>
                                        <div class="form-group has-float-label">
                                            <input name="pr_house_no" type="text" id="pr_house_no" placeholder="Present house no" class="form-control" />
                                            <label  for="pr_house_no"> House No </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <legend class="block-title ">Nominee Permanent Address</legend>
                                        
                                        <div class="form-group has-float-label select-search-group has-required mt-2">
                                            {{ Form::select('per_district', district_by_id(), null, ['placeholder'=>'Select District', 'id'=>'per_district', 'class'=> 'form-control','required']) }}  
                                            <label  for="per_district"> District </label>
                                        </div>

                                        <div class="form-group has-float-label select-search-group has-required">
                                            {{ Form::select('per_upzila', upzila_by_id(),null, [ 'placeholder' =>'Select Upazilla', 'id'=>'per_upzila', 'class'=> 'no-select form-control','required']) }}
                                            <label  for="per_upzila"> Upazilla </label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                            <input name="per_post" type="text" id="per_post" placeholder="Permanent PO" class="form-control" required/>
                                            <label  for="per_post"> PO </label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                            <input name="per_village" type="text" id="per_village" placeholder="Permanent village" class="form-control"  required/>
                                            <label  for="per_village"> Village </label>
                                        </div>

                                        @if( ($leave->no_of_son + $leave->no_of_daughter ) > 1)
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group has-float-label has-required">
                                                        <input name="earned_leave" type="text" id="earned_leave" placeholder="Earned Leave" class="form-control"  required/>
                                                        <label  for="earned_leave"> Earned Leave </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group has-float-label has-required">
                                                        <input name="sick_leave" type="text" id="sick_leave" placeholder="Sick Leave" class="form-control"  required/>
                                                        <label  for="sick_leave"> Sick Leave </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="form-group has-float-label select-search-group has-required">
                                            <button id="approve" class="btn btn-primary w-100" type="submit">Approve and Process Payment</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @else
                                {!!$view !!}
                            @endif
                            <!-- leave approval form end -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
@include('hr.operation.maternity.maternity-modal')
@push('js')
<script type="text/javascript">
    $("#approval-form").submit(function(e){
        e.preventDefault();
        var curMeInputs = $(this).find("input[type='text'],input[type='email'],input[type='password'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
            isValid = true;
        for (var i = 0; i < curMeInputs.length; i++) {
           if (!curMeInputs[i].validity.valid) {
              isValid = false;
           }
        }
        if(isValid){
            $('.app-loader').show();
            var data = $(this).serialize(); 
            $.ajax({
                type: "POST",
                url: '{{ url("hr/maternity-leave/approve") }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: data, 
                success: function(response)
                {
                    $('#approval-form').html(response.view);
                    $('.app-loader').hide();
                }
            });
        }else{
            $.notify('Please fill all required fields!','error');
        }
    });

    $(document).on('change', '#leave_from', function(){
        var d = new Date($(this).val());
        d.setDate(d.getDate() + 111);
        $('#leave_to').val(JSON.stringify(new Date(d)).slice(1,11));
    });

    $("#pr_district").on('change', function()
    { 
        var id = $(this).val();
        if (id != '')
        {
          $('.app-loader').show();
            $.ajax({
                url: '{{ url("district_wise_upazilla") }}',
                type: 'json',
                method: 'get',
                data: {district_id: $(this).val() },
                success: function(data)
                {
                    $("#pr_upzila").html(data);
                    $('.app-loader').hide();
                },
                error: function()
                {
                    $('.app-loader').hide();
                    $.notify('please try again','error');
                }

            });
        } 
    });

    $("#per_district").on('change', function()
    { 
        var id = $(this).val();
        if (id != '')
        {
          $('.app-loader').show();
            $.ajax({
                url: '{{ url("district_wise_upazilla") }}',
                type: 'json',
                method: 'get',
                data: {district_id: $(this).val() },
                success: function(data)
                {
                    $("#per_upzila").html(data);
                    $('.app-loader').hide();
                },
                error: function()
                {
                    $('.app-loader').hide();
                    $.notify('please try again','error');
                }

            });
        } 
    });
    
</script>
@endpush
@endsection