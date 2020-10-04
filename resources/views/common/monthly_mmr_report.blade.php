@extends('hr.layout')
@section('title', 'Monthly MMR Report')
@section('main-content')
@php 
    if(request()->has('month')){ $month = request()->month; }else{ $month = date('Y-m'); }
@endphp
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Home</a>
            </li> 
            <li>
                <a href="#"> Monthly MMR Report </a>
            </li>
            <li class="active"> {{$month}} </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h6>Monthly MMR Report
                <div class="pull-right">
                    @php
                        $nextDate = date('Y-m', strtotime($month.' +1 month'));
                        $prevDate = date('Y-m', strtotime($month.' -1 month'));

                        $prevUrl = url("hr/reports/monthly-mmr-report?month=$prevDate");
                        $nextUrl = url("hr/reports/monthly-mmr-report?month=$nextDate");
                    @endphp
                    <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous month mmr Report" >
                      <i class="las la-chevron-left f-18"></i>
                    </a>
                    <b style="font-weight: normal;">{{ $month }} </b>
                    @if($month < date('Y-m'))
                    <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next month MMR report" >
                      <i class="las la-chevron-right f-18"></i>
                    </a>
                    @endif
                </div>
            </h6>
        </div>
        <div class="panel-body">
            <div class="row justify-content-center">
                
                <div class="col-sm-10 p-0">
                    <div id="mmr-compare" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@push('js')
<!-- am core JavaScript -->
<script src="{{ asset('assets/js/core.js') }}"></script>
<!-- am charts JavaScript -->
<script src="{{ asset('assets/js/charts.js') }}"></script>

<script src="{{ asset('assets/js/animated.js') }}"></script>
<script>
    function printDiv(divName)
    {   
        

        var mywindow=window.open('','','width=800,height=800');
        
        mywindow.document.write('<html><head><title>Print Contents</title>');
        mywindow.document.write('</head><body>');
        mywindow.document.write(document.getElementById(divName).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close();  
        mywindow.focus();           
        mywindow.print();
        mywindow.close();
    }
    if (jQuery('#mmr-compare').length) {
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("mmr-compare", am4charts.XYChart);
        chart.colors.list = [am4core.color("#089bab"), ];

        // Add data
        chart.data = @php echo json_encode($chart_data) @endphp;

        // Create axes

        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "Date";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 30;

        categoryAxis.renderer.labels.template.adapter.add("dy", function(dy, target) {
            if (target.dataItem && target.dataItem.index & 2 == 2) {
                return dy + 25;
            }
            return dy;
        });

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        var tooltipText = `[bold]Date : {categoryX}[/]
                            ----
                            MMR: {valueY}`;
        series.dataFields.valueY = "MMR";
        series.dataFields.categoryX = "Date";
        series.name = "Date";
        series.columns.template.tooltipText = tooltipText; //"{categoryX}: [bold]{valueY} Hour[/]";
        series.columns.template.fillOpacity = .8;

        var columnTemplate = series.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;

    }); // end am4core.ready()
}
</script>
@endpush
@endsection