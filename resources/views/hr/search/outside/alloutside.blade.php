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
        @php
        $resultLocationList = [];
        if(!empty($locationList)){
        	foreach($locationList as $location=>$llist) {
				$list = array_column($llist, 'as_id');
				$resultLocationList[Custom::getUnitName($location)] = count(array_unique($list));
        	}
    	}
    	@endphp
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($resultLocationList)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        <div class="row">
        		@php
        			$totalCount = 0;
        		@endphp
	        	@if(!empty($locationList))
		        	@foreach($locationList as $location=>$llist)
						<div class="col-xs-4 col-sm-3 pricing-box outside_emp" data-id="{{$location}}">
							<div class="widget-box widget-color-green2">
								<div class="widget-header">
									<h5 class="widget-title bigger lighter">{{Custom::getUnitName($location)}}</h5>
								</div>

								<div class="widget-body">
									<div class="widget-main center">
										<span class="infobox-data-number">
										@php
											$list = array_column($llist, 'as_id');
											$count = count(array_unique($list));
											$totalCount += $count;
											echo $count;
										@endphp
										</span>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				@else
					<div class="col-xs-4 col-sm-3 pricing-box">
						<div class="widget-box widget-color-green2">
							<div class="widget-header">
								<h5 class="widget-title bigger lighter">No Data Found</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<span class="infobox-data-number">0</span>
								</div>
							</div>
						</div>
					</div>
				@endif
				@if($request['type'] == 'date')
					<div class="col-xs-4 col-sm-3 pricing-box outside_emp_date_total" data-request="{{htmlspecialchars(json_encode($request), ENT_QUOTES, 'UTF-8')}}">
						<div class="widget-box widget-color-dark">
							<div class="widget-header">
								<h5 class="widget-title bigger lighter">Total</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<span class="infobox-data-number">{{$totalCount}}</span>
								</div>
							</div>
						</div>
					</div>
				@endif
			</div>
		</div>
    </div>
</div>
@php
	//dump($request);
@endphp
<div id="printOutputSection" style="display: none;"></div>

<script>
    function printDiv(result,pagetitle) {
		$.ajax({
			url: '{{url('hr/search/hr_outside_searchPrint')}}',
			type: 'get',
			data: {
				data: result,
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