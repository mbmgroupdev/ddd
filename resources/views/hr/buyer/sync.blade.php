@extends('hr.layout')
@section('title', 'Buyer Templates')
@section('main-content')
@push('css')
    <style type="text/css">
        .disabled-row::before {
            content: '';
            position: absolute;
            z-index: 10;
            width: 100%;
            height: calc(100% + 20px);
            height: -webkit-calc(100% + 20px);
            height: -moz-calc(100% + 20px);
            background: #099faf;
            opacity: 0.3;
            border-radius: 10px;
            top: -10px;
        }
        .custom-form-control{
            height: 25px;
            line-height: 25px;
            background: 0 0;
            border: 1px solid #d7dbda;
            font-size: 12px;
            border-radius: 10px;
            width: 100%;
            margin: 1px 0;
            text-align: center;
            min-width: 120px;
        }
        .fa-trash{
            font-size: 14px;
            position: relative;
            top: 3px;
            cursor: pointer;
            color: red;
        }
        .date-rows{
            display:  flex;
            justify-content: space-between;
        }
        .date-p{
            padding: 2px 10px;
            border: 1px solid #d1d1d1;
            border-radius: 10px;
            margin: 2px 0;
        }
        #sync-area{
            border-left: 1px solid  #099faf;
            }
        .btn-rounded {
            width: 100%;
            height: 25px;
            font-size: 12px;
            line-height: 13px;
            border-radius: 10px;
        }
        .buttons-area{
            padding: 3px 10px;
            justify-content: space-between;
            display: flex;
        }
        .bg-danger{
            border-color: #e3342f !important;
            background-color: #e3342f !important;
            background: #e3342f !important;
        }
        .nav-year {
            font-size: 13px;
            font-weight: bold;
            color: #9c9c9c;
            padding: 1px 10px;
            border-radius: 10px;
            margin: 0 2px;
            background: #eff7f8;
            display: inline-block;
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
                    <a href="#">Buyer Mode</a>
                </li>
                <li class="top-nav-btn btn btn-sm btn-primary" data-toggle="modal" data-target="#right_modal_template"></li>
            </ul>
        </div>
    </div>
    <div class="panel">
        <div class="panel-body text-center p-2">
            @foreach(array_reverse($months) as $k => $i)
                <a href="{{url('hr/buyer/sync/'.$buyer->id.'?month='.$k)}}" class="nav-year @if($k== $reqMonth->copy()->format('Y-m')) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sync buyer data of {{$i}}" >
                    {{$i}}
                </a>
            @endforeach
        </div>
    </div>

    <div class="page-content">
        <div class="panel panel-success" style=""> 
            <div class="panel-heading">
                <h6>Buyer Mode: {{$buyer->template_name}}</h6>
            </div>
            <div class="panel-body">
                
                <div class="row">
                    <div class="col-sm-4 ">
                        <div class="patient-steps">
                            <p class="text-primary font-weight-bold">{{$unit[$buyer->hr_unit_id] }}</p>
                           <div class="d-flex align-items-center justify-content-between">
                              <div class="col-md-6 pl-0">
                                 <div class="data-block">
                                    <p class="mb-0 text-primary">Template</p>
                                    <h5>{{$buyer->template_name}}</h5> <br>
                                    <p class="mb-0 text-primary">Month</p>
                                    <h5>{{$reqMonth->copy()->format('F, Y')}}</h5>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="progress-round patient-progress mx-auto" data-value="80">
                                    
                                    <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center text-center">
                                       <div class="h4 mb-0"><span class="text-primary font-weight-bold" style="font-size: 24px;">{{numberToTimeClockFormat($buyer->base_ot)}}</span><br> <span class="font-size-14">Hour</span></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <ul class="patient-role list-inline d-flex align-items-center p-0 mt-4 mb-0">
                              <li class="text-left m-0">
                                 <h6 class="text-primary">Holidays</h6>
                              </li>
                           </ul>
                            <hr>
                        </div>
                        <form id="holiday-buyer" method="post">
                            @csrf
                            <input type="hidden" name="month" value="{{$reqMonth->copy()->format('m')}}">
                            <input type="hidden" name="year" value="{{$reqMonth->copy()->format('Y')}}">
                            <div class="holidays-block">
                                @foreach($holidays as $key => $hl)
                                <div class="row">
                                    <div class="col-sm-5 pr-0">
                                        <input type="date" name="holidays[]" class="custom-form-control " placeholder="Y-m-d" required="required" min="{{$start_date}}" max="{{$end_date}}" value="@if($temp_info){{$hl->date}}@else{{$hl->hr_yhp_dates_of_holidays}}@endif">
                                    </div>
                                    <div class="col-sm-5 pr-0">
                                        <input type="text" name="title[]" class="custom-form-control" placeholder="Holiday Name" required value="@if($temp_info) {{$hl->title}} @else{{$hl->hr_yhp_comments}}@endif">
                                    </div>
                                    <div class="col-sm-2 buttons-area">
                                        {{-- <i class="fa fa-edit" area-hidden="true"></i>  --}}
                                        <i class="fa fa-trash" area-hidden="true"></i>
                                    </div>
                                </div>
                                @endforeach
                                
                            </div>
                            <div class="row pr-0 mt-2">
                                <div class="col-sm-5 pr-0">
                                    <button type="button" id="add-btn" class="btn btn-primary btn-sm btn-rounded">Add Holiday  +</button>
                                </div>
                            </div>
                            @if($temp_info == null)
                                <button type="submit" id="start-sync" class="btn btn-danger mt-3">Getting Started &nbsp;&nbsp;&nbsp;&nbsp; &#8594;</button>
                            @else
                                <button type="submit" id="start-sync" class="btn btn-danger mt-3 round-btn">Update Holiday &nbsp;&nbsp;&nbsp;&nbsp; &#8594;</button>
                            @endif
                            
                        </form>
                        
                    </div>
                    <div class="col-sm-8" style="position: relative;">
                        {{--  if get holidays --}}
                        
                        <div id="sync-area" class="row @if($temp_info == null) disabled-row @endif" >
                            <div class="col-sm-12">
                                <input type="checkbox" class="checkBoxGroup" onclick="checkAllGroup(this)" id="check-all" checked />
                                <button  class="btn btn-sm btn-primary sync" style="font-size: 11px;" onclick="syncAll()"  >Sync All <i class="fa fa-refresh" aria-hidden="true"></i></button>

                            </div>
                            @foreach($date_array as $key => $dates)
                                <div class="col-sm-6">
                                    <table style="width: 100%" border="0">
                                        @foreach($dates as $k => $d)
                                            <tr @if($d > date('Y-m-d')) disabled class="text-muted" @endif style="border-bottom: 1px solid #d1d1d1;">
                                                <td>
                                                    <input type='checkbox' class="checkbox-sync" style="position: relative;top: 3px;" value="{{$d}}" id="check_{{$d}}" @if($d > date('Y-m-d')) disabled @else checked @endif/> 

                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    <span>{{$d}}</span> 
                                                </td>
                                                <td class="count-{{$d}}" style="text-align: right;padding-right: 20px;">
                                                    @if(isset($getSynced[$d]))
                                                    {{$getSynced[$d]->count}}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                                <td style="text-align: center;padding: 3px 0;">

                                                    <button id="date-{{$d}}" class="btn btn-sm btn-primary sync" style="font-size: 11px;" onclick="sync('{{$d}}')" @if($d > date('Y-m-d')) disabled @endif >Sync <i class="fa fa-refresh" aria-hidden="true"></i></button>
                                                </td>
                                                
                                            </tr>
                                         
                                        @endforeach  
                                    </table>
                                 </div>
                            @endforeach
                        </div>
                        {{--  --}}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script type="text/javascript">
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';

    var newdate = '<div class="row"> <div class="col-sm-5 pr-0"> <input min="{{$start_date}}" max="{{$end_date}}" type="date" name="holidays[]" class="custom-form-control " placeholder="Y-m-d" > </div><div class="col-sm-5 pr-0"> <input type="text" name="title[]" class="custom-form-control col-sm-5 " placeholder="Holiday Name"> </div><div class="col-sm-2 pr-0 buttons-area"> <i class="fa fa-trash" area-hidden="true"></i> </div></div>';

    function checkAllGroup(val){
        if($(val).is(':checked')){
            $('.checkbox-sync').prop("checked", true);
        }else{
            $('.checkbox-sync').prop("checked", false);
        }
    }

    function syncAll()
    {
        // apply promise for one after one request
        var promises = [];
        $('.checkbox-sync').each(function() {
            if ($(this).is(":checked") && !$(this).is(":disabled")) {
                var date = $(this).val(),
                    request = $.ajax({
                        url: '{{ url('hr/buyer/sync/'.$buyer->id) }}',
                        type: "POST",
                        data : {
                            _token: "{{ csrf_token() }}", 
                            date: date,
                        },
                        beforeSend: function() {
                            $('#date-'+date).addClass("bg-danger").attr('disabled',true);
                            $('#date-'+date+' i').addClass('fa-spin');
                        },
                        success: function(res){
                            $('.count-'+date).text(res.count);
                            $('#date-'+date).removeClass("bg-danger").attr('disabled',false);
                            $('#date-'+date+' i').removeClass('fa-spin');
                            $.notify('Date: '+date+' Data sync successfully!','success');
                        },
                        error: function (reject) {
                        }
                    });
                promises.push(request);
            }
        });

        $.when.apply(null, promises).done(function() {
            // update salary process
            $.ajax({
                url: '{{ url('hr/buyer/salary-process/'.$buyer->id) }}',
                type: "POST",
                data : {
                    _token: "{{ csrf_token() }}", 
                    month: '{{$reqMonth->copy()->format('m')}}',
                    year: '{{$reqMonth->copy()->format('Y')}}'
                },
                beforeSend: function() {
                },
                success: function(res){
                    $.notify('Data synced successfuly! Now salary is being processed');
                },
                error: function (reject) {
                }
            });
        });

    }

    $(document).on('click','#add-btn', function(e){
        $('.holidays-block').append(newdate);
    });

    $(document).on('click','.fa-trash', function(e){
        $(this).parent().parent().remove();
    });

    $('#holiday-buyer').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: '{{ url('hr/buyer/holidays/'.$buyer->id) }}',
            type: "POST",
            data : $('#holiday-buyer').serializeArray(),
            success: function(res){
                if(res.status == 1){
                    $('#sync-area').removeClass('disabled-row');
                    $.notify(res.msg, 'success');
                }else{
                    $.notify(res.msg, 'error');
                }
            },
            error: function (reject) {
            }
        });
    });

        


    
    function sync(date)
    {
        
        $.ajax({
            url: '{{ url('hr/buyer/sync/'.$buyer->id) }}',
            type: "POST",
            data : {
                _token: "{{ csrf_token() }}", 
                date: date,
                process: 1
            },
            beforeSend: function() {
                $('#date-'+date).addClass("bg-danger").attr('disabled',true);
                $('#date-'+date+' i').addClass('fa-spin');
            },
            success: function(res){
                $('.count-'+date).text(res.count);
                $('#date-'+date).removeClass("bg-danger").attr('disabled',false);
                $('#date-'+date+' i').removeClass('fa-spin');
                $.notify('Date: '+date+' Data sync successfully!','success');
            },
            error: function (reject) {
            }
        });
    }
</script>
@endpush
@endsection