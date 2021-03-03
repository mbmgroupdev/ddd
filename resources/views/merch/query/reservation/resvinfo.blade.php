
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
		        <div class="col-sm-offset-1 col-sm-10">
		        	
					<div class="col-xs-6 col-sm-4 pricing-box">
						<div class="widget-box widget-color-green2 search_unit" >
							<div class="widget-header">
									<h5 class="widget-title bigger lighter">Total Unit</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<span class="infobox-data-number" > {{$globalinfo->unit}}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-4 pricing-box">
						<div class="widget-box widget-color-green2 search_buyer" >
							<div class="widget-header">
									<h5 class="widget-title bigger lighter">Total Buyer</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<span class="infobox-data-number" > {{$globalinfo->buyer}}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-4 pricing-box">
						<div class="widget-box widget-color-green2 search_resv" >
							<div class="widget-header">
									<h5 class="widget-title bigger lighter">Total Reservation</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<span class="infobox-data-number" > {{$globalinfo->resv}}</span>
								</div>
							</div>
						</div>
					</div>
		        </div>
				

			</div>
			<div class="space-10"></div>
			<div class="row">
				<div class="col-sm-offset-1 col-sm-10">
					<div class="col-xs-6 col-sm-4  pricing-box">
						<div class="widget-box widget-color-green2">
							<div class="widget-header search_resv" data-col="reserved">
									<h5 class="widget-title bigger lighter">Projection</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
								@php $sum=0; @endphp
								@foreach($globalinfo->data as $key => $reserved)
								@php $sum+=$reserved->reserved; @endphp
									<a href="#" class="search_resv" data-col="reserved" data-product="{{ $key }}">
	                                    <div class="profile-info-row">
	                                        <div class="profile-info-name"> {{ $reserved->name }} </div>

	                                        <div class="profile-info-value">
	                                            <span>{{ $reserved->reserved }}</span>
	                                        </div>
	                                    </div>
	                                </a>
	                            @endforeach
	                            	<a href="#" class="search_resv" data-col="reserved">
	                                    <div class="profile-info-row row-sum">
	                                        <div class="profile-info-name"> Total </div>

	                                        <div class="profile-info-value">
	                                            <span>{{ $sum }}</span>
	                                        </div>
	                                    </div>
	                                </a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-4 pricing-box">
						<div class="widget-box widget-color-green2">
							<div class="widget-header search_resv" data-col="confirmed">
									<h5 class="widget-title bigger lighter">
										Confirmed
									</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
								@php $sum=0; @endphp
								@foreach($globalinfo->data as $key => $confirmed)
								@php $sum+=$confirmed->confirmed; @endphp
									<a href="#" class="search_resv" data-col="confirmed"  data-product="{{ $key }}">
	                                    <div class="profile-info-row">
	                                        <div class="profile-info-name"> {{ $confirmed->name }} </div>

	                                        <div class="profile-info-value">
	                                            <span>{{ $confirmed->confirmed }}</span>
	                                        </div>
	                                    </div>
	                                </a>
	                            @endforeach
	                            	<a href="#" class="search_resv" data-col="confirmed">
	                                    <div class="profile-info-row row-sum">
	                                        <div class="profile-info-name "> Total </div>

	                                        <div class="profile-info-value">
	                                            <span>{{ $sum }}</span>
	                                        </div>
	                                    </div>
	                                </a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-4 pricing-box">
						<div class="widget-box widget-color-green2">
							<div class="widget-header search_resv" data-col="balance">
									<h5 class="widget-title bigger lighter">
										Balance
									</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
								@php $sum=0; @endphp
								@foreach($globalinfo->data as $key => $balance)
								@php $sum+=$balance->balance; @endphp
									<a href="#" class="search_resv" data-col="balance"  data-product="{{ $key }}">
	                                    <div class="profile-info-row">
	                                        <div class="profile-info-name"> {{ $balance->name }} </div>

	                                        <div class="profile-info-value">
	                                            <span>{{ $balance->balance }}</span>
	                                        </div>
	                                    </div>
	                                </a>
	                            @endforeach
	                            	<a href="#" class="search_resv" data-col="balance">
	                                    <div class="profile-info-row row-sum">
	                                        <div class="profile-info-name"> Total </div>

	                                        <div class="profile-info-value">
	                                            <span>{{ $sum }}</span>
	                                        </div>
	                                    </div>
	                                </a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
