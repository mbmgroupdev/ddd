@extends('hr.layout')
@section('title', 'Monthly OT Report')
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
                <a href="#"> Monthly OT Report </a>
            </li>
            <li class="active"> {{$month}} </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h6>Monthly OT Report
                <div class="pull-right">
                    @php
                        $nextDate = date('Y-m', strtotime($month.' +1 month'));
                        $prevDate = date('Y-m', strtotime($month.' -1 month'));

                        $prevUrl = url("hr/reports/monthly-ot-report?month=$prevDate");
                        $nextUrl = url("hr/reports/monthly-ot-report?month=$nextDate");
                    @endphp
                    <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month OT Report" >
                      <i class="las la-chevron-left f-18"></i>
                    </a>
                    <b style="font-weight: normal;">{{ $month }} </b>
                    @if($month < date('Y-m'))
                    <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month MMR report" >
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
                <div class="col-sm-10">
                    <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-table')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button> <strong> Summery OT Report </strong> <hr>
                    <br>
                    <div id="print-table">
                        
                        <style type="text/css">
                              .table{
                                width: 100%;
                              }
                              a{text-decoration: none;}
                              .table-bordered {
                                  border-collapse: collapse;
                              }
                              .table-bordered th,
                              .table-bordered td {
                                border: 1px solid #777 !important;
                                padding:5px;
                              }
                              .no-border td, .no-border th{
                                border:0 !important;
                                vertical-align: top;
                              }
                              .f-16 th, .f-16 td, .f-16 td b{
                                font-size: 16px !important;
                              }
                          </style>
                        <table  class="table table-bordered table-stripped">
                            <tr style="text-align: center;">
                                <th>Date</th>
                                <th>OT Employee</th>
                                <th>Total</th>
                                <th>Maximum</th>
                                <th>Average</th>
                            </tr>
                            @foreach($otdata as $key => $ot)
                                <tr>
                                    @php
                                        $year = date('Y', strtotime($month));
                                        $date = date('Y-m-d', strtotime($ot['date'].' '.$year));
                                    @endphp
                                    <td>{{$ot['date']}}</td>
                                    <td style="text-align: center;">{{$ot['emp']}}</td>
                                    <td style="text-align: right;"> {{numberToTimeClockFormat($ot['ot_hour'])}}</td>
                                    <td style="text-align: center;"><a target="_blank" href='{{ url("hr/reports/daily-attendance-activity?report_type=ot&date=$date")}}'> {{numberToTimeClockFormat($ot['max'])}}</a></td>
                                    <td style="text-align: center;"> {{numberToTimeClockFormat($ot['avg'])}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
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
                            Maximum OT: {valueY}`;
        series.dataFields.valueY = "Avg";
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