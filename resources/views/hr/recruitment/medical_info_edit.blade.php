@extends('hr.layout')
@section('title', '')
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
                    <a href="#">Recruitment </a>
                </li>
                <li>
                    <a href="#">Operation </a>
                </li>
                <li class="active"> Medical Information</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Recruitment <small> <i class="ace-icon fa fa-angle-double-right"></i> Operation <i class="ace-icon fa fa-angle-double-right"></i> Medical Information</small>
                    <div class="btn-group pull-right">
                        <a href='{{ url("hr/recruitment/employee/show/$medical->med_as_id") }}'  class="btn btn-sm btn-success" title="Profile"><i class="glyphicon glyphicon-user"></i></a>
                    </div>
                </h1>
            </div>

            <div class="row">

                <!-- Display Erro/Success Message -->
                @include('inc/message')


                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                    </br>
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/recruitment/operation/medical_info_update') }}" enctype="multipart/form-data"> 
                         {{ csrf_field() }}
                         {{ Form::hidden('med_id', $medical->med_id) }}
                        <div class="col-sm-5">
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_as_id"> Associate's ID </label>
                            <div class="col-sm-8">
                                <input type="text" name="med_as_id" placeholder="Associate" class="col-xs-12" value="{{ $medical->med_as_id }}" readonly /> 

                            </div>
                        </div> 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_height"> Height <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" id="med_height" name="med_height" value="{{ $medical->med_height }}" data-validation=" required length" data-validation-length="1-50" placeholder="Height in Inch" class="col-xs-12"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_weight"> Weight <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" id="med_weight" name="med_weight" value="{{ $medical->med_weight }}" placeholder="Weight in Kg" class="col-xs-12" data-validation="required length" data-validation-length="1-50" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_tooth_str"> Tooth Structure </label>
                            <div class="col-sm-8">
                                <input type="text" id="med_tooth_str" name="med_tooth_str" value="{{ $medical->med_tooth_str }}" placeholder="Tooth Structure" class="col-xs-12" data-validation="length" data-validation-length="0-124" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_blood_group"> Blood Group <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                                <select id="med_blood_group" name="med_blood_group" class="col-xs-12" data-validation="required">
                                    @if(!empty($medical->med_blood_group))
                                    <option value="{{$medical->med_blood_group}}">{{$medical->med_blood_group}}</option>
                                    @endif
                                    <option value="">Select Blood Group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_ident_mark"> Identification Mark </label>
                            <div class="col-sm-8">
                                <textarea id="med_ident_mark" name="med_ident_mark" class="col-xs-12" placeholder="Identification Mark" data-validation="length" data-validation-length="0-256">{{ $medical->med_ident_mark }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_others"> Other </label>
                            <div class="col-sm-8">
                                <textarea id="med_others" name="med_others" class="col-xs-12" placeholder="Other" data-validation="length" data-validation-length="0-256"> {{ $medical->med_others }}</textarea>
                            </div>
                        </div>

                        </div>

                        <div class="col-sm-2"></div>

                        <div class="col-sm-5">

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_doct_comment"> Doctor's Comments <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                                <textarea id="med_doct_comment" name="med_doct_comment" class="col-xs-12" placeholder="Doctor's Comments" data-validation="required length" data-validation-length="1-256">{{ $medical->med_doct_comment }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_doct_conf_age"> Doctor's Age Confirmation <span style="color: red; vertical-align: text-top;">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" id="med_doct_conf_age" name="med_doct_conf_age" value="{{ $medical->med_doct_conf_age }}" placeholder="Doctor's Age Confirmation" class="col-xs-12" data-validation="required length" data-validation-length="1-128"/>
                            </div>
                        </div> 
 

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_signature">Signature <span>(jpg|jpeg|png)</span> </label>
                            <div class="col-sm-8">
                                @if(!empty($medical->med_signature))
                                <a href="{{ url($medical->med_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                    <i class="fa fa-eye"></i>
                                     View
                                </a>
                                <a href="{{ url($medical->med_signature) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                    <i class="fa fa-eye"></i>
                                     Download
                                </a>
                                @else
                                    <strong class="text-danger">No file found!</strong>
                                @endif
                                <input type="file" id="med_signature" name="med_signature" value="{{ $medical->med_signature }}" data-validation="mime size" data-validation-allowing="jpeg,png,jpg" data-validation-max-size="512kb" data-validation-error-msg-size="You can not upload images larger than 512kb" data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                                <span id="file_upload_error" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<512kb).</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_auth_signature">Authority Signature <span>(jpg|jpeg|png)</span> </label>
                            <div class="col-sm-8">
                                @if(!empty($medical->med_auth_signature))
                                <a href="{{ url($medical->med_auth_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                    <i class="fa fa-eye"></i>
                                     View
                                </a>
                                <a href="{{ url($medical->med_auth_signature) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                    <i class="fa fa-eye"></i>
                                     Download
                                </a>
                                @else
                                    <strong class="text-danger">No file found!</strong>
                                @endif
                                <input type="file" id="med_auth_signature" name="med_auth_signature" value="{{ $medical->med_auth_signature }}" data-validation="mime size" data-validation-allowing="jpeg,png,jpg" data-validation-max-size="512kb" data-validation-error-msg-size="You can not upload images larger than 512kb" data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                                <span id="file_upload_error2" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<512kb).</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="med_doct_signature">Doctor's Signature <span>(jpg|jpeg|png)</span> </label>
                            <div class="col-sm-8">
                                @if(!empty($medical->med_doct_signature))
                                <a href="{{ url($medical->med_doct_signature) }}" class="btn btn-xs btn-primary" target="_blank" title="View">
                                    <i class="fa fa-eye"></i>
                                     View
                                </a>
                                <a href="{{ url($medical->med_doct_signature) }}" class="btn btn-xs btn-success" target="_blank" download title="Download">
                                    <i class="fa fa-eye"></i>
                                     Download
                                </a>
                                @else
                                    <strong class="text-danger">No file found!</strong>
                                @endif
                                <input type="file" id="med_doct_signature" name="med_doct_signature" value="{{ $medical->med_doct_signature }}" data-validation="mime size" data-validation-allowing="jpeg,png,jpg" data-validation-max-size="512kb" data-validation-error-msg-size="You can not upload images larger than 512kb" data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                                <span id="file_upload_error3" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<512kb).</span>
                            </div>
                        </div>

                        </div>

                        <!-- /.row -->

                    <!-- PAGE CONTENT ENDS -->
                </div>

                <div class="col-sm-12">
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center">
                                <button name="approve" class="btn btn-sm btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Update
                                </button>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                <a href="{{ url('hr/recruitment/operation/medical_info_list')}}" name="reject" class="btn btn-sm btn-danger" type="submit">
                                    <i class="ace-icon fa fa-arrow-left bigger-110"></i> Cancel 
                                </a>
                            </div>
                        </div>
                </div>
                <!-- /.col -->
                </form>
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

    //file upload validation....
    $('#med_signature').on('change', function(){
        var fileExtension = ['jpeg','png','jpg'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#file_upload_error').show();
            $(this).val('');
        }
        else{
            $('#file_upload_error').hide();
        }
    });
    $('#med_auth_signature').on('change', function(){
        var fileExtension = ['jpeg','png','jpg'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#file_upload_error2').show();
            $(this).val('');
        }
        else{
            $('#file_upload_error2').hide();
        }
    });
    $('#med_doct_signature').on('change', function(){
        var fileExtension = ['jpeg','png','jpg'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#file_upload_error3').show();
            $(this).val('');
        }
        else{
            $('#file_upload_error3').hide();
        }
    }); 

});
</script>
@endsection