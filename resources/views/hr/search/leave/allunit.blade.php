
<style type="text/css">

    @media only screen and (max-width: 1280px) {
        .search-result-div{width: 33.33%;}
    }
    @media only screen and (max-width: 767px){
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
            
            @php
                // dump($unit_leave_wise)
                $count = 0;
            @endphp
            @foreach($unit_list as $k=>$unit)
                @if($count == 0)
                    <div class="row">
                @endif
                @php
                    $count++;
                @endphp
                <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                    <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                        <div class="widget-header">
                            <a href="#" class="white search_area" data-unit="{{ $unit->hr_unit_id }}">
                                <h5 class="widget-title smaller" title="{{ strlen($unit->hr_unit_name)>28?$unit->hr_unit_name:'' }}">  {{ strlen($unit->hr_unit_name)>28?substr($unit->hr_unit_name,0,28).'...':$unit->hr_unit_name  }}</h5></a>
                        </div>

                        <div class="widget-body">
                            <div class="widget-main padding-6">
                                <div class="profile-info-row">
                                    <div class="profile-info-name search_area" data-unit="{{ $unit->hr_unit_id }}"> Total Area </div>

                                    <div class="profile-info-value">
                                        <span>{{ $area_count }}</span>
                                    </div>
                                </div>
                                {{-- <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id }}" data-leavetype="all">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Employee </div>

                                        <div class="profile-info-value">
                                            <span>{{ count($unit->getUnitWiseEmp($unit->hr_unit_id)) }}</span>
                                        </div>
                                    </div>
                                </a> --}}
                                @php
                                    $totalLeaveCount = 0;
                                    $empArry = [];
                                @endphp
                                @foreach($unit_leave_wise[$unit->hr_unit_id] as $type=>$unit_leave)
                                @php
                                    foreach($unit_leave as $k=>$uleave) {
                                            $empArry[$unit->hr_unit_id][$type][$uleave->leave_ass_id] = $uleave->leave_ass_id;
                                    }
                                    $totalLeaveCount += count($empArry[$unit->hr_unit_id][$type]);
                                @endphp
                                    <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id }}" data-leavetype="{{ $type }}">
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> {{ $type }} Leave </div>

                                            <div class="profile-info-value after-load">
                                                <span>{{ count($empArry[$unit->hr_unit_id][$type]) }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                                <a href="#" data-unit="{{ $unit->hr_unit_id }}">
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
            url: '{{url('hr/search/hr_leave_search_unitPrint')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: result,
                data1: JSON.stringify(@json($unit_leave_wise)),
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