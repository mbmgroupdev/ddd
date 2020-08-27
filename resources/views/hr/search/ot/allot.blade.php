
<div class="panel panel-info col-sm-12">
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                MBM Group 
            </li>
        </ul>
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($ot_data)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        
   	    	<div class="col-sm-3 col-xs-12">
   	    		
				<div class="search_unit col-sm-12 pricing-box" style="padding:0;">
					<div class="widget-box widget-color-dark">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total Unit</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number">{{ count($unit_list) }}</span>
							</div>
						</div>
					</div>
				</div>

				<div class="search_ot_hour col-sm-12 pricing-box" style="padding:0;">
					<div class="widget-box widget-color-orange ">
						<div class="widget-header">
								<h5 class="widget-title bigger lighter">
									Total Employee
								</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number">{{ $employee }}</span>
							</div>
						</div>
					</div>
				</div>

				<div class=" search_emp col-sm-12 pricing-box" style="padding:0;">
					<div class="widget-box widget-color-green2 ">
						<div class="widget-header">
								<h5 class="widget-title bigger lighter">Total OT</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number">
								{{ Custom::numberToTime($ot_data['dayot']) }} 
								Hour
							</div>
						</div>
					</div>
				</div>
   	    	</div>
   	    	<div class="col-sm-6 col-xs-12">	           	    		
				<div class="widget-box widget-color-green2">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter">Over Time (Hour)</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center" id="ot">
							<canvas id="otgraph" style="height:300px!important;"></canvas>
							<script type="text/javascript">
								$(function(){
							        var ctxL = document.getElementById("otgraph").getContext('2d');

							        var myLineChart = new Chart(ctxL, {
							            type: 'line',
							            data: {

							                labels: [ '{{ $ot_data['day2'] }}','{{ $ot_data['day1'] }}','{{ $ot_data['day'] }}'],
							                datasets: [{ 
									        data: [{{ $ot_data['dayot2'] }},{{ $ot_data['dayot1'] }},{{ $ot_data['dayot'] }}],
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
                                                        labelString: 'Hour(s)'
                                                      }
                                                }],
                                                xAxes: [{
                                                    scaleLabel: {
                                                        display: true,
                                                        labelString: 'Date'
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
		<div class="row">
			<div class="col-sm-offset-3 col-sm-6">
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
                type: 'Day',
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
