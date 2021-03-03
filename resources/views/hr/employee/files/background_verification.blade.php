<div class="row justify-content-center">
	<div class="col-sm-12 mt-2">
                            
        <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('print-area')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>

    </div>
	<div id="print-area" class="col-sm-9">
		<style type="text/css">
				.mb-2 span {
				    width: 160px;
				    font-size: 12px !important;
				    display: inline-block;
				}
			
		</style>
		<style type="text/css" media="print">
			.bn-form-output{padding:54pt 36pt }
		</style>
		@foreach($employees as $key => $emp)
		<div class="bn-form-output" >
		</div>
		<div class="page-break"></div>
		@endforeach
	</div>
</div>   