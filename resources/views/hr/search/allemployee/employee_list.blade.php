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
            @if(isset($floorId))
                <li>
                <a href="#" class="search_unit_floor" data-id="{{$unitId}}"> Floor </a>
                </li>
            @endif
            
            <li>
                <a href="#" class=""> Employee List </a>
            </li>
        </ul>
    </div>
    <hr>
    <p class="search-title">Search results of  &nbsp {{ $showTitle }}</p>
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
                                <th>Floor</th>
                                <th>Shift</th>
                                <th>Line</th>
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
@endphp
<script type="text/javascript">
$(document).ready(function(){ 
    var searchable = [1,2,3,4,5,6];
    

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: true,
        serverSide: true,
        pagingType: "full_numbers",
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        ajax: {
            url: '{!! url("hr/search/hr_all_employee_show_emp_data") !!}',
            data: function (d) {
                    d.unit  = '{{$unitId}}',
                    d.floor ='{{$floorId}}',
                    d.line = '{{$lineId}}'
                    
                },
            type: "get",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "lBfrtip", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Employee Benefit List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Employee Benefit List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Employee Benefit List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'Employee Benefit List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Employee Benefit List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: [0,1,2,3,4,5],
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'associate_id', name: 'associate_id' }, 
            { data: 'as_name',  name: 'as_name' },
            { data: 'hr_designation_name',  name: 'hr_designation_name' },
            { data: 'floor',  name: 'floor' },
            { data: 'shift',  name: 'shift' },
            { data: 'line',  name: 'line' },
            
        ], 
        initComplete: function () {   
            var api =  this.api();

            // Apply the search 
            api.columns(searchable).every(function () {
                var column = this; 
                var input = document.createElement("input"); 
                input.setAttribute('placeholder', $(column.header()).text());
                input.setAttribute('style', 'width: 110px; height:25px; border:1px solid whitesmoke;');

                $(input).appendTo($(column.header()).empty())
                .on('keyup', function () {
                    column.search($(this).val(), false, false, true).draw();
                });

                $('input', this.column(column).header()).on('click', function(e) {
                    e.stopPropagation();
                });
            });
 
            // each column select list
            // api.columns(selectable).every( function (i, x) {
            //     var column = this; 

            //     var select = $('<select style="width: 110px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
            //         .appendTo($(column.header()).empty())
            //         .on('change', function(e){
            //             var val = $.fn.dataTable.util.escapeRegex(
            //                 $(this).val()
            //             );
            //             column.search(val ? val : '', true, false ).draw();
            //             e.stopPropagation();
            //         });

            //     // column.data().unique().sort().each( function ( d, j ) {
            //     // if(d) select.append('<option value="'+d+'">'+d+'</option>' )
            //     // });
            //     $.each(dropdownList[i], function(j, v) {
            //         select.append('<option value="'+v+'">'+v+'</option>')
            //     }); 
            // });
        }   
    }); 
});
</script>
