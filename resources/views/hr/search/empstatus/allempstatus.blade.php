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
	<br>
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                 MBM Group
            </li>
        </ul>
        @php
        $resultEmpList = [];
        if(!empty($empList)){
        	foreach($empList as $type=>$list) {
				$resultEmpList[ucfirst(Custom::getEmpStatusName($type))] = count($list);
        	}
    	}
    	@endphp
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($resultEmpList)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        
        	@php
    			$totalCount = 0;
    		@endphp
        	@if(!empty($empList))
	        	@foreach($empList as $statusType=>$elist)
					<div class="col-xs-4 col-sm-3 pricing-box emp_status" data-id="{{$statusType}}">
						<div class="widget-box widget-color-green2">
							<div class="widget-header">
								<h5 class="widget-title bigger lighter">
									{{ucfirst(Custom::getEmpStatusName($statusType))}}
								</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<span class="infobox-data-number">
									@php
										$count = count($elist);
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

			@endif
			<div class="col-xs-4 col-sm-3 pricing-box emp_status" data-id="join">
				<div class="widget-box widget-color-green2">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter">Join</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center">
							<span class="infobox-data-number">{{count($empActiveList)}}</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-3 pricing-box">
				<div class="widget-box widget-color-dark search_unit">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter">Total</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center">
							<span class="infobox-data-number">{{$totalCount+count($empActiveList)}}</span>
						</div>
					</div>
				</div>
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
			url: '{{url('hr/search/hr_empstatus_searchPrint')}}',
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