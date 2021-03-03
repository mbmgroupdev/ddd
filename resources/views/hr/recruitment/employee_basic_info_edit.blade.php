@extends('hr.layout')
@section('title', $employee->associate_id.'  basic information')
@section('main-content')
@push('css')
<style type="text/css">
    .ace-file-input .ace-file-container:before{
        font-size: 12px !important;
    }
    .ace-file-input .ace-file-container:after{
        font-size: 12px !important;
    }
    
    .form-actions {margin-bottom: 0px; margin-top: 0px; padding: 0px 25px 0px;background-color: unset; border-top: unset;}

    .slide_upload {
        width: 240px;
        height: 240px;
        position: relative;
        cursor: pointer;
        background: #fff;
        height: auto;
        border: 1px solid #cfdecd;
        border-radius: 15px;
        padding: 10px;
    }
    .slide_upload img {
        width: 220px;
        height: 220px;
        padding: 2px;
        border-radius: 15px;
        object-fit: cover;
    }
    .slide_upload::before{content: "+";position: absolute;top: 50%;color: #211515;left: 50%;font-size: 52px;margin-left: -17px;margin-top: -37px;}
    .help-text{
        color: #777978;
        font-size: 10px;
    }
    .help-text strong{
        font-size: 10px;
    }
    .btn-special{
        border: 1px solid #089eaf;
        border-radius: 20px;
        padding: 2px 15px;
        color: #089eaf;
        text-transform: uppercase;
    }
    .btn-special:hover{
        color: #fff;
        background: #089eaf;
    }
    


</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                   <a href="/"><i class="ace-icon fa fa-home home-icon"></i> Human Resource</a> 
                </li>
                <li>
                    <a href="#">Employee</a>
                </li>
                <li>
                    <a href="{{ url("hr/recruitment/employee/show/$employee->associate_id") }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='View Profile' class="font-weight-bold">{{$employee->associate_id}}</a>
                </li>
                <li class="top-nav-btn">
                    @php 
                        $act_page = 'basic'; 
                        $associate_id = $employee->associate_id;
                    @endphp
                    @include('hr.common.emp_profile_pagination')
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        
            <div class="row">
                <div class="col-sm-4">
                    <div class="panel" style="margin-top: 70px;">
                        <div class="panel-body">
                            {{ Form::open(['url'=>'hr/recruitment/employee/update_employee', 'files' => true, 'class'=>'form-horizontal']) }}
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $employee->as_id }}">
                                <div class="form-group text-center" style="margin-bottom: -60px;">
                                    <label class="slide_upload" for="file_image" title="Click to change picture" style="top: -60px;"> 
                                    <img id="image_load_id" src='{{ url(emp_profile_picture($employee)) }}' >
                                    </label>
                                    <input type="file" id="file_image" name="as_pic" onchange="readURL(this,this.id)" style="display:none">
                                    
                                </div>
                                <input type="hidden" name="old_pic" value="{{ $employee->as_pic }}">
                                <p class="help-text text-center mb-3">Picture <strong>(jpg, jpeg, png)</strong>  Maximum Size: 200KB</p>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-special">Update</button>
                                </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 pl-0">
                    <div class="panel">
                        <div class="panel-heading text-left">
                            <h6>Basic Information</h6>
                        </div>
                        <div class="panel-body">
                            <form class="edit-info" method="post">
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $employee->as_id }}">
                            
                                <div class="row">
                                    <div class="col-sm-6">
                                        
                                        <div class="form-group has-required has-float-label">
                                            <input name="as_name" type="text" id="as_name" placeholder="Associate's Name" class="form-control" required="required" value="{{ $employee->as_name }}" />
                                            <label  for="as_name"> Associate's Name </label>
                                        </div>
                                        <input type="hidden" name="as_id" value="{{ $employee->as_id }}">

                                        <div class="form-group has-required has-float-label select-search-group">
                                            {{ Form::select('as_emp_type_id', $employeeTypes, $employee->as_emp_type_id, ['placeholder'=>'Select Employee Type', 'id'=>'as_emp_type_id',  'required'=>'required']) }}  
                                            <label  for="as_emp_type_id"> Employee Type </label>
                                        </div> 

                                        @if(auth()->user()->can('Manage Employee'))
                                        <div class="form-group has-required has-float-label select-search-group">
                                            <select name="as_designation_id" id="as_designation_id" style="width:100%" required="required">
                                                @foreach($designationList AS $desg)
                                                    <option value="{{ $desg->hr_designation_id }}" {{ $desg->hr_designation_id==$employee->as_designation_id?" Selected ":"" }}>{{ $desg->hr_designation_name }} </option>
                                                @endforeach 
                                            </select>
                                            <label  for="as_designation_id">Designation </label>
                                        </div>
                                        @else
                                        <div class="form-group has-required has-float-label">
                                            <input type="hidden" value="{{ $employee->as_designation_id }}" name="as_designation_id">
                                            <input type="text" value="{{ $employee->hr_designation_name }}" readonly class="form-control">
                                            <label  for="as_designation_id">Designation </label>
                                        </div>
                                        @endif

                                        <div class="form-group has-required has-float-label">

                                            <input type="date" name="as_doj" id="as_doj" placeholder="Date of Joining" class="form-control" required="required"  value="{{ $employee->as_doj->format('Y-m-d') }}" />
                                            <label  for="as_doj"> Date of Joining</label>
                                        </div>

                                        <div class="form-group  has-float-label">
                                            <input name="as_rfid_code" type="text" id="as_rfid_code" placeholder="RFID Code" class="form-control"   value="{{ $employee->as_rfid_code }}"  />
                                            <label  for="as_rfid_code"> RFID Code </label>
                                        </div>
                                        <div class="form-group  has-float-label">
                                            <input name="as_oracle_code" type="text" id="as_oracle_code" placeholder="Oracle Code" class="form-control"  value="{{ $employee->as_oracle_code }}"  />
                                            <label  for="as_oracle_code"> Oracle Code </label>
                                        </div>
                                    </div>
                                
                                    <div class="col-sm-6">
                                        <div class="form-group has-required has-float-label">

                                            <input name="as_dob" type="date" id="date" placeholder="Date of Birth" class="age-validate form-control" required="required"  value="{{ $employee->as_dob!=''?$employee->as_dob->format('Y-m-d'):'' }}" />

                                            <label  for="as_dob"> Date of Birth </label>
                                        </div>

                                        <div class="form-group has-required has-float-label">
                                            <input name="as_contact" type="text" id="as_contact" placeholder="Contact Number" class="form-control" required="required" value="{{ $employee->as_contact }}" />
                                            <label  for="as_contact"> Contact No. </label>
                                        </div>
                                       
                                        <!-- ENDS OF WORKER INFORMATION -->
                                        <label>Gender</label> <br>
                                        <div class="form-inline mb-3">
                                            <div class="custom-control custom-radio custom-control-inline">
                                              <input type="radio" id="male" name="as_gender" class="custom-control-input" value="Male" @if($employee->as_gender=="Male") checked @endif>
                                              <label class="custom-control-label" for="male"> Male </label>
                                           </div>

                                           <div class="custom-control custom-radio custom-control-inline">
                                              <input type="radio" id="female" name="as_gender" class="custom-control-input" value="Female" @if($employee->as_gender=="Female") checked @endif>
                                              <label class="custom-control-label" for="female"> Female </label>
                                           </div>
                                        </div>
                                        <div class="form-group has-float-label">
                                            <textarea name="as_remarks" id="as_remarks" class="form-control" style="height: 68px;">{{ $employee->as_remarks }}</textarea>
                                            <label  for="as_remarks"> Remarks </label>           
                                        </div>
                                        <div class="form-group text-right">
                                            <button type="submit" class="btn btn-special">Update</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-4">
                    <div class="panel">
                        <div class="panel-heading text-center">
                            <h6>Unit Mapping</h6>
                        </div>
                        <div class="panel-body">
                            <form class="edit-info" method="post">
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $employee->as_id }}">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_unit_id', $unitList, $employee->as_unit_id, ['placeholder'=>'Select Unit', 'id'=>'as_unit_id',   'required'=>'required']) }}  
                                    <label  for="as_unit_id"> Unit </label>
                                </div>
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_location', $locationList, $employee->as_location, ['placeholder'=>'Select Location', 'id'=>'as_location',   'required'=>'required']) }}  
                                    <label  for="as_location"> Location </label>
                                </div>

                                <!-- WORKER INFORMATION -->
                                <div id="as_emp_type_info"> 
             
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('as_floor_id', $floorList, $employee->as_floor_id, ['placeholder'=>'Select Floor', 'id'=>'as_floor_id']) }}
                                        <label  for="as_floor_id"> Floor </label>
                                    </div>

                                    <div class="form-group has-required has-float-label select-search-group" >
                                        {{ Form::select('as_line_id', $lineList, $employee->as_line_id, ['placeholder'=>'Select Line', 'id'=>'as_line_id' ]) }} 
                                        <label  for="as_line_id"> Line </label>
                                    </div> 

                                    
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-special">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 pl-0">
                    <div class="panel">
                        <div class="panel-heading text-center">
                            <h6>Area Mapping</h6>
                        </div>
                        <div class="panel-body">
                            <form class="edit-info" method="post">
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $employee->as_id }}">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_area_id', $areaList, $employee->as_area_id, ['placeholder'=>'Area Name', 'id'=>'as_area_id',  'required'=>'required']) }}  
                                    <label  for="as_area_id">Area </label>
                                </div>
         
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_department_id', $departmentList, $employee->as_department_id, ['placeholder'=>'Department Name', 'id'=>'as_department_id',  'required'=>'required']) }} 
                                    <label  for="as_department_id" >Department Name </label>
                                </div>

                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_section_id', $sectionList, $employee->as_section_id, ['placeholder'=>'Section Name', 'id'=>'as_section_id',  'required'=>'required']) }}
                                    <label  for="as_section_id" >Section Name </label>
                                </div>

                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_subsection_id', $subsectionList, $employee->as_subsection_id, ['placeholder'=>'Sub Section Name', 'id'=>'as_subsection_id',  'required'=>'required']) }} 
                                    <label  for="as_subsection_id" > Sub Section Name </label>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-special">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 pl-0">
                    <div class="panel">
                        <div class="panel-heading text-center">
                            <h6>Shift & Roaster</h6>
                        </div>
                        <div class="panel-body">
                            <form class="edit-info" method="post">
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $employee->as_id }}">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_ot', [0=>'Non OT',1=>'OT'], $employee->as_ot, ['id'=>'as_ot',  'required'=>'required']) }}  
                                    <label  for="as_ot"> OT Status </label>
                                </div> 
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_shift_id', $shiftList, $employee->as_shift_id, ['placeholder'=>'Select Shift', 'id'=>'as_shift_id',  'required'=>'required']) }} 
                                    <label  for="as_shift_id"> Shift </label>
                                </div> 
                                
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('shift_roaster_status', [0=>'Shift',1=>'Roaster'], $employee->shift_roaster_status, ['id'=>'shift_roaster_status',  'required'=>'required']) }}  
                                    <label  for="as_ot"> Shift/Roaster Status </label>
                                </div> 
                                <div id="holiday" class="form-group has-required has-float-label select-search-group @if($employee->shift_roaster_status == 1) show @else hide @endif">
                                    @php
                                        $days = array(
                                            ''    => 'Select Holiday',
                                            'Fri' => 'Friday',
                                            'Sat' => 'Saturday',
                                            'Sun' => 'Sunday',
                                            'Mon' => 'Monday',
                                            'Tue' => 'Tuesday',
                                            'Wed' => 'Wednesday',
                                            'Thu' => 'Thursday'
                                        );

                                    @endphp
                                    {{ Form::select('day_off', $days, $employee->day_off??null, ['id'=>'day_off']) }}  
                                    <label  for="as_ot"> Holiday </label>
                                </div> 
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-special">Update</button>
                                </div>
                            </form>  
                        </div>
                    </div>
                </div>
            </div>
        
    </div>
