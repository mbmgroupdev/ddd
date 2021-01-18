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
    .iq-card {
        border: 1px solid #ccc;
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
    <div class="iq-accordion career-style mat-style  ">
        <div class="iq-card iq-accordion-block mb-3 {{ Request::get('unit') == null?'accordion-active':'' }}">
            <div class="active-mat clearfix">
              <div class="container-fluid">
                 <div class="row">
                    <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;border-radius: 50%;"> Employee Wise </span> </a></div>
                 </div>
              </div>
            </div>
            <div class="accordion-details">
                <div class="row">
                    <div class="col">
                      <form role="form" method="get" action="{{ url('hr/operation/job_card') }}" class="attendanceReport" id="attendanceReportEmp">
                        <div class="panel" style="margin-bottom: 0;">
                            
                            <div class="panel-body" style="padding-bottom: 5px;">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'allassociates no-select col-xs-12','style', 'required'=>'required']) }}
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
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                  </div>
              </div>
           </div>
        </div>
        {{-- <div class="iq-card iq-accordion-block mb-3 {{ Request::get('unit') != null?'accordion-active':'' }}">
           <div class="active-mat clearfix">
              <div class="container-fluid">
                 <div class="row">
                    <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;border-radius: 50%;"> Unit Wise </span> </a></div>
                 </div>
              </div>
           </div>
           <div class="accordion-details">
              <div class="row1">
                <div class="col-12"> 
                    <div class="panel mb-0">
                        
                        <div class="panel-body pb-0">
                            <form role="form" method="get" action="{{ url('hr/operation/job_card') }}" class="attendanceReport" id="attendanceReport">
                                <div class="row">
                                  <div class="col-3">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        
                                        <select name="unit" class="form-control capitalize select-search" id="unit" required="">
                                            <option selected="" value="">Choose...</option>
                                            
                                            @foreach($unitList as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                      <label for="unit">Unit</label>
                                    </div>
                                    <div class="form-group has-float-label  select-search-group">
                                        <select name="area" class="form-control capitalize select-search" id="area">
                                            <option selected="" value="">Choose...</option>
                                            @foreach($areaList as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <label for="area">Area</label>
                                    </div>
                                    <div class="form-group has-float-label select-search-group">
                                        <select name="department" class="form-control capitalize select-search" id="department" disabled>
                                            <option selected="" value="">Choose...</option>
                                        </select>
                                        <label for="department">Department</label>
                                    </div>
                                  </div>
                                  <div class="col-3">
                                    
                                    <div class="form-group has-float-label select-search-group">
                                        <select name="section" class="form-control capitalize select-search " id="section" disabled>
                                            <option selected="" value="">Choose...</option>
                                        </select>
                                        <label for="section">Section</label>
                                    </div>
                                    <div class="form-group has-float-label select-search-group">
                                        <select name="subSection" class="form-control capitalize select-search" id="subSection" disabled>
                                            <option selected="" value="">Choose...</option> 
                                        </select>
                                        <label for="subSection">Sub Section</label>
                                    </div>
                                    <div class="form-group has-float-label select-search-group">
                                        <select name="floor_id" class="form-control capitalize select-search" id="floor_id" disabled >
                                            <option selected="" value="">Choose...</option>
                                        </select>
                                        <label for="floor_id">Floor</label>
                                    </div>
                                  </div> 
                                  <div class="col-3">
                                    
                                    <div class="form-group has-float-label select-search-group">
                                        <select name="line_id" class="form-control capitalize select-search" id="line_id" disabled >
                                            <option selected="" value="">Choose...</option>
                                        </select>
                                        <label for="line_id">Line</label>
                                    </div>
                                    <div class="form-group has-float-label select-search-group">
                                        <select name="as_ot" class="form-control capitalize select-search" id="as_ot"  >
                                            <option selected="" value="">Choose...</option>
                                            <option  value="1">OT</option>
                                            <option  value="0">Non OT</option>
                                        </select>
                                        <label for="as_ot">OT</label>
                                    </div>
                                  </div>  
                                  <div class="col-3">
                                    <div class="form-group has-float-label has-required">
                                      <input type="month" class="report_date datepicker form-control" id="month" name="month" placeholder="Y-m" required="required" value="{{ date('Y-m') }}" autocomplete="off" />
                                      <label for="month">Month</label>
                                    </div>
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <?php
                                          $status = ['1'=>'Active','25' => 'Left & Resign','2'=>'Resign','3'=>'Terminate','4'=>'Suspend','5'=>'Left', '6'=>'Maternity'];
                                        ?>
                                        {{ Form::select('employee_status', $status, 1, ['placeholder'=>'Select Employee Status ', 'class'=>'form-control capitalize select-search', 'id'=>'estatus', 'required']) }}
                                        <label for="estatus">Status</label>
                                    </div>
                                    
                                    <div class="form-group">
                                      <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" id="attendanceReport"><i class="fa fa-save"></i> Generate</button>
                                    </div>
                                  </div>
                                  
                                  
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
           </div>
        </div> --}}
        
    </div>
    <div class="page-content">
        
        <div class="row1">
            @if(isset($info))
            <div class="panel w-100">
                <div class="panel-body">
                    <div class="offset-1 col-10 h-min-400">
                        
                        @php
                            $year  = date('Y', strtotime(request()->month_year));
                            $month = date('m', strtotime(request()->month_year));
                            $lastMonth = date('m',strtotime("-1 month"));
                            $thisMonth = date('m');
                            
                            // check activity lock/unlock
                            $yearMonth = date('Y-m', strtotime('-1 month'));
                            $lock['month'] = date('m', strtotime($yearMonth));
                            $lock['year'] = date('Y', strtotime($yearMonth));
                            $lock['unit_id'] = $info->as_unit_id;
                            $lockActivity = monthly_activity_close($lock);
                        @endphp
                        <div class="iq-card">
                            <div class="iq-card-header d-flex mb-0">
                               <div class="iq-header-title w-100">
                                  <div class="row">
                                    <div class="col-3">
                                        <div class="action-section">
                                            <h4 class="card-title capitalize inline">
                                                <button type="button" onClick="printMe1('result-data')" class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Job Card">
                                                   <i class="fa fa-print"></i>
                                                </button>
                                                <button type="button" id="excel" class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download Job Card">
                                                   <i class="fa fa-file-excel-o"></i>
                                                </button>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                      <h4 class="card-title capitalize inline">
                                        @php
                                            $associate = request()->associate;
                                            $nextMonth = date('Y-m', strtotime(request()->month_year.' +1 month'));
                                            $prevMonth = date('Y-m', strtotime(request()->month_year.' -1 month'));

                                            $prevUrl = url("hr/operation/job_card?associate=$associate&month_year=$prevMonth");
                                            $nextUrl = url("hr/operation/job_card?associate=$associate&month_year=$nextMonth");
                                            $user  = auth()->user();
                                        @endphp
                                        
                                        @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
                                        <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month Job Card" >
                                          <i class="las la-chevron-left"></i>
                                        </a>
                                        @endif
                                        <b class="f-16" id="result-head">{{ request()->month_year }} </b>
                                        @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
                                        @if(strtotime(date('Y-m')) >= strtotime($nextMonth))
                                        <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Month Job Card" >
                                          <i class="las la-chevron-right"></i>
                                        </a>
                                        @endif
                                        @endif
                                      </h4>
                                    </div>
                                    @php 
                                        $yearMonth = request()->month_year; 

                                        $flagStatus = 0;
                                        $asStatus = emp_status_name($info->as_status);
                                        $statusDate = $info->as_status_date;
                                        if($statusDate != null && $info->as_status != 1){
                                            $statusMonth = date('Ym', strtotime($info->as_status_date));
                                            $requestMonth = date('Ym', strtotime($yearMonth));
                                          
                                            if($requestMonth > $statusMonth){
                                                $flagStatus = 1;
                                            }
                                        }
                                    @endphp
                                    <div class="col-3">
                                    @if($flagStatus == 0)
                                        @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
                                          @if(($lastMonth == $month && $lockActivity == 0)|| $month == date('m'))
                                          <div class="text-right">
                                            <h4 class="card-title capitalize inline">
                                            <a href='{{url("hr/timeattendance/attendance_bulk_manual?associate=$info->associate_id&month=$yearMonth")}}' class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Manual Edit Job Card">
                                              <i class="fa fa-edit bigger-120"></i>
                                            </a>
                                            </h4>
                                          </div>
                                          @endif
                                        @endif
                                    @endif
                                    </div>
                                  </div>
                               </div>
                            </div>
                            <div class="iq-card-body pt-0">
                                <div class="result-data" id="result-data">
                                    
                                    <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:30px auto;">
                                        <div class="page-header" id="brand-head" style="border-bottom:2px double #666; text-align: center;">
                                            <h3 style="margin:4px 10px">{{ $info->unit }}</h3>
                                            <h5 style="margin:4px 10px">Job Card Report</h5>

                                            <h5 style="margin:4px 10px">For the month of {{date('F, Y', strtotime(request()->month_year))}}</h5>
                                        </div>
                                        <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
                                            <tr>
                                                <th style="width:32%">
                                                   <p style="margin:0;padding:4px 10px"><strong>ID </strong> # {{ $info->associate_id }}</p>
                                                   <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ $info->as_name }}</p>
                                                   <p style="margin:0;padding:4px 10px"><strong>DOJ </strong>: {{ date("d-m-Y", strtotime($info->as_doj)) }}</p>
                                                </th>
                                                <th>
                                                    <p style="margin:0;padding:4px 10px"><strong>Oracle ID </strong>: {{ $info->as_oracle_code }} </p>
                                                   <p style="margin:0;padding:4px 10px"><strong>Section </strong>: {{ $info->section }} <span>@if($info->pre_section != null) - Previous: {{ $pre_section }} @endif</span> </p>
                                                   <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ $info->designation }} <span>@if($info->pre_designation != null) - Previous: {{ $pre_designation }} @endif</span></p>
                                                </th>
                                                <th>
                                                   <p style="margin:0;padding:4px 10px"><strong>Total Present </strong>: <b >{{ $info->present }}</b> </p>
                                                   <p style="margin:0;padding:4px 10px"><strong>Total Absent </strong>: <b >{{ $info->absent }}</b></p>
                                                   <p style="margin:0;padding:4px 10px"><strong>Total OT </strong>: <b>{{ numberToTimeClockFormat($info->ot_hour) }}</b> </p>
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
                                                
                                                @if($flagStatus == 0)
                                                @foreach($attendance as $value)
                                                <tr>
                                                    <td>
                                                        {{ $value['date'] }}
                                                        @if($joinExist)
                                                            @if($value['date'] == $info->as_doj)
                                                                <span class="label label-success arrowed-right arrowed-in pull-right">Joined</span>
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
                                                    <td @if($value['present_status'] == 'A' || $value['present_status'] == 'Weekend(General) - A') style="background: #ea9d99;color:#000;" @endif>
                                                    {!! $value['present_status'] !!}

                                                    @if($value['late_status']==1)
                                                        <span style="height: auto;float:right;" class="label label-warning pull-right">Late</span>
                                                    @endif
                                                    @if($value['remarks']== 'HD')
                                                        <span style="height: auto;float:right;" class="label label-danger pull-right">Half Day @if($value['late_status']==1) , @endif</span>

                                                    @endif
                                                    @if($value['outside'] != null)
                                                    <span style="height: auto;float:right;cursor:pointer;" class="label label-success pull-right" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$value['outside_msg']}}" >{{$value['outside']}}</span>
                                                    @endif
                                                    </td>
                                                    <td>{{ $value['floor'] }}</td>
                                                    <td>{{ $value['line'] }}</td>
                                                    <td>{{!empty($value['in_time'])?$value['in_time']:null}}</td>
                                                    <td>{{!empty($value['out_time'])?$value['out_time']:null}}</td>
                                                    <td>
                                                    @if($info->as_ot==1)
                                                        {{numberToTimeClockFormat($value['overtime_time'])}}
                                                    @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="7" class="text-center">This Employee is {{ $asStatus }}</td>
                                                </tr>
                                                @endif
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

                                                        {{numberToTimeClockFormat($info->ot_hour)}}

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
                        
                    </div>
                </div>
            </div>
            @endif
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
        /*$('select.allassociates').select2({
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
        });*/
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