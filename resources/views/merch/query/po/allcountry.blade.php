<style type="text/css">
    .table.table-bordered>thead>tr>th {
        vertical-align: middle;
        background: #2E8965;
        color: #fff;
        font-weight: 400;
    }
</style>
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            <li>
                 Country
            </li>
        </ul><!-- /.breadcrumb -->

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
         
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
            	<div class="col-sm-12 col-xs-12">
                    <table class="table table-hover table-bordered no-margin" style="text-align:right;">
                        <thead>
                            <tr >
                                <th style="text-align:center;">Country</th>
                                <th  style="text-align:right;">Total PO</th>
                                <th style="text-align:right;">Total Order</th>
                                <th style="text-align:right;">Total Qty</th>
                                <th style="text-align:right;">Total FOB</th>
                            </tr>
                        </thead>    
                        <tbody>
                        @if(count($country_data)>0)

                            @foreach($country_data as $key => $country)
                                <tr class="search_po" data-country="{{ $country->id }}">
                                    <td style="text-align:center;">{{$country->name}}</td>
                                    <td >{{$country->po}}</td>
                                    <td >{{$country->order}}</td>
                                    <td >{{$country->qty}}</td>
                                    <td >{{$country->country_fob}}</td>
                                </tr>
                                
                            @endforeach
                        @else
                            <tr> <td>No information found!</td></tr>
                        @endif
                        </tbody>
                    </table>   
                </div>
            </div>
        </div>
    </div>
</div>