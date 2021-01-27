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
                <li class="top-nav-btn btn btn-sm btn-primary" data-toggle="modal" data-target="#right_modal_template">Create</li>
            </ul>
        </div>
    </div>

    

    <div class="row">
        @if(count($templates) > 0)
            @foreach($templates as $key => $tem)
            <div class="col-sm-4">
                <a href="{{url('hr/buyer/sync/'.$tem->id)}}" class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body text-center">
                        <div class="iq-info-box align-items-center p-3">
                            <div class="info-text">
                                <h3>{{$tem->template_name}}</h3>
                                <span>{{$unit[$tem->hr_unit_id]}}</span><br>
                                <span>OT Hour: {{numberToTimeClockFormat($tem->base_ot)}}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        @else
            <div class="col-sm-4">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-body">
                        <div class="iq-info-box d-flex align-items-center p-3">
                            <div class="info-image mr-3">
                                <img src="images/page-img/30.png" class="img-fluid" alt="image-box">
                            </div>
                            <div class="info-text">
                                <h3>No Template</h3>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
            
</div>
<div class="modal right fade" id="right_modal_template" tabindex="-1" role="dialog" aria-labelledby="right_modal_template">
    <div class="modal-dialog modal-lg right-modal-width" role="document" > 
        <div class="modal-content">
            <div class="modal-header">
                <a class="view prev_btn-job" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
                    <i class="las la-chevron-left"></i>
                </a>
                <h5 class="modal-title right-modal-title text-center" id="modal-title-right"> <strong class="text-primary">Buyer Template</strong> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding-top: 0;">
                <div class="modal-content-result" id="content-result">
                    <form id="buyerForm" class="needs-validation buyerForm" novalidate role="form" method="post" action="" >
                        @csrf
                        <div class="panel">
                            
                            <div class="panel-body " id="create-template">
                                <div class="row justify-content-center">
                                    <div class="col-5">
                                        <h2 class="font-weight-bold text-center mb-3">Buyer Template</h2>
                                        <div class="form-group has-float-label has-required">
                                            <input type="template_name" class="form-control" id="template_name" name="template_name" placeholder="Enter Template Name" required="required" value="" autocomplete="off" />
                                            <label  for="template_name"> Template Name </label>
                                        </div>
                                        <div class="form-group has-float-label has-required select-search-group">
                                            {{ Form::select('hr_unit_id', $unit, null, ['placeholder'=>'Select Unit', 'id' => 'hr_unit_id', 'class'=>'hr_unit_id no-select col-xs-12','style', 'required'=>'required']) }}
                                            <label  for="hr_unit_id"> Unit ID </label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                            <input type="table_alias" class="form-control" id="table_alias" name="table_alias" placeholder="Enter Template Name" required="required" value="" autocomplete="off" />
                                            <label  for="table_alias"> Table Alias </label>
                                        </div>
                                        <div class="form-group has-float-label has-required">
                                            <input type="base_ot" class="form-control" id="base_ot" name="base_ot" placeholder="Enter Template Name" required="required" value="" autocomplete="off" />
                                            <label  for="base_ot"> Base OT </label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Create Buyer Template</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
      
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';

    function selectedGroup(e, body, inputUrl){
        var part = e;
        var urldata = inputUrl+part;
        $("#modal-title-right-extra").html(' '+body+' Report Details');
        $('#right_modal_lg').modal('show');
        $("#content-result-extra").html(loaderContent);
        $.ajax({
            url: '{{ url('hr/reports/daily-attendance-activity-report') }}?'+urldata+'&report_format=0',
            type: "GET",
            success: function(response){
                // console.log(response);
                if(response !== 'error'){
                    setTimeout(function(){
                        $("#content-result-extra").html(response);
                    }, 1000);
                }else{
                    console.log(response);
                }
            }
        });

    }

    $('#buyerForm').on('submit', function(e){
        e.preventDefault();
        $('.app-loader').show();
        $.ajax({
            url: '{{ url('hr/buyer/generate') }}',
            type: "POST",
            data : $('#buyerForm').serializeArray(),
            success: function(response){
                console.log(response);
            }
        });
        
    });
</script>
@endpush
@endsection