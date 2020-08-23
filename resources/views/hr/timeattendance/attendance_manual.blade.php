@extends('hr.layout')
@section('title', 'Attendance Upload')
@section('main-content')

@push('css')
    <style>
        h4.widget-header {min-height: 29px;}
        .bulk_upload_section{height: 296px;}
        .bulk_upload_section .panel-success{height: auto;}
        .form-actions {margin-bottom: 0px; margin-top: 0px; padding: 0px 25px 0px;background-color: unset; border-top: unset;}
        .bulk_form_top{margin-bottom: 20px;}
        .select2{width: 100% !important;}
        .alert-icon { width: 40px; height: 40px; display: inline-block;border-radius: 100%;}
        .alert-icon i { width: 40px; height: 40px; display: block; text-align: center; line-height: 40px; font-size: 20px; color: #FFF;}
        .fa-info-circle:before { content: "\f05a";}
        .alert-warning .alert-icon { background-color: #e19b0b;}
        .notification-info { margin-left: 56px; margin-top: -40px;}
        a{cursor: pointer;}
        .alert {padding: 8px 15px;}
        .att_rollback .panel-title {margin-top: 3px; margin-bottom: 3px;}
        .att_rollback .panel-title a{font-size: 15px; display: block;}
        .panel-group { margin-bottom: 5px;}
        h3.smaller {font-size: 13px;}
        .header {margin-top: 0;}
        .file-section .panel-title a {
            font-size: 15px;
            display: block;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Time & Attendance</a>
                </li>
                <li class="active">Attendance Upload</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-xs-12">
                    <!-- Display Erro/Success Message -->
                    @include('inc.notify')
                    @php
                        if(\Session::has('success')) {

                            \Session::forget('success');
                        }
                    @endphp
                </div>
                <br>
                <div id="accordion" class="accordion-style panel-group">
                    <div class="panel panel-info">
                        
                        <div class="panel-heading file-section">
                            <h1 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#fill-upload">
                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                    &nbsp; Bulk Upload
                                </a>
                            </h1>
                        </div>

                        <div class="panel-collapse collapse in" id="fill-upload">
                            <div class="panel-body">
                                <div class="col-sm-offset-3 col-sm-6">
                                    
                                    <div class="msg" id="top-msg">
                                        <div class="alert alert-warning ">
                                            <span class="alert-icon"><i class="fa fa-info-circle"></i></span>
                                            <div class="notification-info">
                                                <h6 class="">Before File Upload, Confirm Shift is Assigned & Holiday is Defined.  </h6>
                                                <a target="_blank" href="{{ URL::to('/hr/timeattendance/shift_assign')}}" class="btn btn-xs btn-info">Shift assign</a>
                                                <a target="_blank" href="{{ URL::to('/hr/timeattendance/operation/yearly_holidays/create')}}" class="btn btn-xs btn-info">Holiday define</a>
                                                

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class="col-sm-offset-3 col-sm-6">
                                    <div id="msg" class="alert alert-block alert-success" style="display:none;"> </div>

                                    {{ Form::open(['url'=>'hr/timeattendance/attendance_manual/import', 'files' => true,  'class'=>'form-horizontal']) }}
                                    
                                        <div class="form-group required bulk_form_top">
                                            <label class="col-sm-4 control-label" for="unit"> Unit Name </label>
                                            <div class="col-sm-8"> 
                                                {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'unit', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}  
                                            </div>
                                        </div>
                                        <div class="form-group required bulk_form_top" id="choose-device" style="display: none;">
                                            <label class="col-sm-4 control-label" for="device"> Select Device  </label>
                                            <div class="col-sm-8"> 
                                                {{ Form::select('device', ['1' => 'Old', '2' => 'Automation (New)'], null, ['placeholder'=>'Select AQL Unit Device', 'id'=>'device', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Device field is required']) }}  
                                            </div>
                                        </div>

                                        <div class="form-group required">
                                            <label class="col-sm-4 control-label no-padding-right no-padding-top" for="file"> File <br><span>(only <strong>.csv</strong> or <strong>.txt</strong> or <strong>.xls</strong> file supported)</span></label>
                                            <div class="col-sm-8">
                                                <input type="file" name="file" id="file" class="col-xs-12 no-padding-left" data-validation-allowing="csv, txt" autocomplete="off" required />
                                                <span id="file_upload_error" class="red" style="display: none; font-size: 14px;">only <strong>.csv</strong> or <strong>.txt</strong> or <strong>.xls</strong> file supported.</span>
                                            </div>
                                        </div> 
                                    
                                        <div class="clearfix form-actions bulk_form_button">
                                            <div class="col-sm-offset-4 col-sm-8 "> 
                                                <button class="btn btn-xs" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                </button>
                                                &nbsp; &nbsp; &nbsp;
                                                <button type="submit" class="btn btn-info btn-xs" id="upload" type="button">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> Upload
                                                </button>
                                                
                                            </div>
                                        </div>
                                    
                                
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading file-section">
                            <h1 class="panel-title">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#file-rollback" aria-expanded="false">
                                    <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                    &nbsp;Attendance file rollback
                                </a>
                            </h1>
                        </div>

                        <div class="panel-collapse collapse" id="file-rollback" aria-expanded="false">
                            <div class="panel-body">
                                @php
                                    $today = date('Y-m-d');
                                    $yesterday = date('Y-m-d',strtotime("-1 days"));
                                    $twoDaysAgo = date('Y-m-d',strtotime("-2 days"));
                                @endphp
                                <div class="form-horizontal" id="rollback-content-content">
                                    <div class="col-sm-offset-3 col-sm-6 no-padding-left">
                                        <form role="form" method="post" action="{{ url('hr/operation/attendance-rollback') }}" id="searchform" >
                                            {{ csrf_field() }} 
                                            <div class="panel panel-info">
                                                <div class="panel-body">
                                                    <h3 class="header smaller lighter green">
                                                        <i class="ace-icon fa fa-bullhorn"></i>
                                                        All <span class="text-red" style="vertical-align: top;">&#42;</span>  required
                                                    </h3>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label" for="unit1"> Unit <span class="text-red" style="vertical-align: top;">&#42;</span> : </label>
                                                        <div class="col-sm-9">
                                                            {{ Form::select('unit', ['1' => 'MBM GARMENTS LTD.', '2' => 'CUTTING EDGE INDUSTRIES LTD.', '8' => 'CUTTING EDGE INDUSTRIES LTD. (WASHING PLANT)', '3' => 'ABSOLUTE QUALITYWEAR LTD.'], null, ['placeholder'=>'Select Unit', 'id'=>'rollback-unit', 'style'=>'width:100%;', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required', 'required']) }}
                                                            <span class="text-red" id="error_unit_s"></span>
                                                        </div>
                                                    </div>
                                                    <div class="check-date" id="rollback-date-content" style="display: none">
                                                        <div class="form-group">
                                                            <label class="col-sm-3 control-label no-padding-right align-left" for="month_number">Day <span class="text-red" style="vertical-align: top;">&#42;</span> :</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="day" class="form-control" id="last-day" value="" required readonly>
                                                                <!-- {{ Form::select('day', [$today => 'Today', $yesterday => 'Yesterday', $twoDaysAgo => 'Day Before Yesterday'], $today, ['placeholder'=>'Select day', 'id'=>'month_number', 'required', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Day field is required', 'required']) }} -->
                                                            </div>
                                                            
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <div class="col-sm-offset-3 col-sm-6 ">
                                                                <button type="submit" class="btn btn-primary btn-xs"
                                                                style=" " ><span class="glyphicon glyphicon-pencil"></span>&nbsp
                                                                Process</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="rollback-content-loader">
                                                        <img src='{{ asset("assets/img/loader-box.gif")}}' class="center-loader">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script>
if (sessionStorage.getItem("msg")!=null) {
    $('#msg').show().html(sessionStorage.getItem("msg"));
    sessionStorage.removeItem("msg");
};

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

        var file=$("#upload");
        file.on('click',function ()
         {
            if (!stringEndsWithValidExtension($("#file").val(), [".csv", ".txt", "txt", ".xls"], false)) {
                alert("Only allowed file types are .csv and .txt and .xls");
                return false;
            }
            return true;
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
   
        $("#file").change(function () {
            var fileExtension = ['csv','txt', 'xls'];
            var f_name = $(this).val();
            // if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            if ($.inArray( f_name.substr(f_name.length-3, f_name.length-1).toLowerCase(), fileExtension) == -1) {
                $('#file_upload_error').show();
                $(this).val('');
            }
            else{
                    $('#file_upload_error').hide();
                }
        });
    });
</script>

<script>
    $('#unit').on('change',function(e){
        var unit =  e.target.value;
        if(unit == 3){
            $("#choose-device").show();
        }else{
            $("#choose-device").hide();
        }

    });
    // rollback process
    $("#rollback-unit").on('change', function(e){
        var unit =  e.target.value;
        if(!(unit)){
            $("#rollback-date-content").hide();
            $("#rollback-content-loader").html("<p class='text-center text-red'>Please select unit</p>").show();
        }else{
            var loader = '<img src=\'{{ asset("assets/img/loader-box.gif")}}\' class="center-loader">';
            $("#rollback-date-content").hide();
            $("#rollback-content-loader").show().html(loader);
            $.ajax({
                url : "{{ URL::to('/hr/operation/attendance-rollback-get-date')}}",
                type: 'GET',
                data: {
                    unit: unit
                },
                success: function(response)
                {
                    console.log(response);
                    if(response.type === 'success'){
                        $("#rollback-date-content").show();
                        $("#rollback-content-loader").hide();
                        $("#last-day").val(response.value);
                    }else{
                        $("#rollback-date-content").hide();
                        $("#rollback-content-loader").html(response.message).show();
                    }
                },
                error: function(response)
                {
                    console.log(response)
                }
            });
        }
    });
    
</script>
@endpush
@endsection
