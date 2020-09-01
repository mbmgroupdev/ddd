@extends('hr.layout')
@section('title', 'Manual Attendance')
@section('main-content')
@push('css')
    <style>
        .alert-warning{
            background-color: #fdf0d4;
            border-color: #fdf0d4;
            color: #c88a0a;
            padding: 10px;
        }
        .alert-warning .alert-icon{
            background-color: #e19b0b;
        }

        .alert-success {
            background-color: #d6e9c6;
            border-color: #fdf0d4;
            color: #000;
            padding: 10px;
        }
        .alert-success .alert-icon {
            background-color: #9ddc9f;
        }
        .alert-icon {
            width: 40px;
            height: 40px;
            display: inline-block;
            border-radius: 100%;
        }
        .alert-icon i {
            width: 40px;
            height: 40px;
            display: block;
            text-align: center;
            line-height: 40px;
            font-size: 20px;
            color: #FFF;
        }
        .notification-info {
            margin-left: 56px;
            margin-top: -40px;
            min-height: 40px;
        }
        .notification-meta {
            margin-bottom: 3px;
            padding-left: 0px;
            list-style: none outside none;
        }
        .notification-sender {
            color: #414147;
        }
        .process_section{margin-top: 22px;}
        .file-processing-log .panel-title {margin-top: 3px; margin-bottom: 3px;}
        .file-processing-log .panel-title a:first-child{font-size: 15px;}
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
                <li class="active">Manual Attendance</li>
            </ul><!-- /.breadcrumb -->
        </div>

            <div class="panel panel-info">
              <div class="panel-body">
                <div class="panel panel-success">
                    <div class="panel-heading"><h6>File Process</h6></div>
                    <div class="panel-body" style="background: #fbfbfb;">
                        <div class="msg" id="top-msg">
                            <div class="alert alert-warning ">
                                <span class="alert-icon"><i class="fa fa-refresh fa-spin"></i></span>
                                <div class="notification-info">
                                    <h5 class=""><b id="unitName"></b> : Attendance file is being processed! Please don't close this page</h5>

                                </div>
                            </div>
                        </div>
                        <div class="att_status_section" id="progressbar-box">
                            <div class="col-sm-1">
                                <img class="img-responsive" src="{{ asset('assets/img/loader-box.gif')}}" style="max-width: 64px;" alt="Synchronizing..." />
                            </div>
                            <div class="col-sm-11">
                                <div class="process_section" >
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                          0%
                                        </div>
                                    </div>
                                    <input type="hidden" name="unit" value="{{$unit}}">
                                    <input type="hidden" name="array_data_count" value="{{$arrayDataCount}}">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="accordion" class="accordion-style panel-group">
                    <div class="panel panel-info">
                        <div class="panel-heading file-processing-log">
                            <h2 class="panel-title">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#individual" aria-expanded="false">
                                    <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                    &nbsp;Log
                                </a>
                                <a class="btn btn-info btn-xx pull-right" onClick="printMe('log-section')">
                                    <i class="bigger-110 ace-icon fa fa-print"></i> Print
                                </a>
                            </h2>
                        </div>

                        <div class="panel-collapse collapse" id="individual" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body">
                                <div id="log-section"></div>
                            </div>
                        </div>
                    </div>
                </div>

              </div>
            </div>
    </div>
</div>

@push('js')
<script>
var url = "{{ url('/') }}";
var cur=0;
var fileDate = new Array();
var _token = $('input[name="_token"]').val();
$(document).ready(function() {
    var datas = @json($chunkValues);
    var device = @json($device);
    var dataLength = datas.length - 1;
    // console.log(device);
    var unit = $('input[name="unit"]').val();
    var unitName = '';
    if(unit == 1){
        unitName = 'MBM GARMENTS LTD.';
    }else if(unit == 2){
        unitName = 'CUTTING EDGE INDUSTRIES LTD.';
    }else if(unit == 3){
        unitName = 'ABSOLUTE QUALITYWEAR LTD.';
    }else if(unit == 8){
        unitName = 'CUTTING EDGE INDUSTRIES LTD. (WASHING PLANT)';
    }else if(unit == 4){
        unitName = 'MBM FASHION WEAR LTD.';
    }else if(unit == 5){
        unitName = 'SRT FASHIONS LTD.';
    }else if(unit == 1001){
        unitName = 'Head Office';
    }else{
        unitName = 'MBM GARMENTS LTD.';
    }
    $("#unitName").html(unitName);
    var per = (100 / datas.length);
    var i = 0;
    $.each(datas, function(len, val) {
        i++;
        setTimeout(function(){
            $.ajax({
                url: url+'/hr/timeattendance/attendance_process_wise_data',
                type: "post",
                data: {
                  _token : _token,
                  unit   : unit,
                  device : device,
                  getdata: val
                },
                success: function(response){
                    console.log(response);
                    if(response.status == 'success'){
                        cur = parseFloat(parseFloat(cur) + parseFloat(per)).toFixed(2);
                        if(cur<100){
                            $('#progress-bar').html(cur+'%');
                            $('#progress-bar').css({width: cur+'%'});
                            $('#progress-bar').attr('aria-valuenow', cur+'%');
                        }

                        if(response.msg.length > 0){
                            $.each(response.msg, function(ind, val) {
                                $("#log-section").prepend("<h6>"+val+"</h6>");
                            });
                        }

                        if(typeof response.date != "undefined"){
                            $.each(response.date, function (index, value) {
                                fileDate.indexOf(value) === -1 ? fileDate.push(value) : '';
                            });
                        }

                        if(len === dataLength){
                            // console.log('complete');
                            $("#progressbar-box").hide();
                            $("#top-msg").html('<div class="alert alert-success "><span class="alert-icon"><i class="fa fa-check"></i></span><div class="notification-info"><h5 class="">'+unitName+' Attendance file is processed.</h5><a href="'+url+'/hr/timeattendance/attendance_manual" class="btn btn-warning btn-xs"> <i class="fa fa-reply-all"></i> Back</a></div></div>');
                            if(unit != 1001){
                                absentCheck(unit, fileDate);
                            }
                        }
                    }else{
                        $("#log-section").prepend("<h6>"+response.result+"</h6>");
                    }
                },
                error: function(response) {
                    $("#log-section").prepend("<h6>"+response+"</h6>");
                }
            });
        }, 1000 * i);
    });
});
function absentCheck(unit, dates){
    $.ajax({
        url: url+'/hr/timeattendance/unit-wise-absent',
        type: "get",
        data: {
          unit : unit,
          fileDate : dates,
        },
        success: function(response){
            console.log(response);
        },
        error: function(response) {
            console.log(response);
        }
    });
}
function printMe(divName)
{
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write('<html><body style="font-size:5px;>');
    myWindow.document.write(document.getElementById(divName).innerHTML);
    myWindow.document.write('</html></body>');
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}
</script>
@endpush
@endsection
