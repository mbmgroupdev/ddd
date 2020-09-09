<div class="panel panel-info col-sm-12">
    <br>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            <li>
                <a href="#" class="search_unit"> All Unit </a>
            </li>
            @if(isset($request2['as_unit_id']))
                <li>
                    <a href="#" class="search_area" data-unit="{{ $request2['as_unit_id'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_area_id']))
                <li>
                     <a href="#" class="search_dept" data-area="{{ $request2['as_area_id'] }}">
                        {{ $data['area']->hr_area_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_department_id']))
                <li>
                    <a href="#" class="search_floor" data-department="{{ $request2['as_department_id'] }}">
                        {{ $data['department']->hr_department_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_floor_id']))
                <li>
                    <a href="#" class="search_floor" data-floor="{{ $request2['as_floor_id'] }}">
                        {{ $data['floor']->hr_floor_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_section_id']))
                <li>
                    <a href="#" class="search_section" data-section="{{ $request2['as_section_id'] }}">
                        {{ $data['section']->hr_section_name }}
                    </a>
                </li>
            @endif
            @if(isset($data['subsection']))
                <li>
                    <a href="#" class="search_subsection" data-section="{{ $request2['as_subsection_id'] }}">
                        {{ $data['subsection']->hr_subsec_name }}
                    </a>
                </li>
            @endif
            <li class="active"> Employee List ({{ $request1['leavetype'] }})</li>
        </ul><!-- /.breadcrumb -->
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
        	<div class="col-sm-12">
                <div class="table-responsive">
                    <table id="dataTables" class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Associate ID</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Shift</th>
                                <th>Day/Type</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $rangeFrom = date('Y-m-d');
    $rangeTo   = date('Y-m-d');
    if($request1['type'] == 'date'){
        $rangeFrom = $request1['date'];
        $rangeTo = $request1['date'];
    }
    if($request1['type'] == 'month') {
        $year = date('Y', strtotime($request1['month']));
        $monthNumber = date('n', strtotime($request1['month']));
        $rangeFrom = $year;
        $rangeTo = $monthNumber;
    }
@endphp
<script>
    var attstatus = '{{ isset($request1['attstatus'])?$request1['attstatus']:'' }}';
    $(document).ready(function(){ 

        var searchable = [1,2];
        var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {};

        var dt =  $('#dataTables').DataTable({
           order: [], //reset auto order
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            processing: true,
            responsive: false,
            serverSide: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            ajax: {
                url: '{!! url("hr/search/hr_leave_search_emp_data") !!}',
                data: function (d) {
                    d.associate_id  = '',
                    d.leavetype     = '{{ $request1['leavetype'] }}',
                    d.type          = '{{ $request1['type'] }}',
                    d.rangeFrom     = '{{ $rangeFrom }}',
                    d.rangeTo       = '{{ $rangeTo }}',
                    d.unit          = parseInt({{ isset($request2['as_unit_id'])?$request2['as_unit_id']:'' }}),
                    d.floor         = parseInt({{ isset($data['as_floor_id'])?$data['floor']->hr_floor_id:'' }}),
                    d.line          = '',
                    d.area          = parseInt({{ isset($request2['as_area_id'])?$request2['as_area_id']:''  }}),
                    d.department    = parseInt({{ isset($request2['as_department_id'])?$request2['as_department_id']:''  }}),
                    d.section       = parseInt({{ isset($request2['as_section_id'])?$request2['as_section_id']:''  }}),
                    d.subsection    = parseInt({{ isset($request2['as_subsection_id'])?$request2['as_subsection_id']:''  }})
                },
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    "action": allExport,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    "action": allExport,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    "action": allExport,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {

                    extend: 'print',
                    autoWidth: true,
                    "action": allExport,
                    className: 'btn-sm btn-default print',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: true
                    },
                    title: '',
                    messageTop: function () {
                        return  '<h3 class="text-center">MBM Garments Ltd.</h3>'+
                                '<h4 class="text-center">{{ $request1['leavetype'] }} Leave Report</h4>'+
                                '@if(isset($request2["as_unit_id"]))<h6 class="text-center">Unit: {{ $data["unit"]->hr_unit_name }} </h6>@endif'+
                                '@if(isset($request2["as_area_id"]))<h6 class="text-center">Area: {{$data["area"]->hr_area_name}}'+
                                    '@if(isset($request2["as_department_id"]))| Department: {{$data["department"]->hr_department_name}} @endif'+
                                    ' @if(isset($request2["as_floor_id"]))| Floor: {{$data["floor"]->hr_floor_name}}  @endif '+
                                    ' @if(isset($request2["as_section_id"]))| Section: {{$data["section"]->hr_section_name}} @endif '+
                                '</h6>@endif'+
                                '<h5 class="text-center">{{$showTitle}}</h5>';
                    }

                }
            ],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'associate_id',  name: 'associate_id' },
                { data: 'as_name', name: 'as_name' },
                { data: 'hr_designation_name', name: 'hr_designation_name' },
                { data: 'hr_shift_name', name: 'hr_shift_name' },
                { data: 'status', name: 'status' }
            ],
            initComplete: function () {
                var api =  this.api();

                // Apply the search
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });

                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });

                // each column select list
                api.columns(selectable).every( function (i, x) {
                    var column = this;

                    var select = $('<select><option value="">'+$(column.header()).text()+'</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function(e){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? val : '', true, false ).draw();
                            e.stopPropagation();
                        });

                    $.each(dropdownList[i], function(j, v) {
                        select.append('<option value="'+v+'">'+v+'</option>')
                    });
                });
            }
        });
    });
</script>