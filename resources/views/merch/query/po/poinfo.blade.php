
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                 MBM Group 
            </li>
        </ul>
    </div>

    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        <div class="row">
	            
				<div class=" col-xs-6 col-sm-3 pricing-box">
					<div class="search_country widget-box widget-color-green2">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total Country</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number"> {{$globalinfo->country}}</span>
							</div>
						</div>
					</div>
				</div>

				

				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2 search_po">
						<div class="widget-header">
								<h5 class="widget-title bigger lighter">Total PO</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number"> {{$globalinfo->po}}</span>
							</div>
						</div>
					</div>
				</div>
				<div class="search_po col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total Qty</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number"> {{$globalinfo->qty}}</span>
							</div>
						</div>
					</div>
				</div>

				

				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2 search_po">
						<div class="widget-header">
								<h5 class="widget-title bigger lighter">Country FOB</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number"> {{$globalinfo->country_fob}}</span>
							</div>
						</div>
					</div>
				</div>
				
			</div>
        </div>
    </div>
</div>
