@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
<style type="text/css">
    {{-- removing the links in print and adding each page header --}}
    a[href]:after { content: none !important; }
    thead {display: table-header-group;}

    /*making place holder custom*/
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
</style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Time & Attendance</a>
                </li>
                <li >
                    <a href="#">Operation</a>
                </li>
                <li class="active">Without Pay List</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Time & Attendance<small> <i class="ace-icon fa fa-angle-double-right"></i> Operation <i class="ace-icon fa fa-angle-double-right"></i> Without Pay List</small></h1>
            </div>

            <div class="row">
                <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12 worker-list">
                    <!-- PAGE CONTENT BEGINS --> 
                    <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;white-space: nowrap; width: 100%;">
                        <thead>
                            <tr>
                                <th width="10%">SL. No.</th>
                                <th width="20%">Associate ID</th>
                                <th width="20%">Start Date</th>
                                <th width="20%">End Date</th>
                                <th width="20%">Reason</th>
                                <th width="20%">Created by</th>
                                <th width="20%">Created at</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th width="10%">SL. No.</th>
                                <th width="20%">Associate ID</th>
                                <th width="20%">Start Date</th>
                                <th width="20%">End Date</th>
                                <th width="20%">Reason</th>
                                <th width="20%">Created by</th>
                                <th width="20%">Created at</th>
                                <th width="20%">Action</th>
                            </tr>
                        </tfoot> 
                    </table>

                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            <br>
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{   
    var searchable = [1,2,3,4,5,6];
    var selectable = []; //use 4,5,6,7,8,9,10,11,....and * for all
 

    $('#dataTables').DataTable({
        order: [], //reset auto order
        processing: true,
        responsive: false,
        serverSide: true,
        pagingType: "full_numbers",
        ajax: {
            url: '{!! url("hr/timeattendance/operation/without_pay_list_data") !!}',
            type: "POST",
            headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
            } 
        },
        dom: "<'row'<'col-sm-2'l><'col-sm-4'i><'col-sm-3 text-center'B><'col-sm-3'f>>tp", 
        buttons: [  
            {
                extend: 'copy', 
                className: 'btn-sm btn-info',
                title: 'Without Pay List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'csv', 
                className: 'btn-sm btn-success',
                title: 'Without Pay List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'excel', 
                className: 'btn-sm btn-warning',
                title: 'Without Pay List',
                header: false,
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'pdf', 
                className: 'btn-sm btn-primary',
                title: 'Without Pay List',
                header: false,
                footer: true, 
                exportOptions: {
                    columns: ':visible'
                }
            }, 
            {
                extend: 'print', 
                className: 'btn-sm btn-default',
                title: 'Without Pay List',
                header: true,
                footer: false,
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false
                } 
            } 
        ], 
        columns: [ 
            { data: 'serial', name: 'serial' }, 
            { data: 'hr_wop_as_id',  name: 'hr_wop_as_id' }, 
            { data: 'hr_wop_start_date',  name: 'hr_wop_start_date' }, 
            { data: 'hr_wop_end_date', name: 'hr_wop_end_date' }, 
            { data: 'hr_wop_reason', name: 'hr_wop_reason' }, 
            { data: 'hr_wop_created_by', name: 'hr_wop_created_by' }, 
            { data: 'hr_wop_created_at', name: 'hr_wop_created_at' }, 
            { data: 'action', name: 'action', orderable: false, searchable: false }
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
            api.columns(selectable).every( function (i, x) {
                var column = this; 

                var select = $('<select style="width: 110px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
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


@endsection