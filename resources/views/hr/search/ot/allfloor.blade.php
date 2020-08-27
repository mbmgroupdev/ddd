
<div class="panel panel-info col-sm-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
                </li>
                <li>
                    <a href="#" class="search_unit"> All Unit </a>
                </li>
                <li>
                    <a href="#" class="search_area" data-unit="{{ $request1['unit'] }}"> {{$unit->hr_unit_name}} </a>
                </li>
                <li>
                     <a href="#" class="search_dept" data-area="{{ $request1['area'] }}">{{ $area->hr_area_name }} </a>
                </li>
                <li class="active"> {{ $department->hr_department_name }} </li>
                <li>
                     Floor
                </li>
            </ul><!-- /.breadcrumb -->
            <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($floor_data)}},"{{$showTitle}}")'>Print</a>
 
        </div>
        <hr>
        <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            
            @foreach($floor_data as $k=>$floor)
                
                <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                    <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                        <div class="widget-header">
                            <a href="#" class="white search_section" data-floor="{{ $floor['id'] }}">
                                <h5 class="widget-title smaller">  {{ $floor['name'] }} </h5></a>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-6">
                                <a href="#" class="search_section" data-floor="{{ $floor['id'] }}">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Section </div>

                                        <div class="profile-info-value">
                                            <span>{{$section_count}}</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- <a href="#" class="search_emp" data-floor="{{ $floor['id'] }}"> -->
                                    <div class="profile-info-row  search_ot_hour" data-floor="{{ $floor['id'] }}">
                                        <div class="profile-info-name"> Total Employee </div>

                                        <div class="profile-info-value">
                                            <span>{{ $floor['employee']}}</span>
                                        </div>
                                    </div>
                              <!--   </a> -->
                                <div class=" profile-info-row search_emp" data-floor="{{ $floor['id'] }}">
                                    <div class="profile-info-name"> Total OT</div>

                                    <div class="profile-info-value">
                                        <span>{{ Custom::numberToTime($floor['ot_hour']) }} 
                                    Hour</span>
                                    </div>
                                </div>
                                <div class="search_ot_shift" data-floor="{{ $floor['id'] }}">View Shift</div>
                               
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
                type: 'Floor',
                title: pagetitle,
                unit : {{ $request1['unit'] }},
                area : {{ $request1['area'] }},
                department : {{ $request1['department'] }}
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