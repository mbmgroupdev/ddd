@extends('hr.layout')
@section('title', 'Job Card')
@section('main-content')



<div class="main-content">
    <style type="text/css">
    @media print {
        #brand-head{display: none !important;}
    }
</style>
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li>
                <li>
                    <a href="#"> Operations </a>
                </li>
                <li class="active"> Job Card </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <form role="form" method="get" action="{{ url('hr/operation/job_card') }}" class="attendanceReport" id="attendanceReport">
                    <div class="col-sm-10">
                        <div class="col-sm-4" style="padding-bottom: 10px;">
                            {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'data-validation'=>'required']) }}
                        </div>

                        <div class="col-sm-3" style="padding-bottom: 10px;">
                            <input type="text" name="month" id="month" class="monthpicker col-xs-12" value="{{ Request::has('month')==true?Request::get('month'):date('F') }}" data-validation="required" placeholder="Month" autocomplete="off" style="height: 33px;" />
                        </div>

                        <div class="col-sm-3" style="padding-bottom: 10px;">
                            <input type="text" name="year" id="year" class="yearpicker col-xs-12" value="{{ Request::has('year')==true?Request::get('year'):date('Y') }}" data-validation="required" placeholder="Year" autocomplete="off" style="height: 33px;"/>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                            @if (!empty(request()->associate)
                            && !empty(request()->month)
                            && !empty(request()->year)
                            )
                            <button type="button" onClick="printMe1('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                <i class="fa fa-print"></i>
                            </button>
                            <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel">
                                <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                           </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-sm-12" id="PrintArea" style="padding-left: 24px;">
                    <!-- PAGE CONTENT BEGINS -->
                @if(isset($info))
                    @php
                        $year  = request()->year;
                        $monthName = request()->month;
                    @endphp

                    <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto;border:1px solid #ccc">
                        <div class="page-header" id="brand-head" style="border-bottom:2px double #666; text-align: center;">
                            @php
                            $lastMonth = date('m',strtotime("-1 month"));
                            $thisMonth = date('m', strtotime(request()->month));
                            $number = salary_lock_date();
                            $lockDate = Date('Y-m')."-".sprintf('%02d', $number);
                            @endphp

                            @if(($lastMonth == $thisMonth && $lockDate> date('Y-m-d'))|| $thisMonth == date('m'))
                            @hasanyrole("super user|user type 2|advance user 2|power user 2|power user 3")
                                <div class="btn-group pull-right">
                                    <a  href={{url("hr/timeattendance/attendance_bulk_manual?associate=$info->associate&&month=$monthName&year=$year")}} target="_blank" data-tooltip="Edit Attendance Manual" data-tooltip-location="top" class="btn btn-sm btn-info"  style="border-radius: 2px !important; padding: 4px; "><i class="fa fa-edit bigger-120"></i></a>
                                </div>
                            @endhasanyrole
                            @endif
                            <h3 style="margin:4px 10px">{{ $info->unit }}</h3>
                            <h5 style="margin:4px 10px">Job Card Report</h5>

                            <h5 style="margin:4px 10px">For the month of {{ request()->month }} - {{ request()->year }}</h5>
                        </div>
                        <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
                            <tr>
                                <th style="width:35%">
                                   <p style="margin:0;padding:4px 10px"><strong>ID </strong> # {{ $info->associate }}</p>
                                   <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ $info->name }}</p>
                                   <p style="margin:0;padding:4px 10px"><strong>DOJ </strong>: {{ date("d-m-Y", strtotime($info->doj)) }}</p>
                                </th>
                                <th>
                                   <p style="margin:0;padding:4px 10px"><strong>Section </strong>: {{ $info->section }} </p>
                                   <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ $info->designation }} </p>
                                </th>
                                <th>
                                   <p style="margin:0;padding:4px 10px"><strong>Total Present </strong>: <b >{{ $info->present }}</b> </p>
                                   <p style="margin:0;padding:4px 10px"><strong>Total Absent </strong>: <b >{{ $info->absent }}</b></p>
                                   <p style="margin:0;padding:4px 10px"><strong>Total Ot </strong>: <b>{{number_to_time_format($info->ot_hour)}}</b> </p>
                                </th>
                            </tr>
                        </table>

                        <table class="table" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;"  cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                <tr>
                                    <th width="20%">Date</th>
                                    <th width="20%">Attendance Status</th>
                                    <th width="20%">Floor</th>
                                    <th width="20%">Line</th>
                                    <th width="30%">In Time</th>
                                    <th width="30%">Out Time</th>
                                    <th width="30%">OT Hour</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $value)
                                <tr>
                                    <td>
                                        {{ $value['date'] }}
                                        @if($joinExist)
                                            @if($value['date'] == $info->doj)
                                                <span class="label label-success arrowed-right arrowed-in pull-right">Join</span>
                                            @endif
                                        @endif
                                        @if($leftExist)
                                            @if($value['date'] == $info->as_status_date)
                                                <span class="label label-warning arrowed-right arrowed-in pull-right">
                                                    @php
                                                        $flag = '';
                                                        if($info->as_status === 0) {
                                                            $flag = 'Delete';
                                                        } else if($info->as_status === 2) {
                                                            $flag = 'Resign';
                                                        } else if($info->as_status === 3) {
                                                            $flag = 'Terminate';
                                                        } else if($info->as_status === 4) {
                                                            $flag = 'Suspend';
                                                        } else if($info->as_status === 5) {
                                                            $flag = 'Left';
                                                        }
                                                        echo $flag;
                                                    @endphp
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                    {{ $value['present_status'] }}

                                    @if($value['late_status']==1)
                                        <span style="height: 17px;float:right;" class="label label-warning pull-right">Late</span>
                                    @endif
                                    @if($value['remarks']== 'HD')
                                        <span style="height: 17px;float:right;" class="label label-danger pull-right">Half Day @if($value['late_status']==1) , @endif</span>

                                    @endif

                                    <span style="height: 17px;float:right;cursor:pointer;" class="label label-success pull-right" data-tooltip="{{$value['outside_msg']}}" data-tooltip-location="top">{{$value['outside']}}</span>

                                    </td>
                                    <td>{{ $value['floor'] }}</td>
                                    <td>{{ $value['line'] }}</td>
                                    <td>{{!empty($value['in_time'])?$value['in_time']:null}}</td>
                                    <td>{{!empty($value['out_time'])?$value['out_time']:null}}</td>
                                    <td>
                                    @if($info->as_ot==1)
                                        {{number_to_time_format($value['overtime_time'])}}
                                    @endif
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>



                            <tfoot style="border-top:2px double #999">
                                <input type="hidden" id="present" value="">
                                <input type="hidden" id="absent" value="">
                                <tr>
                                    <th style="text-align:right">Total present</th>
                                    <th>{{ $info->present }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align:right">Total Over Time</th>
                                    <th>
                                    @if($info->as_ot==1)
                                        {{number_to_time_format($info->ot_hour)}}
                                        <input type="hidden" id="ot" value="0">
                                    @endif
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
                </div>
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<script type="text/javascript">
function printMe1(divName)
{
    var myWindow=window.open('','','width=800,height=800');
    myWindow.document.write('<style>.page-header{text-align:center;}</style>');
    myWindow.document.write(document.getElementById(divName).innerHTML);
    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

$(document).ready(function(){
    //total status show
    $('#total-present').html($('#present').val());
    $('#total-absent').html($('#absent').val());
    $('#total-ot').html($('#ot').val());
    //select 2 check
    function formatState (state) {
     //console.log(state.element);
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span><img /> <span></span></span>'
        );

        var targetName = state.text;
        $state.find("span").text(targetName);
        return $state;
    };
    $('select.associates').select2({
        templateSelection:formatState,
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
                        var oCode = '';
                        if(item.as_oracle_code !== null){
                            oCode = item.as_oracle_code + ' - ';
                        }
                        return {
                            text: oCode + item.associate_name,
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    })
                };
          },
          cache: true
        }
    });
//     function urlExists(testUrl) {
//  var http = jQuery.ajax({
//     type:"HEAD",
//     url: testUrl,
//     async: false
//   })
//   return http.status;
//       // this will return 200 on success, and 0 or negative value on error
// }
// excel conversion -->
   $('#excel').click(function(){
    var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
    location.href=url
    return false
      })

});
function attLocation(loc){
    window.location = loc;
}


</script>
@endsection
