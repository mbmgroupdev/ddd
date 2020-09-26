
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
<div class="panel panel-info col-sm-12 col-xs-12">
    <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all"> MBM Group </a>
            </li>
            <li>
                <a href="#" class="search_unit"> All Unit </a>
            </li>
        </ul>
        {{-- <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($unit_emp)}},"{{$showTitle}}")'>Print</a> --}}
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            
            @foreach($unit_list as $k=>$unit)
                
            <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                <div class="widget-box widget-color-green2 light-border ui-sortable-handle children" id="widget-box-6">
                    <div class="widget-header">
                        <a href="#" class="white">
                            <h5 class="widget-title smaller">  {{ $unit->hr_unit_name }} </h5></a>
                    </div>
                    

                    <div class="widget-body" style="height: 183px;">
                        <div class="widget-main padding-6">
                            {{-- <div class="profile-info-row">
                                <div class="profile-info-name"> Employee </div>

                                <div class="profile-info-value">
                                    <span>{{ count($empList[$unit->hr_unit_id])+count($empActiveList[$unit->hr_unit_id]) }}</span>
                                </div>
                            </div> --}}
                        
                        <div class="profile-info-row unit_emp_list" data-id="join" data-unit="{{$unit->hr_unit_id}}">
                            <div class="profile-info-name">Joined</div>

                            <div class="profile-info-value">
                                <span>{{count($empActiveList[$unit->hr_unit_id]) }}</span>
                            </div>
                        </div>
                        @if(!empty($empList[$unit->hr_unit_id]))
                            @foreach($empList[$unit->hr_unit_id] as $statusType=>$elist)
                                <div class="profile-info-row unit_emp_list" data-id="{{$statusType}}" data-unit="{{$unit->hr_unit_id}}">
                                    <div class="profile-info-name">
                                        {{ucfirst(Custom::getEmpStatusName($statusType))}}
                                    </div>

                                    <div class="profile-info-value">
                                            @php
                                                $count = count($elist);
                                                echo $count;
                                            @endphp
                                    </div>
                                </div>
                            @endforeach
                        @elseif(!empty($empActiveList[$unit->hr_unit_id]))   

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
<script type="text/javascript">
    $(document).ready(function(){

       


});
</script>