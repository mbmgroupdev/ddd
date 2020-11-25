@extends('hr.layout')
@section('title', 'MMR Report')
@section('main-content')
@php 
	if(request()->has('date')){ $date = request()->date; }else{ $date = date('Y-m-d'); }
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
            	<a href="#"> Daily MMR Report </a>
            </li>
            <li class="active"> {{$date}} </li>
        </ul><!-- /.breadcrumb --> 
    </div>
	<div class="panel">
        <div class="panel-heading">
            <h6>MMR Report
            	<div class="pull-right">
            		@php
                        $nextDate = date('Y-m-d', strtotime($date.' +1 day'));
                        $prevDate = date('Y-m-d', strtotime($date.' -1 day'));

                        $prevUrl = url("mmr-report?date=$prevDate");
                        $nextUrl = url("mmr-report?date=$nextDate");
                    @endphp
                    <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous day MMR report" >
                      <i class="las la-chevron-left f-18"></i>
                    </a>
                    <b style="font-weight: normal;">{{ $date }} </b>
                    @if($date < date('Y-m-d'))
                    <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next day MMR report" >
                      <i class="las la-chevron-right f-18"></i>
                    </a>
                    @endif
            	</div>
            </h6>
        </div>
        <div class="panel-body">
        	<div class="row">
        		
	        	<div class="col-sm-6">
		        	<div id="mmr-compare" style="height: 400px;"></div>
	        	</div>
	        	<div class="col-sm-6">
	        		<table style="margin-top: 15px;" class="table tabl-hover table-striped table-bordered">
		        		<thead>
		        			<tr>
		        				<th>Unit</th>
		        				<th class="text-center">Present</th>
		        				<th class="text-center">Operator</th>
		        				<th class="text-center">MMR</th>
		        			</tr>
		        		</thead>
		        		<tbody>
		        			@foreach($unit as $key => $op)
		        			<tr>
		        				<td>
		        					<a href="{{url('hr/reports/attendance_summary_report')}}?unit={{$key}}&date={{$date}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="See details MMR report">
				        				{{$op['name']}}
		        						
		        					</a>
			        			</td>
		        				<td class="text-center">{{$op['present']??0}}</td>
		        				<td class="text-center">{{$op['operator']??0}}</td>
		        				<td class="text-center">{{$op['mmr']??0}}</td>
		        			</tr>
		        			@endforeach
                            <tr>
                                <td>
                                    MBM Combined (MBM,MFW,MBM-2)
                                    @php
                                        
                                    @endphp
                                </td>
                                {{-- <td class="text-center">{{$combpresent}}</td>
                                <td class="text-center">{{$comoperator}}</td>
                                <td class="text-center">{{round(($combpresent/$comoperator),2)}}</td> --}}
                            </tr>
		        		</tbody>
		        	</table>
		        	<h3 class="mb-1 " style="margin: 20px 0;font-size: 14px;font-weight: bold;border-left: 3px solid #099dae;line-height: 18px;padding-left: 10px;">
                        Average MMR 
                        @if($date == date('Y-m-d'))
                            <span style="font-size:11px;font-weight:normal;">(Current)</span>
                        @endif
                    </h3>
                    <div style="padding: 20px 13px;font-size: 40px;font-weight: bold;line-height: 40px;color: #099faf;"></div>
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
        categoryAxis.dataFields.category = "Unit";
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
        series.dataFields.valueY = "MMR";
        series.dataFields.categoryX = "Unit";
        series.name = "Unit";
        series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
        series.columns.template.fillOpacity = .8;

        var columnTemplate = series.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;

    }); // end am4core.ready()
}
</script>
@endpush
@endsection