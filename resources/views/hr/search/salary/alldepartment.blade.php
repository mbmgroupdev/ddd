
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
                 {{ $area->hr_area_name }}
            </li>
            <li class="active"> Department </li>
        </ul>
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv("{{$showTitle}}")'>Print</a>
        <!-- /.breadcrumb -->
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
       <!-- <h4 class="center">MBM Group</h4> -->
        
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
                <div class="col-sm-12">
                    @foreach($department_list as $k=>$department)
                    
                        <div class="search-result-div col-xs-12 col-sm-3 widget-container-col ui-sortable">
                            <div class="widget-box widget-color-green2 light-border ui-sortable-handle" id="widget-box-6">
                                <div class="widget-header">
                                    <a href="#" class="white search_floor" data-department="{{ $department->hr_department_id }}">
                                        <h5 class="widget-title smaller">  {{ $department->hr_department_name }} </h5></a>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main padding-6">
                                        <a href="#" class="search_floor" data-department="{{ $department->hr_department_id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name">Floor </div>

                                                <div class="profile-info-value after-load" id="{{ $department->hr_department_id }}_floor">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="search_emp" data-department="{{ $department->hr_department_id }}">
                                            <div class="profile-info-row">
                                                <div class="profile-info-name"> Total Employee </div>

                                                <div class="profile-info-value after-load" id="{{ $department->hr_department_id }}_emp">
                                                    <span>0</span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="profile-info-row search_emp" data-department="{{ $department->hr_department_id  }}" data-salstatus='salary'>
                                            <div class="profile-info-name"> Salary Payable </div>

                                            <div class="profile-info-value after-load" id="{{ $department->hr_department_id }}_s">
                                                <span>0</span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row search_emp" data-department="{{ $department->hr_department_id  }}" data-salstatus='ot'>
                                            <div class="profile-info-name"> OT Payable </div>

                                            <div class="profile-info-value after-load" id="{{ $department->hr_department_id }}_o">
                                                <span>0</span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row search_emp" data-department="{{ $department->hr_department_id  }}">
                                            <div class="profile-info-name"> Total Payable </div>

                                            <div class="profile-info-value after-load" id="{{ $department->hr_department_id }}_t">
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
    var department_list = <?php echo json_encode($department_list); ?>;
    jQuery.each(department_list, function(index, department) {
        $.ajax({
            url: '{{ url('hr/search/hr_salary_search_res') }}',
            type: 'get',
            data: {
                unit: {{ $request1['unit'] }},
                area: {{ $request1['area'] }},
                department: department.hr_department_id,
                name: department.hr_department_name
            },
            success: function(res) {
                $('#'+department.hr_department_id+'_emp span').html(res.emp);
                $('#'+department.hr_department_id+'_s span').html(res.salary);
                $('#'+department.hr_department_id+'_o span').html(res.ot);
                $('#'+department.hr_department_id+'_t span').html(res.total);
                $('#'+department.hr_department_id+'_floor span').html(res.floor);
                //console.log(res);
                valueToPush.push(res);
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
                type: 'Department',
                title: pagetitle,
                unit: {{ $request1['unit'] }},
                area: {{ $request1['area'] }}
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