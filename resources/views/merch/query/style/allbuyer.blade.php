
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all"> MBM Group </a>
            </li>
            <li>
                 Buyer
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
                            <div class="widget-box widget-color-green2  ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_style" data-buyer="{{ $buyer->id }}">
                                        <h5 class="widget-title smaller">  {{ $buyer->name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        @php $sum=0; @endphp
                                        @foreach($buyer->data as $key => $total)
                                        @php $sum+=$total->value; @endphp
                                            <a href="#" class="search_style" data-buyer="{{ $buyer->id }}" data-product="{{ $key }}">
                                                <div class="profile-info-row">
                                                    <div class="profile-info-name"> {{ $total->name }} </div>

                                                    <div class="profile-info-value">
                                                        <span>{{ $total->value }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                            <a href="#" class="search_style" data-buyer="{{ $buyer->id }}" >
                                                <div class="profile-info-row row-sum">
                                                    <div class="profile-info-name"> Total Style </div>

                                                    <div class="profile-info-value">
                                                        <span>{{ $sum }}</span>
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