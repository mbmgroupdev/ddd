
<style type="text/css">

    @media only screen and (max-width: 1280px) {
        .search-result-div{width: 33.33%;}
    }
    @media only screen and (max-width: 767px) {
        .search-result-div{width: 50%;}
    }
    @media only screen and (max-width: 579px) {
        .search-result-div{width: 100%;}
    }
    @media print {
        #printOutputSection {display: block;}
    }
</style>
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
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($floor_list)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            
            @php
                $count = 0;
            @endphp
            @foreach($floor_list as $k=>$floor)
                @if($count == 0)
                    <div class="row">
                @endif
                @php
                    $count++;
                @endphp
                <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                    <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                        <div class="widget-header">
                            <a href="#" class="white search_section" data-floor="{{ $floor->hr_floor_id }}">
                                <h5 class="widget-title smaller">  {{ $floor->hr_floor_name }} </h5></a>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-6">
                                <div class="profile-info-row">
                                    <div class="profile-info-name search_section" data-floor="{{ $floor->hr_floor_id }}"> Total Section </div>

                                    <div class="profile-info-value">
                                        <span>{{ $floor->getSectionCount($request1['area'],$request1['department']) }}</span>
                                    </div>
                                </div>
                                {{-- <a href="#" class="search_emp" data-floor="{{ $floor->hr_floor_id }}" data-leavetype="all">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Employee </div>

                                        <div class="profile-info-value">
                                            <span>{{ count($floor->getFloorWiseEmp($request1['unit'],$request1['area'],$request1['department'],$floor->hr_floor_id)) }}</span>
                                        </div>
                                    </div>
                                </a> --}}
                                @php
                                    $totalLeaveCount = 0;
                                    $empArry = [];
                                @endphp
                                @foreach($floor_leave_wise[$floor->hr_floor_id] as $type=>$floor_leave)
                                @php
                                    foreach($floor_leave as $k=>$fleave) {
                                        $empArry[$floor->hr_floor_id][$type][$fleave->leave_ass_id] = $fleave->leave_ass_id;
                                    }
                                    $totalLeaveCount += count($empArry[$floor->hr_floor_id][$type]);
                                @endphp
                                    <a href="#" class="search_emp" data-floor="{{ $floor->hr_floor_id }}" data-leavetype="{{ $type }}">
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{ $type }} Leave </div>
                                            <div class="profile-info-value">
                                                <span>{{ count($empArry[$floor->hr_floor_id][$type]) }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                                <a href="#" data-floor="{{ $floor->hr_floor_id }}">
                                    <div class="profile-info-row" style="background: lightcyan; font-weight: bold;">
                                        <div class="profile-info-name"> Total Leave </div>

                                        <div class="profile-info-value after-load">
                                            <span>{{ $totalLeaveCount }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @if($count == 4)
                    </div>
                @endif
                @php
                    if($count == 4){
                        $count = 0;
                    }
                @endphp
            @endforeach
				
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
<script>
    function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_leave_search_floorPrint')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: result,
                unitName: '{{$unit->hr_unit_name}}',
                areaName: '{{$area->hr_area_name}}',
                departmentName: '{{$department->hr_department_name}}',
                data1: JSON.stringify(@json($floor_leave_wise)),
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