<style type="text/css">

    @media only screen and (max-width: 1280px){
        .pricing-box{width: 33.33%; padding-right: 7px;}
        .fob{float: right;}
    
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
	            
				<div class=" col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2 search_unit" style="height:122px;">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Unit</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->unit}}</span>
							</div>
						</div>
					</div>
					<div class="space-10"></div>

					<div class="widget-box widget-color-green2 search_buyer" style="height:125px;">
						<div class="widget-header">
								<h5 class="widget-title bigger lighter">Buyer</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->buyer}}</span>
							</div>
						</div>
					</div>
				</div>

				

				
				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2 ">
						<div class="widget-header search_order">
								<h5 class="widget-title bigger lighter">Order</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main">
							@php $sum=0; @endphp
							@foreach($globalinfo->prdtype as $key => $prdtype)
							@php $sum+=$prdtype->order; @endphp
								<a href="#" class="search_order" data-product="{{ $key }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name" > {{ $prdtype->name }} </div>

                                        <div class="profile-info-value" >
                                            <span>{{ $prdtype->order }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
							</div>
						</div>
						<div class="sum-incomplete search_order" data-status="Incomplete">
							<div class="profile-info-row">
                                <div class="profile-info-name" > On Process </div>

                                <div class="profile-info-value" >
                                    <span>{{ ($sum)-($globalinfo->del_order)}}</span>
                                </div>
                            </div>
						</div>
						<div class="sum-delivery search_order" data-status="Completed">
							<div class="profile-info-row">
                                <div class="profile-info-name" > Delivered </div>

                                <div class="profile-info-value" >
                                    <span>{{ $globalinfo->del_order}}</span>
                                </div>
                            </div>
						</div>
						<div class="sum-row search_order">
							<div class="profile-info-row">
                                <div class="profile-info-name" > Total </div>

                                <div class="profile-info-value" >
                                    <span>{{ $sum}}</span>
                                </div>
                            </div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-3 pricing-box">
					<div class="widget-box widget-color-green2 ">
						<div class="widget-header search_order">
								<h5 class="widget-title bigger lighter">
									Qty
								</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main">
							@php $sum=0; @endphp
							@foreach($globalinfo->prdtype as $key => $prdtype)
							@php $sum+=$prdtype->qty; @endphp
								<a href="#" class="search_order" data-product="{{ $key }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name" > {{ $prdtype->name }} </div>

                                        <div class="profile-info-value" >
                                            <span>{{ $prdtype->qty }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
							</div>
						</div>
						<div class="sum-incomplete search_order" data-status="Incomplete">
							<div class="profile-info-row">
                                <div class="profile-info-name" > On Process </div>

                                <div class="profile-info-value" >
                                    <span>{{ ($sum)-($globalinfo->del_qty)}}</span>
                                </div>
                            </div>
						</div>
						<div class="sum-delivery search_order" data-status="Completed">
							<div class="profile-info-row">
                                <div class="profile-info-name" > Delivered </div>

                                <div class="profile-info-value" >
                                    <span>{{ $globalinfo->del_qty}}</span>
                                </div>
                            </div>
						</div>
						<div class="sum-row search_order">
							<div class="profile-info-row">
                                <div class="profile-info-name" > Total </div>

                                <div class="profile-info-value" >
                                    <span>{{ $sum}}</span>
                                </div>
                            </div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-3 pricing-box fob">
					<div class="widget-box widget-color-green2 ">
						<div class="widget-header ">
								<h5 class="widget-title bigger lighter">
									FOB
								</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main">
							@php $sum=0; @endphp
							@foreach($globalinfo->prdtype as $key => $prdtype)
							@php $sum+=$prdtype->fob; @endphp
                                    <div class="profile-info-row search_order" data-product="{{ $key }}">
                                        <div class="profile-info-name" > {{ $prdtype->name }} </div>

                                        <div class="profile-info-value" >
                                            <span>{{ $prdtype->fob }}</span>
                                        </div>
                                    </div>
                            @endforeach
							</div>
						</div>
						<div class="sum-incomplete search_order" data-status="Incomplete">
							<div class="profile-info-row">
                                <div class="profile-info-name" > On Process </div>

                                <div class="profile-info-value" >
                                    <span>{{ ($sum)-($globalinfo->del_fob)}}</span>
                                </div>
                            </div>
						</div>
						<div class="sum-delivery search_order" data-status="Completed">
							<div class="profile-info-row">
                                <div class="profile-info-name" > Delivered </div>

                                <div class="profile-info-value" >
                                    <span>{{ $globalinfo->del_fob}}</span>
                                </div>
                            </div>
						</div>
						<div class="sum-row search_order">
							<div class="profile-info-row">
                                <div class="profile-info-name" > Total </div>

                                <div class="profile-info-value" >
                                    <span>{{ $sum}}</span>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
