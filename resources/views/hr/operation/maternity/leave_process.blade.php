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
                                     <td><i class="field-title">Date of Join</i></td>
                                     <td class="field-data">: {{ $employee->as_doj->format('Y-m-d') }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Age</i></td>
                                     <td class="field-data">: {{ $employee->as_dob->age }} Years</td>
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
                             
                             <p>
                                <i class="las la-file-prescription f-18 text-success" ></i> 
                                <a href="{{ asset($leave->usg_report) }}" style="    vertical-align: text-bottom;">view USG report</a>
                             </p>
                        </div>
                    </div>
                    <div class="col-sm-9" >
                        <div id="leave-process">
                            <!-- leave approval form start -->
                            @if($tabs['doctors_clearence'] == true && $tabs['leave_approval'] == false)
                            <form id="approval-form" action=""  class="needs-validation" novalidate>
                                
                                <div class="row">
                                    <div class="col-sm-4">
                                        <input type="hidden" name="hr_maternity_leave_id" value="{{$leave->id}}">
                                        <legend class="block-title">Leave Information</legend>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="date" id="leave_from" type="leave_from" name="leave_from" class="form-control" min="{{date('Y-m-d')}}"   value="{{$leave->leave_from_suggestion}}" required > 
                                            <label for="leave_from">Leave From</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="date" id="leave_to" type="leave_to" name="leave_to" class="form-control" min="{{date('Y-m-d')}}"   value="{{\Carbon\Carbon::create($leave->leave_from_suggestion)->addDays(112)->format('Y-m-d')}}" required readonly> 
                                            <label for="leave_to">Leave To</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="text" id="nominee"  name="nominee" class="form-control"  value="" placeholder="Enter nominee name" required>
                                            <label for="nominee">Nominee</label>
                                        </div>
                                        <div class="form-group  has-float-label has-required">
                                            <input type="text" id="relation"  name="relation" class="form-control"  value="" placeholder="Enter relation with nominee" required>
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
                                        <legend class="block-title">Nominee Present Address</legend>
                                       

                                        <div class="form-group has-required has-float-label select-search-group">
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
                                        <div class="form-group has-required has-float-label">
                                            <input name="pr_village" type="text" id="pr_village" placeholder="Present village" class="form-control" required/>
                                            <label  for="pr_village"> Village </label>
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
                                        <legend class="block-title">Nominee Permanent Address</legend>
                                        
                                        <div class="form-group has-float-label select-search-group has-required">
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
        d.setDate(d.getDate() + 112);
        $('#leave_to').val(JSON.stringify(new Date(d)).slice(1,11));
    });
    
</script>
@endpush
@endsection