
<div class="panel panel-info col-sm-12">
    <div class="panel-body">
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
            <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($unit_data)}},"{{$showTitle}}")'>Print</a>
        </div>

        <hr>
        <p class="search-title">Search results of  {{ $showTitle }}</p>
        <!-- <h4 class="center">MBM Group</h4> -->
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            
            @foreach($unit_data as $k=>$unit)
                
                <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                    <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                        <div class="widget-header">
                            <a href="#" class="white search_area" data-unit="{{ $unit['id'] }}">
                                <h5 class="widget-title smaller" title="{{ strlen($unit['name'])>28?$unit['name']:'' }}">  {{ strlen($unit['name'])>28?substr($unit['name'],0,28).'...':$unit['name']  }}</h5> </a>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-6">
                            @if($request['type'] != 'month')
                                <a href="#" class="search_area" data-unit="{{ $unit['id'] }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Area </div>

                                        <div class="profile-info-value">
                                            <span>{{ $area_count }}</span>
                                        </div>
                                    </div>
                                </a>
                             @endif
                             @php 
                                if($request['type'] != 'month'){
                                    $button = 'search_ot_hour';
                                }else{
                                    $button = 'search_emp'; 
                                }
                                
                             @endphp
                                    <div class=" profile-info-row  {{$button}}" data-unit="{{ $unit['id']  }}">
                                        <div class="profile-info-name"> Total Employee </div>

                                        <div class="profile-info-value">
                                            <span> {{ $unit['employee']}}</span>
                                        </div>
                                    </div>
                                <div class="  profile-info-row search_emp" data-unit="{{ $unit['id']  }}">
                                    <div class="profile-info-name"> Total OT</div>

                                    <div class="profile-info-value">
                                        <span>{{ Custom::numberToTime($unit['ot_hour']) }} 
                                    Hour</span>
                                    </div>
                                </div>
                            @if($request['type'] != 'month')
                                <div class="search_ot_shift" data-unit="{{ $unit['id'] }}">View Shift</div>
                            @endif
                               
                            </div>
                        </div>
                    </div>
                </div>
                
            @endforeach
                
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
<script>
function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_ot_search_print_page')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: result,
                type: 'Unit',
                title: pagetitle
            },
            success: function(data) {
                $('#printOutputSection').html(data);
                var divToPrint=document.getElementById('printOutputSection');
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
            }
        });
    }
</script>