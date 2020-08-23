
<style type="text/css">

    @media only screen and (max-width: 1280px){
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
                <a href="#" class="search_unit_floor" data-id="{{$unitId}}"> Floor </a>
            </li>
            
            <li>
                 Line
            </li>
        </ul><!-- /.breadcrumb -->
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($line_list)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
            	<div class="col-sm-12">
                    @foreach($line_list as $k=>$line)
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_section" data-line="{{ $line->hr_line_id }}">
                                        <h5 class="widget-title smaller">  {{ $line->hr_line_name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        
                                        <div class="profile-info-row search_all_employee" data-id="{{$unitId}}" data-floor="{{$line->hr_line_floor_id}}" data-line="{{$line->hr_line_id}}">
                                            <div class="profile-info-name"> Total Employee </div>

                                            <div class="profile-info-value">
                                                <span>{{ $lineEmpCount[$line->hr_line_id] }}</span>
                                            </div>
                                        </div>
                                        
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
    


    {{--function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_att_search_floorPrint')}}',
            type: 'get',
            data: {
                data: result,
                unitName: '{{ $unit->hr_unit_name }}',
                areaName: '{{ $area->hr_area_name }}',
                departmentName: '{{ $department->hr_department_name }}',
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
    }--}}
</script>