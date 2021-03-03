@extends('hr.layout')
@section('title', 'Medical Info '.$medical->med_as_id)
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
                    <a href="#">Employee </a>
                </li>
                <li class="active"> <a href="{{ url("hr/recruitment/employee/show/$medical->med_as_id") }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='View Profile' class="font-weight-bold">{{$medical->med_as_id}}</a></li>
                <li class="top-nav-btn">
                    @php 
                        $act_page = 'medical';  
                        $associate_id = $medical->med_as_id;
                    @endphp
                    @include('hr.common.emp_profile_pagination')
                </li>
            </ul><!-- /.breadcrumb -->
        </div>
        @include('inc/message')
        <div class="panel"> 
            <div class="panel-heading">
                <h6>Medical Information
                </h6>
            </div>

            <div class="panel-body">
                <form class="form-horizontal" role="form" method="post" action="{{ url('hr/recruitment/operation/medical_info_update') }}" enctype="multipart/form-data"> 
                    {{ csrf_field() }}
                    {{ Form::hidden('med_id', $medical->med_id) }}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group has-float-label has-required">
                                <input type="text" name="med_as_id" placeholder="Associate" class="form-control" value="{{ $medical->med_as_id }}" readonly /> 
                                <label for="med_as_id"> Associate's ID </label>
                            </div> 
                            <div class="form-group has-required has-float-label">
                                
                                <input type="text" id="med_height" name="med_height" value="{{ $medical->med_height }}" required=" required"  placeholder="Height in Inch" class="form-control"/>
                                <label for="med_height"> Height </label>
                            </div>

                            <div class="form-group has-required has-float-label">
                                
                                <input type="text" id="med_weight" name="med_weight" value="{{ $medical->med_weight }}" placeholder="Weight in Kg" class="form-control" required="required length" required-length="1-50" />
                                <label for="med_weight"> Weight </label>
                            </div>

                            <div class="form-group has-float-label">
                                
                                <input type="text" id="med_tooth_str" name="med_tooth_str" value="{{ $medical->med_tooth_str }}" placeholder="Tooth Structure" class="form-control"  />
                                <label for="med_tooth_str"> Tooth Structure </label>
                            </div>

                            <div class="form-group has-float-label select-search-group">
                                
                                
                                <select id="med_blood_group" name="med_blood_group" class="form-control" required="required">
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
                                <label for="med_blood_group"> Blood Group </label>
                            </div>


                            <div class="form-group has-float-label">
                                
                                <textarea id="med_ident_mark" name="med_ident_mark" class="form-control" placeholder="Identification Mark" >{{ $medical->med_ident_mark }}</textarea>

                                <label for="med_ident_mark"> Identification Mark </label>
                            </div>

                            <div class="form-group has-float-label">
                                
                                
                                <textarea id="med_others" name="med_others" class="form-control" placeholder="Other" > {{ $medical->med_others }}</textarea>
                                <label for="med_others"> Other </label>
                            </div>

                        </div>
                        <div class="col-sm-4">
                            <div class="form-group has-float-label">
                                
                                
                                <textarea id="med_doct_comment" name="med_doct_comment" class="form-control" placeholder="Doctor's Comments" required="required" required-length="1-256">{{ $medical->med_doct_comment }}</textarea>
                                <label for="med_doct_comment"> Doctor's Comments </label>
                            </div>

                            <div class="form-group has-float-label">
                                <label for="med_doct_conf_age"> Doctor's Age Confirmation </label>
                                
                                <input type="text" id="med_doct_conf_age" name="med_doct_conf_age" value="{{ $medical->med_doct_conf_age }}" placeholder="Doctor's Age Confirmation" class="form-control" required="required" required-length="1-128"/>
                            </div> 
     

                            <div class="form-group">
                                <label for="med_signature">Signature <span>(jpg|jpeg|png)</span> </label> <br>
                                
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
                                        <strong class="text-danger">No file found!</strong><br>
                                    @endif
                                    <input type="file" id="med_signature" name="med_signature" value="{{ $medical->med_signature }}" >
                                    <span id="file_upload_error" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<512kb).</span>
                            </div>

                            <div class="form-group">
                                <label for="med_auth_signature">Authority Signature <span>(jpg|jpeg|png)</span> </label><br>
                                
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
                                        <strong class="text-danger">No file found!</strong><br>
                                    @endif
                                    <input type="file" id="med_auth_signature" name="med_auth_signature" value="{{ $medical->med_auth_signature }}" >
                                    <span id="file_upload_error2" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<512kb).</span>
                            </div>

                            <div class="form-group">
                                <label for="med_doct_signature">Doctor's Signature <span>(jpg|jpeg|png)</span> </label><br>
                                
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
                                        <strong class="text-danger">No file found!</strong><br>
                                    @endif
                                    <input type="file" id="med_doct_signature" name="med_doct_signature" value="{{ $medical->med_doct_signature }}" >
                                    <span id="file_upload_error3" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<512kb).</span>
                            </div>

                            <div class="form-group">
                                <button name="approve" class="btn btn-primary pull-right" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Update
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                </form>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function()
{   
    

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
@endpush
@endsection