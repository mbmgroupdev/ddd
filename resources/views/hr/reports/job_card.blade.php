@extends('hr.layout')
@section('title', 'Job Card')
@section('main-content')
@push('css')
<style>
   .modal-h3{
    margin:5px 0;
   }
   strong{
    font-size: 14px;
   }
   .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
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
          <a href="#">Operation</a>
        </li>
        <li class="active"> Job Card</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col">
                <form role="form" method="get" action="{{ url('hr/operation/job_card') }}" class="attendanceReport" id="attendanceReport">
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                        <label  for="associate"> Associate's ID </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="month" class="form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                        <label  for="year"> Month </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    @if (!empty(request()->associate) && !empty($month) && !empty($year))
                                    <div id="print_pdf" class="custom-control-inline" >
                                         
                                        <button type="button" onClick="printMe1('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                            <i class="fa fa-print"></i>
                                        </button>
                                        <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel">
                                            <i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                       </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="offset-2 col-8 h-min-400">
                @if(isset($info))
                @php
                    $year  = date('Y', strtotime(request()->month_year));
                    $month = date('m', strtotime(request()->month_year));
                    $lastMonth = date('m',strtotime("-1 month"));
                    $thisMonth = date('m');
                    $number = salary_lock_date();
                    $lockDate = Date('Y-m')."-".sprintf('%02d', $number);
                @endphp
                <div class="iq-card">
                    <div class="iq-card-header d-flex mb-0">
                       <div class="iq-header-title w-100">
                          <div class="row">
                            <div class="col-3">
                              
                            </div>
                            <div class="col-6 text-center">
                              <h4 class="card-title capitalize inline">
                                @php
                                    $associate = request()->associate;
                                    $nextMonth = date('Y-m', strtotime(request()->month_year.' +1 month'));
                                    $prevMonth = date('Y-m', strtotime(request()->month_year.' -1 month'));

                                    $prevUrl = url("hr/operation/job_card?associate=$associate&month_year=$prevMonth");
                                    $nextUrl = url("hr/operation/job_card?associate=$associate&month_year=$nextMonth");
                                @endphp
                                <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month Job Card" >
                                  <i class="las la-chevron-left"></i>
                                </a>
                                <b class="f-16" id="result-head">{{ request()->month_year }} </b>
                                @if($month < $thisMonth)
                                <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Month Job Card" >
                                  <i class="las la-chevron-right"></i>
                                </a>
                                @endif
                              </h4>
                            </div>
                            <div class="col-3">
                              @if(($lastMonth == $month && $lockDate > date('Y-m-d'))|| $month == date('m'))
                              <div class="text-right">
                                <a href='{{url("hr/timeattendance/attendance_bulk_manual?associate=$info->associate&month=$month&year=$year")}}' class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Manual Edit Job Card">
                                  <i class="fa fa-edit bigger-120"></i>
                                </a>
                                
                              </div>
                              @endif
                            </div>
                          </div>
                       </div>
                    </div>
                    <div class="iq-card-body pt-0">
                        <div class="result-data" id="result-data">
                            
                            <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:20px auto;">
                                <div class="page-header" id="brand-head" style="border-bottom:2px double #666; text-align: center;">
                                    <h3 style="margin:4px 10px">{{ $info->unit }}</h3>
                                    <h5 style="margin:4px 10px">Job Card Report</h5>

                                    <h5 style="margin:4px 10px">For the month of {{ $month }} - {{ $year }}</h5>
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
                                                        @endphp
                                                        @if($flag != '')
                                                        <span class="label label-warning arrowed-right arrowed-in pull-right">
                                                            {{ $flag }}
                                                        </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                            {{ $value['present_status'] }}

                                            @if($value['late_status']==1)
                                                <span style="height: auto;float:right;" class="label label-warning pull-right">Late</span>
                                            @endif
                                            @if($value['remarks']== 'HD')
                                                <span style="height: auto;float:right;" class="label label-danger pull-right">Half Day @if($value['late_status']==1) , @endif</span>

                                            @endif
                                            @if($value['outside'] != null)
                                            <span style="height: auto;float:right;cursor:pointer;" class="label label-success pull-right" data-tooltip="{{$value['outside_msg']}}" data-tooltip-location="top">{{$value['outside']}}</span>
                                            @endif
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
                        </div> 

                    </div>
                </div>
                @endif
            </div>
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
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
@endpush
@endsection