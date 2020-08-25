@extends('hr.layout')
@section('title', 'Leave List')
@section('main-content')
@push('css')
<style type="text/css">
    {{-- removing the links in print and adding each page header --}}
    /*a[href]:after { content: none !important; }
    thead {display: table-header-group;}
    input::-webkit-input-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    input:-moz-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    input:-ms-input-placeholder {
        color: black;
        font-weight: bold;
        font-size: 12px;
    }
    th{
        font-size: 12px;
        font-weight: bold;
    }

    .dataTables_wrapper .dt-buttons {
        text-align: center;
        padding-left: 20%;
    }
    
    
    .dataTables_processing {
        top: 200px !important;
        z-index: 11000 !important;
        border: 0px !important;
        box-shadow: none !important;
        background: transparent !important;
    }*/
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li>
                <li>
                    <a href="#"> Time & Attendance </a>
                </li>
                <li class="active"> Leaves </li>
            </ul><!-- /.breadcrumb -->
 
        </div>

        <div class="page-content"> 
            @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading"><h6>All Leaves<a href="{{ url('hr/timeattendance/leave-entry')}}" class="pull-right btn  btn-primary">Leave Entry</a></h6></div> 
                <div class="panel-body">

                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100%; overflow-x: auto; display: block; ">
                        <thead>
                            <tr>
                                <th width="10%">Associate ID</th>
                                <th width="20%">Name</th>
                                <th width="20%">Unit</th>
                                <th width="10%">Leave Type</th>
                                <th width="20%">Leave Duration</th>
                                <th width="10%">Leave Status</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead> 
                         <tfoot>
                            <tr>
                                <th width="10%">Associate ID</th>
                                <th width="20%">Name</th>
                                <th width="20%">Unit</th>
                                <th width="10%">Leave Type</th>
                                <th width="20%">Leave Duration</th>
                                <th width="10%">Leave Status</th>
                                <th width="20%">Action</th>
                            </tr>
                        </tfoot> 
                    </table>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){ 
    var searchable = [0,1,4];
    var selectable = [2,3,5]; //use 2,4....and * for all
    // dropdownList = {column_number: {'key':value}};
    var dropdownList = {
        '2':[@foreach($unit as $u) <?php echo "'$u'," ?> @endforeach],
        '3':['Casual','Earned','Sick', 'Special','Maternity'],
        '5':['Applied','Approved','Declined'] 
    };

    $('#dataTables').DataTable({
        order: [],  
        processing: true,
        responsive: false,
        serverSide: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '

        },
        pagingType: "full_numbers", 
        dom:'lBfrtip', 
        ajax: {
            url: '{!! url("hr/timeattendance/all_leaves_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        }, 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'All Leaves',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'All Leaves',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'All Leaves',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary', 
                title: 'All Leaves',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'All Leaves',
                header: true,
                footer: false,
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'leave_ass_id', name: 'leave_ass_id' , orderable: false}, 
            { data: 'as_name',  name: 'as_name' , orderable: false}, 
            { data: 'hr_unit_name',  name: 'hr_unit_name' , orderable: false}, 
            { data: 'leave_type', name: 'leave_type' , orderable: false}, 
            { data: 'leave_duration', name: 'leave_duration' , orderable: false}, 
            { data: 'leave_status', name: 'leave_status' , orderable: false}, 
            { data: 'action', name: 'action', orderable: false, searchable: false}
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

                var select = $('<select  style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function(e){
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column.search(val ? val : '', true, false ).draw();
                        e.stopPropagation();
                    });

                // column.data().unique().sort().each( function ( d, j ) {
                // if(d) select.append('<option value="'+d+'">'+d+'</option>' )
                // });
                $.each(dropdownList[i], function(j, v) {
                    select.append('<option value="'+v+'">'+v+'</option>')
                }); 
            });
        }   
    }); 
});
</script>
@endpush
@endsection