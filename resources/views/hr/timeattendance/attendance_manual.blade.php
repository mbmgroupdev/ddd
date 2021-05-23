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
        .alert-icon { width: 30px; height: 30px; display: inline-block;border-radius: 100%;}
        .alert-icon i { width: 30px; height: 30px; display: block; text-align: center; line-height: 30px; font-size: 20px; color: #FFF;}
        .fa-info-circle:before { content: "\f05a";}
        .alert-warning .alert-icon { background-color: #f16d2f;}
        .notification-info { margin-left: 10px;}
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
        <div class="col-xs-12">
            <!-- Display Erro/Success Message -->
            @include('inc.notify')
            @php
                if(\Session::has('success')) {

                    \Session::forget('success');
                }
            @endphp
        </div>
        <div id="accordion" class="accordion-style panel-group">
            <div class="panel panel-info">
                
                <div class="panel-heading file-section">
                    <h1 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#fill-upload">
                            <i data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right" class="ace-icon fa fa-angle-down bigger-110"></i>
                            &nbsp; Bulk Upload
                        </a>

                    </h1>
                </div>

                <div class="panel-collapse in collapse show" id="fill-upload">
                    <div class="panel-body">
                            
                        <div class="msg row justify-content-center" id="top-msg">
                            <div class="alert alert-warning col-4">
                                <span class="alert-icon"><i class="fa fa-info-circle"></i></span>
                                <div class="notification-info">
                                    Before File Upload, define holiday and assign shift roster.  <br>
                                    <a target="_blank" href="{{ url('hr/operation/shift_assign')}}" class="btn  btn-primary">Shift Assign</a>
                                    <a target="_blank" href="{{ url('hr/operation/holiday-roster')}}" class="btn  btn-primary">Define Holiday </a>
                                </div>
                            </div>
                        </div>
                        
                        {{ Form::open(['url'=>'hr/timeattendance/attendance_manual/import', 'files' => true,  'class'=>'form-horizontal needs-validation form' , "novalidate" => "novalidate"]) }}
                            <div class="row mt-5 justify-content-center">
                                <div class="col-4">
                                    <div class="form-group has-float-label select-search-group has-required">
                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'unit', 'class'=> 'form-control', 'required'=>'required']) }}  
                                        <label for="unit"> Unit Name </label>
                                        <div class="invalid-feedback">
                                          Please select unit!
                                       </div>
                                    </div>
                                    
                                    <div class="form-group has-float-label select-search-group" id="choose-device" style="display:none;">
                                        <div class="div">
                                            
                                            {{ Form::select('device', ['1' => 'Old', '2' => 'New'], null, ['placeholder'=>'Select Unit Device', 'id'=>'device', 'class'=> 'form-control' ]) }}  
                                        </div>
                                        <label  for="device"> Select Device  </label>
                                        <div class="invalid-feedback" role="alert">
                                            Select a device
                                        </div>
                                    </div>
                                    
                                    <div class="form-group has-required file-zone">
                                        <label  for="file"> File </label>
                                        <input type="file" name="file" class="file-type-validation" data-file-allow='["csv", "txt", "xlsx", "xls"]' autocomplete="off" required />
                                        <div class="invalid-feedback" role="alert">
                                            <strong>Select a file</strong>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" id="upload" type="button">
                                            <i class="fa fa-check bigger-110"></i> Upload
                                        </button>
                                        
                                    </div> 
                                </div>
                            </div>
                            
                        
                        {{ Form::close() }}
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
                        <form role="form" method="post" action="{{ url('hr/operation/attendance-rollback') }}" id="searchform" class="form-horizontal needs-validation form" novalidate >
                            {{ csrf_field() }} 
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'id'=>'rollback-unit', 'style'=>'width:100%;', 'required'=>'required']) }}
                                        <label for="rollback-unit"> Unit </label>
                                    </div>
                                </div>
                                <div class="check-date col-6" id="rollback-date-content" style="display: none">
                                    <div class="row">
                                        <div class="col-6">
                                            
                                            <div class="form-group has-required has-float-label">
                                                <input type="text" name="day" class="form-control" id="last-day" value="" required readonly>
                                                <label for="month_number">Day </label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-6">
                                            
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary"
                                                style=" " ><span class="glyphicon glyphicon-pencil"></span>
                                                Process</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="rollback-content-loader">
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    if (sessionStorage.getItem("msg")!=null) {
        $('#msg').show().html(sessionStorage.getItem("msg"));
        sessionStorage.removeItem("msg");
    };

    $('#unit').on('change',function(e){
        var unit =  e.target.value;
        if(unit == 3 || unit == 1){
            $("#choose-device").show();
        }else{
            $("#choose-device").hide();
        }

    });



    // rollback process
    $(document).on('change',"#rollback-unit", function(e){
        var unit =  $(this).val();
        if(!(unit)){
            $("#rollback-date-content").hide();
            $("#rollback-content-loader").html("<p class='text-center text-red'>Please select unit</p>").show();
        }else{
            var loader = '<img src=\'{{ asset("assets/img/loader-box.gif")}}\' class="center-loader">';
            $("#rollback-date-content").hide();
            $("#rollback-content-loader").show().html(loader);
            $.ajax({
                url : "{{ url('/hr/operation/attendance-rollback-get-date')}}",
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