</div>
 @push('js')
<script type="text/javascript">
$(document).ready(function()
{    
    var unit= $("#as_unit_id");
    var floor= $("#as_floor_id");
    var line = $("#as_line_id");
    var shift = $("#as_shift_id");
    var associate_id = $("#associate_id");

    associate_id.on('change', function(){
        window.location = '{{url('hr/recruitment/employee/edit')}}'+'/'+$(this).val();
    });   

    $(document).on('change','#shift_roaster_status', function(){
        $(this).val() == '1'?$('#holiday').removeClass('hide'):$('#holiday').addClass('hide');
    });

    unit.on("change",function(){
        $.ajax({
            url : "{{ url('hr/timeattendance/get_floor_by_unit') }}",
            type: 'get',
            data: {unit: unit.val() },
            success: function(data)
            {
                floor.html(data); 
                
            },
            error: function()
            {
                console.log('failed...');
            }
        });
    });
    unit.on("change",function(){
        $.ajax({
            url : "{{ url('hr/setup/getShiftListByLineID') }}",
            type: 'get',
            data: {unit_id: unit.val()},
            success: function(data)
            {
                shift.html(data);
            },
            error: function()
            {
                console.log('failed...');
            }
        });
    });
    floor.on("change",function(){
        $.ajax({
            url : "{{ url('hr/setup/getLineListByFloorID') }}",
            type: 'get',
            data: {unit_id: unit.val(), floor_id: floor.val() },
            success: function(data)
            {
                line.html(data);
                
            },
            error: function()
            {
                console.log('failed...');
            }
        });
    });
  

    //Load Department List By Area ID
    var area       = $("#as_area_id");
    var department = $("#as_department_id");
    var date_of_joining = $("#as_doj");
    area.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
            type: 'get',
            data: {area_id: $(this).val() },
            success: function(data)
            {
                department.html(data); 
                
            },
            error: function()
            {
                console.log('failed...');
            }
        });
    });
 

    //Load Section List By Department & Area ID
    var area       = $("#as_area_id");
    var department = $("#as_department_id")
    var section    = $("#as_section_id");
    var date_of_joining = $("#as_doj");
    var associate_id = $("#associate_id");
    department.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
            type:  'get',
            data: {area_id: area.val(), department_id: $(this).val() },
            success: function(data)
            {
                section.html(data); 
        
            },
            error: function()
            {
                console.log('failed...');
            }
        });
    });

    //Load Sub Section List By Area ID, Department & Section
    var area       = $("#as_area_id");
    var department = $("#as_department_id")
    var section    = $("#as_section_id");
    var subsection    = $("#as_subsection_id");
    section.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getSubSectionListBySectionID') }}",
            type: 'get',
            data: {area_id: area.val(), department_id: department.val(), section_id: $(this).val() },
            success: function(data)
            {
                subsection.html(data);
            },
            error: function()
            {
                console.log('failed...');
            }
        });
    });


    $('select.associates').select2({
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
                        return {
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    })
                };
          },
          cache: true
        }
    });

     
    //Make unit Floor Line Required if the Unit Cost Mapping Checkbox is checked
    $("#unit_map_checkbox").on("click",function(){
        var unit_check_status= $(this).prop('checked');
        var emp_type= $('#as_emp_type_id :selected').val();
        if(unit_check_status && emp_type != 1){
            floor.attr({'required':"required"});
            line.attr({'required':"required"});
        }
        if(!unit_check_status){
            floor.removeAttr("required");
            line.removeAttr("required");
        }
    });  
    //Make Area, Department, Section, Sub-Section Required if the Area Cost Mapping Checkbox is checked
    $("#area_map_checkbox").on("click",function(){
        var area_check_status= $(this).prop('checked');
        var emp_type= $('#as_emp_type_id :selected').val();
        if(area_check_status && emp_type != 1){
            section.attr({'required':"required"});
            subsection.attr({'required':"required"});
        }
        if(!area_check_status){
            section.removeAttr("required");
            subsection.removeAttr("required");
            
        }
    });
   
});


