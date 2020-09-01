@extends('hr.layout')
@section('title', 'Medical Incident')
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
                    <a href="#">Recruitment</a>
                </li>
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active">Medical Incident</li>
            </ul><!-- /.breadcrumb --> 
        </div>
        <div class="page-content"> 
            <div class="page-header">
                <h1>Recruitment<small> <i class="ace-icon fa fa-angle-double-right"></i> Operation  <i class="ace-icon fa fa-angle-double-right"></i> Medical Incident</small></h1>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->

                    <!-- Display Erro/Success Message -->
                    @include('inc/message')

                    {{ Form::open(['url'=>'hr/ess/medical_incident_update', 'method'=>'POST', 'files' => true, 'class'=>'form-horizontal']) }}
                        <input type="hidden" name="id" value="{{ $medical->id }}">
                        
                        <div class="col-sm-5">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_as_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8"> 
                                <input name="hr_med_incident_as_id" type="text" id="hr_med_incident_as_id" placeholder="Associate's Name" class="col-xs-12" data-validation="required length custom"   data-validation-length="3-64" data-validation-error-msg="The Associate's Name should contain only alphabet between 3-64 characters" readonly value="{{ $medical->hr_med_incident_as_id }}" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_as_name"> Associate's Name <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                                <input name="hr_med_incident_as_name" type="text" id="hr_med_incident_as_name" placeholder="Associate's Name" class="col-xs-12" data-validation="required length custom"   data-validation-length="3-64" data-validation-error-msg="The Associate's Name should contain only alphabet between 3-64 characters" readonly  value="{{ $medical->hr_med_incident_as_name }}"/>
                            </div>
                        </div> 

                        <div class="form-group">

                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_date">Date <span style="color: red; vertical-align: top;">&#42;</span> </label>
                            <div class="col-sm-8">
                                <input type="date" name="hr_med_incident_date" id="hr_med_incident_date" class="col-xs-12" data-validation="required"  value="{{ $medical->hr_med_incident_date }}"/>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_details"> Incident Details <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                               <input type="text" name="hr_med_incident_details" id="hr_med_incident_details" placeholder="Incident Details" class="col-xs-12" data-validation="length" data-validation-length="0-128" data-validation-error-msg="Incident Details should be between 0-128 characters"  value="{{ $medical->hr_med_incident_details }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_doctors_name"> Doctors Name <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                               <input name="hr_med_incident_doctors_name" type="text" id="hr_med_incident_doctors_name" placeholder="Doctors Name" class="col-xs-12" data-validation="length custom" data-validation-optional="true"  data-validation-length="0-128" data-validation-error-msg="Doctors Name be contain only alphabet between 0-64 characters"  value="{{ $medical->hr_med_incident_doctors_name }}"/>
                            </div>
                        </div>
                        
                        </div>
                        <div class="col-sm-2"></div>

                        <div class="col-sm-5">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_doctors_recommendation"> Doctors Recommendation <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                               <input type="text" name="hr_med_incident_doctors_recommendation" id="hr_med_incident_doctors_recommendation" placeholder="Doctors Recommendation" class="col-xs-12" data-validation="length " data-validation-optional="true" data-validation-length="0-128" data-validation-error-msg="Doctors Recommendation contain alphanumeric value between 0-128 characters"  value="{{ $medical->hr_med_incident_doctors_recommendation }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_supporting_file">Supporting File <span>(pdf|doc|docx|jpg|jpeg|png)</span></label>
                            <div class="col-sm-8">
                                @if(!empty($medical->hr_med_incident_supporting_file))
                                <a href="{{ url($medical->hr_med_incident_supporting_file) }}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> View</a>
                                @else
                                    <strong class="text-danger">No file found!</strong>
                                @endif
                                <input type="hidden" name="old_supporting_file" value="{{ $medical->hr_med_incident_supporting_file }}">
                                <input type="file" name="hr_med_incident_supporting_file" id="hr_med_incident_supporting_file" data-validation="mime size" data-validation-allowing="docx,doc,pdf,jpeg,png,jpg" data-validation-max-size="1M"
                                data-validation-error-msg-size="You can not upload images larger than 512kb" data-validation-error-msg-mime="You can only upload docx, doc, pdf, jpeg, jpg or png type file">
                                <span id="file_upload_error" class="red" style="display: none; font-size: 13px;">Only <strong>docx, doc, pdf, jpeg, jpg or png </strong>type file supported(<1MB).</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_action"> Company's Action <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                               <input type="text" name="hr_med_incident_action" id="hr_med_incident_action" placeholder="Company's Action" class="col-xs-12" data-validation="custom length"  data-validation-optional="true" data-validation-length="0-128" data-validation-error-msg="Company's Action should contain alphanumeric value between 0-128 characters"  value="{{ $medical->hr_med_incident_action }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="hr_med_incident_allowance"> Allowance <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                                <input name="hr_med_incident_allowance" type="text" id="hr_med_incident_allowance" placeholder="Allowance" class="col-xs-12" data-validation="required length number" data-validation-optional="true" data-validation-length="1-11" data-validation-error-msg="Allowance should contain numeric value between 0-11 digits"  value="{{ $medical->hr_med_incident_allowance }}"/>
                            </div>
                        </div>
                        </div>

                    <!-- /.row --> 
                  

                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
                <div class="col-sm-12">
                         <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center">
                                <button class="btn btn-sm btn-success" type="submit">
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
            </div>
        </div><!-- /.page-content -->
    </div>
</div>


 
<script type="text/javascript">
$(document).ready(function()
{   
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
  

    // retrive all information 
    var name         = $("input[name=hr_med_incident_as_name]");
    $('body').on('change', '.associates', function(){
        $.ajax({
            url: '{{ url("hr/associate") }}',
            dataType: 'json',
            data: {associate_id: $(this).val()},
            success: function(data)
            {
                name.val(data.as_name);
            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
    });

    //file upload validation....
    $('#hr_med_incident_supporting_file').on('change', function(){
        var fileExtension = ['docx','doc','pdf','jpeg','png','jpg'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#file_upload_error').show();
            $(this).val('');
        }
        else{
            $('#file_upload_error').hide();
        }
    });

});
</script>
@endsection