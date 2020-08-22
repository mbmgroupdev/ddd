
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

                 <li>
                     <a href="#" class="search_floor" data-department="{{ $request1['department'] }}"> {{ $department->hr_department_name }} </a>
                </li>
                <li class="active"> {{ $floor->hr_floor_name }} </li>
                <li>
                     Section
                </li>
            </ul><!-- /.breadcrumb -->
            <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($section_data)}},"{{$showTitle}}")'>Print</a>
 
        </div>
        <hr>
        <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">

        <!-- <h4 class="center">MBM Group</h4> -->
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
            	<div class="col-sm-12">
                    @foreach($section_data as $k=>$section)
                        
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_subsection" data-section="{{ $section['id'] }}">
                                        <h5 class="widget-title smaller">  {{ $section['name'] }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        <a href="#" class="search_subsection" data-section="{{ $section['id'] }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Subsection </div>

                                                <div class="profile-info-value">
                                                    <span>{{ $section['subsection'] }}</span>
                                                </div>
                                            </div>
                                        </a>
                                        
                                            <div class="profile-info-row  search_ot_hour" data-section="{{ $section['id'] }}">
                                                <div class="profile-info-name"> Total Employee </div>

                                                <div class="profile-info-value">
                                                    <span>{{ $section['employee']}}</span>
                                                </div>
                                            </div>
                                        <div class=" profile-info-row search_emp" data-section="{{ $section['id'] }}">
                                            <div class="profile-info-name"> Total OT</div>

                                            <div class="profile-info-value">
                                                <span>{{ Custom::numberToTime($section['ot_hour']) }} 
                                            Hour</span>
                                            </div>
                                        </div>
                                        <div class="search_ot_shift" data-section="{{ $section['id'] }}">View Shift</div>
                                       
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
<div id="printOutputSection" style="display: none;"></div>
<script>
function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_ot_search_print_page')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: result,
                type: 'Section',
                title: pagetitle,
                unit : {{ $request1['unit'] }},
                area : {{ $request1['area'] }},
                department : {{ $request1['department'] }},
                floor : {{ $request1['floor'] }}
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