</script>
<script type="text/javascript">
        function readURL(input,image_load_id) {
          var target_image='#'+$('#'+image_load_id).prev().children().attr('id');
            var filePath = input.files[0].name;
            var fileExtension = ['jpeg', 'jpg', 'png'];
            if ($.inArray(filePath.split('.').pop().toLowerCase(), fileExtension) == -1) {
                alert("Only '.jpeg','.jpg', '.png' formats are allowed.");
            }else{
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(target_image).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
        }

        $(document).on('submit','.edit-info', function(e){
            e.preventDefault();
            $('.app-loader').show();
            var dt = $(this).serializeArray();
            console.log(dt);
            $.ajax({
                url: '{{ url('hr/recruitment/employee/update_employee') }}',
                type: "POST",
                data : dt,
                success: function(response){
                    $('.app-loader').hide();
                    $.notify('Information updated successfully!','success');
                }
            });
        });




        $(document).on('change', '.age-validate', function(){
            var birthDate = new Date($(this).val());
            var difdt = new Date(new Date() - birthDate);
            var age = difdt.toISOString().slice(0, 4) - 1970;
            if (age >= 18) {
                return true;
            }else{
                $.notify('Age is under 18! Please select a valid date', 'error');
                $(this).val('');
            }
        });
    </script>
@endpush
@endsection