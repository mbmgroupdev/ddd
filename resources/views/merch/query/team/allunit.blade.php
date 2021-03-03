
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
                    
                    <div class="search-result-div col-xs-12 col-sm-4 widget-container-col ui-sortable">
                        <div class="widget-box widget-color-green2  ui-sortable-handle" id="widget-box-6">
                            <div class="widget-header">
                                <a href="#" class="white search_team" data-unit="{{ $unit->id }}">
                                    <h5 class="widget-title smaller" title="{{ strlen($unit->name)>28?$unit->name:'' }}"> {{ strlen($unit->name)>28?substr($unit->name,0,28).'...':$unit->name  }} 
                                    </h5>
                                  

                                    </a>
                            </div>

                            <div class="widget-body">
                                <div class="widget-main padding-6">
                                    <div class="profile-info-row search_team" data-unit="{{ $unit->id }}" style="cursor:pointer;">
                                        <div class="profile-info-name" >Total Team </div>

                                        <div class="profile-info-value" >
                                            <span>{{ $unit->team}}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name" > Order Info : </div>

                                        <div class="profile-info-value" >
                                        </div>
                                    </div>
                                   
                                    <table class="table table-hover table-bordered no-margin" style="text-align:right;">
                                        <tr >
                                            <th style="text-align:center;">Status</th>
                                            <th  style="text-align:right;">Order</th>
                                            <th style="text-align:right;">Qty</th>
                                            <th colspan="2" style="text-align:right;">FOB</th>
                                        </tr>
                                        <tr class="search_order" data-unit="{{ $unit->id }}"  data-status="Incomplete" style="color:#d89807;">
                                            <td style="text-align:center;">On Process</td>
                                            <td>{{($unit->order)-($unit->del_order)}}</td>
                                            <td>{{($unit->qty)-($unit->del_qty)}}</td>
                                            <td colspan="2">{{($unit->tfob)-($unit->del_fob)}}</td>
                                        </tr>
                                        <tr class="search_order" data-unit="{{ $unit->id }}"  data-status="Completed" style="color:#2e8965;">
                                            <td style="text-align:center;">Completed</td>
                                            <td>{{ $unit->del_order}}</td>
                                            <td>{{ $unit->del_qty}}</td>
                                            <td colspan="2">{{ $unit->del_fob}}</td>
                                        </tr>
                                        
                                        <tr class="search_order" data-unit="{{ $unit->id }}" >
                                            <td style="text-align:center;"> Total </td>
                                            <td> {{ $unit->order}} </td>
                                            <td> {{ $unit->qty}}  </td>
                                            <td colspan="2"> {{ $unit->tfob}} </td>
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