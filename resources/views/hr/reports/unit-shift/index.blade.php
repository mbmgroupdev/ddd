@extends('hr.layout')
@section('title', 'Unit Wise Shift')
@section('main-content')
@push('css')
    <style>
        .panel {
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
          <a href="#">Reports</a>
        </li>
        <li class="active"> Unit Wise Shift Report</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col">
                <form class="widget-container-col" role="form" id="activityReport" method="get" action="#">
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group has-float-label select-search-group">
                                        {{ Form::select('unit', $unitList, '', ['placeholder'=>'All', 'id'=>'unit', 'class'=> 'no-select col-xs-12','style']) }}
                                        <label  for="unit"> Unit </label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="date" class=" form-control" id="day" name="date" placeholder="Y" required="required" value="{{ date('Y-m-d') }}" autocomplete="off" />
                                        
                                        <label  for="day"> Date </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    <div id="print_pdf" class="custom-control-inline" style="display: none;">
                                        <button type="button" onclick="printMe('result-data')" title="Print" class="btn btn-warning">
                                            <i class="fa fa-print"></i> 
                                        </button> 
                                    </div>
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
            <div class="col">
                <div class="result-data" id="result-data"></div>
            </div>
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script type="text/javascript">
    function printMe(el){ 
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<html><head></head><body style="font-size:8px;">');
        myWindow.document.write(document.getElementById(el).innerHTML);
        myWindow.document.write('</body></html>');
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }
    var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    $('#activityReport').on('submit', function(e) {
        $("#print_pdf").hide();
        $("#result-data").html(loader);
        e.preventDefault();
        var unit = $('select[name="unit"]').val();
        var date = $('input[name="date"]').val();
        var form = $("#activityReport");
        var flag = 0;
        if(unit === ''){
            unit = 'all';
        }
        if(date === ''){
          flag = 1;
        }
        if(flag === 0){
            $('html, body').animate({
                scrollTop: $("#result-data").offset().top
            }, 2000);
            $.ajax({
                url: '/hr/reports/unit-wise-shift-report',
                type: "GET",
                data: {
                    unit: unit,
                    date:date
                },
                success: function(response){
                    // console.log(response);
                    $("#result-data").html(response);
                }
            });
        }else{
          $.notify('Date Field is required', 'error');
          $("#result-data").html('');
        }
    });

</script>
@endpush
@endsection