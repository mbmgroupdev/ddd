@extends('hr.layout')
@section('title', '')
@section('main-content')
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
                    <a href="#">Employee</a>
                </li>
                <li class="active">Recruit</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
             
            <div class="row"> 
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="panel panel-info" style="margin-left: 13px;margin-right: 13px;">
                    <div class="panel-heading"><h6>Employee Recruitment Entry <a href="{{ url('hr/recruitment/worker/recruit_list')}}" class="pull-right btn btn-xx btn-info">Recruit list</a></h6></div> 
                    <div class="panel-body">
                {{ Form::open(['url'=>'hr/recruitment/worker/recruit', 'class'=>'form-horizontal']) }}
                    <div class="col-sm-12">
                        <div class="col-sm-5">
                            <div class="form-group">

                                <label class="col-sm-5 control-label no-padding-right" for="worker_name"> Associate's Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7"> 

                                    <input name="worker_name" type="text" id="worker_name" placeholder="Associate's Name" class="col-xs-12" data-validation="required length custom" value="{{ old('worker_name') }}" data-validation-length="3-64" data-validation-error-msg="The Associate's Name has to be an alphabet value between 3-64 characters" style='text-transform:uppercase'/> 
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="gender"> Gender <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <div class="radio">
                                        <label>
                                            {{ Form::radio('worker_gender', 'Male', true, ['class'=>'ace' ,'data-validation'=>'required']) }}
                                            <span class="lbl" value="Male"> Male</span>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            {{ Form::radio('worker_gender', 'Female', false, ['class'=>'ace']) }}
                                            <span class="lbl" value="Female"> Female</span>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            {{ Form::radio('worker_gender', 'third_gender', false, ['class'=>'ace']) }}
                                            <span class="lbl" value="Third Gender"> Third Gender</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_dob"> Date of Birth <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <input name="worker_dob"  id="worker_dob" placeholder="Y-m-d" class="col-xs-12 datepicker form-control"  data-validation="required adultcheck date"  autocomplete="off" value="{{ old('worker_dob') }}" data-validation-format="yyyy-mm-dd" />

                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_contact"> Contact No. <span style="color: red; vertical-align: text-top;">*</span> </label>
                                <div class="col-sm-7">
                                    <input name="worker_contact" type="text" id="worker_contact" value="{{ old('worker_contact') }}" placeholder="01XXXXXXXXX" class="col-xs-12" data-validation="required length number" data-validation-length="11"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_nid"> NID/Birth Certificate </label>
                                <div class="col-sm-7">
                                    <input type="text" name="worker_nid" placeholder="NID or Birth Certificate" value="{{ old('worker_nid') }}" class="col-xs-12" data-validation="length" data-validation-length="0-64" data-validation-error-msg="NID  between 0-64 characters"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_doj"> Date of Joining <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" name="worker_doj" value="{{ old('worker_doj') }}" id="worker_doj" placeholder="Y-m-d" class="col-xs-12 datepicker" value="<?php echo date('Y-m-d'); ?>" data-validation="required" autocomplete="off"/>


                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_emp_type_id"> Employee Type <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7"> 
                                    {{ Form::select('worker_emp_type_id', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'id'=>'worker_emp_type_id', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Employee Type field is required']) }}  
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_designation_id" >Designation <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <select name="worker_designation_id" id="worker_designation_id" style="width: 100%" data-validation="required" data-validation-error-msg='The Designation field is required'>
                                        <option value="">Designation </option> 
                                    </select>
                                </div>
                            </div>
                                
                        </div> 
                        <!-- /.col -->
                        <div class="col-sm-2"></div>
                        <div class="col-sm-5">

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_unit_id"> Unit <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7"> 
                                    {{ Form::select('worker_unit_id', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'worker_unit_id',  'style'=>'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}  
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_area_id"> Select Area <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7"> 
                                    {{ Form::select('worker_area_id', $areaList, null, ['placeholder'=>'Area Name', 'id'=>'worker_area_id', 'style'=> 'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Area field is required']) }}  
                                </div>
                            </div>
     
                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_department_id" >Department Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <select name="worker_department_id" id="worker_department_id" style="width: 100%" data-validation="required" data-validation-error-msg='The Department field is required'>
                                        <option value="">Department Name </option> 
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_section_id" >Section Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <select name="worker_section_id" id="worker_section_id" data-validation="required" data-validation-error-msg='The Section field is required' style="width: 100%">
                                        <option value="">Section Name </option> 
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_subsection_id" > Sub Section Name <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <select name="worker_subsection_id" id="worker_subsection_id" data-validation="required" data-validation-error-msg='The Sub Section field is required' style="width: 100%">
                                        <option value="">Sub Section Name </option> 
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="worker_ot"> OT / Non OT <span style="color: red; vertical-align: text-top;">*</span></label>
                                <div class="col-sm-7">
                                    <select name="worker_ot" id="worker_ot" style="width: 100%" data-validation="required" data-validation-error-msg="Overtime status is required">
                                        <option value="0">Non OT</option>
                                        <option value="1">OT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="as_oracle_code"> Oracle ID </label>
                                <div class="col-sm-7">
                                    <input type="text" name="as_oracle_code" placeholder="Existing Oracle ID" class="col-xs-12" data-validation="length" data-validation-length="0-64"  value="{{ old('as_oracle_code') }}" data-validation-error-msg="Must be Unique"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label no-padding-right" for="as_rfid"> RFID </label>
                                <div class="col-sm-7">
                                    <input type="text" name="as_rfid" placeholder="Card RFID No" class="col-xs-12" value="{{ old('as_rfid') }}" data-validation="length" data-validation-length="0-64" data-validation-error-msg="Must be Unique"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 responsive-hundred">
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center">

                                <button class="btn btn-sm" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                                &nbsp; &nbsp; &nbsp;
                                <button type="submit" class="btn btn-sm btn-success" type="button">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>
                            </div>
                        </div>

                        <!-- /.row -->
                    </div>

                {{ Form::close() }}
                </div>
                </div>
                <div class="col-sm-12 responsive-hundred" {{-- style="padding-left: 25px; " --}}>
                    <div class="col-sm-12 widget-box widget-color-green2 responsive-hundred" {{-- style="width: 40%;" --}}>
                        <h4 class="page-header widget-header" style="text-align:center;height:10px !important;padding-top: 8px;padding-bottom: 1px;"> Bulk Upload <span style="color: red; vertical-align: text-top;">*</span></h4>
                        {{ Form::open(['url'=>'hr/recruitment/worker/recruit/excel/import', 'files' => true,  'class'=>'form-horizontal']) }}
                            <div class="form-group" style="padding-left: 35%;">
                                <label class="col-sm-3 control-label no-padding-right no-padding-top" for="excel_file"> File <br><span>(only .xls and .xlsx files)<br><a href="{{url('samplefiles/example_worker_recruitment.xlsx')}}" title="Sample File">Sample File</a></span></label>
                                <div class="col-sm-4" style="padding-top: 10px;">
                                    <input type="file" name="excel_file" id="excel_file" class="col-xs-12" style="padding:0;" data-validation-allowing="xls, xlsx" required="required" />
                                    <span id="recruit_file_upload_error" class="red" style="display: none; font-size: 14px;">only <strong>.xls</strong> or <strong>.xlsx</strong> file supported.</span>
                                </div>
                            </div> 
                            
                          <div class="col-sm-12" style="padding-left: 30px; padding-right: 30px;">
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-4 col-md-4 text-center">
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                    </button>
                                    &nbsp; &nbsp; &nbsp;
                                    <button type="submit" id="file_save" class="btn btn-sm btn-primary" type="button">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Upload
                                    </button>
                                </div>
                            </div>
                          </div>
                        {{ Form::close() }}
                    </div>
                </div>
                
                <div class="col-xs-12" style="margin-top: 3%;">
                    <div class="panel panel-info">
                      <div class="panel-heading"><h6>Employee Recruit List</h6></div> 
                        <div class="panel-body">
                            <div class=" responsive-hundred">
                                <table id="dataTables" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Name</th>
                                            <th>Joining Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recruitList as $recruited)
                                        <tr>
                                            <td>{{ $recruited->sl }}</td>
                                            <td>{{ $recruited->worker_name }}</td>
                                            <td>{{ $recruited->worker_doj }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a type="button" href="{{ url('hr/recruitment/worker/recruit_edit/'.$recruited->worker_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit Information"><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /.page-content -->
    </div>
</div>

 
@push('js')
    <script>
        $.validate({
          modules : 'adultcheck'
        });
    </script>
    <script type="text/javascript">

    $(document).ready(function()
    {   




        $('#dataTables').DataTable({
            pagingType: "full_numbers" ,
        }); 

        // show hide employee information
        var emp_type_id    = $("#worker_emp_type_id");
        var emp_type_info  = $("#worker_emp_type_info");
        var designation    = $("#worker_designation_id");
        emp_type_id.on("change", function(){ 

            // designation list
            $.ajax({
                url : "{{ url('hr/setup/getDesignationListByEmployeeTypeID') }}",
                type: 'get',
                data: {employee_type_id : $(this).val()},
                success: function(data)
                {
                    designation.html(data);
                },
                error: function()
                {
                    alert('failed...');
                }
            });

        });
     

        //Load Department List By Area ID
        var area       = $("#worker_area_id");
        var department = $("#worker_department_id");
        var date_of_joining = $("#worker_doj");
        area.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
                type: 'get',
                data: {area_id: $(this).val() },
                success: function(data)
                {
                    department.html(data); 
                    section.html('');
                    subsection.html(''); 
                    $("#associate_id").val('');
                    $("input[name=temp_id]").val('');
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });
     

        //Load Section List By Department & Area ID
        var area       = $("#worker_area_id");
        var department = $("#worker_department_id");
        var section    = $("#worker_section_id");
        var date_of_joining = $("#worker_doj");
        var associate_id = $("#associate_id");
        department.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getSectionListByDepartmentID') }}",
                type:  'get',
                data: {area_id: area.val(), department_id: $(this).val() },
                success: function(data)
                {
                    section.html(data); 
                    subsection.html(''); 
                    associate_id.val('');
                    $("input[name=temp_id]").val('');
                },
                error: function()
                {
                    alert('failed...');
                }
            });
        });

        //Load Sub Section List By Area ID, Department & Section
        var area       = $("#worker_area_id");
        var department = $("#worker_department_id")
        var section    = $("#worker_section_id");
        var subsection    = $("#worker_subsection_id");
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

     
        // HR Floor By Unit ID
        var unit  = $("#worker_unit_id");
        var floor = $("#worker_floor_id")
        unit.on('change', function(){
            $.ajax({
                url : "{{ url('hr/setup/getFloorListByUnitID') }}",
                type: 'get',
                data: {unit_id: $(this).val() },
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

     
    }); 


    </script> 

    <script type="text/javascript">
        $(document).ready(function(){ 

            function stringEndsWithValidExtension(stringToCheck, acceptableExtensionsArray, required) {
            if (required == false && stringToCheck.length == 0) { return true; }
            for (var i = 0; i < acceptableExtensionsArray.length; i++) {
                if (stringToCheck.toLowerCase().endsWith(acceptableExtensionsArray[i].toLowerCase())) { return true; }
            }
            return false;
            }


            String.prototype.startsWith = function (str) { return (this.match("^" + str) == str) }

            String.prototype.endsWith = function (str) { return (this.match(str + "$") == str) }

            var file=$("#file_save");
            file.on('click',function () 
             {
                if (!stringEndsWithValidExtension($("#excel_file").val(), [".xls", ".xlsx"], false)) {
                    alert("Only allowed file types are .xls and .xlsx");
                    return false;
                }
                return true;
            });

        });
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
        $("#excel_file").change(function () {
            var fileExtension = ['xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#recruit_file_upload_error').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#recruit_file_upload_error').hide();
                }
        });
        });
    </script>
@endpush
@endsection