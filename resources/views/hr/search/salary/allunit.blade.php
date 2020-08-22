
<div class="panel panel-info col-sm-12 col-xs-12">
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
            <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv("{{$showTitle}}")'>Print</a>
        </div>

        <hr>
        <p class="search-title">Search results of  {{ $showTitle }}</p>
        <!-- <h4 class="center">MBM Group</h4> -->
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
                <div class="col-sm-12">
                    @foreach($unit_list as $k=>$unit)
                    
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_area" data-unit="{{ $unit->hr_unit_id }}">
                                        <h5 class="widget-title smaller" title="{{ strlen($unit->hr_unit_name)>28?$unit->hr_unit_name:'' }}"> {{ strlen($unit->hr_unit_name)>28?substr($unit->hr_unit_name,0,28).'...':$unit->hr_unit_name  }} 
                                        </h5>
                                      

                                        </a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        <a href="#" class="search_area" data-unit="{{ $unit->hr_unit_id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Area </div>

                                                <div class="profile-info-value">
                                                    <span>{{ $area_count }}</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-unit="{{ $unit->hr_unit_id  }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Employee </div>

                                                <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_emp">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="profile-info-row search_emp" data-unit="{{ $unit->hr_unit_id  }}" data-salstatus='salary'>
                                            <div class="profile-info-name"> Total Salary </div>

                                            <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_s">
                                                <span>0</span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row search_emp" data-unit="{{ $unit->hr_unit_id  }} " data-salstatus='ot'>
                                            <div class="profile-info-name"> Total OT </div>

                                            <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_o">
                                                <span>0</span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row search_emp" data-unit="{{ $unit->hr_unit_id  }}">
                                            <div class="profile-info-name"> Total Amount </div>

                                            <div class="profile-info-value after-load" id="{{ $unit->hr_unit_id }}_t">
                                                <span>0</span>
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
<script>
    $(document).ready(function(){ 
        $('.after-load span').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-30"></i>');
    }); 
    var valueToPush= [];
    var unit_list = <?php echo json_encode($unit_list); ?>;
    jQuery.each(unit_list, function(index, unit) {
        $.ajax({
            url: '{{ url('hr/search/hr_salary_search_res') }}',
            type: 'get',
            data: {
                unit: unit.hr_unit_id,
                name: unit.hr_unit_name
            },
            success: function(res) {
                $('#'+unit.hr_unit_id+'_emp span').html(res.emp);
                $('#'+unit.hr_unit_id+'_s span').html(res.salary);
                $('#'+unit.hr_unit_id+'_o span').html(res.ot);
                $('#'+unit.hr_unit_id+'_t span').html(res.total);
                valueToPush.push(res);
                //console.log(res);
            },
            error: function() {
                console.log('error occored');
            }
        })
    });
    function printDiv(pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_salary_search_print_page')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: valueToPush,
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