<style type="text/css">

    @media only screen and (max-width: 1280px){
        .pricing-box{width: 33.33%; padding-right: 7px;}
    
    }
    @media only screen and (max-width: 767px){
        .pricing-box{width: 50%;}
    }
    @media only screen and (max-width: 579px) {
        .pricing-box{width: 100%;}
        
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
    </div>

    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        <div class="row">
	            

				

				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2 search_buyer" style="min-height: 160px;">
						<div class="widget-header">
								<h5 class="widget-title bigger lighter">Total Buyer</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number" style="margin: 25px;font-size: 44px;"> {{$globalinfo->buyer}}</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2">
						<div class="widget-header search_style">
								<h5 class="widget-title bigger lighter">Total Style</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
							@php $sum=0; @endphp
							@foreach($globalinfo->total as $key => $total)
							@php $sum+=$total->value; @endphp
								<a href="#" class="search_style" data-product="{{ $key }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{ $total->name }} </div>

                                        <div class="profile-info-value">
                                            <span>{{ $total->value }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            	<a href="#" class="search_style" >
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
				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2">
						<div class="widget-header search_style" data-ptype="Bulk">
								<h5 class="widget-title bigger lighter">
									Bulk
								</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
							@php $sum=0; @endphp
							@foreach($globalinfo->bulk as $key => $bulk)
							@php $sum+=$bulk->value; @endphp
								<a href="#" class="search_style" data-ptype="Bulk" data-product="{{ $key }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{ $bulk->name }} </div>

                                        <div class="profile-info-value">
                                            <span>{{ $bulk->value }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            	<a href="#" class="search_style" data-ptype="Bulk">
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
				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2">
						<div class="widget-header search_style" data-ptype="Development">
								<h5 class="widget-title bigger lighter">
									Development
								</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
							@php $sum=0; @endphp
							@foreach($globalinfo->development as $key => $development)
							@php $sum+=$development->value; @endphp
								<a href="#" class="search_style" data-ptype="Development" data-product="{{ $key }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> {{ $development->name }} </div>

                                        <div class="profile-info-value">
                                            <span>{{ $development->value }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            	<a href="#" class="search_style" data-ptype="Development">
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
