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
        position: relative;
        cursor: pointer;
        background: #fff;
        height: 240px;
        border: 1px solid #cfdecd;
        border-radius: 50%;
        padding: 10px;
    }
    .slide_upload img {
        width: 220px;
        max-height: 220px;
        padding: 2px;
        background-size: cover;
        border-radius: 50%;
        
    }
    .slide_upload::before{content: "+";position: absolute;top: 50%;color: #211515;left: 50%;font-size: 52px;margin-left: -17px;margin-top: -37px;}

</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                   <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a> 
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li>
                    <a href="#">{{$employee->associate_id}}</a>
                </li>
                <li class="active">Update Basic Information</li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="panel">
            <div class="panel-heading">
                <h6>Basic: {{$employee->associate_id}}
                    <div class="btn-group pull-right"> 
                        <a href='{{ url("hr/recruitment/employee/show/$employee->associate_id") }}' target="_blank" class="btn  btn-success" title="Profile"><i class="las la-user-tie"></i></a>

                        <a  href="{{url("hr/recruitment/operation/medical_info_edit/$employee->associate_id")}}" target="_blank" data-tooltip="Edit Medical Info" data-tooltip-location="left" class="btn  btn-warning" style="border-radius: 2px !important; padding: 4px;"><i class="las la-stethoscope bigger-100" ></i></a>

                        <a href='{{ url("hr/recruitment/employee/edit/$employee->associate_id") }}' class="btn  btn-success" title="Basic Info"><i class="las la-bold"></i></a>
                        <a href='{{ url("hr/recruitment/operation/advance_info_edit/$employee->associate_id") }}' class="btn  btn-info" title="Advance Info"><i class="las la-id-card"></i></a>
                        <a href='{{ url("hr/recruitment/operation/benefits?associate_id=$employee->associate_id") }}' class="btn  btn-primary" title="Benefits"><i class="las la-dollar-sign"></i></a>
                        <a href='{{ url("hr/ess/medical_incident?associate_id=$employee->associate_id") }}' class="btn  btn-warning" title="Medical Incident"><i class="las la-procedures"></i></a>
                        <a href='{{ url("hr/operation/servicebook?associate_id=$employee->associate_id") }}' class="btn  btn-danger" title="Service Book"><i class="las la-address-book"></i></a>
                    </div>
                </h6>
            </div>
            <div class="panel-body">
                {{ Form::open(['url'=>'hr/recruitment/employee/update_employee', 'files' => true, 'class'=>'form-horizontal']) }}
                    <div class="row">
                        <div class="col-sm-4">

                            <input type="hidden" name="as_id" value="{{ $employee->as_id }}">


                            <div class="form-group text-center mt-5">
                                <label class="slide_upload" for="file_image" title="Click to change picture"> 
                                <img id="image_load_id" src='{{ url(emp_profile_picture($employee)) }}' >
                                </label>
                                <input type="file" id="file_image" name="as_pic" onchange="readURL(this,this.id)" style="display:none">
                                
                            </div>
                            <input type="hidden" name="old_pic" value="{{ $employee->as_pic }}">
                            <p class="help-text text-center mb-3">Picture <strong>(jpg, jpeg, png)</strong>  Maximum Size: 200KB</p>

                            {{-- <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('associate_id', [Request::get('associate') => Request::get('associate')], $employee->associate_id, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select form-control']) }}
                                <label  for="associate_id"> Associate's ID  </label>
                            </div> --}}

                            <div class="form-group has-required has-float-label">
                                <input name="as_name" type="text" id="as_name" placeholder="Associate's Name" class="form-control" required="required" value="{{ $employee->as_name }}" />
                                <label  for="as_name"> Associate's Name </label>
                            </div>

                            

                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('as_emp_type_id', $employeeTypes, $employee->as_emp_type_id, ['placeholder'=>'Select Employee Type', 'id'=>'as_emp_type_id',  'required'=>'required']) }}  
                                <label  for="as_emp_type_id"> Employee Type </label>
                            </div> 

                            @if(auth()->user()->can(''))
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
                                <input name="as_oracle_code" type="text" id="as_oracle_code" placeholder="Oracle Code" class="form-control" required="required" value="{{ $employee->as_oracle_code }}" required-optional="true" />
                                <label  for="as_oracle_code"> Oracle Code </label>
                            </div> 


                            <div class="form-group has-required has-float-label">
                                <input name="as_rfid_code" type="text" id="as_rfid_code" placeholder="RFID Code" class="form-control" required="required"   value="{{ $employee->as_rfid_code }}" required-optional="true" />
                                <label  for="as_rfid_code"> RFID Code </label>
                            </div>

                             

                        </div>
                        <div class="col-sm-4">
                             @if($cost_mapping_unit_status==false)
                            <div class="form-group">
                                <label  for="unit_map_checkbox"></label>
                                <div class="checkbox">
                                    <label style="padding-left: 10px;">
                                        <input name="unit_map_checkbox" id="unit_map_checkbox" type="checkbox" class="ace"/>
                                        <span class="lbl">&nbsp;&nbsp;&nbsp;Assign for Cost Mapping(Unit)</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="form-group has-required has-float-label">

                                <input name="as_dob" type="date" id="date" placeholder="Date of Birth" class="datepicker form-control" required="required" value="{{ $employee->as_dob }}" />
                                <label  for="as_dob"> Date of Birth </label>
                            </div>
                            <div class="form-group">
                                <label  for="gender"> Gender </label>
                                <div class="radio">
                                    <label>
                                        {{ Form::radio('as_gender', 'Male', (($employee->as_gender=="Male")?true:false), ['class'=>'ace' ,'required'=>'required']) }}
                                        <span class="lbl" value="Male"> Male</span>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        {{ Form::radio('as_gender', 'Female', (($employee->as_gender=="Female")?true:false), ['class'=>'ace']) }}
                                        <span class="lbl" value="Female"> Female</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input name="as_contact" type="text" id="as_contact" placeholder="Contact Number" class="form-control" required="required" value="{{ $employee->as_contact }}" />
                                <label  for="as_contact"> Contact No. </label>
                            </div>
                            <div class="form-group has-required has-float-label">

                                <input type="date" name="as_doj" id="as_doj" placeholder="Date of Joining" class="form-control" required="required"  value="{{ $employee->as_doj }}" />
                                <label  for="as_doj"> Date of Joining </label>
                            </div>



                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('as_ot', [0=>'Non OT',1=>'OT'], $employee->as_ot, ['id'=>'as_ot',  'required'=>'required']) }}  
                                <label  for="as_ot"> OT Status </label>
                            </div> 
                            <div class="form-group">
                                <label  for="status"> Status </label>
                                <div class="radio">
                                    <label>
                                        {{ Form::radio('as_status', '1', (($employee->as_status=="1")?true:false), [ 'id'=>'active_status','class'=>'ace' ,'required'=>'required']) }}
                                        <span class="lbl"> Active</span>
                                    </label>
                                    <label>
                                        {{ Form::radio('as_status', '2', (($employee->as_status=="2")?true:false), ['class'=>'ace']) }}
                                        <span class="lbl"> Resign</span>
                                    </label>
                                    <label>
                                        {{ Form::radio('as_status', '3', (($employee->as_status=="3")?true:false), ['class'=>'ace']) }}
                                        <span class="lbl"> Terminate</span>
                                    </label>
                                    <label>
                                        {{ Form::radio('as_status', '4', (($employee->as_status=="4")?true:false), ['class'=>'ace']) }}
                                        <span class="lbl"> Suspend</span>
                                    </label>
                                     <label>
                                        {{ Form::radio('as_status', '5', (($employee->as_status=="5")?true:false), ['class'=>'ace']) }}
                                        <span class="lbl"> Left</span>
                                    </label>
                                </div>
                            </div>
                                    
                            <div class="form-group has-required has-float-label">
                                <input type="date" name="as_status_date" id="as_status_date" placeholder=" Date of Status" class="form-control datepicker" required="required" autocomplete="off" 
                                @if($employee->as_status_date)
                                    value="{{ $employee->as_status_date }}" 
                                @else
                                    value="{{date('Y-m-d')}}" 
                                @endif   
                                />
                                <label  for="as_status_date"> Date of Status </label>
                            </div>

                            <div class="form-group has-float-label">
                                <textarea name="as_remarks" id="as_remarks" class="form-control">{{ $employee->as_remarks }}</textarea>
                                <label  for="as_remarks"> Remarks </label>           
                            </div>
                            
                        </div>

                        <div class="col-sm-4">
                            <input type="hidden" name="temp_id" value="{{ $employee->temp_id }}">
                           
                            
                            
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('as_unit_id', $unitList, $employee->as_unit_id, ['placeholder'=>'Select Unit', 'id'=>'as_unit_id',   'required'=>'required']) }}  
                                <label  for="as_unit_id"> Unit </label>
                            </div>
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('as_location_id', $locationList, $employee->as_location, ['placeholder'=>'Select Location', 'id'=>'as_location_id',   'required'=>'required']) }}  
                                <label  for="as_location_id"> Location </label>
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

                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('as_shift_id', $shiftList, $employee->as_shift_id, ['placeholder'=>'Select Shift', 'id'=>'as_shift_id',  'required'=>'required']) }} 
                                    <label  for="as_shift_id"> Shift </label>
                                </div> 
                            </div>
                           
                            <!-- ENDS OF WORKER INFORMATION -->
                            

                            @if($cost_mapping_area_status == false)
                            <div class="form-group">
                                <label  for="area_map_checkbox"></label>
                                <div class="checkbox">
                                    <label style="padding-left: 10px;">
                                        <input name="area_map_checkbox" id="area_map_checkbox" type="checkbox" class="ace"/>
                                        <span class="lbl">&nbsp;&nbsp;&nbsp;Assign for Cost Mapping(Area)</span>
                                    </label>
                                </div>
                            </div>
                            @endif
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
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right" type="button">
                                        <i class="fa fa-check"></i> Update
                                </button>
                            </div>
                        </div>
                    </div> 
                            
                {{ Form::close() }}
                
            </div>
        </div>
    </div>
