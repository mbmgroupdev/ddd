<style type="text/css">
	.row-sum{border-top:1px solid #f1f1f1!important; }
    .sum-row .profile-info-name,.sum-row  .profile-info-value,.row-sum .profile-info-name,.row-sum  .profile-info-value{color: #1a1a1a;}
    .profile-info-value {
	    min-width: 170px!important;
	}
	.profile-info-row{
		display: block;
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
         <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($lineInfo)}},"{{$showTitle}}")'>Print</a>
    </div>

    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        <div class="row">
	            
				<div class="search_unit col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total Unit</h5>
						</div>

						<div class="widget-body" >
							<div class="widget-main center">
								<span class="infobox-data-number">{{$lineInfo->unit}}</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2 search_line">
						<div class="widget-header">
								<h5 class="widget-title bigger lighter">
									Line
								</h5>
						</div>

						<div class="widget-body" >
							<div class="widget-main center">
								<span class="infobox-data-number">{{$lineInfo->line}}</span>
							</div>
						</div>
					</div>
				</div>
                <div class="col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 search_emp">
                        <div class="widget-header">
                                <h5 class="widget-title bigger lighter">
                                    Total Employee
                                </h5>
                        </div>

                        <div class="widget-body" >
                            <div class="widget-main center">
                                <span class="infobox-data-number">{{$lineInfo->emp}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 line_change">
                        <div class="widget-header">
                                <h5 class="widget-title bigger lighter">
                                    Line Change
                                </h5>
                        </div>

                        <div class="widget-body" >
                            <div class="widget-main center">
                                <span class="infobox-data-number"> {{ $lineInfo->line_change }}</span>
                            </div>
                        </div>
                    </div>
                </div>

				
				
			</div>
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
<script type="text/javascript">
function printDiv(result, pagetitle) {
    $.ajax({
        url: '{{url('hr/search/hr_line_search_print_page')}}',
        type: 'post',
        data: {
        	"_token": "{{ csrf_token() }}",
            data: result,
            type: 'Total',
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
