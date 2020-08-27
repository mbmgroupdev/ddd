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
			<div class="col-xs-3 col-sm-2 pricing-box">
				<div class="widget-box widget-color-dark search_unit">
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

			

			<div class="col-xs-3 col-sm-2 pricing-box">
				<div class="widget-box widget-color-blue search_allemp" data-attstatus="present">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter">Total Present</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center after-load">
							<span class="infobox-data-number" id="tPresent">{{ $attResultCount['present'] }}</span>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-3 col-sm-2 pricing-box">
				<div class="widget-box widget-color-green2 search_allemp" data-attstatus="absent">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter">Total Absent</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center after-load">
							<span class="infobox-data-number" id="tAbsent">{{ $attResultCount['absent'] }}</span>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-3 col-sm-2 pricing-box search_allemp" data-attstatus="leave">
				<div class="widget-box widget-color-red">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter">Total Leave</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center after-load">
							<span class="infobox-data-number" id="tLeave">{{ $attResultCount['leave'] }}</span>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xs-3 col-sm-2 pricing-box search_allemp" data-attstatus="late">
				<div class="widget-box widget-color-grey">
					<div class="widget-header">
						<h5 class="widget-title bigger lighter">Total Late</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center after-load">
							<span class="infobox-data-number" id="tLate">{{ $attResultCount['late'] }}</span>
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