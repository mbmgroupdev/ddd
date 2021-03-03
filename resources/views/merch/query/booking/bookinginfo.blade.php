<style type="text/css">
    @media only screen and (max-width: 1280px) {
        .pricing-box {
            width: 33.33%;
            padding-right: 7px;
        }
        .fob {
            float: right;
        }
    }
    @media only screen and (max-width: 767px) {
        .pricing-box {
            width: 50%;
        }
    }
    @media only screen and (max-width: 579px) {
        .pricing-box {
            width: 100%;
        }
    }
</style>
<div class="panel panel-info col-sm-12 col-xs-12">
    <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i> MBM Group
            </li>
        </ul>
    </div>
    <hr>
    <p class="search-title">Search results of {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
                <div class="search_unit col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 " style="height:122px;">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">Unit</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main center">
                                <span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->unit}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 search_booking" style="height:122px;">
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
                <div class=" col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 search_booking" style="height:122px;">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">Supplier</h5>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main center">
                                <span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->supplier}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 search_booking" style="height:122px;">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">Booking</h5>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main center">
                                <span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->booking}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class=" col-xs-6 col-sm-3 pricing-box">
				</div> -->
                <div class="col-sm-offset-2 col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 search_booking" style="height:122px;">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">Booking Required Qty</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main center">
                                <span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->rqty}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 search_booking" style="height:122px;">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">Booking Qty</h5>
                        </div>
                        <div class="widget-body">
                            <div class="widget-main center">
                                <span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->bqty}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" col-xs-6 col-sm-3 pricing-box">
                    <div class="widget-box widget-color-green2 search_booking" style="height:122px;">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">Booking Value</h5>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main center">
                                <span class="infobox-data-number" style="margin: 10px 0 4px;"> {{$globalinfo->value}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>