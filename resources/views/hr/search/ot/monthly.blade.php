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
                MBM Group 
            </li>
        </ul>
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv("{{$employee}}",{{$otTotal}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">       	    		
			<div class="search_unit col-sm-offset-1 col-sm-3 pricing-box" >
				<div class="widget-box widget-color-green2">
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

			<div class="search_emp col-sm-3 pricing-box" >
				<div class="widget-box widget-color-green2 ">
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

			<div class=" search_emp col-sm-4 pricing-box" >
				<div class="widget-box widget-color-green2 ">
					<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total OT</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center">
							<span class="infobox-data-number">
							{{ num_to_time($otTotal) }} 
							Hour
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
<script>
function printDiv(emp,ot,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_ot_search_print_page')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                emp: emp,
                ot: ot,
                type: 'Month',
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
