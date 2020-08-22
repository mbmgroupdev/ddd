@extends('hr.layout')
@section('title', '')
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

    .slide_upload{width: auto;height: 120px;position: relative;cursor: pointer;background: #eee;border: 1px dashed #999;}
    .slide_upload img{width: auto;height: 100%;border: 1px dashed #999;padding: 2px;}
    .slide_upload::before{content: "+";position: absolute;top: 50%;color: #ccc;left: 50%;font-size: 52px;margin-left: -17px;margin-top: -37px;}

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
                    <a href="#">Employer</a>
                </li>
                <li class="active">Update Basic Information</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header row">
                <h1 class="col-xs-8">Recruitment<small> <i class="ace-icon fa fa-angle-double-right"></i> Update Basic Information</small></h1>
                {{-- <div class="text-right"> --}}
                    <div class="btn-group pull-right"> 
                        <a href='{{ url("hr/recruitment/employee/show/$employee->associate_id") }}' target="_blank" class="btn btn-sm btn-success" title="Profile"><i class="glyphicon glyphicon-user"></i></a>
                        <a  href="{{url("hr/recruitment/operation/medical_info_edit/$employee->associate_id")}}" target="_blank" data-tooltip="Edit Medical Info" data-tooltip-location="left" class="btn btn-sm btn-warning" style="border-radius: 2px !important; padding: 4px;"><i class="fa fa-user-md bigger-120">&nbsp</i><i class="fa fa-edit bigger-100" style="font-size: 10px;"></i></a>
                        <a href='{{ url("hr/recruitment/employee/edit/$employee->associate_id") }}' class="btn btn-sm btn-success" title="Basic Info"><i class="glyphicon glyphicon-bold"></i></a>
                        <a href='{{ url("hr/recruitment/operation/advance_info_edit/$employee->associate_id") }}' class="btn btn-sm btn-info" title="Advance Info"><i class="glyphicon  glyphicon-font"></i></a>
                        <a href='{{ url("hr/recruitment/operation/benefits?associate_id=$employee->associate_id") }}' class="btn btn-sm btn-primary" title="Benefits"><i class="fa fa-usd"></i></a>
                        <a href='{{ url("hr/ess/medical_incident?associate_id=$employee->associate_id") }}' class="btn btn-sm btn-warning" title="Medical Incident"><i class="fa fa-stethoscope"></i></a>
                        <a href='{{ url("hr/operation/servicebook?associate_id=$employee->associate_id") }}' class="btn btn-sm btn-danger" title="Service Book"><i class="fa fa-book"></i></a>
                    </div>
                {{-- </div> --}}
            </div>

            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                
                        {{ Form::open(['url'=>'hr/recruitment/employee/update_employee', 'files' => true, 'class'=>'form-horizontal']) }}
                <div class="col-sm-5">
                    <div id="english">
                        <br/>

                            <input type="hidden" name="as_id" value="{{ $employee->as_id }}">

                            <div class="form-group">

                                <label class="col-sm-4 control-label no-padding-right" for="picture">Picture <span style="color: red; vertical-align: text-top;">*</span><span><br>(jpg|jpeg|png) <br> Maximum Size: 200KB</span> </label>
                                <div class="col-sm-4">
                                    <label class="slide_upload" for="file_image">
                                      <!--  -->
                                      <img id="image_load_id" src='{{ url($employee->as_pic?$employee->as_pic:'assets/images/avatars/profile-pic.jpg') }}' onError='this.onerror=null;this.src="{{ asset('assets/images/avatars/profile-pic.jpg') }}";'>
                                    </label>
                                    <input type="file" id="file_image" name="as_pic" onchange="readURL(this,this.id)" style="display:none">
                                    {{-- <img id="avatar" style="width: 160px; height: 170px;" class="img-responsive" alt="profile picture" src="{{ url($employee->as_pic?$employee->as_pic:'assets/images/avatars/profile-pic.jpg') }}" /> --}}
                                
                                    <input type="hidden" name="old_pic" value="{{ $employee->as_pic }}">
                                </div>
                                <!-- <div class="col-sm-4">
                                    <input name="as_pic" type="file" 
                                    class="dropZone"
                                    data-validation="mime size"
                                    data-validation-allowing="jpeg,png,jpg"
                                    data-validation-max-size="200kb"
                                    data-validation-error-msg-size="You can not upload images larger than 200kb" style="width: 43%;"
                                    data-validation="mime"
                                    data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                                </div> -->
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="associate_id"> Associate's ID <span style="color: red; vertical-align: text-top;">*</span> </label>
                                <div class="col-sm-8">
                                    {{ Form::select('associate_id', [Request::get('associate') => Request::get('associate')], $employee->associate_id, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates no-select col-xs-12','style', 'data-validation'=>'required']) }}                                    
                                
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_name"> Associate's Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    <input name="as_name" type="text" id="as_name" placeholder="Associate's Name" class="col-xs-12" data-validation="required length custom" data-validation-length="3-64" data-validation-error-msg="The Associate's Name has to be an alphabet value between 3-64 characters" style="width: 100%;" value="{{ $employee->as_name }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="gender"> Gender </label>
                                <div class="col-sm-8">
                                    <div class="radio">
                                        <label>
                                            {{ Form::radio('as_gender', 'Male', (($employee->as_gender=="Male")?true:false), ['class'=>'ace' ,'data-validation'=>'required']) }}
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
                            </div>

                            <div class="form-group">

                                <label class="col-sm-4 control-label no-padding-right" for="as_dob"> Date of Birth </label>
                                <div class="col-sm-8">
                                    <input name="as_dob" type="text" id="as_dob" placeholder="Date of Birth" class="datepicker col-xs-10 col-sm-5" data-validation="required" data-validation-format="yyyy-mm-dd" style="width: 100%;" value="{{ $employee->as_dob }}" />

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_contact"> Contact No. <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    <input name="as_contact" type="text" id="as_contact" placeholder="Contact Number" class="col-xs-10 col-sm-5" data-validation="required length number" data-validation-length="1-11" style="width: 100%;" value="{{ $employee->as_contact }}" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_emp_type_id"> Employee Type <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('as_emp_type_id', $employeeTypes, $employee->as_emp_type_id, ['placeholder'=>'Select Employee Type', 'id'=>'as_emp_type_id', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Employee Type field is required']) }}  
                                </div>
                            </div> 
                            @if(auth()->user()->can('hr_designation_update'))
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_designation_id">Designation <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    <select name="as_designation_id" id="as_designation_id" style="width:100%" data-validation="required" data-validation-error-msg='The Designation field is required'>
                                        @foreach($designationList AS $desg)
                                            <option value="{{ $desg->hr_designation_id }}" {{ $desg->hr_designation_id==$employee->as_designation_id?" Selected ":"" }}>{{ $desg->hr_designation_name }} </option>
                                        @endforeach 
                                    </select>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_designation_id">Designation <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="hidden" value="{{ $employee->as_designation_id }}" name="as_designation_id">
                                    <input type="text" value="{{ $employee->hr_designation_name }}" readonly class="form-control">
                                </div>
                            </div>
                            @endif

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="status"> Status <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    <div class="radio">
                                        <label>
                                            {{ Form::radio('as_status', '1', (($employee->as_status=="1")?true:false), [ 'id'=>'active_status','class'=>'ace' ,'data-validation'=>'required']) }}
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
                                        <br>
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
                            </div>
                            
                            @if($cost_mapping_unit_status==false)
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="unit_map_checkbox"></label>
                                <div class="checkbox col-sm-8">
                                    <label class="col-xs-12" style="padding-left: 10px;">
                                        <input name="unit_map_checkbox" id="unit_map_checkbox" type="checkbox" class="ace"/>
                                        <span class="lbl">&nbsp;&nbsp;&nbsp;Assign for Cost Mapping(Unit)</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_oracle_code"> Oracle Code </label>
                                <div class="col-sm-8">
                                    <input name="as_oracle_code" type="text" id="as_oracle_code" placeholder="Oracle Code" class="col-xs-10 col-sm-5" data-validation="required length custom" data-validation-length="1-20" data-validation-error-msg="The Oracle Code has to be an alphabet value between 1-20 characters" style="width: 100%;" value="{{ $employee->as_oracle_code }}" data-validation-optional="true" />
                                </div>
                            </div> 


                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_rfid_code"> RFID Code </label>
                                <div class="col-sm-8">
                                    <input name="as_rfid_code" type="text" id="as_rfid_code" placeholder="RFID Code" class="col-xs-10 col-sm-5" data-validation="required length custom" data-validation-length="1-20" data-validation-error-msg="The RFID Code has to be an alphabet value between 1-20 characters" style="width: 100%;" value="{{ $employee->as_rfid_code }}" data-validation-optional="true" />
                                </div>
                            </div>

                             

                            </div>
                            </div>
                            <div class="col-sm-2"></div>

                            <div class="col-sm-5">
                                <div id="english">
                                <br>
                            <input type="hidden" name="temp_id" value="{{ $employee->temp_id }}">
                            
                            
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_unit_id"> Unit <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('as_unit_id', $unitList, $employee->as_unit_id, ['placeholder'=>'Select Unit', 'id'=>'as_unit_id',  'style'=>'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}  
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_location_id"> Location <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('as_location_id', $locationList, $employee->as_location, ['placeholder'=>'Select Location', 'id'=>'as_location_id',  'style'=>'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Location field is required']) }}  
                                </div>
                            </div>

                            <!-- WORKER INFORMATION -->
                            <div id="as_emp_type_info"> 
         
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="as_floor_id"> Floor </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('as_floor_id', $floorList, $employee->as_floor_id, ['placeholder'=>'Select Floor', 'id'=>'as_floor_id','style'=>'width:100%']) }}   
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="as_line_id"> Line </label>
                                    <div class="col-sm-8">
                                        {{ Form::select('as_line_id', $lineList, $employee->as_line_id, ['placeholder'=>'Select Line', 'id'=>'as_line_id',  'style'=>'width:100%']) }}  
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="as_shift_id"> Shift <span style="color: red; vertical-align: text-top;">*</span></label>
                                    <div class="col-sm-8"> 
                                        {{ Form::select('as_shift_id', $shiftList, $employee->as_shift_id, ['placeholder'=>'Select Shift', 'id'=>'as_shift_id',  'style'=>'width:100%','data-validation'=>'required', 'data-validation-error-msg'=>'Shift field is required']) }} 
                                    </div>
                                </div> 
                            </div>
                           
                            <!-- ENDS OF WORKER INFORMATION -->
                            

                            @if($cost_mapping_area_status == false)
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="area_map_checkbox"></label>
                                <div class="checkbox col-sm-8">
                                    <label class="col-xs-12" style="padding-left: 10px;">
                                        <input name="area_map_checkbox" id="area_map_checkbox" type="checkbox" class="ace"/>
                                        <span class="lbl">&nbsp;&nbsp;&nbsp;Assign for Cost Mapping(Area)</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_area_id">Area <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('as_area_id', $areaList, $employee->as_area_id, ['placeholder'=>'Area Name', 'id'=>'as_area_id', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Area field is required']) }}  
                                </div>
                            </div>
     
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right no-padding-top" for="as_department_id" >Department<br> Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    {{ Form::select('as_department_id', $departmentList, $employee->as_department_id, ['placeholder'=>'Department Name', 'id'=>'as_department_id', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Department field is required']) }} 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_section_id" >Section Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    {{ Form::select('as_section_id', $sectionList, $employee->as_section_id, ['placeholder'=>'Section Name', 'id'=>'as_section_id', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Section field is required']) }}  
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_subsection_id" > Sub Section Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    {{ Form::select('as_subsection_id', $subsectionList, $employee->as_subsection_id, ['placeholder'=>'Sub Section Name', 'id'=>'as_subsection_id', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Section field is required']) }}  
                                </div>
                            </div>

                            <div class="form-group">

                                <label class="col-sm-4 control-label no-padding-right" for="as_doj"> Date of Joining </label>
                                <div class="col-sm-8">
                                    <input type="date" name="as_doj" id="as_doj" placeholder="Date of Joining" class="col-xs-10 col-sm-5" data-validation="required" data-validation-format="mm-dd-yyyy" autocomplete="off" style="width: 100%;" value="{{ $employee->as_doj }}" />

                                </div>
                            </div>



                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_ot"> OT Status <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8"> 
                                    {{ Form::select('as_ot', [0=>'Non OT',1=>'OT'], $employee->as_ot, ['id'=>'as_ot', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Area field is required']) }}  
                                </div>
                            </div> 

            
                            

                            
                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_status_date"> Date of Status <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="as_status_date" id="as_status_date" placeholder=" Date of Status" class="col-xs-10 col-sm-5 datepicker" data-validation="required" data-validation-format="mm-dd-yyyy" style="width: 100%;" autocomplete="off" 
                                    @if($employee->as_status_date)
                                        value="{{ $employee->as_status_date }}" 
                                    @else
                                        value="{{date('Y-m-d')}}" 
                                    @endif   
                                    />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="as_remarks"> Remarks </label>
                                <div class="col-sm-8">
                                    <textarea name="as_remarks" id="as_remarks" class="form-control">{{ $employee->as_remarks }}</textarea>
                                </div>
                            </div>


                             

                            <div class="space-4"></div>
                          

                            <!-- /.row -->

                            

                    </div>
                </div> 
                            <div class="col-sm-12" style="padding-top: 30px;">
                                <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4" style="left: 57px;">
                                    <button type="submit" class="btn btn-sm btn-success" type="button">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Update
                                    </button>

                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                    </button>
                                </div>
                            </div>
                            </div>
                            
                        {{ Form::close() }}
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
 
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
            floor.attr({'data-validation':"required"});
            line.attr({'data-validation':"required"});
        }
        if(!unit_check_status){
            floor.removeAttr("data-validation");
            line.removeAttr("data-validation");
        }
    });  
    //Make Area, Department, Section, Sub-Section Required if the Area Cost Mapping Checkbox is checked
    $("#area_map_checkbox").on("click",function(){
        var area_check_status= $(this).prop('checked');
        var emp_type= $('#as_emp_type_id :selected').val();
        if(area_check_status && emp_type != 1){
            section.attr({'data-validation':"required"});
            subsection.attr({'data-validation':"required"});
        }
        if(!area_check_status){
            section.removeAttr("data-validation");
            subsection.removeAttr("data-validation");
            
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
 
@endsection