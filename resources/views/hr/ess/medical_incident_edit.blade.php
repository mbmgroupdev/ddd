@extends('hr.layout')
@section('title', 'Medical Incident Edit')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Employee</a>
                </li>
                <li class="active">Medical Incident Edit</li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="panel"> 

            <div class="panel-heading">
                <h6>Medical Incident Edit
                    <a href="{{url('hr/employee/medical_incident_update')}}" class="btn btn-sm btn-primary pull-right">List</a>
                </h6>
            </div>
            <div class="panel-body">

                {{ Form::open(['url'=>'hr/ess/medical_incident', 'method'=>'POST', 'files' => true, 'class'=>'form-horizontal']) }}
                    <input type="hidden" name="id" value="{{ $medical->id }}">
                    <div class="row">
                        
                    
                        <div class="col-sm-4">
                            <div class="form-group has-float-label has-required "> 
                                <input name="hr_med_incident_as_id" type="text" id="hr_med_incident_as_id" placeholder="Associate's Name" class="form-control"  readonly value="{{ $medical->hr_med_incident_as_id }}" />
                                <label  for="hr_med_incident_as_id"> Associate's ID  </label> 
                            </div>

                            <div class="form-group has-float-label has-required">
                                    <input name="hr_med_incident_as_name" type="text" id="hr_med_incident_as_name" placeholder="Associate's Name" class="form-control" required="required"  value="{{ $medical->hr_med_incident_as_name }}" readonly/>
                                <label  for="hr_med_incident_as_name"> Associate's Name</label>
                            </div> 

                            <div class="form-group has-float-label has-required">

                                    <input type="date" name="hr_med_incident_date" id="hr_med_incident_date" class="form-control datepicker" required="required" placeholder="Y-m-d" value="{{ $medical->hr_med_incident_date }}"/>
                                <label  for="hr_med_incident_date">Date  </label>
                            </div>

                            <div class="form-group has-float-label">
                                   <input type="text" name="hr_med_incident_details" id="hr_med_incident_details" placeholder="Incident Details" class="form-control" value="{{ $medical->hr_med_incident_details }}"/>
                                <label  for="hr_med_incident_details"> Incident Details </label>
                            </div>

                            <div class="form-group has-float-label">
                                   <input name="hr_med_incident_doctors_name" type="text" id="hr_med_incident_doctors_name" placeholder="Doctors Name" class="form-control" value="{{ $medical->hr_med_incident_doctors_name }}"/>
                                <label  for="hr_med_incident_doctors_name"> Doctors Name </label>
                            </div>
                        
                        </div>

                        <div class="col-sm-4">

                            <div class="form-group has-float-label">
                                   <input type="text" name="hr_med_incident_doctors_recommendation" id="hr_med_incident_doctors_recommendation" placeholder="Doctors Recommendation" class="form-control" value="{{ $medical->hr_med_incident_doctors_recommendation }}"/>
                                <label class="col-sm-4 control-label no-padding-right no-padding-top" for="hr_med_incident_doctors_recommendation"> Recommendation </label>
                                
                            </div>


                            <div class="form-group ">
                                <label for="hr_med_incident_supporting_file">Supporting File <span>(pdf|doc|docx|jpg|jpeg|png) </span></label>
                                    @if(!empty($medical->hr_med_incident_supporting_file))
                                    
                                    <a href="{{ url($medical->hr_med_incident_supporting_file) }}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> View</a>
                                    @else
                                        <p class="text-danger">No file found!</p>
                                    @endif
                                    <input type="hidden" name="old_supporting_file" value="{{ $medical->hr_med_incident_supporting_file }}">

                                    <input type="file" name="hr_med_incident_supporting_file" id="hr_med_incident_supporting_file" >
                                    <span id="upload_error" class="red" style="display: none; font-size: 14px;">You can only upload <strong>docx, doc, pdf, jpeg, jpg or png</strong> type file(<1 MB).</span>
                            </div> 

                            <div class="form-group has-float-label">
                                   <input type="text" name="hr_med_incident_action" id="hr_med_incident_action" placeholder="Company's Action" class="form-control" value="{{ $medical->hr_med_incident_action }}"/>
                                <label  for="hr_med_incident_action"> Company's Action </label>
                            </div>

                            <div class="form-group has-float-label">
                                    <input name="hr_med_incident_allowance" type="text" id="hr_med_incident_allowance" placeholder="Allowance" class="form-control"  value="{{ $medical->hr_med_incident_allowance }}"/>
                                <label  for="hr_med_incident_allowance"> Allowance </label>
                            </div>  
                            <div class="form-group has-float-label">
                                <button class="btn btn-sm btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
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
        </div>
    </div>
</div>


 
<script type="text/javascript">


$(document).ready(function()
{   
    
    $('#hr_med_incident_supporting_file').on('change', function(){
        var x = $(this).val();
        var extension = x.substr(x.indexOf(".")+1, x.length-1);
        if( (extension.localeCompare("pdf")  == 0)||  
            (extension.localeCompare("docx") == 0)||   
            (extension.localeCompare("jpg")  == 0)||   
            (extension.localeCompare("jpeg") == 0)||  
            (extension.localeCompare("png")  == 0)  ){ 
                $('#upload_error').hide();
            }
        else{
            $('#upload_error').show();
            $(this).val('');
        }
    });

});
</script>
@endsection