
<div class="panel panel-info col-sm-12 col-xs-12">
    <div class="panel-body">
       <br>
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    <a href="#" class="search_all" data-category="{{ $request['category'] }}" data-type="{{ $request['type'] }}"> MBM Group </a>
                </li>
                <li>
                    <a href="#" class="search_unit"> All Unit </a>
                </li>
            </ul>
        </div>

        <hr>
        <p class="search-title">Search results of  {{ $showTitle }}</p>
        <!-- <h4 class="center">MBM Group</h4> -->
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
                <div class="col-sm-12">
                @if(count($unit_data)>0)
                    @foreach($unit_data as $k=>$unit)
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white">
                                        <h5 class="widget-title smaller" title="{{ strlen($unit->name)>28?$unit->name:'' }}"> {{ strlen($unit->name)>28?substr($unit->name,0,28).'...':$unit->name  }} 
                                        </h5>
                                    </a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                       <a href="#" class="search_booking" data-unit="{{ $unit->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Buyer </div>

                                                <div class="profile-info-value">
                                                    <span>{{$unit->buyer}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_booking" data-unit="{{ $unit->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Supplier </div>

                                                <div class="profile-info-value">
                                                    <span>{{$unit->supplier}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_booking" data-unit="{{ $unit->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Booking </div>

                                                <div class="profile-info-value">
                                                    <span>{{$unit->Booking}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_booking" data-unit="{{ $unit->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Required Qty </div>

                                                <div class="profile-info-value">
                                                    <span>{{$unit->rqty}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_booking" data-unit="{{ $unit->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Booking Qty </div>

                                                <div class="profile-info-value">
                                                    <span>{{$unit->bqty}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_booking" data-unit="{{ $unit->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Value </div>

                                                <div class="profile-info-value">
                                                    <span>{{$unit->value}}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <center>No information found!</center>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>