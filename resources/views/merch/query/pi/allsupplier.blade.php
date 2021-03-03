
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            @if(isset($request1['unit']))
                <li>
                    <a href="#" class="search_unit"> All Unit </a>
                </li>
                <li>
                @php
                 if(isset($request1['buyer'])){
                    $pp = 'search_buyer';
                 }else
                    $pp = 'search_supplier';
                @endphp
                    <a href="#" class="{{$pp}}" data-unit="{{ $request1['unit'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request1['buyer']))
                @if(!isset($request1['unit']))
                <li>
                    <a href="#" class="search_buyer"> All Buyer </a>
                </li>
                @endif
                <li>
                     <a href="#" class="search_supplier" data-buyer="{{ $request1['buyer'] }}">
                        {{ $data['buyerinfo']->b_name }}
                    </a>
                </li>
            @endif
            
            <li>
                Supplier
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
                @if(count($supplier_data)>0)
                    @foreach($supplier_data as $k=> $supplier)
                        
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_pi" data-supplier="{{ $supplier->id }}">
                                        <h5 class="widget-title smaller">  {{ $supplier->name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                       
                                        <a href="#" class="search_pi" data-supplier="{{ $supplier->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> PI </div>

                                                <div class="profile-info-value">
                                                    <span>{{$supplier->pi}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_pi" data-supplier="{{ $supplier->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> PI Qty </div>

                                                <div class="profile-info-value">
                                                    <span>{{$supplier->qty}}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_pi" data-supplier="{{ $supplier->id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> PI Value </div>

                                                <div class="profile-info-value">
                                                    <span>{{$supplier->value}}</span>
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