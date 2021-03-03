<style type="text/css">
    @media only screen and (max-width: 767px){
        .table{width: 100%; display: block; overflow-x: auto; white-space: nowrap;}
    }

    @media only screen and (max-width: 1280px){
        .search-result-div{width: 50%;}
        .table{width: 100%; display: block; overflow-x: auto; white-space: nowrap;}
    }
    @media only screen and (max-width: 550px){
        .search-result-div{width: 100%;}
        .table{width: 100%; display: block; overflow-x: auto; white-space: nowrap;}
    }

</style>
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all"> MBM Group </a>
            </li>
            <li>
                 All Unit
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
                @if(count($unit_data)>0)

                    @foreach($unit_data as $k=>$unit)
                        
                        <div class="search-result-div col-xs-12 col-sm-6 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_resv" data-unit="{{ $unit->id }}">
                                        <h5 class="widget-title smaller">  {{ $unit->name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        <table class="table table-bordered no-margin">
                                            <tr class="search_resv" data-unit="{{ $unit->id }}" style="cursor:pointer;" >
                                                <th style="text-align:center;">Total Reservation</th>
                                                <th colspan="5" style="text-align:center;">{{$unit->resv}}</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align:center;">Qty</th>
                                                <td colspan="5" style="padding: 0;">
                                                    
                                                    <table class="table table-hover table-bordered no-margin" style="text-align:right;">
                                                        <thead>
                                                            <tr >
                                                                <th style="text-align:center;">Product Type</th>
                                                                <th  style="text-align:right;">Projection</th>
                                                                <th style="text-align:right;">Confirmed</th>
                                                                <th style="text-align:right;">Balance</th>
                                                            </tr>
                                                        </thead>    
                                                        <tbody>
                                                        @php $sumR=0; $sumC=0; @endphp
                                                        @foreach($unit->data as $key => $values)
                                                        @php 
                                                            $sumR+=$values->reserved;
                                                            $sumC+=$values->confirmed; 
                                                        @endphp
                                                            <tr class="search_resv" data-unit="{{ $unit->id }}"  data-product="{{ $key }}">
                                                                <td style="text-align:center;">{{$values->name}}</td>
                                                                <td>{{$values->reserved}}</td>
                                                                <td>{{$values->confirmed}}</td>
                                                                <td>{{$values->balance}}</td>
                                                            </tr>
                                                            
                                                        @endforeach
                                                        </tbody>
                                                        <thead>
                                                            <tr class="search_resv" data-unit="{{ $unit->id }}" >
                                                                <td style="text-align:center;"> <b>Total </b></td>
                                                                <td> <b>{{$sumR}} </b></td>
                                                                <td> <b>{{$sumC}} </b></td>
                                                                <td> <b>{{($sumR)-($sumR)}} </b></td>
                                                            </tr>
                                                        </thead>
                                                    </table> 
                                                </td>

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