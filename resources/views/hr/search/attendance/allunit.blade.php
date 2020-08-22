
<div class="panel panel-info col-sm-12 col-xs-12">
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
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($unit_list)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
                <div class="col-sm-12">
                    @foreach($unit_list as $k=>$unit)
                        
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_area" data-unit="{{ $unit->hr_unit_id }}">
                                        <h5 class="widget-title smaller" title="{{ strlen($unit->hr_unit_name)>28?$unit->hr_unit_name:'' }}">  {{ strlen($unit->hr_unit_name)>28?substr($unit->hr_unit_name,0,28).'...':$unit->hr_unit_name  }}</h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        <div class="profile-info-row search_area" data-unit="{{ $unit->hr_unit_id }}">
                                            <div class="profile-info-name"> Total Area </div>

                                            <div class="profile-info-value">
                                                <span>{{ $area_count }}</span>
                                            </div>
                                        </div>
                                        {{-- <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id }}" data-attstatus="all">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Employee </div>

                                                <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_tAttendance">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a> --}}
                                        <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id }}" data-attstatus="absent">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Absent </div>

                                                <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_tAbsent">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id }}" data-attstatus="present">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Present </div>

                                                <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_tPresent">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id }}" data-attstatus="leave">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Leave </div>

                                                <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_tLeave">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id }}" data-attstatus="late">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Late </div>

                                                <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_tLate">
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
    if($request['type'] == 'date'){
        $rangeFrom = $request['date'];
        $rangeTo = $request['date'];
    }
@endphp

<script>
    $(document).ready(function(){ 
        $('.after-load span').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-30"></i>');
    });
    var response = [];
    var unit_list = <?php echo json_encode($unit_list); ?>;
    jQuery.each(unit_list, function(index, unit) {
        $.ajax({
            url: '{{ url('hr/search/hr_att_search_emp_count') }}',
            type: 'get',
            data: {
                unit: unit.hr_unit_id,
                rangeFrom: '{{ $rangeFrom }}',
                rangeTo: '{{ $rangeTo }}'
            },
            success: function(res) {
                response[unit.hr_unit_id] = res;
                $('#'+unit.hr_unit_id+'_tAbsent span').html(res.absent);
                $('#'+unit.hr_unit_id+'_tPresent span').html(res.present);
                $('#'+unit.hr_unit_id+'_tLeave span').html(res.leave);
                $('#'+unit.hr_unit_id+'_tLate span').html(res.late);
                $('#'+unit.hr_unit_id+'_tAttendance span').html(res.attendance);
            },
            error: function() {
                console.log('error occored');
            }
        })
    });

    function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_att_search_unitPrint')}}',
            type: 'get',
            data: {
                data: result,
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