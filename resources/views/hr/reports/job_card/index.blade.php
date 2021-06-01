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
    td .label {
      padding: 0px 5px !important;
      background: #daf0f3 !important;
      color: #000 !important;
      border-radius: 4px !important;
      font-weight: 400;
    }
    .bg-default{
      background-color: #fff !important;
      border: 1px solid #000;
    }
    .table-bordered th, .table-bordered td {
      border: 1px solid #aeaeae99;
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
        <div class="iq-card iq-accordion-block mb-3 accordion-active">
            <div class="active-mat clearfix">
              <div class="container-fluid">
                 <div class="row">
                    <div class="col-sm-12"><a class="accordion-title"><span class="header-title" style="line-height:1.8;"> Employee Wise </span> </a></div>
                 </div>
              </div>
            </div>
            <div class="accordion-details">
                <div class="row">
                    <div class="col">
                      <form role="form" method="get" action="#" class="attendanceReport" id="attendanceReportEmp">
                        <div class="panel mb-0">
                            
                            <div class="panel-body pb-5" >
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'allassociates no-select col-xs-12','style', 'required'=>'required']) }}
                                            <label  for="associate"> Associate's ID </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <input type="month" class="form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" max="{{ date('Y-m') }}" />
                                            <label  for="month"> Month </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                        
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
    <div class="page-content content-show" style="display: none;">
      <div class="panel w-100">
        <div class="panel-body">
          <div class="offset-1 col-10 h-min-400">
            <div id="result-data"></div>
          </div>
        </div>
      </div>
      
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
  @if(Request::get('associate') !== null && Request::get('month_year') !== null) 
    jobCardPreview();
  @endif

  function jobCardPreview(){

    if($("#associate").val() === null && $("#month") === null){
      $.notify('Please Select Associate Id & Month', 'error');
      return false;
    }
    $(".content-show").show();
    $("#result-data").html(loaderContent);
    $('html, body').animate({
        scrollTop: $("#result-data").offset().top
    }, 2000);
    var data = $("#attendanceReportEmp").serialize();
    $.ajax({
        type: "GET",
        url: '{{ url("hr/reports/job-card-report") }}',
        data: data, // serializes the form's elements.
        success: function(response)
        {
          if(response !== 'error'){
            $("#result-data").html(response);
          }else{
            $("#result-data").html('');
          }
        },
        error: function (reject) {
            console.log(reject);
        }
    });
  }
  $(document).on('click', '.activityReportBtn', function(event) {
    event.preventDefault();
    jobCardPreview();
  });
  $(document).on('click', '.next_btn', function(event) {
    var monthYear = $('input[name="month_year"]').val();
    var monthAfter = moment(monthYear).add(1 , 'month').format("YYYY-MM");
    $('input[name="month_year"]').val(monthAfter);
    jobCardPreview();
  });

  $(document).on('click', '.prev_btn', function(event) {
    var monthYear = $('input[name="month_year"]').val();
    var monthAfter = moment(monthYear).subtract(1 , 'month').format("YYYY-MM");
    $('input[name="month_year"]').val(monthAfter);
    jobCardPreview();
  });
</script>
@endpush
@endsection