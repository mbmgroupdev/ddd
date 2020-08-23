@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
    <style>
        .bootstarp-datetimepicker-widget{display: none !important;}
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
                    <a href="#"> Time & Attendance   </a>
                </li>
                <li class="active"> Attendance Bulk Manual  </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            <?php $type='job_card'; ?>
            <div class="page-header">
                <h1>Time & Attendance  <small> <i class="ace-icon fa fa-angle-double-right"></i> Attendance Bulk Manual </small></h1>
            </div>
            <div class="row">
                <form role="form" method="get" action="{{ url('hr/timeattendance/attendance_bulk_manual') }}" class="attendanceReport" id="attendanceReport">
                    <div class="col-sm-12"> 

                        <div class="col-sm-9">
                            <div class="form-group">
                                <div class="col-sm-4" style="padding-top: 10px;">
                                    {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12', 'data-validation'=>'required']) }}  
                                </div>
                                <div class="col-sm-4" style="padding-top: 10px; padding-bottom:30px; ">
                                    <input type="text" name="month" id="month" class="monthpicker col-xs-12" value="{{ Request::get('month') }}" data-validation="required" placeholder="Month" autocomplete="off" />
                                </div>
                                <div class="col-sm-4" style="padding-top: 10px; padding-bottom:30px; ">
                                    <input type="text" name="year" id="year" class="yearpicker col-xs-12" value="{{ Request::get('year') }}" data-validation="required" placeholder="Year" autocomplete="off" />
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-3" style="padding-top: 10px; padding-left: 25px;">
                            <button type="submit" class="btn btn-info btn-xs">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                            @if (!empty(request()->associate) && !empty(request()->month) && !empty(request()->year))
                                <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning btn-xs" title="Print">
                                    <i class="fa fa-print"></i>
                                </button> 
                                <a href="{{request()->fullUrl()}}&pdf=true" target="_blank" class="btn btn-danger btn-xs" title="PDF">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                                <button type="button"  id="excel"  class="showprint btn btn-success btn-xs" title="Excel">
                                    <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                               </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <!-- Display Erro/Success Message -->
                <div class="col-xs-12" id="PrintArea">
                    @include('inc/message')
                    <!-- PAGE CONTENT BEGINS -->
                    @if($info)
                    @php 
                        $lastMonth = date('m',strtotime("-1 month"));
                        $thisMonth = date('m', strtotime(request()->month));
                        $number = Custom::getLockDate();
                        $lockDate = Date('Y-m')."-".sprintf('%02d', $number);
                        $disabled = 'disabled="disabled"';
                        if(($lastMonth == $thisMonth && $lockDate> date('Y-m-d'))|| $thisMonth == date('m')){
                            $disabled = '';
                        }

                    @endphp
                    <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto;border:1px solid #ccc;">
                        <div class="page-header" style="border-bottom:2px double #666">
                            <h2 style="margin:4px 10px">{{ $info->unit['hr_unit_name'] }}</h2>
                            <h5 style="margin:4px 10px">For the month of {{ request()->month }} - {{ request()->year }}</h5>
                        </div>
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/timeattendance/attendance_bulk_store')  }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
                                <tr>
                                    <th style="width:50%">
                                       <p style="margin:0;padding:4px 10px"><strong>ID </strong> # {{ $info->associate_id }}</p>
                                       <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ $info->as_name }}</p>
                                       <p style="margin:0;padding:4px 10px"><strong>DOJ </strong>: {{ date("d-m-Y", strtotime($info->as_doj)) }}</p>
                                    </th>
                                    <th>
                                       <p style="margin:0;padding:4px 10px"><strong>Section </strong>: {{ $info->section['hr_section_name'] }} </p>
                                       <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ $info->designation['hr_designation_name']}} </p>
                                    </th>
                                </tr> 
                            </table>

                            <table class="table table-bordered" style="width:100%;border:1px solid #ccc;font-size:13px;  overflow-x: auto;"  cellpadding="2" cellspacing="0" border="1" align="center">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Present Status</th>
                                        <th>Floor</th>
                                        <th>Line</th>
                                        <th>In Time</th>
                                        <th>Out Time</th>
                                        <th>OT Hour</th>
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
                                        <td>
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
                                                <span style="height: 17px;float:right;" class="label label-warning pull-right">Late</span>
                                            @endif
                                            @if($data['remarks']== 'HD')
                                                <span style="height: 17px;float:right;" class="label label-danger pull-right">Half Day @if($data['late_status']==1) , @endif</span>
                                            @endif
                                            <span style="height: 17px;float:right;cursor:pointer;" class="label label-success pull-right" data-tooltip="{{$data['outside_msg']}}" data-tooltip-location="top">{{$data['outside']}}</span>
                                        </td>
                                        <td>{{ $data['floor'] }}</td>
                                        <td>{{ $data['line'] }}</td>

                                        @php
                                            $disabled_input = '';
                                            if($data['present_status']=='Holiday' || strpos($data['present_status'],'Leave')!==false) {
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
                                                <input class="intime manual" type="text" name="intime[{{$data['att_id']}}]" value="{{!empty($data['in_time'])?$data['in_time']:null}}"  placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}}>
                                            </td>
                                            <td>
                                                <input type="text" class="outtime manual" name="outtime[{{$data['att_id']}}]" value="{{!empty($data['out_time'])?$data['out_time']:null}}" step="2" placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}}>
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
                                                <input class="intime manual" type="text" name="new_intime[]" value=""  placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}}>
                                            </td>
                                            <td>
                                                <input type="text" class="outtime manual" name="new_outtime[]" value="" step="2" placeholder="HH:mm:ss" {{$disabled}} {{$disabled_input}}>
                                            </td>
                                        @endif
                                        <td> 
                                            @if($info->as_ot==1)
                                               {{Custom::numberToTimeFormat($data['overtime_time'])}}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="border-top:2px double #999">
                                        <tr>
                                            <th colspan="3">Attend</th>
                                            <th>{{ $info->present }}</th>
                                            <th></th>
                                            <th style="text-align:right">Total Ot</th>
                                            <th>
                                                @if($info->as_ot==1)
                                                    <input type="hidden" id="ot" value="{{ $info->ot_hour }}">
                                                    {{Custom::numberToTimeFormat($info->ot_hour)}}
                                                @endif
                                            </th>
                                        </tr>
                                        <tr><td colspan="7">
                                            <input type="hidden" name="month" value="{{request()->month}}">
                                            <input type="hidden" name="year" value="{{request()->year}}">
                                            <input type="hidden" name="ass_id" value="{{$info->as_id}}">
                                            <input type="hidden" name="associate_id" value="{{$info->associate_id}}">
                                            <input type="hidden" name="unit_att" class="unit_att" value="{{$info->as_unit_id}}">
                                                <button class="btn btn-sm btn-info pull-right" type="submit" {{$disabled}}>
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
                <!-- /.col -->
            </div> 
        </div><!-- /.page-content -->
    </div>
</div>

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
        console.log($(this).val());
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

    // Time picker -->
    /*$('.intime, .outtime').datetimepicker({
        format: 'HH:mm:ss'
    }); */

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
@push('js')
    <script>
        $('.intime,.outtime').datetimepicker({
          format:'HH:mm:ss'
        });
        // input focus select all element
        $(function () {
            var focusedElement;
            $(document).on('focus', 'input', function () {
                if (focusedElement == this) return;
                focusedElement = this;
                setTimeout(function () { focusedElement.select(); }, 50);
            });
        });
    </script>
@endpush
@endsection
