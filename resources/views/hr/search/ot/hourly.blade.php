<style type="text/css">
	.infobox-data-number{
	    display: block;
	    font-size: 22px;
	    margin: 2px 0 4px;
	    position: relative;
	    text-shadow: 1px 1px 0 rgba(0,0,0,.15);
	}
	.pricing-box:hover{
		cursor: pointer;
	}
	.search_unit.col-sm-12.pricing-box {
	    height: 124px;
	}

</style>
<div class="panel panel-info col-sm-12">
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    <a href="#" class="search_all" data-category="{{ $request['category'] }}" data-type="{{ $request['type'] }}"> MBM Group </a>
                </li>
                @if(isset($request1['unit']))
                    <li>
                        <a href="#" class="search_area" data-unit="{{ $request1['unit'] }}">
                            {{ $data['unit']->hr_unit_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['area_id']))
                    <li>
                         <a href="#" class="search_dept" data-area="{{ $request1['area'] }}">
                            {{ $data['area']->hr_area_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['department']))
                    <li>
                        <a href="#" class="search_floor" data-department="{{ $request1['department'] }}">
                            {{ $data['department']->hr_department_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['floor']))
                    <li>
                        <a href="#" class="search_section" data-floor="{{ $request1['floor'] }}">
                            {{ $data['floor']->hr_floor_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['section']))
                    <li>
                        <a href="#" class="search_subsection" data-section="{{ $request1['section'] }}">
                            {{ $data['section']->hr_section_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['shiftcode']))
                <li>
                	<a href="#" class="search_ot_shift"
                	@if(isset($request1['unit'])) 
                		data-unit="{{ $request1['unit'] }}"
                	@endif
                	@if(isset($request1['area'])) 
                		data-area="{{ $request1['area'] }}"
                	@endif
                	@if(isset($request1['department'])) 
                		data-department="{{ $request1['department'] }}"
                	@endif
                	@if(isset($request1['floor'])) 
                		data-floor="{{ $request1['floor'] }}"
                	@endif
                	@if(isset($request1['section'])) 
                		data-section="{{ $request1['section'] }}"
                	@endif
                	>  {{$data['shift']->hr_shift_name??''}} 
                	</a>
                </li>
                @endif
                <li>Hourly OT Employee</li>
        </ul>
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($ot_data)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
           	<div class="col-sm-10">
           	    <center>
           	    	<h3>
           	    	@if(isset($data['shift']))
           	    		{{$data['unit']->hr_unit_name}} - {{$data['shift']->hr_shift_name??''}} Shift 
           	    	@else
           	    	   MBM Garments Ltd.
           	    	@endif
           	    	<span style="font-size:13px;font-weight:400;">(Hourly Employee Count)</span>
           	    	</h3>
           	    </center>
           	    <hr>
           	    <div class="row">
           	        <div class="col-sm-4 col-xs-12">
           	        	<div class="widget-box widget-color-green2">
							<div class="widget-header">
								<h5 class="widget-title bigger lighter">Employee Count</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center" style="">
				           	        <table class="table table-bordered  table-hover" style="margin:0!important;height:286px;cursor:pointer;">
				           	        	<tbody>
				           	        		<tr class="search_emp" data-hour="1">
				           	        			<td>1 Hour</td>
				           	        			<td>{{$ot_data['1']}}</td>
				           	        		</tr>
				           	        		<tr class="search_emp" data-hour="2">
				           	        			<td>2 Hour</td>
				           	        			<td>{{$ot_data['2']}}</td>
				           	        		</tr>
				           	        		<tr class="search_emp" data-hour="3">
				           	        			<td>3 Hour</td>
				           	        			<td>{{$ot_data['3']}}</td>
				           	        		</tr>	           	        		
				           	        		<tr class="search_emp" data-hour="4">
				           	        			<td>4 Hour</td>
				           	        			<td>{{$ot_data['4']}}</td>
				           	        		</tr>
				           	        		<tr class="search_emp" data-hour="5">
				           	        			<td>5 Hour</td>
				           	        			<td>{{$ot_data['5']}}</td>
				           	        		</tr>
				           	        		<tr class="search_emp" data-hour="6">
				           	        			<td>6 Hour</td>
				           	        			<td>{{$ot_data['6']}}</td>
				           	        		</tr>
				           	        		<tr class="search_emp" data-hour="7">
				           	        			<td>7 Hour</td>
				           	        			<td>{{$ot_data['7']}}</td>
				           	        		</tr>
				           	        		<tr class="search_emp" data-hour="8">
				           	        			<td>8 Hour</td>
				           	        			<td>{{$ot_data['8']}}</td>
				           	        		</tr>
				           	        	</tbody>
				           	        </table>
								</div>
							</div>
						</div>
           	       	
           	        </div>
           	    	
           	    	
           	    	<div class="col-sm-8 col-xs-12">	           	    		
						<div class="widget-box widget-color-green2">
							<div class="widget-header">
								<h5 class="widget-title bigger lighter">Over Time (Hour)</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<canvas id="otgraph" style="height:300px!important;"></canvas>
									<script type="text/javascript">
										$(function(){
									        var ctxL = document.getElementById("otgraph").getContext('2d');

									        var myLineChart = new Chart(ctxL, {
									            type: 'line',
									            data: {

									                labels: ['1','2','3','4','5','6','7','8'],
									                datasets: [{ 
											        data: [{{$ot_data['1']}},{{$ot_data['2']}},{{$ot_data['3']}},{{$ot_data['4']}},{{$ot_data['5']}},{{$ot_data['6']}},{{$ot_data['7']}},{{$ot_data['8']}}],
											        label: "Over Time (Hour)",
											        borderColor: "#3e95cd",
											        fill: false
											      }
											    ]
									            },
									            options: {
											        scales: {
                                                        yAxes: [{
                                                            ticks: {
                                                                beginAtZero: true
                                                            },
                                                            afterFit: function(scale) {
                                                               scale.width = 80  //<-- set value as you wish 
                                                            },
                                                            scaleLabel: {
                                                                display: true,
                                                                labelString: 'Employee(s)'
                                                              }
                                                        }],
                                                        xAxes: [{
                                                            scaleLabel: {
                                                                display: true,
                                                                labelString: 'Hour(s)'
                                                              }
                                                        }]
                                                    },
											        layout: {
											            padding: {
											                left: 0,
											                right: 50,
											                top: 0,
											                bottom: 0
											            }
											        },
												    legend: {
											            display: true,
											            position: 'bottom'
											        }
											    }
									        });
									    });
									</script>
								</div>
							</div>
						</div>
           	    	</div>
           	    	
           	    </div>
		  	</div>
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
<script>
function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_ot_search_print_page')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: result,
                type: 'Hour',
                title: pagetitle
            },
            success: function(data) {
                $('#printOutputSection').html(data);
                var divToPrint=document.getElementById('printOutputSection');
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
            }
        });
    }
</script>
