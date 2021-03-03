<style type="text/css">
.search-result-div:first-child .widget-header :after {
    content: '(Team Lead)';
    font-size: 11px;
    color: #ffb817;
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
            @if(isset($request1['unit']))
            <li>
                <a href="#" class="search_unit"> All Unit </a>
            </li>
            <li>
                <a href="#" class="search_team" data-unit="{{ $request1['unit'] }}">
                    {{ $data['unit']->hr_unit_name }}
                </a>
            </li>
            @endif
            @if(isset($request1['team']))
                @if(!isset($request1['unit']))
                <li>
                    <a href="#" class="search_team"> All Team </a>
                </li>
                @endif
                <li>
                    <a href="#" class="search_executive" data-team="{{ $request1['team'] }}">
                        {{ $data['teaminfo']->team_name }}
                    </a>
                </li>
            @endif
            
            <li>
                All Executive
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
                    @foreach($executive_data as $k=>$executive)
                        
                    <div class="search-result-div col-xs-12 col-sm-4 widget-container-col ui-sortable">
                        <div class="widget-box widget-color-green2  ui-sortable-handle" id="widget-box-6">
                            <div class="widget-header">
                                <a href="#" class="white search_order" data-executive="{{ $executive->id }}">
                                    <h5 class="widget-title smaller">  {{ $executive->name }} </h5></a>
                            </div>

                            <div class="widget-body">
                                <div class="widget-main padding-6">
                                    <div class="profile-info-row search_order" data-executive="{{ $executive->id }}">
                                        <div class="profile-info-name" > Executive ID </div>

                                        <div class="profile-info-value" >
                                            <span>{{ $executive->as_id}}</span>
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
                                        <tr class="search_order" data-executive="{{ $executive->id }}"  data-status="Incomplete" style="color:#d89807;">
                                            <td style="text-align:center;">On Process</td>
                                            <td>{{($executive->order)-($executive->del_order)}}</td>
                                            <td>{{($executive->qty)-($executive->del_qty)}}</td>
                                            <td colspan="2">{{($executive->tfob)-($executive->del_fob)}}</td>
                                        </tr>
                                        <tr class="search_order" data-executive="{{ $executive->id }}"  data-status="Completed" style="color:#2e8965;">
                                            <td style="text-align:center;">Completed</td>
                                            <td>{{ $executive->del_order}}</td>
                                            <td>{{ $executive->del_qty}}</td>
                                            <td colspan="2"> {{ $executive->del_fob}}</td>
                                        </tr>
                                        <tr class="search_order" data-executive="{{ $executive->id }}" >
                                            <td style="text-align:center;"> Total </td>
                                            <td> {{ $executive->order}} </td>
                                            <td> {{ $executive->qty}}  </td>
                                            <td colspan="2"> {{ $executive->tfob}} </td>
                                        </tr>
                                    </table>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>