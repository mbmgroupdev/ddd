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
                    <a href="#">Employee</a>
                </li>
                <li class="active">Medical Incident</li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="panel"> 

            <div class="panel-heading">
                <h6>Medical Incident
                    <a href="{{url('hr/employee/medical_incident_list')}}" class="btn btn-sm btn-primary pull-right">List</a>
                </h6>
            </div>
            <div class="panel-body">

                {{ Form::open(['url'=>'hr/ess/medical_incident', 'method'=>'POST', 'files' => true, 'class'=>'form-horizontal']) }}
                    <div class="row">
                        
                    
                        <div class="col-sm-4">
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('hr_med_incident_as_id', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Associate\'s ID', 'id'=>'hr_med_incident_as_id', 'class'=> 'associates no-select form-control', 'required'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}  
                                <label  for="hr_med_incident_as_id"> Associate's ID  </label> 
                            </div>

                            <div class="form-group has-float-label has-required">
                                    <input name="hr_med_incident_as_name" type="text" id="hr_med_incident_as_name" placeholder="Associate's Name" class="form-control" required="required"  data-validation-length="3-64" data-validation-error-msg="The Associate's Name should contain only alphabet between 3-64 characters" readonly/>
                                <label  for="hr_med_incident_as_name"> Associate's Name</label>
                            </div> 

                            <div class="form-group has-float-label has-required">

                                    <input type="text" name="hr_med_incident_date" id="hr_med_incident_date" class="form-control datepicker" required="required" placeholder="Y-m-d" />
                                <label  for="hr_med_incident_date">Date  </label>
                            </div>

                            <div class="form-group has-float-label">
                                   <input type="text" name="hr_med_incident_details" id="hr_med_incident_details" placeholder="Incident Details" class="form-control" data-validation="length" data-validation-length="0-128" data-validation-error-msg="Incident Details should be between 0-128 characters"/>
                                <label  for="hr_med_incident_details"> Incident Details </label>
                            </div>

                            <div class="form-group has-float-label">
                                   <input name="hr_med_incident_doctors_name" type="text" id="hr_med_incident_doctors_name" placeholder="Doctors Name" class="form-control" data-validation="length custom"  data-validation-optional="true"  data-validation-length="0-128" data-validation-error-msg="Doctors Name be contain only alphabet between 0-64 characters"/>
                                <label  for="hr_med_incident_doctors_name"> Doctors Name </label>
                            </div>
                        
                        </div>

                        <div class="col-sm-4">

                            <div class="form-group has-float-label">
                                   <input type="text" name="hr_med_incident_doctors_recommendation" id="hr_med_incident_doctors_recommendation" placeholder="Doctors Recommendation" class="form-control" data-validation="length " data-validation-optional="true" data-validation-length="0-128" data-validation-error-msg="Doctors Recommendation contain alphanumeric value between 0-128 characters"/>
                                <label class="col-sm-4 control-label no-padding-right no-padding-top" for="hr_med_incident_doctors_recommendation"> Doctors Recommendation </label>
                                
                            </div>


                            <div class="form-group ">
                                <label for="hr_med_incident_supporting_file">Supporting File <span>(pdf|doc|docx|jpg|jpeg|png) </span></label>
                                
                                    <input type="file" name="hr_med_incident_supporting_file" id="hr_med_incident_supporting_file" data-validation="mime size" data-validation-allowing="docx, doc, pdf, jpg, png, jpeg" data-validation-max-size="1M"
                                    data-validation-error-msg-size="You can not upload file larger than 1MB" data-validation-error-msg-mime="You can only upload docx, doc, pdf, jpeg, jpg or png type file">
                                    <span id="upload_error" class="red" style="display: none; font-size: 14px;">You can only upload <strong>docx, doc, pdf, jpeg, jpg or png</strong> type file(<1 MB).</span>
                            </div> 

                            <div class="form-group has-float-label">
                                   <input type="text" name="hr_med_incident_action" id="hr_med_incident_action" placeholder="Company's Action" class="form-control" data-validation="custom length" data-validation-optional="true" data-validation-length="0-128"/>
                                <label  for="hr_med_incident_action"> Company's Action </label>
                            </div>

                            <div class="form-group has-float-label">
                                    <input name="hr_med_incident_allowance" type="text" id="hr_med_incident_allowance" placeholder="Allowance" class="form-control" data-validation="required length number" data-validation-optional="true" data-validation-length="1-11" data-validation-error-msg="Allowance should contain numeric value between 0-11 digits" />
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
        </div><!-- /.page-content -->
    </div>
</div>


 
<script type="text/javascript">
function drawNewBtn(associate_id)
{
    var url = "{{ url("") }}";
    var newUrl = "<div class=\"btn-group\">"+
        "<a href='"+url+'/hr/recruitment/employee/show/'+associate_id+"' target=\"_blank\" class=\"btn btn-sm btn-success\" title=\"Profile\"><i class=\"glyphicon glyphicon-user\"></i></a>"+ 
        "<a href='"+url+'/hr/recruitment/employee/edit/'+associate_id+"'  class=\"btn btn-sm btn-success\" title=\"Basic Info\"><i class=\"glyphicon glyphicon-bold\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/advance_info_edit/'+associate_id+"'  class=\"btn btn-sm btn-info\" title=\"Advance Info\"><i class=\"glyphicon  glyphicon-font\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/benefits?associate_id='+associate_id+"' class=\"btn btn-sm btn-primary\" title=\"Benefits\"><i class=\"fa fa-usd\"></i></a>"+
        "<a href='"+url+'/hr/ess/medical_incident?associate_id='+associate_id+"'  class=\"btn btn-sm btn-warning\" title=\"Medical Incident\"><i class=\"fa fa-stethoscope\"></i></a>"+
        "<a href='"+url+'/hr/operation/servicebook?associate_id='+associate_id+"' class=\"btn btn-sm btn-danger\" title=\"Service Book\"><i class=\"fa fa-book\"></i></a>"+
    "</div>"; 
    $("#newBtn").html(newUrl);
}
 

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
    var name = $("input[name=hr_med_incident_as_name]");
    var associate_id = '{{ request()->get("associate_id") }}';
    $(window).on("load", function(){
        if (associate_id) 
        {
            ajaxLoad(associate_id);
            drawNewBtn(associate_id);
        }
    });

    $('body').on('change', '.associates', function(){
        ajaxLoad($(this).val());
        drawNewBtn($(this).val());
    });

    function ajaxLoad(associate_id)
    {
        $.ajax({
            url: '{{ url("hr/associate") }}',
            dataType: 'json',
            data: {associate_id},
            success: function(data)
            {
                name.val(data.as_name);
            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
    }

    $('#hr_med_incident_supporting_file').on('change', function(){
        var x = $(this).val();
        var extension = x.substr(x.indexOf(".")+1, x.length-1);
        // console.log(extension);
        // var msg = "The Uploading File type is not allowed.\nPlease upload (pdf/doc/docx/jpg/jpeg/png) type file.";
        if( (extension.localeCompare("pdf")  == 0)||  
            (extension.localeCompare("docx") == 0)||   
            (extension.localeCompare("jpg")  == 0)||   
            (extension.localeCompare("jpeg") == 0)||  
            (extension.localeCompare("png")  == 0)  ){ 
                $('#upload_error').hide();
            }
        else{
            $('#upload_error').show();
            // alert(msg);
            $(this).val('');
        }
    });

});
</script>
@endsection