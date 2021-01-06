@extends('hr.layout')
@section('title', 'Job Card Edit')
@section('main-content')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
    <style>
        .bootstarp-datetimepicker-widget{display: none !important;}
        td input{
            width: 110px !important;
        }
        table.table-head th {
            vertical-align: middle;
        }
        .form-control:disabled, .form-control[readonly] {
            background-color: #abcfd3;
        }
        .shift_link{
            font-weight: bold;
            color: blue;
            cursor: pointer;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li>
                <li>
                    <a href="#"> Operation   </a>
                </li>
                <li class="active"> Job Card Edit  </li>
            </ul><!-- /.breadcrumb -->
        </div>
        @include('inc/message')
        <div class="panel"> 
            <div class="panel-heading">
                <h6>Job Card Edit  </h6>
            </div>
            <div class="panel-body">
                <form role="form" method="get" action="{{ url('hr/timeattendance/attendance_bulk_manual') }}" class="attendanceReport" id="attendanceReport">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates ', 'required'=>'required']) }} 
                                <label>Associate ID</label> 
                            </div>
                        </div>
                        <div class="col-sm-4" >
                            <div class="form-group has-float-label has-required ">
                                <input type="month" name="month" id="month" class="form-control" max="{{date('Y-m')}}" value="{{ Request::get('month') }}" required="required" placeholder="Month" autocomplete="off" />
                                <label>Month</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                                @if (!empty(request()->associate) && !empty(request()->month) && !empty(request()->year))
                                    <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning " title="Print">
                                        <i class="fa fa-print"></i>
                                    </button> 
                                    <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger " title="PDF">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </a>
                                    <button type="button"  id="excel"  class="showprint btn btn-success " title="Excel">
                                        <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                   </button>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
        <div class="panel">  
            <div class="panel-body">
                <div class="row justify-content-center">
                    <!-- Display Erro/Success Message -->
                    <div class="col-sm-1"></div>
                    <div class="col-sm-10" id="PrintArea">
                        @if($info)
                        @php 
                            $lastMonth = date('m',strtotime("-1 month"));
                            $thisMonth = date('m', strtotime(request()->month));
                            
                            $disabled = 'disabled="disabled"';
                            // check activity lock/unlock
                            $yearMonth = date('Y-m', strtotime('-1 month'));
                            $lock['month'] = date('m', strtotime($yearMonth));
                            $lock['year'] = date('Y', strtotime($yearMonth));
                            $lock['unit_id'] = $info->as_unit_id;
                            $lockActivity = monthly_activity_close($lock);
                            if(($lastMonth == $thisMonth && $lockActivity == 0)|| $thisMonth == date('m')){
                                $disabled = '';
                            }

                        @endphp
                        <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:10px auto;">
                            {{-- <div class="page-header" style="border-bottom:2px double #666">
                                <h4 style="margin:4px 10px">{{ $info->unit }}</h4>
                                <h5 style="margin:4px 10px">For the month of {{ request()->month }} </h5>
                            </div> --}}
                            <form class="form-horizontal" role="form" method="post" action="{{ url('hr/timeattendance/attendance_bulk_store')  }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
                                    
                                    <tr>
                                        <th style="width:33%">
                                           <p style="margin:0;padding:4px 10px"><strong>ID </strong> # {{ $info->associate_id }}</p>
                                           <p style="margin:0;padding:4px 10px"><strong>Oracle ID </strong> # {{ $info->as_oracle_code }}</p>
                                           
                                        </th>
                                        <th style="width: 33%;">
                                            <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ $info->as_name }}</p>
                                           <p style="margin:0;padding:4px 10px"><strong>DOJ </strong>: {{ date("d-m-Y", strtotime($info->as_doj)) }}</p>
                                        </th>
                                        <th>
                                           <p style="margin:0;padding:4px 10px"><strong>Section </strong>: {{ $info->section }} </p>
                                           <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ $info->designation }} </p>
                                        </th>
                                    </tr> 
                                </table>

                                <table class="table table-bordered table-head table-hover" style="width:100%;border:1px solid #ccc;font-size:13px;  overflow-x: auto;"  cellpadding="2" cellspacing="0" border="1" align="center">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" width="12%">Date</th>
                                            <th rowspan="2">Present Status</th>
                                            <th rowspan="2">Floor</th>
                                            <th rowspan="2">Line</th>
                                            <th colspan="1" class="text-center">Shift</th>
                                            <th colspan="2" class="text-center">Punch</th>
                                            <th rowspan="2">OT Hour</th>
                                        </tr>
                                        <tr>
                                            <th>In time - Out Time</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($attendance as $data)
                                        <tr>
                                          <td class="startdate">
                                            {{ $data['date'] }}
                                            @if($joinExist)
                                                @if($data['date'] == $info->as_doj)
                                                    <span class="label label-success arrowed-right arrowed-in pull-right">Join</span>
                                                @endif
                                            @endif
                                            @if($leftExist)
                                                @if($data['date'] == $info->as_status_date)
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
                                            <td @if($data['present_status'] == 'A' || $data['present_status'] == 'Weekend(General) - A') style="background: #ea9d99;color:#000;" @endif>
                                                @if($data['attPlusOT'])
                                                   P ( {{ $data['attPlusOT'] }} )
                                                @else
                                                    {{ $data['present_status'] }}
                                                @endif
                                                
                                                <input type="hidden" name="status[{{$data['date']}}]" value="{{ $data['present_status'] }}">
                                                @php
                                                    if (strpos($data['in_time'], ':') !== false) {
                                                        list($one,$two,$three) = array_pad(explode(':',$data['in_time']),3,0);
                                                        if((int)$one+(int)$two+(int)$three == 0) {
                                                            $data['in_time'] = null;
                                                        }
                                                    }
                                                    if (strpos($data['out_time'], ':') !== false) {
                                                        list($one,$two,$three) = array_pad(explode(':',$data['out_time']),3,0);
                                                        if((int)$one+(int)$two+(int)$three == 0) {
                                                            $data['out_time'] = null;
                                                        }
                                                    }
                                                @endphp
                                                @if($data['late_status']==1 || ($data['in_time'] == null && $data['out_time'] != null))
                                                    <span style="height: auto;float:right;" class="label label-warning pull-right">Late</span>
                                                @endif
                                                @if($data['remarks']== 'HD')
                                                    <span style="height: auto;float:right;" class="label label-danger pull-right">Half Day @if($data['late_status']==1) , @endif</span>
                                                @endif
                                                @if($data['outside'] != null)
                                                <span style="height: auto;float:right;cursor:pointer;" class="label label-success pull-right" data-tooltip="{{$data['outside_msg']}}" data-tooltip-location="top">{{$data['outside']}}</span>
                                                @endif
                                            </td>
                                            <td>{{ $data['floor'] }}</td>
                                            <td>{{ $data['line'] }}</td>
                                            <td>
                                                <a class="shift_link" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $data['shift_id']}}" >{{ date('H:i', strtotime($data['shift_start'])) }}
                                                - 
                                                @php
                                                    
                                                    $time = $data['shift_end'];
                                                    $time2 = intdiv($data['shift_break'], 60).':'. ($data['shift_break'] % 60);

                                                    $secs = strtotime($time2)-strtotime("00:00:00");
                                                    $result = date("H:i",strtotime($time)+$secs);
                                                @endphp
                                                {{ $result }}</a>

                                            </td>
                                            

                                            @php
                                                $disabled_input = '';
                                                if($data['holiday'] == 1 || $data['present_status']=='Holiday' || $data['present_status']=='Weekend' || $data['present_status']=='Day Off' || strpos($data['present_status'],'Leave')!==false) {
                                                    $disabled_input = 'readonly="readonly"';
                                                }
                                            @endphp
                                            @if($data['att_id'] != null)
                                                <td>
                                                    <input type="hidden" name="old_status[{{$data['att_id']}}]" value="{{ $data['present_status'] }}">
                                                    <input type="hidden" name="old_date[{{$data['att_id']}}]" value="{{$data['date']}}">
                                                    <input type="hidden" name="this_shift_code[{{$data['att_id']}}]" value="{{$data['shift_code']}}">
                                                    <input type="hidden" name="this_shift_id[{{$data['att_id']}}]" value="{{$data['shift_id']}}">
                                                    <input type="hidden" name="this_shift_start[{{$data['att_id']}}]" value="{{$data['shift_start']}}">
                                                    <input type="hidden" name="this_shift_end[{{$data['att_id']}}]" value="{{$data['shift_end']}}">
                                                    <input type="hidden" name="this_shift_break[{{$data['att_id']}}]" value="{{$data['shift_break']}}">
                                                    <input type="hidden" name="this_shift_night[{{$data['att_id']}}]" value="{{$data['shift_night']}}">
                                                    <input type="hidden" name="this_bill_eligible[{{$data['att_id']}}]" value="{{$data['bill_eligible']}}">
                                                    <input class="intime manual form-control" type="text" name="intime[{{$data['att_id']}}]" value="{{!empty($data['in_time'])?date("H:i:s", strtotime($data['in_time'])):null}}"   placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}} autocomplete="off">
                                                </td>
                                                <td>
                                                    <input type="text" class="outtime manual form-control" name="outtime[{{$data['att_id']}}]" value="{{!empty($data['out_time'])?date("H:i:s", strtotime($data['out_time'])):null}}"  placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}} autocomplete="off">
                                                </td>
                                            @else
                                                <td>
                                                    <input type="hidden" name="new_date[]" value="{{$data['date']}}">
                                                    <input type="hidden" name="new_shift_id[]" value="{{$data['shift_id']}}">
                                                    <input type="hidden" name="new_shift_code[]" value="{{$data['shift_code']}}">
                                                    <input type="hidden" name="new_shift_start[]" value="{{$data['shift_start']}}">
                                                    <input type="hidden" name="new_shift_end[]" value="{{$data['shift_end']}}">
                                                    <input type="hidden" name="new_shift_break[]" value="{{$data['shift_break']}}">
                                                    <input type="hidden" name="new_shift_night[]" value="{{$data['shift_night']}}">
                                                    <input type="hidden" name="new_bill_eligible[]" value="{{$data['bill_eligible']}}">

                                                    <input class="intime manual form-control" type="text" name="new_intime[]" value=""   placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}} autocomplete="off">
                                                </td>
                                                <td>
                                                    <input type="text" class="outtime manual form-control" name="new_outtime[]" value="" placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}} autocomplete="off">
                                                </td>
                                            @endif
                                            <td> 
                                                @if($info->as_ot==1)
                                                    {{ numberToTimeClockFormat($data['overtime_time']) }} 
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot style="border-top:2px double #999">
                                            <tr>
                                                <th>Present</th>
                                                <th>{{ $info->present }}</th>
                                                <th colspan="4"></th>
                                                <th style="text-align:right">Total OT</th>
                                                <th>
                                                    @if($info->as_ot==1)
                                                        <input type="hidden" id="ot" value="{{ $info->ot_hour }}">
                                                        {{ numberToTimeClockFormat($info->ot_hour) }}
                                                    @endif
                                                </th>
                                            </tr>
                                            <tr><td colspan="8">
                                                <input type="hidden" name="month" value="{{request()->month}}">
                                                <input type="hidden" name="year" value="{{request()->year}}">
                                                <input type="hidden" name="ass_id" value="{{$info->as_id}}">
                                                <input type="hidden" name="associate_id" value="{{$info->associate_id}}">
                                                <input type="hidden" name="unit_att" class="unit_att" value="{{$info->as_unit_id}}">
                                                    <button class="btn  btn-primary pull-right" type="submit" {{$disabled}}>
                                                      <i class="ace-icon fa fa-check bigger-110"></i> Update
                                                    </button>
                                                </td>
                                            </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div> 
                        @endif
                    </div>
                    <div class="col-sm-1"></div>
                    <!-- /.col -->
                </div> 
            </div>  
        </div>
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">
    function printMe(divName)
    {
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(document.getElementById(divName).innerHTML); 
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    $(document).ready(function(){ 
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
        // Status Hidden field value change
        $(".manual").on("keyup", function(){ 
            // console.log($(this).val());
            if($(this).val() == '') {
                $(this).val('00:00:00')
            }
            var intime=$(this).parent().parent().find('.intime').val();
            var outtime=$(this).parent().parent().find('.outtime').val();
            if(intime != ''||outtime != ''){
                $(this).parent().parent().find('.att_status').val('P');
            } else {
                $(this).parent().parent().find('.att_status').val('A');
            }
        });

        // excel conversion -->
        $('#excel').click(function(){
            var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html()) 
            location.href=url;
            return false;
        });
    });
    
    $(document).ready(function() {
    $(".intime,.outtime").on("keydown", function(event) {
        if (event.keyCode === 38 || event.keyCode === 40) {
            event.preventDefault();
        }
     });
});
    $('.intime,.outtime').datetimepicker({
      format:'HH:mm:ss',
      allowInputToggle: false
    });
    // input focus select all element
    $(function () {
        var focusedElement;
        $(document).on('focus', 'input', function () {
            if (focusedElement == this) return;
            focusedElement = this;
            setTimeout(function () { focusedElement.select(); }, 100);
        });
    });
    /*$(document).ready(function () {

      $('input').keyup(function (e) {
        e.preventDefault();
        if (e.which == 39) { // right arrow
          $(this).closest('td').next().find('input').focus();

        } else if (e.which == 37) { // left arrow
          $(this).closest('td').prev().find('input').focus();

        } else if (e.which == 40) { // down arrow
          $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();

        } else if (e.which == 38) { // up arrow
          $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
        }
      });


    });*/
</script>
@endpush
@endsection
