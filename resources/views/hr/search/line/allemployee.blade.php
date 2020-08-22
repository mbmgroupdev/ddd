
<div class="panel panel-info col-sm-12 col-xs-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            
            @if(isset($request1['unit']))
                <li>
                    <a href="#" class="search_unit"> All Unit </a>
                </li>
                <li>
                    <a href="#" class="search_floor" data-unit="{{ $request1['unit'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request1['floor']))
                <li>
                    <a href="#" class="search_line" data-floor="{{ $request1line['floor'] }}">
                        {{ $data['floor']->hr_floor_name }}
                    </a>
                </li>
            @endif
            @if(isset($request1['line']))
                <li>
                    <a href="#" class="line_change" data-line="{{ $request1['line'] }}">
                        {{ $data['line']->hr_line_name }}
                    </a>
                </li>
            @endif
            <li class="active"> Employee Line Change List</li>
        </ul><!-- /.breadcrumb -->

    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <!-- <h4 class="center">MBM Group</h4> -->
        
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
                <div class="col-sm-12">
                    <table id="dataTables" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Associate ID</th>
                                <th>Associate Name</th>
                                <th>Unit</th>
                                <th>Floor</th>
                                <th>Line</th>
                                <th>Total Change</th>
                                <th>Changed Line</th>
                                <th>Changed Floor</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>
    </div>
</div>
<div class="modal fade" id="outSideEmp" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" id="outSideEmpContent"></div>
    </div>
</div>

<script type="text/javascript">
$(document).on('click','.lineEmpDetail', function() {
        var as_id = $(this).data('emp');
        $.ajax({
            url: '{{url('/hr/search/hr_line_get_emp_details')}}',
            type: 'get',
            data: {
                as_id: as_id
            },
            success: function(res) {
                $('#outSideEmpContent').html(res);
                $('#outSideEmp').modal('show');
                console.log(res);
            }
        });
    });
$(document).ready(function(){

    
        var searchable = [1,2,3,4];
        var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
        var dropdownList = {
        };

        var dTable =  $('#dataTables').DataTable({
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
                url: '{!! url("hr/search/hr_Line_search_employee_list") !!}',
                data: {
                    unit: "{{ isset($request1['unit'])?$request1['unit']:'' }}",
                    floor: "{{ isset($request1['floor'])?$request1['floor']:'' }}",
                    line: "{{ isset($request1['line'])?$request1['line']:'' }}"
                },
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info text-center',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {

                    extend: 'print',
                    autoWidth: true,
                    className: 'btn-sm btn-default print',
                    title: '',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: false
                    },
                    title: '',
                    messageTop: function () {
                        return  '<h3 class="text-center">MBM Garments Ltd.</h3>'+
                                '<h4 class="text-center">Line Change Report (Employee Wise)</h4>'+
                                '@if(isset($request1["unit"]))<h6 class="text-center">Unit: {{ $data["unit"]->hr_unit_name }} </h6>@endif'+
                                '@if(isset($request1["floor"]))<h6 class="text-center">Floor: {{$data["floor"]->hr_floor_name}}'+
                                    '@if(isset($request1["line"]))| Line: {{$data["line"]->hr_line_name}} @endif'+
                                '</h6>@endif'+
                                '<h5 class="text-center">{{$showTitle}}</h5>';
                    }

                }
            ],

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'associate_id',  name: 'associate_id' },
                { data: 'as_name', name: 'as_name'},
                { data: 'hr_unit_name',  name: 'hr_unit_name' },
                { data: 'as_floor_id',  name: 'as_floor_id' },
                { data: 'as_line_id',  name: 'as_line_id' },
                { data: 'count_change',  name: 'count_change' },
                { data: 'changed_floor',  name: 'changed_floor' },
                { data: 'changed_line',  name: 'changed_line' },
                { data: 'start_date',  name: 'start_date' },
                { data: 'end_date',  name: 'end_date' }
            ],
            initComplete: function () {
                var api =  this.api();

                // Apply the search
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());

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
        var type = '{{ Request::get('type') }}';
        if(type=== 'date'){
            dTable.column(6).visible(false);      
        }else{
            dTable.column(7).visible(false);
            dTable.column(8).visible(false);
            dTable.column(9).visible(false);
            dTable.column(10).visible(false);
        }
        console.log('fbgyh');
    });


    
</script>