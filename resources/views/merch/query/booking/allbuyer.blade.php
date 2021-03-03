
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            
            <li>
                All Buyer
            </li>
        </ul><!-- /.breadcrumb -->

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
         
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
            	<div class="col-sm-12">
                @if(count($buyer_data)>0)
                    @foreach($buyer_data as $k=>$buyer)
                        
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_supplier" data-buyer="{{ $buyer->id }}">
                                        <h5 class="widget-title smaller">  {{ $buyer->name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                       
                                        <a href="#" class="search_supplier" data-buyer="{{ $buyer->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Supplier </div>

                                                <div class="profile-info-value">
                                                    <span>{{$buyer->supplier}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_pi" data-buyer="{{ $buyer->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> PI </div>

                                                <div class="profile-info-value">
                                                    <span>{{$buyer->pi}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_pi" data-buyer="{{ $buyer->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> PI Qty </div>

                                                <div class="profile-info-value">
                                                    <span>{{$buyer->qty}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_pi" data-buyer="{{ $buyer->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> PI Value </div>

                                                <div class="profile-info-value">
                                                    <span>{{$buyer->value}}</span>
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