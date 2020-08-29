<style type="text/css">
	.infobox-data-number{
	    display: block;
	    font-size: 22px;
	    margin: 2px 0 4px;
	    position: relative;
	    text-shadow: 1px 1px 0 rgba(0,0,0,.15);
	}
	h5 {
		font-weight: bold !important;
	}
	@media print {
        #printOutputSection {display: block;}
    }
</style>
<div class="panel panel-info col-sm-12 col-xs-12">
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                 MBM Group
            </li>
        </ul>
        @if(count($statusSalary)> 1 || !empty($joinedSalary))
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($statusSalary)}},{{json_encode($joinedSalary)}},"{{$showTitle}}")'>Print</a>
        @endif
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">

        	<div class="col-xs-4  col-sm-4 pricing-box">
				<div class="widget-box widget-color-green2">
					<div class="widget-header">
						<h5 class="widget-title bigger">
							Status
						</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center" style="min-height:220px;">
							<table class="table table-bordered  table-hover" style="margin:0!important;cursor:pointer;">
								<thead>
									<tr>
										<th>Status</th>
										<th>Employee</th>
										<th>Salary</th>
									</tr>
								</thead>
		           	        	<tbody>
		           	        	@php  
		           	        		$saved = 0;
		           	        	@endphp

				        		@foreach($statusSalary as $status)
				        			<tr style="color:#ff0000;">
										<th>{{ucfirst(emp_status_name($status->as_status))}}</th>
										<th>{{$status->employee}}</th>
										<th>{{$status->total_payable}}</th>
									</tr>
									@php $saved += $status->total_payable; @endphp       
								@endforeach
								@if(!empty($joinedSalary))
									<tr style="color:#076305;">
										<th>New Employee</th>
										<th>{{$joinedSalary->employee}}</th>
										<th>{{$joinedSalary->total_payable}}</th>
									</tr> 
									@php $joined = $joinedSalary->total_payable ;@endphp
							    @else
							    	@php $joined = 0 ;@endphp
								@endif
								@if(count($statusSalary)< 1 && empty($joinedSalary))
									<tr><td colspan="3"> No Data Found</td> </tr>
								@endif

								
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-4  col-sm-4 pricing-box">
				<div class="widget-box widget-color-green2">
					<div class="widget-header">
						<h5 class="widget-title bigger">
							Salary Ratio
						</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center" style="min-height:220px;">
							@php 
	           	        		$rest = $saved-$joined;
	           	        		if($rest>=0){
	           	        			$graph_status = 1;
	           	        			$stat = 'Saved Amount';
	           	        			$net = $joined;
	           	        		}else{
	           	        			$graph_status = 0;
	           	        			$stat = 'Extra Amount';
	           	        			$net = $saved;
	           	        		}
	           	        		$ratio = abs($rest); 
	           	        		$total = $ratio+$net;
	           	        		if($total==0){$total=1;}
	           	        		$net_per = round(($net/$total)*100,2);
	           	        		$net_star = round(($ratio/$total)*100,2);
	           	        	@endphp
	           	        	<div class="profile-info-row">
                                <div class="profile-info-name"> Salary Payable </div>

                                <div class="profile-info-value">
                                    <span> {{$net}}</span>
                                </div>
                            </div>
                            <div class="profile-info-row">
                                <div class="profile-info-name"> {{$stat}}  </div>

                                <div class="profile-info-value">
                                    <span>{{$ratio}}</span>
                                </div>
                            </div>
                            <br>
                            <br>
	           	        	Payable : {{$stat}}
	           	        	<hr>
	           	        	<span class="infobox-data-number" style="font-size: 35px;">{{$net_per}} : {{$net_star}}  </span>

						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-4  col-sm-4 pricing-box">
				<div class="widget-box widget-color-green2">
					<div class="widget-header">
						<h5 class="widget-title bigger">
							Salary Prediction
						</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center" style="min-height:220px;">
							<canvas id="doughnut-chart"  height="180"></canvas>
						</div>
					</div>
				</div>
			</div>
	</div>
    </div>
</div>
@php
	
@endphp
<div id="printOutputSection" style="display: none;"></div>

<script>
    function printDiv(result,joined,pagetitle) {
		$.ajax({
			url: '{{url('hr/search/hr_salratio_searchPrint')}}',
			type: 'get',
			data: {
				data: result,
				title: pagetitle,
				joined : joined
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
	var status = '{{$graph_status}}';
	if(status == 1){
		var stat = 'Saved';
		var color = '#076305';
		var net = '{{$joined}}';
	}else{
		var stat = 'Extra Pay';
		var color = '#FF0000';
		var net = '{{$saved}}';
	}
	new Chart(document.getElementById("doughnut-chart"), {
	    type: 'doughnut',
	    data: {
	      labels: [stat,"Payable"],
	      datasets: [
	        {
	          label: "Salary Ratio",
	          backgroundColor: [color, '#75ff9a'],
	          data: [{{$ratio}},net]
	        }
	      ]
	    },
	    options: {
            layout: {
                padding: {
                    left: 0,
                    right: 0,
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
</script>