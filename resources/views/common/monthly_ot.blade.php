@extends('hr.layout')
@section('title', 'Monthly OT Report')
@section('main-content')
@push('css')
    <style>
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
                <a href="#"> <b>Monthly OT Report</b> </a>
            </li>
            <li class="active"> {{ date('M, y', strtotime($month)) }} </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="panel">
        <div class="panel-heading">
            {{-- <h6>Monthly OT Report --}}
            <h6>
                <div class="row">
                    <div class="col-4">
                        
                    </div>
                    <div class="col-6">
                        @foreach(array_reverse($months) as $k => $i)
                            <a href="{{url('hr/reports/monthly-ot-report?month='.$k)}}" class="nav-year @if($k== $month) bg-primary text-white @endif" data-toggle="tooltip" data-placement="top" title="" data-original-title="Report of {{$i}}" >
                                {{$i}}
                            </a>
                        @endforeach
                    </div>
                    <div class="col-2">
                        <div class="text-right">
                            {{-- <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                              <i class="las la-th-large"></i>
                            </a>
                            <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                              <i class="las la-list-ul"></i>
                            </a> --}}
                            
                        </div>
                    </div>
                </div>
                
            </h6>
            
        </div>
        <div class="panel-body">
            <div class="row justify-content-center">
                <div class="col-1">
                        {{-- <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="customCheck-t" value="0">
                            <label class="custom-control-label" for="customCheck-t">All</label>
                       </div> --}}
                        @foreach($unitList as $key => $u)
                        {{-- <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="customCheck-t{{$key}}" value="{{$key}}">
                            <label class="custom-control-label" for="customCheck-t{{$key}}">{{$u}}</label>
                       </div> --}}
                       <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                          <input type="radio" id="customRadio-{{$key}}" name="customRadio-10" class="custom-control-input bg-primary" @if($key == $selectUnit) checked @endif>
                          <label class="custom-control-label" for="customRadio-{{$key}}"> {{$u}} </label>
                       </div>
                       @endforeach
                </div>
                <div class="col-sm-5 p-0">
                    <div id="mmr-compare" style="height: 500px;"></div>
                </div>
                <div class="col-sm-6 p-0">
                    <div id="mmr-compare1" style="height: 500px;"></div>
                </div>
                <div class="col-1"></div>
            </div>
            <div class="row">
                <div class="col-sm-11">
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
            chart.svgContainer.autoResize = false;
            chart.text = "Maximum OT ";
            // content
            var topContainer = chart.chartContainer.createChild(am4core.Container);
            var dateTitle = topContainer.createChild(am4core.Label);
            dateTitle.text = "January 1st, 2018 -- March 31st, 2018";
            dateTitle.fontWeight = 600;
            dateTitle.align = "right";
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

        });
    }
    if (jQuery('#mmr-compare1').length) {
        am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create("mmr-compare1", am4charts.XYChart);
            chart.svgContainer.autoResize = false;
            chart.colors.list = [am4core.color("#089bab"), ];

            // Add data
            chart.data = @php echo json_encode($chart_ot) @endphp;

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
                                Total OT: {valueY}`;
            series.dataFields.valueY = "totalOt";
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