</div>
 @push('js')
<script type="text/javascript">
$(document).ready(function()
{    
    var id ='{{$employee->associate_id}}';
    var text='{{$employee->associate_id}}'+'-'+'{{$employee->as_name}}';
    var newOption = new Option(text, id, true, true);
    $('#associate_id').append(newOption).trigger('change');

    /*
    |-------------------------------------------------- 
    | ENGLISH
    |-------------------------------------------------- 
    */
    var unit= $("#as_unit_id");
    var floor= $("#as_floor_id");
    var line = $("#as_line_id");
    var shift = $("#as_shift_id");
    var associate_id = $("#associate_id");

    associate_id.on('change', function(){
        window.location = '{{url('hr/recruitment/employee/edit')}}'+'/'+$(this).val();
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
                alert('failed...');
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
                alert('failed...');
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
                alert('failed...');
            }
        });
    });
    line.on("change",function(){
        $.ajax({
            url : "{{ url('hr/setup/getShiftListByLineID') }}",
            type: 'get',
            data: {unit_id: unit.val(), floor_id: floor.val(), line_id: line.val() },
            success: function(data)
            {
                shift.html(data);
            },
            error: function()
            {
                alert('failed...');
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
                alert('failed...');
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
                alert('failed...');
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
                alert('failed...');
            }
        });
    });



    $('.dropZone').ace_file_input({  
        style: 'well',
        btn_choose: 'Drop files here or click to choose',
        btn_change: null,
        no_icon: 'ace-icon fa fa-cloud-upload',
        droppable: true,
        thumbnail: 'fit'//large | fit
        //,icon_remove:null //set null, to hide remove/reset button
        ,before_change:function(files, dropped) {  
            var fileType = ["image/png", "image/jpg", "image/jpeg"]; 

            if ((files[0].size <= 524288) && (jQuery.inArray(files[0].type, fileType) != '-1'))
            { 
                return true;
            }
            else
            {
                return false;
            }
        } 
    }).on('change', function(){
        // console.log($(this).data('ace_input_files'));
        //console.log($(this).data('ace_input_method'));
    });

  
    /*
    |-------------------------------------------------- 
    | BANGLA 
    |-------------------------------------------------- 
    */
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

/*$(window).load(function(){

 // disable Active radio button   
   var status=$('input[name=as_status]:checked').val();
   if(status==2||status==3||status==5){
     $('#active_status').prop('disabled', true);
   }
});*/
 
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
    </script>
@endpush
@endsection