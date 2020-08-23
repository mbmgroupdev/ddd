<style type="text/css">
	.infobox-data-number{
	    display: block;
	    font-size: 22px;
	    margin: 2px 0 4px;
	    position: relative;
	    text-shadow: 1px 1px 0 rgba(0,0,0,.15);
	}
	@media print {
        #printOutputSection {display: block;}
    }
</style>
<div class="panel panel-info col-sm-12 col-xs-12" id="printArea">
	<br>
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                 MBM Group
            </li>
        </ul>
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($attResultCount)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>

    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        <div class="row">
				<div class="col-sm-offset-4 col-xs-offset-4 col-xs-4 col-sm-4 pricing-box">
					<div class="widget-box widget-color-dark search_unit">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total Unit</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number">{{ count($unit_list) }}</span>
							</div>
							<div class="widget-main center">
                                <span class="infobox-data-number"> <h6>Total Employee: {{ $employee_count }}</h6>
                                </span>
                            </div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
@php
   
@endphp
<script>
	function printDiv(result,pagetitle) {
		$.ajax({
			url: '{{url('hr/search/hr_att_searchPrint')}}',
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