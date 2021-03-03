
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
                        
                        <div class="search-result-div col-xs-12 col-sm-4 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_order" data-buyer="{{ $buyer->id }}">
                                        <h5 class="widget-title smaller">  {{ $buyer->name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        <table class="table table-hover table-bordered no-margin" style="text-align:right;">
                                            
                                            <tr >
                                                <th style="text-align:center;">Status</th>
                                                <th  style="text-align:right;">Order</th>
                                                <th style="text-align:right;">Qty</th>
                                                <th colspan="2" style="text-align:right;">FOB</th>
                                            </tr>
                                            <tr class="search_order" data-buyer="{{ $buyer->id }}"  data-status="Incomplete" style="color:#d89807;;">
                                                <td style="text-align:center;">On Process</td>
                                                <td>{{($buyer->order)-($buyer->del_order)}}</td>
                                                <td>{{($buyer->qty)-($buyer->del_qty)}}</td>
                                                <td colspan="2">{{($buyer->tfob)-($buyer->del_fob)}}</td>
                                            </tr>
                                            <tr class="search_order" data-buyer="{{ $buyer->id }}"  data-status="Completed" style="color:#2e8965;">
                                                <td style="text-align:center;">Completed</td>
                                                <td>{{ $buyer->del_order}}</td>
                                                <td>{{ $buyer->del_qty}}</td>
                                                <td colspan="2"> {{ $buyer->del_fob}}</td>
                                            </tr>
                                            <tr class="search_order" data-buyer="{{ $buyer->id }}" >
                                                <td style="text-align:center;">Total </td>
                                                <td>{{ $buyer->order}} </td>
                                                <td>{{ $buyer->qty}}  </td>
                                                <td colspan="2">{{ $buyer->tfob}} </td>
                                            </tr>
                                        </table>
                                       
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