@extends('hr.layout')
@section('title', 'Salary')

@section('main-content')
@push('css')
  <style>
    .single-employee-search {
      margin-top: 82px !important;
    }
    .view:hover, .view:hover{
      color: #ccc !important;
      
    }
    .grid_view{

    }
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .iq-card .iq-card-header {
      margin-bottom: 10px;
      padding: 15px 15px;
      padding-bottom: 0px;
    }
    .modal-h3{
      line-height: 15px !important;
    }
    .select2-container .select2-selection--single, .month-report { height: 30px !important;}
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 30px !important;}
    
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
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Salary Info</li>
            </ul>
        </div>
        <div class="page-content"> 
            
            <div class="row">
                <div class="col">
                  <form role="form" method="get" action="#" id="formReport">
                    <div class="iq-card" id="result-section">
                      <div class="iq-card-header d-flex mb-0">
                         <div class="iq-header-title w-100">
                            <div class="row">
                              <div style="width: 10%; float: left; margin-left: 15px; margin-top: 2px;">
                                <div id="result-section-btn">
                                  <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                  
                                </div>
                              </div>
                              <div class="text-center" style="width: 47%; float: left">
                                {{-- <h4 class="card-title capitalize inline">
                                  @foreach(array_reverse($months) as $k => $i)
                                    <a class="nav-year @if($k== $yearMonth) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" data-year-month="{{ date('Y-m', strtotime($k)) }}" title="" data-original-title="Salary of {{$i}}" >
                                        {{$i}}
                                    </a>
                                  @endforeach
                                </h4> --}}
                              </div>
                              {{-- <input type="hidden" id="yearMonth" name="year_month" value="{{ $yearMonth }}"> --}}
                              <input type="hidden" id="reportFormat" name="report_format" value="1">
                              <div style="width: 40%; float: left">
                                <div class="row">
                                  <div class="col-3 p-0">
                                      <div class="form-group has-float-label has-required ">
                                        <input type="month" class="report_date form-control month-report" id="yearMonth" name="year_month" placeholder=" Month-Year" required="required" value="{{ $yearMonth }}" max="{{ date('Y-m') }}" autocomplete="off">
                                        <label for="yearMonth">Month</label>
                                      </div>
                                  </div>
                                  <div class="col-4 pr-0">
                                    <div class="format">
                                      
                                      <div class="form-group has-float-label select-search-group mb-0">
                                          <?php
                                            
                                            $type = ['as_unit_id'=>'Unit','as_location'=>'Location','as_department_id'=>'Department','as_designation_id'=>'Designation','as_section_id'=>'Section','as_subsection_id'=>'Sub Section'];
                                            if(auth()->user()->hasRole('Super Admin')){
                                              $selectGroup = 'as_unit_id';
                                            }else{
                                              $selectGroup = 'as_department_id';
                                            }
                                          ?>
                                          
                                          {{ Form::select('report_group', $type, $selectGroup, ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                          <label for="reportGroupHead">Report Format</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-5 pl-0">
                                    <div class="text-right">
                                      <a class="btn view no-padding clear-filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear Filter">
                                        <i class="las la-redo-alt" style="color: #f64b4b; border-color:#be7979"></i>
                                      </a>
                                      <a class="btn view no-padding filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Advanced Filter">
                                        <i class="fa fa-filter"></i>
                                      </a>
                                      <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                        <i class="las la-th-large"></i>
                                      </a>
                                      <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                        <i class="las la-list-ul"></i>
                                      </a>
                                      
                                    </div>
                                  </div>
                                </div>
                                
                              </div>
                            </div>
                         </div>
                      </div>
                      <div class="iq-card-body no-padding">
                        <div class="result-data" id="result-data">
                          
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
{{-- modal employee salary --}}
  
<div class="modal right fade" id="right_modal_lg-group" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg-group">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
      <i class="las la-chevron-left"></i>
    </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right-group"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result content-result" id="content-result-group">
          
        </div>
      </div>
      
    </div>
  </div>
</div>
  {{--  --}}
@include('common.right-modal')
@include('common.right-navbar')
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
  @if(!Request::get('unit')) 
    salaryFilter();
  @endif

  function salaryFilter(){
    $("#result-data").html(loaderContent);
    $("#single-employee-search").hide();
    var format = $('input[name="report_format"]').val();
    $('html, body').animate({
        scrollTop: $("#result-data").offset().top
    }, 2000);
    var data = $("#filterForm").serialize() + '&' + $("#formReport").serialize();
    $.ajax({
        type: "POST",
        url: '{{ url("hr/reports/salary-report") }}',
        data: data, // serializes the form's elements.
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        success: function(response)
        {
          // console.log(response);
          if(response !== 'error'){
            $("#result-data").html(response);
          }else{
            // console.log(response);
            $("#result-data").html('');
          }
          if(format == 0 && response !== 'error'){
            $("#single-employee-search").show();
            $('.list_view').addClass('active').attr('disabled', true);
            $('.grid_view').removeClass('active').attr('disabled', false);
          }else{
            $("#single-employee-search").hide();
            $('.grid_view').addClass('active').attr('disabled', true);
            $('.list_view').removeClass('active').attr('disabled', false);
          }
          
        },
        error: function (reject) {
            console.log(reject);
        }
    });
  }
  $(".grid_view, .list_view").click(function() {
    var value = $(this).attr('id');
    // console.log(value);
    $('input[name="report_format"]').val(value);
    $('input[name="employee"]').val('');
    salaryFilter();
  });
    
  $("#reportGroupHead").on("change", function(){
    var group = $(this).val();
    $("#reportGroup").val(group);
    salaryFilter();
  });

  $("#yearMonth").on("change", function(){
    salaryFilter();
  });
  // $(document).on('click', '.nav-year', function(event) {
  //   let month = $(this).data('year-month');
  //   $("#yearMonth").val(month);
  //   salaryFilter();
  //   $(".nav-year").removeClass('bg-primary text-white');
  //   $(this).addClass('bg-primary text-white');
  // });
  $(document).on('click', '.filter', function(event) {
    $('#right_modal_navbar').modal('show');
    $('#navbar-title-right').html('Advanced Filter');
  });

  $(document).on('click', '.clear-filter', function(event) {
    var yearMonth = $("#yearMonth").val();
    window.location.href = '{{ url("hr/reports/salary?year_month=") }}'+yearMonth;
  });
</script>
@endpush
@endsection