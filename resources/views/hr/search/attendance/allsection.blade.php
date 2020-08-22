<div class="panel panel-info col-sm-12 col-xs-12">
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
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($section_list)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
            	<div class="col-sm-12">
                    @foreach($section_list as $k=>$section)
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_subsection" data-section="{{ $section->hr_section_id }}">
                                        <h5 class="widget-title smaller">  {{ $section->hr_section_name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        <div class="profile-info-row search_subsection" data-section="{{ $section->hr_section_id }}">
                                            <div class="profile-info-name"> Total Sub-Section </div>

                                            <div class="profile-info-value">
                                                <span>{{ $section->getSubsectionCount($request1['area'],$request1['department'],$section->hr_section_id) }}</span>
                                            </div>
                                        </div>
                                        {{-- <a href="#" class="search_emp" data-section="{{ $section->hr_section_id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Employee </div>

                                                <div class="profile-info-value">
                                                    <span>{{ count($section->getSectionWiseEmp($request1['unit'],$request1['area'],$request1['department'],$request1['floor'],$section->hr_section_id)) }}</span>
                                                </div>
                                            </div>
                                        </a> --}}
                                        <a href="#" class="search_emp" data-section="{{ $section->hr_section_id }}" data-attstatus="absent">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Absent </div>

                                                <div class="profile-info-value after-load" id="{{ $section->hr_section_id }}_tAbsent">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-section="{{ $section->hr_section_id }}" data-attstatus="present">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Present </div>

                                                <div class="profile-info-value after-load" id="{{ $section->hr_section_id }}_tPresent">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-section="{{ $section->hr_section_id }}" data-attstatus="leave">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Leave </div>

                                                <div class="profile-info-value after-load" id="{{ $section->hr_section_id }}_tLeave">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-section="{{ $section->hr_section_id }}" data-attstatus="late">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Late </div>

                                                <div class="profile-info-value after-load" id="{{ $section->hr_section_id }}_tLate">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
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
@php
    $rangeFrom = date('Y-m-d');
    $rangeTo = date('Y-m-d');
    if($request1['type'] == 'date'){
        $rangeFrom = $request1['date'];
        $rangeTo = $request1['date'];
    }
@endphp
<script>
    $(document).ready(function(){
        $('.after-load span').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-30"></i>');
    });
    var response = [];
    var section_list = <?php echo json_encode($section_list); ?>;
    jQuery.each(section_list, function(index, section) {
        $.ajax({
            url: '{{ url('hr/search/hr_att_search_emp_count') }}',
            type: 'get',
            data: {
                unit: {{ $request1['unit'] }},
                area: {{ $request1['area'] }},
                department: {{ $request1['department'] }},
                floor: {{ $request1['floor'] }},
                section: section.hr_section_id,
                rangeFrom: '{{ $rangeFrom }}',
                rangeTo: '{{ $rangeTo }}'
            },
            success: function(res) {
                response[section.hr_section_id] = res;
                $('#'+section.hr_section_id+'_tAbsent span').html(res.absent);
                $('#'+section.hr_section_id+'_tPresent span').html(res.present);
                $('#'+section.hr_section_id+'_tLeave span').html(res.leave);
                $('#'+section.hr_section_id+'_tLate span').html(res.late);
                // console.log(res);
            },
            error: function() {
                console.log('error occored');
            }
        })
    });

    function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_att_search_sectionPrint')}}',
            type: 'get',
            data: {
                data: result,
                unitName: '{{ $unit->hr_unit_name }}',
                areaName: '{{ $area->hr_area_name }}',
                departmentName: '{{ $department->hr_department_name }}',
                floorName: '{{ $floor->hr_floor_name }}',
                title: pagetitle,
                response: response
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