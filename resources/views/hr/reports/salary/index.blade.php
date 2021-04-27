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
      padding-bottom: 8px;
    }
    .modal-h3{
      line-height: 15px !important;
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
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Salary Info</li>
            </ul>
        </div>
        <div class="page-content"> 
            
            <div class="row">
                <div class="col">
                  <form role="form" method="get" action="#" id="salaryReport">
                    <div class="iq-card" id="result-section">
                      <div class="iq-card-header d-flex mb-0">
                         <div class="iq-header-title w-100">
                            <div class="row">
                              <div class="col-2 pr-0">
                                <div id="result-section-btn">
                                  <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                                  <button class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download">
                                    <i class="fa fa-file-excel-o"></i>
                                  </button>
                                </div>
                              </div>
                              <div class="col-7 p-0 text-center">
                                <h4 class="card-title capitalize inline">
                                  @foreach(array_reverse($months) as $k => $i)
                                    <a class="nav-year @if($k== $yearMonth) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" data-year-month="{{ date('Y-m', strtotime($k)) }}" title="" data-original-title="Salary of {{$i}}" >
                                        {{$i}}
                                    </a>
                                  @endforeach
                                </h4>
                              </div>
                              <input type="hidden" id="yearMonth" name="year_month" value="{{ $yearMonth }}">
                              <input type="hidden" id="reportFormat" name="report_format" value="1">
                              <div class="col-3">
                                <div class="row">
                                  <div class="col-5 pr-0">
                                    <div class="format">
                                      <div class="form-group has-float-label select-search-group mb-0">
                                          <?php
                                              $type = ['unit_id'=>'Unit','line_id'=>'Line','floor_id'=>'Floor','department_id'=>'Department','designation_id'=>'Designation','section_id'=>'Section'];
                                          ?>
                                          {{ Form::select('report_group', $type, 'unit_id', ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                          <label for="reportGroupHead">Report Format</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-7 pl-0">
                                    <div class="text-right">
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

@include('common.right-modal')
@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript">
  @if(!Request::get('unit')) 
    salaryFilter();
  @endif

  function salaryFilter(){
    $("#result-data").html(loaderContent);
    $("#single-employee-search").hide();
    $('html, body').animate({
        scrollTop: $("#result-data").offset().top
    }, 2000);
    var form = $("#salaryReport");
    $.ajax({
        type: "GET",
        url: '{{ url("hr/reports/salary-report") }}',
        data: form.serialize(), // serializes the form's elements.
        success: function(response)
        {
          // console.log(response);
          if(response !== 'error'){
            $("#result-data").html(response);
          }else{
            // console.log(response);
            $("#result-data").html('');
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
    $("#reportformat").val(value);
    $('input[name="employee"]').val('');
    salaryFilter();
  });

  $(document).on('click', '.filter', function(event) {
    console.log('hi');
  });
    
  $("#reportGroupHead").on("change", function(){
    var group = $(this).val();
    $("#reportGroup").val(group);
    salaryFilter();
  });
  $(document).on('click', '.nav-year', function(event) {
    let month = $(this).data('year-month');
    $("#yearMonth").val(month);
    salaryFilter();
    $(".nav-year").removeClass('bg-primary text-white');
    $(this).addClass('bg-primary text-white');
  });
</script>
@endpush
@endsection