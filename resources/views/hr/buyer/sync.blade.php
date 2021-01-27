@extends('hr.layout')
@section('title', 'Buyer Templates')
@section('main-content')
@push('css')
    
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

    <div class="page-content">
        <div class="panel panel-success" style=""> 
            <div class="panel-heading">
                <h6>Data Sync: {{$buyer->template_name}}</h6>
            </div>
            <div class="panel-body">
                
                <div class="row">
                    <div class="col-sm-4">
                        sync
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
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
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script type="text/javascript">
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';

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
                            $('#date-'+date).attr('disabled',true);
                            $('#date-'+date+' i').addClass('fa-spin');
                        },
                        success: function(res){
                            $('.count-'+date).text(res.count);
                            $('#date-'+date).attr('disabled',false);
                            $('#date-'+date+' i').removeClass('fa-spin');
                            $.notify('Date: '+date+' Data sync successfully!','success');
                        },
                        error: function (reject) {
                        }
                    });
                promises.push(request);
            }
        });

        /*$.when.apply(null, promises).done(function() {
            $.notify('Increment saved successfully!','success');
        });*/

    }


    
    function sync(date)
    {
        
        $.ajax({
            url: '{{ url('hr/buyer/sync/'.$buyer->id) }}',
            type: "POST",
            data : {
                _token: "{{ csrf_token() }}", 
                date: date,
            },
            beforeSend: function() {
                $('#date-'+date).attr('disabled',true);
                $('#date-'+date+' i').addClass('fa-spin');
            },
            success: function(res){
                $('.count-'+date).text(res.count);
                $('#date-'+date).attr('disabled',false);
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